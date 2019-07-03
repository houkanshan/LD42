<?php
require_once('./libs/LessQL/Database.php');
require_once('./libs/LessQL/Literal.php');
require_once('./libs/LessQL/Result.php');
require_once('./libs/LessQL/Row.php');
require_once('./libs/utils.php');

date_default_timezone_set('UTC');

define('DEV', false);
define("VERSION", 31);
define('FILE_LOG', "log.txt");
define('IP_LIMIT', 3);
define('MIN_UPDATE_INTERVAL', 12); // hour
define('MIN_STORY_INTERVAL', 6); // hour
define('PLAYER_SLOTS', 12); // hour
define('CHECK_INTERVAL', 48); // hour
$GLOBALS['AVATARS'] = range('1', '20');
$GLOBALS['ADMINS'] = array('Houmai', 'Zerotonin');

if (DEV) {
  header("Access-Control-Allow-Origin: *");
}

function db() {
  $pdo = new \PDO( 'sqlite:db.sqlite3' );
  $db = new \LessQL\Database($pdo);
  $db->setPrimary('user', 'name');
  return $db;
}

function validate_avatar($avatar) {
  return in_array($avatar, $GLOBALS['AVATARS']);
}

function add_log($text) {
  $db = db();
  $row = $db->createRow('log', array(text => $text));
  $db->begin();
  $row->save();
  $db->commit();
}
function add_log_time($text, $time) {
  $db = db();
  $row = $db->createRow('log', array(
    text => $text,
    create_time => $time,
  ));
  $db->begin();
  $row->save();
  $db->commit();
}

function get_user($name) {
  $db = db();
  return $db->table('user', $name);
}

function validate_user($user) {
  if (!$user['name']) {
    raise_e("Error: Please fill in all fields before proceeding.");
  }
  if (!$user['raw_password']) {
    raise_e("Error: Please fill in all fields before proceeding.");
  }
  $len = strlen($user['name']);
  if ($len > 10 || $len < 3 || strpos($user['name'], ' ') !== false) {
    raise_e("Error: Your username must be between 3 - 10 characters with no space.");
  }
  $len = strlen($user['raw_password']);
  if ($len < 5) {
    raise_e("Error: The password must not be shorter than 5 characters due to safety concerns.");
  }
  if (!validate_avatar($user['avatar'])) {
    raise_e("Invalid avatar");
  }
}

function validate_permission($user) {
  if (!$user['name']) {
    raise_e("Error: Please fill in all fields before proceeding.");
  }
  if (!$user['password'] && !$user['raw_password']) {
    raise_e("Error: Please fill in all fields before proceeding.");
  }
  if ($user['raw_password']) {
    $user['password'] = md5($user['raw_password']);
  }

  $existed_user = get_user($user['name']);
  if (!$existed_user || $existed_user['password'] != $user['password']) {
    raise_e('Error: The username or password you entered is incorrect.');
  }
  return $existed_user;
}

function create_user($user) {
  validate_user($user);

  $existed_user = get_user($user['name']);
  if ($existed_user) {
    raise_e('Error: A player with the same username already exists or existed.');
  }

  $db = db();
  $users = $db->user();

  if ($users->where('offline_time', null)->where('ip', $user['ip'])->count() >= IP_LIMIT) {
    raise_e('Error: Each individual IP address is only allowed to possess up to '.IP_LIMIT.' characters.');
  }

  $user['id'] = $users->rowCount();
  $user['password'] = md5($user['raw_password']);
  $row = $db->createRow('user', array(
    'name' => $user['name'],
    'password' => $user['password'],
    'avatar' => $user['avatar'],
    'ip' => $user['ip'],
  ));
  $db->begin();
  $row->save();
  $db->commit();
  add_log('Player ['.$user['name'].'] has joined the game.');

  if ($db->user()->where('offline_time', null)->count() == PLAYER_SLOTS) {
    add_log('Maximum players reached, the countdown timer has been initialized.');
    set_last_check_time();
  }
  return $db->user($user['name']);
}

function login_user($user) {
  $user = validate_permission($user);
  set_cookie('name', $user['name']);
  set_cookie('token', $user['password']);
  return $user;
}
function logout_user() {
  set_cookie('name', null);
  set_cookie('token', null);
}

function can_update_message($existed_user) {
  if (!$existed_user['update_time']) { return true; }
  $span = (new DateTime($existed_user['update_time']))->diff(new DateTime('now'));
  return dateIntervalTimestamp($span) >
    dateIntervalTimestamp(new DateInterval('PT'.MIN_UPDATE_INTERVAL.'H'));
}

function update_message($user) {
  $existed_user = validate_permission($user);
  if (!$user['message']) {
    raise_e("Message can't be empty.");
  }

  if ($existed_user['offline_time']) {
    raise_e("Sorry, your account has been removed from the game for future players.");
  }

  if (!can_update_message($existed_user)) {
    raise_e("Sorry, you should wait ".MIN_UPDATE_INTERVAL." hours before updating.");
  }

  $existed_user->message = substr($user['message'], 0, 140);
  $existed_user->update_time = getDbNow();
  $existed_user->score = $existed_user['score'] + 5;

  $db = db();
  $db->begin();
  $existed_user->save();
  $db->commit();
  add_log('Player ['.$user['name'].'] has updated his/her bio.');
  return $existed_user;
}

function can_update_story($existed_user) {
  if (!$existed_user['story_time']) { return true; }
  $span = (new DateTime($existed_user['story_time']))->diff(new DateTime('now'));
  return dateIntervalTimestamp($span) >
    dateIntervalTimestamp(new DateInterval('PT'.MIN_STORY_INTERVAL.'H'));
}

function update_story($user) {
  $existed_user = validate_permission($user);
  if (!$user['story']) {
    raise_e("Success story can't be empty.");
  }

  if ($existed_user['offline_time']) {
    raise_e("Sorry, your account has been removed from the game for future players.");
  }

  if (!can_update_story($existed_user)) {
    raise_e("Sorry, you should wait ".MIN_STORY_INTERVAL." hours before updating.");
  }

  $existed_user->story = substr($user['story'], 0, 200);
  $existed_user->story_time = getDbNow();
  $existed_user->score = $existed_user['score'] + 3;

  $db = db();
  $db->begin();
  $existed_user->save();
  $db->commit();
  add_log('A new success story has been shared by Player ['.$user['name'].'].');
  return $existed_user;
}

function get_all_users() {
  $db = db();
  $users = $db->user()->fetchAll();

  $users_data = array();
  foreach($users as $u) {
    $data = $u->getData();
    unset($data['password']);
    $data['ip'] = formatIp($data['ip']);
    $users_data[] = $data;
  }
  return $users_data;
}

function offline_user($target_name, $user) {
  validate_permission($user);
  if (!in_array($user['name'], $GLOBALS['ADMINS'])) {
    raise_e('Not Allowed.');
  }

  $target_user = get_user($target_name);
  if (!$target_user) {
    raise_e('No such user');
  }
  $target_user->offline_time = getDbNow();

  $db = db();
  $db->begin();
  $target_user->save();
  $db->commit();
  add_log($target_name.' is deactivated by '.$user['name']);
  return $target_user;
}

function offline_users($target_names, $user) {
  validate_permission($user);
  if (!in_array($user['name'], $GLOBALS['ADMINS'])) {
    raise_e('Not Allowed.');
  }

  $db = db();
  $db->begin();
  foreach($target_names as $target_name) {
    $target_user = get_user($target_name);
    if (!$target_user) {
      raise_e('No user named '.$target_name);
    }
    $target_user->offline_time = getDbNow();
    $target_user->save();
  }
  $db->commit();
  $has = count($target_names) === 1 ? 'has' : 'have';
  add_log(
    'Player ['.implode(' ()], [', $target_names).' ()] '.$has.' been removed from the game by admin to make space for future players'
  );
  return $target_names;
}

function get_log() {
  return db()->log()->fetchAll();
}

function set_last_check_time() {
  $db = db();
  $misc_values = $db->misc()->fetchAll()[0];
  $misc_values->last_checking_time = getDbNow();

  $db->begin();
  $misc_values->save();
  $db->commit();
}

function get_last_check_time() {
  $db = db();
  $misc_values = $db->misc()->fetchAll()[0];
  return $misc_values->last_checking_time;
}

function check_slots() {
  $db = db();
  $misc_values = $db->misc()->fetchAll()[0];
  $last_time = $misc_values['last_checking_time'];
  $last_time = (new DateTime($last_time));
  $span = $last_time->diff(new DateTime('now'));
  $interval_span = new DateInterval('PT'.CHECK_INTERVAL.'H');
  if (
    dateIntervalTimestamp($span) >= dateIntervalTimestamp($interval_span)
  ) {
    $fake_check_time = $last_time->add($interval_span);
    $misc_values->last_checking_time = getDbNow();

    $online_users = $db->user()->where('offline_time', null)->fetchAll();
    $exceeded_count = count($online_users) - PLAYER_SLOTS;
    $selected_users = array();
    $now = getDbNow();
    if ($exceeded_count >= 0) {
      $player_names = array();
      // $selected_indexes = array_rand($online_users, $exceeded_count);
      // if ($exceeded_count == 1) {
      //   $selected_indexes = [$selected_indexes];
      // }
      $selected_indexes = range(0, count($online_users) - 1);
      foreach($selected_indexes as $i) {
        $u = $online_users[$i];
        $u->offline_time = $fake_check_time;
        $selected_users[] = $online_users[$i];
        $player_names[] = $u['name'];
      }
      // add_log_time(
      //   'Player ['.implode('], [', $player_names).'] has been removed from the game by the system to make space for future players',
      //   $fake_check_time
      // );
      add_log_time(
        'All active players have been automatically removed from the game by the system, due to the dereliction of duty of the admins.',
        $fake_check_time
      );
    }

    $db->begin();
    $misc_values->save();
    foreach($selected_users as $u) {
      $u->save();
    }
    $db->commit();
  }
}

check_slots();
?>
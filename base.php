<?php
require_once('./libs/LessQL/Database.php');
require_once('./libs/LessQL/Literal.php');
require_once('./libs/LessQL/Result.php');
require_once('./libs/LessQL/Row.php');
require_once('./libs/utils.php');

date_default_timezone_set('UTC');

define("FILE_LOG", "log.txt");
define('IP_LIMIT', 3);
define('MIN_UPDATE_INTERVAL', 12); // hour
$GLOBALS['AVATARS'] = array('0', '1', '2');
$GLOBALS['ADMINS'] = array('Houmai', 'Zerotonin');

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

function get_user($name) {
  $db = db();
  return $db->table('user', $name);
}

function validate_user($user) {
  return $user['password'] &&
    $user['name'] && $user['message'] &&
    validate_avatar($user['avatar']);
}

function validate_permission($user) {
  $user['password'] = md5($user['password']);

  $existed_user = get_user($user['name']);
  if (!$existed_user || $existed_user['password'] != $user['password']) {
    raise_e('Invalid user.');
  }
}

function create_user($user) {
  if (!validate_user($user)) {
    raise_e('Invalid user.');
  }


  $existed_user = get_user($user['name']);
  if ($existed_user) {
    raise_e('Sorry, username existed.');
  }

  $db = db();
  $users = $db->user();

  if ($users->count("'ip' = '".$user['ip']."'") >= IP_LIMIT) {
    raise_e('Sorry, one IP can only create '.IP_LIMIT.' accounts.');
  }

  $user['id'] = $users->rowCount();
  $user['password'] = md5($user['password']);
  $row = $db->createRow('user', $user);
  $db->begin();
  $row->save();
  $db->commit();
  add_log($user['name'].' joined.');
  return $db->user($user['name']);
}

function update_message($user) {
  validate_permission($user);

  $span = (new DateTime($existed_user['update_time']))->diff(new DateTime('now'));
  if (
    dateIntervalTimestamp($span) <
    dateIntervalTimestamp(new DateInterval('PT'.MIN_UPDATE_INTERVAL.'H'))
  ) {
    raise_e("Sorry, you should wait ".MIN_UPDATE_INTERVAL." hours before updating.");
  }

  $existed_user->message = $user['message'];
  $existed_user->update_time = getDbNow();
  $existed_user->score = $existed_user['score'] + 5;

  $db = db();
  $db->begin();
  $existed_user->save();
  $db->commit();
  add_log($user['name'].' updated her message.');
  return $existed_user;
}

function get_all_users() {
  $db = db();
  return $db->user()->fetchAll();
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
  add_log($target_name.' is never active.');
  return $target_user;
}

function get_log() {
  return db()->log()->fetchAll();
}

?>
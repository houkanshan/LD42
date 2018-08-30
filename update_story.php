<?php
require_once("base.php");

header('Content-Type: application/json');

$user = array(
  'name' => trim($_POST['name'] ? $_POST['name'] : $_COOKIE['name']),
  'raw_password' => $_POST['raw_password'],
  'password' => $_COOKIE['token'],
  'story' => trim($_POST['story']),
);

try {
  $existed_user = validate_permission($user);
  if ($existed_user['offline_time']) {
    redirect('/login.php');
    return;
  }

  $new_user = update_story($user);
  echo json_encode($new_user);
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

redirect('/');

?>
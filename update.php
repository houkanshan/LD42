<?php
require_once("base.php");

header('Content-Type: application/json');

$user = array(
  'name' => trim($_POST['name'] || $_COOKIE['name']),
  'raw_password' => $_POST['raw_password'],
  'password' => $_COOKIE['token'],
  'message' => trim($_POST['message']),
);

try {
  $new_user = update_message($user);
  echo json_encode($new_user);
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

?>
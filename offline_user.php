<?php
require_once("base.php");

header('Content-Type: application/json');

$user = array(
  'name' => trim($_POST['name'] ? $_POST['name'] : $_COOKIE['name']),
  'raw_password' => $_POST['raw_password'],
  'password' => $_COOKIE['token'],
);

try {
  echo json_encode(offline_user($_POST['target_name'], $user));
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

?>
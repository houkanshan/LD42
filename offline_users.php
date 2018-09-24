<?php
require_once("base.php");

header('Content-Type: application/json');

$user = array(
  'name' => trim($_COOKIE['name']),
  'password' => $_COOKIE['token'],
);

try {
  echo json_encode(offline_users($_POST['name'], $user));
} catch (Exception $e) {
  // echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
  echo $e->getMessage();
}

?>
<?php
require_once("base.php");

header('Content-Type: application/json');

$user = array(
  'name' => trim($_POST['name']),
  'password' => $_POST['password'],
);

try {
  echo json_encode(offline_user($_POST['target_name'], $user));
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

?>
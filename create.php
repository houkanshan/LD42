<?php
require_once("base.php");

$ip = get_ip();

header('Content-Type: application/json');

$user = array(
  'name' => trim($_POST['name']),
  'password' => $_POST['password'],
  'message' => trim(str_replace("\n", " ", $_POST['message'])),
  'avatar' => $_POST['avatar'],
  'ip' => $ip,
);

try {
  $new_user = create_user($user);
  echo json_encode($new_user);
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

?>
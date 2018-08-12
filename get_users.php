<?php
require_once("base.php");

header('Content-Type: application/json');

try {
  echo json_encode(get_all_users());
} catch (Exception $e) {
  echo json_encode(array('r' => 1, 'error' => $e->getMessage()));
}

?>
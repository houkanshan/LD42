<?php
require_once("base.php");

$user = array(
  'name' => trim($_COOKIE['name']),
  'password' => $_COOKIE['token'],
);
try {
  login_user($user);
} catch (Exception $e) {
  redirect('/create.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body>
  <form action="./logout.php">
    <button>Logout</button>
  </form>
  <?php include('./include/tail.php') ?>
</body>
</html>
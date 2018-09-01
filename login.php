<?php
require_once("base.php");

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $user = array(
      'name' => trim($_POST['name']),
      'raw_password' => $_POST['raw_password'],
    );
    login_user($user);
    redirect('/');
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
} else {
  // if logged in, redirect to /
  $user = array(
    'name' => trim($_COOKIE['name']),
    'password' => $_COOKIE['token'],
  );
  try {
    $user = login_user($user);
    if ($user['offline_time']) {
      $error = "Sorry, your account has been removed from the game for future players.";
    } else {
      redirect('/');
    }
  } catch (Exception $e) {
    // echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body>
  <div class="account-main">
    <h1 class="login-title">More Players Are Welcomed!</h1>
    <h2 class="login-subtitle">(but Seats Are Limited)</h2>
    <form class="account-form" action="./login.php" method="POST">
      <div class="form-title">Login</div>
      <div class="field-info">
        <div class="field">
          <label class="field-label">Username</label>
          <input type="text" name="name">
        </div>
        <div class="field">
          <label class="field-label">Password</label>
          <input type="text" name="raw_password">
        </div>
      </div>
      <?php if ($error): ?>
        <div class="error">
          <?php echo $error ?>
        </div>
      <?php endif;?>
      <div class="actions">
        <button type="submit">Login</button>
      </div>
    </form>
    <p>
      <a href="./create.php">Create character</a>
    </p>
  </div>

  <?php include('./include/leader-board.php') ?>
  <?php include('./include/tail.php') ?>
  <script>window.initAccountPage()</script>
</body>
</html>
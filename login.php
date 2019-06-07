<?php
require_once("base.php");

$error = '';

$user = array('name' => '');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $user = array(
      'name' => trim($_POST['name']),
      'raw_password' => $_POST['raw_password'],
    );
    $user = login_user($user);
    if ($user['offline_time']) {
      $error = "The character you previously logged into has been removed from the game to make space for future players. You can still rejoin the game by creating a new character.";
    } else {
      redirect('/');
    }
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
      $error = "The character you previously logged into has been removed from the game to make space for future players. \nYou can still rejoin the game by creating a new character.";
    } else {
      redirect('/');
    }
  } catch (Exception $e) {
    // echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en" class="page-login">
<?php include('./include/head.php') ?>
<body>
  <div class="account-main">
    <h1 class="login-title">More Players Are Welcomed!</h1>
    <h2 class="login-subtitle">(but Seats Are Limited)</h2>
    <form class="account-form" action="./login.php" method="POST">
      <div class="form-title">Login with an Existing Character</div>
      <div class="field-info">
        <div class="field">
          <label class="field-label">Username</label>
          <input type="text" name="name" value="<?php echo $user['name'] ?>">
        </div>
        <div class="field">
          <label class="field-label">Password</label>
          <input type="password" name="raw_password">
        </div>
      </div>
      <?php if ($error): ?>
        <div class="error"><?php echo trim($error) ?></div>
      <?php endif;?>
      <div class="actions">
        <button type="submit">Login</button>
      </div>
    </form>
    <p>
      <a href="./create.php">Back to the new character creation page</a>
    </p>
  </div>

  <?php include('./include/copyright.php') ?>
  <?php include('./include/story-board.php') ?>
  <?php include('./include/tail.php') ?>
  <script>window.initAccountPage()</script>
</body>
</html>
<?php
require_once("base.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ip = get_ip();
  $user = array(
    'name' => trim($_POST['name']),
    'raw_password' => $_POST['raw_password'],
    'message' => trim(str_replace("\n", " ", $_POST['message'])),
    'avatar' => $_POST['avatar'],
    'ip' => $ip,
  );
  try {
    $new_user = create_user($user);
    login_user($new_user);
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
    login_user($user);
    redirect('/');
  } catch(Exception $e) {
    // echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body>
  <div class="account-main">
    <h1 class="login-title">More Players Are Welcomed</h1>
    <h2 class="login-subtitle">(but Seats Are Limited)</h2>
    <form class="account-form" action="./create.php" method="POST" autocomplete="off">
        <div class="form-title">Create Your New Character</div>
      <div class="field">
        <label class="field-label">Username</label>
        <input type="text" name="name">
      </div>
      <div class="field">
        <label class="field-label">Password</label>
        <input type="text" name="raw_password">
      </div>
      <div class="field">
        <?php for($i = 0; $i < 2; $i++): ?>
          <label class="field-radio">
            <input type="radio" name="avatar" value="<?php echo $i ?>" <?php echo $i === 0 ? 'checked' : '' ?>>
            <img src="public/avatar/<?php echo $i ?>.png">
          </label>
        <?php endfor; ?>
      </div>
      <div class="field">
        <label class="field-checkbox">
          <input type="checkbox" name="agree" value="1">
        </label>
      </div>
      <?php if ($error): ?>
        <div class="error">
          <?php echo $error ?>
        </div>
      <?php endif;?>
      <div class="field">
        <button type="submit">Create Character</button>
      </div>
    </form>
    <p>
      <a href="./login.php">Login</a>
    </p>
  </div>
  <?php include('./include/tail.php') ?>
</body>
</html>
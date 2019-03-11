<?php
require_once("base.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ip = get_ip();
  $user = array(
    'name' => substr(trim($_POST['name']), 0, 20),
    'raw_password' => $_POST['raw_password'],
    // 'message' => trim(str_replace("\n", " ", $_POST['message'])),
    'avatar' => $_POST['avatar'],
    'ip' => $ip,
  );
  try {
    if (!$user['name']) {
      raise_e("Error: Please fill in all fields before proceeding.");
    }
    if (!$user['password'] && !$user['raw_password']) {
      raise_e("Error: Please fill in all fields before proceeding.");
    }
    if (!$_POST['agree']) {
      throw new Exception('Please tick the checkbox to ensure your optimal gaming experience.');
    }
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
    $user = login_user($user);
    if (!$user['offline_time']) {
      redirect('/');
    }
  } catch(Exception $e) {
    // echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body class="page-create">
  <div class="account-main">
    <h1 class="login-title">More Players Are Welcomed!</h1>
    <h2 class="login-subtitle">(but Seats Are Limited)</h2>
    <form class="account-form" action="./create.php" method="POST" autocomplete="off">
      <div class="form-title">Create Your New Character</div>
        <div class="fieldset">
          <div class="field-avatar">
            <div class="field">
              <label class="field-label">Avatar</label>
              <div class="avatar-selector">
                <div class="avatar-value">
                  <button type="button">▼</button><img
                    class="avatar-img" src="pics/avatars/1.png">
                  <input type="hidden" name="avatar" value="1">
                </div>
                <div class="avatar-options">
                  <?php for($i = 1; $i < 21; $i++): ?>
                    <img class="avatar-img"
                      src="pics/avatars/<?php echo $i ?>.png"
                      data-value="<?php echo $i ?>"
                    >
                  <?php endfor; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="field-info">
            <div class="field">
              <label class="field-label">Username</label>
              <input type="text" name="name" value="<?php echo $user['name'] ?>" max-length="10">
            </div>
            <div class="field">
              <label class="field-label">Password</label>
              <input type="password" name="raw_password">
            </div>
        </div>
        <div class="field field-agree">
          <label class="field-checkbox">
            <input type="checkbox" name="agree" value="1">
            By clicking the checkbox, I agree to unconditionally accept all the terms and
            decisions made by the admins.
          </label>
        </div>
      </div>
      <?php if ($error): ?>
        <div class="error"><?php echo trim($error) ?></div>
      <?php endif;?>
      <div class="actions">
        <button type="submit">Create Character</button>
      </div>
    </form>
    <p>
      <a href="./login.php">Already have an existing character? Login</a>
    </p>
  </div>
  <?php include('./include/copyright.php') ?>
  <?php include('./include/story-board.php') ?>
  <?php include('./include/tail.php') ?>
  <script>window.initAccountPage()</script>
</body>
</html>
<?php
require_once("base.php");

$user = array(
  'name' => trim($_COOKIE['name']),
  'password' => $_COOKIE['token'],
);
try {
  login_user($user);
} catch (Exception $e) {

  error_log(print_r($e->getMessage(), TRUE));
  if ($user['name']) {
    redirect('/login.php');
  } else {
    redirect('/create.php');
  }
}

$user = get_user($user['name']);
if ($user['offline_time']) {
  redirect('/login.php');
  return;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body>
  <div class="main">
  <div class="banner">
    <a href="javascript:window.open('./process.html', null, 'width=300,height=176,resizable,scrollbars=yes,status=1')">
      <img src="pics/banner2.jpg" width="717" height="125">
    </a>
  </div>
  <div class="left">
    <div class="fieldset">
      <label>Figure 1.</label>
      <div class="profile">
        <div class="avatar">
          <img src="pics/avatars/<?php echo $user['avatar'] ?>.png" class="avatar-img">
          <div class="name"><?php echo $user['name'] ?></div>
        </div>
        <div class="info">
          <table>
            <thead>
              <tr>
                <th width="75">Date Joined</th>
                <th width="85">Location</th>
                <th width="43">Level</th>
                <th width="43">Score</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo formatDateShort($user['create_time']) ?></td>
                <td><?php echo formatIp($user['ip']) ?></td>
                <td id="my-level"></td>
                <td id="my-score"></td>
              </tr>
            </tbody>
          </table>
          <p class="bio">
            <b>Bio:</b>
            <br>
            <?php echo $user['message'] ? $user['message'] : 'N/A' ?>
          </p>
        </div>
      </div>
      <div class="actions">
        <div class="buttons">
          <div class="btn-wrapper">
            <button type="button" class="btn-message">Edit My Bio</button>
            <form action="update_message.php" class="message-form" method="POST">
              <textarea name="message" data-min="15" data-max="140"></textarea>
              <div class="action">
                <button type="submit">Submit</button>
                <span class="tip"></span>
              </div>
            </form>
          </div>
          <div class="btn-wrapper">
            <button type="button" class="btn-story">Share My Success Story</button>
            <form action="update_story.php" class="story-form" method="POST">
              <textarea name="story" data-min="15" data-max="200"></textarea>
              <div class="action">
                <button type="submit">Submit</button>
                <span class="tip"></span>
              </div>
            </form>
          </div>
          <form class="form-logout" action="./logout.php">
            <button>Log Out</button>
          </form>
        </div>
        <div class="error" id="profile-error"></div>
      </div>
    </div>
    <div class="fieldset">
      <label>Figure 2.</label>
      <div class="players-list-container">
        <div class="hd">- Current Players List -</div>
        <div class="bd" id="players-list">
        </div>
      </div>
    </div>
    <script type="template" id="tmpl-player-card">
      <div class="player-card">
        <div class="avatar">
          <img src="pics/avatars/{{- avatar }}.png" class="avatar-img">
          <div class="level">Lvl.{{- level }}</div>
        </div>
        <div class="info">
          <div class="hd">
            <b>{{- name }}</b>
            <span class="ip">({{- ip }})</span>
          </div>
          <div class="bd">
            {{- message || 'N/A' }}
          </div>
        </div>
      </div>
    </script>
    <div class="player-slots">
      <span class="label">Player Slots</span>
      <div class="progress">
        <div class="bar"></div>
        <div class="number">0/12</div>
      </div>
    </div>
    <div class="players-countdown" style="display: none">
      <p>Automatic clean-up in <time></time></p>
      <p>All players will be removed by then if no action is taken.</p>
    </div>
  </div>
  <div class="right">
    <div class="fieldset">
      <a class="rules" target="_blank" href="./rules.php">Rules of The Game</a>
      <label>Figure 3.</label>
      <div id="log"></div>
    </div>
  </div>
  </div>

  <?php include('./include/leader-board.php') ?>
  <?php include('./include/copyright.php') ?>

  <script>
    Data = {};
    Data.me = <?php echo json_encode($user) ?>;
    Data.canUpdateMessage = <?php echo json_encode(can_update_message($user)) ?>;
    Data.canUpdateStory = <?php echo json_encode(can_update_story($user)) ?>;
    Data.log = <?php echo json_encode(get_log()) ?>;
    Data.lastCheckTime = '<?php echo get_last_check_time() ?>';
  </script>
  <?php include('./include/tail.php') ?>
  <script>
    window.initMainPage();
  </script>
</body>
</html>
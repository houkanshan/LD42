<?php
require_once("base.php");

$user = array(
  'name' => trim($_COOKIE['name']),
  'password' => $_COOKIE['token'],
);
try {
  login_user($user);
} catch (Exception $e) {
  if ($user['name']) {
    redirect('/login.php');
  } else {
    redirect('/create.php');
  }
}

$user = get_user($user['name']);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('./include/head.php') ?>
<body class="main">
  <div class="banner">
    <span class="close"></span>
  </div>
  <div class="left">
    <div class="fieldset">
      <label>Figure 1.</label>
      <div class="profile">
        <div class="avatar">
          <img src="/pics/avatars/<?php echo $user['avatar'] ?>.png" class="avatar-img">
          <div class="name"><?php echo $user['name'] ?></div>
        </div>
        <div class="info">
          <table>
            <thead>
              <tr>
                <th>Date Joined</th>
                <th width="84">Location</th>
                <th width="38">Level</th>
                <th width="42">Score</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $user['create_time'] ?></td>
                <td><?php echo formatIp($user['ip']) ?></td>
                <td id="my-level"></td>
                <td id="my-score"></td>
              </tr>
            </tbody>
          </table>
          <p class="bio">
            <b>Bio:</b>
            <br>
            <?php echo $user['message'] ?>
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
          <img src="/pics/avatars/{{- avatar }}.png" class="avatar-img">
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
  </div>
  <div class="right">
    <div class="fieldset">
      <a class="rules" href="javascript:;">Rules of The Game</a>
      <label>Figure 3.</label>
      <div id="log"></div>
    </div>
  </div>
  <?php include('./include/tail.php') ?>
  <script>
    Data.me = <?php echo json_encode($user) ?>;
    Data.canUpdateMessage = <?php echo json_encode(can_update_message($user)) ?>;
    Data.canUpdateStory = <?php echo json_encode(can_update_story($user)) ?>;
    Data.log = <?php echo json_encode(get_log()) ?>;
    window.initMainPage();
  </script>
</body>
</html>
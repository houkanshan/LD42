<div id="leader-board">
  <label>Figure 4.</label>
  <div class="table">
    <div class="hd">- Leaderboard -</div>
    <div class="bd leader-board-container"></div>
    <div id="leader-board-update-time"></div>
  </div>
</div>

<script id="tmpl-leader-board-item" type='template'>
  <div class="leader-board-item">
    <div class="rank">{{- index + 1 }}</div>
    <div class="avatar">
      <img src="pics/avatars/{{- avatar }}.png" class="avatar-img">
    </div>
    <div class="player-info">
      <div class="hd"><span class="name">{{- name}}</span> (<span class="ip">{{- ip }}</span>)</div>
      <div class="bd">{{- formatDate(create_date).slice(4) }} — {{- formatDate(offline_date).slice(4) }}</div>
      <div class="ft">
        <div class="btn-message" data-message="{{- message }}">Bio ▼</div>
      </div>
    </div>
    <div class="score-container">
      <div class="hd">Score</div>
      <div class="score">{{- score }}</div>
    </div>
  </div>
</script>

<div id="leader-board">
  <label>Figure 4.</label>
  <div class="table">
    <table>
      <thead>
        <tr>
          <th width="43"></th>
          <th width="47"></th>
          <th></th>
          <th width="71"></th>
        </tr>
      </thead>
      <tbody class="title-container">
        <tr><td colspan="4">
          <span class="title">- Leaderboard -</span>
        </td></tr>
      </tbody>
      <tbody class="leader-board-container">
      </tbody>
    </table>
    <div id="leader-board-update-time"></div>
  </div>
</div>

<script id="tmpl-leader-board-item" type='template'>
  <tr class="leader-board-item">
    <td class="rank" width="43">{{= (index + 1) }}</td>
    <td class="avatar-container" width="45">
      <img src="pics/avatars/{{- avatar }}.png" class="avatar {{- test ? 'it' : '' }}">
    </td>
    <td class="info">
      <div class="player-info">
        <div class="hd"><span class="name">{{- name}}</span> (<span class="ip">{{- ip }}</span>)</div>
        <div class="bd">{{- formatDate(create_date).slice(4) }} — {{- formatDate(update_date).slice(4) }}</div>
        <div class="ft">
          <div class="btn-message" data-message="{{- message }}">Bio ▼</div>
        </div>
      </div>
    </td>
    <td class="score-container">
      <div class="hd">Score</div>
      <div class="score">{{- score }}</div>
    </td>
  </tr>
</script>

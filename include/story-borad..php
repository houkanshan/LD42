<div class="leader-board-container">
  <div id="leader-board">
    <span class="close"></span>
    <table>
      <thead>
        <tr><th>
          <span class="title">- Success Stories Shared by Other Players -</span>
          <br>
          (xxx)
        </th></tr>
      </thead>
      <tbody class="leader-board-container">
      </tbody>
    </table>
  </div>
</div>

<script id="tmpl-leader-board-item" type='template'>
  <tr class="leader-board-item"><td>
    <img src="pics/avatars/{{- avatar }}.png" class="avatar {{- test ? 'it' : '' }}">
    <div class="player-info">
      <div class="hd">{{- name}} (Lv. {{- level}}) {{- update_time }} (UTC)</div>
      <div class="bd message">{{- story }}</div>
    </div>
  </td></tr>
</script>

<div class="story-board-wrapper">
  <div id="story-board">
    <a class="da-knil" href="/centrifuge/LD41/" target="_blank"></a>
    <table>
      <thead>
        <tr><th>
          <span class="title">- Success Stories Shared by Other Players -</span>
          <br>
          (These are all real quotes from real players with real feelings)
        </th></tr>
      </thead>
      <tbody class="story-board-container">
      </tbody>
    </table>
  </div>
</div>

<script id="tmpl-story-board-item" type='template'>
  <tr class="story-board-item"><td>
    <img src="pics/avatars/{{- avatar }}.png" class="avatar {{- test ? 'it' : '' }}">
    <div class="player-info">
      <div class="hd"><span class="name">{{- name}}</span> (Lv. {{- level}}) {{- formatDate(update_time) }}</div>
      <div class="bd message">{{- story }}</div>
    </div>
  </td></tr>
</script>

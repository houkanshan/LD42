import * as $ from 'jquery'
import template from './template'
import './avatar'
declare const Data: any

function initAccountPage() {
  const tmplLeaderBoardItem = template($('#tmpl-leader-board-item').html())
  const listHtml = Data.users
  .filter(u => u.story)
  .sort((a, b) => a.score - b.score)
  .map(function(u) {
    return tmplLeaderBoardItem(u)
  }).join('')
  $('.leader-board-container').html(listHtml)
  $('#leader-board').on('click', '.close', function(e) {
    $(e.currentTarget).closest('#leader-board').addClass('closed')
  })
}

(window as any).initAccountPage = initAccountPage
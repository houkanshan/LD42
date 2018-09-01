import * as $ from 'jquery'
import template from './template'
import './avatar'
declare const Data: any

const tmplLeaderBoardItem = template($('#tmpl-leader-board-item').html())
const listHtml = Data.users
.filter(u => !!u.offline_time)
.sort((a, b) => b.score - a.score)
.slice(0, 9)
.map(function(u, index) {
  u.index = index
  return tmplLeaderBoardItem(u)
}).join('')

$('.leader-board-container').html(listHtml)
$('#leader-board').on('click', '.close', function(e) {
  $(e.currentTarget).closest('#leader-board').addClass('closed')
})

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

const container = $('.leader-board-container')
container
  .html(listHtml)
  .on('mouseenter', '.btn-message', function(e) {
    const target = $(e.target)
    const text = target.data('message')
    const offset = target.offset()
    const message = $('<div class="message-content">')
      .text(text || 'N/A')
      .css({
        top: offset.top,
        left: offset.left + target.width() - 250,
        width: 240,
      }).appendTo(document.body)
    target.data('popup', message)
  })

$(document.body).on('mouseleave', '.message-content', function(e) {
  $(e.currentTarget).remove()
})

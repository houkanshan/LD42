import * as $ from 'jquery'
import template from './template'
import formatDate from './formatDate'
import './avatar'
declare const Data: any

export default function() {
  const tmplLeaderBoardItem = template($('#tmpl-leader-board-item').html())
  const offlineUsers = Data.users
    .filter(u => !!u.offline_time)
  const listHtml = offlineUsers
    .sort((a, b) => b.score - a.score)
    .slice(0, 9)
    .map(function(u, index) {
      u.index = index
      return tmplLeaderBoardItem({ ...u, formatDate })
    }).join('')

  const container = $('.leader-board-container')
  container
    .html(listHtml)

  $('#leader-board-update-time').text(
    formatDate(offlineUsers.sort((a, b) =>
      b.offline_time === a.offline_time ? 0 :
        b.offline_time > a.offline_time ? 1 : -1
    )[0].offline_time) + ' +0000'
  )

  container
    .on('mouseenter', '.btn-message', function(e) {
      const target = $(e.target)
      const text = target.data('message')
      const offset = target.offset()
      const message = $('<div class="message-content">')
        .text(text || 'N/A')
        .css({
          top: offset.top,
          left: offset.left + target.outerWidth() - 250,
          width: 240,
        }).appendTo(document.body)
      target.data('popup', message)
    })
    .on('mouseleave', '.btn-message', function(e) {
      if ($(e.relatedTarget).closest('.message-content').length) {
        return
      }
      const popup = $(e.currentTarget).data('popup')
      if (popup) {
         popup.remove()
      }
    })

  $(document.body).on('mouseleave', '.message-content', function(e) {
    $(e.currentTarget).remove()
  })
}
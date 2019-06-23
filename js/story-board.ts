import * as $ from 'jquery'
import template from './template'
import formatDate from './formatDate'
import './avatar'
declare const Data: any

export default function() {

  const tmplStoryBoardItem = template($('#tmpl-story-board-item').html())
  const listHtml = Data.users
  .filter(u => u.story)
  .sort((a, b) => a.story_time > b.story_time ? -1 : 1)
  .slice(0, 10)
  .map(function(u) {
    return tmplStoryBoardItem({ ...u, formatDate })
  }).join('')

  const container = $('.story-board-container')
  container.html(listHtml)
  $('#story-board').on('click', '.close', function(e) {
    $(e.currentTarget).closest('#story-board').addClass('closed')
  })

  container.find('.story-board-item').each(function(i, _el) {
    const el = $(_el)
    const textEl = el.find('.message')[0]
    if (textEl.offsetWidth < textEl.scrollWidth) {
      el.addClass('need-expand')
      el.on('mouseenter', '.message', function(e) {
        const target = $(e.target)
        const text = textEl.textContent
        const offset = target.offset()
        const message = $('<div class="message-content">')
          .text(text || 'N/A')
          .css({
            top: offset.top,
            left: offset.left + target.width() - 312,
            width: 307,
          }).appendTo(document.body)
        target.data('popup', message)
      })
    }
  })

  $(document.body).on('mousemove', function(e) {
    const el = document.elementFromPoint(e.clientX, e.clientY)
    const isIn = $(el).closest('.message-content').length
    if (!isIn) {
      $('.message-content').remove()
    }
  })
}

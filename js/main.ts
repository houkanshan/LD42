import * as $ from 'jquery'
import template, { escape } from './template'
import initLeaderBoard from './leader-board'
declare const Data: any

const messageTips = [
  'You may only edit your bio once per 12 hours.',
  'Do not reveal your password to others or allow others to access your account.',
  'Do not reveal your password to others even if requested by authorized admin(s).',
  'Sauce up your bio info to display your best self.',
  'You may also communicate with others or advertise yourself through your bio info.',
  'Be friendly to others and pay attention to them.',
  'Do not play video games at your workplace.',
  'Be yourself, don\'t care what others think about you.',
  'The best way to introduce yourself is to keep it short and succinct.',
  'You may only edit your bio once per 12 hours.',
]

const storyTips = [
  'You may only edit your shared success story once per 6 hours.',
  'Believe in yourself, everyone’s a winner.',
  'You are more than you might have thought.',
  'We promise that we won’t use your success stories for anything other than inspiring other players.',
  'Sharing is caring.',
  'If you look close enough, every moment is a success.',
  'Being human, being alive and staying alive, is a kind of success.',
  'Get rid of all the distractions which might prevent you from achieving your goal, such as video games.',
  'Don’t be a spammer, say something constructive.',
  'You may only edit your shared success story once per 6 hours.',
]

function updateTips() {
  $('.message-form .tip').text(messageTips[Math.floor(Math.random() * messageTips.length)])
  $('.story-form .tip').text(storyTips[Math.floor(Math.random() * storyTips.length)])
}


function initMainPage() {

  const tmplPlayerCardItem = template($('#tmpl-player-card').html())
  const userMap = Data.users.reduce((prev, next) => ({ ...prev, [next.name]: next}), {})

  const activePlayers = Data.users
    .filter(u => !u.offline_time)
  const listHtml = activePlayers
    .sort((a, b) => a.score - b.score)
    .map(function(u) {
      return tmplPlayerCardItem(u)
    }).join('')
  $('#players-list').html(listHtml)

  // players
  const playerCount = activePlayers.length
  $('.player-slots .bar')
    .width(`${100 * Math.min(playerCount, 12) / 12}%`)
    .css(
      'background-color',
      playerCount < 8 ? 'green' : playerCount < 12 ? 'yellow' : 'red'
    )
  $('.player-slots .number').text(`${playerCount} / 12`)
  // players count down
  const lastCheckTime = new Date(Data.lastCheckTime.replace(' ', 'T') + '.000Z')
  const cleanTime = lastCheckTime.valueOf() + 48 * 60 * 60 * 1000
  const timeEl = $('.players-countdown time')[0]
  function padZero(n) { return (n/100).toFixed(2).slice(2) }
  function tick() {
    const span = Math.max(0, cleanTime - Date.now())
    const spanDate = new Date(span)
    timeEl.textContent = [
      Math.floor(span/3600/1000),
      padZero(spanDate.getMinutes()),
      padZero(spanDate.getSeconds()) + '.' +
      spanDate.getMilliseconds(),
    ].join(':')
    setTimeout(tick, 54)
  }
  if (playerCount >= 12) {
    tick()
    $('.players-countdown').show()
  }

  // Log
  $('#log').html(Data.log.reverse().map(function(l) {
    let text = escape(l.text).replace(/\[(.+?)( \(\))?\]/g, function(m, p1, p2) {
      if (!userMap[p1]) { return `<b>[${p1}]</b>`}
      if (p2) {
        return `<b>[${p1} (Lv.${userMap[p1].level})]</b>`
      }
      return `<b>[${p1}]</b>`
    })
    return `[${l.create_time}] ${text}`
  }).join('\n'))
  // $('#log')[0].scrollTop = $('#log')[0].scrollHeight

  // Profile
  $('#my-level').text(Data.me.level)
  $('#my-score').text(Data.me.score)

  // update message / story
  $('.btn-message').on('click', function(e) {
    const target = $(e.target)
    if (Data.canUpdateMessage) {
      setTimeout(function() {
        target.closest('.btn-wrapper').addClass('is-open')
      }, 1)
    } else {
      $('#profile-error').text('Sorry, you can only edit your bio once per 12 hours')
    }
  })
  $('.btn-story').on('click', function(e) {
    const target = $(e.target)
    if (Data.canUpdateStory) {
      target.closest('.btn-wrapper').addClass('is-open')
    } else {
      $('#profile-error').text('Sorry, you can only share your success story once per 6 hours')
    }
  })

  updateTips()
  $(document).on('click', function(e) {
    if ($(e.target).closest('.is-open').length) { return }
    $('.is-open').removeClass('is-open')
    updateTips()
  })

  // Word count
  $('textarea').each(function(i, _el) {
    const el = $(_el)
    const countEl = $('<span class="count">')
    const min = el.data('min')
    const max = el.data('max')
    const form = el.closest('form')
    const btn = form.find('button')
    el.after(countEl)
    el.on('input', function() {
      const len = el.val().length
      if (len < min) {
        countEl.show().text('+' + (min - len))
        btn.prop('disabled', true)
      } else if (len > max) {
        countEl.show().text('-' + (len - max))
        btn.prop('disabled', true)
      } else {
        countEl.hide()
        btn.prop('disabled', false)
      }
    }).trigger('input')
  })

  // Log out
  $('.form-logout').on('submit', function(e) {
    if (!confirm('Are you sure you want to log out?')) {
      e.preventDefault()
    }
  })


  initLeaderBoard()
}

(window as any).initMainPage = initMainPage
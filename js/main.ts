import * as $ from 'jquery'
import template from './template'
import parseUser from './parseUser'
declare const Data: any

function initMainPage() {
  Data.users = Data.users.map(parseUser)
  Data.me = parseUser(Data.me)

  const tmplPlayerCardItem = template($('#tmpl-player-card').html())
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

  // Log
  $('#log').text(Data.log.map(function(l) {
    return `[${l.create_time}] ${l.text}`
  }).join('\n'))

  // Profile
  $('#my-level').text(Data.me.level)
  $('#my-score').text(Data.me.score)
}

(window as any).initMainPage = initMainPage
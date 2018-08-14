import * as $ from 'jquery'
import template from './template'
declare const Data: any

function initMainPage() {
  const tmplPlayerCardItem = template($('#tmpl-player-card').html())
  const listHtml = Data.users
  .filter(u => u.offline_time)
  .filter(u => u.message)
  .sort((a, b) => a.score - b.score)
  .map(function(u) {
    return tmplPlayerCardItem(u)
  }).join('')
  $('#players-list').html(listHtml)
}

(window as any).initMainPage = initMainPage
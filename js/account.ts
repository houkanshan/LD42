import * as $ from 'jquery'
import template from './template'
import initStoryBoard from './story-board'
declare const Data: any

function initAccountPage() {
  initStoryBoard()
}

(window as any).initAccountPage = initAccountPage
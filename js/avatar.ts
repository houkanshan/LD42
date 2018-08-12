import * as $ from 'jquery'
const doc = $(document)

doc.on('click', '.avatar-value button', function(e) {
  const avatarSelector = $(e.currentTarget).closest('.avatar-selector')
  avatarSelector.addClass('focused')
  doc.one('click', function() {
    avatarSelector.removeClass('focused')
  })
})

doc.on('click', '.avatar-options img', function(e) {
  const avatarImg = $(e.currentTarget)
  const value = avatarImg.data('value')
  const src = avatarImg.attr('src')
  console.log(value, src)

  const avatarValue = avatarImg.closest('.avatar-selector').find('.avatar-value')
  avatarValue.find('img').attr('src', src)
  avatarValue.find('input').val(value)
})
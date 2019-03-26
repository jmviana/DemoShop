window.onhashchange = function(e) {
    const oldURL = e.oldURL.split('#')[1] || 'home'
    const newURL = e.newURL.split('#')[1]
    const oldLink = document.querySelector(`.menu [href='#${oldURL}']`)
    const newLink = document.querySelector(`.menu [href='#${newURL}']`)
    oldLink && oldLink.classList.remove('selected')
    newLink && newLink.classList.add('selected')
}
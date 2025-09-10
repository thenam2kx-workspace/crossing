(function () {
  // make entire card clickable while keeping link accessible
  document.addEventListener('click', function (e) {
    var card = e.target.closest('.wg-card');
    if (!card) return;
    // if clicked on interactive elements, skip
    var tag = e.target.tagName.toLowerCase();
    if (['a', 'button', 'svg', 'path', 'input'].indexOf(tag) !== -1) return;
    var link = card.querySelector('.wg-card-link');
    if (link) window.location = link.href;
  });
})();
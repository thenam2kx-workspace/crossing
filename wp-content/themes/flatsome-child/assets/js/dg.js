(function () {
  document.addEventListener('click', function (e) {
    var card = e.target.closest('.dg-card');
    if (!card) return;
    var tag = e.target.tagName.toLowerCase();
    if (['a', 'button', 'svg', 'path', 'input'].indexOf(tag) !== -1) return;
    var link = card.querySelector('a.dg-card-link');
    if (link) window.location = link.href;
  });
})();
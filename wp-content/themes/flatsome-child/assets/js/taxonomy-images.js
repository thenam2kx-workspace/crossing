jQuery(document).ready(function($){
  var frame;
  var $btn = $('#nm-add-trip-images');
  var $container = $('#nm-selected-trip-images');
  var $input = $('#nm-trip-images-ids');

  function refreshInput(){
    var ids = [];
    $container.find('.nm-thumb').each(function(){
      ids.push( $(this).attr('data-id') );
    });
    $input.val( ids.join(',') );
  }

  // Open media frame
  $btn.on('click', function(e){
    e.preventDefault();
    if ( frame ) { frame.open(); return; }

    frame = wp.media({
      title: 'Chọn ảnh cho term',
      button: { text: 'Chọn ảnh' },
      multiple: true
    });

    frame.on('select', function(){
      var selection = frame.state().get('selection');
      selection.map( function( attachment ) {
        attachment = attachment.toJSON();
        // render thumb only if not exists
        if ( $container.find('.nm-thumb[data-id="'+attachment.id+'"]').length === 0 ) {
          var thumb = '<div class="nm-thumb" data-id="'+attachment.id+'" style="display:inline-block;margin-right:8px;position:relative;">';
          thumb += '<img src="'+attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url +'" style="width:80px;height:80px;object-fit:cover;border-radius:6px;" />';
          thumb += '<button class="nm-remove-image" style="position:absolute;right:2px;top:2px;">×</button>';
          thumb += '</div>';
          $container.append( thumb );
        }
      });
      refreshInput();
    });

    frame.open();
  });

  // Remove via delegated click
  $container.on('click', '.nm-remove-image', function(e){
    e.preventDefault();
    $(this).closest('.nm-thumb').remove();
    refreshInput();
  });

  // Optional: make thumbnails sortable (jQuery UI)
  if ( $.fn.sortable ) {
    $container.sortable({
      items: '.nm-thumb',
      update: function() { refreshInput(); }
    });
  }

});

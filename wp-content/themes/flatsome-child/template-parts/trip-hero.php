<?php
$term = get_queried_object();
if ( ! ( $term && ! empty( $term->term_id ) ) ) {
    return;
}

// Lấy meta (IDs hoặc URL)
$raw_images = get_term_meta( $term->term_id, 'trip_type_images', true ) ? get_term_meta( $term->term_id, 'trip_type_images', true ) : get_term_meta( $term->term_id, 'destination_images', true );

// Normalise về mảng
$images = array();
if ( is_array( $raw_images ) ) {
    $images = $raw_images;
} elseif ( is_string( $raw_images ) && $raw_images !== '' ) {
    if ( strpos( $raw_images, ',' ) !== false ) {
        $images = array_map( 'trim', explode( ',', $raw_images ) );
    } else {
        $images = array( $raw_images );
    }
}

// Chuyển thành items['img']
$items = array();
foreach ( $images as $val ) {
    if ( empty( $val ) ) continue;
    if ( ctype_digit( (string) $val ) ) {
        $id = intval( $val );
        $img_url = wp_get_attachment_image_url( $id, 'large' );
        $items[] = $img_url;
    } else {
        $items[] = esc_url( $val );
    }
}

?>

<div class="trip-hero-wrap" aria-hidden="false">
  <div class="trip-hero-grid">
    <?php if(count($items) > 0): for ( $i = 0; $i <= count($items); $i++ ) :
        $img = isset( $items[$i] ) ? $items[$i] : '';
      ?>
        <?php if ( empty( $img ) ) continue; ?>
        <div class="item">
          <img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
        </div>
      <?php endfor; endif; ?>
  </div>
</div>

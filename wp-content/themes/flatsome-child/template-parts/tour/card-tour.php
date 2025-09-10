<?php
/**
 * template-parts/tour/card-tour.php
 * Reusable ctp card template.
 *
 * Expects $tour_id via set_query_var('tour_id', $id) OR uses current post ID.
 */

$tour_id = get_query_var('tour_id');

if ( empty( $tour_id ) ) {
    $tour_id = get_the_ID();
}
if ( empty( $tour_id ) ) {
    return;
}

$post = get_post( (int) $tour_id );
if ( ! $post ) return;

setup_postdata( $post );

// Background image
if ( has_post_thumbnail( $post->ID ) ) {
    $bg = get_the_post_thumbnail_url( $post->ID, 'large' );
} else {
    $bg = get_stylesheet_directory_uri() . '/assets/images/no-image-700x450.png';
}

// Location
$location = '';
$taxes = array('destination','destinations','trip_destination','wte_destination');
foreach($taxes as $tx){
    $terms = wp_get_post_terms($id, $tx);
    if (!is_wp_error($terms) && !empty($terms)) { $location = $terms[0]->name; break; }
}
if ( empty($location) ) $location = get_post_meta($id, 'location', true) ?: '';

// Duration
$duration = 24;
$get_hours = get_post_meta($id, 'wp_travel_engine_setting_trip_duration', true) ?: '24';
$duration = hours_to_days($get_hours);
if ( empty($duration) ) $duration = '— days';

// Prices
$regular = 0;
$sale    = 0;
$regular = (float) get_post_meta( $post->ID, 'wp_travel_engine_setting_trip_actual_price', true );
$sale    = (float) get_post_meta( $post->ID, 'wp_travel_engine_setting_trip_price', true );
$old_price_str = '';
$price_str     = '';
if ( $sale > 0 ) {
    $price_str = '$' . number_format( $sale, 0, '.', ',' );

    if ( $regular > $sale ) {
        $old_price_str = '$' . number_format( $regular, 0, '.', ',' );
    }
} elseif ( $regular > 0 ) {
    $price_str = '$' . number_format( $regular, 0, '.', ',' );
} else {
    $price_str = 'Contact';
}

// Discount
$discount_val = 0;
if ( function_exists( 'get_trip_discount_percent' ) ) {
    $discount_val = (int) get_trip_discount_percent( $post->ID );
} else {
    if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
        $discount_val = (int) round((($regular - $sale) / $regular) * 100);
    }
}
$discount = $discount_val ? '-' . intval( $discount_val ) . '%' : '';

// Rating & reviews
$rating = intval( get_post_meta( $post->ID, 'rating', true ) ) ?: 5;
$reviews = intval( get_post_meta( $post->ID, 'reviews_count', true ) ) ?: 250;
?>

<article class="ctp-card" style="background-image:url('<?php echo esc_url( $bg ); ?>');">
    <?php if ( $discount ): ?>
        <div class="ctp-badge"><?php echo $discount; ?></div>
    <?php endif; ?>

    <div class="ctp-bookmark" title="<?php esc_attr_e( 'Bookmark', 'your-textdomain' ); ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#ff5a2b" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h12v20l-6-3-6 3V2z"/></svg>
    </div>

    <a class="ctp-card-link ctp-card-inner" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
        <div class="ctp-location"><span class="ctp-brand-dot"></span><span><?php echo esc_html( $location ); ?></span></div>

        <div class="ctp-content">
            <h3 class="ctp-title"><?php echo wp_kses_post( wp_trim_words( get_the_title( $post ), 12, '...' ) ); ?></h3>

            <div class="ctp-meta">
                <div class="ctp-duration">
                    <span class="ctp-duration-value"><?php echo intval( $duration ); ?> days |</span>
                    <span class="ctp-from">from</span>
                    <div class="ctp-price-wrap">
                        <?php if ( $old_price_str ): ?>
                            <span class="ctp-old-price"><?php echo esc_html( $old_price_str ); ?></span>
                        <?php endif; ?>
                        <span class="ctp-price"><?php echo esc_html( $price_str ); ?></span>
                    </div>
                </div>

                <div class="ctp-rating">
                    <div class="ctp-stars" aria-hidden="true">
                        <?php
                        $stars = intval( round( $rating ) );
                        for ( $i = 1; $i <= 5; $i++ ) {
                            if ( $i <= $stars ) {
                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#ff6a38" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                            } else {
                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                            }
                        }
                        ?>
                    </div>
                    <div class="ctp-reviews" style="font-size:16px; color:rgba(255,255,255,0.95);"><?php echo intval( $reviews ); ?> reviews</div>
                </div>
            </div>
        </div>

        <div class="ctp-card-shadow" style="background-image:url('<?php echo esc_url( $bg ); ?>');"></div>
    </a>
</article>

<?php
wp_reset_postdata();

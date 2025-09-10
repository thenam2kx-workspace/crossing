<?php
function hours_to_days($hours, $rounding = 'ceil') {
    $hours = (float) $hours;
    if ($hours < 0) $hours = 0.0;

    $days = $hours / 24.0;

    switch (strtolower($rounding)) {
        case 'floor':
            return (int) floor($days);
        case 'round':
            return (int) round($days);
        case 'exact':
            return $days; // float
        case 'ceil':
        default:
            return (int) ceil($days);
    }
}

function get_trip_discount_percent($trip_id) {
    $regular_price = (float) get_post_meta($trip_id, 'wp_travel_engine_setting_trip_actual_price', true);
    $sale_price    = (float) get_post_meta($trip_id, 'wp_travel_engine_setting_trip_price', true);

    if ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price) {
        return round((($regular_price - $sale_price) / $regular_price) * 100);
    }

    return 0;
}

function trip_grid_wte_shortcode($atts){
    $a = shortcode_atts(array(
        'number' => 8,
        'cols' => 4,
        'title' => 'Best-selling package tours',
        'subtitle' => '',
        'view_all_url' => '#',
    ), $atts, 'trip_grid_wte');

    // enqueue and inline assets
    wp_enqueue_style('wg-trip-grid-style');
    wp_enqueue_script('wg-trip-grid-script');

    // Query trips (post type = 'trip')
    $args = array(
        'post_type' => 'trip',
        'posts_per_page' => intval($a['number']),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $q = new WP_Query($args);

    ob_start();
    ?>
    <section class="wg-trip-section">
        <div class="wg-trip-header">
            <div>
                <h2 class="wg-trip-title"><?php echo esc_html($a['title']); ?></h2>
                <?php if(!empty($a['subtitle'])): ?><div class="wg-trip-sub"><?php echo esc_html($a['subtitle']); ?></div><?php endif; ?>
            </div>
            <div style="display:flex; align-items:center; gap:18px;">
                <a class="wg-view-all" href="<?php echo esc_url($a['view_all_url']); ?>" style="color:#ff5a2b; font-weight:700; text-decoration:none;">View All</a>
            </div>
        </div>

        <div class="wg-grid" data-cols="<?php echo intval($a['cols']); ?>">
            <?php if($q->have_posts()): while($q->have_posts()): $q->the_post();
                $id = get_the_ID();

                // Featured image (fallback)
                if ( has_post_thumbnail($id) ) {
                    $bg = get_the_post_thumbnail_url($id, 'large');
                } else {
                    $bg = 'https://via.placeholder.com/1200x800?text=Trip+Image';
                }

                // LOCATION: try taxonomy 'destination' or meta name
                $location = '';
                $taxes = array('destination','destinations','trip_destination','wte_destination');
                foreach($taxes as $tx){
                    $terms = wp_get_post_terms($id, $tx);
                    if (!is_wp_error($terms) && !empty($terms)) { $location = $terms[0]->name; break; }
                }
                if ( empty($location) ) $location = get_post_meta($id, 'location', true) ?: '';

                // DURATION: try common meta keys
                $get_hours = get_post_meta($id, 'wp_travel_engine_setting_trip_duration', true) ?: '24';
                $duration = hours_to_days($get_hours);
                if ( empty($duration) ) $duration = '— days';

                // PRICING: WP Travel Engine usually stores prices in 'wp_travel_engine_prices' meta (array)
                $price = '';
                $old_price = '';
                $prices_meta = get_post_meta($id, 'wp_travel_engine_prices', true);

                if ( !empty($prices_meta) ) {
                    // if serialized string, try to unserialize / json decode
                    if ( is_string($prices_meta) ) {
                        $maybe = @unserialize($prices_meta);
                        if ( $maybe === false && json_decode($prices_meta, true) ) {
                            $maybe = json_decode($prices_meta, true);
                        }
                        if ( $maybe ) $prices_meta = $maybe;
                    }
                    if ( is_array($prices_meta) ) {
                        // many installs store array of options. Try to find primary price
                        $found = null;
                        foreach($prices_meta as $p){
                            // typical keys: 'price', 'sale_price', 'is_primary', 'primary'
                            if ( is_array($p) && (isset($p['is_primary']) && $p['is_primary']) ) { $found = $p; break; }
                        }
                        if (!$found) { $found = reset($prices_meta); }
                        if ( is_array($found) ) {
                            if ( isset($found['sale_price']) && $found['sale_price'] !== '' ) {
                                $price = floatval(str_replace(array(',','$'), '', $found['sale_price']));
                                if ( isset($found['price']) ) $old_price = floatval(str_replace(array(',','$'), '', $found['price']));
                            } else if ( isset($found['price']) ) {
                                $price = floatval(str_replace(array(',','$'), '', $found['price']));
                            } else if ( isset($found['regular_price']) ) {
                                $price = floatval(str_replace(array(',','$'), '', $found['regular_price']));
                                if ( isset($found['sale_price']) ) $old_price = floatval(str_replace(array(',','$'), '', $found['sale_price']));
                            }
                        }
                    }
                }

                // fallback: direct meta keys
                if (empty($price)) {
                    $fallback_keys = array('price','_s_price','wte_price','regular_price','_regular_price');
                    foreach($fallback_keys as $k){ $v = get_post_meta($id, $k, true); if($v !== ''){ $price = floatval(str_replace(array(',','$'), '', $v)); break; } }
                }
                if (empty($old_price)) {
                    $fallback_old = array('wp_travel_engine_setting_trip_actual_price','sale_price','trip_old_price','_sale_price');
                    foreach($fallback_old as $k){ $v = get_post_meta($id, $k, true); if($v !== ''){ $old_price = floatval(str_replace(array(',','$'), '', $v)); break; } }
                }

                // Discount label
                $discount = get_trip_discount_percent($id) ? '-'.get_trip_discount_percent($id).'%' : '';

                // ratings & reviews - WPTE may store reviews; fallback to comment count
                $reviews = get_post_meta($id, 'trip_reviews_count', true) ?: get_comments_number($id);
                $rating = get_post_meta($id, 'trip_rating', true) ?: 5;

                // format price strings
                $price_str = $price ? ('$'.number_format((float)$price)) : __('Contact', 'textdomain');
                $old_price_str = $old_price ? ('$'.number_format((float)$old_price)) : '';

                ?>
                <article class="wg-card" style="background-image:url('<?php echo esc_url($bg); ?>');">
                    <?php if($discount): ?><div class="wg-badge"><?php echo $discount; ?></div><?php endif; ?>
                    <div class="wg-bookmark" title="Bookmark">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#ff5a2b" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h12v20l-6-3-6 3V2z"/></svg>
                    </div>

                    <a class="wg-card-link wg-card-inner" href="<?php the_permalink(); ?>">
                        <div class="wg-location"><span class="wg-brand-dot"></span><span><?php echo esc_html($location ?: get_the_title()); ?></span></div>
                        <div class="wg-content">
                            <h3 class="wg-title"><?php echo wp_trim_words(get_the_title(), 12, '...'); ?></h3>

                            <div class="wg-meta">
                                <div class="wg-duration">
                                    <span class="wg-duration-value"><?php echo $duration . ' days |'; ?></span>
                                    <span class="wg-from">from</span>
                                    <div class="wg-price-wrap">
                                        <?php if($old_price_str): ?><span class="wg-old-price"><?php echo esc_html($old_price_str); ?></span><?php endif; ?>
                                        <span class="wg-price"><?php echo esc_html($price_str); ?></span>
                                    </div>
                                </div>
                                <div class="wg-rating">
                                    <div class="wg-stars" aria-hidden="true">
                                        <?php
                                        $stars = intval(round($rating));
                                        for($i=1;$i<=5;$i++){
                                            if($i <= $stars) {
                                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#ff6a38" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                                            } else {
                                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div style="font-size:16px; color:rgba(255,255,255,0.95);"><?php echo intval($reviews); ?> reviews</div>
                                </div>
                            </div>
                        </div>
                        <div class="wg-card-shadow" style="background-image:url('<?php echo esc_url($bg); ?>');"></div>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); else: ?>
                <div><?php _e('No trips found','textdomain'); ?></div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('trip_grid_wte', 'trip_grid_wte_shortcode');

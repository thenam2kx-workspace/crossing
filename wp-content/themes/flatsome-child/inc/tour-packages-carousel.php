<?php
function format_hours_to_days_nights($hours, $rounding = 'ceil', $night_policy = 'days_minus_one') {
    // sanitize
    $hours = (float) $hours;
    if ($hours < 0) $hours = 0.0;

    $exact_days = $hours / 24.0;

    // days theo chế độ làm tròn
    switch (strtolower($rounding)) {
        case 'floor':
            $days_value = (int) floor($exact_days);
            break;
        case 'round':
            $days_value = (int) round($exact_days);
            break;
        case 'exact':
            $days_value = $exact_days; // float
            break;
        case 'ceil':
        default:
            $days_value = (int) ceil($exact_days);
            break;
    }

    // nights theo chính sách
    $policy = strtolower($night_policy);
    if ($policy === 'floor_hours') {
        $nights_value = (int) floor($hours / 24.0);
    } elseif ($policy === 'exact') {
        $nights_value = max($exact_days - 1.0, 0.0);
    } else { // days_minus_one
        // nếu days_value là float (exact), ta tính nights tương ứng (float), còn lại int
        if (is_float($days_value)) {
            $nights_value = max($days_value - 1.0, 0.0);
        } else {
            $nights_value = max((int)$days_value - 1, 0);
        }
    }

    // để hiển thị người dùng thường muốn số nguyên; nếu là float (exact) -> format 2 chữ số thập phân
    if (is_float($days_value) && floor($days_value) != $days_value) {
        $days_str = rtrim(rtrim(number_format($days_value, 2, '.', ''), '0'), '.');
    } else {
        $days_str = (int) $days_value;
    }

    if (is_float($nights_value) && floor($nights_value) != $nights_value) {
        $nights_str = rtrim(rtrim(number_format($nights_value, 2, '.', ''), '0'), '.');
    } else {
        $nights_str = (int) $nights_value;
    }

    return "{$days_str} days, {$nights_str} night";
}

function tour_packages_carousel_shortcode($atts){
    $a = shortcode_atts( array(
        'number' => 6,
        'post_type' => 'trip',
        'title' => 'Best-selling package tours with seasonal promotions',
        'subtitle' => 'Let our amazing customized packages ignite your senses. Our designs, your experiences!',
        'view_all_url' => '#',
    ), $atts, 'tour_packages_carousel' );

    wp_enqueue_style('tpc-style');
    wp_enqueue_script('tpc-script');

    // Determine fallback post type if 'tour' doesn't exist
    $post_type = post_type_exists($a['post_type']) ? $a['post_type'] : 'post';

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => intval($a['number']),
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );


    $q = new WP_Query($args);

    ob_start();
    ?>
    <section class="tpc-section">
        <div class="tpc-header">
            <div>
                <h2 class="tpc-title"><?php echo esc_html($a['title']); ?></h2>
                <?php if(!empty($a['subtitle'])): ?>
                    <div class="tpc-sub"><?php echo esc_html($a['subtitle']); ?></div>
                <?php endif; ?>
            </div>
            <div class="tpc-redirect">
                <a class="tpc-view-all" href="<?php echo esc_url($a['view_all_url']); ?>">View All Tours</a>
                <div class="tpc-nav">
                    <div class="tpc-arrow tpc-prev" aria-label="Previous">&larr;</div>
                    <div class="tpc-arrow tpc-next" aria-label="Next">&rarr;</div>
                </div>
            </div>
        </div>

        <div class="tpc-wrap">
            <div class="swiper tpc-swiper">
                <div class="swiper-wrapper">
                    <?php if($q->have_posts()): while($q->have_posts()): $q->the_post();
                        $id = get_the_ID();
                        // featured image
                        if ( has_post_thumbnail($id) ) {
                            $bg = get_the_post_thumbnail_url($id, 'large');
                        } else {
                            // fallback placeholder
                            $bg = 'https://via.placeholder.com/1200x800?text=Tour+Image';
                        }
                        $get_hours = get_post_meta($id, 'wp_travel_engine_setting_trip_duration', true) ?: '24';
						$duration = format_hours_to_days_nights($get_hours);
						$destinations = wp_get_post_terms($id, 'destination', array('fields' => 'names'));
                        $price = get_post_meta($id, '_s_price', true) ?: '';
                        $old_price = get_post_meta($id, 'wp_travel_engine_setting_trip_actual_price', true) ?: '';
                        $discount = '';
                        if ($old_price && $price && floatval($old_price) > floatval($price)) {
                            $percent = round(100 - (floatval($price)/floatval($old_price)*100));
                            $discount = '-' . intval($percent) . '%';
                        }
                        $rating = get_post_meta($id, 'rating', true) ?: 5;
                        $reviews = get_post_meta($id, 'reviews_count', true) ?: 5;
                    ?>
                        <div class="swiper-slide" style="background-image:url('<?php echo esc_url($bg); ?>');">
                            <div class="tpc-fav" title="Save / Bookmark">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#ff5a2b" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.3l-6.16 3.73 1.66-6.84L2 9.66l6.92-.6L12 3l3.08 6.06 6.92.6-5.5 4.53 1.66 6.84z"/></svg>
                            </div>
                            <?php if($discount): ?>
                                <div style="position:absolute; left:18px; top:18px;">
                                    <span class="tpc-badge"><?php echo esc_html($discount); ?></span>
                                </div>
                            <?php endif; ?>

                            <a class="tpc-overlay" href="<?php the_permalink(); ?>">
                                <div class="tpc-meta">
                                    <span><?php echo implode(', ', $destinations) ?: get_the_title(); ?></span>
                                </div>

                                <div class="tpc-content">
                                    <h3 class="tpc-title-slide"><?php the_title(); ?></h3>

                                    <div class="tpc-bottom">
                                        <div>
                                            <div style="color:rgba(255,255,255,0.9); font-size:14px;"><?php echo esc_html($duration); ?> | from</div>
                                            <div>
                                                <?php if($old_price): ?><span class="tpc-old-price"><?php echo esc_html($old_price ? ('$'.number_format((float)$old_price)) : ''); ?></span><?php endif; ?>
                                                <span class="tpc-price"><?php echo $price ? ('$'.number_format((float)$price)) : 'Contact'; ?></span>
                                            </div>
                                        </div>

                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div style="display:flex; gap:4px; align-items:center;">
                                                <?php
                                                // simple star display
                                                $stars = intval(round($rating));
                                                for($i=1;$i<=5;$i++){
                                                    if($i <= $stars) {
                                                        echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="#ff6a38" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                                                    } else {
                                                        echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" xmlns="http://www.w3.org/2000/svg"><path d="M12 .587l3.668 7.431L23.4 9.75l-5.7 5.55L19.334 24 12 20.013 4.666 24l1.634-8.7L.6 9.75l7.732-1.732z"/></svg>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div style="font-size:14px; color:rgba(255,255,255,0.95);">
                                                <?php echo intval($reviews); ?> reviews
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; wp_reset_postdata(); else: ?>
                        <div class="swiper-slide" style="height:200px; display:flex;align-items:center;justify-content:center;">
                            <div>No tours found</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('tour_packages_carousel', 'tour_packages_carousel_shortcode');

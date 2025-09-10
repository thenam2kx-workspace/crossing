<?php
function destination_grid_shortcode($atts){
    $a = shortcode_atts(array(
        'number' => 8,
        'cols' => 4,
        'trip_tax' => 'destination',
        'title' => 'Top Destinations',
        'subtitle' => '',
        'hide_empty' => 'true',
        'orderby' => 'count',
        'order' => 'DESC',
        'view_all_url' => '#',
    ), $atts, 'destination_grid');

    // enqueue + inline assets
    wp_enqueue_style('dg-style');
    wp_enqueue_script('dg-script');

    // build term query
    $terms_args = array(
        'taxonomy' => sanitize_text_field($a['trip_tax']),
        'number' => intval($a['number']),
        'orderby' => sanitize_text_field($a['orderby']),
        'order' => sanitize_text_field($a['order']),
        'hide_empty' => filter_var($a['hide_empty'], FILTER_VALIDATE_BOOLEAN),
    );

    $terms = get_terms($terms_args);

    ob_start();
    ?>
    <section class="dg-section">
        <div class="dg-header">
            <div>
                <h2 class="dg-title"><?php echo esc_html($a['title']); ?></h2>
                <?php if(!empty($a['subtitle'])): ?><div class="dg-sub"><?php echo esc_html($a['subtitle']); ?></div><?php endif; ?>
            </div>
            <div style="display:flex; align-items:center; gap:18px;">
                <a class="dg-view-all" href="<?php echo esc_url($a['view_all_url']); ?>" style="color:#ff5a2b; font-weight:700; text-decoration:none;">View All Destinations</a>
            </div>
        </div>

        <?php
        // set grid columns inline for initial layout (responsive media queries will override)
        $cols = max(1, intval($a['cols']));
        echo '<div class="dg-grid" style="grid-template-columns: repeat('.esc_attr($cols).', 1fr);">';
        if (!is_wp_error($terms) && !empty($terms)) :
            foreach($terms as $term) :
                $term_id = $term->term_id;
                // try common meta keys to get image (WPTE or other plugins may store differently)
                $img_url = '';
                // 1) common meta key 'image' or 'thumbnail_id'
                $maybe = get_term_meta($term_id, 'image', true);
                if ($maybe) {
                    if (is_numeric($maybe)) {
                        $img_url = wp_get_attachment_url(intval($maybe));
                    } else {
                        $img_url = esc_url($maybe);
                    }
                }
                if (empty($img_url)) {
                    $thumb_id = get_term_meta($term_id, 'thumbnail_id', true);
                    if ($thumb_id) $img_url = wp_get_attachment_url(intval($thumb_id));
                }
                // 2) WP Travel Engine possible meta key
                if (empty($img_url)) {
                    $may = get_term_meta($term_id, 'category-image-id', true);
                    if ($may) {
                        if (is_numeric($may)) $img_url = wp_get_attachment_url(intval($may));
                        else $img_url = esc_url($may);
                    }
                }
                // fallback placeholder
                if (empty($img_url)) $img_url = 'https://via.placeholder.com/1200x800?text='.urlencode($term->name);


                $taxonomy = sanitize_text_field($a['trip_tax']); // hoặc sanitize_text_field($a['trip_tax']);
                // $taxonomy = 'destination'; // hoặc sanitize_text_field($a['trip_tax']);
                $term = get_term($term_id, $taxonomy);

                if ($term && !is_wp_error($term)) {
                    $total_tours = $term->count;
                }

                $term_link = get_term_link($term);
                ?>
                <article class="dg-card" style="background-image:url('<?php echo esc_url($img_url); ?>');">
                    <a class="dg-card-link" href="<?php echo esc_url($term_link); ?>" aria-label="<?php echo esc_attr($term->name); ?>">
                        <div class="dg-inner">
                            <div class="dg-location"><span style="width:8px;height:8px;background:#ff6a38;border-radius:50%;display:inline-block;margin-right:8px;"></span><?php echo esc_html($term->name); ?></div>
                            <h3 class="dg-title-term"><?php echo esc_html($term->name); ?></h3>
                        </div>
                    </a>
                    <div class="dg-more" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#ff5a2b" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h12v20l-6-3-6 3V2z"/></svg>
                    </div>
                    <div class="total-tours"><?= $total_tours . ' tours' ?></div>
                </article>
            <?php
            endforeach;
        else:
            echo '<div>'.esc_html__('No destinations found','textdomain').'</div>';
        endif;
        echo '</div>'; // .dg-grid
        ?>
    </section>
    <?php

    return ob_get_clean();
}

add_shortcode('destination_grid', 'destination_grid_shortcode');

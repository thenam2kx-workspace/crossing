<?php
// Shortcode [our_team]
function shortcode_our_team_cpt($atts) {
    ob_start();

    $args = array(
        'post_type'      => 'our_team',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()): ?>
        <section class="our-team">
            <div class="our-team-header">
                <h2>Our Team</h2>
                <p>Customer-centered, Enthusiastic and Smiley! 
                  We are ready to <strong>accompany</strong> you on your <strong>journey!</strong>
                </p>
            </div>

            <div class="our-team-wrapper">
                <!-- Swiper -->
                <div class="swiper our-team-swiper">
                    <div class="swiper-wrapper">
                        <?php while ($query->have_posts()): $query->the_post(); ?>
                            <div class="swiper-slide team-member">
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="team-photo">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="team-info">
                                    <h3><?php the_title(); ?></h3>
                                    <?php if ($position = get_post_meta(get_the_ID(), 'position', true)): ?>
                                        <p class="position"><?php echo esc_html($position); ?></p>
                                    <?php endif; ?>
                                    <p class="desc"><?php echo wp_trim_words(get_the_content(), 20); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="our-team-nav">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </section>
    <?php endif;
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('our_team', 'shortcode_our_team_cpt');

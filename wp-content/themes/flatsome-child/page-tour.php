<?php
/**
 * Template Name: Tour Page (Reusable Card)
 * Description: Hiển thị các term của taxonomy 'trip_types' và các trip tương ứng.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$taxonomy = 'trip_types';
$posts_per_term = 4;
?>

<div class="page-tour-template container">
    <header class="page-tour-header">
        <?php
        // hiển thị nội dung trang nếu có
        while ( have_posts() ) : the_post();
            echo '<div class="page-content">';
            the_content();
            echo '</div>';
        endwhile;
        ?>
    </header>

    <?php
    $terms = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
    ) );

    if ( empty( $terms ) || is_wp_error( $terms ) ) : ?>
        <p class="wte-no-terms">Không tìm thấy danh mục tour.</p>
    <?php else :

        foreach ( $terms as $term ) :
            $term_link = get_term_link( $term );
            ?>
            <section class="wte-term-section">
                <div class="wte-term-header">
                    <h2 class="wte-term-title">
                        <a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a>
                    </h2>
                    <a class="wte-term-more" href="<?php echo esc_url( $term_link ); ?>">→ View all</a>
                </div>

                <div class="wte-term-grid">
                    <?php
                    $args = array(
                        'post_type'      => 'trip',
                        'posts_per_page' => intval( $posts_per_term ),
                        'tax_query'      => array(
                            array(
                                'taxonomy' => $taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $term->term_id,
                            ),
                        ),
                        'post_status' => 'publish',
                    );
                    $q = new WP_Query( $args );

                    if ( $q->have_posts() ) :
                        while ( $q->have_posts() ) : $q->the_post();
                            $id = get_the_ID();

                            // --- Preferred: use reusable helper returning HTML ---
                            if ( function_exists( 'get_tour_card_html' ) ) {
                                echo get_tour_card_html( $id );
                            } else {
                                // Fallback: use template part and pass tour_id via query_var
                                set_query_var( 'tour_id', $id );
                                get_template_part( 'template-parts/tour/card-tour' );
                                set_query_var( 'tour_id', null );
                            }

                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<div class="wte-no-posts">No tours found in this category.</div>';
                    endif;
                    ?>
                </div>
            </section>
        <?php
        endforeach;
    endif;
    ?>
</div>

<?php
get_footer();

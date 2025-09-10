<?php
// functions.php
require_once get_stylesheet_directory() . '/inc/shortcodes-loader.php';
require_once get_stylesheet_directory() . '/inc/taxonomy-trip-types-images.php';
require_once get_stylesheet_directory() . '/inc/taxonomy-destination-images.php';


// Enqueue page-tour.css only for the page template 'page-tour.php'
function child_enqueue_page_tour_styles() {
    // only enqueue on front-end and when the template is used
    if ( ! is_admin() && is_page_template( 'page-tour.php' ) ) {
        $css_path = get_stylesheet_directory() . '/assets/css/page-tour.css';
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'child-page-tour',
                get_stylesheet_directory_uri() . '/assets/css/page-tour.css',
                array(),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_page_tour_styles', 20 );

function child_enqueue_tour_card_styles() {
    $css = get_stylesheet_directory() . '/assets/css/card-tour.css';
    if ( file_exists( $css ) ) {
        wp_enqueue_style( 'child-tour-card', get_stylesheet_directory_uri() . '/assets/css/card-tour.css', array(), filemtime($css) );
    }
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_tour_card_styles', 20 );



// functions.php (child theme)
if ( ! function_exists('get_tour_card_html') ) {
    function get_tour_card_html($post_id, $args = array()) {
        $post_id = (int) $post_id;
        if ( ! $post_id ) return '';

        // set vars for template part
        set_query_var('tour_id', $post_id);
        set_query_var('tour_args', $args);

        ob_start();
        get_template_part('template-parts/tour/card-tour');
        $html = ob_get_clean();

        // clean up
        set_query_var('tour_id', null);
        set_query_var('tour_args', null);

        return $html;
    }
}

if ( ! function_exists('the_tour_card') ) {
    function the_tour_card($post_id, $args = array()) {
        echo get_tour_card_html($post_id, $args);
    }
}



add_action('wp_enqueue_scripts', 'nm_child_enqueue_trip_hero_styles');
function nm_child_enqueue_trip_hero_styles() {
    if ( is_admin() ) return;

    // Chỉ load khi hiển thị archive trip / taxonomy trip_types
    if ( is_post_type_archive('trip') || is_tax('trip_types') || is_tax('destination') ) {
        $css_file = get_stylesheet_directory() . '/assets/css/archive-trip-hero.css';
        $version = file_exists($css_file) ? filemtime($css_file) : null;
        wp_enqueue_style(
        'child-trip-hero',
        get_stylesheet_directory_uri() . '/assets/css/archive-trip-hero.css',
        array(),
        $version
        );
    }
}

add_action('wp_enqueue_scripts', 'nm_child_enqueue_template_destination_styles');
function nm_child_enqueue_template_destination_styles() {
    if ( is_admin() ) return;

    // Chỉ load khi hiển thị archive trip / taxonomy trip_types
    // if ( is_page_template( 'template-destination.php' ) ) {
        $css_file = get_stylesheet_directory() . '/assets/css/template-destination.css';
        $version = file_exists($css_file) ? filemtime($css_file) : null;
        wp_enqueue_style(
        'child-template-destination',
        get_stylesheet_directory_uri() . '/assets/css/template-destination.css',
        array(),
        $version
        );
    // }
}

add_action( 'admin_enqueue_scripts', function( $hook ) {
    $screen = get_current_screen();
    if ( ! empty( $screen->taxonomy ) && $screen->taxonomy === 'destination' ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'nm-destination-images',
            get_stylesheet_directory_uri() . '/assets/js/taxonomy-destination-images.js',
            array('jquery'),
            filemtime( get_stylesheet_directory() . '/assets/js/taxonomy-destination-images.js' ),
            true
        );
    }
});


function mytheme_enqueue_trip_styles() {
    // if (is_post_type_archive('trip') || is_singular('trip')) {
        wp_enqueue_style(
            'trip-list-style',
            get_stylesheet_directory_uri() . '/assets/css/trip-list.css',
            array(),
            file_exists(get_stylesheet_directory() . '/assets/css/trip-list.css'),
            true
        );
    // }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_trip_styles');

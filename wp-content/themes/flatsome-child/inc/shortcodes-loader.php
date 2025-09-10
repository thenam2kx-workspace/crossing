<?php
// inc/shortcodes-loader.php

// Register common vendor libs + our assets
add_action('wp_enqueue_scripts', function() {
    // Swiper (CDN)
    wp_register_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css', array(), null);
    wp_register_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js', array(), null, true);

    // Tour Packages carousel assets
    wp_register_style('tpc-style', get_stylesheet_directory_uri() . '/assets/css/tpc.css', array('swiper-css'), '1.0.0');
    wp_register_script('tpc-script', get_stylesheet_directory_uri() . '/assets/js/tpc.js', array('swiper-js', 'jquery'), '1.0.0', true);

    // Trip grid assets
    wp_register_style('wg-trip-grid-style', get_stylesheet_directory_uri() . '/assets/css/wg-trip-grid.css', array(), '1.0.0');
    wp_register_script('wg-trip-grid-script', get_stylesheet_directory_uri() . '/assets/js/wg-trip-grid.js', array('jquery'), '1.0.0', true);

    // Destination grid assets
    wp_register_style('dg-style', get_stylesheet_directory_uri() . '/assets/css/dg.css', array(), '1.0.0');
    wp_register_script('dg-script', get_stylesheet_directory_uri() . '/assets/js/dg.js', array('jquery'), '1.0.0', true);

    wp_enqueue_style('our-team-css', get_stylesheet_directory_uri() . '/assets/css/our-team.css', array(), filemtime(get_stylesheet_directory() . '/assets/css/our-team.css'));
    wp_enqueue_script('our-team-js', get_stylesheet_directory_uri() . '/assets/js/our-team.js', array('swiper-js'), filemtime(get_stylesheet_directory() . '/assets/js/our-team.js'), true);
});

// Include shortcode files
require_once get_stylesheet_directory() . '/inc/tour-packages-carousel.php';
require_once get_stylesheet_directory() . '/inc/trip-grid-wte.php';
require_once get_stylesheet_directory() . '/inc/destination-grid.php';
require_once get_stylesheet_directory() . '/inc/our-team.php';

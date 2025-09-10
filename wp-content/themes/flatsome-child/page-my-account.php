<?php
/**
 * Template name: WooCommerce - My Account
 *
 * This template adds My account to the sidebar.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.0
 */

get_header(); ?>

<?php do_action( 'flatsome_before_page' ); ?>

<div class="container">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>

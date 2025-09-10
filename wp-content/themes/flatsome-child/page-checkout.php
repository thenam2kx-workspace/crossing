<?php
/**
 * Template name: WooCommerce - Checkout
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

// wc_get_template_part('checkout/layouts/checkout', get_theme_mod('checkout_layout'));
get_header();
echo '<div class="cart-container container page-wrapper page-checkout">';
  the_content();
echo '</div>';
get_footer();
?>

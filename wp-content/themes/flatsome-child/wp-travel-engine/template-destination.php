<?php
/**
 * The template for displaying trips according to destination.
 *
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since 1.0.0
 */
// get_header();
// 	wte_get_template( 'content--template-taxonomy.php', array( 'taxonomy' => 'destination' ) );
// get_footer();

get_header();
?>


<div class="page-destination">
  <div class="page-destination-wrapper">
    <?php get_template_part('template-parts/destination', 'grid'); ?>
  </div>
</div>


<?php
get_footer();
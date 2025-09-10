<div class="destination-grid">
  <?php
  $terms = get_terms([
    'taxonomy' => 'destination',
    'hide_empty' => false,
  ]);

  if ($terms && ! is_wp_error($terms)):
    foreach($terms as $term):
      $images = get_term_meta($term->term_id, 'destination_images', true);
      if (!is_array($images)) $images = [];
      ?>
      <a href="<?php echo esc_url(get_term_link($term)); ?>">
        <div class="destination-block">
          <h2><?php echo esc_html($term->name); ?></h2>
          <div class="destination-slider">
            <div class="images-track">
              <?php foreach($images as $id):
                  $img = wp_get_attachment_image_url($id,'medium_large');
                  if ($img): ?>
                    <div class="slide"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($term->name); ?>" /></div>
                  <?php endif;
              endforeach; ?>
            </div>
          </div>
        </div>
      </a>
    <?php endforeach;
  endif;
  ?>
</div>

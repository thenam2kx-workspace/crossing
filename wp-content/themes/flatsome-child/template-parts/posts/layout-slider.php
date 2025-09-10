<article <?php post_class('slide-item'); ?>>
    <div class="post-thumbnail-overlay">
        <a href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail('large'); ?>
            <?php else : ?>
                <img src="https://via.placeholder.com/800x500" alt="<?php the_title(); ?>">
            <?php endif; ?>

            <div class="post-title-overlay">
                <h2><?php the_title(); ?></h2>
            </div>
        </a>
    </div>
</article>

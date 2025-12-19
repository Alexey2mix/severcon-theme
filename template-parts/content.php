<?php
/**
 * Template part for displaying posts - Custom design for Severcon
 *
 * @package severcon
 */
?>

<div class="news-archive-item" data-post-id="<?php the_ID(); ?>">
    <a href="<?php the_permalink(); ?>" class="news-archive-card">
        
        <?php if (has_post_thumbnail()) : ?>
            <div class="news-archive-image">
                <?php 
                    the_post_thumbnail('full', array(
                        'alt' => get_the_title()
                    )); 
                ?>
            </div>
        <?php endif; ?>
        
        <div class="news-archive-content">
            <div class="news-date"><?php echo get_the_date('d.m.Y'); ?></div>
            <h3 class="news-title"><?php the_title(); ?></h3>
            <p class="news-excerpt"><?php echo get_the_excerpt(); ?></p>
        </div>
        
    </a>
</div>

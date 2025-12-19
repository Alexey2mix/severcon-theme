<?php
/**
 * Template part for displaying posts
 *
 * @package severcon
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('news-item'); ?>>
    
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'post-image')); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="post-content">
        <h3 class="entry-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>
        
        <div class="entry-meta">
            <span class="post-date">
                <i class="far fa-calendar"></i> <?php echo get_the_date(); ?>
            </span>
            
            <?php if (get_comments_number() > 0) : ?>
                <span class="post-comments">
                    <i class="far fa-comment"></i> <?php comments_number('0', '1', '%'); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>
        
        <a href="<?php the_permalink(); ?>" class="read-more">
            Читать далее <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</article>

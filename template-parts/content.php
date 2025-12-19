<?php
/**
 * Template part for displaying posts
 *
 * @package severcon
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <h3 class="entry-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h3>
    
    <div class="entry-meta">
        <?php echo get_the_date(); ?>
    </div>
    
    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div>
    
    <a href="<?php the_permalink(); ?>" class="read-more">
        Читать далее
    </a>
</article>

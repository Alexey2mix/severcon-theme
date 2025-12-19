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
                <?php the_post_thumbnail('severcon-thumbnail', array('class' => 'img-fluid')); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="entry-content">
        <header class="entry-header">
            <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
            
            <div class="entry-meta">
                <span class="posted-on">
                    <i class="far fa-calendar"></i>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>
                </span>
                
                <?php if (!post_password_required() && (comments_open() || get_comments_number())) : ?>
                    <span class="comments-link">
                        <i class="far fa-comments"></i>
                        <?php comments_popup_link('0', '1', '%'); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="entry-excerpt">
            <?php the_excerpt(); ?>
        </div>
        
        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                Читать далее <i class="fas fa-arrow-right"></i>
            </a>
        </footer>
    </div>
</article>

<?php
/**
 * Template part for displaying posts
 *
 * @package severcon
 */

// Получаем настройки поста
$has_thumbnail = has_post_thumbnail();
$post_class = $has_thumbnail ? 'post-item has-thumbnail' : 'post-item no-thumbnail';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
    
    <?php if ($has_thumbnail) : ?>
        <div class="post-thumbnail-wrapper">
            <a href="<?php the_permalink(); ?>" class="post-thumbnail-link">
                <?php 
                the_post_thumbnail('severcon-thumbnail', array(
                    'class' => 'post-thumbnail-img',
                    'loading' => 'lazy',
                    'alt' => get_the_title()
                )); 
                ?>
                <div class="thumbnail-overlay">
                    <span class="read-more-icon">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </a>
            
            <div class="post-date-badge">
                <span class="post-day"><?php echo get_the_date('d'); ?></span>
                <span class="post-month"><?php echo get_the_date('M'); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="post-content-wrapper">
        
        <div class="post-categories">
            <?php
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="post-category">' . esc_html($categories[0]->name) . '</a>';
            }
            ?>
        </div>
        
        <header class="post-header">
            <?php 
            the_title(
                '<h3 class="post-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">',
                '</a></h3>'
            );
            ?>
        </header>
        
        <div class="post-excerpt">
            <?php 
            // Обрезаем excerpt до 100 слов
            $excerpt = get_the_excerpt();
            if (str_word_count($excerpt) > 25) {
                $excerpt = wp_trim_words($excerpt, 25, '...');
            }
            echo '<p>' . $excerpt . '</p>';
            ?>
        </div>
        
        <footer class="post-footer">
            <div class="post-meta">
                <div class="post-author">
                    <i class="far fa-user"></i>
                    <span><?php the_author(); ?></span>
                </div>
                
                <div class="post-comments">
                    <i class="far fa-comment"></i>
                    <span><?php comments_number('0', '1', '%'); ?></span>
                </div>
                
                <div class="post-time">
                    <i class="far fa-clock"></i>
                    <span><?php echo reading_time(); ?></span>
                </div>
            </div>
            
            <a href="<?php the_permalink(); ?>" class="post-read-more">
                <span>Подробнее</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </footer>
    </div>
</article>

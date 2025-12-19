<?php
/**
 * The template for displaying archive pages
 *
 * @package severcon
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <?php
                the_archive_title('<h1 class="page-title">', '</h1>');
                the_archive_description('<div class="archive-description">', '</div>');
                ?>
            </header>

            <div class="news-grid" id="news-grid">
                <?php
                $current_page = max(1, get_query_var('paged'));
                while (have_posts()) : the_post();
                    // Используем ваш шаблон для постов
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
                ?>
            </div>

            <?php 
                // Определяем максимальное количество страниц
                $max_pages = $wp_query->max_num_pages;
                $current_page = max(1, get_query_var('paged'));
                
                // Показываем кнопку только если есть еще страницы
                if ($max_pages > 1) : 
                ?>
                    <div class="load-more-wrapper text-center py-4">
                        <button id="load-more-news" 
                                class="btn btn-primary btn-load-more" 
                                data-page="<?php echo $current_page; ?>" 
                                data-max-pages="<?php echo $max_pages; ?>"
                                data-category="<?php echo is_category() ? get_queried_object_id() : ''; ?>"
                                data-tag="<?php echo is_tag() ? get_queried_object_id() : ''; ?>"
                                data-posts-per-page="<?php echo get_option('posts_per_page'); ?>">
                            <span class="btn-text">
                                <i class="fas fa-plus-circle me-2"></i>Показать еще
                            </span>
                            <span class="loading-spinner">
                                <i class="fas fa-spinner fa-spin me-2"></i>Загрузка...
                            </span>
                        </button>
                        <div class="load-more-message mt-2"></div>
                    </div>
                <?php endif; ?>

        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();

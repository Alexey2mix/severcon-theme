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

            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="load-more-container">
                    <button id="load-more-news" 
                            class="severcon-button load-more-button"
                            data-page="<?php echo $current_page; ?>" 
                            data-max-pages="<?php echo $max_pages; ?>"
                            data-category="<?php echo is_category() ? get_queried_object_id() : ''; ?>"
                            data-tag="<?php echo is_tag() ? get_queried_object_id() : ''; ?>">
                        <span class="button-text">Показать еще</span>
                        <span class="button-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                    <div class="load-more-message"></div>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();

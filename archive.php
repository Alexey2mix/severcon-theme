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
                while (have_posts()) : the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
                ?>
            </div>

            <?php if ($wp_query->max_num_pages > 1) : ?>
                <div class="load-more-wrapper">
                    <button id="load-more-news" class="btn btn-primary" 
                            data-page="1" 
                            data-max-pages="<?php echo $wp_query->max_num_pages; ?>">
                        Показать еще
                    </button>
                    <div class="loading-spinner" style="display: none;">
                        Загрузка...
                    </div>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();

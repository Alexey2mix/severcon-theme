<?php
/**
 * Archive Product Template
 */
get_header();

if (is_product_category()) {
    global $wp_query;
    $cat = $wp_query->get_queried_object();
    $category_id = $cat->term_id;
    
    // Вывод информации о категории
    echo '<div class="category-header">';
    echo '<h1>' . single_cat_title('', false) . '</h1>';
    echo category_description($category_id);
    echo '</div>';
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <?php 
            // Используем стандартный цикл WooCommerce
            if (have_posts()) :
                echo '<div class="products-grid row">';
                while (have_posts()) : the_post();
                    wc_get_template_part('content', 'product');
                endwhile;
                echo '</div>';
                
                // Пагинация
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => __('Назад', 'severcon'),
                    'next_text' => __('Вперед', 'severcon'),
                ));
            else :
                echo '<p>' . __('Товары не найдены', 'severcon') . '</p>';
            endif;
            ?>
        </div>
        
        <div class="col-md-3">
            <?php 
            // Подключаем фильтры
            get_template_part('inc/simple-filters');
            
            // Сайдбар магазина
            if (is_active_sidebar('sidebar-shop')) {
                dynamic_sidebar('sidebar-shop');
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

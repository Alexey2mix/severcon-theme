<?php
/**
 * Functions File for Severcon Theme
 * Combined from old and new versions
 * Date: 2024
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ==================== БАЗОВЫЕ НАСТРОЙКИ ТЕМЫ ====================
function severcon_setup() {
    // Поддержка WordPress
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Поддержка WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'severcon_setup');

// ==================== ПОДКЛЮЧЕНИЕ СТИЛЕЙ И СКРИПТОВ ====================
function severcon_scripts() {
    // Основной CSS
    wp_enqueue_style('severcon-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0');
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    
    // jQuery (уже есть в WordPress)
    wp_enqueue_script('jquery');
    
    // Основной JavaScript
    wp_enqueue_script('severcon-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
    
    // Локализация для AJAX
    wp_localize_script('severcon-main', 'severcon_ajax', array(
        'ajax_url'      => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('severcon_nonce'),
        'filter_nonce'  => wp_create_nonce('filter_nonce'),
        'quick_nonce'   => wp_create_nonce('quick_view_nonce'),
        'i18n' => array(
            'loading' => 'Загрузка...',
            'no_products' => 'Товаров не найдено',
            'apply_filters' => 'Применить фильтры'
        )
    ));
}
add_action('wp_enqueue_scripts', 'severcon_scripts');

// ==================== РЕГИСТРАЦИЯ МЕНЮ ====================
function severcon_menus() {
    register_nav_menus(array(
        'primary' => 'Основное меню',
        'footer' => 'Меню в футере',
    ));
}
add_action('init', 'severcon_menus');

// ==================== РЕГИСТРАЦИЯ ВИДЖЕТОВ ====================
function severcon_widgets_init() {
    register_sidebar(array(
        'name' => 'Сайдбар магазина',
        'id' => 'shop-sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => 'Футер - Логотип',
        'id' => 'footer-logo',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => 'Футер - Каталог',
        'id' => 'footer-catalog',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => 'Футер - Поддержка',
        'id' => 'footer-support',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => 'Футер - О компании',
        'id' => 'footer-about',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'severcon_widgets_init');

// ==================== CUSTOMIZER НАСТРОЙКИ ====================
function severcon_customize_register($wp_customize) {
    // Контакты
    $wp_customize->add_section('contact_section', array(
        'title' => 'Контактная информация',
        'priority' => 35,
    ));
    
    $wp_customize->add_setting('phone_number', array('default' => '+7 (495) 252-08-28'));
    $wp_customize->add_control('phone_number', array(
        'label' => 'Номер телефона',
        'section' => 'contact_section',
        'type' => 'text',
    ));
}
add_action('customize_register', 'severcon_customize_register');

// ==================== РАЗМЕРЫ ИЗОБРАЖЕНИЙ ====================
function severcon_image_sizes() {
    add_image_size('news-thumb', 400, 250, true);
    add_image_size('equipment-large', 600, 400, true);
    add_image_size('equipment-small', 400, 300, true);
    add_image_size('product-thumb', 300, 300, true);
}
add_action('after_setup_theme', 'severcon_image_sizes', 20);

// ==================== AJAX: ЗАГРУЗКА НОВОСТЕЙ ====================
add_action('wp_ajax_load_more_news', 'severcon_load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'severcon_load_more_news');

function severcon_load_more_news() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $posts_per_page = 6;
    $offset = 7 + (($page - 2) * $posts_per_page);
    
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'offset' => $offset,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    $news_query = new WP_Query($args);
    
    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            ?>
            <div class="news-archive-item" data-post-id="<?php the_ID(); ?>">
                <a href="<?php the_permalink(); ?>" class="news-archive-card">
                    <div class="news-archive-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('news-thumb'); ?>
                        <?php endif; ?>
                    </div>
                    <div class="news-archive-content">
                        <div class="news-date"><?php echo get_the_date('d.m.Y'); ?></div>
                        <h3 class="news-title"><?php the_title(); ?></h3>
                        <p class="news-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                    </div>
                </a>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo 'no_more_posts';
    }
    wp_die();
}

// ==================== AJAX: БЫСТРЫЙ ПРОСМОТР ТОВАРА ====================
add_action('wp_ajax_get_quick_view', 'get_quick_view_product');
add_action('wp_ajax_nopriv_get_quick_view', 'get_quick_view_product');

function get_quick_view_product() {
    // Упрощенная проверка
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Не передан ID товара');
    }
    
    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error('Товар не найден');
    }
    
    ob_start();
    ?>
    <div class="quick-view-product" data-current-id="<?php echo $product_id; ?>">
        <div class="quick-view-nav-container">
            <?php
            // Получаем категорию товара
            $categories = wp_get_post_terms($product_id, 'product_cat');
            $category_id = !empty($categories) ? $categories[0]->term_id : 0;
            
            if ($category_id) {
                // Получаем предыдущий товар
                $prev_id = get_adjacent_products($product_id, $category_id, 'prev');
                // Получаем следующий товар
                $next_id = get_adjacent_products($product_id, $category_id, 'next');
                
                if ($prev_id) : ?>
                    <button class="nav-arrow prev-arrow" data-product-id="<?php echo $prev_id; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                <?php endif; ?>
                
                <?php if ($next_id) : ?>
                    <button class="nav-arrow next-arrow" data-product-id="<?php echo $next_id; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif;
            }
            ?>
        </div>
        
        <div class="quick-view-gallery">
            <div class="quick-view-image-main">
                <?php 
                $image_id = $product->get_image_id();
                if ($image_id) {
                    echo wp_get_attachment_image($image_id, 'large');
                } else {
                    echo wc_placeholder_img('large');
                }
                ?>
            </div>
            
            <?php 
            $gallery_ids = $product->get_gallery_image_ids();
            if ($gallery_ids) : ?>
                <div class="quick-view-thumbnails">
                    <?php if ($image_id) : ?>
                        <div class="quick-view-thumb active">
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        </div>
                    <?php endif;
                    foreach ($gallery_ids as $gallery_id) : ?>
                        <div class="quick-view-thumb">
                            <?php echo wp_get_attachment_image($gallery_id, 'thumbnail'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="quick-view-info">
            <h2><?php echo esc_html($product->get_name()); ?></h2>
            
            <?php if (wc_review_ratings_enabled() && $product->get_average_rating() > 0) : ?>
                <div class="quick-view-rating">
                    <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                    <span class="review-count">(<?php echo $product->get_review_count(); ?> отзывов)</span>
                </div>
            <?php endif; ?>
            
            <div class="quick-view-price">
                <?php echo $product->get_price_html(); ?>
            </div>
            
            <?php if ($product->get_short_description()) : ?>
                <div class="quick-view-excerpt">
                    <?php echo apply_filters('the_content', $product->get_short_description()); ?>
                </div>
            <?php endif; ?>
            
            <div class="quick-view-actions">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" 
                   class="btn btn-primary btn-details">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Перейти к товару</span>
                </a>
                
                <button class="btn btn-dark btn-request quick-view-request"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        data-product-name="<?php echo esc_attr($product->get_name()); ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Запросить цену</span>
                </button>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json_success($html);
    wp_die();
}

    // ==================== AJAX: ФИЛЬТРАЦИЯ ТОВАРОВ ====================
    add_action('wp_ajax_filter_category_products', 'filter_category_products_ajax');
    add_action('wp_ajax_nopriv_filter_category_products', 'filter_category_products_ajax');
    
    function filter_category_products_ajax() {
        // Проверка безопасности
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'filter_nonce')) {
            wp_send_json_error('Ошибка безопасности');
        }
        
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $filters = isset($_POST['filters']) ? $_POST['filters'] : array();
        $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'menu_order';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 12;
        
        if (!$category_id) {
            wp_send_json_error('Категория не указана');
        }
        
        // Основные аргументы запроса
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post_status'    => 'publish',
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                    'include_children' => true
                )
            )
        );
        
        // Сортировка
        switch ($orderby) {
            case 'price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'DESC';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'popularity':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';
                $args['order'] = 'DESC';
                break;
            case 'rating':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_wc_average_rating';
                $args['order'] = 'DESC';
                break;
            default:
                $args['orderby'] = 'menu_order title';
                $args['order'] = 'ASC';
        }
        
        // Фильтры по атрибутам
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $taxonomy => $selected_terms) {
                if (!empty($selected_terms) && is_array($selected_terms)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => array_map('sanitize_text_field', $selected_terms),
                        'operator' => 'IN'
                    );
                }
            }
        }
        
        // Запрос товаров
        $products_query = new WP_Query($args);
        
        ob_start();
        
        if ($products_query->have_posts()) :
            while ($products_query->have_posts()) : $products_query->the_post();
                global $product;
                
                if (!$product) {
                    $product = wc_get_product(get_the_ID());
                }
                
                if (!$product) continue;
                ?>
                <div class="product-item">
                    <a href="<?php the_permalink(); ?>" class="product-card">
                        <div class="product-image">
                            <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                            <?php if ($product->is_on_sale()) : ?>
                                <span class="product-badge sale">Акция</span>
                            <?php elseif ($product->is_featured()) : ?>
                                <span class="product-badge featured">Хит</span>
                            <?php endif; ?>
                            <button class="quick-view-btn" 
                                    data-product-id="<?php echo $product->get_id(); ?>"
                                    title="Быстрый просмотр">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php the_title(); ?></h3>
                            <?php if (wc_review_ratings_enabled() && $product->get_average_rating() > 0) : ?>
                                <div class="product-rating-small">
                                    <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                                    <span class="review-count-small">(<?php echo $product->get_review_count(); ?>)</span>
                                </div>
                            <?php endif; ?>
                            <div class="product-price"><?php echo $product->get_price_html(); ?></div>
                            <div class="product-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></div>
                            <div class="product-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary view-product-btn">
                                    Подробнее
                                </a>
                                <button class="btn btn-dark request-price-btn" 
                                        data-product-id="<?php echo $product->get_id(); ?>"
                                        data-product-name="<?php echo esc_attr(get_the_title()); ?>">
                                    <i class="fas fa-envelope"></i> Запросить цену
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            endwhile;
        else :
            ?>
            <div class="no-products-filtered">
                <p>Товаров по выбранным фильтрам не найдено.</p>
                <button class="btn btn-primary reset-filters-ajax">Сбросить фильтры</button>
            </div>
            <?php
        endif;
        
        $html = ob_get_clean();
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'html'       => $html,
            'count'      => $products_query->found_posts,
            'max_pages'  => $products_query->max_num_pages,
            'page'       => $page
        ));
        
        wp_die();
    }

// ==================== WOOCOMMERCE НАСТРОЙКИ ====================

// Количество товаров на странице
add_filter('loop_shop_per_page', 'severcon_products_per_page', 20);
function severcon_products_per_page($products_per_page) {
    if (isset($_GET['show_all']) && $_GET['show_all'] === 'true') {
        return 24;
    }
    return 12;
}

// Хлебные крошки WooCommerce
add_filter('woocommerce_breadcrumb_defaults', 'severcon_breadcrumbs');
function severcon_breadcrumbs() {
    return array(
        'delimiter'   => ' <span class="separator">/</span> ',
        'wrap_before' => '<div class="breadcrumbs-section"><div class="container"><nav class="breadcrumbs">',
        'wrap_after'  => '</nav></div></div>',
        'before'      => '',
        'after'       => '',
        'home'        => 'Главная',
    );
}

// ==================== ОТКЛЮЧЕНИЕ КОММЕНТАРИЕВ ====================
function severcon_disable_comments() {
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    add_filter('comments_array', '__return_empty_array', 10, 2);
    
    // Убираем из админки
    function severcon_remove_comments_admin_menu() {
        remove_menu_page('edit-comments.php');
    }
    add_action('admin_menu', 'severcon_remove_comments_admin_menu');
}
add_action('init', 'severcon_disable_comments');

// ==================== ВКЛЮЧЕНИЕ ДОПОЛНИТЕЛЬНЫХ ФАЙЛОВ ====================
require_once get_template_directory() . '/inc/simple-filters.php';

// ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================

/**
 * Отладка вывода переменных
 */
function severcon_debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * Проверка, является ли страница WooCommerce
 */
function severcon_is_woocommerce_page() {
    return function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
}

// ==================== ПОЛУЧЕНИЕ СОСЕДНИХ ТОВАРОВ В КАТЕГОРИИ ====================
function get_adjacent_products($product_id, $category_id, $direction = 'next') {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            )
        ),
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
        'fields'         => 'ids'
    );
    
    // Получаем текущий порядок товаров
    $all_products = get_posts(array_merge($args, array('posts_per_page' => -1)));
    
    if (empty($all_products)) {
        return false;
    }
    
    $current_index = array_search($product_id, $all_products);
    
    if ($current_index === false) {
        return false;
    }
    
    if ($direction === 'next') {
        $next_index = $current_index + 1;
        return isset($all_products[$next_index]) ? $all_products[$next_index] : false;
    } else {
        $prev_index = $current_index - 1;
        return isset($all_products[$prev_index]) ? $all_products[$prev_index] : false;
    }
}


    // ==================== AJAX: ОБНОВЛЕНИЕ КОЛИЧЕСТВА ТОВАРОВ В ФИЛЬТРАХ ====================
    add_action('wp_ajax_update_filter_counts', 'update_filter_counts_ajax');
    add_action('wp_ajax_nopriv_update_filter_counts', 'update_filter_counts_ajax');
    
    function update_filter_counts_ajax() {
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $active_filters = isset($_POST['filters']) ? $_POST['filters'] : array();
        
        if (!$category_id) {
            wp_send_json_error('Категория не указана');
        }
        
        // Получаем все атрибуты
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $available_counts = array();
        
        foreach ($attribute_taxonomies as $attribute) {
            $taxonomy = 'pa_' . $attribute->attribute_name;
            
            // Получаем все термины для этого атрибута
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));
            
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    // Считаем товары с учетом активных фильтров
                    $count = count_products_with_filters($category_id, $taxonomy, $term->slug, $active_filters);
                    
                    // Всегда показываем выбранные фильтры, даже если 0
                    $is_selected = isset($active_filters[$taxonomy]) && in_array($term->slug, $active_filters[$taxonomy]);
                    
                    if ($count > 0 || $is_selected) {
                        $available_counts[$taxonomy][$term->slug] = $count;
                    }
                }
            }
        }
        
        wp_send_json_success($available_counts);
        wp_die();
    }

    // ==================== ПОДСЧЕТ ТОВАРОВ С УЧЕТОМ ФИЛЬТРОВ ====================
    function count_products_with_filters($category_id, $current_taxonomy, $current_term_slug, $active_filters) {
        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ),
                array(
                    'taxonomy' => $current_taxonomy,
                    'field'    => 'slug',
                    'terms'    => $current_term_slug,
                )
            )
        );
        
        // Добавляем остальные активные фильтры (кроме текущего атрибута)
        foreach ($active_filters as $taxonomy => $terms) {
            if ($taxonomy !== $current_taxonomy && !empty($terms)) {
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => array_map('sanitize_text_field', $terms),
                    'operator' => 'IN'
                );
            }
        }
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }

// Добавьте этот код в functions.php

// AJAX для загрузки новостей
add_action('wp_ajax_load_more_news', 'severcon_load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'severcon_load_more_news');

function severcon_load_more_news() {
    // Проверка безопасности
    check_ajax_referer('load_more_nonce', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $category = isset($_POST['category']) ? intval($_POST['category']) : '';
    $tag = isset($_POST['tag']) ? intval($_POST['tag']) : '';
    
    // Аргументы для нового запроса
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => $page,
    );
    
    // Если есть категория
    if (!empty($category)) {
        $args['cat'] = $category;
    }
    
    // Если есть тег
    if (!empty($tag)) {
        $args['tag_id'] = $tag;
    }
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) :
        ob_start();
        
        while ($query->have_posts()) : $query->the_post();
            get_template_part('template-parts/content', get_post_type());
        endwhile;
        
        $output = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $output,
            'max_pages' => $query->max_num_pages,
            'current_page' => $page
        ));
    else :
        wp_send_json_error('Нет больше новостей');
    endif;
    
    wp_die();
}

// Локализация скриптов
add_action('wp_enqueue_scripts', 'severcon_localize_ajax');
function severcon_localize_ajax() {
    wp_localize_script('main', 'severcon_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_nonce'),
        'loading_text' => 'Загрузка...',
        'load_more_text' => 'Показать еще'
    ));
}

?>

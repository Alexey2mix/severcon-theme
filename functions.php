<?php
/**
 * Functions and Definitions
 *
 * @package severcon
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Базовые настройки темы
if (!function_exists('severcon_setup')) {
    function severcon_setup() {
        // Поддержка WordPress функционала
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ));
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ));
        add_theme_support('woocommerce');
        
        // Регистрация меню
        register_nav_menus(array(
            'primary' => 'Главное меню',
            'footer'  => 'Футер меню',
        ));
    }
    add_action('after_setup_theme', 'severcon_setup');
}

// Подключение стилей и скриптов
if (!function_exists('severcon_scripts')) {
    function severcon_scripts() {
        // Основной CSS
        wp_enqueue_style('severcon-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
        
        // Дополнительный CSS
        wp_enqueue_style('severcon-main-style', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0');
        
        // Основной JS
        wp_enqueue_script('severcon-main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
        
        // Подключение Font Awesome
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        
        // Локализация для AJAX
        if (is_home() || is_archive() || is_category() || is_tag()) {
            wp_localize_script('severcon-main-js', 'severcon_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('severcon_load_more_nonce'),
                'loading_text' => 'Загрузка...',
                'load_more_text' => 'Показать еще'
            ));
        }
        
        // Комментарии
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
    add_action('wp_enqueue_scripts', 'severcon_scripts');
}

// Регистрация сайдбаров
if (!function_exists('severcon_widgets_init')) {
    function severcon_widgets_init() {
        register_sidebar(array(
            'name'          => 'Основной сайдбар',
            'id'            => 'sidebar-1',
            'description'   => 'Добавьте виджеты сюда.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
        
        register_sidebar(array(
            'name'          => 'Футер Колонка 1',
            'id'            => 'footer-1',
            'description'   => 'Виджеты для первой колонки футера.',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-widget-title">',
            'after_title'   => '</h3>',
        ));
        
        register_sidebar(array(
            'name'          => 'Футер Колонка 2',
            'id'            => 'footer-2',
            'description'   => 'Виджеты для второй колонки футера.',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-widget-title">',
            'after_title'   => '</h3>',
        ));
    }
    add_action('widgets_init', 'severcon_widgets_init');
}

// Кастомные размеры изображений
if (!function_exists('severcon_custom_image_sizes')) {
    function severcon_custom_image_sizes() {
        add_image_size('severcon-thumbnail', 300, 200, true);
        add_image_size('severcon-medium', 600, 400, true);
        add_image_size('severcon-large', 1200, 800, true);
    }
    add_action('after_setup_theme', 'severcon_custom_image_sizes');
}

// Подключение дополнительных файлов
require_once get_template_directory() . '/inc/simple-filters.php';

// AJAX для загрузки новостей (ОСТАВЛЯЕМ ТОЛЬКО ЭТУ ОДНУ ФУНКЦИЮ)
if (!function_exists('severcon_load_more_news')) {
    add_action('wp_ajax_load_more_news', 'severcon_load_more_news');
    add_action('wp_ajax_nopriv_load_more_news', 'severcon_load_more_news');
    
    function severcon_load_more_news() {
        // Проверка безопасности
        if (!check_ajax_referer('severcon_load_more_nonce', 'nonce', false)) {
            wp_send_json_error('Security check failed');
            wp_die();
        }
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        
        // Аргументы для нового запроса
        $args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => get_option('posts_per_page'),
            'paged'          => $page,
        );
        
        // Если есть категория
        if (!empty($_POST['category'])) {
            $args['cat'] = intval($_POST['category']);
        }
        
        // Если есть тег
        if (!empty($_POST['tag'])) {
            $args['tag_id'] = intval($_POST['tag']);
        }
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) :
            ob_start();
            
            while ($query->have_posts()) : $query->the_post();
                // Используем ваш стандартный шаблон для записей
                // Убедитесь что этот файл существует: template-parts/content.php
                get_template_part('template-parts/content', get_post_type());
            endwhile;
            
            wp_reset_postdata();
            
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

}

// WooCommerce совместимость
if (class_exists('WooCommerce')) {
    // Удаляем дублирующие обертки WooCommerce
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    
    // Добавляем свои обертки
    add_action('woocommerce_before_main_content', 'severcon_woocommerce_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'severcon_woocommerce_wrapper_end', 10);
    
    function severcon_woocommerce_wrapper_start() {
        echo '<main id="primary" class="site-main"><div class="container">';
    }
    
    function severcon_woocommerce_wrapper_end() {
        echo '</div></main>';
    }
    
    // Регистрация областей виджетов WooCommerce
    function severcon_woocommerce_widgets_init() {
        register_sidebar(array(
            'name'          => 'Сайдбар WooCommerce',
            'id'            => 'woocommerce-sidebar',
            'description'   => 'Виджеты для страниц WooCommerce.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
    }
    add_action('widgets_init', 'severcon_woocommerce_widgets_init');
}

// Исправление предупреждения WooCommerce о ранней загрузке переводов
add_action('after_setup_theme', function() {
    if (class_exists('WooCommerce')) {
        load_theme_textdomain('woocommerce', get_template_directory() . '/languages/woocommerce');
    }
}, 20);

// Кастомные функции для темы

// Ограничение длины excerpt
if (!function_exists('severcon_excerpt_length')) {
    function severcon_excerpt_length($length) {
        return 20;
    }
    add_filter('excerpt_length', 'severcon_excerpt_length', 999);
}

// Замена [...] в excerpt
if (!function_exists('severcon_excerpt_more')) {
    function severcon_excerpt_more($more) {
        return '...';
    }
    add_filter('excerpt_more', 'severcon_excerpt_more');
}

// Добавление классов к тегу body
if (!function_exists('severcon_body_classes')) {
    function severcon_body_classes($classes) {
        // Добавляем класс, если используется боковая панель
        if (is_active_sidebar('sidebar-1')) {
            $classes[] = 'has-sidebar';
        }
        
        return $classes;
    }
    add_filter('body_class', 'severcon_body_classes');
}

// Удаление префикса в заголовках архивов
if (!function_exists('severcon_archive_title')) {
    function severcon_archive_title($title) {
        if (is_category()) {
            $title = single_cat_title('', false);
        } elseif (is_tag()) {
            $title = single_tag_title('', false);
        } elseif (is_author()) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        } elseif (is_post_type_archive()) {
            $title = post_type_archive_title('', false);
        } elseif (is_tax()) {
            $title = single_term_title('', false);
        }
        
        return $title;
    }
    add_filter('get_the_archive_title', 'severcon_archive_title');
}

// Пагинация для кастомных запросов
if (!function_exists('severcon_pagination')) {
    function severcon_pagination($query = null) {
        if (!$query) {
            global $wp_query;
            $query = $wp_query;
        }
        
        $big = 999999999; // need an unlikely integer
        
        $pages = paginate_links(array(
            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    => '?paged=%#%',
            'current'   => max(1, get_query_var('paged')),
            'total'     => $query->max_num_pages,
            'type'      => 'array',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
        ));
        
        if (is_array($pages)) {
            echo '<nav class="pagination"><ul>';
            foreach ($pages as $page) {
                echo '<li>' . $page . '</li>';
            }
            echo '</ul></nav>';
        }
    }
}

// Хлебные крошки
if (!function_exists('severcon_breadcrumbs')) {
    function severcon_breadcrumbs() {
        if (!is_home()) {
            echo '<nav class="breadcrumbs">';
            echo '<a href="' . home_url() . '">Главная</a> / ';
            
            if (is_category() || is_single()) {
                the_category(' / ');
                if (is_single()) {
                    echo ' / ';
                    the_title();
                }
            } elseif (is_page()) {
                echo the_title();
            }
            
            echo '</nav>';
        }
    }
}

// Оптимизация вывода
if (!function_exists('severcon_cleanup_head')) {
    function severcon_cleanup_head() {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
    }
    add_action('init', 'severcon_cleanup_head');
}

// Удаление версии WordPress из скриптов и стилей
if (!function_exists('severcon_remove_version')) {
    function severcon_remove_version($src) {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    add_filter('style_loader_src', 'severcon_remove_version', 9999);
    add_filter('script_loader_src', 'severcon_remove_version', 9999);
}

// Поддержка SVG
if (!function_exists('severcon_svg_support')) {
    function severcon_svg_support($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
    add_filter('upload_mimes', 'severcon_svg_support');
}

// Редирект после поиска на страницу /search/
if (!function_exists('severcon_search_redirect')) {
    function severcon_search_redirect() {
        if (is_search() && !empty($_GET['s'])) {
            wp_redirect(home_url('/search/?q=') . urlencode(get_query_var('s')));
            exit();
        }
    }
    add_action('template_redirect', 'severcon_search_redirect');
}

// Кастомный логотип для входа в админку
if (!function_exists('severcon_login_logo')) {
    function severcon_login_logo() { ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.png);
                height: 65px;
                width: 320px;
                background-size: contain;
                background-repeat: no-repeat;
                padding-bottom: 30px;
            }
        </style>
    <?php }
    add_action('login_enqueue_scripts', 'severcon_login_logo');
}

// Изменение URL логотипа на входе
if (!function_exists('severcon_login_logo_url')) {
    function severcon_login_logo_url() {
        return home_url();
    }
    add_filter('login_headerurl', 'severcon_login_logo_url');
}

// Изменение title логотипа на входе
if (!function_exists('severcon_login_logo_url_title')) {
    function severcon_login_logo_url_title() {
        return get_bloginfo('name');
    }
    add_filter('login_headertext', 'severcon_login_logo_url_title');
}

// Отключение Gutenberg для определенных типов постов
if (!function_exists('severcon_disable_gutenberg')) {
    function severcon_disable_gutenberg($can_edit, $post_type) {
        if ($post_type === 'page') {
            return false;
        }
        return $can_edit;
    }
    add_filter('use_block_editor_for_post_type', 'severcon_disable_gutenberg', 10, 2);
}

// Кастомный тип поста для портфолио (пример)
if (!function_exists('severcon_register_portfolio')) {
    function severcon_register_portfolio() {
        $labels = array(
            'name'               => 'Портфолио',
            'singular_name'      => 'Работа',
            'menu_name'          => 'Портфолио',
            'add_new'            => 'Добавить работу',
            'add_new_item'       => 'Добавить новую работу',
            'edit_item'          => 'Редактировать работу',
            'new_item'           => 'Новая работа',
            'view_item'          => 'Просмотр работы',
            'search_items'       => 'Поиск работ',
            'not_found'          => 'Работ не найдено',
            'not_found_in_trash' => 'В корзине работ не найдено',
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'portfolio'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        );
        
        register_post_type('portfolio', $args);
    }
    // add_action('init', 'severcon_register_portfolio'); // Раскомментируйте если нужно
}

// Шорткод для вывода контактной информации
if (!function_exists('severcon_contact_shortcode')) {
    function severcon_contact_shortcode($atts) {
        $atts = shortcode_atts(array(
            'phone' => get_theme_mod('contact_phone', ''),
            'email' => get_theme_mod('contact_email', ''),
        ), $atts);
        
        $output = '<div class="contact-info">';
        if ($atts['phone']) {
            $output .= '<p><i class="fas fa-phone"></i> ' . $atts['phone'] . '</p>';
        }
        if ($atts['email']) {
            $output .= '<p><i class="fas fa-envelope"></i> ' . $atts['email'] . '</p>';
        }
        $output .= '</div>';
        
        return $output;
    }
    add_shortcode('contact_info', 'severcon_contact_shortcode');
}

// Поддержка WebP изображений
if (!function_exists('severcon_webp_support')) {
    function severcon_webp_support($mimes) {
        $mimes['webp'] = 'image/webp';
        return $mimes;
    }
    add_filter('upload_mimes', 'severcon_webp_support');
}

// Конец файла functions.php
?>

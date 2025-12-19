<?php
/**
 * Simple Filters - Оптимизированная версия
 * Быстрая работа, правильное скрытие недоступных фильтров
 */

if (!defined('ABSPATH')) {
    exit;
}

// ==================== ОСНОВНАЯ ФУНКЦИЯ ВЫВОДА ФИЛЬТРОВ ====================
function severcon_display_category_filters($category_id = null) {
    // Получаем ID категории
    if (!$category_id) {
        $term = get_queried_object();
        $category_id = is_a($term, 'WP_Term') ? $term->term_id : 0;
    }
    
    if (!$category_id) {
        return;
    }
    
    echo '<div class="compact-filters">';
    
    // Кнопка переключения
    echo '<button class="toggle-all-filters">';
    echo '<i class="fas fa-filter"></i> Фильтры';
    echo '<i class="fas fa-chevron-down toggle-icon"></i>';
    echo '</button>';
    
    echo '<div class="filters-collapse">';
    
    // Получаем и выводим фильтры
    $filters_html = get_filters_html($category_id);
    echo $filters_html;
    
    echo '</div>';
    
    // Кнопки действий
    echo '<div class="filters-actions">';
    echo '<button class="btn btn-primary apply-filters"><i class="fas fa-check"></i> Применить фильтры</button>';
    echo '<button class="btn btn-secondary reset-filters"><i class="fas fa-redo"></i> Сбросить все</button>';
    echo '</div>';
    
    echo '</div>';
}

// ==================== ПОЛУЧЕНИЕ HTML ФИЛЬТРОВ (С КЭШИРОВАНИЕМ) ====================
function get_filters_html($category_id) {
    // Пробуем получить из кэша
    $cache_key = 'severcon_filters_' . $category_id;
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    ob_start();
    
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    if (empty($attribute_taxonomies)) {
        return '<p class="no-filters">Фильтры не настроены</p>';
    }
    
    foreach ($attribute_taxonomies as $attribute) {
        display_optimized_filter_group($attribute, $category_id);
    }
    
    $html = ob_get_clean();
    
    // Сохраняем в кэш на 1 час
    set_transient($cache_key, $html, HOUR_IN_SECONDS);
    
    return $html;
}

// ==================== ОПТИМИЗИРОВАННЫЙ ВЫВОД ГРУППЫ ====================
function display_optimized_filter_group($attribute, $category_id) {
    $taxonomy = 'pa_' . $attribute->attribute_name;
    $attribute_label = $attribute->attribute_label ?: $attribute->attribute_name;
    $attribute_slug = 'filter-' . sanitize_key($attribute->attribute_name);
    
    // Получаем термины для категории (быстрый способ)
    $terms = get_terms_for_category_fast($taxonomy, $category_id);
    
    if (empty($terms)) {
        return;
    }
    
    echo '<div class="compact-filter-group" data-attribute="' . esc_attr($attribute->attribute_name) . '">';
    
    echo '<div class="filter-group-header">';
    echo '<button class="filter-group-toggle" data-target="' . esc_attr($attribute_slug) . '">';
    echo '<span class="filter-group-title">' . esc_html($attribute_label) . '</span>';
    echo '<i class="fas fa-chevron-down toggle-icon"></i>';
    echo '</button>';
    echo '</div>';
    
    echo '<div class="filter-group-content" id="' . esc_attr($attribute_slug) . '" style="display: none;">';
    echo '<div class="filter-items">';
    
    $counter = 0;
    $initial_limit = 5;
    
    foreach ($terms as $term) {
        $counter++;
        $is_hidden = $counter > $initial_limit;
        
        echo '<label class="filter-item' . ($is_hidden ? ' hidden-term' : '') . '" 
                     data-term-slug="' . esc_attr($term->slug) . '"
                     data-original-count="' . esc_attr($term->count) . '">';
        echo '<input type="checkbox" 
                     name="' . esc_attr($taxonomy) . '[]" 
                     value="' . esc_attr($term->slug) . '" 
                     class="filter-checkbox">';
        echo '<span class="filter-item-text">' . esc_html($term->name) . '</span>';
        echo '<span class="filter-item-count">(' . esc_html($term->count) . ')</span>';
        echo '</label>';
    }
    
    echo '</div>';
    
    if (count($terms) > $initial_limit) {
        echo '<button class="show-more-terms" data-target="' . esc_attr($attribute_slug) . '">';
        echo '<span class="show-more-text">Показать еще ' . (count($terms) - $initial_limit) . '</span>';
        echo '<span class="show-less-text" style="display: none;">Скрыть</span>';
        echo '<i class="fas fa-chevron-down"></i>';
        echo '</button>';
    }
    
    echo '</div>';
    echo '</div>';
}

// ==================== БЫСТРЫЙ СПОСОБ ПОЛУЧЕНИЯ ТЕРМИНОВ ====================
function get_terms_for_category_fast($taxonomy, $category_id) {
    global $wpdb;
    
    // Быстрый запрос для получения терминов с количеством товаров в категории
    $query = $wpdb->prepare(
        "SELECT t.term_id, t.name, t.slug, COUNT(DISTINCT tr.object_id) as count
         FROM {$wpdb->terms} t
         INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
         INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
         INNER JOIN {$wpdb->term_relationships} tr_cat ON tr.object_id = tr_cat.object_id
         INNER JOIN {$wpdb->term_taxonomy} tt_cat ON tr_cat.term_taxonomy_id = tt_cat.term_taxonomy_id
         WHERE tt.taxonomy = %s
         AND tt_cat.taxonomy = 'product_cat'
         AND tt_cat.term_id = %d
         GROUP BY t.term_id
         HAVING count > 0
         ORDER BY t.name ASC",
        $taxonomy,
        $category_id
    );
    
    $results = $wpdb->get_results($query);
    
    if (empty($results)) {
        // Альтернативный способ через API WordPress (медленнее)
        $terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ));
        
        if (is_wp_error($terms) || empty($terms)) {
            return array();
        }
        
        // Фильтруем только те, что есть в категории
        $filtered_terms = array();
        foreach ($terms as $term) {
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'post_status'    => 'publish',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $category_id,
                    ),
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $term->slug
                    )
                )
            );
            
            $query = new WP_Query($args);
            if ($query->found_posts > 0) {
                $term->count = $query->found_posts;
                $filtered_terms[] = $term;
            }
        }
        
        return $filtered_terms;
    }
    
    return $results;
}

// ==================== AJAX: ОБНОВЛЕНИЕ ДОСТУПНЫХ ФИЛЬТРОВ ====================
add_action('wp_ajax_update_filter_counts', 'severcon_update_filter_counts_fast');
add_action('wp_ajax_nopriv_update_filter_counts', 'severcon_update_filter_counts_fast');

function severcon_update_filter_counts_fast() {
    // Быстрая проверка
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'filter_nonce')) {
        wp_send_json_success(array()); // Возвращаем пустой массив вместо ошибки
        wp_die();
    }
    
    $category_id = intval($_POST['category_id']);
    $active_filters = $_POST['filters'] ?: array();
    
    if (!$category_id) {
        wp_send_json_success(array());
        wp_die();
    }
    
    // Быстрый расчет доступных фильтров
    $available_counts = calculate_available_filters_fast($category_id, $active_filters);
    
    wp_send_json_success($available_counts);
    wp_die();
}

// ==================== БЫСТРЫЙ РАСЧЕТ ДОСТУПНЫХ ФИЛЬТРОВ ====================
function calculate_available_filters_fast($category_id, $active_filters) {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    $result = array();
    
    // Если нет активных фильтров, возвращаем все как есть
    if (empty($active_filters)) {
        foreach ($attribute_taxonomies as $attribute) {
            $taxonomy = 'pa_' . $attribute->attribute_name;
            $terms = get_terms_for_category_fast($taxonomy, $category_id);
            
            foreach ($terms as $term) {
                $result[$taxonomy][$term->slug] = $term->count;
            }
        }
        
        return $result;
    }
    
    // С активными фильтрами - упрощенный расчет
    // Для скорости: скрываем только те фильтры, которые точно не подходят
    
    // 1. Получаем ID товаров по активным фильтрам
    $product_ids = get_filtered_product_ids($category_id, $active_filters);
    
    if (empty($product_ids)) {
        // Если товаров нет, возвращаем только выбранные фильтры
        foreach ($active_filters as $taxonomy => $selected_terms) {
            foreach ($selected_terms as $term_slug) {
                $result[$taxonomy][$term_slug] = 0;
            }
        }
        return $result;
    }
    
    // 2. Для каждого атрибута считаем сколько товаров с каждым термином
    foreach ($attribute_taxonomies as $attribute) {
        $taxonomy = 'pa_' . $attribute->attribute_name;
        $terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ));
        
        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }
        
        foreach ($terms as $term) {
            // Проверяем, есть ли товары с этим термином
            $count = count_products_with_term_fast($product_ids, $taxonomy, $term->slug);
            
            // Включаем если есть товары ИЛИ термин уже выбран
            $is_selected = isset($active_filters[$taxonomy]) && in_array($term->slug, $active_filters[$taxonomy]);
            
            if ($count > 0 || $is_selected) {
                $result[$taxonomy][$term->slug] = $count;
            }
        }
    }
    
    return $result;
}

// ==================== ПОЛУЧЕНИЕ ID ТОВАРОВ ПО ФИЛЬТРАМ ====================
function get_filtered_product_ids($category_id, $filters) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            )
        )
    );
    
    foreach ($filters as $taxonomy => $terms) {
        if (!empty($terms)) {
            $args['tax_query'][] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $terms,
                'operator' => 'IN'
            );
        }
    }
    
    if (count($args['tax_query']) > 1) {
        $args['tax_query']['relation'] = 'AND';
    }
    
    $query = new WP_Query($args);
    return $query->posts;
}

// ==================== БЫСТРЫЙ ПОДСЧЕТ ТОВАРОВ С ТЕРМИНОМ ====================
function count_products_with_term_fast($product_ids, $taxonomy, $term_slug) {
    if (empty($product_ids)) {
        return 0;
    }
    
    global $wpdb;
    
    $product_ids_string = implode(',', array_map('intval', $product_ids));
    
    $query = $wpdb->prepare(
        "SELECT COUNT(DISTINCT tr.object_id)
         FROM {$wpdb->term_relationships} tr
         INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
         INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
         WHERE tr.object_id IN ($product_ids_string)
         AND tt.taxonomy = %s
         AND t.slug = %s",
        $taxonomy,
        $term_slug
    );
    
    return (int) $wpdb->get_var($query);
}


function display_filter_group_fast($attribute, $terms) {
    $taxonomy = 'pa_' . $attribute->attribute_name;
    $attribute_label = $attribute->attribute_label ?: $attribute->attribute_name;
    $attribute_slug = 'filter-' . sanitize_key($attribute->attribute_name);
    
    // Определяем тип раскладки по количеству терминов
    $items_count = count($terms);
    $layout_type = get_layout_type($items_count);
    $show_more = $items_count > 8; // Показывать кнопку "Ещё" если больше 8
    
    ?>
    <div class="filter-group-wrapper" data-layout="<?php echo esc_attr($layout_type); ?>">
        <div class="compact-filter-group" 
             data-attribute="<?php echo esc_attr($attribute->attribute_name); ?>"
             data-total-items="<?php echo $items_count; ?>">
            
            <!-- Заголовок группы -->
            <div class="filter-group-header">
                <button class="filter-group-toggle" data-target="<?php echo esc_attr($attribute_slug); ?>">
                    <span class="filter-group-title"><?php echo esc_html($attribute_label); ?></span>
                    <span class="filter-group-count">(<?php echo $items_count; ?>)</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </button>
                
                <!-- Быстрые действия группы -->
                <div class="group-actions">
                    <button class="group-action-select-all" title="Выбрать все">
                        <i class="fas fa-check-double"></i>
                    </button>
                    <button class="group-action-reset" title="Сбросить">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
            
            <!-- Контент группы -->
            <div class="filter-group-content" id="<?php echo esc_attr($attribute_slug); ?>" style="display: none;">
                <!-- Поиск для длинных списков -->
                <?php if ($items_count > 12): ?>
                <div class="filter-search-container">
                    <input type="text" 
                           class="filter-search" 
                           placeholder="Поиск в <?php echo strtolower($attribute_label); ?>..."
                           data-attribute="<?php echo esc_attr($attribute->attribute_name); ?>">
                    <i class="fas fa-search"></i>
                </div>
                <?php endif; ?>
                
                <!-- Сетка фильтров -->
                <div class="filter-grid" id="grid-<?php echo esc_attr($attribute_slug); ?>">
                    <?php 
                    $counter = 0;
                    foreach ($terms as $term):
                        $counter++;
                        $is_hidden = $show_more && $counter > 8;
                    ?>
                    <label class="filter-grid-item <?php echo $is_hidden ? 'initially-hidden' : ''; ?>" 
                           data-term-slug="<?php echo esc_attr($term->slug); ?>"
                           data-term-name="<?php echo esc_attr($term->name); ?>"
                           data-original-count="<?php echo esc_attr($term->count); ?>">
                        
                        <!-- Кастомный чекбокс -->
                        <div class="custom-checkbox">
                            <input type="checkbox" 
                                   name="<?php echo esc_attr($taxonomy); ?>[]" 
                                   value="<?php echo esc_attr($term->slug); ?>" 
                                   class="filter-checkbox"
                                   id="filter-<?php echo esc_attr($attribute_slug); ?>-<?php echo esc_attr($term->slug); ?>">
                            <span class="checkmark"></span>
                        </div>
                        
                        <!-- Текст и счетчик -->
                        <div class="filter-item-content">
                            <span class="filter-item-text"><?php echo esc_html($term->name); ?></span>
                            <span class="filter-item-count"><?php echo esc_html($term->count); ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <!-- Управление показом -->
                <?php if ($show_more): ?>
                <div class="filter-grid-controls">
                    <button class="show-more-filters" data-target="<?php echo esc_attr($attribute_slug); ?>">
                        <span class="show-more-text">
                            Показать ещё <?php echo ($items_count - 8); ?> 
                            <i class="fas fa-chevron-down"></i>
                        </span>
                        <span class="show-less-text">
                            Скрыть <i class="fas fa-chevron-up"></i>
                        </span>
                    </button>
                    <span class="filtered-count" style="display: none;"></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Функция определения типа раскладки
function get_layout_type($items_count) {
    if ($items_count <= 4) return 'single-row';     // Одна строка
    if ($items_count <= 8) return 'two-columns';    // Две колонки
    if ($items_count <= 15) return 'three-columns'; // Три колонки
    return 'four-columns';                          // Четыре колонки
}
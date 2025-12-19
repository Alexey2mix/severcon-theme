<?php
/**
 * Single Product Template
 */
get_header();

while (have_posts()) : the_post();
    global $product;
    ?>
    
    <div class="container single-product">
        <div class="row">
            <div class="col-md-6">
                <?php
                // Галерея изображений
                do_action('woocommerce_before_single_product_summary');
                ?>
            </div>
            
            <div class="col-md-6">
                <div class="product-summary">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <?php
                    // Рейтинг
                    if (wc_review_ratings_enabled()) {
                        echo wc_get_rating_html($product->get_average_rating());
                    }
                    
                    // Описание
                    the_content();
                    
                    // Атрибуты
                    $attributes = $product->get_attributes();
                    if (!empty($attributes)) {
                        echo '<div class="product-attributes">';
                        echo '<h3>' . __('Характеристики', 'severcon') . '</h3>';
                        foreach ($attributes as $attribute) {
                            if ($attribute->get_visible()) {
                                $name = $attribute->get_name();
                                $options = $attribute->get_options();
                                
                                if (!empty($options)) {
                                    echo '<div class="attribute-row">';
                                    echo '<span class="attribute-label">' . esc_html(wc_attribute_label($name)) . ':</span> ';
                                    echo '<span class="attribute-value">' . esc_html(implode(', ', $options)) . '</span>';
                                    echo '</div>';
                                }
                            }
                        }
                        echo '</div>';
                    }
                    
                    // Кнопка запроса цены
                    echo '<button class="btn btn-primary price-request-btn" data-product-id="' . $product->get_id() . '">';
                    echo __('Запросить цену', 'severcon');
                    echo '</button>';
                    ?>
                </div>
            </div>
        </div>
        
        <?php
        // Дополнительная информация
        do_action('woocommerce_after_single_product_summary');
        ?>
    </div>
    
<?php endwhile;

get_footer();

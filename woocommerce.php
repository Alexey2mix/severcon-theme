<?php
/**
 * WooCommerce Template
 */
get_header(); 
?>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <?php 
            if (is_shop() || is_product_category() || is_product_tag()) {
                wc_get_template('archive-product.php');
            } elseif (is_product()) {
                wc_get_template('single-product.php');
            }
            ?>
        </div>
        <div class="col-md-3">
            <?php 
            if (is_active_sidebar('sidebar-shop')) {
                dynamic_sidebar('sidebar-shop');
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

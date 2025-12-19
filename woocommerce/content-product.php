<?php
/**
 * The template for displaying product content within loops
 */
defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('', $product); ?>>
    <?php
    /**
     * Hook: woocommerce_before_shop_loop_item.
     */
    do_action('woocommerce_before_shop_loop_item');
    
    /**
     * Hook: woocommerce_before_shop_loop_item_title.
     */
    do_action('woocommerce_before_shop_loop_item_title');
    
    /**
     * Hook: woocommerce_shop_loop_item_title.
     */
    do_action('woocommerce_shop_loop_item_title');
    
    /**
     * Hook: woocommerce_after_shop_loop_item_title.
     */
    do_action('woocommerce_after_shop_loop_item_title');
    
    /**
     * Hook: woocommerce_after_shop_loop_item.
     */
    do_action('woocommerce_after_shop_loop_item');
    ?>
</li>

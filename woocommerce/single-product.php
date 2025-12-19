<?php
/**
 * The Template for displaying single products
 */
defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 */
do_action('woocommerce_before_main_content');

while (have_posts()) :
    the_post();
    
    wc_get_template_part('content', 'single-product');
    
endwhile;

/**
 * Hook: woocommerce_after_main_content.
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 */
do_action('woocommerce_sidebar');

get_footer('shop');

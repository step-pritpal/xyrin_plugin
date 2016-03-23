<?php
/**
 * Plugin Name: XyrinTech Plugin
 * Description: Plugin for changes in the website
 * Version: 1.0 (Dev)
 * Author: Pritpal Singh
 */
defined( 'ABSPATH' ) or die( 'No Direct Access Allowed' );

//include the dependency files
require_once trailingslashit(__DIR__) . 'config.php';
require_once trailingslashit(__DIR__) . 'functions.php';

//sort popular products by default
add_action('wp_enqueue_scripts', 'xt_popular_sorting_js');

//vendor dashboard visible to vendor users only
add_filter('wp_nav_menu_objects', 'xt_handle_seller_dashboard', 10, 2);

//menu order and mobile friendly
{
    add_filter('pre_wp_nav_menu', 'xt_top_menu_mobile_friendly', 10, 2);
    add_action('wp_enqueue_scripts', 'xt_top_menu_mobile_friendly_scripts');
}

//WC Vendors Plugin changes
{
    //show from and to dates by default
    add_action('wp_enqueue_scripts', 'xt_show_product_date_fields');

    //change the label of From date field
    add_filter('wcv_product_sale_price_date_from', 'xt_change_product_from_date_label');

    //remove the to date field from add product form
    add_filter('wcv_product_sale_price_date_to', 'xt_remove_product_to_date_field');
}

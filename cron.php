<?php
include rtrim(realpath(__DIR__ . "//../../../"), '/') . "/wp-load.php";
defined('XT_PLUGIN_ACTIVE') or wp_die("Plugin is not active, Please activate the plugin <b><i>XyrinTech Plugin</i></b>");

//set defaults
{
    $ending_soon_category_slug = 'ending-soon';
    $new_deals_category_slug = 'new-deals';

    $product_post_type = 'product';
    $product_cat_slug = 'product_cat';
    
    $time = time();
}

//get Ending Soon category id
{
    $category = get_term_by('slug', $ending_soon_category_slug, $product_cat_slug);
    if(!$category)
        wp_die("<b>Ending Soon</b> Category not found. Add the category and try again...");
    $ending_soon_category_id = $category->term_id;
}

//get New Deals category id
{
    $category = get_term_by('slug', $new_deals_category_slug, $product_cat_slug);
    if(!$category)
        wp_die("<b>New Deals</b> Category not found. Add the category and try again...");
    $new_deals_category_id = $category->term_id;
}

//get all products
$products = get_posts(array(
    'posts_per_page' => -1,
    'post_type' => $product_post_type,
));
if(empty($products))
    wp_die("No products found. Add products and try again.");
foreach($products as $product) {
    //set global post
    {
        global $post;
        $post = $product;
    }

    //product id
    $product_id = get_the_id();

    //remove the ending soon and new deals category
    {
        wp_remove_object_terms($product_id, array(
            $ending_soon_category_id,
            $new_deals_category_id,
        ), $product_cat_slug);
    }

    //get the post add time
    $product_add_time = strtotime(get_the_date('Y-m-d H:i:s'));

    //assign new deal category if post is new (<24 hours)
    {
        if($time - $product_add_time < 24 * 60 * 60)
            wp_set_object_terms($product_id, $new_deals_category_id, $product_cat_slug);
    }

    //assign ending soon category if post is ending soon (<24 hours)
    {
        $end_date = get_post_meta($product_id, '_expiration-date', true);
        if(is_numeric($end_date) && $end_date > $time && $end_date - $time < 24 * 60 * 60)
            wp_set_object_terms($product_id, $ending_soon_category_slug, $product_cat_slug);
    }
}
wp_die("CRON script is successfully executed.", "Cron Job Complete");
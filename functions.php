<?php

if(!function_exists('pr')) {
    function pr($e) {
        echo "<pre>";
        print_r($e);
        echo "</pre>";
    }
}

if(!function_exists('vd')) {
    function vd($e) {
        echo "<pre>";
        var_dump($e);
        echo "</pre>";
    }
}

function xt_popular_sorting_js () {

    //get menu items
    {
        //menu location
        $location = 'category';

        $menu_items = xt_get_location_menu_items($location);
        if(!$menu_items)
            return;
    }

    //get popular cat
    {
        $popular_cat = get_term_by('slug', 'popular', 'product_cat');
        if(!$popular_cat)
            return;
        $popular_cat_id = $popular_cat->term_id;
    }

    //get All Deals Category
    {
        $all_deals_cat = get_term_by('slug', 'all-deals', 'product_cat');
        if(!$all_deals_cat)
            return;
        $all_deals_cat_link = get_term_link($all_deals_cat->term_id, 'product_cat');
    }

    $menu_item_id = null;
    //traverse through menu items to get the menu id for jquery selector
    {
        foreach ($menu_items as $menu_item) {
            if($menu_item->object_id == $popular_cat_id) {
                $menu_item_id = $menu_item->ID;
                break;
            }
        }
    }
    
    //skip if we don't have our menu in the list
    {
        if(!$menu_item_id)
            return;
    }

    //enqueue script for popular sorting
    {
        $handle = 'xt_popular_sorting_script';

        $popular_sort_js = 'js/popular_sort.js';

        $popular_js_path = XT_FS_PATH . $popular_sort_js;
        $popular_js_url = XT_WS_PATH . $popular_sort_js;

        $version = filemtime($popular_js_path);

        wp_register_script($handle, $popular_js_url , array('jquery'), $version, true);
        @wp_localize_script($handle, 'menu_item_id', $menu_item_id);
        @wp_localize_script($handle, 'all_deals_cat_link', $all_deals_cat_link);
        wp_enqueue_script($handle);
    }
}

function xt_handle_seller_dashboard ($items, $args) {
    if ($args->menu === 'top-menu' && !empty($items)) {
        if(!in_array('vendor', wp_get_current_user()->roles)) {
            //shortcode to search
            $shortcode_to_seacrh = '[wcv_pro_dashboard]';
    
            //get pages
            {
                $pages = get_posts(array(
                    'post_type' => 'page',
                    'posts_per_page' => -1,
                    's' => $shortcode_to_seacrh,
                    'fields' => 'ids',
                ));
            }
    
            //proceed if the pages are there
            if (is_array($pages) && !empty($pages)) {
                //get page link to be removed
                $permalink = get_permalink($pages[0]);
    
                //traverse items and remove the required item
                foreach ($items as $k => $item) {
                    if($item->url === $permalink) {
                        unset($items[$k]);
                        break;
                    }
                }
            }
        }
    }
    return $items;
}

function xt_top_menu_mobile_friendly ($nav_menu, $args) {
    $menu = 'menu-category';
    if($args->menu !== $menu)
        return $nav_menu;
    $location = 'category';
    if($args->theme_location !== $location)
        return $nav_menu;
    
    $menu_items = xt_get_location_menu_items($location);
    if(!is_array($menu_items) || empty($menu_items) || count($menu_items) <= 5)
        return $nav_menu;


    //move items except first 4 to another array
    {
        $special_items = array();
        $temp = array();
        $num = 0;
        foreach ($menu_items as $key => $menu_item) {
            if(++$num <= 4) continue;
            $special_items[$key] = $menu_item;
            unset($menu_items[$key]);
        }
    }

    $nav_menu = $items = '';
    

    $show_container = false;
    if ( $args->container ) {
        $allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
        if ( is_string( $args->container ) && in_array( $args->container, $allowed_tags ) ) {
            $show_container = true;
            $class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
            $id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
            $nav_menu .= '<'. $args->container . $id . $class . '>';
        }
    }

    // Set up the $menu_item variables
    _wp_menu_item_classes_by_context( $menu_items );

    $sorted_menu_items = $menu_items_with_children = array();

    foreach ( (array) $menu_items as $menu_item ) {
        $sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
        if ( $menu_item->menu_item_parent )
            $menu_items_with_children[ $menu_item->menu_item_parent ] = true;
    }

    // Add the menu-item-has-children class where applicable
    if ( $menu_items_with_children ) {
        foreach ( $sorted_menu_items as &$menu_item ) {
            if ( isset( $menu_items_with_children[ $menu_item->ID ] ) )
                $menu_item->classes[] = 'menu-item-has-children';
        }
    }

    unset( $menu_items, $menu_item );

    $sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

    $items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
    unset($sorted_menu_items);

    $menu_id_slugs = array();

    // Attributes
    if ( ! empty( $args->menu_id ) ) {
        $wrap_id = $args->menu_id;
    } else {
        $wrap_id = 'menu-' . $menu->slug;
        while ( in_array( $wrap_id, $menu_id_slugs ) ) {
            if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) )
                $wrap_id = preg_replace('#-(\d+)$#', '-' . ++$matches[1], $wrap_id );
            else
                $wrap_id = $wrap_id . '-1';
        }
    }
    $menu_id_slugs[] = $wrap_id;

    $wrap_class = $args->menu_class ? $args->menu_class : '';

    $items = apply_filters( 'wp_nav_menu_items', $items, $args );
    $items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );

    // Don't print any markup if there are no items at this point.
    if ( empty( $items ) )
        return null;

    $nav_menu .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
    unset( $items );

    //special menu items
    {
        $menu_items = $special_items;
        _wp_menu_item_classes_by_context($menu_items);

        //get special menus heading
        {
            $special_items_heading = current($special_items);
            foreach ($menu_items as $menu_item)
                if(is_array($menu_item->classes) && !empty($menu_item->classes) && in_array('current-menu-item', $menu_item->classes))
                    $special_items_heading = $menu_item;
        }

        $nav_menu .= '<div class="xt_extra_menus_container">';
        $nav_menu .= '<a href="#" class="" tabindex="-1" data-toggle="dropdown" style="font-family: \'Montserrat\', sans-serif;color: #555;">' . strtoupper($special_items_heading->title) . ' <i class="fa fa-chevron-down"></i></a>';
        $nav_menu .= '<ul class="category-menu style2">';
        foreach($menu_items as $menu_item) {

            $menu_classes = array_filter($menu_item->classes);
            $menu_classes[] = "menu-item-$menu_item->ID";
            $menu_classes = implode(" ", $menu_classes);

            $nav_menu .= '<li id="menu-item-' . $menu_item->ID . '" class="' . $menu_classes . '"><a href="' . $menu_item->url . '">' . strtoupper($menu_item->title) . '</a></li>';
        }
        $nav_menu .= '</ul>';
        $nav_menu .= '</div>';

    }

    if ( $show_container )
        $nav_menu .= '</' . $args->container . '>';

    return $nav_menu;
}

function xt_top_menu_mobile_friendly_scripts () {
    //css
    {
        $src = XT_WS_PATH . "css/mobile_friendly.css";
        $ver = filemtime(XT_FS_PATH . "css/mobile_friendly.css");
        wp_enqueue_style('xt_mobile_friendly_css', $src, array(), $ver);
    }

    //js
    {
        $src = XT_WS_PATH . "js/mobile_friendly.js";
        $ver = filemtime(XT_FS_PATH . "js/mobile_friendly.js");
        wp_enqueue_script('xt_mobile_friendly_js', $src, array('jquery'), $ver, true);
    }
}

function kutetheme_ovic_catmenu( $menu_class = 'category-menu' ) { 
    wp_nav_menu( array(
            'menu'              => 'menu-category',
            'theme_location'    => 'category',
            'container'         => false,
            'menu_class'        => rtrim("xt-category-main-menu " . $menu_class),
            'fallback_cb'       => 'kt_bootstrap_navwalker::fallback',
            'walker'            => new kutetheme_ovic_bootstrap_navwalker()
        )
     );

    wp_nav_menu( array(
            'menu'              => 'menu-category-mobile',
            'theme_location'    => 'category',
            'container'         => false,
            'menu_class'        => rtrim("xt-category-mobile-menu " . $menu_class),
            'fallback_cb'       => 'kt_bootstrap_navwalker::fallback',
            'walker'            => new kutetheme_ovic_bootstrap_navwalker()
        )
     );
}

function xt_get_location_menu_items($location) {

    $locations = get_registered_nav_menus();
    $menus = wp_get_nav_menus();
    $menu_locations = get_nav_menu_locations();

    if (isset($menu_locations[ $location ]))
    	foreach ($menus as $menu)
    		if ($menu->term_id == $menu_locations[ $location ])
    			return wp_get_nav_menu_items($menu);
    return false;
}

function xt_show_product_date_fields () {
    //get vendor page
    {
        //shortcode to search
        $shortcode_to_seacrh = '[wcv_pro_dashboard]';

        //get pages
        {
            $pages = get_posts(array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                's' => $shortcode_to_seacrh,
                'fields' => 'ids',
            ));
            if(!isset($pages[0]))
                return;
        }
        $page_id = $pages[0];
    }

    //get current post
    global $post;
    if(!is_object($post))
        return;
    if($page_id !== $post->ID)
        return;
    $src = XT_WS_PATH . "js/show_product_date_fields.js";
    $ver = filemtime(XT_FS_PATH . "js/show_product_date_fields.js");
    wp_enqueue_script('xt_show_product_date_fields_js', $src, array('jquery'), $ver, true);
}
function xt_change_product_from_date_label ($e) {
    $e['label'] = __('Preferred Start Date', XT_DOMAIN);
    return $e;
}

function xt_remove_product_to_date_field ($e) {
    $e['label'] = __('End Date', XT_DOMAIN);
    return $e;
}
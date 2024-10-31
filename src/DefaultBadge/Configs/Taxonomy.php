<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

$aLabels = [
    'name'      => esc_html__('Keywords',  'myshopkit-product-badges-wp'),
    'singular'  => esc_html__('Keyword',  'myshopkit-product-badges-wp'),
    'menu_name' => esc_html__('Keywords',  'myshopkit-product-badges-wp'),
];

return [
    'labels'            => $aLabels,
    'hierarchical'      => false,
    'taxonomyType'      => AutoPrefix::namePrefix('keyword'),
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_nav_menus' => true,
    'show_tagcloud'     => true,
];

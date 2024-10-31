<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

$aLabels = [
    'name'           => esc_html__('Default Badges',  'myshopkit-product-badges-wp'),
    'singular_name'  => esc_html__('Default Badge',  'myshopkit-product-badges-wp'),
    'menu_name'      => esc_html__('Default Badges',  'myshopkit-product-badges-wp'),
    'name_admin_bar' => esc_html__('Default Badges',  'myshopkit-product-badges-wp'),
];

return [
    'labels'             => $aLabels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => ['slug' => AutoPrefix::namePrefix('badge')],
    'postType'           => AutoPrefix::namePrefix('badge'),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => null,
    'supports'           => ['title', 'editor', 'thumbnail', 'author']
];

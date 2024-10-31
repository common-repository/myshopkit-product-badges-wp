<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

$aLabels = [
    'name'           => esc_html__('Manuals', 'myshopkit-product-badges-wp'),
    'singular_name'  => esc_html__('Manual', 'myshopkit-product-badges-wp'),
    'menu_name'      => esc_html__('Manuals', 'myshopkit-product-badges-wp'),
    'name_admin_bar' => esc_html__('Manuals', 'myshopkit-product-badges-wp'),
];

return [
    'labels'             => $aLabels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => ['slug' => AutoPrefix::namePrefix('manual')],
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => null,
    'supports'           => ['title', 'editor', 'thumbnail', 'author'],
    'postType'           => AutoPrefix::namePrefix('manual')
];

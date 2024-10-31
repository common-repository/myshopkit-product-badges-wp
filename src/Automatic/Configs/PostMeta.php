<?php

use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

return [
    'new_arrival_general_settings_section'  => [
        'id'           => 'new_arrival_general_settings_section',
        'title'        => esc_html__('New Arrival Settings', 'myshopkit-product-badges-wp'),
        'object_types' => [AutoPrefix::namePrefix('new_arrival')],
        'fields'       => [
            'config' => [
                'name'       => esc_html__('New Arrival Configuration', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'config',
                'type'       => 'textarea'
            ],
            'badgeUrl' => [
                'name'       => esc_html__('Badge URL', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'badgeUrl',
                'type'       => 'text'
            ]
        ]
    ],
    'out_of_stock_general_settings_section' => [
        'id'           => 'out_of_stock_general_settings_section',
        'title'        => esc_html__('Out Of Stock Settings', 'myshopkit-product-badges-wp'),
        'object_types' => [AutoPrefix::namePrefix('out_of_stock')],
        'fields'       => [
            'config' => [
                'name'       => esc_html__('Out Of Stock Configuration', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'config',
                'type'       => 'textarea'
            ],
            'badgeUrl' => [
                'name'       => esc_html__('Badge URL', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'badgeUrl',
                'type'       => 'text'
            ]
        ]
    ],
    'on_sale_general_settings_section'      => [
        'id'           => 'on_sale_general_settings_section',
        'title'        => esc_html__('On Sale Settings', 'myshopkit-product-badges-wp'),
        'object_types' => [AutoPrefix::namePrefix('on_sale')],
        'fields'       => [
            'config' => [
                'name'       => esc_html__('On Sale Configuration', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'config',
                'type'       => 'textarea'
            ],
            'badgeUrl' => [
                'name'       => esc_html__('Badge URL', 'myshopkit-product-badges-wp'),
                'save_field' => true,
                'id'         => 'badgeUrl',
                'type'       => 'text'
            ]
        ]
    ]
];

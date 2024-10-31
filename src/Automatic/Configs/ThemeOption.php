<?php


$prefixNewArrival = 'default-new-arrival';
$prefixOutOfStock = 'default-out-of-stock';
$prefixOnSale = 'default-on-sale';
return [
    'new-arrival'  => [
        'title'            => esc_html__('Default New Arrival Settings',  'myshopkit-product-badges-wp'),
        'id'               => 'default-new-arrival-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixNewArrival . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixNewArrival . 'title',
                'type'    => 'text',
                'default' => esc_html__('New Arrival',  'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Title',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixNewArrival . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge on products that are added in last 7 days',
                     'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Description',  'myshopkit-product-badges-wp'),
            ]
        ]
    ],
    'out-of-stock' => [
        'title'            => esc_html__('Default Out Of Stock Settings',  'myshopkit-product-badges-wp'),
        'id'               => 'default-out-of-stock-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixOutOfStock . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixOutOfStock . 'title',
                'type'    => 'text',
                'default' => esc_html__('Out Of Stock',  'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Title',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixOutOfStock . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge when stock drops to zero',
                    'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Description',  'myshopkit-product-badges-wp'),
            ]
        ]
    ],
    'on-sale'      => [
        'title'            => esc_html__('Default On Sale Settings', 'myshopkit-product-badges-wp'),
        'id'               => 'default-on-sale-settings',
        'subsection'       => true,
        'customizer_width' => '450px',
        'fields'           => [
            [
                'id'    => $prefixOnSale . 'badgeID',
                'type'  => 'text',
                'title' => esc_html__('Badge ID',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixOnSale . 'title',
                'type'    => 'text',
                'default' => esc_html__('On Sale',  'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Title',  'myshopkit-product-badges-wp'),
            ],
            [
                'id'      => $prefixOnSale . 'description',
                'type'    => 'textarea',
                'default' => esc_html__('Show badge on products with discount crated using price rules',
                    'myshopkit-product-badges-wp'),
                'title'   => esc_html__('Description',  'myshopkit-product-badges-wp'),
            ]
        ]
    ],
];

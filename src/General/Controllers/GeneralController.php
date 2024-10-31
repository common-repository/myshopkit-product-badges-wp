<?php

namespace MyShopKitMBWP\General\Controllers;


use MyShopKitMBWP\Dashboard\Shared\Option;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class GeneralController
{
	protected static string $GLOBAL_MYSMBWP = 'GLOBAL_MYSMBWP';

	public function __construct()
	{
		add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	}

	public function enqueueScripts()
	{
		wp_localize_script('jquery', self::$GLOBAL_MYSMBWP, [
			'restBase'      => trailingslashit(rest_url(MYSHOPKIT_MB_WP_REST))
		]);

		wp_enqueue_script(
			AutoPrefix::namePrefix('product-badge-script'),
			plugin_dir_url(__FILE__) . "../Source/main.js",
			['jquery'],
			MYSHOPKIT_MB_WP_VERSION,
			true
		);


		wp_enqueue_style(
			AutoPrefix::namePrefix('product-badge-style'),
			plugin_dir_url(__FILE__) . "../Source/main.css",
			[],
			MYSHOPKIT_MB_WP_VERSION
		);

		wp_enqueue_style(
			AutoPrefix::namePrefix('extra-badge-style'),
			plugin_dir_url(__FILE__) . "../Source/style.css",
			[],
			MYSHOPKIT_MB_WP_VERSION
		);
	}
}

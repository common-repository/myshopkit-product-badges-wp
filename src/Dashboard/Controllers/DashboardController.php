<?php

namespace MyShopKitMBWP\Dashboard\Controllers;


use MyShopKitMBWP\Dashboard\Shared\GeneralHelper;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class DashboardController
{
	use GeneralHelper;

	const MYSMBWP_GLOBAL = 'MYSMBWP_GLOBAL';
	private string $purchaseCodeAction = 'mks_product_badge_purchase_code';

	public function __construct()
	{
		add_action('admin_menu', [$this, 'registerMenu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueueScriptsToDashboard']);

	}

	public function enqueueScriptsToDashboard($hook): bool
	{
		$currencyFormat = (in_array('woocommerce/woocommerce.php',
			apply_filters('active_plugins', get_option('active_plugins')))) ? get_woocommerce_currency_symbol() : "$";
		wp_localize_script('jquery', self::MYSMBWP_GLOBAL, [
			'url'                => admin_url('admin-ajax.php'),
			'purchaseCodeAction' => $this->purchaseCodeAction,
			'restBase'           => admin_url('admin-ajax.php'),
			'email'              => get_option('admin_email'),
			'clientSite'         => home_url('/'),
			'purchaseCode'       => $this->getToken(),
			'currencyFormat'     => $currencyFormat,
			'purchaseCodeLink'   => 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code',
			'tidio'              => 'bdzedo8yftsclnwmwmbcqcsyscbk4rtl'
		]);

		if ((strpos($hook, $this->getDashboardSlug()) !== false) || (strpos($hook, $this->getAuthSlug()) !== false)) {
			// enqueue script
			wp_enqueue_script(
				AutoPrefix::namePrefix('dashboard-script'),
				plugin_dir_url(__FILE__) . '../Assets/Js/Script.js',
				['jquery'],
				MYSHOPKIT_MB_WP_VERSION,
				true
			);

		}
		wp_enqueue_style(
			AutoPrefix::namePrefix('dashboard-style'),
			plugin_dir_url(__FILE__) . '../Assets/Css/Style.css',
			[],
			MYSHOPKIT_MB_WP_VERSION
		);
		return false;
	}


	public function registerMenu()
	{
		add_menu_page(
			esc_html__('MyShopKit Product Badges', 'myshopkit-product-badges-wp'),
			esc_html__('MyShopKit Product Badges', 'myshopkit-product-badges-wp'),
			'administrator',
			$this->getDashboardSlug(),
			[$this, 'renderSettings'],
			plugin_dir_url(__FILE__) . '../Assets/dashboard.svg'
		);
	}

	public function renderSettings()
	{
		?>
        <div id="mskmbwp-dashboard">
            <iframe id="badges-iframe" src="https://magics-badges-wp.netlify.app"></iframe>
        </div>
		<?php
	}

	private function getIframe(): string
	{
		return defined('MSKMBWP_IFRAME') ? MSKMBWP_IFRAME : 'https://magics-badges-wp.netlify.app';
	}
}

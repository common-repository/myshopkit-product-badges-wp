<?php
/**
 * Plugin Name: WooCommerce Product Badges | Myshopkit
 * Plugin URI: https://product-badges.myshopkit.app/
 * Author: wiloke
 * Author URI: https://woocommerce.myshopkit.app/
 * Version: 1.0.4
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Text-Domain: myshopkit-product-badges-wp
 * License:     GPL-2.0+
 * Copyright:   Wiloke
 * Description: Highlighting your product with 500+ pre-made badges and Boost Sales custom code.
 * Tags: Product Labels For WooCommerce, WooCommerce Labels, WooCommerce Badges, WooCommerce Sale Badges, Badges, Sale Badges
 */


add_action('admin_notices', function () {

	if (!class_exists('WooCommerce')) {
		?>
        <div id="mysmbwp-converter-warning" class="notice notice-error sf-notice-nux is-dismissible">
			<?php esc_html_e('Please install and activate WooCommerce to use Product Badges for WooCommerce plugin.',
				'myshopkit-product-badges-wp'); ?>
        </div>
		<?php
	}
});

use MyShopKitMBWP\Dashboard\Controllers\AuthController;

define('MYSHOPKIT_MB_WP_VERSION', '1.0.4');
define('MYSHOPKIT_MB_WP_HOOK_PREFIX', 'mskmbwp/');
define('MYSHOPKIT_MB_WP_PREFIX', 'mskmbwp_');
define('MYSHOPKIT_MB_WP_REST_VERSION', 'v1');
define('MYSHOPKIT_MB_WP_REST_NAMESPACE', 'myshopkit-product-badges-wp');
define('MYSHOPKIT_MB_WP_REST', MYSHOPKIT_MB_WP_REST_NAMESPACE . '/' . MYSHOPKIT_MB_WP_REST_VERSION);
define('MYSHOPKIT_MB_WP_URL', plugin_dir_url(__FILE__));
define('MYSHOPKIT_MB_WP_PATH', plugin_dir_path(__FILE__));

add_action('plugins_loaded', 'wilokeProductBadgeLoadPluginDomain');
if (!function_exists('wilokeProductBadgeLoadPluginDomain')) {
	function wilokeProductBadgeLoadPluginDomain()
	{
		load_plugin_textdomain('myshopkit-product-badges-wp', false, plugin_dir_path(__FILE__) . 'languages');
	}
}
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . 'src/Dashboard/Dashboard.php';
require_once plugin_dir_path(__FILE__) . 'src/DefaultBadge/DefaultBadge.php';
require_once plugin_dir_path(__FILE__) . 'src/Product/Product.php';
require_once plugin_dir_path(__FILE__) . 'src/Automatic/Automatic.php';
require_once plugin_dir_path(__FILE__) . 'src/General/General.php';


register_activation_hook(__FILE__, function () {
	AuthController::generateAuth();
});

register_deactivation_hook(__FILE__, function () {
	AuthController::autoDeleteAuth();
});

add_filter('wp_is_application_passwords_available', '__return_true', 9999);

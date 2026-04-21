<?php
/**
 * Plugin Name:       WC Product Loop Slider
 * Plugin URI:        https://github.com/webwecreate/wc-product-loop-slider
 * Description:       Per-product image gallery slider for WooCommerce shop and archive pages
 * Version:           0.3.6
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            WebWeCreate
 * Author URI:        https://webwecreate.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wc-product-loop-slider
 * Domain Path:       /languages
 *
 * WC requires at least: 7.0
 * WC tested up to:      9.x
 *
 * @package WC_Product_Loop_Slider
 * @version 0.3.6
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define( 'WCPLS_VERSION', '0.3.6' );
define( 'WCPLS_FILE',    __FILE__ );
define( 'WCPLS_PATH',    plugin_dir_path( __FILE__ ) );
define( 'WCPLS_URL',     plugin_dir_url( __FILE__ ) );

// ---------------------------------------------------------------------------
// WooCommerce HPOS Compatibility Declaration
// ---------------------------------------------------------------------------

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS).
 *
 * This plugin does not interact with orders in any way — it only renders
 * product image sliders on shop/archive pages. Declaring compatibility
 * removes the WooCommerce admin warning about incompatible plugins.
 *
 * @since 0.3.3
 */
add_action( 'before_woocommerce_init', function(): void {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
} );

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------

/**
 * Instantiate the Core class after all plugins are loaded.
 *
 * Using `plugins_loaded` ensures WooCommerce is available before we run
 * any WC-specific code.
 *
 * @since 0.1.0
 * @return void
 */
function wcpls_init(): void {
	require_once WCPLS_PATH . 'includes/class-wcpls-core.php';
	WCPLS_Core::instance();
}
add_action( 'plugins_loaded', 'wcpls_init' );

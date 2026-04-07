<?php
/**
 * Plugin Name:       WC Product Loop Slider
 * Plugin URI:        https://github.com/webwecreate/wc-product-loop-slider
 * Description:       Per-product image gallery slider for WooCommerce shop and archive pages
 * Version:           0.1.1
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
 * @version 0.1.0
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define( 'WCPLS_VERSION', '0.1.1' );
define( 'WCPLS_FILE',    __FILE__ );
define( 'WCPLS_PATH',    plugin_dir_path( __FILE__ ) );
define( 'WCPLS_URL',     plugin_dir_url( __FILE__ ) );

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
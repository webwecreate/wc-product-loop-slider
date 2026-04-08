<?php
/**
 * Class WCPLS_Assets
 *
 * @package     WC_Product_Loop_Slider
 * @version     0.3.0
 * @since       0.1.2
 * @author      webwecreate.com
 * @license     GPL-2.0-or-later
 *
 * Handles enqueueing of all frontend scripts and styles.
 * Swiper.js is loaded from bundled vendor assets (no CDN dependency).
 *
 * Load condition: is_shop() | is_product_category() | is_product_taxonomy()
 *                 | Elementor-built pages (0.3.0+)
 */

defined( 'ABSPATH' ) || exit;

class WCPLS_Assets {

	/**
	 * Constructor — register enqueue hook.
	 *
	 * @since 0.1.2
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueue frontend assets on WooCommerce archive pages only.
	 *
	 * @since  0.1.2
	 * @return void
	 */
	public function enqueue(): void {

		if ( ! $this->is_product_archive() ) {
			return;
		}

		// 1. Swiper CSS — vendor bundle.
		wp_enqueue_style(
			'wcpls-swiper',
			WCPLS_URL . 'assets/vendor/swiper/swiper-bundle.min.css',
			[],
			'11.0.0'
		);

		// 2. Plugin frontend CSS.
		wp_enqueue_style(
			'wcpls-front',
			WCPLS_URL . 'assets/css/wcpls-front.css',
			[ 'wcpls-swiper' ],
			WCPLS_VERSION
		);

		// 3. Swiper JS — vendor bundle.
		wp_enqueue_script(
			'wcpls-swiper',
			WCPLS_URL . 'assets/vendor/swiper/swiper-bundle.min.js',
			[],
			'11.0.0',
			true
		);

		// 4. Plugin frontend JS.
		wp_enqueue_script(
			'wcpls-front',
			WCPLS_URL . 'assets/js/wcpls-front.js',
			[ 'wcpls-swiper' ],
			WCPLS_VERSION,
			true
		);

		wp_localize_script(
			'wcpls-front',
			'wcplsConfig',
			$this->get_js_config()
		);
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Check whether the current page should load WCPLS assets.
	 *
	 * Covers:
	 *   - Standard WooCommerce shop / category / tag / taxonomy archive pages.
	 *   - [0.3.0] Singular pages built with Elementor (Loop Builder support).
	 *
	 * @since  0.1.2
	 * @return bool
	 */
	private function is_product_archive(): bool {

		// Standard WooCommerce archive pages.
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			return true;
		}

		// [0.3.0] Elementor-built singular pages (e.g. custom Loop Builder pages).
		if ( class_exists( 'WCPLS_Elementor' )
			&& WCPLS_Elementor::is_elementor_active()
			&& WCPLS_Elementor::is_elementor_built_page()
		) {
			return true;
		}

		return false;
	}

	/**
	 * Build the JS config object passed via wp_localize_script.
	 *
	 * @since  0.1.2
	 * @return array<string, mixed>
	 */
	private function get_js_config(): array {
		return [
			'version'       => WCPLS_VERSION,
			'pagination'    => true,
			'navigation'    => false,
			'autoplay'      => false,
			'autoplayDelay' => 3000,
		];
	}
}
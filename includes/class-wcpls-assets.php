<?php
/**
 * Class WCPLS_Assets
 *
 * @package     WC_Product_Loop_Slider
 * @version     0.1.2
 * @since       0.1.2
 * @author      [Your Name]
 * @license     GPL-2.0-or-later
 *
 * Handles enqueueing of all frontend scripts and styles.
 * Swiper.js is loaded from bundled vendor assets (no CDN dependency).
 *
 * Load condition: is_shop() | is_product_category() | is_product_taxonomy()
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
	 * Loads:
	 *   1. Swiper CSS  (vendor bundle)
	 *   2. Swiper JS   (vendor bundle)
	 *   3. wcpls-front.css (plugin styles)
	 *   4. wcpls-front.js  (plugin Swiper init)
	 *
	 * @since 0.1.2
	 * @return void
	 */
	public function enqueue(): void {

		// Only load on WooCommerce shop / category / taxonomy pages.
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

		// 3. Swiper JS — vendor bundle (deferred, no jQuery dependency).
		wp_enqueue_script(
			'wcpls-swiper',
			WCPLS_URL . 'assets/vendor/swiper/swiper-bundle.min.js',
			[],
			'11.0.0',
			true  // load in footer
		);

		// 4. Plugin frontend JS — depends on Swiper.
		wp_enqueue_script(
			'wcpls-front',
			WCPLS_URL . 'assets/js/wcpls-front.js',
			[ 'wcpls-swiper' ],
			WCPLS_VERSION,
			true  // load in footer
		);

		// Pass PHP config to JS via wp_localize_script (extendable in v0.3.0).
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
	 * Check whether the current page is a WooCommerce product archive.
	 *
	 * @since  0.1.2
	 * @return bool
	 */
	private function is_product_archive(): bool {
		return is_shop()
			|| is_product_category()
			|| is_product_tag()
			|| is_product_taxonomy();
	}

	/**
	 * Build the JS config object passed via wp_localize_script.
	 * Values here mirror the planned Settings options (Section 6 of Master).
	 *
	 * @since  0.1.2
	 * @return array<string, mixed>
	 */
	private function get_js_config(): array {
		return [
			'version'       => WCPLS_VERSION,
			'pagination'    => true,   // wcpls_show_pagination  (future option)
			'navigation'    => false,  // wcpls_show_navigation  (future option)
			'autoplay'      => false,  // wcpls_autoplay         (future option)
			'autoplayDelay' => 3000,   // wcpls_autoplay_delay   (future option)
		];
	}
}

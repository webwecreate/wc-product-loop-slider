<?php
/**
 * Elementor compatibility layer for WC Product Loop Slider.
 *
 * Handles Swiper asset loading inside the Elementor Editor
 * and on frontend pages built with Elementor Loop Builder.
 *
 * @package WC_Product_Loop_Slider
 * @version 0.3.7
 * @since   0.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCPLS_Elementor
 *
 * @since 0.3.0
 */
class WCPLS_Elementor {

	// -------------------------------------------------------------------------
	// Boot
	// -------------------------------------------------------------------------

	/**
	 * Constructor — register Elementor-specific hooks.
	 *
	 * Bails silently when Elementor is not installed/active,
	 * so the main plugin continues to work without Elementor.
	 *
	 * @since 0.3.0
	 */
	public function __construct() {
		if ( ! self::is_elementor_active() ) {
			return;
		}

		add_action(
			'elementor/widgets/register',
			[ $this, 'register_widgets' ]
		);

		// Load assets inside the Elementor editor canvas.
		add_action(
			'elementor/editor/after_enqueue_scripts',
			[ $this, 'enqueue_editor_assets' ]
		);

		// Load full asset stack on Elementor-built frontend pages.
		add_action(
			'elementor/frontend/after_enqueue_scripts',
			[ $this, 'force_enqueue_assets' ]
		);
	}

	// -------------------------------------------------------------------------
	// Detection
	// -------------------------------------------------------------------------

	/**
	 * Check whether Elementor plugin is installed and active.
	 *
	 * @since  0.3.0
	 * @return bool
	 */
	public static function is_elementor_active(): bool {
		return defined( 'ELEMENTOR_VERSION' )
			&& class_exists( '\Elementor\Plugin' );
	}

	/**
	 * Check whether the current page was built with Elementor.
	 *
	 * Uses post meta `_elementor_edit_mode` (set to 'builder' by Elementor)
	 * as a lightweight check — avoids loading the full Documents API.
	 *
	 * @since  0.3.0
	 * @return bool
	 */
	public static function is_elementor_built_page(): bool {
		$post_id = (int) get_the_ID();
		if ( $post_id < 1 ) {
			return false;
		}

		return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
	}

	/**
	 * Check whether the current page is a WooCommerce product archive
	 * OR an Elementor-built page that may contain the product slider widget.
	 *
	 * Mirrors WCPLS_Assets::is_product_archive() but exposed as a public
	 * static so it can be shared between WCPLS_Assets and WCPLS_Elementor.
	 *
	 * @since  0.3.7
	 * @return bool
	 */
	public static function is_wcpls_page(): bool {

		// Standard WooCommerce archive pages.
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			return true;
		}

		// Elementor-built singular pages (e.g. custom Loop Builder pages).
		if ( self::is_elementor_built_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Register WCPLS_Widget with Elementor's widget manager.
	 *
	 * @since  0.3.0
	 * @param  \Elementor\Widgets_Manager $widgets_manager
	 * @return void
	 */
	public function register_widgets( \Elementor\Widgets_Manager $widgets_manager ): void {
		require_once WCPLS_PATH . 'includes/widgets/class-wcpls-widget.php';
		$widgets_manager->register( new WCPLS_Widget() );
	}

	// -------------------------------------------------------------------------
	// Editor assets
	// -------------------------------------------------------------------------

	/**
	 * Enqueue assets inside the Elementor editor canvas.
	 *
	 * Fires on `elementor/editor/after_enqueue_scripts`.
	 * Always enqueues in the editor (wp-admin) so the slider previews
	 * correctly while designing — no page-type guard needed here.
	 *
	 * @since  0.3.0
	 * @return void
	 */
	public function enqueue_editor_assets(): void {

		// Bail if webwecreate-swiper is not active.
		if ( ! defined( 'WWCS_HANDLE_CSS' ) || ! defined( 'WWCS_HANDLE_JS' ) ) {
			return;
		}

		wp_enqueue_style( WWCS_HANDLE_CSS );
		wp_enqueue_script( WWCS_HANDLE_JS );

		wp_enqueue_style(
			'wcpls-front',
			WCPLS_URL . 'assets/css/wcpls-front.css',
			[ WWCS_HANDLE_CSS ],
			WCPLS_VERSION
		);

		wp_enqueue_script(
			'wcpls-front',
			WCPLS_URL . 'assets/js/wcpls-front.js',
			[ WWCS_HANDLE_JS ],
			WCPLS_VERSION,
			true
		);
	}

	// -------------------------------------------------------------------------
	// Frontend assets
	// -------------------------------------------------------------------------

	/**
	 * Force-enqueue full asset stack on relevant Elementor-built frontend pages.
	 *
	 * Fires on `elementor/frontend/after_enqueue_scripts`.
	 * Only loads on WooCommerce archive pages or Elementor-built pages that
	 * may contain the product slider widget — not on every page of the site.
	 *
	 * wp_enqueue_* is idempotent — safe to call even if WCPLS_Assets
	 * already enqueued the handles on a standard archive page.
	 *
	 * @since  0.3.0
	 * @return void
	 */
	public function force_enqueue_assets(): void {

		// Only load on shop/archive pages or Elementor Loop Builder pages.
		if ( ! self::is_wcpls_page() ) {
			return;
		}

		// Bail if webwecreate-swiper is not active.
		if ( ! defined( 'WWCS_HANDLE_CSS' ) || ! defined( 'WWCS_HANDLE_JS' ) ) {
			return;
		}

		wp_enqueue_style( WWCS_HANDLE_CSS );
		wp_enqueue_script( WWCS_HANDLE_JS );

		wp_enqueue_style(
			'wcpls-front',
			WCPLS_URL . 'assets/css/wcpls-front.css',
			[ WWCS_HANDLE_CSS ],
			WCPLS_VERSION
		);

		wp_enqueue_script(
			'wcpls-front',
			WCPLS_URL . 'assets/js/wcpls-front.js',
			[ WWCS_HANDLE_JS ],
			WCPLS_VERSION,
			true
		);

		// Guard: skip localize if already done by WCPLS_Assets on archive pages.
		if ( ! wp_script_is( 'wcpls-front', 'done' ) ) {
			wp_localize_script(
				'wcpls-front',
				'wcplsConfig',
				[
					'version'       => WCPLS_VERSION,
					'pagination'    => true,
					'navigation'    => false,
					'autoplay'      => false,
					'autoplayDelay' => 3000,
				]
			);
		}
	}
}
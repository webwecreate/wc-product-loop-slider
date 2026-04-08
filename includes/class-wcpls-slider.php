<?php
/**
 * Class WCPLS_Slider
 *
 * @package    WC Product Loop Slider
 * @file       includes/class-wcpls-slider.php
 * @version    0.1.3
 * @since      0.1.2
 * @last-updated 2026-04-07
 *
 * Responsibilities:
 *  - Remove default WooCommerce product thumbnail from loop
 *  - Gather Featured Image + Gallery Image IDs for a product
 *  - Load the loop-slider.php template to render Swiper HTML
 */

defined( 'ABSPATH' ) || exit;

class WCPLS_Slider {

	/**
	 * Constructor — registers all hooks.
	 */
	public function __construct() {
		$this->hook_into_loop();
	}

	// -------------------------------------------------------------------------
	// Hooks
	// -------------------------------------------------------------------------

	/**
	 * Replace WooCommerce's default thumbnail with our slider.
	 *
	 * Hook: woocommerce_before_shop_loop_item_title (priority 10)
	 */
	public function hook_into_loop(): void {
		
		// [v0.3.2 short-term fix]
		// เมื่อ Elementor active → ให้ WCPLS_Widget จัดการแทน
		// ใน v0.4.0 (Part 8) จะเปลี่ยนเป็น settings option
		if ( class_exists( 'WCPLS_Elementor' ) && WCPLS_Elementor::is_elementor_active() ) {
			return;
		}

		// Remove the default thumbnail output.
		remove_action(
			'woocommerce_before_shop_loop_item_title',
			'woocommerce_template_loop_product_thumbnail',
			10
		);

		// Add our slider in its place.
		add_action(
			'woocommerce_before_shop_loop_item_title',
			[ $this, 'render_slider' ],
			10
		);
	}

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Collect all image IDs for a given product.
	 *
	 * Order: Featured Image first, then Gallery Images.
	 * Returns an empty array when no images are found.
	 *
	 * @param  int $product_id  WooCommerce product ID.
	 * @return int[]            Ordered array of attachment IDs.
	 */
	public function get_image_ids( int $product_id ): array {
		$ids = [];

		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return $ids;
		}

		// 1. Featured image.
		$featured_id = (int) $product->get_image_id();
		if ( $featured_id > 0 ) {
			$ids[] = $featured_id;
		}

		// 2. Gallery images (exclude featured to avoid duplicates).
		$gallery_ids = $product->get_gallery_image_ids();
		foreach ( $gallery_ids as $gallery_id ) {
			$gallery_id = (int) $gallery_id;
			if ( $gallery_id > 0 && ! in_array( $gallery_id, $ids, true ) ) {
				$ids[] = $gallery_id;
			}
		}

		return $ids;
	}

	/**
	 * Render the slider for the current product in the loop.
	 *
	 * Called via the `woocommerce_before_shop_loop_item_title` action.
	 * Falls back to the default WooCommerce thumbnail when no images exist.
	 */
	public function render_slider(): void {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$product_id = (int) $product->get_id();
		$image_ids  = $this->get_image_ids( $product_id );

		// Fallback: no images — render the default WooCommerce thumbnail.
		if ( empty( $image_ids ) ) {
			woocommerce_template_loop_product_thumbnail();
			return;
		}

		// Build the path to our template.
		$template = $this->locate_template( 'loop-slider.php' );

		if ( ! $template || ! file_exists( $template ) ) {
			// Template missing — fallback gracefully.
			woocommerce_template_loop_product_thumbnail();
			return;
		}

		// Pass data to the template via compact variables.
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		include $template; // $image_ids and $product_id are in scope.
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Locate a template file.
	 *
	 * Checks (in order):
	 *  1. Theme override: {theme}/wc-product-loop-slider/{file}
	 *  2. Plugin bundled template: {plugin}/templates/{file}
	 *
	 * @param  string $file  Template filename.
	 * @return string        Absolute path to the resolved template.
	 */
	private function locate_template( string $file ): string {
		// Allow themes to override.
		$theme_template = locate_template(
			[ trailingslashit( 'wc-product-loop-slider' ) . $file ]
		);

		if ( $theme_template ) {
			return $theme_template;
		}

		// Default bundled template.
		return WCPLS_PATH . 'templates/' . $file;
	}
}

<?php
/**
 * Slider HTML template for Elementor Widget (WCPLS_Widget::render)
 *
 * Variables available (passed via extract in WCPLS_Widget::render):
 *   @var int[]  $image_ids   Array of WP attachment IDs (featured + gallery).
 *   @var int    $product_id  Current product post ID.
 *   @var array  $settings    Elementor widget settings array.
 *
 * Theme override:
 *   Copy this file to: {theme}/wc-product-loop-slider/elementor-slider.php
 *
 * @package WC_Product_Loop_Slider
 * @version 0.3.3
 * @since   0.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -------------------------------------------------------------------------
// Resolve settings with safe defaults
// -------------------------------------------------------------------------

$image_size      = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'woocommerce_thumbnail';
$show_pagination = isset( $settings['show_pagination'] ) && 'yes' === $settings['show_pagination'];
$show_navigation = isset( $settings['show_navigation'] ) && 'yes' === $settings['show_navigation'];
$slide_count     = count( $image_ids );

// Unique ID per widget instance (needed when multiple sliders exist on page)
$slider_id = 'wcpls-slider-' . esc_attr( $product_id );

// -------------------------------------------------------------------------
// Resolve link
// -------------------------------------------------------------------------

$link_url         = '';
$link_is_external = false;
$link_nofollow    = false;

if ( ! empty( $settings['link']['url'] ) ) {
	$link_url         = $settings['link']['url'];
	$link_is_external = ! empty( $settings['link']['is_external'] );
	$link_nofollow    = ! empty( $settings['link']['nofollow'] );
}

$has_link = ! empty( $link_url );

/**
 * Action: before slider wrapper.
 *
 * @param int   $product_id
 * @param array $settings
 */
do_action( 'wcpls_before_slider', $product_id, $settings );

// Build link attributes string
if ( $has_link ) {
	$link_attrs  = 'href="' . esc_url( $link_url ) . '"';
	$link_attrs .= ' class="wcpls-slider-link"';
	$link_attrs .= ' aria-label="' . esc_attr( get_the_title( $product_id ) ) . '"';
	if ( $link_is_external ) {
		$link_attrs .= ' target="_blank" rel="noopener';
		$link_attrs .= $link_nofollow ? ' nofollow"' : '"';
	} elseif ( $link_nofollow ) {
		$link_attrs .= ' rel="nofollow"';
	}
}
?>

<?php if ( $has_link ) : ?>
<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php endif; ?>

<div class="wcpls-slider-wrapper" id="<?php echo esc_attr( $slider_id ); ?>">

	<?php
	/**
	 * Action: before swiper container.
	 *
	 * @param int   $product_id
	 * @param array $settings
	 */
	do_action( 'wcpls_before_slider_inner', $product_id, $settings );
	?>

	<div class="wcpls-slider swiper">
		<div class="swiper-wrapper">

			<?php foreach ( $image_ids as $image_id ) : ?>

				<?php
				/**
				 * Action: before individual slide.
				 *
				 * @param int $image_id
				 * @param int $product_id
				 */
				do_action( 'wcpls_before_slide', $image_id, $product_id );

				/**
				 * Filter: image size per slide.
				 *
				 * @param string $image_size
				 * @param int    $image_id
				 * @param int    $product_id
				 */
				$resolved_size = apply_filters( 'wcpls_elementor_image_size', $image_size, $image_id, $product_id );
				?>

				<div class="swiper-slide">
					<?php
					echo wp_get_attachment_image(
						$image_id,
						$resolved_size,
						false,
						[
							'loading'   => 'lazy',
							'decoding'  => 'async',
							'draggable' => 'false',
							'class'     => 'wcpls-slide-img',
							'alt'       => get_the_title( $product_id ),
						]
					);
					?>
				</div>

				<?php
				/**
				 * Action: after individual slide.
				 *
				 * @param int $image_id
				 * @param int $product_id
				 */
				do_action( 'wcpls_after_slide', $image_id, $product_id );
				?>

			<?php endforeach; ?>

		</div><!-- /.swiper-wrapper -->

		<?php if ( $show_pagination && $slide_count > 1 ) : ?>
			<div class="swiper-pagination"></div>
		<?php endif; ?>

		<?php if ( $show_navigation && $slide_count > 1 ) : ?>
			<button
				type="button"
				class="swiper-button-prev"
				aria-label="<?php esc_attr_e( 'Previous image', 'wc-product-loop-slider' ); ?>"
			></button>
			<button
				type="button"
				class="swiper-button-next"
				aria-label="<?php esc_attr_e( 'Next image', 'wc-product-loop-slider' ); ?>"
			></button>
		<?php endif; ?>

	</div><!-- /.wcpls-slider.swiper -->

	<?php
	/**
	 * Action: after swiper container.
	 *
	 * @param int   $product_id
	 * @param array $settings
	 */
	do_action( 'wcpls_after_slider_inner', $product_id, $settings );
	?>

</div><!-- /.wcpls-slider-wrapper -->

<?php if ( $has_link ) : ?>
</a><!-- /.wcpls-slider-link -->
<?php endif; ?>

<?php
/**
 * Action: after slider wrapper.
 *
 * @param int   $product_id
 * @param array $settings
 */
do_action( 'wcpls_after_slider', $product_id, $settings );

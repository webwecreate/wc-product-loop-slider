<?php
/**
 * Template: Loop Slider
 *
 * @package    WC Product Loop Slider
 * @file       templates/loop-slider.php
 * @version    0.2.2
 * @since      0.1.2
 * @last-updated 2026-04-08
 *
 * Variables available (set by WCPLS_Slider::render_slider()):
 *  @var int[]  $image_ids   Ordered array of WP attachment IDs.
 *  @var int    $product_id  Current WooCommerce product ID.
 *
 * Override this template by copying it to:
 *  {theme}/wc-product-loop-slider/loop-slider.php
 */

defined( 'ABSPATH' ) || exit;

// Safety guard — both variables must exist and be valid.
if ( empty( $image_ids ) || empty( $product_id ) ) {
	return;
}

$image_size = apply_filters( 'wcpls_image_size', 'woocommerce_thumbnail' );

/**
 * Fires before the slider wrapper opens.
 *
 * @param int   $product_id  Current product ID.
 * @param int[] $image_ids   Array of image attachment IDs.
 */
do_action( 'wcpls_before_slider', $product_id, $image_ids );
?>

<div class="wcpls-slider-wrapper">
	<div class="wcpls-slider swiper"
		data-product-id="<?php echo esc_attr( $product_id ); ?>"
		aria-label="<?php esc_attr_e( 'Product image gallery', 'wc-product-loop-slider' ); ?>"
	>

		<div class="swiper-wrapper">

			<?php
			$slide_index = 0;

			foreach ( $image_ids as $image_id ) :
				$image_id = (int) $image_id;

				// Retrieve image data.
				$image_src  = wp_get_attachment_image_url( $image_id, $image_size );
				$image_meta = wp_get_attachment_metadata( $image_id );
				$image_alt  = trim( wp_strip_all_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );

				// Fallback alt text.
				if ( empty( $image_alt ) ) {
					$product_obj = wc_get_product( $product_id );
					$image_alt   = $product_obj ? $product_obj->get_name() : '';
				}

				if ( ! $image_src ) {
					continue;
				}

				/**
				 * Fires before each individual slide.
				 *
				 * @param int $image_id    Attachment ID.
				 * @param int $slide_index Zero-based slide index.
				 */
				do_action( 'wcpls_before_slide', $image_id, $slide_index );
				?>

				<div class="swiper-slide wcpls-slide"
					data-slide-index="<?php echo esc_attr( $slide_index ); ?>"
					role="group"
					aria-label="<?php
						/* translators: 1: current slide number, 2: total slides */
						printf(
							esc_attr__( 'Slide %1$d of %2$d', 'wc-product-loop-slider' ),
							$slide_index + 1,
							count( $image_ids )
						);
					?>"
				>

					<?php
					/**
					 * Use wp_get_attachment_image() for proper srcset / sizes support
					 * while adding loading="lazy" for performance.
					 */
					echo wp_get_attachment_image(
						$image_id,
						$image_size,
						false,
						[
							'class'   => 'wcpls-slide__img',
							'alt'     => esc_attr( $image_alt ),
							'loading' => 'lazy',
							'decoding' => 'async',
						]
					);
					?>

				</div><!-- .swiper-slide -->

				<?php
				do_action( 'wcpls_after_slide', $image_id, $slide_index );
				$slide_index++;
			endforeach;
			?>

		</div><!-- .swiper-wrapper -->

		<?php if ( count( $image_ids ) > 1 ) : ?>
			<div class="swiper-pagination wcpls-pagination" aria-hidden="true"></div>
			<button class="swiper-button-prev wcpls-nav-prev" aria-label="<?php esc_attr_e( 'Previous image', 'wc-product-loop-slider' ); ?>"></button>
			<button class="swiper-button-next wcpls-nav-next" aria-label="<?php esc_attr_e( 'Next image', 'wc-product-loop-slider' ); ?>"></button>
		<?php endif; ?>

	</div><!-- .wcpls-slider -->
</div><!-- .wcpls-slider-wrapper -->

<?php
/**
 * Fires after the slider wrapper closes.
 *
 * @param int   $product_id  Current product ID.
 * @param int[] $image_ids   Array of image attachment IDs.
 */
do_action( 'wcpls_after_slider', $product_id, $image_ids );
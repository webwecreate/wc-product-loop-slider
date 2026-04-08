<?php
/**
 * Plugin Name: WC Product Loop Slider
 * File:        includes/widgets/class-wcpls-widget.php
 * Version:     0.3.1
 * Description: Elementor Widget — WCPLS_Widget extends \Elementor\Widget_Base
 * Author:      webwecreate.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WCPLS_Widget
 *
 * Registers the "Product Slider" widget inside Elementor.
 * Designed for use inside Elementor Loop Grid + Custom Loop Item.
 *
 * @since 0.3.1
 */
class WCPLS_Widget extends \Elementor\Widget_Base {

    // -------------------------------------------------------------------------
    // Section 1 — Widget Identity
    // -------------------------------------------------------------------------

    /**
     * Unique slug used by Elementor internally.
     *
     * @return string
     */
    public function get_name(): string {
        return 'wcpls-product-slider';
    }

    /**
     * Human-readable title shown in the panel.
     *
     * @return string
     */
    public function get_title(): string {
        return esc_html__( 'Product Slider', 'wc-product-loop-slider' );
    }

    /**
     * Elementor icon class.
     *
     * @return string
     */
    public function get_icon(): string {
        return 'eicon-media-carousel';
    }

    /**
     * Panel category — shows under WooCommerce section.
     *
     * @return string[]
     */
    public function get_categories(): array {
        return [ 'woocommerce-elements' ];
    }

    // -------------------------------------------------------------------------
    // Section 2 — Controls
    // -------------------------------------------------------------------------

    /**
     * Register widget controls (settings panel).
     *
     * Controls:
     *  - image_size      : select  — thumbnail / medium / large / full
     *  - show_pagination : switcher — default ON
     *  - show_navigation : switcher — default OFF
     *
     * @return void
     */
    protected function register_controls(): void {

        $this->start_controls_section(
            'section_slider_settings',
            [
                'label' => esc_html__( 'Slider Settings', 'wc-product-loop-slider' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Image Size -------------------------------------------------------
        $this->add_control(
            'image_size',
            [
                'label'   => esc_html__( 'Image Size', 'wc-product-loop-slider' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'thumbnail',
                'options' => [
                    'thumbnail' => esc_html__( 'Thumbnail', 'wc-product-loop-slider' ),
                    'medium'    => esc_html__( 'Medium', 'wc-product-loop-slider' ),
                    'large'     => esc_html__( 'Large', 'wc-product-loop-slider' ),
                    'full'      => esc_html__( 'Full', 'wc-product-loop-slider' ),
                ],
            ]
        );

        // Show Pagination --------------------------------------------------
        $this->add_control(
            'show_pagination',
            [
                'label'        => esc_html__( 'Show Pagination Dots', 'wc-product-loop-slider' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'wc-product-loop-slider' ),
                'label_off'    => esc_html__( 'Hide', 'wc-product-loop-slider' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        // Show Navigation --------------------------------------------------
        $this->add_control(
            'show_navigation',
            [
                'label'        => esc_html__( 'Show Navigation Arrows', 'wc-product-loop-slider' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'wc-product-loop-slider' ),
                'label_off'    => esc_html__( 'Hide', 'wc-product-loop-slider' ),
                'return_value' => 'yes',
                'default'      => '',   // OFF by default
            ]
        );

        $this->end_controls_section();
    }

    // -------------------------------------------------------------------------
    // Section 3 — Render
    // -------------------------------------------------------------------------

    /**
     * Front-end render callback.
     *
     * Flow:
     *  1. Resolve current product from the loop's post object.
     *  2. Bail gracefully when not inside a product context.
     *  3. Fetch image IDs via WCPLS_Slider::get_image_ids().
     *  4. Bail gracefully when no images found.
     *  5. Load templates/elementor-slider.php, passing variables via extract().
     *
     * @return void
     */
    protected function render(): void {

        // 1. Resolve product -----------------------------------------------
        $post_id = get_the_ID();

        if ( ! $post_id || get_post_type( $post_id ) !== 'product' ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="padding:1em;color:#999;font-size:12px;">'
                     . esc_html__( '[WCPLS] Place this widget inside a WooCommerce Loop Item.', 'wc-product-loop-slider' )
                     . '</p>';
            }
            return;
        }

        $product_id = absint( $post_id );

        // 2. Guard: WCPLS_Slider must be available --------------------------
        if ( ! class_exists( 'WCPLS_Slider' ) ) {
            return;
        }

        // 3. Fetch image IDs -----------------------------------------------
        $slider    = new WCPLS_Slider();
        $image_ids = $slider->get_image_ids( $product_id );

        if ( empty( $image_ids ) ) {
            // Graceful fallback: render nothing (WC default thumbnail is
            // handled by the parent loop; widget simply outputs nothing).
            return;
        }

        // 4. Widget settings -----------------------------------------------
        $settings = $this->get_settings_for_display();

        // 5. Load template -------------------------------------------------
        $template = $this->locate_template( 'elementor-slider.php' );

        if ( ! $template ) {
            return;
        }

        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
        extract(
            [
                'image_ids'  => $image_ids,
                'product_id' => $product_id,
                'settings'   => $settings,
            ],
            EXTR_SKIP
        );

        include $template;
    }

    // -------------------------------------------------------------------------
    // Section 4 — Helpers
    // -------------------------------------------------------------------------

    /**
     * Locate template: theme override first, then plugin bundled.
     *
     * Theme override path: {theme}/wc-product-loop-slider/{file}
     *
     * @param  string $file  Template filename (e.g. 'elementor-slider.php').
     * @return string|false  Absolute path, or false when not found.
     */
    private function locate_template( string $file ): string|false {

        // Theme override
        $theme_file = get_stylesheet_directory() . '/wc-product-loop-slider/' . $file;
        if ( file_exists( $theme_file ) ) {
            return $theme_file;
        }

        // Parent theme override
        $parent_file = get_template_directory() . '/wc-product-loop-slider/' . $file;
        if ( file_exists( $parent_file ) ) {
            return $parent_file;
        }

        // Plugin bundled
        $plugin_file = WCPLS_PATH . 'templates/' . $file;
        if ( file_exists( $plugin_file ) ) {
            return $plugin_file;
        }

        return false;
    }
}
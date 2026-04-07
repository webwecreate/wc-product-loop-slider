<?php
/**
 * Core controller for WC Product Loop Slider.
 *
 * Bootstraps all plugin components, verifies WooCommerce is active,
 * and registers top-level hooks.
 *
 * @package WC_Product_Loop_Slider
 * @version 0.1.1
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCPLS_Core
 *
 * Singleton controller — instantiated once via wcpls_init().
 *
 * @since 0.1.0
 */
final class WCPLS_Core {

    // ---------------------------------------------------------------------------
    // Singleton
    // ---------------------------------------------------------------------------

    /** @var self|null Single instance. */
    private static ?self $instance = null;

    /**
     * Return (and create if needed) the single instance.
     *
     * @since  0.1.0
     * @return self
     */
    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ---------------------------------------------------------------------------
    // Boot
    // ---------------------------------------------------------------------------

    /**
     * Private constructor — runs only once via instance().
     *
     * @since 0.1.0
     */
    private function __construct() {
        $this->check_woocommerce();
        $this->load_dependencies();
    }

    /**
     * Prevent cloning the singleton.
     *
     * @since 0.1.0
     */
    private function __clone() {}

    // ---------------------------------------------------------------------------
    // Dependencies
    // ---------------------------------------------------------------------------

    /**
     * Require all plugin class files.
     *
     * Each class file is responsible for hooking itself into WordPress/WooCommerce
     * inside its own constructor.
     *
     * @since  0.1.0
     * @return void
     */
    private function load_dependencies(): void {
        $includes = [
            'includes/class-wcpls-assets.php',
            'includes/class-wcpls-slider.php',
        ];

        foreach ( $includes as $file ) {
            $path = WCPLS_PATH . $file;
            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }

        // Instantiate sub-components.
        // (Classes guard themselves with is_woocommerce_active checks if needed.)
        if ( class_exists( 'WCPLS_Assets' ) ) {
            new WCPLS_Assets();
        }

        if ( class_exists( 'WCPLS_Slider' ) ) {
            new WCPLS_Slider();
        }
    }

    // ---------------------------------------------------------------------------
    // WooCommerce Check
    // ---------------------------------------------------------------------------

    /**
     * Display an admin notice when WooCommerce is not active.
     *
     * Hooks early so the notice appears regardless of which admin page is loaded.
     *
     * @since  0.1.0
     * @return void
     */
    public function check_woocommerce(): void {
        if ( $this->is_woocommerce_active() ) {
            return;
        }

        add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce' ] );
    }

    /**
     * Output the "WooCommerce missing" admin notice HTML.
     *
     * @since  0.1.0
     * @return void
     */
    public function admin_notice_missing_woocommerce(): void {
        $message = sprintf(
            /* translators: 1: Plugin name, 2: WooCommerce */
            esc_html__(
                '%1$s requires %2$s to be installed and active.',
                'wc-product-loop-slider'
            ),
            '<strong>WC Product Loop Slider</strong>',
            '<strong>WooCommerce</strong>'
        );

        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            wp_kses(
                $message,
                [ 'strong' => [] ]
            )
        );
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    /**
     * Check whether WooCommerce is currently active.
     *
     * Compatible with both single-site and Multisite installations.
     *
     * @since  0.1.0
     * @return bool
     */
    private function is_woocommerce_active(): bool {
        $active_plugins = (array) get_option( 'active_plugins', [] );

        // Multisite network-activated plugins.
        if ( is_multisite() ) {
            $active_plugins = array_merge(
                $active_plugins,
                array_keys( (array) get_site_option( 'active_sitewide_plugins', [] ) )
            );
        }

        return in_array( 'woocommerce/woocommerce.php', $active_plugins, true )
            || class_exists( 'WooCommerce' );
    }
}
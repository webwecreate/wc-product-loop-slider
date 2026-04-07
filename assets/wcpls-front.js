/*!
 * wcpls-front.js
 *
 * @package     WC_Product_Loop_Slider
 * @version     0.1.2
 * @since       0.1.2
 * @license     GPL-2.0-or-later
 *
 * Frontend JavaScript — Initialises Swiper for each product card slider.
 * Depends on: swiper-bundle.min.js (loaded via WCPLS_Assets::enqueue)
 * Config passed from PHP: window.wcplsConfig (via wp_localize_script)
 *
 * Table of Contents:
 *   1. Config / constants
 *   2. Swiper factory
 *   3. Init on DOMContentLoaded
 *   4. Re-init helpers (AJAX / infinite scroll)
 */

( function () {
	'use strict';

	/* =========================================================================
	   1. Config / constants
	   wcplsConfig is injected by wp_localize_script in class-wcpls-assets.php
	========================================================================= */

	// will be wired up in v0.2.0
	// const config = window.wcplsConfig || {};


	/* =========================================================================
	   2. Swiper factory
	   Accepts a single slider DOM element and returns a Swiper instance.
	========================================================================= */

	// function createSlider( el ) { ... }   // will be implemented in v0.2.0


	/* =========================================================================
	   3. Init on DOMContentLoaded
	   Query all .wcpls-slider elements and initialise each one.
	========================================================================= */

	// document.addEventListener( 'DOMContentLoaded', function () { ... } );
	// will be implemented in v0.2.0


	/* =========================================================================
	   4. Re-init helpers
	   Hook into WooCommerce AJAX / infinite-scroll events so new cards that
	   arrive after initial page load also get sliders.
	========================================================================= */

	// jQuery( document.body ).on( 'wc_fragments_loaded wc_fragments_refreshed', reinit );
	// will be implemented in v0.2.0

} )();

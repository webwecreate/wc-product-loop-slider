/*!
 * wcpls-front.js
 *
 * @package     WC_Product_Loop_Slider
 * @version     0.3.0
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
 *   5. Elementor Loop Grid compatibility
 */
( function () {
	'use strict';

	/* =========================================================================
	   1. Config / constants
	   wcplsConfig is injected by wp_localize_script in class-wcpls-assets.php
	========================================================================= */
	const config = window.wcplsConfig || {};
	const SLIDER_SELECTOR = '.wcpls-slider';

	/* =========================================================================
	   2. Swiper factory
	   Accepts a single slider DOM element and returns a Swiper instance.
	   - loop: true  when slide count > 1
	   - loop: false when single image (prevents Swiper duplication artefacts)
	   - pagination dots rendered only when slide count > 1
	   - preventClicks blocks click-through to product page during swipe
	========================================================================= */
	function createSlider( el ) {

		// Guard: skip if already initialised on this element
		if ( el.swiper ) {
			return el.swiper;
		}

		const slideCount = el.querySelectorAll( '.swiper-slide' ).length;
		const enableLoop = slideCount > 1;

		const options = {

			// ── Core ─────────────────────────────────────────────────────────
			loop:          enableLoop,
			slidesPerView: 1,
			spaceBetween:  0,

			// ── Touch / swipe ─────────────────────────────────────────────────
			touchStartPreventDefault:  false,
			preventClicks:             true,
			preventClicksPropagation:  true,

			// ── Pagination dots ───────────────────────────────────────────────
			pagination: ( enableLoop && config.pagination !== false )
				? {
					el:        el.querySelector( '.swiper-pagination' ),
					clickable: true,
				  }
				: false,

			// ── Navigation arrows ─────────────────────────────────────────────
			navigation: enableLoop
				? {
					nextEl: el.querySelector( '.swiper-button-next' ),
					prevEl: el.querySelector( '.swiper-button-prev' ),
				  }
				: false,

			// ── Autoplay ──────────────────────────────────────────────────────
			autoplay: config.autoplay
				? {
					delay:                config.autoplayDelay || 3000,
					disableOnInteraction: true,
				  }
				: false,

			// ── Accessibility ─────────────────────────────────────────────────
			a11y: { enabled: true },
		};

		return new Swiper( el, options );
	}

	/* =========================================================================
	   3. Init on DOMContentLoaded
	   Query all .wcpls-slider elements and initialise each one independently.
	========================================================================= */
	function initAllSliders() {
		document.querySelectorAll( SLIDER_SELECTOR ).forEach( function ( el ) {
			createSlider( el );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', initAllSliders );

	/* =========================================================================
	   4. Re-init helpers
	   Handles product cards that arrive after initial page load:
	   - WooCommerce AJAX fragments (cart update, mini-cart refresh)
	   - Infinite scroll / Load More plugins
	   - Custom event 'wcpls_reinit' for theme/plugin developers
	========================================================================= */
	function reinitNewSliders() {
		document.querySelectorAll( SLIDER_SELECTOR ).forEach( function ( el ) {
			createSlider( el );
		} );
	}

	// WooCommerce AJAX events
	if ( typeof jQuery !== 'undefined' ) {
		jQuery( document.body ).on(
			'wc_fragments_loaded wc_fragments_refreshed wcpls_reinit',
			reinitNewSliders
		);
	}

	// Public API
	window.wcplsReinit = reinitNewSliders;

	/* =========================================================================
	   5. Elementor Loop Grid compatibility
	   Elementor Loop Builder re-renders DOM after initial load →
	   Swiper instances are destroyed. Re-init after each element renders.
	   Uses elementorFrontend.hooks (Elementor Pro frontend API).
	========================================================================= */
	function initElementorHooks() {
		if ( typeof window.elementorFrontend === 'undefined' ) {
			return;
		}

		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/global',
			function ( $element ) {
				if ( typeof $element === 'undefined' || ! $element[0] ) {
					return;
				}

				$element[0].querySelectorAll( SLIDER_SELECTOR ).forEach( function ( el ) {
					// Destroy stale instance before re-init
					if ( el.swiper ) {
						el.swiper.destroy( true, true );
						delete el.swiper;
					}
					createSlider( el );
				} );
			}
		);
	}

	// Elementor fires 'init' after elementorFrontend is ready
	if ( typeof window.elementorFrontend !== 'undefined' ) {
		initElementorHooks();
	} else {
		document.addEventListener( 'DOMContentLoaded', function () {
			if ( typeof window.elementorFrontend !== 'undefined' ) {
				window.elementorFrontend.on( 'init', initElementorHooks );
			}
		} );
	}

} )();
/*!
 * wcpls-front.js
 *
 * @package     WC_Product_Loop_Slider
 * @version     0.2.1
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
			// touchStartPreventDefault: false  → lets vertical page-scroll work normally
			// preventClicks: true              → cancels click if pointer moved (swipe)
			// preventClicksPropagation: true   → stops click bubbling up to <a> wrapper
			touchStartPreventDefault:  false,
			preventClicks:             true,
			preventClicksPropagation:  true,

			// ── Pagination dots ───────────────────────────────────────────────
			// Shown only when > 1 slide AND not disabled by PHP config
			pagination: ( enableLoop && config.pagination !== false )
				? {
					el:        el.querySelector( '.swiper-pagination' ),
					clickable: true,
				  }
				: false,

			// ── Navigation arrows ─────────────────────────────────────────────
		// Always enabled for multi-slide — CSS controls show/hide on hover.
		// On mobile (no hover), arrows stay hidden via CSS opacity: 0.
		navigation: enableLoop
			? {
				nextEl: el.querySelector( '.swiper-button-next' ),
				prevEl: el.querySelector( '.swiper-button-prev' ),
			  }
			: false,

			// ── Autoplay ──────────────────────────────────────────────────────
			// Off by default — wired to wcplsConfig.autoplay (v0.3.0 settings)
			autoplay: config.autoplay
				? {
					delay:              config.autoplayDelay || 3000,
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
	   Each instance is isolated → no conflict with other Swiper instances
	   elsewhere on the same page.
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
	   createSlider() guards against double-init via el.swiper check,
	   so calling reinitNewSliders() repeatedly is safe.
	========================================================================= */
	function reinitNewSliders() {
		document.querySelectorAll( SLIDER_SELECTOR ).forEach( function ( el ) {
			createSlider( el );
		} );
	}

	// WooCommerce AJAX events (requires jQuery — already bundled with WC)
	if ( typeof jQuery !== 'undefined' ) {
		jQuery( document.body ).on(
			'wc_fragments_loaded wc_fragments_refreshed wcpls_reinit',
			reinitNewSliders
		);
	}

	// Public API — allows themes/plugins to trigger re-init manually:
	// window.wcplsReinit()
	window.wcplsReinit = reinitNewSliders;

} )();
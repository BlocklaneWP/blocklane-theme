/**
 * Blocklane — mobile overlay alignment.
 *
 * Companion to core-navigation.css ("Mobile overlay: the header keeps its own
 * logo"). When a Navigation block opens its custom overlay, the header's own
 * logo and hamburger are lifted above it and the overlay part's band is
 * expected to sit exactly where the header is. That holds by itself for a
 * header pinned at the viewport top with the page at rest. Two things move
 * the header away from the band: a static (or absolute) header the visitor
 * has scrolled partly off screen, which leaves the hamburger tappable but the
 * logo clipped; and the admin bar or a top bar pushing the header down while
 * the band starts at the viewport top.
 *
 * On every open of a HEADER nav whose overlay part carries the theme's
 * Header band, this measures the hamburger against the close block and
 * closes the gap. If the hamburger sits above the band, the page is scrolled
 * up so the header comes fully into view (core locks scrolling while the
 * overlay is open, and the overlay is a fixed layer, so only the lifted logo
 * and hamburger are seen to settle); a header that cannot be scrolled back
 * (page already at the top) gets a 0 shift, never a negative one. Whatever
 * vertical difference remains is handed to CSS as --blocklane-overlay-shift
 * on the responsive container (the dialog's top margin), and the residual
 * x/y between the two controls as --blocklane-overlay-close-dx/dy (a
 * translate on the close block), so the real control sits under the drawn X
 * whatever the header's padding, justification or direction. A nav outside
 * the site header part, or a part without the band (one customized before
 * 0.10.0), is left alone. On close the properties are cleared so the next
 * open measures fresh; a resize while open re-measures.
 *
 * Loaded by inc/assets.php only on pages that render a Navigation block with
 * a custom overlay. Plain script, no build step.
 *
 * @package blocklane
 * @since   0.10.0
 */
( function () {
	'use strict';

	var CONTAINER = '.wp-block-navigation__responsive-container.disable-default-overlay';
	var OPEN_BUTTON = '.wp-block-navigation__responsive-container-open';
	// The close block inside the theme's Header band — the marker that the
	// overlay part is the one that mirrors the header (core-navigation.css
	// "Gate"). A part customized before 0.10.0 has no band: left alone.
	var CLOSE_BLOCK = '.blocklane-overlay-band .wp-block-navigation-overlay-close';
	var SHIFT = '--blocklane-overlay-shift';
	var DX = '--blocklane-overlay-close-dx';
	var DY = '--blocklane-overlay-close-dy';
	var OPEN_CLASS = 'is-menu-open';

	/**
	 * Line the overlay's band up with the header for one open overlay.
	 *
	 * @param {Element} container The nav's responsive container, currently open.
	 */
	function align( container ) {
		var nav = container.closest( '.wp-block-navigation' );
		var openButton = nav ? nav.querySelector( OPEN_BUTTON ) : null;
		var closeBlock = container.querySelector( CLOSE_BLOCK );
		// Only the site header part's nav has a band to line up with: a
		// footer, in-page or section-header nav given the overlay would
		// otherwise get the full viewport distance as a margin. Same gate as
		// the stylesheet's lift and morph (the template part rendered with the
		// header element, not any Group set to <header>).
		if ( ! openButton || ! closeBlock || ! openButton.closest( 'header.wp-block-template-part' ) ) {
			clear( container );
			return;
		}
		// Measure from a clean slate: the stylesheet's no-script fallback
		// margin must not be counted twice.
		clear( container );
		container.style.setProperty( SHIFT, '0px' );
		var gap = openButton.getBoundingClientRect().top - closeBlock.getBoundingClientRect().top;
		if ( gap < -0.5 ) {
			// The hamburger is above the band: the header has scrolled partly
			// off screen. Bring it back so the lifted logo is whole. A pinned
			// header does not move on scroll; the re-measure catches that.
			// Positional arguments: instant, and no options object an older
			// engine could reject.
			window.scrollBy( 0, gap );
			gap = openButton.getBoundingClientRect().top - closeBlock.getBoundingClientRect().top;
		}
		// A header the page cannot scroll back into place (already at the top,
		// or shorter than the band) must not push the band above the viewport.
		gap = Math.max( 0, gap );
		container.style.setProperty( SHIFT, round( gap ) + 'px' );
		// Whatever offset remains between the two controls — a header with
		// different side padding, justification or direction than the band —
		// moves the real control onto the drawn X.
		var open = openButton.getBoundingClientRect();
		var close = closeBlock.getBoundingClientRect();
		container.style.setProperty( DX, round( open.left - close.left ) + 'px' );
		container.style.setProperty( DY, round( open.top - close.top ) + 'px' );
	}

	/**
	 * Drop every measured property so the next open measures fresh.
	 *
	 * @param {Element} container The nav's responsive container.
	 */
	function clear( container ) {
		container.style.removeProperty( SHIFT );
		container.style.removeProperty( DX );
		container.style.removeProperty( DY );
	}

	/**
	 * Two decimals: enough for sub-pixel layout, no float noise in the value.
	 *
	 * @param {number} px A length in CSS pixels.
	 * @return {number} The rounded length.
	 */
	function round( px ) {
		return Math.round( px * 100 ) / 100;
	}

	/**
	 * Watch one responsive container's open state.
	 *
	 * @param {Element} container The nav's responsive container.
	 */
	function watch( container ) {
		var wasOpen = container.classList.contains( OPEN_CLASS );
		var onResize = function () {
			if ( container.classList.contains( OPEN_CLASS ) ) {
				align( container );
			}
		};
		new MutationObserver( function () {
			var isOpen = container.classList.contains( OPEN_CLASS );
			if ( isOpen && ! wasOpen ) {
				align( container );
				window.addEventListener( 'resize', onResize );
			} else if ( ! isOpen && wasOpen ) {
				clear( container );
				window.removeEventListener( 'resize', onResize );
			}
			wasOpen = isOpen;
		} ).observe( container, { attributes: true, attributeFilter: [ 'class' ] } );
	}

	function init() {
		document.querySelectorAll( CONTAINER ).forEach( watch );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

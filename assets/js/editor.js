/**
 * Blocklane — editor-only adjustments.
 *
 * Core's "Rounded" image style hard-codes a 9999px radius, which both
 * bypasses the theme's border-radius tokens and exactly duplicates the
 * theme's own "Rounded Full" style (base images already default to the
 * `md` radius). Remove it from the style picker so the ladder reads:
 * default (md) → Large Rounded (lg) → Rounded Full (full).
 */
wp.domReady( function () {
	wp.blocks.unregisterBlockStyle( 'core/image', 'rounded' );
} );

/**
 * Structural-contract group styles: marquees need a doubled inner track,
 * Wall Hero absolutizes the first child, and the timeline pair expects a
 * step wrapping a rail whose first child is the node row — on an arbitrary
 * group they do nothing (or break its layout), so they stay registered
 * (pattern CSS + portability-gate roster) but leave the style picker.
 */
wp.domReady( function () {
	[
		'marquee',
		'marquee-reverse',
		'marquee-vertical',
		'wall-hero',
		'timeline-step',
		'timeline-rail',
	].forEach(
		function ( style ) {
			wp.blocks.unregisterBlockStyle( 'core/group', style );
		}
	);
} );

/**
 * Backport of Gutenberg PR #81427 (merged 2026-08-11, ships in the release
 * after WP 7.1): the List View shows each Tab Panel's actual title instead
 * of a generic "Tab" row, so a five-tab block reads as five named entries.
 * The logic is that PR's tab-panel __experimentalLabel; the settings guard
 * retires this filter the moment core ships one that labels list-view.
 */
wp.hooks.addFilter(
	'blocks.registerBlockType',
	'blocklane/tab-panel-list-view-label',
	function ( settings, name ) {
		if ( 'core/tab-panel' !== name ) {
			return settings;
		}

		// WP 7.1's core/tab-panel registers NO __experimentalLabel at all
		// (verified in wp-includes/js/dist/block-library.js: its settings are
		// { icon, edit, save }), so today coreLabel is undefined and the
		// wrapper below installs. The guard is written for the release that
		// ships one, and it is behavioral rather than existence: if core's
		// own function already labels list-view, this filter does nothing
		// and can be deleted; if core ships a partial one that answers other
		// contexts only, the wrapper defers to it and fills the list-view gap.
		var coreLabel = settings.__experimentalLabel;
		if (
			coreLabel &&
			coreLabel( { label: 'probe' }, { context: 'list-view' } )
		) {
			return settings;
		}

		return Object.assign( {}, settings, {
			__experimentalLabel: function ( attributes, args ) {
				var fromCore = coreLabel
					? coreLabel( attributes, args )
					: undefined;
				if ( fromCore ) {
					return fromCore;
				}

				var context = args && args.context;
				var label = attributes && attributes.label;
				var customName =
					attributes &&
					attributes.metadata &&
					attributes.metadata.name;
				var hasLabel = label && label.trim().length > 0;

				if ( 'list-view' === context && ( customName || hasLabel ) ) {
					return customName || label;
				}

				if ( 'breadcrumb' === context && customName ) {
					return customName;
				}
			},
		} );
	}
);

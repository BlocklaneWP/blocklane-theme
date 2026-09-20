<?php
/**
 * Blocklane — block style variation registrations.
 *
 * Each entry pairs a unique slug with `style_data` (the same shape
 * theme.json's styles tree uses) so everything is expressed as design
 * tokens — no CSS-string interpolation. Variations whose effect falls
 * outside theme.json's style tree (object-fit, line-clamp, width: 100%,
 * pseudo-element content, etc.) live in `assets/styles/core-<block>.css`
 * and are auto-loaded on demand by `blocklane_enqueue_block_styles()`
 * in `inc/assets.php`.
 *
 * @package blocklane
 * @since   0.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the theme's block style variations from one data-driven array.
 *
 * @since 0.1.0
 * @return void
 */
function blocklane_register_block_styles(): void {
	$variations = array(
		'core/button'       => array(
			// ── Component styles (Untitled-derived roster, de-UUI'd per
			// specs/2026-08-19-block-style-migration.md). Since 0.7.0 the
			// default theme.json + generated color-ramps.css define every
			// preset these reference (white/gray-*/primary-*), so they render
			// fully on the stock theme; on a foreign theme without those
			// presets they render plain.
			array(
				'name'       => 'secondary',
				'label'      => __( 'Secondary', 'blocklane' ),
				'style_data' => array(
					'color'  => array(
						'background' => 'var:preset|color|white',
						'text'       => 'var:preset|color|gray-700',
					),
					'border' => array(
						'color' => 'var:preset|color|gray-300',
						'style' => 'solid',
						'width' => '1px',
					),
					'shadow' => 'var:preset|shadow|xx-small',
					':hover' => array(
						'color' => array( 'background' => 'var:preset|color|gray-50' ),
					),
				),
			),
			array(
				'name'       => 'tertiary',
				'label'      => __( 'Tertiary', 'blocklane' ),
				'style_data' => array(
					'color'  => array(
						'background' => 'var:preset|color|gray-50',
						'text'       => 'var:preset|color|gray-700',
					),
					'border' => array(
						'style' => 'none',
						'width' => '0',
					),
					'shadow' => 'none',
				),
			),
			// (`uui-link` was deleted in the 2026-08-19 migration — `ghost`
			// is the one link-form button style.)
			array(
				'name'       => 'ghost',
				'label'      => __( 'Ghost', 'blocklane' ),
				'style_data' => array(
					'color'   => array(
						'background' => 'transparent',
						'text'       => 'var:preset|color|primary',
					),
					'border'  => array(
						'style' => 'none',
						'width' => '0',
					),
					'spacing' => array(
						'padding' => array(
							'left'  => '0',
							'right' => '0',
						),
					),
					':hover'  => array(
						'typography' => array( 'textDecoration' => 'underline' ),
					),
				),
			),
			// Full-width card buttons — no width attribute exists on the modern
			// button block; CSS lives in assets/styles/core-button.css.
			//
			// NOTE: this entry previously lived under a SECOND 'core/button'
			// array key. PHP array literals keep only the last duplicate key,
			// so the four styles above were silently discarded and never
			// registered (fixed in 0.7.0).
			array(
				'name'       => 'full-width',
				'label'      => __( 'Full Width', 'blocklane' ),
				'style_data' => array(),
			),
		),
		// The +/− disclosure marker comes from assets/styles/core-details.css —
		// pseudo-element content has no theme.json representation, so only
		// the container look lives here.
		// The open-state background lives in assets/styles/core-accordion.css —
		// state selectors (:has aria-expanded) have no theme.json representation.
		// Check-circle list markers — marker content has no theme.json
		// representation, so the CSS lives in assets/styles/core-list.css.
		'core/list'         => array(
			array(
				'name'       => 'checks',
				'label'      => __( 'Checks', 'blocklane' ),
				'style_data' => array(),
			),
		),
		// The segmented billing-toggle look — selection state is aria-driven,
		// so the CSS lives in assets/styles/core-tabs.css.
		'core/tabs'         => array(
			array(
				'name'       => 'segmented',
				'label'      => __( 'Segmented', 'blocklane' ),
				'style_data' => array(),
			),
			// The locations rail — tab list and panes side by side, entries
			// left-bordered, the active border turns primary. Selection state
			// is aria-driven, so the CSS lives in assets/styles/core-tabs.css.
			array(
				'name'       => 'vertical',
				'label'      => __( 'Vertical', 'blocklane' ),
				'style_data' => array(),
			),
			// The same rail mirrored — media in the wide LEFT track, rail on
			// the right (the Elementor/Relume switcher arrangement). CSS in
			// assets/styles/core-tabs.css beside the vertical style's.
			array(
				'name'       => 'vertical-reverse',
				'label'      => __( 'Vertical Reversed', 'blocklane' ),
				'style_data' => array(),
			),
		),
		'core/accordion'    => array(
			array(
				'name'       => 'card',
				'label'      => __( 'Card', 'blocklane' ),
				'style_data' => array(),
			),
		),
		// Post Terms — the same badge language as the paragraph style, but a
		// post-terms block renders ONE wrapper around N term links, so a badge on
		// the wrapper would draw a single pill around every category at once. The
		// pill has to land on each anchor, which style_data cannot express, so the
		// CSS lives in assets/styles/core-post-terms.css and this entry exists to
		// register the name (the portability gate reads its roster from this file).
		'core/post-terms'   => array(
			array(
				'name'       => 'badge',
				'label'      => __( 'Badge', 'blocklane' ),
				'style_data' => array(),
			),
		),
		'core/paragraph'    => array(
			// (`uui-price` was deleted in the 2026-08-19 migration — it did
			// nothing without a <sub>; in-card price suffixes now ride the
			// pricing-card cascade via core-paragraph.css.)
			array(
				'name'       => 'badge',
				'label'      => __( 'Badge', 'blocklane' ),
				'style_data' => array(
					'color'      => array(
						'background' => 'var:preset|color|gray-100',
						'text'       => 'var:preset|color|gray-700',
					),
					'border'     => array(
						'color'  => 'var:preset|color|gray-200',
						'style'  => 'solid',
						'width'  => '1px',
						'radius' => '10rem',
					),
					'typography' => array(
						'fontSize'   => '0.875rem',
						'fontWeight' => '500',
					),
					'spacing'    => array(
						'padding' => array(
							'top'    => '0.125rem',
							'right'  => '0.625rem',
							'bottom' => '0.125rem',
							'left'   => '0.625rem',
						),
					),
				),
			),
		),
		'core/details'      => array(
			array(
				'name'       => 'boxed',
				'label'      => __( 'Boxed', 'blocklane' ),
				'style_data' => array(
					'border'  => array(
						'color'  => 'var:preset|color|outline',
						'style'  => 'solid',
						'width'  => '1px',
						'radius' => 'var:preset|border-radius|md',
					),
					'spacing' => array(
						'padding' => array(
							'top'    => 'var:preset|spacing|30',
							'right'  => 'var:preset|spacing|40',
							'bottom' => 'var:preset|spacing|30',
							'left'   => 'var:preset|spacing|40',
						),
					),
				),
			),
		),
		'core/group'        => array(
			array(
				'name'       => 'card',
				'label'      => __( 'Card', 'blocklane' ),
				'style_data' => array(
					'color'   => array( 'background' => 'var:preset|color|base' ),
					'border'  => array(
						'color'  => 'var:preset|color|outline',
						'style'  => 'solid',
						'width'  => '1px',
						'radius' => 'var:preset|border-radius|md',
					),
					'shadow'  => 'var:preset|shadow|x-small',
					'spacing' => array( 'padding' => 'var:preset|spacing|50' ),
				),
			),
			// Same surface family as `card`, without the border or lift — for
			// cards that sit in a grid and get their separation from the
			// background rather than from a shadow.
			array(
				'name'       => 'card-flat',
				'label'      => __( 'Card (Flat)', 'blocklane' ),
				'style_data' => array(
					'color'   => array( 'background' => 'var:preset|color|subtle' ),
					'border'  => array( 'radius' => 'var:preset|border-radius|lg' ),
					'spacing' => array( 'padding' => 'var:preset|spacing|50' ),
				),
			),
			// Media marquees — a keyframe animation has no theme.json
			// representation, so the CSS lives in assets/styles/core-group.css.
			// Contract: the style clips, the single inner group is the track,
			// the pattern supplies the duplicated content; reduced-motion
			// stands still. (`uui-logo-marquee` merged into `marquee` in the
			// 2026-08-19 migration — one mechanism, horizontal + vertical.)
			// Structural-contract styles: registered for the gate roster and
			// pattern CSS, but hidden from the style picker in
			// assets/js/editor.js — they do nothing on an arbitrary group.
			array(
				'name'       => 'marquee',
				'label'      => __( 'Marquee', 'blocklane' ),
				'style_data' => array(),
			),
			array(
				'name'       => 'marquee-reverse',
				'label'      => __( 'Marquee Reverse', 'blocklane' ),
				'style_data' => array(),
			),
			array(
				'name'       => 'marquee-vertical',
				'label'      => __( 'Marquee Vertical', 'blocklane' ),
				'style_data' => array(),
			),
			// Wall Hero — the section's FIRST child group becomes a
			// full-bleed scrimmed backdrop (an image wall) and everything
			// after stacks above it. Core has no layered-background form
			// beyond cover's single image; this supplies the layering.
			// Structural contract: picker-hidden in assets/js/editor.js.
			array(
				'name'       => 'wall-hero',
				'label'      => __( 'Wall Hero', 'blocklane' ),
				'style_data' => array(),
			),
			// Timeline Step / Timeline Rail — the horizontal timeline rows.
			// Their rail is an inline border-TOP, and core has no way to move a
			// border to another edge at a breakpoint. Below core's own
			// columns-stacking width the row IS a vertical stack, so the rail
			// rotates with it and the section keeps reading as a timeline
			// instead of five stacked rules. Relume does the same rotation with
			// flex-col -> md:flex-row.
			//
			// Contract: `timeline-step` is the step's OUTERMOST group and owns
			// the rotated rail; `timeline-rail` is the group carrying the
			// desktop border-top, and its FIRST child group is the node row.
			// The desktop rail stays INLINE in the pattern, so a row off this
			// theme still draws it and merely keeps the horizontal-rule mobile
			// form — the mobile rotation is the enhancement, not the shape.
			// Structural-contract styles: picker-hidden in assets/js/editor.js.
			array(
				'name'       => 'timeline-step',
				'label'      => __( 'Timeline Step', 'blocklane' ),
				'style_data' => array(),
			),
			array(
				'name'       => 'timeline-rail',
				'label'      => __( 'Timeline Rail', 'blocklane' ),
				'style_data' => array(),
			),
			// Pricing Card — a SECTION style (WP 6.6+): picked once on the
			// card's wrapper group, it styles the blocks inside by cascade
			// via the nested `blocks` key, so pricing-only looks need no
			// per-block styles in every paragraph/list Styles panel.
			// Complement model (migration ruling D5): pattern cards ALSO
			// carry the same chrome inline so they render on any theme —
			// identical values, nothing double-fights. Marker and suffix
			// visuals (::before checks in core-list.css, sub sizing in
			// core-paragraph.css) have no theme.json representation.
			array(
				'name'       => 'pricing-card',
				'label'      => __( 'Pricing Card', 'blocklane' ),
				'style_data' => array(
					'color'   => array( 'background' => 'var:preset|color|base' ),
					'border'  => array(
						'color'  => 'var:preset|color|gray-200',
						'style'  => 'solid',
						'width'  => '1px',
						'radius' => '1rem',
					),
					'shadow'  => 'var:preset|shadow|medium',
					'spacing' => array( 'padding' => 'var:preset|spacing|50' ),
					'blocks'  => array(
						'core/list' => array(
							'color'   => array( 'text' => 'var:preset|color|gray-600' ),
							'spacing' => array( 'padding' => array( 'left' => '0' ) ),
						),
					),
				),
			),
			// Scroll-linked fade — CSS lives in assets/styles/core-group.css.
			// A scroll-driven animation has no theme.json representation.
			//
			// Applies to the container's direct children, not the container
			// itself: each child needs its own view() timeline so they fade
			// individually as they rise up the viewport. It also keeps the
			// motion off the children's own style slot, since a block can
			// only carry one `is-style-*` at a time — the cards themselves
			// use `card-flat`.
			array(
				'name'  => 'fade-children',
				'label' => __( 'Scroll Fade (Children)', 'blocklane' ),
			),
		),
		'core/separator'    => array(
			// `wide` — border tokens native; width: 100% override lives in
			// assets/styles/core-separator.css.
			array(
				'name'       => 'wide',
				'label'      => __( 'Wide', 'blocklane' ),
				'style_data' => array(
					'border' => array(
						'color'  => 'var:preset|color|muted',
						'bottom' => array( 'width' => '2px' ),
					),
				),
			),
		),
		// `plain` — drops the markers core leaves on a standalone page list.
		// `list-style` has no theme.json representation, so the CSS lives in
		// assets/styles/core-page-list.css.
		'core/page-list'    => array(
			array(
				'name'  => 'plain',
				'label' => __( 'Plain', 'blocklane' ),
			),
		),
	);

	foreach ( $variations as $block => $styles ) {
		foreach ( $styles as $style ) {
			$args = array(
				'name'  => $style['name'],
				'label' => $style['label'],
			);
			if ( isset( $style['style_data'] ) ) {
				$args['style_data'] = $style['style_data'];
			}
			register_block_style( $block, $args );
		}
	}
}
add_action( 'init', 'blocklane_register_block_styles' );

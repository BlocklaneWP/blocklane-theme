<?php
/**
 * Blocklane — site-level configuration and WP core compat.
 *
 * Custom "Sidebar" and "Menu" template-part areas (the Menu registration
 * defers to any layer that got there first — Blocklane Pro registers the
 * same area), one-shot discussion defaults set at theme activation, and
 * the WP 6.8+ Navigation block aria-label duplication workaround.
 *
 * @package blocklane
 * @since   0.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a "sidebar" template part area so Sidebar parts group on their own
 * in the Site Editor instead of landing under the catch-all "General" bucket
 * alongside comments and post-meta — and a "menu" area, because the theme
 * ships parts/wireframe-mega-*.html declaring it: without a registration the
 * Site Editor downgrades them to "uncategorized", and the theme must not
 * depend on Blocklane Pro (whose menu-designer registers the same area, and
 * defers when one exists) for its own parts to categorize (#162).
 *
 * @since 0.3.0
 * @param array $areas Default template part area definitions.
 * @return array
 */
function blocklane_register_template_part_areas( array $areas ): array {
	$areas[] = array(
		'area'        => 'sidebar',
		'area_tag'    => 'aside',
		'label'       => __( 'Sidebar', 'blocklane' ),
		'description' => __( 'Sidebar template parts.', 'blocklane' ),
		'icon'        => 'sidebar',
	);

	foreach ( $areas as $area ) {
		if ( 'menu' === ( $area['area'] ?? '' ) ) {
			return $areas; // Another layer (Blocklane Pro) got here first.
		}
	}
	$areas[] = array(
		'area'        => 'menu',
		'area_tag'    => 'div',
		'label'       => __( 'Menu', 'blocklane' ),
		'description' => __( 'Menu template parts — mega menu panels and mobile menu replacements.', 'blocklane' ),
		'icon'        => 'layout',
	);

	return $areas;
}
add_filter( 'default_wp_template_part_areas', 'blocklane_register_template_part_areas' );

/**
 * Flip site-wide discussion defaults to off on theme activation so new posts
 * ship without a comment form, don't accept pingbacks/trackbacks, and don't
 * auto-ping other sites on outbound links. Users can still re-enable via
 * Settings → Discussion, and existing posts are never touched.
 *
 * Applied once per site: after_switch_theme fires on every (re-)activation,
 * and without the guard a temporary theme switch would silently clobber the
 * owner's deliberate Discussion choices on the way back.
 *
 * @since 0.3.0
 * @return void
 */
function blocklane_set_defaults_on_activation(): void {
	if ( get_option( 'blocklane_defaults_applied' ) ) {
		return;
	}

	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'default_pingback_flag', 0 );
	update_option( 'blocklane_defaults_applied', 1 );
}
add_action( 'after_switch_theme', 'blocklane_set_defaults_on_activation' );

/**
 * Dedupe adjacent repeated tokens in the core Navigation block's aria-label.
 *
 * Why: in WP 6.8+ the Navigation block renders aria-label twice from the
 * same `ariaLabel` attribute — once through its own render.php (navigation.php
 * line 575) and once through the universal `ariaLabel` block support
 * (block-supports/aria-label.php). `get_block_wrapper_attributes()` merges
 * the two into a single attribute by concatenation, so an ariaLabel of
 * "Primary" emits `aria-label="Primary Primary"`. Stripping the attribute
 * from our template gets us no aria-label at all (the block's fallback
 * only fires for `ref`-backed navigations). Keeping it and deduping adjacent
 * repeats is the least-invasive workaround until the core regression is
 * fixed upstream.
 *
 * How to apply: runs only on core/navigation render output, only when a
 * duplicate-token aria-label is present. Won't touch legitimate labels
 * like "Primary Navigation".
 *
 * @since 0.2.1
 * @param string $block_content Rendered block HTML.
 * @return string
 */
function blocklane_dedupe_nav_aria_label( string $block_content ): string {
	// Limit 1 — only the first (outermost) aria-label, which is always on
	// the <nav> wrapper where the WP 6.8+ duplicate is emitted. Leaves any
	// aria-labels on inner <a> children (e.g., from submenu toggles) alone.
	$result = preg_replace_callback(
		'/\baria-label="([^"]+)"/',
		static function ( array $m ): string {
			$tokens = preg_split( '/\s+/', trim( $m[1] ) );
			if ( count( $tokens ) < 2 ) {
				return $m[0];
			}
			// Collapse runs of identical adjacent tokens: ["Primary","Primary"] → ["Primary"].
			$deduped = array();
			foreach ( $tokens as $token ) {
				if ( empty( $deduped ) || end( $deduped ) !== $token ) {
					$deduped[] = $token;
				}
			}
			return 'aria-label="' . implode( ' ', $deduped ) . '"';
		},
		$block_content,
		1
	);

	// preg_replace_callback returns null on PCRE error (e.g., backtrack
	// limit exceeded on unusually long nav markup). Fall back to the
	// untouched content rather than propagating null — a correctly-rendered
	// nav with a duplicated aria-label is still better than no nav at all.
	return is_string( $result ) ? $result : $block_content;
}
add_filter( 'render_block_core/navigation', 'blocklane_dedupe_nav_aria_label' );

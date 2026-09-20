<?php
/**
 * Blocklane — Full Site Editing Block Theme.
 *
 * Thin bootstrap: declares the theme constants, registers theme
 * supports, and loads the modular concerns from /inc/:
 *
 *   inc/assets.php        Color-ramp + print stylesheet enqueue, on-demand
 *                         per-block CSS loader, core block CSS split, emoji
 *                         polyfill removal. (Fonts are theme.json fontFace
 *                         presets — nothing to enqueue.)
 *   inc/block-styles.php  `register_block_style()` data loop for every theme
 *                         block-style variation.
 *   inc/site-config.php   Custom Sidebar template-part area, discussion
 *                         defaults set on activation, WP 6.8+ nav
 *                         aria-label duplication workaround.
 *   inc/mega-menu-fallback.php  Read-only editor stand-in for Blocklane
 *                         Pro's mega menu while the plugin is inactive,
 *                         so navigation degrades without "unsupported
 *                         block" placeholders.
 *   inc/icons.php         Blocklane icon collection registered with core's
 *                         Icons API (WP 7.1+) so placed icons outlive the
 *                         Pro plugin.
 *
 * All visual design decisions live in theme.json; this file is plumbing.
 *
 * @package blocklane
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'BLOCKLANE_VERSION' ) ) {
	// Derived from style.css so asset cache-busters move with every release —
	// the hand-bumped literal here sat at 0.4.0 through six releases while
	// the theme shipped 0.5.0…0.6.3.
	define( 'BLOCKLANE_VERSION', (string) wp_get_theme( get_template() )->get( 'Version' ) );
}

// Theme updates come from the Blocklane update gateway (ungated — the theme
// ships with client sites and stays after handoff, so there is no license or
// session to present). plugin-update-checker filters the update_themes
// transient; WordPress surfaces the update in Appearance → Themes and
// Dashboard → Updates. Override in wp-config to point at staging.
if ( ! defined( 'BLOCKLANE_UPDATE_MANIFEST_URL' ) ) {
	define( 'BLOCKLANE_UPDATE_MANIFEST_URL', 'https://uyrbfzgfkiufiekvssrt.supabase.co/functions/v1/plugin-update?plugin=blocklane' );
}

require_once get_template_directory() . '/inc/plugin-update-checker/plugin-update-checker.php';

if ( BLOCKLANE_UPDATE_MANIFEST_URL && class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	\YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		BLOCKLANE_UPDATE_MANIFEST_URL,
		get_template_directory(),
		'blocklane'
	);
}

/**
 * Register theme-level features, translations, editor assets, and opt
 * out of the WordPress.org remote block pattern directory so only
 * the theme's own categories/patterns appear in the inserter.
 *
 * @since 0.1.0
 * @return void
 */
function blocklane_setup() {
	load_theme_textdomain( 'blocklane', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array(
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'script',
			'search-form',
			'style',
		)
	);
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	$editor_styles = array(
		'assets/css/color-ramps.css',
		'assets/css/editor-style.css',
	);

	/*
	 * Block style variation CSS. The front end loads these per block via
	 * wp_enqueue_block_style() (inc/assets.php), which is render-triggered
	 * and never reaches the editor canvas — so without this every variation
	 * whose effect lives in a file (the Boxed details marker, Scroll Fade,
	 * the Plain page list, nav link states, striped tables) previewed as
	 * unstyled in the editor while rendering correctly on the front end.
	 *
	 * Loaded wholesale rather than per block: the editor can show any block
	 * at any time, so there is nothing to load on demand against.
	 */
	$block_style_files = glob( get_theme_file_path( '/assets/styles/core-*.css' ) );
	if ( is_array( $block_style_files ) ) {
		foreach ( $block_style_files as $file ) {
			$editor_styles[] = 'assets/styles/' . basename( $file );
		}
	}

	add_editor_style( $editor_styles );

	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'blocklane_setup' );

// Modular concerns.
require_once get_theme_file_path( 'inc/upgrade.php' );  // one-time version upgrades
require_once get_theme_file_path( 'inc/assets.php' );
require_once get_theme_file_path( 'inc/block-styles.php' );
require_once get_theme_file_path( 'inc/site-config.php' );
require_once get_theme_file_path( 'inc/mega-menu-fallback.php' );
require_once get_theme_file_path( 'inc/icons.php' );

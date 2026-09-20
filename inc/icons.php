<?php
/**
 * Icon Library — theme-side registration into core's Icons API (WP 7.1+).
 *
 * Blocklane Pro's icon picker writes core/icon blocks whose names live in
 * the blocklane-pro collection. That is content, not tooling, so it must
 * keep rendering with the plugin deactivated — and the free theme is the
 * layer that survives. This pass registers the collection first (init 10):
 * the bundled Phosphor set from assets/icon-library/ files (file_path, so
 * core reads and sanitizes an icon only when it first renders) and any
 * user-saved custom icons from the blocklane_pro_custom_icons option
 * (which uninstall deliberately keeps). Blocklane Pro runs the identical
 * pass at init 20 for non-companion themes; is_registered() guards make
 * the two homes order-safe in both directions.
 *
 * WP 7.0 ships the Icons API with a sealed registry — no
 * wp_register_icon() — so there this file is a no-op and the plugin's
 * render-callback wrapper carries placed icons instead.
 *
 * @package blocklane
 * @since   0.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the blocklane-pro icon collection and its icons with core.
 *
 * @since 0.5.0
 * @return void
 */
function blocklane_register_icon_library(): void {
	// Core opened icon registration in 7.1; the 7.0 registry is sealed.
	if ( ! function_exists( 'wp_register_icon_collection' ) ) {
		return;
	}

	// Safe mode silences every Blocklane surface, theme-side included.
	if ( defined( 'BLOCKLANE_PRO_SAFE_MODE' ) && BLOCKLANE_PRO_SAFE_MODE ) {
		return;
	}

	if ( ! WP_Icon_Collections_Registry::get_instance()->is_registered( 'blocklane-pro' ) ) {
		wp_register_icon_collection(
			'blocklane-pro',
			array(
				'label'       => __( 'Blocklane', 'blocklane' ),
				'description' => __( 'Phosphor icons bundled with Blocklane, plus icons saved from its cloud library.', 'blocklane' ),
			)
		);
	}

	$registry = WP_Icons_Registry::get_instance();

	$manifest = include get_theme_file_path( 'inc/icon-library-manifest.php' );
	foreach ( (array) $manifest as $name => $label ) {
		$icon_name = 'blocklane-pro/' . $name;
		if ( $registry->is_registered( $icon_name ) ) {
			continue;
		}
		wp_register_icon(
			$icon_name,
			array(
				'label'     => (string) $label,
				'file_path' => get_theme_file_path( 'assets/icon-library/' . $name . '.svg' ),
			)
		);
	}

	// Custom icons: prefer the uploads mirror Blocklane Pro writes at save
	// (uploads/blocklane-icons/<name>.svg — the convention is duplicated in
	// the plugin's icon-library.php, since each side must work without the
	// other). file_path is lazy: core reads and sanitizes an icon only when
	// it first renders. Without a mirror file the option row's SVG registers
	// as content. Names outside the collection (or already registered by a
	// newer layer) are skipped rather than argued with.
	$uploads    = wp_upload_dir( null, false );
	$mirror_dir = empty( $uploads['error'] ) && ! empty( $uploads['basedir'] )
		? $uploads['basedir'] . '/blocklane-icons/'
		: '';

	foreach ( (array) get_option( 'blocklane_pro_custom_icons', array() ) as $custom ) {
		$name    = isset( $custom['name'] ) ? (string) $custom['name'] : '';
		$content = isset( $custom['content'] ) ? (string) $custom['content'] : '';
		if ( '' === $content || 0 !== strpos( $name, 'blocklane-pro/' ) || $registry->is_registered( $name ) ) {
			continue;
		}

		$args = array(
			'label' => isset( $custom['label'] ) ? (string) $custom['label'] : $name,
		);

		$mirror = '';
		if ( '' !== $mirror_dir && preg_match( '#^blocklane-pro/([a-z0-9](?:[a-z0-9_-]*[a-z0-9])?)$#', $name, $m ) ) {
			$mirror = $mirror_dir . $m[1] . '.svg';
		}
		if ( '' !== $mirror && file_exists( $mirror ) ) {
			$args['file_path'] = $mirror;
		} else {
			$args['content'] = $content;
		}

		wp_register_icon( $name, $args );
	}
}
add_action( 'init', 'blocklane_register_icon_library' );

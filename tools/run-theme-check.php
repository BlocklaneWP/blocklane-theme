<?php
/**
 * Run Theme Check against blocklane and dump the raw output.
 *
 * Invoke via:
 *   studio wp --path=. eval-file tools/run-theme-check.php
 *
 * Theme Check's admin UI gathers results into the $themechecks global after
 * calling run_themechecks_against_theme(). We invoke the same entry point and
 * read back the $tc_results buffer that the plugin fills.
 */

if ( ! function_exists( 'run_themechecks_against_theme' ) ) {
	$main = WP_PLUGIN_DIR . '/theme-check/main.php';
	$base = WP_PLUGIN_DIR . '/theme-check/checkbase.php';
	if ( file_exists( $main ) ) {
		require_once $base;
		require_once $main;
	}
	if ( ! function_exists( 'run_themechecks_against_theme' ) ) {
		echo "theme-check plugin not loadable\n";
		return;
	}
}

$slug = 'blocklane';
$theme = wp_get_theme( $slug );
if ( ! $theme->exists() ) {
	echo "theme not found: $slug\n";
	return;
}

$result = run_themechecks_against_theme( $theme, $slug );

echo "=== Theme Check result flag: " . ( $result ? 'TRUE (all checks passed or only minor)' : 'FALSE (has errors/warnings)' ) . " ===\n";

global $themechecks;
if ( ! is_array( $themechecks ) ) {
	echo "no \$themechecks global populated\n";
	return;
}

$buckets = [ 'error' => [], 'warning' => [], 'recommended' => [], 'info' => [] ];
foreach ( $themechecks as $check ) {
	if ( ! method_exists( $check, 'getError' ) ) {
		continue;
	}
	$messages = $check->getError();
	if ( empty( $messages ) ) {
		continue;
	}
	foreach ( $messages as $raw ) {
		$text = wp_strip_all_tags( $raw );
		if ( stripos( $raw, 'tc-required' ) !== false || stripos( $raw, 'tc-error' ) !== false || stripos( $raw, 'class="tc-lead tc-required"' ) !== false || stripos( $raw, 'REQUIRED' ) !== false ) {
			$buckets['error'][] = $text;
		} elseif ( stripos( $raw, 'WARNING' ) !== false || stripos( $raw, 'tc-warning' ) !== false ) {
			$buckets['warning'][] = $text;
		} elseif ( stripos( $raw, 'RECOMMENDED' ) !== false || stripos( $raw, 'tc-recommended' ) !== false ) {
			$buckets['recommended'][] = $text;
		} else {
			$buckets['info'][] = $text;
		}
	}
}

foreach ( $buckets as $level => $lines ) {
	echo "\n--- " . strtoupper( $level ) . " (" . count( $lines ) . ") ---\n";
	foreach ( $lines as $line ) {
		echo trim( preg_replace( '/\s+/', ' ', $line ) ) . "\n";
	}
}

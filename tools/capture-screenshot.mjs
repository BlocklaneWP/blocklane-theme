/**
 * Phase 8 screenshot capture.
 *
 * Temporarily swaps the front-page content with a release demo (hero + three
 * feature columns, all core blocks), captures a 1200x900 screenshot, then
 * restores the original content and template. The output lands at
 * `screenshot.png` in the theme root — the size and format WordPress.org
 * expects.
 *
 * Usage: node tools/capture-screenshot.mjs
 */

import { chromium } from 'playwright';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { execFileSync } from 'node:child_process';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const THEME_ROOT = resolve( __dirname, '..' );
const SITE_ROOT = resolve( THEME_ROOT, '../../..' );
const SITE_URL = process.env.SITE_URL || 'http://localhost:8881';
const HOME_ID = Number( process.env.HOME_ID || 2 );

const DEMO_CONTENT = `<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|90"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--90)">
	<!-- wp:paragraph {"align":"center","fontSize":"small","style":{"color":{"text":"var:preset|color|primary"},"typography":{"textTransform":"uppercase","letterSpacing":"0.08em","fontWeight":"600"}}} -->
	<p class="has-text-align-center has-text-color has-small-font-size" style="color:var(--wp--preset--color--primary);font-weight:600;letter-spacing:0.08em;text-transform:uppercase">A block theme, fully editable</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"heading-xxx-large","style":{"typography":{"letterSpacing":"-0.03em","lineHeight":"1.05"}}} -->
	<h1 class="wp-block-heading has-text-align-center has-heading-xxx-large-font-size" style="letter-spacing:-0.03em;line-height:1.05">A versatile foundation for thoughtful sites.</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","fontSize":"x-large","style":{"color":{"text":"var:preset|color|muted"},"spacing":{"margin":{"top":"var:preset|spacing|30"}},"typography":{"lineHeight":"1.5"}}} -->
	<p class="has-text-align-center has-x-large-font-size has-text-color" style="color:var(--wp--preset--color--muted);margin-top:var(--wp--preset--spacing--30);line-height:1.5">Core blocks, a semantic two-tier token system, WCAG 2.1 AA by default — every visual decision lives in one file.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:button {"fontSize":"medium"} -->
		<div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link wp-element-button" href="/about/">Explore the system</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"fontSize":"medium","className":"is-style-ghost"} -->
		<div class="wp-block-button has-custom-font-size has-medium-font-size is-style-ghost"><a class="wp-block-button__link wp-element-button" href="/blog/">Read the journal →</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:separator {"className":"is-style-wide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|70"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--70)"/>
<!-- /wp:separator -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"heading-medium","style":{"typography":{"letterSpacing":"-0.015em"}}} -->
		<h3 class="wp-block-heading has-heading-medium-font-size" style="letter-spacing:-0.015em">Design tokens</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}}} -->
		<p class="has-text-color" style="color:var(--wp--preset--color--muted)">Every color, size, radius, and shadow is a named preset. Retheme the whole site by editing <code>theme.json</code>.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"heading-medium","style":{"typography":{"letterSpacing":"-0.015em"}}} -->
		<h3 class="wp-block-heading has-heading-medium-font-size" style="letter-spacing:-0.015em">Core blocks only</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}}} -->
		<p class="has-text-color" style="color:var(--wp--preset--color--muted)">No page builders, no ACF, no third-party libraries. Templates and patterns compose from core blocks alone.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"fontSize":"heading-medium","style":{"typography":{"letterSpacing":"-0.015em"}}} -->
		<h3 class="wp-block-heading has-heading-medium-font-size" style="letter-spacing:-0.015em">Accessible by default</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}}} -->
		<p class="has-text-color" style="color:var(--wp--preset--color--muted)">WCAG 2.1 Level AA, forward-compatible with 2.2. A full audit ships in <code>docs/accessibility.md</code>.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
`;

function wpEval( phpCode ) {
	return execFileSync( 'studio', [ 'wp', '--path=' + SITE_ROOT, 'eval', phpCode ], {
		encoding: 'utf8',
		stdio: [ 'ignore', 'pipe', 'pipe' ],
	} );
}

// Stash the live home content + template, swap in the demo.
wpEval( `
	global $__orig_home;
	$p = get_post( ${ HOME_ID } );
	$__orig_home = [
		'content' => $p->post_content,
		'template' => get_page_template_slug( ${ HOME_ID } ),
	];
	file_put_contents( sys_get_temp_dir() . '/blocklane-home.json', wp_json_encode( $__orig_home ) );
	wp_update_post( [ 'ID' => ${ HOME_ID }, 'post_content' => ${ JSON.stringify( DEMO_CONTENT ) } ] );
` );

try {
	const browser = await chromium.launch();
	const context = await browser.newContext( {
		viewport: { width: 1200, height: 900 },
		deviceScaleFactor: 1,
		colorScheme: 'light',
	} );
	const page = await context.newPage();
	await page.goto( SITE_URL + '/', { waitUntil: 'networkidle' } );
	// The 'landing' page template this tool once assigned was removed in
	// 0.3.0, so the page renders with page.html's post-title. Hide it at
	// capture time only — the demo hero is the shot's h1-equivalent.
	await page.addStyleTag( {
		content: '.wp-block-post-title { display: none; }',
	} );
	await page.evaluate( () => document.fonts.ready );
	await page.waitForTimeout( 500 );
	await page.screenshot( {
		path: resolve( THEME_ROOT, 'screenshot.png' ),
		clip: { x: 0, y: 0, width: 1200, height: 900 },
		type: 'png',
	} );
	await browser.close();
	console.log( 'Wrote screenshot.png (1200x900)' );
} finally {
	// Always restore — even on capture failure.
	wpEval( `
		$raw = @file_get_contents( sys_get_temp_dir() . '/blocklane-home.json' );
		if ( $raw ) {
			$d = json_decode( $raw, true );
			wp_update_post( [ 'ID' => ${ HOME_ID }, 'post_content' => $d['content'] ] );
			if ( empty( $d['template'] ) ) {
				delete_post_meta( ${ HOME_ID }, '_wp_page_template' );
			} else {
				update_post_meta( ${ HOME_ID }, '_wp_page_template', $d['template'] );
			}
			@unlink( sys_get_temp_dir() . '/blocklane-home.json' );
		}
	` );
}

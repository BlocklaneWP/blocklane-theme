/**
 * Phase 7 accessibility scan.
 *
 * Launches headless Chromium, loads each representative route, injects axe-core
 * from the local node_modules copy, runs the WCAG 2.1 AA ruleset (one "advisory"
 * pass with best-practice + 2.2 rules enabled), and writes per-route JSON plus a
 * combined markdown summary to docs/accessibility/.
 *
 * Usage:
 *   node tools/a11y-scan.mjs
 *
 * Run from the theme directory. The dev site must be running at SITE_URL.
 */

import { chromium } from 'playwright';
import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const THEME_ROOT = resolve( __dirname, '..' );
const OUT_DIR = resolve( THEME_ROOT, 'docs/accessibility' );
const AXE_PATH = resolve( THEME_ROOT, 'node_modules/axe-core/axe.min.js' );

const SITE_URL = process.env.SITE_URL || 'http://localhost:8881';
const FIXTURE_POST_ID = Number( process.env.FIXTURE_POST_ID || 26 );

const ROUTES = [
	{ slug: 'home',    url: `${ SITE_URL }/`, label: 'Home' },
	{ slug: 'page',    url: `${ SITE_URL }/about/`, label: 'Page (About)' },
	{ slug: 'single',  url: `${ SITE_URL }/?p=${ FIXTURE_POST_ID }`, label: 'Single post (fixture)' },
	{ slug: 'archive', url: `${ SITE_URL }/?post_type=post`, label: 'Archive' },
	{ slug: 'search',  url: `${ SITE_URL }/?s=accessibility`, label: 'Search results' },
	{ slug: '404',     url: `${ SITE_URL }/this-does-not-exist-${ Date.now() }/`, label: '404' },
];

// WCAG 2.1 AA — our stated compliance floor.
const AA_TAGS = [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa' ];

// Advisory — best-practice plus WCAG 2.2 AA (forward-compat).
const ADVISORY_TAGS = [ 'wcag22aa', 'best-practice' ];

const axeSource = readFileSync( AXE_PATH, 'utf8' );

if ( ! existsSync( OUT_DIR ) ) {
	mkdirSync( OUT_DIR, { recursive: true } );
}

async function scan( page, route, tags ) {
	await page.goto( route.url, { waitUntil: 'networkidle', timeout: 30000 } );
	await page.addScriptTag( { content: axeSource } );
	return page.evaluate( ( runTags ) => {
		// eslint-disable-next-line no-undef
		return axe.run( document, {
			runOnly: { type: 'tag', values: runTags },
			resultTypes: [ 'violations', 'incomplete' ],
		} );
	}, tags );
}

function summarizeViolations( violations ) {
	return violations.map( ( v ) => ( {
		id: v.id,
		impact: v.impact,
		description: v.description,
		help: v.help,
		helpUrl: v.helpUrl,
		tags: v.tags,
		nodes: v.nodes.map( ( n ) => ( {
			target: n.target,
			html: n.html.slice( 0, 240 ),
			failureSummary: n.failureSummary,
		} ) ),
	} ) );
}

function renderMarkdown( routeResults ) {
	const lines = [];
	lines.push( '# Phase 7 — axe-core scan results' );
	lines.push( '' );
	lines.push( `_Generated ${ new Date().toISOString() } — axe-core injected from local node_modules._` );
	lines.push( '' );
	lines.push( '## Summary' );
	lines.push( '' );
	lines.push( '| Route | AA violations | AA incomplete | Advisory violations |' );
	lines.push( '| --- | ---: | ---: | ---: |' );
	for ( const r of routeResults ) {
		lines.push(
			`| ${ r.label } | ${ r.aa.violations.length } | ${ r.aa.incomplete.length } | ${ r.advisory.violations.length } |`
		);
	}
	lines.push( '' );

	for ( const r of routeResults ) {
		lines.push( `## ${ r.label } — \`${ r.url }\`` );
		lines.push( '' );

		lines.push( '### WCAG 2.1 AA violations' );
		if ( r.aa.violations.length === 0 ) {
			lines.push( '_None._' );
		} else {
			for ( const v of r.aa.violations ) {
				lines.push( `- **${ v.id }** (${ v.impact }) — ${ v.help }` );
				lines.push( `  - ${ v.description }` );
				lines.push( `  - Tags: \`${ v.tags.join( ', ' ) }\`` );
				lines.push( `  - Docs: ${ v.helpUrl }` );
				lines.push( `  - Nodes: ${ v.nodes.length }` );
				for ( const n of v.nodes.slice( 0, 3 ) ) {
					lines.push( `    - \`${ n.target.join( ' ' ) }\`` );
					lines.push( '      ```html' );
					lines.push( `      ${ n.html }` );
					lines.push( '      ```' );
				}
			}
		}
		lines.push( '' );

		lines.push( '### WCAG 2.1 AA incomplete (needs manual review)' );
		if ( r.aa.incomplete.length === 0 ) {
			lines.push( '_None._' );
		} else {
			for ( const v of r.aa.incomplete ) {
				lines.push( `- **${ v.id }** — ${ v.help } (${ v.nodes.length } node${ v.nodes.length === 1 ? '' : 's' })` );
			}
		}
		lines.push( '' );

		lines.push( '### Advisory (WCAG 2.2 AA + best-practice)' );
		if ( r.advisory.violations.length === 0 ) {
			lines.push( '_None._' );
		} else {
			for ( const v of r.advisory.violations ) {
				lines.push( `- **${ v.id }** (${ v.impact }) — ${ v.help } — \`${ v.tags.join( ', ' ) }\`` );
				lines.push( `  - Docs: ${ v.helpUrl }` );
				lines.push( `  - Nodes: ${ v.nodes.length }` );
			}
		}
		lines.push( '' );
	}

	return lines.join( '\n' );
}

( async () => {
	const browser = await chromium.launch();
	const context = await browser.newContext();
	const page = await context.newPage();

	const routeResults = [];
	for ( const route of ROUTES ) {
		process.stdout.write( `Scanning ${ route.label } ... ` );
		try {
			const aa = await scan( page, route, AA_TAGS );
			const advisory = await scan( page, route, ADVISORY_TAGS );
			routeResults.push( {
				slug: route.slug,
				label: route.label,
				url: route.url,
				aa: {
					violations: summarizeViolations( aa.violations ),
					incomplete: summarizeViolations( aa.incomplete ),
				},
				advisory: {
					violations: summarizeViolations( advisory.violations ),
				},
			} );
			console.log( `AA: ${ aa.violations.length } violations, ${ aa.incomplete.length } incomplete — advisory: ${ advisory.violations.length } violations` );
		} catch ( err ) {
			console.log( `error — ${ err.message }` );
			routeResults.push( {
				slug: route.slug,
				label: route.label,
				url: route.url,
				error: err.message,
				aa: { violations: [], incomplete: [] },
				advisory: { violations: [] },
			} );
		}
	}

	await browser.close();

	writeFileSync(
		resolve( OUT_DIR, 'axe-results.json' ),
		JSON.stringify( routeResults, null, 2 )
	);
	writeFileSync(
		resolve( OUT_DIR, 'axe-results.md' ),
		renderMarkdown( routeResults )
	);

	console.log( `\nWrote docs/accessibility/axe-results.{json,md}` );
} )();

/**
 * Phase 7 contrast matrix.
 *
 * Reads theme.json, resolves every named color slug to its final #rrggbb value
 * (following nested var(--wp--preset--color--*) references), then computes WCAG
 * 2.1 contrast ratios and APCA (SAPC-8) Lc scores for every text/background
 * pair actually in use across theme.json styles, block-style variations, and
 * element states. Outputs a markdown table to docs/accessibility/contrast.md.
 *
 * Usage:
 *   node tools/contrast-matrix.mjs
 */

import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const THEME_ROOT = resolve( __dirname, '..' );
const OUT_DIR = resolve( THEME_ROOT, 'docs/accessibility' );

const themeJson = JSON.parse(
	readFileSync( resolve( THEME_ROOT, 'theme.json' ), 'utf8' )
);

/* -----------------------------------------------------------------------------
 * Color resolution — a theme.json color may be a #rrggbb literal OR a reference
 * like "var(--wp--preset--color--contrast)". Walk the references until we hit
 * a literal.
 * ---------------------------------------------------------------------------*/
const palette = Object.fromEntries(
	themeJson.settings.color.palette.map( ( c ) => [ c.slug, c.color ] )
);

function resolveColor( value, depth = 0 ) {
	if ( depth > 8 ) throw new Error( 'Color reference loop' );
	if ( /^#[0-9a-f]{6}$/i.test( value ) ) return value.toLowerCase();
	const m = value.match( /^var\(--wp--preset--color--([a-z0-9-]+)\)$/ );
	if ( ! m ) throw new Error( `Unresolvable color: ${ value }` );
	return resolveColor( palette[ m[ 1 ] ], depth + 1 );
}

const resolved = Object.fromEntries(
	Object.entries( palette ).map( ( [ slug, v ] ) => [ slug, resolveColor( v ) ] )
);

/* -----------------------------------------------------------------------------
 * Ramp layer — the under-the-hood slugs from assets/design-tokens/ramps.json.
 * Derived families are color-mix(in oklab, seed, white|black N%) over the
 * palette roles; replicate the browser's OKLab interpolation here so audited
 * hexes match what actually renders.
 * ---------------------------------------------------------------------------*/
const ramps = JSON.parse(
	readFileSync( resolve( THEME_ROOT, 'assets/design-tokens/ramps.json' ), 'utf8' )
);

const srgbToLin = ( c ) =>
	( c /= 255 ) <= 0.04045 ? c / 12.92 : Math.pow( ( c + 0.055 ) / 1.055, 2.4 );
const linToSrgb = ( c ) => {
	c = Math.min( 1, Math.max( 0, c ) );
	const v = c <= 0.0031308 ? 12.92 * c : 1.055 * Math.pow( c, 1 / 2.4 ) - 0.055;
	return Math.round( v * 255 );
};

function hexToOklab( hex ) {
	const n = parseInt( hex.slice( 1 ), 16 );
	const [ r, g, b ] = [ ( n >> 16 ) & 0xff, ( n >> 8 ) & 0xff, n & 0xff ].map( srgbToLin );
	const l = Math.cbrt( 0.4122214708 * r + 0.5363325363 * g + 0.0514459929 * b );
	const m = Math.cbrt( 0.2119034982 * r + 0.6806995451 * g + 0.1073969566 * b );
	const s = Math.cbrt( 0.0883024619 * r + 0.2817188376 * g + 0.6299787005 * b );
	return [
		0.2104542553 * l + 0.793617785 * m - 0.0040720468 * s,
		1.9779984951 * l - 2.428592205 * m + 0.4505937099 * s,
		0.0259040371 * l + 0.7827717662 * m - 0.808675766 * s,
	];
}

function oklabToHex( [ L, a, bb ] ) {
	const l = Math.pow( L + 0.3963377774 * a + 0.2158037573 * bb, 3 );
	const m = Math.pow( L - 0.1055613458 * a - 0.0638541728 * bb, 3 );
	const s = Math.pow( L - 0.0894841775 * a - 1.291485548 * bb, 3 );
	const r = linToSrgb( 4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s );
	const g = linToSrgb( -1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s );
	const b = linToSrgb( -0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s );
	return '#' + [ r, g, b ].map( ( v ) => v.toString( 16 ).padStart( 2, '0' ) ).join( '' );
}

function mixOklab( seedHex, towards, pct ) {
	const a = hexToOklab( seedHex );
	const b = hexToOklab( towards === 'white' ? '#ffffff' : '#000000' );
	const t = pct / 100;
	return oklabToHex( a.map( ( v, i ) => v + ( b[ i ] - v ) * t ) );
}

for ( const [ family, steps ] of Object.entries( ramps.fixedFamilies ) ) {
	for ( const [ step, hex ] of Object.entries( steps ) ) {
		resolved[ step === '' ? family : `${ family }-${ step }` ] = hex;
	}
}
for ( const [ family, { seedVar } ] of Object.entries( ramps.derivedFamilies ) ) {
	const seedSlug = seedVar.replace( '--wp--preset--color--', '' );
	for ( const [ step, { mix, pct } ] of Object.entries( ramps.derivedSteps ) ) {
		resolved[ `${ family }-${ step }` ] =
			! mix || pct === 0 ? resolved[ seedSlug ] : mixOklab( resolved[ seedSlug ], mix, pct );
	}
}

/* -----------------------------------------------------------------------------
 * WCAG 2.1 relative luminance and contrast ratio.
 * ---------------------------------------------------------------------------*/
function hexToRgb( hex ) {
	const n = parseInt( hex.slice( 1 ), 16 );
	return [ ( n >> 16 ) & 0xff, ( n >> 8 ) & 0xff, n & 0xff ];
}

function relativeLuminance( hex ) {
	const [ r, g, b ] = hexToRgb( hex ).map( ( c ) => {
		const s = c / 255;
		return s <= 0.03928 ? s / 12.92 : Math.pow( ( s + 0.055 ) / 1.055, 2.4 );
	} );
	return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function wcagRatio( fg, bg ) {
	const [ l1, l2 ] = [ relativeLuminance( fg ), relativeLuminance( bg ) ].sort(
		( a, b ) => b - a
	);
	return ( l1 + 0.05 ) / ( l2 + 0.05 );
}

/* -----------------------------------------------------------------------------
 * APCA (SAPC-8, W3 working draft). Formula from the public APCA reference:
 * https://github.com/Myndex/apca-w3 — implemented inline so we don't take a
 * runtime dep. Returns Lc (perceptual-contrast) in the range ~±108.
 * ---------------------------------------------------------------------------*/
function apcaY( hex ) {
	const [ r, g, b ] = hexToRgb( hex ).map( ( c ) => Math.pow( c / 255, 2.4 ) );
	return 0.2126729 * r + 0.7151522 * g + 0.0721750 * b;
}

function apcaLc( fgHex, bgHex ) {
	const fg = apcaY( fgHex );
	const bg = apcaY( bgHex );

	// Soft-clip both to avoid numerical noise at extremes.
	const softClip = ( y ) => ( y < 0.022 ? y + Math.pow( 0.022 - y, 1.414 ) : y );
	const yFg = softClip( fg );
	const yBg = softClip( bg );

	// Below the point where the algorithm loses precision.
	if ( Math.abs( yBg - yFg ) < 0.0005 ) return 0;

	let sapc;
	if ( yBg > yFg ) {
		// Normal polarity: dark text on light bg.
		sapc = ( Math.pow( yBg, 0.56 ) - Math.pow( yFg, 0.57 ) ) * 1.14;
		return ( sapc < 0.1 ? 0 : sapc - 0.027 ) * 100;
	}
	// Reverse polarity: light text on dark bg.
	sapc = ( Math.pow( yBg, 0.65 ) - Math.pow( yFg, 0.62 ) ) * 1.14;
	return ( sapc > -0.1 ? 0 : sapc + 0.027 ) * 100;
}

/* -----------------------------------------------------------------------------
 * Text/background pairs in actual use. Each pair records a human-readable
 * description, the required WCAG 2.1 minimum (AA), and the minimum APCA Lc
 * recommended for the text size category by the Bronze-Silver APCA guidance.
 *
 * WCAG 2.1 AA thresholds:
 *   - 4.5:1 for body text
 *   - 3:1 for large text (≥18pt / 14pt bold) and non-text UI components
 *
 * APCA guidance (Bronze conformance, informational only):
 *   - body text:     Lc ≥ 75 (use-case 5)
 *   - large text:    Lc ≥ 60 (use-case 4)
 *   - non-text UI:   Lc ≥ 45 (use-case 2)
 * ---------------------------------------------------------------------------*/
const PAIRS = [
	// Body text on base/subtle.
	{ role: 'Body text on Base',                 fg: 'contrast',      bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Body text on Subtle',               fg: 'contrast',      bg: 'subtle',        wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Muted text on Base',                fg: 'muted',         bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },

	// Links.
	{ role: 'Link (default) on Base',            fg: 'primary',       bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Link hover (primary-700) on Base',  fg: 'primary-700',   bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },

	// Buttons.
	{ role: 'Button text on Primary',            fg: 'base',          bg: 'primary',       wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Button text on Primary-700 hover',  fg: 'base',          bg: 'primary-700',   wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Ghost button (primary on base)',    fg: 'primary',       bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },

	// Ramp pairs the pattern catalog leans on.
	{ role: 'Gray-600 body on Base',             fg: 'gray-600',      bg: 'base',          wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Gray-600 body on Gray-50 panel',    fg: 'gray-600',      bg: 'gray-50',       wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Gray-900 heading on Primary-100',   fg: 'gray-900',      bg: 'primary-100',   wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'Primary-700 label on Primary-50',   fg: 'primary-700',   bg: 'primary-50',    wcagMin: 4.5, apcaMin: 75, kind: 'text' },
	{ role: 'White text on Primary-500 band',    fg: 'white',         bg: 'primary-500',   wcagMin: 3.0, apcaMin: 60, kind: 'text' },

	// Non-text UI — WCAG 2.1 1.4.11 requires 3:1.
	{ role: 'Border on Base (decorative)',       fg: 'outline',        bg: 'base',          wcagMin: 1.0, apcaMin: 0,  kind: 'ui-decorative' },
	{ role: 'Muted as stronger border on Base',  fg: 'muted',         bg: 'base',          wcagMin: 3.0, apcaMin: 45, kind: 'ui' },
	{ role: 'Focus ring (primary) on Base',      fg: 'primary',       bg: 'base',          wcagMin: 3.0, apcaMin: 45, kind: 'ui' },
];

/* -----------------------------------------------------------------------------
 * Evaluate and render.
 * ---------------------------------------------------------------------------*/
if ( ! existsSync( OUT_DIR ) ) {
	mkdirSync( OUT_DIR, { recursive: true } );
}

const rows = PAIRS.map( ( p ) => {
	const fgHex = resolved[ p.fg ];
	const bgHex = resolved[ p.bg ];
	const ratio = wcagRatio( fgHex, bgHex );
	const lc = apcaLc( fgHex, bgHex );
	const pass21 = ratio >= p.wcagMin;
	const passApca = Math.abs( lc ) >= p.apcaMin;
	return { ...p, fgHex, bgHex, ratio, lc, pass21, passApca };
} );

const failures = rows.filter( ( r ) => ! r.pass21 );

const md = [];
md.push( '# Phase 7 — contrast matrix' );
md.push( '' );
md.push(
	`_Generated ${ new Date().toISOString() } from theme.json. WCAG 2.1 AA: 4.5:1 body text · 3:1 large text and non-text UI. APCA (informational, Bronze guidance): Lc ≥ 75 body · Lc ≥ 60 large · Lc ≥ 45 non-text._`
);
md.push( '' );
md.push(
	'| Pair | FG | BG | WCAG 2.1 | Min | Pass | APCA Lc | Min | Pass |'
);
md.push(
	'| --- | --- | --- | ---: | ---: | :---: | ---: | ---: | :---: |'
);
for ( const r of rows ) {
	md.push(
		[
			`| ${ r.role }`,
			`\`${ r.fg }\` ${ r.fgHex }`,
			`\`${ r.bg }\` ${ r.bgHex }`,
			r.ratio.toFixed( 2 ) + ':1',
			r.wcagMin === 1 ? '—' : r.wcagMin + ':1',
			r.pass21 ? '✅' : '❌',
			r.lc.toFixed( 1 ),
			r.apcaMin || '—',
			r.apcaMin === 0 ? '—' : r.passApca ? '✅' : '⚠️',
		].join( ' | ' ) + ' |'
	);
}
md.push( '' );

if ( failures.length === 0 ) {
	md.push( '**No WCAG 2.1 AA failures.** APCA warnings are informational — APCA thresholds are tighter than WCAG 2.1 in some regions and looser in others; they will become relevant when WCAG 3.0 (Silver) ships.' );
} else {
	md.push( `## WCAG 2.1 AA failures (${ failures.length })` );
	md.push( '' );
	for ( const f of failures ) {
		md.push(
			`- **${ f.role }** — \`${ f.fg }\` (${ f.fgHex }) on \`${ f.bg }\` (${ f.bgHex }) has ratio ${ f.ratio.toFixed( 2 ) }:1, below required ${ f.wcagMin }:1.`
		);
	}
}

writeFileSync( resolve( OUT_DIR, 'contrast.md' ), md.join( '\n' ) + '\n' );

console.log(
	`Wrote docs/accessibility/contrast.md — ${ rows.length } pairs, ${ failures.length } WCAG 2.1 AA failure${ failures.length === 1 ? '' : 's' }.`
);

/**
 * Color-ramp generator + fluid-preset accessibility lint.
 *
 * Reads assets/design-tokens/ramps.json and emits assets/css/color-ramps.css:
 *
 *   1. CSS custom properties in WordPress's preset namespace
 *      (--wp--preset--color--<family>-<step>) declared on `body`, the same
 *      element WordPress attaches its own palette vars to. Derived families
 *      (primary / secondary / accent) are color-mix(in oklab, …) expressions
 *      over the palette role var, so a Global Styles edit to the role
 *      re-tints the whole ramp live. Fixed families (gray, status, hue
 *      accents) are literals shared by every brand.
 *
 *   2. The `.has-<slug>-color` / `-background-color` / `-border-color`
 *      utility classes WordPress would generate had these been palette
 *      entries. They are deliberately NOT palette entries — the picker stays
 *      at the 8 brand roles; the ramps exist for pattern markup and
 *      block-style CSS. `!important` matches core's own preset classes.
 *
 * The mix percentages in ramps.json are least-squares fits against the
 * Untitled UI reference ramp computed in OKLab (max deltaE-OK ≈ 2.8 as
 * fitted, against the placeholder purple seed of the day). The seeds are
 * read from theme.json's palette at build time and deliberately not repeated
 * here — a hex in this comment only goes stale on the next rebrand. CSS
 * `color-mix(in oklab, seed, white P%)` is the same interpolation browsers
 * use, so the derived steps track whatever seed the palette carries.
 *
 * Also enforces the theme's fluid-preset accessibility rule on theme.json:
 * every fontSizes/spacingSizes preset with a min→max range must satisfy
 * max ≤ 2.5 × min (WCAG 1.4.4 — text must survive 200% resize; vw-heavy
 * clamps with wider ranges stop tracking browser zoom), and hand-authored
 * clamp() middle terms must contain a rem component, never bare vw.
 *
 * Usage:  node tools/build-color-ramps.mjs        # generate + lint
 *         node tools/build-color-ramps.mjs --lint # lint only, no write
 */

import { readFileSync, writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const THEME_ROOT = resolve( __dirname, '..' );

const ramps = JSON.parse(
	readFileSync( resolve( THEME_ROOT, 'assets/design-tokens/ramps.json' ), 'utf8' )
);
const themeJson = JSON.parse(
	readFileSync( resolve( THEME_ROOT, 'theme.json' ), 'utf8' )
);

/* -----------------------------------------------------------------------------
 * Fluid lint — fails the build on a WCAG 1.4.4 hazard.
 * ---------------------------------------------------------------------------*/

const REM = ( v ) => {
	const m = /^([\d.]+)(rem|px)$/.exec( String( v ).trim() );
	if ( ! m ) return null;
	return m[ 2 ] === 'px' ? parseFloat( m[ 1 ] ) / 16 : parseFloat( m[ 1 ] );
};

const failures = [];

for ( const preset of themeJson.settings?.typography?.fontSizes ?? [] ) {
	const fluid = preset.fluid;
	if ( ! fluid || fluid === false ) continue;
	const min = REM( fluid.min );
	const max = REM( fluid.max ?? preset.size );
	if ( min == null || max == null ) {
		failures.push( `fontSize "${ preset.slug }": non-rem/px fluid bounds` );
		continue;
	}
	if ( max > min * 2.5 ) {
		failures.push(
			`fontSize "${ preset.slug }": max ${ max }rem > 2.5 × min ${ min }rem`
		);
	}
}

for ( const preset of themeJson.settings?.spacing?.spacingSizes ?? [] ) {
	const size = String( preset.size );
	const clamp = /^clamp\(\s*([^,]+),\s*([^,]+),\s*([^)]+)\)$/.exec( size );
	if ( ! clamp ) continue;
	const [ , minRaw, mid, maxRaw ] = clamp;
	const min = REM( minRaw );
	const max = REM( maxRaw );
	if ( min != null && max != null && max > min * 2.5 ) {
		failures.push(
			`spacing "${ preset.slug }": max ${ max }rem > 2.5 × min ${ min }rem`
		);
	}
	if ( /vw/.test( mid ) && ! /rem/.test( mid ) ) {
		failures.push(
			`spacing "${ preset.slug }": clamp middle term "${ mid.trim() }" is bare vw — needs a rem component`
		);
	}
}

if ( failures.length ) {
	console.error( 'Fluid-preset lint FAILED:' );
	for ( const f of failures ) console.error( `  ✖ ${ f }` );
	process.exit( 1 );
}
console.log( '✓ Fluid-preset lint passed (max ≤ 2.5 × min, rem-based middles)' );

if ( process.argv.includes( '--lint' ) ) process.exit( 0 );

/* -----------------------------------------------------------------------------
 * Generation.
 * ---------------------------------------------------------------------------*/

const vars = [];
const classes = [];

const emit = ( slug, value ) => {
	vars.push( `\t--wp--preset--color--${ slug }: ${ value };` );
	classes.push(
		`.has-${ slug }-color { color: var(--wp--preset--color--${ slug }) !important; }`,
		`.has-${ slug }-background-color { background-color: var(--wp--preset--color--${ slug }) !important; }`,
		`.has-${ slug }-border-color { border-color: var(--wp--preset--color--${ slug }) !important; }`
	);
};

for ( const [ family, steps ] of Object.entries( ramps.fixedFamilies ) ) {
	for ( const [ step, hex ] of Object.entries( steps ) ) {
		emit( step === '' ? family : `${ family }-${ step }`, hex );
	}
}

for ( const [ family, { seedVar } ] of Object.entries( ramps.derivedFamilies ) ) {
	for ( const [ step, { mix, pct } ] of Object.entries( ramps.derivedSteps ) ) {
		const slug = `${ family }-${ step }`;
		if ( ! mix || pct === 0 ) {
			emit( slug, `var(${ seedVar })` );
		} else {
			emit(
				slug,
				`color-mix(in oklab, var(${ seedVar }), ${ mix } ${ pct }%)`
			);
		}
	}
}

const banner = `/**
 * GENERATED FILE — do not edit by hand.
 * Source: assets/design-tokens/ramps.json
 * Build:  node tools/build-color-ramps.mjs
 *
 * The theme's under-the-hood color ramps: fixed neutrals/status literals and
 * brand ramps derived live from the palette roles via color-mix(). See the
 * generator header for the full design rationale.
 */
`;

const css = `${ banner }
body {
${ vars.join( '\n' ) }
}

${ classes.join( '\n' ) }
`;

writeFileSync( resolve( THEME_ROOT, 'assets/css/color-ramps.css' ), css );
console.log(
	`✓ Wrote assets/css/color-ramps.css (${ vars.length } vars, ${ classes.length } class rules)`
);

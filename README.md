# Blocklane

A versatile, production-grade WordPress Full Site Editing block theme built on core blocks and a semantic design token system. WCAG 2.1 Level AA compliant.

## Developer setup

The theme itself has **no build step** — templates, parts, patterns, and
`theme.json` ship as authored. Node.js is only needed for the accessibility
tooling:

```sh
cd blocklane-theme
npm install          # one-time — installs axe-core + Playwright (dev tools only)
npm run a11y:scan    # axe-core scan across every route → docs/accessibility/
npm run a11y:contrast# recompute the WCAG/APCA contrast matrix from theme.json
npm run screenshot   # capture screenshot.png from a running site
```

Breadcrumbs use the native **`core/breadcrumbs`** block (added in WordPress 7.0);
templates reference it directly, so no companion plugin is needed. Earlier
versions shipped a `blocklane/breadcrumbs` block via the Blocklane Blocks plugin
— that was retired once core shipped an equivalent (which is why the theme now
requires WP 7.0).

## Structure

```
blocklane/
├── theme.json                 Design tokens (single source of truth)
├── style.css                  Theme header (metadata only)
├── functions.php              Theme bootstrap
├── inc/                       Setup: assets, block styles, pattern categories, site config
├── templates/                 Block theme templates (HTML)
├── parts/                     Template parts (thin wrappers around hidden patterns)
├── patterns/                  PHP patterns — all user-facing strings live here (translatable)
├── assets/css/                Hand-authored stylesheets (editor chrome, font fallbacks, print)
├── docs/                      Accessibility audit + design docs
└── tools/                     a11y scan / contrast matrix / screenshot scripts
```

Template parts and string-bearing template sections are one-line `wp:pattern`
references into `patterns/hidden-*.php`: static template HTML can't pass
through gettext, PHP patterns can. Add or change user-facing copy in the
pattern files, not the templates.

## Fonts

The default stacks are **system fonts** (`system-ui` sans, `ui-serif` serif,
`ui-monospace` mono): zero font bytes, zero layout shift, zero external
requests — GDPR-safe by construction.

Three optional families ship self-hosted (`assets/fonts/`, OFL-licensed
latin-subset variable builds, declared via theme.json `fontFace`):

- **Inter** — sans (weight 100–900 variable, + italic)
- **Lora** — serif (weight 400–700 variable, + italic)
- **JetBrains Mono** — mono (weight 100–800 variable)

A family's woff2 downloads only when content actually uses it — browsers
lazy-load `@font-face` sources — so registered-but-unused presets cost
nothing. No CDN is involved.

## About this repository

This is a mirror of the theme's source, published from the Blocklane monorepo by `bin/publish-theme-source.sh` for every release; one commit per version, tagged. Pull requests are welcome as reports, but changes land in the monorepo.

The built script `assets/js/mega-menu-fallback/index.js` (the editor fallback for the mega menu, compiled with `@wordpress/scripts`) is shipped here as built: its source imports the Blocklane Pro plugin's menu-designer block definitions, which are not published, so it cannot be built from this repository alone.

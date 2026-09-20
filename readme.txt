=== Blocklane ===
Contributors: garrettjohnson
Tags: accessibility-ready, block-styles, blog, custom-logo, editor-style, featured-images, full-site-editing, rtl-language-support, threaded-comments, translation-ready, wide-blocks
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 0.10.3
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A versatile, production-grade WordPress Full Site Editing block theme built on core blocks and a semantic two-tier design token system. WCAG 2.1 Level AA compliant.

== Description ==

Blocklane is a full site editing theme for people who want a real design system without a page builder. Every visual decision lives in `theme.json`. Every template uses only core blocks. Every state / surface / elevation is a named token, so a designer can retheme the whole site by editing one file.

Blocklane is core-only by choice:

* No page builders. No ACF. No third-party block libraries.
* System fonts by default — zero font bytes, zero layout shift, no external requests, GDPR-safe by construction. Inter, Lora, and JetBrains Mono ship self-hosted as optional presets and download only when content actually uses them.
* A compact brand palette of eight named roles (`base`, `contrast`, `subtle`, `muted`, `outline`, `primary`, `secondary`, `accent`) — and under the hood, full 25–900 color ramps derived live from the brand roles via `color-mix()`, so setting three brand colors re-tints the entire design system, patterns included.
* A fluid two-track type scale (core-standard `small`–`x-large` body sizes plus `heading-small`–`heading-xxx-large` display sizes), a ten-step fluid spacing scale on numeric core-convention slugs (`10`–`100`), a five-step border-radius preset scale, and seven shadow presets — all named tokens in `theme.json`.
* WCAG 2.1 Level AA out of the box, forward-compatible with WCAG 2.2. The audit, tooling, and contrast matrix live in `docs/accessibility.md`.
* Editor styles keyed to the same tokens so the block editor previews match production rendering.
* Print stylesheet with ink-economical layout, automatic URL expansion for external prose links, heading/figure/table break discipline, and Lora serif body for long-form paper reading.

Default templates render breadcrumbs with the native `core/breadcrumbs` block (WordPress 7.0+) on page, single, archive, search, and 404 templates — no custom block and no companion plugin required.

== Installation ==

1. In your WordPress admin, go to **Appearance → Themes → Add New → Upload Theme**.
2. Select the Blocklane zip and click **Install Now**.
3. Click **Activate**.
4. Go to **Appearance → Editor** to customize templates, template parts, and styles.

No extra setup is required. The theme makes no external font or CDN requests: default typography is the visitor's system font stack, and the optional bundled typefaces are served from the theme itself.

== Frequently Asked Questions ==

= Does this theme work with WooCommerce, forms plugins, or page builders? =

WooCommerce and form plugins — yes. The theme follows core conventions and does not interfere with plugin styles. Page builders — you don't need one, and the theme is not designed around them.

= Can I change the color palette? =

Yes. All colors live in `theme.json` under `settings.color.palette` as eight named roles (`base`, `contrast`, `subtle`, `muted`, `outline`, `primary`, `secondary`, `accent`). Edit `primary` (or any role) and the derived 25–900 ramps — and with them every pattern, block style, and template — re-tint automatically. Re-run `npm run a11y:contrast` afterwards to confirm the pairings still clear WCAG AA.

= Can I change the typefaces? =

Yes. The defaults are system stacks, and Inter / Lora / JetBrains Mono ship as ready-made presets. To add your own brand font, use the Site Editor's Font Library (Styles → Typography), or add a `fontFace` entry under `settings.typography.fontFamilies` in `theme.json` pointing at a self-hosted woff2 in `assets/fonts/`.

= Is this theme accessible? =

It aims to be. See `docs/accessibility.md` for the full audit: axe-core scans on every route, a 12-pair contrast matrix with WCAG 2.1 and APCA ratings, landmark / heading review, and a manual-test checklist for things automation can't cover (screen reader narration, reduced motion, 400% zoom).

= Does it come with demo content? =

No. A stub fixture post lives in `tools/fixture-post.html` for accessibility testing only. It is not imported automatically.

== Changelog ==

= 0.10.3 =
* Maintenance: the Author URI now points at the author's wordpress.org profile; the previous domain is not deployed. Nothing in the shipped theme's templates, styles or patterns changed.

= 0.10.2 =
* Maintenance: the theme's packaging script now names the theme by its own key in the release gate, which the free edition of Blocklane Pro shares the gate with. Nothing in the shipped theme changed.

= 0.10.1 =
* Maintenance: updated a development dependency (adm-zip 0.6.1, a security fix in the build toolchain). Nothing in the shipped theme changed.

= 0.10.0 =
* Mobile menu: when the overlay opens, the header keeps its own logo on screen instead of drawing a second copy inside the overlay, and the hamburger twists into the close icon in place. The Navigation Overlay part now paints a header band with the header's own padding, height and bottom border, so the close control lands exactly where the hamburger is; logged-in viewers get the same admin-bar offset core gives its default overlay; and the overlay fades in without core's upward slide, so the band stays put under the logo. Works for sticky and static headers alike: a small script lines the overlay up with wherever the header sits when it opens, admin bar included, and pulls a partly scrolled static header back into view so the logo is whole. A nav with text buttons instead of icons crossfades; motion follows the reduced-motion preference. The header's site title stays on screen with the logo. Each navigation's hamburger answers only to its own overlay, so a second navigation in the header is untouched. Sites that customized the Navigation Overlay part before 0.10.0 keep their copy: no lift, no twist, no alignment script — only the overlay's reveal (a fade instead of core's upward slide) and the logged-in admin-bar offset apply to every custom overlay; rebuild it from the theme's copy to pick up the band.

= 0.9.0 =
* Wireframe Mega Tiles: the three collection tiles now size to the space the panel gives them — three across when there is room, otherwise two or one — instead of a fixed three-column grid that squeezed each tile to about 96 pixels at content width and broke its label one letter per line.
* Requires PHP 8.1, matching Blocklane Pro and Blocklane Patterns.
* The mega menu's no-JavaScript fallback script is now built from its source when the theme is packaged, instead of shipping as a prebuilt file checked in beside it, so the copy a site receives always matches the source it was built from.
* Ships a translation template (`languages/blocklane.pot`) covering the theme's labels, block styles and pattern titles, so the translation-ready tag is backed by a file translators can start from. Every release regenerates it and refuses to build when a string was missed.
* New **Vertical Reversed** style for the Tabs block: the vertical rail mirrored, with the media in the wide left track and the tab list on the right. Both vertical styles reveal a tab's supporting text (from Blocklane Pro's Advanced Tabs) only on the selected tab, and the selected state is rendered server-side, so nothing flashes on first paint.
* Tab panels show their own titles in the List View instead of a column of rows all reading "Tab Panel" (a backport of Gutenberg #81427 that steps aside once core ships the label).
* The theme's tab styles and Blocklane Pro's Advanced Tabs no longer compete: the theme hands its reveal and rail placement to Pro only when Pro is actually present, and every theme rule that hides tab content is guarded the same way, so a theme-only site keeps working tabs.
* Brand palette rebuilt on a monochrome-family architecture: one hero hue carries the identity across four depths, with a single outside hue for energy. `primary` #1a58e4 sits deliberately a hair off the WordPress brand blue — same family at a glance, its own value on inspection, and a touch deeper and less indigo; `secondary` #0b3083 is the same hue at depth for dark sections and headings; `contrast` #051438 is a near-black tinted with that hue rather than a neutral black; `subtle` #f2f5fa is a brand-tinted ground rather than a gray one. `accent` #006646 is the one hue from outside the family. `base`, `muted`, and `outline` are unchanged, and the neutral gray ramp stays neutral by design — a branded ink and a neutral UI gray are different jobs.
* Every brand role now clears WCAG AA in both directions on both light grounds — as text, and under white text — with no role below 5.40:1: `primary` 5.90:1, `secondary` 11.90:1, `accent` 7.03:1, `contrast` 18.05:1 against Base. The regenerated contrast matrix passes all 16 pairs on APCA as well as WCAG 2.1, so the block editor's contrast checker cannot flag a brand-role pairing.
* New `lime` ramp family (`lime-50/300/500/700`) for the high-energy highlight on dark brand sections: `lime-300` #d5ff45 reads 15.66:1 on `contrast`. It is deliberately a dark-surface token (1.15:1 on white) — use `lime-700` #586d00 when a lime must sit on a light ground. Like every ramp family it ships as a CSS variable and `has-*` classes, not a palette entry, so the color picker stays at eight roles.
* The derived ramps mean the palette change re-tints the whole system on its own: block styles, all seven gradient presets, and the pattern catalog follow the roles with no per-file edits.
* The Emerald style variation is removed. It existed to demonstrate a three-color rebrand away from the placeholder purple; with the brand ratified it had no job left, and the theme now ships no style variations at all — recolor through the brand roles in Global Styles and the ramps follow. A site that had activated Emerald keeps the colors it chose: activating a variation copies its payload into that site's own Global Styles, so removing the file takes nothing away.
* New Badge style for the Post Terms block: categories and tags render as small filled pills rather than a run of plain text, which is what most post headers and blog cards actually want. Pick it from the Styles panel like any other block style.
* New Timeline Step and Timeline Rail styles for Group blocks. They carry the connecting line and node geometry the timeline patterns need, and they are what makes a timeline rotate from a horizontal rail on a wide screen to a vertical one on a phone. They are structural rather than decorative, so they stay out of the style picker — the timeline patterns apply them for you.
* Header and footer patterns are now findable by search. Every one carries a description of what it is and when to reach for it, plus search keywords — so typing "announcement", "sitemap" or "cart" in the pattern inserter surfaces the right one. Previously they could only be found by their exact title and their description panel was empty.

= 0.8.0 =
* Template parts: a roster of seven shipped `menu`-area wireframe mega panels (`parts/wireframe-mega-*.html` — columns, featured article, four-group grid, articles, products, promo, collection tiles) so the wireframe library's mega-menu navbars bind to working panels out of the box (Pro's Mega Menu block renders nothing without a bound part).
* UUI Vertical Tabs no longer overrides the editor's tab-list justification or spacing: the style owns only the vertical flip and the rail visuals (core locks tab-list orientation, so the flip must live in the style), and the editor's justification classes are translated into the column axis so the toolbar control keeps working.
* Block styles: generic UUI Marquee, Marquee Reverse, and Marquee Vertical group styles (the logo-marquee mechanics opened to any doubled media track — reduced-motion safe, odd wall columns auto-stagger) plus UUI Wall Hero (first child group becomes a full-bleed scrimmed backdrop). The wireframe library's motion rows ride these.
* Pricing Card section style (WP 6.6 mechanism, first use in the theme): one style picked on the card's wrapper group styles the blocks inside by cascade — surface/border/shadow/padding on the group, muted flush feature list with circled check markers, body-scale price suffix on any `sub` — so pricing-only looks no longer need per-block styles cluttering every paragraph/list Styles panel site-wide. Pattern cards keep the same chrome inline (theme-agnostic) and the style complements it.
* Block styles de-UUI'd (clean break, no aliases — the pattern catalog republishes in lockstep): `secondary`, `tertiary`, `full-width` (buttons), `checks` (list), `badge` (paragraph), `segmented` + `vertical` (tabs), `card` (accordion), `marquee`/`marquee-reverse`/`marquee-vertical` + `wall-hero` (groups). Deleted: `uui-link` (ghost is the one link-form button), `uui-price` (a silent no-op without a `<sub>` — the pricing-card cascade owns the suffix now), `uui-logo-marquee` (merged into `marquee`). The structural marquee/wall-hero styles stay registered for pattern CSS but leave the style picker — they do nothing on an arbitrary group, and a style tile that does nothing when clicked is a bug.
* Brand: the placeholder Untitled-UI purple/blue/pink brand roles are replaced by the ratified Blocklane brand — electric blue `primary` #2145e0, azure `secondary` #0675c4, and green `accent` #15803d. All three clear WCAG AA in both directions (as text on light surfaces and under white text: 7.02:1 / 4.83:1 / 5.02:1 against white), so the editor's contrast checker never flags a brand-role combination; the variable-driven gradient presets re-tint automatically (Brand Sweep is now blue→green).
* WordPress 7.1: the theme registers the `blocklane-pro` icon collection with core's Icons API — the bundled Phosphor set (lazy, by file path) plus any custom icons — so placed icons keep rendering even with Blocklane Pro deactivated.
* theme.json gains `settings.viewport` (tablet 768px, mobile 480px): core's responsive styles, block visibility, and Blocklane Pro's responsive extras all share the theme's breakpoints.
* Content still referencing tokens this theme retired is detected, not silently accepted: one scan covers every form the retired "Border" color serializes as (text, background, and border-color attributes, per-side and element references, the raw CSS variable, and your Site Editor → Styles customizations), a second covers content carrying the renamed UUI block styles. Admin notices name the affected content so you can re-pick — nothing is rewritten automatically, content with ordinary custom border colors is never flagged, a re-defined "border" palette color counts as healthy, and an interrupted check stays visible and retries instead of passing as clean.
* The theme registers the "Menu" template-part area itself, so its mega-menu panel parts group correctly in the Site Editor without Blocklane Pro (which registers the same area and defers when one exists).

= 0.7.0 =
* The default design system is now the widely-adopted baseline across the board: system fonts, a brand-role palette, core-convention preset slugs, fluid type and spacing, Untitled-UI/Tailwind shadow tiers, and 48rem/80rem layout widths.
* Color: the palette is eight brand roles (`base`, `contrast`, `subtle`, `muted`, `outline`, `primary`, `secondary`, `accent`); full 25–900 gray/status ramps plus primary/secondary/accent ramps derived live from the roles via `color-mix()` ship in a generated stylesheet (`assets/css/color-ramps.css`), so a three-color brand edit re-tints the whole system — pattern catalog included. `primary-hover` retired in favor of the derived `primary-700`.
* Typography: system font stacks by default; Inter, Lora, and JetBrains Mono now self-hosted optional presets (no Bunny CDN, no external requests, Font Library compatible). Two-track fluid scale on core-standard slugs: `small`–`x-large` body sizes + `heading-small`–`heading-xxx-large` display sizes; headings 600 weight, −0.02em tracking on h1–h3, uniform 1.2 line-height; body 1rem/1.5.
* Spacing: ten-step 4px-grid scale on numeric core-convention slugs (`10`–`100`, Medium = `50` = 2rem), fluid clamp() on the upper half. Every fluid preset is linted to max ≤ 2.5× min with rem-based middle terms (WCAG 1.4.4).
* Shadows: seven presets (`xx-small`–`xx-large`). Border radius: five WordPress 6.9 native presets (`none`/`sm`/`md`/`lg`/`full`); buttons default to the 8px `md` radius instead of square.
* Layout: `contentSize` 48rem / `wideSize` 80rem.
* The Untitled Mirror style variation is retired — its system now IS the theme default; a slim Emerald variation demonstrates the three-color rebrand story.
* Fixed: a duplicate array key in the block-style registrations silently discarded four button styles (UUI Secondary Gray, UUI Tertiary Gray, UUI Link, Ghost); hardcoded white/shadow literals in the billing-tabs CSS now ride tokens.
* Accessibility: contrast matrix regenerated for the new defaults (16 pairs, 0 WCAG 2.1 AA failures).

= 0.6.4 =
* New UUI Vertical Tabs style for the Tabs block: tab list and panes sit side by side, with left-bordered entries and a primary accent on the active tab — the treatment behind the pattern catalog's locations sections.

= 0.6.3 =
* Untitled Mirror spacing presets are namespaced (uui-tiny through uui-xhuge) so they can never collide with a theme's own same-named slugs — pattern fallbacks now always apply at baseline and the exact system values apply under the variation.

= 0.6.2 =
* New UUI Card accordion style: rounded items that show the gray-50 surface while expanded — the card-style FAQ look, state-styled via CSS since block attributes cannot express open-state.

= 0.6.1 =
* Untitled Mirror gains the heading-tiny (1.125rem) type size the first extraction missed — the FAQ family's question headings use it.

= 0.6.0 =
* New "Untitled Mirror" style variation: a complete design-system port for the pattern clone program — 68-color palette (gray and primary ramps 25-900 plus semantic and accent hues), a ten-size type scale, ten spacing steps, seven shadow presets, 48rem/80rem content widths, and Inter-first typography with matching heading and button element styling.
* New UUI component block styles referencing that variation's presets: Secondary Gray, Tertiary Gray and Link button styles, and a Badge paragraph pill. They render plain when the variation is not active.

= 0.5.0 =
* Six gradient presets join the palette — Brand Sweep, Deep Brand, Soft Wash, Subtle Rise, Primary Glow and Accent Glow. Each is built from the palette roles themselves, so editing your brand colors re-colors every gradient (and everything using one) automatically.
* New pattern categories for the cloud pattern library: "Heroes" and "Heroes — Enhanced (Pro)", registered by the theme so downloaded patterns keep their grouping even if the Patterns plugin is removed.
* New "Landing" page template: header and footer with a full-width, title-free content canvas — built for hero patterns, which carry their own headings.
* The Outline button style now sizes identically to the filled button: it uses the theme's own button padding (minus its border) instead of core's hard-coded padding.
* The table "Stripes" style now colors its stripes with the theme's `subtle` token (and its bottom border with `border`) instead of core's hard-coded gray — palettes and child themes inherit automatically.
* Core's "Rounded" image style is removed from the style picker: it hard-coded a 9999px radius (images already default to the medium radius, and the border-radius control covers everything else).
* New "Plain" style for the Page List block: drops the bullet markers and the browser's indent, keeping a smaller indent on child pages so the hierarchy still reads — built for footer sitemaps, where core leaves a standalone page list unstyled. Page List links also now follow the theme's navigation convention — no underline at rest, underlined on hover and keyboard focus — instead of inheriting the browser's default underline on every item.
* New "Boxed" style for the Details block: a bordered, padded disclosure row whose native triangle marker becomes a right-aligned + that flips to − while open — built for FAQs.
* The block-style roster is deliberately small: the styles above plus Ghost buttons, the Card / Card (Flat) / Scroll Fade groups and the Wide separator — only styles the theme's own templates, patterns and site content actually use. Unused image, quote, list, separator and excerpt variations from earlier releases are removed; future style variations arrive alongside the patterns that need them.
* Block styles now preview correctly in the editor. Variations whose styling lives in a stylesheet — the Boxed details marker, Scroll Fade, the Plain page list and striped tables — were rendering correctly on the front end but showed as unstyled while editing.

= 0.4.1 =
* The mega menu stays coherent without Blocklane Pro: the theme now registers a faithful read-only stand-in for saved mega menus in the editor (sharing Pro's exact markup, enqueued only while Pro is inactive), so templates render instead of showing an invalid block. Pro's new "Convert to core Submenu" control pairs with it as the permanent exit.
* New "Header (solid)" template part variant, so individual templates can opt out of a transparent header.
* New core Group block styles: a flat card and a scroll-linked fade; the fade-children animation now completes within a fixed screen height instead of stretching across the whole page.

= 0.4.0 =
* Translation-ready for real: every user-facing string moved out of static template HTML into hidden PHP patterns where it passes through gettext. The footer copyright year is now dynamic, and the 404/nav links resolve via `home_url()` so subdirectory installs work. The theme deliberately ships no inserter patterns yet (the `block-patterns` tag is removed until a designed pattern set lands); the hidden patterns are template infrastructure only.
* Remove `inc/patterns.php` entirely: the 11 empty dev-mode category registrations and the category sweep that unregistered every non-theme pattern category (it also stripped categories registered by active plugins). The theme currently defines no pattern categories.
* Replace the deprecated `core/post-author` block with `core/avatar` + `core/post-author-name` (`isLink`), so the author byline links to the author archive.
* Sidebar headings drop from h3 to h2: with comments closed and none existing (the theme's own activation default), core renders the comments block — and its h2 — empty, so the h3s skipped a heading level.
* Quote styling moves to logical properties (`border-inline-start` / `padding-inline-start`, incl. the print stylesheet) so the accent rail sits on the leading edge in RTL locales.
* Activation defaults now apply once per site instead of clobbering Settings → Discussion on every re-activation.
* Fix stale docs: readme Description/FAQ updated to the actual 0.3.0+ token architecture (8 named color roles, 7 spacing steps, 4 radii, 2 shadows, 12-pair contrast matrix); `Stable tag` synced.
* Housekeeping: drop a vestigial legacy comments class, avatar radius uses the `full` token, `theme.json` `$schema` pinned to `wp/6.9`.
* Breadcrumbs switch to the native `core/breadcrumbs` block (new in WordPress 7.0). The `page`, `single`, `archive`, `search`, and `404` templates were migrated off the old `blocklane/breadcrumbs` block, which is retired — the Blocklane Blocks companion plugin is no longer needed for breadcrumbs.
* Ship a themed Navigation Overlay template part (`parts/navigation-overlay.html`, registered in the new WordPress 7.0 `navigation-overlay` area): base background, contrast text, site logo + close button row, and a large vertical menu — so assigning an overlay on the Navigation block looks designed out of the box instead of core's unstyled default.
* Raise `Requires at least` to WP 7.0, which is where `core/breadcrumbs` lands.

= 0.3.0 =
* Consolidate custom page templates from six to two: `Full Width` and `With Sidebar`. `Sidebar Left` and `Sidebar Right` merge into a single `With Sidebar` template that defaults to a right-hand sidebar; edit the template in the Site Editor to move the sidebar column. `Blank (no header/footer)`, `No Header`, and `Landing Page` removed — all three were thin wrappers around `post-content` with no real-world differentiation from a standard page plus a `Full Width` option.
* Remove the `breadcrumbs` template part. Templates now reference the `blocklane/breadcrumbs` block directly, removing one layer of indirection. Sites that customized the template part override will need to re-apply styling to the block in the companion plugin. The no-plugin fallback still produces empty output with no broken markup.
* Register a dedicated `Sidebar` template-part area via the `default_wp_template_part_areas` filter so the sidebar part appears in its own group in the Site Editor template-part picker instead of the generic `General` bucket.
* Sync `BLOCKLANE_VERSION` constant with `style.css` / `readme.txt` version (was stale at 0.2.1 after the initial 0.3.0 bump).
* Tighten header: padding drops from `spacing|5` (24px) to `spacing|4` (16px), site-logo width from 48 to 40, site-title font-size from `xl` to `lg`. Net result: header collapses from ~186px to ~61px without feeling cramped.
* Remove the global `core/group` default padding and border-radius from `theme.json`. Every nested group in templates and user content was silently inheriting 32px of padding and `md` corner radius, which exploded header layout and forced every template to override. The Card and Card (Elevated) block styles now apply their own padding + radius inline, so cards still look the same; plain groups become neutral layout containers as intended.
* On theme activation, flip site-wide discussion defaults off: `default_comment_status` → `closed` (no comment form on new posts), `default_ping_status` → `closed` (no pingbacks/trackbacks), `default_pingback_flag` → `0` (don't ping other sites on outbound links). Users can re-enable globally in Settings → Discussion or per-post in the editor. Existing posts are not touched.
* Rename font-size presets whose slugs started with a digit (`2xl`, `3xl`, `4xl`, `5xl` → `xxl`, `xxxl`, `huge`, `hero`) and the matching `borderRadius` slug (`2xl` → `xxl`). Numeric-prefixed slugs produced empty CSS custom properties in WordPress's generated global stylesheet, so every `h1`, `h2`, `h3` — plus any heading block using those presets — silently fell back to body font-size. After the rename, the heading hierarchy resolves correctly (`h1` ≈ 67px at 1280px viewport, cascading down by a consistent 1.333× step at max viewport).
* Fix editor vs front-end post-title mismatch: `.editor-post-title__input` (WordPress's title field at the top of the block-editor canvas) doesn't inherit the `core/post-title` block styles from `theme.json`, so it rendered at body size (~18px) while the production page shows ~67px. Mirror the front-end sizing in `editor-style.css` so authors see the title the way readers will.
* Convert font-size presets from hand-rolled `clamp()` strings to WordPress's native fluid typography (`fluid: true` plus `fluid: { min, max }` on each preset). Same min/max values, same computed sizes at both endpoints — but WP now interpolates against the theme's `layout.wideSize` (1200px) instead of our previously hardcoded 80rem (1280px) viewport ceiling, and the `size` field is the non-fluid fallback. Diffs and tweaks become dramatically easier to read.
* Remove three unused duotone presets (`primary-inverse`, `accent-inverse`, `neutral-warm`) and four unused gradient presets (`primary-to-accent`, `surface-to-raised`, `inverse-fade`, `primary-subtle`) from `theme.json`. They were referenced nowhere in templates, patterns, or styles and only cluttered the block-editor color pickers. `defaultDuotone: false` and `defaultGradients: false` remain set, so the pickers stay empty by design; `customDuotone: true` / `customGradient: true` still let authors roll their own on demand.
* Migrate block style variations from `register_block_style()` CSS-string `inline_style` arguments to WordPress's native `style_data` arrays (WP 6.6+). Five of the seven (`ghost`, `rounded-lg`, `card`, `card-elevated`, `plain`) are now fully declarative in theme-token form. The two exceptions (`circle` needs `aspect-ratio`/`object-fit` on the inner `<img>`; `wide` separator needs `width: 100%`) keep a small `inline_style` escape for properties that do not map to any theme.json style path.
* Drop the `max-width: 65ch` rule from `core/paragraph` in `theme.json`. The 65ch cap (~738px at our md font metrics) was narrower than `contentSize` (760px), so the constrained layout auto-centered the paragraph inside its parent, producing a visible 10px indent relative to headings and lists on the front-end. Removing the cap re-aligns prose with the rest of the column while staying inside WCAG 1.4.8's 80ch AAA ceiling.
* Flip `appearanceTools` from `false` to `true` and drop the individual setting flags it now enables (`background.backgroundImage`/`backgroundSize`, `border.color`/`radius`/`style`/`width`, `color.link`/`heading`/`button`/`caption`, `dimensions.aspectRatio`/`minHeight`, `position.sticky`, `spacing.blockGap`/`margin`/`padding`, `typography.lineHeight`). The editor gains no new tools and loses none — this is purely a readability cleanup, trading 17 redundant toggles for WordPress's native umbrella switch.
* Rewrite the color palette in brand-guide vocabulary with 8 entries: `base`, `contrast`, `subtle`, `muted`, `border`, `primary`, `primary-hover`, `accent`. The previous `surface`/`surface-raised`/`on-surface-*` naming was CSS-design-system jargon; the new names match the language every brand guideline already uses (brand white → `base`, brand body-text → `contrast`, brand primary → `primary`, etc.). An agency/freelancer now maps at most five hex values from the brand guide and everything else is derived. Consolidations: `surface`/`surface-raised`/`surface-sunken` → `base`/`subtle`/`subtle`; `on-surface`/`on-surface-muted` → `contrast`/`muted`; `on-primary`/`on-accent` → `base` (button text just uses the white token directly); `border-strong` → `muted`; `accent-hover` dropped (use `primary-hover` for both hover states or define a custom color). All templates, parts, styles, block style variations, editor CSS, and the contrast-matrix tool were migrated to the new slugs; `docs/accessibility/contrast.md` regenerated — 12 pairs, 0 WCAG 2.1 AA failures.

= 0.2.1 =
* Extract the custom `blocklane/breadcrumbs` block into a separate companion plugin (Blocklane Blocks). WordPress.org theme directory policy does not permit custom blocks in themes, so the block moved out to keep the theme submission-ready. Existing sites should install Blocklane Blocks to continue rendering breadcrumbs; the theme's templates already reference the block, so activation is enough.

= 0.2.0 =
* Add print stylesheet: Lora body on paper, hides interactive chrome (navigation, search, forms, comments, pagination), expands external link URLs in prose for printed context, enforces break-inside / break-after discipline on headings, figures, tables, and pull-quotes.
* Add editor-style refinements: `font-feature-settings` for Inter, disabled ligatures in monospace, figcaption contrast lifted to `on-surface-muted`, empty-state placeholder tied to `on-surface-subtle`, keyboard focus ring on non-link interactives keyed to the `primary` token.
* Add paired semantic text tokens (`on-success`, `on-warning`, `on-destructive`, `on-info`, `on-primary-surface`) so pattern authors can always pair text with its matching state surface and clear WCAG 1.4.3 at 4.5:1. Base state colors remain for non-text UI (icons, borders) on the same surfaces at 3:1.
* Add an accessibility-audit toolkit in `tools/` (axe-core scan, contrast matrix) and a full audit document in `docs/accessibility.md`.
* Replace the header Navigation block's Page List fallback with explicit navigation-link children, eliminating the nested `<ul>` WCAG 1.3.1 violation.
* Raise `Requires PHP` to 8.0.

= 0.1.0 =
* Initial release. theme.json, templates, parts. The custom breadcrumb block shipped here in 0.1.0 but moved to the Blocklane Blocks companion plugin in 0.2.1.

== Credits ==

Bundled fonts (Inter, Lora, JetBrains Mono) are licensed under the SIL Open Font License 1.1 and served from the theme itself — see assets/fonts/LICENSE.md. No font CDN is used.

* Inter — licensed under the SIL Open Font License 1.1 by Rasmus Andersson
* Lora — licensed under the SIL Open Font License 1.1 by Cyreal
* JetBrains Mono — licensed under the SIL Open Font License 1.1 by JetBrains

Screenshot imagery is original work by Garrett Johnson, released under GPLv2.

Development tooling (not bundled in the distributed theme):

* axe-core by Deque Systems — used for automated accessibility scanning — Mozilla Public License 2.0
* Playwright by Microsoft — used to drive the headless browser during audits — Apache License 2.0

== Copyright ==

Blocklane WordPress Theme, Copyright 2026 Garrett Johnson.
Blocklane is distributed under the terms of the GNU GPL version 2 or later.

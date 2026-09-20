<?php
/**
 * Blocklane — one-time upgrade routines.
 *
 * 0.7.0 retired the "Untitled Mirror" style variation: the default theme.json
 * now carries the whole design system, and styles/untitled.json is gone. A
 * site that had ACTIVATED the variation still carries its full payload in the
 * user global-styles CPT, which silently overrides the new default forever —
 * the variation file's deletion does not touch stored user data.
 *
 * Case table (each case exercised live in the 0.7.0 review + fix round):
 *   0. Request context ............................ the routine runs only for
 *      users with `edit_theme_options` (never anonymous/admin-ajax traffic),
 *      and the CPT lookup is READ-ONLY — the core resolver helper was not
 *      reused precisely because it creates a post as a side effect.
 *   1. No user global-styles post ................. no-op (nothing created).
 *   2. Post exists, no `uui-` spacingSizes slugs .. no-op (organic user styles
 *      untouched; the marker inspects settings.spacing.spacingSizes slugs
 *      specifically — `is-style-uui-*` block-style names, which pre-0.9
 *      payloads can carry (the styles were renamed in the 2026-08-19
 *      migration), cannot false-positive it).
 *   3. Pristine variation payload (fingerprint
 *      match against the retired file) ........... deleted. Proven lossless:
 *      activating a variation copies its payload verbatim (modulo origin
 *      wrapping and var syntax), so deletion exactly reverts the activation.
 *      A failed deletion falls through to the case-4 notice, and the version
 *      stamp still advances (the notice option carries the signal).
 *   4. Variation + user tweaks (markers present,
 *      fingerprint differs) ...................... persistent dismissible
 *      admin notice pointing at Styles → Revert; nothing is modified or
 *      deleted — user data is never destroyed on a guess.
 *   5. Idempotence ................................ the per-stylesheet version
 *      option gates the check (child themes resolve their own CPT, so the
 *      gate is keyed to the stylesheet); case-3 deletion also removes the
 *      markers.
 *
 * @package blocklane
 * @since   0.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * md5 of the retired Untitled Mirror payload, normalized: `$schema`/`version`/
 * `title` stripped, `var:preset|kind|slug` rewritten to `var(--wp--kind--slug)`,
 * flattened with control-character separators (0x1F between path segments,
 * 0x1E between sorted `path=value` records — characters that cannot appear in
 * the payload, so paths cannot be forged by key contents), then hashed.
 * blocklane_upgrade_canonicalize() applies the same normalization (plus
 * origin-key unwrapping) to a stored payload. Recompute from git history
 * (styles/untitled.json at 0.6.4) if this ever needs to change.
 */
const BLOCKLANE_UNTITLED_FINGERPRINT = 'bace18db913f93359d0f74864ceb674c';

/**
 * Run one-time upgrade routines when the theme version changes.
 *
 * Gated on `edit_theme_options`: the routine touches theme-scoped user data,
 * so it waits for a capable admin request rather than running for arbitrary
 * admin_init traffic (admin-ajax, cron-ish hits, low-cap users).
 *
 * @since 0.7.0
 * @return void
 */
function blocklane_maybe_upgrade(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$option = 'blocklane_theme_version_' . get_stylesheet();
	$stored = (string) get_option( $option, '' );
	if ( '' === $stored ) {
		// Pre-release builds briefly used an unsuffixed option name; adopt and
		// remove it so the routine neither re-runs nor orphans the row.
		$legacy = (string) get_option( 'blocklane_theme_version', '' );
		if ( '' !== $legacy ) {
			$stored = $legacy;
			update_option( $option, $stored );
			delete_option( 'blocklane_theme_version' );
		}
	}
	if ( BLOCKLANE_VERSION === $stored ) {
		return;
	}

	if ( version_compare( $stored, '0.7.0', '<' ) ) {
		blocklane_upgrade_retire_untitled_variation();
	}

	// The degradation SCANS (retired border slug, retired uui-* block
	// styles) deliberately do NOT gate on a version compare here: a one-shot
	// version gate made a failed scan permanently indistinguishable from a
	// clean one (#148). They run from blocklane_upgrade_run_scans() below,
	// driven by their own per-scan state options and marker-set revisions.

	update_option( $option, BLOCKLANE_VERSION );
}
add_action( 'admin_init', 'blocklane_maybe_upgrade' );

/**
 * Find the user global-styles post for the active stylesheet WITHOUT creating
 * one (the core resolver helper inserts a post when none exists, which would
 * turn the no-op case into a write).
 *
 * @since 0.7.0
 * @return WP_Post|null
 */
function blocklane_upgrade_get_user_styles_post(): ?WP_Post {
	$query = new WP_Query(
		array(
			'post_type'              => 'wp_global_styles',
			'posts_per_page'         => 1,
			'post_status'            => array( 'publish', 'draft' ),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => get_stylesheet(),
				),
			),
		)
	);
	return $query->posts ? $query->posts[0] : null;
}

/**
 * Detect (and, when provably pristine, remove) a stored Untitled Mirror
 * global-styles payload. See the case table in the file header.
 *
 * @since 0.7.0
 * @return void
 */
function blocklane_upgrade_retire_untitled_variation(): void {
	$post = blocklane_upgrade_get_user_styles_post();
	if ( ! $post || '' === trim( (string) $post->post_content ) ) {
		return; // Case 1.
	}

	$data = json_decode( $post->post_content, true );
	if ( ! is_array( $data ) ) {
		return;
	}

	// Case 2: only the variation payload defines uui-* spacing SIZE SLUGS.
	// (A plain substring probe would false-positive on is-style-uui-* block
	// style names, which pre-0.9 user payloads legitimately carry — the
	// styles were renamed in the 2026-08-19 migration, but old payloads
	// keep the old class names.)
	if ( ! blocklane_upgrade_has_uui_spacing( $data ) ) {
		return;
	}

	unset( $data['isGlobalStylesUserThemeJSON'], $data['version'] );
	$hash = md5( blocklane_upgrade_canonicalize( $data ) );

	if ( BLOCKLANE_UNTITLED_FINGERPRINT === $hash ) {
		// Case 3: pristine activation — deletion exactly reverts it.
		$deleted = wp_delete_post( $post->ID, true );
		if ( $deleted ) {
			delete_option( 'blocklane_untitled_styles_notice' );
			return;
		}
		// A filter short-circuited the delete — fall through to the notice
		// so the stale payload is at least surfaced, never silently kept.
	}

	// Case 4: variation plus user edits — flag it, never touch it.
	update_option( 'blocklane_untitled_styles_notice', 1 );
}

/**
 * Does the payload define spacingSizes with the retired uui- slug namespace?
 *
 * @since 0.7.0
 * @param array $data Decoded global-styles payload.
 * @return bool
 */
function blocklane_upgrade_has_uui_spacing( array $data ): bool {
	$sizes = $data['settings']['spacing']['spacingSizes'] ?? array();
	// The stored form may wrap preset arrays under an origin key.
	if ( is_array( $sizes ) && ( isset( $sizes['theme'] ) || isset( $sizes['custom'] ) ) ) {
		$sizes = $sizes['theme'] ?? $sizes['custom'];
	}
	if ( ! is_array( $sizes ) ) {
		return false;
	}
	foreach ( $sizes as $size ) {
		if ( is_array( $size ) && isset( $size['slug'] ) && str_starts_with( (string) $size['slug'], 'uui-' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Surface the case-4 notice until dismissed or resolved.
 *
 * @since 0.7.0
 * @return void
 */
function blocklane_untitled_styles_notice(): void {
	if ( ! get_option( 'blocklane_untitled_styles_notice' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( isset( $_GET['blocklane-dismiss-untitled-notice'] ) && check_admin_referer( 'blocklane_dismiss_untitled' ) ) {
		delete_option( 'blocklane_untitled_styles_notice' );
		return;
	}
	$dismiss = wp_nonce_url( add_query_arg( 'blocklane-dismiss-untitled-notice', '1' ), 'blocklane_dismiss_untitled' );
	printf(
		'<div class="notice notice-warning"><p>%s</p><p><a class="button" href="%s">%s</a> <a class="button-link" href="%s">%s</a></p></div>',
		esc_html__( 'Blocklane 0.7.0 retired the “Untitled Mirror” style variation, but this site still carries a customized copy of its styles. Your pages may not reflect the theme’s new design system until you revert or re-apply your styles in the Site Editor (Styles → Revert to reset).', 'blocklane' ),
		esc_url( admin_url( 'site-editor.php?path=%2Fstyles' ) ),
		esc_html__( 'Open Styles', 'blocklane' ),
		esc_url( $dismiss ),
		esc_html__( 'Dismiss', 'blocklane' )
	);
}
add_action( 'admin_notices', 'blocklane_untitled_styles_notice' );

/*
 * ── Degradation scans ────────────────────────────────────────────────────────
 *
 * A small engine, N declarative scan definitions. Three properties every scan
 * must have — each was a shipped defect when missing:
 *
 * 1. Markers come from the retired token's FULL serialization contract —
 *    every form core writes it in — not the forms the fix's author thought
 *    of (#144: the border scan missed every border-color carrier of a
 *    palette color literally named "Border", plus all of wp_global_styles).
 * 2. Scope comes from provenance and resolution: only stores that render,
 *    and only references that actually fail to resolve.
 * 3. "Could not determine" is a first-class, visible, retried state — never
 *    collapsed into "clean" (#148: a failed scan deleted its own notice and
 *    nothing ever retried; the icon-cache incident's shape).
 *
 * Per-scan state option `blocklane_scan_{id}`:
 *   array{state:'clean'|'found'|'error'|'dismissed', rev:int,
 *         findings:string[], time:int}
 * A scan runs when its option is absent, its stored rev is below the def's
 * rev (bump the rev when widening a marker set — every site rescans,
 * including ones previously marked clean), or its state is 'error'. Runners
 * are pure reads plus one absolute update_option — idempotent under
 * double-run by construction.
 */

/**
 * The scan definitions.
 *
 * @since 0.8.0
 * @return array<string,array{rev:int,run:callable,label:string}>
 */
function blocklane_upgrade_scan_defs(): array {
	return array(
		'border_slug' => array(
			'rev'   => 2,
			'run'   => 'blocklane_upgrade_scan_border_slug',
			'label' => __( 'content using the retired “Border” color', 'blocklane' ),
		),
		'uui_styles'  => array(
			'rev'   => 1,
			'run'   => 'blocklane_upgrade_scan_uui_styles',
			'label' => __( 'content using the retired UUI block styles', 'blocklane' ),
		),
	);
}

/**
 * Run every scan whose state demands it. Same capability gate and hook as
 * blocklane_maybe_upgrade(), one priority later so version routines land
 * first.
 *
 * @since 0.8.0
 * @return void
 */
function blocklane_upgrade_run_scans(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// The pre-engine border scan stored a bare title list here; the engine
	// owns the state now. One-time cleanup, cheap when already absent.
	delete_option( 'blocklane_border_slug_notice' );

	foreach ( blocklane_upgrade_scan_defs() as $id => $def ) {
		$state = get_option( 'blocklane_scan_' . $id );
		if ( is_array( $state )
			&& (int) ( $state['rev'] ?? 0 ) >= $def['rev']
			&& in_array( $state['state'] ?? '', array( 'clean', 'found', 'dismissed' ), true ) ) {
			continue; // Settled at the current marker-set revision.
		}

		$result = call_user_func( $def['run'] );

		// On error, keep any findings gathered before the failure — shown as
		// "found these; scan incomplete" — and never delete prior state.
		// Non-autoloaded, like the pre-engine option deliberately was: the
		// rows can carry 20 title strings and only admin_init reads them (#177).
		update_option(
			'blocklane_scan_' . $id,
			array(
				'state'    => $result['state'],
				'rev'      => $def['rev'],
				'findings' => array_slice( array_map( 'strval', $result['findings'] ), 0, 20 ),
				'time'     => time(),
			),
			false
		);
	}
}
add_action( 'admin_init', 'blocklane_upgrade_run_scans', 11 );

/**
 * Shared posts-table pass: one bounded OR-of-LIKEs over renderable statuses.
 *
 * @since 0.8.0
 * @param array $markers Raw substrings to probe for.
 * @return array{state:string,findings:array<int,string>} 'error' when the
 *                        SELECT failed; findings labels otherwise.
 */
function blocklane_upgrade_scan_posts_for( array $markers ): array {
	global $wpdb;

	$like   = array();
	$values = array();
	foreach ( $markers as $marker ) {
		$like[]   = 'post_content LIKE %s';
		$values[] = '%' . $wpdb->esc_like( $marker ) . '%';
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- the interpolated fragment is built from fixed placeholders above.
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_type FROM {$wpdb->posts}
			WHERE post_status IN ( 'publish', 'draft', 'private', 'future', 'pending' )
			AND post_type NOT IN ( 'revision', 'attachment', 'nav_menu_item', 'wp_global_styles' )
			AND ( " . implode( ' OR ', $like ) . ' ) LIMIT 20',
			$values
		)
	);

	$had_error = '' !== $wpdb->last_error;

	$findings = array();
	foreach ( (array) $rows as $row ) {
		// Provenance scope for template rows: a fork belonging to an
		// INACTIVE theme can never render, so flagging it is the same
		// false-positive class the Pro fork scan excludes via its
		// wp_theme tax_query (#175). Bounded — at most 20 rows reach this.
		if ( in_array( (string) $row->post_type, array( 'wp_template', 'wp_template_part' ), true ) ) {
			$themes = wp_get_object_terms( (int) $row->ID, 'wp_theme', array( 'fields' => 'names' ) );
			if ( is_wp_error( $themes ) || ! in_array( get_stylesheet(), (array) $themes, true ) ) {
				continue;
			}
		}
		$findings[] = '' !== (string) $row->post_title
			? (string) $row->post_title
			: '#' . (int) $row->ID . ' (' . (string) $row->post_type . ')';
	}

	return array(
		'state'    => $had_error ? 'error' : ( $findings ? 'found' : 'clean' ),
		'findings' => $findings,
	);
}

/**
 * Scan for the color slug retired in 0.7.0 (`border` → `outline`), rev 2.
 *
 * The rename shipped no content migration — the degradation is
 * inherit-plainer, never broken, and auto-rewriting user content was judged
 * riskier than the harm (issue #116; detection over acceptance). Rev 2
 * derives its markers from the slug's FULL serialization contract (#144):
 * every color attribute core writes it into (text, background, BORDER — the
 * most likely use of a color literally named "Border" — overlays, icons),
 * the `var:preset|color|border` reference form (per-side border colors,
 * element refs; trailing quote so a future `border-2` slug never matches),
 * the slug-specific emitted classes, and the raw CSS-var form for hand-
 * written markup. NEVER the generic `has-border-color` class: core emits
 * that on any block with a custom border color — the very collision that
 * forced the rename.
 *
 * Stores: the posts table, the ACTIVE stylesheet's user global-styles
 * payload (its shapes are the var forms only), and block widgets — the
 * latter only while a sidebar is registered, because on stock Blocklane
 * widget content never renders and flagging it would be its own false
 * positive.
 *
 * Resolution guard: if ANY merged palette entry is slugged `border` (a
 * child theme or the user's own Styles re-defined it), every reference
 * resolves and the scan is clean — the claim is "this reference is dead",
 * not "this string is old".
 *
 * @since 0.8.0
 * @return array{state:string,findings:array<int,string>}
 */
function blocklane_upgrade_scan_border_slug(): array {
	global $wpdb;

	// Resolution guard across every origin.
	$palette = (array) wp_get_global_settings( array( 'color', 'palette' ) );
	foreach ( $palette as $origin_entries ) {
		foreach ( (array) $origin_entries as $entry ) {
			if ( is_array( $entry ) && 'border' === ( $entry['slug'] ?? '' ) ) {
				return array(
					'state'    => 'clean',
					'findings' => array(),
				);
			}
		}
	}

	$attr_markers = array(
		'"textColor":"border"',
		'"backgroundColor":"border"',
		'"borderColor":"border"',
		'"overlayColor":"border"',
		'"overlayTextColor":"border"',
		'"overlayBackgroundColor":"border"',
		'"iconColor":"border"',
		'"iconBackgroundColor":"border"',
		// The reference form: per-side style.border.*.color and element
		// refs. The trailing quote is the precision terminator.
		'var:preset|color|border"',
		// Slug-specific emitted classes — cannot collide with core's
		// generic has-border-color marker.
		'has-border-border-color',
		'has-border-background-color',
		// Raw CSS-var form in Custom-HTML/hand-written markup and
		// global-styles payloads; ')' terminates the slug. Attr-JSON
		// markers must never contain a double hyphen — inside block
		// comments serialize_block_attributes() stores it as
		// -- — so this marker aims at HTML and styles text,
		// where the sequence survives verbatim.
		'--color--border)',
	);

	$result = blocklane_upgrade_scan_posts_for( $attr_markers );
	if ( 'error' === $result['state'] ) {
		return $result;
	}
	$findings = $result['findings'];

	// The active stylesheet's user global-styles payload — the store the
	// rev-1 scan explicitly excluded (#144). Read-only lookup.
	$styles_post = blocklane_upgrade_get_user_styles_post();
	if ( '' !== $wpdb->last_error ) {
		return array(
			'state'    => 'error',
			'findings' => $findings,
		);
	}
	if ( $styles_post ) {
		$payload = (string) $styles_post->post_content;
		if ( false !== strpos( $payload, 'var:preset|color|border"' )
			|| false !== strpos( $payload, '--color--border)' ) ) {
			$findings[] = __( 'Styles (Site Editor → Styles)', 'blocklane' );
		}
	}

	// Block widgets render only through a registered sidebar.
	if ( ! empty( $GLOBALS['wp_registered_sidebars'] ) ) {
		$widgets = get_option( 'widget_block' );
		if ( is_array( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$content = is_array( $widget ) ? (string) ( $widget['content'] ?? '' ) : '';
				foreach ( $attr_markers as $marker ) {
					if ( '' !== $content && false !== strpos( $content, $marker ) ) {
						$findings[] = __( 'A block widget', 'blocklane' );
						break 2;
					}
				}
			}
		}
	}

	return array(
		'state'    => $findings ? 'found' : 'clean',
		'findings' => $findings,
	);
}

/**
 * Scan for the fifteen `is-style-uui-*` block-style classes retired by the
 * 2026-08-19 rename (#149). The prefix is a complete, collision-free
 * superset — nothing legitimate ever carried it — so one LIKE beats fifteen.
 * Also walks the active stylesheet's user global-styles payload for
 * `uui-`-prefixed per-block-style variation keys, which store the bare slug
 * (no class form for the posts probe to see).
 *
 * Detection over acceptance, per the same #116 precedent: no CSS shims, no
 * registration aliases, no silent rewrite — the notice tells the admin what
 * to re-pick.
 *
 * @since 0.8.0
 * @return array{state:string,findings:array<int,string>}
 */
function blocklane_upgrade_scan_uui_styles(): array {
	global $wpdb;

	// Resolution guard (the engine's property 2, same as the border scan's
	// palette check): if ANY registered block style still carries the uui-
	// prefix — a child theme re-registering them for back-compat — the
	// references resolve and nothing is degraded (#187).
	foreach ( WP_Block_Styles_Registry::get_instance()->get_all_registered() as $block_styles ) {
		foreach ( array_keys( (array) $block_styles ) as $style_name ) {
			if ( str_starts_with( (string) $style_name, 'uui-' ) ) {
				return array(
					'state'    => 'clean',
					'findings' => array(),
				);
			}
		}
	}

	$result = blocklane_upgrade_scan_posts_for( array( 'is-style-uui-' ) );
	if ( 'error' === $result['state'] ) {
		return $result;
	}
	$findings = $result['findings'];

	$styles_post = blocklane_upgrade_get_user_styles_post();
	// Same guard as the border twin — a failed styles lookup must surface
	// as 'error', never read as "no Styles customizations" (#167).
	if ( '' !== $wpdb->last_error ) {
		return array(
			'state'    => 'error',
			'findings' => $findings,
		);
	}
	if ( $styles_post ) {
		$data = json_decode( (string) $styles_post->post_content, true );
		foreach ( (array) ( $data['styles']['blocks'] ?? array() ) as $block_styles ) {
			foreach ( array_keys( (array) ( $block_styles['variations'] ?? array() ) ) as $variation ) {
				if ( str_starts_with( (string) $variation, 'uui-' ) ) {
					$findings[] = __( 'Styles customizations (Site Editor → Styles)', 'blocklane' );
					break 2;
				}
			}
		}
	}

	return array(
		'state'    => $findings ? 'found' : 'clean',
		'findings' => $findings,
	);
}

/**
 * Dismiss / re-scan actions for the scan notices, nonce'd.
 *
 * @since 0.8.0
 * @return void
 */
function blocklane_upgrade_scan_actions(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$defs = blocklane_upgrade_scan_defs();

	if ( isset( $_GET['blocklane-scan-dismiss'] ) && check_admin_referer( 'blocklane_scan_action' ) ) {
		$id = sanitize_key( (string) wp_unslash( $_GET['blocklane-scan-dismiss'] ) );
		if ( isset( $defs[ $id ] ) ) {
			$state = get_option( 'blocklane_scan_' . $id );
			if ( is_array( $state ) && 'found' === ( $state['state'] ?? '' ) ) {
				$state['state'] = 'dismissed'; // Keeps rev: a later rev bump re-scans.
				update_option( 'blocklane_scan_' . $id, $state );
			}
		}
		wp_safe_redirect( remove_query_arg( array( 'blocklane-scan-dismiss', '_wpnonce' ) ) );
		exit;
	}

	if ( isset( $_GET['blocklane-scan-rescan'] ) && check_admin_referer( 'blocklane_scan_action' ) ) {
		$id = sanitize_key( (string) wp_unslash( $_GET['blocklane-scan-rescan'] ) );
		if ( isset( $defs[ $id ] ) ) {
			delete_option( 'blocklane_scan_' . $id ); // Absent = re-runs next load.
		}
		wp_safe_redirect( remove_query_arg( array( 'blocklane-scan-rescan', '_wpnonce' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'blocklane_upgrade_scan_actions', 9 );

/**
 * One renderer for every scan's notice. 'found' is dismissible; 'error' is
 * NOT — an unresolvable scan staying visible is the point (#148).
 *
 * @since 0.8.0
 * @return void
 */
function blocklane_upgrade_scan_notices(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	foreach ( blocklane_upgrade_scan_defs() as $id => $def ) {
		$state = get_option( 'blocklane_scan_' . $id );
		if ( ! is_array( $state ) ) {
			continue;
		}
		$findings = array_map( 'strval', (array) ( $state['findings'] ?? array() ) );

		if ( 'error' === ( $state['state'] ?? '' ) ) {
			printf(
				'<div class="notice notice-warning"><p>%s%s</p></div>',
				sprintf(
					/* translators: %s: what the scan looks for. */
					esc_html__( 'Blocklane could not finish checking your site for %s; it will retry automatically.', 'blocklane' ),
					esc_html( $def['label'] )
				),
				$findings ? ' ' . sprintf(
					/* translators: %s: comma-separated content titles. */
					esc_html__( 'Found before the check stopped: %s.', 'blocklane' ),
					esc_html( implode( ', ', $findings ) )
				) : ''
			);
			continue;
		}

		if ( 'found' !== ( $state['state'] ?? '' ) || ! $findings ) {
			continue;
		}

		$message = 'border_slug' === $id
			? sprintf(
				/* translators: %s: comma-separated content titles. */
				__( 'Blocklane renamed its “Border” palette color to “Outline” in 0.7.0. This content still references the old color and now falls back to inherited colors: %s. Re-pick the Outline color where those blocks used Border — nothing is changed automatically.', 'blocklane' ),
				implode( ', ', $findings )
			)
			: sprintf(
				/* translators: %s: comma-separated content titles. */
				__( 'Blocklane 0.8.0 renamed its block styles (the UUI prefix is gone) and this content still carries the old names, so it now renders without them: %s. Open each and re-pick the style (Secondary, Tertiary, Full Width, Checks, Badge, Segmented, Vertical, Card). Two styles were removed: UUI Link (use Ghost) and UUI Price (the Pricing Card group style covers it). Marquee and Wall Hero sections: re-insert the pattern. Nothing is changed automatically.', 'blocklane' ),
				implode( ', ', $findings )
			);

		printf(
			'<div class="notice notice-warning"><p>%s</p><p><a class="button-link" href="%s">%s</a> <a class="button-link" href="%s">%s</a></p></div>',
			esc_html( $message ),
			esc_url( wp_nonce_url( add_query_arg( 'blocklane-scan-dismiss', $id ), 'blocklane_scan_action' ) ),
			esc_html__( 'Dismiss', 'blocklane' ),
			esc_url( wp_nonce_url( add_query_arg( 'blocklane-scan-rescan', $id ), 'blocklane_scan_action' ) ),
			esc_html__( 'Re-scan', 'blocklane' )
		);
	}
}
add_action( 'admin_notices', 'blocklane_upgrade_scan_notices' );

/**
 * Canonicalize a decoded global-styles payload for fingerprinting: unwrap
 * single-key origin wrappers, rewrite var:preset shorthand, flatten to
 * control-character-separated sorted path=value records (0x1F joins path
 * segments, 0x1E joins records — unforgeable by key or value contents).
 * Mirrors the normalization the 0.7.0 review used to prove stored-vs-file
 * byte equivalence.
 *
 * @since 0.7.0
 * @param array $data Decoded payload (isGlobalStylesUserThemeJSON/version removed).
 * @return string Canonical representation.
 */
function blocklane_upgrade_canonicalize( array $data ): string {
	$unwrap = function ( $node ) use ( &$unwrap ) {
		if ( ! is_array( $node ) ) {
			if ( is_string( $node ) && str_starts_with( $node, 'var:preset|' ) ) {
				$parts = explode( '|', substr( $node, 4 ) );
				return 'var(--wp--' . implode( '--', $parts ) . ')';
			}
			return $node;
		}
		if ( 1 === count( $node ) && ( isset( $node['theme'] ) || isset( $node['custom'] ) ) ) {
			$node = $node['theme'] ?? $node['custom'];
		}
		if ( ! is_array( $node ) ) {
			return $unwrap( $node );
		}
		foreach ( $node as $k => $v ) {
			$node[ $k ] = $unwrap( $v );
		}
		return $node;
	};

	$flat    = array();
	$flatten = function ( $node, string $path ) use ( &$flatten, &$flat ) {
		if ( is_array( $node ) ) {
			foreach ( $node as $k => $v ) {
				$flatten( $v, $path . "\x1f" . $k );
			}
			return;
		}
		if ( is_bool( $node ) ) {
			$node = $node ? 'true' : 'false';
		}
		$flat[ $path ] = (string) $node;
	};
	$flatten( $unwrap( $data ), '' );
	ksort( $flat, SORT_STRING );

	$records = array();
	foreach ( $flat as $k => $v ) {
		$records[] = $k . '=' . $v;
	}
	return implode( "\x1e", $records );
}

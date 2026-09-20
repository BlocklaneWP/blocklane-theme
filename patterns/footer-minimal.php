<?php
/**
 * Title: Footer Minimal
 * Slug: blocklane/footer-minimal
 * Categories: footer
 * Keywords: footer, minimal, compact footer, copyright line, single row, small site
 * Block Types: core/template-part/footer
 * Viewport Width: 1400
 * Description: Compact single-column footer with the site title, one inline link row and a copyright line. The lightest footer - best where a full sitemap would be more structure than the content warrants.
 *
 * The setup wizard's footer styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"border":{"top":{"color":"var:preset|color|outline","style":"solid","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--outline);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"0.4em"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} -->
			<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--muted)">©</p>
			<!-- /wp:paragraph -->

			<!-- wp:site-title {"level":0,"isLink":false,"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"small","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

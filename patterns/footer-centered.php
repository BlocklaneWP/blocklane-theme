<?php
/**
 * Title: Footer Centered
 * Slug: blocklane/footer-centered
 * Categories: footer
 * Keywords: footer, centered, typographic footer, one page, landing page, no navigation
 * Block Types: core/template-part/footer
 * Viewport Width: 1400
 * Description: Centered footer with the site title stacked above two short lines of text. Deliberately typographic rather than navigational - suits single-page sites and campaign landing pages with nowhere to link.
 *
 * The setup wizard's footer styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"border":{"top":{"color":"var:preset|color|outline","style":"solid","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--outline);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"style":{"spacing":{"blockGap":"0.4em"}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--muted)">©</p>
		<!-- /wp:paragraph -->

		<!-- wp:site-title {"level":0,"isLink":false,"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} /-->

		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--muted)">· All rights reserved.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

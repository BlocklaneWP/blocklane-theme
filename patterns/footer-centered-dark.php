<?php
/**
 * Title: Footer Centered — Dark
 * Slug: blocklane/footer-centered-dark
 * Categories: footer
 * Keywords: footer, centered, dark, dark centered, inverted typographic, campaign page
 * Block Types: core/template-part/footer
 * Viewport Width: 1400
 * Description: Dark-surface take on the centered, typographic footer. For dark campaign pages that need a closing mark rather than a set of links.
 *
 * The setup wizard's footer styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"style":{"spacing":{"blockGap":"0.4em"}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|subtle"}},"fontSize":"small"} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--subtle)">©</p>
		<!-- /wp:paragraph -->

		<!-- wp:site-title {"level":0,"isLink":false,"style":{"color":{"text":"var:preset|color|subtle"}},"fontSize":"small"} /-->

		<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|subtle"}},"fontSize":"small"} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--subtle)">· All rights reserved.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

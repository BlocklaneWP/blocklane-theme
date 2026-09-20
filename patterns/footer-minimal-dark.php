<?php
/**
 * Title: Footer Minimal — Dark
 * Slug: blocklane/footer-minimal-dark
 * Categories: footer
 * Keywords: footer, minimal, dark, dark compact, inverted minimal, dark page
 * Block Types: core/template-part/footer
 * Viewport Width: 1400
 * Description: Dark-surface take on the compact single-row footer. Closes a dark page without the abrupt light block a default footer would leave at the bottom.
 *
 * The setup wizard's footer styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"0.4em"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|subtle"}},"fontSize":"small"} -->
			<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--subtle)">©</p>
			<!-- /wp:paragraph -->

			<!-- wp:site-title {"level":0,"isLink":false,"style":{"color":{"text":"var:preset|color|subtle"}},"fontSize":"small"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"never","textColor":"base","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"small","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

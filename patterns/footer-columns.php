<?php
/**
 * Title: Footer with Nav Columns
 * Slug: blocklane/footer-columns
 * Categories: footer
 * Keywords: footer, columns, sitemap, navigation, tagline, wayfinding
 * Block Types: core/template-part/footer
 * Viewport Width: 1400
 * Description: Full sitemap footer with the site logo and tagline beside two columns of grouped navigation links, over a copyright line. The most substantial footer - use it when the site has enough sections to need wayfinding at the bottom.
 *
 * The setup wizard's footer styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|60"},"border":{"top":{"color":"var:preset|color|outline","style":"solid","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--outline);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"40%","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
			<div class="wp-block-group">
				<!-- wp:site-logo {"width":32} /-->
				<!-- wp:site-title {"level":0,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:site-tagline {"style":{"color":{"text":"var:preset|color|muted"}},"fontSize":"small"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
			<h3 class="wp-block-heading has-small-font-size" style="font-style:normal;font-weight:600">Pages</h3>
			<!-- /wp:heading -->

			<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"fontSize":"small","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
			<h3 class="wp-block-heading has-small-font-size" style="font-style:normal;font-weight:600">Connect</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><a href="#">Twitter / X</a></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><a href="#">Instagram</a></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><a href="#">LinkedIn</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50"},"blockGap":"0.4em"},"border":{"top":{"color":"var:preset|color|outline","style":"solid","width":"1px"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group alignwide" style="border-top-color:var(--wp--preset--color--outline);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--50)">
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

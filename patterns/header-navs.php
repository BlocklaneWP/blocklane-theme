<?php
/**
 * Title: Header with Navs
 * Slug: blocklane/header-navs
 * Categories: header
 * Keywords: header, navigation, minimal, single row, lean header, content site
 * Block Types: core/template-part/header
 * Viewport Width: 1400
 * Description: Single-row header with the site logo and one inline navigation menu. The leanest header in the set - use it for content sites and one-page layouts where the top of the page should stay quiet.
 *
 * The setup wizard's header styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":40} /-->
			<!-- wp:site-title {"level":0,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"medium","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

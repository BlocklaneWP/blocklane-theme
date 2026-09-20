<?php
/**
 * Title: Header with Navs — Dark
 * Slug: blocklane/header-navs-dark
 * Categories: header
 * Keywords: header, navigation, dark, inverted header, dark hero, night
 * Block Types: core/template-part/header
 * Viewport Width: 1400
 * Description: Dark-surface take on the lean logo-and-navigation header. Use it when the page opens on a dark hero and the header should read as part of that surface rather than a separate light band.
 *
 * The setup wizard's header styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":40} /-->
			<!-- wp:site-title {"level":0,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","textColor":"base","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"medium","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

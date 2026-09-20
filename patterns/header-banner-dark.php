<?php
/**
 * Title: Header with Banner — Dark
 * Slug: blocklane/header-banner-dark
 * Categories: header
 * Keywords: header, announcement, dark, dark announcement, inverted promo, standing notice
 * Block Types: core/template-part/header
 * Viewport Width: 1400
 * Description: Dark-surface take on the two-tier header with an announcement bar. Suits dark sites running a standing notice that should stay legible without introducing a bright band at the very top.
 *
 * The setup wizard's header styles source from these patterns (and they
 * double as core's template-part Replace choices). Light/dark pairs ride
 * the "-dark" slug suffix convention.
 *
 * @package blocklane
 * @since   0.6.0
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group">
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"backgroundColor":"primary","textColor":"base","layout":{"type":"constrained"}} -->
	<div class="wp-block-group has-base-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)">
		<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
		<p class="has-text-align-center has-small-font-size">Announce something exciting here — <a href="#">learn more</a>.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

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
</div>
<!-- /wp:group -->

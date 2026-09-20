<?php
/**
 * Title: Header with Buttons — Dark
 * Slug: blocklane/header-buttons-dark
 * Categories: header
 * Keywords: header, buttons, dark, inverted buttons, dark call to action, dark theme
 * Block Types: core/template-part/header
 * Viewport Width: 1400
 * Description: Dark-surface take on the header with two call-to-action buttons. For dark-themed sites where the buttons should sit on an inverted bar instead of a light strip across the top of the page.
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

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","textColor":"base","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"medium","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} /-->

			<!-- wp:buttons {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-ghost","textColor":"base","fontSize":"small"} -->
				<div class="wp-block-button has-custom-font-size is-style-ghost has-small-font-size"><a class="wp-block-button__link has-base-color has-text-color wp-element-button" href="#">Log In</a></div>
				<!-- /wp:button -->

				<!-- wp:button {"fontSize":"small"} -->
				<div class="wp-block-button has-custom-font-size has-small-font-size"><a class="wp-block-button__link wp-element-button" href="#">Get Started</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

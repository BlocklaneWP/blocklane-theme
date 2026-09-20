<?php
/**
 * Title: Hidden — Header
 * Slug: blocklane/hidden-header
 * Inserter: no
 *
 * The header part's content lives here (not in parts/header.html) so its
 * strings pass through gettext — static template HTML never does.
 *
 * @package blocklane
 * @since   0.4.0
 */

?>
<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:site-logo {"width":40} /-->
			<!-- wp:site-title {"level":0,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","ariaLabel":"<?php echo esc_attr_x( 'Primary', 'Accessible name of the main navigation landmark.', 'blocklane' ); ?>","layout":{"type":"flex","justifyContent":"right","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"medium","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
			<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'Home', 'blocklane' ); ?>","url":"<?php echo esc_url( home_url( '/' ) ); ?>","kind":"custom"} /-->
			<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'About', 'blocklane' ); ?>","url":"<?php echo esc_url( home_url( '/about/' ) ); ?>","kind":"custom"} /-->
			<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'Blog', 'blocklane' ); ?>","url":"<?php echo esc_url( home_url( '/blog/' ) ); ?>","kind":"custom"} /-->
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

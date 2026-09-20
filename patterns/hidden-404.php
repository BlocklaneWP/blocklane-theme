<?php
/**
 * Title: Hidden — 404 content
 * Slug: blocklane/hidden-404
 * Inserter: no
 *
 * @package blocklane
 * @since   0.4.0
 */

?>
<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"heading-xxx-large","style":{"typography":{"letterSpacing":"-0.04em"}}} -->
<h1 class="wp-block-heading has-text-align-center has-heading-xxx-large-font-size" style="letter-spacing:-0.04em"><?php echo esc_html_x( '404', 'Error code heading on the not-found page.', 'blocklane' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"x-large","style":{"color":{"text":"var:preset|color|muted"}}} -->
<p class="has-text-align-center has-x-large-font-size has-text-color" style="color:var(--wp--preset--color--muted)"><?php esc_html_e( 'The page you&rsquo;re looking for doesn&rsquo;t exist or has moved.', 'blocklane' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
	<!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return Home', 'blocklane' ); ?></a></div>
	<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained","contentSize":"480px"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-x-large-font-size"><?php esc_html_e( 'Or try a search', 'blocklane' ); ?></h2>
	<!-- /wp:heading -->
	<!-- wp:search {"label":"<?php echo esc_attr_x( 'Search', 'Search form label.', 'blocklane' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr_x( 'Type a search term…', 'Search input placeholder on the 404 page.', 'blocklane' ); ?>","buttonText":"<?php echo esc_attr_x( 'Search', 'Search submit button.', 'blocklane' ); ?>"} /-->
</div>
<!-- /wp:group -->

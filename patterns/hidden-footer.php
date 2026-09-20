<?php
/**
 * Title: Hidden — Footer
 * Slug: blocklane/hidden-footer
 * Inserter: no
 *
 * The dynamic copyright year is why this is a PHP pattern — a static
 * template can only hardcode one.
 *
 * @package blocklane
 * @since   0.4.0
 */

?>
<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"border":{"top":{"color":"var:preset|color|outline","style":"solid","width":"1px"}}}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--outline);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.4em"}}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"fontSize":"small","style":{"color":{"text":"var:preset|color|muted"}}} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--muted)">©&nbsp;<?php echo esc_html( date_i18n( 'Y' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:site-title {"level":0,"isLink":false,"fontSize":"small","style":{"color":{"text":"var:preset|color|muted"}}} /-->

		<!-- wp:paragraph {"fontSize":"small","style":{"color":{"text":"var:preset|color|muted"}}} -->
		<p class="has-text-color has-small-font-size" style="color:var(--wp--preset--color--muted)">· <?php esc_html_e( 'All rights reserved.', 'blocklane' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

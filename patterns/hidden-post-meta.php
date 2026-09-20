<?php
/**
 * Title: Hidden — Post Meta
 * Slug: blocklane/hidden-post-meta
 * Inserter: no
 *
 * Byline built from the modern author blocks (core/post-author is
 * deprecated and rendered the name unlinked); the "by" text and the rest of
 * the strings translate here.
 *
 * @package blocklane
 * @since   0.4.0
 */

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"},"typography":{"fontSize":"var:preset|font-size|small"},"color":{"text":"var:preset|color|muted"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group has-text-color" style="color:var(--wp--preset--color--muted);font-size:var(--wp--preset--font-size--small)">
	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:avatar {"size":32,"style":{"border":{"radius":"var:preset|border-radius|full"}}} /-->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><?php echo esc_html_x( 'by', 'Precedes the post author name.', 'blocklane' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:post-author-name {"isLink":true,"fontSize":"small"} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:post-date {"fontSize":"small"} /-->
	<!-- wp:post-terms {"term":"category","fontSize":"small"} /-->
</div>
<!-- /wp:group -->

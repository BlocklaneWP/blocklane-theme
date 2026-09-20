<?php
/**
 * Title: Hidden — Sidebar
 * Slug: blocklane/hidden-sidebar
 * Inserter: no
 *
 * Sidebar content with translatable strings. Headings are h2 (not h3): when
 * comments are closed with none existing, core renders the comments block —
 * and its h2 title — as empty, so an h3 here would skip a heading level on
 * the theme's own default settings.
 *
 * @package blocklane
 * @since   0.4.0
 */

?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40)">
	<!-- wp:search {"label":"<?php echo esc_attr_x( 'Search', 'Search form label.', 'blocklane' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr_x( 'Search…', 'Search input placeholder.', 'blocklane' ); ?>","buttonText":"<?php echo esc_attr_x( 'Go', 'Sidebar search submit button.', 'blocklane' ); ?>"} /-->

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Recent Posts', 'blocklane' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:latest-posts {"postsToShow":5,"displayPostDate":true} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Categories', 'blocklane' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:categories {"showPostCounts":true} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Tags', 'blocklane' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:tag-cloud /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<?php
/**
 * Template used to display post content on single ml_collections pages.
 *
 * @package storefront
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	do_action( 'storefront_single_post_top' );

	/**
	 * Functions hooked into storefront_single_ml_collections action
	 *
	 * @hooked storefront_post_header          - 10
	 * @hooked storefront_post_content         - 30
	 */
	// do_action( 'storefront_single_post' );
	do_action( 'storefront_single_ml_collections' );

	?>

</article><!-- #post-## -->

<?php
/**
 * The template for displaying all single Collections posts.
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) :
				the_post();

				do_action( 'storefront_single_post_before' );

				get_template_part( 'content', 'ml_collections' );

				do_action( 'storefront_single_post_after' );

			endwhile; // End of the loop.
			?>
			
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

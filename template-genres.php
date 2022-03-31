<?php
/**
 * The template for displaying the Genres page.
 *
 * This page template will display any functions hooked into the `genres` action.
 *
 * Template name: Genres
 *
 * @package storefront
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php
      /**
       * Functions hooked in to genre action
       *
       * @hooked storefront_genres_content - 10
       */
      do_action( 'genres' );
      ?>

    </main><!-- #main -->
  </div><!-- #primary -->
<?php
get_footer();

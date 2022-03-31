<?php
/**
 * The template for displaying the Favorites.
 *
 * This page template will display any functions hooked into the `favorites` action.
 *
 * Template name: Favorites
 *
 * @package storefront
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php
      /**
       * Functions hooked in to favorites action
       *
       * @hooked storefront_favorites_content - 10
       */
      do_action( 'favorites' );
      ?>

    </main><!-- #main -->
  </div><!-- #primary -->
<?php
get_footer();

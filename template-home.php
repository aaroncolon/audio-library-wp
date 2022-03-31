<?php
/**
 * The template for displaying the Home page.
 *
 * This page template will display any functions hooked into the `home` action.
 *
 * Template name: Home
 *
 * @package storefront
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php
      /**
       * Functions hooked in to home action
       *
       * @hooked storefront_home_content - 10
       */
      do_action( 'home' );
      ?>

    </main><!-- #main -->
  </div><!-- #primary -->
<?php
get_footer();

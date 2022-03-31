<?php
/**
 * The template for displaying the Projects.
 *
 * This page template will display any functions hooked into the `projects` action.
 *
 * Template name: Projects
 *
 * @package storefront
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php
      /**
       * Functions hooked in to projects action
       *
       * @hooked storefront_projects_content - 10
       */
      do_action( 'projects' );
      ?>

    </main><!-- #main -->
  </div><!-- #primary -->
<?php
get_footer();

<?php
/**
 * The template for displaying the Audio Library.
 *
 * This page template will display any functions hooked into the `audio-library` action.
 *
 * Template name: Audio Library
 *
 * @package storefront
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php
      /**
       * Functions hooked in to audio_library action
       *
       * @hooked storefront_audio_library_content - 10
       */
      do_action( 'audio_library' );
      ?>

    </main><!-- #main -->
  </div><!-- #primary -->
<?php
get_footer();

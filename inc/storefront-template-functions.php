<?php

/**
 * Display Audio Library content
 * Hooked into the `audio_library` action in the Audio Library template
 *
 * @since  1.0.0
 * @return  void
 */

/**
 * Display Home content
 * Hooked into the `home` action in the Home template
 *
 * @since  1.0.0
 * @return  void
 */
 function storefront_home_content() {
   while ( have_posts() ) {
     the_post();

     get_template_part( 'content', 'home' );

   } // end of the loop.
 }

/**
 * Display Audio Library content
 * Hooked into the `audio_library` action in the Audio Library template
 *
 * @since  1.0.0
 * @return  void
 */
function storefront_audio_library_content() {
  while ( have_posts() ) {
    the_post();

    get_template_part( 'content', 'audio-library' );

  } // end of the loop.
}

/**
 * Display Favorites content
 * Hooked into the `favorites` action in the Favorites template
 *
 * @since  1.0.0
 * @return  void
 */
function storefront_favorites_content() {
  while ( have_posts() ) {
    the_post();

    get_template_part( 'content', 'favorites' );

  } // end of the loop.
}

/**
 * Display Projects content
 * Hooked into the `projects` action in the Projects template
 *
 * @since  1.0.0
 * @return  void
 */
function storefront_projects_content() {
  while ( have_posts() ) {
    the_post();

    get_template_part( 'content', 'projects' );

  } // end of the loop.
}

/**
 * Display Genres content
 * Hooked into the `genres` action in the Genres template
 *
 * @since  1.0.0
 * @return  void
 */
function storefront_genres_content() {
  while ( have_posts() ) {
    the_post();

    get_template_part( 'content', 'genres' );

  } // end of the loop.
}

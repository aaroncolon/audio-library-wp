<?php
function ml_wp_robots_meta($robots) {
  // Collections
  if (is_singular('ml_collections')) {
    $robots['noindex'] = true;
    $robots['nofollow'] = true;
    $robots['noarchive'] = true;
  }

  return $robots;
}
add_filter( 'wp_robots', 'ml_wp_robots_meta' );

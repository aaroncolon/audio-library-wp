<?php
/**
 * Add preconnect hints
 */
function ml_resource_hints($hints, $relation_type) {
  if ('dns-prefetch' === $relation_type) {
    // remove Google Fonts dns-prefetch
    $key_google_fonts = array_search('fonts.googleapis.com', $hints, true);
    if ($key_google_fonts !== false) {
      unset($hints[$key_google_fonts]);
    }
    // $hints[] = '//www.google-analytics.com';
  }
  else if ('preconnect' === $relation_type) {
    // add Google Fonts preconnect
    $hints[] = array(
      'href' => 'https://fonts.gstatic.com',
      'crossorigin' => '' // '' same as 'anonymous' @NOTE https://html.spec.whatwg.org/multipage/urls-and-fetching.html#cors-settings-attributes
    );
  }
  return $hints;
}
add_filter( 'wp_resource_hints', 'ml_resource_hints', 10, 2 );

/**
 * Remove Unecessary Items from wp_head
 */

function ml_remove_head_links() {
  remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
  remove_action('wp_head', 'wp_generator'); // remove wordpress version

  remove_action('wp_head', 'feed_links', 2); // remove rss feed links
  remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

  remove_action('wp_head', 'index_rel_link'); // remove link to index page
  remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)

  remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
  remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
  remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
}
add_action( 'init', 'ml_remove_head_links' );

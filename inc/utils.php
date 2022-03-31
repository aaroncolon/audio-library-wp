<?php
/**
 * Returns page id by template file name
 *
 * @param string $template name of template file including .php
 * @return array
 */
function get_page_id_by_template( $template ) {
  $args = [
    'post_type'      => 'page',
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'nopaging'       => true,
    'meta_key'       => '_wp_page_template',
    'meta_value'     => $template
  ];
  $pages = get_posts( $args );
  return $pages;
}

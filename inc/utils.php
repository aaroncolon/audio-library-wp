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


/**
 * Retrieves an array of the class names for the html element.
 *
 * @param string|string[] $class Space-separated string or array of class names to add to the class list.
 * @return string[] Array of class names.
 */
function get_html_class($class = '') {
  $classes = array();

  if ( ! empty( $class ) ) {
    if ( ! is_array( $class ) ) {
      $class = preg_split( '#\s+#', $class );
    }
    $classes = array_merge( $classes, $class );
  } else {
    // Ensure that we always coerce class to being an array.
    $class = array();
  }

  $classes = array_map( 'esc_attr', $classes );

  /**
   * Filters the list of CSS html class names for the current post or page.
   *
   * @param string[] $classes An array of html class names.
   * @param string[] $class   An array of additional class names added to the html element.
   */
  $classes = apply_filters( 'html_class', $classes, $class );

  return array_unique($classes);
}

/**
 * Displays the class names for the html element.
 *
 * @param string|string[] $class Space-separated string or array of class names to add to the class list.
 */
function html_class($class = '') {
  echo 'class="' . esc_attr( implode( ' ', get_html_class($class) ) ) . '"';
}

<?php
/**
 * Dynamically Populate Select Field with WooCommerce Products (songs) 
 */
function ml_acf_load_song_field_choices($field) {
  // reset choices
  $field['choices'] = array();

  // get WooCommerce Products
  $args = array(
      'post_type'      => 'product',
      'posts_per_page' => -1,
      'orderby'        => 'post_title',
      'order'          => 'ASC'
  );
  $products = new WP_Query($args);

  if ($products->have_posts()) {
    while ($products->have_posts()) {
      $products->the_post();
      $field['choices'][get_the_ID()] = esc_html(get_the_title());
    }
    wp_reset_postdata();
  }

  return $field;
}
// add_filter('acf/load_field/name=ml_song', 'ml_acf_load_song_field_choices');
add_filter('acf/load_field/name=ml_default_song', 'ml_acf_load_song_field_choices');
add_filter('acf/load_field/name=ml_song_sub_repeater', 'ml_acf_load_song_field_choices');

function ml_acf_load_genres_field_choices($field) {
  // reset choices
  $field['choices'] = array();

  $genres = get_terms(array(
    'taxonomy' => 'pa_genre'
  ));

  foreach ($genres as $genre) {
    $field['choices'][$genre->term_taxonomy_id] = $genre->name;
  }

  return $field;
}
add_filter('acf/load_field/name=show_hide_genres', 'ml_acf_load_genres_field_choices');
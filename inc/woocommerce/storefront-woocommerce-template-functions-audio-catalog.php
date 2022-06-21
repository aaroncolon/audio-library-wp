<?php
/**
 * WooCommerce Audio Library Template Functions.
 *
 * @package storefront
 */

/**
 * Audio Library Filters
 * Output the Audio Library filters
 *
 * @since   1.0.0
 *
 * @param   array $taxonomies The Taxonomies to display as filters
 * @return  void
 */
function storefront_audio_library_filters($taxonomies = array()) {
  $taxonomies = array('artist', 'duration', 'genre', 'instrument', 'mood', 'tempo');

  $attribute_array = array();
  $attribute_terms = array();
  $attribute_taxonomies = wc_get_attribute_taxonomies();

  // Get non-variation attributes (taxonomies)
  if ( ! empty( $attribute_taxonomies ) ) {
    foreach ( $attribute_taxonomies as $tax ) {
      if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) && in_array( $tax->attribute_name, $taxonomies ) ) {
        $attribute_array[ $tax->attribute_name ] = $tax->attribute_label;
      }
    }
  }

  // get terms
  if ( ! empty( $attribute_array ) ) {
    foreach ( $attribute_array as $k => $v ) {
      $attribute_terms[$k] = get_terms( array('taxonomy' => wc_attribute_taxonomy_name($k), 'hide_empty' => false) );
    }
  }

  if ( ! empty( $attribute_terms ) ) {
    echo '<form id="music-list-filters" class="music-list-filters">';
      foreach ( $attribute_terms as $key => $terms ) {
        echo '<div class="select-wrap music-list-filters__select-wrap">';
          $attr_label = wc_attribute_label(wc_attribute_taxonomy_name($key));
          echo '<label for="select-'. wc_attribute_taxonomy_name($key) .'" class="visuallyhidden">'. esc_html($attr_label) .'</label>';
          echo '<select id="select-'. wc_attribute_taxonomy_name($key) .'" name="'. esc_attr($key) .'" data-taxonomy-name="'. wc_attribute_taxonomy_name($key) .'">';
            // use key as label and first option
            echo '<option value="">'. esc_html( $attr_label ) .'...</option>';
            foreach ( $terms as $term ) {
              echo '<option value="'. esc_attr( $term->slug ) .'">'. esc_html( $term->name ) .'</option>';
            }
          echo '</select>';
        echo '</div>';
      }
      echo '<input type="submit" class="button music-list-filters__submit" value="Filter">';
    echo '</form>';
  }

  // echo '<pre>';
  //   var_dump(wc_attribute_label(wc_attribute_taxonomy_name('duration')));
  // echo '</pre>';
  // echo '<pre>Attr Tax Labels';
  //   var_dump(wc_get_attribute_taxonomy_labels());
  // echo '</pre>';
  // echo '<pre>Attr Array';
  //   var_dump($attribute_array);
  // echo '</pre>';
  // echo '<pre>Attr Terms';
  //   var_dump($attribute_terms);
  // echo '</pre>';
  // echo '<pre>Attr Terms';
  //   var_dump($attribute_taxonomies);
  // echo '</pre>';

  // $product = wc_get_product('64');

  // // check if product is variable
  // if ($product->is_type('variable')) {
  //   $res = array(
  //     'var_attrs'  => $product->get_variation_attributes(),
  //     'variations' => $product->get_available_variations(),
  //   );

  //   // $variations = $product->get_available_variations();
  // }

  // echo '<pre>';
  //   // print_r($variations);
  //   var_dump($res);
  // echo '</pre>';

  // echo '<pre>';
  //   // var_dump(wc_attribute_label('pa_production-budget'));
  //   // var_dump(wc_get_attribute_taxonomy_labels());

  //   // $test = get_term_by('slug', 'customer-type-1', 'pa_customer-type');
  //   $test = get_terms( array('taxonomy' => 'pa_campus-size', 'hide_empty' => false) );
  //   var_dump($test);
  // echo '</pre>';
}


/**
 * Audio Library List
 * Output the Audio Library list
 *
 * @since   1.0.0
 * @return  void
 */
function storefront_audio_library_list() {
?>
  <div
    id="music-list"
    class="music-list"
    data-nonce-products="<?php echo esc_attr(ml_do_nonce('get_products')) ?>"
    data-nonce-favorites="<?php echo esc_attr(ml_do_nonce('get_favorites')) ?>"
    data-nonce-create-favorite="<?php echo esc_attr(ml_do_nonce('create_favorite')) ?>"
    data-nonce-delete-favorite="<?php echo esc_attr(ml_do_nonce('delete_favorite')) ?>"
    <?php if (get_field('ml_monetization_model', 'options') === 'membership') : ?>
    data-nonce-get-user-downloads="<?php echo esc_attr(ml_do_nonce('get_user_downloads')) ?>"
    <?php endif; ?>>
    <div id="music-list__table">
      <div class="music-list__body"></div>
    </div>

    <div class="music-list__pagination">
      <button class="btn btn--load-more" style="display:none;">
        Load More
      </button>
    </div>
  </div>

  <?php
  if (get_monetization_model() === 'licensing') {
    require get_stylesheet_directory() .'/inc/dialogs/dialog-license.php';
  }

  if (get_monetization_model() === 'membership') {
    require get_stylesheet_directory() .'/inc/dialogs/dialog-download.php';
  }
}

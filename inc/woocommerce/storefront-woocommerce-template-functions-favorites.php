<?php
/**
 * WooCommerce Favorites Template Functions.
 *
 * @package storefront
 */

/**
 * Favorites Filters
 * Output the Audio Library filters
 *
 * @since   1.0.0
 *
 * @param   array $taxonomies The Taxonomies to display as filters
 * @return  void
 */
function storefront_favorites_filters($taxonomies = array()) {
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
}


/**
 * Favorites List
 * Output the Favorites list
 *
 * @since   1.0.0
 * @return  void
 */
function storefront_favorites_list() {
?>
  <div
    id="music-list"
    class="music-list"
    data-nonce-products="<?php echo esc_attr(ml_do_nonce('get_products')) ?>"
    data-nonce-favorites="<?php echo esc_attr(ml_do_nonce('get_favorites')) ?>"
    data-nonce-create-favorite="<?php echo esc_attr(ml_do_nonce('create_favorite')) ?>"
    data-nonce-delete-favorite="<?php echo esc_attr(ml_do_nonce('delete_favorite')) ?>">
    <div id="music-list__table">
      <div class="music-list__body"></div>
    </div>

    <div class="music-list__pagination">
      <button class="btn btn--load-more" style="display:none;">
        Load More
      </button>
    </div>
  </div>

  <div id="license-dialog" class="license-dialog mfp-hide" data-nonce-product="<?php echo esc_attr(ml_do_nonce('get_product')) ?>">
    <h3 class="title--license-dialog">License Details</h3>
    <div class="license-dialog__song-details clear">
      <div class="license-dialog__song-image">
        <a href="javascript:;" class="license-dialog__song-link">
          <img src="#" alt="" />
        </a>
      </div>
      <div class="license-dialog__song-text">
        <div class="license-dialog__song-title"></div>
        <div class="license-dialog__song-artist"></div>
      </div>
    </div>
    <div class="license-form-wrap">
      <form id="license-form" class="license-form" action="" method="post">
        <div id="product-variations" class="product-variations"></div>

        <div class="license-form__summary">
          <div id="product-variation-price" class="product-variation-price"></div>
          <button id="btn-add-to-cart" class="btn btn--add-to-cart" disabled data-nonce-add-to-cart-variation="<?php echo esc_attr(ml_do_nonce('add_to_cart_variation')) ?>">Add to Cart</button>
        </div>
      </form>
    </div>
  </div>

<?php
}

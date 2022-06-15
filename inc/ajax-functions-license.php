<?php
/**
 * Get Product Variations
 */
function ml_get_product_variations_handler() {
  check_ajax_referer('ml_get_product_nonce', 'nonce');

  $result = array();

  $id = sanitize_text_field($_POST['id']);

  if (! isset($id)) {
    wp_send_json_error();
  }

  $transient = get_transient('ml_get_product_variations_'. $id);
  if (!empty($transient)) :

    wp_send_json_success($transient);

  else :

    $product = wc_get_product($id);

    // check if product is variable
    if ($product->is_type('variable')) {
      $variation_attrs  = $product->get_variation_attributes();
      $variation_labels = array();
      $variations       = $product->get_available_variations();
      $term_data        = array();

      // Get Variation Attribute Labels
      foreach ($variation_attrs as $k => $v) {
        $variation_labels[$k] = wc_attribute_label($k);
      }

      // // Get Attribute Terms (names)
      // foreach ($variation_attrs as $k => $v) {
      //   $term_data = get_terms( array('taxonomy' => $k, 'hide_empty' => false) );
      // }

      // Get Attribute Terms (names)
      foreach ($variation_attrs as $tax => $terms) {
        foreach ($terms as $term) {
          $t = get_term_by( 'slug', $term, $tax );
          $term_data[$term] = $t->name;
        }
      }

      $result['product_id']       = $id;
      $result['variation_attrs']  = $variation_attrs;
      $result['variation_labels'] = $variation_labels;
      $result['variations']       = $variations;
      $result['term_data']        = $term_data;
    }

    set_transient('ml_get_product_variations_'. $id, $result, HOUR_IN_SECONDS);

    wp_send_json_success($result);
  endif;
}
add_action('wp_ajax_ml_get_product_variations', 'ml_get_product_variations_handler');
add_action('wp_ajax_nopriv_ml_get_product_variations', 'ml_get_product_variations_handler');

/**
 * Add Variation To Cart
 */
function ml_add_to_cart_variation_hanlder() {
  check_ajax_referer('ml_add_to_cart_variation_nonce', 'nonce');

  $result = array();

  $product_id        = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
  $quantity          = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
  $variation_id      = absint(sanitize_text_field($_POST['variation_id']));
  $variation         = json_decode(stripslashes(sanitize_text_field($_POST['variation_data'])), true);
  // create array from variation_data
  $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

  // add to cart
  $result['add_to_cart'] = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

  if ($passed_validation && $result['add_to_cart']) {

    do_action('woocommerce_ajax_added_to_cart', $product_id);

    if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
      wc_add_to_cart_message( $product_id );
    }

    WC_AJAX::get_refreshed_fragments();
  } else {
    $data = array(
      'error' => true,
      'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id)
    );
    wp_send_json_error($data);
  }

  $result['product_id']   = $product_id;
  $result['quantity']     = $quantity;
  $result['variation_id'] = $variation_id;
  $result['variation']    = $variation;
  wp_send_json_success($result);
}
add_action('wp_ajax_ml_add_to_cart_variation', 'ml_add_to_cart_variation_hanlder');
add_action('wp_ajax_nopriv_ml_add_to_cart_variation', 'ml_add_to_cart_variation_hanlder');

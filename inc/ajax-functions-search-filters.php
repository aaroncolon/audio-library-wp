<?php

function ml_get_posts_handler() {
  // contentType application/json can only be read from php://input

  // Verify nonce
  check_ajax_referer('ml_get_products_nonce', 'nonce');

  $result             = array();
  $result['products'] = array();
  $page               = (! empty($_POST['page'])) ? intval(sanitize_text_field($_POST['page'])) : 1;
  $page_template      = sanitize_text_field($_POST['pageTemplate']);
  $filters            = json_decode(stripslashes($_POST['filters']), true); // quotes are escaped during POST
  $filter_args        = array();
  $tax_query          = array();
  $tax_query_merged   = array();

  $tax_query = array(
    'relation' => 'AND',
    array(
      'field'    => 'name',
      'operator' => 'NOT IN',
      'taxonomy' => 'product_visibility',
      'terms'    => array( 'exclude-from-catalog' ),
    )
  );

  if (!empty($filters)) :
    // Verify filters
    foreach ($filters as $v) :
      if (! isset($v['taxName']) || ! isset($v['termValue'])) {
        wp_send_json_error();
        break;
      }
      sanitize_text_field($v['taxName']);
      sanitize_text_field($v['termValue']);
    endforeach;

    // Build Taxonomy Query
    foreach ($filters as $v) :
      $filter_args[] = array(
        'taxonomy' => $v['taxName'],
        'terms'    => array( $v['termValue'] ),
        'field'    => 'slug',
        'operator' => 'IN',
      );
    endforeach;

    $tax_query_merged = array_merge($tax_query, $filter_args);
  endif;

  // Build Query
  $args = array(
    'paginate'       => true,
    'posts_per_page' => 10,
    'page'           => $page,
    // 'return'         => 'ids',
    'tax_query'      => $tax_query_merged,
  );

  // User's Favorites
  if ($page_template === 'template-favorites.php') {
    $user      = wp_get_current_user();
    $user_id   = $user->ID;
    $favorites = get_user_meta($user_id, 'ml_favorites', true);
    $fav_ids   = array();

    if ( count($favorites) ) {
      foreach ($favorites as $k => $v) {
        $fav_ids[] = $k;
      }
      $args['include'] = $fav_ids;
    } else {
      $result['total']         = 0;
      $result['max_num_pages'] = 0;
      $result['page']          = $page;
      wp_send_json_success($result);
    }
  }

  // Query
  $transient = get_transient( 'ml_get_posts_' . md5( json_encode($args) ) );

  if (!empty($transient)) :

    wp_send_json_success($transient);

  else :

    $q = new WC_Product_Query($args);
    $q_res = $q->get_products();
    $products = $q_res->products;

    // Get Product Data
    if (count($products)) :
      foreach($products as $product) :
        $id = $product->id;
        $downloads = array();

        if ($product->is_type('variable')) :

          // use downloads from first variation
          $variations = $product->get_available_variations();
          $variation = wc_get_product($variations[0]['variation_id']);
          $variation_id = $variation->get_id();
          $variation_dls = $variation->get_downloads();
          
          foreach ($variation_dls as $key => $download) :
            $download_data = $download->get_data();
            $download_name = $download->get_name(); // File label name
            $download_file = $download->get_file(); // File Url
            $download_id   = $download->get_id(); // File Id (same as $key)
            $download_type = $download->get_file_type(); // File type
            $download_ext  = $download->get_file_extension(); // File extension
  
            $file_path = parse_url($download_file, PHP_URL_PATH);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
  
            $downloads[] = array(
              // 'data' => $download_data,
              'key'  => $key,
              'name' => $download_name,
              'file' => $download_file,
              'type' => $download_type,
              'path' => $file_path,
              'file_exists' => file_exists($file_path),
              'file_size' => filesize($file_path),
              'basename' => basename($file_path),
            );
          endforeach;

        elseif ($product->is_type('simple')) :

          foreach ($product->get_downloads() as $key => $download) :
            $download_data = $download->get_data();
            $download_name = $download->get_name(); // File label name
            $download_file = $download->get_file(); // File Url
            $download_id   = $download->get_id(); // File Id (same as $key)
            $download_type = $download->get_file_type(); // File type
            $download_ext  = $download->get_file_extension(); // File extension
  
            $file_path = parse_url($download_file, PHP_URL_PATH);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
  
            $downloads[] = array(
              // 'data' => $download_data,
              'key'  => $key,
              'name' => $download_name,
              'file' => $download_file,
              'type' => $download_type,
              'path' => $file_path,
              'file_exists' => file_exists($file_path),
              'file_size' => filesize($file_path),
              'basename' => basename($file_path),
            );
          endforeach;

        endif;

        $result['products'][] = array(
          'id'               => $id,
          'title'            => get_the_title($id),
          'songImage'       => wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full')[0],
          'artist'           => wc_get_product_terms($id, 'pa_artist', array('fields' => 'names'))[0],
          'length'           => wc_get_product_terms($id, 'pa_duration', array('fields' => 'names'))[0],
          'genre'            => wc_get_product_terms($id, 'pa_genre', array('fields' => 'names'))[0],
          'inst'             => wc_get_product_terms($id, 'pa_instrument', array('fields' => 'names')),
          'mood'             => wc_get_product_terms($id, 'pa_mood', array('fields' => 'names')),
          'tempo'            => wc_get_product_terms($id, 'pa_tempo', array('fields' => 'names'))[0],
          'previewSongUrl' => get_field('preview_song_file', $id),
          'downloads'        => $downloads,
          'membershipAccess' => pmpro_has_membership_access($id, null, false),
          'variation' => $variation,
          'variationId' => $variation_id,
        );
      endforeach;

      $result['total']         = $q_res->total;
      $result['max_num_pages'] = $q_res->max_num_pages;
      $result['page']          = $page;

      // $result['filters_raw'] = $_POST['filters'];
      // $result['filters'] = $filters;
      // $result['filter_args'] = $filter_args;
      // $result['tax_query'] = $tax_query;
      // $result['tax_query_merged'] = $tax_query_merged;
    endif;

    // $result['page']        = $page;
    // $result['filters_raw'] = $_POST['filters'];
    // $result['filters']     = $filters;

    set_transient('ml_get_posts_' . md5( json_encode($args) ), $result, HOUR_IN_SECONDS);

    wp_send_json_success($result);

  endif;
}
add_action('wp_ajax_ml_get_posts', 'ml_get_posts_handler');
add_action('wp_ajax_nopriv_ml_get_posts', 'ml_get_posts_handler');

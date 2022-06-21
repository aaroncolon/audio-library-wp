<?php

/**
 * Get user's downloads
 */
function ml_get_user_downloads_handler() {
  check_ajax_referer('ml_get_user_downloads_nonce', 'nonce');

  $result = array();

  $user_id = get_current_user_id();

  $transient = get_transient('ml_downloads_'. $user_id);

  if (!empty($transient)) :

    wp_send_json_success($transient);

  else :

    $downloads = get_user_meta($user_id, 'ml_downloads', true);

    if (! empty($downloads)) {
      $result = $downloads;
      set_transient('ml_downloads_'. $user_id, $result, 30 * DAY_IN_SECONDS);
    }

    wp_send_json_success($result);

  endif;
}
add_action('wp_ajax_ml_get_user_downloads', 'ml_get_user_downloads_handler');
add_action('wp_ajax_nopriv_ml_get_user_downloads', 'ml_get_user_downloads_handler');

/**
 * Create user's download
 */
function ml_create_user_download_handler() {
  check_ajax_referer('ml_create_user_download_nonce', 'nonce');

  $result = array();

  $user_id   = get_current_user_id();
  $downloads = get_user_meta($user_id, 'ml_downloads', true); // @return empty string if doesn't exist
  $song_id   = sanitize_text_field($_POST['songId']);

  // delete transient
  delete_transient('ml_downloads_'. $user_id);

  // initialize $downloads as array
  if (empty($downloads)) {
    $downloads = array();
  }

  // add new download to $downloads array
  if (! isset($downloads[$song_id])) {
    $downloads[$song_id] = $song_id;
  }

  $meta_res = update_user_meta($user_id, 'ml_downloads', $downloads);

  $result['meta_res'] = $meta_res;
  wp_send_json_success($result);
}
add_action('wp_ajax_ml_create_user_download', 'ml_create_user_download_handler');
add_action('wp_ajax_nopriv_ml_create_user_download', 'ml_create_user_download_handler');

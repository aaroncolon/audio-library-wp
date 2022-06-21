<?php

/**
 * Get user's downloads
 */
function ml_get_user_downloads() {
  $result = array();
  $user_id = get_current_user_id();

  $transient = get_transient('ml_downloads_'. $user_id);
  
  if (! empty($transient)) {
    return $transient;
  }

  $downloads = get_user_meta($user_id, 'ml_downloads', true);

  if (! empty($downloads)) {
    $result = $downloads;
    set_transient('ml_downloads_'. $user_id, $downloads, 30 * DAY_IN_SECONDS);
  }

  return $result;
}

/**
 * Add Product ID to user's downloads list
 */
function ml_create_user_download($product_id) {
  $result = array();

  $user_id =  get_current_user_id();
  $downloads = get_user_meta($user_id, 'ml_downloads', true); // @return empty string if doesn't exist

  // delete transient
  delete_transient('ml_downloads_'. $user_id);

  // initialize $downloads as array
  if (empty($downloads)) {
    $downloads = array();
  }

  // add new download to $downloads array
  if (! isset($downloads[$product_id])) {
    $downloads[$product_id] = $product_id;
  }

  $meta_res = update_user_meta($user_id, 'ml_downloads', $downloads);

  $result['meta_res'] = $meta_res;

  return $result;
}

/**
 * Alias: Track user's download
 */
function ml_track_download($product_id) {
  return ml_create_user_download($product_id);
}
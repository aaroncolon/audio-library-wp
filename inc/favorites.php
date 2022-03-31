<?php
function ml_get_favorites() {
  $favorites = null;
  $user_id = get_current_user_id();

  if (! $user_id) {
    return $favorites;
  }

  $transient = get_transient('ml_favorites_'. $user_id);

  if (! empty($transient)) {
    $favorites = $transient;
  } else {
    $favorites = get_user_meta($user_id, 'ml_favorites', true);
    set_transient('ml_favorites_'. $user_id, $favorites, 7 * DAY_IN_SECONDS);
  }

  return $favorites;
}

function ml_is_favorite($id, $favorites) {
  return (is_array($favorites) && !empty($favorites[$id])) ? true : false;
}

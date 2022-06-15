<?php
function ml_get_download_files_handler() {
  check_ajax_referer('ml_get_download_files', 'nonce');

  $id = absint(sanitize_text_field($_POST['id']));
  $pid = absint(sanitize_text_field($_POST['pid']));
  if (! isset($id) || ! isset($pid)) {
    wp_send_json_error();
  }

  $result = array();

  $transient = get_transient('ml_get_download_files_'. md5( json_encode($id, $pid, get_current_user_id()) ));
  if (! empty($transient)) :

    wp_send_json_success($transient);

  else :

    // S3 Presigned URL
    // $result['surl'] = ml_get_s3_presigned_request();

    // Build Download URL
    $d_url = add_query_arg(
      array(
        'download_file' => $id,
        'pid'           => $pid,
        'email'         => get_current_user_id(),
      ),
      home_url( '/' )
    );

    $result['d_url'] = $d_url;

    set_transient('ml_get_download_files'. md5( json_encode($id, $pid, get_current_user_id()) ), $result, HOUR_IN_SECONDS);

    wp_send_json_success($result);

  endif;
}
add_action('wp_ajax_ml_get_download_files', 'ml_get_download_files_handler');
add_action('wp_ajax_nopriv_ml_get_download_files', 'ml_get_download_files_handler');

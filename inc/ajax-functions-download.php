<?php
function ml_get_download_files_handler() {
  check_ajax_referer('ml_get_download_files', 'nonce');

  $id  = absint($_POST['id']);
  $pid = absint($_POST['pid']);
  $key = sanitize_text_field($_POST['key']);

  if (! isset($id, $pid, $key) ) {
    wp_send_json_error();
  }

  $result = array();
  $user = wp_get_current_user();
  $user_email = $user->user_email;
  $user_id = $user->ID;
  $transient_ttl = HOUR_IN_SECONDS;

  $transient = get_transient('ml_get_download_files_'. md5( json_encode(array($id, $pid, $user_id)) ));
  if (! empty($transient)) :

    wp_send_json_success($transient);

  else :

    if (get_field('ml_downloads_storage_type', 'option') === 'aws-s3') :

      // Build S3 Presigned Request URL
      $d_url = ml_get_s3_presigned_request();

      $transient_ttl = get_field('ml_aws_s3_presigned_req_ttl', 'option');
    
    else :

      // Build Download URL
      $d_url = add_query_arg(
        array(
          'download_file' => $id,
          'pid'           => $pid, // pmpro_has_membership_access() uses Parent ID
          'email'         => $user_email,
          'key'           => $key
        ),
        home_url( '/' )
      );

      $result['d_url'] = $d_url;
    
    endif;

    set_transient('ml_get_download_files'. md5( json_encode(array($id, $pid, $user_id)) ), $result, $transient_ttl);

    wp_send_json_success($result);

  endif;
}
add_action('wp_ajax_ml_get_download_files', 'ml_get_download_files_handler');
add_action('wp_ajax_nopriv_ml_get_download_files', 'ml_get_download_files_handler');

<?php

function ml_share_email_handler() {
  // contentType application/json can only be read from php://input

  // Verify nonce
  check_ajax_referer('ml_share_email_nonce');

  $result  = false;
  $name    = sanitize_text_field($_POST['name']);
  $to      = sanitize_email($_POST['email']);
  $subject = sanitize_text_field($_POST['subject']);
  $message = sanitize_textarea_field($_POST['message']);
  // $url     = filter_var($_POST['url'], FILTER_SANITIZE_URL);
  $url     = sanitize_text_field($_POST['url']);
  $data    = json_decode(stripslashes($_POST['collectionData']), true); // quotes are escaped during POST

  if ( empty($name) || empty($to) || empty($subject) || empty($message) || empty($url) || empty($data) ) {
    wp_send_json_error('Required Form Fields Missing.');
  }

  $finalMessage = storefront_collections_build_email($subject, $message, $url, $data);

  $from = $name .' <'. wp_get_current_user()->user_email .'>';

  $headers = array(
    'From: '. $from,
    'Reply-To: '. $from,
    'Content-Type: text/html; charset=UTF-8'
  );

  $result = wp_mail($to, $subject, $finalMessage, $headers);

  if ($result) {
    wp_send_json_success($result);
  } else {
    wp_send_json_error(
      array(
        'Email Failed To Send.',
        $to,
        $subject,
        $headers,
        $data,
        $finalMessage
      )
    );
  }
}
add_action('wp_ajax_ml_share_email', 'ml_share_email_handler');
add_action('wp_ajax_nopriv_ml_share_email', 'ml_share_email_handler');

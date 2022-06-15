<?php

if ( isset( $_GET['download_file'] ) && isset( $_GET['pid'] ) && isset( $_GET['email'] ) ) { // WPCS: input var ok, CSRF ok.
  add_action( 'init', 'ml_download_product' );
}

function ml_download_product() {
  $user_id = get_current_user_id();

  if ( ! is_user_logged_in() || $user_id !== absint($_GET['email']) ) { 
    download_error( __( 'Invalid download link.', 'ml-textdomain' ) );
  }

  $product_id = absint(sanitize_text_field($_GET['pid']));

  if ( ! pmpro_has_membership_access($product_id) ) {
    download_error( __( 'Invalid download link.', 'ml-textdomain' ) );
  }

  // get Download data
  $downloads = array();
  $product = wc_get_product($product_id);

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
      $download_name = $download->get_name(); // File label name
      $download_file = $download->get_file(); // File Url
      $download_id   = $download->get_id(); // File Id (same as $key)
      $download_type = $download->get_file_type(); // File type
      $download_ext  = $download->get_file_extension(); // File extension
      $file_path = parse_url($download_file, PHP_URL_PATH);
      $file_path2 = $_SERVER['DOCUMENT_ROOT'] . $file_path;

      $downloads[] = array(
        'key'  => $key,
        'name' => $download_name,
        'file' => $download_file,
        'type' => $download_type,
        'path' => $file_path2,
        'basename' => basename($file_path2),
      );
    endforeach;
  
  endif;

  $_file_path = $download_file;
  $_filename = basename($download_file);

  // if ( is_ssl() && ! empty( $GLOBALS['is_IE'] ) ) {
  //   $headers['Cache-Control'] = 'private';
  //   unset( $headers['Pragma'] );
  // }

  download_file_xsendfile($_file_path, $_filename);
}

/**
 * Download a file using X-Sendfile, X-Lighttpd-Sendfile, or X-Accel-Redirect if available.
 *
 * @param string $file_path File path.
 * @param string $filename  File name.
 */
function download_file_xsendfile( $file_path, $filename ) {
  $parsed_file_path = parse_file_path($file_path);

  if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_xsendfile', apache_get_modules(), true ) ) {

    // self::download_headers( $parsed_file_path['file_path'], $filename );

    // Clean all OBs
    if ( ob_get_level() ) {
			$levels = ob_get_level();
			for ( $i = 0; $i < $levels; $i++ ) {
				@ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		} else {
			@ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

    nocache_headers();

    download_headers($parsed_file_path['file_path'], $filename);

    $filepathFinal = $parsed_file_path['file_path'];

    header( 'X-Sendfile: ' . $filepathFinal );  
    exit;
  } else {
    // Fallback to Force download
    download_file_force($file_path, $filename);
  }
}

function get_download_content_type( $file_path ) {
  $file_extension = strtolower( substr( strrchr( $file_path, '.' ), 1 ) );
  $ctype          = 'application/force-download';

  foreach ( get_allowed_mime_types() as $mime => $type ) {
    $mimes = explode( '|', $mime );
    if ( in_array( $file_extension, $mimes, true ) ) {
      $ctype = $type;
      break;
    }
  }

  return $ctype;
}

/**
 * Parse file path and see if its remote or local.
 *
 * @param  string $file_path File path.
 * @return array
 */
function parse_file_path( $file_path ) {
  $wp_uploads     = wp_upload_dir();
  $wp_uploads_dir = $wp_uploads['basedir'];
  $wp_uploads_url = $wp_uploads['baseurl'];

  /**
   * Replace uploads dir, site url etc with absolute counterparts if we can.
   * Note the str_replace on site_url is on purpose, so if https is forced
   * via filters we can still do the string replacement on a HTTP file.
   */
  $replacements = array(
    $wp_uploads_url                                                   => $wp_uploads_dir,
    network_site_url( '/', 'https' )                                  => ABSPATH,
    str_replace( 'https:', 'http:', network_site_url( '/', 'http' ) ) => ABSPATH,
    site_url( '/', 'https' )                                          => ABSPATH,
    str_replace( 'https:', 'http:', site_url( '/', 'http' ) )         => ABSPATH,
  );

  $count            = 0;
  $file_path        = str_replace( array_keys( $replacements ), array_values( $replacements ), $file_path, $count );
  $parsed_file_path = wp_parse_url( $file_path );
  $remote_file      = null === $count || 0 === $count; // Remote file only if there were no replacements.

  // Paths that begin with '//' are always remote URLs.
  if ( '//' === substr( $file_path, 0, 2 ) ) {
    $file_path = ( is_ssl() ? 'https:' : 'http:' ) . $file_path;

    /**
     * Filter the remote filepath for download.
     *
     * @since 6.5.0
     * @param string $file_path File path.
     */
    return array(
      'remote_file' => true,
      'file_path'   => $file_path,
    );
  }

  // See if path needs an abspath prepended to work.
  if ( file_exists( ABSPATH . $file_path ) ) {
    $remote_file = false;
    $file_path   = ABSPATH . $file_path;

  } elseif ( '/wp-content' === substr( $file_path, 0, 11 ) ) {
    $remote_file = false;
    $file_path   = realpath( WP_CONTENT_DIR . substr( $file_path, 11 ) );

    // Check if we have an absolute path.
  } elseif ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array( 'http', 'https', 'ftp' ), true ) ) && isset( $parsed_file_path['path'] ) ) {
    $remote_file = false;
    $file_path   = $parsed_file_path['path'];
  }

  /**
  * Filter the filepath for download.
  *
  * @since 6.5.0
  * @param string  $file_path File path.
  * @param bool $remote_file Remote File Indicator.
  */
  return array(
    'remote_file' => $remote_file,
    'file_path'   => $file_path,
  );
}

/**
 * Force download - this is the default method.
 *
 * @param string $file_path File path.
 * @param string $filename  File name.
 */
function download_file_force( $file_path, $filename ) {
  $parsed_file_path = parse_file_path( $file_path );
  $download_range   = get_download_range( @filesize( $parsed_file_path['file_path'] ) ); // @codingStandardsIgnoreLine.

  download_headers( $parsed_file_path['file_path'], $filename, $download_range );

  $start  = isset( $download_range['start'] ) ? $download_range['start'] : 0;
  $length = isset( $download_range['length'] ) ? $download_range['length'] : 0;
  if ( ! readfile_chunked( $parsed_file_path['file_path'], $start, $length ) ) {
    download_error( __( 'File not found', 'woocommerce' ) );
  }

  exit;
}

/**
 * Parse the HTTP_RANGE request from iOS devices.
 * Does not support multi-range requests.
 *
 * @param int $file_size Size of file in bytes.
 * @return array {
 *     Information about range download request: beginning and length of
 *     file chunk, whether the range is valid/supported and whether the request is a range request.
 *
 *     @type int  $start            Byte offset of the beginning of the range. Default 0.
 *     @type int  $length           Length of the requested file chunk in bytes. Optional.
 *     @type bool $is_range_valid   Whether the requested range is a valid and supported range.
 *     @type bool $is_range_request Whether the request is a range request.
 * }
 */
function get_download_range( $file_size ) {
  $start          = 0;
  $download_range = array(
    'start'            => $start,
    'is_range_valid'   => false,
    'is_range_request' => false,
  );

  if ( ! $file_size ) {
    return $download_range;
  }

  $end                      = $file_size - 1;
  $download_range['length'] = $file_size;

  if ( isset( $_SERVER['HTTP_RANGE'] ) ) { // @codingStandardsIgnoreLine.
    $http_range                         = sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) ); // WPCS: input var ok.
    $download_range['is_range_request'] = true;

    $c_start = $start;
    $c_end   = $end;
    // Extract the range string.
    list( , $range ) = explode( '=', $http_range, 2 );
    // Make sure the client hasn't sent us a multibyte range.
    if ( strpos( $range, ',' ) !== false ) {
      return $download_range;
    }

    /*
      * If the range starts with an '-' we start from the beginning.
      * If not, we forward the file pointer
      * and make sure to get the end byte if specified.
      */
    if ( '-' === $range[0] ) {
      // The n-number of the last bytes is requested.
      $c_start = $file_size - substr( $range, 1 );
    } else {
      $range   = explode( '-', $range );
      $c_start = ( isset( $range[0] ) && is_numeric( $range[0] ) ) ? (int) $range[0] : 0;
      $c_end   = ( isset( $range[1] ) && is_numeric( $range[1] ) ) ? (int) $range[1] : $file_size;
    }

    /*
      * Check the range and make sure it's treated according to the specs: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html.
      * End bytes can not be larger than $end.
      */
    $c_end = ( $c_end > $end ) ? $end : $c_end;
    // Validate the requested range and return an error if it's not correct.
    if ( $c_start > $c_end || $c_start > $file_size - 1 || $c_end >= $file_size ) {
      return $download_range;
    }
    $start  = $c_start;
    $end    = $c_end;
    $length = $end - $start + 1;

    $download_range['start']          = $start;
    $download_range['length']         = $length;
    $download_range['is_range_valid'] = true;
  }
  return $download_range;
}

/**
 * Set headers for the download.
 *
 * @param string $file_path      File path.
 * @param string $filename       File name.
 * @param array  $download_range Array containing info about range download request (see {@see get_download_range} for structure).
 */
function download_headers( $file_path, $filename, $download_range = array() ) {
  check_server_config();
  clean_buffers();
  nocache_headers();

  header( 'X-Robots-Tag: noindex, nofollow', true );
  header( 'Content-Type: ' . get_download_content_type( $file_path ) );
  header( 'Content-Description: File Transfer' );
  header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
  header( 'Content-Transfer-Encoding: binary' );

  $file_size = @filesize( $file_path ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
  if ( ! $file_size ) {
    return;
  }

  if ( isset( $download_range['is_range_request'] ) && true === $download_range['is_range_request'] ) {
    if ( false === $download_range['is_range_valid'] ) {
      header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
      header( 'Content-Range: bytes 0-' . ( $file_size - 1 ) . '/' . $file_size );
      exit;
    }

    $start  = $download_range['start'];
    $end    = $download_range['start'] + $download_range['length'] - 1;
    $length = $download_range['length'];

    header( 'HTTP/1.1 206 Partial Content' );
    header( "Accept-Ranges: 0-$file_size" );
    header( "Content-Range: bytes $start-$end/$file_size" );
    header( "Content-Length: $length" );
  } else {
    header( 'Content-Length: ' . $file_size );
  }
}

/**
 * Check and set certain server config variables to ensure downloads work as intended.
 */
function check_server_config() {
  wc_set_time_limit( 0 );
  if ( function_exists( 'apache_setenv' ) ) {
    @apache_setenv( 'no-gzip', 1 ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_apache_setenv
  }
  @ini_set( 'zlib.output_compression', 'Off' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_ini_set
  @session_write_close(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.VIP.SessionFunctionsUsage.session_session_write_close
}

/**
 * Read file chunked.
 *
 * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/.
 *
 * @param  string $file   File.
 * @param  int    $start  Byte offset/position of the beginning from which to read from the file.
 * @param  int    $length Length of the chunk to be read from the file in bytes, 0 means full file.
 * @return bool Success or fail
 */
function readfile_chunked( $file, $start = 0, $length = 0 ) {
  if ( ! defined( 'WC_CHUNK_SIZE' ) ) {
    define( 'WC_CHUNK_SIZE', 1024 * 1024 );
  }
  $handle = @fopen( $file, 'r' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen

  if ( false === $handle ) {
    return false;
  }

  if ( ! $length ) {
    $length = @filesize( $file ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
  }

  $read_length = (int) WC_CHUNK_SIZE;

  if ( $length ) {
    $end = $start + $length - 1;

    @fseek( $handle, $start ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
    $p = @ftell( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

    while ( ! @feof( $handle ) && $p <= $end ) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
      // Don't run past the end of file.
      if ( $p + $read_length > $end ) {
        $read_length = $end - $p + 1;
      }

      echo @fread( $handle, $read_length ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.XSS.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.file_system_read_fread
      $p = @ftell( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

      if ( ob_get_length() ) {
        ob_flush();
        flush();
      }
    }
  } else {
    while ( ! @feof( $handle ) ) { // @codingStandardsIgnoreLine.
      echo @fread( $handle, $read_length ); // @codingStandardsIgnoreLine.
      if ( ob_get_length() ) {
        ob_flush();
        flush();
      }
    }
  }

  return @fclose( $handle ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fclose
}

/**
 * Clean all output buffers.
 *
 * Can prevent errors, for example: transfer closed with 3 bytes remaining to read.
 */
function clean_buffers() {
  if ( ob_get_level() ) {
    $levels = ob_get_level();
    for ( $i = 0; $i < $levels; $i++ ) {
      @ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
    }
  } else {
    @ob_end_clean(); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
  }
}

/**
 * Die with an error message if the download fails.
 *
 * @param string  $message Error message.
 * @param string  $title   Error title.
 * @param integer $status  Error status.
 */
function download_error( $message, $title = '', $status = 404 ) {
  /*
  * Since we will now render a message instead of serving a download, we should unwind some of the previously set
  * headers.
  */
  if ( headers_sent() ) {
    wc_get_logger()->log( 'warning', __( 'Headers already sent when generating download error message.', 'ml-textdomain' ) );
  } else {
    header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
    header_remove( 'Content-Description;' );
    header_remove( 'Content-Disposition' );
    header_remove( 'Content-Transfer-Encoding' );
  }

  if ( ! strstr( $message, '<a ' ) ) {
    $message .= ' <a href="' . esc_url( get_home_url() ) . '" class="wc-forward">' . esc_html__( 'Go to home', 'ml-textdomain' ) . '</a>';
  }
  wp_die( $message, $title, array( 'response' => $status ) ); // WPCS: XSS ok.
}
<?php

require 'inc/optimize.php';
require 'inc/custom-post-types.php';
require 'inc/utils.php';
require 'inc/favicons.php';
require 'inc/robots.php';
require 'inc/acf-functions.php';
require 'inc/acf-functions-options.php';

/**
 * Custom Google Fonts
 */
function ml_storefront_google_font_families( $fonts ) {
	// Remove default Source Sans Pro
	if (array_key_exists('source-sans-pro', $fonts)) {
		unset($fonts['source-sans-pro']);
	}

	// Add custom Fonts
	// $fonts['montserrat'] = 'Montserrat:500';
	// $fonts['roboto'] = 'Roboto:400&display=swap';
	return $fonts;
}
add_filter( 'storefront_google_font_families', 'ml_storefront_google_font_families', 10, 1 );

/**
 * Dequeue Styles
 */
function ml_dequeue_styles() {
	wp_dequeue_style( 'storefront-fonts' );
}
add_action( 'wp_enqueue_scripts', 'ml_dequeue_styles', 11);

/**
 * Dequeue Scripts
 */
function ml_dequeue_scripts() {
	wp_dequeue_script( 'storefront-header-cart' );
	wp_dequeue_script( 'storefront-handheld-footer-bar' );
}
// add_action( 'wp_print_scripts', 'ml_dequeue_scripts', 10 );
add_action( 'wp_enqueue_scripts', 'ml_dequeue_scripts', 21 );

/**
 * Enqueue Scripts
 */
function ml_enqueue_scripts() {
	/**
	 * Scripts
	 */

// 	wp_enqueue_script( 'ml-events', get_stylesheet_directory_uri() . '/assets/js/src/Events.js', array(), '', true);
// 	wp_enqueue_script( 'ml-utilities', get_stylesheet_directory_uri() . '/assets/js/src/Utilities.js', array(), '', true);
// 	wp_enqueue_script( 'ml-favorites', get_stylesheet_directory_uri() . '/assets/js/src/Favorites.js', array('jquery'), '', true);
//
// 	wp_enqueue_script( 'ml-global', get_stylesheet_directory_uri() . '/assets/js/src/global.js', array(), '', true);
// 	wp_localize_script( 'ml-global', 'ml_js_data', array(
// 		'ajax_url'                    => admin_url('admin-ajax.php'),
// 		'current_song'                => array('id' => 0, 'isPlaying' => false),
// 		'default_song'                => ml_player_default_song(),
// 		'user_logged_in'              => is_user_logged_in(),
// 		'page_template_slug'          => get_page_template_slug(),
// 		'nonce_get_favorites'         => wp_create_nonce('ml_get_favorites_nonce'),
// 		'nonce_create_favorite'       => wp_create_nonce('ml_create_favorite_nonce'),
// 		'nonce_delete_favorite'       => wp_create_nonce('ml_delete_favorite_nonce'),
// 		'nonce_get_product'           => wp_create_nonce('ml_get_product_nonce'),
// 		'nonce_add_to_cart_variation' => wp_create_nonce('ml_add_to_cart_variation_nonce'))
// 	);
//
// 	// Audio Library || Projects || Favorites
// 	if ( is_page_template( array('template-audio-library.php', 'template-favorites.php', 'template-projects.php') ) ) :
// 		wp_enqueue_script( 'ml-magnific-popup', get_stylesheet_directory_uri() . '/assets/js/src/vendor/magnific-popup/magnific-popup.min.js', array('jquery'), '', true);
// 		wp_enqueue_script( 'ml-wavesurfer', get_stylesheet_directory_uri() . '/assets/js/src/vendor/wavesurfer/wavesurfer.min.js', array('jquery'), '', true);
// 		wp_enqueue_script( 'ml-player', get_stylesheet_directory_uri() . '/assets/js/src/player.js', array('jquery'), '', true);
// 		wp_enqueue_script( 'ml-favorites-header', get_stylesheet_directory_uri() . '/assets/js/src/favoritesHeader.js', array('jquery'), '', true);
// 	endif;
//
// 	// Audio Library || Favorites
// 	if ( is_page_template( array('template-audio-library.php', 'template-favorites.php') ) ) :
// 		wp_enqueue_script( 'ml-audio-library-list', get_stylesheet_directory_uri() . '/assets/js/src/musicLibraryList.js', array('jquery'), '', true);
// 		wp_enqueue_script( 'ml-audio-library-filters', get_stylesheet_directory_uri() . '/assets/js/src/musicLibraryFilters.js', array('jquery', 'wp-util'), '', true);
// 		wp_enqueue_script( 'ml-license-dialog', get_stylesheet_directory_uri() . '/assets/js/src/licenseDialog.js', array('jquery'), '', true);
// 	endif;
//
// 	// Projects
// 	if (is_page_template('template-projects.php')) :
// 		wp_enqueue_script( 'ml-matchheight', get_stylesheet_directory_uri() . '/assets/js/src/vendor/match-height/jquery.matchHeight-min.js', array('jquery'), '', true);
// 		wp_enqueue_script( 'ml-popup-player', get_stylesheet_directory_uri() . '/assets/js/src/popupPlayer.js', array('jquery'), '', true);
// 	endif;
//

	wp_enqueue_script( 'ml-magnific-popup', get_stylesheet_directory_uri() . '/assets/js/src/vendor/magnific-popup/magnific-popup.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'ml-wavesurfer', get_stylesheet_directory_uri() . '/assets/js/src/vendor/wavesurfer/custom/wavesurfer.js', array('jquery'), '6.0.4', true);
	wp_enqueue_script( 'ml-matchheight', get_stylesheet_directory_uri() . '/assets/js/src/vendor/match-height/jquery.matchHeight-min.js', array('jquery'), '', true);

	wp_enqueue_script( 'ml-main', get_stylesheet_directory_uri() . '/assets/js/dist/bundle.js', array('jquery', 'wp-util'), '', true);
	
	$ml_js_data = array(
		'ajax_url'                 => admin_url('admin-ajax.php'),
		'current_song'             => array('id' => 0, 'isPlaying' => false),
		'default_song'             => ml_player_default_song(),
		'default_image'						 => ml_player_default_image(),
		'user_logged_in'           => is_user_logged_in(),
		'page_template_slug'       => get_page_template_slug(),
		'customer_type_individual' => get_field('ml_individual_customer_type', 'option'),
		'monetization_model'       => get_field('ml_monetization_model', 'option'),
		'membership_data'					 => null,
		'user_id'									 => get_current_user_id(),
		'urls'										 => array(
			'wc_my_account' => esc_attr( add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) ),
			'pmpro_login'		=> null,
			'pmpro_levels' 	=> null 
		)
	);

	// Add PMPro data
	if ( defined( 'PMPRO_VERSION' ) && is_plugin_active('paid-memberships-pro/paid-memberships-pro.php') ) {
		if (get_field('ml_monetization_model', 'option') === 'membership') {
			$ml_js_data['membership_data'] = (pmpro_getMembershipLevelForUser(get_current_user_id())) ? pmpro_getMembershipLevelForUser(get_current_user_id()) : null;
			$ml_js_data['urls']['pmpro_login'] = esc_attr( add_query_arg('redirect', rawurlencode(get_the_permalink()), pmpro_url('login')) );
			$ml_js_data['urls']['pmpro_levels'] = esc_attr( add_query_arg('redirect', rawurlencode(get_the_permalink()), pmpro_url('levels')) );
		}
	}

	wp_localize_script( 'ml-main', 'ml_js_data', $ml_js_data );
}
add_action( 'wp_enqueue_scripts', 'ml_enqueue_scripts', 20 );

function ml_do_nonce($nonceName = '') {
	$nonces = array(
		'get_products'          => wp_create_nonce('ml_get_products_nonce'),
		'get_product'           => wp_create_nonce('ml_get_product_nonce'),
		'get_favorites'         => wp_create_nonce('ml_get_favorites_nonce'),
		'get_user_downloads'		=> wp_create_nonce('ml_get_user_downloads_nonce'),
		'create_user_download'	=> wp_create_nonce('ml_create_user_download_nonce'),
		'create_favorite'       => wp_create_nonce('ml_create_favorite_nonce'),
		'delete_favorite'       => wp_create_nonce('ml_delete_favorite_nonce'),
		'add_to_cart_variation' => wp_create_nonce('ml_add_to_cart_variation_nonce'),
		'get_download_files'		=> wp_create_nonce('ml_get_download_files'),
		'download_file'					=> wp_create_nonce('ml_download_file'),
	);
	return ($nonceName && $nonces[$nonceName]) ? $nonces[$nonceName] : $nonces;
}

function ml_js_templates() {
	require 'inc/templates/music-list-row.php';
}
add_action( 'wp_footer', 'ml_js_templates' );

require 'inc/remove-parent-actions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

/**
 * The header container
 */
function storefront_header_container() {
	echo '<div class="col-full col-full--full-width">';
}

/**
 * Display the site title or logo
 *
 * @since 2.1.0
 * @param bool $echo Echo the string or return it.
 * @return string
 */
function storefront_site_title_or_logo( $echo = true ) {
	if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
		$logo = get_custom_logo();
		$html = is_front_page() ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
	} else {
		$tag = is_front_page() ? 'h1' : 'div';

		// add custom filter `ml_bloginfo`
		$bloginfo_name = apply_filters( 'ml_bloginfo', get_bloginfo( 'name' ) );

		$html = '<' . esc_attr( $tag ) . ' class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . wp_kses_post( $bloginfo_name ) . '</a></' . esc_attr( $tag ) . '>';

		if ( '' !== get_bloginfo( 'description' ) ) {
			$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
		}
	}

	if ( ! $echo ) {
		return $html;
	}

	echo $html; // WPCS: XSS ok.
}

/**
 * Edit the Site Name on front page
 */
function ml_filter_bloginfo_name( $output ) {
	if ( ! is_admin() ) {
		if ( ! empty($output) ) {
			$pieces = explode(' ', $output);
		}
		$output = '<span>'. implode('</span> <span>', $pieces) .'</span>';
	}
	return $output;
}
add_filter( 'ml_bloginfo', 'ml_filter_bloginfo_name', 10, 1 );

/**
 * Add custom html element classes
 */
function ml_html_class($classes) {
	$full_height = get_field('full_height', get_the_ID());
	if ($full_height) {
		$classes = array_merge($classes, array('full-height'));
	}
	return $classes;
}
add_filter('html_class', 'ml_html_class');

/**
 * Add custom body classes
 */
function ml_body_class($classes) {
	$hide_title = get_field('hide_title', get_the_ID());
	if ($hide_title) {
		$classes = array_merge($classes, array('page--entry-title-hide'));
	}
	return $classes;
}
add_filter('body_class', 'ml_body_class');

function storefront_homepage_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$hero_title = get_post_meta( get_the_ID(), 'ml_hero_lede_title', true );
	$hero_subtitle = get_post_meta( get_the_ID(), 'ml_hero_lede_subtitle', true );
	?>
	<div id="hero" class="site-hero hentry">
		<div class="site-hero__image-wrap">
			<?php storefront_post_thumbnail( 'full' ); ?>
		</div>
		<div class="site-hero__lede-wrap">
			<h1 class="site-hero__lede-title"><?php echo wp_kses_post($hero_title) ?></h1>
			<p class="site-hero__lede-subtitle"><?php echo wp_kses_post($hero_subtitle) ?></p>
		</div>
	</div>
<?php
}

/**
 * Edit the Featured Products section title
 */
function ml_storefront_featured_products_title( $args ) {
	if ( ! empty($args) ) {
		$args['limit']   = 2;
		$args['columns'] = 2;
		$args['title']   = __( 'Featured Products', 'storefront' );
	}
	return $args;
}
add_filter( 'storefront_featured_products_args', 'ml_storefront_featured_products_title', 10, 1 );

/**
 * Remove the credit link
 */
function ml_storefront_credit_link( $show ) {
	return false;
}
add_filter( 'storefront_credit_link', 'ml_storefront_credit_link', 10, 1 );

/**
 * WooCommerce includes
 */
if ( class_exists('woocommerce') ) {
	require 'inc/acf-functions-wc.php';	// prepopulate ACF fields with WC data
	require 'inc/favorites.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-dialogs.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-home.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-audio-catalog.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-favorites.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-genres.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-projects.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions-collections.php';
	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/ajax-functions-search-filters.php';
	require 'inc/ajax-functions-favorites.php';
	require 'inc/ajax-functions-license.php';
	require 'inc/ajax-functions-share.php';
}

/**
 * Paid Memberships Pro includes
 */
if ( defined( 'PMPRO_VERSION' ) && is_plugin_active('paid-memberships-pro/paid-memberships-pro.php') ) {
	if (get_field('ml_downloads_storage_type', 'option') === 'aws-s3') {
		require 'inc/aws-s3.php';
	}

	require 'inc/pmpro-functions.php';
	require 'inc/ajax-functions-download.php';
	require 'inc/ajax-functions-user-downloads.php';
	require 'inc/user-downloads.php';
	require 'inc/downloads.php';
}

// // Add Sign Up or Log In button to content-single-product.php
// add_action('woocommerce_single_product_summary', 'ml_wc_display_sign_up_log_in_message', 32);
// function ml_wc_display_sign_up_log_in_message() {
//   // Display link to My Account page
//   if (!is_user_logged_in()) {
//     echo '<p><a href="'. add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount')) .'" class="button" aria-label="Log In or Sign Up" rel="nofollow">Log In or Register</a></p>';
//   }
// }

add_action('woocommerce_login_form', 'ml_wc_login_form_redirect');
function ml_wc_login_form_redirect() {
  if ( empty($_GET['redirect']) ) {
    return;
  }

  $query_url = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL);
  $query_url = ($query_url) ? rawurldecode($query_url) : null;

  if ($query_url) {
    echo '<input type="hidden" name="redirect" id="redirect" value="'. esc_url($query_url) .'">';
  }
}

add_action('woocommerce_register_form', 'ml_wc_register_form_redirect');
function ml_wc_register_form_redirect() {
	if ( empty($_GET['redirect']) ) {
		return;
	}

	$query_url = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL);
	$query_url = ($query_url) ? rawurldecode($query_url) : null;

	if ($query_url) {
		echo '<input type="hidden" name="redirect" id="redirect" value="'. esc_url($query_url) .'">';
	}
}

/**
 * WooCommerce change continue shopping URL
 */
function ml_continue_shopping_redirect( $url ) {
	return get_permalink(get_page_id_by_template('template-audio-library.php')[0]);
}
add_filter( 'woocommerce_continue_shopping_redirect', 'ml_continue_shopping_redirect' );

/**
 * WooCommerce change return to shop URL
 */
function ml_return_to_shop_redirect( $wc_get_page_permalink ) {
	return get_permalink(get_page_id_by_template('template-audio-library.php')[0]);
}
add_filter( 'woocommerce_return_to_shop_redirect', 'ml_return_to_shop_redirect' );

/**
 * Default song data
 * @return array default song data
 */
function ml_player_default_song() {
	$id = get_field('ml_default_song', 'option');

	$song_image = get_post_thumbnail_id($id);
	$song_image = ($song_image) ? wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full')[0] : null;

	$artist = wc_get_product_terms($id, 'pa_artist', array('fields' => 'names'));
	$length = wc_get_product_terms($id, 'pa_duration', array('fields' => 'names'));
	$genre  = wc_get_product_terms($id, 'pa_genre', array('fields' => 'names'));
	$inst   = wc_get_product_terms($id, 'pa_instrument', array('fields' => 'names'));
	$mood   = wc_get_product_terms($id, 'pa_mood', array('fields' => 'names'));
	$tempo  = wc_get_product_terms($id, 'pa_tempo', array('fields' => 'names'));

	return array(
		'id'               => $id,
		'title'            => get_the_title($id),
		'song_image'       => $song_image,
		'artist'           => (count($artist) > 0) ? $artist[0] : null,
		'length'           => (count($length) > 0) ? $length[0] : null,
		'genre'            => (count($genre) > 0) ? $genre[0] : null,
		'inst'             => (count($inst) > 0) ? $inst[0] : null,
		'mood'             => (count($mood) > 0) ? $mood[0] : null,
		'tempo'            => (count($tempo) > 0) ? $tempo[0] : null,
		'preview_song_url' => get_field('preview_song_file', $id),
	);
}

/**
 * Default image data
 * @return string default image url
 */
function ml_player_default_image() {
	$image = get_field('ml_default_image', 'option');
	return $image['url'];
}

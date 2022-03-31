<?php

require 'inc/optimize.php';
require 'inc/custom-post-types.php';
require 'inc/utils.php';
require 'inc/favicons.php';
require 'inc/robots.php';

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
	wp_localize_script( 'ml-main', 'ml_js_data', array(
		'ajax_url'                 => admin_url('admin-ajax.php'),
		'current_song'             => array('id' => 0, 'isPlaying' => false),
		'default_song'             => ml_player_default_song(),
		'user_logged_in'           => is_user_logged_in(),
		'page_template_slug'       => get_page_template_slug(),
		'customer_type_individual' => true) // enable Individual Customer Type handling
	);

}
add_action( 'wp_enqueue_scripts', 'ml_enqueue_scripts', 20 );

function ml_do_nonce($nonceName = '') {
	$nonces = array(
		'get_products'          => wp_create_nonce('ml_get_products_nonce'),
		'get_product'           => wp_create_nonce('ml_get_product_nonce'),
		'get_favorites'         => wp_create_nonce('ml_get_favorites_nonce'),
		'create_favorite'       => wp_create_nonce('ml_create_favorite_nonce'),
		'delete_favorite'       => wp_create_nonce('ml_delete_favorite_nonce'),
		'add_to_cart_variation' => wp_create_nonce('ml_add_to_cart_variation_nonce'),
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

if ( class_exists('woocommerce') ) {
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

/* Advanced Custom Fields */
/* Dynamically Populate Select Field with WooCommerce Products (songs) */
function ml_acf_load_song_field_choices($field) {
  // reset choices
  $field['choices'] = array();

  // get WooCommerce Products
  $args = array(
      'post_type'      => 'product',
      'posts_per_page' => -1,
      'orderby'        => 'post_title',
      'order'          => 'ASC'
  );
  $products = new WP_Query($args);

  if ($products->have_posts()) {
    while ($products->have_posts()) {
      $products->the_post();
      $field['choices'][get_the_ID()] = esc_html(get_the_title());
    }
    wp_reset_postdata();
  }

  return $field;
}
// add_filter('acf/load_field/name=ml_song', 'ml_acf_load_song_field_choices');
add_filter('acf/load_field/name=ml_default_song', 'ml_acf_load_song_field_choices');
add_filter('acf/load_field/name=ml_song_sub_repeater', 'ml_acf_load_song_field_choices');

/**
 * Add ACF Options Page
 */
function ml_add_acf_options_pages() {
	if ( function_exists('acf_add_options_page') ) {
		acf_add_options_page(array(
			'page_title' => 'Theme General Settings',
			'menu_title' => 'Theme Settings',
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'manage_options',
			'redirect'	 => false
		));
	}
}
add_action('acf/init', 'ml_add_acf_options_pages');

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
 * Get Page ID by Template Name
 * @param {string} full filename of template
 * @return {array} IDs of pages assigned the template
 */
function ml_get_id_by_template($templateName) {
	$args = array(
    'post_type'  => 'page',
    'fields'     => 'ids',
    'nopaging'   => true,
    'meta_key'   => '_wp_page_template',
    'meta_value' => $templateName
	);
	$pages = get_posts($args);
	return $pages;
}

function ml_player_default_song() {
	$id = get_field('ml_default_song', 'option');

	return array(
		'id'               => $id,
		'title'            => get_the_title($id),
		'song_image'       => wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full')[0],
		'artist'           => wc_get_product_terms($id, 'pa_artist', array('fields' => 'names'))[0],
		'length'           => wc_get_product_terms($id, 'pa_duration', array('fields' => 'names'))[0],
		'genre'            => wc_get_product_terms($id, 'pa_genre', array('fields' => 'names'))[0],
		'inst'             => wc_get_product_terms($id, 'pa_instrument', array('fields' => 'names'))[0],
		'mood'             => wc_get_product_terms($id, 'pa_mood', array('fields' => 'names'))[0],
		'tempo'            => wc_get_product_terms($id, 'pa_tempo', array('fields' => 'names'))[0],
		'preview_song_url' => get_field('preview_song_file', $id),
	);
}

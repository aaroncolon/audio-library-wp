<?php
/**
 * Storefront WooCommerce hooks
 *
 * @package storefront
 */

/**
 * Header
 *
 * @see storefront_header_cart()
 * @see storfront_header_favorites()
 */
add_action( 'storefront_header', 'storefront_header_cart', 29 );
add_action( 'storefront_header', 'storefront_header_favorites', 29);

/**
 * Home
 *
 * @see  storefront_home()
 */
add_action( 'home', 'storefront_home', 20 );
add_action( 'homepage', 'storefront_home', 5 );

/**
 * Audio Library
 *
 * @see  storefront_audio_library_filters()
 * @see  storefront_audio_library_list()
 */
add_action( 'audio_library', 'storefront_audio_library_filters', 20 );
add_action( 'audio_library', 'storefront_audio_library_list', 30 );

/**
 * Favorites
 *
 * @see  storefront_favorites_filters()
 * @see  storefront_favorites_list()
 */
add_action( 'favorites', 'storefront_favorites_filters', 20 );
add_action( 'favorites', 'storefront_favorites_list', 30 );

/**
 * Projects
 *
 * @see  storefront_projects()
 */
add_action( 'projects', 'storefront_projects', 20 );

/**
 * Genres
 *
 * @see  storefront_genres()
 */
add_action( 'genres', 'storefront_genres', 20 );

/**
 * Collections
 *
 * @see  storefront_collections()
 */
add_action( 'storefront_single_ml_collections', 'storefront_collections_content', 40 ); 
// add_action( 'storefront_single_ml_collections', 'storefront_collections_share', 40 );
// add_action( 'storefront_single_ml_collections', 'storefront_collections_filters', 40 );
// add_action( 'storefront_single_ml_collections', 'storefront_collections_list', 40 );

/**
 * Footer
 *
 * @see ml_player()
 * @see storefront_confirm_dialog()
 */
add_action( 'storefront_before_footer', 'ml_player', 10 );
add_action( 'storefront_after_footer', 'storefront_confirm_dialog', 10);

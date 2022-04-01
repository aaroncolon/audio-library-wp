<?php
/**
 * Storefront hooks
 *
 * @package storefront
 */

/**
 * Header
 *
 * @see  storefront_primary_navigation()
 */
add_action( 'storefront_header', 'storefront_primary_navigation', 30 );

// add_action( 'storefront_before_content', 'storefront_homepage_hero', 5 );

/**
 * Home
 *
 * @see  storefront_home_content()
 */
add_action( 'home', 'storefront_home_content', 10 );

/**
 * Home Page Template (default page content)
 *
 * @see  storefront_page_header()
 * @see  storefront_page_content()
 */
add_action( 'storefront_home', 'storefront_page_header', 10 );
add_action( 'storefront_home', 'storefront_page_content', 20 );

/**
 * Audio Library
 *
 * @see  storefront_audio_library_content()
 */
add_action( 'audio_library', 'storefront_audio_library_content', 10 );

/**
 * Audio Library Page Template (default page content)
 *
 * @see  storefront_page_header()
 * @see  storefront_page_content()
 */
// add_action( 'storefront_audio_library', 'storefront_page_header', 10 );
// add_action( 'storefront_audio_library', 'storefront_page_content', 20 );

/**
 * Favorites
 *
 * @see  storefront_favorites_content()
 */
add_action( 'favorites', 'storefront_favorites_content', 10 );

/**
 * Favorites Page Template (default page content)
 *
 * @see  storefront_page_header()
 * @see  storefront_page_content()
 */
add_action( 'storefront_favorites', 'storefront_page_header', 10 );
add_action( 'storefront_favorites', 'storefront_page_content', 20 );

/**
 * Projects
 *
 * @see  storefront_projects_content()
 */
add_action( 'projects', 'storefront_projects_content', 10 );

/**
 * Projects Page Template (default page content)
 *
 * @see  storefront_page_header()
 * @see  storefront_page_content()
 */
add_action( 'storefront_projects', 'storefront_page_header', 10 );
add_action( 'storefront_projects', 'storefront_page_content', 20 );


/**
 * Genres
 *
 * @see  storefront_genres_content()
 */
add_action( 'genres', 'storefront_genres_content', 10 );

/**
 * Genres Page Template (default page content)
 *
 * @see  storefront_page_header()
 * @see  storefront_page_content()
 */
add_action( 'storefront_genres', 'storefront_page_header', 10 );
add_action( 'storefront_genres', 'storefront_page_content', 20 );

/**
 * Collections
 *
 * @see  storefront_post_header()
 * @see  storefront_post_meta()
 * @see  storefront_post_content()
 * @see  storefront_paging_nav()
 * @see  storefront_single_post_header()
 * @see  storefront_post_nav()
 * @see  storefront_display_comments()
 */
add_action( 'storefront_single_ml_collections', 'storefront_post_header', 10 );
add_action( 'storefront_single_ml_collections', 'storefront_post_content', 30 );

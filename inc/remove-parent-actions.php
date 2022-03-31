<?php
/**
 * Storefront Remove Actions From Parent Theme
 *
 * @package storefront
 */

add_action( 'init', 'ml_remove_actions_parent_theme' );
function ml_remove_actions_parent_theme() {
	/**
	 * Storefront hooks
	 *
	 * @package storefront
	 */
	//* inc/storefront-template-hooks.php

	/**
   * Header
   *
   * @see  storefront_secondary_navigation()
   * @see  storefront_site_branding()
   * @see  storefront_primary_navigation()
   */
  remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
  remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
  remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
  remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );


	/**
	 * Storefront WooCommerce hooks
	 *
	 * @package storefront
	 */
  //* inc/woocommerce/storefront-woocommerce-template-hooks.php

	/**
	 * Homepage
	 *
	 * @see  storefront_popular_products()
	 * @see  storefront_on_sale_products()
	 * @see  storefront_best_selling_products()
	 */
	// remove_action( 'homepage', 'storefront_popular_products', 50 );
	// remove_action( 'homepage', 'storefront_on_sale_products', 60 );
	// remove_action( 'homepage', 'storefront_best_selling_products', 70 );

	/**
	 * Header
	 *
	 * @see storefront_product_search()
	 * @see storefront_header_cart()
	 */
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_cart', 60 );

  /**
   * Homepage
   *
   * @see  storefront_homepage_content()
   */
  // remove_action( 'homepage', 'storefront_homepage_content', 10 );
  remove_action( 'homepage', 'storefront_product_categories', 20 );
  remove_action( 'homepage', 'storefront_recent_products', 30 );
  remove_action( 'homepage', 'storefront_popular_products', 50 );
  remove_action( 'homepage', 'storefront_on_sale_products', 60 );
  remove_action( 'homepage', 'storefront_best_selling_products', 70 );

  /**
   * Layout
   *
   * @see  woocommerce_breadcrumb()
   */
  remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );

  /**
   * Homepage Page Template
   *
   * @see  storefront_homepage_header()
   * @see  storefront_page_content()
   */
  // remove_action( 'storefront_homepage', 'storefront_homepage_header', 10 );
  // remove_action( 'storefront_homepage', 'storefront_page_content', 20 );

  /**
   * Footer
   */
  remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );

}

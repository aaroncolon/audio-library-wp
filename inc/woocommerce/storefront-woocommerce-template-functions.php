<?php

function ml_get_favorites_total() {
	if ( ! is_user_logged_in() ) {
		return 0;
	}

	$user      = wp_get_current_user();
	$user_id   = $user->ID;
	$favorites = get_user_meta($user_id, 'ml_favorites', true);

	return count($favorites);
}

/**
 * Display Header Favorites
 *
 * @since  1.0.0
 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
 * @return void
 */
function storefront_header_favorites() {
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_page_template('template-favorites.php') ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-favorites" class="site-header-favorites menu">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php storefront_favorites_link(); ?>
			</li>
		</ul>
	<?php
	}
}

/**
 * Display Favorites link
 */
function storefront_favorites_link() {
	$url = (is_user_logged_in()) ? get_site_url(null, '/favorites/') : add_query_arg('redirect', rawurlencode(get_the_permalink()), wc_get_page_permalink('myaccount'));
	?>
	<a class="site-header-favorites__link" href="<?php echo esc_url($url) ?>" title="<?php esc_attr_e( 'View your Favorites', 'storefront' ) ?>" data-favorites="<?php esc_attr_e(ml_get_favorites_total()) ?>">
		<span class="visuallyhidden">Favorites</span>
	</a>
	<?php
}

/**
 * Display Header Cart
 *
 * @since  1.0.0
 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
 * @return void
 */
function storefront_header_cart() {
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
	<ul id="site-header-cart" class="site-header-cart menu">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php storefront_cart_link(); ?>
		</li>
	</ul>
		<?php
	}
}

/**
 * Cart Link
 * Displayed a link to the cart including the number of items present and the cart total
 *
 * @return void
 * @since  1.0.0
 */
function storefront_cart_link() {
	?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
			<?php /* translators: %d: number of items in cart */ ?>
			<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'storefront' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
		</a>
	<?php
}

/**
 * Display Footer Player
 *
 * @return void
 * @since  1.0.0
 */
function ml_player() {
	$page_templates = array(
		'template-audio-library.php',
		'template-favorites.php',
		'template-projects.php',
	);

	$single_templates = array(
		'ml_collections'
	);
	
	if ( is_page_template( $page_templates ) || is_singular( $single_templates ) ) :
		get_template_part('inc/player');
	endif;
}

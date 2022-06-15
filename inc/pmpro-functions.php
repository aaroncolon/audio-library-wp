<?php
/**
 * Add the PMPro meta box to a CPT
 */
function my_add_pmpro_meta_box_to_cpts() {
	// Duplicate this row for each CPT. This one adds the meta boxes to 'product' CPTs.
	add_meta_box('pmpro_page_meta', 'Require Membership', 'pmpro_page_meta', 'product', 'side' );
}
add_action( 'admin_menu', 'my_add_pmpro_meta_box_to_cpts', 20 );

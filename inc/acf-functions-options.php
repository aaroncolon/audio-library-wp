<?php
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

<?php
function ml_register_cpt() {
  $ml_projects_labels = array(
    'name'               => _x( 'Projects', 'post type general name', 'ml-textdomain' ),
    'singular_name'      => _x( 'Project', 'post type singular name', 'ml-textdomain' ),
    'menu_name'          => _x( 'Projects', 'admin menu', 'ml-textdomain' ),
    'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'ml-textdomain' ),
    'add_new'            => _x( 'Add New', 'Project', 'ml-textdomain' ),
    'add_new_item'       => __( 'Add New Project', 'ml-textdomain' ),
    'new_item'           => __( 'New Project', 'ml-textdomain' ),
    'edit_item'          => __( 'Edit Project', 'ml-textdomain' ),
    'view_item'          => __( 'View Project', 'ml-textdomain' ),
    'all_items'          => __( 'All Projects', 'ml-textdomain' ),
    'search_items'       => __( 'Search Projects', 'ml-textdomain' ),
    'parent_item_colon'  => __( 'Parent Projects:', 'ml-textdomain' ),
    'not_found'          => __( 'No Projects found.', 'ml-textdomain' ),
    'not_found_in_trash' => __( 'No Projects found in Trash.', 'ml-textdomain' )
  );

  register_post_type('ml_projects', array(
    'labels'              => $ml_projects_labels,
    'description'         => '',
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'menu_position'       => 7,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array('slug' => 'projects', 'with_front' => false),
    'query_var'           => true,
    'supports'            => array('title','editor','thumbnail')
  ));

  $ml_collections_labels = array(
    'name'               => _x( 'Collections', 'post type general name', 'ml-textdomain' ),
    'singular_name'      => _x( 'Collection', 'post type singular name', 'ml-textdomain' ),
    'add_new'            => _x( 'Add New', 'Collection', 'ml-textdomain' ),
    'add_new_item'       => __( 'Add New Collection', 'ml-textdomain' ),
    'new_item'           => __( 'New Collection', 'ml-textdomain' ),
    'edit_item'          => __( 'Edit Collection', 'ml-textdomain' ),
    'view_item'          => __( 'View Collection', 'ml-textdomain' ),
    'view_items'         => __( 'View Collections', 'ml-textdomain' ),
    'search_items'       => __( 'Search Collections', 'ml-textdomain' ),
    'not_found'          => __( 'No Collections found.', 'ml-textdomain' ),
    'not_found_in_trash' => __( 'No Collections found in Trash.', 'ml-textdomain' ),
    'parent_item_colon'  => __( 'Parent Collections:', 'ml-textdomain' ),
    'all_items'          => __( 'All Collections', 'ml-textdomain' ),
  );

  register_post_type('ml_collections', array(
    'labels'              => $ml_collections_labels,
    'description'         => '',
    'has_archive'         => false,
    'public'              => true,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => false,
    'show_in_admin_bar'   => true,
    'menu_position'       => 7,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'hierarchical'        => false,
    'rewrite'             => array('slug' => 'collections', 'with_front' => false),
    'query_var'           => true,
    'supports'            => array('title','editor','thumbnail','custom-fields')
  ));
}
add_action( 'init', 'ml_register_cpt' );

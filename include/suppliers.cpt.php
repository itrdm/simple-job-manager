<?php


	$labels = array(
		'name'                => _x( 'Supplier', 'Post Type General Name', 'simple-job-managment' ),
		'singular_name'       => _x( 'Supplier', 'Post Type Singular Name', 'simple-job-managment' ),
		'menu_name'           => __( 'Suppliers', 'simple-job-managment' ),
		'parent_item_colon'   => __( 'Parent Supplier :', 'simple-job-managment' ),
		'all_items'           => __( 'Suppliers', 'simple-job-managment' ),
		'view_item'           => __( 'View Supplier', 'simple-job-managment' ),
		'add_new_item'        => __( 'Add Supplier', 'simple-job-managment' ),
		'add_new'             => __( 'Add Supplier', 'simple-job-managment' ),
		'edit_item'           => __( 'Edit Supplier', 'simple-job-managment' ),
		'update_item'         => __( 'Update Supplier', 'simple-job-managment' ),
		'search_items'        => __( 'Search Supplier', 'simple-job-managment' ),
		'not_found'           => __( 'No Supplier found', 'simple-job-managment' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'simple-job-managment' ),
	);

	$args = array(
		'label'               => __( 'rdm_supplier', 'simple-job-managment' ),
		'description'         => __('Suppliers', 'simple-job-managment' ),
		'labels'              => $labels,
		"supports" 			  => array( "title", "thumbnail" ),
		'taxonomies'          => array( 'Job_taxonomy', 'product_brand' ),
		'hierarchical'        => false,
		"rewrite" 			  => array( "slug" => "suppliers", "with_front" => true ),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=rdm_job',
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 75,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		"map_meta_cap" => true,
		
	);
	register_post_type( 'rdm_supplier', $args );

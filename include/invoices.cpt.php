<?php


	$labels = array(
		'name'                => _x( 'Invoice', 'Post Type General Name', 'simple-job-managment' ),
		'singular_name'       => _x( 'Invoice', 'Post Type Singular Name', 'simple-job-managment' ),
		'menu_name'           => __( 'Invoices', 'simple-job-managment' ),
		'parent_item_colon'   => __( 'Parent Invoice :', 'simple-job-managment' ),
		'all_items'           => __( 'Invoices', 'simple-job-managment' ),
		'view_item'           => __( 'View Invoice', 'simple-job-managment' ),
		'add_new_item'        => __( 'Add Invoice', 'simple-job-managment' ),
		'add_new'             => __( 'Add Invoice', 'simple-job-managment' ),
		'edit_item'           => __( 'Edit Invoice', 'simple-job-managment' ),
		'update_item'         => __( 'Update Invoice', 'simple-job-managment' ),
		'search_items'        => __( 'Search Invoice', 'simple-job-managment' ),
		'not_found'           => __( 'No Invoice found', 'simple-job-managment' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'simple-job-managment' ),
	);

	$args = array(
		'label'               => __( 'rdm_invoice', 'simple-job-managment' ),
		'description'         => __('Invoices', 'simple-job-managment' ),
		'labels'              => $labels,
		'supports'            => array(),
		'taxonomies'          => array( 'Job_taxonomy' ),
		'hierarchical'        => false,
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
		'capability_type'     => 'page',
		
	);
	register_post_type( 'rdm_invoice', $args );

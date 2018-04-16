<?php


	$labels = array(
		'name'                => _x( 'Client', 'Post Type General Name', 'simple-job-managment' ),
		'singular_name'       => _x( 'Client', 'Post Type Singular Name', 'simple-job-managment' ),
		'menu_name'           => __( 'Clients', 'simple-job-managment' ),
		'parent_item_colon'   => __( 'Parent Client :', 'simple-job-managment' ),
		'all_items'           => __( 'Clients', 'simple-job-managment' ),
		'view_item'           => __( 'View Client', 'simple-job-managment' ),
		'add_new_item'        => __( 'Add Client', 'simple-job-managment' ),
		'add_new'             => __( 'Add Client', 'simple-job-managment' ),
		'edit_item'           => __( 'Edit Client', 'simple-job-managment' ),
		'update_item'         => __( 'Update Client', 'simple-job-managment' ),
		'search_items'        => __( 'Search Client', 'simple-job-managment' ),
		'not_found'           => __( 'No Client found', 'simple-job-managment' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'simple-job-managment' ),
	);

	$args = array(
		'label'               => __( 'rdm_client', 'simple-job-managment' ),
		'description'         => __('Clients', 'simple-job-managment' ),
		'labels'              => $labels,
		'supports'            => array(),
		'taxonomies'          => array( 'Job_taxonomy' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=rdm_job',
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 75,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		
	);
	register_post_type( 'rdm_client', $args );

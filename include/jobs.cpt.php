<?php


	$labels = array(
		'name'                => _x( 'Job', 'Post Type General Name', 'simple-job-managment' ),
		'singular_name'       => _x( 'Job', 'Post Type Singular Name', 'simple-job-managment' ),
		'menu_name'           => __( 'Jobs', 'simple-job-managment' ),
		'parent_item_colon'   => __( 'Parent Job :', 'simple-job-managment' ),
		'all_items'           => __( 'Jobs', 'simple-job-managment' ),
		'view_item'           => __( 'View Job', 'simple-job-managment' ),
		'add_new_item'        => __( 'Add Job', 'simple-job-managment' ),
		'add_new'             => __( 'Add Job', 'simple-job-managment' ),
		'edit_item'           => __( 'Edit Job', 'simple-job-managment' ),
		'update_item'         => __( 'Update Job', 'simple-job-managment' ),
		'search_items'        => __( 'Search Job', 'simple-job-managment' ),
		'not_found'           => __( 'No Job found', 'simple-job-managment' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'simple-job-managment' ),
	);

	$args = array(
		'label'               => __( 'rdm_Job', 'simple-job-managment' ),
		'description'         => __('Jobs', 'simple-job-managment' ),
		'labels'              => $labels,
		'supports'            => array(),
		'taxonomies'          => array( 'Job_taxonomy' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'			  => 'dashicons-clipboard',
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
		
	);
	register_post_type( 'rdm_job', $args );
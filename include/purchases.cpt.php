<?php


	$labels = array(
		'name'                => _x( 'Purchase', 'Post Type General Name', 'simple-job-managment' ),
		'singular_name'       => _x( 'Purchase', 'Post Type Singular Name', 'simple-job-managment' ),
		'menu_name'           => __( 'Purchases', 'simple-job-managment' ),
		'parent_item_colon'   => __( 'Parent Purchase :', 'simple-job-managment' ),
		'all_items'           => __( 'Purchases', 'simple-job-managment' ),
		'view_item'           => __( 'View Purchase', 'simple-job-managment' ),
		'add_new_item'        => __( 'Add Purchase', 'simple-job-managment' ),
		'add_new'             => __( 'Add Purchase', 'simple-job-managment' ),
		'edit_item'           => __( 'Edit Purchase', 'simple-job-managment' ),
		'update_item'         => __( 'Update Purchase', 'simple-job-managment' ),
		'search_items'        => __( 'Search Purchase', 'simple-job-managment' ),
		'not_found'           => __( 'No Purchase found', 'simple-job-managment' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'simple-job-managment' ),
	);

	$args = array(
		'label'               => __( 'rdm_purchase', 'simple-job-managment' ),
		'description'         => __('Purchases', 'simple-job-managment' ),
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
		'capability_type'     => 'post',
		
	);
	register_post_type( 'rdm_purchase', $args );

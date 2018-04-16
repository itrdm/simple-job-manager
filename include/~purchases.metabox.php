<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* 
* Purchases metaboxes prefix
*/

	$prefix_purchases = 'rdm_jobs_purchases_';

	
/*
* List all suppliers,job, tasks
*/

	$list_all_suppliers_info_config = array(
		'id'             	=> 	'all_suppliers_meta_box',          // meta box id, unique per meta box
		'title'          	=> 	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_title',__('Prepare Purchases','simple-job-managment')), // meta box title
		'pages'          	=> 	array('rdm_purchase'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        	=> 	'normal',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       	=> 	'high',            // order of meta box: high (default), low; optional
		'fields'        	=> 	array(),            // list of meta fields (can be added by field arrays)
		'local_images'  	=> 	false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme'	=> 	false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	$all_suppliers_info_metabox =  new AT_Meta_Box($list_all_suppliers_info_config);

	//Associate purchase to supplier,job task
	$all_suppliers_info_metabox->addPosts($prefix_purchases.'supplier_field_id',
										array(
											'post_type' =>	'rdm_supplier',
											'args' 		=> 	array('orderby' => 'title', 'order' => 'ASC')
										),
										array(
											'name'			=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_send_to_supplier_label',__('Send to supplier','simple-job-managment')),
											'emptylabel'	=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_send_to_supplier_no_supplier_selected_option',__('No supplier selected','simple-job-managment')),
											'group' 		=> 'start'
										)
									);
	
	$all_suppliers_info_metabox->addPosts($prefix_purchases.'job_field_id',
										array(
											'post_type'		=>	'rdm_job',
											'args' 			=>	array('orderby' => 'title', 'order' => 'DESC')
										),
										array(
											'name'			=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_related_to_Job_label',__('Related to job','simple-job-managment')),
											'emptylabel'	=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_related_to_Job_no_Job_selected',__('No job selected','simple-job-managment')),
										)
									);
	
	$all_suppliers_info_metabox->addPosts($prefix_purchases.'task_field_id',
										array(
											'post_type' => 'rdm_task',
											'args' 		=> array('orderby' => 'title', 'order' => 'DESC')
										),
										array(
											'name'			=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_related_to_task_label',__('Related to task','simple-job-managment')),
											'emptylabel'	=>	apply_filters('albwppm_purchases_cpt_purchase_infos_metabox_related_to_Job_no_task_selected',__('No task selected','simple-job-managment')),
											'group' 		=>	'end'
										)
									);	

	$all_suppliers_info_metabox->Finish();

	
//Add our custom metabox that will contain the purchase order table 	
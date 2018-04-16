<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
require_once('helpers/invoice.helper.class.php');
require_once('helpers/jobs.helper.class.php');
	
/* 
* CLIENTS metaboxes prefix
*/

$prefix_clients = 'rdm_client_';

	
/*
* Client Infos
*/

	$clients_info_config = array(
		'id'             => 'clients_meta_box',          // meta box id, unique per meta box
		'title'          => apply_filters('albwppm_clients_cpt_personal_information_metabox_title',__('Personal Information','simple-job-managment')),          // meta box title
		'pages'          => array('rdm_client'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'high',            // order of meta box: high (default), low; optional
		'fields'         => array(),            // list of meta fields (can be added by field arrays)
		'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	$clients_info_metabox =  new AT_Meta_Box($clients_info_config);

	//Associate client to existing WP account

		$getUserFromWpArray['dont_associate'] = apply_filters('albwppm_client_cpt_associate_with_user_dropdown_dont_associate_option_text',__('Dont Associate','simple-job-managment'));
		
		$getAllWpUsers = get_users( 'orderby=nicename' );

		foreach ( $getAllWpUsers as $getAllWpUser ) {
			$getUserFromWpArray[$getAllWpUser->ID] = apply_filters('albwppm_client_cpt_associate_with_user_dropdown_text', $getAllWpUser->user_nicename  , $getAllWpUser->ID);
		}

		$clients_info_metabox->addSelect(
										$prefix_clients.'asociate_with_existing_wp_account_field',
										$getUserFromWpArray,
										array(
											'name'=>  apply_filters('albwppm_client_cpt_associate_with_existing_wp_account_dropdown_label',__('Associate with existing WordPress account','simple-job-managment')), 
											'std'=> array(
														'dont_associate'
													),
										)
								);


	//array of options/fields for personal information metabox
	$clients_info_array=array(
	
		//first name , middle name , last name ... GROUP
		array(
			'id'		=> 'rdm_client_first_name_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> apply_filters('albwppm_single_client_cpt_first_name_label_text',__('First name','simple-job-managment')),
				'group' => 'start',
				'class'	=> 'albwppm_text_input',
			)
		),
		array(
			'id'		=> 'rdm_client_middle_name_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_middle_name_label_text',__('Middle name','simple-job-managment')),
				'class'	=> 'albwppm_text_input',
			)
		),		
		array(
			'id'		=> 'rdm_client_last_name_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_last_name_label_text',__('Last name','simple-job-managment')),
				'group' => 	'end',			
				'class'	=> 'albwppm_text_input',				
			)
		),
		
		//email,phone,mobile .... GROUP
		array(
			'id'		=> 'rdm_client_email_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_email_label_text',__('Email','simple-job-managment')),
				'group' => 	'start',
				'class'	=> 'albwppm_text_input',
			)
		),
		array(
			'id'		=> 'rdm_client_phone_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_phone_label_text',__('Phone','simple-job-managment')),
				'class'	=> 'albwppm_text_input',
				
			)
		),		
		array(
			'id'		=> 'rdm_client_mobile_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_mobile_label_text',__('Mobile','simple-job-managment')),
				'group' => 	'end',	
				'class'	=> 'albwppm_text_input',
			)
		),		
		
		//Address
		array(
			'id'		=> 'rdm_client_address_field_id',
			'type' 		=> 'textarea',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_address_label_text',__('Address','simple-job-managment')),
				'class'	=> 'albwppm_textarea_input',
			)
		),
		
		//skype,facebook,twitter.... GROUP
		array(
			'id'		=> 'rdm_client_skype_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_skype_label_text',__('Skype','simple-job-managment')),
				'group' => 	'start',
				'class'	=> 'albwppm_text_input',				
			)
		),
		array(
			'id'		=> 'rdm_client_facebook_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_facebook_label_text',__('Facebook','simple-job-managment')),
				'class'	=> 'albwppm_text_input',				
				
			)
		),		
		array(
			'id'		=> 'rdm_client_twitter_field_id',
			'type' 		=> 'text',
			'options' 	=> array(
				'name' 	=> 	apply_filters('albwppm_single_client_cpt_twitter_label_text','Twitter'),
				'group' => 	'end',
				'class'	=> 'albwppm_text_input',				
			)
		),			
		
	);
	
	//Allow others to add custom fields to this metabox
	$clients_info_array = apply_filters('albwppm_clients_extra_personal_information_metabox_fields',$clients_info_array);
	
	
	foreach($clients_info_array as $single_client_info){
		
		if($single_client_info['type']=='text'){
			$clients_info_metabox->addText($single_client_info['id'],$single_client_info['options']);
		}
		
		if($single_client_info['type']=='textarea'){
			$clients_info_metabox->addTextarea($single_client_info['id'],$single_client_info['options']);
		}
		
	}

	$clients_info_metabox->Finish();



/*
* Client Jobs
*/


	$clients_Job_config = array(
		'id'             => 'clients_Job_meta_box',          // meta box id, unique per meta box
		'title'          => apply_filters('albwppm_clients_cpt_Jobs_metabox_title',__('Client Jobs','simple-job-managment')),          // meta box title
		'pages'          => array('rdm_client'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'high',            // order of meta box: high (default), low; optional
		'fields'         => array(),            // list of meta fields (can be added by field arrays)
		'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	$JobsAssociateWithClient = apply_filters('albwppm_no_Jobs_from_this_client_yet','No jobs','default_value');

	//If we have a client ID , look for existing jobs associated with client ID
	if(isset($_GET['post'])){
	
		$postIDForJobs = $_GET['post'];

		$JobsAssociateWithClient = Rdm_Jobs_Job_Helpers::get_Jobs_for_client_extra_columns($postIDForJobs);

	} //end if isset post id 

	$clients_Jobs_metabox =  new AT_Meta_Box($clients_Job_config);
	
	$clients_Jobs_metabox->addParagraph('button_id',array('value' => $JobsAssociateWithClient));
	
	$clients_Jobs_metabox->Finish();


/*
* Client Invoices 
*/
	$clients_invoices_config = array(
		'id'             => 'clients_invoices_meta_box',          // meta box id, unique per meta box
		'title'          => apply_filters('albwppm_clients_cpt_invoices_metabox_title',__('Client Invoices','simple-job-managment')),          // meta box title
		'pages'          => array('rdm_client'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'low',            // order of meta box: high (default), low; optional
		'fields'         => array(),            // list of meta fields (can be added by field arrays)
		'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	
	//Default value for new clients
	$invoicesAssociateWithClient = apply_filters('albwppm_no_invoices_for_this_client','No invoices','default_value');

	//If we have a client ID , look for existing invoices associated with client ID
	if(isset($_GET['post'])){
		$postIDForInvoices = $_GET['post'];

		$invoiceStatusToDisplay = 'Not Set';

		$invoicesAssociateWithClient = Rdm_Jobs_Invoice_Helpers::get_invoices_for_client_extra_columns($postIDForInvoices);

	} //end if isset post id 	
	
	$clients_invoices_metabox =  new AT_Meta_Box($clients_invoices_config);
	$clients_invoices_metabox->addParagraph($prefix_clients.'client_invoices_field_id',array('value' => $invoicesAssociateWithClient));
	$clients_invoices_metabox->Finish();
	
	
/*
* Client Review  
*/

	$clients_review_config = array(
		'id'             => 'clients_review_meta_box',        
		'title'          => apply_filters('albwppm_clients_cpt_reviews_metabox_title',__('Client Review','simple-job-managment')),         
		'pages'          => array('rdm_client'),    
		'context'        => 'side',          
		'priority'       => 'low',          
		'fields'         => array(),        
		'local_images'   => false,        
		'use_with_theme' => false        
	);


	$clients_reviews_metabox =  new AT_Meta_Box($clients_review_config);
	$clients_reviews_metabox->addSelect($prefix_clients.'review_field',array(
					'client_no_review_set'	=>	apply_filters('albwppm_client_reviews_dropdown_review_default_text',__('No reviews','simple-job-managment')) , 
					'client_review_1_star'	=>	apply_filters('albwppm_client_reviews_dropdown_one_star_text','1 star'),
					'client_review_2_star'	=>	apply_filters('albwppm_client_reviews_dropdown_two_star_text','2 stars'),
					'client_review_3_star'	=>	apply_filters('albwppm_client_reviews_dropdown_three_star_text','3 stars'),
					'client_review_4_star'	=>	apply_filters('albwppm_client_reviews_dropdown_four_star_text','4 stars'),
					'client_review_5_star'	=>	apply_filters('albwppm_client_reviews_dropdown_five_star_text','5 stars'),
					),
					array('name'=> '','std'=> array('client_no_review_set'))
										);
	$clients_reviews_metabox->Finish();

	
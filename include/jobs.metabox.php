<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
	
	
/* 
* configure JobS metaboxes
*/

$prefix = 'rdm_job_';

$config = array(
	'id'             => 'Jobs_meta_box',         
	'title'          => apply_filters('alwppm_Job_cpt_Job_infos_metabox_title',__('Job Infos','simple-job-managment')),          
	'pages'          => array('rdm_job'),    
	'context'        => 'normal',           
	'priority'       => 'high',            
	'fields'         => array(),          
	'local_images'   => false,         
	'use_with_theme' => false         
);


/*
* Initiate your meta box
*/
$Jobs_meta =  new AT_Meta_Box($config);

$Jobs_meta->addText($prefix.'estimate_field_id',array(
														'name'	=>	apply_filters('albwppm_Job_estimate_label_text',__('Estimate','simple-job-managment'))
													)
												);

$Jobs_meta->addTextarea($prefix.'private_notes_field_id',array(
														'name'	=> 	apply_filters('albwppm_Job_private_notes_label_text',__('Private Job Notes','simple-job-managment')), 
														'std'	=>	'',
														'group'	=>	'start'
													)
												);


$Jobs_meta->addTextarea($prefix.'public_notes_field_id',array(
														'name'	=>	apply_filters('albwppm_Job_public_notes_label_text',__('Public Job Notes','simple-job-managment')), 
														'std'	=>	'',
														'group'	=>	'end'
													)
												);

//Job associated to client
	$Jobs_meta->addPosts($prefix.'client_field_id',array(
															'post_type' => 'rdm_client'
														),
														array(
															'name'			=> apply_filters('albwppm_Job_associate_to_client_label_text',__('Associate to client','simple-job-managment')),
															'emptylabel'	=> apply_filters('albwppm_Job_associate_to_client_no_client_selected_label_text',__('No client selected','simple-job-managment'))
														)
											);
//Job associated to order
$Jobs_meta->addPosts('shop_order_field_id',array(
	'post_type' => 'shop_order',
	'post_status'	=> 'draft'
),
array(
	'name'			=> apply_filters('albwppm_Job_associate_to_order_label_text',__('Associate to order','simple-job-managment')),
	'emptylabel'	=> apply_filters('albwppm_Job_associate_to_order_no_order_selected_label_text',__('No order selected','simple-job-managment'))
)
);
								

//Job associated to supplier
$Jobs_meta->addPosts($prefix.'supplier_field_id',array(
	'post_type' => 'rdm_supplier'
),
array(
	'name'			=> apply_filters('albwppm_Job_associate_to_supplier_label_text',__('Associate to supplier','simple-job-managment')),
	'emptylabel'	=> apply_filters('albwppm_Job_associate_to_supplier_no_supplier_selected_label_text',__('No supplier selected','simple-job-managment'))
)
);



//Job Start Date , end date 
	$Jobs_meta->addDate($prefix.'start_date_field_id',array(
											'name'=> apply_filters('albwppm_Job_start_date_label_text',__('Start Date','simple-job-managment').' ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy',
											'group' => 'start')
										);
										
	$Jobs_meta->addDate($prefix.'target_end_date_field_id',array(
											'name'=> apply_filters('albwppm_Job_target_end_date_label_text',__('Target End Date','simple-job-managment').' ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy')
										);
	
	$Jobs_meta->addDate($prefix.'end_date_field_id',array(
											'name'=> apply_filters('albwppm_Job_actual_end_date_label_text',__('Actual End Date','simple-job-managment').' ( i.e 24-12-2015 )'),
											'format' => 'd-m-yy',
											'group' => 'end')
										);

//Job Status .... Lead , Ongoing , Finished
	$Jobs_meta->addHidden($prefix.'text_field_id',array('name'=> 'rdmDummyGroupStartText' , 'std' => 'rdmDummyGroupStartText','group' => 'start'));

	$Jobs_meta->addSelect($prefix.'status_field',
								array(
									'Job_status_not_set'			=>	apply_filters('albwppm_Job_status_dropdown_not_set_text',__('Not Set','simple-job-managment')) , 
									'Job_status_lead'				=>	apply_filters('albwppm_Job_status_dropdown_lead_text',__('Lead','simple-job-managment')) , 
									'Job_status_ongoing'			=>	apply_filters('albwppm_Job_status_dropdown_ongoing_text',__('Ongoing','simple-job-managment')) , 
									'Job_status_on_hold' 			=> 	apply_filters('albwppm_Job_status_dropdown_on_hold_text',__('Onhold','simple-job-managment')) , 
									'Job_status_waiting_feedback' 	=> 	apply_filters('albwppm_Job_status_dropdown_awaiting_feedback_text',__('Awaiting Feedback','simple-job-managment')) , 
									'Job_status_finished'			=>	apply_filters('albwppm_Job_status_dropdown_completed_text',__('Completed','simple-job-managment')) ,
								),
								array(
									'name'	=>	apply_filters('albwppm_Job_status_dropdown_label_text',__('Job Status ','simple-job-managment')), 
									'std'	=>	array('Job_status_not_set')
									)
							);
							
							
	
//Job progress	
	$Jobs_meta->addSelect($prefix.'progress_field',array(
													'not_set'	=>	apply_filters('albwppm_Job_progress_dropdown_not_set_text',__('Not Set','simple-job-managment')) ,
													'10'		=>	apply_filters('albwppm_Job_progress_dropdown_10_percent_text','10 %') ,
													'20'		=>	apply_filters('albwppm_Job_progress_dropdown_20_percent_text','20 %') ,
													'30'		=>	apply_filters('albwppm_Job_progress_dropdown_30_percent_text','30 %') ,
													'40' 		=> 	apply_filters('albwppm_Job_progress_dropdown_40_percent_text','40 %') ,
													'50'		=>	apply_filters('albwppm_Job_progress_dropdown_50_percent_text','50 %') ,
													'60'		=>	apply_filters('albwppm_Job_progress_dropdown_60_percent_text','60 %') ,
													'70'		=>	apply_filters('albwppm_Job_progress_dropdown_70_percent_text','70 %') ,
													'80'		=>	apply_filters('albwppm_Job_progress_dropdown_80_percent_text','80 %') ,
													'90'		=>	apply_filters('albwppm_Job_progress_dropdown_90_percent_text','90 %') ,
													'100'		=>	apply_filters('albwppm_Job_progress_dropdown_100_percent_text','100 %') ,
												),
												array(
													'name'	=>	apply_filters('albwppm_Job_progress_dropdown_label_text',__('Job Progress ','simple-job-managment')), 
													'std'	=>	array('not_set')
												)
											);	

//Job priority
	$Jobs_meta->addSelect($prefix.'priority_field',array(
														'Job_priority_not_set'	=>	apply_filters('albwppm_Job_priority_dropdown_not_set_text',__('Not Set','simple-job-managment')),
														'Job_priority_low'		=>	apply_filters('albwppm_Job_priority_dropdown_low_text',__('Low','simple-job-managment')),
														'Job_priority_normal'	=>	apply_filters('albwppm_Job_priority_dropdown_normal_text',__('Normal','simple-job-managment')),
														'Job_priority_high' 	=>	apply_filters('albwppm_Job_priority_dropdown_high_text',__('High','simple-job-managment')),
													),
													array(
														'name'	=>	apply_filters('albwppm_Job_priority_dropdown_label_text',__('Job Priority','simple-job-managment')), 
														'std'	=>	array('Job_priority_not_set')
													)
												);	
	
	$Jobs_meta->addHidden($prefix.'text_field_id',array(
													'name'	=>	'rdmDummyGroupEndText' , 
													'std' 	=>	'rdmDummyGroupEndText',
													'group' =>	'end'
													)
												);

// Job Garments List 
/* if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5ab3a0e92953a',
		'title' => 'Client Products Details',
		'fields' => array(
			array(
				'key' => 'field_5aabb14f6f21a',
				'label' => 'Garments Sheets',
				'name' => 'garments_sheets',
				'type' => 'relationship',
				'instructions' => 'Use this field to select a sheet of garments related to this customer.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'show_fields_options' => '',
				'user_roles' => array(
					0 => 'all',
				),
				'post_type' => array(
					0 => 'garment_sheets',
				),
				'taxonomy' => array(
				),
				'filters' => array(
					0 => 'search',
				),
				'elements' => array(
					0 => 'featured_image',
				),
				'min' => '',
				'max' => '',
				'return_format' => 'object',
			),
		),
		'location' => array(
			array(
				
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'rdm_job',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));
	
	endif; */


	//Finish Meta Box Declaration 
	$Jobs_meta->Finish();



/*
* Job has the following tasks 
*/


	$Jobs_tasks_config = array(
		'id'             => 'Job_tasks_meta_box',          // meta box id, unique per meta box
		'title'          => apply_filters('albwppm_Jobs_cpt_Job_tasks_metabox_title',__('Job Tasks','simple-job-managment')),          // meta box title
		'pages'          => array('rdm_job'),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'low',            // order of meta box: high (default), low; optional
		'fields'         => array(),            // list of meta fields (can be added by field arrays)
		'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	$tasksAssociateWithJob = apply_filters('albwppm_Jobs_cpt_Job_tasks_metabox_no_tasks_yet',__('Job Tasks','simple-job-managment'));

	//If we have a job ID , look for existing tasks associated with it
	if(isset($_GET['post'])){
		
		$taskIDForJobs = $_GET['post'];

		$taskStatusToDisplay = __('Not Set','simple-job-managment'); 

		//get all jobs for this client
		$get_tasks_for_Job_params =array(
			'showposts'		=>	-1,
			'post_type' 	=>	'rdm_task',
			'post_status' 	=>	'publish',
			'meta_key'		=>	'rdm_task_for_Job_field',
			'meta_value'	=>	$taskIDForJobs
		);
		
		$query_tasks_for_Job = new WP_Query();
		
		$results_tasks_for_Job = $query_tasks_for_Job->query($get_tasks_for_Job_params);

		//if we have at least one job for this client
		if(sizeof($results_tasks_for_Job)>=1){
		
			$tasksAssociateWithJob='';
		
			foreach($results_tasks_for_Job as $single_task_for_Job){
				
				//Task Status
				if(get_post_meta($single_task_for_Job->ID , 'rdm_task_status_task_field', true)){
				
					$taskStatus = get_post_meta($single_task_for_Job->ID , 'rdm_task_status_task_field', true);

					$taskStatusToDisplay = rdm_get_human_task_status_by_meta_value_as_bullet($taskStatus);
				}
				
				$tasksAssociateWithJob.= $taskStatusToDisplay ;
				
				//Task edit link
				$tasksAssociateWithJob.= apply_filters ( 'albwppm_Jobs_cpt_Job_single_task_metabox_link' , '<a href="'.get_edit_post_link($single_task_for_Job->ID).'">'. $single_task_for_Job->post_title .'</a>' , $single_task_for_Job->ID , $single_task_for_Job->post_title );				
				$tasksAssociateWithJob.= '<br>';
				
			} 
		
		} 

		

	} //end if isset post id 


	$tasks_Jobs_metabox =  new AT_Meta_Box($Jobs_tasks_config);
	
	$tasks_Jobs_metabox->addParagraph('button_id',array('value' => $tasksAssociateWithJob));
	
	$tasks_Jobs_metabox->Finish();
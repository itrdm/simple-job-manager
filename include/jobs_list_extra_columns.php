<?php

//$post_id refers to job ID 

switch ( $column ) {
	
	case 'deadline':
		$deadline= get_post_meta($post_id,'rdm_job_end_date_field_id',true);
		echo apply_filters('albwppm_Jobs_cpt_list_post_table_Job_deadline_value',$deadline , $post_id);
		break;

	case 'client' :
		$client_id= get_post_meta($post_id,'rdm_job_client_field_id',true);
		$client_name = get_the_title($client_id);
		echo apply_filters('albwppm_Jobs_cpt_list_post_table_Job_client_name_value','<a href="'.get_edit_post_link($client_id).'">' . $client_name .' </a>' , $client_id);
		break;
	
	case 'earnings':
		$Job_estimate = get_post_meta($post_id,'rdm_job_estimate_field_id',true);
		$estimate_value = ($Job_estimate ==''  ) ? 'Not set' : $Job_estimate;
		echo apply_filters('albwppm_Jobs_cpt_list_post_table_Job_estimate_value',$estimate_value, $post_id);
		break;
	
	case 'get_tasks_for_Job':
		tasks_for_Job_as_bullets($post_id);
		break;
	
	case 'status':
	
		$status = get_post_meta($post_id,'rdm_job_status_field',true);
		$JobStatusToDisplay = '';

		switch($status){
			case 'Job_status_not_set':
				$JobStatusToDisplay = apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_not_set_value','Not Set',$status , $post_id);
				break;
			
			case 'Job_status_lead':
				$JobStatusToDisplay =  apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_lead_value','Lead',$status , $post_id);
				break;
				
			case 'Job_status_on_hold':
				$JobStatusToDisplay = apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_onhold_value','On hold',$status , $post_id);
				break;

			case 'Job_status_waiting_feedback':
				$JobStatusToDisplay = apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_awaiting_feedback_value','Awaiting feedback',$status , $post_id);
				break;			
			
			case 'Job_status_ongoing':
				$JobStatusToDisplay = apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_ongoing_value','Ongoing',$status , $post_id);
				break;
				
			case 'Job_status_finished':
				$JobStatusToDisplay = apply_filters('albwppm_Jobs_cpt_list_post_table_Job_status_finished_value','Finished',$status , $post_id);
				break;					
				
		}
		
		echo $JobStatusToDisplay ;
		
		break;			
}
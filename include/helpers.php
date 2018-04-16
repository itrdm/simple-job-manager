<?php




/*
* Returns Task Status as bullets
*/
function rdm_get_human_task_status_by_meta_value_as_bullet($status){

	$taskStatusToDisplay = 'Not Set';

	
	switch ($status){
		
		case 'task_status_cancelled':
			$taskStatusToDisplay = '<span class="rdm_generic_bullet_cancelled ">X</span>';
			break;
	
		case 'task_status_not_started':
			$taskStatusToDisplay = '<span class="rdm_generic_bullet_not_started"></span>';
			break;
		
		case 'task_status_ongoing':
			$taskStatusToDisplay =  '<span class="rdm_generic_bullet_ongoing"></span>';
			break;
		
		case 'task_status_finished':
			$taskStatusToDisplay =  '<span class="rdm_generic_bullet_finished"></span>';
			break;
		
		case 'task_status_onhold':
			$taskStatusToDisplay =  '<span class="rdm_generic_bullet_onhold"></span>';
			break;			
	}

	return $taskStatusToDisplay ;
}



/*
* ECHO Task status as LI bullets
*/
function tasks_for_Job_as_bullets($JobID){
			//get all tasks for this post 
			$get_task_for_Job_params =array(
				'showposts'=>-1,
				'post_type' => 'rdm_task',
				'post_status' => 'publish',
				'meta_key'=>'rdm_task_for_Job_field',
				'meta_value'=> $JobID
			);
			$query_task_of_Job = new WP_Query();
			$results_tasks_for_Job = $query_task_of_Job->query($get_task_for_Job_params);
		
			//if we have a task
			if(sizeof($results_tasks_for_Job) >= 1 ){
				
			
				foreach($results_tasks_for_Job as $single_task_for_Job){
				
					$task_id	 = $single_task_for_Job->ID ;
					$task_status_meta = get_post_meta($single_task_for_Job->ID,'rdm_task_status_task_field',true);
					

						if ($task_status_meta == 'task_status_not_started'){
							echo apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_not_started',rdm_get_human_task_status_by_meta_value_as_bullet('task_status_not_started').'<a href="'.get_edit_post_link($task_id).'"  title="Task not Started" > # ' . $task_id .' </a> <div></div>',$JobID,$task_id , $task_status_meta );
							
						}elseif ($task_status_meta == 'task_status_ongoing'){
							
							echo  apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_ongoing',rdm_get_human_task_status_by_meta_value_as_bullet('task_status_ongoing').'<a href="'.get_edit_post_link($task_id).'" title="Task ongoing"> # ' . $task_id .' </a>  <br>',$JobID,$task_id , $task_status_meta);
							
						}elseif ($task_status_meta == 'task_status_finished'){
							echo  apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_finished',rdm_get_human_task_status_by_meta_value_as_bullet('task_status_finished').'<a href="'.get_edit_post_link($task_id).'" title="Task finished"> # ' . $task_id .' </a>  <br>',$JobID,$task_id , $task_status_meta);
							
						}elseif ($task_status_meta == 'task_status_cancelled'){
							echo  apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_cancelled',rdm_get_human_task_status_by_meta_value_as_bullet('task_status_cancelled').'<a href="'.get_edit_post_link($task_id).'" title="Task cancelled"> # ' . $task_id .' </a>  <br>',$JobID,$task_id , $task_status_meta);
							
						}elseif ($task_status_meta == 'task_status_onhold'){
							echo  apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_onhold',rdm_get_human_task_status_by_meta_value_as_bullet('task_status_onhold').'<a href="'.get_edit_post_link($task_id).'" title="Task onhold"> # ' . $task_id .' </a>  <br>',$JobID,$task_id , $task_status_meta);
							
						}else{
							
							//unexpected task status ... return plain edit link for task
							echo  apply_filters('albwppm_Jobs_cpt_list_post_table_single_Job_task_unexpected_status','<a href="'.get_edit_post_link($task_id).'" title="Task status unknown"> # ' . $task_id .' </a>  <br>',$JobID,$task_id , $task_status_meta);
						}						

				}
				
			}
}

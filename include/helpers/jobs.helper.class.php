<?php
class Rdm_Jobs_Job_Helpers{
	
	
	/*
	* Returns job status as text
	*/
	public static function rdm_get_human_Job_status_by_meta_value_as_text($status){

		$JobStatusToDisplay = 'Not Set';

		switch($status){
			case 'Job_status_not_set':
				$JobStatusToDisplay = apply_filters('albwppm_single_Job_status_not_set','Not Set');
				break;
			
			case 'Job_status_lead':
				$JobStatusToDisplay = __('Lead','simple-job-managment');
				break;
				
			case 'Job_status_on_hold':
				$JobStatusToDisplay = __('On Hold','simple-job-managment');
				break;

			case 'Job_status_waiting_feedback':
				$JobStatusToDisplay = __('Awaiting Feedback','simple-job-managment');
				break;			
			
			case 'Job_status_ongoing':
				$JobStatusToDisplay = __('Ongoing','simple-job-managment');
				break;
				
			case 'Job_status_finished':
				$JobStatusToDisplay = __('Completed','simple-job-managment');
				break;					
				
		}
		
		return $JobStatusToDisplay ;
	}			

	/*
	* Returns a list of jobs associated with client ... Used on extra columns and inside client CPT
	*/
	public static function get_Jobs_for_client_extra_columns($clientid,$return_or_show='return'){
	
		if($clientid && ( $clientid > 0)){
			
			$JobsAssociateWithClient= apply_filters('albwppm_no_Jobs_from_this_client_yet',__('No Jobs','simple-job-managment'));
			
			//get all jobs for this client
			$get_Jobs_for_clients_params =array(
				'showposts'		=>	-1,
				'post_type' 	=> 	'rdm_job',
				'post_status' 	=> 	'publish',
				'meta_key'		=>	'rdm_job_client_field_id',
				'meta_value'	=> 	$clientid
			);
			
			$query_Jobs_for_client = new WP_Query();
			
			$results_Jobs_for_client = $query_Jobs_for_client->query($get_Jobs_for_clients_params);

			//if we have at least one job for this client
			if(sizeof($results_Jobs_for_client)>=1){
			
				$JobsAssociateWithClient='';
			
				foreach($results_Jobs_for_client as $single_Job_for_client){
					
					//Job edit link
					$JobsAssociateWithClient.= '<a href="post.php?post='.$single_Job_for_client->ID .'&action=edit">'. $single_Job_for_client->post_title .'</a>';
					
					//Job Status
					if(get_post_meta($single_Job_for_client->ID , 'rdm_job_status_field', true)){
					
						$JobStatus = get_post_meta($single_Job_for_client->ID , 'rdm_job_status_field', true);

						$JobStatusToDisplay = self::rdm_get_human_Job_status_by_meta_value_as_text($JobStatus);
					}
					
					$JobsAssociateWithClient.= ' ' . $JobStatusToDisplay ;
					
					$JobsAssociateWithClient.= '<br>';
					
				} //end foreach
			
			} else{
				//existing client but no jobs associated with him
				$JobsAssociateWithClient= apply_filters('albwppm_no_Jobs_from_this_client_yet','No jobs','no_Jobs_found_for_client');
			}

			return $JobsAssociateWithClient;

		}
		
		return false;
	}
	



	/*
	* Returns Purchase status as text
	*/
	public static function rdm_get_human_Purchase_status_by_meta_value_as_text($status){

		$PurchaseStatusToDisplay = 'Not Set';

		switch($status){
			case 'Purchase_status_not_set':
			$PurchaseStatusToDisplay = apply_filters('albwppm_single_Purchase_status_not_set','Not Set');
				break;
			
			case 'Purchase_status_sent':
			$PurchaseStatusToDisplay = __('Sent','simple-job-managment');
				break;
				
			case 'Purchase_status_on_hold':
			$PurchaseStatusToDisplay = __('On Hold','simple-job-managment');
				break;

			case 'Purchase_status_waiting_confirmation':
			$PurchaseStatusToDisplay = __('Awaiting Confirmation','simple-job-managment');
				break;			
			
			case 'Purchase_status_progress':
			$PurchaseStatusToDisplay = __('In Progress','simple-job-managment');
				break;
				
			case 'Purchase_status_completed':
			$PurchaseStatusToDisplay = __('Completed','simple-job-managment');
				break;					
				
		}
		
		return $PurchaseStatusToDisplay ;
	}			
	/*
	* Returns a list of Purchases associated with supplier ... Used on extra columns and inside client CPT
	*/
	public static function get_Jobs_for_supplier_extra_columns($supplierid,$return_or_show='return'){
	
		if($supplierid && ( $supplierid > 0)){
			
			$JobsAssociateWithSupplier= apply_filters('albwppm_no_Jobs_from_this_supplier_yet',__('No Jobs','simple-job-managment'));
			
			//get all purchases for this supplier
			$get_Jobs_for_suppliers_params =array(
				'showposts'		=>	-1,
				'post_type' 	=> 	'rdm_job',
				'post_status' 	=> 	'publish',
				'meta_key'		=>	'rdm_job_supplier_field_id',
				'meta_value'	=> 	$supplierid
			);
			
			$query_Jobs_for_supplier = new WP_Query();
			
			$results_Jobs_for_supplier = $query_Jobs_for_supplier->query($get_Jobs_for_suppliers_params);

			//if we have at least one job for this supplier
			if(sizeof($results_Jobs_for_supplier)>=1){
			
				$JobsAssociateWithSupplier='';
			
				foreach($results_Jobs_for_supplier as $single_Job_for_supplier){
					
					//Purchase edit link
					$JobsAssociateWithSupplier.= '<a href="post.php?post='.$single_job_for_supplier->ID .'&action=edit">'. $single_jobv_for_supplier->post_title .'</a>';
					
					//Purchase Status
					if(get_post_meta($single_purchase_for_supplier->ID , 'rdm_purchase_status_field', true)){
					
						$PurchaseStatus = get_post_meta($single_purchase_for_supplier->ID , 'rdm_purchase_status_field', true);

						$PurchaseStatusToDisplay = self::rdm_get_human_Job_status_by_meta_value_as_text($PurchaseStatus);
					}
					
					$JobsAssociateWithSupplier.= ' ' . $PurchaseStatusToDisplay ;
					
					$JobsAssociateWithSupplier.= '<br>';
					
				} //end foreach
			
			} else{
				//existing supplier but no jobs associated with him
				$JobsAssociateWithSupplier= apply_filters('albwppm_no_Jobs_from_this_supplier_yet','No jobs','no_Job_found_for_supplier');
			}

			return $JobsAssociateWithSupplier;

		}
		
		return false;
	}
	

	
	/*
	* Return number of all jobs by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'rdm_job');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all jobs
		$query_all_Jobs = new WP_Query();
		$results_all_Jobs = $query_all_Jobs->query($query);
		
		return sizeof($results_all_Jobs) ;
	}
	


	/*
	* Get jobs by status ... completed , cancelled , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'completed':
				$which_status = 'Job_status_finished';
				break;
				
			case 'not_set':
				$which_status = 'Job_status_not_set';
				break;
				
			case 'lead':
				$which_status = 'Job_status_lead';
				break;
				
			case 'ongoing':
				$which_status = 'Job_status_ongoing';
				break;	

			case 'onhold':
				$which_status = 'Job_status_on_hold';
				break;	
				
			case 'awaiting_feedback':
				$which_status = 'Job_status_waiting_feedback';
				break;					

			default :
				$which_status = 'Job_status_finished';				
				
		}
	
		$k['args'] = array( 'meta_key'   =>  'rdm_job_status_field',
							'meta_value' => $which_status
						 );
		
		return self::get_all($k) ;
	}	
	
	
	/*
	* Get human job status by job ID 
	*/
	static function get_human_status_by_Job_id($id){
		
		if($id==''){
			return 'No ID specified';
		}

		$status = self::get_Job_meta_value_by_Job_id($id,'rdm_job_status_field');

		return  self::rdm_get_human_Job_status_by_meta_value_as_text($status);

	}
		
	
	/*
	* Get client name by job ID
	*/
	static function get_client_name_by_Job_id($id){
		
		if($id==''){
			return 'No ID specified';
		}
	
		$client_id = get_post_meta($id,'rdm_job_client_field_id',true);

		if($client_id > 0 ){
			return get_the_title($client_id);
		}
		
		return 'No Client set';
	}

	
	/*
	* Get job meta value by job ID
	*/
	static function get_Job_meta_value_by_Job_id($id,$meta){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}

		$meta_value = get_post_meta($id,$meta,true);
		
		if($meta_value=='not_set'){
			return 'Not Set';	
		}
		
		return ($meta_value) ? $meta_value : 'Not Set';
		
	}
	
	
	/*
	* Return human job priority by job ID 
	*/
	static function get_human_priority_by_Job_id($id){
		
		$priority = self::get_Job_meta_value_by_Job_id($id,'rdm_job_priority_field');
		
		return self::convert_priority_to_human_priority($priority);
		
	}
	
	
	/*
	* Converts a non-human priority meta to human text
	*/
	static function convert_priority_to_human_priority($priority){
		
		$return = 'Not Set';
		
		if($priority==''){
			return $return;
		}
		
		switch ($priority){
		
			case 'Job_priority_not_set':
				$return = __('Not Set','simple-job-managment');;
				break;
				
			case 'Job_priority_low':
				$return = __('Low','simple-job-managment');
				break;
				
			case 'Job_priority_normal':
				$return = __('Normal','simple-job-managment');
				break;
				
			case 'Job_priority_high':
				$return = __('High','simple-job-managment');
				break;	

			default :
				$return = __('Not Set','simple-job-managment'); 
		}
		
		return $return;
		
	}
	
	
	/*
	* Convert unix to human date
	*/
	static function convert_unix_date_to_human_by_Job_id($id,$which_date){
		
		$date = self::get_Job_meta_value_by_Job_id($id,$which_date);
		
		if(!is_numeric($date)){
			return 'Not Set';
		}
		
		$return_date =  date('d-M-Y',$date);
	
		if($return_date > 0){
			return $return_date;
		}else{
			return __('Not Set','simple-job-managment');
		}
	
	}
	
	
	/*
	* Lists possible job statuses as dropdown
	*/
	
	static function dropdown_statuses(){
	
		$selected ='';
	
		//check if we have a STATUS selected 
		if(isset($_POST['rdm_job_status_field']) && $_POST['rdm_job_status_field']){
			$selected = $_POST['rdm_job_status_field'];
		}
		
		?><select name="rdm_job_status_field">
			<option  value="" <?php echo ($selected == '') ? ' selected = "selected" ' : ''; ?> ><?php echo  __('All','simple-job-managment'); ?></option>
			<option value="Job_status_not_set" <?php echo ($selected == 'Job_status_not_set') ? ' selected = "selected" ' : ''; ?> ><?php  echo  __('Not set','simple-job-managment'); ?></option>
			<option value="Job_status_lead" <?php echo ($selected == 'Job_status_lead') ? ' selected = "selected" ' : ''; ?> ><?php echo   __('Lead','simple-job-managment'); ?></option>
			<option value="Job_status_ongoing"  <?php echo ($selected == 'Job_status_ongoing') ? ' selected = "selected" ' : ''; ?> ><?php echo  __('Ongoing','simple-job-managment'); ?></option>
			<option value="Job_status_on_hold" <?php echo ($selected == 'Job_status_on_hold') ? ' selected = "selected" ' : ''; ?> ><?php  echo  __('Lead','simple-job-managment'); ?></option>
			<option value="Job_status_waiting_feedback" <?php echo ($selected == 'Job_status_waiting_feedback') ? ' selected = "selected" ' : ''; ?> ><?php  echo  __('Awaiting Feedback','simple-job-managment'); ?></option>
			<option value="Job_status_finished" <?php echo ($selected == 'Job_status_finished') ? ' selected = "selected" ' : ''; ?> ><?php  echo   __('Completed','simple-job-managment'); ?></option>
		</select>
		
		<?php
	}
	
	
	
	/*
	* Lists possible job priorities as dropdown
	*/
	
	static function dropdown_priorities(){
	
		$selected ='';
	
		//check if we have a PRIORITY selected 
		if(isset($_POST['rdm_job_priority_field']) && $_POST['rdm_job_priority_field']){
			$selected = $_POST['rdm_job_priority_field'];
		}
		
		?><select name="rdm_job_priority_field">
			<option value="">All</option>
			<option value="Job_priority_not_set"  <?php echo ($selected == 'Job_priority_not_set') ? ' selected = "selected" ' : ''; ?> ><?php echo  __('Not Set','simple-job-managment'); ?></option>
			<option value="Job_priority_low"  <?php echo ($selected == 'Job_priority_low') ? ' selected = "selected" ' : ''; ?> ><?php echo __('Low','simple-job-managment'); ?></option>
			<option value="Job_priority_normal" <?php echo ($selected == 'Job_priority_normal') ? ' selected = "selected" ' : ''; ?> ><?php echo __('Normal','simple-job-managment'); ?></option>
			<option value="Job_priority_high" <?php echo ($selected == 'Job_priority_high') ? ' selected = "selected" ' : ''; ?> ><?php echo __('High','simple-job-managment'); ?></option>
		</select>
		
		<?php
	}	
	
	
	
	/*
	* Returns all jobs as a <select>
	*/
	static function get_all_as_dropdown(){
	
		//check if we have a selected job 
		$selected_Job = (isset($_POST['rdm_jobs_invoices_Job_field_id']) && $_POST['rdm_jobs_invoices_Job_field_id'] > 0 ) ? $_POST['rdm_jobs_invoices_Job_field_id'] : '';
	
		$start_of_dropdown = '<select name="rdm_jobs_invoices_Job_field_id">';
		$end_of_dropdown = '</select>';
		$dropdown_options ='<option value="-1"> All </option>';
	
		//if we have at least a job
		if(self::get_all() > 0 ){
			
			//get all jobs
			$query = array('posts_per_page' => -1, 'post_type' => 'rdm_job');
			$query_all = new WP_Query();
			$results_all = $query_all->query($query);
			
			foreach($results_all as $single_result_for_client){
			
				//if we have an ID .... return that as the selected OPTION on the SELECT
				$selected = ($selected_Job == $single_result_for_client->ID) ? ' selected="selected" ' : '';

				$dropdown_options.='<option value='. $single_result_for_client->ID .' '. $selected . '>' . get_the_title($single_result_for_client->ID) .'</option>';
			
			}

			echo $start_of_dropdown . $dropdown_options . $end_of_dropdown;
			
		}else {
			echo $start_of_dropdown .  $end_of_dropdown;
		}
		
	}
	
	
	/*
	* Lists possible job PROGRESS as dropdown
	*/
	
	static function dropdown_progress(){
	
		$selected ='';
	
		//check if we have a PROGRESS selected 
		if(isset($_POST['rdm_job_progress_field']) && $_POST['rdm_job_progress_field']){
			$selected = $_POST['rdm_job_progress_field'];
		}
		
		?><select  name="rdm_job_progress_field">
			<option value="">All</option>
			<option value="10"  <?php echo ($selected == '10') ? ' selected = "selected" ' : ''; ?>>10 %</option>
			<option value="20"  <?php echo ($selected == '20') ? ' selected = "selected" ' : ''; ?>>20 %</option>
			<option value="30"  <?php echo ($selected == '30') ? ' selected = "selected" ' : ''; ?>>30 %</option>
			<option value="40"  <?php echo ($selected == '40') ? ' selected = "selected" ' : ''; ?>>40 %</option>
			<option value="50"  <?php echo ($selected == '50') ? ' selected = "selected" ' : ''; ?>>50 %</option>
			<option value="60"  <?php echo ($selected == '60') ? ' selected = "selected" ' : ''; ?>>60 %</option>
			<option value="70"  <?php echo ($selected == '70') ? ' selected = "selected" ' : ''; ?>>70 %</option>
			<option value="80"  <?php echo ($selected == '80') ? ' selected = "selected" ' : ''; ?>>80 %</option>
			<option value="90"  <?php echo ($selected == '90') ? ' selected = "selected" ' : ''; ?>>90 %</option>
			<option value="100" <?php echo ($selected == '100') ? ' selected = "selected" ' : ''; ?>>100 %</option>
		</select>
		
		<?php
	}	

	
	
	/*
	* Create dropdown for dates .... BEFORE , EXACTLY, AFTER
	*/
	
	static function dropdown_before_exactly_after($select_name){
		
		$selected ='';
		$selected_value='';
	
		//check if we have a after,before,exactly selected 
		if(isset($_POST['rdm_job_'.$select_name.'_before_exactly_after']) && $_POST['rdm_job_'.$select_name.'_before_exactly_after']){
			$selected = $_POST['rdm_job_'.$select_name.'_before_exactly_after'];
		}
		
		//check if we have a value for the date
		if(isset($_POST['rdm_job_'.$select_name.'_field_id']) && $_POST['rdm_job_'.$select_name.'_field_id']){
			$selected_value = $_POST['rdm_job_'.$select_name.'_field_id'];
		}		
		
		?>
		<div class="rdm_input_header">
		<select name="rdm_job_<?php echo $select_name;?>_before_exactly_after" id="rdm_<?php echo $select_name;?>_before_exactly_after">
			<option value="0" <?php echo ($selected == '0') ? ' selected = "selected" ' : ''; ?>> <?php echo __('All','simple-job-managment'); ?> </option>
			<option value="before" <?php echo ($selected == 'before') ? ' selected = "selected" ' : ''; ?>> <?php echo __('Before','simple-job-managment'); ?> </option>
			<option value="exactly" <?php echo ($selected == 'exactly') ? ' selected = "selected" ' : ''; ?>> <?php echo __('Exactly','simple-job-managment'); ?> </option>
			<option value="after" <?php echo ($selected == 'after') ? ' selected = "selected" ' : ''; ?>> <?php echo __('After','simple-job-managment'); ?> </option>
		</select>
		</div>
		<input type="text" name="rdm_job_<?php echo $select_name;?>_field_id" placeholder="dd-mm-yyyy" value="<?php echo $selected_value;?>">
	
		<?php
	}
	
	
	
	
	/*
	* Prepare the job tab report listing
	*/
	static function get_results_for_report(){
		
		if(sizeof($_POST)<1 ){
			return;
		}
		
		$default_args =array();
		
		//default args to return all Jobs
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'rdm_job',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);
		
		
		//check if client is set
		if(isset($_POST['rdm_jobs_reports_JobTab_clients_list']) && $_POST['rdm_jobs_reports_JobTab_clients_list']>0){

			$prep_client = array(
							'key' => 'rdm_job_client_field_id' , 
							'value' => $_POST['rdm_jobs_reports_JobTab_clients_list'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_client);
			
		}
		
		
		// check if job priority is set
		if(isset($_POST['rdm_job_priority_field']) && $_POST['rdm_job_priority_field']!=''){

			$prep_Job_priority = array(
							'key' => 'rdm_job_priority_field' , 
							'value' => $_POST['rdm_job_priority_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_Job_priority);
			
		}
		
		
		// check if job PROGRESS is set
		if(isset($_POST['rdm_job_progress_field']) && $_POST['rdm_job_progress_field']!=''){

			$prep_Job_progress = array(
							'key' => 'rdm_job_progress_field' , 
							'value' => $_POST['rdm_job_progress_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_Job_progress);
			
		}		
		
		
		// check if job STATUS is set
		if(isset($_POST['rdm_job_status_field']) && $_POST['rdm_job_status_field']!=''){

			$prep_Job_status = array(
							'key' => 'rdm_job_status_field' , 
							'value' => $_POST['rdm_job_status_field'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_Job_status);
			
		}
		
		
		
		// check if after,exactly,before  for START DATE is set
		if(isset($_POST['rdm_job_start_date_before_exactly_after']) && $_POST['rdm_job_start_date_before_exactly_after']!='0' && isset($_POST['rdm_job_start_date_field_id']) && $_POST['rdm_job_start_date_field_id']!=''){

			$start_date_before_exactly_after_from_post = $_POST['rdm_job_start_date_before_exactly_after'];
			
			if($start_date_before_exactly_after_from_post =='before'){
				$start_date_before_exactly_after = '<';
			}
			
			if($start_date_before_exactly_after_from_post =='exactly'){
				$start_date_before_exactly_after = '=';
			}			
		
			if($start_date_before_exactly_after_from_post =='after'){
				$start_date_before_exactly_after = '>';
			}			
		
			$prep_Job_start_date_status = array(
							'key' => 'rdm_job_start_date_field_id_timestamp' , 
							'value' => Rdm_Job_Management::convert_human_date_to_unix($_POST['rdm_job_start_date_field_id']) , 
							'compare' => $start_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_Job_start_date_status);
			
		}		
		
		
		
		// check if after,exactly,before  for TARGET END DATE is set
		if(isset($_POST['rdm_job_target_end_date_before_exactly_after']) && $_POST['rdm_job_target_end_date_before_exactly_after']!='0' && isset($_POST['rdm_job_target_end_date_field_id']) && $_POST['rdm_job_target_end_date_field_id']!=''){

			$end_date_before_exactly_after_from_post = $_POST['rdm_job_target_end_date_before_exactly_after'];
			
			if($end_date_before_exactly_after_from_post =='before'){
				$end_date_before_exactly_after = '<';
			}
			
			if($end_date_before_exactly_after_from_post =='exactly'){
				$end_date_before_exactly_after = '=';
			}			
		
			if($end_date_before_exactly_after_from_post =='after'){
				$end_date_before_exactly_after = '>';
			}			
		
			$prep_Job_end_date_status = array(
							'key' => 'rdm_job_target_end_date_field_id_timestamp' , 
							'value' => Rdm_Job_Management::convert_human_date_to_unix($_POST['rdm_job_target_end_date_field_id']) , 
							'compare' => $end_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_Job_end_date_status);
			
		}			
		
		
		
		// check if after,exactly,before  for ACTUAL END DATE is set
		if(isset($_POST['rdm_job_actual_end_date_before_exactly_after']) && $_POST['rdm_job_actual_end_date_before_exactly_after']!='0' && isset($_POST['rdm_job_actual_end_date_field_id']) && $_POST['rdm_job_actual_end_date_field_id']!=''){

			$actual_end_date_before_exactly_after_from_post = $_POST['rdm_job_actual_end_date_before_exactly_after'];
			
			if($actual_end_date_before_exactly_after_from_post =='before'){
				$actual_end_date_before_exactly_after = '<';
			}
			
			if($actual_end_date_before_exactly_after_from_post =='exactly'){
				$actual_end_date_before_exactly_after = '=';
			}			
		
			if($actual_end_date_before_exactly_after_from_post =='after'){
				$actual_end_date_before_exactly_after = '>';
			}			
		
			$prep_Job_actual_end_date_status = array(
							'key' => 'rdm_job_end_date_field_id_timestamp' , 
							'value' => Rdm_Job_Management::convert_human_date_to_unix($_POST['rdm_job_actual_end_date_field_id']) , 
							'compare' => $actual_end_date_before_exactly_after
							); 

			array_push( $default_args['meta_query'], $prep_Job_actual_end_date_status);
			
		}		
		
		//print_r($default_args);
		
		//get all jobs
		$query_all_Jobs = new WP_Query();
		$results_all_Jobs = $query_all_Jobs->query($default_args);		
		
		$Jobs_found = sizeof($results_all_Jobs) ; 


		echo  apply_filters('albwppm_reports_Job_page_found_Jobs_title' , sprintf( _n( '<h3>Found  %s job </h3>', '<h3>Found  %s jobs </h3>', $Jobs_found, 'simple-job-managment' ), $Jobs_found ));
		
		//if we have at least one job ... show the table
		if($Jobs_found>=1){
			
			?>
			<div style="padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts albwppm_reports_Job_page">
				<thead>
					<tr>
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a class="table_header_text_link"><span><strong>ID</strong></span></a>
						</th>					
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Title','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Client','simple-job-managment'); ?></strong></span></a>
						</th>			

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo  __('Progress','simple-job-managment'); ?> %</strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Status','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php  echo __('Priority','simple-job-managment'); ?></strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo  __('Start Date','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo  __('Target end date','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Actual end date','simple-job-managment'); ?></strong></span></a>
						</th>						
						
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a class="table_header_text_link"><span><strong>ID</strong></span></a>
						</th>						
						<th scope="col"  class="manage-column column-title sortable desc">
							<a class="table_header_text_link"><span><strong><?php echo __('Title','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Client','simple-job-managment'); ?></strong></span></a>
						</th>					

						<th scope="col" class="manage-column column-title sortable desc">
							<a class="table_header_text_link"><span><strong><?php echo  __('Progress','simple-job-managment'); ?> %</strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php  echo __('Status','simple-job-managment'); ?></strong></span></a>
						</th>			
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo  __('Priority','simple-job-managment'); ?></strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo  __('Start Date','simple-job-managment'); ?> </strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Target end date','simple-job-managment'); ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a class="table_header_text_link"><span><strong><?php echo __('Actual end date','simple-job-managment'); ?></strong></span></a>
						</th>	
						
					</tr>
				</tfoot>	
				<tbody id="the-list">	
					
				
				

			
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_Jobs as $single_result){ 
				
				if($row_counter%2==0){
					$color='alternate';
				}else{
					$color='';
				}
			
				?>
				
				<tr class=" hentry  iedit <?php echo $color ;?> widefat">
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<a href="<?php echo get_edit_post_link($single_result->ID);?>"><?php echo $single_result->ID ;?></a>
					</td>

					<td class="post-title page-title column-title">
							<a href="<?php echo get_edit_post_link($single_result->ID);?>"><strong><?php echo $single_result->post_title ;?></strong></a>
					</td>	
					
					<td class="post-title page-title column-title">
							<?php echo self::get_client_name_by_Job_id($single_result->ID) ;?>
					</td>		

					<td class="post-title page-title column-title">
							<?php echo self::get_Job_meta_value_by_Job_id($single_result->ID,'rdm_job_progress_field') ;?>
					</td>					
					
					<td class="post-title page-title column-title"> 
							<?php echo self::get_human_status_by_Job_id($single_result->ID) ;?>
					</td>	
					
					<td class="post-title page-title column-title">
							<?php echo self::get_human_priority_by_Job_id($single_result->ID,'rdm_job_priority_field') ;?>
					</td>					
	

					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_Job_id($single_result->ID,'rdm_job_start_date_field_id_timestamp') ;?>
					</td>
					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_Job_id($single_result->ID,'rdm_job_target_end_date_field_id_timestamp') ;?>
					</td>
					<td class="post-title page-title column-title">
							<?php echo self::convert_unix_date_to_human_by_Job_id($single_result->ID,'rdm_job_end_date_field_id_timestamp') ;?>
					</td>					
					
					
				</tr>
				
				<?php
				
				$row_counter++ ; 
				
			} //end foreach
			
			?>
			
					</tbody>
				</table>	
			</div>
			
			<?php
		}
	}
} //end class
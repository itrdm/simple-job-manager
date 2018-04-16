<?php
class Rdm_Jobs_Suppliers_Helpers{

	/*
	* Returns a list of purchases associated with supplier ... Used on extra columns and inside supplier CPT
	*/
	public static function get_purchases_for_supplier_extra_columns($supplierid,$return_or_show='return'){
	
		if($supplierid && ( $supplierid > 0)){
			//echo $supplierid;
			
			$purchaseStatusToDisplay = __('Not Set','simple-job-managment');

			//get all jobs for this supplier
			$get_purchases_for_suppliers_params =array(
				'showposts'=>-1,
				'post_type' => 'rdm_purchase',
				'post_status' => 'publish',
				'meta_key'=>'rdm_jobs_purchases_supplier_field_id',
				'meta_value'=> $supplierid
			);
			$query_purchases_for_supplier = new WP_Query();
			$results_purchases_for_supplier = $query_purchases_for_supplier->query($get_purchases_for_suppliers_params);

			
			//if we have at least one purchase for this supplier
			if(sizeof($results_purchases_for_supplier)>=1){
			
				$purchasesAssociateWithSupplier='';
			
				foreach($results_purchases_for_supplier as $single_purchase_for_supplier){
					
					//Purchase edit link
					$purchasesAssociateWithSupplier.= '<a href="post.php?post='.$single_purchase_for_supplier->ID .'&action=edit">'. $single_purchase_for_supplier->post_title .'</a>';
					
					//Purchase Status
					if(get_post_meta($single_purchase_for_supplier->ID , '_rdm_purchase_notes', true)){
					
						$purchaseStatus = get_post_meta($single_purchase_for_supplier->ID , '_rdm_purchase_notes', true);

						$purchaseStatusToDisplay = (isset($purchaseStatus['status'])) ? ucfirst($purchaseStatus['status']) :  __('Not Set','simple-job-managment');
					}
					
					$purchasesAssociateWithSupplier.= ' ' . $purchaseStatusToDisplay ;
					$purchasesAssociateWithSupplier.= '<br>';
				} //end foreach
			
				
				return $purchasesAssociateWithSupplier;
				
			
			} //end sizeof

		}
		
		return false;
	}
	
	/*
	* Returns all suppliers as a <select>
	*/
	static function get_all_as_dropdown(){
	
		//check if we have a selected supplier 
		$selected_supplier = (isset($_POST['rdm_jobs_reports_JobTab_suppliers_list']) && $_POST['rdm_jobs_reports_JobTab_suppliers_list'] > 0 ) ? $_POST['rdm_jobs_reports_JobTab_suppliers_list'] : '';
	
		$start_of_dropdown = '<select name="rdm_jobs_reports_JobTab_suppliers_list">';
		$end_of_dropdown = '</select>';
		$dropdown_options ='<option value="-1">'. __('All','simple-job-managment').'</option>';
	
		//if we have at least a supplier
		if(self::get_all() > 0 ){
			
			//get all suppliers
			$query = array('posts_per_page' => -1, 'post_type' => 'rdm_supplier');
			$query_all = new WP_Query();
			$results_all = $query_all->query($query);
			
			foreach($results_all as $single_result_for_supplier){
			
				//if we have an ID .... return that as the selected OPTION on the SELECT
				$selected = ($selected_supplier == $single_result_for_supplier->ID) ? ' selected="selected" ' : '';

				$dropdown_options.='<option value='. $single_result_for_supplier->ID .' '. $selected . '>' . get_the_title($single_result_for_supplier->ID) .'</option>';
			
			}

			echo $start_of_dropdown . $dropdown_options . $end_of_dropdown;
			
		}else {
			echo $start_of_dropdown .  $end_of_dropdown;
		}
		
	}
	
	
	/*
	* Return number of all purchases by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'rdm_supplier');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all suppliers
		$query_all = new WP_Query();
		$results_all = $query_all->query($query);
		
		return sizeof($results_all) ;
	}
	
	
	/*
	* Return number of paid purchases
	*/
	static function get_paid(){
	
		$paidPurchasesCount = 0;
	
		//get all jobs for this supplier
		$get_purchases_params =array(
			'showposts'=>-1,
			'post_type' => 'rdm_purchase',
			'post_status' => 'publish',
		);
		$query_purchases = new WP_Query();
		$results_purchases = $query_purchases->query($get_purchases_params);

		
		//if we have at least one purchase
		if(sizeof($results_purchases)>=1){
		
			foreach($results_purchases as $single_purchase){
				
				//Purchase Status
				if(get_post_meta($single_purchase->ID , '_rdm_purchase_notes', true)){
					$purchaseStatus = get_post_meta($single_purchase->ID , '_rdm_purchase_notes', true);
					//print_r($purchaseStatus);
					//die();
					if(isset($purchaseStatus)){
						if($purchaseStatus['status']=='paid'){
							$paidPurchasesCount++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $paidPurchasesCount;
		
	}		
	
	/*
	* Returns % of paid purchases 
	*/
	static function get_paid_purchases_percent(){
		
		if(self::get_all() <= 0 ){
			return 0;
		}
		
		return ( self::get_paid() / self::get_all() );
	}
	
	
	/*
	* Get purchases by status ... completed , ongoing , hold
	*/
	static function get_by_status($status){
	
		switch ($status){
		
			case 'unpaid':
				$which_status = 'unpaid';
				break;
				
			case 'paid':
				$which_status = 'paid';
				break;
				
			case 'overdue':
				$which_status = 'overdue';
				break;
				
			case 'cancelled':
				$which_status = 'cancelled';
				break;					

			default :
				$which_status = 'paid';				
				
		}
	

	
		$purchasesFound = 0;
	
		//get all jobs for this supplier
		$get_purchases_params =array(
			'showposts'=>-1,
			'post_type' => 'rdm_purchase',
			'post_status' => 'publish',
		);
		$query_purchases = new WP_Query();
		$results_purchases = $query_purchases->query($get_purchases_params);

		
		//if we have at least one purchase
		if(sizeof($results_purchases)>=1){
		
			foreach($results_purchases as $single_purchase){
				
				//Purchase Status
				if(get_post_meta($single_purchase->ID , '_rdm_purchase_notes', true)){
					$purchaseStatus = get_post_meta($single_purchase->ID , '_rdm_purchase_notes', true);
					if(isset($purchaseStatus)){
						if($purchaseStatus['status']==$which_status){
							$purchasesFound++;
						}
					}
				}

			} //end foreach

		} //end sizeof
		
		return $purchasesFound;	

	}			

	/*
	* Get supplier meta value by supplier ID
	*/
	static function get_supplier_meta_value_by_supplier_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return 'Not Set';
		}

		$meta_value = get_post_meta($id,$meta,true);
		
		if($meta_value=='not_set'){
			return 'Not Set';	
		}
		
		return ($meta_value) ? $meta_value :  __('Not Set','simple-job-managment');
		
	}
	
	
	/*
	* Create INPUT FIELD
	*/
	static function create_input($input_name,$placeholder=''){
		
		$selected_value ='';
		
		//check if we have a value set for the field
		if(isset($_POST['rdm_supplier_'.$input_name.'_field_id']) && $_POST['rdm_supplier_'.$input_name.'_field_id']!=''){
			$selected_value = $_POST['rdm_supplier_'.$input_name.'_field_id'];
		}	

		echo '<input type="text" name="rdm_supplier_'.$input_name.'_field_id" placeholder="'.$placeholder.'" value="'.$selected_value.'">';
		
	}
	
	/*
	* Prepare the suppliers tab report listing
	*/
	static function get_results_for_report(){

		if(sizeof($_POST)<1 ){
			return;
		}	
		
	
	
		//default args to return all Suppliers
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'rdm_supplier',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);	
	
		//A supplier was selected on the supplier username dropdown
		//get the CPT that has that ID
		if(isset($_POST['rdm_jobs_reports_JobTab_suppliers_list']) && $_POST['rdm_jobs_reports_JobTab_suppliers_list']!='-1'){

			$default_args['p'] = $_POST['rdm_jobs_reports_JobTab_suppliers_list'];
			
		}
		

		//check if supplier name is set
		if(isset($_POST['rdm_supplier_first_name_field_id']) && $_POST['rdm_supplier_first_name_field_id']!=''){

			$prep_supplier_name = array(
							'key' => 'rdm_supplier_first_name_field_id' , 
							'value' => $_POST['rdm_supplier_first_name_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_name);
			
		}		
		
		
		//check if supplier surname is set
		if(isset($_POST['rdm_supplier_last_name_field_id']) && $_POST['rdm_supplier_last_name_field_id']!=''){

			$prep_supplier_surname = array(
							'key' => 'rdm_supplier_last_name_field_id' , 
							'value' => $_POST['rdm_supplier_last_name_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_surname);
			
		}	

		//check if supplier email is set
		if(isset($_POST['rdm_supplier_email_field_id']) && $_POST['rdm_supplier_email_field_id']!=''){

			$prep_supplier_email = array(
							'key' => 'rdm_supplier_email_field_id' , 
							'value' => $_POST['rdm_supplier_email_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_email);
			
		}			

		//check if supplier phone is set
		if(isset($_POST['rdm_supplier_phone_field_id']) && $_POST['rdm_supplier_phone_field_id']!=''){

			$prep_supplier_phone = array(
							'key' => 'rdm_supplier_phone_field_id' , 
							'value' => $_POST['rdm_supplier_phone_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_phone);
			
		}	


		//check if supplier mobile is set
		if(isset($_POST['rdm_supplier_mobile_field_id']) && $_POST['rdm_supplier_mobile_field_id']!=''){

			$prep_supplier_mobile = array(
							'key' => 'rdm_supplier_mobile_field_id' , 
							'value' => $_POST['rdm_supplier_mobile_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_mobile);
			
		}			
		

		//check if supplier skype is set
		if(isset($_POST['rdm_supplier_skype_field_id']) && $_POST['rdm_supplier_skype_field_id']!=''){

			$prep_supplier_skype = array(
							'key' => 'rdm_supplier_skype_field_id' , 
							'value' => $_POST['rdm_supplier_skype_field_id'] , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_supplier_skype);
			
		}	

		
		
	
		//get all suppliers
		$query_all_suppliers = new WP_Query();
		$results_all_suppliers = $query_all_suppliers->query($default_args);			
		$suppliers_found = sizeof($results_all_suppliers); 
		
		echo  apply_filters('albwppm_reports_suppliers_page_found_suppliers_title' , sprintf( _n( '<h3>Found  %s supplier </h3>', '<h3>Found  %s suppliers </h3>', $suppliers_found, 'simple-job-managment' ), $suppliers_found ));

		
		//if we have at least one supplier ... show the table
		if( $suppliers_found >= 1 ){
			
			?>
			<div style="color:red;padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong>ID</strong></span></a>
						</th>					
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Supplier','simple-job-managment') ?></strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Name','simple-job-managment') ?></strong></span></a>
						</th>			

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Surname','simple-job-managment') ?></strong></span></a>
						</th>							
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Email','simple-job-managment') ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Phone','simple-job-managment') ?></strong></span></a>
						</th>	

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Mobile','simple-job-managment') ?></strong></span></a>
						</th>
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Skype','simple-job-managment') ?></strong></span></a>
						</th>
							
						
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong>ID</strong></span></a>
						</th>						
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Supplier','simple-job-managment') ?></strong></span></a>
						</th>
						<th scope="col"  class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Name','simple-job-managment') ?></strong></span></a>
						</th>					

						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Surname','simple-job-managment') ?></strong></span></a>
						</th>			
						
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Email','simple-job-managment') ?></strong></span></a>
						</th>		
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Phone','simple-job-managment') ?></strong></span></a>
						</th>		
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Mobile','simple-job-managment') ?></strong></span></a>
						</th>	
						<th scope="col" class="manage-column column-title sortable desc" >
							<a ><span><strong><?php echo __('Skype','simple-job-managment') ?></strong></span></a>
						</th>							

					</tr>
				</tfoot>	
				<tbody id="the-list">	
	
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_suppliers as $single_result){ 
				
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
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_first_name_field_id') ;?>
					</td>		

					<td class="post-title page-title column-title">
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_last_name_field_id') ;?>
					</td>					

					<td class="post-title page-title column-title">
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_email_field_id') ;?>
					</td>	

					<td class="post-title page-title column-title">
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_phone_field_id') ;?>
					</td>					
					
					<td class="post-title page-title column-title">
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_mobile_field_id') ;?>
					</td>	
					<td class="post-title page-title column-title">
							<?php echo self::get_supplier_meta_value_by_supplier_id($single_result->ID,'rdm_supplier_skype_field_id') ;?>
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
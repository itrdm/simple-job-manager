<?php
class Rdm_Jobs_Purchase_Helpers{

	/*
	* Returns a list of purchases associated with supplier ... Used on extra columns and inside supplier CPT
	*/
	public static function get_purchases_for_supplier_extra_columns($supplierid,$return_or_show='return'){
	
		if($supplierid && ( $supplierid > 0)){
			//echo $supplierid;
			
			$purchasesAssociateWithSupplier = __('No Purchases','simple-job-managment');

			//get all purchases for this supplier
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

						$purchaseStatusToDisplay = (isset($purchaseStatus['status'])) ? ucfirst($purchaseStatus['status']) : 'Not set';
					}
					
					$purchasesAssociateWithSupplier.= ' ' . $purchaseStatusToDisplay ;
					$purchasesAssociateWithSupplier.= '<br>';
				} //end foreach
			
				
				return $purchasesAssociateWithSupplier;
			
			} else {
				
				//existing supplier but no purchases associated with him
				return apply_filters('albwppm_no_purchases_for_this_supplier',$purchasesAssociateWithSupplier ,'no_purchases_found');
			}

		}
		
		return false;
	}
	
	
	/*
	* Returns purchase total value
	*/
	public static function calculate_total($purchase_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){

		$purchase_id = (int) $purchase_id;
		
		$purchase_meta = get_post_meta($purchase_id,'_purchase_discount_and_vat',true);

		//if we have i.e subtotal set ... it means we are good to go
		if(!isset($purchase_meta['purchase_subtotal'])){
			return false;
		}

		$discountValueEntered = $purchase_meta['discountValue'];
		$discountTypeEntered  = $purchase_meta['discountType'];
		
		if( $discountValueEntered &&  $discountTypeEntered != 'none' ){
			
			if($discountTypeEntered=='percent'){
				//check so discounted value isnt lower than 0
				
				$valueAfterDiscount = $actualTotal - ( $actualTotal * $discountValueEntered/100 );

				if( $valueAfterDiscount > 0 ){
					
					if($returnNewValueOrDiscount=='newvalue'){
						$value_to_return =  $valueAfterDiscount;
					}else{
						$value_to_return =  $discountValueEntered ;
					}
				}

			}
			
			if($discountTypeEntered=='amount'){

				//check so discounted value isnt lower than 0
				if( $actualTotal - $discountValueEntered > 0 ){

					if($returnNewValueOrDiscount=='newvalue'){
						$toreturn  = $actualTotal - $discountValueEntered;

						$value_to_return =   $toreturn;
					}else{
						$value_to_return =  $discountValueEntered  ;
					}
				}
				
			}
			
			if(isset($purchase_meta['vat'])){
				if($purchase_meta['vat']>0){
					return number_format ($value_to_return  +   ($value_to_return * $purchase_meta['vat']/100),2) ;
				}else{
					return number_format($value_to_return,2);
					
				}
			}
			
			//return $value_to_return 
			
		}else{
			if(isset($purchase_meta['vat'])){
				if($purchase_meta['vat']>0){
					return number_format ($actualTotal  +   ($actualTotal * $purchase_meta['vat']/100),2) ;
				}else{
					return number_format($actualTotal,2);
				}
			}
		}
	}
	
	
	/*
	* Calculate new value after discount,vat 
	*/
	public static function calculateDiscount($purchase_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){

		$purchase_id = (int) $purchase_id;
		
		$purchase_meta = get_post_meta($purchase_id,'_purchase_discount_and_vat',true);
	
		//if we have i.e subtotal set ... it means we are good to go
		if(!isset($purchase_meta['purchase_subtotal'])){
			return false;
		}
	
		$discountValueEntered = $purchase_meta['discountValue'];
		$discountTypeEntered  = $purchase_meta['discountType'];
		
		if( $discountValueEntered &&  $discountTypeEntered != 'none' ){
			
			if($discountTypeEntered=='percent'){
				//check so discounted value isnt lower than 0
				
				$valueAfterDiscount = $actualTotal - ( $actualTotal * $discountValueEntered/100 );

				if( $valueAfterDiscount > 0 ){
					
					if($returnNewValueOrDiscount=='newvalue'){
						return $valueAfterDiscount;
					}else{
						return $discountValueEntered ;
					}
				}
				
				return false;		
			}
			
			if($discountTypeEntered=='amount'){

				//check so discounted value isnt lower than 0
				if( $actualTotal - $discountValueEntered > 0 ){

					if($returnNewValueOrDiscount=='newvalue'){
						 $toreturn  = $actualTotal - $discountValueEntered;

						return  $toreturn;
					}else{
						return $discountValueEntered + ' ' ;
					}
				}
				return false;
			}
			
		}else{
			return false;
		}
	}	

	
	
	/*
	* Return number of all purchases by default , OPTIONS is array of additional WP_QUERY params
	*/
	
	static function get_all($options=array()){
	
		$query = array('posts_per_page' => -1, 'post_type' => 'rdm_purchase');
		
		if (isset($options['args']) ) {
			$query = array_merge($query,(array)$options['args']);
		}

	
		//get all purchases
		$query_all = new WP_Query();
		$results_all = $query_all->query($query);
		
		return sizeof($results_all) ;
	}
	
	
	/*
	* Return number of paid purchases
	*/
	static function get_paid(){
	
		$paidPurchasesCount = 0;
	
		
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
	* Get the price amount of paid purchases
	*/
	
	static function get_purchases_amount_by_status($status){
		
		$total_amount = 0;
	
		//get all paid purchases
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
				
				if(self::get_purchase_notes_value_by_purchase_id($single_purchase->ID,'status')==$status){
				
					$purchase_subtotal = self::get_purchase_discount_and_value_by_purchase_id($single_purchase->ID,'purchase_subtotal');
					if($purchase_subtotal>0){
						$total_amount+=  $purchase_subtotal;
					}
				
				}

			} 

		} //end sizeof
		
		return $total_amount;		
		
	}

	
	/*
	* Get purchase meta value by purchase ID
	*/
	static function get_purchase_meta_value_by_purchase_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return __('Not Set','simple-job-managment');	
		}

		$meta_value = get_post_meta($id,$meta,true);
		
		if($meta_value=='not_set'){
			return __('Not Set','simple-job-managment');
		}
		
		
		return ($meta_value) ? $meta_value : __('Not Set','simple-job-managment');
		
	}
	
	/*
	* Get purchase notes
	*/
	static function get_purchase_notes_value_by_purchase_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return __('Not Set','simple-job-managment');
		}
		
		$value_to_return = __('Not Set','simple-job-managment');

		$meta_value_array = get_post_meta($id,'_rdm_purchase_notes',true);
		
		if(isset($meta_value_array)){
			if(isset($meta_value_array[$meta])){
				if($meta_value_array[$meta]=='not_set'){
					return $value_to_return;
				}
				$value_to_return = $meta_value_array[$meta];
			}
		}
		return $value_to_return;
		
	}	
	
	/*
	* Get purchase price/vat/amount
	*/
	static function get_purchase_discount_and_value_by_purchase_id($id,$meta=''){
		
		if($id=='' || $meta == ''){
			return __('Not Set','simple-job-managment');
		}
		
		$value_to_return = __('Not Set','simple-job-managment');

		$meta_value_array = get_post_meta($id,'_purchase_discount_and_vat',true);
		
		if(isset($meta_value_array)){
			if(isset($meta_value_array[$meta])){
				if($meta_value_array[$meta]=='not_set'){
					return $value_to_return;
				}
				$value_to_return = $meta_value_array[$meta];
			}
		}
		return $value_to_return;
		
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
	
		//get all purchases
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
	* Lists possible purchase payment status as dropdown
	*/
	
	static function dropdown_paid_status(){
	
		$selected ='';
	
		//check if we have a status selected 
		if(isset($_POST['rdm_purchase_status_for_report_page']) && $_POST['rdm_purchase_status_for_report_page']){
			$selected = $_POST['rdm_purchase_status_for_report_page'];
		}
		
		?><select  name="rdm_purchase_status_for_report_page">
			<option value=""><?php echo __('All','simple-job-managment');?></option>
			<option value="unpaid" <?php echo ($selected == 'unpaid') ? ' selected = "selected" ' : ''; ?>><?php echo __('Unpaid','simple-job-managment') ?></option>
			<option value="paid" <?php echo ($selected == 'paid') ? ' selected = "selected" ' : ''; ?>><?php echo __('Paid','simple-job-managment') ?></option>
			<option value="overdue" <?php echo ($selected == 'overdue') ? ' selected = "selected" ' : ''; ?>><?php echo __('Overdue','simple-job-managment') ?></option>
			<option value="cancelled" <?php echo ($selected == 'cancelled') ? ' selected = "selected" ' : ''; ?>><?php echo __('Cancelled','simple-job-managment') ?></option>
		</select>
		
		<?php
	}		
	
	/*
	* Create INPUT FIELD
	*/
	static function create_input($input_name,$placeholder=''){
		
		$selected_value ='';
		
		//check if we have a value set for the field
		if(isset($_POST[$input_name]) && $_POST[$input_name]!=''){
			$selected_value = $_POST[$input_name];
		}	

		echo '<input type="text" name="'.$input_name.'" placeholder="'.$placeholder.'" value="'.$selected_value.'">';
		
	}	
	
	
	/*
	*  REPORT SECTION BEGINS
	*/
	
	static function get_results_for_report(){
	
	
		if(sizeof($_POST)<1 ){
			return;
		}	
		
		//default args to return all purchases
		$default_args = array(
			'showposts'=>-1,
			'post_type' => 'rdm_purchase',
			'post_status' => 'publish',		
			
			'meta_query' => array(
				'relation' => 'AND',
			)
			
		);			


		//check if supplier is set
		if(isset($_POST['rdm_jobs_reports_JobTab_suppliers_list']) && $_POST['rdm_jobs_reports_JobTab_suppliers_list']>0){

			$prep_supplier = array(
							'key' => 'rdm_jobs_purchases_supplier_field_id' , 
							'value' => $_POST['rdm_jobs_reports_JobTab_suppliers_list'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_supplier);
			
		}		
		
		
		//check if job is set
		if(isset($_POST['rdm_jobs_purchases_Job_field_id']) && $_POST['rdm_jobs_purchases_Job_field_id']>0){

			$prep_Job = array(
							'key' => 'rdm_jobs_purchases_Job_field_id' , 
							'value' => $_POST['rdm_jobs_purchases_Job_field_id'] , 
							'compare' => '='
							); 

			array_push( $default_args['meta_query'], $prep_Job);
			
		}		
		
		//check if status
		if(isset($_POST['rdm_purchase_status_for_report_page']) && $_POST['rdm_purchase_status_for_report_page']!=''){

			$prep_status = array(
							'key' => '_rdm_purchase_notes' , 
							'value' => serialize(strval($_POST['rdm_purchase_status_for_report_page'])) , 
							'compare' => 'LIKE'
							); 

			array_push( $default_args['meta_query'], $prep_status);
			
		}			
		
		
		//get all purchases
		$query_all_purchases = new WP_Query();
		$results_all_purchases = $query_all_purchases->query($default_args);			
		$purchases_found = sizeof($results_all_purchases) ; 	 
	 
		
	 
		echo  apply_filters('albwppm_reports_purchases_page_found_purchases_title' , sprintf( _n( '<h3>Found  %s purchase </h3>', '<h3>Found  %s purchases </h3>', $purchases_found, 'simple-job-managment' ), $purchases_found ));
		
		
		//if we have at least one purchase ... show the table
		if( $purchases_found >=1 ){
			
			?>
			<div style="color:red;padding: 20px;">
			
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Title','simple-job-managment') ?></strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Status','simple-job-managment') ?></strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Amount','simple-job-managment') ?></strong></span></a>
						</th>							
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('To be paid by','simple-job-managment') ?></strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Paid on','simple-job-managment') ?></strong></span></a>
						</th>							
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Supplier','simple-job-managment') ?></strong></span></a>
						</th>		
									
						
					</tr>
				</thead>
				<tfoot>
					<tr>
	
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong><?php echo __('Title','simple-job-managment') ?></strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Status','simple-job-managment') ?></strong></span></a>
						</th>	
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Amount','simple-job-managment') ?></strong></span></a>
						</th>						
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('To be paid by','simple-job-managment') ?></strong></span></a>
						</th>		
						<th scope="col"  class="check-column manage-column column-title sortable desc " style="padding-top:0px;width: 3em;">
							<a ><span><strong><?php echo __('Paid on','simple-job-managment') ?></strong></span></a>
						</th>							
						<th scope="col"  class="manage-column column-title sortable desc" style="width: 3em;">
							<a ><span><strong><?php echo __('Supplier','simple-job-managment') ?></strong></span></a>
						</th>	
							
			

					</tr>
				</tfoot>	
				<tbody id="the-list">	
	
			<?php
			
			$row_counter = 0;
			$color = '';
			
			foreach ($results_all_purchases as $single_purchase){ 
				
				if($row_counter%2==0){
					$color='alternate';
				}else{
					$color='';
				}
			
				?>
				
				<tr class=" hentry  iedit <?php echo $color ;?> widefat">
				
					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<a href="<?php echo get_edit_post_link($single_purchase->ID);?>"><strong><?php echo $single_purchase->post_title ;?></strong></a>
					</td>	
					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo ucwords(self::get_purchase_notes_value_by_purchase_id($single_purchase->ID,'status'));?>
					</td>
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php 
						$purchase_subtotal = self::get_purchase_discount_and_value_by_purchase_id($single_purchase->ID,'purchase_subtotal');
						if($purchase_subtotal>0){
							//($purchase_id , $actualTotal ,$returnNewValueOrDiscount='newvalue'){
							echo self::calculate_total($single_purchase->ID,$purchase_subtotal);
						}
						?>
					</td>					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo self::get_purchase_notes_value_by_purchase_id($single_purchase->ID,'toBePaidOn');?>
					</td>	
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php echo self::get_purchase_notes_value_by_purchase_id($single_purchase->ID,'paidOn');?>
					</td>					
					<td class="check-column" style="padding:9px 0px 8px 10px;">
						<?php 
							$supplier_id_for_purchase = self::get_purchase_meta_value_by_purchase_id($single_purchase->ID,'rdm_jobs_purchases_supplier_field_id');
							
							if($supplier_id_for_purchase>0){
							echo '<a href="'.get_edit_post_link($supplier_id_for_purchase).'">'.get_the_title($supplier_id_for_purchase).'</a>';
						}?>
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
	
	/*
	*  REPORT SECTION ENDS
	*/
	
	

} //end class
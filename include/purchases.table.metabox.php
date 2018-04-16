<?php
/*
* Class to create the metabox for purchase tables
*/


class Rdm_Purchase_Table_Metabox {

	private $plugin_path;
	private $plugin_url;
	private static $instance = null;
	
	private $mainPluginPath ='' ;

	
	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}		
	
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
	
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		
		$this->mainPluginPath = dirname(dirname(__FILE__)) ;
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' )  );
		add_action( 'save_post', array( $this, 'save' ) );
		
		
		//add ajax function to update fields of table and dropdown 
		add_action( 'wp_ajax_rdm_job_purchase_cpt_backend_ajax', array( $this, 'rdm_job_purchase_cpt_backend_ajax'));
		
		//get items for existing purchase
		add_action( 'wp_ajax_rdm_job_items_for_purchase_ajax', array( $this, 'rdm_job_items_for_purchase_ajax'));
		
		//get items for existing purchase that have "is_Job=yes"
		add_action( 'wp_ajax_get_Jobs_items_on_purchase_ajax', array( $this, 'get_Jobs_items_on_purchase_ajax'));		
		
		//Get all jobs if a supplier is associated with purchase.
		add_action( 'wp_ajax_get_and_check_Jobs_checkbox_list_ajax', array( $this, 'get_and_check_Jobs_checkbox_list_ajax'));		
		
		//save purchase total,vat,discount
		add_action( 'wp_ajax_rdm_job_purchase_save_vat_discount_ajax', array( $this, 'rdm_job_purchase_save_vat_discount_ajax'));
		
		//add admin css,js
		add_action('admin_enqueue_scripts', array($this,'purchase_admin_css'));
		
		//add ajax for PDF preview
		add_action( 'wp_ajax_rdm_job_purchase_preview_pdf_ajax', array( $this, 'rdm_job_purchase_preview_pdf_ajax'));
		
		//remove default PUBLISH box
		//add_action( 'admin_menu', array($this,'remove_publish_box') );
		
		//Download PDF
		add_action( 'init',array($this,'rdm_job_purchase_pdf_download'));
		
		// Submit Purchase order to supplier
		// Add filter and see function on line 1401
		//add_filter('wp_mail','rdm_job_submit_purchase_wp_mail_filter');
	}
	
	
	/*
	* Localize the JS scripts 
	*/
	
	function localize_scripts(){
		
		$translation_array = array(
			'table_purchase_add_record_title' => apply_filters('albwppm_table_purchase_add_record_title',__('Add new item','simple-job-managment')),
			'table_purchase_line_total'		 => apply_filters('albwppm_table_purchase_line_total_title',__('Line total','simple-job-managment')),
			'table_purchase_line_quantity'	 => apply_filters('albwppm_table_purchase_line_quantity_title',__('Quantity','simple-job-managment')),
			'table_purchase_line_price'	 	 => apply_filters('albwppm_table_purchase_line_price_title',__('Unit Price','simple-job-managment')),
			'table_purchase_line_item'	 	 => apply_filters('albwppm_table_purchase_line_item_title',__('Item','simple-job-managment')),
			'table_purchase_items_on_purchase_title'	 	 => apply_filters('albwppm_table_purchase_items_on_purchase_title',__('Items on purchase','simple-job-managment')),
			'table_purchase_save_new_item_button_title'	 	 => apply_filters('albwppm_table_purchase_save_new_item_button_title',__('Save','simple-job-managment')),
			'table_purchase_cancel_new_item_button_title'	 	 => apply_filters('albwppm_table_purchase_cancel_new_item_button_title',__('Cancel','simple-job-managment')),
			'table_purchase_edit_item_popup_title'	 	 => apply_filters('albwppm_table_purchase_edit_item_popup_title',__('Edit item','simple-job-managment')),
			'table_purchase_delete_item_popup_title_are_you_sure'	 	 => apply_filters('albwppm_table_purchase_delete_item_popup_title_are_you_sure',__('Are you sure','simple-job-managment')),
			'table_purchase_delete_item_confirmation_text'	 	 => apply_filters('albwppm_table_purchase_delete_item_confirmation_text',__('This item will be deleted','simple-job-managment')),
			'table_purchase_delete_item_button_text'	 	 => apply_filters('albwppm_table_purchase_delete_item_button_text',__('Delete','simple-job-managment')),
			'table_purchase_no_data_available'	 	 => apply_filters('albwppm_table_purchase_no_data_available',__('No data available','simple-job-managment')),
			'table_purchase_loading_records'	 	 => apply_filters('albwppm_table_purchase_loading_records',__('Loading records','simple-job-managment')),
			'table_purchase_no_items_on_purchase'	 	 => apply_filters('albwppm_table_purchase_no_items_on_purchase',__('No items on purchase','simple-job-managment')),
		);

		wp_localize_script( 'rdm-job-purchase-page', 'albwppm', $translation_array );
	}
	
	
	public function remove_publish_box(){
		
		//remove_meta_box( 'submitdiv', 'rdm_purchase', 'side' );
		remove_meta_box( 'submitdiv', 'rdm_purchase', 'normal' ); // Publish meta box
		remove_meta_box( 'commentsdiv', 'rdm_purchase', 'normal' ); // Comments meta box
		remove_meta_box( 'revisionsdiv', 'rdm_purchase', 'normal' ); // Revisions meta box
		remove_meta_box( 'authordiv', 'rdm_purchase', 'normal' ); // Author meta box
		remove_meta_box( 'slugdiv', 'rdm_purchase', 'normal' );	// Slug meta box
		remove_meta_box( 'tagsdiv-post_tag', 'rdm_purchase', 'side' ); // Post tags meta box
		remove_meta_box( 'categorydiv', 'rdm_purchase', 'side' ); // Category meta box
		remove_meta_box( 'postexcerpt', 'rdm_purchase', 'normal' ); // Excerpt meta box
		remove_meta_box( 'formatdiv', 'rdm_purchase', 'normal' ); // Post format meta box
		remove_meta_box( 'trackbacksdiv', 'rdm_purchase', 'normal' ); // Trackbacks meta box
		remove_meta_box( 'postcustom', 'rdm_purchase', 'normal' ); // Custom fields meta box
		remove_meta_box( 'commentstatusdiv', 'rdm_purchase', 'normal' ); // Comment status meta box
		remove_meta_box( 'postimagediv', 'rdm_purchase', 'side' ); // Featured image meta box
		remove_meta_box( 'pageparentdiv', 'rdm_purchase', 'side' ); // Page attributes meta box
		
	}

	public function get_plugin_url() {
		return $this->plugin_url;
	}
	
	/*
	* Admin Scripts,Styles
	*/
	public function purchase_admin_css(){
		
		global $pagenow, $typenow;
		
		//which page are we
		$screen = get_current_screen();

		if( $typenow=='rdm_purchase' || $typenow =='rdm_job' || $typenow == 'rdm_task' || $typenow == 'rdm_supplier' ){
			wp_enqueue_script( 'at-meta-box', $this->get_plugin_url() .'meta-box-class/js/meta-box.js', array( 'jquery' ), null, true );
			wp_enqueue_style('at-multiselect-select2-css',  $this->get_plugin_url() . 'meta-box-class/js/select2/select2.css', array(), null);
			wp_enqueue_script('at-multiselect-select2-js', $this->get_plugin_url() . 'meta-box-class/js/select2/select2.js', array('jquery'), false, true);
		}

		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		if($screen->post_type =='rdm_purchase'){
			
			wp_enqueue_script('jquery-ui-datepicker');
		
			wp_enqueue_script( 'rdm-job-purchase-page', plugin_dir_url(dirname(__FILE__)) .'assets/admin/js/purchases.admin.js', array( 'jquery' ), null, false );
			
			//jTable
			wp_enqueue_script( 'rdm-job-purchase-jtable-jquery', plugin_dir_url(dirname(__FILE__)) .'assets/admin/jtable/jquery.jtable.js', array( 'jquery' ), null, true );	
			wp_enqueue_style('rdm-job-purchase-jtable-css',  plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/jquery-ui.css', array(), null);
			wp_enqueue_style('rdm-job-purchase-jtable-css3',   plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/themes/jqueryui/jtable_jqueryui.css', array(), null);
			wp_enqueue_style('rdm-job-purchase-jtable-css2', plugin_dir_url(dirname(__FILE__)) .   'assets/admin/jtable/themes/metro/blue/jtable.css', array(), null);
			
			$this->localize_scripts();
		
		}

	}	
	
	
	public function rdm_job_purchase_cpt_backend_ajax(){
		
		//if nothing set 
		if(!isset($_POST['whathappend'])){
			 die('NO whathappend action received');
		}
		
		//supplier dropdown changed ... find jobs related to supplier
		if($_POST['whathappend']=='supplierDropdownChanged'){
			

			$supplierID = (int) $_POST['supplierID'];

			//$JobsArray['found_any_Job'] = 'no';
			
			//get all jobs for this supplier
			$get_Jobs_for_suppliers_params =array(
				'showposts'=>-1,
				'post_type' => 'rdm_job',
				'post_status' => 'publish',
				'meta_key'=>'rdm_job_supplier_field_id',
				'meta_value'=> $supplierID
			);
			
			$query_Jobs_for_supplier = new WP_Query();
			
			$results_Jobs_for_supplier = $query_Jobs_for_supplier->query($get_Jobs_for_suppliers_params);
			
			//if we have at least one job for this supplier
			if(sizeof($results_Jobs_for_supplier)>=1){
			
				//$JobsArray['found_any_Job'] = 'yes';
			
				foreach($results_Jobs_for_supplier as $single_Job_for_supplier){
					
					$Job_price = get_post_meta($single_Job_for_supplier->ID,'rdm_job_estimate_field_id',true);
					
					$JobsArray['Job_id']	= $single_Job_for_supplier->ID;
					$JobsArray['Job_title']	= $single_Job_for_supplier->post_title;
					$JobsArray['Job_price']	= $Job_price;
					
					$JobsArrayToReturn[] = $JobsArray;
				}
				
				//return job id , job title 
				die(json_encode($JobsArrayToReturn));
			
			} //end sizeof			
			
			die();
		}
		
		
		//job dropdown changed ... get tasks for the selected job
		if($_POST['whathappend']=='JobDropdownChanged'){
		
			$JobID = (int) $_POST['JobID'];

			
			//get all tasks for this job
			$get_tasks_for_Jobs_params =array(
				'showposts'=>-1,
				'post_type' => 'rdm_task',
				'post_status' => 'publish',
				'meta_key'=>'rdm_task_for_Job_field',
				'meta_value'=> $JobID
			);
			
			$query_tasks_of_Job = new WP_Query();
			
			$results_tasks_for_Job = $query_tasks_of_Job->query($get_tasks_for_Jobs_params);
			
			//if we have at least one tasks for this job
			if(sizeof($results_tasks_for_Job)>=1){
			
				foreach($results_tasks_for_Job as $single_task_for_Job){
				
					$task_status = get_post_meta($single_task_for_Job->ID,'rdm_task_status_task_field',true);
					
					$tasksArray['task_id']= $single_task_for_Job->ID;
					$tasksArray['task_title']= $single_task_for_Job->post_title;
					$tasksArray['task_price']= $single_task_for_Job->ID;
					
					$tasksArray['task_status'] = ($task_status) ? rdm_get_human_task_status_by_meta_value_as_bullet($task_status) : 'Not Set';
					
					$tasksArrayToReturn[] = $tasksArray;
				}
				
				//return task id , task title 
				die(json_encode($tasksArrayToReturn));
			
			} //end sizeof			
			
			die();
		}

		die();
	}
	
	
	/*
	*  Add,update,delete items on purchase
	*/
	function rdm_job_items_for_purchase_ajax(){

	
		if(!isset($_POST['purchaseAction']) || !isset($_POST['purchaseID'])){
			die('No purchaseAction action received');
		}
		
		$purchaseID = (int) $_POST['purchaseID'];
	
		//Get all items for existing INVOICE
		if($_POST['purchaseAction']=='getItemsForPurchase'){

		
			//print_r($this->get_items_of_purchase($purchaseID));
		
			$res = array('Result' => 'OK', 'Records' => $this->get_items_of_purchase($purchaseID,true));

			die (json_encode($res));
		
		}
		
		//Add new item to INVOICE
		if($_POST['purchaseAction']=='addItemToPurchase'){

			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			//set a random ID for this item 
			if(!isset($itemArray['purchaseRowId'])){
				$itemArray['purchaseRowId'] = time();
			}
			

			//calculate total for this item
			$itemArray['ItemTotalCost'] = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
			
			$rezi = $this->add_items_to_purchase($purchaseID,$itemArray);
			
			$actual_items = $this->get_items_of_purchase($purchaseID,true);
		
			//send back last item inserted via the END($array) 
			$res = array('Result' => 'OK' ,  'Record' => end($actual_items));
			
			die (json_encode($res));
		
		}	

		//update existing item of INVOICE
		if($_POST['purchaseAction']=='updateExistingItemOnPurchase'){
		
			$purchaseID 	= $_POST['purchaseID'];
			
			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			$actual_items = $this->get_items_of_purchase($purchaseID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['purchaseRowId'] == $itemArray['purchaseRowId']){

					$actual_items[$single_item_k]['ItemName']		= $itemArray['ItemName'];
					$actual_items[$single_item_k]['ItemUnitCost']	= $itemArray['ItemUnitCost'];
					$actual_items[$single_item_k]['ItemQuantity']	= $itemArray['ItemQuantity'];
					$actual_items[$single_item_k]['ItemTotalCost']  = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
					
				}

			}

			//delete all items
			delete_post_meta($purchaseID, '_items_on_purchase');

			//add the items
			update_post_meta($purchaseID, '_items_on_purchase',$actual_items);
			
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end update existing item	
		
		
		//remove existing item from INVOICE
		if($_POST['purchaseAction']=='removeExistingItemFromPurchase'){
		
			$purchaseID 	= $_POST['purchaseID'];
			$itemID 	= $_POST['purchaseItemID'];
		
			$actual_items = $this->get_items_of_purchase($purchaseID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['purchaseRowId'] == $itemID){

					unset($actual_items[$single_item_k]);
					
				}

			}
			
			$new_items_array = array_values($actual_items);

			//delete all items
			delete_post_meta($purchaseID, '_items_on_purchase');

			//add the items
			update_post_meta($purchaseID, '_items_on_purchase',$new_items_array);
		
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end remove existing item		
		
		die();
	}
	
	
	/*
	* Returns all items associated to an purchase
	*/
	function get_items_of_purchase($purchaseID,$as_single=false){
		
		if (!$purchaseID){
			return false;
		}
		
		$items_meta = get_post_meta($purchaseID,'_items_on_purchase',$as_single);
		
		if(!$items_meta){
		
			return false;
			
		} else {
			
			return $items_meta;
			
		}
		
	} 
	
	
	/*
	* Add items to purchase
	*/
	function add_items_to_purchase($purchaseID,$newItemData){
		
		//get actual items on purchase
		$actual_items = $this->get_items_of_purchase($purchaseID,true);
		
		$actual_items[]= $newItemData;
		
		if(update_post_meta($purchaseID,'_items_on_purchase',$actual_items)){
		
			return $this->get_items_of_purchase($purchaseID,true);
			
		}
		
			return false;

	}
	
	
	/*
	* Returns all "job" items in a given purchase
	*/
	function get_Jobs_items_on_purchase_ajax($purchase_id){
		
		$list_of_Jobs_in_purchase = array();
		$found_Jobs = 0;
		$response = array();
		$return_Job_array = array();
		
		$find_Jobs_in_purchase_items = get_post_meta($purchase_id,'_items_on_purchase',true);

		if($find_Jobs_in_purchase_items){
			foreach ($find_Jobs_in_purchase_items as $find_Jobs_in_purchase_item_single){
				foreach($find_Jobs_in_purchase_item_single as $find_Jobs_in_purchase_item_single_data_key => $find_Jobs_in_purchase_item_single_data_value){
					
					if(isset($find_Jobs_in_purchase_item_single_data_key)){
						if($find_Jobs_in_purchase_item_single_data_key=='is_Job'){
							$list_of_Jobs_in_purchase[]= $find_Jobs_in_purchase_item_single['purchaseRowId'];
							//$list_of_Jobs_in_purchase['Job_title']= $find_Jobs_in_purchase_item_single['ItemName'];
							//$list_of_Jobs_in_purchase['Job_price']= $find_Jobs_in_purchase_item_single['ItemTotalCost'];
							$return_Job_array[] = $list_of_Jobs_in_purchase;
						}
					}
					
				}
				
			}
		}

		//if we have any "job" on purchase
		if(count($list_of_Jobs_in_purchase) > 0){
			//return array of job id ... make values unique ... reset keys
			return array_values(array_unique($list_of_Jobs_in_purchase));
		}

		return false;

	}	
	
	

	/*
	* Get all jobs for supplier on purchase if supplier is associated with purchase.
	* Returns all jobs as checkbox for "related jobs" on "purchase" edit page
	*/
	function get_and_check_Jobs_checkbox_list_ajax(){

			if(!isset( $_POST['supplierID']) || !isset( $_POST['purchaseID'])){
				die();
			}
			
			$supplierID = (int) $_POST['supplierID'];
			$purchaseID = (int) $_POST['purchaseID'];
			
			$Jobs_on_purchase = array();
			
			if($purchaseID <= 0 || $supplierID <= 0 ){
				die();
			}
			
			//get all jobs for this supplier
				$get_Jobs_for_suppliers_params =array(
					'showposts'=>-1,
					'post_type' => 'rdm_job',
					'post_status' => 'publish',
					'meta_key'=>'rdm_job_supplier_field_id',
					'meta_value'=> $supplierID
				);
				
				
				
				$query_Jobs_for_supplier = new WP_Query();
				
				$results_Jobs_for_supplier = $query_Jobs_for_supplier->query($get_Jobs_for_suppliers_params);
				
				if(sizeof($results_Jobs_for_supplier)>=1){
				
					//we found jobs for supplier.Check if job is already on purchase
					if($this->get_Jobs_items_on_purchase_ajax($purchaseID)){
						$Jobs_on_purchase = $this->get_Jobs_items_on_purchase_ajax($purchaseID);
					}				
				
					foreach($results_Jobs_for_supplier as $single_Job_for_supplier){
						
						$Job_price = get_post_meta($single_Job_for_supplier->ID,'rdm_job_estimate_field_id',true);
						
						$JobsArray['Job_id']	= $single_Job_for_supplier->ID;
						$JobsArray['Job_title']	= $single_Job_for_supplier->post_title;
						$JobsArray['Job_price']	= $Job_price;
						
						//if job is also on purchase
						if(in_array($JobsArray['Job_id'],$Jobs_on_purchase)){
							$JobsArray['is_on_purchase_already']	= 'yes';
						}else{
							$JobsArray['is_on_purchase_already']	= 'no';
						}
						
						$JobsArrayToReturn[] = $JobsArray;
					}

					//($JobsArrayToReturn);
					//return job id , job title , Job_price
					die(json_encode($JobsArrayToReturn));	
					
				} //end sizeof

		
			
			die();
		
	} 
	
	
	/*
	* Save,update,delete purchase`s VAT,DISCOUNT
	*/
	
	function rdm_job_purchase_save_vat_discount_ajax(){
	
		$response = array('purchaseUpdated' => 'no');
		
		$purchaseID = $_POST['purchaseID'];
	
		if($purchaseID > 0 ){
			
			$purchase_array['vat'] 			= $_POST['vatValue'];
			$purchase_array['discountType'] 	= $_POST['discountType'];
			$purchase_array['discountValue'] = $_POST['discountValue'];
		
			//get purchase items
			$purchase_items = $this->get_items_of_purchase($purchaseID,true);
			
			if($purchase_items){

				//calculate subtotal ( without vat,discount )
				$subtotal = 0 ; 
				foreach($purchase_items as $single_purchase_item){
					$subtotal+=$single_purchase_item['ItemTotalCost'];
				}
				
				$purchase_array['purchase_subtotal'] = $subtotal;
				
				
				update_post_meta($purchaseID,'_purchase_discount_and_vat',$purchase_array);
				
				$response = array('purchaseUpdated' => 'yes');
			
			}else{
				
				$response = array('purchaseUpdated' => 'NoItemsOnPurchase');
				
			}

		}
		
		die(json_encode($response));

	}
	
	/*
	* Get saved Discount,Vat or return default 0
	*/
	static function get_vat_or_discount($what){
		
		global $post;
		
		$saved_post_meta = get_post_meta($post->ID,'_purchase_discount_and_vat',true);

		if($saved_post_meta){
			if(isset($saved_post_meta[$what])){
				return $saved_post_meta[$what];
			}
		}
		
		//get default VAT from SETTINGS
		if($what=='vat'){
			if(Rdm_Jobs_Settings_Option_Page::get('purchase_default_vat') > 0){
				return Rdm_Jobs_Settings_Option_Page::get('purchase_default_vat');
			}
		}
		
		//return failsafe value
		return 0;
	}
	
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('rdm_purchase'); 
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'some_meta_box_name'
					,__( 'Prepare purchase', 'simple-job-managment' )
					,array( $this, 'render_meta_box_content' )
					,$post_type
					,'advanced'
					,'high'
				);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 */
	public function save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['rdm_jobs_purchases_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['rdm_jobs_purchases_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'rdm_jobs_purchases_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'rdm_purchase' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		
		
		
		$save_purchase_notes_date_status = array();
		
		//save "show default terms also" on this purchase
		$save_purchase_notes_date_status['purchase_personal_notes'] 		= ($_POST['purchase_specific_personal_notes']!='') 			? esc_textarea($_POST['purchase_specific_personal_notes']) : '';
		
		if(isset($_POST['rdm_show_general_purchase_terms_also'])){
			$save_purchase_notes_date_status['show_default_terms'] 		= ($_POST['rdm_show_general_purchase_terms_also']=='false') 	? 'no' : 'yes';
		}
		
		
		$save_purchase_notes_date_status['specific_purchase_terms']		= ($_POST['purchase_specific_public_notes']!='') 		? esc_textarea($_POST['purchase_specific_public_notes']) : '';
		$save_purchase_notes_date_status['status']						= ($_POST['rdm_jobs_purchases_status_field_id']!='') 		? $_POST['rdm_jobs_purchases_status_field_id'] : 'unpaid';
		$save_purchase_notes_date_status['toBePaidOn']					= ($_POST['rdm_purchase_to_be_paid_by_date_field_id']!='') 		? $_POST['rdm_purchase_to_be_paid_by_date_field_id'] : '';
		$save_purchase_notes_date_status['paidOn']						= ($_POST['rdm_purchase_paid_date_field_id']!='') 	? $_POST['rdm_purchase_paid_date_field_id'] : '';
		$save_purchase_notes_date_status['purchase_currency']				= ($_POST['purchase_currency']!='') 	? $_POST['purchase_currency'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency');
		$save_purchase_notes_date_status['purchase_currency_position']	= ($_POST['purchase_currency_position']!='') 	? $_POST['purchase_currency_position'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency_position');
		
		update_post_meta($post_id,'rdm_jobs_purchases_supplier_field_id',$_POST['rdm_jobs_purchases_supplier_field_id']);

		update_post_meta($post_id,'_rdm_purchase_notes',$save_purchase_notes_date_status);


	}


	/**
	 * Show metabox 
	 */
	public function render_meta_box_content( $post ) {
		wp_nonce_field( 'rdm_jobs_purchases_box', 'rdm_jobs_purchases_box_nonce' );

		// Get existing meta
		$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

		?>
		
		<script type="text/javascript">

			jQuery(document).ready(function() {
				jQuery('#rdm_purchase_paid_date_field_id , #rdm_purchase_to_be_paid_by_date_field_id').datepicker({
				});
			});

		</script>
		
		
	
		
			<table class="form-table">
				<tbody>
					<tr>
						<td>
							<table class="form-table rdmPurchasePageTable">
								<tbody>
									
									<?php
										//get purchase meta
										
										if($post->ID){
											$purchase_notes_date_status = get_post_meta($post->ID,'_rdm_purchase_notes',true);
											$purchase_personal_note = (isset($purchase_notes_date_status['purchase_personal_notes'])) ? esc_textarea($purchase_notes_date_status['purchase_personal_notes']) : '';
											$purchase_specific_terms = (isset($purchase_notes_date_status['specific_purchase_terms'])) ? esc_textarea($purchase_notes_date_status['specific_purchase_terms']) : '';
											$purchase_show_default_terms = (isset($purchase_notes_date_status['show_default_terms'])) ? 'checked="checked"' : '';
											$purchase_status = (isset($purchase_notes_date_status['status'])) ? $purchase_notes_date_status['status'] : 'unpaid';
											$purchase_to_be_paid_on = (isset($purchase_notes_date_status['toBePaidOn'])) ? $purchase_notes_date_status['toBePaidOn'] : '';
											$purchase_paid_on = (isset($purchase_notes_date_status['paidOn'])) ? $purchase_notes_date_status['paidOn'] : '';
											$purchase_currency_position = (isset($purchase_notes_date_status['purchase_currency_position'])) ? $purchase_notes_date_status['purchase_currency_position'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency_position');
											
											$purchase_currency = (isset($purchase_notes_date_status['purchase_currency'])) ? $purchase_notes_date_status['purchase_currency'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency');
										}
									?>
									
									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_purchase_cpt_single_private_notes_label',__('Purchase Private Notes','simple-job-managment')); ?></div>
											<textarea name="purchase_specific_personal_notes" id="purchase_specific_personal_notes" style="width:100%" rows="5"><?php echo $purchase_personal_note;?></textarea>
										</td>

										<td class="at-field" style="vertical-align: top;" colspan="2" >
											<div> <?php echo apply_filters('albwppm_purchase_cpt_single_public_notes_label',__('Purchase public notes (visible on purchase)','simple-job-managment'));?> </div>
											<textarea name="purchase_specific_public_notes" id="purchase_specific_public_notes" style="width:100%"  rows="5"><?php echo $purchase_specific_terms; ?></textarea>
											<div>
												
												<input type="checkbox" name="rdm_show_general_purchase_terms_also" id="rdm_show_general_purchase_terms_also" <?php echo $purchase_show_default_terms;?> ><?php echo apply_filters('albwppm_purchase_cpt_single_show_general_terms_also_label',__('Show general terms also','simple-job-managment'));?>
											</div>
										</td>										

									</tr>

									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_purchase_cpt_single_purchase_status_label',__('Purchase status','simple-job-managment'));?> </div>
											<select class="at-posts-select" name="rdm_jobs_purchases_status_field_id" id="rdm_jobs_purchases_status_field_id">
												<option value="unpaid" 		<?php selected($purchase_status,'unpaid');?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_status_unpaid_dropdown_option_text',__('Unpaid','simple-job-managment'));?></option>
												<option value="paid"  		<?php selected($purchase_status,'paid');?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_status_paid_dropdown_option_text',__('Paid','simple-job-managment'));?></option>
												<option value="overdue" 	<?php selected($purchase_status,'overdue');?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_status_overdue_dropdown_option_text',__('Overdue','simple-job-managment'));?></option>
												<option value="cancelled"  	<?php selected($purchase_status,'cancelled');?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_status_cancelled_dropdown_option_text',__('Cancelled','simple-job-managment'));?></option>
											</select>
										</td>
										
										<td class="at-field"  style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_to_be_paid_by_date_label',__('To be paid by date','simple-job-managment'));?></div>
											<input type="text" name="rdm_purchase_to_be_paid_by_date_field_id" id="rdm_purchase_to_be_paid_by_date_field_id" rel="d MM, yy" value="<?php echo $purchase_to_be_paid_on;?>" size="30">
										</td>			
										<td class="at-field"  style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_purchase_cpt_single_purchase_paid_date_label',__('Paid Date','simple-job-managment'));?> </div>
											<input type="text" name="rdm_purchase_paid_date_field_id" id="rdm_purchase_paid_date_field_id"  value="<?php echo $purchase_paid_on;?>" size="30">
										</td>											
									</tr>
									
									<tr>
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_purchases_supplier_field_id"><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_supplier_label',__('Supplier','simple-job-managment'));?> </label>
											</div>
		
											<select class="at-posts-select" name="rdm_jobs_purchases_supplier_field_id" id="rdm_jobs_purchases_supplier_field_id">
												<option value="-1"><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_supplier_no_supplier_selected_option_text',__('No supplier selected','simple-job-managment'));?></option>
												<?php
													//get all suppliers
													$get_all_suppliers_params =	array(
																					'showposts'		=>	-1,
																					'post_type'		=>	'rdm_supplier',
																					'post_status'	=> 'publish',
													);
													
													$query_all_suppliers = new WP_Query();
													$results_all_suppliers = $query_all_suppliers->query($get_all_suppliers_params);
													
													//if we have a supplier
													if(sizeof($results_all_suppliers) >= 1 ){ 
														foreach($results_all_suppliers as $results_single_supplier){
														
															$selected = '';

															if($post->ID){
																//get supplier ID saved as meta of purchase
																$supplierIDOnPurchaseMeta = get_post_meta($post->ID,'rdm_jobs_purchases_supplier_field_id',true);
																if($supplierIDOnPurchaseMeta){
																	if($supplierIDOnPurchaseMeta == $results_single_supplier->ID){
																		$selected ='selected="selected" ';
																	}
																}
															}
														
															echo '<option value="'.$results_single_supplier->ID.'" '.$selected.'> '.$results_single_supplier->post_title.' </option>';
														}														
													}
												?>
											</select>
										</td>
										
									
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_purchases_Job_field_id">Related to job </label>
											</div>
									
											<select class="at-posts-select" name="rdm_jobs_purchases_Job_field_id" id="rdm_jobs_purchases_Job_field_id">
												<option value="-1">No job selected</option>
											</select>
										</td>
										
										
										
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_purchases_Job_list"><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_related_to_Job_label',__('Related to job','simple-job-managment'));?></label>
											</div>

											<span  id="rdm_jobs_purchases_Job_list" ></span>
											
										</td>										
										
										
									
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_purchases_task_field_id">Related to task </label>
											</div>

											<span  id="rdm_jobs_purchases_task_field_id" ></span>
											
										</td>
									

									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											<div id="PersonTableContainer"></div>
										</td>
										
									</tr>	
									<tr>

										<td class="at-field" valign="top" >
										
											<div>
												<div class="rdm_discount_title"><span > <?php echo apply_filters('albwppm_purchase_cpt_single_purchase_discount_label',__('Discount','simple-job-managment'));?> </span> </div>
												
												<div class="rdm_discount_form">
												
													<input type="text" name="purchase_discount_value" class="rdm_purchase_discount_fields" id="purchase_discount_value"  value="<?php echo Rdm_Purchase_Table_Metabox::get_vat_or_discount('discountValue') ;?>">
												
													<select name="purchase_discount_type" id="purchase_discount_type"  class="rdm_purchase_discount_fields" >
														<option value="none" 	<?php selected( Rdm_Purchase_Table_Metabox::get_vat_or_discount('discountType') ,'0' );?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_discount_none_option_text',__('None','simple-job-managment'));?></option>
														<option value="percent" <?php selected( Rdm_Purchase_Table_Metabox::get_vat_or_discount('discountType') , 'percent');?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_discount_percent_option_text',__('Percent','simple-job-managment'));?></option>
														<option value="amount" 	<?php selected( Rdm_Purchase_Table_Metabox::get_vat_or_discount('discountType') , 'amount' );?> ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_discount_amount_option_text',__('Amount','simple-job-managment'));?></option>
													</select>
													
												</div>
												
												<div class="rdm_clear"></div>
											</div>	
											
											<div>
												<div class="rdm_vat_title">
													<span ><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_vat_label',__('VAT','simple-job-managment'));?> </span>    
												</div>

												<div class="rdm_vat_form">
													<input type="text" name="purchase_vat_value"  class="rdm_purchase_discount_fields" id="purchase_vat_value"  value="<?php echo Rdm_Purchase_Table_Metabox::get_vat_or_discount('vat') ;?>"> %
												</div>
																					
												<div class="rdm_clear"></div>

											</div>
											
											<div style="display:none">
												<p><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_total_label',__('VAT','simple-job-managment'));?> <span id="totalPurchaseCost">0</span> </p>
											</div>
											
										</td>
										
										

										<td class="at-field" colspan="3" valign="top" style="vertical-align: top;">
												<div>
													<div class="rdm_vat_title">
														<span > <?php echo apply_filters('albwppm_purchase_cpt_single_purchase_currency_label',__('Currency','simple-job-managment'));?> </span>    
													</div>
													<div class="rdm_vat_form">
														<input type="text" name="purchase_currency"  class="rdm_purchase_discount_fields" id="purchase_currency"  value="<?php echo $purchase_currency ;?>"> 
													</div>
													<div class="rdm_clear"></div>
												</div>
												<div>
													<div class="rdm_vat_title">
														<span > <?php echo apply_filters('albwppm_purchase_cpt_single_purchase_currency_position_label',__('Currency Position','simple-job-managment'));?> </span>    
													</div>
													<div class="rdm_vat_form">
														<select  name="purchase_currency_position" id="purchase_currency_position">
															<option <?php selected( $purchase_currency_position, 'left'); ?>   value="left"><?php  echo apply_filters('albwppm_purchase_cpt_single_purchase_currency_position_left_of_price_option_text',__('Left of price','simple-job-managment'));?> </option >
															<option  <?php selected(  $purchase_currency_position, 'right'); ?>    value="right"><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_currency_position_right_of_price_option_text',__('Right of price','simple-job-managment'));?> </option>
													</select>
													</div>
													<div class="rdm_clear"></div>
												</div>
										</td>
										
												
										
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="4">
												<input type="button" value="<?php echo apply_filters('albwppm_purchase_cpt_single_purchase_apply_vat_discount_button_text',__('1. Apply VAT and discount','simple-job-managment'));?>" class="button button-primary button-large rdm_purchase_page_buttons" id="rdm_apply_vat_btn">
												
												<input type="button" id="rdm_preview_purchase" value="<?php echo apply_filters('albwppm_purchase_cpt_single_purchase_generate_purchase_button_text',__('Generate Purchase','simple-job-managment')) ?>" class="button button-primary button-large rdm_purchase_page_buttons">
												
												
												<form method="post">
													<input type="submit" id="rdm_jobs_download_purchase" name="rdm_jobs_download_purchase" value="<?php echo apply_filters('albwppm_purchase_cpt_single_purchase_download_purchase_button_text',__('3.Download Purchase','simple-job-managment'));?>" class="button button-primary button-large rdm_purchase_page_buttons">
												</form>

												<form method="post" action="">
													<input type="submit" id="rdm_jobs_submit_purchase_to_supplier" name="rdm_jobs_submit_purchase_to_supplier" value="<?php echo apply_filters('albwppm_purchase_cpt_single_purchase_submit_purchase_button_text',__('4.Send Purchase','simple-job-managment'));?>" class="button button-primary button-large rdm_purchase_page_buttons">
												</form>
												
												
												<span class="rdm_apply_vat_loader rdm_loading_ajax"><?php echo __('Loading','simple-job-managment') ?></span>
												
												
												<?php do_action('albwppm_before_purchase_preview'); ?>
												
												<div id="htmlForPdf"></div> 
												
										</td>
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											
											
												<span class="purchase_preview_text"><?php echo apply_filters('albwppm_purchase_cpt_single_purchase_pdf_purchase_preview_label',__('PDF Purchase Preview','simple-job-managment'));?></span>
											
												<div id="rdm_pdf_preview_in_browser"></div> 
											
												
													<script>
															jQuery(document).ready(function () {

																	//Check if we have already an purchase in DB
																	<?php
																		if(Rdm_Purchase_Table_Metabox::maybe_get_existing_pdf_data($post->ID)){
																			?>
																			
																				jQuery('#rdm_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,<?php echo Rdm_Purchase_Table_Metabox::maybe_get_existing_pdf_data($post->ID) ?>"></iframe>');
																			
																			<?php
																		}
																	?>
																	jQuery('#rdm_preview_purchase').click(function () {
																		
																		rdm_jobs_functions.disable_purchase_buttons();
																		
																		var supplierID = jQuery('#rdm_jobs_purchases_supplier_field_id').val();
																		jQuery.ajax({
																			url: ajaxurl,
																			type: 'POST',
																			data: {
																				specificNotes 		: jQuery('#purchase_specific_public_notes').val(),
																				privateNotes 		: jQuery('#purchase_specific_personal_notes').val(),
																				showDefaultTerms 	: jQuery('#rdm_show_general_purchase_terms_also').is(':checked'),
																				purchaseStatus 		: jQuery('#rdm_jobs_purchases_status_field_id').val(),
																				purchaseToBePaid		: jQuery('#rdm_purchase_to_be_paid_by_date_field_id').val(),
																				purchasePaidOnDate	: jQuery('#rdm_purchase_paid_date_field_id').val(),
																				purchaseCurrency		: jQuery('#purchase_currency').val(),
																				purchaseCurrencyPosition	: jQuery('#purchase_currency_position').val(),
																				action	  			: 'rdm_job_purchase_preview_pdf_ajax',
																				supplierID			: supplierID,
																				purchaseID 			: <?php echo (isset($post->ID)) ? $post->ID : 0 ; ?>
																				
																			},
																			success: function (data) {
																					jQuery('#rdm_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,'+data+'"></iframe>');
																					
																					rdm_jobs_functions.enable_purchase_buttons();
																			},
																			error: function () {
																					console.log('No PDF from server');		
																			}
																		});
																		
																		
																	});
																
															});
														</script>

										</td>
									</tr>									
									
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>	
		
		<script>
			var rdm_jobs_purchase_details;
		</script>
		
		<style>
			.select2-container-multi .select2-choices .select2-search-field input{
				height:inherit !important;
			}
		</style>
		
		<?php
	} //end render_metabox_content 
	
	
	


	/*
	* Template helper
	*/
	public  function locate_template($file,$atts=array()){

			$template_from_plugin_or_theme = Rdm_Jobs_Settings_Option_Page::get('pdf_template');

			if($template_from_plugin_or_theme=='theme'){
			//check if file exists on theme folder
				if (file_exists(TEMPLATEPATH . '/rdm_jobs/purchase_templates/template/'.$file)){
					$return_template = TEMPLATEPATH .'/rdm_jobs/purchase_templates/template/'.$file;
				}
			}
			else {
				
				//set default theme
				if($template_from_plugin_or_theme==''){
					$template_from_plugin_or_theme='template3';
				}
				
				//no overridings. use the templates from plugin folder
				$return_template = $this->mainPluginPath . '/purchase_templates/'.$template_from_plugin_or_theme.'/'.$file;
			}
			
			return $return_template;
		
	}

	
	/*
	* Get supplier infos based on purchase id
	*/
	public function get_supplier_info($purchaseID,$what){
	
		$no_supplier_found = apply_filters('albwppm_purchase_cpt_single_purchase_no_supplier_found_label',__('No Supplier Found','simple-job-managment'));
	
		$supplier_id = get_post_meta($purchaseID,'rdm_jobs_purchases_supplier_field_id',true);
		
		
		if(!$supplier_id || $supplier_id <= 0 ){
			return $no_supplier_found ;
		}
		
		if(!get_post($supplier_id)){
			return $no_supplier_found ;
		}

		
		
		if($what=='supplier_id'){
			return ($supplier_id > 0 ) ? $supplier_id :  '-1';
		}
		
		$return_string ='';
		
		if($what=='first_name'){
			
			$return_string = (get_post_meta($supplier_id,'rdm_supplier_first_name_field_id',true)!= false) ? get_post_meta($supplier_id,'rdm_supplier_first_name_field_id',true) : '' ;
		}
		
		if($what=='middle_name'){		
			$return_string =  (get_post_meta($supplier_id,'rdm_supplier_middle_name_field_id',true)!= false) ? get_post_meta($supplier_id,'rdm_supplier_middle_name_field_id',true) : '' ;		
		}
		
		if($what=='last_name'){		
			$return_string =  (get_post_meta($supplier_id,'rdm_supplier_last_name_field_id',true)!= false) ? get_post_meta($supplier_id,'rdm_supplier_last_name_field_id',true) : '' ;
		}
		
		if($what=='address'){		
			$return_string = (get_post_meta($supplier_id,'rdm_supplier_address_field_id',true)!= false) ? nl2br(get_post_meta($supplier_id,'rdm_supplier_address_field_id',true)) : '' ;		
		}
		
		
		return $return_string;
		
		
	}
	
	/*
	* Returns purchase specific currency or default currency
	*/
	public function get_currency($purchase_id=''){

		if($purchase_id!=''){
			return Rdm_Jobs_Purchase_Helpers::get_purchase_notes_value_by_purchase_id($purchase_id,'purchase_currency');
		}
		return Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency');
	}
	
	/*
	* Returns currency position ... left or right of the price
	*/
	public function is_currency_on_right_side_of_price($purchase_id=''){
		
		if($purchase_id!=''){
			return Rdm_Jobs_Purchase_Helpers::get_purchase_notes_value_by_purchase_id($purchase_id,'purchase_currency_position');
			
		}
		return  Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency_position');
	}
	
	/*
	* Returns currency and price 
	*/
	public function format_price_and_currency($price, $purchase_id=''){

		if($purchase_id!=''){
	
			if($this->is_currency_on_right_side_of_price($purchase_id)=='right'){
				return  $price . ' ' . $this->get_currency( $purchase_id) ;
			}
		
		}
		
		return apply_filters('albwppm_purchase_cpt_single_purchase_format_price_and_currency',$this->get_currency($purchase_id) . ' ' . $price, $this->get_currency($purchase_id) , $price);
	}
	
	/*
	* Generate PDF purchase
	*/
	public  function rdm_job_purchase_preview_pdf_ajax(){

		$purchase_id = (int) $_POST['purchaseID'];
		$supplierID   = $_POST['supplierID'];
		
		$download_pdf_file = false;
		
		if($purchase_id <= 0){
			die();
		}
	
		if(isset($_POST['download_pdf'])){
			$download_pdf_file=true;
		}
	
	
		//save specific notes,date,status for purchase
		$save_purchase_notes_date_status = array();
		
		//save "show default terms also" on this purchase
		$save_purchase_notes_date_status['purchase_personal_notes'] 	= ($_POST['privateNotes']!='') 			? esc_textarea($_POST['privateNotes']) : '';
		$save_purchase_notes_date_status['show_default_terms'] 		= ($_POST['showDefaultTerms']=='false') 	? 'no' : 'yes';
		$save_purchase_notes_date_status['specific_purchase_terms']	= ($_POST['specificNotes']!='') 		? esc_textarea($_POST['specificNotes']) : '';
		$save_purchase_notes_date_status['status']					= ($_POST['purchaseStatus']!='') 		? $_POST['purchaseStatus'] : 'unpaid';
		$save_purchase_notes_date_status['toBePaidOn']				= ($_POST['purchaseToBePaid']!='') 		? $_POST['purchaseToBePaid'] : '';
		$save_purchase_notes_date_status['paidOn']					= ($_POST['purchasePaidOnDate']!='') 	? $_POST['purchasePaidOnDate'] : 'yes';
		$save_purchase_notes_date_status['purchase_currency']			= ($_POST['purchaseCurrency']!='') 	? $_POST['purchaseCurrency'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency');
		$save_purchase_notes_date_status['purchase_currency_position'] = ($_POST['purchaseCurrencyPosition']!='') 	? $_POST['purchaseCurrencyPosition'] : Rdm_Jobs_Settings_Option_Page::get('purchase_default_currency_position');

		update_post_meta($purchase_id,'_rdm_purchase_notes',$save_purchase_notes_date_status);
	
		//Update supplier associated with purchase
		update_post_meta($purchase_id,'rdm_jobs_purchases_supplier_field_id',$supplierID);
		
	
	
		ob_start();
		
		//Company infos
		$company_name = Rdm_Jobs_Settings_Option_Page::get('company_name');
		
		if(Rdm_Jobs_Settings_Option_Page::get('company_logo_img')){
			$company_logo = '<img src="'. Rdm_Jobs_Settings_Option_Page::get('company_logo_img').'">';
		}else{
			$company_logo ='';
		}
		
		$company_address = nl2br(Rdm_Jobs_Settings_Option_Page::get('company_address'));
		
		$company_email = nl2br(Rdm_Jobs_Settings_Option_Page::get('company_email'));
		
		$company_website = nl2br(Rdm_Jobs_Settings_Option_Page::get('company_website'));
		
		$company_mobile = nl2br(Rdm_Jobs_Settings_Option_Page::get('company_mobile'));
		
		//Supplier infos
		$supplier_first_name 	= $this->get_supplier_info($purchase_id,'first_name');
		$supplier_middle_name = $this->get_supplier_info($purchase_id,'middle_name');
		$supplier_last_name 	= $this->get_supplier_info($purchase_id,'last_name');
		$supplier_address 	= $this->get_supplier_info($purchase_id,'address');


		//get purchase items meta
		$items_array = get_post_meta($purchase_id,'_items_on_purchase',true);
		
		//get purchase discount,vat etc
		$purchase_subtotal_meta = get_post_meta($purchase_id,'_purchase_discount_and_vat',true);
		
		//get purchase terms,status,dates
		$purchase_notes_date_status = get_post_meta($purchase_id,'_rdm_purchase_notes',true);		
		
		//get general purchase terms ...check if should be shown for this purchase
		if(isset($purchase_notes_date_status['show_default_terms'])){
			$purchase_general_terms_and_conditions = ( $purchase_notes_date_status['show_default_terms']=='yes' )  ?  nl2br(Rdm_Jobs_Settings_Option_Page::get('purchase_terms')) : '';
		}
		
		//get specific purchase terms 
		$purchase_specific_terms = (isset($purchase_notes_date_status['specific_purchase_terms'])) ? nl2br($purchase_notes_date_status['specific_purchase_terms']) : '';
		
		//Get Dompdf 
		require_once ( $this->mainPluginPath .'/assets/admin/dompdf/dompdf_config.inc.php');

		//read CSS file of template
		$css_content = file_get_contents($this->locate_template('style.css'));


		
		
		$vat = (isset($purchase_subtotal_meta['vat']) && $purchase_subtotal_meta['vat'] > 0 ) ? $purchase_subtotal_meta['vat'] . ' %'  :  '';

		$discount =''; 
		
		if(isset($purchase_subtotal_meta['discountType']) && $purchase_subtotal_meta['discountValue']> 0 ) {
			if(isset($purchase_subtotal_meta['discountType'])){
				
				//format the discount if its a number 
				if ( $purchase_subtotal_meta['discountType'] =='amount'){
					$discount = number_format(  $purchase_subtotal_meta['discountValue'] , 2 );
					
					$discount = $this->format_price_and_currency($discount,$purchase_id);
					
				}else {
					$discount =  $purchase_subtotal_meta['discountValue'] . ' %';
				}
				
			}
		}
		
		
		
		$subtotal = (isset($purchase_subtotal_meta['purchase_subtotal'])) ?  number_format ( $purchase_subtotal_meta['purchase_subtotal'],2) : '';
		
		$subtotal =  $this->format_price_and_currency($subtotal,$purchase_id);
		
		$subtotal_not_formated = (isset($purchase_subtotal_meta['purchase_subtotal'])) ?  $purchase_subtotal_meta['purchase_subtotal'] : '';
		
		if(isset($purchase_subtotal_meta['discountValue'])){
			if($purchase_subtotal_meta['discountValue'] > 0 &&  $purchase_subtotal_meta['discountType'] !='none') {

				$subtotal_after_discount =  number_format ( Rdm_Jobs_Purchase_Helpers::calculateDiscount($purchase_id,$subtotal_not_formated ,'newvalue'),2) ;
				$subtotal_after_discount =   $this->format_price_and_currency($subtotal_after_discount,$purchase_id);
				$subtotal_after_discount_not_formated =  Rdm_Jobs_Purchase_Helpers::calculateDiscount($purchase_id,$subtotal_not_formated ,'newvalue') ;

			}else{
				$subtotal_after_discount= false ;
				$subtotal_after_discount_not_formated = false;
			}
		}else{
			$subtotal_after_discount= false ;
			$subtotal_after_discount_not_formated = false;			
		}
		
		//if we have discount
		if($subtotal_after_discount == true && $subtotal_after_discount_not_formated > 0 ){
			$total  = number_format ($subtotal_after_discount_not_formated  +   ($subtotal_after_discount_not_formated * $purchase_subtotal_meta['vat']/100),2) ;
			
			$total = $this->format_price_and_currency($total,$purchase_id);
			
		}else{
			//dont have discount
			if (isset($purchase_subtotal_meta['vat']) && $purchase_subtotal_meta['vat'] > 0 ){
				$total_with_vat  = number_format ($subtotal_not_formated  +   ($subtotal_not_formated * $purchase_subtotal_meta['vat']/100),2) ;
			
				$total = $this->format_price_and_currency($total_with_vat,$purchase_id);
				
			}else{

				$total  = $this->format_price_and_currency(  $subtotal_not_formated   ,$purchase_id);
			
			}
		}

		
		ob_start();
		
		require ($this->locate_template('template.php'));
		
		$html = ob_get_clean();

		//print_r($html); die();

		$dompdf = new DOMPDF();
		$dompdf->load_html($html );
		$dompdf->render();

		$pdf_as_base64 = base64_encode($dompdf->output());
		
		//save purchase base64 data into DB
		update_post_meta($purchase_id,'rdm_jobs_purchases_pdf_base64',$pdf_as_base64);
		
		//$dompdf->stream("sample.pdf", array('Attachment'=>'0'));
		die($pdf_as_base64 );	
	}
	
	
	/*
	* Check and return if we have a purchase PDF base64 data in DB
	*/
	
	public function maybe_get_existing_pdf_data($purchaseID){
		
		if(isset($purchaseID)){
			
			$existing_data = get_post_meta($purchaseID,'rdm_jobs_purchases_pdf_base64',true);
			
			if($existing_data){
				return $existing_data;
			}
			
		}
		
		return false;
		
	}
	
	/*
	* Download PDF
	*/
	public function rdm_job_purchase_pdf_download(){
		
		if(isset($_POST['post_ID'])){
			if($_POST['post_ID'] <= 0){
				return;
			}else{
				$purchase_id = (int)$_POST['post_ID'];
			}
		}
		
		if(isset($_POST['rdm_jobs_download_purchase'])){

			$saved_pdf_base64 = get_post_meta($purchase_id,'rdm_jobs_purchases_pdf_base64',true);

			$saved_pdf_base64= $this->maybe_get_existing_pdf_data($purchase_id);
			
			if($saved_pdf_base64){

				$filename= apply_filters('albwppm_download_pdf_file_name', 'Purchase_'.$purchase_id.'.pdf' ,$purchase_id);
				header('Content-type: application/pdf');
				header('Content-disposition: attachment; filename="'.$filename.'"');
				echo base64_decode($saved_pdf_base64); 
				exit();
				
			}
		}

	}

	/*
	* Email Purchase
	*/

	public function rdm_job_submit_purchase_wp_mail_filter(){
		
		if(isset($_POST['post_ID'])){
			if($_POST['post_ID'] <= 0){
				return;
			}else{
				$purchase_id = (int)$_POST['post_ID'];
			}
		}
		
		if(isset($_POST['rdm_jobs_submit_purchase_to_supplier'])){

			function sendMail() {
				if($_POST['send']) {
					$sendto = esc_attr( get_option('custom_mail_to') );
					$sendfrom =  esc_attr( get_option('custom_mail_from') );
					$sendsub = esc_attr( get_option('custom_mail_sub') );
					$sendmess = esc_attr( get_option('custom_mail_message') );
					$headers = "From: Wordpress <" . $sendfrom . ">";
					wp_mail($sendto, $sendsub, $sendmess, $headers);
				}
			}

		}
	}

} //end class

//Start it all 
Rdm_Purchase_Table_Metabox::get_instance();	
<?php
/*
* Class to create the metabox for invoice tables
*/


class Rdm_Invoice_Table_Metabox {

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
		add_action( 'wp_ajax_rdm_job_invoice_cpt_backend_ajax', array( $this, 'rdm_job_invoice_cpt_backend_ajax'));
		
		//get items for existing invoice
		add_action( 'wp_ajax_rdm_job_items_for_invoice_ajax', array( $this, 'rdm_job_items_for_invoice_ajax'));
		
		//get items for existing invoice that have "is_Job=yes"
		add_action( 'wp_ajax_get_Jobs_items_on_invoice_ajax', array( $this, 'get_Jobs_items_on_invoice_ajax'));		
		
		//Get all jobs if a client is associated with invoice.
		add_action( 'wp_ajax_get_and_check_Jobs_checkbox_list_ajax', array( $this, 'get_and_check_Jobs_checkbox_list_ajax'));		
		
		//save invoice total,vat,discount
		add_action( 'wp_ajax_rdm_job_invoice_save_vat_discount_ajax', array( $this, 'rdm_job_invoice_save_vat_discount_ajax'));
		
		//add admin css,js
		add_action('admin_enqueue_scripts', array($this,'invoice_admin_css'));
		
		//add ajax for PDF preview
		add_action( 'wp_ajax_rdm_job_invoice_preview_pdf_ajax', array( $this, 'rdm_job_invoice_preview_pdf_ajax'));
		
		//remove default PUBLISH box
		//add_action( 'admin_menu', array($this,'remove_publish_box') );
		
		//Download PDF
		add_action( 'init',array($this,'rdm_job_invoice_pdf_download'));
		
		
	}
	
	
	/*
	* Localize the JS scripts 
	*/
	
	function localize_scripts(){
		
		$translation_array = array(
			'table_invoice_add_record_title' => apply_filters('albwppm_table_invoice_add_record_title',__('Add new item','simple-job-managment')),
			'table_invoice_line_total'		 => apply_filters('albwppm_table_invoice_line_total_title',__('Line total','simple-job-managment')),
			'table_invoice_line_quantity'	 => apply_filters('albwppm_table_invoice_line_quantity_title',__('Quantity','simple-job-managment')),
			'table_invoice_line_price'	 	 => apply_filters('albwppm_table_invoice_line_price_title',__('Unit Price','simple-job-managment')),
			'table_invoice_line_item'	 	 => apply_filters('albwppm_table_invoice_line_item_title',__('Item','simple-job-managment')),
			'table_invoice_items_on_invoice_title'	 	 => apply_filters('albwppm_table_invoice_items_on_invoice_title',__('Items on invoice','simple-job-managment')),
			'table_invoice_save_new_item_button_title'	 	 => apply_filters('albwppm_table_invoice_save_new_item_button_title',__('Save','simple-job-managment')),
			'table_invoice_cancel_new_item_button_title'	 	 => apply_filters('albwppm_table_invoice_cancel_new_item_button_title',__('Cancel','simple-job-managment')),
			'table_invoice_edit_item_popup_title'	 	 => apply_filters('albwppm_table_invoice_edit_item_popup_title',__('Edit item','simple-job-managment')),
			'table_invoice_delete_item_popup_title_are_you_sure'	 	 => apply_filters('albwppm_table_invoice_delete_item_popup_title_are_you_sure',__('Are you sure','simple-job-managment')),
			'table_invoice_delete_item_confirmation_text'	 	 => apply_filters('albwppm_table_invoice_delete_item_confirmation_text',__('This item will be deleted','simple-job-managment')),
			'table_invoice_delete_item_button_text'	 	 => apply_filters('albwppm_table_invoice_delete_item_button_text',__('Delete','simple-job-managment')),
			'table_invoice_no_data_available'	 	 => apply_filters('albwppm_table_invoice_no_data_available',__('No data available','simple-job-managment')),
			'table_invoice_loading_records'	 	 => apply_filters('albwppm_table_invoice_loading_records',__('Loading records','simple-job-managment')),
			'table_invoice_no_items_on_invoice'	 	 => apply_filters('albwppm_table_invoice_no_items_on_invoice',__('No items on invoice','simple-job-managment')),
		);

		wp_localize_script( 'rdm-job-invoice-page', 'albwppm', $translation_array );
	}
	
	
	public function remove_publish_box(){
		
		//remove_meta_box( 'submitdiv', 'rdm_invoice', 'side' );
		remove_meta_box( 'submitdiv', 'rdm_invoice', 'normal' ); // Publish meta box
		remove_meta_box( 'commentsdiv', 'rdm_invoice', 'normal' ); // Comments meta box
		remove_meta_box( 'revisionsdiv', 'rdm_invoice', 'normal' ); // Revisions meta box
		remove_meta_box( 'authordiv', 'rdm_invoice', 'normal' ); // Author meta box
		remove_meta_box( 'slugdiv', 'rdm_invoice', 'normal' );	// Slug meta box
		remove_meta_box( 'tagsdiv-post_tag', 'rdm_invoice', 'side' ); // Post tags meta box
		remove_meta_box( 'categorydiv', 'rdm_invoice', 'side' ); // Category meta box
		remove_meta_box( 'postexcerpt', 'rdm_invoice', 'normal' ); // Excerpt meta box
		remove_meta_box( 'formatdiv', 'rdm_invoice', 'normal' ); // Post format meta box
		remove_meta_box( 'trackbacksdiv', 'rdm_invoice', 'normal' ); // Trackbacks meta box
		remove_meta_box( 'postcustom', 'rdm_invoice', 'normal' ); // Custom fields meta box
		remove_meta_box( 'commentstatusdiv', 'rdm_invoice', 'normal' ); // Comment status meta box
		remove_meta_box( 'postimagediv', 'rdm_invoice', 'side' ); // Featured image meta box
		remove_meta_box( 'pageparentdiv', 'rdm_invoice', 'side' ); // Page attributes meta box
		
	}

	public function get_plugin_url() {
		return $this->plugin_url;
	}
	
	/*
	* Admin Scripts,Styles
	*/
	public function invoice_admin_css(){
		
		global $pagenow, $typenow;
		
		//which page are we
		$screen = get_current_screen();

		if( $typenow=='rdm_invoice' || $typenow =='rdm_job' || $typenow == 'rdm_task' || $typenow == 'rdm_client' ){
			wp_enqueue_script( 'at-meta-box', $this->get_plugin_url() .'meta-box-class/js/meta-box.js', array( 'jquery' ), null, true );
			wp_enqueue_style('at-multiselect-select2-css',  $this->get_plugin_url() . 'meta-box-class/js/select2/select2.css', array(), null);
			wp_enqueue_script('at-multiselect-select2-js', $this->get_plugin_url() . 'meta-box-class/js/select2/select2.js', array('jquery'), false, true);
		}

		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		if($screen->post_type =='rdm_invoice'){
			
			wp_enqueue_script('jquery-ui-datepicker');
		
			wp_enqueue_script( 'rdm-job-invoice-page', plugin_dir_url(dirname(__FILE__)) .'assets/admin/js/invoices.admin.min.js', array( 'jquery' ), null, false );
			
			//jTable
			wp_enqueue_script( 'rdm-job-invoice-jtable-jquery', plugin_dir_url(dirname(__FILE__)) .'assets/admin/jtable/jquery.jtable.js', array( 'jquery' ), null, true );	
			wp_enqueue_style('rdm-job-invoice-jtable-css',  plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/jquery-ui.css', array(), null);
			wp_enqueue_style('rdm-job-invoice-jtable-css3',   plugin_dir_url(dirname(__FILE__)) . 'assets/admin/jtable/themes/jqueryui/jtable_jqueryui.css', array(), null);
			wp_enqueue_style('rdm-job-invoice-jtable-css2', plugin_dir_url(dirname(__FILE__)) .   'assets/admin/jtable/themes/metro/blue/jtable.css', array(), null);
			
			$this->localize_scripts();
		
		}

	}	
	
	
	public function rdm_job_invoice_cpt_backend_ajax(){
		
		//if nothing set 
		if(!isset($_POST['whathappend'])){
			 die('NO whathappend action received');
		}
		
		//client dropdown changed ... find jobs related to client
		if($_POST['whathappend']=='clientDropdownChanged'){
			

			$clientID = (int) $_POST['clientID'];

			//$JobsArray['found_any_Job'] = 'no';
			
			//get all jobs for this client
			$get_Jobs_for_clients_params =array(
				'showposts'=>-1,
				'post_type' => 'rdm_job',
				'post_status' => 'publish',
				'meta_key'=>'rdm_job_client_field_id',
				'meta_value'=> $clientID
			);
			
			$query_Jobs_for_client = new WP_Query();
			
			$results_Jobs_for_client = $query_Jobs_for_client->query($get_Jobs_for_clients_params);
			
			//if we have at least one job for this client
			if(sizeof($results_Jobs_for_client)>=1){
			
				//$JobsArray['found_any_Job'] = 'yes';
			
				foreach($results_Jobs_for_client as $single_Job_for_client){
					
					$Job_price = get_post_meta($single_Job_for_client->ID,'rdm_job_estimate_field_id',true);
					
					$JobsArray['Job_id']	= $single_Job_for_client->ID;
					$JobsArray['Job_title']	= $single_Job_for_client->post_title;
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
	*  Add,update,delete items on invoice
	*/
	function rdm_job_items_for_invoice_ajax(){

	
		if(!isset($_POST['invoiceAction']) || !isset($_POST['invoiceID'])){
			die('No invoiceAction action received');
		}
		
		$invoiceID = (int) $_POST['invoiceID'];
	
		//Get all items for existing INVOICE
		if($_POST['invoiceAction']=='getItemsForInvoice'){

		
			//print_r($this->get_items_of_invoice($invoiceID));
		
			$res = array('Result' => 'OK', 'Records' => $this->get_items_of_invoice($invoiceID,true));

			die (json_encode($res));
		
		}
		
		//Add new item to INVOICE
		if($_POST['invoiceAction']=='addItemToInvoice'){

			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			//set a random ID for this item 
			if(!isset($itemArray['invoiceRowId'])){
				$itemArray['invoiceRowId'] = time();
			}
			

			//calculate total for this item
			$itemArray['ItemTotalCost'] = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
			
			$rezi = $this->add_items_to_invoice($invoiceID,$itemArray);
			
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
		
			//send back last item inserted via the END($array) 
			$res = array('Result' => 'OK' ,  'Record' => end($actual_items));
			
			die (json_encode($res));
		
		}	

		//update existing item of INVOICE
		if($_POST['invoiceAction']=='updateExistingItemOnInvoice'){
		
			$invoiceID 	= $_POST['invoiceID'];
			
			//itemData is in URL form ... convert to array
			parse_str($_POST['itemData'], $itemArray);
			
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['invoiceRowId'] == $itemArray['invoiceRowId']){

					$actual_items[$single_item_k]['ItemName']		= $itemArray['ItemName'];
					$actual_items[$single_item_k]['ItemUnitCost']	= $itemArray['ItemUnitCost'];
					$actual_items[$single_item_k]['ItemQuantity']	= $itemArray['ItemQuantity'];
					$actual_items[$single_item_k]['ItemTotalCost']  = $itemArray['ItemUnitCost'] * $itemArray['ItemQuantity'];
					
				}

			}

			//delete all items
			delete_post_meta($invoiceID, '_items_on_invoice');

			//add the items
			update_post_meta($invoiceID, '_items_on_invoice',$actual_items);
			
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end update existing item	
		
		
		//remove existing item from INVOICE
		if($_POST['invoiceAction']=='removeExistingItemFromInvoice'){
		
			$invoiceID 	= $_POST['invoiceID'];
			$itemID 	= $_POST['invoiceItemID'];
		
			$actual_items = $this->get_items_of_invoice($invoiceID,true);
			
			foreach($actual_items as $single_item_k => $single_item_v){
			
				if ($single_item_v['invoiceRowId'] == $itemID){

					unset($actual_items[$single_item_k]);
					
				}

			}
			
			$new_items_array = array_values($actual_items);

			//delete all items
			delete_post_meta($invoiceID, '_items_on_invoice');

			//add the items
			update_post_meta($invoiceID, '_items_on_invoice',$new_items_array);
		
			$res = array('Result' => 'OK');

			die (json_encode($res));
		
		} //end remove existing item		
		
		die();
	}
	
	
	/*
	* Returns all items associated to an invoice
	*/
	function get_items_of_invoice($invoiceID,$as_single=false){
		
		if (!$invoiceID){
			return false;
		}
		
		$items_meta = get_post_meta($invoiceID,'_items_on_invoice',$as_single);
		
		if(!$items_meta){
		
			return false;
			
		} else {
			
			return $items_meta;
			
		}
		
	} 
	
	
	/*
	* Add items to invoice
	*/
	function add_items_to_invoice($invoiceID,$newItemData){
		
		//get actual items on invoice
		$actual_items = $this->get_items_of_invoice($invoiceID,true);
		
		$actual_items[]= $newItemData;
		
		if(update_post_meta($invoiceID,'_items_on_invoice',$actual_items)){
		
			return $this->get_items_of_invoice($invoiceID,true);
			
		}
		
			return false;

	}
	
	
	/*
	* Returns all "job" items in a given invoice
	*/
	function get_Jobs_items_on_invoice_ajax($invoice_id){
		
		$list_of_Jobs_in_invoice = array();
		$found_Jobs = 0;
		$response = array();
		$return_Job_array = array();
		
		$find_Jobs_in_invoice_items = get_post_meta($invoice_id,'_items_on_invoice',true);

		if($find_Jobs_in_invoice_items){
			foreach ($find_Jobs_in_invoice_items as $find_Jobs_in_invoice_item_single){
				foreach($find_Jobs_in_invoice_item_single as $find_Jobs_in_invoice_item_single_data_key => $find_Jobs_in_invoice_item_single_data_value){
					
					if(isset($find_Jobs_in_invoice_item_single_data_key)){
						if($find_Jobs_in_invoice_item_single_data_key=='is_Job'){
							$list_of_Jobs_in_invoice[]= $find_Jobs_in_invoice_item_single['invoiceRowId'];
							//$list_of_Jobs_in_invoice['Job_title']= $find_Jobs_in_invoice_item_single['ItemName'];
							//$list_of_Jobs_in_invoice['Job_price']= $find_Jobs_in_invoice_item_single['ItemTotalCost'];
							$return_Job_array[] = $list_of_Jobs_in_invoice;
						}
					}
					
				}
				
			}
		}

		//if we have any "job" on invoice
		if(count($list_of_Jobs_in_invoice) > 0){
			//return array of job id ... make values unique ... reset keys
			return array_values(array_unique($list_of_Jobs_in_invoice));
		}

		return false;

	}	
	
	

	/*
	* Get all jobs for client on invoice if client is associated with invoice.
	* Returns all jobs as checkbox for "related jobs" on "invoice" edit page
	*/
	function get_and_check_Jobs_checkbox_list_ajax(){

			if(!isset( $_POST['clientID']) || !isset( $_POST['invoiceID'])){
				die();
			}
			
			$clientID = (int) $_POST['clientID'];
			$invoiceID = (int) $_POST['invoiceID'];
			
			$Jobs_on_invoice = array();
			
			if($invoiceID <= 0 || $clientID <= 0 ){
				die();
			}
			
			//get all jobs for this client
				$get_Jobs_for_clients_params =array(
					'showposts'=>-1,
					'post_type' => 'rdm_job',
					'post_status' => 'publish',
					'meta_key'=>'rdm_job_client_field_id',
					'meta_value'=> $clientID
				);
				
				
				
				$query_Jobs_for_client = new WP_Query();
				
				$results_Jobs_for_client = $query_Jobs_for_client->query($get_Jobs_for_clients_params);
				
				if(sizeof($results_Jobs_for_client)>=1){
				
					//we found jobs for client.Check if job is already on invoice
					if($this->get_Jobs_items_on_invoice_ajax($invoiceID)){
						$Jobs_on_invoice = $this->get_Jobs_items_on_invoice_ajax($invoiceID);
					}				
				
					foreach($results_Jobs_for_client as $single_Job_for_client){
						
						$Job_price = get_post_meta($single_Job_for_client->ID,'rdm_job_estimate_field_id',true);
						
						$JobsArray['Job_id']	= $single_Job_for_client->ID;
						$JobsArray['Job_title']	= $single_Job_for_client->post_title;
						$JobsArray['Job_price']	= $Job_price;
						
						//if job is also on invoice
						if(in_array($JobsArray['Job_id'],$Jobs_on_invoice)){
							$JobsArray['is_on_invoice_already']	= 'yes';
						}else{
							$JobsArray['is_on_invoice_already']	= 'no';
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
	* Save,update,delete invoice`s VAT,DISCOUNT
	*/
	
	function rdm_job_invoice_save_vat_discount_ajax(){
	
		$response = array('invoiceUpdated' => 'no');
		
		$invoiceID = $_POST['invoiceID'];
	
		if($invoiceID > 0 ){
			
			$invoice_array['vat'] 			= $_POST['vatValue'];
			$invoice_array['discountType'] 	= $_POST['discountType'];
			$invoice_array['discountValue'] = $_POST['discountValue'];
		
			//get invoice items
			$invoice_items = $this->get_items_of_invoice($invoiceID,true);
			
			if($invoice_items){

				//calculate subtotal ( without vat,discount )
				$subtotal = 0 ; 
				foreach($invoice_items as $single_invoice_item){
					$subtotal+=$single_invoice_item['ItemTotalCost'];
				}
				
				$invoice_array['invoice_subtotal'] = $subtotal;
				
				
				update_post_meta($invoiceID,'_invoice_discount_and_vat',$invoice_array);
				
				$response = array('invoiceUpdated' => 'yes');
			
			}else{
				
				$response = array('invoiceUpdated' => 'NoItemsOnInvoice');
				
			}

		}
		
		die(json_encode($response));

	}
	
	/*
	* Get saved Discount,Vat or return default 0
	*/
	static function get_vat_or_discount($what){
		
		global $post;
		
		$saved_post_meta = get_post_meta($post->ID,'_invoice_discount_and_vat',true);

		if($saved_post_meta){
			if(isset($saved_post_meta[$what])){
				return $saved_post_meta[$what];
			}
		}
		
		//get default VAT from SETTINGS
		if($what=='vat'){
			if(Rdm_Jobs_Settings_Option_Page::get('invoice_default_vat') > 0){
				return Rdm_Jobs_Settings_Option_Page::get('invoice_default_vat');
			}
		}
		
		//return failsafe value
		return 0;
	}
	
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('rdm_invoice'); 
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'some_meta_box_name'
					,__( 'Prepare invoice', 'simple-job-managment' )
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
		if ( ! isset( $_POST['rdm_jobs_invoices_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['rdm_jobs_invoices_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'rdm_jobs_invoices_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'rdm_invoice' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		
		
		
		$save_invoice_notes_date_status = array();
		
		//save "show default terms also" on this invoice
		$save_invoice_notes_date_status['invoice_personal_notes'] 		= ($_POST['invoice_specific_personal_notes']!='') 			? esc_textarea($_POST['invoice_specific_personal_notes']) : '';
		
		if(isset($_POST['rdm_show_general_invoice_terms_also'])){
			$save_invoice_notes_date_status['show_default_terms'] 		= ($_POST['rdm_show_general_invoice_terms_also']=='false') 	? 'no' : 'yes';
		}
		
		
		$save_invoice_notes_date_status['specific_invoice_terms']		= ($_POST['invoice_specific_public_notes']!='') 		? esc_textarea($_POST['invoice_specific_public_notes']) : '';
		$save_invoice_notes_date_status['status']						= ($_POST['rdm_jobs_invoices_status_field_id']!='') 		? $_POST['rdm_jobs_invoices_status_field_id'] : 'unpaid';
		$save_invoice_notes_date_status['toBePaidOn']					= ($_POST['rdm_invoice_to_be_paid_by_date_field_id']!='') 		? $_POST['rdm_invoice_to_be_paid_by_date_field_id'] : '';
		$save_invoice_notes_date_status['paidOn']						= ($_POST['rdm_invoice_paid_date_field_id']!='') 	? $_POST['rdm_invoice_paid_date_field_id'] : '';
		$save_invoice_notes_date_status['invoice_currency']				= ($_POST['invoice_currency']!='') 	? $_POST['invoice_currency'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency');
		$save_invoice_notes_date_status['invoice_currency_position']	= ($_POST['invoice_currency_position']!='') 	? $_POST['invoice_currency_position'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency_position');
		
		update_post_meta($post_id,'rdm_jobs_invoices_client_field_id',$_POST['rdm_jobs_invoices_client_field_id']);

		update_post_meta($post_id,'_rdm_invoice_notes',$save_invoice_notes_date_status);


	}


	/**
	 * Show metabox 
	 */
	public function render_meta_box_content( $post ) {
		wp_nonce_field( 'rdm_jobs_invoices_box', 'rdm_jobs_invoices_box_nonce' );

		// Get existing meta
		$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

		?>
		
		<script type="text/javascript">

			jQuery(document).ready(function() {
				jQuery('#rdm_invoice_paid_date_field_id , #rdm_invoice_to_be_paid_by_date_field_id').datepicker({
				});
			});

		</script>
		
		
	
		
			<table class="form-table">
				<tbody>
					<tr>
						<td>
							<table class="form-table rdmInvoicePageTable">
								<tbody>
									
									<?php
										//get invoice meta
										
										if($post->ID){
											$invoice_notes_date_status = get_post_meta($post->ID,'_rdm_invoice_notes',true);
											$invoice_personal_note = (isset($invoice_notes_date_status['invoice_personal_notes'])) ? esc_textarea($invoice_notes_date_status['invoice_personal_notes']) : '';
											$invoice_specific_terms = (isset($invoice_notes_date_status['specific_invoice_terms'])) ? esc_textarea($invoice_notes_date_status['specific_invoice_terms']) : '';
											$invoice_show_default_terms = (isset($invoice_notes_date_status['show_default_terms'])) ? 'checked="checked"' : '';
											$invoice_status = (isset($invoice_notes_date_status['status'])) ? $invoice_notes_date_status['status'] : 'unpaid';
											$invoice_to_be_paid_on = (isset($invoice_notes_date_status['toBePaidOn'])) ? $invoice_notes_date_status['toBePaidOn'] : '';
											$invoice_paid_on = (isset($invoice_notes_date_status['paidOn'])) ? $invoice_notes_date_status['paidOn'] : '';
											$invoice_currency_position = (isset($invoice_notes_date_status['invoice_currency_position'])) ? $invoice_notes_date_status['invoice_currency_position'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency_position');
											
											$invoice_currency = (isset($invoice_notes_date_status['invoice_currency'])) ? $invoice_notes_date_status['invoice_currency'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency');
										}
									?>
									
									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_invoice_cpt_single_private_notes_label',__('Invoice Private Notes','simple-job-managment')); ?></div>
											<textarea name="invoice_specific_personal_notes" id="invoice_specific_personal_notes" style="width:100%" rows="5"><?php echo $invoice_personal_note;?></textarea>
										</td>

										<td class="at-field" style="vertical-align: top;" colspan="2" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_public_notes_label',__('Invoice public notes (visible on invoice)','simple-job-managment'));?> </div>
											<textarea name="invoice_specific_public_notes" id="invoice_specific_public_notes" style="width:100%"  rows="5"><?php echo $invoice_specific_terms; ?></textarea>
											<div>
												
												<input type="checkbox" name="rdm_show_general_invoice_terms_also" id="rdm_show_general_invoice_terms_also" <?php echo $invoice_show_default_terms;?> ><?php echo apply_filters('albwppm_invoice_cpt_single_show_general_terms_also_label',__('Show general terms also','simple-job-managment'));?>
											</div>
										</td>										

									</tr>

									<tr>
										<td class="at-field" style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_label',__('Invoice status','simple-job-managment'));?> </div>
											<select class="at-posts-select" name="rdm_jobs_invoices_status_field_id" id="rdm_jobs_invoices_status_field_id">
												<option value="unpaid" 		<?php selected($invoice_status,'unpaid');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_unpaid_dropdown_option_text',__('Unpaid','simple-job-managment'));?></option>
												<option value="paid"  		<?php selected($invoice_status,'paid');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_paid_dropdown_option_text',__('Paid','simple-job-managment'));?></option>
												<option value="overdue" 	<?php selected($invoice_status,'overdue');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_overdue_dropdown_option_text',__('Overdue','simple-job-managment'));?></option>
												<option value="cancelled"  	<?php selected($invoice_status,'cancelled');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_status_cancelled_dropdown_option_text',__('Cancelled','simple-job-managment'));?></option>
											</select>
										</td>
										
										<td class="at-field"  style="vertical-align: top;" >
											<div><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_to_be_paid_by_date_label',__('To be paid by date','simple-job-managment'));?></div>
											<input type="text" name="rdm_invoice_to_be_paid_by_date_field_id" id="rdm_invoice_to_be_paid_by_date_field_id" rel="d MM, yy" value="<?php echo $invoice_to_be_paid_on;?>" size="30">
										</td>			
										<td class="at-field"  style="vertical-align: top;" >
											<div> <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_paid_date_label',__('Paid Date','simple-job-managment'));?> </div>
											<input type="text" name="rdm_invoice_paid_date_field_id" id="rdm_invoice_paid_date_field_id"  value="<?php echo $invoice_paid_on;?>" size="30">
										</td>											
									</tr>
									
									<tr>
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_invoices_client_field_id"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_client_label',__('Client','simple-job-managment'));?> </label>
											</div>
		
											<select class="at-posts-select" name="rdm_jobs_invoices_client_field_id" id="rdm_jobs_invoices_client_field_id">
												<option value="-1"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_client_no_client_selected_option_text',__('No client selected','simple-job-managment'));?></option>
												<?php
													//get all clients
													$get_all_clients_params =	array(
																					'showposts'		=>	-1,
																					'post_type'		=>	'rdm_client',
																					'post_status'	=> 'publish',
													);
													
													$query_all_clients = new WP_Query();
													$results_all_clients = $query_all_clients->query($get_all_clients_params);
													
													//if we have a client
													if(sizeof($results_all_clients) >= 1 ){ 
														foreach($results_all_clients as $results_single_client){
														
															$selected = '';

															if($post->ID){
																//get client ID saved as meta of invoice
																$clientIDOnInvoiceMeta = get_post_meta($post->ID,'rdm_jobs_invoices_client_field_id',true);
																if($clientIDOnInvoiceMeta){
																	if($clientIDOnInvoiceMeta == $results_single_client->ID){
																		$selected ='selected="selected" ';
																	}
																}
															}
														
															echo '<option value="'.$results_single_client->ID.'" '.$selected.'> '.$results_single_client->post_title.' </option>';
														}														
													}
												?>
											</select>
										</td>
										
										<!-- 
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_invoices_Job_field_id">Related to job </label>
											</div>
									
											<select class="at-posts-select" name="rdm_jobs_invoices_Job_field_id" id="rdm_jobs_invoices_Job_field_id">
												<option value="-1">No job selected</option>
											</select>
										</td>
										
										-->
										
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_invoices_Job_list"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_related_to_Job_label',__('Related to job','simple-job-managment'));?></label>
											</div>

											<span  id="rdm_jobs_invoices_Job_list" ></span>
											
										</td>										
										
										
										<!-- 
										<td class="at-field" valign="top">
											<div class="at-label">
												<label for="rdm_jobs_invoices_task_field_id">Related to task </label>
											</div>

											<span  id="rdm_jobs_invoices_task_field_id" ></span>
											
										</td>
										-->

									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											<div id="PersonTableContainer"></div>
										</td>
										
									</tr>	
									<tr>

										<td class="at-field" valign="top" >
										
											<div>
												<div class="rdm_discount_title"><span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_label',__('Discount','simple-job-managment'));?> </span> </div>
												
												<div class="rdm_discount_form">
												
													<input type="text" name="invoice_discount_value" class="rdm_invoice_discount_fields" id="invoice_discount_value"  value="<?php echo Rdm_Invoice_Table_Metabox::get_vat_or_discount('discountValue') ;?>">
												
													<select name="invoice_discount_type" id="invoice_discount_type"  class="rdm_invoice_discount_fields" >
														<option value="none" 	<?php selected( Rdm_Invoice_Table_Metabox::get_vat_or_discount('discountType') ,'0' );?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_none_option_text',__('None','simple-job-managment'));?></option>
														<option value="percent" <?php selected( Rdm_Invoice_Table_Metabox::get_vat_or_discount('discountType') , 'percent');?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_percent_option_text',__('Percent','simple-job-managment'));?></option>
														<option value="amount" 	<?php selected( Rdm_Invoice_Table_Metabox::get_vat_or_discount('discountType') , 'amount' );?> ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_discount_amount_option_text',__('Amount','simple-job-managment'));?></option>
													</select>
													
												</div>
												
												<div class="rdm_clear"></div>
											</div>	
											
											<div>
												<div class="rdm_vat_title">
													<span ><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_vat_label',__('VAT','simple-job-managment'));?> </span>    
												</div>

												<div class="rdm_vat_form">
													<input type="text" name="invoice_vat_value"  class="rdm_invoice_discount_fields" id="invoice_vat_value"  value="<?php echo Rdm_Invoice_Table_Metabox::get_vat_or_discount('vat') ;?>"> %
												</div>
																					
												<div class="rdm_clear"></div>

											</div>
											
											<div style="display:none">
												<p><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_total_label',__('VAT','simple-job-managment'));?> <span id="totalInvoiceCost">0</span> </p>
											</div>
											
										</td>
										
										

										<td class="at-field" colspan="3" valign="top" style="vertical-align: top;">
												<div>
													<div class="rdm_vat_title">
														<span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_label',__('Currency','simple-job-managment'));?> </span>    
													</div>
													<div class="rdm_vat_form">
														<input type="text" name="invoice_currency"  class="rdm_invoice_discount_fields" id="invoice_currency"  value="<?php echo $invoice_currency ;?>"> 
													</div>
													<div class="rdm_clear"></div>
												</div>
												<div>
													<div class="rdm_vat_title">
														<span > <?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_label',__('Currency Position','simple-job-managment'));?> </span>    
													</div>
													<div class="rdm_vat_form">
														<select  name="invoice_currency_position" id="invoice_currency_position">
															<option <?php selected( $invoice_currency_position, 'left'); ?>   value="left"><?php  echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_left_of_price_option_text',__('Left of price','simple-job-managment'));?> </option >
															<option  <?php selected(  $invoice_currency_position, 'right'); ?>    value="right"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_currency_position_right_of_price_option_text',__('Right of price','simple-job-managment'));?> </option>
													</select>
													</div>
													<div class="rdm_clear"></div>
												</div>
										</td>
										
												
										
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
												<input type="button" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_apply_vat_discount_button_text',__('1. Apply VAT and discount','simple-job-managment'));?>" class="button button-primary button-large rdm_invoice_page_buttons" id="rdm_apply_vat_btn">
												
												<input type="button" id="rdm_preview_invoice" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_generate_invoice_button_text',__('Generate Invoice','simple-job-managment')) ?>" class="button button-primary button-large rdm_invoice_page_buttons">
												
												
												<form method="post">
													<input type="submit" id="rdm_jobs_download_invoice" name="rdm_jobs_download_invoice" value="<?php echo apply_filters('albwppm_invoice_cpt_single_invoice_download_invoice_button_text',__('3.Download Invoice','simple-job-managment'));?>" class="button button-primary button-large rdm_invoice_page_buttons">
												</form>
												
												<span class="rdm_apply_vat_loader rdm_loading_ajax"><?php echo __('Loading','simple-job-managment') ?></span>
												
												
												<?php do_action('albwppm_before_invoice_preview'); ?>
												
												<div id="htmlForPdf"></div> 
												
										</td>
									</tr>
									
									<tr>
										<td class="at-field" valign="top" colspan="3">
											
											
												<span class="invoice_preview_text"><?php echo apply_filters('albwppm_invoice_cpt_single_invoice_pdf_invoice_preview_label',__('PDF Invoice Preview','simple-job-managment'));?></span>
											
												<div id="rdm_pdf_preview_in_browser"></div> 
											
												
													<script>
															jQuery(document).ready(function () {

																	//Check if we have already an invoice in DB
																	<?php
																		if(Rdm_Invoice_Table_Metabox::maybe_get_existing_pdf_data($post->ID)){
																			?>
																			
																				jQuery('#rdm_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,<?php echo Rdm_Invoice_Table_Metabox::maybe_get_existing_pdf_data($post->ID) ?>"></iframe>');
																			
																			<?php
																		}
																	?>
																	jQuery('#rdm_preview_invoice').click(function () {
																		
																		rdm_jobs_functions.disable_invoice_buttons();
																		
																		var clientID = jQuery('#rdm_jobs_invoices_client_field_id').val();
																		jQuery.ajax({
																			url: ajaxurl,
																			type: 'POST',
																			data: {
																				specificNotes 		: jQuery('#invoice_specific_public_notes').val(),
																				privateNotes 		: jQuery('#invoice_specific_personal_notes').val(),
																				showDefaultTerms 	: jQuery('#rdm_show_general_invoice_terms_also').is(':checked'),
																				invoiceStatus 		: jQuery('#rdm_jobs_invoices_status_field_id').val(),
																				invoiceToBePaid		: jQuery('#rdm_invoice_to_be_paid_by_date_field_id').val(),
																				invoicePaidOnDate	: jQuery('#rdm_invoice_paid_date_field_id').val(),
																				invoiceCurrency		: jQuery('#invoice_currency').val(),
																				invoiceCurrencyPosition	: jQuery('#invoice_currency_position').val(),
																				action	  			: 'rdm_job_invoice_preview_pdf_ajax',
																				clientID			: clientID,
																				invoiceID 			: <?php echo (isset($post->ID)) ? $post->ID : 0 ; ?>
																				
																			},
																			success: function (data) {
																					jQuery('#rdm_pdf_preview_in_browser').html('<iframe style="width:100%;height:400px" src="data:application/pdf;base64,'+data+'"></iframe>');
																					
																					rdm_jobs_functions.enable_invoice_buttons();
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
			var rdm_jobs_invoice_details;
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
				if (file_exists(TEMPLATEPATH . '/rdm_jobs/invoice_templates/template/'.$file)){
					$return_template = TEMPLATEPATH .'/rdm_jobs/invoice_templates/template/'.$file;
				}
			}
			else {
				
				//set default theme
				if($template_from_plugin_or_theme==''){
					$template_from_plugin_or_theme='template1';
				}
				
				//no overridings. use the templates from plugin folder
				$return_template = $this->mainPluginPath . '/invoice_templates/'.$template_from_plugin_or_theme.'/'.$file;
			}
			
			return $return_template;
		
	}

	
	/*
	* Get client infos based on invoice id
	*/
	public function get_client_info($invoiceID,$what){
	
		$no_client_found = apply_filters('albwppm_invoice_cpt_single_invoice_no_client_found_label',__('No Client Found','simple-job-managment'));
	
		$client_id = get_post_meta($invoiceID,'rdm_jobs_invoices_client_field_id',true);
		
		
		if(!$client_id || $client_id <= 0 ){
			return $no_client_found ;
		}
		
		if(!get_post($client_id)){
			return $no_client_found ;
		}

		
		
		if($what=='client_id'){
			return ($client_id > 0 ) ? $client_id :  '-1';
		}
		
		$return_string ='';
		
		if($what=='first_name'){
			
			$return_string = (get_post_meta($client_id,'rdm_client_first_name_field_id',true)!= false) ? get_post_meta($client_id,'rdm_client_first_name_field_id',true) : '' ;
		}
		
		if($what=='middle_name'){		
			$return_string =  (get_post_meta($client_id,'rdm_client_middle_name_field_id',true)!= false) ? get_post_meta($client_id,'rdm_client_middle_name_field_id',true) : '' ;		
		}
		
		if($what=='last_name'){		
			$return_string =  (get_post_meta($client_id,'rdm_client_last_name_field_id',true)!= false) ? get_post_meta($client_id,'rdm_client_last_name_field_id',true) : '' ;
		}
		
		if($what=='address'){		
			$return_string = (get_post_meta($client_id,'rdm_client_address_field_id',true)!= false) ? nl2br(get_post_meta($client_id,'rdm_client_address_field_id',true)) : '' ;		
		}
		
		
		return $return_string;
		
		
	}
	
	/*
	* Returns invoice specific currency or default currency
	*/
	public function get_currency($invoice_id=''){

		if($invoice_id!=''){
			return Rdm_Jobs_Invoice_Helpers::get_invoice_notes_value_by_invoice_id($invoice_id,'invoice_currency');
		}
		return Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency');
	}
	
	/*
	* Returns currency position ... left or right of the price
	*/
	public function is_currency_on_right_side_of_price($invoice_id=''){
		
		if($invoice_id!=''){
			return Rdm_Jobs_Invoice_Helpers::get_invoice_notes_value_by_invoice_id($invoice_id,'invoice_currency_position');
			
		}
		return  Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency_position');
	}
	
	/*
	* Returns currency and price 
	*/
	public function format_price_and_currency($price, $invoice_id=''){

		if($invoice_id!=''){
	
			if($this->is_currency_on_right_side_of_price($invoice_id)=='right'){
				return  $price . ' ' . $this->get_currency( $invoice_id) ;
			}
		
		}
		
		return apply_filters('albwppm_invoice_cpt_single_invoice_format_price_and_currency',$this->get_currency($invoice_id) . ' ' . $price, $this->get_currency($invoice_id) , $price);
	}
	
	/*
	* Generate PDF invoice
	*/
	public  function rdm_job_invoice_preview_pdf_ajax(){

		$invoice_id = (int) $_POST['invoiceID'];
		$clientID   = $_POST['clientID'];
		
		$download_pdf_file = false;
		
		if($invoice_id <= 0){
			die();
		}
	
		if(isset($_POST['download_pdf'])){
			$download_pdf_file=true;
		}
	
	
		//save specific notes,date,status for invoice
		$save_invoice_notes_date_status = array();
		
		//save "show default terms also" on this invoice
		$save_invoice_notes_date_status['invoice_personal_notes'] 	= ($_POST['privateNotes']!='') 			? esc_textarea($_POST['privateNotes']) : '';
		$save_invoice_notes_date_status['show_default_terms'] 		= ($_POST['showDefaultTerms']=='false') 	? 'no' : 'yes';
		$save_invoice_notes_date_status['specific_invoice_terms']	= ($_POST['specificNotes']!='') 		? esc_textarea($_POST['specificNotes']) : '';
		$save_invoice_notes_date_status['status']					= ($_POST['invoiceStatus']!='') 		? $_POST['invoiceStatus'] : 'unpaid';
		$save_invoice_notes_date_status['toBePaidOn']				= ($_POST['invoiceToBePaid']!='') 		? $_POST['invoiceToBePaid'] : '';
		$save_invoice_notes_date_status['paidOn']					= ($_POST['invoicePaidOnDate']!='') 	? $_POST['invoicePaidOnDate'] : 'yes';
		$save_invoice_notes_date_status['invoice_currency']			= ($_POST['invoiceCurrency']!='') 	? $_POST['invoiceCurrency'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency');
		$save_invoice_notes_date_status['invoice_currency_position'] = ($_POST['invoiceCurrencyPosition']!='') 	? $_POST['invoiceCurrencyPosition'] : Rdm_Jobs_Settings_Option_Page::get('invoice_default_currency_position');

		update_post_meta($invoice_id,'_rdm_invoice_notes',$save_invoice_notes_date_status);
	
		//Update client associated with invoice
		update_post_meta($invoice_id,'rdm_jobs_invoices_client_field_id',$clientID);
		
	
	
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
		
		//Client infos
		$client_first_name 	= $this->get_client_info($invoice_id,'first_name');
		$client_middle_name = $this->get_client_info($invoice_id,'middle_name');
		$client_last_name 	= $this->get_client_info($invoice_id,'last_name');
		$client_address 	= $this->get_client_info($invoice_id,'address');


		//get invoice items meta
		$items_array = get_post_meta($invoice_id,'_items_on_invoice',true);
		
		//get invoice discount,vat etc
		$invoice_subtotal_meta = get_post_meta($invoice_id,'_invoice_discount_and_vat',true);
		
		//get invoice terms,status,dates
		$invoice_notes_date_status = get_post_meta($invoice_id,'_rdm_invoice_notes',true);		
		
		//get general invoice terms ...check if should be shown for this invoice
		if(isset($invoice_notes_date_status['show_default_terms'])){
			$invoice_general_terms_and_conditions = ( $invoice_notes_date_status['show_default_terms']=='yes' )  ?  nl2br(Rdm_Jobs_Settings_Option_Page::get('invoice_terms')) : '';
		}
		
		//get specific invoice terms 
		$invoice_specific_terms = (isset($invoice_notes_date_status['specific_invoice_terms'])) ? nl2br($invoice_notes_date_status['specific_invoice_terms']) : '';
		
		//Get Dompdf 
		require_once ( $this->mainPluginPath .'/assets/admin/dompdf/dompdf_config.inc.php');

		//read CSS file of template
		$css_content = file_get_contents($this->locate_template('style.css'));


		
		
		$vat = (isset($invoice_subtotal_meta['vat']) && $invoice_subtotal_meta['vat'] > 0 ) ? $invoice_subtotal_meta['vat'] . ' %'  :  '';

		$discount =''; 
		
		if(isset($invoice_subtotal_meta['discountType']) && $invoice_subtotal_meta['discountValue']> 0 ) {
			if(isset($invoice_subtotal_meta['discountType'])){
				
				//format the discount if its a number 
				if ( $invoice_subtotal_meta['discountType'] =='amount'){
					$discount = number_format(  $invoice_subtotal_meta['discountValue'] , 2 );
					
					$discount = $this->format_price_and_currency($discount,$invoice_id);
					
				}else {
					$discount =  $invoice_subtotal_meta['discountValue'] . ' %';
				}
				
			}
		}
		
		
		
		$subtotal = (isset($invoice_subtotal_meta['invoice_subtotal'])) ?  number_format ( $invoice_subtotal_meta['invoice_subtotal'],2) : '';
		
		$subtotal =  $this->format_price_and_currency($subtotal,$invoice_id);
		
		$subtotal_not_formated = (isset($invoice_subtotal_meta['invoice_subtotal'])) ?  $invoice_subtotal_meta['invoice_subtotal'] : '';
		
		if(isset($invoice_subtotal_meta['discountValue'])){
			if($invoice_subtotal_meta['discountValue'] > 0 &&  $invoice_subtotal_meta['discountType'] !='none') {

				$subtotal_after_discount =  number_format ( Rdm_Jobs_Invoice_Helpers::calculateDiscount($invoice_id,$subtotal_not_formated ,'newvalue'),2) ;
				$subtotal_after_discount =   $this->format_price_and_currency($subtotal_after_discount,$invoice_id);
				$subtotal_after_discount_not_formated =  Rdm_Jobs_Invoice_Helpers::calculateDiscount($invoice_id,$subtotal_not_formated ,'newvalue') ;

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
			$total  = number_format ($subtotal_after_discount_not_formated  +   ($subtotal_after_discount_not_formated * $invoice_subtotal_meta['vat']/100),2) ;
			
			$total = $this->format_price_and_currency($total,$invoice_id);
			
		}else{
			//dont have discount
			if (isset($invoice_subtotal_meta['vat']) && $invoice_subtotal_meta['vat'] > 0 ){
				$total_with_vat  = number_format ($subtotal_not_formated  +   ($subtotal_not_formated * $invoice_subtotal_meta['vat']/100),2) ;
			
				$total = $this->format_price_and_currency($total_with_vat,$invoice_id);
				
			}else{

				$total  = $this->format_price_and_currency(  $subtotal_not_formated   ,$invoice_id);
			
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
		
		//save invoice base64 data into DB
		update_post_meta($invoice_id,'rdm_jobs_invoices_pdf_base64',$pdf_as_base64);
		
		//$dompdf->stream("sample.pdf", array('Attachment'=>'0'));
		die($pdf_as_base64 );	
	}
	
	
	/*
	* Check and return if we have a invoice PDF base64 data in DB
	*/
	
	public function maybe_get_existing_pdf_data($invoiceID){
		
		if(isset($invoiceID)){
			
			$existing_data = get_post_meta($invoiceID,'rdm_jobs_invoices_pdf_base64',true);
			
			if($existing_data){
				return $existing_data;
			}
			
		}
		
		return false;
		
	}
	
	/*
	* Download PDF
	*/
	public function rdm_job_invoice_pdf_download(){
		
		if(isset($_POST['post_ID'])){
			if($_POST['post_ID'] <= 0){
				return;
			}else{
				$invoice_id = (int)$_POST['post_ID'];
			}
		}
		
		if(isset($_POST['rdm_jobs_download_invoice'])){

			$saved_pdf_base64 = get_post_meta($invoice_id,'rdm_jobs_invoices_pdf_base64',true);

			$saved_pdf_base64= $this->maybe_get_existing_pdf_data($invoice_id);
			
			if($saved_pdf_base64){

				$filename= apply_filters('albwppm_download_pdf_file_name', 'Invoice_'.$invoice_id.'.pdf' ,$invoice_id);
				header('Content-type: application/pdf');
				header('Content-disposition: attachment; filename="'.$filename.'"');
				echo base64_decode($saved_pdf_base64); 
				exit();
				
			}
		}

	}
} //end class

//Start it all 
Rdm_Invoice_Table_Metabox::get_instance();	
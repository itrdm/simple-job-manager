<?php
/*
Plugin Name: Simple Job Manager
Plugin URI: https://profiles.wordpress.org/
Description: Simple job management this plugin 
Author: Rdm
Version: 1.2.1
Text Domain: simple-job-managment
Domain Path: /languages
Author URI: https://profiles.wordpress.org/
*/

add_action('plugins_loaded', 'rdm_load_textdomain');
function rdm_load_textdomain() {
    load_plugin_textdomain( 'simple-job-managment', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

class Rdm_Job_Management {

	private $plugin_slug ='simple-job-managment';
	private $singular_cpt_name = 'Job';
	private $plural_cpt_name = 'Jobs';
	private static $instance = null;
	private $plugin_path;
	private $plugin_url;

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
	 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
	 */	
	function __construct(){
	
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );	

		// Register different CPT_s
		add_action( 'init', array($this,'register_cpts'), 0 );

		//add custom columns to JobS
		add_action( 'manage_posts_custom_column' , array($this,'show_Job_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_job_columns' , array($this,'add_Job_custom_columns'), 10, 2 );
	
		//add custom columns to TASK
		add_action( 'manage_posts_custom_column' , array($this,'show_task_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_task_columns' , array($this,'add_task_custom_columns'), 10, 2 );	
		
		//add custom columns to CLIENTS
		add_action( 'manage_posts_custom_column' , array($this,'show_clients_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_client_columns' , array($this,'add_clients_custom_columns'), 10, 2 );	
		
		//add custom columns to INVOICES
		add_action( 'manage_posts_custom_column' , array($this,'show_invoice_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_invoice_columns' , array($this,'add_invoice_custom_columns'), 10, 2 );		
		
		//add custom columns to SUPPLIERS
		add_action( 'manage_posts_custom_column' , array($this,'show_suppliers_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_supplier_columns' , array($this,'add_suppliers_custom_columns'), 10, 2 );	
		
		//add custom columns to PURCHASES
		add_action( 'manage_posts_custom_column' , array($this,'show_purchase_custom_columns'), 10, 2 );
		add_action( 'manage_edit-rdm_purchase_columns' , array($this,'add_purchase_custom_columns'), 10, 2 );	
		
		//add ajax function to update client infos if associated with WP account
		add_action( 'wp_ajax_update_client_infos_if_associated_ajax', array( $this, 'update_client_infos_if_associated_ajax'));
		
		//add ajax function to update client infos if associated with WP account
		add_action( 'wp_ajax_update_supplier_infos_if_associated_ajax', array( $this, 'update_supplier_infos_if_associated_ajax'));
		
		//add admin css
		add_action('admin_enqueue_scripts', array($this,'simple_Job_managment_admin_css'));

		//add admin js
		add_action('in_admin_footer', array($this,'admin_footer'));
		
		$this->run_plugin();	
		
		//add metaboxes
		add_action( 'admin_menu', array( $this, 'albJob_add_meta_boxes' ) );

		//remove ADD JOB from menu on the left
		add_action('admin_menu', array($this,'remove_or_add_submenu_pages'));
		
		//do extra checks when our CPT-s are saved/updated
		add_action('save_post',array($this,'save_post'));
		
		//remove QUICK EDIT
		add_filter('post_row_actions',array($this,'remove_quick_edit'),10,2);
		
		
		require_once('include/helpers/invoice.helper.class.php');
		require_once('include/invoices.table.metabox.php');

		require_once('include/helpers/purchase.helper.class.php');
		require_once('include/purchases.table.metabox.php');
		
	}	
	
	/*
	* Remove QUIK EDIT on our CPT-s
	*/
	public function remove_quick_edit($actions ){
		
		global $post;
		
		if( $post->post_type == 'rdm_job' ||  $post->post_type == 'rdm_task'  ||  $post->post_type == 'rdm_client'  ||  $post->post_type == 'rdm_invoice' ||  $post->post_type == 'rdm_supplier'  ||  $post->post_type == 'rdm_purchase'  ) {
			
			unset($actions['inline hide-if-no-js']);
			
		}
		
		return $actions;
		
	}
	
	public function run_plugin(){
	
		require('include/helpers.php');
		require_once('include/meta-box-class/my-meta-box-class.php');

	}
	

	function admin_footer(){
	
		global $pagenow, $typenow;
		if( $typenow=='rdm_invoice' || $typenow =='rdm_job' || $typenow == 'rdm_task' || $typenow == 'rdm_client'  || $typenow == 'rdm_supplier' || $typenow=='rdm_purchase'){
			
			
			wp_register_script( 'rdm-jobs-admin-script', $this->get_plugin_url() . 'assets/admin/js/admin.js' );

			// Localize the script with new data
			$translation_array = array(
				'value_from_wp_user' => __( 'Value From Wordpress Account', 'simple-job-managment' ),
				
			);
			wp_localize_script( 'rdm-jobs-admin-script', 'rdmJobsadmin', $translation_array );

			// Enqueued script with localized data.
			wp_enqueue_script( 'rdm-jobs-admin-script' );
			
			//wp_enqueue_script( 'rdm-jobs-admin-script', $this->get_plugin_url() . 'assets/admin/js/admin.js', array( 'jquery' ), null, true );
		}

	}
	
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	public function get_plugin_path() {
		return $this->plugin_path;
	}	
	
	/*
	* Admin Scripts,Styles
	*/
	public function simple_Job_managment_admin_css(){
		
		global $pagenow, $typenow;
		
		if( $typenow=='rdm_invoice' || $typenow =='rdm_job' || $typenow == 'rdm_task' || $typenow == 'rdm_client' || $typenow == 'rdm_supplier' || $typenow == 'rdm_purchase' ){
			wp_enqueue_style('simple-job-managment-circular_admin', $this->get_plugin_url().'assets/admin/css/Jobs_admin.css');
		}
		 
		wp_register_script('simple-job-managment-circular-diagram',$this->get_plugin_url().'assets/circle-diagram/js/circle-progress.js' ,array( 'jquery' ) );
		wp_register_style('simple-job-managment-circular-diagram',$this->get_plugin_url().'assets/circle-diagram/css/style.css' );
	}
	
	/*
	* Return WP account infos if client is associated with an account
	*/
	public function update_client_infos_if_associated_ajax(){
		$userid = (int) $_POST['userID'];

		if( false == get_user_by( 'id', $userid ) ) {
			$return = array('rdm_found_user' => 'not');
		}else{
			
			$userFound = get_user_by( 'id', $userid );
		
			$return = array(
					'rdm_found_user' 	=>	'yes',
					'user_id' 				=>	$userFound->ID,
					'user_first_name' 		=>	$userFound->first_name,
					'user_last_name' 		=>	$userFound->last_name,
					'user_email' 			=>	$userFound->user_email,
					);
		}

		
		die(json_encode($return));
	}
	
	/*
	* Return WP account infos if supplier is associated with an account
	*/
	public function update_supplier_infos_if_associated_ajax(){
		$userid = (int) $_POST['userID'];

		if( false == get_user_by( 'id', $userid ) ) {
			$return = array('rdm_found_user' => 'not');
		}else{
			
			$userFound = get_user_by( 'id', $userid );
		
			$return = array(
					'rdm_found_user' 	=>	'yes',
					'user_id' 				=>	$userFound->ID,
					'user_first_name' 		=>	$userFound->first_name,
					'user_last_name' 		=>	$userFound->last_name,
					'user_email' 			=>	$userFound->user_email,
					);
		}

		
		die(json_encode($return));
	}
	
	/*
	* Register the different CPT_s 
	*/
	function register_cpts() {

				
		require_once('include/jobs.cpt.php');
		require_once('include/tasks.cpt.php');
		require_once('include/clients.cpt.php');
		require_once('include/invoices.cpt.php');		
		require_once('include/suppliers.cpt.php');
		require_once('include/purchases.cpt.php');	
		
		/*
		$jobs 	= new Rdm_Register_CPT('Job','Jobs');
		$tasks 		= new Rdm_Register_CPT('Task','Tasks','edit.php?post_type=rdm_job');
		$clients 	= new Rdm_Register_CPT('Client','Clients','edit.php?post_type=rdm_job');
		$invoices 	= new Rdm_Register_CPT('Invoice','Invoices','edit.php?post_type=rdm_job');
		*/
		//remove editor from INVOICE
		remove_post_type_support( 'rdm_invoice', 'editor' );
		remove_post_type_support( 'rdm_purchase', 'editor' );

		do_action('simple_Job_managment_add_new_cpt');
	}	
	
	//Remove "Add Job" from admin menu
	function remove_or_add_submenu_pages() { 
	
		remove_submenu_page('edit.php?post_type=rdm_job', 'post-new.php?post_type=rdm_job');
		
		//Add "Reports" as submenu to job
		add_submenu_page( 'edit.php?post_type=rdm_job', __('Reports','simple-job-managment'),  __('Reports','simple-job-managment'), 'manage_options', 'rdm-job-Reports', array($this,'reports_submenu_page_callback' ));		
		
		//Add "Settings" as submenu to job
		add_submenu_page( 'edit.php?post_type=rdm_job',  __('Settings','simple-job-managment'),  __('Settings','simple-job-managment'), 'manage_options', 'rdm-job-settings', array($this,'settings_submenu_page_callback' ));

	}	
	
	
	/*
	*	Add metaboxes to CPT_s
	*/	
	function albJob_add_meta_boxes(){
	
		if (is_admin()){

			require_once('include/meta-box-class/my-meta-box-class.php');
			
			//Jobs metaboxes
			require_once('include/jobs.metabox.php');

			//Task metaboxes
			require_once('include/tasks.metabox.php');
			
			//Clients metaboxes
			require_once('include/clients.metabox.php');

			//Suppliers metaboxes
			require_once('include/suppliers.metabox.php');
			
			//Purchases metaboxes
			//require_once('include/purchases.metabox.php');
			require_once('include/purchases.table.metabox.php');

			//Invoices metaboxes
			//require_once('include/invoices.metabox.php');
			require_once('include/invoices.table.metabox.php');

		} //end if is_admin
		
	}

	

	//Show additional columns on JOB list
	function show_Job_custom_columns( $column, $post_id ) {
		require('include/Jobs_list_extra_columns.php');		
	}
	
	//Show additional columns on TASK list
	function show_task_custom_columns( $column, $post_id ) {
		require('include/tasks_list_extra_columns.php');		
	}	

	//Show additional columns on CLIENTS list
	function show_clients_custom_columns( $column, $post_id){
		require('include/clients_list_extra_columns.php');
	}
	//Show additional columns on SUPPLIERS list
	function show_suppliers_custom_columns( $column, $post_id){
		require('include/suppliers_list_extra_columns.php');
	}
	function add_Job_custom_columns($columns) {
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 					=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_Job_text',__('Job','simple-job-managment'));
		$columns['deadline'] 				=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_deadline_text',__('Deadline','simple-job-managment'));
		$columns['status'] 	 				=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_status_text',__('Status','simple-job-managment'));
		$columns['get_tasks_for_Job'] 	=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_tasks_text',__('Tasks','simple-job-managment'));
		$columns['client']	 				=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_client_text',__('Client','simple-job-managment'));
		$columns['earnings'] 				=	apply_filters('albwppm_Jobs_cpt_list_post_table_header_earning_text',__('Earning','simple-job-managment'));

		return apply_filters('albwppm_Jobs_cpt_list_post_table_header_array',$columns);
		
		
	}

	
	function add_task_custom_columns($columns){
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 				= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_title_text',__('Task','simple-job-managment'));
		$columns['task_deadline'] 		= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_deadline_text',__('Task Deadline','simple-job-managment'));
		$columns['task_status'] 		= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_status_text',__('Task Status','simple-job-managment'));
		$columns['task_for_Job']	= 	apply_filters('albwppm_task_cpt_list_post_table_header_task_Job_text',__('Job','simple-job-managment'));
		
		return apply_filters('albwppm_tasks_cpt_list_post_table_header_array',$columns);

	}
	

	function add_clients_custom_columns($columns){

		//remove default WP date column
		unset($columns['date']);	
	
		$columns['title'] 								=	apply_filters('albwppm_clients_cpt_list_post_table_header_client_name',__('Client','simple-job-managment'));
		$columns['rdm_jobs_client_Jobs'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_Jobs',__('Jobs','simple-job-managment'));
		$columns['rdm_jobs_client_invoices'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_invoices',__('Invoices','simple-job-managment'));
		$columns['rdm_jobs_client_reviews'] 	=	apply_filters('albwppm_clients_cpt_list_post_table_header_reviews',__('Reviews','simple-job-managment'));		
		
		return apply_filters('albwppm_clients_cpt_list_post_table_header_array',$columns);

	}
	
	
	function show_invoice_custom_columns( $column, $post_id){
		require('include/invoice_list_extra_columns.php');
	}
	
	function add_invoice_custom_columns($columns){
	
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 										=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_name',__('Invoice','simple-job-managment'));
		$columns['rdm_jobs_invoice_total'] 			=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_total',__('Total','simple-job-managment'));
		$columns['rdm_jobs_invoice_status'] 			=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_status',__('Status','simple-job-managment'));
		$columns['rdm_jobs_invoice_for_client'] 		=	apply_filters('albwppm_invoice_cpt_list_post_table_header_invoice_client_name',__('Client','simple-job-managment'));

		return $columns;	
	}
	
	//Suppliers Custom Columns
	function add_suppliers_custom_columns($columns){

		//remove default WP date column
		unset($columns['date']);	
	
		$columns['title'] 								=	apply_filters('albwppm_suppliers_cpt_list_post_table_header_supplier_name',__('Supplier','simple-job-managment'));
		$columns['rdm_jobs_supplier_Jobs'] 	=	apply_filters('albwppm_suppliers_cpt_list_post_table_header_Jobs',__('Jobs','simple-job-managment'));
		$columns['rdm_jobs_supplier_purchases'] 	=	apply_filters('albwppm_suppliers_cpt_list_post_table_header_purchases',__('Purchases','simple-job-managment'));
		$columns['rdm_jobs_supplier_reviews'] 	=	apply_filters('albwppm_suppliers_cpt_list_post_table_header_reviews',__('Reviews','simple-job-managment'));		
		
		return apply_filters('albwppm_suppliers_cpt_list_post_table_header_array',$columns);

	}

	function show_purchase_custom_columns( $column, $post_id){
		require('include/purchase_list_extra_columns.php');
	}
	
	function add_purchase_custom_columns($columns){
	
		//remove default WP date column
		unset($columns['date']);
		
		$columns['title'] 										=	apply_filters('albwppm_purchase_cpt_list_post_table_header_purchase_name',__('Purchase','simple-job-managment'));
		$columns['rdm_jobs_purchase_total'] 			=	apply_filters('albwppm_purchase_cpt_list_post_table_header_purchase_total',__('Total','simple-job-managment'));
		$columns['rdm_jobs_purchase_status'] 			=	apply_filters('albwppm_purchase_cpt_list_post_table_header_purchase_status',__('Status','simple-job-managment'));
		$columns['rdm_jobs_purchase_for_supplier'] 		=	apply_filters('albwppm_purchase_cpt_list_post_table_header_purchase_client_name',__('Supplier','simple-job-managment'));

		return $columns;	
	}

	/*
	* Add "Reports" as submenu page
	*/
	function reports_submenu_page_callback(){
		require_once('include/admin_reports.class.php');
		require_once('include/helpers/invoice.helper.class.php');
		require_once('include/helpers/clients.helper.class.php');
		require_once('include/helpers/purchase.helper.class.php');
		require_once('include/helpers/suppliers.helper.class.php');

		require_once('include/helpers/tasks.helper.class.php');
		require_once('include/admin_reports_page.php');

	}
	
	
	/*
	* Add "Settings" as submenu to job
	*/
	function settings_submenu_page_callback(){
		require_once('include/admin_settings_page.php');
	}
	

	/*
	* Additional functions when our CPT-s are saved
	*/
	function save_post($postID){
	
		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']) || false !== wp_is_post_revision( $postID )){
			return;
		}
	
		if(get_post_type($postID) == 'rdm_job'){
			
			//start date
			if(isset($_POST['rdm_job_start_date_field_id'])){
				if($_POST['rdm_job_start_date_field_id']!=''){
					update_post_meta($postID,'rdm_job_start_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['rdm_job_start_date_field_id']));
				}
			}

			//target end date
			if(isset($_POST['rdm_job_target_end_date_field_id'])){
				if($_POST['rdm_job_target_end_date_field_id']!=''){
					update_post_meta($postID,'rdm_job_target_end_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['rdm_job_target_end_date_field_id']));
				}
			}			
			
			//end date
			if(isset($_POST['rdm_job_end_date_field_id'])){
				if($_POST['rdm_job_end_date_field_id']!=''){
					update_post_meta($postID,'rdm_job_end_date_field_id_timestamp',self::convert_human_date_to_unix($_POST['rdm_job_end_date_field_id']));
				}
			}
	
		}
	
	}
	


	/**
	 *  @brief Convert a date with format
	 *  
	 *  @param [in] $date 22-05-1981
	 *  @return unixtimestamp
	 *  
	 *  @details Details
	 */
	static function convert_human_date_to_unix($date){
		$converted_date = date_parse_from_format('d-m-Y', $date);
		$timestamp = mktime(0, 0, 0, $converted_date['month'], $converted_date['day'], $converted_date['year']);
		
		return $timestamp;
	}
	
}

//Start it all 
Rdm_Job_Management::get_instance();

$GLOBALS['kari'] = Rdm_Job_Management::get_instance();

//Options page helper class 
require_once('include/settings.options.class.php');





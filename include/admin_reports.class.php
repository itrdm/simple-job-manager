<?php
class Rdm_Jobs_Reports_Page{

	//Under which name to save the array of options
	static private $slug = 'rdm_job_reports_page_slug';

	
	static function get_slug(){
		return self::$slug;
	}
	
	
	/*
	* Admin Reports tabs
	*/
	static function admin_tabs($current=NULL){
	
		$my_plugin_tabs = array(
			'rdm-job-Reports&tab=overview' => __('Overview','simple-job-managment'),
			'rdm-job-Reports&tab=jobs' => __('Jobs','simple-job-managment'),
			'rdm-job-Reports&tab=clients' => __('Clients','simple-job-managment'),
			'rdm-job-Reports&tab=invoices' => __('Invoices','simple-job-managment'),

		);

	
		if(is_null($current)){
			if(isset($_GET['tab'])){
				$current = $_GET['tab'];
			}else{
				$current ='overview';
			}
		}
		
		
		$header_tabs = '';
		$header_tabs .= '<h2 class="nav-tab-wrapper">';
		
		foreach($my_plugin_tabs as $location => $tabname){
			
			
			$class = ($current == self::get_tab_slug_from_tab_url($location)) ? ' nav-tab-active' :  ''; 
			
			
			
			$header_tabs .= '<a class="nav-tab'.$class.'" href="?post_type=rdm_job&page='.$location.'">'.$tabname.'</a>';
		}
		
		$header_tabs .= '</h2>';
		
		switch($current){
			
			case 'overview' :
				$include_tab_content = 'overview_tab_content.php';
				break;
			
			case 'jobs' :
				$include_tab_content = 'Jobs_tab_content.php';
				break;
				
			case 'clients' :
				$include_tab_content = 'clients_tab_content.php';
				break;	
				
			case 'invoices' :
				$include_tab_content = 'invoices_tab_content.php';
				break;		
			
			default :
				$include_tab_content = 'overview_tab_content.php';
				
		}
				
		echo  $header_tabs;
		
		require_once('helpers/reports_pages/'.$include_tab_content);

	}	
	
	
	/*
	* Get tab slug from URL
	* So the translation doesnt interfer with active tab 
	*/
	
	public static function get_tab_slug_from_tab_url($url){
		
		preg_match('/rdm-job-Reports&tab=(.*)/',$url,$matched_string);
		return($matched_string[1]);
		
		
	}
	
	/*
	* Show report specific page 
	*/

	static function show_tab($which ='overview'){
		return self::admin_tabs($which);
	}
	
} //end class

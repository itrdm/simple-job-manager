<?php

switch ( $column ) {
	
	case 'rdm_jobs_client_reviews':
	
		$client_review = __('No reviews','simple-job-managment');
		$total_stars ='';
		
		$client_reviews_meta= get_post_meta($post_id,'rdm_client_review_field',true);
		switch ($client_reviews_meta){
		
			case 'client_review_5_star':
				$client_review = '5';
				break;
			
			case 'client_review_4_star':
				$client_review =  '4';
				break;
			
			case 'client_review_3_star':
				$client_review =  '3';
				break;
			
			case 'client_review_2_star':
				$client_review = '2';
				break;
				
			case 'client_review_1_star':
				$client_review = '1';
				break;		
				
			case 'client_no_review_set':
				$client_review = __('No reviews','simple-job-managment');
				break;					
		}
		
		if(is_numeric($client_review)){
			for($i=0;$i<$client_review;$i++){
				$total_stars.='<span style="color:rgb(255, 174, 10);font-size: 18px;">&#9733;</span>';
			}
			echo apply_filters('albwppm_client_reviews_star_icons',$total_stars,$client_review);
		}else{	
			echo  apply_filters('albwppm_client_reviews_no_review',$client_review);
		}
		
		break;

	case 'rdm_jobs_client_Jobs':
		require_once('helpers/jobs.helper.class.php');
		echo Rdm_Jobs_Job_Helpers::get_Jobs_for_client_extra_columns($post_id);
		break;
		
	case 'rdm_jobs_client_invoices':
		require_once('helpers/invoice.helper.class.php');
		echo Rdm_Jobs_Invoice_Helpers::get_invoices_for_client_extra_columns($post_id);
		break;
}
<?php

switch ( $column ) {
	
	case 'rdm_jobs_supplier_reviews':
	
		$supplier_review = __('No reviews','simple-job-managment');
		$total_stars ='';
		
		$supplier_reviews_meta= get_post_meta($post_id,'rdm_supplier_review_field',true);
		switch ($supplier_reviews_meta){
		
			case 'supplier_review_5_star':
				$supplier_review = '5';
				break;
			
			case 'supplier_review_4_star':
				$supplier_review =  '4';
				break;
			
			case 'supplier_review_3_star':
				$supplier_review =  '3';
				break;
			
			case 'supplier_review_2_star':
				$supplier_review = '2';
				break;
				
			case 'supplier_review_1_star':
				$supplier_review = '1';
				break;		
				
			case 'supplier_no_review_set':
				$supplier_review = __('No reviews','simple-job-managment');
				break;					
		}
		
		if(is_numeric($supplier_review)){
			for($i=0;$i<$supplier_review;$i++){
				$total_stars.='<span style="color:rgb(255, 174, 10);font-size: 18px;">&#9733;</span>';
			}
			echo apply_filters('albwppm_supplier_reviews_star_icons',$total_stars,$supplier_review);
		}else{	
			echo  apply_filters('albwppm_supplier_reviews_no_review',$supplier_review);
		}
		
		break;

 	case 'rdm_jobs_supplier_Jobs':
		require_once('helpers/jobs.helper.class.php');
		echo Rdm_Jobs_Job_Helpers::get_Jobs_for_supplier_extra_columns($post_id);
		break; 
		
	case 'rdm_jobs_supplier_purchases':
		require_once('helpers/purchase.helper.class.php');
		echo Rdm_Jobs_Purchase_Helpers::get_purchases_for_supplier_extra_columns($post_id);
		break;
}
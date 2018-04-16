<?php

switch ( $column ) {

	case 'rdm_jobs_purchase_total':

		$purchase_total_meta	= get_post_meta($post_id,'_purchase_discount_and_vat',true);
		if(isset($purchase_total_meta['purchase_subtotal'])){
			echo Rdm_Jobs_Invoice_Helpers::calculate_total($post_id,$purchase_total_meta['purchase_subtotal']);
		}else{
			 __('Not Set','simple-job-managment');
		}
		break;
		
	case 'rdm_jobs_purchase_status':
		$purchase_status_date_meta = get_post_meta($post_id,'_rdm_purchase_notes',true);
		echo  (isset($purchase_status_date_meta['status'])) ? ucfirst($purchase_status_date_meta['status']) :  __('Not Set','simple-job-managment');
		
		break;
		
	case 'rdm_jobs_purchase_for_supplier':
		$supplier_id = get_post_meta($post_id,'rdm_jobs_purchases_supplier_field_id',true);
		if($supplier_id){
			echo '<a href="'.get_edit_post_link($supplier_id).'">' . get_the_title($supplier_id) . '</a>';
		}else{
			 __('Not Set','simple-job-managment');
		}
		break;
		

}
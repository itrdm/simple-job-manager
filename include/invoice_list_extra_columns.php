<?php

switch ( $column ) {

	case 'rdm_jobs_invoice_total':

		$invoice_total_meta	= get_post_meta($post_id,'_invoice_discount_and_vat',true);
		if(isset($invoice_total_meta['invoice_subtotal'])){
			echo Rdm_Jobs_Invoice_Helpers::calculate_total($post_id,$invoice_total_meta['invoice_subtotal']);
		}else{
			 __('Not Set','simple-job-managment');
		}
		break;
		
	case 'rdm_jobs_invoice_status':
		$invoice_status_date_meta = get_post_meta($post_id,'_rdm_invoice_notes',true);
		echo  (isset($invoice_status_date_meta['status'])) ? ucfirst($invoice_status_date_meta['status']) :  __('Not Set','simple-job-managment');
		
		break;
		
	case 'rdm_jobs_invoice_for_client':
		$client_id = get_post_meta($post_id,'rdm_jobs_invoices_client_field_id',true);
		if($client_id){
			echo '<a href="'.get_edit_post_link($client_id).'">' . get_the_title($client_id) . '</a>';
		}else{
			 __('Not Set','simple-job-managment');
		}
		break;
		

}
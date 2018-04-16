<div class="wrap" style="background-color: #fff;padding: 20px;">
	
		<table class="form-table rdm_jobs_reports_page reports_overview">
			
			<tr valign="top">
				
				<td style="text-align:center">					
					<p class="diagram_title"><?php  echo __('Completed Jobs','simple-job-managment'); ?></p>
					<div class="rdm_jobs_diagram Jobs_progress"><strong></strong></div>	

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Total Jobs','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_all(); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>					

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Completed Jobs','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('completed'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>	
				
					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Lead Jobs','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('lead'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>
					
					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Ongoing Jobs','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('ongoing'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>	

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('On-hold Jobs','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('onhold'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>		

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Awaiting Feedback','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('awaiting_feedback'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>			
					
					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Status not set','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Job_Helpers::get_by_status('not_set'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>
					
					
				</td>
				
				<td style="text-align:center">		
				
					<p class="diagram_title"><?php  echo __('Completed Tasks','simple-job-managment'); ?></p>		
					<div class="rdm_jobs_diagram tasks_progress"><strong></strong> </div>	

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Total Tasks','simple-job-managment'); ?></p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Tasks_Helpers::get_all(); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>
					
					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Completed tasks','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Tasks_Helpers::get_by_status('completed'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>	

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Ongoing Tasks','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Tasks_Helpers::get_by_status('ongoing'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>			

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('On-hold Jobs','simple-job-managment'); ?>  </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Tasks_Helpers::get_by_status('onhold'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>		

					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Tasks not started','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Tasks_Helpers::get_by_status('not_started'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>					
					
				</td>	
				
				<td style="text-align:center">

					<p class="diagram_title"> <?php  echo __('Paid Invoices','simple-job-managment'); ?> </p>		
					<div class="rdm_jobs_diagram invoices_progress"><strong></strong></div>
					
				
					<div class="breakdown_container">
						<div class="paid_title"> <p> <?php  echo __('Income','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Invoice_Helpers::get_invoices_amount_by_status('paid'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>
					
					<div class="breakdown_container">
						<div class="unpaid_title"> <p><?php  echo __('Pending','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Invoice_Helpers::get_invoices_amount_by_status('unpaid'); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>					

					
					<div class="breakdown_container">
						<div class="breakdown_title"> <p> <?php  echo __('Total Invoices','simple-job-managment'); ?> </p></div>
						<div class="breakdown_value"> <p> <?php echo Rdm_Jobs_Invoice_Helpers::get_all(); ?> </p> </div>
						<div class="rdm_clear"></div>
					</div>
					
					<div class="breakdown_container">
						<div class="breakdown_title"> <?php  echo __('Paid Invoices','simple-job-managment'); ?> </div>
						<div class="breakdown_value"><?php echo Rdm_Jobs_Invoice_Helpers::get_by_status('paid'); ?></div>
						<div class="rdm_clear"></div>						
					</div>		

					<div class="breakdown_container">
						<div class="breakdown_title"> <?php  echo __('Unpaid Invoices','simple-job-managment'); ?> </div>
						<div class="breakdown_value"><?php echo Rdm_Jobs_Invoice_Helpers::get_by_status('unpaid'); ?></div>
						<div class="rdm_clear"></div>						
					</div>		

					<div class="breakdown_container">
						<div class="breakdown_title"> <?php  echo __('Overdue Invoices','simple-job-managment'); ?> </div>
						<div class="breakdown_value"><?php echo Rdm_Jobs_Invoice_Helpers::get_by_status('overdue'); ?></div>
						<div class="rdm_clear"></div>						
					</div>		

					<div class="breakdown_container">
						<div class="breakdown_title"> <?php  echo __('Cancelled Invoices','simple-job-managment'); ?> </div>
						<div class="breakdown_value"><?php echo Rdm_Jobs_Invoice_Helpers::get_by_status('cancelled'); ?></div>
						<div class="rdm_clear"></div>						
					</div>						

				</td>					
			</tr>			

			
			
		</table>
		
	
</div>

<script>
	jQuery(document).ready(function(){
	
		
		//Jobs % report
		jQuery('.rdm_jobs_reports_page .Jobs_progress').circleProgress({
			<?php if(Rdm_Jobs_Job_Helpers::get_all() > 0) { ?>
				value: <?php echo (Rdm_Jobs_Job_Helpers::get_by_status('completed') / Rdm_Jobs_Job_Helpers::get_all()); ?>,
			<?php } else { ?>
				value: 0,
			<?php } ?>
			size: 200,
			thickness:30,
			startAngle: -1.57,
			fill: {
			  gradient: ['#3aeabb', '#3aeabb']
			}}
			).on('circle-animation-progress', function(event, progress, stepValue) {
				jQuery(this).find('strong').html(parseInt(100 * stepValue) + '<i>%</i>');
			});
		
		
		//Tasks % report
		jQuery('.rdm_jobs_reports_page .tasks_progress').circleProgress({
			<?php if(Rdm_Jobs_Tasks_Helpers::get_all() > 0) { ?>
				value: <?php echo (Rdm_Jobs_Tasks_Helpers::get_completed() / Rdm_Jobs_Tasks_Helpers::get_all()); ?>,
			<?php }else{ ?>
				value: 0,
			<?php } ?>
			size: 200,
			thickness:30,
			startAngle: -1.57,
			fill: {
			  gradient: ['#3aeabb', '#3aeabb']
			}}
			).on('circle-animation-progress', function(event, progress, stepValue) {
				jQuery(this).find('strong').html(parseInt(100 * stepValue) + '<i>%</i>');
			});			
		
		
		
		//Invoices % report
		jQuery('.rdm_jobs_reports_page .invoices_progress').circleProgress({
			value: <?php echo Rdm_Jobs_Invoice_Helpers::get_paid_invoices_percent(); ?>,
			size: 200,
			thickness:30,
			startAngle: -1.57,
			fill: {
			  gradient: ['#3aeabb', '#3aeabb']
			}}
			).on('circle-animation-progress', function(event, progress, stepValue) {
				jQuery(this).find('strong').html(parseInt(100 * stepValue) + '<i>%</i>');
			});	
		
						
	});
</script>
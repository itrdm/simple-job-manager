<div class="wrap" style="background-color: #fff;padding: 20px;">
	<h1><?php echo __('Clients Tab','simple-job-managment') ?></h1>

		<table class="form-table rdm_jobs_reports_page">

			<tr valign="top">
				<td class="rdm_jobs_report_Jobs_tab_form">
					<form method="post" >

						<div>
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Client','simple-job-managment') ?></div>
								<?php Rdm_Jobs_Clients_Helpers::get_all_as_dropdown() ;?>
							</div>
							
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Name','simple-job-managment') ?></div>							
								<?php echo Rdm_Jobs_Clients_Helpers::create_input('first_name'); ?>
							</div>	
							
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Surname','simple-job-managment') ?></div>	
								<?php echo Rdm_Jobs_Clients_Helpers::create_input('last_name'); ?>				

							</div>								
							
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Email','simple-job-managment') ?></div>							
								<?php echo Rdm_Jobs_Clients_Helpers::create_input('email'); ?>	
							</div>
							
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Phone','simple-job-managment') ?></div>
									<?php echo Rdm_Jobs_Clients_Helpers::create_input('phone'); ?>
							</div>		
							
							<div class="rdm_input">
								<div class="rdm_input_header"><?php echo __('Mobile','simple-job-managment') ?></div>
									<?php echo Rdm_Jobs_Clients_Helpers::create_input('mobile'); ?>
							</div>
					
							<div class="rdm_input">
								<div class="rdm_input_header">Skype</div>
									<?php echo Rdm_Jobs_Clients_Helpers::create_input('skype'); ?>
							</div>									
							
							<div class="rdm_input">
								<div class="rdm_input_header">&nbsp;</div>							
								<input type="submit" class="button button-primary button-large" name="<?php echo Rdm_Jobs_Reports_Page::get_slug();?>"  value="<?php echo __('Search Clients','simple-job-managment') ?>">
							</div>
							
							<div class="rdm_clear"></div>
						</div>
						
					</form>
				</td>
			</tr>
		</table>
		
		
		<table>
			<tr>
				<td>
					<?php echo Rdm_Jobs_Clients_Helpers::get_results_for_report(); ?>
				</td>
			</tr>
		</table>
		
</div>


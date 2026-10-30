<div class="card-header">
    <h3 class="card-title"><?php echo $this->lang->line('radiology'); ?></h3>
</div>
<div class="card-body row">
    <ul class="reportlists"> 
		<?php  if ($this->module_lib->hasActive('radiology')) {
        if ($this->rbac->hasPrivilege('radiology_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/radio/radiologyreport'); ?>"><a href="<?php echo base_url(); ?>admin/radio/radiologyreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('radiology_patient_report'); ?></a></li>
		<?php
        }
        } ?>
		
		<?php  if ($this->module_lib->hasActive('radiology')) {
        if ($this->rbac->hasPrivilege('radiology_balance_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/radio/radiologybalancereport'); ?>"><a href="<?php echo base_url(); ?>admin/radio/radiologybalancereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('radiology_balance_report'); ?></a></li>
		<?php
        }
        } ?>   
    </ul>
</div>
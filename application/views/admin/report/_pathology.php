<div class="card-header">
    <h3 class="card-title"><?php echo $this->lang->line('pathology'); ?></h3>
</div>
<div class="card-body row">
    <ul class="reportlists">               
		<?php  if ($this->module_lib->hasActive('pathology')) {
        if ($this->rbac->hasPrivilege('pathology_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('reports/pathology/pathologyreport'); ?>"><a href="<?php echo base_url(); ?>admin/pathology/pathologyreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('pathology_patient_report'); ?></a></li>
		<?php
        }
        }?> 
		
		<?php  if ($this->module_lib->hasActive('pathology')) {
        if ($this->rbac->hasPrivilege('pathology_balance_report', 'can_view')) {
            ?>
			<li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('reports/pathology/pathologybalancereport'); ?>"><a href="<?php echo base_url(); ?>admin/pathology/pathologybalancereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('pathology_balance_report'); ?></a></li>
		<?php
        }
        }?>
    </ul>
</div>
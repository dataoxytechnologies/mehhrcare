<div class="card-header">
    <h3 class="card-title"><?php echo $this->lang->line('pharmacy'); ?></h3>
</div>
<div class="card-body row">
    <ul class="reportlists">
    <?php
        if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('pharmacy_bill_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/pharmacy/billreport'); ?>"><a href="<?php echo base_url(); ?>admin/pharmacy/billreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('pharmacy_balance_report'); ?></a></li>
    <?php
        }
        }

        if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/expmedicinereport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/expmedicinereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expiry_medicine_report'); ?></a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('stock_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/report/stock_report'); ?>"><a href="<?php echo base_url(); ?>admin/report/stock_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('stock_report'); ?></a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('medicine_purchase_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/medicinepurchasereport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/medicinepurchasereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('medicine_purchase_report'); ?></a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('medicine_purchase_return_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/medicinepurchasereturnreport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/medicinepurchasereturnreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('medicine_purchase_return_report'); ?></a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/report/salereturns'); ?>"><a href="<?php echo base_url(); ?>admin/report/salereturns"><i class="fa fa-file-text-o"></i> Sale Return Report</a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('medicine_sale_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/medicinesalereport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/medicinesalereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('medicine_sale_report'); ?></a></li>
    <?php
        }
        } if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('medicine_profit_loss_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/medicineprofitlossreport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/medicineprofitlossreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('medicine_profit_loss_report'); ?></a></li>
    <?php
        }
        } ?>
    </ul>
</div>
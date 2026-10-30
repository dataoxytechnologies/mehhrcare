<link rel="stylesheet" href="<?php echo base_url();?>backend\dist\css\jquery-ui.css">
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title mb-0"><?php echo $this->lang->line('opd_ipd_billing_through_case_id'); ?></h3>
                        <div class="ms-auto">
                            <div class="btn-group">
                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             <i class="fas fa-bars"></i></button>
                              <ul class="dropdown-menu s-bill-list">
                                <li class="s-bill-title"><?php echo $this->lang->line('single_module_billing'); ?></li>
                                 <?php if ($this->rbac->hasPrivilege('appointment_billing', 'can_view')) {?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/appointment');?>"><i class="fa fa-calendar-check-o me-1"></i><?php echo $this->lang->line('appointment'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) { ?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/opd');?>"><i class="fas fa-stethoscope me-1"></i> <?php echo $this->lang->line('opd'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('pathology_billing', 'can_view')) { ?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/pathology');?>"><i class="fas fa-flask me-1"></i> <?php echo $this->lang->line('pathology'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) { ?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/radiology');?>"><i class="fas fa-microscope me-1"></i> <?php echo $this->lang->line('radiology'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/issueblood');?>"><i class="fas fa-tint me-1"></i> <?php echo $this->lang->line('blood_issue'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li><a class="dropdown-item" href="<?php echo base_url('admin/bill/issuecomponent');?>"><i class="fas fa-burn me-1"></i> <?php echo $this->lang->line('blood_component_issue'); ?></a></li>
                            <?php } ?>
                              </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                            <form id="formsearch" accept-charset="utf-8" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                    <label class="mb-0 text-nowrap fw-semibold"><?php echo $this->lang->line('case_id'); ?><small class="req"> *</small></label>
                                    <div class="input-group sh-bill-caseid-search">
                                        <input type="text" name="case_id" class="form-control form-control-sm" id="case_id"
                                               value="<?php echo $case_id; ?>"
                                               placeholder="<?php echo $this->lang->line('enter_case_id'); ?>">
                                        <button type="submit" id="serach_btn" name="search" value="search_filter"
                                                class="btn btn-primary btn-sm checkbox-toggle">
                                            <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                    <div class="text-danger small"><?php echo form_error('search_type'); ?></div>
                                </div>
                            </form>
                            <div id="patient_details"></div>
                    </div>
                        <div class="nav-tabs-custom border0">
                            <div class="d-flex align-items-stretch nav-tabs-bar">
                            <ul class="nav nav-tabs navlistscroll flex-grow-1">
                                <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                                <li class="nav-item"><a class="nav-link active" href="#opd" data-bs-toggle="tab" onclick="load_opd_data()"><i class="fas fa-stethoscope me-1"></i><?php echo $this->lang->line('opd') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) { ?>
                                <li class="nav-item"><a class="nav-link" href="#ipd" data-bs-toggle="tab" onclick="load_ipd_data()"><i class="fas fa-bed me-1"></i><?php echo $this->lang->line('ipd') ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>
                                <li class="nav-item"><a class="nav-link" href="#pharmacy" data-bs-toggle="tab" onclick="load_pharmacy_data()"><i class="fas fa-pills me-1"></i><?php echo $this->lang->line('pharmacy') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                                <li class="nav-item"><a class="nav-link" href="#pathology" data-bs-toggle="tab" onclick="load_pathology_data()"><i class="fas fa-flask me-1"></i><?php echo $this->lang->line('pathology') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) { ?>
                                <li class="nav-item"><a class="nav-link" href="#radiology" data-bs-toggle="tab" onclick="load_radiology_data()"><i class="fas fa-x-ray me-1"></i><?php echo $this->lang->line('radiology') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li class="nav-item"><a class="nav-link" href="#blood_bank" data-bs-toggle="tab" onclick="load_blood_bank_data()"><i class="fas fa-tint me-1"></i><?php echo $this->lang->line('blood_issue') ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="#blood_components" data-bs-toggle="tab" onclick="load_blood_components_data()"><i class="fas fa-burn me-1"></i><?php echo $this->lang->line('component_issue') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                                <li class="nav-item"><a class="nav-link" href="#ambulance" data-bs-toggle="tab" onclick="load_ambulance_data()"><i class="fas fa-ambulance me-1"></i><?php echo $this->lang->line('ambulance') ?></a></li>
                                 <?php } ?>
                            </ul>
                            <div id="bill-action-tools" class="d-flex align-items-center gap-1 px-2 flex-shrink-0 nav-tools-bar"></div>
                            </div><!-- /.nav-tabs-bar -->
                            <div class="tab-content">
                                <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                                <div class="tab-pane active" id="opd">
                                </div>
                                <?php } if ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                                <!-- end opd -->
                                <!-- start ipd -->
                                <div class="tab-pane " id="ipd">                                    
                                </div> 
                                <?php } if ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>                              
                                <!-- end ipd --> 
                                <!-- start pharmacy -->
                                <div class="tab-pane" id="pharmacy">                                   
                                    <div>
                                        <table class="table table-striped table-bordered table-hover load_pharmacy" data-export-title="<?php echo $this->lang->line('pharmacy_bill_details'); ?>">
											<thead>
												<tr>
													<th><?php echo $this->lang->line('bill_no'); ?></th>                                 
													<th><?php echo $this->lang->line('date'); ?></th>                                  
													<th><?php echo $this->lang->line('doctor_name'); ?></th>  

                                                    <th class="text-end"><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>

													<th class="text-end"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                    
                                                    <th class="text-end"><?php echo $this->lang->line('tax') . " " . '(' . $currency_symbol . ')'; ?></th>
													
                                                    <th class="text-end"><?php echo $this->lang->line('net_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
													<th class="text-end"><?php echo $this->lang->line("paid_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
													<th class="text-end"><?php echo $this->lang->line("refund") . " " . '(' . $currency_symbol . ')'; ?></th>
                                                    <th class="text-end"><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
													<th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
												</tr>                                   
											</thead>
											<tbody>                                     
											</tbody>                                    
										</table>
                                    </div>
                                </div>  
                                <?php }?>       
            <!-- end pharmacy -->
            <!-- start pathology -->           
                                <div class="tab-pane" id="pathology">                                    
                                    <div>
                                        <table class="table table-striped table-bordered table-hover load_pathology" data-export-title="<?php echo $this->lang->line('pathology_bill_details'); ?>">
                                    <thead>
                                    <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>                            
                            <th><?php echo $this->lang->line('reporting_date'); ?></th>                            
                            <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                            <th><?php echo $this->lang->line('note'); ?></th> 
                            <th><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th><?php echo $this->lang->line('discount'); ?>(%)</th>
                            <th><?php echo $this->lang->line('tax'); ?>(%)</th>
                            <th><?php echo $this->lang->line('net_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                            <th><?php echo $this->lang->line('paid_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                            <th class="text-end" ><?php echo $this->lang->line('balance_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                            </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                    </div>
                                </div>
            <!-- end pthology -->
            <!-- start radiology -->
            
                                <div class="tab-pane" id="radiology">
                                    <div >
                                        <table class="table table-striped table-bordered table-hover load_radiology" data-export-title="<?php echo $this->lang->line('radiology_bill_details'); ?>">
                                            <thead>
                                                <th><?php echo $this->lang->line('bill_no'); ?></th>                                
                                                <th><?php echo $this->lang->line('reporting_date'); ?></th>                                            
                                                <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                                                <th><?php echo $this->lang->line('note'); ?></th>
                                                <th><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th><?php echo $this->lang->line('discount'); ?>(%)</th>
                                                <th><?php echo $this->lang->line('tax'); ?>(%)</th>
                                                <th><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th><?php echo $this->lang->line("paid_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end" ><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="blood_bank">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover load_blood_bank" data-export-title="<?php echo $this->lang->line('blood_bank_bill_details'); ?>">
                                    <thead>
                                     <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                                    <th><?php echo $this->lang->line('received_to'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <th><?php echo $this->lang->line('bag_no'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="blood_components">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover load_blood_components_bank" data-export-title="<?php echo $this->lang->line('blood_bank_bill_details'); ?>">
                                    <thead>
										 <tr>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('issue_date'); ?></th>
                                         <th><?php echo $this->lang->line('generated_by'); ?></th>
                                        <th><?php echo $this->lang->line('received_to'); ?></th>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <th><?php echo $this->lang->line('component'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('donor_name'); ?></th>
                                        <th><?php echo $this->lang->line('bags'); ?></th>
                                        
                                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="ambulance">                                    
                                    <div >
                                        <table class="table table-striped table-bordered table-hover load_ambulance" data-export-title="<?php echo $this->lang->line('ambulance_bill_details'); ?>">
                                            <thead>
                                                <th><?php echo $this->lang->line('ambulance_no'); ?></th>                               
                                                <th><?php echo $this->lang->line('vehicle_number'); ?></th>  
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('tax') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('net_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('paid_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('balance_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
            </div>
        </div>   <!-- /.row -->

<div class="modal fade sh-modal sh-modal-branded" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="action_detail_report_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportbilldata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_deletebill"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body min-h-3">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade sh-modal sh-modal-branded" id="addrefundPaymentModal" tabindex="-1" aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title"><?php echo $this->lang->line('payments'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='allpayments_print'></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body min-h-3">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="allpayments" tabindex="-1" aria-labelledby="allpaymentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allpaymentsLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='allpayments_print'></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="allpayments_result">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade sh-modal sh-modal-branded" id="generate_bill" tabindex="-1" aria-labelledby="generate_billLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generate_billLabel"><?php echo $this->lang->line('bill'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='refund_print'></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="bill_result">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="myPaymentModal" tabindex="-1" aria-labelledby="myPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myPaymentModalLabel"><?php echo $this->lang->line('add_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_payment" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <input type="hidden" name="module_id" id="module_id" class="form-control">
                        <input type="hidden" name="module_name" id="module_name" class="form-control">
                        <input type="hidden" name="case_reference_id" id="case_reference_id" class="form-control">
                        <div class="sh-form-card m-2">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-credit-card me-1"></i> <?php echo $this->lang->line('add_payment'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="payment_date" id="date" class="form-control datetime" autocomplete="off">
                                            <span class="text-danger"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                            <select class="form-control payment_mode" name="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) { ?>
                                                <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                            <input type="text" name="amount" id="amount" class="form-control">
                                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('note'); ?></label>
                                            <input type="text" name="note" id="note" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 cheque_div d-none" >
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                            <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                            <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 cheque_div d-none" >
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                            <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                            <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 cheque_div d-none" >
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('attach_document'); ?></label>
                                            <input type="file" class="filestyle form-control" name="document">
                                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_paymentbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>  

<div class="modal fade sh-modal sh-modal-nospace" id="billSummaryModal" tabindex="-1" aria-labelledby="billSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billSummaryModalLabel"><span id="patient_bill_summary"> </span> <?= $this->lang->line("bill_summary"); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="modal_action"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="billSummaryData">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
     $(document).on('change','.death_status',function(){
      var status=$(this).val();
      if(status == "1"){
         $('.filestyle','#addPaymentModal').dropify();
          $('.filestyle','#add_refund').dropify();
        $('.death_status_div').removeClass('d-none');
        $('.reffer_div').addClass('d-none');
      }else if(status == "2"){
        $('.reffer_div').removeClass('d-none');
         $('.death_status_div').addClass('d-none');
      }else{
        $('.reffer_div').addClass('d-none');
         $('.death_status_div').addClass('d-none');
      }
    });

     $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#addPaymentModal').dropify();
          $('.filestyle','#add_refund').dropify();
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });

    $(document).on('click','.add_payment',function(e){
        $('.cheque_div').addClass('d-none');
        $('#add_payment').trigger("reset");
        var record_id=$(this).data('recordId');
        var payment_module=$(this).data('module');
        var caseid =$(this).data('caseid');
        var amount =$(this).data('totalamount');
        $('#amount').val(amount);
        $('#module_id').val(record_id);
        $('#module_name').val(payment_module);
        $('#case_reference_id').val(caseid);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('myPaymentModal'), {backdrop:'static', keyboard:false}).show();
     });
	 
	   $(document).on('click','.add_bloodbankpayment',function(){  
            var record_id=$(this).data('recordId');
            var patient_id=$(this).data('patientId'); 
            var add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            getbloodbankPayments(record_id,patient_id);           

    });
	
   $(document).on('click','.payment_refund',function(){  
            if(get_case_id()!==0){               
               var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            $.ajax({
            url: base_url+'admin/bill/getrefund/'+get_case_id(),
            type: "POST",           
            dataType: 'json',
               beforeSend: function() {
               
               }, 
            success: function (data) {             
                
                 $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addrefundPaymentModal').dropify();
           $('.date','#addrefundPaymentModal').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

             error: function(xhr) { // if error occured
          alert("Error occured.please try again");            
               
      },
      complete: function() {
            payment_modal.removeClass('modal_loading');      
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    });

	  $(document).on('click','.patient_discharge',function(){  
            if(get_case_id()!==0){               
               var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            $.ajax({ 
            url: base_url+'admin/bill/patient_discharge/'+get_case_id(),
            type: "POST",
            data:{'module_type':'bill'},
            dataType: 'json',
               beforeSend: function() {
               
               }, 
            success: function (data) {              
                
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addrefundPaymentModal').dropify();
           $('.date','#addrefundPaymentModal').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

             error: function(xhr) { // if error occured
          alert("Error occured.please try again");            
               
      },
      complete: function() {
            payment_modal.removeClass('modal_loading');      
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    });

	function getbloodbankPayments(record_id,patient_id=null){
         var payment_modal=$('#addPaymentModal');
        
        $.ajax({
            url: '<?php echo base_url() ?>admin/bill/getBloodbankTransaction',
            type: "POST",
            data: {'id': record_id,'patient_id':patient_id},
            dataType:"JSON",
            beforeSend: function(){
   
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addPaymentModal').dropify();
            payment_modal.removeClass('modal_loading');               
          
            },
             error: function () {
           
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
          
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }
	
	 $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var billing_id=$("input[name='billing_id']",'#add_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,              
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getbloodbankPayments(billing_id);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

     $(document).on('submit','#add_partial_payment_ambulance', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var billing_id=$("input[name='billing_id']",'#add_partial_payment_ambulance').val();

            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,              
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);						 
                         getambulancePayments(billing_id);						 
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

      $(document).on('submit','#add_refund', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();			
			var amount = $('#amount').val();			 
			$('#replace_amount').html('<?php echo $currency_symbol; ?>' + amount );			
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        shModal("addrefundPaymentModal").hide();
                         
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

      $(document).on('click','.view_payment',function(e){        
        var record_id=$(this).data('recordId');
        var caseid =$(this).data('case_id');
         var module_type =$(this).data('module_type');
        $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/getpayment',
          type: "POST",
          data:{'case_id':caseid,'id':record_id,'module_type':module_type},
          dataType: 'json',
           beforeSend: function() {                 
             
          },
          success: function(res) {            
            $('#allpayments_result').html(res.page);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('allpayments'), {backdrop:'static', keyboard:false}).show();
          },
             error: function(xhr) { // if error occured
          alert("Error occured.please try again");                 
              
         },
              complete: function() {                  
                 
             }
      });        
     }); 

     $(document).ready(function (e) {
        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();         
            $.ajax({
                url: base_url+'admin/bill/makepayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                  $("#add_paymentbtn").btnLoading();
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        var module_name =$('#module_name').val();
                        if(module_name=='pharmacy'){
                            load_pharmacy_data();
                        }else if(module_name=='pathology'){
                            load_pathology_data();
                        }else if(module_name=='radiology'){
                            load_radiology_data();
                        }else if(module_name=='ipd'){
                            load_ipd_data();
                        }else if(module_name=='opd'){
                            load_opd_data();
                        }
                     
                        shModal('myPaymentModal').toggle();
                        $("#add_paymentbtn").btnReset();
                        shModal('myPaymentModal').hide();
                    }                    
                },
                error: function () {
                    $("#add_paymentbtn").btnReset();
                },
  
                complete: function(){
                    $("#add_paymentbtn").btnReset();                    
                }
            });
        }));
    }); 
 
    $(document).ready(function (e) {
            <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#opd"]'); if(_t) new bootstrap.Tab(_t).show();
                load_opd_data();
            <?php  }elseif ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#ipd"]'); if(_t) new bootstrap.Tab(_t).show();
                load_ipd_data();
            <?php  }elseif ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#pharmacy"]'); if(_t) new bootstrap.Tab(_t).show();
                load_pharmacy_data();
            <?php  }elseif($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#pathology"]'); if(_t) new bootstrap.Tab(_t).show();
                load_pathology_data();
            <?php  }elseif ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#radiology"]'); if(_t) new bootstrap.Tab(_t).show();
                load_radiology_data();
            <?php  }elseif ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#blood_bank"]'); if(_t) new bootstrap.Tab(_t).show();
                load_blood_bank_data();
                load_blood_components_data();
            <?php  }elseif ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                var _t = document.querySelector('[href="#ambulance"]'); if(_t) new bootstrap.Tab(_t).show();
                load_ambulance_data();
            <?php } ?>
            get_patientdetails('<?php echo $case_id; ?>');

        $("#formsearch").on('submit', (function (e) {
            $("#serach_btn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bill/get',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#opd"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_opd_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#ipd"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_ipd_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#pharmacy"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_pharmacy_data();
                        <?php  }elseif($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#pathology"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_pathology_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#radiology"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_radiology_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#blood_bank"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_blood_bank_data();
                            load_blood_components_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                            var _t = document.querySelector('[href="#ambulance"]'); if(_t) new bootstrap.Tab(_t).show();
                            load_ambulance_data();
                        <?php } ?>
                       get_patientdetails(data.case_id);
                    }
                    $("#serach_btn").btnReset();
                },
                error: function () {

                }
            });            
        }));

        // Clear action tools when switching to tabs that have no OPD/IPD action buttons
        $(document).on('show.bs.tab', '[data-bs-toggle="tab"]', function(e) {
            var target = $(e.target).attr('href');
            if (target !== '#opd' && target !== '#ipd') {
                $('#bill-action-tools').empty();
            }
        });
    });

    function get_patientdetails(case_id){
        $.ajax({
                url: base_url+'admin/bill/getDetailsByCaseId/'+case_id,
                type: "POST",
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if(data.status==1){
                        $('#patient_details').html(data.page);
                    }else{
                        errorMsg('<?php echo $this->lang->line("no_record_found"); ?>');
                        $('#patient_details').html('');
                    }
                },
                error: function () {

                }
            });
    }

    function get_case_id(){
      var case_id=$('#case_id').val();
       if (isNaN(case_id)) {
        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
        $('#case_id').val('');
        $('#patient_details').html('');
        return 0; 
        }else{
          if(case_id==''){ 
        return 0;
      }else{
        return case_id;
      }   
        } 
    }

    function load_opd_data(){
        if(get_case_id()!==0){            
            $.ajax({
            url: base_url+'admin/bill/getopd/'+get_case_id(),
            type: "POST",
           
            dataType: 'json',
               beforeSend: function() {
              
               },
            success: function (data) {

                $('#opd').html(data.page);
                var $tools = $('#opd .box-miustop').detach();
                $('#bill-action-tools').empty().append($tools.children());
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
      },
      complete: function() {            
     
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }      
    }

    function load_ipd_data(){
           if(get_case_id()!==0){
       $.ajax({
            url: base_url+'admin/bill/getipd/'+get_case_id(),
            type: "POST",
           
            dataType: 'json',
               beforeSend: function() {
              
               },
            success: function (data) {
                $('#ipd').html(data.page);
                var $tools = $('#ipd .box-miustop').detach();
                $('#bill-action-tools').empty().append($tools.children());
            },

             error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");           
               
      },
      complete: function() {            
     
      }
        }); 
         }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    }

     function load_pharmacy_data(){
          if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_pharmacy','admin/bill/getpharmacy/'+get_case_id(),[],[],100,
            [
            { "bSortable": false, "sWidth": "105px", "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
            {  "sWidth": "150px", "aTargets": [ 0, 1, 2 ] ,'sClass': 'sh-col-nowrap'},
             {  "sWidth": "105px", "aTargets": [  -1,-2,-3,-4,-5,-6,-7,-8] ,'sClass': 'dt-body-right'}
            ]
            );
    });
} ( jQuery ) ) 
         }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        }
    }

     function load_pathology_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_pathology','admin/bill/getpathology/'+get_case_id(),[],[],100,
            [
                  { "bSortable": false, "sWidth": "105px", "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
                  {  "sWidth": "150px", "aTargets": [ 0, 1, 2 ] ,'sClass': 'sh-col-nowrap'},
        {  "sWidth": "105px", "aTargets": [ -1,-2,-3,-4,-5,-6 ] ,'sClass': 'dt-body-right dt-head-right'},

            ]);
    });
} ( jQuery ) )
        }else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');
        }
    }

     function load_radiology_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_radiology','admin/bill/getradiology/'+get_case_id(),[],[],100,
            [
                  { "bSortable": false, "sWidth": "105px", "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
                  {  "sWidth": "150px", "aTargets": [ 0, 1, 2 ] ,'sClass': 'sh-col-nowrap'},
        {  "sWidth": "105px", "aTargets": [ -1,-2,-3,-4,-5,-6 ] ,'sClass': 'dt-body-right dt-head-right'},

            ]);
    });
} ( jQuery ) )
}else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');
        }
    }

    function load_blood_bank_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
                    initDatatable('load_blood_bank','admin/bill/getbloodbank/'+get_case_id(),[],[],100,
                        [
                            { "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3,-4,-5,-6] ,'sClass': 'dt-head-right dt-body-right'},
                            { 'sClass': 'sh-col-nowrap', 'aTargets': ['_all'] }
                        ]
                    );
    });
} ( jQuery ) )
}else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');
        }
    }

	function load_blood_components_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
                    initDatatable('load_blood_components_bank','admin/bill/getcomponentissuebycashidDatatable/'+get_case_id(),[],[],100,
                        [
                            { "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3,-4,-5,-6] ,'sClass': 'dt-head-right dt-body-right'},
                            { 'sClass': 'sh-col-nowrap', 'aTargets': ['_all'] }
                        ]
                    );
    });
} ( jQuery ) )
}else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');
        }
    }

    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        
        /* .date init removed - auto-init via event delegation */
    });

$(document).on('click','.print_charge',function(){    

      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('moduletype');         
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/printCharge',
          type: "POST",
          data:{'id':record_id,'type':moduletype},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();
              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

$(document).on('click','.print_transactions',function(){    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/printTransaction',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });
    
    $(document).on('click','.view_generate_bill',function(){    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/generate_bill_result',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('generate_bill'), {backdrop:'static', keyboard:false}).show();
            $('#refund_print').html(' <a href="javascript:void(0);" class="btn btn-sm btn-light generate_bill" data-bs-toggle="tooltip" title="<?php echo $this->lang->line("print_bill"); ?>" data-module_type="ipd_opd" data-case_id="'+case_id+'"><i class="fa fa-print"></i> </a> ');
           $('#bill_result').html(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

    $(document).on('click','.generate_bill',function(){    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/generate_bill',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });
    
     $(document).on('click','.print_bill',function(){
        let case_id=$(this).data('caseId');      
        var $this = $(this);
         $.ajax({
            url: base_url+'admin/bill/print_patient_bill',
            type: "POST",
            data: {'case_id': case_id},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
            },
            success: function (data) {                          
              popup(data.page);
              $this.btnReset();
            },
             error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                $this.btnReset();               
            },
            complete: function() {
            $this.btnReset();
            }
        });
});

 $(document).on('click','.print_radio_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getBillDetails/',
            type: "POST",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.btnLoading();
            },
            complete: function(){
                $print_btn.btnReset();
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.btnReset();
            },
             error: function () {
                     $print_btn.btnReset();
                }
        });
    });

  $(document).on('click','.print_pharmacy_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.btnLoading();
            },
            complete: function(){
                $print_btn.btnReset();
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.btnReset();
            },
             error: function () {
                     $print_btn.btnReset();
                }
        });
    });
	
  $(document).on('click','.print_bloodbank_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({ 
          url: '<?php echo base_url(); ?>admin/bloodbank/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

    $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/radio/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

 $(document).on('click','.print_trans',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/transaction/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

    function printbloodbankData(id) {     
           let $this = $('.print_blood_issue');
         
       $this.btnLoading();
      $.ajax({
          url: base_url+'admin/bloodbank/printBloodIssueBill',
          type: "POST",
          data:{'id':id},
          dataType: 'json',
           beforeSend: function() {
           $this.btnLoading();      
          },
          success: function(res) {
     popup(res.page);       
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        $this.btnReset();              
         },
              complete: function() {
    $this.btnReset();                 
             }
      })
    }

    function printAmbulanceData(id){
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }

    $(document).on('click','.add_ambulancecallpayment',function(){          
        var record_id=$(this).data('recordId'); 
        var $add_btn= $(this);  
        var payment_modal=$('#addPaymentModal');
        payment_modal.addClass('modal_loading'); 
        shModal(payment_modal[0]).show(); 
        getambulancePayments(record_id);
    });
 
    function getambulancePayments(record_id){
        var payment_modal=$('#addPaymentModal');
        var patient_referance_case_id="<?php echo $case_id; ?>";
        $.ajax({
            url: '<?php echo base_url() ?>admin/bill/getAmbulanceCallTransaction',
            type: "POST",
            data: {'id': record_id,'patient_referance_case_id':patient_referance_case_id},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading'); 
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

    $(document).on('click','.add_radio_payment',function(){          
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            getradioPayments(record_id);
    });
  
        $(document).on('click','.print_radio_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/radio/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });
    
    function viewPharmacyDetail(id){
         var view_modal=$('#viewModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id,'is_bill': 'yes'},
            dataType:"JSON",
            beforeSend: function(){
                $('#reportdata,#edit_deletebill').html("");
           shModal('viewModal').show();
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#reportdata').html(data.page);
                $('#edit_deletebill').html(data.actions);
                view_modal.removeClass('modal_loading');
            },
        });
    }

     $(document).on('click','.add_pharmacypayment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            getPharmacyPayments(record_id);
    });

     $(document).on('submit','#add_bill_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_bill_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getPharmacyPayments(pharmacy_bill_basic_id);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

      function getPharmacyPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getPharmacyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill':'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){

             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

       $(document).on('click','.print_pharmacyBillReceipt',function(){
      var $this = $(this);
      var record_id=$this.data('recordId')
      $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pharmacy/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

 $(document).on('click','.view_pathology_detail',function(){
         var id=$(this).data('recordId');
         PatientPathologyDetails(id,$(this));
       });
        function PatientPathologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/pathology/getPatientPathologyDetails',
            type: "POST",
            data: {'id': id,'is_bill': 'yes'},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
                modal_view.addClass('modal_loading');                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions); 
             shModal('viewDetailReportModal').show();
              modal_view.removeClass('modal_loading');
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.btnReset();
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.btnReset();
                modal_view.removeClass('modal_loading');          
           }
        });  
        }

         $(document).on('click','.print_pathology_bill',function(){
    var id=$(this).data('recordId');      
        var $this = $(this);   
        $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();               
               },
            success: function (data) {    
           popup(data.page);
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.btnReset();               
      },
      complete: function() {
            $this.btnReset();     
      }
        });
    }); 
          $(document).on('click','.add_pathology_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading');               
            shModal(payment_modal[0]).show();
            getpathologyPayments(record_id);
    });

   function getpathologyPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getPathologyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill':'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');  
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }
	
      $(document).on('submit','#add_pathopartial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_pathopartial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getpathologyPayments(pathology_billing_id);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

      $(document).on('click','.print_patho_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });
    
    $(document).on('click','.print_report',function(){
       var id=$(this).data('recordId');  
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/radio/printPatientReportDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();
               },
            success: function (data) {       
          popup(data.page);
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.btnReset();               
      },
      complete: function() {
            $this.btnReset();     
      }
        });
    });
    
    $(document).on('click','.view_radio_detail',function(){
         var id=$(this).data('recordId');
         PatientRadiologyDetails(id,$(this));
       });
        function PatientRadiologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/radio/getPatientRadiologyDetails',
            type: "POST",
            data: {'id': id,'is_bill':'yes'},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
                modal_view.addClass('modal_loading');                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
             shModal('viewDetailReportModal').show();
              modal_view.removeClass('modal_loading');
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.btnReset();
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.btnReset();
                modal_view.removeClass('modal_loading');          
           }
        });  
        }

         $(document).on('submit','#add_radio_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var radiology_billing_id=$("input[name='radiology_billing_id']",'#add_radio_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,                 
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getradioPayments(radiology_billing_id);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

         $(document).on('click','.add_radio_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            getradioPayments(record_id);
    });

   function getradioPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getRadiologyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill': 'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');      
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }    
    
    function load_ambulance_data(){
        if(get_case_id()!==0){
            ( function ( $ ) {
                'use strict';
                $(document).ready(function () {
                    initDatatable('load_ambulance','admin/bill/getambulance/'+get_case_id(),[],[],100,
                        [
                            { "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3,-4,-5,-6] ,'sClass': 'dt-head-right dt-body-right'},
                            { "sWidth": "150px", "aTargets": [ 0, 1, 2 ], 'sClass': 'sh-col-nowrap' }
                        ]
                    );
                });
            } ( jQuery ) )
        }else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        } 
    }

    $(document).on('submit','#patient_discharge', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        shModal('addrefundPaymentModal').hide();
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            }); 
        });

     $(document).on('click','.print_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id');   
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'bill'},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });  

      $(document).on('click','.view_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id'); 
         var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show();  
         $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'bill'},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
            $('#modal_title').html('<?php echo $this->lang->line('discharge_card');?>');
            $('#allpayments_print').html(res.action);
            $('.modal-body',payment_modal).html(res.page);
             payment_modal.removeClass('modal_loading'); 
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

      $(document).on('click','.view_radiodetail',function(){
         var id=$(this).data('recordId');
         PatientRadiologyDetails(id,$(this));
    });

    function PatientRadiologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({ 
            url: base_url+'admin/radio/getPatientRadiologyDetails',
            type: "POST",
            data: {'id': id,'is_bill': 'yes'},
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
                modal_view.addClass('modal_loading');
               },
            success: function (data) {                      
                 $('#viewDetailReportModal .modal-body').html(data.page);  
                 $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);  
                 shModal('viewDetailReportModal').show();
                  modal_view.removeClass('modal_loading');
            },
            error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.btnReset();
                modal_view.removeClass('modal_loading');
           },
            complete: function() {
            $this.btnReset();
                modal_view.removeClass('modal_loading');          
           }
        });  
    }

       $(document).on('click','.print_radiology_bill',function(){
        var id=$(this).data('recordId');      
        var $this = $(this);   
        $.ajax({
            url: base_url+'admin/radio/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();
               },
            success: function (data) {     
           popup(data.page);

            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.btnReset();               
      },
      complete: function() {
            $this.btnReset();     
      }
        });
    });
</script>
<script>
    $(document).on('click','.showbill',function(){
        let $this=$(this);
        let case_id=$(this).data('caseId');
            $.ajax({
            type: 'POST',
            url: base_url+'admin/bill/patient_bill',
            data: {case_reference_id:case_id},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
             },
            success: function (result) {                
                $("#patient_bill_summary").html(result.patient_name);
                $("#billSummaryData").html(result.page);
                $('#billSummaryModal .modal_action').html(result.modal_action);
                shModal("billSummaryModal").show();
                $this.btnReset();
            },
             error: function(xhr) { // if error occured
                $this.btnReset();
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");    
            },
             complete: function() {
                $this.btnReset();
             }
        });
    }) 

    $(document).on('click','.print_ambulance_receipt',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
        $this.btnLoading();
        $.ajax({ 
            url: '<?php echo base_url(); ?>admin/bill/print_ambulance_Transaction',
            type: "POST",
            data:{'id':record_id},
            dataType: 'json',
            beforeSend: function() {
                    $this.btnLoading();        
            },
            success: function(res) {
            popup(res.page);
            },
            error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                    $this.btnReset();
            },
            complete: function() {
                $this.btnReset();                
            }
        });
    });
    
    $(document).ready(function (e) {
        modal_click_disabled('billSummaryModal', 'addPaymentModal', 'addrefundPaymentModal');
    }); 

    $(document).on('click','.print_pathology_report',function(){
   var id=$(this).data('recordId');

   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printPatientReportDetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.btnLoading();
           },
        success: function (data) {       
      popup(data.page);
        },

         error: function(xhr) { // if error occured
         alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         $this.btnReset();           
  },
  complete: function() {
        $this.btnReset(); 
  }
    });
});

    $(document).ready(function() {
        $("#case_id").autocomplete({
            source:function(request, response) {
                $.ajax({
                    type:"GET",
                    url: base_url+'admin/bill/getcaseid',
                    data: { caseid: $("#case_id").val() },
                    dataType:"json",
                    contentType:"application/json; charset=utf-8",
                    success:function(data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.id,
                                value: item.id
                            };
                        }));
                    },
                    error:function(data) {
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
    
     $(document).on('click','.printcomponentIssueBill',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: base_url+'admin/bloodbank/printcomponentIssueBill',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
           $this.btnLoading();

          },
          success: function(res) {
     popup(res.page);

          },
             error: function(xhr) { // if error occured
          alert("Error Occurred, Please Try Again");
        $this.btnReset();

         },
              complete: function() {
    $this.btnReset();

             }
      });
  });

    $(document).on('click','.add_payment_blood_component',function(){
            var record_id=$(this).data('recordId');
            var $add_btn= $(this);
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading');
            $('.filestyle','#addPaymentModal').dropify();
            bootstrap.Modal.getOrCreateInstance(payment_modal[0], {backdrop:'static', keyboard:false}).show();
            getPayments(record_id);
    });

    function getPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/bloodbank/getBloodbankTransaction',
            type: "POST",
            data: {'id': record_id,'transaction_type':'blood_component'},
            dataType:"JSON",
            beforeSend: function(){
            },
            success: function (data) {

           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');
            },
             error: function () {
             payment_modal.removeClass('modal_loading');
            },  complete: function(){
             payment_modal.removeClass('modal_loading');
            }
        });
    }
     $(document).on('click','.delete_trans', function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_want_to_delete'); ?>")){
        e.preventDefault();
        var record_id=$(this).data('recordId');
        var billing_id=$("input[name='billing_id']",'#add_partial_payment').val();

        var btn = $(this);
        btn.btnLoading();
        $.ajax({
            url: base_url+'admin/transaction/deleteByID',
            type: "POST",
            data: {'id':record_id},
            dataType: 'JSON',
            success: function (data) {
                successMsg(data.message);
                getPayments(billing_id);
                btn.btnReset();
            },
            error: function () {
                btn.btnReset();
            },
            complete: function(){
                btn.btnReset();
            }
        });
        }
    });
    
</script>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>

<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('ambulance_call_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_add')) { ?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm ambulancecall"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_ambulance_call'); ?></a>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('ambulance', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/vehicle/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('ambulance_list'); ?></a>
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('ambulance_call_list'); ?></div>
                            <table id="ambulanceCallTable" class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('ambulance_call_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('generated_by'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                        <th><?php echo $this->lang->line('driver_name'); ?></th>
                                        <th><?php echo $this->lang->line('driver_contact_no'); ?></th>
                                        <th><?php echo $this->lang->line('patient_address'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <?php if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                        ?>
                                                <th><?php echo ucfirst($fields_value->name); ?></th>
                                        <?php }
                                        } ?>
                                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('discount'); ?>(%)</th>
                                        <th class="text-end"><?php echo $this->lang->line('tax'); ?>(%)</th>
                                        <th class="text-end" width="80"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-end" width="80"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="noExport text-end" width="80"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formcall" method="post" accept-charset="utf-8">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_ambulance_call'); ?></h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <select class="form-control patient_list_ajax sh-bill-patient-pick" name='patient_id' id="addpatient_id"></select>
                        <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                        <a data-bs-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-sm btn-light flex-shrink-0 text-nowrap"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                        <?php } ?>
                        <div class="input-group flex-shrink-0 sh-bill-patient-pick">
                            <input type="text" class="form-control" id="case_reference_id" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                            <button class="btn btn-secondary" type="button" id="search_case_reference_id"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn-close close_ambulance_call" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <input type="hidden" id="case_reference_id_add" name="case_reference_id_add" />
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fas fa-ambulance"></i> <?php echo $this->lang->line('ambulance_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('vehicle_model'); ?></label><small class="req"> *</small>
                                        <select name="vehicle_no" id="vehicle_no" class="form-control form-control-sm" onchange="getVehicleDetail(this.value)">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($vehiclelist as $key => $vehicle) {  ?>
                                                <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('driver_name'); ?></label>
                                        <input name="driver" id="driver_search" type="text" class="form-control form-control-sm" readonly />
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input name="date" type="text" class="form-control form-control-sm datetime" />
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req">*</small>
                                        <select class="form-control form-control-sm select2 charge_category" name='charge_category_id'>
                                            <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) {   ?>
                                            <option value="<?php echo $charge_cat_value["id"]; ?>"><?php echo $charge_cat_value["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('charge_name'); ?></label>
                                        <small class="req">*</small>
                                        <select class="form-control form-control-sm select2 charge" name='code' id="code">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('standard_charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                        <small class="req">*</small>
                                        <input class="form-control form-control-sm standard_charge" name='standard_charge' id="standard_charge">
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('note'); ?></span>
                                    </div>
                                    <div class="px-2 py-3">
                                        <textarea name="note" rows="4" id="note" class="form-control form-control-sm"></textarea>
                                        <hr class="my-3">
                                        <div class="mt-2"><?php echo display_custom_fields('ambulance_call'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom">
                                        <span class="me-auto small"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" value="0" name="total" id="total" class="form-control form-control-sm text-end total sh-summary-input" readonly>
                                    </div>
                                    <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom gap-2">
                                        <span class="me-auto small text-nowrap"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="input-group input-group-sm sh-summary-input">
                                            <input type="text" value="0" name="discount_percent" id="discount_percent" class="form-control form-control-sm text-end discount_percent">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <input type="text" value="0" name="discount" id="discount" onkeyup="get_percentage(this.value)" class="form-control form-control-sm text-end discount sh-summary-input">
                                    </div>
                                    <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom gap-2">
                                        <span class="me-auto small text-nowrap"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></span>
                                        <div class="input-group input-group-sm sh-summary-input">
                                            <input type="text" name="tax_percentage" id="tax_percentage" class="form-control form-control-sm text-end tax_percentage" readonly>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <input type="text" value="0" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-summary-input" readonly>
                                    </div>
                                    <div class="sh-summary-netamt d-flex align-items-center px-3 py-2 border-bottom">
                                        <span class="me-auto small"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                                        <input type="text" value="0" name="net_amount" id="net_amount" class="form-control form-control-sm text-end fw-semibold net_amount sh-summary-input" readonly>
                                    </div>
                                    <div class="px-2 py-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('payment_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req">*</small></label>
                                                <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm text-end payment_amount">
                                                <span class="text-danger small"><?php echo form_error('payment_amount'); ?></span>
                                            </div>
                                        </div>
                                        <div class="cheque_div d-none" >
                                            <div class="row g-2 mt-1">
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="formcallbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formedit" method="post" accept-charset="utf-8">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('ambulance_call'); ?></h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <select class="form-control patient_list_ajax sh-bill-patient-pick" id="addpatientid" name='patient_id'>
                            <option value=""><?php echo $this->lang->line('select_patient'); ?></option>
                        </select>
                        <div class="input-group flex-shrink-0 sh-bill-patient-pick">
                            <input type="text" class="form-control" id="case_reference_id_edit" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                            <button class="btn btn-secondary" type="button" id="search_case_reference_id_edit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <input name="patient_name" id="patienteditid" type="hidden" class="form-control" value="<?php echo set_value('patient_name'); ?>" />
                    <input type="hidden" id="ambulance_call_edit_id" name="id" />
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fas fa-ambulance"></i> <?php echo $this->lang->line('ambulance_details'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-2">
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('vehicle_number'); ?></label><small class="req"> *</small>
                                    <select name="vehicle_no" id="vehicleno" class="form-control form-control-sm" onchange="getVehicleDetail(this.value, 'vehicle_model', 'driver_name')">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($vehiclelist as $key => $vehicle) { ?>
                                            <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('vehicle_model'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('driver_name'); ?></label>
                                    <input name="driver_name" id="driver_name" type="text" class="form-control form-control-sm" value="<?php echo set_value('vehicle_model'); ?>" readonly />
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                    <input name="date" id="edit_date" type="text" class="form-control form-control-sm datetime" value="<?php echo set_value('amount'); ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('charge_category'); ?></label>
                                    <small class="req">*</small>
                                    <select class="form-control form-control-sm select2 charge_category" name='charge_category_id' id="charge_category_id_edit">
                                        <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) { ?>
                                            <option value="<?php echo $charge_cat_value["id"]; ?>"><?php echo $charge_cat_value["name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('charge_name'); ?></label>
                                    <small class="req">*</small>
                                    <select class="form-control form-control-sm select2 charge_edit" name='code' id="code_edit">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('standard_charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                    <small class="req">*</small>
                                    <input class="form-control form-control-sm standard_charge" name='standard_charge' id="standard_charge_edit">
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="sh-flex-col">
                            <div class="sh-form-card h-100">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('note'); ?></span>
                                </div>
                                <div class="px-2 py-3">
                                    <textarea name="note" rows="3" id="edit_note" class="form-control form-control-sm"></textarea>
                                    <hr class="my-3">
                                    <div id="customfield"></div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-flex-col">
                            <div class="sh-form-card h-100 overflow-hidden">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
                                </div>
                                <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom">
                                    <span class="me-auto small"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                                    <input type="text" value="0" name="total" id="total_edit" class="form-control form-control-sm text-end total sh-summary-input" readonly>
                                </div>
                                <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom gap-2">
                                    <span class="me-auto small text-nowrap"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></span>
                                    <div class="input-group input-group-sm sh-summary-input">
                                        <input type="text" value="0" name="discount_percent_edit" id="discount_percent_edit" class="form-control form-control-sm text-end discount_percent">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <input type="text" value="0" name="discount_edit" id="discount_edit" onkeyup="update_percentage(this.value)" class="form-control form-control-sm text-end discount sh-summary-input">
                                </div>
                                <div class="d-flex align-items-center sh-summary-row px-3 py-2 border-bottom gap-2">
                                    <span class="me-auto small text-nowrap"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></span>
                                    <div class="input-group input-group-sm sh-summary-input">
                                        <input type="text" name="tax_percentage" id="tax_percentage_edit" class="form-control form-control-sm text-end tax_percentage" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <input type="text" value="0" name="tax" id="tax_edit" class="form-control form-control-sm text-end tax sh-summary-input" readonly>
                                </div>
                                <div class="sh-summary-netamt d-flex align-items-center px-3 py-2 border-bottom">
                                    <span class="me-auto small"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                                    <input type="text" value="0" name="net_amount" id="net_amount_edit" class="form-control form-control-sm text-end fw-semibold net_amount sh-summary-input" readonly>
                                </div>
                                <div class="px-2 py-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('payment_mode'); ?></label>
                                            <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('payment_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req">*</small></label>
                                            <input type="text" name="payment_amount" id="payment_amount_edit" class="form-control form-control-sm text-end payment_amount">
                                            <span class="text-danger small"><?php echo form_error('payment_amount'); ?></span>
                                        </div>
                                    </div>
                                    <div class="cheque_div d-none" >
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-6">
                                                <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                                                <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                                                <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control form-control-sm" name="document">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer sticky-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formeditbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModalBill" tabindex="-1" aria-labelledby="viewModalBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalBillLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id='edit_deletebill'></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
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
                <div class="modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    $(function() {
        $('#easySelectable').easySelectable();
        $('.select2').select2()
    })
</script>
<script type="text/javascript">
    (function($) {
        //selectable html elements
        $.fn.easySelectable = function(options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function(el) {

                },
                onSelected: function(el) {

                },
                onUnSelected: function(el) {

                }
            }, options);
            el.on('dragstart', function(event) {
                event.preventDefault();
            });
            el.off('mouseover');
            el.addClass('easySelectable');
            if (options.state) {
                el.find(options.item).addClass('es-selectable');
                el.on('mousedown', options.item, function(e) {
                    $(this).trigger('start_select');
                    var offset = $(this).offset();
                    var hasClass = $(this).hasClass('es-selected');
                    var prev_el = false;
                    el.on('mouseover', options.item, function(e) {
                        if (prev_el == $(this).index())
                            return true;
                        prev_el = $(this).index();
                        var hasClass2 = $(this).hasClass('es-selected');
                        if (!hasClass2) {
                            $(this).addClass('es-selected').trigger('selected');
                            el.trigger('selected');
                            options.onSelecting($(this));
                            options.onSelected($(this));
                        } else {
                            $(this).removeClass('es-selected').trigger('unselected');
                            el.trigger('unselected');
                            options.onSelecting($(this))
                            options.onUnSelected($(this));
                        }
                    });
                    if (!hasClass) {
                        $(this).addClass('es-selected').trigger('selected');
                        el.trigger('selected');
                        options.onSelecting($(this));
                        options.onSelected($(this));
                    } else {
                        $(this).removeClass('es-selected').trigger('unselected');
                        el.trigger('unselected');
                        options.onSelecting($(this));
                        options.onUnSelected($(this));
                    }
                    var relativeX = (e.pageX - offset.left);
                    var relativeY = (e.pageY - offset.top);
                });
                $(document).on('mouseup', function() {
                    el.off('mouseover');
                });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);
</script>
<script type="text/javascript">
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'GET',
            data: {
                id: id,
                print: 'no'
            },
            success: function(result) {
                popup(result);
            }
        });
    }     

    $("form#formcall button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function(e) {
           modal_click_disabled('addPaymentModal');
        $("#formcall").on('submit', (function(e) {
            let submit_btn_clicked = $("button[type=submit][clicked=true]", $(this));
            let submit_btn_clicked_name = submit_btn_clicked.attr('name');
            console.log(submit_btn_clicked_name);
            e.preventDefault();
            $.ajax({
                url: base_url + 'admin/vehicle/addCallAmbulance',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    submit_btn_clicked.btnLoading();
                },
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        table.ajax.reload();
                        shModal('myModal').hide();
                        if (submit_btn_clicked_name == "save_print") {
                            printData(data.id);
                        }
                    }
                    submit_btn_clicked.btnReset();
                },
                error: function(xhr) {
                    alert("Error occured.please try again");
                    submit_btn_clicked.btnReset();
                },
                complete: function() {
                    submit_btn_clicked.btnReset();
                }
            });
        }));
    });

    $("form#formedit button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function(e) {
        $("#formedit").on('submit', (function(e) {
            e.preventDefault();
            let submit_btn_clicked = $("button[type=submit][clicked=true]", $(this));
            let submit_btn_clicked_name = submit_btn_clicked.attr('name');

            $.ajax({
                url: base_url + 'admin/vehicle/updatecallambulance',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    submit_btn_clicked.btnLoading();
                },
                success: function(data) {

                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        table.ajax.reload();
                        shModal('editModal').hide();
                        if (submit_btn_clicked_name == "save_print") {
                            printData(data.id);
                        }
                    }
                    submit_btn_clicked.btnReset();
                },
                error: function(xhr) {
                    alert("Error occured.please try again");
                    submit_btn_clicked.btnReset();
                },
                complete: function() {
                    submit_btn_clicked.btnReset();
                }
            });
        }));
    });

    function viewDetailBill(id) {
        var $modal = $('#viewModalBill');
        $modal.addClass('modal_loading');
        shModal($modal[0]).show();
        $.ajax({
            url: '<?php echo base_url() ?>admin/vehicle/getAmbulanceBillView',
            type: "POST",
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#reportdata').html(data.page);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_view')) { ?><a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_edit')) { ?><a href='javascript:void(0)' class='btn btn-sm btn-light' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_delete')) { ?><a onclick='delete_bill(" + id + ")' href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
            },
            complete: function() {
                $modal.removeClass('modal_loading');
            }
        });
    }

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/editCall',
            type: "GET",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
				$("#case_reference_id_edit").attr("disabled", "disabled");
				$("#search_case_reference_id_edit").attr("disabled", "disabled");
                $('#vehicleno').val(data.vehicle_id);
                $('#vehicleno').trigger('change');
                $('#charge_category_id_edit').val(data.charge_category_id);
                $('#charge_category_id_edit').trigger('change');
                getchargecode(data.charge_category_id, data.charge_id);
                $("#ambulance_call_edit_id").val(data.id);
                $("#edit_note").val(data.note);
                $('#customfield').html(data.custom_fields_value);
                $("#driver_name").val(data.driver);
                $("#patienteditid").val(data.patient_id);
                $("#contact_no").val(data.contact_no);
                $("#edit_date").val(data.date);
                $("#address").val(data.address);				
                $("#tax_percentage_edit").val(data.tax_percentage);				
                $("#standard_charge_edit").val(data.standard_charge);
                $("#discount_percent_edit").val(data.discount_percentage);
                $("#discount_edit").val(data.discount);
                $("#net_amount_edit").val(data.net_amount);
                $("#total_edit").val(data.amount);
                shModal('viewModalBill').hide();
                shModal('editModal').show();
                $('#code_edit').val(data.charge_id);
                $("#case_reference_id_edit").val(data.case_reference_id);
                $("#payment_amount_edit").val(data.payment_amount);
                $(".payment_mode").val(data.payment_mode);
                $("#cheque_no", $('#formedit')).val(data.cheque_no);
                if (data.payment_mode == "Cheque") {
                    // #cheque_date auto-initialized as TD 6 picker via .date class. Set value via SHPicker:
                    SHPicker.setDate('#formedit #cheque_date', new Date(data.cheque_date));
                    $('.cheque_div').removeClass('d-none');
                }
                $('#code_edit').trigger('change');
                var tax_amount = ((data.standard_charge-data.discount) * data.tax_percentage) / 100;
                $("#tax_edit").val(tax_amount.toFixed(2));
                get_PatienteditDetails(data.patient_id);
            },
        });
    }

    function getVehicleDetail(id, vh = 'vehicle_model_search', dr = 'driver_search') {
        $("#" + dr).val("");
        $("#" + vh).val("");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/getVehicleDetail',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $("#" + dr).val(data.driver_name);
                $("#" + vh).val(data.vehicle_model);
            },
        });
    }

    function get_PatienteditDetails(id) {
        if(id!=""){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    var option = new Option(res.patient_name + " (" + res.id + ")", res.id, true, true);
                    $("#editModal .patient_list_ajax").append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    $("#editModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                }
            }
        });
    }
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deleteCallAmbulance/' + id,
                success: function(res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function() {
                    alert("Fail")
                }
            });
        }
    }

    $(".ambulancecall").click(function() {
        
    });

    $('#myModal').on('shown.bs.modal', function () {
        var $sel = $('#addpatient_id');
        if ($sel.hasClass('select2-hidden-accessible')) { $sel.select2('destroy'); }
        $sel.select2({
            ajax: {
                url: "<?= base_url(); ?>admin/patient/getPatientListAjax",
                type: "post", dataType: 'json', delay: 250,
                data: function (params) { return { searchTerm: params.term }; },
                processResults: function (response) { return { results: response }; },
                cache: true
            },
            dropdownParent: $('#myModal'),
            dropdownAutoWidth: true,
            width: '220px'
        });
    });

    $('#editModal').on('shown.bs.modal', function () {
        var $sel = $('#addpatientid');
        if ($sel.hasClass('select2-hidden-accessible')) { $sel.select2('destroy'); }
        $sel.select2({
            ajax: {
                url: "<?= base_url(); ?>admin/patient/getPatientListAjax",
                type: "post", dataType: 'json', delay: 250,
                data: function (params) { return { searchTerm: params.term }; },
                processResults: function (response) { return { results: response }; },
                cache: true
            },
            dropdownParent: $('#editModal'),
            dropdownAutoWidth: true,
            width: '220px'
        });
    });

    $('#myModal').on('hidden.bs.modal', function(e) {
        let modal_ = e.delegateTarget;
        $('#case_reference_id').val('');
        $("form#formcall", modal_).find('input:text, input:password, input:file, select, textarea').val('');
        $("form#formcall", modal_).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        $(".charge_category", modal_).select2("val", "");
        $(".charge ", modal_).html('').select2({
            data: [{
                id: '',
                text: ''
            }]
        });
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(document).on('select2:select', '.charge_category', function() {
        var charge_category = $(this).val();
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        getchargecode(charge_category, "");
    });

    function getchargecode(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if (charge_category != "") {
            $.ajax({
                url: base_url + 'admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(i, obj) {
                        var sel = "";
                        if (charge_id == obj.id) {
                            sel = "selected";
                        }

                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";

                    });
                    $
                    $('.charge').html(div_data);
                    $(".charge").select2("val", charge_id);
                    $('.charge_edit').html(div_data);
                    $(".charge_edit").select2("val", charge_id);
                }
            });
        }
    }
</script>
<script>
    $(document).on('select2:select', '.charge', function(e) {
        var charge = $(this).val();
        var object_model = $(e.target).closest('div.modal');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge
            },
            dataType: 'json',
            success: function(ambulance_data) {
                if (ambulance_data) {
					
					var discount_percent = $('#discount_percent').val();					
                    object_model.find('#standard_charge').val((ambulance_data.result.standard_charge));
                    object_model.find('#amount').val(parseFloat((ambulance_data.result.standard_charge) * ambulance_data.result.percentage / 100) + (parseFloat(ambulance_data.result.standard_charge)).toFixed(2));					
                    var discount_amt = (ambulance_data.result.standard_charge * discount_percent)/100;
                    object_model.find('#discount').val(discount_amt.toFixed(2));					
                    var tax_percentage = ambulance_data.result.percentage;
                    var total_amount = ambulance_data.result.standard_charge;                  
                    object_model.find('#tax_percentage').val(parseFloat(tax_percentage).toFixed(2));
                    object_model.find('#total').val(parseFloat(total_amount).toFixed(2));                  
                    var tax_amount = parseFloat(((total_amount-discount_amt)) * tax_percentage / 100);                    
                    object_model.find('#tax').val(parseFloat(tax_amount).toFixed(2));
                    var final_amount = parseFloat(total_amount-discount_amt) + parseFloat(tax_amount);
                    object_model.find('#net_amount,#payment_amount').val(final_amount.toFixed(2));
                } else {

                }
            }
        });
    });

    $(document).on('input paste keyup','.tax_percent,.discount_percent,#standard_charge', function(e){ 
        calculate_amount($(e.target).closest('div.modal'));
    });
	
    let calculate_amount=(object_model)=>{        
        let  standard_charge=object_model.find('.standard_charge').val();
        let  tax_percentage = object_model.find('.tax_percentage').val();
        let  discount_percent = object_model.find('.discount_percent').val();
        let  discount=isNaN((standard_charge * discount_percent / 100 ))? 0 : (standard_charge * discount_percent / 100 );
		let  tax_amount = ((standard_charge-discount) * tax_percentage / 100);
        object_model.find('.discount').val(discount.toFixed(2));        
        object_model.find('.total').val(parseFloat(standard_charge).toFixed(2));
        object_model.find('.tax').val(tax_amount.toFixed(2));
        var final_amount = (parseFloat(standard_charge)- parseFloat(discount)) + parseFloat(tax_amount);                    
        object_model.find('.net_amount,.payment_amount').val(final_amount.toFixed(2));        
    }

    //calculate discount amount to discount persantage
    function get_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#total').val();
        var tax=$('#tax_percentage').val();
        var discount_percent=0;
        var net_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));
        // ===============
        var taxamt = (((parseInt(total)-parseInt(discount_amount))*parseInt(tax)) )/100;     
        $('#tax').val(taxamt.toFixed(2));
        // ===============
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(taxamt));
        $('#net_amount,#payment_amount').val(net_amount.toFixed(2));
    }

    function update_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#total_edit').val();
        var tax=$('#tax_percentage_edit').val();
        var discount_percent=0;
        var net_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percent_edit').val(discount_percent.toFixed(2));
        // ===============
        var taxamt = (((parseInt(total)-parseInt(discount_amount))*parseInt(tax)) )/100;     
        $('#tax_edit').val(taxamt.toFixed(2));
        // ===============
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(taxamt));
        $('#net_amount_edit,#payment_amount_edit').val(net_amount.toFixed(2));
    }
    //calculate discount amount to discount persantage

</script>
<script>
    $(document).on('select2:select', '.charge_edit', function() {
        var charge = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#amount_edit').val(parseFloat((res.result.standard_charge) * res.result.percentage / 100) + (parseFloat(res.standard_charge)));
                    var tax_percentage = res.result.percentage;
                    var total_amount = res.result.standard_charge;					
					var discount_percent = $('#discount_percent_edit').val();
					var discount_amt = (res.result.standard_charge * discount_percent)/100;
                    $('#discount_edit').val(discount_amt);					
                    $('#tax_percentage_edit').val(tax_percentage);
                    $('#total_edit').val(total_amount);
                    var tax_amount = parseFloat((total_amount - discount_amt) * parseFloat(tax_percentage) / 100);
                    $('#standard_charge_edit').val((res.result.standard_charge));
                    $('#tax_edit').val(tax_amount.toFixed(2));
					
					var net_amount = parseFloat(total_amount) + parseFloat(tax_amount) - parseFloat(discount_amt)
                    $('#net_amount_edit,#payment_amount_edit').val(net_amount.toFixed(2));
                } else {

                }
            }
        });
    });
</script>
<script>
        
    $(document).on('click', '.add_payment', function() {
        var record_id = $(this).data('recordId');
        var patient_id = $(this).data('patientId');
        var case_id = $(this).attr('data-case-id');
        var balance_amount = $(this).data('balanceAmount');
        var $add_btn = $(this);
        var payment_modal = $('#addPaymentModal');        
        $('.filestyle', '#addPaymentModal').dropify();
        shModal(payment_modal[0]).show();
        getPayments(record_id, patient_id, balance_amount, case_id);
    });

    function getPayments(record_id, patient_id = null, balance_amount = null, case_id = null) {
        var payment_modal = $('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/vehicle/getAmbulanceCallTransaction',
            type: "POST",
            data: {
                'id': record_id,
                'patient_id': patient_id,
                'balance_amount': balance_amount,
                'case_id': case_id
            },
            dataType: "JSON",
            beforeSend: function() {
                payment_modal.addClass('modal_loading');
            },
            success: function(data) {
                $('.modal-body', payment_modal).html(data.page);
                payment_modal.removeClass('modal_loading');
            },
            error: function() {
                payment_modal.removeClass('modal_loading');
            },
            complete: function() {
                payment_modal.removeClass('modal_loading');
            }
        });
    }

    $('.close_ambulance_call').click(function() {
        $('.cheque_div').addClass('d-none');
        $("#addpatient_id").select2("val", "");
    })

    $(document).on('submit', '#add_partial_payment', function(e) {
        e.preventDefault();
        var clicked_btn = $("button[type=submit]");
        let billing_id = $("input[name='billing_id']", '#add_partial_payment').val();
        let case_id = $("input[name='case_id']", '#add_partial_payment').val();
        let patient_id = $("input[name='patient_id']", '#add_partial_payment').val();		
        let net_amount = $("input[name='net_amount']", '#add_partial_payment').val();
        let amount = $("input[name='payment_amount']", '#add_partial_payment').val();	
		let replace_amount =  parseInt(net_amount) - parseInt(amount); 	 
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
            success: function(data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
					initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [],100);
                    getPayments(billing_id,patient_id,replace_amount,case_id);					 
                }
                btn.btnReset();
            },
            error: function() {

            },
            complete: function() {
                btn.btnReset();
            }
        });
    });    
	
	$(document).on('input', '#standard_charge_edit', function() {
        var standard_charge = $("#standard_charge_edit").val();		
        var discount_percent_edit = $("#discount_percent_edit").val();		
		var discount_amount = standard_charge * discount_percent_edit / 100 ;		
        $("#total_edit").val(standard_charge);
        var tax_percentage = $("#tax_percentage_edit").val();		
        var tax_amount = ((standard_charge - discount_amount) * tax_percentage) / 100
        $("#tax_edit").val(tax_amount.toFixed(2));		
        var net_amount = parseInt(tax_amount) + parseInt(standard_charge) - parseInt(discount_amount);		
        $("#net_amount_edit").val(net_amount);		
        $("#payment_amount_edit").val(net_amount);		
        $("#discount_edit").val(discount_amount);	
    });

    $(document).on('click', '.print_receipt', function() {
        var $this = $(this);
        var record_id = $this.data('recordId')
        $this.btnLoading();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/printTransaction',
            type: "POST",
            data: {
                'id': record_id
            },
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


function reset_details(){
     $('#case_reference_id').val('');
        $("form#formcall","#myModal").find('input:text, input:password, input:file, select, textarea').val('');
        $("form#formcall", "#myModal").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        $(".charge_category","#myModal").select2("val", "");
        $(".charge ","#myModal").html('').select2({
            data: [{
                id: '',
                text: ''
            }]
        });
        $('#addpatient_id').select2("val", "");

}
    $(document).on('click', '#search_case_reference_id', function() {
        var case_reference_id = $("input[name=case_reference_id]").val();
        if(case_reference_id!=""){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/' + case_reference_id,
            type: "POST",
            data: {
                case_reference_id: case_reference_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#myModal .patient_list_ajax").append(option).trigger('change');
                    $("#myModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                    $("#case_reference_id_add").val(case_reference_id);
                } else {
                     reset_details();
                    $("#case_reference_id_add").val("");
                    errorMsg("<?php echo $this->lang->line('patient_not_found'); ?>");
                }
            }
        });
    }else{
        reset_details();
        $("#case_reference_id_add").val("");
        errorMsg("<?php echo $this->lang->line('patient_not_found'); ?>");
    }
    });

    $(document).on('click', '#search_case_reference_id_edit', function() {
        var case_reference_id = $("#case_reference_id_edit").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/' + case_reference_id,
            type: "POST",
            data: {
                case_reference_id: case_reference_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#editModal .patient_list_ajax").append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    $("#editModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                } else {
                    errorMsg("<?php echo $this->lang->line('patient_not_found'); ?>");
                }
            }
        });
    });
</script>
<script>
    $(document).on('change', '.payment_mode', function() {
        var mode = $(this).val();
        if (mode == "Cheque") {
            $('.filestyle', '#addPaymentModal').dropify();
            $('.cheque_div').removeClass('d-none');
        } else {
            $('.cheque_div').addClass('d-none');
        }
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
    var table;
    (function($) {
        'use strict';
        $(document).ready(function() {

            table = initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [], 100, [
                { "sWidth": "80px",  "bSortable": false, "aTargets": [-1], 'sClass': 'dt-body-right' },
                { "sWidth": "80px",  "aTargets": [-2, -3, -4, -5, -6], 'sClass': 'dt-body-right' },
                { "sWidth": "60px",  "aTargets": [0, 1], 'sClass': 'dt-body-center' },
                { "sWidth": "120px", "aTargets": [2, 8], 'sClass': 'dt-body-left' },
                { "sWidth": "100px", "aTargets": [3, 6, 7], 'sClass': 'dt-body-left' },
                { "sWidth": "90px",  "aTargets": [4, 5, 9], 'sClass': 'dt-body-left' }
            ]);
            table.on('draw.dt', function () {
                ['nth-last-child(1)','nth-last-child(2)','nth-last-child(3)','nth-last-child(4)','nth-last-child(5)','nth-last-child(6)'].forEach(function(sel) {
                    $('#ambulanceCallTable th:' + sel + ', #ambulanceCallTable td:' + sel).each(function () {
                        this.style.setProperty('width',     '80px', 'important');
                        this.style.setProperty('min-width', '80px', 'important');
                        this.style.setProperty('max-width', '80px', 'important');
                    });
                });
            });

        });
    }(jQuery))
</script>
<script type="text/javascript">
    $(document).on('click','.delete_trans',function(e){
let record_id=$(this).data('recordId');
let billing_id=$(this).data('billingId');
let patient_id=$(this).data('patientId');
let case_id=$(this).data('caseId');
let balance=$(this).data('balance');
let replace_amount=$(this).data('amount');
 
if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePayment/' + record_id,
                beforeSend: function() {               

            },
            success: function(res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);                    
					initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [],100);					
					var replace_amount1 =  parseInt(balance) + parseInt(replace_amount);		
					getPayments(billing_id,patient_id,replace_amount1,case_id);	 
                  
                },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
            },
            complete: function() {                

            }

            })
        }
    });
	
    function deletePayment(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePayment/' + id,
                success: function(res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>
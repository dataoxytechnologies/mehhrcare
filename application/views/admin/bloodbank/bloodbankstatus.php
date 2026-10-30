<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$load_blood_id = "";
if (!empty($bloodgroup)) {
    $load_blood_id = array_keys($bloodgroup)[0];
}
?>
<div class="bb-card">
    <div class="bb-card-hdr">
        <h4 class="bb-page-title">
            <?php echo $this->lang->line('blood_bank_status'); ?>
        </h4>
        <div class="bb-hdr-actions">
            <?php if ($this->rbac->hasPrivilege('blood_donor', 'can_view')) { ?>
                <a href="<?php echo base_url(); ?>admin/bloodbank/search" class="btn-bb-action"><i class="fa fa-users fa-fw"></i> <?php echo $this->lang->line('donor_details'); ?></a>
            <?php } if ($this->rbac->hasPrivilege('blood_issue', 'can_view')) { ?>
                <a href="<?php echo base_url() ?>admin/bloodbank/issue" class="btn-bb-action"><i class="fa fa-list-alt fa-fw"></i> <?php echo $this->lang->line('blood_issue_details'); ?></a>
            <?php } if ($this->rbac->hasPrivilege('issue_component', 'can_view')) { ?>
                <a href="<?php echo base_url(); ?>admin/bloodbank/component_issue" class="btn-bb-action"><i class="fa fa-flask fa-fw"></i> <?php echo $this->lang->line('component_issue'); ?></a>
            <?php } ?>
        </div>
    </div>
    <hr class="m-0">
    <div class="bb-card-body">
        <div class="d-flex">

            <!-- Blood Group Pills -->
            <div class="bb-group-col">
                <div class="bb-group-label"><?php echo $this->lang->line('blood_group'); ?></div>
                <div class="bb-pill-list">
                    <?php $i = 1; foreach ($bloodgroup as $group_key => $group_value) { ?>
                    <div class="bb-pill<?= $i == 1 ? ' active' : ''; ?>"
                         id="<?= $group_key; ?>"
                         onclick="bbPillClick(this)"><?= $group_value; ?></div>
                    <?php $i++; } ?>
                </div>
            </div>

            <!-- Blood + Components content (loaded via AJAX) -->
            <div class="bb-content">
                <div id="bloodGroupDiv"></div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_blood_bank_status'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bloodgroupstatus" action="<?php echo site_url('admin/bloodbankstatus/status') ?>" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) { ?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php } ?>
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?></label>
                            <input name="blood_group" readonly type="text" id="blood_group" class="form-control form-control-sm" value="<?php if (isset($result)) { echo html_escape($result["blood_group"]); } ?>">
                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label small"><?php echo $this->lang->line('status_in_bags'); ?> <small class="req">*</small></label>
                            <input id="status" name="status" type="text" class="form-control form-control-sm" value="<?php if (isset($result)) { echo html_escape($result["status"]); } ?>">
                            <span class="text-danger"><?php echo form_error('status'); ?></span>
                            <input id="id" name="id" type="hidden" value="<?php if (isset($result)) { echo (int) $result["id"]; } ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="bloodgroupstatusbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="addBloodDetailModal" tabindex="-1" aria-labelledby="addBloodDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBloodDetailModalLabel"><?php echo $this->lang->line('blood_donor_details'); ?></h5>
                <button type="button" class="btn-close close_button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="donorblood" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                <div class="modal-body pb-3">
                    <input type="hidden" name="blood_bank_product_id" id="blood_group_id">
                    <input type="hidden" name="qty" id="qty" value="1">
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('blood_donor_details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line("blood_donor"); ?> <small class="req">*</small></label>
                                        <select name="blood_donor_id" id="blood_donor_id" class="form-control form-control-sm select2">
                                            <option value=""><?php echo $this->lang->line("select"); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('blood_donor_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('donate_date'); ?> <small class="req">*</small></label>
                                        <input name="donate_date" type="text" class="form-control form-control-sm datetime">
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></label>
                                        <input name="bag_no" type="text" class="form-control form-control-sm">
                                        <span class="text-danger"><?php echo form_error('bag_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('volume'); ?></label>
                                        <input id="volume" name="volume" type="text" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('unit_type'); ?></label>
                                        <select name="unit" id="unit" class="form-control form-control-sm unit_type">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($unit_type as $unit_type_key => $unit_type_value) { ?>
                                            <option value="<?php echo $unit_type_value->id; ?>"><?php echo $unit_type_value->unit; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('unit_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('lot'); ?></label>
                                        <input name="lot" type="text" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                                        <select name="charge_category" id="charge_category" class="form-control form-control-sm select2 charge_category">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('charge_name'); ?> <small class="req">*</small></label>
                                        <select name="charge_id" id="code" class="form-control form-control-sm addcharge select2">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                                        <input type="text" name="standard_charge" id="addstandard_charge" class="form-control form-control-sm" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label class="form-label small"><?php echo $this->lang->line('institution'); ?></label>
                                        <input name="institution" type="text" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="sh-flex-col">
                            <div class="sh-form-card h-100">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('note'); ?></span>
                                </div>
                                <div class="p-3">
                                    <textarea name="note" rows="3" id="note" class="form-control form-control-sm"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="sh-flex-col">
                            <div class="sh-form-card h-100 overflow-hidden">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                                    <input type="text" value="0" name="total" id="total" class="form-control sh-summary-input text-end total" readonly>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="text" name="discount_percent" id="discount_percent" value="0" class="form-control sh-summary-input-pct text-end discount_percent">
                                        <span class="text-muted small">%</span>
                                        <input type="text" value="0" name="discount" id="discount" class="form-control sh-summary-input text-end discount">
                                    </div>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="text" name="tax_percentage" id="tax_percentage" class="form-control sh-summary-input-pct text-end tax_percentage" readonly>
                                        <span class="text-muted small">%</span>
                                        <input type="text" value="0" name="tax" id="tax" class="form-control sh-summary-input sh-summary-input-tax text-end tax" readonly>
                                    </div>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('net_amount'); ?></span>
                                    <input type="text" value="0" name="net_amount" id="net_amount" class="form-control sh-summary-input text-end net_amount" readonly>
                                </div>
                                <div class="px-2 py-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <label class="form-label small"><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                                                <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm payment_amount text-end" readonly>
                                                <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cheque_div mt-2 d-none" >
                                        <div class="d-flex gap-3">
                                            <div class="flex-fill">
                                                <div class="mb-2">
                                                    <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="mb-2">
                                                    <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                                </div>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="mb-2">
                                                    <label class="form-label small"><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
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
                    <button type="button" onclick="calculateAmt(true)" class="btn btn-info"><i class="fa fa-calculator"></i> <?php echo $this->lang->line('calculate'); ?></button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="donorbloodbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_components'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="componentsadd" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('blood_group'); ?> &amp; <?php echo $this->lang->line('bag'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?> <small class="req">*</small></label>
                                    <select class="form-control form-control-sm select2 blood_group" id="blood_bank_product_id" name="blood_bank_product_id">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($bloodgroup as $key => $value) { ?>
                                        <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small"><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></label>
                                    <select class="form-control form-control-sm select2 bag_no" name="blood_donor_cycle_id">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('components_name'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('components_name'); ?> <small class="req">*</small></th>
                                            <th><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></th>
                                            <th><?php echo $this->lang->line('volume'); ?></th>
                                            <th><?php echo $this->lang->line('unit'); ?></th>
                                            <th><?php echo $this->lang->line('lot'); ?> <small class="req">*</small></th>
                                            <th><?php echo $this->lang->line('institution'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($components as $key => $value) { ?>
                                        <tr>
                                            <td>
                                                <div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="select[]" value="<?php echo $key; ?>" /> <?php echo $value; ?></label></div>
                                            </td>
                                            <td><input type="text" class="form-control form-control-sm" name="bag_no_<?php echo $key; ?>" value=""></td>
                                            <td><input type="text" class="form-control form-control-sm" name="volume_<?php echo $key; ?>" value=""></td>
                                            <td>
                                                <select class="form-control form-control-sm" name="unit_<?php echo $key; ?>">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($unit_type as $typekey => $typevalue) { ?>
                                                    <option value="<?php echo $typevalue->id; ?>"><?php echo $typevalue->unit; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control form-control-sm" name="lot_<?php echo $key; ?>" value=""></td>
                                            <td><input type="text" class="form-control form-control-sm" name="institution_<?php echo $key; ?>" value=""></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="bloodIssueModal" tabindex="-1" aria-labelledby="bloodIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="modal-header gap-2">
                    <h5 class="modal-title" id="bloodIssueModalLabel"><?php echo $this->lang->line('issue_blood'); ?></h5>
                    <input type="hidden" id="id_case_reference_id_exist" name="id_case_reference_id_exist" class="is_caseid_exit">
                    <select class="form-control form-control-sm patient_list_ajax sh-bill-patient-pick" name="patient_id" id="addpatient_id" onchange="get_PatientDetails(this.value)" data-placeholder="<?php echo $this->lang->line('search_patient'); ?>"></select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                    <a data-bs-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                    <?php } ?>
                    <div class="input-group input-group-sm sh-ig-180">
                        <input type="text" class="form-control form-control-sm" id="case_reference_id" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                        <button class="btn btn-secondary btn-sm search_case_reference_id" type="button" id="search_case_reference_id" data-modal-name="bloodIssueModal">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div class="form-check ms-1">
                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_all(this)">
                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="pup-scroll-area">
                        <div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="organisation_id" name="organisation_id" />
                    <input type="hidden" id="insurance_id" name="insurance_id" />
                    <input type="hidden" id="insurance_validity" name="insurance_validity" />
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="button" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn_issue"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn_issue" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="componentIssueModal" tabindex="-1" aria-labelledby="componentIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formaddComponent" accept-charset="utf-8" method="post">
                <div class="modal-header gap-2">
                    <h5 class="modal-title" id="componentIssueModalLabel"><?php echo $this->lang->line('component_issue'); ?></h5>
                    <input type="hidden" name="is_case_reference_idd" id="is_case_reference_idd" class="is_caseid_exit">
                    <select class="form-control form-control-sm patient_list_ajax sh-bill-patient-pick" name="patient_id" id="addpatient_id_component" onchange="get_component_PatientDetails(this.value)" data-placeholder="<?php echo $this->lang->line('search_patient'); ?>"></select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                    <a data-bs-toggle="modal" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                    <?php } ?>
                    <div class="input-group input-group-sm sh-ig-180">
                        <input type="text" class="form-control form-control-sm" id="case_reference_id_component" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                        <button class="btn btn-secondary btn-sm search_case_reference_id" type="button" data-modal-name="componentIssueModal">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div class="form-check ms-1">
                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa_component" name="is_tpa" onclick="reset_all(this)">
                        <label class="form-check-label" for="is_tpa_component"><?php echo $this->lang->line('apply_tpa'); ?></label>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="pup-scroll-area">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="component_organisation_id" name="organisation_id" />
                    <input type="hidden" id="component_insurance_id" name="insurance_id" />
                    <input type="hidden" id="component_insurance_validity" name="insurance_validity" />
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="button" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn_issue"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn_issue_component" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="availableBloodModal" tabindex="-1" aria-labelledby="availableBloodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="availableBloodModalLabel"><?php echo $this->lang->line('available_bloods'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pup-scroll-area">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/edit/' + id,
            success: function(result) {
                $('#id').val(result.id);
                $('#status').val(result.status);
                $('#blood_group').val(result.blood_group);
            }
        });
    }

    $(document).on('change', '.payment_mode', function() {
        var mode = $(this).val();
        if (mode == "Cheque") {
            $('.filestyle', '#addBloodDetailModal').dropify();
            $('.cheque_div').removeClass('d-none');
        } else {
            $('.cheque_div').addClass('d-none');
        }
    });
	
    $(document).ready(function(e) {
         
        $('.select2').select2();
        $('#editformadd').on('submit', (function(e) {
            $("#editformaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").btnLoading();
                },
                error: function() {}
            });
        }));

        $('#bloodgroupstatus').on('submit', (function(e) {
            $("#bloodgroupstatusbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        window.location.reload(true);
                    }
                    $("#bloodgroupstatusbtn").btnReset();
                },
                error: function() {}
            });
        }));
    });

    $(document).on('click', '.getbatchlist', function() {
        var createModal = $('#availableBloodModal');
        var $this = $(this);
        var bloodgroup = $(this).data('bloodGroup');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getAvailabelBloodByGroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                this.btnLoading();
                createModal.addClass('modal_loading');
            },
            success: function(res) {
                $('.modal-body', createModal).html(res.page);
                shModal(createModal[0]).show();

            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                this.btnReset();
                createModal.removeClass('modal_loading');
            },
            complete: function() {
                this.btnReset();
                createModal.removeClass('modal_loading');
            }
        });
    });

    function bloodDetailsModal(bloodgroupid) {
        $("#donorblood").trigger("reset");
        modal_click_disabled('addBloodDetailModal');
        shModal('addBloodDetailModal').show();
        div_data = "";
        $("#blood_group_id").val(bloodgroupid);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: "blood_bank"
            },
            dataType: 'json',
            success: function(res) {

                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
                $('#charge_category').val(null).trigger('change');
            }
        });

        get_donor_list(bloodgroupid);
    }

    function get_donor_list(bloodgroupid) {
        div_val = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/get_donor_list/' + bloodgroupid,
            type: "POST",
            dataType: 'json',
            beforeSend: function() {
                $('#blood_donor_id').html("").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
            },
            success: function(res) {

                $.each(res, function(i, obj) {
                    var sel = "";
                    var option = new Option(obj.donor_name + " (" + obj.blood_group + ")", obj.id, true, true);
                    $('#blood_donor_id').append(option).trigger('change');
                });

                $('#blood_donor_id').select2('val', ''); // select the new term
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }

    function componentDetailsModal(bloodgroupid) {
        $("#blood_bank_product_id").val(bloodgroupid).trigger('change');
        modal_click_disabled('myModal');
        shModal('myModal').show();
        var bloodgroup = $("#blood_bank_product_id").val();
        getBloodGroupBagNos(bloodgroup, "");
    }

    $(document).ready(function(e) {
        $("#donorblood").on('submit', (function(e) {
            var button_loading = $("#donorbloodbtn");

            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/donorCycle',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    button_loading.btnLoading();
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
                        window.location.reload(true);
                    }
                    $("#donorbloodbtn").btnReset();
                },
                error: function() {
                    button_loading.btnReset();
                },
                complete: function() {
                    button_loading.btnReset();
                }
            });
        }));
    });

    $(document).on('select2:select', '.charge_category', function() {
        var charge_category = $(this).val();
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        getchargecode(charge_category, "");
    });

    function getchargecode(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $('#tax_percentage').val(0);
        $('#code').val("").trigger("change");
        $("#addstandard_charge").val(0);
        $("#total").val(0);
        $("#discount").val(0);
        $("#tax").val(0);
        $("#net_amount").val(0);
        $("#payment_amount").val(0);
        if (charge_category != "") {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {                   
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.addcharge').html(div_data);
                    $(".addcharge").select2("val", charge_id);
                }
            });
        }
    }

    $(document).on('select2:select', '.addcharge', function() {        
        var charge = $(this).val();
        var orgid = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge,
                organisation_id: orgid
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    var quantity = $('#qty').val();
                    quantity = (quantity == "") ? 0 : quantity;
                    var total_amout = parseFloat(res.result.standard_charge) * quantity;

                    $('#addstandard_charge').val(res.result.standard_charge);
                    var discount_percent = $('#discount_percent').val();
                    $('#tax_percentage').val(res.result.percentage);
                    var discount_amount = parseFloat(total_amout * discount_percent / 100);
                    var tax = $('#tax_percentage').val();
                    var tax_amount = parseFloat((total_amout - discount_amount) * tax / 100)
                    var net_amount = (total_amout - discount_amount) + tax_amount;
                    $('#total').val(total_amout.toFixed(2));
                    $('#discount').val(discount_amount.toFixed(2));
                    $('#tax').val(tax_amount.toFixed(2));

                    $('#net_amount').val(net_amount.toFixed(2));
                    $('#payment_amount').val(net_amount.toFixed(2));
                }
            }
        });
    });

    $(document).on('change keyup input paste', '#qty', function() {
        var quantity = $(this).val();
        var standard_charge = $('#addstandard_charge').val();
        var tax_percent = $('#tax_percentage').val();
        var total_charge = (standard_charge == "") ? 0 : standard_charge;
        var apply_charge = isNaN(parseFloat(total_charge) * parseFloat(quantity)) ? 0 : parseFloat(total_charge) * parseFloat(quantity);
        $('#total').val(apply_charge);
        var discount_percent = $('#discount_percent').val();
        var discount_amount = isNaN((apply_charge * discount_percent) / 100) ? 0 : (apply_charge * discount_percent) / 100;
        var final_amount = apply_charge - discount_amount;
        var total_tax = ((final_amount * tax_percent) / 100);
        var total_payment_amount = final_amount + ((final_amount * tax_percent) / 100);
        $('#discount').val(discount_amount.toFixed(2));
        $('#tax').val(total_tax.toFixed(2));
        $('.payment_amount').val(total_payment_amount.toFixed(2));
        $('#net_amount').val(total_payment_amount.toFixed(2));
    });

    $(document).on('change keyup input paste', '#discount', function() {
        calculateAmt(false);
    });

    $(document).on('change keyup input paste', '#addstandard_charge', function() {
        var standard_charge = $("#addstandard_charge").val();
        var qty = $("#qty").val();
        $("#total").val(standard_charge * qty);
        calculateAmt(false);
    });

    $(document).on('change keyup input paste', '#discount_percent', function() {
        calculateAmt(true);
    });

    function calculateAmt(is_percentage) {
       
        var tot_amt = parseFloat($('#total').val());
        if (is_percentage) {
            var dis_per = $('#discount_percent').val();
            var dis_amt = isNaN(parseFloat(tot_amt * dis_per / 100)) ? 0 : parseFloat(tot_amt * dis_per / 100);
            $('#discount').val(dis_amt.toFixed(2));
        } else {
            var dis_amt = parseFloat($('#discount').val());
            var dis_per = isNaN(((dis_amt * 100) / tot_amt)) ? 0 : ((dis_amt * 100) / tot_amt);
            $('#discount_percent').val(dis_per.toFixed(2));
        }
        var tax_per = parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt - dis_amt) * tax_per / 100);
        $('#tax').val(tax_amt.toFixed(2));
        var net_amt = isNaN(tax_amt + (tot_amt - dis_amt)) ? "" : (tax_amt + (tot_amt - dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
    }
    <?php
    if ($load_blood_id != '') { ?>
        getBloodListTable('<?php echo $load_blood_id; ?>');
    <?php
    }
    ?>

    function bbPillClick(el) {
        document.querySelectorAll('.bb-pill').forEach(function(p){ p.classList.remove('active'); });
        el.classList.add('active');
        getBloodListTable(el.id);
    }

    function getBloodListTable(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodListTable/' + id,
            type: "POST",
            data: {
                id: id
            },
            success: function(res) {                
                $("#bloodGroupDiv").html(res);
            }
        });
    }

    $(document).on('change keyup input paste', '#addstandard_charge_issue', function() {
        var standard_charge = $("#addstandard_charge_issue").val();
        var qty = $("#qty_issue").val();
        $("#total_issue").val(standard_charge * qty);
        calculateAmtIssue(false);
    });

    $(document).on('change keyup input paste', '#discount_percent_issue', function() {
        calculateAmtIssue(true);
    });
    $(document).on('change keyup input paste', '#discount_issue', function() {
        calculateAmtIssue(false);
    });

    function calculateAmtIssue(is_percentage) {
        let dis_amt=0;
        var tot_amt = parseFloat($('#total_issue').val());
        if (is_percentage) {
            var dis_per = $('#discount_percent_issue').val();
             dis_amt =  isNaN((tot_amt * dis_per / 100)) ? 0 : parseFloat(tot_amt * dis_per / 100);;
            $('#discount_issue').val(dis_amt.toFixed(2));
        } else {
             dis_amt = parseFloat($('#discount_issue').val());
            var dis_per = isNaN(((dis_amt * 100) / tot_amt)) ? 0 : ((dis_amt * 100) / tot_amt);
            $('#discount_percent_issue').val(dis_per.toFixed(2));
        }

        var tax_per = parseFloat($('#tax_percentage_issue').val());
        var tax_amt = parseFloat((tot_amt - dis_amt) * tax_per / 100);
        $('#tax_issue').val(tax_amt.toFixed(2));
        var net_amt = isNaN(tax_amt + (tot_amt - dis_amt)) ? "" : (tax_amt + (tot_amt - dis_amt)).toFixed(2);
        $('#net_amount_issue').val(net_amt);
        $('#payment_amount_issue').val(net_amt);
    }

    function getBloodGroupBagNos(bloodgroup, bagno) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?= base_url(); ?>admin/bloodbank/getbatchbybloodgroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let volume = obj.volume != null ? obj.volume : "" ;
                    let unit = obj.charge_unit != null ? obj.charge_unit : "" ;
                    if(volume !="" || unit !=""  ){
                        val_unit= " (" + volume + " " + unit + ")";
                    }
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + val_unit + "</option>";
                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagno);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }

    $(document).ready(function(e) {
        $("#componentsadd").on('submit', (function(e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addcomponents',
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function() {}
            });
        }));
    });
</script> 

<script>
    function componentIssueModal(blood_group_id, bag_no,bloodid) {
        $("#formaddComponent").trigger('reset');
        issueModal = $("#componentIssueModal");
        shModal(issueModal[0]).show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/issuecomponentfront',
            type: "POST",
            data: {blood_group_id: blood_group_id},
            dataType: 'json',
            success: function(res) {
                $('.pup-scroll-area', issueModal).html(res.page);
                initBloodPatientSelect('#addpatient_id_component', '#componentIssueModal');
                getcharge_category_issue("blood_bank");
                $('.filestyle', '#componentIssueModal').dropify();
                // Init all select2 except blood group select (needs value set first)
                $('.modal-body', issueModal).find('.select2:not(#blood_group_issue_select)').select2();
                // Set value then init blood group select2 separately so it picks up pre-selected option
                issueModal.find("#blood_group_issue").val(blood_group_id);
                issueModal.find("#blood_group_issue_select").val(blood_group_id).select2();
                getComponentBagNosIssue(blood_group_id, bag_no,bloodid)
            },
        });
    }

    // Initialize the ajax patient-search select2 inside a blood/component issue modal.
    // Without this the patient dropdown (#addpatient_id) never gets its ajax config and won't open.
    function initBloodPatientSelect(selectSelector, modalSelector) {
        var $sel = $(selectSelector);
        if (!$sel.length) return;
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.select2({
            ajax: {
                url: "<?php echo base_url(); ?>admin/patient/getPatientListAjax",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            },
            placeholder: "<?php echo $this->lang->line('search_patient'); ?>",
            dropdownParent: $(modalSelector),
            width: '300px'
        });
    }

    function bloodIssueModal(blood_group_id, bag_no) {
        $("#formadd").trigger('reset');
        issueModal = $("#bloodIssueModal");
        shModal(issueModal[0]).show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/issuebloodFront',
            type: "POST",
            data: {blood_group_id: blood_group_id},
            dataType: 'json',
            success: function(res) {
                $('.pup-scroll-area', issueModal).html(res.page);
                $('.datetime').trigger('change');
                initBloodPatientSelect('#addpatient_id', '#bloodIssueModal');
                getcharge_category_issue("blood_bank");
                $('.filestyle', '#bloodIssueModal').dropify();
                // Init all select2 except blood group select (needs value set first)
                $('.modal-body', issueModal).find('.select2:not(#blood_group_issue_select)').select2();
                // Set value then init blood group select2 separately so it picks up pre-selected option
                issueModal.find("#blood_group_issue").val(blood_group_id);
                issueModal.find("#blood_group_issue_select").val(blood_group_id).select2();
                getBloodGroupBagNosIssue(blood_group_id, bag_no)
            },
        });
    }

    // Bind consultant_doctor event
    $(document).on('select2:select', '#consultant_doctor', function(e) {
        var reference_name = $("#consultant_doctor option:selected").text();
        $('#reference').val(reference_name.trim());
    });

    function get_Docname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {
                doctor: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#reference').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function getComponentBagNosIssue(component_id, bagno,bloodid) {
      
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getComponentBagNosIssue',
            type: "POST",
            data: {
                'blood_group_id': bloodid,
                'component_id':component_id
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no_issue').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let val_unit = "";
                    let volume = obj.volume != null ? obj.volume : "";
                    let unit = obj.unit_name != null ? obj.unit_name : "" ;
                    if(volume !="" || unit !=""  ){
                        val_unit= " (" + volume + " " + unit + ")";
                    }
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no  + val_unit+" </option>";
                });
                $('.bag_no_issue').html(div_data);
                $('.bag_no_issue').select2("val", bagno);
            },
        });
    }

    function getBloodGroupBagNosIssue(bloodgroup, bagno) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getbatchbybloodgroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no_issue').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let val_unit = "";
                    let volume = obj.volume != null ? obj.volume : "";
                    let unit = obj.charge_unit != null ? obj.charge_unit : "";
                    if (volume != "" || unit != "") {
                        val_unit = " (" + volume + " " + unit + ")";
                    }
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + val_unit + " </option>";
                });
                $('.bag_no_issue').html(div_data);
                $('.bag_no_issue').select2("val", bagno);
            },
        });
    }

    $(document).on('select2:select', '.blood_group_issue', function() {
        var bloodgroup = $(this).val();
        getBloodGroupBagNosIssue(bloodgroup, "");
    });
    $(document).on('select2:select', '.component_issue', function() {
        var bloodgroup = $(this).val();
        getComponentBagNosIssue(bloodgroup, "");
    });

    function getcharge_category_issue(module) {
        var div_data = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: module
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category_issue').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category_issue').append(div_data);
                $('.charge_category_issue').select2("val", charge_category);
            }
        });
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

    $(document).on('select2:select', '.charge_category_issue', function() {
        var charge_category = $(this).val();
        $('.addcharge_issue').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        getchargecodeissue(charge_category, "");
    });

    function getchargecodeissue(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $('#tax_percentage_issue').val(0);
        $('#addstandard_charge_issue').val(0);
        $('#total_issue').val(0);
        $('#discount_percent_issue').val(0);
        $('#discount_issue').val(0);
        $('#tax_issue').val(0);
        $('#net_amount_issue').val(0);
        $('#payment_amount_issue').val(0);
        if (charge_category != "") {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {                  
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.addcharge_issue').html(div_data);
                    $(".addcharge_issue").select2("val", charge_id);
                }
            });
        }
    }

    $(document).on('select2:select', '.addcharge_issue', function(e) {
        var charge      = $(this).val();       
        let modal_div   = $(e.target).closest('div.modal')
        let is_tpa      = modal_div.find("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var patient_id  = modal_div.find('.patient_list_ajax').val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodBankChargeById',
            type: "POST",           
            data: {'charge_id': charge ,'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            success: function(res) {
                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{

                    if(res.display_tpa_charge){
                        var charge_amt=res.result.org_charge;
                    }else{
                        var charge_amt=res.result.standard_charge;
                    }          

                    var quantity = $('#qty_issue').val();
                    quantity = (quantity == "") ? 0 : quantity;
                    var total_amout = parseFloat(charge_amt) * quantity;
                    $('#total_issue').val(total_amout);
                    $('#addstandard_charge_issue').val(res.result.standard_charge);
                    var discount_percent = $('#discount_percent_issue').val();
                    $('#tax_percentage_issue').val(res.result.percentage);
                    var discount_amount = parseFloat(total_amout * discount_percent / 100);
                    var tax = $('#tax_percentage_issue').val();
                    var tax_amount = parseFloat((total_amout - discount_amount) * tax / 100)
                    var net_amount_issue = (total_amout - discount_amount) + tax_amount;
                    var payment_amount_issue = (total_amout - discount_amount) + tax_amount;
                    $('#discount_issue').val(discount_amount.toFixed(2));
                    $('#tax_issue').val(tax_amount.toFixed(2));
                    $('#net_amount_issue').val(net_amount_issue.toFixed(2));
                    $('#payment_amount_issue').val(payment_amount_issue.toFixed(2));
                    if(res.status == 2){
                            errorMsg(res.msg);
                        }
                }
            }
        });
    });

    $(document).on('change', '.bag_no_issue', function() {
        var available_unit = $(this).find('option:selected').attr("available_unit");
        $('#qty_issue').val(available_unit);
    });

    $(document).ready(function(e) {
        $(".printsavebtn_issue").on('click', (function(e) {
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });

            $("#printsavebtn_issue").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssue',
                type: "POST",
                data: postData,
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
                        printData(data.id);
                    }
                    $("#printsavebtn_issue").btnReset();
                },
                error: function() {

                }
            });
        }));
    });

    function printData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getBillDetails/' + id,
            type: 'POST',
            data: {
                id: id,
                print: 'yes'
            },
            success: function(result) {
                popup(result);
            }
        });
    }    

    $(document).ready(function(e) {
        $("#formadd").on('submit', (function(e) {
            var str = $("#formadd").serializeArray();
            var postData = new FormData();
            var case_reference_id = $("input[name=case_reference_id]").val();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssue',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#formaddbtn_issue").btnLoading();
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn_issue").btnReset();
                },
                error: function() {
                    $("#formaddbtn_issue").btnReset();
                },
                complete: function() {
                    $("#formaddbtn_issue").btnReset();
                }
            });
        }));
    });

    $(document).ready(function(e) {
        $("#formaddComponent").on('submit', (function(e) {
            var str = $("#formaddComponent").serializeArray();
            var postData = new FormData();
            var case_reference_id = $("input[name=case_reference_id]").val();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssueComponent',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#formaddbtn_issue_component").btnLoading();
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn_issue_component").btnReset();
                },
                error: function() {
                    $("#formaddbtn_issue_component").btnReset();
                },
                complete: function() {
                    $("#formaddbtn_issue_component").btnReset();
                }
            });
        }));
    });

    $(document).on('click', '.search_case_reference_id', function() {        
        var case_reference_id = $(this).closest("div.modal").find("input[name='case_reference_id']").val();
        var modalname=$(this).data('modal-name');	
        if(case_reference_id != ''){	 
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
                    $("#"+modalname).find(".patient_list_ajax").append(option).trigger('change');
                    $("#"+modalname).find(".patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                    $("#"+modalname).find(".is_caseid_exit").val(case_reference_id);             
                } else {
                    $("#"+modalname).find(".is_caseid_exit").val("");    
                    $("#formadd").trigger('reset'); 
                    $("#formaddComponent").trigger('reset');
                    $('.patient_list_ajax').select2("val", "");
                    $('#consultant_doctor').select2("val", "");
                    $('.bag_no_issue').select2('val', '');
                    errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
                }
            }
        });
    }else{
        $("#"+modalname).find(".is_caseid_exit").val("");    
        $("#formadd,#formaddComponent").trigger('reset'); 
        $('.patient_list_ajax').select2("val", "");
        $('#consultant_doctor').select2("val", "");
        $('.bag_no_issue').select2('val', '');
        errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
    }
    });



    function reset_all(context){
        var $ctx = $(context).closest('.modal');
        $ctx.find('.payment_amount').val(0);
        $ctx.find('#addstandard_charge_issue').val('');
        $ctx.find('.addcharge_issue').val('').trigger('change');
        $ctx.find('.charge_category_issue').val('').trigger('change');
        $ctx.find('.sh-summary-row input').val(0);
    }

</script>

<script>
    function getcharge_category_module(module) {
        var div_data = "";
        $.ajax({
            url: base_url + 'admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: module
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
    }

    $('.close_button').click(function() {
        $(".select2").select2().select2('val', '');
        $(".addcharge").select2().select2('val', '');
        $('.cheque_div').addClass('d-none');
    })
</script>

<?php $this->load->view('admin/patient/patientaddmodal') ?>

<script>
	function get_PatientDetails(id) {
        if(id!=""){
        reset_all(document.getElementById('bloodIssueModal'));
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {                
                if (res) {
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);                   
                } 
            }
        });
    }
    }
	
    function get_component_PatientDetails(id) {
        reset_all(document.getElementById('componentIssueModal'));
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {                
                if (res) {
                    $("#component_organisation_id").val(res.organisation_id);
                    $("#component_insurance_id").val(res.insurance_id);
                    $("#component_insurance_validity").val(res.insurance_validity);                   
                } 
            }
        });
    }
</script>


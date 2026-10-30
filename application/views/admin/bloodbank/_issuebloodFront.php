<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('blood_issue_details'); ?></span>
    </div>
    <div class="p-3">
        <div class="row g-2">
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('issue_date'); ?> <small class="req">*</small></label>
                    <input type="text" name="date_of_issue" id="dates_of_issue" class="form-control form-control-sm datetime">
                    <span class="text-danger"><?php echo form_error('date_of_issue'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('hospital_doctor'); ?></label>
                    <select name="consultant_doctor" id="consultant_doctor" class="form-control form-control-sm select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>>
                            <?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?>
                        </option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('reference_name'); ?> <small class="req">*</small></label>
                    <input type="text" id="reference" name="reference" class="form-control form-control-sm">
                    <span class="text-danger"><?php echo form_error('reference'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('technician'); ?></label>
                    <input type="text" name="technician" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?></label>
                    <select class="form-control form-control-sm select2 component_issue" id="blood_group_issue_select" disabled readonly>
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($stockbloodgroup as $blood_grp_value) { ?>
                        <option value="<?php echo $blood_grp_value['id']; ?>" <?php echo (isset($selected_blood_group) && $selected_blood_group == $blood_grp_value['id']) ? 'selected' : ''; ?>><?php echo $blood_grp_value['name']; ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="blood_group_issue" name="blood_group">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 bag_no_issue" name="bag_no">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                    <select name="charge_category" id="charge_category_issue" class="form-control form-control-sm select2 charge_category_issue">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('charge_name'); ?></label>
                    <select name="charge_id" id="code" class="form-control form-control-sm addcharge_issue select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></label>
                    <input type="text" name="standard_charge" id="addstandard_charge_issue" class="form-control form-control-sm" value="<?php echo set_value('standard_charge'); ?>">
                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                </div>
            </div>
            <input type="hidden" name="qty" id="qty_issue" value="1">
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
                <div class="mt-2"><?php echo display_custom_fields('blood_issue'); ?></div>
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
                <input type="text" value="0" name="total" id="total_issue" class="form-control sh-summary-input text-end total" readonly>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex align-items-center gap-1">
                    <input type="text" name="discount_percent" id="discount_percent_issue" value="0" class="form-control sh-summary-input-pct text-end discount_percent">
                    <span class="text-muted small">%</span>
                    <input type="text" value="0" name="discount" id="discount_issue" class="form-control sh-summary-input text-end discount">
                </div>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex align-items-center gap-1">
                    <input type="text" name="tax_percentage" id="tax_percentage_issue" class="form-control sh-summary-input-pct text-end tax_percentage" readonly>
                    <span class="text-muted small">%</span>
                    <input type="text" value="0" name="tax" id="tax_issue" class="form-control sh-summary-input sh-summary-input-tax text-end tax" readonly>
                </div>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('net_amount'); ?></span>
                <input type="text" value="0" name="net_amount" id="net_amount_issue" class="form-control sh-summary-input text-end net_amount" readonly>
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
                            <input type="text" name="payment_amount" id="payment_amount_issue" class="form-control form-control-sm payment_amount text-end">
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

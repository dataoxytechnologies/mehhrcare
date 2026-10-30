<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('issue_blood'); ?></span>
    </div>
    <div class="p-2">
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
                    <select name='consultant_doctor' id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control form-control-sm select2" <?php if ($disable_option == true) { echo "disabled"; } ?>>
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue['employee_id'] . ")"; ?></option>
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
                    <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 blood_group" name="blood_group">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($stockbloodgroup as $blood_grp_value) { ?>
                            <option value="<?php echo $blood_grp_value['id']; ?>"><?php echo $blood_grp_value['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 bag_no" name="bag_no">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                    <select name="charge_category" id="charge_category" class="form-control form-control-sm select2 charge_category">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('charge_name'); ?> <small class="req">*</small></label>
                    <select name="charge_id" id="code" class="form-control form-control-sm addcharge select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>)</label>
                    <input type="text" name="standard_charge" id="addstandard_charge" class="form-control form-control-sm" value="<?php echo set_value('standard_charge'); ?>">
                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                </div>
            </div>
            <input type="hidden" name="qty" id="qty" value="1">
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-3">
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('note'); ?></span>
            </div>
            <div class="px-2 py-3">
                <textarea name="note" rows="4" id="note" class="form-control form-control-sm"></textarea>
                <div class="mt-2"><?php echo display_custom_fields('blood_issue'); ?></div>
            </div>
        </div>
    </div>
    <div class="sh-flex-col">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</span>
                <input type="text" value="0" name="total" id="total" class="form-control sh-summary-input text-end total" readonly>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</span>
                <div class="d-flex align-items-center gap-1">
                    <div class="input-group input-group-sm sh-ig-130">
                        <input type="text" value="0" name="discount_percent" id="discount_percent" class="form-control text-end discount_percent">
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" value="0" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-ig-130">
                </div>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
                <div class="d-flex align-items-center gap-1">
                    <div class="input-group input-group-sm sh-ig-130">
                        <input type="text" name="tax_percentage" id="tax_percentage" class="form-control text-end tax_percentage" readonly>
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" value="0" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-ig-130" readonly>
                </div>
            </div>
            <div class="sh-summary-row border-bottom sh-summary-netamt">
                <span><?php echo $this->lang->line('net_amount'); ?></span>
                <input type="text" value="0" name="net_amount" id="net_amount" class="form-control sh-summary-input text-end net_amount" readonly>
            </div>
            <div class="px-2 py-3">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                        <select class="form-control form-control-sm payment_mode" name="payment_mode">
                            <?php foreach ($payment_mode as $key => $value) { ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                    </div>
                    <div class="col-6">
                        <label class="form-label small"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm text-end payment_amount">
                        <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                    </div>
                </div>
                <div class="cheque_div mt-2 d-none" >
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date" readonly>
                        </div>
                        <div class="col-12">
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

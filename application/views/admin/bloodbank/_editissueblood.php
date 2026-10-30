<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$total_discount  = calculatePercent($result['amount'], $result['discount_percentage']);
$taxable_amount  = $result['amount'] - $total_discount;
$total_tax       = calculatePercent($taxable_amount, $result['tax_percentage']);
?>
<input type="hidden" name="id" value="<?php echo $result['id']; ?>">
<input type="hidden" name="post_patient_id" class="post_patient_id" value="<?php echo $result['patient_id']; ?>">
<input type="hidden" name="post_patient_name" class="post_patient_name" value="<?php echo $result['patient_name'] . ' (' . $result['patient_id'] . ')'; ?>">
<input type="hidden" name="post_charge_type_id" class="post_charge_type_id" value="<?php echo $result['charge_type_id']; ?>">
<input type="hidden" name="post_charge_category_id" class="post_charge_category_id" value="<?php echo $result['charge_category_id']; ?>">
<input type="hidden" name="post_charge_id" class="post_charge_id" value="<?php echo $result['charge_id']; ?>">
<input type="hidden" name="post_blood_donor_cycle_id" class="post_blood_donor_cycle_id" value="<?php echo $result['blood_donor_cycle_id']; ?>">
<input type="hidden" name="post_blood_group" class="post_blood_group" value="<?php echo $result['blood_group_id']; ?>">
<input type="hidden" name="post_quantity" class="post_quantity" value="<?php echo $result['quantity']; ?>">
<input type="hidden" name="post_bag_no" class="post_bag_no" value="<?php echo $this->customlib->bag_string($result['bag_no'], $result['volume'], $result['unit_name']); ?>">
<input type="hidden" name="qty" id="qty" value="<?php echo $result['quantity']; ?>">

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('issue_blood'); ?></span>
    </div>
    <div class="p-2">
        <div class="row g-2">
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('issue_date'); ?> <small class="req">*</small></label>
                    <input type="text" name="date_of_issue" id="dates_of_issue" class="form-control form-control-sm datetime" value="<?php echo set_value('date_of_issue', $this->customlib->YYYYMMDDHisTodateFormat($result['date_of_issue'])); ?>">
                    <span class="text-danger"><?php echo form_error('date_of_issue'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('hospital_doctor'); ?></label>
                    <select name='consultant_doctor' id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control form-control-sm select2" <?php if ($disable_option == true) { echo "disabled"; } ?>>
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ($result['hospital_doctor'] == $dvalue["id"]) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('reference_name'); ?> <small class="req">*</small></label>
                    <input type="text" id="reference" name="reference" class="form-control form-control-sm" value="<?php echo $result['reference']; ?>">
                    <span class="text-danger"><?php echo form_error('reference'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('technician'); ?></label>
                    <input type="text" name="technician" class="form-control form-control-sm" value="<?php echo $result['technician']; ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-2">
                    <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?></label>
                    <select class="form-control form-control-sm select2 blood_group" name="blood_group">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($stockbloodgroup as $blood_grp_value) { ?>
                            <option value="<?php echo $blood_grp_value['id']; ?>" <?php echo ($blood_grp_value['id'] == $result['blood_group_id']) ? 'selected' : ''; ?>><?php echo $blood_grp_value['name']; ?></option>
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
                    <input type="text" name="standard_charge" id="addstandard_charge" class="form-control form-control-sm" readonly value="<?php echo set_value('standard_charge', $result['standard_charge']); ?>">
                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
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
            <div class="px-2 py-3">
                <textarea name="note" rows="4" id="note" class="form-control form-control-sm"><?php echo set_value('note', $result['remark']); ?></textarea>
                <div class="mt-2"><?php echo display_custom_fields('blood_issue', $result['id']); ?></div>
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
                <input type="text" name="total" id="total" class="form-control sh-summary-input text-end total" readonly value="<?php echo set_value('total', $result['amount']); ?>">
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</span>
                <div class="d-flex align-items-center gap-1">
                    <div class="input-group input-group-sm sh-ig-130">
                        <input type="text" name="discount_percent" id="discount_percent" class="form-control text-end discount_percent" value="<?php echo set_value('discount_percentage', $result['discount_percentage']); ?>">
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-ig-130" value="<?php echo $total_discount; ?>">
                </div>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
                <div class="d-flex align-items-center gap-1">
                    <div class="input-group input-group-sm sh-ig-130">
                        <input type="text" name="tax_percentage" id="tax_percentage" class="form-control text-end tax_percentage" readonly value="<?php echo set_value('tax_percentage', $result['tax_percentage']); ?>">
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-ig-130" readonly value="<?php echo $total_tax; ?>">
                </div>
            </div>
            <div class="sh-summary-row border-bottom sh-summary-netamt">
                <span><?php echo $this->lang->line('net_amount'); ?></span>
                <input type="text" name="net_amount" id="net_amount" class="form-control sh-summary-input text-end net_amount" readonly value="<?php echo set_value('net_amount', $result['net_amount']); ?>">
            </div>
        </div>
    </div>
</div>

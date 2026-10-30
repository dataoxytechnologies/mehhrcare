<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<input type="hidden" name="pathology_billing_id" value="0">
<input type="hidden" name="transaction_id" value="0">

<!-- Card 1: Test Items -->
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_name'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">

            <?php if (!empty($prescription_test->tests)) { ?>

            <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID">
                <thead>
                    <tr>
                        <th width="30%"><?php echo $this->lang->line('test_name'); ?> <small class="req">*</small></th>
                        <th width="13%"><?php echo $this->lang->line('report_days'); ?> <small class="req">*</small></th>
                        <th width="20%"><?php echo $this->lang->line('report_date'); ?> <small class="req">*</small></th>
                        <th width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end" width="17%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1;
                    foreach ($prescription_test->tests as $test_key => $test_value) { ?>
                    <tr id="row<?php echo $counter; ?>">
                        <td>
                            <input type="hidden" name="total_rows[]" value="<?php echo $counter; ?>">
                            <select class="form-control form-control-sm test_name select2 w-100"  name="test_name_<?php echo $counter; ?>">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($pathology_tests as $pathology_test_key => $pathology_test_value) { ?>
                                    <option value="<?php echo $pathology_test_value["id"]; ?>" <?php echo ($pathology_test_value["id"] == $test_value->pathology_id) ? 'selected' : ''; ?>>
                                        <?php echo $pathology_test_value["test_name"] . " (" . $pathology_test_value["short_name"] . ")"; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('test_name_id[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" name="reportday_<?php echo $counter; ?>" id="reportday_<?php echo $counter; ?>" value="<?php echo $test_value->report_days; ?>" class="form-control form-control-sm text-end days" readonly>
                            <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" name="reportdate_<?php echo $counter; ?>" id="reportdate_<?php echo $counter; ?>" value="<?php echo ($date == "") ? "" : $this->customlib->strtotimeToDateFormat($date + ($test_value->report_days * 86400)); ?>" class="form-control form-control-sm text-end report_date">
                            <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm text-end taxpercent" name="taxpercent_<?php echo $counter; ?>" id="taxpercent_<?php echo $counter; ?>" value="<?php echo $test_value->tax; ?>" readonly autocomplete="off">
                                <span class="input-group-text">%</span>
                            </div>
                            <input type="hidden" name="taxamount_<?php echo $counter; ?>" id="taxamount_<?php echo $counter; ?>" value="<?php
                                if ($is_tpa == 1) {
                                    if ($test_value->organisation_id == 0 || strtotime($insurance_validity) < strtotime(date('Y-m-d'))) {
                                        echo $test_value->standard_charge * $test_value->tax / 100;
                                    } else {
                                        echo ($test_value->org_charge == null || $test_value->org_charge == 0) ? $test_value->standard_charge * $test_value->tax / 100 : $test_value->org_charge * $test_value->tax / 100;
                                    }
                                } else {
                                    echo $test_value->standard_charge * $test_value->tax / 100;
                                }
                            ?>" class="taxamount">
                        </td>
                        <td class="text-end">
                            <input type="text" name="amount_<?php echo $counter; ?>" id="amount_<?php echo $counter; ?>" value="<?php
                                if ($is_tpa == 1 && $insurance_validity != '') {
                                    if (strtotime($insurance_validity) < strtotime(date('Y-m-d'))) {
                                        echo $test_value->standard_charge;
                                        $error_msg = "";
                                    } else {
                                        if ($test_value->organisation_id == 0) {
                                            echo $test_value->standard_charge;
                                            $error_msg = "";
                                        } else {
                                            if ($test_value->org_charge == null || $test_value->org_charge == 0) {
                                                $error_msg = $this->lang->line('no_charge_has_configured_for_selected_test');
                                            } else {
                                                echo $test_value->org_charge;
                                                $error_msg = "";
                                            }
                                        }
                                    }
                                } else {
                                    echo $test_value->standard_charge;
                                    $error_msg = "";
                                }
                            ?>" class="form-control form-control-sm text-end amount" readonly>
                            <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                            <span class="text-danger"><?php echo $error_msg; ?></span>
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-outline-danger delete_rows" data-row-id="<?php echo $counter; ?>"><i class="fa fa-remove"></i></button>
                        </td>
                    </tr>
                    <?php $counter++; } ?>
                </tbody>
            </table>

            <?php } else { ?>

            <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID">
                <thead>
                    <tr>
                        <th width="30%"><?php echo $this->lang->line('test_name'); ?> <small class="req">*</small></th>
                        <th width="13%"><?php echo $this->lang->line('report_days'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('report_date'); ?> <small class="req">*</small></th>
                        <th width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end" width="17%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="addrows">
                    <tr id="row1">
                        <td>
                            <input type="hidden" name="total_rows[]" value="1">
                            <select class="form-control form-control-sm test_name select2 w-100"  name="test_name_1" onchange="gettestpathodetails(this.value, 1)">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($pathology_tests as $test_key => $test_value) { ?>
                                    <option value="<?php echo $test_value["id"]; ?>"><?php echo $test_value["test_name"] . " (" . $test_value["short_name"] . ")"; ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('test_name_id[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" name="reportday_1" id="reportday_1" class="form-control form-control-sm text-end days" readonly>
                            <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" name="reportdate_1" id="reportdate_1" class="form-control form-control-sm text-end report_date">
                            <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control form-control-sm text-end taxpercent" name="taxpercent_1" id="taxpercent_1" readonly autocomplete="off">
                                <span class="input-group-text">%</span>
                            </div>
                            <input type="hidden" name="taxamount_1" id="taxamount_1" class="taxamount">
                        </td>
                        <td class="text-end">
                            <input type="text" name="amount_1" id="amount_1" class="form-control form-control-sm text-end amount" readonly>
                            <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-sm btn-outline-danger delete_rows" data-row-id="1"><i class="fa fa-remove"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php } ?>

        </div>
        <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
    </div>
</div>

<!-- Cards 2 & 3 side by side -->
<div class="d-flex flex-wrap gap-3">

    <!-- Card 2: Doctor & Notes -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('doctor_name'); ?></span>
            </div>
            <div class="px-2 py-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('referral_doctor'); ?></label>
                            <select name="consultant_doctor" id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control consultant_doctor select2 w-100">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>>
                                        <?php echo $dvalue["name"] . " " . $dvalue["surname"] . " ( " . $dvalue["employee_id"] . " )"; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('doctor_name'); ?></label>
                            <input name="doctor_name" id="doctname" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <?php echo display_custom_fields('pathology'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Billing Summary + Payment -->
    <div class="sh-flex-col" id="showamtdiv">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="0" name="total" id="total" class="form-control form-control-sm text-end total sh-bill-summary-input" readonly />
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm sh-discount-pct-group">
                        <input type="text" value="0" name="discount_percent" id="discount_percent" class="form-control text-end discount_percent" />
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" value="0" onkeyup="get_percentage(this.value)" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-bill-summary-input" />
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="0" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-bill-summary-input" readonly />
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="0" name="net_amount" id="net_amount" class="form-control form-control-sm text-end text-secondary fw-semibold net_amount sh-bill-summary-input" readonly />
            </div>
            <div class="px-2 py-3">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('payment_mode'); ?></label>
                        <select class="form-control form-select-sm payment_mode" name="payment_mode">
                            <?php foreach ($payment_mode as $key => $value) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                        <input type="text" name="amount" id="payamount" class="form-control form-control-sm text-end">
                        <span class="text-danger"></span>
                    </div>
                </div>
                <div class="cheque_div mt-1 d-none" >
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!--./flex-->

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<!-- Hidden tracking fields -->
<input name="sr_customer_name" id="sr_patient_name" type="hidden"/>
<input name="sr_customer_type" id="sr_customer_type" type="hidden" value="Patient"/>
<input name="sr_pharmacy_bill_basic_id" id="sr_pharmacy_bill_basic_id" type="hidden" value=""/>

<!-- Card 1: Medicines Table -->
<div class="sh-form-card mt-3 mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered table-hover srProducts mb-0" id="srTableID">
                <thead>
                    <tr class="white-space-nowrap">
                        <th><?php echo $this->lang->line('medicine_name'); ?> <small class="text-danger">*</small></th>
                        <th><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th><?php echo $this->lang->line('batch_no'); ?> <small class="text-danger">*</small></th>
                        <th><?php echo $this->lang->line('expiry_date'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                        <th class="text-center"><?php echo $this->lang->line('return_qty'); ?> <small class="text-danger">*</small></th>
                        <th class="text-end"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                        <th><?php echo $this->lang->line('tax'); ?></th>
                        <th><?php echo $this->lang->line('discount'); ?> (%)</th>
                        <th class="text-end text-nowrap"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        <th class="text-center" width="50px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="sr_row1">
                        <td>
                            <input type="hidden" name="sr_total_rows[]" value="1">
                            <select class="form-control select3 sr_medicine_name" id="sr_medicine_name1" name="sr_medicine_name_id_1">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control sr_medicine_category_display" readonly placeholder="-">
                        </td>
                        <td>
                            <select class="form-control sr_batch_no select3" id="sr_batch_no1" name="sr_batch_no_id_1">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                            </select>
                        </td>
                        <td>
                            <input type="text" readonly name="sr_expire_date_1" id="sr_expire_date1" class="form-control form-control-sm sr_exp_date sr-readonly-input"/>
                            <input type="hidden" readonly name="sr_expiry_1" id="sr_expiry_1" class="sr_expiry"/>
                        </td>
                        <td>
                            <input type="text" name="sr_sale_qty_1" class="form-control form-control-sm text-end sr_sale_qty sr-readonly-input" readonly placeholder="—">
                        </td>
                        <td>
                            <input type="text" name="sr_quantity_1" id="sr_quantity1" class="form-control form-control-sm sr_qty sr-qty-input" placeholder="0">
                        </td>
                        <td>
                            <input type="text" name="sr_sale_price_1" id="sr_sale_price1" class="form-control form-control-sm text-end sr_price sr-readonly-input" readonly>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm right-border-none sr_medicine_tax sr-readonly-input" name="sr_tax_1" id="sr_tax1" readonly autocomplete="off" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm right-border-none sr_medicine_discount" name="sr_mdiscount_1" id="sr_mdiscount1" autocomplete="off" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="sr_amount_1" id="sr_amount1" class="form-control form-control-sm text-end sr_subtot sr-readonly-input" value="0.00" readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger sr_delete_row" data-row-id="1" autocomplete="off" data-bs-toggle="tooltip" title="<?= $this->lang->line('remove') ?>"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Card 2: Doctor/Notes + Card 3: Summary/Payment (side by side) -->
<div class="d-flex flex-wrap gap-3">

    <!-- Card 2: Doctor / Notes -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('hospital_doctor'); ?></span></div>
            <div class="px-2 py-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('hospital_doctor'); ?></label>
                            <select name="sr_consultant_doctor" id="sr_consultant_doctor" class="form-control select3">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($doctors as $dvalue) { ?>
                                    <option value="<?php echo $dvalue['id']; ?>"><?php echo composeStaffNameByString($dvalue['name'], $dvalue['surname'], $dvalue['employee_id']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('doctor_name'); ?></label>
                            <input name="sr_doctor_name" id="sr_doctor_name" type="text" class="form-control form-control-sm"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-0">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="sr_note" rows="3" id="sr_note" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Billing Summary + Refund/Payment -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span></div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</span>
                <input type="text" value="0" name="sr_total" id="sr_total" class="form-control form-control-sm text-end sr_total sh-bill-summary-input" readonly>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</span>
                <div class="d-flex gap-1">
                    <input type="text" placeholder="Gross %" name="sr_discount_percent" id="sr_discount_percent" class="form-control form-control-sm text-end sr_discount_percent sh-bill-pct-input">
                    <input type="text" value="0" name="sr_discount" id="sr_discount" class="form-control form-control-sm text-end sr_discount sh-bill-summary-input" readonly>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
                <input type="text" value="0" name="sr_tax" id="sr_tax" class="form-control form-control-sm text-end sr_tax sh-bill-summary-input" readonly>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label fw-bold"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                <input type="text" value="0" name="sr_net_amount" id="sr_net_amount" class="form-control form-control-sm text-end sr_net_amount fw-bold sh-bill-summary-input">
            </div>
            <div class="px-2 py-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <select class="form-control form-control-sm sr_payment_mode" name="sr_payment_mode" id="sr_payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)</label>
                            <input type="text" name="sr_payment_amount" id="sr_payment_amount" class="form-control form-control-sm text-end sr_payment_amount" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="sr_cheque_div d-none" >
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                <input type="text" name="sr_cheque_no" id="sr_cheque_no" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                <input type="text" name="sr_cheque_date" id="sr_cheque_date" class="form-control form-control-sm date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label small"><?php echo $this->lang->line('attach_document'); ?></label>
                                <input type="file" name="sr_document" id="sr_document" class="form-control form-control-sm filestyle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Previous Returns (hidden by JS until bill is loaded) -->
<div id="sr_prev_returns_section" class="mt-3 d-none" >
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title">
                <i class="fa fa-history me-1"></i><?php echo $this->lang->line('previous_returns') ?: 'Previous Returns'; ?>
            </span>
            <span id="sr_prev_bill_no_label" class="badge bg-warning text-dark ms-2"></span>
            <span id="sr_prev_count_badge" class="badge bg-danger ms-2"></span>
        </div>
        <div class="p-2">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap"><?php echo $this->lang->line('return_no') ?: 'Return No'; ?></th>
                            <th class="text-nowrap"><?php echo $this->lang->line('date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-end"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-end"><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-center"><?php echo $this->lang->line('items') ?: 'Items'; ?></th>
                        </tr>
                    </thead>
                    <tbody id="sr_prev_returns_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

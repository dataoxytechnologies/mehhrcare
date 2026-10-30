<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<input name="pharmacy_bill_basic_id" id="pharmacy_bill_basic_id" type="hidden" class="form-control" value="<?php echo $bill['id'] ?>"/>
<input name="case_reference_id" id="case_reference_id" type="hidden" class="form-control" value="<?php echo $bill['case_reference_id'] ?>"/>
<input name="patient_id" id="patienteditid" type="hidden" class="form-control" value="<?php echo $bill['patient_id'] ?>"/>
<input name="customer_name" id="patienteditname" type="hidden" class="form-control" value="<?php echo $bill['customer_name'] ?>"/>
<input name="action_type" id="action_type" value="update" type="hidden" class="form-control"/>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID" style="table-layout:fixed;width:100%">
                <thead>
                    <tr class="font13 white-space-nowrap">
                        <th><?php echo $this->lang->line('medicine_name'); ?><small class="req"> *</small></th>
                        <th><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th><?php echo $this->lang->line('batch_no'); ?> <small class="req">*</small></th>
                        <th><?php echo $this->lang->line('expiry_date'); ?><small class="req"> *</small></th>
                        <th class="text-end"><?php echo $this->lang->line('quantity'); ?><small class="req"> *</small> <?php echo " | " . $this->lang->line('available_qty'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('sale_price') . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                        <th><?php echo $this->lang->line('tax'); ?></th>
                        <th><?php echo $this->lang->line('discount'); ?> (%)</th>
                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                        <th width="50px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_value = 1;
                    foreach ($detail as $bill_detail_key => $bill_detail_value) { ?>
                        <input type="hidden" name="previous_ids[]" value="<?php echo $bill_detail_value['id']; ?>">
                        <tr id="row<?php echo $row_value ?>">
                            <td width="12%">
                                <input type="hidden" name="insert_id_<?php echo $row_value; ?>" value="<?php echo $bill_detail_value['id']; ?>">
                                <input type="hidden" name="total_rows[]" value="<?php echo $row_value; ?>">
                                <input type="hidden" class="post_medicine_id" value="<?php echo $bill_detail_value['medicine_id']; ?>">
                                <input type="hidden" class="post_medicine_batch_detail_id" value="<?php echo $bill_detail_value['medicine_batch_detail_id']; ?>">
                                <input type="hidden" class="sale_price" value="<?php echo $bill_detail_value['sale_price']; ?>">
                                <input type="hidden" class="quantity" value="<?php echo $bill_detail_value['quantity']; ?>">
                                <input type="hidden" class="mdiscount" value="<?php echo $bill_detail_value['discount']; ?>">
                                <select class="form-control select3 medicine_name w-100"  id="medicine_name<?php echo $row_value; ?>" name='medicine_name_id_<?php echo $row_value; ?>'>
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                            </td>
                            <td>
                                <input type="text" class="form-control medicine_category_display" readonly placeholder="-">
                            </td>
                            <td width="12%">
                                <select class="form-control batch_no select3" id="batch_no<?php echo $row_value; ?>" name="batch_no_id_<?php echo $row_value; ?>">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                            </td>
                            <td width="12%">
                                <input type="text" readonly name="expire_date_<?php echo $row_value; ?>" id="expire_date<?php echo $row_value; ?>" class="form-control exp_date"/>
                                <input type="hidden" readonly name="expiry_<?php echo $row_value; ?>" id="expiry_<?php echo $row_value; ?>" class="form-control expiry"/>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="quantity_<?php echo $row_value; ?>" id="quantity<?php echo $row_value; ?>" data-rowid="<?php echo $row_value; ?>" class="form-control text-end edit_refund_qty qty">
                                    <input type="hidden" id="valid_reund_quantity<?php echo $row_value; ?>" class="form-control text-end qty">
                                    <span class="input-group-text text-danger available_qty sh-fs-10pt"  id="totalqty0">&nbsp;&nbsp;</span>
                                </div>
                                <input type="hidden" class="available_quantity" name="available_quantity_<?php echo $row_value; ?>" id="available_quantity0">
                            </td>
                            <td class="text-end">
                                <input type="hidden" name="sale_rate_price_<?php echo $row_value; ?>" id="sale_rate_price<?php echo $row_value; ?>" class="form-control text-end sale_rate" readonly>
                                <input type="hidden" name="tpa_rate_<?php echo $row_value; ?>" id="tpa_rate<?php echo $row_value; ?>" class="form-control text-end tpa_rate" readonly>
                                <input type="text" name="sale_price_<?php echo $row_value; ?>" id="sale_price<?php echo $row_value; ?>" class="form-control text-end price" readonly>
                                <span class="text-danger"><?php echo form_error('sale_price[]'); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="input-group">
                                    <input type="text" class="form-control right-border-none medicine_tax" name="tax_<?php echo $row_value; ?>" readonly id="tax<?php echo $row_value; ?>" autocomplete="off">
                                    <span class="input-group-text"> %</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="input-group">
                                    <input type="text" class="form-control right-border-none medicine_discount" name="mdiscount_<?php echo $row_value; ?>" id="mdiscount<?php echo $row_value; ?>" autocomplete="off">
                                    <span class="input-group-text"> %</span>
                                </div>
                            </td>
                            <td class="text-end" width="12%">
                                <input type="text" name="amount_<?php echo $row_value; ?>" id="amount<?php echo $row_value; ?>" placeholder="" class="form-control text-end subtot" readonly>
                                <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger editdelete_row" data-row-id="<?php echo $row_value; ?>" autocomplete="off"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    <?php $row_value++; } ?>
                </tbody>
            </table>
            <div class="text-end">
                <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-3">
    <div class="sh-flex-col-260">
        <div class="sh-form-card h-100">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('hospital_doctor'); ?></span></div>
            <div class="px-2 py-3">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('hospital_doctor'); ?></label>
                            <select name='consultant_doctor' id="consultant_doctor" class="form-control select3 w-100" <?php if ($disable_option == true) { echo "disabled"; } ?>>
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('doctor_name'); ?></label>
                            <input name="doctor_name" id="doctname" type="text" class="form-control" value="<?php echo $bill['doctor_name'] ?>"/>
                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"><?php echo $bill['note']; ?></textarea>
                        </div>
                    </div>
                    <div>
                        <?php echo $custom_fields_value; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sh-flex-col-260">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span></div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="<?php echo $bill['total']; ?>" name="total" id="total" class="form-control form-control-sm text-end total sh-mw-130" >
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-1">
                    <input type="text" placeholder="Gross %" name="discount_percent" id="discount_percent" class="form-control form-control-sm text-end discount_percent" value="<?php echo $bill['discount_percentage'] ?>" style="max-width:75px;">
                    <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" onkeyup="get_percentage(this.value)" value="<?php echo $bill['discount']; ?>" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-mw-130" >
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="<?php echo $bill['tax'] ?>" id="tax" class="form-control form-control-sm text-end tax sh-mw-130" readonly>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label fw-bold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" onchange="amount_settlement(this.value)" placeholder="<?php echo $this->lang->line('net_amount'); ?>" value="<?php echo $bill['net_amount'] ?>" name="net_amount" id="net_amount" class="form-control form-control-sm text-end net_amount fw-bold sh-mw-130" >
            </div>
            <div class="px-2 py-3">
                <p class="sh-info-label fw-semibold mb-2"><?php echo $this->lang->line('refund_amount'); ?></p>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label>
                            <input type="text" name="payment_amount" id="payment_refund_amount" class="form-control form-control-sm payment_refund_amount text-end">
                            <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="cheque_div d-none" >
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
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

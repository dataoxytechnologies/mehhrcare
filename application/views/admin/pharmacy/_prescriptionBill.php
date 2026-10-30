<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<?php if (!empty($prescription_data) && !empty($prescription_data->medicines)) { ?>

<input name="customer_name" id="patient_name" type="hidden" class="form-control"/>
<input name="action_type" id="action_type" value="insert" type="hidden" class="form-control"/>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID">
                <thead>
                    <tr class="font13 white-space-nowrap">
                        <th width="12%"><?php echo $this->lang->line('medicine_name'); ?><small class="req"> *</small></th>
                        <th width="12%"><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th width="12%"><?php echo $this->lang->line('batch_no'); ?> <small class="req">*</small></th>
                        <th width="12%"><?php echo $this->lang->line('expiry_date'); ?><small class="req"> *</small></th>
                        <th class="text-end" width="10%"><?php echo $this->lang->line('quantity'); ?><small class="req"> *</small> <?php echo " | " . $this->lang->line('available_qty'); ?></th>
                        <th class="text-end" width="10%"><?php echo $this->lang->line('sale_price') . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                        <th class="text-end" width="10%"><?php echo $this->lang->line('tax'); ?></th>
                        <th width="10%"><?php echo $this->lang->line('discount'); ?> (%)</th>
                        <th class="text-end" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                        <th width="2%"></th>
                    </tr>
                </thead>
                <?php
                $row_value = 1;
                foreach ($prescription_data->medicines as $medicine_key => $medicine_value) { ?>
                    <tr id="row<?php echo $row_value; ?>">
                        <td>
                            <input type="hidden" name="total_rows[]" value="<?php echo $row_value; ?>">
                            <input type="hidden" class="post_medicine_category_id" value="<?php echo $medicine_value->medicine_category_id; ?>">
                            <input type="hidden" class="post_medicine_id" value="<?php echo $medicine_value->pharmacy_id; ?>">
                            <input type="hidden" class="post_medicine_batch_detail_id" value="">
                            <input type="hidden" class="sale_price" value="">
                            <input type="hidden" class="quantity" value="">
                            <input type="hidden" class="mdiscount" value="">
                            <select class="form-control select3 medicine_name w-100"  id="medicine_name0" name='medicine_name_id_<?php echo $row_value; ?>'>
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                            </select>
                            <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" class="form-control medicine_category_display" readonly placeholder="-">
                        </td>
                        <td>
                            <select class="form-control batch_no select3" id="batch_no0" name="batch_no_id_<?php echo $row_value; ?>">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                            </select>
                            <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                        </td>
                        <td>
                            <input type="text" readonly name="expire_date_<?php echo $row_value; ?>" id="expire_date<?php echo $row_value; ?>" class="form-control exp_date"/>
                            <input type="hidden" readonly name="expiry_<?php echo $row_value; ?>" id="expiry_<?php echo $row_value; ?>" class="form-control expiry"/>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" name="quantity_<?php echo $row_value; ?>" id="quantity0" class="form-control text-end qty">
                                <span class="input-group-text text-danger available_qty sh-fs-10pt"  id="totalqty0">&nbsp;&nbsp;</span>
                            </div>
                            <input type="hidden" class="available_quantity" name="available_quantity_<?php echo $row_value; ?>" id="available_quantity0">
                        </td>
                        <td class="text-end">
                            <input type="text" name="sale_price_<?php echo $row_value; ?>" id="sale_price0" class="form-control text-end price" readonly>
                            <span class="text-danger"><?php echo form_error('sale_price[]'); ?></span>
                        </td>
                        <td class="text-end">
                            <div class="input-group">
                                <input type="text" class="form-control right-border-none medicine_tax" name="tax_<?php echo $row_value; ?>" readonly id="tax0" autocomplete="off">
                                <span class="input-group-text"> %</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="input-group">
                                <input type="text" class="form-control right-border-none medicine_discount" name="mdiscount_<?php echo $row_value; ?>" id="mdiscount0" autocomplete="off">
                                <span class="input-group-text"> %</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <input type="text" name="amount_<?php echo $row_value; ?>" id="amount0" placeholder="" class="form-control text-end subtot" readonly>
                            <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger delete_row" data-row-id="<?php echo $row_value; ?>" autocomplete="off"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                <?php $row_value++; } ?>
            </table>
            <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
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
                                <option value=""><?php echo $this->lang->line('select') ?></option>
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
                            <input name="doctor_name" id="doctname" type="text" class="form-control"/>
                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                        </div>
                    </div>
                    <div>
                        <?php echo display_custom_fields('pharmacy'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sh-flex-col-260" id="showamtdiv">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span></div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('total'); ?>" value="0" name="total" id="total" class="form-control form-control-sm text-end total sh-mw-130" >
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-1">
                    <input type="text" placeholder="Gross %" name="discount_percent" id="discount_percent" class="form-control form-control-sm text-end discount_percent" style="max-width:75px;">
                    <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" value="0" onkeyup="get_percentage(this.value)" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-mw-130" >
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="0" id="tax" class="form-control form-control-sm text-end tax sh-mw-130" readonly>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label fw-bold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('net_amount'); ?>" value="0" name="net_amount" id="net_amount" class="form-control form-control-sm text-end net_amount fw-bold sh-mw-130" >
            </div>
            <div class="px-2 py-3">
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
                            <label class="form-label small"><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                            <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm payment_amount text-end">
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

<?php } else { ?>

<input name="customer_name" id="patient_name" type="hidden" class="form-control"/>
<input name="patient_id" id="patient_id" type="hidden" class="form-control"/>
<input name="action_type" id="action_type" value="insert" type="hidden" class="form-control"/>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID">
                <thead>
                    <tr class="font13 white-space-nowrap">
                        <th width="15%"><?php echo $this->lang->line('medicine_name'); ?><small class="req"> *</small></th>
                        <th width="15%"><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th width="15%"><?php echo $this->lang->line('batch_no'); ?> <small class="req">*</small></th>
                        <th width="15%"><?php echo $this->lang->line('expiry_date'); ?><small class="req"> *</small></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('quantity'); ?><small class="req"> *</small> <?php echo " | " . $this->lang->line('available_qty'); ?></th>
                        <th class="text-end" width="13%"><?php echo $this->lang->line('sale_price') . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                        <th class="text-end" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                        <th width="2%"></th>
                    </tr>
                </thead>
                <tr id="row1">
                    <td>
                        <input type="hidden" name="total_rows[]" value="1">
                        <select class="form-control select3 medicine_name w-100"  id="medicine_name0" name='medicine_name_id_1'>
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                        </select>
                        <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                    </td>
                    <td>
                        <input type="text" class="form-control medicine_category_display" readonly placeholder="-">
                    </td>
                    <td>
                        <select class="form-control batch_no select3" id="batch_no0" name="batch_no_id_1">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                        </select>
                        <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                    </td>
                    <td>
                        <input type="text" readonly name="expire_date_1" id="expire_date0" class="form-control exp_date"/>
                        <input type="hidden" readonly name="expiry_1" id="expiry_0" class="form-control expiry"/>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" name="quantity_1" id="quantity0" class="form-control text-end qty">
                            <span class="input-group-text text-danger available_qty sh-fs-10pt"  id="totalqty0">&nbsp;&nbsp;</span>
                        </div>
                        <input type="hidden" class="available_quantity" name="available_quantity_1" id="available_quantity0">
                    </td>
                    <td class="text-end">
                        <input type="text" name="sale_price_1" id="sale_price0" class="form-control text-end price" readonly>
                        <span class="text-danger"><?php echo form_error('sale_price[]'); ?></span>
                    </td>
                    <td class="text-end">
                        <input type="text" name="amount_1" id="amount0" placeholder="" class="form-control text-end subtot" readonly>
                        <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger delete_row" data-row-id="1" autocomplete="off"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            </table>
            <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
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
                            <select name='consultant_doctor' id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control select3 w-100" <?php if ($disable_option == true) { echo "disabled"; } ?>>
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('doctor_name'); ?></label>
                            <input name="doctor_name" id="doctname" type="text" class="form-control"/>
                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sh-flex-col-260" id="showamtdiv">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span></div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('total'); ?>" value="0" name="total" id="total" class="form-control form-control-sm text-end total sh-mw-130" >
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-1">
                    <input type="text" placeholder="Gross %" name="discount_percent" id="discount_percent" class="form-control form-control-sm text-end discount_percent" style="max-width:75px;">
                    <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" value="0" name="discount" onkeyup="get_percentage(this.value)" id="discount" class="form-control form-control-sm text-end discount sh-mw-130" >
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-1">
                    <input type="text" placeholder="%" name="tax_percent" value="" id="tax_percent" class="form-control form-control-sm text-end tax_percent" style="max-width:75px;">
                    <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="0" id="tax" class="form-control form-control-sm text-end tax sh-mw-130" >
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="sh-info-label fw-bold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" placeholder="<?php echo $this->lang->line('net_amount'); ?>" value="0" name="net_amount" id="net_amount" class="form-control form-control-sm text-end net_amount fw-bold sh-mw-130" >
            </div>
            <div class="px-2 py-3">
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
                            <label class="form-label small"><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                            <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-sm payment_amount text-end">
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
                <div class="col-md-12 mt-2">
                    <div class="mb-3">
                        <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                        <textarea name="note" id="note" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>

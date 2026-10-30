<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<input type="hidden" name="id" value="<?php echo set_value('id', $result->id); ?>">
<input type="hidden" value="<?php echo $result->charge_category_id; ?>" name="post_charge_category_id">
<input type="hidden" value="<?php echo $result->charge_id; ?>" name="post_charge_id">

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_details'); ?></span>
    </div>
    <div class="p-3">
        <div class="row g-2">
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('test_name'); ?> <small class="req">*</small></label>
                    <input type="text" name="test_name" class="form-control form-control-sm" value="<?php echo set_value('test_name', $result->test_name); ?>">
                    <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('short_name'); ?> <small class="req">*</small></label>
                    <input type="text" name="short_name" class="form-control form-control-sm" value="<?php echo set_value('short_name', $result->short_name); ?>">
                    <span class="text-danger"><?php echo form_error('short_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('test_type'); ?></label>
                    <input type="text" name="test_type" class="form-control form-control-sm" value="<?php echo set_value('test_type', $result->test_type); ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('category_name'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 w-100" name="radiology_category_id">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($categoryName as $dkey => $dvalue) { ?>
                        <option value="<?php echo $dvalue['id']; ?>" <?php echo set_select('radiology_category_id', $dvalue['id'], ($result->radiology_category_id == $dvalue['id']) ? true : false); ?>><?php echo $dvalue['lab_name']; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('radio_category_id'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('sub_category'); ?></label>
                    <input type="text" name="sub_category" class="form-control form-control-sm" value="<?php echo set_value('sub_category', $result->sub_category); ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('report_days'); ?> <small class="req">*</small></label>
                    <input type="number" min="0" name="report_days" class="form-control form-control-sm" value="<?php echo set_value('report_days', $result->report_days); ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm charge_category select2 w-100" name="charge_category_id">
                        <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) { ?>
                        <option value="<?php echo $charge_cat_value['id']; ?>" <?php echo set_select('charge_category_id', $charge_cat_value['id'], ($result->charge_category_id == $charge_cat_value['id']) ? true : false); ?>><?php echo $charge_cat_value['name']; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('code'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm charge select2 w-100" name="code" id="code">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('tax'); ?> (%) <small class="req">*</small></label>
                    <input class="form-control form-control-sm" name="tax" id="tax" value="<?php echo set_value('tax', $result->percentage); ?>" readonly>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                    <input class="form-control form-control-sm" name="standard_charge" id="standard_charge" value="<?php echo set_value('standard_charge', $result->standard_charge); ?>" readonly>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                    <input readonly value="<?php echo set_value('standard_charge', amountFormat($result->standard_charge + calculatePercent($result->standard_charge, $result->percentage))); ?>" class="form-control form-control-sm" name="amount" id="amount">
                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                </div>
            </div>
            <div class="col-sm-12">
                <?php echo display_custom_fields('radiologytest', $result->id); ?>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_parameter_name'); ?></span>
    </div>
    <div class="p-2">
        <div class="table_inner">
            <table class="table table-sm table-bordered table-hover mb-0" id="tableID">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('test_parameter_name'); ?> <small class="req">*</small></th>
                        <th><?php echo $this->lang->line('reference_range'); ?> <small class="req">*</small></th>
                        <th><?php echo $this->lang->line('unit'); ?> <small class="req">*</small></th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($result->radiology_parameter)) {
                        $total_rows = 1;
                        foreach ($result->radiology_parameter as $param_key => $param_value) {
                    ?>
                    <input type="hidden" name="prev_inserted[]" value="<?php echo $param_value->id; ?>">
                    <tr id="row<?php echo $total_rows; ?>">
                        <td width="35%">
                            <input type="hidden" name="total_rows[]" value="<?php echo $total_rows; ?>">
                            <input type="hidden" name="inserted_id_<?php echo $total_rows; ?>" value="<?php echo $param_value->id; ?>">
                            <input type="hidden" class="post_parameter_id" name="post_parameter_id" value="<?php echo $param_value->radiology_parameter_id; ?>">
                            <select class="form-control form-control-sm select2 radiology_parmeter w-100" id="parameter_name_<?php echo $total_rows; ?>" name="parameter_name_<?php echo $total_rows; ?>">
                                <option value="<?php echo set_value('radiology_parameter_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($parametername as $dkey => $dvalue) { ?>
                                <option value="<?php echo $dvalue['id']; ?>" <?php echo set_select('parameter_name_' . $total_rows, $dvalue['id'], ($param_value->radiology_parameter_id == $dvalue['id']) ? true : false); ?>><?php echo $dvalue['parameter_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="30%">
                            <input type="text" readonly name="reference_range_<?php echo $total_rows; ?>" id="reference_range_<?php echo $total_rows; ?>" class="form-control form-control-sm reference_range">
                        </td>
                        <td width="30%">
                            <input type="text" readonly name="radio_unit_<?php echo $total_rows; ?>" id="radio_unit_" class="form-control form-control-sm radio_unit">
                        </td>
                        <td class="text-center">
                            <?php if ($this->rbac->hasPrivilege('radiology_parameter', 'can_delete')) { ?>
                            <button type="button" class="btn btn-sm btn-light delete_row" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-times text-danger"></i></button>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                        $total_rows++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
        </div>
    </div>
</div>

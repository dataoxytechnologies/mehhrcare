<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<input type="hidden" name="id" value="0">

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_details'); ?></span>
    </div>
    <div class="p-3">
        <div class="row g-2">
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('test_name'); ?> <small class="req">*</small></label>
                    <input type="text" name="test_name" class="form-control form-control-sm">
                    <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('short_name'); ?> <small class="req">*</small></label>
                    <input type="text" name="short_name" class="form-control form-control-sm">
                    <span class="text-danger"><?php echo form_error('short_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('test_type'); ?></label>
                    <input type="text" name="test_type" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('category_name'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 w-100" name="pathology_category_id">
                        <option value="<?php echo set_value('pathology_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($categoryName as $dkey => $dvalue) { ?>
                        <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['category_name']; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('pathology_category_id'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('sub_category'); ?></label>
                    <input type="text" name="sub_category" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('method'); ?></label>
                    <input type="text" name="method" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('report_days'); ?> <small class="req">*</small></label>
                    <input type="number" min="0" value="0" name="report_days" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 charge_category w-100" name="charge_category_id">
                        <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($charge_category as $dkey => $dvalue) { ?>
                        <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['name']; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('charge_name'); ?> <small class="req">*</small></label>
                    <select class="form-control form-control-sm select2 charge w-100" name="code" id="code">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('tax'); ?> (%) <small class="req">*</small></label>
                    <input class="form-control form-control-sm" name="tax" id="tax" readonly>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                    <input class="form-control form-control-sm" name="standard_charge" id="standard_charge" readonly>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="mb-3">
                    <label class="form-label small"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                    <input class="form-control form-control-sm" name="amount" id="amount" readonly>
                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                </div>
            </div>
            <div class="col-sm-12">
                <?php echo display_custom_fields('pathologytest'); ?>
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
                    <tr id="row0">
                        <input type="hidden" name="total_rows[]" value="1">
                        <input type="hidden" name="inserted_id_1" value="0">
                        <td width="35%">
                            <select class="form-control form-control-sm select2 pathology_parmeter w-100" id="parameter_name_1" name="parameter_name_1">
                                <option value="<?php echo set_value('pathology_parameter_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($parametername as $dkey => $dvalue) { ?>
                                <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['parameter_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="30%">
                            <input type="text" readonly name="reference_range_1" id="reference_range_1" class="form-control form-control-sm reference_range">
                        </td>
                        <td width="30%">
                            <input type="text" readonly name="patho_unit_1" id="patho_unit_1" class="form-control form-control-sm patho_unit">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-light delete_row" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-times text-danger"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            <a class="btn btn-info btn-sm add-record mt-2" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
        </div>
    </div>
</div>

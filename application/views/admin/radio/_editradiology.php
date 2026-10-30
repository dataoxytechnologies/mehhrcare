<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<!-- Card 1: Test Items -->
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_name'); ?></span>
    </div>
    <div class="p-2">
        <div class="sh-overflow-x-hidden">
            <table class="table table-sm table-striped table-bordered table-hover tblProducts sh-radio-edit-table-wide mb-0" id="tableID">
                <thead>
                    <tr>
                        <th width="30%"><?php echo $this->lang->line('test_name'); ?> <small class="req">*</small></th>
                        <th width="13%"><?php echo $this->lang->line('report_days'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('report_date'); ?> <small class="req">*</small></th>
                        <th width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end" width="17%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody id="addrows">
                <?php
                if (!empty($radiology_data->radiology_report)) {
                    $total_rows = 1;
                    foreach ($radiology_data->radiology_report as $report_key => $report_value) {
                ?>
                <tr id="row<?php echo $total_rows; ?>">
                    <td>
                        <input type="hidden" name="prev_reports[]" value="<?php echo $report_value->id; ?>">
                        <input type="hidden" name="total_rows[]" value="<?php echo $total_rows; ?>">
                        <input type="hidden" name="inserted_id_<?php echo $total_rows; ?>" value="<?php echo $report_value->id; ?>">
                        <select class="form-control form-control-sm test_name select2 w-100"  name="test_name_<?php echo $total_rows; ?>" id="<?php echo $total_rows; ?>">
                            <option value="<?php echo set_value('test_name_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($testlist as $dkey => $dvalue) { ?>
                                <option value="<?php echo $dvalue["id"]; ?>" <?php echo set_select('test_name_' . $total_rows, $dvalue["id"], ($report_value->radiology_id == $dvalue["id"]) ? TRUE : FALSE); ?>><?php echo $dvalue["test_name"] . " (" . $dvalue["short_name"] . ")"; ?></option>
                            <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('test_name_id[]'); ?></span>
                    </td>
                    <td>
                        <input type="text" name="reportday_<?php echo $total_rows; ?>" id="reportday_<?php echo $total_rows; ?>" value="<?php echo $report_value->report_days; ?>" class="form-control form-control-sm text-end days" readonly>
                        <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                    </td>
                    <td>
                        <input type="text" name="reportdate_<?php echo $total_rows; ?>" id="reportdate_<?php echo $total_rows; ?>" value="<?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); ?>" class="form-control form-control-sm text-end report_date">
                        <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control form-control-sm text-end taxpercent" name="taxpercent_<?php echo $total_rows; ?>" id="taxpercent_<?php echo $total_rows; ?>" readonly autocomplete="off" value="<?php echo $report_value->tax_percentage; ?>">
                            <span class="input-group-text">%</span>
                        </div>
                        <input type="hidden" name="taxamount_<?php echo $total_rows; ?>" id="taxamount_<?php echo $total_rows; ?>" value="<?php echo $report_value->apply_charge * $report_value->tax_percentage / 100; ?>" class="taxamount">
                    </td>
                    <td class="text-end">
                        <input type="text" name="amount_<?php echo $total_rows; ?>" id="amount_<?php echo $total_rows; ?>" value="<?php echo $report_value->apply_charge; ?>" class="form-control form-control-sm text-end amount" readonly>
                        <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger delete_row" data-row-id="<?php echo $total_rows; ?>"><i class="fa fa-remove"></i></button>
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
        <div class="d-flex justify-content-end mt-2">
            <a class="btn btn-info btn-sm add-record" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
        </div>
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
                            <select name='consultant_doctor' id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control consultant_doctor select2 w-100">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($radiology_data->doctor_id)) && ($radiology_data->doctor_id == $dvalue["id"])) { echo "selected"; } ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " ( " . $dvalue["employee_id"] . " )"; ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('doctor_name'); ?></label>
                            <input name="doctor_name" id="doctname" type="text" value="<?php echo $radiology_data->doctor_name; ?>" class="form-control">
                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"><?php echo $radiology_data->note; ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $custom_fields_value; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Billing Summary -->
    <div class="sh-flex-col-260" id="showamtdiv">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span>
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="<?php echo $radiology_data->total; ?>" name="total" id="total" class="form-control form-control-sm text-end total sh-mw-130"  readonly />
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm sh-w-130">
                        <input type="text" value="<?php echo $radiology_data->discount_percentage; ?>" name="discount_percent" id="discount_percent" class="form-control text-end discount_percent" onchange="addTotal()" />
                        <span class="input-group-text">%</span>
                    </div>
                    <input type="text" value="<?php echo $radiology_data->discount; ?>" onkeyup="get_percentage(this.value)" name="discount" id="discount" class="form-control form-control-sm text-end discount sh-w-130" />
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="<?php echo $radiology_data->tax; ?>" name="tax" id="tax" class="form-control form-control-sm text-end tax sh-mw-130"  readonly />
            </div>
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <span class="text-secondary small fw-semibold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                <input type="text" value="<?php echo $radiology_data->net_amount; ?>" name="net_amount" id="net_amount" class="form-control form-control-sm text-end text-secondary fw-semibold net_amount sh-mw-130"  readonly />
            </div>
        </div>
    </div>

</div><!--./flex-->

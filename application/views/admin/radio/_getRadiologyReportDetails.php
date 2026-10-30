<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$collected_by_name = composeStaffNameByString(
    $result->collection_specialist_staff_name,
    $result->collection_specialist_staff_surname,
    $result->collection_specialist_staff_employee_id
);
?>

<input type="hidden" name="radiology_bill_id" value="<?php echo $result->radiology_bill_id; ?>">
<input type="hidden" name="radiology_center" value="<?php echo html_escape($result->radiology_center); ?>">
<input type="hidden" name="collected_date" value="<?php echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?>">
<input type="hidden" name="collected_by" value="<?php echo html_escape($collected_by_name); ?>">
<input type="hidden" name="collected_id" value="<?php echo $result->collection_specialist; ?>">

<!-- Card 1: Test Info (read-only) -->
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-flask me-1 opacity-75"></i><?php echo $this->lang->line('test_name'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('test_name'); ?></div>
                <div class="sh-info-value highlight"><?php echo html_escape($result->short_name); ?></div>
            </div>
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('date_of_collection'); ?></div>
                <div class="sh-info-value"><?php echo !empty($result->collection_date) ? $this->customlib->YYYYMMDDTodateFormat($result->collection_date) : '—'; ?></div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('expected_date'); ?></div>
                <div class="sh-info-value"><?php echo !empty($result->reporting_date) ? $this->customlib->YYYYMMDDTodateFormat($result->reporting_date) : '—'; ?></div>
            </div>
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('collection_by'); ?></div>
                <div class="sh-info-value"><?php echo !empty($collected_by_name) ? html_escape($collected_by_name) : '—'; ?></div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('approve_date'); ?></div>
                <div class="sh-info-value"><?php echo !empty($result->parameter_update) ? $this->customlib->YYYYMMDDTodateFormat($result->parameter_update) : '—'; ?></div>
            </div>
            <div class="col-6 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('radiology_center'); ?></div>
                <div class="sh-info-value"><?php echo !empty($result->radiology_center) ? html_escape($result->radiology_center) : '—'; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Card 2: Approved By & Upload Report -->
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-check-circle me-1 opacity-75"></i><?php echo $this->lang->line('approved_by'); ?> &amp; <?php echo $this->lang->line('upload_report'); ?></span>
    </div>
    <div class="row g-2 px-3 pt-2 pb-2">
        <div class="col-md-4">
            <label class="form-label" for="approved_by"><?php echo $this->lang->line('approved_by'); ?><small class="req"> *</small></label>
            <select class="form-control select2" name="approved_by" id="approved_by">
                <option value=""><?php echo $this->lang->line('select'); ?></option>
                <?php foreach ($staff as $staff_value) { ?>
                <option value="<?php echo $staff_value['id']; ?>" <?php echo set_select('approved_by', $staff_value['id'], (set_value('approved_by', $result->approved_by) == $staff_value['id']) ? TRUE : FALSE); ?>>
                    <?php echo html_escape($staff_value['name'] . ' ' . $staff_value['surname'] . ' (' . $staff_value['employee_id'] . ')'); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="approve_date"><?php echo $this->lang->line('approve_date'); ?><small class="req"> *</small></label>
            <input class="form-control date" type="text" name="approve_date" id="approve_date"
                   value="<?php echo !empty($result->parameter_update) ? $this->customlib->YYYYMMDDTodateFormat($result->parameter_update) : $this->customlib->YYYYMMDDTodateFormat(date('Y-m-d')); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="attachment_report"><?php echo $this->lang->line('upload_report'); ?></label>
            <input class="filestyle form-control" type="file" name="attachment_report" id="attachment_report" size="20">
            <?php if (!empty($result->radiology_report)) { ?>
            <a class="d-inline-flex align-items-center gap-1 mt-1 small text-primary"
               href="<?php echo site_url('admin/radio/downloadReport/' . $result->id); ?>">
                <i class="fa fa-download"></i> <?php echo html_escape($result->report_name); ?>
            </a>
            <?php } ?>
        </div>
        <div class="col-12">
            <label class="form-label" for="test_result"><?php echo $this->lang->line('result'); ?></label>
            <textarea name="test_result" id="test_result" rows="2" class="form-control"><?php echo html_escape($result->radiology_result); ?></textarea>
        </div>
    </div>
</div>

<!-- Card 3: Test Parameters -->
<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-list-alt me-1 opacity-75"></i><?php echo $this->lang->line('test_parameter_name'); ?></span>
    </div>
    <div class="table-responsive">
        <input type="hidden" name="radiology_report_id" value="<?php echo $result->id; ?>">
        <table class="table table-sm sh-tests-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('test_parameter_name'); ?><small class="req"> *</small></th>
                    <th><?php echo $this->lang->line('report_value'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('reference_range'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $row_counter = 1;
                foreach ($result->radiology_parameter as $parameter_value) {
                ?>
                <input type="hidden" name="radiology_parameterdetails[]" value="<?php echo $parameter_value->id; ?>">
                <input type="hidden" name="radiology_reference_range_<?php echo $parameter_value->id; ?>" value="<?php echo html_escape($parameter_value->reference_range); ?>">
                <tr>
                    <td class="text-secondary"><?php echo $row_counter; ?></td>
                    <td>
                        <span class="fw-semibold"><?php echo html_escape($parameter_value->parameter_name); ?></span>
                        <?php if (!empty($parameter_value->description)) { ?>
                        <div class="text-muted small mt-1"><strong><?php echo $this->lang->line('description'); ?>:</strong> <?php echo html_escape($parameter_value->description); ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="hidden" name="prev_id_<?php echo $parameter_value->id; ?>" value="<?php echo $parameter_value->radiology_report_parameterdetail_id; ?>">
                            <input type="text" class="form-control" name="radiology_parameter_<?php echo $parameter_value->id; ?>" value="<?php echo html_escape($parameter_value->radiology_report_value); ?>">
                            <span class="input-group-text" name="radiology_parameterdetails_<?php echo $parameter_value->id; ?>"><?php echo html_escape($parameter_value->unit_name); ?></span>
                        </div>
                    </td>
                    <td class="text-end"><?php echo html_escape($parameter_value->reference_range . ' ' . $parameter_value->unit_name); ?></td>
                </tr>
                <?php $row_counter++; } ?>
            </tbody>
        </table>
    </div>
</div>

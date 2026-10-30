<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$balance         = $result->net_amount - $result->total_deposit;
$tax_pct         = ($result->total - $result->discount) != 0
    ? ($result->tax * 100) / ($result->total - $result->discount)
    : 0;
$bill_prefix     = $this->customlib->getPatientSessionPrefixByType('radiology_billing');
?>

<!-- Bill Info Card -->
<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-file-text me-1"></i><?php echo $this->lang->line('bill_details'); ?></span>
        <span class="ms-auto text-muted small"><?php echo $this->lang->line('bill_no'); ?>: <strong><?php echo $bill_prefix . $result->id; ?></strong></span>
        <span class="text-muted small ms-3"><?php echo $this->lang->line('reporting_date'); ?>: <strong><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result->date); ?></strong></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($result->patient_name, $result->patient_id); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getPatientAge($result->age, $result->month, $result->day); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result->gender)); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->case_reference_id); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('doctor_name'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->doctor_name); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->mobileno); ?></span>
            </div>
        </div>
        <?php if (!empty($result->email) || !empty($result->blood_group_name) || !empty($result->address)) { ?>
        <div class="row g-0 sh-row-divider">
            <?php if (!empty($result->email)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->email); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result->blood_group_name)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->blood_group_name); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result->address)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->address); ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        <?php if (!empty($prescription) || !empty($result->organisation_name) || !empty($result->insurance_id)) { ?>
        <div class="row g-0 sh-row-divider">
            <?php if (!empty($prescription)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('prescription_no'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($prescription); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result->organisation_name)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->organisation_name); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result->insurance_id)) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->insurance_id); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($result->insurance_validity); ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('generated_by'); ?></span>
                <span class="sh-info-value">
                    <?php if (!($superadmin_restriction == 'disabled' && $result->staff_roles_id == 7)) {
                        echo composeStaffNameByString($result->name, $result->surname, $result->employee_id);
                    } ?>
                </span>
            </div>
            <?php if (!empty($result->note)) { ?>
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->note); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($fields)) {
                foreach ($fields as $fields_value) {
                    $display_field = $fields_value->type == 'link'
                        ? '<a href="' . $result->{$fields_value->name} . '" target="_blank">' . html_escape($result->{$fields_value->name}) . '</a>'
                        : html_escape($result->{$fields_value->name}); ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                <span class="sh-info-value"><?php echo $display_field; ?></span>
            </div>
            <?php } } ?>
        </div>
    </div>
</div>

<!-- Totals Summary -->
<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-calculator me-1"></i><?php echo $this->lang->line('amount_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                <span class="sh-info-value"><?php echo $result->total; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('total_discount'); ?></span>
                <span class="sh-info-value"><?php echo $currency_symbol . (!empty($result->discount) ? $result->discount . ' (' . $result->discount_percentage . '%)' : '0.00 (0.00%)'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('total_tax'); ?></span>
                <span class="sh-info-value"><?php echo $currency_symbol . (!empty($result->tax) ? $result->tax . ' (' . amountFormat($tax_pct) . '%)' : '0.00'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                <span class="sh-info-value highlight"><?php echo $result->net_amount; ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('total_deposit'); ?></span>
                <span class="sh-info-value text-success fw-semibold"><?php echo $currency_symbol . (!empty($result->total_deposit) ? $result->total_deposit : '0.00'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('balance_amount'); ?></span>
                <span class="sh-info-value fw-bold <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?>"><?php echo $currency_symbol . amountFormat($balance); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Test Details Table -->
<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-flask me-1"></i><?php echo $this->lang->line('test_details'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('test_name'); ?></th>
                    <th><?php echo $this->lang->line('sample_collected'); ?></th>
                    <th><?php echo $this->lang->line('expected_date'); ?></th>
                    <th><?php echo $this->lang->line('approved_by') . ' / ' . $this->lang->line('update_date'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount'); ?></th>
                    <th class="text-center noExport"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $row = 1;
                foreach ($result->radiology_report as $rep) {
                    $disc_amt = ($rep->apply_charge * $result->discount_percentage) / 100;
                    $tax_amt  = (($rep->apply_charge - $disc_amt) * $rep->tax_percentage / 100);
                    $net_amt  = $rep->apply_charge - $disc_amt + $tax_amt;
                ?>
                <tr>
                    <td><?php echo $row; ?></td>
                    <td>
                        <strong><?php echo html_escape($rep->test_name); ?></strong>
                        <div class="text-muted small">(<?php echo html_escape($rep->short_name); ?>)</div>
                    </td>
                    <td>
                        <?php echo composeStaffNameByString($rep->collection_specialist_staff_name, $rep->collection_specialist_staff_surname, $rep->collection_specialist_staff_employee_id); ?>
                        <div class="text-muted small"><?php echo html_escape($rep->radiology_center); ?></div>
                        <?php if ($rep->collection_date) { echo '<div class="text-muted small">' . $this->customlib->YYYYMMDDTodateFormat($rep->collection_date) . '</div>'; } ?>
                    </td>
                    <td><?php if ($rep->reporting_date) { echo $this->customlib->YYYYMMDDTodateFormat($rep->reporting_date); } ?></td>
                    <td>
                        <?php echo composeStaffNameByString($rep->approved_by_staff_name, $rep->approved_by_staff_surname, $rep->approved_by_staff_employee_id); ?>
                        <?php if ($rep->parameter_update) { echo '<div class="text-muted small">' . $this->customlib->YYYYMMDDTodateFormat($rep->parameter_update) . '</div>'; } ?>
                    </td>
                    <td class="text-end"><?php echo $currency_symbol . number_format($tax_amt, 2) . ' (' . $rep->tax_percentage . '%)'; ?></td>
                    <td class="text-end fw-semibold"><?php echo $currency_symbol . amountFormat($net_amt); ?></td>
                    <td class="text-center white-space-nowrap">
                        <a href="javascript:void(0)" class="btn btn-sm btn-light print_report"
                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"
                           data-record-id="<?php echo $rep->id; ?>">
                            <i class="fa fa-print"></i>
                        </a>
                        <?php if (!empty($rep->radiology_report)) { ?>
                        <a class="btn btn-sm btn-light"
                           href="<?php echo site_url('patient/dashboard/downloadRadiologyReport/' . $rep->id); ?>"
                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                            <i class="fa fa-download"></i>
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php $row++; } ?>
            </tbody>
        </table>
    </div>
</div>

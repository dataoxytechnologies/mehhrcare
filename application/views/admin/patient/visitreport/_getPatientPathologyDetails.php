<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$total_due       = !empty($result->net_amount) ? ($result->net_amount - $result->total_deposit) : 0;
$denominator     = $result->total - $result->discount;
$tax_percentage  = ($denominator != 0) ? amountFormat(($result->tax * 100) / $denominator) : 0;
$due_class       = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<div class="d-flex gap-2 mb-3 flex-wrap">

    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
            </div>
            <div class="sh-info-grid">

                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $bill_prefix . $result->id; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('patient'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $result->patient_name . ' (' . $result->patient_id . ')'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                        <div class="sh-info-value"><?php echo $result->case_reference_id ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                        <div class="sh-info-value"><?php echo $result->blood_group_name ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('age'); ?></div>
                        <div class="sh-info-value"><?php echo $this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('gender'); ?></div>
                        <div class="sh-info-value"><?php echo $result->gender ? $this->lang->line(strtolower($result->gender)) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></div>
                        <div class="sh-info-value"><?php echo $result->mobileno ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('email'); ?></div>
                        <div class="sh-info-value"><?php echo $result->email ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('doctor_name'); ?></div>
                        <div class="sh-info-value"><?php echo $result->doctor_name ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('generated_by'); ?></div>
                        <div class="sh-info-value"><?php echo composeStaffNameByString($result->name, $result->surname, $result->employee_id) ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('address'); ?></div>
                        <div class="sh-info-value"><?php echo $result->address ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                        <div class="sh-info-value"><?php echo $result->note ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></div>
                        <div class="sh-info-value"><?php echo $result->organisation_name ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                        <div class="sh-info-value"><?php echo $result->insurance_id ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                        <div class="sh-info-value"><?php echo $result->insurance_validity ? $this->customlib->YYYYMMDDTodateFormat($result->insurance_validity) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item"></div>
                </div>

                <?php if (!empty($fields)) { ?>
                <div class="row g-0 sh-row-divider">
                    <?php foreach ($fields as $fk => $fv) {
                        $val = $result->{"$fv->name"};
                        if ($fv->type == 'link') $val = "<a href='" . $val . "' target='_blank'>" . $val . "</a>"; ?>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $fv->name; ?></div>
                        <div class="sh-info-value"><?php echo $val ?: '—'; ?></div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('bill_summary'); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total'); ?></span>
                <span><?php echo !empty($result->total) ? $currency_symbol . amountFormat($result->total) : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total_discount'); ?></span>
                <span class="text-danger"><?php echo !empty($result->discount) ? '- ' . $currency_symbol . amountFormat($result->discount) . ' <small class="text-secondary">(' . $result->discount_percentage . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total_tax'); ?></span>
                <span><?php echo !empty($result->tax) ? $currency_symbol . amountFormat($result->tax) . ' <small class="text-secondary">(' . $tax_percentage . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-netamt">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo !empty($result->net_amount) ? $currency_symbol . amountFormat($result->net_amount) : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('total_deposit'); ?></span>
                <span class="text-success fw-semibold"><?php echo !empty($result->total_deposit) ? $currency_symbol . amountFormat($result->total_deposit) : '—'; ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('balance_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($total_due); ?></span>
            </div>
        </div>
    </div>

</div>

<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-flask me-1 opacity-75"></i><?php echo $this->lang->line('pathology_test'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive rounded-3 overflow-hidden border mb-0">
            <table class="table table-sm table-hover sh-tests-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center ps-3" style="width:36px">#</th>
                        <th><?php echo $this->lang->line('test_name'); ?></th>
                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                        <th><?php echo $this->lang->line('expected_date'); ?></th>
                        <th><?php echo $this->lang->line('approved_by'); ?> / <?php echo $this->lang->line('approved_date'); ?></th>
                        <th class="text-end text-nowrap"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end text-nowrap pe-3"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $row = 1;
                    foreach ($result->pathology_report as $rv) {
                        $discount_amt = ($rv->apply_charge * $result->discount_percentage) / 100;
                        $tax_amount   = ($rv->apply_charge - $discount_amt) * $rv->tax_percentage / 100;
                    ?>
                    <tr>
                        <td class="text-center text-secondary ps-3"><?php echo $row; ?></td>
                        <td>
                            <span class="fw-semibold"><?php echo $rv->test_name; ?></span>
                            <small class="text-muted d-block"><?php echo $rv->short_name; ?></small>
                        </td>
                        <td>
                            <?php if ($rv->collection_specialist_staff_employee_id != '') { ?>
                            <span class="d-block"><?php echo composeStaffNameByString($rv->collection_specialist_staff_name, $rv->collection_specialist_staff_surname, $rv->collection_specialist_staff_employee_id); ?></span>
                            <small class="text-muted d-block"><?php echo $this->lang->line('pathology'); ?>: <?php echo $rv->pathology_center; ?></small>
                            <small class="text-muted"><?php if ($rv->collection_date) echo $this->customlib->YYYYMMDDTodateFormat($rv->collection_date); ?></small>
                            <?php } else { echo '<span class="text-muted small">—</span>'; } ?>
                        </td>
                        <td class="text-nowrap"><?php echo $rv->reporting_date ? $this->customlib->YYYYMMDDTodateFormat($rv->reporting_date) : '—'; ?></td>
                        <td>
                            <?php if ($rv->approved_by_staff_employee_id != '') { ?>
                            <span class="d-block"><?php echo composeStaffNameByString($rv->approved_by_staff_name, $rv->approved_by_staff_surname, $rv->approved_by_staff_employee_id); ?></span>
                            <small class="text-muted"><?php if ($rv->parameter_update) echo $this->customlib->YYYYMMDDTodateFormat($rv->parameter_update); ?></small>
                            <?php } else { echo '<span class="text-muted small">—</span>'; } ?>
                        </td>
                        <td class="text-end text-nowrap"><?php echo $rv->tax_percentage > 0 ? $currency_symbol . amountFormat($tax_amount) . ' (' . $rv->tax_percentage . '%)' : '—'; ?></td>
                        <td class="text-end text-nowrap fw-semibold pe-3"><?php echo $currency_symbol . amountFormat($rv->apply_charge); ?></td>
                    </tr>
                    <?php $row++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$total_due        = !empty($result->net_amount) ? ($result->net_amount - $result->total_deposit) : 0;
$denominator      = $result->total - $result->discount;
$tax_percentage   = ($denominator != 0) ? amountFormat(($result->tax * 100) / $denominator) : 0;
$due_class        = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
$has_billing_perm = $this->rbac->hasPrivilege('pathology_billing_payment', 'can_add');
?>

<!-- Top: Patient info card + Billing summary card side by side -->
<div class="d-flex gap-2 mb-2 flex-wrap">

    <!-- Card 1: Patient & Bill Info -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
            </div>
            <div class="sh-info-grid">

                <!-- Row 1: Bill identity -->
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
                        <div class="sh-info-label"><?php echo $this->lang->line('prescription_no'); ?></div>
                        <div class="sh-info-value"><?php echo $prescription ?: '—'; ?></div>
                    </div>
                </div>

                <!-- Row 2: Patient profile -->
                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('age'); ?></div>
                        <div class="sh-info-value"><?php echo $this->customlib->get_patient_current_age($result->patient_id) ?: '—'; ?></div>
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

                <!-- Row 3: Clinical / staff -->
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
                        <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                        <div class="sh-info-value"><?php echo $result->blood_group_name ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                        <div class="sh-info-value"><?php echo $result->note ?: '—'; ?></div>
                    </div>
                </div>

                <!-- Row 4: TPA -->
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
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('address'); ?></div>
                        <div class="sh-info-value"><?php echo $result->address ?: '—'; ?></div>
                    </div>
                </div>

                <?php if (!empty($fields)) { ?>
                <!-- Custom Fields -->
                <div class="row g-0 sh-row-divider">
                    <?php foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $result->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href='" . $display_field . "' target='_blank'>" . $display_field . "</a>";
                        } ?>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $fields_value->name; ?></div>
                        <div class="sh-info-value"><?php echo $display_field ?: '—'; ?></div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <!-- Card 2: Billing Summary -->
    <?php if ($has_billing_perm) { ?>
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
                <span class="text-danger">
                    <?php echo !empty($result->discount)
                        ? '- ' . $currency_symbol . amountFormat($result->discount) . ' <small class="text-secondary">(' . $result->discount_percentage . '%)</small>'
                        : '—'; ?>
                </span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total_tax'); ?></span>
                <span>
                    <?php echo !empty($result->tax)
                        ? $currency_symbol . amountFormat($result->tax) . ' <small class="text-secondary">(' . $tax_percentage . '%)</small>'
                        : '—'; ?>
                </span>
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
    <?php } ?>

</div><!-- /d-flex top -->

<!-- Card 3: Pathology Tests -->
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
                        <th style="width:180px"><?php echo $this->lang->line('sample_collected'); ?></th>
                        <th class="text-nowrap"><?php echo $this->lang->line('report_date'); ?></th>
                        <th><?php echo $this->lang->line('approved_by'); ?> / <?php echo $this->lang->line('approve_date'); ?></th>
                        <th class="text-end text-nowrap"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end text-nowrap"><?php echo $this->lang->line('net_amount'); ?></th>
                        <th class="text-end pe-3" style="width:90px"><?php echo $this->lang->line('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_counter = 1;
                    foreach ($result->pathology_report as $report_key => $report_value) {
                        $discount_amt = ($report_value->apply_charge * $result->discount_percentage) / 100;
                        $tax_amount   = ($report_value->apply_charge - $discount_amt) * $report_value->tax_percentage / 100;
                        $net_row      = $report_value->apply_charge - $discount_amt + $tax_amount;
                        ?>
                    <tr>
                        <td class="text-center text-secondary ps-3"><?php echo $row_counter; ?></td>
                        <td>
                            <span class="fw-semibold"><?php echo $report_value->test_name; ?></span>
                            <span class="sh-short-name"><?php echo $report_value->short_name; ?></span>
                        </td>
                        <td>
                            <?php if ($report_value->collection_specialist_staff_employee_id != '') { ?>
                            <span class="d-block"><?php echo composeStaffNameByString($report_value->collection_specialist_staff_name, $report_value->collection_specialist_staff_surname, $report_value->collection_specialist_staff_employee_id); ?></span>
                            <small class="text-muted d-block"><?php echo $this->lang->line('pathology_center'); ?>: <?php echo $report_value->pathology_center; ?></small>
                            <small class="text-muted"><?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->collection_date); ?></small>
                            <?php } else { echo '<span class="text-muted small">—</span>'; } ?>
                        </td>
                        <td class="text-nowrap"><?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date) ?: '—'; ?></td>
                        <td>
                            <?php if ($report_value->approved_by_staff_employee_id != '') { ?>
                            <span class="d-block"><?php echo composeStaffNameByString($report_value->approved_by_staff_name, $report_value->approved_by_staff_surname, $report_value->approved_by_staff_employee_id); ?></span>
                            <small class="text-muted"><?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->parameter_update); ?></small>
                            <?php } else { echo '<span class="text-muted small">—</span>'; } ?>
                        </td>
                        <td class="text-end text-nowrap"><?php echo $report_value->tax_percentage > 0 ? $currency_symbol . amountFormat($tax_amount) . ' (' . $report_value->tax_percentage . '%)' : '—'; ?></td>
                        <td class="text-end text-nowrap fw-semibold"><?php echo $currency_symbol . amountFormat($net_row); ?></td>
                        <td class="text-end pe-3">
                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                <?php if ($is_bill) { ?>
                                    <?php if ($this->rbac->hasPrivilege('pathology_add_edit_collection_person', 'can_view')) { ?>
                                    <a href="javascript:void(0)" data-record-id="<?php echo $report_value->id; ?>" class="btn btn-sm btn-light add_collection" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_edit_collection_person'); ?>"><i class="fa fa-user-plus"></i></a>
                                    <?php } ?>
                                    <?php if ($this->rbac->hasPrivilege('pathology_add_edit_report', 'can_view') && $report_value->collection_specialist_staff_employee_id != '') { ?>
                                    <a href="javascript:void(0)" data-record-id="<?php echo $report_value->id; ?>" class="btn btn-sm btn-light add_report" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_edit_report'); ?>"><i class="fa fa-flask"></i></a>
                                    <?php } ?>
                                <?php } ?>
                                <a href="javascript:void(0)" data-record-id="<?php echo $report_value->id; ?>" class="btn btn-sm btn-light print_pathology_report" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                                <?php if ($report_value->pathology_report != '') { ?>
                                <a href="<?php echo site_url('admin/pathology/downloadReport/' . $report_value->id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php $row_counter++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div><!-- /tests card -->

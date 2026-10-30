<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$balance_amount   = $pathology_billing->total + $pathology_billing->tax - $pathology_billing->discount - $pathology_total_payment;
$due_class        = ($balance_amount <= 0) ? 'sh-status-paid' : 'sh-status-due';
$denominator      = $pathology_billing->total - $pathology_billing->discount;
$tax_percentage   = ($denominator != 0) ? amountFormat(($pathology_billing->tax * 100) / $denominator) : 0;
$has_payment_perm = $this->rbac->hasPrivilege('pathology_billing_payment', 'can_add');
?>

<?php ob_start(); ?>
<div class="sh-form-card">
    <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('transaction_history'); ?></span></div>
    <?php if (!empty($pathology_transaction)) { ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover sh-tests-table mb-0">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('transaction_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('mode'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('amount'); ?></th>
                    <th class="text-end pe-3"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pathology_transaction as $transaction) { ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                    <td class="text-nowrap"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td>
                        <?php echo $this->lang->line(strtolower($transaction->payment_mode));
                        if ($transaction->payment_mode == "Cheque") {
                            if ($transaction->cheque_no != '') { echo '<br><small class="text-secondary">' . $this->lang->line('cheque_no') . ': ' . $transaction->cheque_no . '</small>'; }
                            if ($transaction->cheque_date != '' && $transaction->cheque_date != '0000-00-00') { echo '<br><small class="text-secondary">' . $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date) . '</small>'; }
                        } ?>
                    </td>
                    <td><?php echo html_escape($transaction->note); ?></td>
                    <td class="text-center fw-semibold"><?php echo $currency_symbol . amountFormat($transaction->amount); ?></td>
                    <td class="text-end pe-3">
                        <div class="d-inline-flex gap-1">
                            <?php if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "") { ?>
                            <a href="<?php echo site_url('admin/transaction/download/' . $transaction->id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                            <?php } ?>
                            <?php if ($is_bill) { ?>
                            <a href="javascript:void(0)" data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light print_patho_receipt" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                            <?php } else { ?>
                            <a href="javascript:void(0)" data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light print_receipt" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                            <?php if ($this->rbac->hasPrivilege('pathology_billing_payment', 'can_delete') || $this->rbac->hasPrivilege('pathology_partial_payment', 'can_delete')) { ?>
                            <a href="javascript:void(0)" data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light delete_trans" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash text-danger"></i></a>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <div class="px-3 py-3">
        <div class="alert alert-info mb-0"><?php echo $this->lang->line('no_record_found'); ?></div>
    </div>
    <?php } ?>
</div>
<?php $tx_html = ob_get_clean(); ?>

<?php ob_start(); ?>
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                    <div class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') . $pathology_billing->id; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('patient'); ?></div>
                    <div class="sh-info-value highlight"><?php echo html_escape($pathology_billing->patient_name . ' (' . $pathology_billing->patient_id . ')'); ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                    <div class="sh-info-value"><?php echo $pathology_billing->case_reference_id ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('prescription_no'); ?></div>
                    <div class="sh-info-value">—</div>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('age'); ?></div>
                    <div class="sh-info-value"><?php echo $this->customlib->getPatientAge($pathology_billing->age, $pathology_billing->month, $pathology_billing->day) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('gender'); ?></div>
                    <div class="sh-info-value"><?php echo $pathology_billing->gender ? $this->lang->line(strtolower($pathology_billing->gender)) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></div>
                    <div class="sh-info-value"><?php echo $pathology_billing->mobileno ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('email'); ?></div>
                    <div class="sh-info-value"><?php echo $pathology_billing->email ?: '—'; ?></div>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('doctor_name'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($pathology_billing->doctor_name) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('generated_by'); ?></div>
                    <div class="sh-info-value"><?php echo composeStaffNameByString($pathology_billing->name, $pathology_billing->surname, $pathology_billing->employee_id) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($pathology_billing->blood_group_name) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                    <div class="sh-info-value"><?php echo !empty($pathology_billing->note) ? html_escape($pathology_billing->note) : '—'; ?></div>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></div>
                    <div class="sh-info-value"><?php echo !empty($pathology_billing->organisation_name) ? html_escape($pathology_billing->organisation_name) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                    <div class="sh-info-value"><?php echo !empty($pathology_billing->insurance_id) ? html_escape($pathology_billing->insurance_id) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                    <div class="sh-info-value"><?php echo !empty($pathology_billing->insurance_validity) ? $this->customlib->YYYYMMDDTodateFormat($pathology_billing->insurance_validity) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('address'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($pathology_billing->address) ?: '—'; ?></div>
                </div>
            </div>
        </div>
<?php $patient_info_html = ob_get_clean(); ?>

<?php ob_start(); ?>
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('bill_summary'); ?></span>
        </div>
        <div class="sh-summary-row">
            <span class="text-secondary"><?php echo $this->lang->line('total'); ?></span>
            <span><?php echo $currency_symbol . amountFormat($pathology_billing->total); ?></span>
        </div>
        <div class="sh-summary-row">
            <span class="text-secondary"><?php echo $this->lang->line('total_discount'); ?></span>
            <span class="text-danger">
                <?php if ($pathology_billing->discount > 0) {
                    echo '- ' . $currency_symbol . amountFormat($pathology_billing->discount);
                    if ($pathology_billing->discount_percentage > 0) {
                        echo ' <small class="text-secondary">(' . $pathology_billing->discount_percentage . '%)</small>';
                    }
                } else { echo '—'; } ?>
            </span>
        </div>
        <div class="sh-summary-row">
            <span class="text-secondary"><?php echo $this->lang->line('total_tax'); ?></span>
            <span>
                <?php echo ($pathology_billing->tax > 0)
                    ? $currency_symbol . amountFormat($pathology_billing->tax) . ' <small class="text-secondary">(' . $tax_percentage . '%)</small>'
                    : '—'; ?>
            </span>
        </div>
        <div class="sh-summary-netamt">
            <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
            <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat($pathology_billing->net_amount); ?></span>
        </div>
        <div class="sh-summary-row">
            <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
            <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat($pathology_billing->total_deposit); ?></span>
        </div>
        <div class="sh-due-row <?php echo $due_class; ?>">
            <span><?php echo $this->lang->line('due_amount'); ?></span>
            <span><?php echo $currency_symbol . amountFormat($balance_amount); ?></span>
        </div>
<?php $net_amount_html = ob_get_clean(); ?>

<?php if ($has_payment_perm) { ?>

<!-- RBAC TRUE: Patient Info (full width) → [Net Amount | Add Payment] -->
<div class="sh-form-card mb-3"><?php echo $patient_info_html; ?></div>

<div class="d-flex gap-2 mb-2 flex-wrap">
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden"><?php echo $net_amount_html; ?></div>
    </div>
    <div class="sh-tx-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
            </div>
            <div class="px-3 py-3">
                <form id="<?php echo $form_id; ?>" action="<?php echo site_url('admin/bill/partial_pathology_bill'); ?>" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="pathology_billing_id" value="<?php echo $pathology_billing_id; ?>">
                    <input type="hidden" name="case_reference_id" value="<?php echo $pathology_billing->case_reference_id; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $pathology_billing->patient_id; ?>">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime">
                            <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></label>
                            <input type="text" name="amount" id="amount" class="form-control form-control-sm" value="<?php echo amountFormat($balance_amount > 0 ? $balance_amount : 0); ?>">
                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <select class="form-control form-select-sm payment_mode" name="payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                <option value="<?php echo $key; ?>" <?php echo ($key == 'cash') ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" id="note" class="form-control form-control-sm" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="row g-2 cheque_div mt-1 d-none" >
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('cheque_no'); ?><small class="req"> *</small></label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                            <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('cheque_date'); ?><small class="req"> *</small></label>
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                            <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-sm btn-info"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>

<!-- RBAC FALSE: [Patient Info (flex-1)] | [Net Amount (234px)] side by side -->
<div class="d-flex gap-2 mb-2 flex-wrap">
    <div class="sh-flex-col">
        <div class="sh-form-card h-100"><?php echo $patient_info_html; ?></div>
    </div>
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden"><?php echo $net_amount_html; ?></div>
    </div>
</div>

<?php } ?>

<?php echo $tx_html; ?>

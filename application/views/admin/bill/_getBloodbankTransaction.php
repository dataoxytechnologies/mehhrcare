<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$discount_amt    = !empty($blood_issue_detail) ? calculatePercent($blood_issue_detail['amount'], $blood_issue_detail['discount_percentage']) : 0;
$taxable         = !empty($blood_issue_detail) ? $blood_issue_detail['amount'] - $discount_amt : 0;
$tax_amt         = !empty($blood_issue_detail) ? calculatePercent($taxable, $blood_issue_detail['tax_percentage']) : 0;
$total_due       = !empty($blood_issue_detail) ? amountFormat($blood_issue_detail['net_amount'] - $blood_issue_detail['total_deposit']) : 0;
$due_class        = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
$has_payment_perm = $this->rbac->hasPrivilege('blood_bank_billing_payment', 'can_add');
?>

<?php ob_start(); ?>
<div class="sh-form-card">
    <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('transaction_history'); ?></span></div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-hover sh-tests-table mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('mode'); ?></th>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transaction)) {
                        foreach ($transaction as $transaction_key => $transaction) { ?>
                    <tr>
                        <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
                        <td><?php echo $this->lang->line(strtolower($transaction->payment_mode));
                            if ($transaction->payment_mode == 'Cheque') {
                                if ($transaction->cheque_no != '') echo '<br>' . $this->lang->line('cheque_no') . ': ' . html_escape($transaction->cheque_no);
                                if (!is_null($transaction->cheque_date)) echo '<br>' . $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                            } ?></td>
                        <td><?php echo html_escape($transaction->note); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . $transaction->amount; ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <?php if ($transaction->payment_mode == 'Cheque' && $transaction->attachment != '') { ?>
                                <a href="<?php echo site_url('admin/transaction/download_cheque_attachment/' . $transaction->id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                <?php } ?>
                                <a href="javascript:void(0)" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light print_receipt" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                                <?php if ($this->rbac->hasPrivilege('blood_bank_partial_payment', 'can_delete')) { ?>
                                <a href="javascript:void(0)" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light delete_trans" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash text-danger"></i></a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr><td colspan="6" class="text-center"><?php echo $this->lang->line('no_record_found'); ?></td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $tx_html = ob_get_clean(); ?>

<?php if (!empty($blood_issue_detail)) { ?>

<?php ob_start(); ?>
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('blood_issue_details'); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></span>
                    <span class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('blood_bank_billing') . $billing_id; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('received_to'); ?></span>
                    <span class="sh-info-value highlight"><?php echo composePatientName($blood_issue_detail['patient_name'], $blood_issue_detail['patient_id']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('bags'); ?></span>
                    <span class="sh-info-value"><?php echo $blood_issue_detail['bag_no']; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('issue_date'); ?></span>
                    <span class="sh-info-value"><?php echo $blood_issue_detail['date_of_issue'] ? $this->customlib->YYYYMMDDTodateFormat($blood_issue_detail['date_of_issue']) : '—'; ?></span>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                    <span class="sh-info-value"><?php echo $blood_issue_detail['blood_group']; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($blood_issue_detail['reference']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('donor_name'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($blood_issue_detail['donor_name']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('technician'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($blood_issue_detail['technician']); ?></span>
                </div>
            </div>
        </div>
<?php $patient_info_html = ob_get_clean(); ?>

<?php ob_start(); ?>
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
        </div>
        <div class="sh-summary-row border-bottom">
            <span><?php echo $this->lang->line('amount'); ?></span>
            <span><?php echo $currency_symbol . $blood_issue_detail['amount']; ?></span>
        </div>
        <div class="sh-summary-row border-bottom">
            <span><?php echo $this->lang->line('discount'); ?></span>
            <span><?php echo $currency_symbol . $discount_amt; ?> (<?php echo $blood_issue_detail['discount_percentage']; ?>%)</span>
        </div>
        <div class="sh-summary-row border-bottom">
            <span><?php echo $this->lang->line('tax'); ?></span>
            <span><?php echo $currency_symbol . $tax_amt; ?> (<?php echo $blood_issue_detail['tax_percentage']; ?>%)</span>
        </div>
        <div class="sh-summary-netamt">
            <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
            <span class="fw-bold fs-6"><?php echo $currency_symbol . $blood_issue_detail['net_amount']; ?></span>
        </div>
        <div class="sh-summary-row border-bottom">
            <span><?php echo $this->lang->line('paid_amount'); ?></span>
            <span><?php echo $currency_symbol . $blood_issue_detail['total_deposit']; ?></span>
        </div>
        <div class="sh-due-row <?php echo $due_class; ?>">
            <span><?php echo $this->lang->line('balance_amount'); ?></span>
            <span><?php echo $currency_symbol . $total_due; ?></span>
        </div>
<?php $net_amount_html = ob_get_clean(); ?>

<?php if ($has_payment_perm) { ?>

<!-- RBAC TRUE: Patient Info (full width) → [Net Amount | Add Payment] -->
<div class="sh-form-card mb-3"><?php echo $patient_info_html; ?></div>

<div class="d-flex gap-3 mb-2 flex-wrap">
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden"><?php echo $net_amount_html; ?></div>
    </div>
    <div class="sh-tx-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
            </div>
            <div class="px-2 py-3">
                <form id="add_partial_payment" action="<?php echo site_url('admin/bloodbank/partialbill'); ?>" accept-charset="utf-8" method="post">
                    <input type="hidden" name="billing_id" value="<?php echo $billing_id; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $blood_issue_detail['patient_id']; ?>">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime">
                            <span class="text-danger small"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                            <input type="text" name="amount" id="amount" value="<?php echo $total_due; ?>" class="form-control form-control-sm">
                            <span class="text-danger small"><?php echo form_error('amount'); ?></span>
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if ($key == 'cash') echo 'selected'; ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" id="note" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="cheque_div mt-2 d-none" >
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                                <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                <span class="text-danger small"><?php echo form_error('cheque_no'); ?></span>
                            </div>
                            <div class="col-6">
                                <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                                <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date" readonly>
                                <span class="text-danger small"><?php echo form_error('cheque_date'); ?></span>
                            </div>
                            <div class="col-12">
                                <label class="form-label small"><?php echo $this->lang->line('attach_document'); ?></label>
                                <input type="file" class="form-control form-control-sm filestyle" name="document">
                                <span class="text-danger small"><?php echo form_error('document'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-sm btn-info">
                            <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php } else { ?>

<!-- RBAC FALSE: [Patient Info (flex-1)] | [Net Amount (234px)] side by side -->
<div class="d-flex gap-3 mb-2 flex-wrap">
    <div class="sh-flex-col">
        <div class="sh-form-card h-100"><?php echo $patient_info_html; ?></div>
    </div>
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden"><?php echo $net_amount_html; ?></div>
    </div>
</div>

<?php } ?>

<?php } ?>

<?php echo $tx_html; ?>

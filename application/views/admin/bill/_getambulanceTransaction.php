<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$discount_amt    = !empty($ambullance_call_detail) ? calculatePercent($ambullance_call_detail['amount'], $ambullance_call_detail['discount_percentage']) : 0;
$taxable         = !empty($ambullance_call_detail) ? $ambullance_call_detail['amount'] - $discount_amt : 0;
$tax_amt         = !empty($ambullance_call_detail) ? calculatePercent($taxable, $ambullance_call_detail['tax_percentage']) : 0;
$total_due       = !empty($ambullance_call_detail) ? amountFormat($ambullance_call_detail['net_amount'] - $ambullance_call_detail['paid_amount']) : 0;
$due_class        = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
$has_payment_perm = $this->rbac->hasPrivilege('ambulance_billing_payment', 'can_add');
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
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transaction)) {
                        foreach ($transaction as $transaction_key => $transaction) { ?>
                    <tr>
                        <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $time_format); ?></td>
                        <td><?php if ($transaction->payment_mode) { echo $this->lang->line(strtolower($transaction->payment_mode)); }
                            if ($transaction->payment_mode == 'Cheque') {
                                if ($transaction->cheque_no != '') echo '<br>' . $this->lang->line('cheque_no') . ': ' . html_escape($transaction->cheque_no);
                                if ($transaction->cheque_date != '' && $transaction->cheque_date != '0000-00-00') echo '<br>' . $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                            } ?></td>
                        <td><?php echo html_escape($transaction->note); ?></td>
                        <td class="text-end"><?php echo $transaction->amount; ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <?php if ($transaction->payment_mode == 'Cheque' && $transaction->attachment != '') { ?>
                                <a href="<?php echo site_url('admin/transaction/download/' . $transaction->id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                <?php } ?>
                                <a href="javascript:void(0)" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id; ?>" class="btn btn-sm btn-light print_ambulance_receipt" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
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

<?php if (!empty($ambullance_call_detail)) { ?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('ambulance_bill_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></span>
                <span class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $billing_id; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('received_to'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($ambullance_call_detail['patient'], $ambullance_call_detail['patient_id']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('vehicle_no'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($ambullance_call_detail['vehicle_no']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php echo $ambullance_call_detail['date'] ? $this->customlib->YYYYMMDDTodateFormat($ambullance_call_detail['date']) : '—'; ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('vehicle_model'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($ambullance_call_detail['vehicle_model']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('driver_name'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($ambullance_call_detail['driver']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('driver_contact'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($ambullance_call_detail['driver_contact']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_address'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($ambullance_call_detail['address']); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-3 mb-2 flex-wrap">
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('amount'); ?></span>
                <span><?php echo $currency_symbol . $ambullance_call_detail['amount']; ?></span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('discount'); ?></span>
                <span><?php echo $currency_symbol . $discount_amt; ?> (<?php echo $ambullance_call_detail['discount_percentage']; ?>%)</span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('tax'); ?></span>
                <span><?php echo $currency_symbol . $tax_amt; ?> (<?php echo $ambullance_call_detail['tax_percentage']; ?>%)</span>
            </div>
            <div class="sh-summary-netamt">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . $ambullance_call_detail['net_amount']; ?></span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('paid_amount'); ?></span>
                <span><?php echo $currency_symbol . $ambullance_call_detail['paid_amount']; ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('balance_amount'); ?></span>
                <span><?php echo $currency_symbol . $total_due; ?></span>
            </div>
        </div>
    </div>
    <?php if ($has_payment_perm) { ?>
    <div class="sh-tx-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
            </div>
            <div class="px-2 py-3">
                <form id="add_partial_payment_ambulance" action="<?php echo site_url('admin/vehicle/partialbill'); ?>" accept-charset="utf-8" method="post">
                    <input type="hidden" name="billing_id" value="<?php echo $billing_id; ?>">
                    <input type="hidden" name="net_amount" id="net_amount" value="<?php echo $balance_amount; ?>">
                    <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
                    <input type="hidden" name="patient_referance_case_id" id="patient_referance_case_id" value="<?php echo $patient_referance_case_id; ?>">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime">
                            <span class="text-danger small"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                            <input type="text" name="payment_amount" id="amount" value="<?php echo $balance_amount; ?>" class="form-control form-control-sm">
                            <span class="text-danger small"><?php echo form_error('payment_amount'); ?></span>
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
                                <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
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
    <?php } else { ?>
    <div class="sh-tx-col"><?php echo $tx_html; ?></div>
    <?php } ?>
</div>
<?php } ?>

<?php if (empty($ambullance_call_detail) || $has_payment_perm) { echo $tx_html; } ?>

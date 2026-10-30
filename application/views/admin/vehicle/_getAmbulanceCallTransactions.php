<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$detail           = $ambullance_call_detail;
$discount_amount  = 0; $tax_amount = 0; $calc_balance = 0;
if (!empty($detail)) {
    $discount_amount = calculatePercent($detail['amount'], $detail['discount_percentage']);
    $tax_amount      = calculatePercent($detail['amount'] - $discount_amount, $detail['tax_percentage']);
    $calc_balance    = $detail['net_amount'] - $detail['paid_amount'];
}
$due_class = ($calc_balance <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<!-- Card 1: Patient & Bill Details -->
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
    </div>
    <?php if (!empty($detail)) { ?>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                <div class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $billing_id; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('patient'); ?></div>
                <div class="sh-info-value highlight"><?php echo html_escape(composePatientName($detail['patient'], $detail['patient_id'])); ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['case_reference_id']) ? html_escape($detail['case_reference_id']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['mobileno']) ? html_escape($detail['mobileno']) : '—'; ?></div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('vehicle_model'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['vehicle_model']) ? html_escape($detail['vehicle_model']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('vehicle_no'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['vehicle_no']) ? html_escape($detail['vehicle_no']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('driver_name'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['driver_name']) ? html_escape($detail['driver_name']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('driver_contact'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['driver_contact']) ? html_escape($detail['driver_contact']) : '—'; ?></div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($detail['date']); ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('charge_name'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['charge_name']) ? html_escape($detail['charge_name']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('generated_by'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['staff_name']) ? composeStaffNameByString($detail['staff_name'], $detail['staff_surname'], $detail['staff_employee_id']) : '—'; ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('patient_address'); ?></div>
                <div class="sh-info-value"><?php echo !empty($detail['address']) ? html_escape($detail['address']) : '—'; ?></div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<!-- Cards 2 & 3: Billing Summary + Payment Form -->
<?php
$has_payment_perm = $this->rbac->hasPrivilege('ambulance_partial_payment', 'can_add');
ob_start(); ?>
<!-- Card 4: Transaction History -->
<div class="sh-form-card">
    <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('transaction_history'); ?></span></div>
    <?php if (!empty($transaction)) { ?>
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
                <?php foreach ($transaction as $trans) { ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $trans->id; ?></td>
                    <td class="text-nowrap"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($trans->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td>
                        <?php if ($trans->payment_mode) {
                            echo $this->lang->line(strtolower($trans->payment_mode));
                            if ($trans->payment_mode == "Cheque") {
                                if ($trans->cheque_no != '') { echo '<br><small class="text-secondary">' . $this->lang->line('cheque_no') . ': ' . html_escape($trans->cheque_no) . '</small>'; }
                                if ($trans->cheque_date != '' && $trans->cheque_date != '0000-00-00') { echo '<br><small class="text-secondary">' . $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($trans->cheque_date) . '</small>'; }
                            }
                        } ?>
                    </td>
                    <td><?php echo html_escape($trans->note); ?></td>
                    <td class="text-center fw-semibold"><?php echo $currency_symbol . amountFormat($trans->amount); ?></td>
                    <td class="text-end pe-3">
                        <div class="d-inline-flex gap-1">
                            <?php if ($trans->payment_mode == "Cheque" && $trans->attachment != "") { ?>
                            <a href="<?php echo site_url('admin/transaction/download/' . $trans->id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                            <?php } ?>
                            <a href="javascript:void(0)" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $trans->id; ?>" class="btn btn-sm btn-light print_receipt" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>
                            <?php if ($this->rbac->hasPrivilege('ambulance_partial_payment', 'can_delete')) { ?>
                            <a href="javascript:void(0)" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $trans->id; ?>" data-billing-id="<?php echo $billing_id; ?>" data-patient-id="<?php echo $patient_id; ?>" data-amount="<?php echo $trans->amount; ?>" data-case-id="<?php echo $case_id; ?>" data-balance="<?php echo $calc_balance; ?>" class="btn btn-sm btn-light delete_trans" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
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

<div class="d-flex gap-2 mb-2 flex-wrap">

    <!-- Card 2: Billing Summary -->
    <?php if (!empty($detail)) { ?>
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('bill_summary'); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($detail['amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total_discount'); ?></span>
                <span class="text-danger">
                    <?php if ($discount_amount > 0) {
                        echo '- ' . $currency_symbol . amountFormat($discount_amount);
                        if ($detail['discount_percentage'] > 0) {
                            echo ' <small class="text-secondary">(' . $detail['discount_percentage'] . '%)</small>';
                        }
                    } else { echo '—'; } ?>
                </span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('total_tax'); ?></span>
                <span>
                    <?php echo ($tax_amount > 0)
                        ? $currency_symbol . amountFormat($tax_amount) . ' <small class="text-secondary">(' . $detail['tax_percentage'] . '%)</small>'
                        : '—'; ?>
                </span>
            </div>
            <div class="sh-summary-netamt">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat($detail['net_amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat($detail['paid_amount']); ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('due_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($calc_balance); ?></span>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Card 3: Payment Form -->
    <?php if ($has_payment_perm) { ?>
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span>
            </div>
            <div class="px-3 py-3">
                <form id="add_partial_payment" action="<?php echo site_url('admin/vehicle/partialbill'); ?>" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="billing_id" value="<?php echo $billing_id; ?>">
                    <input type="hidden" name="case_id" value="<?php echo $case_id; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                    <input type="hidden" name="net_amount" value="<?php echo $calc_balance; ?>">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime">
                            <span class="text-danger"><?php echo form_error('payment_date'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></label>
                            <input type="text" name="payment_amount" id="amount" class="form-control form-control-sm" value="<?php echo amountFormat(max(0, $calc_balance)); ?>">
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
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('cheque_date'); ?><small class="req"> *</small></label>
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold mb-1"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-sm btn-info"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="sh-flex-col"><?php echo $tx_html; ?></div>
    <?php } ?>

</div>

<?php if ($has_payment_perm) { echo $tx_html; } ?>

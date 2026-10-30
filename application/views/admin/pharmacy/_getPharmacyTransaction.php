<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if ($view_delete) {
    $form_id       = "add_partial_payment";
    $print_receipt = "print_receipt";
} else {
    $form_id       = "add_bill_partial_payment";
    $print_receipt = "print_pharmacyBillReceipt";
}
$total_due_val    = ($pharmacy_bill_detail['net_amount'] + $pharmacy_bill_detail['refund_amount']) - $pharmacy_bill_detail['paid_amount'];
$due_class        = ($total_due_val <= 0) ? 'sh-status-paid' : 'sh-status-due';
$total_due        = amountFormat($total_due_val);
$has_payment_perm = $this->rbac->hasPrivilege('pharmacy_billing_payment', 'can_add') || $this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_add');
?>

<?php ob_start(); ?>
<div class="sh-form-card">
    <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('transaction_history'); ?></span></div>
    <?php if (!empty($pharmacy_transaction)) { ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover sh-tests-table mb-0">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('transaction_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('payment_type'); ?></th>
                    <th><?php echo $this->lang->line('mode'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pharmacy_transaction as $transaction_key => $transaction) { ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td><?php echo $this->lang->line($transaction->type); ?></td>
                    <td><?php
                        echo $this->lang->line(strtolower($transaction->payment_mode));
                        if ($transaction->payment_mode == "Cheque") {
                            if ($transaction->cheque_no != '') {
                                echo "<br>" . $this->lang->line('cheque_no') . ": " . $transaction->cheque_no;
                            }
                            if ($transaction->cheque_date != '' && $transaction->cheque_date != '0000-00-00') {
                                echo "<br>" . $this->lang->line('cheque_date') . ": " . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                            }
                        }
                    ?></td>
                    <td><?php echo $transaction->note; ?></td>
                    <td class="text-end"><?php echo $transaction->amount; ?></td>
                    <td class="text-end">
                        <?php if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "") { ?>
                            <a href='<?php echo site_url('admin/transaction/download/' . $transaction->id); ?>' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                        <?php } ?>
                        <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id; ?>" class='btn btn-sm btn-light <?php echo $print_receipt; ?>' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
                        <?php if ($view_delete) {
                            if ($this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_delete')) { ?>
                                <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id; ?>" class='btn btn-sm btn-light delete_trans' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
                        <?php } } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <div class="alert alert-info m-2"><?php echo $this->lang->line('no_record_found'); ?></div>
    <?php } ?>
</div>
<?php $tx_html = ob_get_clean(); ?>

<?php ob_start(); ?>
        <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('bill_details'); ?></span></div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                    <div class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $pharmacy_bill_detail['id']; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('patient'); ?></div>
                    <div class="sh-info-value highlight"><?php echo composePatientName($pharmacy_bill_detail['patient_name'], $pharmacy_bill_detail['patient_id']); ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                    <div class="sh-info-value"><?php echo $pharmacy_bill_detail['case_reference_id']; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('generated_by'); ?></div>
                    <div class="sh-info-value"><?php echo composeStaffNameByString($pharmacy_bill_detail['name'], $pharmacy_bill_detail['surname'], $pharmacy_bill_detail['employee_id']); ?></div>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                    <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($pharmacy_bill_detail['date']); ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></div>
                    <div class="sh-info-value"><?php echo $pharmacy_bill_detail['mobileno']; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></div>
                    <div class="sh-info-value"><?php echo $pharmacy_bill_detail['organisation_name']; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                    <div class="sh-info-value"><?php echo $pharmacy_bill_detail['insurance_id']; ?></div>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                    <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($pharmacy_bill_detail['insurance_validity']); ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item"></div>
                <div class="col-6 col-md-3 sh-info-item"></div>
                <div class="col-6 col-md-3 sh-info-item"></div>
            </div>
        </div>
<?php $patient_info_html = ob_get_clean(); ?>

<?php ob_start(); ?>
        <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?></span></div>
        <div class="sh-summary-row">
            <span><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php echo $pharmacy_bill_detail['total']; ?></span>
        </div>
        <div class="sh-summary-row">
            <span><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php echo $pharmacy_bill_detail['discount'] . " (" . $pharmacy_bill_detail['discount_percentage'] . "%)"; ?></span>
        </div>
        <div class="sh-summary-row">
            <span><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php
                $tax_base = (float)$pharmacy_bill_detail['total'] - (float)$pharmacy_bill_detail['discount'];
                echo $pharmacy_bill_detail['tax'] . " (" . amountFormat($tax_base != 0 ? ((float)$pharmacy_bill_detail['tax'] * 100) / $tax_base : 0, 2) . "%)";
            ?></span>
        </div>
        <div class="sh-summary-netamt">
            <span class="fw-bold"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
            <span class="fw-bold"><?php echo $pharmacy_bill_detail['net_amount']; ?></span>
        </div>
        <div class="sh-summary-row">
            <span><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php echo $pharmacy_bill_detail['paid_amount']; ?></span>
        </div>
        <div class="sh-summary-row">
            <span><?php echo $this->lang->line('refund_amount') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php echo $pharmacy_bill_detail['refund_amount']; ?></span>
        </div>
        <div class="sh-due-row <?php echo $due_class; ?>">
            <span><?php echo $this->lang->line('due_amount') . " (" . $currency_symbol . ")"; ?></span>
            <span><?php echo $total_due; ?></span>
        </div>
<?php $net_amount_html = ob_get_clean(); ?>

<?php if ($has_payment_perm) { ?>

<!-- RBAC TRUE: Patient Info (full width) → [Net Amount | Add Payment] -->
<div class="sh-form-card mb-2"><?php echo $patient_info_html; ?></div>

<div class="d-flex gap-2 mb-2 flex-wrap">
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden"><?php echo $net_amount_html; ?></div>
    </div>
    <div class="sh-tx-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header"><span class="sh-card-header-title"><?php echo $this->lang->line('add_payment'); ?></span></div>
            <div class="px-2 py-3">
                <form id="<?php echo $form_id; ?>" action="<?php echo site_url('admin/pharmacy/partialbill') ?>" accept-charset="utf-8" method="post">
                    <input type="hidden" name="pharmacy_bill_basic_id" value="<?php echo $pharmacy_bill_basic_id; ?>">
                    <input type="hidden" name="patient_id" value="<?= $pharmacy_bill_detail['patient_id']; ?>">
                    <input type="hidden" name="case_reference_id" value="<?= $pharmacy_bill_detail['case_reference_id']; ?>">
                    <input type="hidden" name="refund_amount" value="<?= $pharmacy_bill_detail['refund_amount']; ?>">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                            <input type="text" name="payment_date" id="date" class="form-control form-control-sm datetime">
                            <span class="text-danger small"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                            <input type="text" name="amount" id="amount" class="form-control form-control-sm" value="<?php echo $balance_amount; ?>">
                            <span class="text-danger small"><?php echo form_error('amount'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                            <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                    <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger small"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" id="note" class="form-control form-control-sm" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="row g-2 cheque_div mt-1 d-none" >
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                            <span class="text-danger small"><?php echo form_error('cheque_no'); ?></span>
                        </div>
                        <div class="col-6">
                            <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                            <span class="text-danger small"><?php echo form_error('cheque_date'); ?></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label small"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                            <span class="text-danger small"><?php echo form_error('document'); ?></span>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-sm btn-info px-4"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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

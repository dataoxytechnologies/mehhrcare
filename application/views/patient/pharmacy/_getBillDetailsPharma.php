<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$denominator     = $result['total'] - $result['discount'];
$tax_pct         = ($denominator != 0) ? ($result['tax'] * 100) / $denominator : 0;
$due             = ($result['net_amount'] + $result['refund_amount']) - $result['paid_amount'];
$due_class       = ($due <= 0) ? 'sh-status-paid' : 'sh-status-due';
$bill_prefix     = $this->customlib->getPatientSessionPrefixByType('pharmacy_billing');

if ($print) {
    include(APPPATH . 'views/admin/shared/_print_css.php');
}
?>

<div class="fixed-print-header">
<?php if (!empty($print_details[0]['print_header'])) { ?>
    <div class="pprinta4">
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>" class="img-fluid sh-avatar-cover" >
    </div>
<?php } ?>
</div>
<table class="table-print-full" width="100%">
    <thead><tr><td><div class="header-space">&nbsp;</div></td></tr></thead>
    <tbody><tr><td>

<?php if ($print) { /* ────── PRINT LAYOUT (matches pathology/radiology print pattern) ────── */ ?>
    <div class="content-body">
    <div class="print-area" id="html-2-pdfwrapper">

        <div class="sh-print-title"><?php echo $this->lang->line('pharmacy_bill'); ?></div>

        <div class="sh-print-info-block">
            <div class="sh-flex-gap18">
                <table class="sh-print-info-table w-50" >
                    <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                    <tr>
                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                        <td><?php echo $bill_prefix . $result['id']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('name'); ?></th>
                        <td><?php echo composePatientName($result['patient_name'], $result['patient_unique_id']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('phone'); ?></th>
                        <td><?php echo ($result['mobileno'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('case_id'); ?></th>
                        <td><?php echo ($result['case_reference_id'] ?: '-'); ?></td>
                    </tr>
                    <?php if (!empty($result['organisation_name'])) { ?>
                    <tr>
                        <th><?php echo $this->lang->line('tpa'); ?></th>
                        <td><?php echo html_escape($result['organisation_name']); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($result['insurance_id'])) { ?>
                    <tr>
                        <th><?php echo $this->lang->line('tpa_id'); ?></th>
                        <td><?php echo html_escape($result['insurance_id']); ?></td>
                    </tr>
                    <?php } ?>
                </table>

                <table class="sh-print-info-table w-50" >
                    <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                    <tr>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('doctor'); ?></th>
                        <td><?php echo ($result['doctor_name'] ?: '-'); ?></td>
                    </tr>
                    <?php if (!empty($prescription)) { ?>
                    <tr>
                        <th><?php echo $this->lang->line('prescription'); ?></th>
                        <td><?php echo html_escape($prescription); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($result['insurance_validity'])) { ?>
                    <tr>
                        <th><?php echo $this->lang->line('tpa_validity'); ?></th>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($result['note'])) { ?>
                    <tr>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <td><?php echo html_escape($result['note']); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($fields)) {
                        foreach ($fields as $fields_value) { ?>
                    <tr>
                        <th><?php echo html_escape($fields_value->name); ?></th>
                        <td><?php echo html_escape($result[$fields_value->name]); ?></td>
                    </tr>
                    <?php } } ?>
                </table>
            </div>
        </div>

        <table class="sh-print-table">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th><?php echo $this->lang->line('medicine_name'); ?></th>
                    <th><?php echo $this->lang->line('batch_no'); ?></th>
                    <th><?php echo $this->lang->line('expiry_date'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('quantity'); ?></th>
                    <th class="sh-text-right"><?php echo $this->lang->line('tax'); ?></th>
                    <th class="sh-text-right"><?php echo $this->lang->line('discount'); ?></th>
                    <th class="sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $row_counter = 1;
                foreach ($detail as $bill) {
                    $total_sale_amount = $bill['sale_price'] * $bill['quantity'];
                    $discount_amt = (($total_sale_amount * $bill['discount']) / 100);
                ?>
                <tr>
                    <td><?php echo $row_counter; ?></td>
                    <td>
                        <?php echo html_escape($bill['medicine_name']); ?>
                        <?php if (!empty($bill['medicine_category'])) { ?>
                        <small><?php echo html_escape($bill['medicine_category']); ?> · <?php echo html_escape($bill['unit_name']); ?></small>
                        <?php } ?>
                    </td>
                    <td><?php echo html_escape($bill['batch_no']); ?></td>
                    <td><?php echo $this->customlib->getmedicine_expire_month($bill['expiry']); ?></td>
                    <td class="text-center"><?php echo $bill['quantity']; ?></td>
                    <td class="sh-text-right"><?php echo $bill['tax']; ?>%</td>
                    <td class="sh-text-right"><?php echo $bill['discount']; ?>%</td>
                    <td class="sh-text-right"><?php echo amountFormat($total_sale_amount - $discount_amt); ?></td>
                </tr>
                <?php $row_counter++; } ?>
            </tbody>
            <tfoot>
                <tr class="sh-row-first">
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['total']); ?></td>
                </tr>
                <?php if (!empty($result['discount'])) { ?>
                <tr>
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('discount'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['discount']) . ' (' . $result['discount_percentage'] . '%)'; ?></td>
                </tr>
                <?php } ?>
                <?php if (!empty($result['tax'])) { ?>
                <tr>
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('tax'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['tax']) . ' (' . amountFormat($tax_pct) . '%)'; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('net_amount'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['net_amount']); ?></td>
                </tr>
                <tr>
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('paid_amount'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['paid_amount']); ?></td>
                </tr>
                <?php if (!empty($result['refund_amount'])) { ?>
                <tr>
                    <td colspan="7" class="sh-text-right"><?php echo $this->lang->line('refund_amount'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat((float)$result['refund_amount']); ?></td>
                </tr>
                <?php } ?>
                <tr class="sh-row-total">
                    <td colspan="7"><?php echo $this->lang->line('due_amount'); ?></td>
                    <td><?php echo $currency_symbol . amountFormat($due); ?></td>
                </tr>
            </tfoot>
        </table>

    </div>
    </div>

<?php } else { /* ────── MODAL LAYOUT (sh-form-card pattern) ────── */ ?>
    <div class="content-body">
    <div id="html-2-pdfwrapper">

        <div class="d-flex gap-2 flex-wrap m-2">
            <!-- Bill Info Card -->
            <div class="sh-flex-col">
                <div class="sh-form-card h-100">
                    <div class="sh-card-header">
                        <span class="sh-card-header-title"><i class="fa fa-file-text me-1"></i><?php echo $this->lang->line('bill_details'); ?></span>
                        <span class="ms-auto text-muted small"><?php echo $this->lang->line('bill_no'); ?>: <strong><?php echo $bill_prefix . $result['id']; ?></strong></span>
                        <span class="text-muted small ms-3"><?php echo $this->lang->line('date'); ?>: <strong><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></strong></span>
                    </div>
                    <div class="sh-info-grid">
                        <div class="row g-0">
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('name'); ?></span>
                                <span class="sh-info-value highlight"><?php echo composePatientName($result['patient_name'], $result['patient_unique_id']); ?></span>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                                <span class="sh-info-value"><?php echo !empty($result['mobileno']) ? html_escape($result['mobileno']) : '—'; ?></span>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('doctor'); ?></span>
                                <span class="sh-info-value"><?php echo !empty($result['doctor_name']) ? html_escape($result['doctor_name']) : '—'; ?></span>
                            </div>
                        </div>
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                                <span class="sh-info-value"><?php echo !empty($result['case_reference_id']) ? html_escape($result['case_reference_id']) : '—'; ?></span>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('prescription'); ?></span>
                                <span class="sh-info-value"><?php echo !empty($prescription) ? html_escape($prescription) : '—'; ?></span>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                                <span class="sh-info-value"><?php echo !empty($result['organisation_name']) ? html_escape($result['organisation_name']) : '—'; ?></span>
                            </div>
                        </div>
                        <?php if (!empty($result['insurance_id'])) { ?>
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                                <span class="sh-info-value"><?php echo html_escape($result['insurance_id']); ?></span>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                                <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($result['note']) || !empty($fields)) { ?>
                        <div class="row g-0 sh-row-divider">
                            <?php if (!empty($result['note'])) { ?>
                            <div class="col-12 sh-info-item">
                                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                                <span class="sh-info-value"><?php echo html_escape($result['note']); ?></span>
                            </div>
                            <?php } ?>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) { ?>
                            <div class="col-6 col-md-4 sh-info-item">
                                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                                <span class="sh-info-value"><?php echo html_escape($result[$fields_value->name]); ?></span>
                            </div>
                            <?php } } ?>
                        </div>
                        <?php } ?>
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-4 sh-info-item" id="generated_by">
                                <span class="sh-info-label"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('collected_by'); ?></span>
                                <span class="sh-info-value">
                                    <?php
                                    if (!($superadmin_restriction == 'disabled' && $result['staff_roles_id'] == 7)) {
                                        echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']);
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bill Summary -->
            <div class="sh-vd-sum-wrap">
                <div class="sh-form-card h-100 overflow-hidden">
                    <div class="sh-card-header">
                        <span class="sh-card-header-title"><i class="fa fa-calculator me-1"></i><?php echo $this->lang->line('bill_summary'); ?></span>
                    </div>
                    <?php if (!empty($result['total'])) { ?>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                        <span><?php echo $currency_symbol . amountFormat((float)$result['total']); ?></span>
                    </div>
                    <?php } ?>
                    <?php if (!empty($result['discount'])) { ?>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('discount'); ?></span>
                        <span class="text-danger">- <?php echo $currency_symbol . amountFormat((float)$result['discount']); ?> <small class="text-secondary">(<?php echo $result['discount_percentage']; ?>%)</small></span>
                    </div>
                    <?php } ?>
                    <?php if (!empty($result['tax'])) { ?>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></span>
                        <span><?php echo $currency_symbol . amountFormat((float)$result['tax']); ?> <small class="text-secondary">(<?php echo amountFormat($tax_pct); ?>%)</small></span>
                    </div>
                    <?php } ?>
                    <?php if (!empty($result['net_amount'])) { ?>
                    <div class="sh-summary-row">
                        <span class="fw-bold"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                        <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat((float)$result['net_amount']); ?></span>
                    </div>
                    <?php } ?>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></span>
                        <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat((float)$result['paid_amount']); ?></span>
                    </div>
                    <?php if (!empty($result['refund_amount'])) { ?>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('refund_amount') . ' (' . $currency_symbol . ')'; ?></span>
                        <span><?php echo $currency_symbol . amountFormat((float)$result['refund_amount']); ?></span>
                    </div>
                    <?php } ?>
                    <div class="sh-due-row <?php echo $due_class; ?>">
                        <span><?php echo $this->lang->line('due_amount') . ' (' . $currency_symbol . ')'; ?></span>
                        <span><?php echo $currency_symbol . amountFormat($due); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicine Details -->
        <div class="sh-form-card m-2">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-medkit me-1"></i><?php echo $this->lang->line('medicine_details'); ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('medicine_category'); ?></th>
                            <th><?php echo $this->lang->line('medicine_name'); ?></th>
                            <th><?php echo $this->lang->line('batch_no'); ?></th>
                            <th><?php echo $this->lang->line('unit'); ?></th>
                            <th><?php echo $this->lang->line('expiry_date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($detail as $bill) {
                            $total_sale_amount = $bill['sale_price'] * $bill['quantity'];
                            $discount_amt = (($total_sale_amount * $bill['discount']) / 100);
                        ?>
                        <tr>
                            <td><?php echo html_escape($bill['medicine_category']); ?></td>
                            <td><?php echo html_escape($bill['medicine_name']); ?></td>
                            <td><?php echo html_escape($bill['batch_no']); ?></td>
                            <td><?php echo html_escape($bill['unit_name']); ?></td>
                            <td><?php echo $this->customlib->getmedicine_expire_month($bill['expiry']); ?></td>
                            <td class="text-end"><?php echo $bill['quantity']; ?></td>
                            <td class="text-end"><?php echo $bill['tax']; ?>%</td>
                            <td class="text-end"><?php echo $bill['discount']; ?>%</td>
                            <td class="text-end fw-semibold"><?php echo amountFormat($total_sale_amount - $discount_amt); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </div>
<?php } /* end if/else $print */ ?>

    </td></tr></tbody>
    <tfoot><tr><td>
    <?php if (!empty($print_details[0]['print_footer'])) { ?>
        <div class="footer-space">&nbsp;</div>
    <?php } ?>
    </td></tr></tfoot>
</table>
<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

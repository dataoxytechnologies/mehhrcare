<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$has_billing_perm = $this->rbac->hasPrivilege('blood_bank_partial_payment', 'can_add') || $this->rbac->hasPrivilege('blood_bank_billing_payment', 'can_add');
if (!empty($result)) {
    $discount_amt = calculatePercent($result['amount'], $result['discount_percentage']);
    $taxable      = $result['amount'] - $discount_amt;
    $tax_amt      = calculatePercent($taxable, $result['tax_percentage']);
    $total_due    = amountFormat($result['net_amount'] - $result['total_deposit']);
    $due_class    = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
}
?>
<?php if (!empty($result)) { ?>
<div class="d-flex gap-2 mb-3 flex-wrap">

    <!-- Info Card -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('blood_issue_details'); ?></span>
            </div>
            <div class="sh-info-grid">
                <div class="row g-0">
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></span>
                        <span class="sh-info-value highlight"><?php echo $bill_prefix . $result['id']; ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('received_to'); ?></span>
                        <span class="sh-info-value highlight"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                        <span class="sh-info-value"><?php echo $result['case_reference_id']; ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('issue_date'); ?></span>
                        <span class="sh-info-value"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($result['date_of_issue'], false); ?></span>
                    </div>
                </div>
                <div class="row g-0 sh-row-divider">
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                        <span class="sh-info-value"><?php echo $result['blood_group']; ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('bags'); ?></span>
                        <span class="sh-info-value"><?php echo $this->customlib->bag_string($result['bag_no'], $result['volume'], $result['unit_name']); ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['reference']); ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('donor_name'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['donor_name']); ?></span>
                    </div>
                </div>
                <div class="row g-0 sh-row-divider">
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('technician'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['technician']); ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                        <span class="sh-info-value"><?php echo isset($result['organisation_name']) ? $result['organisation_name'] : '—'; ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                        <span class="sh-info-value"><?php echo isset($result['insurance_id']) ? $result['insurance_id'] : '—'; ?></span>
                    </div>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                        <span class="sh-info-value"><?php echo isset($result['insurance_validity']) ? $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']) : '—'; ?></span>
                    </div>
                </div>
                <div class="row g-0 sh-row-divider">
                    <div class="col-12 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($result['remark']); ?></span>
                    </div>
                </div>
                <?php if (!empty($fields)) { ?>
                <div class="row g-0 sh-row-divider">
                    <?php foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $result[$fields_value->name];
                        if ($fields_value->type == 'link') {
                            $display_field = '<a href="' . $result[$fields_value->name] . '" target="_blank">' . $result[$fields_value->name] . '</a>';
                        }
                    ?>
                    <div class="col-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $fields_value->name; ?></span>
                        <span class="sh-info-value"><?php echo $display_field; ?></span>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Billing Summary -->
    <?php if ($has_billing_perm) { ?>
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('amount'); ?></span>
                <span><?php echo $currency_symbol . $result['amount']; ?></span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('discount'); ?></span>
                <span><?php echo $currency_symbol . $discount_amt; ?> (<?php echo $result['discount_percentage']; ?>%)</span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('tax'); ?></span>
                <span><?php echo $currency_symbol . $tax_amt; ?> (<?php echo $result['tax_percentage']; ?>%)</span>
            </div>
            <div class="sh-summary-row border-bottom sh-summary-netamt">
                <span><?php echo $this->lang->line('net_amount'); ?></span>
                <span><?php echo $currency_symbol . $result['net_amount']; ?></span>
            </div>
            <div class="sh-summary-row border-bottom">
                <span><?php echo $this->lang->line('paid_amount'); ?></span>
                <span><?php echo $currency_symbol . $result['total_deposit']; ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('balance_amount'); ?></span>
                <span><?php echo $currency_symbol . $total_due; ?></span>
            </div>
        </div>
    </div>
    <?php } ?>

</div>
<?php } ?>

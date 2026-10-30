<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if (!empty($result)) {
    $disc_amt   = calculatePercent($result['amount'], $result['discount_percentage']);
    $after_disc = $result['amount'] - $disc_amt;
    $tax_amt    = calculatePercent($after_disc, $result['tax_percentage']);
    $balance    = $result['net_amount'] - $result['paid_amount'];
?>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="far fa-caret-square-down me-1"></i><?php echo $this->lang->line('component_issue_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></span>
                <span class="sh-info-value highlight"><?php echo $prefix . $result['id']; ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['case_reference_id']); ?></span>
            </div>
            <?php if (!empty($result['transaction_id'])) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('transaction_id'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getpatientSessionPrefixByType('transaction_id') . html_escape($result['transaction_id']); ?></span>
            </div>
            <?php } ?>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('received_to'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['patient_name']) . ' (' . html_escape($result['patient_id']) . ')'; ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('issue_date'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($result['date_of_issue'], false); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['blood_group_name']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('component'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['component_name']); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('bags'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->bag_string($result['bag_no'], $result['volume'], $result['unit_name']); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('technician'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['technician']); ?></span>
            </div>
        </div>
        <?php if (!empty($result['remark']) || !empty($result['organisation_name']) || !empty($result['insurance_id'])) { ?>
        <div class="row g-0 sh-row-divider">
            <?php if (!empty($result['remark'])) { ?>
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['remark']); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result['organisation_name'])) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['organisation_name']); ?></span>
            </div>
            <?php } ?>
            <?php if (!empty($result['insurance_id'])) { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['insurance_id']); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']); ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
        <?php if (!empty($fields)) {
            foreach ($fields as $fields_value) {
                $display_field = isset($result[$fields_value->name]) ? html_escape($result[$fields_value->name]) : ''; ?>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                <span class="sh-info-value"><?php echo $display_field; ?></span>
            </div>
        </div>
        <?php } } ?>
    </div>
</div>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-calculator me-1"></i><?php echo $this->lang->line('amount_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></span>
                <span class="sh-info-value"><?php echo $result['amount']; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('discount'); ?></span>
                <span class="sh-info-value"><?php echo $currency_symbol . amountFormat($disc_amt) . ' (' . $result['discount_percentage'] . '%)'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?></span>
                <span class="sh-info-value"><?php echo $currency_symbol . amountFormat($tax_amt) . ' (' . $result['tax_percentage'] . '%)'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></span>
                <span class="sh-info-value highlight"><?php echo $result['net_amount']; ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="sh-info-value text-success fw-semibold"><?php echo $currency_symbol . $result['paid_amount']; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('balance_amount'); ?></span>
                <span class="sh-info-value fw-bold <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?>"><?php echo $currency_symbol . amountFormat($balance); ?></span>
            </div>
        </div>
    </div>
</div>
<?php } ?>

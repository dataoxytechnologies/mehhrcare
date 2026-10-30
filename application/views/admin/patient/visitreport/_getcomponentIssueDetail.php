<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if (empty($result)) return;

$discount_amt = calculatePercent($result['amount'], $result['discount_percentage']);
$tax_base     = $result['amount'] - $discount_amt;
$tax_amount   = ($tax_base * $result['tax_percentage']) / 100;
$total_due    = $result['net_amount'] - $result['paid_amount'];
$due_class    = ($total_due <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<div class="d-flex gap-2 mb-3 flex-wrap">

    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('blood_component_details'); ?></span>
            </div>
            <div class="sh-info-grid">

                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $prefix . $result['id']; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('received_to'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $result['patient_name'] . ' (' . $result['patient_id'] . ')'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('issue_date'); ?></div>
                        <div class="sh-info-value"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($result['date_of_issue'], false); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                        <div class="sh-info-value"><?php echo $result['blood_group_name'] ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('component'); ?></div>
                        <div class="sh-info-value"><?php echo $result['component_name'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bags'); ?></div>
                        <div class="sh-info-value"><?php echo $this->customlib->bag_string($result['bag_no'], $result['volume'], $result['unit_name']); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('technician'); ?></div>
                        <div class="sh-info-value"><?php echo $result['technician'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                        <div class="sh-info-value"><?php echo $result['remark'] ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></div>
                        <div class="sh-info-value"><?php echo $result['organisation_name'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                        <div class="sh-info-value"><?php echo $result['insurance_id'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($result['insurance_validity']) ? $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item"></div>
                </div>

                <?php if (!empty($fields)) { ?>
                <div class="row g-0 sh-row-divider">
                    <?php foreach ($fields as $fk => $fv) {
                        $val = $result[$fv->name];
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
                <span class="text-secondary"><?php echo $this->lang->line('amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($result['amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('discount'); ?></span>
                <span class="text-danger"><?php echo $result['discount_percentage'] > 0 ? '- ' . $currency_symbol . amountFormat($discount_amt) . ' <small class="text-secondary">(' . $result['discount_percentage'] . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('tax'); ?></span>
                <span><?php echo $result['tax_percentage'] > 0 ? $currency_symbol . amountFormat($tax_amount) . ' <small class="text-secondary">(' . $result['tax_percentage'] . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat($result['net_amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat($result['paid_amount']); ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('balance_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($total_due); ?></span>
            </div>
        </div>
    </div>

</div>

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if (empty($result)) return;

$discount_amt = !empty($result['discount']) ? $result['discount'] : 0;
$tax_amount   = !empty($result['amount']) && !empty($result['tax_percentage'])
    ? (($result['amount'] - $discount_amt) * $result['tax_percentage']) / 100 : 0;
$total_paid   = !empty($result['total_paid']) ? $result['total_paid'] : 0;
$balance      = $result['net_amount'] - $total_paid;
$due_class    = ($balance <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<div class="d-flex gap-2 mb-3 flex-wrap">

    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('ambulance_details'); ?></span>
            </div>
            <div class="sh-info-grid">

                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $bill_prefix . $result['id']; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></div>
                        <div class="sh-info-value highlight"><?php echo composePatientName($result['patientname'], $result['patient_id']); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                        <div class="sh-info-value"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                        <div class="sh-info-value"><?php echo $result['case_reference_id'] ?: '—'; ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('vehicle_number'); ?></div>
                        <div class="sh-info-value"><?php echo $result['vehicle_no'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('vehicle_model'); ?></div>
                        <div class="sh-info-value"><?php echo $result['vehicle_model'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('driver_name'); ?></div>
                        <div class="sh-info-value"><?php echo $result['driver'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('collected_by'); ?></div>
                        <div class="sh-info-value"><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('charge_category'); ?></div>
                        <div class="sh-info-value"><?php echo $result['charge_category_name'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('charge_name'); ?></div>
                        <div class="sh-info-value"><?php echo $result['charge_name'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item"></div>
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
                <span class="text-secondary"><?php echo $this->lang->line('standard_charge'); ?></span>
                <span><?php echo !empty($result['standard_charge']) ? $currency_symbol . amountFormat($result['standard_charge']) : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('discount'); ?></span>
                <span class="text-danger"><?php echo $discount_amt > 0 ? '- ' . $currency_symbol . amountFormat($discount_amt) . ' <small class="text-secondary">(' . $result['discount_percentage'] . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('tax'); ?></span>
                <span><?php echo $tax_amount > 0 ? $currency_symbol . amountFormat($tax_amount) . ' <small class="text-secondary">(' . $result['tax_percentage'] . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat($result['net_amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat($total_paid); ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('balance_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($balance); ?></span>
            </div>
        </div>
    </div>

</div>

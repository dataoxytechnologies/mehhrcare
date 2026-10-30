<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$due_amount      = ($result['net_amount'] + $result['refund_amount']) - $result['paid_amount'];
$due_class       = ($due_amount <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<div class="d-flex gap-2 mb-3 flex-wrap">

    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
            </div>
            <div class="sh-info-grid">

                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $result['id']; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></div>
                        <div class="sh-info-value highlight"><?php echo $result['patient_name'] . ' (' . $result['patient_unique_id'] . ')'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                        <div class="sh-info-value"><?php echo $result['case_reference_id'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                        <div class="sh-info-value"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></div>
                    </div>
                </div>

                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('mobile_no'); ?></div>
                        <div class="sh-info-value"><?php echo $result['mobileno'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('doctor'); ?></div>
                        <div class="sh-info-value"><?php echo $result['doctor_name'] ?: '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('collected_by'); ?></div>
                        <div class="sh-info-value"><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                        <div class="sh-info-value"><?php echo $result['note'] ?: '—'; ?></div>
                    </div>
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
                <span class="text-secondary"><?php echo $this->lang->line('total'); ?></span>
                <span><?php echo !empty($result['total']) ? $currency_symbol . amountFormat($result['total']) : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('discount'); ?></span>
                <span class="text-danger"><?php echo !empty($result['discount']) ? '- ' . $currency_symbol . amountFormat($result['discount']) . ' <small class="text-secondary">(' . $result['discount_percentage'] . '%)</small>' : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('tax'); ?></span>
                <span><?php echo !empty($result['tax']) ? $currency_symbol . amountFormat($result['tax']) : '—'; ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat($result['net_amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat($result['paid_amount'] - $result['refund_amount']); ?></span>
            </div>
            <?php if ($result['refund_amount'] > 0) { ?>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('refund_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($result['refund_amount']); ?></span>
            </div>
            <?php } ?>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('due_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($due_amount); ?></span>
            </div>
        </div>
    </div>

</div>

<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-medkit me-1 opacity-75"></i><?php echo $this->lang->line('medicine_details'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive rounded-3 overflow-hidden border mb-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:36px">#</th>
                        <th><?php echo $this->lang->line('medicine_name'); ?></th>
                        <th><?php echo $this->lang->line('batch_no'); ?></th>
                        <th><?php echo $this->lang->line('expiry_date'); ?></th>
                        <th class="text-center"><?php echo $this->lang->line('quantity'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end pe-3"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $row = 1;
                    foreach ($detail as $bill) {
                        $tax        = ($bill['tax'] > 0) ? ($bill['sale_price'] * $bill['tax'] / 100) * $bill['quantity'] : 0;
                        $line_total = $bill['sale_price'] * $bill['quantity'];
                        $disc_amt   = ($line_total * $bill['discount']) / 100;
                    ?>
                    <tr>
                        <td class="text-secondary ps-3"><?php echo $row; ?></td>
                        <td>
                            <span class="fw-semibold"><?php echo $bill['medicine_name']; ?></span>
                            <small class="text-muted d-block"><?php echo $bill['medicine_category']; ?></small>
                        </td>
                        <td><?php echo $bill['batch_no'] ?: '—'; ?></td>
                        <td class="text-nowrap"><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></td>
                        <td class="text-center"><?php echo $bill['quantity']; ?> <small class="text-muted"><?php echo $bill['unit_name']; ?></small></td>
                        <td class="text-end text-nowrap"><?php echo $tax > 0 ? $currency_symbol . amountFormat($tax) . ' (' . $bill['tax'] . '%)' : '—'; ?></td>
                        <td class="text-end"><?php echo $bill['discount'] > 0 ? $bill['discount'] . '%' : '—'; ?></td>
                        <td class="text-end fw-semibold pe-3"><?php echo $currency_symbol . amountFormat($line_total - $disc_amt); ?></td>
                    </tr>
                    <?php $row++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

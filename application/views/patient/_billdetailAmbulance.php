<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$r               = $result;
$discount_amount = 0; $tax_amount = 0; $balance = 0;
if (!empty($r)) {
    $discount_amount = calculatePercent($r['amount'], $r['discount_percentage']);
    $tax_amount      = calculatePercent($r['amount'] - $discount_amount, $r['tax_percentage']);
    $balance         = $r['net_amount'] - $r['paid_amount'];
}
$due_class = ($balance <= 0) ? 'sh-status-paid' : 'sh-status-due';
?>

<div class="d-flex gap-2 flex-wrap m-2">
    <!-- Card 1: Bill & Patient Details -->
    <div class="sh-flex-col">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?> &amp; <?php echo $this->lang->line('bill_details'); ?></span>
            </div>
            <?php if (!empty($r)) { ?>
            <div class="sh-info-grid">
                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('bill'); ?> #</div>
                        <div class="sh-info-value highlight"><?php echo $this->customlib->getPatientSessionPrefixByType('ambulance_call_billing') . (int)$id; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></div>
                        <div class="sh-info-value highlight"><?php echo composePatientName($r['patient'], $r['patient_id']); ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['case_reference_id']) ? html_escape($r['case_reference_id']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['date']) ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($r['date'])) : '—'; ?></div>
                    </div>
                </div>
                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('vehicle_model'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['vehicle_model']) ? html_escape($r['vehicle_model']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('vehicle_number'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['vehicle_no']) ? html_escape($r['vehicle_no']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('driver_name'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['driver']) ? html_escape($r['driver']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('charge_category'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['charge_category_name']) ? html_escape($r['charge_category_name']) : '—'; ?></div>
                    </div>
                </div>
                <div class="row g-0 sh-row-divider">
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('charge_name'); ?></div>
                        <div class="sh-info-value"><?php echo !empty($r['charge_name']) ? html_escape($r['charge_name']) : '—'; ?></div>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('collected_by'); ?></div>
                        <div class="sh-info-value">
                            <?php
                            if ($superadmin_restriction == 'disabled' && $r['staff_roles_id'] == 7) {
                                echo '—';
                            } else {
                                echo composeStaffNameByString($r['staff_name'], $r['staff_surname'], $r['staff_employee_id']);
                            }
                            ?>
                        </div>
                    </div>
                    <?php if (!empty($r['note'])) { ?>
                    <div class="col-12 col-md-6 sh-info-item">
                        <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                        <div class="sh-info-value"><?php echo html_escape($r['note']); ?></div>
                    </div>
                    <?php } ?>
                </div>
                <?php if (!empty($fields)) { ?>
                <div class="row g-0 sh-row-divider">
                    <?php foreach ($fields as $fields_value) {
                        $display_field = $r[$fields_value->name] ?? '';
                        if ($fields_value->type == 'link') {
                            $display_field = ($display_field !== '')
                                ? '<a href="' . html_escape($display_field) . '" target="_blank">' . html_escape($display_field) . '</a>'
                                : '—';
                        } else {
                            $display_field = html_escape($display_field ?: '—');
                        }
                    ?>
                    <div class="col-6 col-md-3 sh-info-item">
                        <div class="sh-info-label"><?php echo html_escape($fields_value->name); ?></div>
                        <div class="sh-info-value"><?php echo $display_field; ?></div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

    </div><!-- /.sh-flex-col -->

    <!-- Card 2: Payment Details -->
    <?php if (!empty($r)) { ?>
    <div class="sh-vd-sum-wrap">
        <div class="sh-form-card h-100 overflow-hidden">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat((float)$r['amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('discount'); ?></span>
                <span class="text-danger">
                    <?php if ($discount_amount > 0) {
                        echo '- ' . $currency_symbol . amountFormat($discount_amount);
                        if ($r['discount_percentage'] > 0) {
                            echo ' <small class="text-secondary">(' . $r['discount_percentage'] . '%)</small>';
                        }
                    } else { echo '—'; } ?>
                </span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><?php echo $this->lang->line('tax'); ?></span>
                <span>
                    <?php echo ($tax_amount > 0)
                        ? $currency_symbol . amountFormat($tax_amount) . ' <small class="text-secondary">(' . $r['tax_percentage'] . '%)</small>'
                        : '—'; ?>
                </span>
            </div>
            <div class="sh-summary-row">
                <span class="fw-bold"><?php echo $this->lang->line('net_amount'); ?></span>
                <span class="fw-bold fs-6"><?php echo $currency_symbol . amountFormat((float)$r['net_amount']); ?></span>
            </div>
            <div class="sh-summary-row">
                <span class="text-secondary"><i class="fa fa-check-circle text-success me-1"></i><?php echo $this->lang->line('paid_amount'); ?></span>
                <span class="text-success fw-semibold"><?php echo $currency_symbol . amountFormat((float)$r['paid_amount']); ?></span>
            </div>
            <div class="sh-due-row <?php echo $due_class; ?>">
                <span><?php echo $this->lang->line('due_amount'); ?></span>
                <span><?php echo $currency_symbol . amountFormat($balance); ?></span>
            </div>
        </div>
    </div><!-- /.sh-vd-sum-wrap -->
    <?php } ?>

</div><!-- /.d-flex -->

<script type="text/javascript">
    function printData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getBillDetailsAmbulance/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) { popup(result); }
        });
    }
</script>

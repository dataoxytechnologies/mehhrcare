<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$print_date      = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('patient_visit_report'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>
<body>

<!-- ══ FIXED HEADER ══ -->
<div class="fixed-print-header">
    <span class="report-title"><?php echo $this->lang->line('patient_visit_report'); ?></span>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
        <div class="content-body">

            <!-- Patient info bar -->
            <table class="info-table">
                <tr>
                    <td class="lbl"><?php echo $this->lang->line('patient_name'); ?></td>
                    <td><?php echo html_escape(!empty($patient_name) ? composePatientName($patient_name, $patient_id) : '—'); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('patient_id'); ?></td>
                    <td><?php echo html_escape($patient_id); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('date'); ?></td>
                    <td><?php echo $print_date; ?></td>
                </tr>
            </table>

            <!-- ══ 1. OPD ══ -->
            <div class="section-heading"><?php echo $this->lang->line('opd_details'); ?></div>
            <?php if (!empty($opd_data)) : ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('opd_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('checkup_id'); ?></th>
                    <th><?php echo $this->lang->line('doctor_name'); ?></th>
                    <th><?php echo $this->lang->line('symptoms'); ?></th>
                    <th><?php echo $this->lang->line('findings'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($opd_data as $v) :
                    $case_id = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : ''; ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php if ($v['appointment_date']) echo $this->customlib->YYYYMMDDTodateFormat($v['appointment_date']); ?></td>
                    <td><?php echo $this->customlib->getSessionPrefixByType('checkup_id') . $v['visit_id']; ?></td>
                    <td><?php echo html_escape($v['name'] . ' ' . $v['surname'] . ' (' . $v['employee_id'] . ')'); ?></td>
                    <td><?php echo html_escape($v['symptoms']); ?></td>
                    <td><?php echo html_escape($v['finding_description']); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 2. IPD ══ -->
            <div class="section-heading"><?php echo $this->lang->line('ipd_details'); ?></div>
            <?php if (!empty($ipd_data)) : ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('ipd_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('doctor_name'); ?></th>
                    <th><?php echo $this->lang->line('symptoms'); ?></th>
                    <th><?php echo $this->lang->line('findings'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($ipd_data as $v) :
                    $case_id = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : ''; ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('ipd_no') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php if ($v['date']) echo $this->customlib->YYYYMMDDTodateFormat($v['date']); ?></td>
                    <td><?php echo html_escape($v['name'] . ' ' . $v['surname'] . ' (' . $v['employee_id'] . ')'); ?></td>
                    <td><?php echo html_escape($v['symptoms']); ?></td>
                    <td><?php echo html_escape($v['finding_description']); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 3. Pharmacy ══ -->
            <div class="section-heading"><?php echo $this->lang->line('pharmacy_details'); ?></div>
            <?php if (!empty($pharmacy_data)) :
                $t_amt = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = $t_refund = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (%)'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('refund_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($pharmacy_data as $v) :
                    $balance  = $v['net_amount'] - ($v['paid_amount'] - $v['refund_amount']);
                    $tax_base = $v['total'] - $v['discount'];
                    $tax_pct  = $tax_base != 0 ? number_format((float)(($v['tax'] * 100) / $tax_base), 2, '.', '') : '0.00';
                    $case_id  = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : '';
                    $t_amt += $v['total']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $v['discount']; $t_tax += $v['tax']; $t_refund += $v['refund_amount'];
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['total']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['discount']) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo $v['tax'] . ' (' . $tax_pct . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo number_format((float)$v['paid_amount'] - $v['refund_amount'], 2, '.', ''); ?></td>
                    <td class="text-end"><?php echo number_format((float)$v['refund_amount'], 2, '.', ''); ?></td>
                    <td class="text-end"><?php echo number_format((float)$balance, 2, '.', ''); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="2"></td>
                    <td><b><?php echo $this->lang->line('total'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_amt, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_refund, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 4. Pathology ══ -->
            <div class="section-heading"><?php echo $this->lang->line('pathology_details'); ?></div>
            <?php if (!empty($pathology_data)) :
                $t_amt = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (%)'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($pathology_data as $v) :
                    $balance = $v['net_amount'] - $v['paid_amount'];
                    $tax_pct = ($v['total'] - $v['discount']) != 0 ? number_format(($v['tax'] * 100) / ($v['total'] - $v['discount']), 2) : '0.00';
                    $case_id = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : '';
                    $t_amt += $v['total']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $v['discount']; $t_tax += $v['tax'];
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['total']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['discount']) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['tax']) . ' (' . $tax_pct . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['paid_amount']); ?></td>
                    <td class="text-end"><?php echo number_format($balance, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="2"></td>
                    <td><b><?php echo $this->lang->line('total_amount'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_amt, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 5. Radiology ══ -->
            <div class="section-heading"><?php echo $this->lang->line('radiology_details'); ?></div>
            <?php if (!empty($radiology_data)) :
                $t_amt = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($radiology_data as $v) :
                    $balance = $v['net_amount'] - $v['paid_amount'];
                    $tax_pct = ($v['total'] - $v['discount']) != 0 ? number_format(($v['tax'] * 100) / ($v['total'] - $v['discount']), 2) : '0.00';
                    $case_id = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : '';
                    $t_amt += $v['total']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $v['discount']; $t_tax += $v['tax'];
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('radiology_billing') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['total']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['discount']) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['tax']) . ' (' . $tax_pct . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['paid_amount']); ?></td>
                    <td class="text-end"><?php echo number_format($balance, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="2"></td>
                    <td><b><?php echo $this->lang->line('total_amount'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_amt, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 6. Blood Bank — Issues ══ -->
            <div class="section-heading"><?php echo $this->lang->line('blood_bank_issue_details'); ?></div>
            <?php if (!empty($blood_bank_data['blood_issue'])) :
                $t_charge = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                    <th><?php echo $this->lang->line('bags'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($blood_bank_data['blood_issue'] as $v) :
                    $discount_amt = calculatePercent($v['standard_charge'], $v['discount_percentage']);
                    $tax          = (($v['standard_charge'] - $discount_amt) * $v['tax_percentage']) / 100;
                    $balance      = $v['net_amount'] - $v['paid_amount'];
                    $t_charge += $v['standard_charge']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $discount_amt; $t_tax += $tax;
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('blood_bank_billing') . $v['id']; ?></td>
                    <td><?php echo $v['case_reference_id']; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td><?php echo html_escape($v['donor_name']); ?></td>
                    <td><?php echo $this->customlib->bag_string($v['bag_no'], $v['volume'], $v['charge_unit']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['standard_charge']); ?></td>
                    <td class="text-end"><?php echo amountFormat($discount_amt) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($tax) . ' (' . $v['tax_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['paid_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($balance); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="4"></td>
                    <td><b><?php echo $this->lang->line('total_amount'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_charge, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 7. Blood Bank — Components ══ -->
            <div class="section-heading"><?php echo $this->lang->line('blood_bank_component_details'); ?></div>
            <?php if (!empty($blood_bank_data['component_issue'])) :
                $t_charge = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                    <th><?php echo $this->lang->line('component'); ?></th>
                    <th><?php echo $this->lang->line('bags'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($blood_bank_data['component_issue'] as $v) :
                    $discount_amt = calculatePercent($v['standard_charge'], $v['discount_percentage']);
                    $tax          = (($v['standard_charge'] - $discount_amt) * $v['tax_percentage']) / 100;
                    $balance      = $v['net_amount'] - $v['paid_amount'];
                    $t_charge += $v['standard_charge']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $discount_amt; $t_tax += $tax;
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('blood_bank_billing') . $v['id']; ?></td>
                    <td><?php echo $v['case_reference_id']; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td><?php echo html_escape($v['donor_name']); ?></td>
                    <td><?php echo html_escape($v['component_name']); ?></td>
                    <td><?php echo $this->customlib->bag_string($v['bag_no'], $v['volume'], $v['charge_unit']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['standard_charge']); ?></td>
                    <td class="text-end"><?php echo amountFormat($discount_amt) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($tax) . ' (' . $v['tax_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['paid_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($balance); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="5"></td>
                    <td><b><?php echo $this->lang->line('total_amount'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_charge, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <!-- ══ 8. Ambulance ══ -->
            <div class="section-heading"><?php echo $this->lang->line('ambulance_details'); ?></div>
            <?php if (!empty($ambulance_data)) :
                $t_amt = $t_net = $t_paid = $t_balance = $t_discount = $t_tax = 0;
            ?>
            <table class="print-table">
                <thead><tr>
                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                    <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($ambulance_data as $v) :
                    $tax     = (($v['standard_charge'] - $v['discount']) * $v['tax_percentage']) / 100;
                    $balance = $v['net_amount'] - $v['paid_amount'];
                    $case_id = ($v['case_reference_id'] > 0) ? $v['case_reference_id'] : '';
                    $t_amt += $v['standard_charge']; $t_net += $v['net_amount']; $t_paid += $v['paid_amount'];
                    $t_balance += $balance; $t_discount += $v['discount']; $t_tax += $tax;
                ?>
                <tr>
                    <td><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $v['id']; ?></td>
                    <td><?php echo $case_id; ?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($v['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    <td><?php echo html_escape($v['vehicle_model']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['standard_charge']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['discount']) . ' (' . $v['discount_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($tax) . ' (' . $v['tax_percentage'] . '%)'; ?></td>
                    <td class="text-end"><?php echo amountFormat($v['net_amount']); ?></td>
                    <td class="text-end"><?php echo amountFormat($v['paid_amount']); ?></td>
                    <td class="text-end"><?php echo number_format($balance, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="row-total">
                    <td colspan="3"></td>
                    <td><b><?php echo $this->lang->line('total_amount'); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_amt, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_discount, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_tax, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_net, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_paid, 2); ?></b></td>
                    <td class="text-end"><b><?php echo $currency_symbol . number_format($t_balance, 2); ?></b></td>
                </tr>
                </tbody>
            </table>
            <?php else : ?><p class="no-data"><?php echo $this->lang->line('no_record_found'); ?></p><?php endif; ?>

            <div class="divider mt-10"></div>

        </div>
        </td></tr>
    </tbody>
</table>

</body>
</html>

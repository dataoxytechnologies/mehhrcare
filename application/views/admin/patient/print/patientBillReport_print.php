<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$print_date      = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('patient_bill_report'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>

<body>

<!-- ══ FIXED HEADER — repeats on every printed page ══ -->
<div class="fixed-print-header">
    <span class="report-title"><?php echo $this->lang->line('patient_bill_report'); ?></span>
</div>

<!-- ══ WRAPPER TABLE — header-space pushes content below fixed header ══ -->
<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="content-body">

                    <!-- Patient info -->
                    <?php
                    $patient_name = !empty($charge_payment_data)
                        ? composePatientName($charge_payment_data[0]->patient_name, $charge_payment_data[0]->patient_id)
                        : '—';
                    ?>
                    <table class="info-table">
                        <tr>
                            <td class="lbl"><?php echo $this->lang->line('patient_name'); ?></td>
                            <td><?php echo html_escape($patient_name); ?></td>
                            <td class="lbl"><?php echo $this->lang->line('case_id'); ?></td>
                            <td><?php echo html_escape($case_reference_id); ?></td>
                            <td class="lbl"><?php echo $this->lang->line('date'); ?></td>
                            <td><?php echo $print_date; ?></td>
                        </tr>
                    </table>

                    <!-- Bill table -->
                    <?php if (!empty($charge_payment_data)) :
                        $grand_total_charge  = 0;
                        $grand_total_payment = 0;
                    ?>
                    <table class="bill-table">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('module'); ?></th>
                                <th><?php echo $this->lang->line('opd_no'); ?> / <?php echo $this->lang->line('ipd_no'); ?></th>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                <th><?php echo $this->lang->line('payment_date'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($charge_payment_data as $cp) :
                            $bill_prefix = '';
                            if     ($cp->module == 'Pathology')  $bill_prefix = $this->customlib->getSessionPrefixByType('pathology_billing');
                            elseif ($cp->module == 'Pharmacy')   $bill_prefix = $this->customlib->getSessionPrefixByType('pharmacy_billing');
                            elseif ($cp->module == 'Radiology')  $bill_prefix = $this->customlib->getSessionPrefixByType('radiology_billing');
                            elseif ($cp->module == 'Ambulance')  $bill_prefix = $this->customlib->getSessionPrefixByType('ambulance_call_billing');
                            elseif ($cp->module == 'Blood Bank') $bill_prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing');

                            $visit_no = '';
                            if (isset($cp->opd_id) && $cp->opd_id) $visit_no = $this->customlib->getSessionPrefixByType('opd_no') . $cp->opd_id;
                            if (isset($cp->ipd_id) && $cp->ipd_id) $visit_no = $this->customlib->getSessionPrefixByType('ipd_no') . $cp->ipd_id;
                        ?>
                        <tr>
                            <td><?php echo html_escape($cp->module); ?></td>
                            <td><?php echo html_escape($visit_no); ?></td>
                            <td><?php echo isset($cp->bill_no) ? html_escape($bill_prefix . $cp->bill_no) : ''; ?></td>
                            <td>
                                <ul class="pay-list">
                                <?php foreach ($cp->payments as $pay) : ?>
                                    <li><?php
                                        if ($pay->payment_mode) {
                                            echo $this->lang->line(strtolower($pay->payment_mode));
                                            if ($pay->payment_mode == 'Cheque') {
                                                echo '<br><span class="muted">No.&nbsp;' . html_escape($pay->cheque_no)
                                                   . '&nbsp;&nbsp;' . $this->customlib->YYYYMMDDTodateFormat($pay->cheque_date) . '</span>';
                                            }
                                        }
                                    ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <ul class="pay-list">
                                <?php foreach ($cp->payments as $pay) : ?>
                                    <li><?php
                                        echo $this->customlib->YYYYMMDDHisTodateFormat($pay->payment_date, $this->customlib->getHospitalTimeFormat());
                                        if ($pay->payment_mode == 'Cheque') echo '<br>&nbsp;';
                                    ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                            <td class="text-end">
                                <ul class="pay-list">
                                <?php $row_total = 0;
                                foreach ($cp->payments as $pay) : ?>
                                    <li><?php
                                        $row_total += $pay->amount;
                                        echo $pay->amount;
                                        if ($pay->payment_mode == 'Cheque') echo '<br>&nbsp;';
                                    ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>

                        <!-- sub-total per module -->
                        <?php
                        $charge = ($cp->charge == '') ? 0 : $cp->charge;
                        $grand_total_charge  += $charge;
                        $grand_total_payment += $row_total;
                        ?>
                        <tr class="row-subtotal">
                            <td colspan="4"></td>
                            <td class="text-end"><?php echo $this->lang->line('total_charge'); ?>:&nbsp;<?php echo $currency_symbol . amountFormat($charge); ?></td>
                            <td class="text-end"><?php echo $this->lang->line('total_payment'); ?>:&nbsp;<?php echo $currency_symbol . amountFormat($row_total); ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- grand total -->
                        <tr class="row-grandtotal">
                            <td colspan="3"></td>
                            <td class="text-end"><?php echo $this->lang->line('refund'); ?>:&nbsp;<?php
                                echo $currency_symbol . (!empty($total_refund_amount->payment_amount)
                                    ? amountFormat($total_refund_amount->payment_amount) : '0');
                            ?></td>
                            <td class="text-end"><?php echo $this->lang->line('grand_total_charge'); ?>:&nbsp;<?php
                                echo $currency_symbol . amountFormat($grand_total_charge);
                            ?></td>
                            <td class="text-end"><?php echo $this->lang->line('grand_total_payment'); ?>:&nbsp;<?php
                                echo $currency_symbol . amountFormat($grand_total_payment);
                            ?></td>
                        </tr>

                        </tbody>
                    </table>

                    <?php else : ?>
                        <p class="text-center"><?php echo $this->lang->line('no_record_found'); ?></p>
                    <?php endif; ?>

                    <div class="divider mt-10"></div>

                </div>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>

<?php
if ($charge->opd_id != '' && $charge->opd_id != 0) {
    $patient_name       = $charge->opd_patient_name;
    $patient_id         = $charge->opd_patient_id;
    $case_reference_id  = $charge->opd_case_reference_id;
} else {
    $patient_name       = $charge->ipd_patient_name;
    $patient_id         = $charge->ipd_patient_id;
    $case_reference_id  = $charge->ipd_case_reference_id;
}
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>"
             class="img-fluid sh-avatar-cover" >
    <?php } ?>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
            <div class="content-body sh-px-12" >
            <div class="print-area">

                <!-- ① Document title -->
                <div class="sh-print-title"><?php echo $this->lang->line('opd_bill'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:16%"><col style="width:34%">
                            <col style="width:16%"><col style="width:16%">
                            <col style="width:9%"><col style="width:9%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('patient'); ?></th>
                            <td><?php echo (($patient_name || $patient_id) ? composePatientName($patient_name, $patient_id) : '-'); ?></td>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <td><?php echo ($case_reference_id ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php echo ($charge->date ? $this->customlib->YYYYMMDDHisTodateFormat($charge->date, $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Charge table -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('charge_name') . ' / ' . $this->lang->line('charge_note'); ?></th>
                            <th style="width:18%"><?php echo $this->lang->line('discount') . ' (%)'; ?></th>
                            <th style="width:18%"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <?php echo $charge->charge_name; ?>
                                <?php if (!empty($charge->note)) { ?><small><?php echo $charge->note; ?></small><?php } ?>
                            </td>
                            <td><?php
                                $discount_amount = calculatePercent($charge->apply_charge, $charge->discount_percentage);
                                echo amountFormat($discount_amount) . ' (' . $charge->discount_percentage . '%)';
                            ?></td>
                            <td><?php
                                $tax_amount = ($charge->apply_charge - $discount_amount) * $charge->tax / 100;
                                $tax = ($charge->tax > 0) ? $tax_amount : 0;
                                echo amountFormat($tax) . ' (' . $charge->tax . '%)';
                            ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($charge->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="4"><?php echo $this->lang->line('amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($charge->apply_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php
                                $discount_amount = calculatePercent($charge->apply_charge, $charge->discount_percentage);
                                echo $currency_symbol . amountFormat($discount_amount) . ' (' . $charge->discount_percentage . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php
                                $tax_amount = ($charge->apply_charge - $discount_amount) * $charge->tax / 100;
                                $tax_amt    = ($charge->tax > 0) ? $tax_amount : 0;
                                $total      = $charge->amount;
                                echo $currency_symbol . amountFormat($tax_amt) . ' (' . $charge->tax . '%)';
                            ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="4"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- ④ Signature block -->

            </div>
            </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr><td>
            <?php if (!empty($print_details[0]['print_footer'])) { ?>
                <div class="footer-space">&nbsp;</div>
            <?php } ?>
        </td></tr>
    </tfoot>
</table>

<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

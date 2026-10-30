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

                <div class="sh-print-title"><?php echo $this->lang->line('charge'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo composePatientName($patient_name, $patient_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($case_reference_id ?: '-'); ?></td>
                            </tr>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($charge->date); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('charge_name') . ' / ' . $this->lang->line('charge_note'); ?></th>
                            <th class="sh-col-22 text-center"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="sh-col-22 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $discountamtt = ($charge->apply_charge * $charge->discount_percentage / 100);
                        $tax_amt      = (($charge->apply_charge - $discountamtt) * $charge->tax / 100);
                        $total        = (($charge->apply_charge - $discountamtt) + $tax_amt);
                        ?>
                        <tr>
                            <td>1</td>
                            <td><?php echo $charge->charge_name; ?><br><?php echo $charge->note; ?></td>
                            <td class="text-center"><?php echo amountFormat((($charge->apply_charge - $discountamtt) * $charge->tax) / 100) . ' (' . $charge->tax . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$charge->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('net_amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$charge->apply_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($discountamtt) . ' (' . $charge->discount_percentage . '%)'; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($tax_amt) . ' (' . $charge->tax . '%)'; ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                    </tfoot>
                </table>

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

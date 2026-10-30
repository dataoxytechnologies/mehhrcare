<?php
if ($transaction->opd_id != '') {
    $patient_name = $transaction->opd_patient_name;
    $patient_id   = $transaction->opd_patient_id;
} else {
    $patient_name = $transaction->ipd_patient_name;
    $patient_id   = $transaction->ipd_patient_id;
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
            <div class="content-body sh-px-12">
            <div class="print-area">

                <!-- ① Document title -->
                <div class="sh-print-title"><?php echo $this->lang->line('payment_receipt'); ?></div>

                <!-- ② Patient / transaction info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:14%"><col style="width:36%">
                            <col style="width:16%"><col style="width:34%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('patient'); ?></th>
                            <td><?php echo (($patient_name || $patient_id) ? composePatientName($patient_name, $patient_id) : '-'); ?></td>
                            <th><?php echo $this->lang->line('transaction_id'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <td><?php echo ($transaction->case_reference_id ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php echo ($transaction->payment_date ? $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Payment detail table -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <?php echo ($transaction->type == 'payment') ? $this->lang->line('payment_received') : $this->lang->line('payment_refund'); ?>
                                <small>
                                    <?php echo $this->lang->line('by') . ': ' . $this->lang->line(strtolower($transaction->payment_mode)); ?>
                                    <?php if ($transaction->note != '') { ?>
                                        <br><?php echo $this->lang->line('note') . ': ' . $transaction->note; ?>
                                    <?php } ?>
                                    <?php if ($transaction->payment_mode == 'Cheque') { ?>
                                        <br><?php echo $this->lang->line('cheque_no') . ': ' . $transaction->cheque_no; ?>
                                        <br><?php echo $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date); ?>
                                    <?php } ?>
                                </small>
                            </td>
                            <td class="sh-text-right"><?php echo amountFormat($transaction->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-total">
                            <td colspan="2"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($transaction->amount); ?></td>
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

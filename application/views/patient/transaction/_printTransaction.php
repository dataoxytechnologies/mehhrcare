<?php
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

                <div class="sh-print-title"><?php echo $this->lang->line('receipt'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id') . $transaction->id; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo composePatientName($transaction->patient_name, $transaction->patient_id); ?></td>
                            </tr>
                            <?php if ($transaction->case_reference_id != 0 && $transaction->case_reference_id != '') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo $transaction->case_reference_id; ?></td>
                            </tr>
                            <?php } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <?php if ($transaction->payment_mode) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('payment') . ' ' . $this->lang->line('mode'); ?></th>
                                <td><?php echo $this->lang->line(strtolower($transaction->payment_mode)); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if ($transaction->payment_mode == 'Cheque') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('cheque_no'); ?></th>
                                <td><?php echo $transaction->cheque_no; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('cheque_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if ($transaction->note != '') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('note'); ?></th>
                                <td><?php echo $transaction->note; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th class="sh-col-28 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo $this->lang->line('payment_received'); ?></td>
                            <td class="sh-text-right"><?php echo amountFormat((float)$transaction->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-total">
                            <td colspan="2"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$transaction->amount); ?></td>
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

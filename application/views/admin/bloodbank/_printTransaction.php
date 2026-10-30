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

                <!-- ① Document title -->
                <div class="sh-print-title"><?php echo $this->lang->line('payment_received'); ?></div>

                <!-- ② Patient / transaction info block -->
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo (($transaction->patient_name || $transaction->patient_id) ? composePatientName($transaction->patient_name, $transaction->patient_id) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($transaction->case_reference_id && $transaction->case_reference_id != 0) ? $transaction->case_reference_id : '-'; ?></td>
                            </tr>
                        </table>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                <td><?php echo html_escape($this->customlib->getSessionPrefixByType('transaction_id') . $transaction->id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo ($transaction->payment_date ? $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Payment table -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
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
                            <td>
                                <?php echo $this->lang->line('payment_received'); ?><br>
                                <?php echo $this->lang->line('by') . ': ' . $this->lang->line(strtolower($transaction->payment_mode)); ?>
                                <?php if ($transaction->payment_mode == 'Cheque') {
                                    echo ' ' . html_escape($transaction->cheque_no) . '<br>' . $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                                } ?>
                            </td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$transaction->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-total">
                            <td colspan="2"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$transaction->amount); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <?php if (!empty($transaction->name)) { ?>
                <div class="sh-summary-note">
                    <span class="fw-semibold"><?php echo $this->lang->line('received_by'); ?>: </span>
                    <?php echo html_escape(composeStaffNameByString($transaction->name, $transaction->surname, $transaction->employee_id)); ?>
                </div>
                <?php } ?>

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

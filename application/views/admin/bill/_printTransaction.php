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
                <div class="sh-print-title"><?php echo $this->lang->line('transactions'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo ($patient['patient_name'] ? html_escape($patient['patient_name']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_id'); ?></th>
                                <td><?php echo ($patient['id'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($case_id ?: '-'); ?></td>
                            </tr>
                        </table>
                        <?php if ($module_type == 'ipd_id') { ?>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('admission_date'); ?></th>
                                <td><?php echo (!empty($patient['admission_date']) ? $this->customlib->YYYYMMDDTodateFormat($patient['admission_date']) : '-'); ?></td>
                            </tr>
                        </table>
                        <?php } ?>
                    </div>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Transaction groups -->
                <?php
                $total = 0;
                foreach ($all_paymets as $all_paymetskey => $all_paymetsvalue) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:22%"><?php echo $this->lang->line('transaction_id'); ?></th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th style="width:20%"><?php echo $this->lang->line('payment_date'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($all_paymetsvalue['result'])) {
                            foreach ($all_paymetsvalue['result'] as $key => $transaction) {
                                if ($transaction['type'] == 'payment') {
                                    $payment_type = $this->lang->line('payment_received');
                                } elseif ($transaction['type'] == 'refund') {
                                    $payment_type = $this->lang->line('payment_refund');
                                }
                        ?>
                        <tr>
                            <td><?php echo html_escape($transaction['id']); ?></td>
                            <td>
                                <?php echo $payment_type; ?><br>
                                <?php echo $this->lang->line('by') . ': ' . $this->lang->line(strtolower($transaction['payment_mode'])); ?>
                                <?php if ($transaction['payment_mode'] == 'Cheque') {
                                    echo ' ' . html_escape($transaction['cheque_no']);
                                    echo '<br>' . $this->customlib->YYYYMMDDTodateFormat($transaction['cheque_date']);
                                } ?>
                            </td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($transaction['payment_date']); ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$transaction['amount']); ?></td>
                        </tr>
                        <?php
                                $total += (float)$transaction['amount'];
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <?php if (!empty($charge_details)) { ?>
                        <tr class="sh-row-first">
                            <td colspan="3"><?php echo $this->lang->line('standard_charge'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$charge_details->standard_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php echo $this->lang->line('apply_charge'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$charge_details->apply_charge); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('total_paid'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                        <?php } else { ?>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('total_paid'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                        <?php } ?>
                    </tfoot>
                </table>
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

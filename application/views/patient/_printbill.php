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
                <div class="sh-print-title"><?php echo $this->lang->line('opd_bill'); ?></div>

                <!-- ② Patient / visit info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('opd_id'); ?></th>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') . ($result['opd_details_id'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                            <td><?php echo ($result['appointment_date'] ? $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('checkup_id'); ?></th>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') . ($result['id'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('known_allergies'); ?></th>
                            <td><?php echo ($result['known_allergies'] ?: '-'); ?></td>
                        </tr>
                        <?php if ($result['appointment_no'] != '' || $result['appointment_serial_no']) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('appointment_no'); ?></th>
                            <td><?php if ($result['appointment_no'] != '') { echo $this->customlib->getpatientSessionPrefixByType('appointment') . $result['appointment_no']; } else { echo '-'; } ?></td>
                            <th><?php echo $this->lang->line('appointment_sno'); ?></th>
                            <td><?php echo ($result['appointment_serial_no'] ?: '-'); ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo ($result['patient_name'] ?: '-') . ($result['patient_id'] ? ' (' . $result['patient_id'] . ')' : ''); ?></td>
                            <th><?php echo $this->lang->line('address'); ?></th>
                            <td><?php echo ($result['address'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->get_patient_current_age($result['patient_id']) ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo ($result['gender'] ? $this->lang->line(strtolower($result['gender'])) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <td><?php echo ($blood_group_name ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('department'); ?></th>
                            <td colspan="3"><?php echo ($result['department_name'] ? $this->lang->line(strtolower($result['department_name'])) : '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Charge section (conditional) -->
                <?php if (!empty($charge)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th class="sh-col-18 text-center"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="sh-col-22 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <?php echo html_escape($charge->charge_name); ?><br>
                                <?php echo html_escape($charge->note) . ' (' . html_escape($charge->employee_id) . ')'; ?>
                            </td>
                            <td class="text-center"><?php
                                $tax = ($charge->tax > 0) ? (($charge->apply_charge * $charge->tax) / 100) : 0;
                                echo amountFormat($tax) . ' (' . $charge->tax . '%)';
                            ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$charge->amount); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$charge->apply_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php
                                $discount_amt = ($charge->apply_charge * $charge->discount_percentage) / 100;
                                echo $currency_symbol . amountFormat($discount_amt) . ' (' . $charge->discount_percentage . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php
                                $tax_amt = ($charge->tax > 0) ? (($charge->apply_charge - $discount_amt) * $charge->tax / 100) : 0;
                                echo $currency_symbol . amountFormat($tax_amt) . ' (' . $charge->tax . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($charge->apply_charge + $tax_amt - $discount_amt); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('paid_amount'); ?></td>
                            <td><?php
                                $amount_paid = (!isset($transaction) || empty($transaction)) ? 0 : $transaction->amount;
                                echo $currency_symbol . amountFormat((float)$amount_paid);
                            ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('balance_amount'); ?></td>
                            <td><?php
                                $amount_paid = (!isset($transaction) || empty($transaction)) ? 0 : $transaction->amount;
                                echo $currency_symbol . amountFormat(($charge->apply_charge + $tax_amt - $discount_amt) - (float)$amount_paid);
                            ?></td>
                        </tr>
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

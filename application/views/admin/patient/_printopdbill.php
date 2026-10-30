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
                            <col style="width:16%"><col style="width:18%">
                            <col style="width:16%"><col style="width:16%">
                            <col style="width:16%"><col style="width:18%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('opd_id'); ?></th>
                            <td><?php echo $opd_prefix . ($result['opd_details_id'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('checkup_id'); ?></th>
                            <td><?php echo $checkup_prefix . ($result['id'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                            <td><?php echo ($result['appointment_date'] ? $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                        </tr>
                        <?php if ($result['appointment_no'] != '' || $result['appointment_serial_no']) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('appointment_no'); ?></th>
                            <td><?php echo ($result['appointment_no'] ? $this->customlib->getSessionPrefixByType('appointment') . $result['appointment_no'] : '-'); ?></td>
                            <th><?php echo $this->lang->line('appointment_sno'); ?></th>
                            <td><?php echo ($result['appointment_serial_no'] ?: '-'); ?></td>
                            <th></th><td></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo ($result['patient_name'] ? $result['patient_name'] . ' (' . $result['patient_id'] . ')' : '-'); ?></td>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->get_patient_current_age($result['patient_id']) ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo isset($result['gender']) ? $this->lang->line(strtolower($result['gender'])) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <td><?php echo ($blood_group_name ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('known_allergies'); ?></th>
                            <td><?php echo ($result['known_allergies'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('department'); ?></th>
                            <td><?php echo ($result['department_name'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('address'); ?></th>
                            <td colspan="5"><?php echo ($result['address'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td colspan="5"><?php echo ($result['name'] ? $result['name'] . ' ' . $result['surname'] . ' (' . $result['employee_id'] . ')' : '-'); ?></td>
                        </tr>
                        <?php if (!empty($fields)) { foreach ($fields as $fields_value) { ?>
                        <tr>
                            <th><?php echo $fields_value->name; ?></th>
                            <td colspan="5"><?php echo ($result[$fields_value->name] ?: '-'); ?></td>
                        </tr>
                        <?php } } ?>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Charges table (shown only when charges exist) -->
                <?php if (!empty($charge)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th style="width:22%"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_standard_charge = 0; $total_discount = 0; $total_tax = 0; $total_apply_charge = 0;
                        $i = 1;
                        foreach ($charge as $charge_value) {
                            $discount = 0; $discount_amt = 0;
                            $total_standard_charge += $charge_value['standard_charge'];
                            $total_apply_charge    += $charge_value['apply_charge'];
                            $discount               = ($charge_value['apply_charge'] * $charge_value['discount_percentage']) / 100;
                            $total_discount        += $discount;
                            $tax = ($charge_value['tax'] > 0) ? (($charge_value['apply_charge'] - $discount) * $charge_value['tax']) / 100 : 0;
                            $total_tax             += $tax;
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td class="fw-bold">
                                <?php echo $charge_value['name']; ?>
                                <?php if (!empty($charge_value['note'])) { ?><small class="fw-medium"><br><?php echo $charge_value['note']; ?></small><?php } ?>
                            </td>
                            <td class="fw-bold"><?php echo amountFormat($tax) . ' (' . $charge_value['tax'] . '%)'; ?></td>
                            <td style="text-align:right; font-weight: 700;"><?php echo amountFormat($charge_value['apply_charge'] - $discount + $tax); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="3"><?php echo $this->lang->line('amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total_apply_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php
                                $total_dis_per = ($total_apply_charge > 0) ? ($total_discount * 100 / $total_apply_charge) : 0;
                                echo $currency_symbol . amountFormat($total_discount) . ' (' . amountFormat($total_dis_per) . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php
                                $tax_base = $total_apply_charge - $total_discount;
                                $total_tax_per = ($tax_base > 0) ? ($total_tax * 100 / $tax_base) : 0;
                                echo $currency_symbol . amountFormat($total_tax) . ' (' . amountFormat($total_tax_per) . '%)';
                            ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('net_amount'); ?></td>
                            <td><?php
                                $net_amt = $total_apply_charge - $total_discount + $total_tax;
                                echo $currency_symbol . amountFormat($net_amt);
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

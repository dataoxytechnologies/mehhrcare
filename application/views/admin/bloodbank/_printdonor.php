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

                <div class="sh-print-title"><?php echo $this->lang->line('blood_donor'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('donor_name'); ?></th>
                                <td><?php echo ($result['donor_name'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                <td><?php echo ($result['date_of_birth'] ? $this->customlib->YYYYMMDDTodateFormat($result['date_of_birth']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo (isset($result['gender']) && $result['gender'] ? $this->lang->line(strtolower($result['gender'])) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <td><?php echo ($result['father_name'] ?: '-'); ?></td>
                            </tr>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($result['date_of_birth'] ? $this->customlib->getAgeBydob($result['date_of_birth']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('blood_group'); ?></th>
                                <td><?php echo ($result['blood_group_name'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('contact_no'); ?></th>
                                <td><?php echo ($result['contact_no'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('address'); ?></th>
                                <td><?php echo ($result['address'] ?: '-'); ?></td>
                            </tr>
                            <?php if (!empty($fields)) { foreach ($fields as $fields_key => $fields_value) { ?>
                            <tr>
                                <th><?php echo html_escape($fields_value->name); ?></th>
                                <td><?php echo html_escape($result[$fields_value->name] ?: '-'); ?></td>
                            </tr>
                            <?php } } ?>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <?php if ($this->rbac->hasPrivilege('blood_stock', 'can_view')) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('donation_history'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bags'); ?></th>
                            <th><?php echo $this->lang->line('donate_date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('apply_charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . ' (%)'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th><?php echo $this->lang->line('payment_date'); ?></th>
                            <th><?php echo $this->lang->line('note'); ?></th>
                            <th><?php echo $this->lang->line('payment_mode'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bloodbatch as $detail) {
                            $discount_amt = calculatePercent($detail->apply_charge, $detail->discount_percentage);
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->bag_string($detail->bag_no, $detail->volume, $detail->unit_name); ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($detail->donate_date)); ?></td>
                            <td class="text-end"><?php echo amountFormat($detail->standard_charge); ?></td>
                            <td class="text-end"><?php echo amountFormat($detail->apply_charge); ?></td>
                            <td class="text-end"><?php echo amountFormat($discount_amt) . ' (' . $detail->discount_percentage . '%)'; ?></td>
                            <td class="text-end"><?php echo amountFormat(calculatePercent(($detail->apply_charge - $discount_amt), $detail->tax_percentage)) . ' (' . $detail->tax_percentage . '%)'; ?></td>
                            <td class="text-end"><?php echo amountFormat($detail->standard_charge + calculatePercent(($detail->apply_charge - $discount_amt), $detail->tax_percentage)); ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($detail->payment_date); ?></td>
                            <td><?php echo html_escape($detail->note); ?></td>
                            <td>
                                <?php echo $this->lang->line(strtolower($detail->payment_mode));
                                if ($detail->payment_mode == 'Cheque') {
                                    if ($detail->cheque_no != '') {
                                        echo '<br>' . $this->lang->line('cheque_no') . ': ' . html_escape($detail->cheque_no);
                                    }
                                    if ($detail->cheque_date != '' && $detail->cheque_date != '0000-00-00') {
                                        echo '<br>' . $this->lang->line('cheque_date') . ': ' . $this->customlib->YYYYMMDDTodateFormat($detail->cheque_date);
                                    }
                                } ?>
                            </td>
                            <td class="text-end"><?php echo amountFormat($detail->amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
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

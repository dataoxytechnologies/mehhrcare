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
                <div class="sh-print-title"><?php echo $this->lang->line('bill'); ?></div>

                <!-- ② Patient / bill info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('bill'); ?> #</th>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('ambulance_call_billing') . $id; ?></td>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo composePatientName($result['patient'], $result['patient_id']); ?></td>
                            <th><?php echo $this->lang->line('driver_name'); ?></th>
                            <td><?php echo ($result['driver'] ? html_escape($result['driver']) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                            <td><?php echo ($result['vehicle_no'] ? html_escape($result['vehicle_no']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                            <td><?php echo ($result['vehicle_model'] ? html_escape($result['vehicle_model']) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <td><?php echo ($result['case_reference_id'] ? html_escape($result['case_reference_id']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('charge_category'); ?></th>
                            <td><?php echo ($result['charge_category_name'] ? html_escape($result['charge_category_name']) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('charge_name'); ?></th>
                            <td><?php echo ($result['charge_name'] ? html_escape($result['charge_name']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('collected_by'); ?></th>
                            <td><?php
                                if ($superadmin_restriction == 'disabled' && $result['staff_roles_id'] == 7) {
                                    echo '-';
                                } else {
                                    echo composeStaffNameByString($result['staff_name'], $result['staff_surname'], $result['staff_employee_id']);
                                }
                            ?></td>
                        </tr>
                        <?php if (!empty($fields)) {
                            foreach ($fields as $fields_key => $fields_value) {
                                if ($fields_value->type == 'link') {
                                    $display_field = '<a href="' . html_escape($result["$fields_value->name"]) . '" target="_blank">' . html_escape($result["$fields_value->name"]) . '</a>';
                                } else {
                                    $display_field = html_escape($result["$fields_value->name"]);
                                }
                        ?>
                        <tr>
                            <th><?php echo html_escape($fields_value->name); ?></th>
                            <td colspan="3"><?php echo $display_field; ?></td>
                        </tr>
                        <?php } } ?>
                    </table>
                </div>

                <!-- Divider -->
                <div class="sh-section-divider"></div>

                <!-- ③ Financial summary -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table" style="width:50%; margin-left:auto;">
                    <tbody>
                        <?php if (!empty($result['amount'])) { ?>
                        <tr class="sh-row-first">
                            <td class="sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['discount'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['discount']) . ' (' . $result['discount_percentage'] . '%)'; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['tax_percentage'])) {
                            $final_amount = $result['standard_charge'] - $result['discount']; ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)calculatePercent($final_amount, $result['tax_percentage'])) . ' (' . $result['tax_percentage'] . '%)'; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['net_amount'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['net_amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['paid_amount'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['paid_amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="sh-row-total">
                            <td><?php echo $this->lang->line('due_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result['net_amount'] - (float)$result['paid_amount']); ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Note (Rule #15 — text field, not a financial figure) -->
                <?php if (!empty($result['note'])) { ?>
                <div class="sh-note-box">
                    <span class="fw-semibold"><?php echo $this->lang->line('note'); ?>: </span><?php echo html_escape($result['note']); ?>
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

<script type="text/javascript">
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/getBillDetailsAmbulance/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
</script>

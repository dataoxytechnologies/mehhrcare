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

                <div class="sh-print-title"><?php echo $this->lang->line('ambulance_billing'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $result["id"]; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo composePatientName($result['patientname'], $result['patient_id']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                                <td><?php echo ($result['vehicle_no'] ? html_escape($result['vehicle_no']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($result['case_reference_id'] ? html_escape($result['case_reference_id']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('charge_name'); ?></th>
                                <td><?php echo ($result['charge_name'] ? html_escape($result['charge_name']) : '-'); ?></td>
                            </tr>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo ($result['date'] ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('driver_name'); ?></th>
                                <td><?php echo ($result['driver'] ? html_escape($result['driver']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                <td><?php echo ($result['vehicle_model'] ? html_escape($result['vehicle_model']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('charge_category'); ?></th>
                                <td><?php echo ($result['charge_category_name'] ? html_escape($result['charge_category_name']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('collected_by'); ?></th>
                                <td><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></td>
                            </tr>
                            <?php if ($print == 'yes' && !empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $result["$fields_value->name"];
                                    if ($fields_value->type == 'link') {
                                        $display_field = ($display_field !== '')
                                            ? '<a href="' . html_escape($display_field) . '" target="_blank">' . html_escape($display_field) . '</a>'
                                            : '-';
                                    } else {
                                        $display_field = html_escape($display_field ?: '-');
                                    }
                            ?>
                            <tr>
                                <th><?php echo html_escape($fields_value->name); ?></th>
                                <td><?php echo $display_field; ?></td>
                            </tr>
                            <?php } } ?>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table">
                    <tfoot>
                        <?php if (!empty($result['amount'])) { ?>
                        <tr class="sh-row-first">
                            <td><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['discount'])) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php echo amountFormat($result['discount']) . ' (' . $result['discount_percentage'] . '%)'; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['tax_percentage'])) {
                            $final_amount = $result['standard_charge'] - $result['discount'];
                        ?>
                        <tr>
                            <td><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php echo amountFormat(calculatePercent($final_amount, $result['tax_percentage'])) . ' (' . $result['tax_percentage'] . '%)'; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['net_amount'])) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['net_amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($result['total_paid'])) { ?>
                        <tr>
                            <td><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['total_paid']); ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="sh-row-total">
                            <td><?php echo $this->lang->line('due_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['net_amount'] - $result['total_paid']); ?></td>
                        </tr>
                    </tfoot>
                </table>

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
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deleteCallAmbulance/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
</script>

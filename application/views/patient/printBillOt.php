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

                <div class="sh-print-title"><?php echo $this->lang->line('operation_theatre'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill'); ?> #</th>
                                <td><?php echo $result['id']; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo (!empty($result['date']) ? date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['date'])) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient') . ' ' . $this->lang->line('name'); ?></th>
                                <td><?php echo ($result['patient_name'] ?: '-'); ?></td>
                            </tr>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('operation') . ' ' . $this->lang->line('name'); ?></th>
                                <td><?php echo ($result['operation_name'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('operation') . ' ' . $this->lang->line('type'); ?></th>
                                <td><?php echo ($result['operation_type'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('doctor'); ?></th>
                                <td><?php echo $result['doctor_name'] . ' ' . $result['doctor_surname']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <div class="sh-print-section-title"><?php echo $this->lang->line('payment_details'); ?></div>
                <table class="sh-print-table" style="width:50%; margin-left:auto;">
                    <tbody>
                        <?php
                        $j = 0;
                        foreach ($detail as $bill) {
                            $j++;
                        ?>
                        <tr class="sh-row-first">
                            <td class="sh-text-right"><?php echo $this->lang->line('amount') . ' ' . $j . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$bill['apply_charge']); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if (!empty($result['total'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['total']); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if (!empty($result['discount'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['discount']); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if (!empty($result['tax'])) { ?>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$result['tax']); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if ((!empty($result['discount'])) && (!empty($result['tax']))) {
                            if (!empty($result['net_amount'])) { ?>
                        <tr class="sh-row-total">
                            <td><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result['net_amount']); ?></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
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
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deletePharmacyBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail");
                }
            });
        }
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/getBillDetailsOt/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
</script>

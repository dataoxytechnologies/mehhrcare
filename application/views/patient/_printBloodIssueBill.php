<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');

$discont_amt  = calculatePercent((float)$blood_issues_detail['amount'], $blood_issues_detail['discount_percentage']);
$after_disc   = (float)$blood_issues_detail['amount'] - $discont_amt;
$tax_amt      = calculatePercent($after_disc, $blood_issues_detail['tax_percentage']);
$net_amount   = $after_disc + $tax_amt;
$total_due    = $net_amount - (float)$blood_issues_detail['total_deposit'];
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>"
             class="img-fluid" style="height:100px;width:100%;display:block;object-fit:cover;">
    <?php } ?>
</div>

<table class="table-print-full" width="100%">
    <thead><tr><td><div class="header-space">&nbsp;</div></td></tr></thead>
    <tbody><tr><td>
        <div class="content-body sh-px-12" >
        <div class="print-area">

            <!-- Title -->
            <div class="sh-print-title"><?php echo $this->lang->line('blood_bank'); ?> — <?php echo $this->lang->line('bill'); ?></div>

            <!-- Bill / patient info block -->
            <div class="sh-print-info-block">
                <table class="sh-print-info-table">
                    <colgroup>
                        <col style="width:20%"><col style="width:30%">
                        <col style="width:20%"><col style="width:30%">
                    </colgroup>
                    <tr>
                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                        <td><?php echo html_escape($prefix . $blood_issues_detail['id']); ?></td>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($blood_issues_detail['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                        <td><?php echo composePatientName($blood_issues_detail['patient_name'], $blood_issues_detail['patient_id']); ?></td>
                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                        <td><?php echo ($blood_issues_detail['blood_group'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('case_id'); ?></th>
                        <td><?php echo ($blood_issues_detail['case_reference_id'] ? html_escape($blood_issues_detail['case_reference_id']) : '-'); ?></td>
                        <th><?php echo $this->lang->line('bag'); ?></th>
                        <td><?php echo html_escape($this->customlib->bag_string($blood_issues_detail['bag_no'], $blood_issues_detail['volume'], $blood_issues_detail['unit_name'])); ?></td>
                    </tr>
                    <?php if (!empty($fields)) {
                        foreach ($fields as $fields_value) {
                            $display_field = ($fields_value->type == 'link')
                                ? '<a href="' . html_escape($blood_issues_detail[$fields_value->name]) . '" target="_blank">' . html_escape($blood_issues_detail[$fields_value->name]) . '</a>'
                                : html_escape($blood_issues_detail[$fields_value->name]);
                    ?>
                    <tr>
                        <th><?php echo html_escape($fields_value->name); ?></th>
                        <td colspan="3"><?php echo $display_field; ?></td>
                    </tr>
                    <?php } } ?>
                </table>
            </div>

            <!-- Divider -->
            <div style="border-bottom:2px solid #cbd5e1;margin:8px 0 16px;"></div>

            <!-- Charges table -->
            <div class="sh-print-section-title"><?php echo $this->lang->line('bill_details'); ?></div>
            <table class="sh-print-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('description'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tax'); ?> (%)</th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?php echo html_escape($blood_issues_detail['charge_category_name']); ?></td>
                        <td class="text-end"><?php echo $blood_issues_detail['tax_percentage']; ?>%</td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['amount']); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><?php echo $this->lang->line('total'); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['amount']); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><?php echo $this->lang->line('discount'); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat($discont_amt) . ' (' . $blood_issues_detail['discount_percentage'] . '%)'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><?php echo $this->lang->line('tax'); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat($tax_amt) . ' (' . $blood_issues_detail['tax_percentage'] . '%)'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><?php echo $this->lang->line('net_amount'); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat($net_amount); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><?php echo $this->lang->line('paid'); ?></td>
                        <td class="text-end"><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['total_deposit']); ?></td>
                    </tr>
                    <tr class="sh-row-total">
                        <td colspan="3"><?php echo $this->lang->line('total_due'); ?></td>
                        <td><?php echo $currency_symbol . amountFormat($total_due); ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Signatures -->
            <div class="sh-print-signatures">
                <div class="sh-print-sig-box">
                    <div class="sh-print-sig-line"></div>
                    <div class="sh-print-sig-label"><?php echo $this->lang->line('patient_signature'); ?></div>
                </div>
                <div class="sh-print-sig-box">
                    <div class="sh-print-sig-line"></div>
                    <div class="sh-print-sig-label"><?php echo $this->lang->line('authorized_signatory'); ?></div>
                </div>
            </div>

        </div>
        </div>
    </td></tr></tbody>
    <tfoot><tr><td>
        <?php if (!empty($print_details[0]['print_footer'])) { ?>
            <div class="footer-space">&nbsp;</div>
        <?php } ?>
    </td></tr></tfoot>
</table>

<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

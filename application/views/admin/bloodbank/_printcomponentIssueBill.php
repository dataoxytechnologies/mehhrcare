<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');
$discont_amt = calculatePercent($blood_issues_detail['amount'], $blood_issues_detail['discount_percentage']);
$total_amount = $blood_issues_detail['amount'] - $discont_amt;
$tax_amt = calculatePercent($total_amount, $blood_issues_detail['tax_percentage']);
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
                <div class="sh-print-title"><?php echo $this->lang->line('component_issue'); ?></div>

                <!-- ② Bill info block -->
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo $bill_prefix . ($blood_issues_detail['id'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo (($blood_issues_detail['patient_name'] || $blood_issues_detail['patient_id']) ? composePatientName($blood_issues_detail['patient_name'], $blood_issues_detail['patient_id']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('blood_group'); ?></th>
                                <td><?php echo ($blood_issues_detail['blood_group_name'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('bag'); ?></th>
                                <td><?php echo $this->customlib->bag_string($blood_issues_detail['bag_no'], $blood_issues_detail['volume'], $blood_issues_detail['unit_name']); ?></td>
                            </tr>
                        </table>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('issue_date'); ?></th>
                                <td><?php echo ($blood_issues_detail['date_of_issue'] ? $this->customlib->YYYYMMDDHisTodateFormat($blood_issues_detail['date_of_issue'], $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($blood_issues_detail['case_reference_id'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('component'); ?></th>
                                <td><?php echo ($blood_issues_detail['component_name'] ?: '-'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Custom fields (if any) -->
                    <?php if (!empty($fields)) { ?>
                    <table class="sh-print-info-table" style="width:100%; margin-top:6px;">
                        <colgroup><col style="width:20%"><col style="width:80%"></colgroup>
                        <?php foreach ($fields as $fields_key => $fields_value) { ?>
                        <tr>
                            <th><?php echo $fields_value->name; ?></th>
                            <td><?php echo $blood_issues_detail[$fields_value->name]; ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <?php } ?>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Charges table -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('charge_details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th class="sh-col-18 text-center"><?php echo $this->lang->line('tax'); ?> (%)</th>
                            <th class="sh-col-22 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo ($blood_issues_detail['charge_categorie_name'] ?: '-'); ?></td>
                            <td class="text-center"><?php echo amountFormat((float)$tax_amt) . ' (' . $blood_issues_detail['tax_percentage'] . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['amount']); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['amount']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$discont_amt) . ' (' . $blood_issues_detail['discount_percentage'] . '%)'; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$tax_amt) . ' (' . $blood_issues_detail['tax_percentage'] . '%)'; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="sh-text-right"><?php echo $this->lang->line('paid'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$blood_issues_detail['paid_amount']); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="3"><?php echo $this->lang->line('total_due'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)(($blood_issues_detail['amount'] - $discont_amt) + $tax_amt - $blood_issues_detail['paid_amount'])); ?></td>
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

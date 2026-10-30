<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$amount = 0;
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

                <div class="sh-print-title"><?php echo $this->lang->line('radiology_lab_investigation'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo $bill_prefix . $result->id; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo composePatientName($result->patient_name, $result->patient_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($result->case_reference_id ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            </tr>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                            ?>
                            <tr>
                                <th><?php echo html_escape($fields_value->name); ?></th>
                                <td><?php echo html_escape($result->{"$fields_value->name"}); ?></td>
                            </tr>
                            <?php } } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->date); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th style="width:18%"><?php echo $this->lang->line('date'); ?></th>
                            <th class="sh-col-18 text-center"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_counter = 1;
                        $tax_amt = 0;
                        foreach ($result->radiology_report as $report_key => $report_value) {
                            $amount += $report_value->apply_charge;
                            $discountamtt  = ($report_value->apply_charge * $result->discount_percentage) / 100;
                            $afterdiscount = $report_value->apply_charge - $discountamtt;
                        ?>
                        <tr>
                            <td><?php echo $row_counter; ?></td>
                            <td>
                                <?php echo $report_value->test_name; ?>
                                <?php if (!empty($report_value->short_name)) { ?>
                                <span style="font-size:9.5px; color:#555;">(<?php echo $report_value->short_name; ?>)</span>
                                <?php } ?>
                            </td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); ?></td>
                            <td class="text-center"><?php echo calculatePercent($afterdiscount, $report_value->tax_percentage) . ' (' . $report_value->tax_percentage . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo amountFormat((float)$report_value->apply_charge); ?></td>
                        </tr>
                        <?php $row_counter++; } ?>
                    </tbody>
                    <tfoot>
                        <?php $tax_percentage = ($amount - $result->discount) != 0 ? ($result->tax * 100) / ($amount - $result->discount) : 0; ?>
                        <tr class="sh-row-first">
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($amount); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result->discount) . ' (' . $result->discount_percentage . '%)'; ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result->tax) . ' (' . amountFormat($tax_percentage) . '%)'; ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('net_amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result->net_amount); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('paid'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result->total_deposit); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="4"><?php echo $this->lang->line('total_due'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$result->net_amount - (float)$result->total_deposit); ?></td>
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

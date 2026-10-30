<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$amount          = 0;
$tax_amt         = 0;
include(APPPATH . 'views/admin/shared/_print_css.php');

// Pre-calculate totals for status badge
foreach ($result->radiology_report as $rv) {
    $d_amt    = ($rv->apply_charge * $result->discount_percentage) / 100;
    $amount  += $rv->apply_charge;
    $tax_amt += ($rv->tax_percentage > 0) ? (($rv->apply_charge - $d_amt) * $rv->tax_percentage / 100) : 0;
}
$total_due = ($amount - $result->discount) + $result->tax - $result->total_deposit;
$amount    = 0; // reset for row-level loop below
$tax_amt   = 0;
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

                <div class="sh-print-title"><?php echo $this->lang->line('radiology_single_billing'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo ($bill_prefix . $result->id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo composePatientName($result->patient_name, $result->patient_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($this->customlib->get_patient_current_age($result->patient_id) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($result->case_reference_id ?: '-'); ?></td>
                            </tr>
                            <?php if (!empty($prescription)) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('prescription_no'); ?></th>
                                <td><?php echo $prescription; ?></td>
                            </tr>
                            <?php } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo ($result->date ? $this->customlib->YYYYMMDDHisTodateFormat($result->date, $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('paid_amount'); ?></th>
                                <td><?php echo $currency_symbol . amountFormat($result->total_deposit); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('balance'); ?></th>
                                <td><?php echo $currency_symbol . amountFormat($total_due); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <td><?php echo ($total_due <= 0) ? $this->lang->line('paid') : $this->lang->line('due'); ?></td>
                            </tr>
                            <?php if (!empty($fields)) { foreach ($fields as $fields_key => $fields_value) {
                                $display_field = $result->{"$fields_value->name"} ?? '';
                                if ($fields_value->type == "link") {
                                    $display_field = ($display_field !== '')
                                        ? "<a href=\"" . html_escape($display_field) . "\" target=\"_blank\">" . html_escape($display_field) . "</a>"
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
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('test_name'); ?></th>
                            <th style="width:22%" class="text-end"><?php echo $this->lang->line('date'); ?></th>
                            <th style="width:22%" class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_counter = 1;
                        foreach ($result->radiology_report as $report_key => $report_value) {
                            $discount_amt  = ($report_value->apply_charge * $result->discount_percentage) / 100;
                            $amount       += $report_value->apply_charge;
                            $tax           = ($report_value->tax_percentage > 0)
                                             ? (($report_value->apply_charge - $discount_amt) * $report_value->tax_percentage / 100)
                                             : 0;
                            $tax_amt      += $tax;
                        ?>
                        <tr>
                            <td><?php echo $row_counter++; ?></td>
                            <td class="fw-bold">
                                <?php echo ($report_value->test_name ?: '-'); ?>
                                <?php if (!empty($report_value->short_name)) { ?>
                                    <small class="fw-medium"><?php echo html_escape($report_value->short_name); ?></small>
                                <?php } ?>
                            </td>
                            <td class="sh-text-right"><?php echo (!empty($report_value->reporting_date) ? $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date) : '-'); ?></td>
                            <td class="sh-text-right">
                                <?php echo ($report_value->tax_percentage > 0) ? (amountFormat($tax) . ' (' . $report_value->tax_percentage . '%)') : '-'; ?>
                            </td>
                            <td class="sh-text-right"><?php echo amountFormat($report_value->apply_charge - $discount_amt + $tax); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="4"><?php echo $this->lang->line('subtotal'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($amount); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('discount'); ?></td>
                            <td><?php
                                $discount_per = ($amount > 0) ? ($result->discount * 100) / $amount : 0;
                                echo $currency_symbol . amountFormat($result->discount) . ($discount_per > 0 ? (' (' . number_format((float)$discount_per, 2, '.', '') . '%)') : '');
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('tax'); ?></td>
                            <td><?php
                                $denominator = $amount - $result->discount;
                                $tax_per     = ($denominator > 0) ? ($result->tax * 100 / $denominator) : 0;
                                echo $currency_symbol . amountFormat($result->tax) . ($tax_per > 0 ? (' (' . amountFormat($tax_per) . '%)') : '');
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('net_amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat(($amount - $result->discount) + $result->tax); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?php echo $this->lang->line('paid_amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($result->total_deposit); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="4"><?php echo $this->lang->line('balance'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total_due); ?></td>
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

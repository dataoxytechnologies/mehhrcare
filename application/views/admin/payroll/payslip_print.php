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

                <div class="sh-print-title">
                    <?php echo $this->lang->line('payslip_for_the_period_of') . ' ' . $this->lang->line($result['month']) . ' ' . $result['year']; ?>
                </div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('payslip'); ?> #</th>
                                <td><?php echo $result['id']; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('payment_date'); ?></th>
                                <td><?php echo (!empty($result['payment_date']) ? $this->customlib->YYYYMMDDTodateFormat($result['payment_date']) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                <td><?php echo (!empty($result['payment_mode']) ? $payment_mode[$result['payment_mode']] : '-'); ?></td>
                            </tr>
                            <?php if ($result['payment_mode'] == 'Cheque') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('cheque_no'); ?></th>
                                <td>
                                    <?php echo html_escape($result['cheque_no']); ?>
                                    <?php if (!empty($result['attachment'])) { ?>
                                    <a href="<?php echo base_url() . 'admin/payroll/download/' . $result['id']; ?>" class="btn btn-sm btn-light no-print ms-1" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('cheque_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']); ?></td>
                            </tr>
                            <?php } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('staff_id'); ?></th>
                                <td><?php echo ($result['employee_id'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <td><?php echo ($result['name'] . ' ' . $result['surname'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('department'); ?></th>
                                <td><?php echo ($result['department'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('designation'); ?></th>
                                <td><?php echo ($result['designation'] ?: '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <div class="sh-print-section-title"><?php echo $this->lang->line('earning') . ' &amp; ' . $this->lang->line('deduction'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:30%"><?php echo $this->lang->line('earning'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th style="width:30%"><?php echo $this->lang->line('deduction'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j = 0;
                        foreach ($allowance as $key => $value) { ?>
                        <tr>
                            <?php if (array_key_exists($j, $positive_allowance)) { ?>
                            <td><?php echo $positive_allowance[$j]['allowance_type']; ?></td>
                            <td class="sh-text-right"><?php echo amountFormat($positive_allowance[$j]['amount']); ?></td>
                            <?php } else { ?><td></td><td></td><?php } ?>
                            <?php if (array_key_exists($j, $negative_allowance)) { ?>
                            <td><?php echo $negative_allowance[$j]['allowance_type']; ?></td>
                            <td class="sh-text-right"><?php echo amountFormat($negative_allowance[$j]['amount']); ?></td>
                            <?php } else { ?><td></td><td></td><?php } ?>
                        </tr>
                        <?php $j++; } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td><?php echo $this->lang->line('total') . ' ' . $this->lang->line('earning'); ?></td>
                            <td><?php echo amountFormat($result['total_allowance']); ?></td>
                            <td><?php echo $this->lang->line('total') . ' ' . $this->lang->line('deduction'); ?></td>
                            <td><?php echo amountFormat($result['total_deduction']); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div style="border-bottom: 1px solid #e2e8f0; margin: 16px 0 12px;"></div>

                <div class="sh-print-section-title"><?php echo $this->lang->line('salary_summary'); ?></div>
                <table class="sh-print-table">
                    <tfoot>
                        <?php $gross = $result['basic'] + $result['total_allowance'] - $result['total_deduction']; ?>
                        <tr class="sh-row-first">
                            <td><?php echo $this->lang->line('basic_salary') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['basic']); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('gross_salary') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($gross); ?></td>
                        </tr>
                        <?php if (!empty($result['tax'])) {
                            $tax_amount = ($gross * $result['tax']) / 100;
                        ?>
                        <tr>
                            <td><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($tax_amount) . ' (' . $result['tax'] . '%)'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="sh-row-total">
                            <td><?php echo $this->lang->line('net_salary') . ' (' . $currency_symbol . ')'; ?></td>
                            <td><?php echo amountFormat($result['net_salary']); ?></td>
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

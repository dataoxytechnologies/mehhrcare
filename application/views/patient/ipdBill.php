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

                <div class="sh-print-title"><?php echo $this->lang->line('ipd_bill'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('ipd') . ' ' . $this->lang->line('no'); ?></th>
                                <td><?php echo ($result['ipd_no'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo ($result['patient_name'] ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('admission_date'); ?></th>
                                <td><?php echo (!empty($result['date']) ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])) : '-'); ?></td>
                            </tr>
                            <?php if (!empty($result['discharge_date'])) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('discharged_date'); ?></th>
                                <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($result['discharge_date'])); ?></td>
                            </tr>
                            <?php } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('doctor'); ?></th>
                                <td><?php echo $result['name'] . '' . $result['surname']; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('organisation'); ?></th>
                                <td><?php echo ($result['organisation_name'] ?: '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <?php
                $j = 0;
                $total = 0;
                ?>

                <?php if (!empty($charges)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('charges'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('charges'); ?></th>
                            <th style="width:20%"><?php echo $this->lang->line('category'); ?></th>
                            <th style="width:22%"><?php echo $this->lang->line('date'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($charges as $key => $charge) {
                            $j++;
                            $total += $charge['apply_charge'];
                        ?>
                        <tr>
                            <td><?php echo $j; ?></td>
                            <td><?php echo $charge['charge_type']; ?></td>
                            <td><?php echo $charge['charge_category']; ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($charge['date'])); ?></td>
                            <td class="sh-text-right"><?php echo amountFormat((float)$charge['apply_charge']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="4" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php } ?>

                <?php
                $k = 0;
                $total_paid = 0;
                $status = ($result['status'] != 'paid') ? $this->lang->line('unpaid') : $this->lang->line('paid');
                ?>

                <?php if (!empty($payment_details)) { ?>
                <div class="sh-print-section-title sh-mt-14" ><?php echo $this->lang->line('payment') . ' ' . $this->lang->line('details'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('payment') . ' ' . $this->lang->line('mode'); ?></th>
                            <th style="width:28%"><?php echo $this->lang->line('payment') . ' ' . $this->lang->line('date'); ?></th>
                            <th class="sh-col-28 sh-text-right"><?php echo $this->lang->line('paid') . ' ' . $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payment_details as $key => $payment) {
                            $total_paid += $payment['paid_amount'];
                        ?>
                        <tr>
                            <td><?php echo $payment['payment_mode']; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($payment['date']); ?></td>
                            <td class="sh-text-right"><?php echo amountFormat((float)$payment['paid_amount']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="2" class="sh-text-right"><?php echo $this->lang->line('total'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total_paid); ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php } ?>

                <div class="sh-print-section-title sh-mt-14" ><?php echo $this->lang->line('summary'); ?></div>
                <table class="sh-print-table" style="width:50%; margin-left:auto;">
                    <tbody>
                        <tr class="sh-row-first">
                            <td class="sh-text-right"><?php echo $this->lang->line('total') . ' ' . $this->lang->line('charges') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($total); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('any_other_charges') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)(!empty($result['other_charge']) ? $result['other_charge'] : $other_charge)); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)(!empty($result['discount']) ? $result['discount'] : $discount)); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)(empty($result['tax']) ? $tax : $result['tax'])); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('gross_total') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)(!empty($result['gross_total']) ? $result['gross_total'] : $gross_total)); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right"><?php echo $this->lang->line('total_payment') . ' (' . $currency_symbol . ')'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)(empty($result['paid_amount']) ? $paid_amount : $result['paid_amount'])); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td><?php echo $this->lang->line('net_payable') . ' ' . $this->lang->line('amount') . ' (' . $status . ')'; ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)(empty($result['net_amount']) ? $net_amount : $result['net_amount'])); ?></td>
                        </tr>
                    </tbody>
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

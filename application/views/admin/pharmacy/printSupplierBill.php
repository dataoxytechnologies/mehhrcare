<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$purchase_prefix = $this->customlib->getSessionPrefixByType('purchase_no');
$tot             = (float) $result['total'];
$disc            = (float) $result['discount'];
$tax             = (float) $result['tax'];
$base            = $tot - $disc;
$net             = !empty($result['net_amount']) ? (float) $result['net_amount'] : $tot;
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
                <div class="sh-print-title"><?php echo $this->lang->line('purchase_details'); ?></div>

                <!-- ② Supplier / purchase info block -->
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('purchase_no'); ?></th>
                                <td><?php echo html_escape($purchase_prefix . $result['id']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('purchase_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                <td><?php echo !empty($result['invoice_no']) ? html_escape($result['invoice_no']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                <td><?php echo html_escape($result['supplier']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('supplier_contact'); ?></th>
                                <td><?php echo html_escape($result['contact']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('drug_license_number'); ?></th>
                                <td><?php echo html_escape($result['supplier_drug_licence']) ?: '-'; ?></td>
                            </tr>
                        </table>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('contact_person'); ?></th>
                                <td><?php echo html_escape($result['supplier_person']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                <td><?php echo html_escape($result['supplier_person_contact']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                <td><?php echo $this->lang->line(strtolower($result['payment_mode'])) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('address'); ?></th>
                                <td><?php echo html_escape($result['address']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('note'); ?></th>
                                <td><?php echo !empty($result['note']) ? html_escape($result['note']) : '-'; ?></td>
                            </tr>
                            <?php if ($result['payment_mode'] == 'Cheque') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('cheque_no'); ?></th>
                                <td>
                                    <?php echo html_escape($result['cheque_no']); ?>
                                    <?php if (!empty($result['cheque_date'])) { echo ' (' . $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']) . ')'; } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <!-- ③ Medicine details table -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('medicine_detail'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('medicine_category'); ?></th>
                            <th><?php echo $this->lang->line('medicine_name'); ?></th>
                            <th><?php echo $this->lang->line('batch_no'); ?></th>
                            <th><?php echo $this->lang->line('expiry_month'); ?></th>
                            <th class="sh-text-right"><?php echo $this->lang->line('mrp'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('batch_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('packing_qty'); ?></th>
                            <th class="sh-text-right"><?php echo $this->lang->line('quantity'); ?></th>
                            <th class="sh-text-right"><?php echo $this->lang->line('tax'); ?> (%)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('purchase_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail as $bill) { ?>
                        <tr>
                            <td><?php echo html_escape($bill['medicine_category']); ?></td>
                            <td><?php echo html_escape($bill['medicine_name']); ?></td>
                            <td><?php echo html_escape($bill['batch_no']); ?></td>
                            <td><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></td>
                            <td class="sh-text-right"><?php echo $bill['mrp']; ?></td>
                            <td class="sh-text-right"><?php echo $bill['batch_amount']; ?></td>
                            <td class="sh-text-right"><?php echo number_format($bill['sale_rate'], 2); ?></td>
                            <td class="sh-text-right"><?php echo $bill['packing_qty']; ?></td>
                            <td class="sh-text-right"><?php echo $bill['quantity']; ?></td>
                            <td class="sh-text-right"><?php echo $bill['tax']; ?></td>
                            <td class="sh-text-right"><?php echo number_format($bill['purchase_price'], 2); ?></td>
                            <td class="sh-text-right"><?php echo number_format($bill['amount'], 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="sh-row-first">
                            <td colspan="11"><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</td>
                            <td><?php echo number_format($tot, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="11"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>) <?php echo '(' . ($tot > 0 ? amountFormat(($disc * 100) / $tot) : 0) . '%)'; ?></td>
                            <td><?php echo number_format($disc, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="11"><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>) <?php echo '(' . ($base > 0 ? amountFormat(($tax * 100) / $base) : 0) . '%)'; ?></td>
                            <td><?php echo number_format($tax, 2); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td colspan="11"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</td>
                            <td><?php echo number_format($net, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- ④ Cheque payment note (text block) -->
                <?php if ($result['payment_mode'] == 'Cheque' && !empty($result['payment_note'])) { ?>
                <div class="sh-note-block">
                    <span class="fw-semibold"><?php echo $this->lang->line('payment_note'); ?>: </span><?php echo html_escape($result['payment_note']); ?>
                </div>
                <?php } ?>

                <!-- ⑤ Return history -->
                <?php if (!empty($return_history)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('return_history'); ?></div>
                <?php foreach ($return_history as $return) { ?>
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('return_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($return['return_date']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('reason'); ?></th>
                                <td><?php echo html_escape($return['reason']) ?: '-'; ?></td>
                            </tr>
                        </table>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('total_amount'); ?></th>
                                <td><?php echo $currency_symbol . ' ' . number_format($return['total_amount'], 2); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('returned_by'); ?></th>
                                <td><?php echo html_escape($return['returned_by_name'] . ' ' . $return['returned_by_surname']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table class="sh-print-table sh-mb-12" >
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('medicine_name'); ?></th>
                            <th><?php echo $this->lang->line('batch_no'); ?></th>
                            <th class="sh-text-right"><?php echo $this->lang->line('quantity'); ?></th>
                            <th class="sh-text-right"><?php echo $this->lang->line('purchase_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="sh-text-right"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($return['items'] as $item) { ?>
                        <tr>
                            <td><?php echo html_escape($item['medicine_name']); ?></td>
                            <td><?php echo html_escape($item['batch_no']); ?></td>
                            <td class="sh-text-right"><?php echo $item['quantity']; ?></td>
                            <td class="sh-text-right"><?php echo number_format($item['purchase_price'], 2); ?></td>
                            <td class="sh-text-right"><?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
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

<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$purchase_prefix  = $this->customlib->getSessionPrefixByType('purchase_no');
$has_discount     = isset($result['discount']);
$has_tax          = !empty($result['tax']) && floatval($result['tax']) > 0;
$has_net          = !empty($result['net_amount']);
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-theme.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
    </head>
    <div id="html-2-pdfwrapper" class="p-1">

        <?php if (!empty($print_details[0]['print_header'])) { ?>
        <div class="mb-2">
            <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>" class="img-fluid w-100">
        </div>
        <?php } ?>

        <!-- ── Top: Supplier info card + Purchase summary card ── -->
        <div class="d-flex gap-2 mb-2 flex-wrap">

            <!-- Card 1: Supplier & Purchase Info -->
            <div class="sh-flex-col">
                <div class="sh-form-card h-100 mx-0">
                    <div class="sh-card-header">
                        <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_details'); ?></span>
                    </div>
                    <div class="sh-info-grid">

                        <!-- Row 1: Purchase identity + Supplier Name -->
                        <div class="row g-0">
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('purchase_no'); ?></div>
                                <div class="sh-info-value highlight"><?php echo $purchase_prefix . $result['id']; ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('purchase_date'); ?></div>
                                <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat()); ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('supplier_name'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['supplier']) ?: '—'; ?></div>
                            </div>
                        </div>

                        <!-- Row 2: Remaining supplier details -->
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('supplier_contact'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['contact']) ?: '—'; ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('contact_person'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['supplier_person']) ?: '—'; ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('contact_person_phone'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['supplier_person_contact']) ?: '—'; ?></div>
                            </div>
                        </div>

                        <!-- Row 3: License, Address, Note -->
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('drug_license_number'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['supplier_drug_licence']) ?: '—'; ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('address'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($result['address']) ?: '—'; ?></div>
                            </div>
                            <div class="col-6 col-md-4 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                                <div class="sh-info-value"><?php echo !empty($result['note']) ? html_escape($result['note']) : '—'; ?></div>
                            </div>
                        </div>

                        <?php if ($result['payment_mode'] == 'Cheque') { ?>
                        <!-- Row 4: Cheque details -->
                        <div class="row g-0 sh-row-divider">
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('cheque_no'); ?></div>
                                <div class="sh-info-value">
                                    <?php echo html_escape($result['cheque_no']); ?>
                                    <?php if ($print == 'no') { ?>
                                    <a href="<?php echo site_url('admin/pharmacy/downloadcheque/' . $result['id']); ?>" class="btn btn-sm btn-light ms-1" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                                <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']); ?></div>
                            </div>
                            <div class="col-6 col-md-6 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('payment_note'); ?></div>
                                <div class="sh-info-value"><?php echo !empty($result['payment_note']) ? html_escape($result['payment_note']) : '—'; ?></div>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>

            <!-- Card 2: Purchase Summary -->
            <div class="sh-vd-sum-wrap">
                <div class="sh-form-card overflow-hidden mx-0 h-100">
                    <div class="sh-card-header">
                        <span class="sh-card-header-title"><?php echo $this->lang->line('bill_summary'); ?></span>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('invoice_number'); ?></span>
                        <span class="fw-semibold"><?php echo !empty($result['invoice_no']) ? html_escape($result['invoice_no']) : '—'; ?></span>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('payment_mode'); ?></span>
                        <span><?php echo $this->lang->line(strtolower($result['payment_mode'])) ?: '—'; ?></span>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('attach_document'); ?></span>
                        <?php if (!empty($result['attachment']) && $print == 'no') { ?>
                        <a href="<?php echo site_url('admin/pharmacy/download_attachment/' . $result['id']); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>" download><i class="fa fa-download"></i></a>
                        <?php } else { echo '<span>—</span>'; } ?>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</span>
                        <span><?php echo number_format((float)$result['total'], 2); ?></span>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</span>
                        <span><?php $disc = (float)$result['discount']; $tot = (float)$result['total']; echo number_format($disc, 2) . ' <small class="text-secondary">(' . ($tot > 0 ? amountFormat(($disc * 100) / $tot) : 0) . '%)</small>'; ?></span>
                    </div>
                    <div class="sh-summary-row">
                        <span class="text-secondary"><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
                        <span><?php $tax = (float)$result['tax']; $base = $tot - $disc; echo number_format($tax, 2) . ' <small class="text-secondary">(' . ($base > 0 ? amountFormat(($tax * 100) / $base) : 0) . '%)</small>'; ?></span>
                    </div>
                    <div class="sh-due-row sh-status-paid">
                        <span><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                        <span><?php echo number_format(!empty($result['net_amount']) ? (float)$result['net_amount'] : $tot, 2); ?></span>
                    </div>
                </div>
            </div>

        </div><!-- /d-flex top -->

        <!-- ── Medicines Table ── -->
        <div class="sh-form-card mb-3 mx-0">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_detail'); ?></span>
            </div>
            <div class="p-2">
                <div class="rounded-3 border overflow-hidden mb-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-nowrap"><?php echo $this->lang->line('medicine_category'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('medicine_name'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('batch_no'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('expiry_month'); ?></th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('mrp'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('batch_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('packing_qty'); ?></th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('quantity'); ?></th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('tax'); ?> (%)</th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('purchase_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end text-nowrap"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail as $bill) { ?>
                            <tr>
                                <td><?php echo html_escape($bill['medicine_category']); ?></td>
                                <td><?php echo html_escape($bill['medicine_name']); ?></td>
                                <td><?php echo html_escape($bill['batch_no']); ?></td>
                                <td><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></td>
                                <td class="text-end"><?php echo $bill['mrp']; ?></td>
                                <td class="text-end"><?php echo $bill['batch_amount']; ?></td>
                                <td class="text-end">
                                    <input type="text" name="salerate[]" class="form-control form-control-sm text-end sh-summary-input" value="<?php echo number_format($bill['sale_rate'], 2); ?>">
                                    <input type="hidden" name="id[]" value="<?php echo $bill['id']; ?>">
                                </td>
                                <td class="text-end"><?php echo $bill['packing_qty']; ?></td>
                                <td class="text-end"><?php echo $bill['quantity']; ?></td>
                                <td class="text-end"><?php echo $bill['tax']; ?></td>
                                <td class="text-end"><?php echo number_format($bill['purchase_price'], 2); ?></td>
                                <td class="text-end"><?php echo number_format($bill['amount'], 2); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <!-- ── Return History ── -->
        <?php if (!empty($return_history)) { ?>
        <div class="sh-form-card mx-0">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('return_history'); ?></span>
            </div>
            <div class="p-2">
                <?php foreach ($return_history as $return) { ?>
                <div class="sh-form-card mb-2 mx-0">
                    <div class="sh-info-grid">
                        <div class="row g-0">
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('return_date'); ?></div>
                                <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($return['return_date']); ?></div>
                            </div>
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('reason'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($return['reason']) ?: '—'; ?></div>
                            </div>
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('total_amount'); ?></div>
                                <div class="sh-info-value fw-semibold"><?php echo $currency_symbol . ' ' . number_format($return['total_amount'], 2); ?></div>
                            </div>
                            <div class="col-6 col-md-3 sh-info-item">
                                <div class="sh-info-label"><?php echo $this->lang->line('returned_by'); ?></div>
                                <div class="sh-info-value"><?php echo html_escape($return['returned_by_name'] . ' ' . $return['returned_by_surname']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-2">
                        <div class="rounded-3 border overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap"><?php echo $this->lang->line('medicine_name'); ?></th>
                                        <th class="text-nowrap"><?php echo $this->lang->line('batch_no'); ?></th>
                                        <th class="text-end text-nowrap"><?php echo $this->lang->line('quantity'); ?></th>
                                        <th class="text-end text-nowrap"><?php echo $this->lang->line('purchase_price'); ?></th>
                                        <th class="text-end text-nowrap"><?php echo $this->lang->line('amount'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($return['items'] as $item) { ?>
                                    <tr>
                                        <td><?php echo html_escape($item['medicine_name']); ?></td>
                                        <td><?php echo html_escape($item['batch_no']); ?></td>
                                        <td class="text-end"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo number_format($item['purchase_price'], 2); ?></td>
                                        <td class="text-end"><?php echo number_format($item['amount'], 2); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="footer-fixed printfooter">
            <p><?php if (!empty($print_details[0]['print_footer'])) { echo $print_details[0]['print_footer']; } ?></p>
        </div>

    </div>
</html>

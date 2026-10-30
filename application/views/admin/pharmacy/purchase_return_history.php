<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<?php if (!empty($history)) { ?>
    <?php foreach ($history as $i => $return) { ?>
    <div class="sh-form-card <?php echo $i > 0 ? 'mt5' : ''; ?>">
        <div class="sh-card-header d-flex align-items-center justify-content-between">
            <span class="sh-card-header-title">
                <i class="fa fa-history me-1"></i>
                <?php echo $this->customlib->YYYYMMDDHisTodateFormat($return['return_date']); ?>
            </span>
            <span class="badge bg-secondary fs-7"><?php echo $currency_symbol . ' ' . number_format($return['total_amount'], 2); ?></span>
        </div>
        <div class="p-2">
            <div class="sh-info-grid">
                <div class="row g-0">
                    <div class="col-6 col-md-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('reason'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($return['reason']); ?></span>
                    </div>
                    <div class="col-6 col-md-3 sh-info-item">
                        <span class="sh-info-label"><?php echo $this->lang->line('returned_by'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($return['returned_by_name'] . ' ' . $return['returned_by_surname']); ?></span>
                    </div>
                    <?php if (!empty($return['note'])) { ?>
                    <div class="col-12 col-md-6 sh-info-item sh-row-divider">
                        <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                        <span class="sh-info-value"><?php echo html_escape($return['note']); ?></span>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <?php if (!empty($return['items'])) { ?>
            <div class="sh-form-card mt5">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_name'); ?></span>
                </div>
                <div class="p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('medicine_name'); ?></th>
                                    <th><?php echo $this->lang->line('batch_no'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('purchase_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                                    <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
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
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</strong></td>
                                    <td class="text-end"><strong><?php echo $currency_symbol . ' ' . number_format($return['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
<?php } else { ?>
<div class="text-center py-4 text-muted">
    <i class="fa fa-history fa-2x mb-2 d-block"></i>
    <?php echo $this->lang->line('no_return_found'); ?>
</div>
<?php } ?>

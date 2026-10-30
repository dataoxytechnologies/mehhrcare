<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$prefix_return   = 'RET';
?>
<?php if (!empty($history)) { ?>
    <?php foreach ($history as $i => $return) { ?>
    <div class="sh-form-card mb-3">

        <div class="sh-card-header d-flex align-items-center justify-content-between">
            <span class="sh-card-header-title">
                <?php echo $prefix_return . html_escape($return['return_no']); ?>
                &nbsp;&mdash;&nbsp;
                <?php echo $this->customlib->YYYYMMDDTodateFormat($return['date']); ?>
            </span>
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($return['returned_by_name'])) { ?>
                <span class="badge bg-secondary"><?php echo html_escape($return['returned_by_name']); ?></span>
                <?php } ?>
                <span class="badge bg-primary"><?php echo $currency_symbol . ' ' . number_format($return['net_amount'], 2); ?></span>
            </div>
        </div>

        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('total'); ?></span>
                    <span class="sh-info-value"><?php echo $currency_symbol . ' ' . number_format($return['total'], 2); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('discount'); ?></span>
                    <span class="sh-info-value"><?php echo $currency_symbol . ' ' . number_format($return['discount'], 2); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?></span>
                    <span class="sh-info-value"><?php echo $currency_symbol . ' ' . number_format($return['tax'], 2); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('net_amount'); ?></span>
                    <span class="sh-info-value highlight"><?php echo $currency_symbol . ' ' . number_format($return['net_amount'], 2); ?></span>
                </div>
            </div>
            <?php if (!empty($return['note'])) { ?>
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($return['note']); ?></span>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if (!empty($return['items'])) { ?>
        <div class="p-2">
            <div class="table-responsive">
                <table class="table table-sm table-hover sh-tests-table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('medicine_name'); ?></th>
                            <th><?php echo $this->lang->line('batch_no'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-end"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</th>
                            <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($return['items'] as $item) { ?>
                        <tr>
                            <td><?php echo html_escape($item['medicine_name']); ?></td>
                            <td><?php echo html_escape($item['batch_no']); ?></td>
                            <td class="text-end"><?php echo html_escape($item['quantity']); ?></td>
                            <td class="text-end"><?php echo number_format($item['sale_price'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($item['discount'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>

    </div>
    <?php } ?>
<?php } else { ?>
<div class="text-center text-muted py-4">
    <i class="fa fa-history fa-2x mb-2 d-block"></i>
    <?php echo $this->lang->line('no_return_found'); ?>
</div>
<?php } ?>

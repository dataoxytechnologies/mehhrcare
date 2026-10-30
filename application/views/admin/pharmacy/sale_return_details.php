<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<?php if (!empty($items)) { ?>
<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('returned_medicines'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive rounded overflow-hidden border border-light-subtle">
            <table class="table table-sm table-hover sh-tests-table mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('medicine_name'); ?></th>
                        <th><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th><?php echo $this->lang->line('batch_no'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                        <th class="text-end"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo html_escape($item['medicine_name']); ?></td>
                        <td><?php echo html_escape($item['category_name']); ?></td>
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
</div>
<?php } else { ?>
<div class="text-center text-muted py-4">
    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
    <?php echo $this->lang->line('no_record_found'); ?>
</div>
<?php } ?>

<?php
$currency_symbol  = $this->customlib->getHospitalCurrencyFormat();
$net_amount       = isset($purchase['net_amount']) ? (float)$purchase['net_amount'] : 0;
$already_returned = isset($returned_amount) ? (float)$returned_amount : 0;
$remaining        = $net_amount - $already_returned;
?>
<input type="hidden" id="return_supplier_bill_basic_id" value="<?php echo $purchase['id']; ?>">
<input type="hidden" id="return_supplier_id"            value="<?php echo $purchase['supplier_id']; ?>">

<?php if ($already_returned > 0) { ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_return'); ?></span>
    </div>
    <div class="p-2">
        <div class="row text-center">
            <div class="col-sm-4">
                <small class="text-muted"><?php echo $this->lang->line('total'); ?></small><br>
                <strong><?php echo $currency_symbol . ' ' . number_format($net_amount, 2); ?></strong>
            </div>
            <div class="col-sm-4">
                <small class="text-muted"><?php echo $this->lang->line('already_returned'); ?></small><br>
                <strong class="text-danger"><?php echo $currency_symbol . ' ' . number_format($already_returned, 2); ?></strong>
            </div>
            <div class="col-sm-4">
                <small class="text-muted"><?php echo $this->lang->line('remaining'); ?></small><br>
                <strong class="text-success"><?php echo $currency_symbol . ' ' . number_format($remaining, 2); ?></strong>
            </div>
        </div>
    </div>
</div>
<?php } ?>

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
                <th><?php echo $this->lang->line('expiry_date'); ?></th>
                <th class="text-end"><?php echo $this->lang->line('purchase_price') . ' (' . $currency_symbol . ')'; ?></th>
                <th class="text-end"><?php echo $this->lang->line('available_qty'); ?></th>
                <th class="text-end"><?php echo $this->lang->line('return_qty'); ?><small class="text-danger"> *</small></th>
                <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($batches)) { ?>
                <?php foreach ($batches as $i => $b) { ?>
                    <tr>
                        <td>
                            <?php echo html_escape($b['medicine_name']); ?>
                            <input type="hidden" name="batch_id[]"       value="<?php echo $b['id']; ?>">
                            <input type="hidden" name="pharmacy_id[]"    value="<?php echo $b['pharmacy_id']; ?>">
                            <input type="hidden" name="batch_no[]"       value="<?php echo html_escape($b['batch_no']); ?>">
                            <input type="hidden" name="purchase_price[]" value="<?php echo $b['purchase_price']; ?>">
                            <input type="hidden" name="available_qty[]"  value="<?php echo $b['available_quantity']; ?>">
                        </td>
                        <td><?php echo html_escape($b['batch_no']); ?></td>
                        <td><?php echo $b['expiry'] ? date('M/Y', strtotime($b['expiry'])) : '-'; ?></td>
                        <td class="text-end"><?php echo number_format($b['purchase_price'], 2); ?></td>
                        <td class="text-end"><?php echo $b['available_quantity']; ?></td>
                        <td class="text-end">
                            <input type="number" name="return_qty[]"
                                id="return_qty_<?php echo $i; ?>"
                                class="form-control form-control-sm text-center return_qty_input sh-qty-input"
                                data-index="<?php echo $i; ?>"
                                data-price="<?php echo $b['purchase_price']; ?>"
                                data-available="<?php echo $b['available_quantity']; ?>"
                                value="0" min="0" max="<?php echo $b['available_quantity']; ?>">
                        </td>
                        <td class="text-end">
                            <span id="row_amount_<?php echo $i; ?>">0.00</span>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7" class="text-center"><?php echo $this->lang->line('no_data_available'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <?php if (!empty($batches)) { ?>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</strong></td>
                <td class="text-end"><strong id="return_total_amount">0.00</strong></td>
            </tr>
        </tfoot>
        <?php } ?>
    </table>
</div>
    </div>
</div>

<script>
$(document).on('input', '.return_qty_input', function () {
    var idx       = $(this).data('index');
    var price     = parseFloat($(this).data('price'));
    var available = parseInt($(this).data('available'));
    var qty       = parseInt($(this).val()) || 0;

    if (qty > available) { qty = available; $(this).val(qty); }
    if (qty < 0)         { qty = 0;         $(this).val(0); }

    $('#row_amount_' + idx).text((qty * price).toFixed(2));

    var total = 0;
    $('.return_qty_input').each(function () {
        total += (parseInt($(this).val()) || 0) * parseFloat($(this).data('price'));
    });
    $('#return_total_amount').text(total.toFixed(2));
});
</script>

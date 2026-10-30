<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$prefix_bill     = isset($prefix_bill)   ? $prefix_bill   : '';
$prefix_return   = isset($prefix_return) ? $prefix_return : '';
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('sale_return_bills'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <a href="<?php echo base_url(); ?>admin/pharmacy/bill" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back_to_pharmacy_bills'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('sale_return_bills'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example" data-export-title="<?= $this->lang->line('sale_return_bills') ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('return_no'); ?></th>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('generated_by'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end"><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end"><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($saleReturnBills)) : ?>
                                        <?php foreach ($saleReturnBills as $row) : ?>
                                        <tr>
                                            <td><?php echo html_escape($prefix_return . $row['return_no']); ?></td>
                                            <td><?php echo html_escape($prefix_bill . $row['pharmacy_bill_basic_id']); ?></td>
                                            <td><?php echo html_escape($this->customlib->YYYYMMDDTodateFormat($row['date'])); ?></td>
                                            <td><?php echo html_escape($row['patient_name']); ?><?php if (!empty($row['patient_id'])) : ?> (<?php echo (int)$row['patient_id']; ?>)<?php endif; ?></td>
                                            <td><?php echo html_escape($row['returned_by_name']); ?><?php if (!empty($row['returned_by_employee_id'])) : ?> (<?php echo html_escape($row['returned_by_employee_id']); ?>)<?php endif; ?></td>
                                            <td class="text-end"><?php echo number_format($row['total'], 2); ?></td>
                                            <td class="text-end"><?php echo number_format($row['discount'], 2); ?></td>
                                            <td class="text-end"><?php echo number_format($row['tax'], 2); ?></td>
                                            <td class="text-end"><?php echo number_format($row['net_amount'], 2); ?></td>
                                            <td class="text-end">
                                                <a href="#" onclick="viewReturnItems(<?php echo (int)$row['id']; ?>); return false;" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_items'); ?>"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr><td colspan="10" class="text-center"><?php echo $this->lang->line('no_record_found'); ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- ========== RETURN ITEMS MODAL ========== -->
<div class="modal fade sh-modal sh-modal-accent" id="returnItemsModal" tabindex="-1" aria-labelledby="returnItemsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnItemsLabel"><?php echo $this->lang->line('returned_medicines'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div id="returnItemsContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ========== /RETURN ITEMS MODAL ========== -->

<script type="text/javascript">
    function viewReturnItems(id) {
        $('#returnItemsContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
        shModal('returnItemsModal').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/getSaleReturnDetails/' + id,
            type: 'GET',
            success: function (data) {
                $('#returnItemsContent').html(data);
            },
            error: function () {
                $('#returnItemsContent').html('<p class="text-danger text-center">Error loading items.</p>');
            }
        });
    }
</script>

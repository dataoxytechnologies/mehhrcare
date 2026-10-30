<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$canViewStock    = $this->rbac->hasPrivilege('medicine', 'can_view');
$canDelStock     = $this->rbac->hasPrivilege('medicine', 'can_delete');
$canViewBad      = $this->rbac->hasPrivilege('medicine_bad_stock', 'can_view');
$canDelBad       = $this->rbac->hasPrivilege('medicine_bad_stock', 'can_delete');
$stockCount      = !empty($result)         ? count($result)         : 0;
$badCount        = !empty($badstockresult) ? count($badstockresult) : 0;
?>
<div class="sh-form-card sh-stock-panel mb-0 overflow-hidden">
    <div class="sh-card-header sh-stock-tabs-header">
        <ul class="nav nav-tabs sh-stock-tabs mb-0" id="stockTabs" role="tablist">
            <?php if ($canViewStock) { ?>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" href="#current_stock" data-bs-toggle="tab" data-bs-target="#current_stock" role="tab" aria-controls="current_stock" aria-selected="true">
                        <i class="fa fa-cubes me-1"></i><?php echo $this->lang->line('stock'); ?>
                        <span class="sh-tab-count"><?php echo $stockCount; ?></span>
                    </a>
                </li>
            <?php } if ($canViewBad) { ?>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo $canViewStock ? '' : 'active'; ?>" href="#bad_stock" data-bs-toggle="tab" data-bs-target="#bad_stock" role="tab" aria-controls="bad_stock" aria-selected="<?php echo $canViewStock ? 'false' : 'true'; ?>">
                        <i class="fa fa-exclamation-triangle me-1"></i><?php echo $this->lang->line('bad_stock'); ?>
                        <span class="sh-tab-count"><?php echo $badCount; ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="tab-content sh-stock-tab-content p-2">
        <?php if ($canViewStock) { ?>
            <div class="tab-pane fade show active" id="current_stock" role="tabpanel">
                <?php if ($stockCount > 0) { ?>
                    <div class="table-responsive rounded overflow-hidden border border-light-subtle">
                        <table class="table table-sm table-hover sh-tests-table mb-0" id="detail" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('inward_date'); ?></th>
                                    <th><?php echo $this->lang->line('batch_no'); ?></th>
                                    <th><?php echo $this->lang->line('purchase_no'); ?></th>
                                    <th><?php echo $this->lang->line('expiry_date'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('packing_qty'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('purchase_rate') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('mrp') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('sale_price') . " (" . $currency_symbol . ")"; ?></th>
                                    <?php if ($canDelStock) { ?>
                                        <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $detail) { ?>
                                    <tr>
                                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($detail->inward_date)); ?></td>
                                        <td><?php echo html_escape($detail->batch_no); ?></td>
                                        <td><?php echo html_escape($this->customlib->getSessionPrefixByType('purchase_no') . $detail->purchase_no); ?></td>
                                        <td><?php echo $this->customlib->getMedicine_expire_month($detail->expiry); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->packing_qty); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->purchase_price); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->amount); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->quantity); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->mrp); ?></td>
                                        <td class="text-end"><?php echo html_escape($detail->sale_rate); ?></td>
                                        <?php if ($canDelStock) { ?>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_batch('<?php echo (int)$detail->id; ?>', '<?php echo (int)$detail->pharmacy_id; ?>'); return false;"><i class="fa fa-trash"></i></a>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="sh-empty-state text-center text-muted py-4">
                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                        <?php echo $this->lang->line('no_record_found'); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } if ($canViewBad) { ?>
            <div class="tab-pane fade <?php echo $canViewStock ? '' : 'show active'; ?>" id="bad_stock" role="tabpanel">
                <?php if ($badCount > 0) { ?>
                    <div class="table-responsive rounded overflow-hidden border border-light-subtle">
                        <table class="table table-sm table-hover sh-tests-table mb-0" id="bad_stock_detail" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('outward_date'); ?></th>
                                    <th><?php echo $this->lang->line('batch_no'); ?></th>
                                    <th><?php echo $this->lang->line('expiry_date'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('quantity'); ?></th>
                                    <?php if ($canDelBad) { ?>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($badstockresult as $stockdetail) { ?>
                                    <tr>
                                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($stockdetail->outward_date)); ?></td>
                                        <td><?php echo html_escape($stockdetail->batch_no); ?></td>
                                        <td><?php echo $this->customlib->getMedicine_expire_month($stockdetail->expiry_date); ?></td>
                                        <td class="text-end"><?php echo html_escape($stockdetail->quantity); ?></td>
                                        <?php if ($canDelBad) { ?>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_badstock('<?php echo (int)$stockdetail->id; ?>', '<?php echo (int)$stockdetail->pharmacy_id; ?>', '<?php echo (int)$stockdetail->medicine_batch_details_id; ?>'); return false;"><i class="fa fa-trash"></i></a>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="sh-empty-state text-center text-muted py-4">
                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                        <?php echo $this->lang->line('no_record_found'); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var dtOptions = {
            dom: '<"sh-dt-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2"<"sh-dt-search"f><"sh-dt-buttons"B>>rtip',
            pageLength: 10,
            lengthChange: false,
            language: { search: '', searchPlaceholder: '<?php echo $this->lang->line('search'); ?>' },
            buttons: [
                { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i>', titleAttr: 'Excel', className: 'btn btn-sm btn-light', title: $('.download_label').html(), exportOptions: { columns: ['thead th:not(.noExport)'] } },
                { extend: 'pdfHtml5',   text: '<i class="fa fa-file-pdf-o"></i>',   titleAttr: 'PDF',   className: 'btn btn-sm btn-light', title: $('.download_label').html(), exportOptions: { columns: ['thead th:not(.noExport)'] } },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    className: 'btn btn-sm btn-light',
                    title: $('.download_label').html(),
                    customize: function (win) {
                        $(win.document.body).css('font-size', '10pt');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    },
                    exportOptions: { columns: ['thead th:not(.noExport)'] }
                },
                { extend: 'colvis', text: '<i class="fa fa-columns"></i>', titleAttr: 'Columns', className: 'btn btn-sm btn-light', postfixButtons: ['colvisRestore'] }
            ]
        };

        if ($('#detail').length) {
            $('#detail').DataTable(dtOptions);
        }
        if ($('#bad_stock_detail').length) {
            $('#bad_stock_detail').DataTable(dtOptions);
        }

        if (window.bootstrap && bootstrap.Tooltip) {
            $('#tabledata [data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (!existing) { new bootstrap.Tooltip(this); }
            });
        }
    });

    function delete_batch(id, pharmacy_id) {
        if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/delete_medicine_batch/' + id,
                type: "POST",
                data: {opdid: ''},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 'success') {
                        viewDetail(pharmacy_id, id);
                    }
                }
            });
        }
    }

    function delete_badstock(id, pharmacy_id, medicine_batch_details_id) {
        if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/deleteBadStock/' + id + '/' + medicine_batch_details_id,
                type: "POST",
                data: {opdid: ''},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 'success') {
                        viewDetail(pharmacy_id, id);
                        $('.ajaxlist').DataTable().ajax.reload();
                    }
                }
            });
        }
    }
</script>

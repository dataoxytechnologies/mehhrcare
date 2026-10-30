<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php $this->load->view('admin/report/_pharmacy'); ?>
                    <div class="card-header ptbnull"></div>
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('expiry_medicine_report'); ?></h3>
                        <button type="button" id="legendInfoBtn" class="float-end btn-legend-info" title="<?php echo $this->lang->line('remaining_days'); ?>">
                            <i class="fa fa-info"></i>
                        </button>
                    </div>

                    <form id="form1" action="" method="post">
                        <div class="card-body row pb-0">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('report_type'); ?></label>
                                    <select class="form-control" name="filter_type" id="filterTypeSelect" onchange="toggleFilterType(this.value)">
                                        <option value="near_expiry"><?php echo $this->lang->line('near_expiry'); ?></option>
                                        <option value="expired_medicines"><?php echo $this->lang->line('expired_medicines'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3" id="search_type_container">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('search_type'); ?></label> <small class="req"> *</small>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($searchlist as $key => $search) { ?>
                                            <option value="<?php echo $key ?>" <?php if ((isset($search_type)) && ($search_type == $key)) echo "selected"; ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 d-none" id="fromdate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_from'); ?></label> <small class="req"> *</small>
                                    <input id="date_from" name="date_from" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 d-none" id="todate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_to'); ?></label> <small class="req"> *</small>
                                    <input id="date_to" name="date_to" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('medicine_category'); ?></label>
                                    <select class="form-control select2" name="medicine_category">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('supplier'); ?></label>
                                    <select name="supplier" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select_supplier'); ?></option>
                                        <?php foreach ($supplierCategory as $dkey => $dvalue) { ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($supplier_select)) && ($supplier_select == $dvalue["id"])) echo "selected"; ?>><?php echo $dvalue["supplier"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-auto d-flex align-items-end ms-auto">
                                <div class="mb-3">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                        <i class="fa fa-search"></i>
                                        <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card-body table-responsive pt-0">
                        <div class="download_label"><?php echo $this->lang->line('expiry_medicine_report'); ?></div>
                            <table id="expiryMedicineTable" class="table table-striped table-bordered table-hover" data-export-title="<?php echo $this->lang->line('expiry_medicine_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('medicine_name'); ?></th>
                                        <th><?php echo $this->lang->line('medicine_category'); ?></th>
                                        <th><?php echo $this->lang->line('medicine_group'); ?></th>
                                        <th><?php echo $this->lang->line('company_name'); ?></th>
                                        <th><?php echo $this->lang->line('supplier'); ?></th>
                                        <th><?php echo $this->lang->line('pharmacy_purchase_no'); ?></th>
                                        <th><?php echo $this->lang->line('batch_no'); ?></th>
                                        <th><?php echo $this->lang->line('inward_date'); ?></th>
                                        <th><?php echo $this->lang->line('expire_date'); ?></th>
                                        <th class="text-center"><?php echo $this->lang->line('remaining_days'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('purchase_qty'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('available_quantity'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('purchase_rate'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end">Stock Value (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                    </div>

                </div>
            </div>
        </div>

<!-- Legend Popover Card -->
<div id="legendPopover" class="legend-popover">
    <div class="legend-popover-title">
        <i class="fa fa-info-circle legend-icon"></i><?php echo $this->lang->line('remaining_days'); ?>
    </div>
    <div class="legend-popover-body">
        <table>
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('remaining_days'); ?></th>
                    <th><?php echo $this->lang->line('status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge bg-danger">&lt; 0 <?php echo $this->lang->line('days'); ?></span></td>
                    <td><span class="status-expired">&#9679; <?php echo $this->lang->line('expired'); ?></span></td>
                </tr>
                <tr>
                    <td><span class="badge bg-warning text-dark">0 – 180 <?php echo $this->lang->line('days'); ?></span></td>
                    <td><span class="status-near">&#9679; <?php echo $this->lang->line('near_expiry'); ?></span></td>
                </tr>
                <tr>
                    <td><span class="badge bg-success">&gt; 180 <?php echo $this->lang->line('days'); ?></span></td>
                    <td><span class="status-safe">&#9679; <?php echo $this->lang->line('safe'); ?></span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
$(function () {
    $('.select2').select2();

    // Position and toggle legend popover
    $('#legendInfoBtn').on('click', function (e) {
        e.stopPropagation();
        var $pop = $('#legendPopover');
        if ($pop.is(':visible')) {
            $pop.hide();
            return;
        }
        var btnOffset = $(this).offset();
        var btnH      = $(this).outerHeight();
        $pop.css({
            top  : btnOffset.top + btnH + 6,
            left : btnOffset.left - $pop.outerWidth() + $(this).outerWidth()
        }).show();
    });

    // Close on outside click
    $(document).on('click.legendPopover', function (e) {
        if (!$(e.target).closest('#legendPopover, #legendInfoBtn').length) {
            $('#legendPopover').hide();
        }
    });
});
</script>

<script type="text/javascript">
function showdate(value) {
    if (value == 'period') {
        $('#fromdate').removeClass('d-none');
        $('#todate').removeClass('d-none');
    } else {
        $('#fromdate').addClass('d-none');
        $('#todate').addClass('d-none');
    }
}

function toggleFilterType(type) {
    $('#error_search_type').html('');
}

</script>

<script>
$(document).ready(function () {

    var expTable = null;

    function loadExpTable(param) {
        var exportTitle = $('#expiryMedicineTable').data('exportTitle');

        if ($.fn.DataTable.isDataTable('#expiryMedicineTable')) {
            $('#expiryMedicineTable').DataTable().destroy();
        }

        expTable = $('#expiryMedicineTable').DataTable({
            processing : true,
            serverSide : true,
            searching  : true,
            dom        : '<"dt-toolbar"f<"dt-toolbar-right"lB>>r<t>ip',
            lengthMenu : [[100, -1], [100, 'All']],
            language   : { processing: '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>', sLengthMenu: '_MENU_' },
            buttons    : [
                { extend: 'copy',  text: '<i class="fa fa-files-o"></i>',      titleAttr: 'Copy',  title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
                { extend: 'excel', text: '<i class="fa fa-file-excel-o"></i>',  titleAttr: 'Excel', title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
                { extend: 'csv',   text: '<i class="fa fa-file-text-o"></i>',   titleAttr: 'CSV',   title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
                { extend: 'pdf',   text: '<i class="fa fa-file-pdf-o"></i>',    titleAttr: 'PDF',   title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
                { extend: 'print', text: '<i class="fa fa-print"></i>',         titleAttr: 'Print', title: exportTitle,
                  customize: function (win) {
                      $(win.document.body).find('th,td').css('text-align', 'left');
                      $(win.document.body).find('table').css('font-size', '14px');
                      $(win.document.body).find('h1').css('text-align', 'center');
                  },
                  exportOptions: { columns: ['thead th:not(.noExport)'] }
                }
            ],
            ajax: {
                url  : '<?php echo base_url(); ?>admin/expmedicine/expmedicinereports',
                type : 'POST',
                data : function (d) {
                    $.each(param, function (key, value) { d[key] = value; });
                    d.<?php echo $this->security->get_csrf_token_name(); ?> = '<?php echo $this->security->get_csrf_hash(); ?>';
                }
            },
            columnDefs: [
                { targets: [9],                className: 'text-center' },
                { targets: [10, 11, 12, 13, 14], className: 'text-end' }
            ],
            order: [[8, 'desc']],
            drawCallback: function () {
                var api  = this.api();
                var json = api.ajax.json();
                var $tbody = $(api.table().body());
                $tbody.find('tr.exp-total-row').remove();
                if (json && typeof json.total_amount !== 'undefined') {
                    $tbody.append(
                        '<tr class="exp-total-row fw-bold" >' +
                        '<td><?php echo $this->lang->line('total_amount'); ?></td>' +
                        '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>' +
                        '<td class="text-end">' + json.total_packing_qty + '</td>' +
                        '<td class="text-end">' + json.total_available_qty + '</td>' +
                        '<td class="text-end"><?php echo $currency_symbol; ?>' + json.total_purchase_rate + '</td>' +
                        '<td class="text-end"><?php echo $currency_symbol; ?>' + json.total_stock_value   + '</td>' +
                        '<td class="text-end"><?php echo $currency_symbol; ?>' + json.total_amount        + '</td>' +
                        '</tr>'
                    );
                }
            }
        });
    }

    $('#form1').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url         : '<?php echo base_url(); ?>admin/expmedicine/checkvalidation',
            type        : 'POST',
            data        : formData,
            dataType    : 'json',
            contentType : false,
            cache       : false,
            processData : false,
            success     : function (data) {
                if (data.status == 'fail') {
                    $.each(data.error, function (key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $('#error_search_type').html('');
                    loadExpTable(data.param);
                }
            }
        });
    });

});
</script>

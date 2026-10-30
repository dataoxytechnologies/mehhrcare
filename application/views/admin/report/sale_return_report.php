<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$date_format     = $this->customlib->getHospitalDateFormat();
$today           = $this->customlib->YYYYMMDDTodateFormat(date('Y-m-d'));
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php $this->load->view('admin/report/_pharmacy'); ?>
                    <div class="card-header ptbnull"></div>
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('sale_return_report'); ?></h3>
                    </div>

                    <!-- Filter Form -->
                    <form id="saleReturnFilterForm">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="card-body row pb-0">

                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('time_duration'); ?></label>
                                    <select name="search_type" id="search_type" class="form-control select2" onchange="toggleDateRange(this.value)">
                                        <option value="all_time"><?php echo $this->lang->line('all_time'); ?></option>
                                        <option value="today"><?php echo $this->lang->line('today'); ?></option>
                                        <option value="this_week"><?php echo $this->lang->line('this_week'); ?></option>
                                        <option value="last_week"><?php echo $this->lang->line('last_week'); ?></option>
                                        <option value="this_month" selected><?php echo $this->lang->line('this_month'); ?></option>
                                        <option value="last_month"><?php echo $this->lang->line('last_month'); ?></option>
                                        <option value="this_year"><?php echo $this->lang->line('this_year'); ?></option>
                                        <option value="period"><?php echo $this->lang->line('period'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2" id="date_from_wrap" class="d-none">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input type="text" name="date_from" id="date_from" class="form-control datepicker" value="<?php echo $today; ?>" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2" id="date_to_wrap" class="d-none">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input type="text" name="date_to" id="date_to" class="form-control datepicker" value="<?php echo $today; ?>" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('bill_no'); ?></label>
                                    <input type="text" name="bill_no" id="bill_no" class="form-control" placeholder="<?php echo $this->lang->line('sale_bill_no'); ?>">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('return_bill_no'); ?></label>
                                    <input type="text" name="return_no" id="return_no" class="form-control" placeholder="<?php echo $this->lang->line('return_bill_no'); ?>">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('patient_name'); ?></label>
                                    <input type="text" name="patient_name" id="patient_name" class="form-control" placeholder="<?php echo $this->lang->line('patient_name'); ?>">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                        <i class="fa fa-search"></i>
                                        <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                    <!-- Report Table -->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table id="saleReturnTable" class="table table-striped table-bordered table-hover" data-export-title="<?php echo $this->lang->line('sale_return_report'); ?>">
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
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

<!-- Returned Medicines Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="returnItemsModal" tabindex="-1" aria-labelledby="returnItemsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnItemsLabel"><?php echo $this->lang->line('returned_medicines'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="returnItemsContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    $('.select2').select2();

    var prefixBill   = '<?php echo isset($prefix_bill)   ? $prefix_bill   : ''; ?>';
    var prefixReturn = '<?php echo isset($prefix_return) ? $prefix_return : ''; ?>';

    var exportTitle = $('#saleReturnTable').data('exportTitle');
    var saleReturnTable = $('#saleReturnTable').DataTable({
        processing  : true,
        serverSide  : true,
        searching   : true,
        ordering    : true,
        dom         : '<"dt-toolbar"f<"dt-toolbar-right"lB>>r<t>ip',
        lengthMenu  : [[100, -1], [100, 'All']],
        language    : { processing: '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>', sLengthMenu: '_MENU_' },
        buttons     : [
            { extend: 'copy',  text: '<i class="fa fa-files-o"></i>',    titleAttr: 'Copy',  title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
            { extend: 'excel', text: '<i class="fa fa-file-excel-o"></i>',titleAttr: 'Excel', title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
            { extend: 'csv',   text: '<i class="fa fa-file-text-o"></i>', titleAttr: 'CSV',   title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
            { extend: 'pdf',   text: '<i class="fa fa-file-pdf-o"></i>',  titleAttr: 'PDF',   title: exportTitle, exportOptions: { columns: ['thead th:not(.noExport)'] } },
            { extend: 'print', text: '<i class="fa fa-print"></i>',       titleAttr: 'Print', title: exportTitle,
              customize: function(win) {
                $(win.document.body).find('th,td').addClass('display').css('text-align','left');
                $(win.document.body).find('table').addClass('display').css('font-size','14px');
                $(win.document.body).find('h1').css('text-align','center');
              },
              exportOptions: { columns: ['thead th:not(.noExport)'] }
            }
        ],
        ajax: {
            url  : '<?php echo base_url(); ?>admin/report/getsalereturnreportdata',
            type : 'POST',
            data : function(d) {
                d.search_type  = $('#search_type').val();
                d.date_from    = $('#date_from').val();
                d.date_to      = $('#date_to').val();
                d.bill_no      = $('#bill_no').val();
                d.return_no    = $('#return_no').val();
                d.patient_name = $('#patient_name').val();
                d.<?php echo $this->security->get_csrf_token_name(); ?> = '<?php echo $this->security->get_csrf_hash(); ?>';
            }
        },
        columns: [
            { data: 'return_no',               render: function(d) { return '<span class="badge bg-primary" style="display:inline-block;min-width:60px;text-align:center;font-size:12px;font-weight:600;letter-spacing:0.3px;">' + prefixReturn + d + '</span>'; } },
            { data: 'pharmacy_bill_basic_id',  render: function(d) { return prefixBill + d; } },
            { data: 'date',                    defaultContent: '' },
            { data: 'patient_name',            render: function(d, t, row) {
                var id = row.patient_id ? ' (' + row.patient_id + ')' : '';
                return $('<span>').text(d + id).html();
            }},
            { data: 'returned_by_name',        render: function(d, t, row) {
                var emp = row.returned_by_employee_id ? ' (' + row.returned_by_employee_id + ')' : '';
                return $('<span>').text((d || '') + emp).html();
            }},
            { data: 'total',      className: 'text-end', render: function(d) { return parseFloat(d).toFixed(2); } },
            { data: 'discount',   className: 'text-end', render: function(d) { return parseFloat(d).toFixed(2); } },
            { data: 'tax',        className: 'text-end', render: function(d) { return parseFloat(d).toFixed(2); } },
            { data: 'net_amount', className: 'text-end', render: function(d) { return parseFloat(d).toFixed(2); } },
            { data: 'id', className: 'text-end noExport', orderable: false,
              render: function(d) {
                return '<a href="#" onclick="viewReturnItems(' + d + '); return false;" class="btn btn-secondary btn-sm" title="<?php echo $this->lang->line('view_items'); ?>"><i class="fa fa-eye"></i></a>';
              }
            }
        ],
        order: [[0, 'desc']]
    });

    // Search on form submit
    $('#saleReturnFilterForm').on('submit', function(e) {
        e.preventDefault();
        saleReturnTable.ajax.reload();
    });
});

function toggleDateRange(val) {
    if (val === 'period') {
        $('#date_from_wrap, #date_to_wrap').removeClass('d-none');
    } else {
        $('#date_from_wrap, #date_to_wrap').addClass('d-none');
    }
}

function viewReturnItems(id) {
    $('#returnItemsContent').html('<div class="text-center" style="padding:20px;"><i class="fa fa-spinner fa-spin"></i></div>');
    shModal('returnItemsModal').show();
    $.ajax({
        url  : '<?php echo base_url(); ?>admin/pharmacy/getSaleReturnDetails/' + id,
        type : 'GET',
        success: function(data) {
            $('#returnItemsContent').html(data);
        },
        error: function() {
            $('#returnItemsContent').html('<p class="text-danger text-center">Error loading items.</p>');
        }
    });
}
</script>

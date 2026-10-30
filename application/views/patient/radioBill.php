<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('radiology_test_reports'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('radiology_test_reports'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example" id="testreport">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('reporting_date'); ?></th>
                            <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                            <th><?php echo $this->lang->line('note'); ?></th>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) { ?>
                                <th><?php echo ucfirst($fields_value->name); ?></th>
                            <?php } } ?>
                            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result)) {
                            foreach ($result as $detail) {
                                $balance_amount = ($detail->net_amount - $detail->paid_amount);
                                $tax_percentage = ($detail->total - $detail->discount) != 0
                                    ? ($detail->tax * 100) / ($detail->total - $detail->discount) : 0;
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('radiology_billing') . $detail->id; ?></td>
                            <td><?php echo html_escape($detail->case_reference_id); ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($detail->date); ?></td>
                            <td><?php echo composeStaffNameByString($detail->name, $detail->surname, $detail->employee_id); ?></td>
                            <td><?php echo html_escape($detail->note); ?></td>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) {
                                    $display_field = $detail->{"$fields_value->name"};
                                    if ($fields_value->type == 'link') {
                                        $display_field = '<a href="' . $detail->{"$fields_value->name"} . '" target="_blank">' . $detail->{"$fields_value->name"} . '</a>';
                                    }
                            ?>
                            <td><?php echo $display_field; ?></td>
                            <?php } } ?>
                            <td class="text-end"><?php echo number_format((float)$detail->total, 2); ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->discount, 2) . ' (' . number_format((float)$detail->discount_percentage, 2) . '%)'; ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->tax, 2) . ' (' . number_format((float)$tax_percentage, 2) . '%)'; ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->net_amount, 2); ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->paid_amount, 2); ?></td>
                            <td class="text-end"><?php echo number_format((float)$balance_amount, 2); ?></td>
                            <td class="text-end">
                                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary view_payment"
                                       data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_payments'); ?>"
                                       data-record-id="<?php echo $detail->id; ?>" data-module_type="radiology">
                                        <i class="fa fa-money"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary view_detail"
                                       data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_reports'); ?>"
                                       data-record-id="<?php echo $detail->id; ?>">
                                        <i class="fa fa-reorder"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('pay'); ?>"
                                            onclick="payModal('<?php echo $detail->id; ?>','<?php echo $balance_amount; ?>')">
                                        <?php echo $this->lang->line('pay'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div id="action_detail_report_modal" class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Test Report Modal -->
<div class="modal fade sh-modal sh-modal-branded" id="editTestReportModal" tabindex="-1" aria-labelledby="editTestReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestReportModalLabel"><?php echo $this->lang->line('edit_test_report'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form id="updatetest" enctype="multipart/form-data" accept-charset="utf-8" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="report_id">
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('customer_type'); ?></label>
                            <input class="form-control text-capitalize" type="text" name="customer_type" id="customer_types" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('patient_name'); ?> <small class="req"> *</small></label>
                            <input type="text" name="patient_name" class="form-control" id="edit_patient_name">
                            <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('reporting_date'); ?></label>
                            <input type="text" id="edit_report_date" name="reporting_date" class="form-control date">
                            <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('referral') . ' ' . $this->lang->line('doctor'); ?></label>
                            <select class="form-select select2" name="consultant_doctor" id="edit_consultant_doctor">
                                <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($doctors as $dvalue) { ?>
                                <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['name'] . ' ' . $dvalue['surname']; ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                            <textarea name="description" id="edit_description" class="form-control"></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('test') . ' ' . $this->lang->line('report'); ?></label>
                            <input type="file" class="filestyle form-control" data-height="40" name="radiology_report">
                            <span class="text-danger"><?php echo form_error('pathology_report'); ?></span>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('charge') . ' ' . $this->lang->line('category'); ?></label>
                            <input type="text" class="form-control" readonly id="charge_category_html">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('code'); ?></label>
                            <input type="text" readonly class="form-control" id="code_html">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('standard') . ' ' . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                            <input type="text" readonly class="form-control" id="charge_html">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label"><?php echo $this->lang->line('apply_charge')  . ' (' . $currency_symbol . ')'; ?></label>
                            <input type="text" name="apply_charge" class="form-control" id="apply_charge">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="updatetestbtn" class="btn btn-info">
                        <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Bill (sub) Modal -->
<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id="edit_deletebill" class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body" id="reportdata"></div>
        </div>
    </div>
</div>

<!-- View Report (sub) Modal -->
<div class="modal fade sh-modal sh-modal-branded" id="viewModalReport" tabindex="-1" aria-labelledby="viewModalReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalReportLabel"><?php echo $this->lang->line('report_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id="edit_deletereport" class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body" id="reportdatareport"></div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="payMoney" tabindex="-1" aria-labelledby="payMoneyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payMoneyLabel"><?php echo $this->lang->line('make_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payment_form" class="modal_payment" method="POST">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-money me-1 opacity-75"></i><?php echo $this->lang->line('payment_details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="amount_total_paid" class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)</label><small class="req"> *</small>
                                        <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid">
                                        <input type="hidden" class="form-control" name="net_amount" id="net_amount">
                                        <span id="deposit_amount_error" class="text text-danger"></span>
                                        <input type="hidden" name="payment_for" value="radiology">
                                        <input type="hidden" id="bill_id_modal" name="id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" type="button" class="btn btn-info payment_radio"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('add'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- All Payments Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="allpayments" tabindex="-1" aria-labelledby="allpaymentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allpaymentsLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <div id="allpayments_print" class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="allpayments_result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function holdModal(modalId) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId), {backdrop: 'static', keyboard: false}).show();
    }

    function viewDetail(id, radiology_id) {
        $('#reportdata, #edit_deletebill').html('');
        $('#viewModal').addClass('modal_loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getBillDetailsRadio/' + id + '/' + radiology_id,
            type: 'GET',
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + "," + radiology_id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                $('#viewModal').removeClass('modal_loading');
            },
            complete: function () {
                $('#viewModal').removeClass('modal_loading');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static'}).show();
            }
        });
    }

    $(document).on('click', '.view_detail', function () {
        var id = $(this).data('recordId');
        PatientRadiologyDetails(id, $(this));
    });

    function PatientRadiologyDetails(id, btn_obj) {
        var $btn = btn_obj;
        $btn.prop('disabled', true);
        $('#reportbilldata').html('');
        $('#viewDetailReportModal').addClass('modal_loading');
        $.ajax({
            url: base_url + 'patient/dashboard/getPatientRadiologyDetails',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                $('#reportbilldata').html(data.page);
                $('#action_detail_report_modal').html(data.actions);
                $('#viewDetailReportModal').removeClass('modal_loading');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDetailReportModal'), {backdrop: 'static'}).show();
            },
            error: function () {
                alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                $('#viewDetailReportModal').removeClass('modal_loading');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('#viewDetailReportModal').removeClass('modal_loading');
            }
        });
    }

    $(document).on('click', '.print_report', function () {
        var $btn = $(this);
        var id = $btn.data('recordId');
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/printPatientRadiologyReportDetail',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                popup(data.page);
            },
            error: function () {
                alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.print_bill', function () {
        var $btn = $(this);
        var id = $btn.data('recordId');
        var normalText = $btn.find('.normal-text');
        var loadingText = $btn.find('.loading-text');
        if (normalText.length) { normalText.hide(); loadingText.show(); }
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/PrintBillDetailsRadiology',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                popup(data.page);
            },
            error: function () {
                alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
            },
            complete: function () {
                if (normalText.length) { normalText.show(); loadingText.hide(); }
                $btn.prop('disabled', false);
            }
        });
    });

    function payModal(bill_id, balance_amount) {
        $('#bill_id_modal').val(bill_id);
        $('#amount_total_paid').val(parseFloat(balance_amount).toFixed(2));
        $('#net_amount').val(balance_amount);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payMoney'), {backdrop: 'static'}).show();
    }

    function viewDetailReport(id, radiology_id) {
        $('#reportdatareport, #edit_deletereport').html('');
        $('#viewModalReport').addClass('modal_loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getReportDetailsRadio/' + id + '/' + radiology_id,
            type: 'GET',
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + "," + radiology_id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                $('#viewModalReport').removeClass('modal_loading');
            },
            complete: function () {
                $('#viewModalReport').removeClass('modal_loading');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModalReport'), {backdrop: 'static'}).show();
            }
        });
    }

    $('#pay_button').click(function () {
        var formdata = new FormData($('#payment_form')[0]);
        $.ajax({
            url: base_url + 'patient/pay/checkvalidate',
            type: 'POST',
            data: formdata,
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == 'fail') {
                    var message = '';
                    $.each(data.error, function (index, value) { message += value; });
                    errorMsg(message);
                } else {
                    window.location.replace(base_url + 'patient/pay');
                }
            }
        });
    });

    $(document).on('click', '.view_payment', function () {
        var record_id = $(this).data('recordId');
        var module_type = $(this).data('module_type');
        getPayments(record_id, module_type);
    });

    function getPayments(record_id, module_type) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getpayment',
            type: 'POST',
            data: {'id': record_id, module_type: module_type},
            dataType: 'JSON',
            success: function (data) {
                $('#allpayments_result').html(data.page);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('allpayments'), {backdrop: 'static', keyboard: false}).show();
            }
        });
    }

    $(document).on('click', '.print_trans', function () {
        var $this = $(this);
        var record_id = $this.data('recordId');
        var module_type = $this.data('moduleType');
        $this.prop('disabled', true);
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/printbilltransaction',
            type: 'POST',
            data: {'id': record_id, 'module_type': module_type},
            dataType: 'json',
            success: function (res) {
                popup(res.page);
            },
            error: function () {
                alert('Error occurred. Please try again.');
            },
            complete: function () {
                $this.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.print_parameter', function () {
        var $btn = $(this);
        var id = $btn.data('recordId');
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/printradiotestparameterdetail',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                popup(data.page);
            },
            error: function () {
                alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

</script>

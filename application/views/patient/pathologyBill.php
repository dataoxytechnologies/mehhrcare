<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('pathology_test_reports'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('pathology_test_reports'); ?></div>
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
                                <th><?php echo html_escape($fields_value->name); ?></th>
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
                                $balance_amount = $detail->net_amount - $detail->paid_amount;
                                $tax_percentage = ($detail->total - $detail->discount) != 0
                                    ? ($detail->tax * 100) / ($detail->total - $detail->discount) : 0;
                                $balance_fmt = number_format((float)$balance_amount, 2, '.', '');
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('pathology_billing') . $detail->id; ?></td>
                            <td><?php echo html_escape($detail->case_reference_id); ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($detail->date); ?></td>
                            <td><?php echo html_escape($detail->doctor_name); ?></td>
                            <td><?php echo html_escape($detail->note); ?></td>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) { ?>
                                <td><?php echo html_escape($detail->{$fields_value->name}); ?></td>
                            <?php } } ?>
                            <td class="text-end"><?php echo number_format((float)$detail->total, 2, '.', ''); ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->discount, 2, '.', ''); ?> (<?php echo number_format((float)$detail->discount_percentage, 2, '.', ''); ?>%)</td>
                            <td class="text-end"><?php echo number_format((float)$detail->tax, 2, '.', ''); ?> (<?php echo number_format((float)$tax_percentage, 2, '.', ''); ?>%)</td>
                            <td class="text-end"><?php echo number_format((float)$detail->net_amount, 2, '.', ''); ?></td>
                            <td class="text-end"><?php echo number_format((float)$detail->paid_amount, 2, '.', ''); ?></td>
                            <td class="text-end"><?php echo $balance_fmt; ?></td>
                            <td class="text-end">
                                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary view_payment"
                                       data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_payments'); ?>"
                                       data-record-id="<?php echo $detail->id; ?>" data-module_type="pathology">
                                        <i class="fa fa-money"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary view_detail"
                                       data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_reports'); ?>"
                                       data-record-id="<?php echo $detail->id; ?>">
                                        <i class="fa fa-reorder"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('pay'); ?>"
                                            onclick="payModal('<?php echo $detail->id; ?>','<?php echo $balance_fmt; ?>')">
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

<!-- View Bill/Report Details Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="action_detail_report_modal" class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportbilldata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Test Report Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="editTestReportModal" tabindex="-1" aria-labelledby="editTestReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestReportModalLabel"><?php echo $this->lang->line('edit_test_report'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updatetest" enctype="multipart/form-data" method="post">
                <input type="hidden" name="id" id="report_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('customer_type'); ?></label>
                                        <input class="form-control" type="text" name="customer_type" id="customer_types" readonly class="text-capitalize">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('opd_ipd_no'); ?></label>
                                        <input type="text" name="" class="form-control" id="">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('patient_name'); ?> <small class="req"> *</small></label>
                                        <input type="text" name="patient_name" class="form-control" id="edit_patient_name">
                                        <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('reporting_date'); ?></label>
                                        <input type="text" id="edit_report_date" name="reporting_date" class="form-control date">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?php echo $this->lang->line('referral_doctor'); ?></label>
                                        <select class="form-select select2" name="consultant_doctor" id="edit_consultant_doctor">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($doctors as $dvalue) { ?>
                                            <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['name'] . ' ' . $dvalue['surname']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" id="edit_description" class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?php echo $this->lang->line('test_report'); ?></label>
                                        <input type="file" class="form-control filestyle" name="pathology_report">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('charge_category'); ?></label>
                                        <input type="text" class="form-control" readonly id="charge_category_html">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('code'); ?></label>
                                        <input type="text" class="form-control" readonly id="code_html">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" class="form-control" readonly id="charge_html">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?> <small class="req"> *</small></label>
                                        <input type="text" name="apply_charge" id="apply_charge" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="updatetestbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="payMoney" tabindex="-1" aria-labelledby="payMoneyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payMoneyLabel"><?php echo $this->lang->line('make_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="payment_form" class="modal_payment" method="POST">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="amount_total_paid" class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req"> *</small></label>
                                        <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid">
                                        <input type="hidden" name="net_amount" id="net_amount">
                                        <span id="deposit_amount_error" class="text text-danger"></span>
                                        <input type="hidden" name="payment_for" value="pathology">
                                        <input type="hidden" id="bill_id_modal" name="id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" type="button" class="btn btn-info payment_pathology"><?php echo $this->lang->line('add'); ?></button>
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
                <div class="d-flex align-items-center gap-2">
                    <div id="allpayments_print"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="allpayments_result"></div>
                </div>
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

    $(document).on('click', '.view_detail', function () {
        var id = $(this).data('recordId');
        PatientPathologyDetails(id, $(this));
    });

    function PatientPathologyDetails(id, btn_obj) {
        var $btn = btn_obj;
        $btn.prop('disabled', true);
        $('#reportbilldata, #action_detail_report_modal').html('');
        $('#viewDetailReportModal').addClass('modal_loading');
        $.ajax({
            url: base_url + 'patient/dashboard/getPatientPathologyDetails',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                $('#reportbilldata').html(data.page);
                $('#action_detail_report_modal').html(data.actions);
                $('#viewDetailReportModal').removeClass('modal_loading');
                holdModal('viewDetailReportModal');
            },
            error: function () {
                alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('#viewDetailReportModal').removeClass('modal_loading');
            }
        });
    }

    $(document).on('click', '.print_report', function () {
        var id = $(this).data('recordId');
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/printPatientPathologyReportDetail',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) { popup(data.page); },
            error: function () { alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>'); },
            complete: function () { $btn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.print_bill', function () {
        var id = $(this).data('recordId');
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/PrintBillDetailsPathology',
            type: 'POST',
            data: {'id': id},
            dataType: 'json',
            success: function (data) { popup(data.page); },
            error: function () { alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>'); },
            complete: function () { $btn.prop('disabled', false); }
        });
    });

    function payModal(bill_id, balance_amount) {
        $('#bill_id_modal').val(bill_id);
        $('#amount_total_paid').val(balance_amount);
        $('#net_amount').val(balance_amount);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payMoney'), {backdrop: 'static'}).show();
    }

    $('#pay_button').click(function (e) {
        e.preventDefault();
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
                    $.each(data.error, function (i, v) { message += v; });
                    errorMsg(message);
                } else {
                    window.location.replace(base_url + 'patient/pay');
                }
            }
        });
    });

    $(document).on('click', '.view_payment', function () {
        getPayments($(this).data('recordId'), $(this).data('module_type'));
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
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/printbilltransaction',
            type: 'POST',
            data: {'id': $btn.data('recordId'), 'module_type': $btn.data('moduleType')},
            dataType: 'json',
            success: function (res) { popup(res.page); },
            error: function () { alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>'); },
            complete: function () { $btn.prop('disabled', false); }
        });
    });

    $(document).on('click', '.print_parameter', function () {
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: base_url + 'patient/dashboard/printtestparameterdetail',
            type: 'POST',
            data: {'id': $btn.data('recordId')},
            dataType: 'json',
            success: function (data) { popup(data.page); },
            error: function () { alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>'); },
            complete: function () { $btn.prop('disabled', false); }
        });
    });

</script>

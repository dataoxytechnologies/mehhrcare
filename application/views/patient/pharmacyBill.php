<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('pharmacy_bill'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('pharmacy_bill'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('doctor_name'); ?></th>
                            <th><?php echo $this->lang->line('note'); ?></th>
                            <?php if (is_array($fields) || is_object($fields)) {
                                foreach ($fields as $fields_value) { ?>
                                <th><?php echo ucfirst($fields_value->name); ?></th>
                            <?php } } ?>
                            <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('refund_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resultlist)) {
                            foreach ($resultlist as $bill) {
                                $balance_amount = ($bill['net_amount'] - $bill['paid_amount']) + $bill['refund_amount'];
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('pharmacy_billing') . $bill['id']; ?></td>
                            <td><?php echo html_escape($bill['case_reference_id']); ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($bill['date'])); ?></td>
                            <td><?php echo html_escape($bill['doctor_name']); ?></td>
                            <td><?php echo html_escape($bill['note']); ?></td>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) {
                                    $display_field = $bill[$fields_value->name];
                                    if ($fields_value->type == 'link') {
                                        $display_field = '<a href="' . $bill[$fields_value->name] . '" target="_blank">' . $bill[$fields_value->name] . '</a>';
                                    }
                            ?>
                            <td><?php echo $display_field; ?></td>
                            <?php } } ?>
                            <td class="text-end"><?php echo number_format($bill['discount'], 2) . ' (' . $bill['discount_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php echo number_format($bill['net_amount'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($bill['paid_amount'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($bill['refund_amount'], 2); ?></td>
                            <td class="text-end"><?php echo number_format($balance_amount, 2); ?></td>
                            <td class="text-end white-space-nowrap">
                                <a href="#" class="btn btn-sm btn-outline-secondary view_payment"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_payments'); ?>"
                                   data-record-id="<?php echo $bill['id']; ?>" data-module_type="pharmacy">
                                    <i class="fa fa-money"></i>
                                </a>
                                <a href="#" onclick="viewDetail('<?php echo $bill['id']; ?>')"
                                   class="btn btn-sm btn-outline-secondary"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('pay'); ?>"
                                        onclick="payModal('<?php echo $bill['id']; ?>','<?php echo $balance_amount; ?>')">
                                    <?php echo $this->lang->line('pay'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade sh-modal sh-modal-branded" id="payMoney" tabindex="-1" aria-labelledby="payMoneyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="payment_form" class="modal_payment" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="payMoneyLabel"><?php echo $this->lang->line('make_payment'); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-0">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-credit-card me-1"></i><?php echo $this->lang->line('make_payment'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="amount_total_paid" class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req"> *</small></label>
                                        <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid">
                                        <input type="hidden" class="form-control" name="net_amount" id="net_amount">
                                        <span id="deposit_amount_error" class="text text-danger"></span>
                                        <input type="hidden" name="payment_for" value="pharmacy">
                                        <input type="hidden" id="bill_id_modal" name="id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" type="button" class="btn btn-info payment_pharmacy"><?php echo $this->lang->line('add'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Bill Details Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div id="edit_deletebill" class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="reportdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
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

    function viewDetail(id) {
        var viewModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static'});
        $('#reportdata, #edit_deletebill').html('');
        $('#viewModal').addClass('modal_loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getBillDetails/',
            type: 'GET',
            data: {'id': id},
            dataType: 'JSON',
            success: function (data) {
                $('#reportdata').html(data.page);
                $('#edit_deletebill').html(data.actions);
                $('#viewModal').removeClass('modal_loading');
            },
            complete: function () {
                $('#viewModal').removeClass('modal_loading');
                viewModal.show();
            }
        });
    }

    function payModal(bill_id, balance_amount) {
        $('#bill_id_modal').val(bill_id);
        $('#amount_total_paid').val(balance_amount);
        $('#net_amount').val(balance_amount);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payMoney'), {backdrop: 'static'}).show();
    }

    $(document).on('click', '.print_bill', function () {
        var $btn = $(this);
        var record_id = $(this).data('recordId');
        var normalText = $btn.find('.normal-text');
        var loadingText = $btn.find('.loading-text');
        if (normalText.length) { normalText.hide(); loadingText.show(); }
        $btn.prop('disabled', true);
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getBillDetails/',
            type: 'GET',
            data: {'id': record_id, 'print': true},
            dataType: 'JSON',
            success: function (data) {
                popup(data.page);
            },
            complete: function () {
                if (normalText.length) { normalText.show(); loadingText.hide(); }
                $btn.prop('disabled', false);
            }
        });
    });

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
            url: '<?php echo base_url(); ?>patient/dashboard/printbillTransaction',
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

</script>

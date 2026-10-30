<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="container-fluid px-1 py-1">
    <div class="card sh-patient-card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('ambulance_bill'); ?></h3>
        </div>
        <div class="card-body p-3">
            <div class="download_label"><?php echo $this->lang->line('ambulance_bill'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('vehicle_no'); ?></th>
                            <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                            <th><?php echo $this->lang->line('driver_name'); ?></th>
                            <th><?php echo $this->lang->line('driver_contact'); ?></th>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) { ?>
                                <th><?php echo html_escape(ucfirst($fields_value->name)); ?></th>
                            <?php } } ?>
                            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . ' (%)'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resultlist)) {
                            foreach ($resultlist as $bill) {
                                $tax_amount    = (($bill['amount'] - $bill['discount']) * $bill['tax_percentage']) / 100;
                                $balance_amount = amountFormat($bill['net_amount'] - $bill['paid_amount']);
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('ambulance_call_billing') . (int)$bill['id']; ?></td>
                            <td><?php echo html_escape($bill['vehicle_no']); ?></td>
                            <td><?php echo html_escape($bill['vehicle_model']); ?></td>
                            <td><?php echo html_escape($bill['driver']); ?></td>
                            <td><?php echo html_escape($bill['driver_contact']); ?></td>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_value) {
                                    $display_field = $bill[$fields_value->name];
                                    if ($fields_value->type == 'link') {
                                        $display_field = '<a href="' . html_escape($bill[$fields_value->name]) . '" target="_blank">' . html_escape($bill[$fields_value->name]) . '</a>';
                                    } else {
                                        $display_field = html_escape($display_field);
                                    }
                                    echo '<td>' . $display_field . '</td>';
                                }
                            } ?>
                            <td class="text-end"><?php echo $bill['amount']; ?></td>
                            <td class="text-end"><?php echo $bill['discount'] . ' (' . $bill['discount_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php echo amountFormat($tax_amount) . ' (' . $bill['tax_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php echo amountFormat($bill['amount'] - $bill['discount'] + $tax_amount); ?></td>
                            <td class="text-end"><?php echo $bill['paid_amount']; ?></td>
                            <td class="text-end"><?php echo $balance_amount; ?></td>
                            <td class="text-end white-space-nowrap">
                                <a href="#" class="btn btn-sm btn-outline-secondary view_payment"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_payments'); ?>"
                                   data-record-id="<?php echo (int)$bill['id']; ?>" data-module_type="ambulance">
                                    <i class="fa fa-money"></i>
                                </a>
                                <a href="#" onclick="viewDetail('<?php echo (int)$bill['id']; ?>')"
                                   class="btn btn-sm btn-outline-secondary"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('pay'); ?>"
                                        onclick="payModal('<?php echo (int)$bill['id']; ?>','<?php echo $balance_amount; ?>')">
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

<!-- Bill Details Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_deletebill"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
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
                                <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="amount_total_paid" class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)</label><small class="req"> *</small>
                                        <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid">
                                        <input type="hidden" class="form-control" name="net_amount" id="net_amount">
                                        <span id="deposit_amount_error" class="text text-danger"></span>
                                        <input type="hidden" name="payment_for" value="ambulance">
                                        <input type="hidden" id="bill_id_modal" name="id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" type="button" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('add'); ?></button>
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

    function viewDetail(id) {
        $('#reportdata, #edit_deletebill').html('');
        $('#viewModal').addClass('modal_loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getBillDetailsAmbulance/' + id,
            type: 'GET',
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                $('#viewModal').removeClass('modal_loading');
            },
            complete: function () {
                $('#viewModal').removeClass('modal_loading');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static'}).show();
            }
        });
    }

    function payModal(bill_id, balance_amount) {
        $('#bill_id_modal').val(bill_id);
        $('#amount_total_paid').val(balance_amount);
        $('#net_amount').val(balance_amount);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payMoney'), {backdrop: 'static'}).show();
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
            success: function (res) { popup(res.page); },
            error: function () { alert('Error occurred. Please try again.'); },
            complete: function () { $this.prop('disabled', false); }
        });
    });

</script>

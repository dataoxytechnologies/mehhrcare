<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$_bb_has_image   = !empty($result['image']) && strpos($result['image'], 'no_image') === false;
$_bb_file        = $_bb_has_image ? $result['image'] : 'uploads/patient_images/no_image.png';
if (!$_bb_has_image) {
    $_bb_parts    = preg_split('/\s+/', trim($result['patient_name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $_bb_initials = count($_bb_parts) === 0 ? '?' : (count($_bb_parts) === 1
        ? mb_strtoupper(mb_substr($_bb_parts[0], 0, 1))
        : mb_strtoupper(mb_substr($_bb_parts[0], 0, 1) . mb_substr($_bb_parts[count($_bb_parts) - 1], 0, 1)));
}
$_bb_age         = $this->customlib->get_patient_current_age($result['id']);
$_bb_barcode     = './uploads/patient_id_card/barcodes/' . $id . '.png';
$_bb_qrcode      = './uploads/patient_id_card/qrcode/' . $id . '.png';
?>
<div class="container-fluid px-1 py-1">

    <!-- Patient welcome banner -->
    <div class="sh-welcome-banner">
        <div class="sh-profile-avatar-wrap">
            <?php if ($_bb_has_image): ?>
                <img src="<?php echo $this->media_storage->getImageURL($_bb_file); ?>"
                     alt="<?php echo html_escape($result['patient_name']); ?>"
                     class="sh-profile-avatar">
            <?php else: ?>
                <div class="sh-profile-avatar-initials"><?php echo html_escape($_bb_initials); ?></div>
            <?php endif; ?>
        </div>
        <div class="sh-welcome-text">
            <h2><?php echo html_escape($result['patient_name']); ?></h2>
            <p class="sub"><?php echo $this->lang->line('blood_bank'); ?></p>
            <div class="sh-welcome-meta">
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('patient_id'); ?></span>
                    <span class="val"><?php echo (int)$result['id']; ?></span>
                </div>
                <?php if (!empty($_bb_age)): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('age'); ?></span>
                    <span class="val"><?php echo html_escape($_bb_age); ?><?php if (!empty($result['gender'])): ?>, <?php echo html_escape($this->lang->line(strtolower($result['gender']))); endif; ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($result['mobileno'])): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('phone'); ?></span>
                    <span class="val"><?php echo html_escape($result['mobileno']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($result['email'])): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('email'); ?></span>
                    <span class="val"><?php echo html_escape($result['email']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barcode / QR -->
        <?php if (file_exists($_bb_barcode) || file_exists($_bb_qrcode)): ?>
        <div class="d-flex gap-3 align-items-center flex-shrink-0">
            <?php if (file_exists($_bb_barcode)): ?>
            <a href="<?php echo $this->media_storage->getImageURL($_bb_barcode); ?>" target="_blank" rel="noopener">
                <img class="sh-qr-code" src="<?php echo $this->media_storage->getImageURL($_bb_barcode); ?>" width="100" height="40" alt="barcode">
            </a>
            <?php endif; ?>
            <?php if (file_exists($_bb_qrcode)): ?>
            <a href="<?php echo $this->media_storage->getImageURL($_bb_qrcode); ?>" target="_blank" rel="noopener" class="sh-welcome-qr-link">
                <img class="sh-qr-code sh-welcome-qr" src="<?php echo $this->media_storage->getImageURL($_bb_qrcode); ?>" width="50" height="50" alt="qr">
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- Tabs Card -->
    <div class="card sh-card-token">
        <div class="card-body p-0">
            <ul class="nav nav-tabs px-3 pt-2 border-bottom" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#bloodissue" href="#bloodissue" role="tab">
                        <i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('blood_issue'); ?>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#activity" href="#activity" role="tab">
                        <i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('component_issue'); ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content p-3">

                <!-- Blood Issue Tab -->
                <div class="tab-pane fade show active" id="bloodissue" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover bloodissuelist" data-export-title="<?php echo $this->lang->line('blood_bank'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                                    <th><?php echo $this->lang->line('received_to'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <?php if (!empty($blood_issuefields)) {
                                        foreach ($blood_issuefields as $fields_value) { ?>
                                        <th class="white-space-nowrap"><?php echo html_escape($fields_value->name); ?></th>
                                    <?php } } ?>
                                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end">Paid (<?php echo $currency_symbol; ?>)</th>
                                    <th class="text-end">Balance (<?php echo $currency_symbol; ?>)</th>
                                    <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Component Issue Tab -->
                <div class="tab-pane fade" id="activity" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover componentlist" data-export-title="<?php echo $this->lang->line('blood_bank'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                                    <th><?php echo $this->lang->line('received_to'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('component'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_value) { ?>
                                        <th class="white-space-nowrap"><?php echo html_escape($fields_value->name); ?></th>
                                    <?php } } ?>
                                    <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end">Paid (<?php echo $currency_symbol; ?>)</th>
                                    <th class="text-end">Balance (<?php echo $currency_symbol; ?>)</th>
                                    <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="payMoney" tabindex="-1" aria-labelledby="payMoneyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payMoneyLabel"><i class="fa fa-money me-2"></i><?php echo $this->lang->line('make_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <input type="hidden" class="form-control" name="net_amount" id="net_amount">
                                        <span id="deposit_amount_error" class="text text-danger"></span>
                                        <input type="hidden" name="payment_for" value="blood_bank">
                                        <input type="hidden" id="bill_id_modal" name="id" value="">
                                        <input type="hidden" id="blood_donor_cycle_id" name="blood_donor_cycle_id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" type="button" class="btn btn-info"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('add'); ?></button>
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
                <h5 class="modal-title" id="allpaymentsLabel"><i class="fa fa-list me-2"></i><?php echo $this->lang->line('payments'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="allpayments_print"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<!-- View Detail Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalDynamicTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalDynamicTitle"><span id="modal_title"></span></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="edit_delete" class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="viewDetail_content"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var id = <?php echo (int)$result['id']; ?>;

    function holdModal(modalId) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId), {backdrop: 'static', keyboard: false}).show();
    }

    function payModal(bill_id, balance_amount) {
        $('#bill_id_modal').val(bill_id);
        $('#amount_total_paid').val(parseFloat(balance_amount).toFixed(2));
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

    $(document).ready(function () {
        var $viewModal = $('#viewModal');

        initDatatable('bloodissuelist', 'patient/dashboard/getbloodissueDatatable/' + id, {}, [], 100,
            [
                {"aTargets": [-1, -2, -3, -4], "bSortable": false, 'sClass': 'dt-body-right'},
                {"aTargets": [1, 2], 'sClass': 'dt-body-left'},
                {"aTargets": [6], 'sClass': 'dt-body-left'},
                {"aTargets": [3, 7], 'sClass': 'dt-body-left'}
            ]);

        initDatatable('componentlist', 'patient/dashboard/getcomponentissueDatatable/' + id, {}, [], 100,
            [
                {"aTargets": [-1, -2, -3, -4], "bSortable": false, 'sClass': 'dt-body-right'},
                {"aTargets": [1, 2], 'sClass': 'dt-body-left'},
                {"aTargets": [6], 'sClass': 'dt-body-left'},
                {"aTargets": [3, 7], 'sClass': 'dt-body-left'}
            ]);

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (el) {
            el.addEventListener('shown.bs.tab', function () {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            });
        });

        $(document).on('click', '.printIssueBill', function () {
            var $btn = $(this);
            var record_id = $btn.data('recordId');
            $btn.prop('disabled', true);
            $.ajax({
                url: base_url + 'patient/dashboard/printBloodIssueBill',
                type: 'POST',
                data: {'id': record_id},
                dataType: 'json',
                success: function (res) { popup(res.page); },
                error: function () { alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>'); },
                complete: function () { $btn.prop('disabled', false); }
            });
        });

        $(document).on('click', '.printcomponentIssueBill', function () {
            var $btn = $(this);
            var record_id = $btn.data('recordId');
            $btn.prop('disabled', true);
            $.ajax({
                url: base_url + 'patient/dashboard/printcomponentIssueBill',
                type: 'POST',
                data: {'id': record_id},
                dataType: 'json',
                success: function (res) { popup(res.page); },
                error: function () { alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>'); },
                complete: function () { $btn.prop('disabled', false); }
            });
        });

        $(document).on('click', '.view_payment', function () {
            var record_id = $(this).data('recordId');
            var module_type = $(this).data('module_type');
            getPayments(record_id, module_type);
        });

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

        $(document).on('click', '.viewDetail', function () {
            var $btn = $(this);
            $('#modal_title').text('<?php echo $this->lang->line('blood_issue_details'); ?>');
            $('#edit_delete').html('');
            $viewModal.addClass('modal_loading');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static'}).show();
            $.ajax({
                url: base_url + 'patient/dashboard/getBloodIssueDetail',
                type: 'POST',
                data: {'blood_issue_id': $btn.data('recordId')},
                dataType: 'json',
                success: function (data) {
                    $('#edit_delete').html(data.action);
                    $('#viewDetail_content').html(data.page);
                },
                complete: function () { $viewModal.removeClass('modal_loading'); }
            });
        });

        $(document).on('click', '.viewcomponentDetail', function () {
            var $btn = $(this);
            $('#modal_title').text('<?php echo $this->lang->line('component_issue_details'); ?>');
            $('#edit_delete').html('');
            $viewModal.addClass('modal_loading');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static'}).show();
            $.ajax({
                url: base_url + 'patient/dashboard/getComponentIssueDetail',
                type: 'POST',
                data: {'blood_issue_id': $btn.data('recordId')},
                dataType: 'json',
                success: function (data) {
                    $('#edit_delete').html(data.action);
                    $('#viewDetail_content').html(data.page);
                },
                complete: function () { $viewModal.removeClass('modal_loading'); }
            });
        });
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
</script>

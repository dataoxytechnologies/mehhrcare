<?php
$currency_symbol = !empty($currency_symbol) ? $currency_symbol : $this->customlib->getHospitalCurrencyFormat();
?>
<!-- Razorpay UPI QR payment page for a patient flow visit.
     Shows a scannable QR code (primary) with a "Check Payment" poll, and a
     fallback Razorpay Checkout link for patients who cannot scan. -->
<div class="container-fluid px-1 py-1">
    <div class="row g-3">

        <!-- LEFT: patient / visit summary -->
        <div class="col-md-7">
            <div class="sh-form-card h-100 mb-0">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><i class="fa fa-user me-1"></i><?php echo $this->lang->line('patient_details'); ?></span>
                </div>
                <div class="p-3">
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('patient') ?: 'Patient'; ?></span>
                        <span class="val"><?php echo htmlspecialchars(!empty($visit->patient_name) ? $visit->patient_name : $patient_name); ?></span>
                    </div>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></span>
                        <span class="val"><?php echo htmlspecialchars($visit->visit_no); ?></span>
                    </div>
                    <?php if (!empty($billing_reference)): ?>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></span>
                        <span class="val" title="<?php echo htmlspecialchars($billing_reference); ?>"><?php echo htmlspecialchars($billing_reference); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($visit->level_code)): ?>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('current_level') ?: 'Level'; ?></span>
                        <span class="val"><?php echo htmlspecialchars($visit->level_code . ' — ' . $visit->level_title); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: QR code + amount -->
        <div class="col-md-5">
            <div class="sh-form-card h-100 mb-0">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><i class="fa fa-qrcode me-1"></i><?php echo $this->lang->line('qr_payment') ?: 'QR Payment'; ?></span>
                </div>
                <div class="p-3 text-center">

                    <div class="sh-pay-summary mb-3">
                        <div class="pay-row">
                            <span class="lbl"><?php echo $this->lang->line('payment_amount') . ' (' . $currency_symbol . ')'; ?></span>
                            <span class="val"><?php echo number_format((float)$total_amount, 2, '.', ''); ?></span>
                        </div>
                    </div>

                    <div id="qr-display" class="mb-3">
                        <?php if (!empty($qr_code_image)): ?>
                        <img id="qr-image" src="<?php echo htmlspecialchars($qr_code_image); ?>" alt="QR Code"
                             style="max-width: 260px; max-height: 260px; border: 1px solid #ddd; padding: 8px; background: #fff;"
                             onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='block';">
                        <?php endif; ?>
                        <div id="qr-fallback" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle me-1"></i>
                                <?php echo $this->lang->line('qr_image_unavailable') ?: 'QR image unavailable.'; ?> <br>
                                <a href="<?php echo htmlspecialchars($qr_short_url); ?>" target="_blank"><?php echo $this->lang->line('open_qr_url') ?: 'Open QR link'; ?></a>
                            </div>
                        </div>
                    </div>

                    <div id="check-status" class="mb-3 text-muted small"></div>

                    <div class="sh-pay-actions">
                        <button type="button" id="btn-check-payment"
                                class="btn btn-primary"
                                data-visit-id="<?php echo (int) $visit_id; ?>"
                                data-verify-url="<?php echo htmlspecialchars($verify_url); ?>"
                                data-interval="5000"
                                data-max-attempts="12">
                            <i class="fa fa-search me-1"></i><?php echo $this->lang->line('check_payment') ?: 'Check Payment'; ?>
                        </button>
                        <a href="<?php echo htmlspecialchars($fallback_pay_url); ?>" target="_blank"
                           class="btn btn-outline-secondary">
                            <i class="fa fa-credit-card me-1"></i><?php echo $this->lang->line('pay_with_razorpay') ?: 'Pay with Razorpay Checkout'; ?>
                        </a>
                        <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-light">
                            <i class="fa fa-chevron-left me-1"></i><?php echo $this->lang->line('back'); ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    var SITEURL = "<?php echo base_url() ?>";
    $(function () {
        var $btn = $('#btn-check-payment');
        var visitId = $btn.data('visit-id');
        var verifyUrl = $btn.data('verify-url');
        var interval = parseInt($btn.data('interval')) || 5000;
        var maxAttempts = parseInt($btn.data('max-attempts')) || 12;
        var attempts = 0;
        var timer = null;
        var $status = $('#check-status');

        function startPolling() {
            $btn.prop('disabled', true).find('i').addClass('fa-spin');
            checkOnce();
            timer = setInterval(checkOnce, interval);
        }

        function stopPolling() {
            if (timer) { clearInterval(timer); timer = null; }
            $btn.prop('disabled', false).find('i').removeClass('fa-spin');
        }

        function checkOnce() {
            attempts++;
            $.ajax({
                url: verifyUrl,
                type: 'get',
                dataType: 'json',
                success: function (res) {
                    if (res && res.paid === true) {
                        stopPolling();
                        $status.removeClass('text-muted text-warning').addClass('text-success')
                            .html('<i class="fa fa-check-circle me-1"></i>' + (res.message || 'Payment verified.'));
                        $btn.prop('disabled', true);
                        setTimeout(function () {
                            window.location.assign(SITEURL + 'admin/patientflow?paid=1');
                        }, 1500);
                    } else if (res && res.status === false) {
                        stopPolling();
                        $status.removeClass('text-muted text-success').addClass('text-warning')
                            .html('<i class="fa fa-exclamation-triangle me-1"></i>' + (res.message || 'Verification failed.'));
                    } else {
                        $status.removeClass('text-success text-warning').addClass('text-muted')
                            .html('<i class="fa fa-clock me-1"></i>' + (res && res.message ? res.message : 'Waiting for payment...'));
                        if (attempts >= maxAttempts) {
                            stopPolling();
                            $status.removeClass('text-muted').addClass('text-warning')
                                .html('<i class="fa fa-exclamation-triangle me-1"></i> Payment not received yet. Try again or use Razorpay Checkout.');
                        }
                    }
                },
                error: function () {
                    if (attempts >= maxAttempts) {
                        stopPolling();
                        $status.removeClass('text-muted').addClass('text-warning')
                            .html('<i class="fa fa-exclamation-triangle me-1"></i> Could not verify payment.');
                    }
                }
            });
        }

        $btn.on('click', function () {
            if ($btn.prop('disabled')) return;
            startPolling();
        });
    });
</script>

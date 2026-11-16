<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<!-- Admin-side Razorpay checkout for patient flow programme bills.
     Self-contained (no patient login required): patient info comes from the
     visit record, not the patient session. -->
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
                        <span class="val"><?php echo htmlspecialchars(!empty($visit->patient_name) ? $visit->patient_name : $name); ?></span>
                    </div>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('visit_no') ?: 'Visit No'; ?></span>
                        <span class="val"><?php echo htmlspecialchars($visit->visit_no); ?></span>
                    </div>
                    <?php if (!empty($visit->billing_reference)): ?>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></span>
                        <span class="val" title="<?php echo htmlspecialchars($visit->billing_reference); ?>"><?php echo htmlspecialchars($visit->billing_reference); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($visit->token_no)): ?>
                    <div class="pay-row mb-2">
                        <span class="lbl"><?php echo $this->lang->line('token_no') ?: 'Token No'; ?></span>
                        <span class="val"><?php echo htmlspecialchars($visit->token_no); ?></span>
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

        <!-- RIGHT: payment card -->
        <div class="col-md-5">
            <div class="sh-form-card h-100 mb-0">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><i class="fa fa-credit-card me-1"></i><?php echo $this->lang->line('payment_details'); ?></span>
                </div>
                <div class="p-3">

                    <div class="sh-pay-summary mb-2">
                        <div class="pay-row">
                            <span class="lbl"><?php echo $this->lang->line('payment_amount') . ' (' . $currency_symbol . ')'; ?></span>
                            <span class="val"><?php echo number_format((float)$amount, 2, '.', ''); ?></span>
                        </div>
                        <div class="pay-row">
                            <span class="lbl"><?php echo $this->lang->line('processing_fees') . ' (' . $currency_symbol . ')'; ?></span>
                            <span class="val"><?php echo number_format((float)$gateway_processing_charge, 2, '.', ''); ?></span>
                        </div>
                        <div class="pay-row pay-total">
                            <span class="lbl"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                            <span class="val"><?php echo number_format(((float)$gateway_processing_charge + $amount), 2, '.', ''); ?></span>
                        </div>
                    </div>

                    <div class="sh-pay-actions">
                        <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-light">
                            <i class="fa fa-chevron-left me-1"></i><?php echo $this->lang->line('back'); ?>
                        </button>
                        <button type="button" onclick="pay()" class="btn btn-primary btn-pay submit_button">
                            <i class="fa fa-lock me-1"></i><?php echo $this->lang->line('make_payment'); ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var SITEURL = "<?php echo base_url() ?>";

    function pay() {
        var totalAmount = <?php echo $total; ?>;
        var product_id = <?php echo $merchant_order_id; ?>;
        var options = {
            "key": "<?php echo $key_id; ?>",
            "amount": "<?php echo $total; ?>",
            "name": "<?php echo addslashes($name); ?>",
            "description": "<?php echo addslashes($title); ?>",
            "currency": "<?php echo $currency; ?>",
            "image": "",
            "handler": function (response) {
                $.ajax({
                    url: '<?php echo $return_url; ?>',
                    type: 'post',
                    data: {
                        razorpay_payment_id: response.razorpay_payment_id,
                        totalAmount: totalAmount,
                        product_id: product_id
                    },
                    success: function (msg) {
                        var res;
                        try { res = JSON.parse(msg); } catch (e) { res = {}; }
                        if (res && res.insert_id) {
                            window.location.assign(SITEURL + 'admin/patientflow?paid=1');
                        } else {
                            window.location.assign(SITEURL + 'admin/patientflow?payment=fail');
                        }
                    }
                });
            },
            "theme": {
                "color": "#528FF0"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    }
</script>

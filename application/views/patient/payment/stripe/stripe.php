<!-- Content Wrapper. Contains page content -->
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<!-- NOTE: Legacy jQuery 3.0.0, Bootstrap 3, style-main.css, font-awesome and toastr
     includes were removed (2026-05-20). The patient layout (header.php/footer.php)
     already provides jQuery, Bootstrap 5, Font Awesome, sh-theme.css and toastr.
     Re-loading the legacy stack here forced style-main.css's `.box{background:#fff}`
     over the theme tokens, breaking the dark-theme background. -->
<!-- No .content-wrapper / section.content here: the patient layout already
     wraps the page in <main class="content">, which carries the sidebar/topbar
     offset (margin-left + padding-top). Re-nesting .content applied that
     spacing twice, adding the extra top/left margin. -->
<div class="container-fluid px-1 py-1">
        <div class="row g-3">

            <!-- LEFT: patient summary — _patient_details.php (an sh-form-card) is
                 injected here by the AJAX call at the bottom of this file. -->
            <div class="col-md-7">
                <div id="patient_details" class="h-100"></div>
            </div>

            <!-- RIGHT: payment card — amount breakdown + Stripe card entry -->
            <div class="col-md-5">
                <div class="sh-form-card h-100 mb-0">
                    <div class="sh-card-header">
                        <span class="sh-card-header-title"><i class="fa fa-credit-card me-1"></i><?php echo $this->lang->line('payment_details'); ?></span>
                    </div>
                    <div class="p-3">

                        <!-- Amount breakdown -->
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

                        <div id="stripe-payment-message" class="hidden mt-2"></div>

                        <form id="stripe-payment-form" action="<?php echo site_url('patient/payment/stripe/complete'); ?>" method="POST">

                            <input type="hidden" id="publishable_key" value="<?php echo $api_publishable_key; ?>">
                            <input type="hidden" id="currency" value="<?php echo $currency_name; ?>">
                            <input type="hidden" id="description" value="<?php echo $this->lang->line('online_appointment_fees'); ?>">
                            <input type="hidden" name="total" id="amount" value="<?php echo number_format((float)($amount + $gateway_processing_charge), 2, '.', ''); ?>">

                            <!-- Stripe.js injects the Payment Element (card fields) here -->
                            <div id="stripe-payment-element" class="mt-3"></div>

                            <div class="sh-pay-actions">
                                <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-light">
                                    <i class="fa fa-chevron-left me-1"></i><?php echo $this->lang->line('back'); ?>
                                </button>
                                <button type="submit" class="pay btn btn-primary btn-pay" id="submit-button" data-loading-text="<i class='fa fa-circle-o-notch fa-spin me-1'></i> Processing">
                                    <i class="fa fa-lock me-1"></i><?php echo $this->lang->line('pay_now'); ?>
                                </button>
                                <!-- Reinitiate replaces Pay Now (hidden by JS on failure) in the
                                     SAME flex row so it sits inline next to Back, not on a 2nd row.
                                     flex-fill mirrors .btn-pay's flex:1 — it fills the width left of Back. -->
                                <div id="payment-reinitiate" class="hidden flex-fill">
                                    <button class="btn btn-primary w-100" type="button" onclick="reinitiateStripe()">
                                        <i class="fa fa-redo me-1"></i><?php echo $this->lang->line('reinitiate_payment'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>   <!-- /.row -->
</div>   <!-- /.container-fluid -->

    <script src="https://js.stripe.com/v3/"></script>
 <script src="<?php echo base_url('backend/js/stripe_patient-checkout.js') ?>" defer></script>
<script>
get_patientdetails();

    function get_patientdetails(){
        $.ajax({
            url: '<?php echo base_url("patient/pay/getPatientDetail/$case_reference_id"); ?>',
            type: "POST",
            success: function (data) {
                $("#patient_details").html(data);
            },
            error: function () {
                
            }
        });
    }
</script>
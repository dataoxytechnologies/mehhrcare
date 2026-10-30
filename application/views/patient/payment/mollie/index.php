<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<!-- No .content-wrapper / section.content here: the patient layout already
     wraps the page in <main class="content">, which carries the sidebar/topbar
     offset (margin-left + padding-top). Re-nesting .content applied that
     spacing twice, adding the extra top/left margin. (Mirrors the Stripe
     checkout redesign — see patient/payment/stripe/stripe.php.) -->
<div class="container-fluid px-1 py-1">
        <div class="row g-3">

            <!-- LEFT: patient summary — _patient_details.php (an sh-form-card) is
                 injected here by the AJAX call at the bottom of this file. -->
            <div class="col-md-7">
                <div id="patient_details" class="h-100"></div>
            </div>

            <!-- RIGHT: payment card — amount breakdown + Mollie redirect.
                 Mollie takes no customer fields here: the form POSTs to mollie/pay,
                 which creates the payment via the API and redirects to the hosted
                 checkout (or re-renders this view with $error on API failure). -->
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

                        <?php if (isset($error) && !empty($error)) { ?>
                            <div class="alert alert-danger mt-2 mb-0"><?php echo $error; ?></div>
                        <?php } ?>

                        <form action="<?php echo base_url(); ?>patient/payment/mollie/pay" method="post">
                            <div class="sh-pay-actions">
                                <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-light">
                                    <i class="fa fa-chevron-left me-1"></i><?php echo $this->lang->line('back'); ?>
                                </button>
                                <button type="submit" class="btn btn-primary btn-pay submit_button">
                                    <i class="fa fa-lock me-1"></i><?php echo $this->lang->line('make_payment') ?>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>   <!-- /.row -->
</div>   <!-- /.container-fluid -->

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

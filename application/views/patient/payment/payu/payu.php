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

            <!-- RIGHT: payment card — amount breakdown + PayU redirect form -->
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
                                <span class="val"><?php echo number_format((float)$payment_amount, 2, '.', ''); ?></span>
                            </div>
                            <div class="pay-row">
                                <span class="lbl"><?php echo $this->lang->line('processing_fees') . ' (' . $currency_symbol . ')'; ?></span>
                                <span class="val"><?php echo number_format((float)$gateway_processing_charge, 2, '.', ''); ?></span>
                            </div>
                            <div class="pay-row pay-total">
                                <span class="lbl"><?php echo $this->lang->line('total') . ' (' . $currency_symbol . ')'; ?></span>
                                <span class="val"><?php echo number_format((float)$amount, 2, '.', ''); ?></span>
                            </div>
                        </div>

                        <!-- PayU redirect form: hidden fields carry the server-signed hash
                             and POST straight to PayU's secure endpoint ($action). The
                             "Make Payment" button is intercepted by the script below —
                             it validates via AJAX first, then submits this form. -->
                        <form id="payuForm" action="<?php echo $action; ?>" method="post">
                            <input type="hidden" name="key" value="<?php echo $mkey ?>" />
                            <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                            <input type="hidden" name="txnid" value="<?php echo $tid ?>" />
                            <input type="hidden" name="amount" value="<?php echo set_value('amount', $amount) ?>" />
                            <input type="hidden" name="firstname" id="firstname" value="<?php echo set_value('firstname', $name); ?>" />
                            <textarea name="productinfo" class="d-none"><?php echo set_value('productinfo', $productinfo); ?></textarea>
                            <input type="hidden" name="surl" value="<?php echo set_value('surl', $sucess); ?>" size="64" />
                            <input type="hidden" name="furl" value="<?php echo set_value('furl', $failure); ?>" size="64" />
                            <input type="hidden" name="service_provider" value="payu_paisa" size="64" />

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
<script type="text/javascript">
    $(document).ready(function () {
        $(".submit_button").click(function (e) {
            var url = "<?php echo site_url('patient/payment/payu/checkout') ?>";

            $.ajax({
                type: "POST",
                url: url,
                data: $("#payuForm").serialize(),
                dataType: "Json",
                success: function (response)
                {

                     if (response.status == "success") {
                         $('form#payuForm').submit();
                     } else if (response.status == "fail") {
                        $.each(response.error, function (index, value) {
                            var errorDiv = '.' + index + '_error';
                            $(errorDiv).empty().append(value);
                        });
                     }
                }
            });

            e.preventDefault();
        });
    });
</script>
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

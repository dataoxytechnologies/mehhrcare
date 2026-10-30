<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<!-- TWIN of index.php — this is the SUCCESS view rendered by pay() once phone/email
     validate. Same checkout layout as index.php, but here the 2Checkout inline-cart
     SDK (twoCoInlineCart.js) is loaded at the bottom. Keep this layout in sync with
     index.php. The SDK <script> blocks below are preserved verbatim. -->
<div class="container-fluid px-1 py-1">
        <div class="row g-3">

            <!-- LEFT: patient summary — injected by the AJAX call below. -->
            <div class="col-md-7">
                <div id="patient_details" class="h-100"></div>
            </div>

            <!-- RIGHT: payment card — amount breakdown + email/phone (carried over,
                 set_value-filled from the validated POST). -->
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

                        <form action="#" method="post">
                            <div class="form-group row mt-3 align-items-center">
                                <label for="email" class="col-sm-3 col-form-label"><?php echo $this->lang->line('email'); ?> <small class="req text-danger">*</small></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="email" id="email" value="<?php echo set_value('email'); ?>" />
                                    <span class="text-danger"><?php echo form_error('email'); ?></span>
                                </div>
                            </div>

                            <div class="form-group row mt-2 align-items-center">
                                <label for="phone" class="col-sm-3 col-form-label"><?php echo $this->lang->line('phone'); ?> <small class="req text-danger">*</small></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo set_value('phone'); ?>" />
                                    <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                </div>
                            </div>

                            <?php if (isset($api_error) && !empty($api_error)) { ?>
                                <div class="alert alert-danger mt-2 mb-0"><?php echo $api_error; ?></div>
                            <?php } ?>

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

<script src="<?php echo base_url();?>backend/custom/jquery.min.js"></script>

         <script>
            (function(document, src, libName, config) {
                var script = document.createElement('script');
                script.src = src;
                script.async = true;
                var firstScriptElement = document.getElementsByTagName('script')[0];
                script.onload = function() {
                    for (var namespace in config) {
                        if (config.hasOwnProperty(namespace)) {
                            window[libName].setup.setConfig(namespace, config[namespace]);
                        }
                    }
                    window[libName].register();
                };

                firstScriptElement.parentNode.insertBefore(script, firstScriptElement);
            })(document, 'https://secure.2checkout.com/checkout/client/twoCoInlineCart.js', 'TwoCoInlineCart', {
                "app": {
                    "merchant": "<?php echo $api_config->api_publishable_key; ?>"
                },
                "cart": {
                    "host": "https:\/\/secure.2checkout.com"
                }
            });
        </script>
          <script type="text/javascript">
          	//$('#buy-button').trigger("click");
                window.document.getElementById('buy-button').addEventListener('click', function() {

                    TwoCoInlineCart.events.subscribe('cart:closed', function(e) {
                        alert();
                        //window.location.replace("");
                    });

                    TwoCoInlineCart.setup.setMerchant("<?php echo $api_config->api_publishable_key; ?>");
                    TwoCoInlineCart.setup.setMode('DYNAMIC'); // product type
                    TwoCoInlineCart.register();

                    TwoCoInlineCart.products.add({
                        name: "Patient Bill",
                        quantity: 1,
                        price: "<?php echo $total_amount;//$amount;?>",
                    });

                    TwoCoInlineCart.cart.setOrderExternalRef("<?php echo md5(time()); ?>");
                    TwoCoInlineCart.cart.setExternalCustomerReference("<?php echo md5("1".time()); ?>"); // external customer reference
                    TwoCoInlineCart.cart.setCurrency("<?php echo $currency; ?>");
                    TwoCoInlineCart.cart.setTest(false);
                    TwoCoInlineCart.cart.setReturnMethod({
                        type: 'redirect',
                        url: "<?php echo base_url() ?>patient/payment/twocheckout/success",
                    });

                    TwoCoInlineCart.cart.checkout(); // start checkout process
                });

                setTimeout(function() {
                    $('#buy-button').removeClass('disabled');
                }, 3000);
            </script>

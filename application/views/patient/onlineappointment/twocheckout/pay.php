<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#424242" />
        <title>Hospital Management System</title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
        <?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-theme.css">
    </head>
    <body class="variant-<?php echo isset($sh_theme_tokens) ? theme_preset_to_variant($sh_theme_tokens['theme_preset']) : $this->customlib->getCurrentVariant(); ?> sh-gateway-redirect" data-preset="<?php echo isset($sh_theme_tokens) ? htmlspecialchars($sh_theme_tokens['theme_preset']) : 'clinical'; ?>">
        <div class="container">
            <div class="row">
                <div class="paddtop20">
                    <div class="col-md-8 offset-md-2 text-center">
                        <img src="<?php echo $this->media_storage->getImageURL('uploads/hospital_content/logo/' . $setting['image']); ?>">
                    </div>
                    <div class="col-md-6 offset-md-3 mt20">
                        <div class="paymentbg">
                            <div class="invtext"><?php echo $this->lang->line('fees_payment_details'); ?> </div>
                            <div class="padd2 paddtzero">
                            <form action="<?php echo base_url(); ?>patient/onlineappointment/twocheckout/pay" method="post">
                                    <table class="table2" width="100%">
                                        <tr>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('amount') ?></th>
                                        </tr>

                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("online_appointment_fees"); ?></span></td>
                                            <td class="text-end"><?php echo $setting['currency_symbol'] . number_format((float) $standard_charge, 2, '.', ''); ?></td>
                                        </tr>

                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("tax"); ?></span></td>
                                            <td class="text-end"><?php echo $setting['currency_symbol'] . number_format((float) $tax_amount, 2, '.', ''); ?></td>
                                        </tr>
                                        <!-- processing fees added -->
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("processing_fees"); ?></span></td>
                                            <td class="text-end"><?php  echo $setting['currency_symbol'] . number_format((float) $gateway_processing_charge, 2, '.', ''); ?></td>
                                        </tr>
                                        <!-- processing fees added -->
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("phone"); ?></span></td>
                                            <td>
                                                <input type="text" class="form-control"  name="phone" value="<?php echo set_value('phone'); ?>" /> <span class="alert-danger"> <?php echo form_error('phone');?></span>
                                            </td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("email"); ?></span></td>
                                            <td>
                                                <input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"> <span class="alert-danger"> <?php echo form_error('email');?></span>
                                            </td>
                                        </tr>
                                        <tr class="bordertoplightgray">
                                            <td colspan="2" class="text-end"><?php echo $this->lang->line('total');?>: <?php echo $setting['currency_symbol'] . number_format((float)($amount), 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="bordertoplightgray">
                                            <td  bgcolor="#fff"><button type="submit" onclick="window.history.go(-1); return false;" name="search"  value="" class="btn btn-info"><i class="fa fa fa-chevron-left"></i> <?php echo $this->lang->line('back'); ?> </button>  </td>
                                            <td  bgcolor="#fff" class="text-end"> <button type="submit"  name="search"  value="" class="btn btn-info"><?php echo $this->lang->line("pay_with_twocheckout"); ?> <i class="fa fa fa-chevron-right"></i></button></td>
                                        </tr>
                                    </table>
                                     <?php if (!empty($api_error)) { ?>
                                        <div class="alert alert-danger">
                                            <?= $api_error; ?> 
                                        </div>
                                    <?php } ?>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </body>
</html>

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
                        name: "Appointment",
                        quantity: 1,
                        price: "<?php echo $amount;?>",
                    });

                    TwoCoInlineCart.cart.setOrderExternalRef("<?php echo md5(time()); ?>");
                    TwoCoInlineCart.cart.setExternalCustomerReference("<?php echo md5("1".time()); ?>"); // external customer reference 
                    TwoCoInlineCart.cart.setCurrency("<?php echo $currency; ?>");
                    TwoCoInlineCart.cart.setTest(false);
                    TwoCoInlineCart.cart.setReturnMethod({
                        type: 'redirect',
                        url: "<?php echo base_url() ?>patient/onlineappointment/twocheckout/success",
                    });

                    TwoCoInlineCart.cart.checkout(); // start checkout process
                });

                setTimeout(function() {
                    $('#buy-button').removeClass('disabled');
                }, 3000);
            </script>
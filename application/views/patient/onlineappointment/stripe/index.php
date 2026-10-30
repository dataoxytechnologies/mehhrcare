<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#424242" />
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
        <?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-theme.css">
    <link href="<?php echo base_url(); ?>backend/toast-alert/toastr.css" rel="stylesheet"/>
    <script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/hospital-custom.js"></script>


    <?php if ($this->customlib->getRTL() == "yes") { ?>
           <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.rtl.min.css"/>
           <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">      
        <?php } ?> 

         <script type="text/javascript">
        var baseurl = "<?php echo base_url(); ?>";
    </script>
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
                            <div class="padd2">
                                    <table class="table2" width="100%">
                                        <tr>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="text-end text-rtl-left"><?php echo $this->lang->line('amount') ?></th>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td> 
                                                <span class="title"><?php echo $this->lang->line("online_appointment_fees"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php echo $setting['currency_symbol'] . number_format((float) $standard_charge, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td> 
                                                <span class="title"><?php echo $this->lang->line("tax"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php echo $setting['currency_symbol'] . number_format((float) $tax_amount, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("processing_fees"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php 
                                            echo $setting['currency_symbol'].number_format((float)$gateway_processing_charge, 2, '.', ''); ?></td>
                                        </tr>
                                    <tr class="bordertoplightgray">
                                        <td colspan="2" class="text-end text-rtl-left"><?php echo $this->lang->line('total');?>: <?php echo $setting['currency_symbol'] . number_format((float)($amount), 2, '.', ''); ?></td>
                                    </tr>
                                   
                                    </table>

                                    <div class="divider"></div>

<div id="stripe-payment-message" class="hidden"></div>
<form id="stripe-payment-form" class="paddtlrb" action="<?php echo site_url('patient/onlineappointment/stripe/complete'); ?>" method="POST">
    <input type='hidden' id='publishable_key' value='<?php echo $api_publishable_key; ?>'>
    <input type='hidden' id='currency' value='<?php echo $currency_name; ?>'>
    <input type='hidden' id='description' value='<?php echo $this->lang->line("online_appointment_fees"); ?>'>
    <input type="hidden" name="total" id="amount" value="<?php echo number_format((float)($amount), 2, '.', ''); ?>">
    
            <div id="stripe-payment-element">
                <!--Stripe.js will inject the Payment Element here to get card details-->
            </div>
            <div class="button-between">
                <button type="button" onclick="window.history.go(-1); return false;" name="search" value="" class="btn btn-info"><i class="fa fa-chevron-left chevron-rtl-left"></i> <?php echo $this->lang->line('back') ?></button>
                <button type="submit" class="pay btn btn-primary" id="submit-button"  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <i class="fa fa-money"></i> <?php echo $this->lang->line('pay_now') ?> </button>
                <div id="payment-reinitiate" class="hidden" >
    <button class="btn btn-primary" type="button" onclick="reinitiateStripe()"> <i class="fa fa-money"></i>  <?php echo $this->lang->line('reinitiate_payment') ?> </button>
    </div>
            </div>   

</form>
                            
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </body>

    <script src="https://js.stripe.com/v3/"></script>
 <script src="<?php echo base_url('backend/js/stripe_patient_appointment_checkout.js') ?>" defer></script>
</html>

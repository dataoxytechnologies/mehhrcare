<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#424242" />
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
        <?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-theme.css">
       <?php if ($this->customlib->getRTL() == "yes") { ?>
           <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.rtl.min.css"/>
           <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">      
        <?php } ?>        

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
                            <div class="invtext"><?php echo $this->lang->line('fees_payment_details'); ?></div>
                            <div class="padd2">
                            <form action="<?php echo base_url(); ?>patient/onlineappointment/payfast" method="post">
                                    <table class="table2" width="100%">
                                        <tr>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="text-end text-rtl-left"><?php echo $this->lang->line('amount') ?></th>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("online_appointment_fees"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php echo $setting['currency_symbol'] . number_format((float) $standard_charge, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("tax"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php echo $setting['currency_symbol'] . number_format((float) $tax_amount, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("processing_fees"); ?></span></td>
                                            <td class="text-end text-rtl-left"><?php  echo $setting['currency_symbol'] . number_format((float) $gateway_processing_charge, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="border_bottom">
                                            <td><span class="title"><?php echo $this->lang->line("email"); ?></span></td>
                                            <td>
                                                <input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"> <span class="alert-danger"> <?php echo form_error('email');?></span>
                                            </td>
                                        </tr>
                                        <tr class="bordertoplightgray">
                                            <td colspan="2" class="text-end text-rtl-left"><?php echo $this->lang->line('total');?>: <?php echo $setting['currency_symbol'] . number_format((float)($amount), 2, '.', ''); ?></td>
                                        </tr>
                                        <tr class="bordertoplightgray">
                                            <td bgcolor="#fff"><button type="submit" onclick="window.history.go(-1); return false;" name="search"  value="" class="btn btn-info"><i class="fa fa-chevron-left chevron-rtl-left"></i> <?php echo $this->lang->line('back'); ?> </button>  </td>
                                            <td bgcolor="#fff" class="text-end text-rtl-left"> <button type="submit"  name="search"  value="" class="btn btn-info"><?php echo $this->lang->line("pay_with_payfast"); ?> <i class="fa fa-chevron-right chevron-rtl-right"></i></button></td>
                                        </tr>
                                    </table>
                                    <?php if (isset($api_error)) {
                                    ?>
                                    <div class="alert alert-danger">
                                    <?= $api_error[0]; ?> </div>
                                    <?php }
                                    ?>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </body>
</html>
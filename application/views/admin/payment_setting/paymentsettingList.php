<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<div class="row payment-settings-row">
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-8 payment-methods-col">
                <div class="card">
                    <div class="card-header">
                       <h3 class="card-title titlefix"><?php echo $this->lang->line('payment_methods'); ?></h3>
                    </div>
                   <div class="nav-tabs-bar d-flex">
                   <ul class="nav nav-tabs navlistscroll">
                        <!-- <li class="nav-item"><a class="nav-link active" href="#tab_1" data-bs-toggle="tab"><?php echo $this->lang->line('paypal'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-bs-toggle="tab"><?php echo $this->lang->line('stripe'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_3" data-bs-toggle="tab"><?php echo $this->lang->line('payu'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_14" data-bs-toggle="tab"><?php echo $this->lang->line('ccavenue'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_5" data-bs-toggle="tab"><?php echo $this->lang->line('instamojo'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_6" data-bs-toggle="tab"><?php echo $this->lang->line('paystack'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_7" data-bs-toggle="tab"><?php echo $this->lang->line('razorpay'); ?></a></li> -->
                        <li class="nav-item"><a class="nav-link" href="#tab_8" data-bs-toggle="tab"><?php echo $this->lang->line('paytm'); ?></a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="#tab_9" data-bs-toggle="tab"><?php echo $this->lang->line('midtrans'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_10" data-bs-toggle="tab"><?php echo $this->lang->line('pesapal'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_15" data-bs-toggle="tab"><?php echo $this->lang->line('flutter_wave'); ?></a></li>    
                        <li class="nav-item"><a class="nav-link" href="#tab_11" data-bs-toggle="tab"><?php echo $this->lang->line('ipay_africa'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_13" data-bs-toggle="tab"><?php echo $this->lang->line('jazzcash'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_12" data-bs-toggle="tab"><?php echo $this->lang->line('billplz'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_16" data-bs-toggle="tab"><?php echo $this->lang->line('sslcommerz'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_17" data-bs-toggle="tab"><?php echo $this->lang->line('walkingm'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_4" data-bs-toggle="tab"><?php echo $this->lang->line('mollie'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_18" data-bs-toggle="tab"><?php echo $this->lang->line('cashfree'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_19" data-bs-toggle="tab"><?php echo $this->lang->line('payfast'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_20" data-bs-toggle="tab"><?php echo $this->lang->line('toyyibpay'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_21" data-bs-toggle="tab"><?php echo $this->lang->line('twocheckout'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_22" data-bs-toggle="tab"><?php echo $this->lang->line('skrill'); ?></a></li>
                         <li class="nav-item"><a class="nav-link" href="#tab_23" data-bs-toggle="tab"><?php echo $this->lang->line('payhere'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_24" data-bs-toggle="tab"><?php echo $this->lang->line('onepay'); ?></a></li> -->
                    </ul>
                    </div>
                    <?php $radio_check = check_selected($paymentlist); ?>
                    <div class="tab-content pb0">                        <div class="tab-pane" id="tab_1">
                            <form role="form" id="paypal" action="<?php echo site_url('admin/paymentsettings/paypal') ?>" method="post">
                                <?php $paypal_result = check_in_array('paypal', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/paypal.png'); ?>" alt="<?php echo $this->lang->line('paypal'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('paypal'); ?></p>
                                                    <a class="gw-desc" href="https://www.paypal.com" target="_blank" rel="noopener">https://www.paypal.com</a>
                                                </div>
                                                <?php if($radio_check == 'paypal'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('paypal_username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paypal_username" value="<?php echo isset($paypal_result->api_username) ? $paypal_result->api_username : ''; ?>">
                                                    <span class="text text-danger paypal_username_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('paypal_password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="password" class="inp" name="paypal_password" value="">
                                                    <?php if(isset($paypal_result->api_password)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($paypal_result->api_password); ?></span><?php } ?>
                                                    <span class="text text-danger paypal_password_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('paypal_signature'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paypal_signature" value="">
                                                    <?php if(isset($paypal_result->api_signature)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($paypal_result->api_signature); ?></span><?php } ?>
                                                    <span class="text text-danger paypal_signature_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- processing fees header -->
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <!-- processing fees body -->
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('paypal_charge_value',this.value)" <?php if(!isset($paypal_result->charge_type) || $paypal_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('paypal_charge_value',this.value)" <?php if(isset($paypal_result->charge_type) && $paypal_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('paypal_charge_value',this.value)" <?php if(isset($paypal_result->charge_type) && $paypal_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="paypal_charge_value" name="paypal_charge_value" type="text" class="inp-amt" value="<?php echo isset($paypal_result->charge_value) ? $paypal_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger paypal_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm paypal_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <form role="form" id="stripe" action="<?php echo site_url('admin/paymentsettings/stripe') ?>" method="post">
                                <?php $stripe_result = check_in_array('stripe', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/stripe.png'); ?>" alt="<?php echo $this->lang->line('stripe'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('stripe'); ?></p>
                                                    <a class="gw-desc" href="https://stripe.com" target="_blank" rel="noopener">https://stripe.com</a>
                                                </div>
                                                <?php if($radio_check == 'stripe'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('stripe_api_secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="api_secret_key" value="">
                                                    <?php if(isset($stripe_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($stripe_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger api_secret_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('stripe_publishable_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="api_publishable_key" value="">
                                                    <?php if(isset($stripe_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($stripe_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger api_publishable_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- processing fees header -->
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <!-- processing fees body -->
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('stripe_charge_value',this.value)" <?php if(!isset($stripe_result->charge_type) || $stripe_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('stripe_charge_value',this.value)" <?php if(isset($stripe_result->charge_type) && $stripe_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('stripe_charge_value',this.value)" <?php if(isset($stripe_result->charge_type) && $stripe_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="stripe_charge_value" name="stripe_charge_value" type="text" class="inp-amt" value="<?php echo isset($stripe_result->charge_value) ? $stripe_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger stripe_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm stripe_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_3">
                            <form role="form" id="payu" action="<?php echo site_url('admin/paymentsettings/payu') ?>" method="post">
                                <?php $payu_result = check_in_array('payu', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/payment-payu.png'); ?>" alt="<?php echo $this->lang->line('payu'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('payu'); ?></p>
                                                    <a class="gw-desc" href="https://payu.in" target="_blank" rel="noopener">https://payu.in</a>
                                                </div>
                                                <?php if($radio_check == 'payu'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('payu_money_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="key" value="">
                                                    <?php if(isset($payu_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($payu_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('payu_money_salt'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="salt" value="<?php echo isset($payu_result->salt) ? $payu_result->salt : ''; ?>">
                                                    <span class="text text-danger salt_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('payu_charge_value',this.value)" <?php if(!isset($payu_result->charge_type) || $payu_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('payu_charge_value',this.value)" <?php if(isset($payu_result->charge_type) && $payu_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('payu_charge_value',this.value)" <?php if(isset($payu_result->charge_type) && $payu_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="payu_charge_value" name="payu_charge_value" type="text" class="inp-amt" value="<?php echo isset($payu_result->charge_value) ? $payu_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger payu_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm payu_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_5">
                            <form role="form" id="instamojo" action="<?php echo site_url('admin/paymentsettings/instamojo') ?>" method="post">
                                <?php $instamojo_result = check_in_array('instamojo', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/instamojo.png'); ?>" alt="<?php echo $this->lang->line('instamojo'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('instamojo'); ?></p>
                                                    <a class="gw-desc" href="https://www.instamojo.com" target="_blank" rel="noopener">https://www.instamojo.com</a>
                                                </div>
                                                <?php if($radio_check == 'instamojo'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('private_api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="instamojo_apikey" value="">
                                                    <?php if(isset($instamojo_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($instamojo_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger instamojo_apikey_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('private_auth_token'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="instamojo_authtoken" value="">
                                                    <?php if(isset($instamojo_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($instamojo_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger instamojo_authtoken_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('private_salt'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="instamojo_salt" value="<?php echo isset($instamojo_result->salt) ? $instamojo_result->salt : ''; ?>">
                                                    <span class="text text-danger instamojo_salt_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('instamojo_charge_value',this.value)" <?php if(!isset($instamojo_result->charge_type) || $instamojo_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('instamojo_charge_value',this.value)" <?php if(isset($instamojo_result->charge_type) && $instamojo_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('instamojo_charge_value',this.value)" <?php if(isset($instamojo_result->charge_type) && $instamojo_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="instamojo_charge_value" name="instamojo_charge_value" type="text" class="inp-amt" value="<?php echo isset($instamojo_result->charge_value) ? $instamojo_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger instamojo_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm instamojo_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_6">
                            <form role="form" id="paystack" action="<?php echo site_url('admin/paymentsettings/paystack') ?>" method="post">
                                <?php $paystack_result = check_in_array('paystack', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/paystack.png'); ?>" alt="<?php echo $this->lang->line('paystack'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('paystack'); ?></p>
                                                    <a class="gw-desc" href="https://paystack.com" target="_blank" rel="noopener">https://paystack.com</a>
                                                </div>
                                                <?php if($radio_check == 'paystack'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('paystack_secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paystack_secretkey" value="">
                                                    <?php if(isset($paystack_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($paystack_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger paystack_secretkey_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('paystack_charge_value',this.value)" <?php if(!isset($paystack_result->charge_type) || $paystack_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('paystack_charge_value',this.value)" <?php if(isset($paystack_result->charge_type) && $paystack_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('paystack_charge_value',this.value)" <?php if(isset($paystack_result->charge_type) && $paystack_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="paystack_charge_value" name="paystack_charge_value" type="text" class="inp-amt" value="<?php echo isset($paystack_result->charge_value) ? $paystack_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger paystack_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm paystack_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_7">
                            <form role="form" id="razorpay" action="<?php echo site_url('admin/paymentsettings/razorpay') ?>" method="post">
                                <?php $razorpay_result = check_in_array('razorpay', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/razorpay.jpg'); ?>" alt="<?php echo $this->lang->line('razorpay'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('razorpay'); ?></p>
                                                    <a class="gw-desc" href="https://razorpay.com" target="_blank" rel="noopener">https://razorpay.com</a>
                                                </div>
                                                <?php if($radio_check == 'razorpay'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('razorpay_key_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="razorpay_keyid" value="">
                                                    <?php if(isset($razorpay_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($razorpay_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger razorpay_keyid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('razorpay_secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="razorpay_secretkey" value="">
                                                    <?php if(isset($razorpay_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($razorpay_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger razorpay_secretkey_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('razorpay_charge_value',this.value)" <?php if(!isset($razorpay_result->charge_type) || $razorpay_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('razorpay_charge_value',this.value)" <?php if(isset($razorpay_result->charge_type) && $razorpay_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('razorpay_charge_value',this.value)" <?php if(isset($razorpay_result->charge_type) && $razorpay_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="razorpay_charge_value" name="razorpay_charge_value" type="text" class="inp-amt" value="<?php echo isset($razorpay_result->charge_value) ? $razorpay_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger razorpay_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm razorpay_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane active show" id="tab_8">
                            <form role="form" id="paytm" action="<?php echo site_url('admin/paymentsettings/paytm') ?>" method="post">
                                <?php $paytm_result = check_in_array('paytm', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/paytm.jpg'); ?>" alt="<?php echo $this->lang->line('paytm'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('paytm'); ?></p>
                                                    <a class="gw-desc" href="https://paytm.com" target="_blank" rel="noopener">https://paytm.com</a>
                                                </div>
                                                <?php if($radio_check == 'paytm'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paytm_merchantid" value="">
                                                    <?php if(isset($paytm_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($paytm_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger paytm_merchantid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paytm_merchantkey" value="">
                                                    <?php if(isset($paytm_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($paytm_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger paytm_merchantkey_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('website'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paytm_website" value="<?php echo isset($paytm_result->paytm_website) ? $paytm_result->paytm_website : ''; ?>">
                                                    <span class="text text-danger paytm_website_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('industry_type'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="paytm_industrytype" value="<?php echo isset($paytm_result->paytm_industrytype) ? $paytm_result->paytm_industrytype : ''; ?>">
                                                    <span class="text text-danger paytm_industrytype_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('paytm_charge_value',this.value)" <?php if(!isset($paytm_result->charge_type) || $paytm_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('paytm_charge_value',this.value)" <?php if(isset($paytm_result->charge_type) && $paytm_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('paytm_charge_value',this.value)" <?php if(isset($paytm_result->charge_type) && $paytm_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="paytm_charge_value" name="paytm_charge_value" type="text" class="inp-amt" value="<?php echo isset($paytm_result->charge_value) ? $paytm_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger paytm_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm paytm_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_9">
                            <form role="form" id="midtrans" action="<?php echo site_url('admin/paymentsettings/midtrans') ?>" method="post">
                                <?php $midtrans_result = check_in_array('midtrans', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/midtrans.jpg'); ?>" alt="<?php echo $this->lang->line('midtrans'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('midtrans'); ?></p>
                                                    <a class="gw-desc" href="https://midtrans.com" target="_blank" rel="noopener">https://midtrans.com</a>
                                                </div>
                                                <?php if($radio_check == 'midtrans'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('server_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="midtrans_serverkey" value="">
                                                    <?php if(isset($midtrans_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($midtrans_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger midtrans_serverkey_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('midtrans_charge_value',this.value)" <?php if(!isset($midtrans_result->charge_type) || $midtrans_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('midtrans_charge_value',this.value)" <?php if(isset($midtrans_result->charge_type) && $midtrans_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('midtrans_charge_value',this.value)" <?php if(isset($midtrans_result->charge_type) && $midtrans_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="midtrans_charge_value" name="midtrans_charge_value" type="text" class="inp-amt" value="<?php echo isset($midtrans_result->charge_value) ? $midtrans_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger midtrans_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm midtrans_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_10">
                            <form role="form" id="pesapal" action="<?php echo site_url('admin/paymentsettings/pesapal') ?>" method="post">
                                <?php $pesapal_result = check_in_array('pesapal', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/pesapal.jpg'); ?>" alt="<?php echo $this->lang->line('pesapal'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('pesapal'); ?></p>
                                                    <a class="gw-desc" href="https://pesapal.com" target="_blank" rel="noopener">https://pesapal.com</a>
                                                </div>
                                                <?php if($radio_check == 'pesapal'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('consumer_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="pesapal_consumer_key" value="">
                                                    <?php if(isset($pesapal_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($pesapal_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger pesapal_consumer_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('consumer_secret'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="pesapal_consumer_secret" value="">
                                                    <?php if(isset($pesapal_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($pesapal_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger pesapal_consumer_secret_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('pesapal_charge_value',this.value)" <?php if(!isset($pesapal_result->charge_type) || $pesapal_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('pesapal_charge_value',this.value)" <?php if(isset($pesapal_result->charge_type) && $pesapal_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('pesapal_charge_value',this.value)" <?php if(isset($pesapal_result->charge_type) && $pesapal_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="pesapal_charge_value" name="pesapal_charge_value" type="text" class="inp-amt" value="<?php echo isset($pesapal_result->charge_value) ? $pesapal_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger pesapal_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm pesapal_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_11">
                            <form role="form" id="ipayafrica" action="<?php echo site_url('admin/paymentsettings/ipayafrica') ?>" method="post">
                                <?php $ipayafrica_result = check_in_array('ipayafrica', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/ipayafrica.png'); ?>" alt="<?php echo $this->lang->line('ipay_africa'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('ipay_africa'); ?></p>
                                                    <a class="gw-desc" href="https://www.ipayafrica.com" target="_blank" rel="noopener">https://www.ipayafrica.com</a>
                                                </div>
                                                <?php if($radio_check == 'ipayafrica'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('vendor_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="ipayafrica_vendorid" value="">
                                                    <?php if(isset($ipayafrica_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($ipayafrica_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger ipayafrica_vendorid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('hashkey'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="ipayafrica_hashkey" value="">
                                                    <?php if(isset($ipayafrica_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($ipayafrica_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger ipayafrica_hashkey_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('ipayafrica_charge_value',this.value)" <?php if(!isset($ipayafrica_result->charge_type) || $ipayafrica_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('ipayafrica_charge_value',this.value)" <?php if(isset($ipayafrica_result->charge_type) && $ipayafrica_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('ipayafrica_charge_value',this.value)" <?php if(isset($ipayafrica_result->charge_type) && $ipayafrica_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="ipayafrica_charge_value" name="ipayafrica_charge_value" type="text" class="inp-amt" value="<?php echo isset($ipayafrica_result->charge_value) ? $ipayafrica_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger ipayafrica_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm ipayafrica_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_12">
                            <form role="form" id="billplz" action="<?php echo site_url('admin/paymentsettings/billplz') ?>" method="post">
                                <?php $billplz_result = check_in_array('billplz', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/billplz.jpg'); ?>" alt="<?php echo $this->lang->line('billplz'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('billplz'); ?></p>
                                                    <a class="gw-desc" href="https://www.billplz.com" target="_blank" rel="noopener">https://www.billplz.com</a>
                                                </div>
                                                <?php if($radio_check == 'billplz'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="billplz_api_key" value="">
                                                    <?php if(isset($billplz_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($billplz_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger billplz_api_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('customer_service_email'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="billplz_customer_service_email" value="<?php echo isset($billplz_result->api_email) ? $billplz_result->api_email : ''; ?>">
                                                    <span class="text text-danger billplz_customer_service_email_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('billplz_charge_value',this.value)" <?php if(!isset($billplz_result->charge_type) || $billplz_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('billplz_charge_value',this.value)" <?php if(isset($billplz_result->charge_type) && $billplz_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('billplz_charge_value',this.value)" <?php if(isset($billplz_result->charge_type) && $billplz_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="billplz_charge_value" name="billplz_charge_value" type="text" class="inp-amt" value="<?php echo isset($billplz_result->charge_value) ? $billplz_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger billplz_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm billplz_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_13">
                            <form role="form" id="jazzcash" action="<?php echo site_url('admin/paymentsettings/jazzcash') ?>" method="post">
                                <?php $jazzcash_result = check_in_array('jazzcash', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/jazzcash.jpg'); ?>" alt="<?php echo $this->lang->line('jazzcash'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('jazzcash'); ?></p>
                                                    <a class="gw-desc" href="https://www.jazzcash.com.pk" target="_blank" rel="noopener">https://www.jazzcash.com.pk</a>
                                                </div>
                                                <?php if($radio_check == 'jazzcash'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('pp_merchantid'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="jazzcash_pp_MerchantID" value="">
                                                    <?php if(isset($jazzcash_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($jazzcash_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger jazzcash_pp_MerchantID_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('pp_password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="jazzcash_pp_Password" value="">
                                                    <?php if(isset($jazzcash_result->api_password)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($jazzcash_result->api_password); ?></span><?php } ?>
                                                    <span class="text text-danger jazzcash_pp_Password_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('jazzcash_charge_value',this.value)" <?php if(!isset($jazzcash_result->charge_type) || $jazzcash_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('jazzcash_charge_value',this.value)" <?php if(isset($jazzcash_result->charge_type) && $jazzcash_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('jazzcash_charge_value',this.value)" <?php if(isset($jazzcash_result->charge_type) && $jazzcash_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="jazzcash_charge_value" name="jazzcash_charge_value" type="text" class="inp-amt" value="<?php echo isset($jazzcash_result->charge_value) ? $jazzcash_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger jazzcash_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm jazzcash_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_14">
                            <form role="form" id="ccavenue" action="<?php echo site_url('admin/paymentsettings/ccavenue') ?>" method="post">
                                <?php $ccavenue_result = check_in_array('ccavenue', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/ccavenue.png'); ?>" alt="<?php echo $this->lang->line('ccavenue'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('ccavenue'); ?></p>
                                                    <a class="gw-desc" href="https://www.ccavenue.com" target="_blank" rel="noopener">https://www.ccavenue.com</a>
                                                </div>
                                                <?php if($radio_check == 'ccavenue'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="ccavenue_secret" value="">
                                                    <?php if(isset($ccavenue_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($ccavenue_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger ccavenue_secret_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('working_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="ccavenue_salt" value="<?php echo isset($ccavenue_result->salt) ? $ccavenue_result->salt : ''; ?>">
                                                    <span class="text text-danger ccavenue_salt_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('access_code'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="ccavenue_api_publishable_key" value="">
                                                    <?php if(isset($ccavenue_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($ccavenue_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger ccavenue_api_publishable_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('ccavenue_charge_value',this.value)" <?php if(!isset($ccavenue_result->charge_type) || $ccavenue_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('ccavenue_charge_value',this.value)" <?php if(isset($ccavenue_result->charge_type) && $ccavenue_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('ccavenue_charge_value',this.value)" <?php if(isset($ccavenue_result->charge_type) && $ccavenue_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="ccavenue_charge_value" name="ccavenue_charge_value" type="text" class="inp-amt" value="<?php echo isset($ccavenue_result->charge_value) ? $ccavenue_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger ccavenue_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm ccavenue_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_15">
                            <form role="form" id="flutterwave" action="<?php echo site_url('admin/paymentsettings/flutterwave') ?>" method="post">
                                <?php $flutterwave_result = check_in_array('flutterwave', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/flutterwave.png'); ?>" alt="<?php echo $this->lang->line('flutter_wave'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('flutter_wave'); ?></p>
                                                    <a class="gw-desc" href="https://flutterwave.com" target="_blank" rel="noopener">https://flutterwave.com</a>
                                                </div>
                                                <?php if($radio_check == 'flutterwave'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('public_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="public_key" value="">
                                                    <?php if(isset($flutterwave_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($flutterwave_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger public_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="secret_key" value="">
                                                    <?php if(isset($flutterwave_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($flutterwave_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger secret_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('flutterwave_charge_value',this.value)" <?php if(!isset($flutterwave_result->charge_type) || $flutterwave_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('flutterwave_charge_value',this.value)" <?php if(isset($flutterwave_result->charge_type) && $flutterwave_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('flutterwave_charge_value',this.value)" <?php if(isset($flutterwave_result->charge_type) && $flutterwave_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="flutterwave_charge_value" name="flutterwave_charge_value" type="text" class="inp-amt" value="<?php echo isset($flutterwave_result->charge_value) ? $flutterwave_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger flutterwave_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm flutterwave_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_16">
                            <form role="form" id="sslcommerz" action="<?php echo site_url('admin/paymentsettings/sslcommerz') ?>" method="post">
                                <?php $sslcommerz_result = check_in_array('sslcommerz', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/sslcommerz.png'); ?>" alt="<?php echo $this->lang->line('sslcommerz'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('sslcommerz'); ?></p>
                                                    <a class="gw-desc" href="https://www.sslcommerz.com" target="_blank" rel="noopener">https://www.sslcommerz.com</a>
                                                </div>
                                                <?php if($radio_check == 'sslcommerz'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('store_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="sslcommerz_api_key" value="">
                                                    <?php if(isset($sslcommerz_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($sslcommerz_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger sslcommerz_api_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('store_password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="sslcommerz_store_password" value="">
                                                    <?php if(isset($sslcommerz_result->api_password)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($sslcommerz_result->api_password); ?></span><?php } ?>
                                                    <span class="text text-danger sslcommerz_store_password_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('sslcommerz_charge_value',this.value)" <?php if(!isset($sslcommerz_result->charge_type) || $sslcommerz_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('sslcommerz_charge_value',this.value)" <?php if(isset($sslcommerz_result->charge_type) && $sslcommerz_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('sslcommerz_charge_value',this.value)" <?php if(isset($sslcommerz_result->charge_type) && $sslcommerz_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="sslcommerz_charge_value" name="sslcommerz_charge_value" type="text" class="inp-amt" value="<?php echo isset($sslcommerz_result->charge_value) ? $sslcommerz_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger sslcommerz_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm sslcommerz_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_17">
                            <form role="form" id="walkingm" action="<?php echo site_url('admin/paymentsettings/walkingm') ?>" method="post">
                                <?php $walkingm_result = check_in_array('walkingm', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/walkingm.png'); ?>" alt="<?php echo $this->lang->line('walkingm'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('walkingm'); ?></p>
                                                    <a class="gw-desc" href="https://walkingm.com" target="_blank" rel="noopener">https://walkingm.com</a>
                                                </div>
                                                <?php if($radio_check == 'walkingm'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('client_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="walkingm_client_id" value="">
                                                    <?php if(isset($walkingm_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($walkingm_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger walkingm_client_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('client_secret'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="walkingm_client_secret" value="">
                                                    <?php if(isset($walkingm_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($walkingm_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger walkingm_client_secret_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('walkingm_charge_value',this.value)" <?php if(!isset($walkingm_result->charge_type) || $walkingm_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('walkingm_charge_value',this.value)" <?php if(isset($walkingm_result->charge_type) && $walkingm_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('walkingm_charge_value',this.value)" <?php if(isset($walkingm_result->charge_type) && $walkingm_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="walkingm_charge_value" name="walkingm_charge_value" type="text" class="inp-amt" value="<?php echo isset($walkingm_result->charge_value) ? $walkingm_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger walkingm_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm walkingm_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_4">
                            <form role="form" id="mollie" action="<?php echo site_url('admin/paymentsettings/mollie') ?>" method="post">
                                <?php $mollie_result = check_in_array('mollie', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/mollie.png'); ?>" alt="<?php echo $this->lang->line('mollie'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('mollie'); ?></p>
                                                    <a class="gw-desc" href="https://www.mollie.com" target="_blank" rel="noopener">https://www.mollie.com</a>
                                                </div>
                                                <?php if($radio_check == 'mollie'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="mollie_api_key" value="<?php echo isset($mollie_result->api_publishable_key) ? $mollie_result->api_publishable_key : ''; ?>">
                                                    <?php if(isset($mollie_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($mollie_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger mollie_api_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('mollie_charge_value',this.value)" <?php if(!isset($mollie_result->charge_type) || $mollie_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('mollie_charge_value',this.value)" <?php if(isset($mollie_result->charge_type) && $mollie_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('mollie_charge_value',this.value)" <?php if(isset($mollie_result->charge_type) && $mollie_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="mollie_charge_value" name="mollie_charge_value" type="text" class="inp-amt" value="<?php echo isset($mollie_result->charge_value) ? $mollie_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger mollie_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm mollie_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_18">
                            <form role="form" id="cashfree" action="<?php echo site_url('admin/paymentsettings/cashfree') ?>" method="post">
                                <?php $cashfree_result = check_in_array('cashfree', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/cashfree.png'); ?>" alt="<?php echo $this->lang->line('cashfree'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('cashfree'); ?></p>
                                                    <a class="gw-desc" href="https://www.cashfree.com" target="_blank" rel="noopener">https://www.cashfree.com</a>
                                                </div>
                                                <?php if($radio_check == 'cashfree'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('app_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="cashfree_app_id" value="">
                                                    <?php if(isset($cashfree_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($cashfree_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger cashfree_app_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="cashfree_secret_key" value="">
                                                    <?php if(isset($cashfree_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($cashfree_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger cashfree_secret_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('cashfree_charge_value',this.value)" <?php if(!isset($cashfree_result->charge_type) || $cashfree_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('cashfree_charge_value',this.value)" <?php if(isset($cashfree_result->charge_type) && $cashfree_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('cashfree_charge_value',this.value)" <?php if(isset($cashfree_result->charge_type) && $cashfree_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="cashfree_charge_value" name="cashfree_charge_value" type="text" class="inp-amt" value="<?php echo isset($cashfree_result->charge_value) ? $cashfree_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger cashfree_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm cashfree_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_19">
                            <form role="form" id="payfast" action="<?php echo site_url('admin/paymentsettings/payfast') ?>" method="post">
                                <?php $payfast_result = check_in_array('payfast', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/payfast.png'); ?>" alt="<?php echo $this->lang->line('payfast'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('payfast'); ?></p>
                                                    <a class="gw-desc" href="https://www.payfast.co.za" target="_blank" rel="noopener">https://www.payfast.co.za</a>
                                                </div>
                                                <?php if($radio_check == 'payfast'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="payfast_api_publishable_key" value="">
                                                    <?php if(isset($payfast_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($payfast_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger payfast_api_publishable_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="payfast_api_secret_key" value="">
                                                    <?php if(isset($payfast_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($payfast_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger payfast_api_secret_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('security_passphrase'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="payfast_salt" value="<?php echo isset($payfast_result->salt) ? $payfast_result->salt : ''; ?>">
                                                    <span class="text text-danger payfast_salt_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('payfast_charge_value',this.value)" <?php if(!isset($payfast_result->charge_type) || $payfast_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('payfast_charge_value',this.value)" <?php if(isset($payfast_result->charge_type) && $payfast_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('payfast_charge_value',this.value)" <?php if(isset($payfast_result->charge_type) && $payfast_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="payfast_charge_value" name="payfast_charge_value" type="text" class="inp-amt" value="<?php echo isset($payfast_result->charge_value) ? $payfast_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger payfast_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm payfast_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_20">
                            <form role="form" id="toyyibpay" action="<?php echo site_url('admin/paymentsettings/toyyibPay') ?>" method="post">
                                <?php $toyyibpay_result = check_in_array('toyyibpay', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/toyyibpay.png'); ?>" alt="<?php echo $this->lang->line('toyyibpay'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('toyyibpay'); ?></p>
                                                    <a class="gw-desc" href="https://toyyibpay.com" target="_blank" rel="noopener">https://toyyibpay.com</a>
                                                </div>
                                                <?php if($radio_check == 'toyyibpay'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="toyyibpay_api_secret_key" value="">
                                                    <?php if(isset($toyyibpay_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($toyyibpay_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger toyyibpay_api_secret_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('category_code'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="toyyibpay_category_code" value="">
                                                    <?php if(isset($toyyibpay_result->api_signature)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($toyyibpay_result->api_signature); ?></span><?php } ?>
                                                    <span class="text text-danger toyyibpay_category_code_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('toyyibpay_charge_value',this.value)" <?php if(!isset($toyyibpay_result->charge_type) || $toyyibpay_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('toyyibpay_charge_value',this.value)" <?php if(isset($toyyibpay_result->charge_type) && $toyyibpay_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('toyyibpay_charge_value',this.value)" <?php if(isset($toyyibpay_result->charge_type) && $toyyibpay_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="toyyibpay_charge_value" name="toyyibpay_charge_value" type="text" class="inp-amt" value="<?php echo isset($toyyibpay_result->charge_value) ? $toyyibpay_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger toyyibpay_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm toyyibpay_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->                        <div class="tab-pane" id="tab_21">
                            <form role="form" id="twocheckout" action="<?php echo site_url('admin/paymentsettings/twocheckout') ?>" method="post">
                                <?php $twocheckout_result = check_in_array('twocheckout', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/twocheckout.png'); ?>" alt="<?php echo $this->lang->line('twocheckout'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('twocheckout'); ?></p>
                                                    <a class="gw-desc" href="https://www.2checkout.com" target="_blank" rel="noopener">https://www.2checkout.com</a>
                                                </div>
                                                <?php if($radio_check == 'twocheckout'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_code'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="twocheckout_api_publishable_key" value="">
                                                    <?php if(isset($twocheckout_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($twocheckout_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger twocheckout_api_publishable_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('secret_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="twocheckout_api_secret_key" value="">
                                                    <?php if(isset($twocheckout_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($twocheckout_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger twocheckout_api_secret_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label class="text-muted text-xs"><?php echo 'IPN Endpoint for 2Checkout  ('.base_url().'gateway_ins/twocheckout)'; ?></label>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('twocheckout_charge_value',this.value)" <?php if(!isset($twocheckout_result->charge_type) || $twocheckout_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('twocheckout_charge_value',this.value)" <?php if(isset($twocheckout_result->charge_type) && $twocheckout_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('twocheckout_charge_value',this.value)" <?php if(isset($twocheckout_result->charge_type) && $twocheckout_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="twocheckout_charge_value" name="twocheckout_charge_value" type="text" class="inp-amt" value="<?php echo isset($twocheckout_result->charge_value) ? $twocheckout_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger twocheckout_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm twocheckout_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_22">
                            <form role="form" id="skrill" action="<?php echo site_url('admin/paymentsettings/skrill') ?>" method="post">
                                <?php $skrill_result = check_in_array('skrill', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/skrill.jpg'); ?>" alt="<?php echo $this->lang->line('skrill'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('skrill'); ?></p>
                                                    <a class="gw-desc" href="https://www.skrill.com" target="_blank" rel="noopener">https://www.skrill.com</a>
                                                </div>
                                                <?php if($radio_check == 'skrill'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_account_email'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="skrill_api_email" value="<?php echo isset($skrill_result->api_email) ? $skrill_result->api_email : ''; ?>">
                                                    <span class="text text-danger skrill_api_email_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_secret_salt'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="skrill_salt" value="">
                                                    <?php if(isset($skrill_result->salt)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($skrill_result->salt); ?></span><?php } ?>
                                                    <span class="text text-danger skrill_salt_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('skrill_charge_value',this.value)" <?php if(!isset($skrill_result->charge_type) || $skrill_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('skrill_charge_value',this.value)" <?php if(isset($skrill_result->charge_type) && $skrill_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('skrill_charge_value',this.value)" <?php if(isset($skrill_result->charge_type) && $skrill_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="skrill_charge_value" name="skrill_charge_value" type="text" class="inp-amt" value="<?php echo isset($skrill_result->charge_value) ? $skrill_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger skrill_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm skrill_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_23">
                            <form role="form" id="payhere" action="<?php echo site_url('admin/paymentsettings/payhere') ?>" method="post">
                                <?php $payhere_result = check_in_array('payhere', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/payhere.png'); ?>" alt="<?php echo $this->lang->line('payhere'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('payhere'); ?></p>
                                                    <a class="gw-desc" href="https://www.payhere.lk" target="_blank" rel="noopener">https://www.payhere.lk</a>
                                                </div>
                                                <?php if($radio_check == 'payhere'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="payhere_api_publishable_key" value="">
                                                    <?php if(isset($payhere_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($payhere_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger payhere_api_publishable_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('merchant_secret'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="payhere_api_secret_key" value="">
                                                    <?php if(isset($payhere_result->api_secret_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($payhere_result->api_secret_key); ?></span><?php } ?>
                                                    <span class="text text-danger payhere_api_secret_key_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('payhere_charge_value',this.value)" <?php if(!isset($payhere_result->charge_type) || $payhere_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('payhere_charge_value',this.value)" <?php if(isset($payhere_result->charge_type) && $payhere_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('payhere_charge_value',this.value)" <?php if(isset($payhere_result->charge_type) && $payhere_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="payhere_charge_value" name="payhere_charge_value" type="text" class="inp-amt" value="<?php echo isset($payhere_result->charge_value) ? $payhere_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger payhere_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm payhere_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_24">
                            <form role="form" id="onepay" action="<?php echo site_url('admin/paymentsettings/onepay') ?>" method="post">
                                <?php $onepay_result = check_in_array('onepay', $paymentlist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/onepay.svg'); ?>" alt="<?php echo $this->lang->line('onepay'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('onepay'); ?></p>
                                                    <a class="gw-desc" href="https://www.onepay.lk" target="_blank" rel="noopener">https://www.onepay.lk</a>
                                                </div>
                                                <?php if($radio_check == 'onepay'){ ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('onepay_merchant_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="onepay_merchant_id" value="">
                                                    <?php if(isset($onepay_result->api_publishable_key)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($onepay_result->api_publishable_key); ?></span><?php } ?>
                                                    <span class="text text-danger onepay_merchant_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('access_code'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="onepay_salt" value="<?php echo isset($onepay_result->salt) ? $onepay_result->salt : ''; ?>">
                                                    <span class="text text-danger onepay_salt_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('hash_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="onepay_api_signature" value="">
                                                    <?php if(isset($onepay_result->api_signature)){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($onepay_result->api_signature); ?></span><?php } ?>
                                                    <span class="text text-danger onepay_api_signature_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cred-header">
                                            <div class="cred-header-icon"><i class="fa fa-credit-card"></i></div>
                                            <h6><?php echo $this->lang->line('processing_fees_type'); ?></h6>
                                            <span class="fees-optional"><?php echo $this->lang->line('optional'); ?></span>
                                        </div>
                                        <div class="cred-body">
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('processing_fees_type'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="fee-seg">
                                                        <label><input class="finetype" type="radio" name="charge_type" value="none" onclick="get_payment_type('onepay_charge_value',this.value)" <?php if(!isset($onepay_result->charge_type) || $onepay_result->charge_type=='none'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('none'); ?></span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="percentage" onclick="get_payment_type('onepay_charge_value',this.value)" <?php if(isset($onepay_result->charge_type) && $onepay_result->charge_type=='percentage'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('percentage'); ?> (%)</span></label>
                                                        <label><input class="finetype" type="radio" name="charge_type" value="fix" onclick="get_payment_type('onepay_charge_value',this.value)" <?php if(isset($onepay_result->charge_type) && $onepay_result->charge_type=='fix'){ ?>checked<?php } ?>><span><?php echo $this->lang->line('fix_amount'); ?> (<?php echo $currency_symbol; ?>)</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('percentage_fix_amount'); ?></label>
                                                <div class="inp-wrap">
                                                    <div class="amount-group">
                                                        <input id="onepay_charge_value" name="onepay_charge_value" type="text" class="inp-amt" value="<?php echo isset($onepay_result->charge_value) ? $onepay_result->charge_value : ''; ?>">
                                                        <span class="amt-unit"><?php echo $currency_symbol; ?></span>
                                                    </div>
                                                    <span class="text text-danger onepay_charge_value_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <button type="submit" class="btn btn-info btn-sm onepay_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div><!-- /.tab-content -->
                </div><!-- /.card -->
            </div><!-- /.col-md-8 -->
            <div class="col-md-2 gateway-selector-col">
                <div class="gw-selector-card card">
                    <div class="cred-header">
                        <div class="cred-header-icon"><i class="fa fa-exchange"></i></div>
                        <h6><?php echo $this->lang->line('select_payment_gateway'); ?></h6>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/paymentsettings/setting') ?>" id="payment_gateway" method="POST">
                        <div class="gw-selector-list">
                            <?php $radio_check = check_selected($paymentlist); ?>
                            <!-- <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="paypal" <?php if($radio_check == 'paypal') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('paypal'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="stripe" <?php if($radio_check == 'stripe') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('stripe'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="payu" <?php if($radio_check == 'payu') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('payu'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="ccavenue" <?php if($radio_check == 'ccavenue') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('ccavenue'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="instamojo" <?php if($radio_check == 'instamojo') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('instamojo'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="paystack" <?php if($radio_check == 'paystack') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('paystack'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="razorpay" <?php if($radio_check == 'razorpay') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('razorpay'); ?></span>
                            </label> -->
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="paytm" <?php if($radio_check == 'paytm') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('paytm'); ?></span>
                            </label>
                            <!-- <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="midtrans" <?php if($radio_check == 'midtrans') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('midtrans'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="pesapal" <?php if($radio_check == 'pesapal') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('pesapal'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="flutterwave" <?php if($radio_check == 'flutterwave') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('flutter_wave'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="ipayafrica" <?php if($radio_check == 'ipayafrica') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('ipay_africa'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="jazzcash" <?php if($radio_check == 'jazzcash') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('jazzcash'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="billplz" <?php if($radio_check == 'billplz') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('billplz'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="sslcommerz" <?php if($radio_check == 'sslcommerz') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('sslcommerz'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="walkingm" <?php if($radio_check == 'walkingm') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('walkingm'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="mollie" <?php if($radio_check == 'mollie') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('mollie'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="cashfree" <?php if($radio_check == 'cashfree') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('cashfree'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="payfast" <?php if($radio_check == 'payfast') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('payfast'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="toyyibpay" <?php if($radio_check == 'toyyibpay') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('toyyibpay'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="twocheckout" <?php if($radio_check == 'twocheckout') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('twocheckout'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="skrill" <?php if($radio_check == 'skrill') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('skrill'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="payhere" <?php if($radio_check == 'payhere') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('payhere'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="onepay" <?php if($radio_check == 'onepay') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('onepay'); ?></span>
                            </label>
                            <label class="gw-selector-item">
                                <input type="radio" name="payment_setting" value="none" <?php if($radio_check == 'none') echo 'checked'; ?>>
                                <span class="gw-selector-dot"></span>
                                <span class="gw-selector-name"><?php echo $this->lang->line('none'); ?></span>
                            </label> -->
                            <span class="text text-danger payment_setting_error px-3 d-block"></span>
                        </div>
                        <?php if ($this->rbac->hasPrivilege('payment_methods', 'can_edit')) { ?>
                        <div class="cred-footer">
                            <button type="submit" class="btn btn-info btn-sm payment_gateway_save" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

<script type="text/javascript">
    function get_payment_type(id,value){
        $("#"+id).val("");
        if(value=="none"){
            $("#"+id).attr("readonly",true);
        }else{
            $("#"+id).attr("readonly",false);
        }
    }

    $("#payment_gateway").submit(function (e) {
        $("[class$='_error']").html("");

        var $this = $(".payment_gateway_save");
        $this.btnLoading();
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payment_gateway").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }, complete: function () {
                $this.btnReset();
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#paypal").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".paypal_save");
        $this.btnLoading();
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paypal").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }, complete: function () {
                $this.btnReset();
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#stripe").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".stripe_save");
        $this.btnLoading();
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#stripe").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }, complete: function () {
                $this.btnReset();
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#payu").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".payu_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payu").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#twocheckout").submit(function (e) {
        $("[class$='_twocheckout_error']").html("");
        var $this = $(".twocheckout_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#twocheckout").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_twocheckout_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#ccavenue").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".ccavenue_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#ccavenue").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#flutterwave").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".flutterwave_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#flutterwave").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".flutterwave_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    }); 

    $("#paystack").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".paystack_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paystack").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#instamojo").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".instamojo_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#instamojo").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#razorpay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".razorpay_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#razorpay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

  
    $("#paytm").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".paytm_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#paytm").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#midtrans").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".midtrans_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#midtrans").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#pesapal").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".pesapal_save");
        $this.btnLoading();
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#pesapal").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#jazzcash").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".jazzcash_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#jazzcash").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#billplz").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".billplz_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#billplz").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#ipayafrica").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".ipayafrica_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#ipayafrica").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".ipayafrica_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#sslcommerz").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".sslcommerz_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#sslcommerz").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#walkingm").submit(function (e) {
        e.preventDefault();
        $("[class$='_error']").html("");
        var $this = $(".walkingm_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#walkingm").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#mollie").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".mollie_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#mollie").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#cashfree").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".cashfree_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#cashfree").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

       $("#payfast").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".payfast_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payfast").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#toyyibpay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".toyyibpay_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#toyyibpay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#twocheckout").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".twocheckout_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#twocheckout").serialize(),
            success: function (data, textStatus, jqXHR)
            { 
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    $("#skrill").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".skrill_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#skrill").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, 
            complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

        $("#payhere").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".skrill_save");
        $this.btnLoading();
        var url = $(this).attr('action');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#payhere").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            },
            complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

     $("#onepay").submit(function (e) {
        $("[class$='_error']").html("");
        var $this = $(".onepay_save");
        $this.btnLoading();
        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#onepay").serialize(),
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
            }, complete: function () {
                $this.btnReset();
            }
        });
        e.preventDefault();
    });

    // ─── Tab-scroll arrows for .navlistscroll ─────────────────────────
    $(function () {
        $('.nav-tabs.navlistscroll').each(function () {
            var $tabs = $(this);
            if ($tabs.parent().hasClass('tabscroll-wrap')) return;
            var $wrap = $('<div class="tabscroll-wrap"></div>');
            var $prev = $('<button type="button" class="tabscroll-btn tabscroll-prev" aria-label="Scroll left">&#10094;</button>');
            var $next = $('<button type="button" class="tabscroll-btn tabscroll-next" aria-label="Scroll right">&#10095;</button>');
            $tabs.wrap($wrap);
            $tabs.parent().prepend($prev).append($next);

            function updateArrows() {
                var el = $tabs[0];
                var overflow = el.scrollWidth > el.clientWidth + 1;
                var showPrev = overflow && el.scrollLeft > 2;
                var showNext = overflow && el.scrollLeft < el.scrollWidth - el.clientWidth - 2;
                $prev.toggleClass('show', showPrev);
                $next.toggleClass('show', showNext);
                $tabs.css('padding-left', showPrev ? '38px' : '');
                $tabs.css('padding-right', overflow ? '38px' : '');
            }
            $prev.on('click', function () { $tabs[0].scrollBy({ left: -200, behavior: 'smooth' }); });
            $next.on('click', function () { $tabs[0].scrollBy({ left:  200, behavior: 'smooth' }); });
            $tabs.on('scroll', updateArrows);
            $(window).on('resize', updateArrows);
            setTimeout(updateArrows, 50);
        });
    });
</script>
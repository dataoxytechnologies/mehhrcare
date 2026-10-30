<style>
.navscroll-wrap{ display:flex; align-items:stretch; position:relative; }
.navscroll-wrap .navlistscroll{ flex:1 1 auto; min-width:0; scroll-behavior:smooth; scrollbar-width:none; -ms-overflow-style:none; }
.navscroll-wrap .navlistscroll::-webkit-scrollbar{ display:none; }
.navscroll-btn{ flex:0 0 auto; display:none; align-items:center; justify-content:center; width:34px; padding:0; border:none; border-bottom:1px solid var(--border); background:var(--surface); color:var(--muted); cursor:pointer; font-size:16px; transition:color .15s ease, background .15s ease; }
.navscroll-btn:hover{ color:var(--accent); background:var(--surface-2); }
.navscroll-btn[disabled]{ opacity:.3; cursor:default; color:var(--muted); background:var(--surface); }
.navscroll-wrap.is-scrollable .navscroll-btn{ display:flex; }
</style>
<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('sms_setting'); ?></h3>
            </div>
                    <div class="navscroll-wrap">
                    <button type="button" class="navscroll-btn navscroll-prev" aria-label="Scroll tabs left"><i class="fa fa-angle-left"></i></button>
                    <ul class="nav nav-tabs navlistscroll">
                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-bs-toggle="tab"><?php echo $this->lang->line('clickatell_sms_gateway'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-bs-toggle="tab"><?php echo $this->lang->line('twilio_sms_gateway'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_4" data-bs-toggle="tab"><?php echo $this->lang->line('msg_91'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_6" data-bs-toggle="tab"><?php echo $this->lang->line('text_local'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_5" data-bs-toggle="tab"><?php echo $this->lang->line('sms_country'); ?></a></li>       
                        <li class="nav-item"><a class="nav-link" href="#tab_7" data-bs-toggle="tab"><?php echo $this->lang->line('bulk_sms'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_8" data-bs-toggle="tab"><?php echo $this->lang->line('mobireach'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_9" data-bs-toggle="tab"><?php echo $this->lang->line('nexmo'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_10" data-bs-toggle="tab"><?php echo $this->lang->line('africastalking'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_3" data-bs-toggle="tab"><?php echo $this->lang->line('custom_sms_gateway'); ?></a></li>
                    </ul>
                    <button type="button" class="navscroll-btn navscroll-next" aria-label="Scroll tabs right"><i class="fa fa-angle-right"></i></button>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <form role="form" id="clickatell" action="<?php echo site_url('smsconfig/clickatell') ?>" method="post">
                                <?php $clickatell_result = check_sms_in_array('clickatell', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo base_url() ?>backend/images/clickatell.png" alt="<?php echo $this->lang->line('clickatell_sms_gateway'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('clickatell_sms_gateway'); ?></p>
                                                    <a class="gw-desc" href="https://www.clickatell.com/" target="_blank" rel="noopener">https://www.clickatell.com</a>
                                                </div>
                                                <?php if (isset($clickatell_result->is_active) && $clickatell_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('clickatell_username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input autofocus type="text" class="inp" name="clickatell_user" value="<?php echo $clickatell_result->username; ?>">
                                                    <span class="text text-danger clickatell_user_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('clickatell_password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="password" class="inp" name="clickatell_password" value="">
                                                    <?php if($clickatell_result->password){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($clickatell_result->password); ?></span><?php } ?>
                                                    <span class="text text-danger clickatell_password_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('clickatell_api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="clickatell_api_id" value="">
                                                    <?php if($clickatell_result->api_id){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($clickatell_result->api_id); ?></span><?php } ?>
                                                    <span class="text text-danger clickatell_api_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="clickatell_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($clickatell_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="clickatell_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <form role="form" id="twilio" action="<?php echo site_url('smsconfig/twilio') ?>" method="post">
                                <?php $twilio_result = check_sms_in_array('twilio', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/twilio.png') ?>" alt="<?php echo $this->lang->line('twilio_sms_gateway'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('twilio_sms_gateway'); ?></p>
                                                    <a class="gw-desc" href="https://www.twilio.com/?v=t" target="_blank" rel="noopener">https://www.twilio.com</a>
                                                </div>
                                                <?php if (isset($twilio_result->is_active) && $twilio_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('twilio_account_sid'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="twilio_account_sid" value="">
                                                    <?php if($twilio_result->api_id){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($twilio_result->api_id); ?></span><?php } ?>
                                                    <span class="text text-danger twilio_account_sid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('authentication_token'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="twilio_auth_token" value="">
                                                    <?php if($twilio_result->password){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($twilio_result->password); ?></span><?php } ?>
                                                    <span class="text text-danger twilio_auth_token_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('registered_phone_number'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="twilio_sender_phone_number" value="<?php echo $twilio_result->contact; ?>">
                                                    <span class="text text-danger twilio_sender_phone_number_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="twilio_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($twilio_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="twilio_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_3">
                            <form role="form" id="custom" action="<?php echo site_url('smsconfig/custom') ?>" method="post">
                                <?php $custom_result = check_sms_in_array('custom', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/custom-sms.png') ?>" alt="<?php echo $this->lang->line('custom_sms_gateway'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('custom_sms_gateway'); ?></p>
                                                </div>
                                                <?php if (isset($custom_result->is_active) && $custom_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('gateway_name'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="name" value="<?php echo $custom_result->name; ?>">
                                                    <span class="text text-danger name_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="custom_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($custom_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="custom_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_4">
                            <form role="form" id="msg_nineone" action="<?php echo site_url('smsconfig/msgnineone') ?>" method="post">
                                <?php $msg_nineone_result = check_sms_in_array('msg_nineone', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/msg91.png') ?>" alt="<?php echo $this->lang->line('msg_91'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('msg_91'); ?></p>
                                                    <a class="gw-desc" href="https://msg91.com/" target="_blank" rel="noopener">https://msg91.com</a>
                                                </div>
                                                <?php if (isset($msg_nineone_result->is_active) && $msg_nineone_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('auth_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="authkey" value="">
                                                    <?php if($msg_nineone_result->authkey){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($msg_nineone_result->authkey); ?></span><?php } ?>
                                                    <span class="text text-danger authkey_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('sender_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="senderid" value="<?php echo $msg_nineone_result->senderid; ?>">
                                                    <span class="text text-danger senderid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="msg_nineone_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($msg_nineone_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="msg_nineone_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_5">
                            <form role="form" id="smscountry" action="<?php echo site_url('smsconfig/smscountry') ?>" method="post">
                                <?php $smscountry_result = check_sms_in_array('smscountry', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/sms-country.jpg') ?>" alt="<?php echo $this->lang->line('sms_country'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('sms_country'); ?></p>
                                                    <a class="gw-desc" href="https://www.smscountry.com/" target="_blank" rel="noopener">https://www.smscountry.com</a>
                                                </div>
                                                <?php if (isset($smscountry_result->is_active) && $smscountry_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="smscountry" value="<?php echo $smscountry_result->username; ?>">
                                                    <span class="text text-danger smscountry_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('sender_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="smscountrysenderid" value="<?php echo $smscountry_result->senderid; ?>">
                                                    <span class="text text-danger smscountrysenderid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="password" class="inp" name="smscountrypassword" value="">
                                                    <?php if($smscountry_result->password){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($smscountry_result->password); ?></span><?php } ?>
                                                    <span class="text text-danger smscountrypassword_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="smscountry_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($smscountry_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="smscountry_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_6">
                            <form role="form" id="text_local" action="<?php echo site_url('smsconfig/textlocal') ?>" method="post">
                                <?php $text_local_result = check_sms_in_array('text_local', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/textlocal.png') ?>" alt="<?php echo $this->lang->line('text_local'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('text_local'); ?></p>
                                                    <a class="gw-desc" href="https://www.textlocal.in/" target="_blank" rel="noopener">https://www.textlocal.in</a>
                                                </div>
                                                <?php if (isset($text_local_result->is_active) && $text_local_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="text_local" value="<?php echo $text_local_result->username; ?>">
                                                    <span class="text text-danger text_local_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('hash_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="password" class="inp" name="text_localpassword" value="">
                                                    <?php if($text_local_result->password){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($text_local_result->password); ?></span><?php } ?>
                                                    <span class="text text-danger text_localpassword_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('sender_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="text_localsenderid" value="<?php echo $text_local_result->senderid; ?>">
                                                    <span class="text text-danger text_localsenderid_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="text_local_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($text_local_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="text_local_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab_7">
                            <form role="form" id="bulk_sms" action="<?php echo site_url('smsconfig/bulk_sms') ?>" method="post">
                                <?php $bulk_sms_result = check_sms_in_array('bulk_sms', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/bulk_sms.png') ?>" alt="<?php echo $this->lang->line('bulk_sms'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('bulk_sms'); ?></p>
                                                    <a class="gw-desc" href="https://www.textlocal.in/" target="_blank" rel="noopener">https://www.textlocal.in</a>
                                                </div>
                                                <?php if (isset($bulk_sms_result->is_active) && $bulk_sms_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="bulk_sms_user_name" value="<?php echo $bulk_sms_result->username; ?>">
                                                    <span class="text text-danger bulk_sms_user_name_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('password'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="password" class="inp" name="bulk_sms_user_password" value="">
                                                    <?php if($bulk_sms_result->password){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($bulk_sms_result->password); ?></span><?php } ?>
                                                    <span class="text text-danger bulk_sms_user_password_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="bulk_sms_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($bulk_sms_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text text-danger bulk_sms_status_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="bulk_sms_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab_8">
                            <form role="form" id="mobireach" action="<?php echo site_url('smsconfig/mobireach') ?>" method="post">
                                <?php $mobireach_result = check_sms_in_array('mobireach', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/mobireach.jpg') ?>" alt="<?php echo $this->lang->line('mobireach'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('mobireach'); ?></p>
                                                    <a class="gw-desc" href="https://user.mobireach.com.bd/" target="_blank" rel="noopener">https://user.mobireach.com.bd/</a>
                                                </div>
                                                <?php if (isset($mobireach_result->is_active) && $mobireach_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('auth_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="mobireach_auth_key" value="">
                                                    <?php if($mobireach_result->authkey){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($mobireach_result->authkey); ?></span><?php } ?>
                                                    <span class="text text-danger mobireach_auth_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('sender_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="mobireach_sender_id" value="<?php echo $mobireach_result->senderid; ?>">
                                                    <span class="text text-danger mobireach_sender_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('route_id'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="mobireach_route_id" value="">
                                                    <?php if($mobireach_result->api_id){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($mobireach_result->api_id); ?></span><?php } ?>
                                                    <span class="text text-danger mobireach_route_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="mobireach_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($mobireach_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text text-danger mobireach_status_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="mobireach_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="tab_9">
                            <form role="form" id="nexmo" action="<?php echo site_url('smsconfig/nexmo') ?>" method="post">
                                <?php $nexmo_result = check_sms_in_array('nexmo', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/nexmo.jpg') ?>" alt="<?php echo $this->lang->line('nexmo'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('nexmo'); ?></p>
                                                    <a class="gw-desc" href="https://dashboard.nexmo.com/sign-up" target="_blank" rel="noopener">https://dashboard.nexmo.com/sign-up</a>
                                                </div>
                                                <?php if (isset($nexmo_result->is_active) && $nexmo_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="nexmo_api_key" value="">
                                                    <?php if($nexmo_result->api_id){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($nexmo_result->api_id); ?></span><?php } ?>
                                                    <span class="text text-danger nexmo_api_key_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('nexmo_api_secret'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="nexmo_api_secret" value="">
                                                    <?php if($nexmo_result->authkey){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($nexmo_result->authkey); ?></span><?php } ?>
                                                    <span class="text text-danger nexmo_api_secret_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('registered_from_number'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="nexmo_registered_phone_number" value="<?php echo $nexmo_result->senderid; ?>">
                                                    <span class="text text-danger nexmo_registered_phone_number_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="nexmo_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($nexmo_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text text-danger nexmo_status_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="nexmo_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="tab-pane" id="tab_10">
                            <form role="form" id="africastalking" action="<?php echo site_url('smsconfig/africastalking') ?>" method="post">
                                <?php $africastalking_result = check_sms_in_array('africastalking', $smslist); ?>
                                <div class="p-3">
                                    <div class="gw-tab-card">
                                        <!-- brand panel -->
                                        <div class="cred-header">
                                            <div class="gw-logo-box"><img src="<?php echo $this->media_storage->getImageURL('backend/images/africastalking.png') ?>" alt="<?php echo $this->lang->line('africastalking'); ?>"></div>
                                            <div class="gw-brand-info">
                                                <div class="gw-brand-text">
                                                    <p class="gw-name"><?php echo $this->lang->line('africastalking'); ?></p>
                                                    <a class="gw-desc" href="https://africastalking.com/" target="_blank" rel="noopener">https://africastalking.com/</a>
                                                </div>
                                                <?php if (isset($africastalking_result->is_active) && $africastalking_result->is_active == 'enabled') { ?><span class="gw-status"><?php echo $this->lang->line('active'); ?></span><?php } ?>
                                            </div>
                                        </div>
                                        <!-- credentials body -->
                                        <div class="cred-body">
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('username'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="africastalking_username" value="<?php echo $africastalking_result->username; ?>">
                                                    <span class="text text-danger africastalking_username_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('api_key'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="africastalking_apikey" value="">
                                                    <?php if($africastalking_result->api_id){ ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($africastalking_result->api_id); ?></span><?php } ?>
                                                    <span class="text text-danger africastalking_apikey_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field cred-field-top">
                                                <label><?php echo $this->lang->line('short_code'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <input type="text" class="inp" name="africastalking_short_code" value="<?php echo $africastalking_result->senderid; ?>">
                                                    <span class="text text-danger africastalking_short_code_error"></span>
                                                </div>
                                            </div>
                                            <div class="cred-field">
                                                <label><?php echo $this->lang->line('status'); ?> <span class="req">*</span></label>
                                                <div class="inp-wrap">
                                                    <select class="inp" name="africastalking_status">
                                                        <?php foreach ($statuslist as $s_key => $s_value) { ?>
                                                            <option value="<?php echo $s_key; ?>" <?php if ($africastalking_result->is_active == $s_key) { echo "selected=selected"; } ?>><?php echo $s_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text text-danger africastalking_status_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('sms_setting', 'can_edit')) { ?>
                                        <div class="cred-footer">
                                            <span class="nexmo_loader"></span>
                                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            <!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>

<?php
 
function check_sms_in_array($find, $array) {

    foreach ($array as $element) {
        if ($find == $element->type) {
            return $element;
        }
    }
    $object = new stdClass();
    $object->id = "";
    $object->type = "";
    $object->api_id = "";
    $object->username = "";
    $object->url = "";
    $object->name = "";
    $object->contact = "";
    $object->password = "";
    $object->authkey = "";
    $object->senderid = "";
    $object->is_active = "";
    return $object;
}
?>


<script type="text/javascript">
    var img_path = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif'); ?>";
    $("#clickatell").submit(function (e) {
        $("[class$='_error']").html("");

        $(".clickatell_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#clickatell").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".clickatell_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".clickatell_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#twilio").submit(function (e) {
        $("[class$='_error']").html("");

        $(".twilio_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#twilio").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".twilio_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".twilio_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


    $("#custom").submit(function (e) {
        $("[class$='_error']").html("");

        $(".custom_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#custom").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".custom_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#msg_nineone").submit(function (e) {
        $("[class$='_error']").html("");

        $(".msg_nineone_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#msg_nineone").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".msg_nineone_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".msg_nineone_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#smscountry").submit(function (e) {
        $("[class$='_error']").html("");

        $(".smscountry_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#smscountry").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".smscountry_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".msg_nineone_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


    $("#text_local").submit(function (e) {
        $("[class$='_error']").html("");
        $(".text_local_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#text_local").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".text_local_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".text_local_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

$("#bulk_sms").submit(function (e) {
        $("[class$='_error']").html("");
        $(".bulk_sms_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#bulk_sms").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else { 
                    successMsg(data.msg);
                }
                $(".bulk_sms_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".bulk_sms_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


      $("#mobireach").submit(function (e) {
        $("[class$='_error']").html("");
        $(".mobireach_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#mobireach").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }

                $(".mobireach_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".mobireach_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

       $("#nexmo").submit(function (e) {
        $("[class$='_error']").html("");
        $(".nexmo_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#nexmo").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }

                $(".nexmo_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".nexmo_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

        $("#africastalking").submit(function (e) {
        $("[class$='_error']").html("");
        $(".africastalking_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#africastalking").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }

                $(".africastalking_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".africastalking_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // Horizontal scroll controls for the SMS gateway tab strip
    $(function () {
        var $wrap = $('.navscroll-wrap');
        if (!$wrap.length) { return; }
        var list = $wrap.find('.navlistscroll')[0];
        var $prev = $wrap.find('.navscroll-prev');
        var $next = $wrap.find('.navscroll-next');

        function update() {
            var overflow = (list.scrollWidth - list.clientWidth) > 1;
            $wrap.toggleClass('is-scrollable', overflow);
            $prev.prop('disabled', list.scrollLeft <= 0);
            $next.prop('disabled', list.scrollLeft >= (list.scrollWidth - list.clientWidth - 1));
        }

        function step(dir) {
            var amount = Math.max(list.clientWidth * 0.6, 120);
            list.scrollBy({ left: dir * amount, behavior: 'smooth' });
        }

        $prev.on('click', function () { step(-1); });
        $next.on('click', function () { step(1); });
        $(list).on('scroll', update);
        $(window).on('resize', update);

        update();
        setTimeout(update, 250); // re-check after fonts/layout settle
    });
</script>
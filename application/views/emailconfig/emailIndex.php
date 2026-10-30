<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('email_setting'); ?></h3>
            </div>
            <form id="form1" action="<?php echo base_url() ?>emailconfig/index" name="employeeform" method="post" accept-charset="utf-8">
                <?php if ($this->session->flashdata('msg')) { ?>
                    <?php
                        echo $this->session->flashdata('msg');
                        $this->session->unset_userdata('msg');
                    ?>
                <?php } ?>
                <?php echo $this->customlib->getCSRF(); ?>
                <div class="p-3">
                    <div class="gw-tab-card">
                        <!-- engine selector body -->
                        <div class="cred-body">
                        <div class="cred-field">
                            <label for="email_type"><?php echo $this->lang->line('email_engine'); ?></label>
                            <div class="inp-wrap">
                                <select autofocus id="email_type" name="email_type" class="inp">
                                    <?php foreach ($mailMethods as $method_key => $method_value) { ?>
                                        <option value="<?php echo $method_key ?>" <?php if (set_value('email_type', $emaillist->email_type) == $method_key) echo "selected=selected" ?>><?php echo $method_value ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('email_type'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php $display = (set_value('email_type', $emaillist->email_type) != "smtp") ? 'ss-none' : '' ?>
                    <div class="is_disabled <?php echo $display; ?>">
                        <!-- SMTP sub-section header -->
                        <div class="cred-header">
                            <div class="cred-header-icon"><i class="fa fa-lock"></i></div>
                            <h6><?php echo $this->lang->line('smtp_server'); ?></h6>
                        </div>
                        <!-- SMTP credentials body -->
                        <div class="cred-body">
                            <div class="cred-field cred-field-top">
                                <label for="smtp_username"><?php echo $this->lang->line('smtp_username'); ?> <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input id="smtp_username" name="smtp_username" type="text" class="inp" value="<?php echo set_value('smtp_username', $emaillist->smtp_username); ?>">
                                    <span class="text-danger"><?php echo form_error('smtp_username'); ?></span>
                                </div>
                            </div>
                            <div class="cred-field cred-field-top">
                                <label for="smtp_password"><?php echo $this->lang->line('smtp_password'); ?> <span class="req">*</span></label>
                                <div class="inp-wrap">
                                    <input id="smtp_password" name="smtp_password" type="password" class="inp" value="">
                                    <?php if ($emaillist->smtp_password) { ?><span class="saved-hint"><i class="fa fa-lock"></i> <?php echo mask_sensitive($emaillist->smtp_password); ?></span><?php } ?>
                                    <span class="text-danger"><?php echo form_error('smtp_password'); ?></span>
                                </div>
                            </div>
                            <div class="cred-field cred-field-top">
                                <label for="smtp_server"><?php echo $this->lang->line('smtp_server'); ?></label>
                                <div class="inp-wrap">
                                    <input id="smtp_server" name="smtp_server" type="text" class="inp" value="<?php echo set_value('smtp_server', $emaillist->smtp_server); ?>">
                                    <span class="text-danger"><?php echo form_error('smtp_server'); ?></span>
                                </div>
                            </div>
                            <div class="cred-field cred-field-top">
                                <label for="smtp_port"><?php echo $this->lang->line('smtp_port'); ?></label>
                                <div class="inp-wrap">
                                    <input id="smtp_port" name="smtp_port" type="text" class="inp" value="<?php echo set_value('smtp_port', $emaillist->smtp_port); ?>">
                                    <span class="text-danger"><?php echo form_error('smtp_port'); ?></span>
                                </div>
                            </div>
                            <div class="cred-field">
                                <label for="smtp_security"><?php echo $this->lang->line('smtp_security'); ?></label>
                                <div class="inp-wrap">
                                    <select id="smtp_security" name="smtp_security" class="inp">
                                        <?php foreach ($smtp_encryption as $encryption_key => $encryption_value) { ?>
                                            <option value="<?php echo $encryption_key ?>" <?php echo set_select('smtp_security', $encryption_key, (set_value('smtp_security', $emaillist->ssl_tls) == $encryption_key) ? TRUE : FALSE); ?>><?php echo $encryption_value; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('smtp_security'); ?></span>
                                </div>
                            </div>
                            <div class="cred-field">
                                <label for="smtp_auth"><?php echo $this->lang->line('smtp_auth'); ?></label>
                                <div class="inp-wrap">
                                    <select id="smtp_auth" name="smtp_auth" class="inp">
                                        <?php foreach ($smtp_auth as $smtp_auth_key => $smtp_auth_value) { ?>
                                            <option value="<?php echo $smtp_auth_key ?>" <?php echo set_select('smtp_security', $smtp_auth_key, (set_value('auth_key', $emaillist->smtp_auth) == $smtp_auth_key) ? TRUE : FALSE); ?>><?php echo $smtp_auth_value; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('smtp_security'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($this->rbac->hasPrivilege('email_setting', 'can_edit')) { ?>
                    <div class="cred-footer">
                        <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        if ($('#email_type').val() != "smtp") {
            $('.is_disabled').hide();
        } else {
            $('.is_disabled').show();
        }
        $(document).on('change', '#email_type', function () {
            var selected = $(this).val();
            is_disabled(selected);
        });
    });
    function is_disabled(selected) {
        if (selected != "smtp") {
            $('.is_disabled').slideUp();
        } else {
            $('.is_disabled').slideDown();
        }
    }
</script>

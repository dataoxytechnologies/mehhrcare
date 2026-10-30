<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="sh-form-card">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><?php echo $this->lang->line('change_password'); ?></span>
            </div>
            <div class="p-3">
                <?php if ($this->session->flashdata('msg')) { ?>
                    <?php echo $this->session->flashdata('msg'); ?>
                <?php } ?>
                <?php if (isset($error_message)) { echo $error_message; } ?>

                <form action="<?php echo site_url('admin/admin/changepass'); ?>" id="passwordform" name="passwordform" method="post">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $this->lang->line('current_password'); ?><small class="req"> *</small></label>
                        <div class="input-group">
                            <input type="password" name="current_pass" class="form-control" value="<?php echo set_value('currentr_password'); ?>">
                            <button type="button" class="btn btn-outline-secondary sh-pass-toggle" tabindex="-1"><i class="fa fa-eye"></i></button>
                        </div>
                        <span class="text-danger small"><?php echo form_error('current_pass'); ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $this->lang->line('new_password'); ?><small class="req"> *</small></label>
                        <div class="input-group">
                            <input type="password" name="new_pass" class="form-control" value="<?php echo set_value('new_password'); ?>">
                            <button type="button" class="btn btn-outline-secondary sh-pass-toggle" tabindex="-1"><i class="fa fa-eye"></i></button>
                        </div>
                        <span class="text-danger small"><?php echo form_error('new_pass'); ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo $this->lang->line('confirm_password'); ?><small class="req"> *</small></label>
                        <div class="input-group">
                            <input type="password" name="confirm_pass" id="confirm_pass" class="form-control" value="<?php echo set_value('confirm_password'); ?>">
                            <button type="button" class="btn btn-outline-secondary sh-pass-toggle" tabindex="-1"><i class="fa fa-eye"></i></button>
                        </div>
                        <span class="text-danger small"><?php echo form_error('confirm_pass'); ?></span>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-info"><?php echo $this->lang->line('change_password'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.sh-pass-toggle', function () {
    var input = $(this).closest('.input-group').find('input');
    var icon  = $(this).find('i');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
</script>

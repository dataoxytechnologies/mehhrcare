<div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('change_password'); ?></h3>
            </div>
            <ul class="nav nav-tabs px-3 pt-2 border-bottom-0">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo site_url('user/user/changepass'); ?>"><?php echo $this->lang->line('change_password'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('user/user/changeusername'); ?>"><?php echo $this->lang->line('change_username'); ?></a>
                </li>
            </ul>
            <div class="card-body">
                <?php if ($this->session->flashdata('success_msg')) { ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
                <?php } ?>
                <?php if ($this->session->flashdata('error_msg')) { ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
                <?php } ?>

                <form action="<?php echo site_url('user/user/changepass'); ?>" id="passwordform" name="passwordform" method="post" novalidate>
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-lock me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="mb-3 col-12">
                                    <label class="form-label" for="current_pass"><?php echo $this->lang->line('current_password'); ?></label><small class="req"> *</small>
                                    <input id="current_pass" name="current_pass" type="password" required class="form-control" value="<?php echo set_value('current_password'); ?>">
                                    <span class="text-danger small"><?php echo form_error('current_pass'); ?></span>
                                </div>
                                <div class="mb-3 col-12">
                                    <label class="form-label" for="new_pass"><?php echo $this->lang->line('new_password'); ?></label><small class="req"> *</small>
                                    <input id="new_pass" name="new_pass" type="password" required class="form-control" value="<?php echo set_value('new_password'); ?>">
                                    <span class="text-danger small"><?php echo form_error('new_pass'); ?></span>
                                </div>
                                <div class="mb-3 col-12">
                                    <label class="form-label" for="confirm_pass"><?php echo $this->lang->line('confirm_password'); ?></label><small class="req"> *</small>
                                    <input id="confirm_pass" name="confirm_pass" type="password" class="form-control" value="<?php echo set_value('confirm_password'); ?>">
                                    <span class="text-danger small"><?php echo form_error('confirm_pass'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('change_password'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

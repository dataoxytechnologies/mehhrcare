<!-- Content Wrapper. Contains page content -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('settings') ?></h3>
            </div>
            <form id="form1" action="<?php echo base_url() ?>admin/zoom_conference" name="employeeform" method="post" accept-charset="utf-8">

                <div class="card-body">
                    <?php if ($this->session->flashdata('msg')) {
                        echo $this->session->flashdata('msg');
                        $this->session->unset_userdata('msg');
                    } ?>
                    <?php echo $this->customlib->getCSRF(); ?>

                    <?php if (!$this->session->has_userdata('zoom_access_token')) { ?>
                        <div class="alert alert-info"><?php echo $this->lang->line('access_token_not_generated_please_authenticate_your_account'); ?></div>
                    <?php } ?>

                    <div class="row">

                        <!-- Form Fields -->
                        <div class="col-md-8 border-end">
                            <input type="hidden" name="id" value="<?php echo (int)$setting->id; ?>">

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end" for="zoom_api_key">
                                    <?php echo $this->lang->line('zoom_api_key'); ?>
                                </label>
                                <div class="col-md-7">
                                    <input id="zoom_api_key" name="zoom_api_key" type="text" class="form-control" value="<?php echo html_escape(set_value('zoom_api_key', $setting->zoom_api_key)); ?>">
                                    <span class="text-danger"><?php echo form_error('zoom_api_key'); ?></span>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end" for="zoom_api_secret">
                                    <?php echo $this->lang->line('zoom_api_secret'); ?>
                                </label>
                                <div class="col-md-7">
                                    <input id="zoom_api_secret" name="zoom_api_secret" type="text" class="form-control" value="<?php echo html_escape(set_value('zoom_api_secret', $setting->zoom_api_secret)); ?>">
                                    <span class="text-danger"><?php echo form_error('zoom_api_secret'); ?></span>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end">
                                    <?php echo $this->lang->line('doctor_api_credential'); ?>
                                </label>
                                <div class="col-md-7 d-flex align-items-center gap-3">
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="use_doctor_api" value="0" <?php if (!$setting->use_doctor_api) echo 'checked'; ?>>
                                        <span class="form-check-label"><?php echo $this->lang->line('disabled'); ?></span>
                                    </label>
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="use_doctor_api" value="1" <?php if ($setting->use_doctor_api) echo 'checked'; ?>>
                                        <span class="form-check-label"><?php echo $this->lang->line('enabled'); ?></span>
                                    </label>
                                    <span class="text-danger"><?php echo form_error('use_teacher_api'); ?></span>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end">
                                    <?php echo $this->lang->line('use_zoom_client_app'); ?>
                                </label>
                                <div class="col-md-7 d-flex align-items-center gap-3">
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="use_zoom_app" value="0" <?php if (!$setting->use_zoom_app) echo 'checked'; ?>>
                                        <span class="form-check-label"><?php echo $this->lang->line('disabled'); ?></span>
                                    </label>
                                    <label class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="use_zoom_app" value="1" <?php if ($setting->use_zoom_app) echo 'checked'; ?>>
                                        <span class="form-check-label"><?php echo $this->lang->line('enabled'); ?></span>
                                    </label>
                                    <span class="text-danger"><?php echo form_error('use_zoom_app'); ?></span>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end" for="opdduration">
                                    <?php echo $this->lang->line('default_opd_duration_in_minutes'); ?>
                                </label>
                                <div class="col-md-7">
                                    <input type="number" class="form-control" value="<?php echo html_escape(set_value('opd_duration', $setting->opd_duration)); ?>" id="opdduration" name="opd_duration">
                                    <span class="text-danger"><?php echo form_error('opd_duration'); ?></span>
                                </div>
                            </div>

                            <div class="row mb-0 align-items-center">
                                <label class="col-md-4 col-form-label text-md-end" for="ipdduration">
                                    <?php echo $this->lang->line('default_ipd_duration_in_minutes'); ?>
                                </label>
                                <div class="col-md-7">
                                    <input type="number" class="form-control" value="<?php echo html_escape(set_value('ipd_duration', $setting->ipd_duration)); ?>" id="ipdduration" name="ipd_duration">
                                    <span class="text-danger"><?php echo form_error('ipd_duration'); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Panel -->
                        <div class="col-md-4 ps-4">
                            <div class="card border-0 rounded-3 h-100 zoom-info-panel">
                                <div class="card-body p-4">

                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <img src="<?php echo $this->media_storage->getImageURL('backend/images/zoom-icon.png'); ?>" class="zoom-icon">
                                        <h6 class="mb-0 fw-bold text-primary"><?php echo $this->lang->line('zoom_oauth_setup'); ?></h6>
                                    </div>

                                    <div class="alert alert-primary border-0 py-2 px-3 mb-3 small" role="alert">
                                        <i class="fa fa-info-circle me-1"></i>
                                        <?php echo $this->lang->line('to_set_zoom_api'); ?>
                                        <a href="https://marketplace.zoom.us/" target="_blank" class="alert-link fw-semibold">
                                            <?php echo $this->lang->line('click_here'); ?> &rarr;
                                        </a>
                                    </div>

                                    <p class="small fw-bold mb-1">
                                        <i class="fa fa-link me-1 text-primary"></i>
                                        <?php echo $this->lang->line('set_zoom_redirect_url'); ?>:
                                    </p>
                                    <div class="rounded p-2 mb-3 border border-primary-subtle zoom-url-box">
                                        <code class="small text-primary text-break d-block"><?php echo site_url('admin/zoom_conference/generatetoken'); ?></code>
                                    </div>

                                    <?php if ($this->session->has_userdata('zoom_access_token')) { ?>
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-success"><i class="fa fa-check me-1"></i><?= $this->lang->line('token_active') ?></span>
                                        </div>
                                    <?php } else { ?>
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-warning text-dark"><i class="fa fa-exclamation-triangle me-1"></i><?= $this->lang->line('token_not_generated') ?></span>
                                        </div>
                                    <?php } ?>

                                    <a href="<?php echo $oAuthURL; ?>" class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-key me-1"></i> <?php echo $this->lang->line('get_access_token'); ?>
                                    </a>

                                </div>
                            </div>
                        </div>

                    </div>
                </div><!--./card-body-->

                <?php if ($this->rbac->hasPrivilege('setting', 'can_edit')) { ?>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                <?php } ?>

            </form>
        </div>
    </div>
</div>

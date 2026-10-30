<?php /* ?><div class="row">
    <?php $this->load->view('setting/sidebar'); ?>
    <div class="col-md-10">

        <!-- System Update Card -->
        <div class="card mb-3">
            <div class="card-header ptbnull">
                <h3 class="card-title titlefix">
                    <i class="fa fa-cloud-download"></i> <?php echo $this->lang->line('system_update'); ?>
                    <?php if (isset($version) && $version != ''): ?>
                        <span class="stat-badge queue"><i class="fa fa-cloud-download"></i> Update Available</span>
                    <?php else: ?>
                        <span class="stat-badge direct"><i class="fa fa-check-circle"></i> Up to date</span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="card-body">

                <!-- Hero: App identity + version -->
                <?php
                    $logoresult = $this->customlib->getLogoImage();
                    $hero_logo  = (!empty($logoresult['mini_logo']))
                        ? base_url('uploads/hospital_content/logo/' . $logoresult['mini_logo'])
                        : base_url('backend/images/s-favican.png');
                ?>
                <div class="sh-updater-hero">
                    <div class="sh-updater-hero-left">
                        <div class="sh-updater-hero-icon"><img src="<?php echo $hero_logo; ?>" alt="<?php echo html_escape($this->customlib->getAppName()); ?>"></div>
                        <div>
                            <div class="sh-updater-hero-name">Smart Hospital</div>
                            <div class="sh-updater-hero-subtitle">Hospital Management System</div>
                        </div>
                    </div>
                    <div class="sh-updater-hero-right">
                        <div class="sh-updater-hero-ver-label"><?php echo $this->lang->line('your_app_name_version'); ?></div>
                        <div class="sh-updater-hero-ver-num"><?php echo $current_version; ?></div>
                    </div>
                </div>

                <!-- Progress bar — shown during update -->
                <div class="d-none mt-3" id="update-progress">
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"></div>
                    </div>
                    <p class="text-center text-muted small mb-0">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Applying update &mdash; please do not close this page&hellip;
                    </p>
                </div>

                <!-- Update available strip with inline action -->
                <?php if (isset($version) && $version != ''): ?>
                <div class="sh-update-strip mt-3">
                    <div class="sh-update-strip-icon"><i class="fa fa-cloud-download"></i></div>
                    <div class="sh-update-strip-body">
                        <div class="sh-update-strip-title"><?php echo $this->lang->line('latest_app_name_version'); ?></div>
                        <div class="sh-update-strip-sub">New update available &mdash; features &amp; security fixes</div>
                    </div>
                    <div class="sh-update-strip-ver"><?php echo $version; ?></div>
                    <form method="POST" action="<?php echo site_url('admin/updater'); ?>" id="form-update">
                        <button type="button" class="btn btn-primary btn-submit">
                            <i class="fa fa-refresh me-1"></i><?php echo $this->lang->line('update_now'); ?>
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Success messages from update -->
                <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-success mt-3 mb-0">
                    <ul class="sh-update-list">
                        <?php foreach ($this->session->flashdata('message') as $msg): ?>
                        <li><i class="fa fa-check-circle me-1"></i><?php echo $msg; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Error messages from update -->
                <?php if (!$this->session->flashdata('message') && $this->session->flashdata('error')): ?>
                <div class="alert alert-danger mt-3 mb-0">
                    <ul class="sh-update-list">
                        <?php foreach ($this->session->flashdata('error') as $err): ?>
                        <li><i class="fa fa-exclamation-circle me-1"></i><?php echo $err; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Environment Meta Grid -->
                <div class="sh-updater-meta-grid mt-3">
                    <div class="sh-updater-meta-item">
                        <div class="sh-updater-meta-icon"><i class="fa fa-code"></i></div>
                        <div>
                            <div class="sh-updater-meta-key">PHP</div>
                            <div class="sh-updater-meta-val"><?php echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION; ?></div>
                        </div>
                    </div>
                    <div class="sh-updater-meta-item">
                        <div class="sh-updater-meta-icon"><i class="fa fa-server"></i></div>
                        <div>
                            <div class="sh-updater-meta-key">Environment</div>
                            <div class="sh-updater-meta-val"><?php echo ucfirst(ENVIRONMENT); ?></div>
                        </div>
                    </div>
                    <div class="sh-updater-meta-item">
                        <div class="sh-updater-meta-icon"><i class="fa fa-key"></i></div>
                        <div>
                            <div class="sh-updater-meta-key">License</div>
                            <div class="sh-updater-meta-val">Active</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- PHP Info Card -->
        <div class="card mb-3">
            <div class="card-header ptbnull d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h3 class="card-title titlefix mb-0">
                    <i class="fa fa-server"></i> Hosting Server PHP Information
                </h3>
                <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm" id="btn-phpinfo-toggle">
                    <i class="fa fa-eye-slash me-1"></i><?php echo $this->lang->line('hide'); ?>
                </a>
            </div>
            <div class="card-body" id="phpinfo-body">
                <?php $this->load->view('admin/updater/qdtest'); ?>
            </div>
        </div>

    </div>
</div>

<!-- Confirm Update Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="confirm-update" tabindex="-1" aria-labelledby="confirmUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUpdateLabel"><?php echo $this->lang->line('confirmation'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo $this->lang->line('updater_instruction'); ?></p>
                <p><?php echo $this->lang->line('do_you_want_to_proceed'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn btn-primary confirm-yes"><?php echo $this->lang->line('yes'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $('#btn-phpinfo-toggle').on('click', function () {
        var $body = $('#phpinfo-body');
        var $btn  = $(this);
        if ($body.hasClass('d-none')) {
            $body.removeClass('d-none');
            $btn.html('<i class="fa fa-eye-slash me-1"></i><?php echo $this->lang->line('hide'); ?>');
        } else {
            $body.addClass('d-none');
            $btn.html('<i class="fa fa-eye me-1"></i><?php echo $this->lang->line('show'); ?>');
        }
    });

    $('.btn-submit').on('click', function () {
        shModal('confirm-update').show();
    });

    $('.confirm-yes').on('click', function () {
        shModal('confirm-update').hide();
        $('#update-progress').removeClass('d-none');
        $('form#form-update').submit();
    });
});
</script>

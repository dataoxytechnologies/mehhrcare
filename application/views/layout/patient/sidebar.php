<nav class="sh-sidebar" id="sidebar">
    <div class="sh-sidebar-brand">
        <?php
        $logoresult_sb = $this->customlib->getLogoImage();
        $sb_logo = (!empty($logoresult_sb['mini_logo']))
            ? base_url('uploads/hospital_content/logo/' . $logoresult_sb['mini_logo'])
            : base_url('backend/images/s-favican.png');
        ?>
        <a href="<?php echo site_url('patient/dashboard'); ?>" class="sh-sidebar-logo">
            <img src="<?php echo $sb_logo; ?>" alt="Logo" class="sh-sidebar-logo-img">
            <span class="sh-sidebar-name"><?php echo $this->customlib->getAppName(); ?></span>
        </a>
    </div>

    <div class="sh-sidebar-scroll">
        <ul class="sh-nav" id="sh-sidenav">
            <?php if (!empty($_SESSION['patient'])): ?>

            <li class="nav-item <?php echo set_Topmenu('dashboard'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard'); ?>">
                    <i class="fas fa-television"></i><span> <?php echo $this->lang->line('dashboard'); ?></span>
                </a>
            </li>

            <?php if ($this->module_lib->hasActive('appointment') && $this->module_lib->hasPatientActive('my_appointments')): ?>
            <li class="nav-item <?php echo set_Topmenu('myprofile'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/appointment'); ?>">
                    <i class="fa fa-calendar-check-o"></i><span> <?php echo $this->lang->line('my_appointments'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('opd') && $this->module_lib->hasPatientActive('opd')): ?>
            <li class="nav-item <?php echo set_Topmenu('profile'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/profile'); ?>">
                    <i class="fas fa-stethoscope"></i><span> <?php echo $this->lang->line('opd'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('ipd') && $this->module_lib->hasPatientActive('ipd')): ?>
            <li class="nav-item <?php echo set_Topmenu('ipdprofile'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/ipdprofile'); ?>">
                    <i class="fas fa-procedures"></i><span> <?php echo $this->lang->line('ipd'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('pharmacy') && $this->module_lib->hasPatientActive('pharmacy')): ?>
            <li class="nav-item <?php echo set_Topmenu('pharmacy'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/bill'); ?>">
                    <i class="fas fa-mortar-pestle"></i><span> <?php echo $this->lang->line('pharmacy'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('pathology') && $this->module_lib->hasPatientActive('pathology')): ?>
            <li class="nav-item <?php echo set_Topmenu('pathology'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/search'); ?>">
                    <i class="fas fa-flask"></i><span> <?php echo $this->lang->line('pathology'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('radiology') && $this->module_lib->hasPatientActive('radiology')): ?>
            <li class="nav-item <?php echo set_Topmenu('radiology'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/radioreport'); ?>">
                    <i class="fas fa-microscope"></i><span> <?php echo $this->lang->line('radiology'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('blood_bank') && $this->module_lib->hasPatientActive('blood_bank')): ?>
            <li class="nav-item <?php echo set_Topmenu('blood_bank'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/bloodbank'); ?>">
                    <i class="fas fa-tint"></i><span> <?php echo $this->lang->line('blood_bank'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('ambulance') && $this->module_lib->hasPatientActive('ambulance')): ?>
            <li class="nav-item <?php echo set_Topmenu('ambulance'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/ambulance'); ?>">
                    <i class="fas fa-ambulance"></i><span> <?php echo $this->lang->line('ambulance'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('live_consultation') && $this->module_lib->hasPatientActive('live_consultation')): ?>
            <li class="nav-item <?php echo set_Topmenu('live_consult'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/liveconsult'); ?>">
                    <i class="fa fa-video-camera"></i><span> <?php echo $this->lang->line('live_consultation'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasActive('download_center') && $this->module_lib->hasPatientActive('download_center')): ?>
            <li class="nav-item <?php echo set_Topmenu('content_list'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/dashboard/contentlist'); ?>">
                    <i class="fas fa-download"></i><span> <?php echo $this->lang->line('download_center'); ?></span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($this->module_lib->hasModule('survey_form')): ?>
			<?php if ($this->module_lib->hasActive('survey_form') && $this->module_lib->hasPatientActive('survey_form')): ?>
            <li class="nav-item <?php echo set_Topmenu('survey'); ?>">
                <a class="nav-link" href="<?php echo base_url('patient/survey'); ?>">
                    <i class="fa fa-file-text-o"></i><span> <?php echo $this->lang->line('survey_forms'); ?></span>
                    <?php $cf_count_sb = $this->customlib->getPatientPendingCustomFormCount(); ?>
                    <?php if ($cf_count_sb > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $cf_count_sb; ?></span>
                    <?php endif; ?>
                </a>
            </li>
			<?php endif; ?>
			<?php endif; ?>

            <?php endif; // patient session check ?>
        </ul>
    </div>
</nav>

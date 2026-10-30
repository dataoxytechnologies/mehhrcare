<nav class="sh-sidebar" id="sidebar">
    <!-- Sidebar brand/logo -->
    <div class="sh-sidebar-brand">
        <?php
        $logoresult_sb = $this->customlib->getLogoImage();
        $sb_logo = (!empty($logoresult_sb['mini_logo']))
            ? base_url('uploads/hospital_content/logo/' . $logoresult_sb['mini_logo'])
            : base_url('backend/images/s-favican.png');
        ?>
        <a href="<?php echo base_url('admin/admin/dashboard'); ?>" class="sh-sidebar-logo">
            <img src="<?php echo $sb_logo; ?>" alt="Logo" class="sh-sidebar-logo-img">
            <span class="sh-sidebar-name"><?php echo $this->customlib->getAppName(); ?></span>
        </a>
    </div>

    <!-- Menu filter -->
    <div class="sh-sidebar-search">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fa fa-search"></i></span>
            <input type="text" id="sidebar-menu-filter" class="form-control" placeholder="<?php echo $this->lang->line('filter_menu'); ?>" autocomplete="off">
            <button type="button" class="sh-search-clear" id="sidebar-menu-filter-clear" aria-label="Clear" tabindex="-1"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- Navigation menu -->
    <div class="sh-sidebar-scroll">
        <ul class="sh-nav" id="sh-sidenav">
            <li class="sh-section"><?= $this->lang->line('nav_core_operations') ?></li>
            <li class="nav-item <?php echo set_Topmenu('dashboard'); ?>">
                <a class="nav-link" href="<?php echo base_url(); ?>admin/admin/dashboard">
                   <i class="fas fa-television"></i> <span> <?php echo $this->lang->line('dashboard'); ?></span>
               </a>
           </li>  

<?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')) { ?>
                        <li class="nav-item <?php echo set_Topmenu('patient_flow'); ?>">
                            <a class="nav-link d-flex align-items-center" href="#" role="button">
                                <i class="fas fa-route"></i> <span><?php echo $this->lang->line('patient_flow') ?: 'Patient Flow'; ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                            </a>
                            <ul class="sh-subnav <?php echo set_Topmenu('patient_flow') ? 'show' : ''; ?>" id="sub-patientflow">
                                <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')) { ?>
                                <li class="nav-item" data-submenu="patient_flow"><a class="nav-link" href="<?php echo base_url('admin/patientflow'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('patient_flow') ?: 'Patient Flow'; ?></a></li>
                                <li class="nav-item" data-submenu="patient_flow_token_queue"><a class="nav-link" href="<?php echo base_url('admin/patientflow/token_queue'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('token_queue') ?: 'Token Queue'; ?></a></li>
                                <?php } ?>
                                <?php if ($this->rbac->hasPrivilege('patient_flow_levels', 'can_view')) { ?>
                                <li class="nav-item" data-submenu="patient_flow_levels"><a class="nav-link" href="<?php echo base_url('admin/patientflow/levels'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('add_patient_flow_level') ?: 'Levels'; ?></a></li>
                                <?php } ?>
                                
                                <li class="nav-item" data-submenu="patient_flow_setting"><a class="nav-link" href="<?php echo base_url('admin/patientflowsetting/setting?group=constitution_master'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('patient_flow_master') ?: 'Master'; ?></a></li>
                                <!-- <li class="nav-item" data-submenu="patient_flow_add"><a class="nav-link" href="<?php echo base_url('admin/patientflow/register'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('add_patient') ?: 'Add Patient'; ?></a></li> -->
                            </ul>
                        </li>
                        <?php } ?>

           <?php
           if($this->module_lib->hasActive('patient')){
                if ($this->rbac->hasPrivilege('patient', 'can_view')) { ?>
                    <li class="nav-item <?php echo set_Topmenu('patient'); ?>">
                        <a class="nav-link" href="<?php echo base_url(); ?>admin/admin/search">
                        <i class="fa fa-user"></i><span> <?php echo $this->lang->line('patient'); ?></span></a>
                    </li>
            <?php
                }
                } ?>			
			
            <?php
                if($this->module_lib->hasActive('bill')){
                    if(($this->rbac->hasPrivilege('opd_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('opd_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('ipd_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('ipd_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('pharmacy_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('pharmacy_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('pathology_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('pathology_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('radiology_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('radiology_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('blood_bank_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('blood_bank_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('ambulance_billing','can_view')) ||
                        ($this->rbac->hasPrivilege('ambulance_billing_payment','can_view')) ||
                        ($this->rbac->hasPrivilege('generate_bill','can_view')) ||
                        ($this->rbac->hasPrivilege('generate_discharge_card','can_view'))){ ?>
                        <li class="nav-item <?php echo set_Topmenu('bill'); ?>">
                            <a class="nav-link" href="<?php echo site_url('admin/bill/dashboard'); ?>">
                                <i class="fas fa-file-invoice"></i> <span> <?php echo $this->lang->line('billing'); ?></span>
                            </a>
                        </li>
            <?php
                    } 
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('appointment')) {
                    if ($this->rbac->hasPrivilege('online_appointment_slot','can_view')||
						$this->rbac->hasPrivilege('online_appointment_doctor_shift','can_view')||
						$this->rbac->hasPrivilege('online_appointment_shift','can_view')||
						$this->rbac->hasPrivilege('doctor_wise_appointment','can_view')||
						$this->rbac->hasPrivilege('patient_queue','can_view')||
						$this->rbac->hasPrivilege('appointment','can_view')||
						$this->rbac->hasPrivilege('reschedule','can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('appointment'); ?>">
                            <a class="nav-link"  href="<?php echo base_url(); ?>admin/appointment/index">
                                <i class="fa fa-calendar-check-o"></i> <span><?php echo $this->lang->line('appointment'); ?></span>
                            </a>
                        </li>
            <?php
					}
				}
            ?>
            <?php
                if ($this->module_lib->hasActive('opd')) {
                    if ($this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('OPD_Out_Patient'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/patient/search">
                                <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('opd_out_patient'); ?></span>
                            </a>
                        </li>
            <?php 
                    }
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('ipd')) {
                    if ($this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('IPD_in_patient'); ?>">
                            <a class="nav-link" href="<?php echo base_url() ?>admin/patient/ipdsearch">
                                <i class="fas fa-procedures" aria-hidden="true"></i> <span> <?php echo $this->lang->line('ipd_in_patient'); ?></span>
                            </a>
                        </li>
            <?php 
                    }
                } 
            ?>
            <li class="sh-section"><?= $this->lang->line('nav_clinical') ?></li>
            <?php
                if ($this->module_lib->hasActive('pharmacy')) {
                    if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('pharmacy'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/pharmacy/bill">
                                <i class="fas fa-mortar-pestle"></i> <span> <?php echo $this->lang->line('pharmacy'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('pathology')) {
                    if ($this->rbac->hasPrivilege('pathology_test', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('pathology'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/pathology/gettestreportbatch">
                                <i class="fas fa-flask"></i> <span><?php echo $this->lang->line('pathology'); ?></span>
                            </a>
                        </li>
            <?php 
                    }
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('radiology')) {
                    if ($this->rbac->hasPrivilege('radiology_test', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('radiology'); ?>">                               
                            <a class="nav-link" href="<?php echo base_url() ?>admin/radio/gettestreportbatch">
                                <i class="fas fa-microscope"></i> <span><?php echo $this->lang->line('radiology'); ?></span>
                            </a>
                        </li>
            <?php 
                    }
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('blood_bank')) {
                    if (($this->rbac->hasPrivilege('blood_issue', 'can_view')) || 
                        ($this->rbac->hasPrivilege('blood_donor', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_stock', 'can_view')) || 
                        ($this->rbac->hasPrivilege('bloodbank_print_header_footer', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_bank_product', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_bank_components', 'can_view')) ||
                        ($this->rbac->hasPrivilege('issue_component', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_bank_product', 'can_view'))
                        ) { ?>
                        <li class="nav-item <?php echo set_Topmenu('blood_bank'); ?>">
                            <a class="nav-link" href="<?php echo base_url() ?>admin/bloodbankstatus/">
                                <i class="fas fa-tint"></i> <span><?php echo $this->lang->line('blood_bank'); ?></span>
                            </a>
                        </li>
            <?php 
                    }
                }
            ?>
			
            <?php 
                if ($this->module_lib->hasActive('ambulance')) {
                    if ($this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
                    ?>
                        <li class="nav-item <?php echo set_Topmenu('Transport'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/vehicle/getcallambulance">
                                <i class="fas fa-ambulance" aria-hidden="true"></i>
                                <span> <?php echo $this->lang->line('ambulance'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                } 
            ?>
			
            <?php
                if ($this->module_lib->hasActive('front_office')) {
                    if(($this->rbac->hasPrivilege('visitor_book','can_view')) ||
                        ($this->rbac->hasPrivilege('phone_call_log','can_view')) ||
                        ($this->rbac->hasPrivilege('postal_dispatch','can_view')) ||
                        ($this->rbac->hasPrivilege('postal_receive','can_view')) ||
                        ($this->rbac->hasPrivilege('complain','can_view')) ||
                        ($this->rbac->hasPrivilege('setup_front_office','can_view')))
                        { ?>
                        <li class="nav-item <?php echo set_Topmenu('front_office'); ?>">
                            <a class="nav-link"  href="<?php echo base_url(); ?>admin/visitors">
                                <i class="fas fa-dungeon"></i> <span><?php echo $this->lang->line('front_office'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                }
            ?>
			
            <li class="sh-section"><?= $this->lang->line('nav_administration') ?></li>
            <?php
                if (($this->module_lib->hasActive('birth_death_report')) || ($this->module_lib->hasActive('birth_death_report'))) {
                    if (($this->rbac->hasPrivilege('birth_record', 'can_view')) || ($this->rbac->hasPrivilege('death_record', 'can_view'))) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('birthordeath'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/birthordeath"><i class="fa fa-birthday-cake" aria-hidden="true"></i><span> <?php echo $this->lang->line('birth_death_record'); ?></span><i class="fa fa-angle-down ms-auto sidebar-arrow"></i></a>
                            <ul class="sh-subnav <?php echo set_Topmenu('birthordeath') ? 'show' : ''; ?>" id="sub-1">
                            <?php
                                if ($this->module_lib->hasActive('birth_death_report')) {
                                    if ($this->rbac->hasPrivilege('birth_record', 'can_view')) {
                                ?>
                                    <li class="nav-item" data-submenu="birthordeath/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/birthordeath"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('birth_record'); ?> </a></li>
                            <?php
                                    }
                                }

                                if ($this->rbac->hasPrivilege('death_record', 'can_view')) {
                            ?>
                                <li class="nav-item" data-submenu="birthordeath/death"><a class="nav-link" href="<?php echo base_url(); ?>admin/birthordeath/death"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('death_record'); ?></a></li>
                                <?php }?>
                            </ul>
                        </li>
            <?php
                    }
                }
            ?>
			
			<?php  
			if($this->auth->addonchk('shmb')){
			if($this->module_lib->hasModule('multi_branch')){ ?> 
            <?php  if($this->module_lib->hasActive('multi_branch')){ ?> 
					<?php if(($this->rbac->hasPrivilege('multi_branch_appointment_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_opd_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ipd_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_pharmacy_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_medicine_expiry_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_pathology_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_radiology_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_blood_issue_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_component_issue_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ambulance_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_birth_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_payroll_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_income_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_expense_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_live_consultation_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ot_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_transaction_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_overview','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_setting','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_appointment','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_opd','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_ipd','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_operation_theatre','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_pharmacy','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_pathology','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_radiology','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_blood_donor','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_blood_issue','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_component_issue','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_ambulance','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_birth_record','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_death_record','can_view')) ||
						($this->rbac->hasPrivilege('multi_branch_overview_staff_attendance','can_view'))||
						($this->rbac->hasPrivilege('multi_branch_overview_staff_payroll','can_view'))||
						($this->rbac->hasPrivilege('multi_branch_overview_transactions','can_view'))||
						($this->rbac->hasPrivilege('multi_branch_blood_donor_report','can_view')) ){ ?>
			
            <li class="nav-item <?php echo set_Topmenu('multibranch'); ?>">
				<a class="nav-link d-flex align-items-center" href="#" role="button"><i class="fa fa-sitemap ftlayer"></i> <span> <?php echo $this->lang->line('multi_branch'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i></a>
				<ul class="sh-subnav <?php echo set_Topmenu('multibranch') ? 'show' : ''; ?>" id="sub-2">
				
					<?php if(($this->rbac->hasPrivilege('multi_branch_overview','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_appointment','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_opd','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_ipd','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_operation_theatre','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_pharmacy','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_pathology','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_radiology','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_blood_donor','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_blood_issue','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_component_issue','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_ambulance','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_birth_record','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_death_record','can_view')) ||
						 ($this->rbac->hasPrivilege('multi_branch_overview_staff_attendance','can_view'))||
						 ($this->rbac->hasPrivilege('multi_branch_overview_staff_payroll','can_view'))||
						 ($this->rbac->hasPrivilege('multi_branch_overview_transactions','can_view'))
					){ ?>
					<li class="nav-item" data-submenu="admin/multibranch/branch/overview"><a class="nav-link" href="<?php echo site_url('admin/multibranch/branch/overview'); ?>"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('overview'); ?></a></li>	
					<?php } ?>
					
					<?php if(($this->rbac->hasPrivilege('multi_branch_appointment_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_opd_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ipd_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_pharmacy_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_medicine_expiry_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_pathology_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_radiology_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_blood_issue_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_component_issue_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ambulance_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_birth_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_payroll_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_income_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_expense_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_live_consultation_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_ot_report','can_view')) ||
                        ($this->rbac->hasPrivilege('multi_branch_transaction_report','can_view'))||
                        ($this->rbac->hasPrivilege('multi_branch_blood_donor_report','can_view'))  ){ ?>
					<li class="nav-item" data-submenu="admin/multibranch/branch/report"><a class="nav-link" href="<?php echo site_url('admin/multibranch/branch/report'); ?>"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('report'); ?></a></li>
					<?php } ?>
					
					<?php if($this->rbac->hasPrivilege('multi_branch_setting','can_view')){ ?>
					<li class="nav-item" data-submenu="admin/multibranch/branch"><a class="nav-link" href="<?php echo site_url('admin/multibranch/branch'); ?>"><i class="fa fa-angle-double-right"></i> <?php echo $this->lang->line('settings'); ?></a></li>
					<?php } ?>		 
                 

				</ul>
			</li> 
			<?php } } } } ?>
			
            <?php
                if ($this->module_lib->hasActive('human_resource')) {
                    if (($this->rbac->hasPrivilege('staff', 'can_view') ||
                        $this->rbac->hasPrivilege('staff_attendance', 'can_view') ||
                        $this->rbac->hasPrivilege('staff_payroll', 'can_view'))) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('HR'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/staff">
                                <i class="fas fa-sitemap"></i> <span><?php echo $this->lang->line('human_resource'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                }
            ?>
			
			<?php
				if($this->auth->addonchk('shqra')){
				if($this->module_lib->hasModule('qr_code_attendence')){
                if($this->module_lib->hasActive('qr_code_attendence')){			
                    if(($this->rbac->hasPrivilege('qr_code_attendance','can_view')) ||
                        ($this->rbac->hasPrivilege('qr_code_setting','can_view'))){ ?>
						<li class="nav-item <?php echo set_Topmenu('qrattendance'); ?>">
							<a class="nav-link" href="<?php echo base_url(); ?>admin/qrattendance/setting/index"><i class="fa fa-qrcode" aria-hidden="true"></i><span><?php echo $this->lang->line('qr_code_attendance'); ?></span><i class="fa fa-angle-down ms-auto sidebar-arrow"></i></a>
							<ul class="sh-subnav <?php echo set_Topmenu('qrattendance') ? 'show' : ''; ?>" id="sub-3">
									
								<?php if ($this->rbac->hasPrivilege('qr_code_attendance', 'can_view')) { ?>
								
									<li class="nav-item" data-submenu="admin/qrattendance/attendance/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/qrattendance/attendance/index"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('attendance'); ?> </a></li>
									
								<?php } if ($this->rbac->hasPrivilege('qr_code_setting', 'can_view')) { ?>
								
									<li class="nav-item" data-submenu="admin/qrattendance/setting/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/qrattendance/setting/index"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('settings'); ?></a></li>
									
								<?php } ?>
								
							</ul>
						</li>
				<?php } } } } ?>
			
			<?php 
				if($this->module_lib->hasActive('duty_roster')){ 
					if($this->rbac->hasPrivilege('duty_roster','can_view') || $this->rbac->hasPrivilege('roster_shift','can_view') || $this->rbac->hasPrivilege('roster_list','can_view') || $this->rbac->hasPrivilege('roster_assign','can_view') ){ 
					?>
						<li class="nav-item <?php echo set_Topmenu('dutyroster'); ?>">
                            <a class="nav-link"  href="<?php echo base_url(); ?>admin/dutyroster/roster_report">
                                <i class="fas fa-clock"></i> <span><?php echo $this->lang->line("duty_roster"); ?></span>
                            </a>
                        </li>         
			<?php 
					}
				}
			?>	
			
			<?php
                if($this->module_lib->hasActive('annual_calendar')){
                    if(($this->rbac->hasPrivilege('annual_calendar','can_view'))){ ?>
					<li class="nav-item <?php echo set_Topmenu('annual_calendar'); ?>">
                            <a class="nav-link"  href="<?php echo base_url(); ?>admin/holiday/index">
                                <i class="fas fa-calculator"></i> <span> <?php echo $this->lang->line('annual_calendar'); ?> </span>
                            </a>
                    </li>  
					<?php
                    } 
                }
            ?>
			
            <?php
                if($this->module_lib->hasActive('referral')){
                    if ($this->rbac->hasPrivilege('referral_payment', 'can_view')) {  ?>
                        <li class="nav-item <?php echo set_Topmenu('referral_payment'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/referral/payment">
                                <i class="fas fa-users"></i> <span><?php echo $this->lang->line('referral'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                }
            ?>
            <?php
                if ($this->module_lib->hasActive('tpa_management')) {
                    if ($this->rbac->hasPrivilege('organisation', 'can_view')) {
                        ?>
                        <li class="nav-item <?php echo set_Topmenu('tpa_management'); ?>">
                            <a class="nav-link" href="<?php echo base_url() ?>admin/tpamanagement">
                                <i class="fas fa-umbrella"></i> <span><?php echo $this->lang->line('tpa_management'); ?></span>
                            </a>
                        </li>
            <?php
                    }
                }
            ?>
            <?php
                if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) {
                    if (($this->rbac->hasPrivilege('income', 'can_view')) || ($this->rbac->hasPrivilege('expense', 'can_view'))) {
                        ?>
                            <li class="nav-item <?php echo set_Topmenu('finance'); ?>">
                                <a class="nav-link" href="#">
                                <i class="fas fa-money-bill-wave"></i> <span><?php echo $this->lang->line('finance'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                                </a>
                                <ul class="sh-subnav <?php echo set_Topmenu('finance') ? 'show' : ''; ?>" id="sub-4">
                                    <?php
                                        if ($this->module_lib->hasActive('income')) {
                                            if ($this->rbac->hasPrivilege('income', 'can_view')) {
                                    ?>
                                                <li class="nav-item" data-submenu="income/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/income"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('income'); ?> </a></li>
                                    <?php
                                            }
                                        }
                                        if ($this->module_lib->hasActive('expense')) {
                                            if ($this->rbac->hasPrivilege('expense', 'can_view')) {
                                    ?>
                                                <li class="nav-item" data-submenu="expense/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/expense"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('expenses'); ?></a></li>
                                    <?php 
                                            }
                                        }
                                    ?>
                                </ul>
                            </li>
                <?php
                        }
                    }
                ?>
                <li class="sh-section"><?= $this->lang->line('nav_tools_content') ?></li>					
						
                <?php
					if($this->auth->addonchk('shsf')){
					if ($this->module_lib->hasModule('survey_form')) {
					if($this->module_lib->hasActive('survey_form')){	
                    if (($this->rbac->hasPrivilege('survey_form', 'can_view')) || ($this->rbac->hasPrivilege('my_survey', 'can_view')) ) { ?>
                        <li class="nav-item <?php echo set_Topmenu('survey'); ?>">
                            <a class="nav-link" href="<?php echo base_url(); ?>admin/survey"><i class="fas fa-clipboard-list"></i><span> <?php echo $this->lang->line('survey_forms'); ?></span><i class="fa fa-angle-down ms-auto sidebar-arrow"></i></a>
                            <ul class="sh-subnav <?php echo set_Topmenu('survey') ? 'show' : ''; ?>" id="sub-11">
								<?php if ($this->rbac->hasPrivilege('survey_form', 'can_view')) { ?>
								
                                <li class="nav-item" data-submenu="admin/survey"><a class="nav-link" href="<?php echo base_url(); ?>admin/survey"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('survey_forms'); ?></a></li>
								
								<?php } if ($this->rbac->hasPrivilege('my_survey', 'can_view')) { ?>
								
                                <li class="nav-item" data-submenu="admin/survey/staff_forms"><a class="nav-link" href="<?php echo base_url(); ?>admin/survey/staff_forms"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('my_forms'); ?></a></li>
								
								<?php } ?>
                            </ul>
                        </li>
						
					<?php } } } } ?>				
				
                <?php
                    if ($this->module_lib->hasActive('communicate')) {
                        if (($this->rbac->hasPrivilege('notice_board', 'can_view') ||
                            $this->rbac->hasPrivilege('email_sms', 'can_view') ||
                            $this->rbac->hasPrivilege('email_sms_log', 'can_view'))) {
                            ?>
                            <li class="nav-item <?php echo set_Topmenu('Messaging'); ?>">
                                <a class="nav-link" href= "<?php echo base_url(); ?>admin/notification">
                                    <i class = "fas fa-bullhorn"></i> <span><?php echo $this->lang->line('messaging'); ?></span>
                                </a>
                            </li>
                <?php
                        }
                    }
                ?>
                <?php
                    if ($this->module_lib->hasActive('inventory')) {
                        if (($this->rbac->hasPrivilege('issue_item', 'can_view') ||
                            $this->rbac->hasPrivilege('item_stock', 'can_view') ||
                            $this->rbac->hasPrivilege('item', 'can_view') ||
                            $this->rbac->hasPrivilege('item_category', 'can_view') ||
                            $this->rbac->hasPrivilege('item_category', 'can_view') ||
                            $this->rbac->hasPrivilege('store', 'can_view') ||
                            $this->rbac->hasPrivilege('supplier', 'can_view'))) {
                            ?>
                            <li class="nav-item <?php echo set_Topmenu('Inventory'); ?>">
                                <a class="nav-link" href="<?php echo base_url(); ?>admin/itemstock">
                                    <i class="fas fa-luggage-cart"></i> <span><?php echo $this->lang->line('inventory'); ?></span>
                                </a>
                            </li>
                <?php
                        }
                    }
                ?>
                <?php
                    if ($this->module_lib->hasActive('download_center')) {
                        if (($this->rbac->hasPrivilege('upload_share_content', 'can_view')) || ($this->rbac->hasPrivilege('content_share_list', 'can_view')) ||  ($this->rbac->hasPrivilege('content_type', 'can_view'))  ) {
                            ?>
                            <li class="nav-item ">
                                
                            </li>
                            
                            <li class="nav-item <?php echo set_Topmenu('Download Center'); ?>">
                                <a class="nav-link d-flex align-items-center" href="#" role="button">
                                <i class="fas fa-download"></i> <span><?php echo $this->lang->line('download_center'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                                </a>
                                <ul class="sh-subnav <?php echo set_Topmenu('Download Center') ? 'show' : ''; ?>" id="sub-5">                                    
									
                                    <?php if ($this->rbac->hasPrivilege('upload_share_content', 'can_view')) { ?>
                                    
										<li class="nav-item" data-submenu="admin/content/upload"><a class="nav-link" href="<?php echo base_url(); ?>admin/content/upload"><i class="fas fa-angle-right"></i><span><?php echo $this->lang->line('upload_share_content'); ?></span></a></li>
                                    
                                    <?php } if ($this->rbac->hasPrivilege('content_share_list', 'can_view')) { ?>
									
										<li class="nav-item" data-submenu="admin/content/list"><a class="nav-link" href="<?php echo base_url('admin/content/list'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('content_share_list'); ?></a></li>
									
									<?php } if ($this->rbac->hasPrivilege('content_type', 'can_view')) { ?>
									
										<li class="nav-item" data-submenu="admin/contenttype"><a class="nav-link" href="<?php echo base_url('admin/contenttype/'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('content_type'); ?></a></li>  
									
									<?php } ?>
                                
                                </ul>
                            </li>
                            
                <?php
                        }
                    }
                ?>
                <?php 
                    if ($this->module_lib->hasActive('certificate')) {
                        if (($this->rbac->hasPrivilege('patient_id_card',"can_view"))||
                            ($this->rbac->hasPrivilege('generate_patient_id_card', "can_view"))||
                            ($this->rbac->hasPrivilege('staff_id_card',"can_view"))||
                            ($this->rbac->hasPrivilege('generate_staff_id_card',"can_view"))||
                            ($this->rbac->hasPrivilege('certificate',"can_view"))||
                            ($this->rbac->hasPrivilege('generate_certificate',"can_view"))) {
                            ?>
                            <li class="nav-item <?php echo set_Topmenu('Certificate'); ?>">
                                <a class="nav-link d-flex align-items-center" href="#" role="button">
                                <i class="fa fa-newspaper-o ftlayer"></i> <span><?php echo $this->lang->line('certificate'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                                </a>
                                <ul class="sh-subnav <?php echo set_Topmenu('Certificate') ? 'show' : ''; ?>" id="sub-6">
                                   <?php if ($this->rbac->hasPrivilege('certificate', 'can_view')) { ?>
                                    <li class="nav-item" data-submenu="admin/generatecertificate"><a class="nav-link" href="<?php echo base_url(); ?>admin/generatecertificate"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('certificate'); ?> </a></li>
                                <?php } if ($this->rbac->hasPrivilege('generate_patient_id_card', 'can_view')) { ?>
                                     <li class="nav-item" data-submenu="admin/generatepatientidcard"><a class="nav-link" href="<?php echo base_url('admin/generatepatientidcard/'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('patient_id_card'); ?></a></li>
                                     <?php }  if ($this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) { ?>
                                    <li class="nav-item" data-submenu="admin/generatestaffidcard"><a class="nav-link" href="<?php echo base_url('admin/generatestaffidcard/'); ?>"><i class="fas fa-angle-right"></i><?php echo $this->lang->line('staff_id_card');?></a></li>
                                <?php } ?>
                                </ul>
                            </li>
                       
                <?php 
                        }
                    }
                ?>
                <?php
                    if ($this->module_lib->hasActive('front_cms')) {
                        if (($this->rbac->hasPrivilege('event', 'can_view') ||
                            $this->rbac->hasPrivilege('gallery', 'can_view') ||
                            $this->rbac->hasPrivilege('notice', 'can_view') ||
                            $this->rbac->hasPrivilege('media_manager', 'can_view') ||
                            $this->rbac->hasPrivilege('pages', 'can_view') ||
                            $this->rbac->hasPrivilege('menus', 'can_view') ||
                            $this->rbac->hasPrivilege('banner_images', 'can_view'))) {
                            ?>
                            <li class="nav-item <?php echo set_Topmenu('Front CMS'); ?>">
                                <a class="nav-link" href="<?php echo base_url(); ?>admin/front/page">
                                    <i class="fas fa-solar-panel"></i> <span><?php echo $this->lang->line('front_cms'); ?></span>
                                </a>
                            </li>
                <?php
                        }
                    } 
                ?>
				
				<?php
				if($this->auth->addonchk('shtfa')){
				if($this->module_lib->hasModule('google_authenticator')){
                if($this->module_lib->hasActive('google_authenticator')){			
                    if(($this->rbac->hasPrivilege('google_authenticate_setting','can_view')) ||
                        ($this->rbac->hasPrivilege('google_authenticate_setup_two_fa','can_view'))){?>
						<li class="nav-item <?php echo set_Topmenu('two_factor_authentication'); ?>">
							<a class="nav-link" href="<?php echo base_url(); ?>admin/gauthenticate/admin/setup"><i class="fa fa-lock ftlayer" aria-hidden="true"></i><span><?php echo $this->lang->line('two_factor_authentication'); ?></span><i class="fa fa-angle-down ms-auto sidebar-arrow"></i></a>
							<ul class="sh-subnav <?php echo set_Topmenu('two_factor_authentication') ? 'show' : ''; ?>" id="sub-7">
							
								<?php if ($this->rbac->hasPrivilege('google_authenticate_setup_two_fa', 'can_view')) { ?>
								
									<li class="nav-item" data-submenu="admin/gauthenticate/admin/setup"><a class="nav-link" href="<?php echo base_url(); ?>admin/gauthenticate/admin/setup"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('setup_two_fa'); ?></a></li>
									
								<?php } if ($this->rbac->hasPrivilege('google_authenticate_setting', 'can_view')) { ?>
								
									<li class="nav-item" data-submenu="admin/gauthenticate/admin"><a class="nav-link" href="<?php echo base_url(); ?>admin/gauthenticate/admin"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('settings'); ?> </a></li>
								
								<?php } ?>
								
							</ul>
						</li>
				<?php } } } } ?>
			
                <?php 
                    if ($this->module_lib->hasActive('live_consultation')) {
                        if (($this->rbac->hasPrivilege('live_consultation', 'can_view')) || ($this->rbac->hasPrivilege('live_meeting', 'can_view'))) {?>
                            <li class="nav-item <?php echo set_Topmenu('conference'); ?>">
                               <a class="nav-link d-flex align-items-center" href="#" role="button">
                                    <i class="fa fa-video-camera ftlayer"></i> <span><?php echo $this->lang->line('live_consultation'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                                </a>
                                 <ul class="sh-subnav <?php echo set_Topmenu('conference') ? 'show' : ''; ?>" id="sub-8">
                                    <?php if ($this->rbac->hasPrivilege('live_consultation', 'can_view')) {?>
                                        <li class="nav-item" data-submenu="conference/live_consult"><a class="nav-link" href="<?php echo base_url('admin/zoom_conference/consult'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('live_consultation'); ?></a></li>
                                    <?php }if ($this->rbac->hasPrivilege('live_meeting', 'can_view')) {?>
                                        <li class="nav-item" data-submenu="conference/live_meeting"><a class="nav-link" href="<?php echo base_url('admin/zoom_conference/meeting'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('live_meeting'); ?> </a></li>
                                    <?php }?>
                                </ul>
                            </li>
                <?php
                        }
                    }
                ?>
                <li class="sh-section"><?= $this->lang->line('nav_reports_setup') ?></li>
                <?php
                if ($this->module_lib->hasActive('reports')) {
                    if (($this->rbac->hasPrivilege('opd_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('staff_attendance_report' , 'can_view')) ||
                        ($this->rbac->hasPrivilege('payroll_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('ipd_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('pharmacy_bill_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('pathology_patient_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('radiology_patient_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('ot_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_donor_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('payroll_month_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('staff_attendance_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('user_log', 'can_view')) ||
                        ($this->rbac->hasPrivilege('patient_login_credential', 'can_view')) ||
                        ($this->rbac->hasPrivilege('email_sms_log', 'can_view')) ||
                        ($this->rbac->hasPrivilege('tpa_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('ambulance_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('discharge_patient_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('appointment_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('blood_issue_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('income_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('expense_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('income_group_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('expense_group_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('inventory_stock_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('add_item_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('issue_inventory_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('birth_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('death_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('opd_balance_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('ipd_balance_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('live_consultation_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('live_meeting_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('all_transaction_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('patient_visit_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('patient_bill_report', 'can_view')) ||                         
                        ($this->rbac->hasPrivilege('component_issue_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('daily_transaction_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('balance_amount_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('processing_transaction_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('referral_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('staff_day_wise_attendance_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('audit_trail_report', 'can_view')) ||
                        ($this->rbac->hasPrivilege('stock_report', 'can_view'))  ) {
                        ?> 
                        <li class="nav-item <?php echo set_Topmenu('Reports'); ?>">
                            <a class="nav-link d-flex align-items-center" href="#" role="button">
                                <i class="fas fa-line-chart"></i> <span><?php echo $this->lang->line('reports'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                            </a>
                            <ul class="sh-subnav <?php echo set_Topmenu('Reports') ? 'show' : ''; ?>" id="sub-9">                               
                                
                            <?php if (($this->rbac->hasPrivilege('daily_transaction_report', 'can_view')) || ($this->rbac->hasPrivilege('all_transaction_report', 'can_view')) || ($this->rbac->hasPrivilege('income_report', 'can_view')) || ($this->rbac->hasPrivilege('income_group_report', 'can_view')) || ($this->rbac->hasPrivilege('expense_report', 'can_view')) || ($this->rbac->hasPrivilege('expense_group_report', 'can_view')) || ($this->rbac->hasPrivilege('patient_bill_report', 'can_view')) || ($this->rbac->hasPrivilege('referral_report', 'can_view')) || ($this->rbac->hasPrivilege('balance_amount_report', 'can_view')) || ($this->rbac->hasPrivilege('processing_transaction_report', 'can_view'))) {?>
							
                                <li class="nav-item" data-submenu="reports/finance|transaction/transactionreport|income/incomesearch|income/incomegroup|income/alltransaction|expense/expensesearch|expense/expensegroup|patient/patientbillreport|referral/report|transaction/processingtransaction|report/balanceamount|report/incomeexpense"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/finance"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("finance"); ?></a></li>
                                
                            <?php } if ($this->module_lib->hasActive('appointment')) {
                            if ($this->rbac->hasPrivilege('appointment_report', 'can_view')) {?>
                            
                                <li class="nav-item" data-submenu="reports/appointment|appointment/appointmentreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/appointment"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("appointment"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('opd')) {
                            if (($this->rbac->hasPrivilege('opd_report', 'can_view')) || ($this->rbac->hasPrivilege('opd_balance_report', 'can_view')) || ($this->rbac->hasPrivilege('discharge_patient_report', 'can_view'))) { ?>
                            
                                <li class="nav-item" data-submenu="reports/opd|patient/opd_report|patient/opdreportbalance|patient/opddischargepatient"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/opd"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("opd"); ?></a></li>
                                
                            <?php } }  if ($this->module_lib->hasActive('ipd')) {
                            if (($this->rbac->hasPrivilege('ipd_report', 'can_view')) || ($this->rbac->hasPrivilege('ipd_balance_report', 'can_view')) || ($this->rbac->hasPrivilege('discharge_patient_report', 'can_view'))) { ?>
                            
                                <li class="nav-item" data-submenu="reports/ipd|patient/ipdreport|patient/ipdreportbalance|patient/dischargepatientreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/ipd"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("ipd"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('pharmacy')) {
                            if (($this->rbac->hasPrivilege('pharmacy_bill_report', 'can_view')) || ($this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) || ($this->rbac->hasPrivilege('stock_report', 'can_view')) ) {?>
                            
                                <li class="nav-item" data-submenu="report/pharmacy|pharmacy/billreport|expmedicine/expmedicinereport|report/stock_report|expmedicine/medicinepurchasereport|expmedicine/medicinepurchasereturnreport|report/salereturns|expmedicine/medicinesalereport|expmedicine/medicineprofitlossreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/pharmacy"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("pharmacy"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('pathology')) {
                             if ($this->rbac->hasPrivilege('pathology_patient_report', 'can_view')) { ?>
                            
                                <li class="nav-item" data-submenu="reports/pathology|pathology/pathologyreport|pathology/pathologybalancereport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/pathology"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("pathology"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('radiology')) {
                            if ($this->rbac->hasPrivilege('radiology_patient_report', 'can_view')) {?>
                            
                                <li class="nav-item" data-submenu="reports/radiology|radio/radiologyreport|radio/radiologybalancereport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/radiology"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("radiology"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('blood_bank')) {
                                if (($this->rbac->hasPrivilege('blood_issue_report', 'can_view')) || ($this->rbac->hasPrivilege('component_issue_report', 'can_view')) || ($this->rbac->hasPrivilege('blood_donor_report', 'can_view'))){ ?>

                                <li class="nav-item" data-submenu="reports/bloodbank|bloodbank/bloodissuereport|bloodbank/componentissuereport|bloodbank/blooddonorreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/blood_bank"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("blood_bank"); ?></a></li>
                                
                            <?php } } if ($this->module_lib->hasActive('ambulance')) {
                            if ($this->rbac->hasPrivilege('ambulance_report', 'can_view')) { ?>
							
                                <li class="nav-item" data-submenu="reports/ambulance|vehicle/ambulancereport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/ambulance"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("ambulance"); ?></a></li>
                                
                            <?php } } if (($this->rbac->hasPrivilege('birth_report', 'can_view')) || ($this->rbac->hasPrivilege('death_report', 'can_view'))) {?>
                            
                                <li class="nav-item" data-submenu="reports/birth_death|birthordeath/birthreport|birthordeath/deathreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/birth_death"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("birth_death"); ?></a></li>
                                
                            <?php } if (($this->rbac->hasPrivilege('payroll_report', 'can_view')) || ($this->rbac->hasPrivilege('payroll_month_report', 'can_view')) || ($this->rbac->hasPrivilege('staff_attendance_report', 'can_view')) || ($this->rbac->hasPrivilege('staff_day_wise_attendance_report', 'can_view'))){ ?>
                            
                                <li class="nav-item" data-submenu="report/human_resource|payroll/payrollsearch|payroll/payrollreport|staffattendance/attendancereport|staffattendance/staffdaywiseattendancereport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/human_resource"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("human_resource"); ?></a></li>
                                 
                            <?php } if ($this->rbac->hasPrivilege('tpa_report', 'can_view')) { ?>
                            
                                <li class="nav-item" data-submenu="reports/tpa|tpamanagement/tpareport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/tpa"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("tpa"); ?></a></li>
                                 
                            <?php } if (($this->rbac->hasPrivilege('inventory_stock_report', 'can_view')) || ($this->rbac->hasPrivilege('add_item_report', 'can_view')) || ($this->rbac->hasPrivilege('issue_inventory_report', 'can_view'))) { ?>
                            
                                <li class="nav-item" data-submenu="reports/inventory|item/itemreport|item/additemreport|issueitem/issueinventoryreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/inventory"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("inventory"); ?></a></li>
                                 
                            <?php } if (($this->rbac->hasPrivilege('live_consultation_report', 'can_view')) || ($this->rbac->hasPrivilege('live_meeting_report', 'can_view')))  { ?>
                            
                                <li class="nav-item" data-submenu="reports/live_consultation|zoom_conference/consult_report|zoom_conference/meeting_report"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/live_consultation"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("live_consultation"); ?></a></li>
                                 
                            <?php } if (($this->rbac->hasPrivilege('user_log', 'can_view')) || ($this->rbac->hasPrivilege('email_sms_log', 'can_view')) || ($this->rbac->hasPrivilege('audit_trail_report', 'can_view'))) {?>
                            
                                <li class="nav-item" data-submenu="reports/log|admin/userlog|admin/audit"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/log"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("log"); ?></a></li>
								 
                            <?php } if ($this->rbac->hasPrivilege('ot_report', 'can_view')) {?>
                            
                                <li class="nav-item <?php echo set_SubSubmenu('reports/operationtheatre/otreport'); ?>" data-submenu="reports/ot|operationtheatre/otreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/ot"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("ot"); ?></a></li>
                                 
                            <?php } if (($this->rbac->hasPrivilege('patient_visit_report', 'can_view')) || ($this->rbac->hasPrivilege('patient_login_credential', 'can_view'))) {?>
                            
                                <li class="nav-item" data-submenu="reports/patient|patient/patientvisitreport|patient/patientcredentialreport"><a class="nav-link" href="<?php echo base_url(); ?>admin/report/patient"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line("patient"); ?></a></li>
                                 
                            <?php } ?> 
                            </ul>
                        </li>
                <?php
                        }
                    }
                ?>
				
                <?php
                if(   
					($this->rbac->hasPrivilege('general_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('charges', 'can_view')) || 
					($this->rbac->hasPrivilege('bed_status', 'can_view')) || 
					($this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) || 
					($this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) || 
					($this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) || 
					($this->rbac->hasPrivilege('setup_front_office', 'can_view')) || 
					($this->rbac->hasPrivilege('medicine_category', 'can_view')) || 
					($this->rbac->hasPrivilege('pathology_category', 'can_view')) || 
					($this->rbac->hasPrivilege('radiology_category', 'can_view')) || 
					($this->rbac->hasPrivilege('income_head', 'can_view')) || 
					($this->rbac->hasPrivilege('leave_types', 'can_view')) || 
					($this->rbac->hasPrivilege('item_category', 'can_view')) || 
					($this->rbac->hasPrivilege('hospital_charges', 'can_view')) || 
					($this->rbac->hasPrivilege('medicine_supplier', 'can_view')) || 
					($this->rbac->hasPrivilege('medicine_dosage', 'can_view')) || 
					($this->rbac->hasPrivilege('users', 'can_view')) || 
					($this->rbac->hasPrivilege('finding', 'can_view')) || 
					($this->rbac->hasPrivilege('finding_category', 'can_view')) || 
					($this->rbac->hasPrivilege('notification_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('system_notification_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('sms_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('email_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('payment_methods', 'can_view')) || 
					($this->rbac->hasPrivilege('front_cms_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('prefix_setting', 'can_view')) || 
					($this->rbac->hasPrivilege('backup', 'can_view')) || 
					($this->rbac->hasPrivilege('languages', 'can_view')) || 
					($this->rbac->hasPrivilege('captcha_setting', 'can_view'))   ) {
                ?> 
                            <li class="nav-item <?php echo set_Topmenu('setup'); ?>">
                                <a class="nav-link" href="#">
									<i class="fas fa-cogs"></i> <span><?php echo $this->lang->line('setup'); ?></span> <i class="fa fa-angle-down ms-auto sidebar-arrow"></i>
                                </a>
                                <ul class="sh-subnav <?php echo set_Topmenu('setup') ? 'show' : ''; ?>" id="sub-10">
                                 
                                <?php
							if ($this->rbac->hasPrivilege('general_setting', 'can_view') || $this->rbac->hasPrivilege('notification_setting', 'can_view') || $this->rbac->hasPrivilege('system_notification_setting', 'can_view') || $this->rbac->hasPrivilege('sms_setting', 'can_view') || $this->rbac->hasPrivilege('email_setting', 'can_view') || $this->rbac->hasPrivilege('payment_methods', 'can_view') || $this->rbac->hasPrivilege('front_cms_setting', 'can_view') || $this->rbac->hasPrivilege('prefix_setting', 'can_view') || $this->rbac->hasPrivilege('backup', 'can_view') || $this->rbac->hasPrivilege('languages', 'can_view') || $this->rbac->hasPrivilege('users', 'can_view') ||$this->rbac->hasPrivilege('captcha_setting', 'can_view')  ) {
                        ?>
                            <li class="nav-item <?php echo set_Submenu('schsettings/index', 'setup'); ?>" data-submenu="schsettings/index"><a class="nav-link" href="<?php echo base_url(); ?>schsettings"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('settings'); ?></a></li>
                                            <?php
                }
                

                    if ($this->rbac->hasPrivilege('hospital_charges', 'can_view') || $this->rbac->hasPrivilege('charge_category', 'can_view') || $this->rbac->hasPrivilege('charge_type', 'can_view') || $this->rbac->hasPrivilege('tax_category', 'can_view') || $this->rbac->hasPrivilege('unit_type', 'can_view') ) {
                        ?>
                            <li class="nav-item" data-submenu="admin/charges|admin/chargecategory|admin/chargetype|admin/taxcategory|admin/unittype"><a class="nav-link" href="<?php echo base_url(); ?>admin/charges"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('hospital_charges'); ?></a></li>
                                            <?php
					}
                    if ($this->module_lib->hasActive('ipd')) {
                        if ($this->rbac->hasPrivilege('bed_status', 'can_view') || $this->rbac->hasPrivilege('bed', 'can_view') || $this->rbac->hasPrivilege('bed_type', 'can_view') || $this->rbac->hasPrivilege('bed_group', 'can_view') || $this->rbac->hasPrivilege('floor', 'can_view')  ) {
                            ?>
                            <li class="nav-item" data-submenu="admin/setup/bed|admin/setup/bedtype|admin/setup/bedgroup|admin/setup/floor"><a class="nav-link" href="<?php echo base_url(); ?>admin/setup/bed/status"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('bed'); ?></a></li>
                                                <?php
						}
                    }

                    if (($this->rbac->hasPrivilege('print_appointment_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('opd_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('print_payslip_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('payment_receipt_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('birth_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('death_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('pathology_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('radiology_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ot_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('bloodbank_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ambulance_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('discharge_summary_print_header_footer', 'can_view'))  || ($this->rbac->hasPrivilege('opd_antenatal_finding_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_obstetric_history_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_antenatal_finding_print_header_footer', 'can_view'))) {
                        ?>
                            <li class="nav-item" data-submenu="admin/printing"><a class="nav-link" href="<?php echo base_url(); ?>admin/printing"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('print_header_footer'); ?></a></li>
                                            <?php
                }
                    if ($this->module_lib->hasActive('front_office')) {
                        if ($this->rbac->hasPrivilege('setup_front_office', 'can_view')) {
                            ?>
                            <li class="nav-item" data-submenu="admin/visitorspurpose|admin/source|admin/complainttype"><a class="nav-link" href="<?php echo base_url(); ?>admin/visitorspurpose"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('front_office'); ?></a></li>
                                                <?php
                }
                    }
                     if (($this->rbac->hasPrivilege('operation', 'can_view')) || ($this->rbac->hasPrivilege('operation_category', 'can_view'))) {
                            ?>
                            <li class="nav-item <?php echo set_Submenu('operation_theatre/index', 'setup'); ?>" data-submenu="admin/operationtheatre/index|admin/operationtheatre/category|admin/operationtheatre/operation"><a class="nav-link" href="<?php echo base_url(); ?>admin/operationtheatre/index"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('operations'); ?></a></li>
                                                <?php

                        }
                    if ($this->module_lib->hasActive('pharmacy')) {
                        if (($this->rbac->hasPrivilege('medicine_category', 'can_view') || ($this->rbac->hasPrivilege('medicine_supplier', 'can_view')) || ($this->rbac->hasPrivilege('medicine_dosage', 'can_view')) || ($this->rbac->hasPrivilege('dosage_interval', 'can_view')) || ($this->rbac->hasPrivilege('dosage_duration', 'can_view'))  || ($this->rbac->hasPrivilege('medicine_unit', 'can_view'))  )) {
                            ?>
                            <li class="nav-item" data-submenu="admin/medicinecategory|admin/medicinedosage|admin/medicineunit"><a class="nav-link" href="<?php echo base_url(); ?>admin/medicinecategory/medicine"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('pharmacy'); ?></a></li>
                                                <?php
                }
                    }

                    if ($this->module_lib->hasActive('pathology')) {
                        if ($this->rbac->hasPrivilege('pathology_category', 'can_view') || $this->rbac->hasPrivilege('pathology_unit', 'can_view') || $this->rbac->hasPrivilege('pathology_parameter', 'can_view')) {
                            ?>
                            <li class="nav-item" data-submenu="admin/pathologycategory"><a class="nav-link" href="<?php echo base_url(); ?>admin/pathologycategory/addcategory"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('pathology'); ?></a></li>
                                                <?php
                }
                    }
                    if ($this->module_lib->hasActive('radiology')) {
                        if ($this->rbac->hasPrivilege('radiology_category', 'can_view') || $this->rbac->hasPrivilege('radiology_unit', 'can_view') || $this->rbac->hasPrivilege('radiology_parameter', 'can_view')) {
                            ?>
                            <li class="nav-item" data-submenu="admin/lab"><a class="nav-link" href="<?php echo base_url(); ?>admin/lab/addlab"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('radiology'); ?></a></li>
                                                <?php
                }
                    } 

                     if ($this->module_lib->hasActive('blood_bank')) {
                        if ($this->rbac->hasPrivilege('blood_bank_product', 'can_view')) {
                            ?>
                            <li class="nav-item" data-submenu="admin/bloodbank/products|admin/bloodbank/components"><a class="nav-link" href="<?php echo base_url(); ?>admin/bloodbank/products"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('blood_bank'); ?></a></li>
                            <?php
                        }}
                    if (($this->rbac->hasPrivilege('symptoms_type', 'can_view')) || ($this->rbac->hasPrivilege('symptoms_head', 'can_view'))) {
                        ?>
                            <li class="nav-item" data-submenu="symptoms/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/symptoms"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('symptoms'); ?></a></li>
                        <?php
                }

                if (($this->rbac->hasPrivilege('icd10_codes', 'can_view')) || ($this->rbac->hasPrivilege('icd10_groups', 'can_view'))) {
                        ?>
                            <li class="nav-item" data-submenu="icd10/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/icd10"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('icd10'); ?></a></li>
                        <?php
                }

                if ($this->rbac->hasPrivilege('finding', 'can_view') || $this->rbac->hasPrivilege('finding_category', 'can_view') ) {
                        ?>
                            <li class="nav-item" data-submenu="finding/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/finding"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('findings'); ?></a></li>
                      <?php
                } 
                
                if ($this->rbac->hasPrivilege('vital', 'can_view'))  {
                    ?>                 
							<li class="nav-item" data-submenu="vital/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/vital"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('vitals') ?></a></li>
                <?php  }
                
                if ($this->rbac->hasPrivilege('setting', 'can_view')) {?>
                            <li class="nav-item" data-submenu="conference/zoom_api_setting"><a class="nav-link" href="<?php echo base_url('admin/zoom_conference'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('zoom_setting') ?></a></li>
                    <?php }

                    if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) {

                        if ($this->rbac->hasPrivilege('income_head', 'can_view')) {
                            ?>
                           
                                <li class="nav-item" data-submenu="admin/incomehead|admin/expensehead"><a class="nav-link" href="<?php echo base_url(); ?>admin/incomehead"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('finance'); ?></a></li>
                            <?php }else{ ?>
                                <li class="nav-item" data-submenu="admin/incomehead|admin/expensehead"><a class="nav-link" href="<?php echo base_url(); ?>admin/expensehead"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('finance'); ?></a></li>

                            <?php } ?>
                    <?php
                
                    }
                  
                    if ($this->rbac->hasPrivilege('leave_types', 'can_view') || $this->rbac->hasPrivilege('department', 'can_view') || $this->rbac->hasPrivilege('designation', 'can_view') || $this->rbac->hasPrivilege('specialist', 'can_view') ) {
                        ?>
                                            <li class="nav-item" data-submenu="admin/leavetypes|admin/department|admin/designation|admin/specialist"><a class="nav-link" href="<?php echo base_url(); ?>admin/leavetypes"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('human_resource'); ?></a></li>
                                            <?php
					} ?>
				
                        <?php if($this->module_lib->hasActive('referral')){
                            if ($this->rbac->hasPrivilege('referral_commission', 'can_view') || $this->rbac->hasPrivilege('referral_category', 'can_view')) {  ?>
                            <li class="nav-item" data-submenu="admin/referral/commission|admin/referral/category"><a class="nav-link" href="<?php echo base_url(); ?>admin/referral/commission"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('referral'); ?></a></li>
                        <?php } } 
						
						if ($this->module_lib->hasActive('appointment')) {if(($this->rbac->hasPrivilege('online_appointment_slot','can_view')) || ($this->rbac->hasPrivilege('online_appointment_doctor_shift','can_view')) || ($this->rbac->hasPrivilege('online_appointment_shift','can_view'))){  ?>

                            <li class="nav-item" data-submenu="admin/onlineappointment/index|admin/onlineappointment/globalshift|admin/onlineappointment/doctorglobalshift|admin/appointpriority"><a class="nav-link" href="<?php echo base_url(); ?>admin/onlineappointment/index"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('appointment'); ?></a></li>
                <?php  } }
				
                    if ($this->module_lib->hasActive('inventory')) {
                        if ($this->rbac->hasPrivilege('item_category', 'can_view') || $this->rbac->hasPrivilege('store', 'can_view') || $this->rbac->hasPrivilege('supplier', 'can_view') ) {
                            ?>
                                                        <li class="nav-item" data-submenu="admin/itemcategory|admin/itemstore|admin/itemsupplier"><a class="nav-link" href="<?php echo base_url(); ?>admin/itemcategory"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('inventory'); ?></a></li>
                                            <?php }
                        } 
                            if ($this->rbac->hasPrivilege('custom_fields', 'can_view')){
                        ?>                              

                                            <li class="nav-item" data-submenu="customfield/index"><a class="nav-link" href="<?php echo base_url(); ?>admin/customfield"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('custom_fields'); ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>

                            </li>
                    <?php
                   
                }
                ?>			
				
                

        </ul>
    </div><!-- /.sh-sidebar-scroll -->
</nav>
<script>
/* sh-sidebar: tree toggle (replaces AdminLTE tree widget) */
(function(){
  var nav = document.getElementById('sh-sidenav');
  if (!nav) return;
  // Manual tree toggle + accordion (BS5 collapse is NOT used here: .sh-subnav
  // toggles via CSS display, so BS5's Collapse only desynced state and left
  // sibling panels open). One handler closes all open panels, then opens the
  // clicked one if it was closed — giving toggle + single-open accordion.
  nav.querySelectorAll('.sh-subnav').forEach(function(ul) {
    var toggle = ul.previousElementSibling;
    if (!toggle) return;
    toggle.setAttribute('aria-expanded', ul.classList.contains('show') ? 'true' : 'false');
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      // Detect open by computed visibility, not just the .show class — another script
      // (footer.php detectActiveSubmenu) may have opened a panel via inline display.
      var wasOpen = (getComputedStyle(ul).display !== 'none');
      // Accordion: close every panel. Clear BOTH the .show class and any inline
      // display so a panel opened via inline style doesn't stay visually open.
      nav.querySelectorAll('.sh-subnav').forEach(function(open) {
        open.classList.remove('show');
        open.style.display = '';
        var ot = open.previousElementSibling;
        if (ot) ot.setAttribute('aria-expanded', 'false');
      });
      // Re-open the clicked panel only if it had been closed.
      if (!wasOpen) {
        ul.classList.add('show'); // CSS .sh-subnav.show -> display:block
        toggle.setAttribute('aria-expanded', 'true');
      }
    });
  });
  /* Mark active submenu item — data-submenu supports | separated patterns.
     Two ways a pattern can match:
       (1) URL path: boundary-aware indexOf — pattern must be followed
           by /, ?, #, or end-of-path (so "birthordeath/death" won't
           false-match "birthordeath/deathreport").
       (2) Session sub_menu: exact equality with the session value the
           controller sets via $this->session->set_userdata('sub_menu', …).
           This lets a controller explicitly highlight a parent menu
           item from a child route (e.g. appointmentreport() sets
           sub_menu = 'admin/multibranch/branch/report' so the Report
           sidebar item lights up on /…/appointmentreport URL).
     Then a "longest-match wins" pass: collect every match with its
     pattern length, then activate only the items whose best match
     equals the global maximum length. This makes the parent item
     active on the parent URL, and only the child active on a deeper
     URL. */
  var path = window.location.pathname;
  var sessionSubMenu = <?php echo json_encode((string)$this->session->userdata('sub_menu')); ?>;
  var matches = [];
  nav.querySelectorAll('[data-submenu]').forEach(function(li) {
    var patterns = li.getAttribute('data-submenu').split('|');
    var bestLen = 0;
    patterns.forEach(function(p) {
      p = p.trim();
      if (!p) return;
      var hit = false;
      /* (1) URL path match */
      var idx = path.indexOf(p);
      if (idx >= 0) {
        var next = path.charAt(idx + p.length);
        if (next === '' || next === '/' || next === '?' || next === '#') {
          hit = true;
        }
      }
      /* (2) Session sub_menu match (server-side hint) */
      if (!hit && sessionSubMenu && p === sessionSubMenu) {
        hit = true;
      }
      if (hit && p.length > bestLen) { bestLen = p.length; }
    });
    if (bestLen > 0) { matches.push({ li: li, len: bestLen }); }
  });
  var maxLen = matches.reduce(function(m, x) { return x.len > m ? x.len : m; }, 0);
  matches.forEach(function(m) {
    if (m.len === maxLen) {
      m.li.classList.add('active');
      var ul = m.li.closest('.sh-subnav');
      if (ul) { ul.classList.add('show'); }
    }
  });

})();

/* Sidebar scroll persistence — save .sh-sidebar-scroll's scrollTop on
   any internal-link click (or form submit) and restore on the next page
   load. Without this, the browser's default scroll-restoration is
   inconsistent for inner scrollable containers across hard navigations:
   it works for top-level items but breaks for deep menu items like
   Multi Branch whose subnav is JS-expanded after page load, throwing
   off the browser's structural heuristic. sessionStorage is per-tab so
   a new tab gets its own scroll state. */
(function(){
  var scroll = document.querySelector('.sh-sidebar-scroll');
  if (!scroll) return;
  var KEY = 'sh-sidebar-scrollTop';

  /* Restore — run on the next animation frame so the sidebar's
     dynamic .show class on .sh-subnav is applied first (it changes
     content height; restoring before that would scroll to the wrong
     visual spot). */
  try {
    var saved = sessionStorage.getItem(KEY);
    if (saved !== null) {
      requestAnimationFrame(function() {
        scroll.scrollTop = parseInt(saved, 10) || 0;
      });
    }
  } catch (e) {}

  /* Save on click of any same-origin nav link. Captured at document
     level so we catch the click before the browser starts navigation. */
  document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href]');
    if (!link) return;
    var href = link.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
    try { sessionStorage.setItem(KEY, String(scroll.scrollTop)); } catch (e) {}
  }, true);

  /* Save on form submit too (sidebar search form, patient search, etc.). */
  document.addEventListener('submit', function() {
    try { sessionStorage.setItem(KEY, String(scroll.scrollTop)); } catch (e) {}
  }, true);
})();

/* Sidebar menu filter */
(function(){
  var input = document.getElementById('sidebar-menu-filter');
  if (!input) return;
  var clearBtn = document.getElementById('sidebar-menu-filter-clear');
  function toggleClear() { if (clearBtn) clearBtn.classList.toggle('show', input.value.length > 0); }
  if (clearBtn) clearBtn.addEventListener('click', function() {
    input.value = '';
    input.dispatchEvent(new Event('input'));   // re-runs the filter → reveals all items
    input.focus();
  });
  input.addEventListener('input', function() {
    toggleClear();
    var q = this.value.trim().toLowerCase();
    var nav = document.getElementById('sh-sidenav');
    if (!nav) return;
    /* Show all items when query is empty */
    if (!q) {
      nav.querySelectorAll('.nav-item, .sh-section').forEach(function(el) { el.style.display = ''; });
      return;
    }
    /* Hide section headers initially; reveal if a sibling item matches */
    nav.querySelectorAll('.sh-section').forEach(function(s) { s.style.display = 'none'; });
    /* Process top-level nav-items */
    nav.querySelectorAll(':scope > .nav-item').forEach(function(li) {
      var labelEl = li.querySelector('.nav-link > span') || li.querySelector('.nav-link');
      var label = labelEl ? labelEl.textContent.toLowerCase() : '';   /* skip stray empty .nav-item with no link */
      /* Check sub-items too */
      var subMatch = false;
      li.querySelectorAll('.sh-subnav .nav-item').forEach(function(sub) {
        var st = sub.textContent.toLowerCase();
        if (st.indexOf(q) !== -1) { sub.style.display = ''; subMatch = true; }
        else { sub.style.display = 'none'; }
      });
      if (label.indexOf(q) !== -1 || subMatch) {
        li.style.display = '';
        /* Reveal preceding section header */
        var prev = li.previousElementSibling;
        while (prev) {
          if (prev.classList.contains('sh-section')) { prev.style.display = ''; break; }
          prev = prev.previousElementSibling;
        }
        /* If sub-items matched, expand the panel */
        if (subMatch) {
          var subnav = li.querySelector('.sh-subnav');
          if (subnav) subnav.classList.add('show');
        }
      } else {
        li.style.display = 'none';
      }
    });
  });
})();
</script>

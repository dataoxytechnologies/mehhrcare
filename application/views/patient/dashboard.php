<script src="<?php echo base_url('/'); ?>backend/js/Chart.bundle.js"></script>
<script src="<?php echo base_url('/'); ?>backend/js/utils.js"></script>

<?php
$_currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$_patient_image  = isset($this->patient_data['image']) ? $this->patient_data['image'] : '';
$_ph_has_image   = !empty($_patient_image) && strpos($_patient_image, 'no_image') === false;
$_patient_file   = $_ph_has_image ? $_patient_image : 'uploads/patient_images/no_image.png';
$_patient_id     = isset($this->patient_data['patient_id']) ? $this->patient_data['patient_id'] : null;
$_patient_name   = isset($patient_detail['patient_name']) ? $patient_detail['patient_name'] : '';
if (!$_ph_has_image) {
    $_name_parts  = preg_split('/\s+/', trim($_patient_name), -1, PREG_SPLIT_NO_EMPTY);
    $_ph_initials = count($_name_parts) === 0 ? '?' : (count($_name_parts) === 1
        ? mb_strtoupper(mb_substr($_name_parts[0], 0, 1))
        : mb_strtoupper(mb_substr($_name_parts[0], 0, 1) . mb_substr($_name_parts[count($_name_parts) - 1], 0, 1)));
}
$_patient_age   = $_patient_id ? $this->customlib->get_patient_current_age($_patient_id) : '';
$_blood_group   = isset($patient_detail['blood_group']) ? $patient_detail['blood_group'] : '';
$_gender        = isset($patient_detail['gender']) ? $patient_detail['gender'] : '';
$_unique_id     = isset($patient_detail['id']) ? $patient_detail['id'] : '';
$_last_visit    = isset($patient_detail['as_of_date']) && !empty($patient_detail['as_of_date'])
    ? $this->customlib->YYYYMMDDTodateFormat($patient_detail['as_of_date']) : '';
$_qrcode_path   = 'uploads/patient_id_card/qrcode/' . $_unique_id . '.png';
$_has_qrcode    = !empty($_unique_id) && file_exists(FCPATH . $_qrcode_path);
?>

<div class="container-fluid px-1 py-1">

    <!-- Welcome banner -->
    <div class="sh-welcome-banner">
        <div class="sh-profile-avatar-wrap">
            <?php if ($_ph_has_image): ?>
                <img src="<?php echo $this->media_storage->getImageURL($_patient_file); ?>"
                     alt="<?php echo html_escape($_patient_name); ?>" class="sh-profile-avatar">
            <?php else: ?>
                <div class="sh-profile-avatar-initials"><?php echo html_escape($_ph_initials); ?></div>
            <?php endif; ?>
        </div>
        <div class="sh-welcome-text">
            <h2><?php echo $this->lang->line('welcome_back'); ?>, <?php echo html_escape($_patient_name); ?></h2>
            <p class="sub"><?php echo $this->lang->line('medical_history'); ?> &amp; <?php echo $this->lang->line('upcoming_appointments'); ?></p>
            <div class="sh-welcome-meta">
                <?php if (!empty($_unique_id)): ?>
                <div class="item"><span class="lbl"><?php echo $this->lang->line('id'); ?></span><span class="val"><?php echo html_escape($_unique_id); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($_patient_age)): ?>
                <div class="item"><span class="lbl"><?php echo $this->lang->line('age'); ?></span><span class="val"><?php echo html_escape($_patient_age); ?><?php if (!empty($_gender)): ?>, <?php echo html_escape($this->lang->line(strtolower($_gender))); endif; ?></span></div>
                <?php endif; ?>
                <?php if (!empty($_blood_group)): ?>
                <div class="item"><span class="lbl"><?php echo $this->lang->line('blood_group'); ?></span><span class="val"><?php echo html_escape($_blood_group); ?></span></div>
                <?php endif; ?>
                <?php if (!empty($_last_visit)): ?>
                <div class="item"><span class="lbl"><?php echo $this->lang->line('last_visit'); ?></span><span class="val"><?php echo html_escape($_last_visit); ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($_has_qrcode): ?>
        <a href="<?php echo base_url($_qrcode_path); ?>" target="_blank" rel="noopener"
           title="<?php echo $this->lang->line('qrcode'); ?>" class="sh-welcome-qr-link">
            <img src="<?php echo base_url($_qrcode_path); ?>" alt="<?php echo $this->lang->line('qrcode'); ?>" class="sh-qr-code sh-welcome-qr">
        </a>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('my_appointments')): ?>
        <a href="<?php echo site_url('patient/dashboard/appointment'); ?>" class="btn-pulse">
            <i class="fa fa-calendar-plus"></i> <?php echo $this->lang->line('book_appointment'); ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!IsNullOrEmptyString($insurance_validity) && strtotime(date('Y-m-d')) > strtotime($insurance_validity)): ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="sh-ins-alert">
                <i class="fa fa-exclamation-triangle"></i>
                <div>
                    <strong><?php echo $this->customlib->YYYYMMDDTodateFormat($insurance_validity); ?></strong>
                    <?php echo $this->lang->line('was_your_tpa_validity_date_which_is_expired_now_please_renew_your_tpa_and_update_its_status_with_us'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- KPI cards -->
    <div class="row g-3 mb-3 sh-pulse-kpi-strip">

        <?php if ($this->module_lib->hasPatientActive('opd')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/profile'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-accent">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('opd'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_visits['total_visit']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('ipd')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/ipdprofile'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-blue">
                    <i class="fas fa-procedures"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('ipd'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_ipd['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('pharmacy')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/bill'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-green">
                    <i class="fas fa-mortar-pestle"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('pharmacy'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_pharmacy['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('pathology')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/search'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-amber">
                    <i class="fas fa-flask"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('pathology'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_pathology['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('radiology')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/radioreport'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-purple">
                    <i class="fas fa-microscope"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('radiology'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_radiology['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('blood_bank')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/bloodbank'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-red">
                    <i class="fas fa-tint"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('blood_bank'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_blood_issue['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('ambulance')): ?>
        <div class="col-xl col-md-3 col-sm-6">
            <a href="<?php echo site_url('patient/dashboard/ambulance'); ?>" class="sh-kpi-card">
                <div class="sh-kpi-icon sh-kpi-teal">
                    <i class="fas fa-ambulance"></i>
                </div>
                <div>
                    <div class="sh-kpi-label"><?php echo $this->lang->line('ambulance'); ?></div>
                    <div class="sh-kpi-value"><?php echo $total_ambulance['total']; ?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>

    </div><!-- /.row KPI -->

    <!-- Medical history chart + Upcoming appointments -->
    <div class="row g-3 mb-3">
        <div class="col-lg-<?php echo $this->module_lib->hasPatientActive('my_appointments') ? '8' : '12'; ?>">
            <div class="sh-chart-card">
                <div class="sh-chart-header">
                    <i class="fas fa-chart-line"></i>
                    <?php echo $this->lang->line('medical_history'); ?>
                </div>
                <div class="p-3">
                    <div class="sh-chart-canvas-wrap">
                        <canvas id="medical-history-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->module_lib->hasPatientActive('my_appointments')): ?>
        <div class="col-lg-4">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <h6 class="sh-card-header-title"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('upcoming_appointments'); ?></h6>
                    <a href="<?php echo site_url('patient/dashboard/appointment'); ?>" class="sh-section-link"><?php echo $this->lang->line('view_all'); ?></a>
                </div>
                <?php if (!empty($upcoming_appointments)): ?>
                <ul class="sh-mini-list">
                    <?php foreach ($upcoming_appointments as $appt): ?>
                        <?php
                        // Relative-time hint (Today / Tomorrow / in N days)
                        $_appt_rel = '';
                        if (!empty($appt['date'])) {
                            $_today    = new DateTime('today');
                            $_appt_day = new DateTime(date('Y-m-d', strtotime($appt['date'])));
                            $_diff_days = (int) $_today->diff($_appt_day)->format('%r%a');
                            if ($_diff_days === 0)      $_appt_rel = $this->lang->line('today');
                            else if ($_diff_days === 1) $_appt_rel = $this->lang->line('tomorrow');
                            else if ($_diff_days > 1 && $_diff_days <= 30) $_appt_rel = 'in ' . $_diff_days . ' days';
                        }
                        // Calendar tile parts
                        $_cal_ts    = !empty($appt['date']) ? strtotime($appt['date']) : 0;
                        $_cal_month = $_cal_ts ? strtoupper(date('M', $_cal_ts)) : '';
                        $_cal_day   = $_cal_ts ? date('d', $_cal_ts) : '';
                        $_cal_time  = $_cal_ts ? date('h:i A', $_cal_ts) : '';
                        // Status class
                        $_st_cls = $appt['appointment_status'] === 'approved' ? 'is-confirmed' : 'is-pending';
                        $_st_lbl = $appt['appointment_status'] === 'approved' ? 'confirmed' : 'pending';
                        // Chips
                        $_has_shift = !empty($appt['shift_day']) && !empty($appt['shift_start']) && !empty($appt['shift_end']);
                        $_has_chips = !empty($appt['priority_name']) || $_has_shift || !empty($appt['source']);
                        ?>
                    <li class="sh-appt-row">
                        <div class="sh-appt-cal <?php echo $_st_cls; ?>">
                            <div class="sh-appt-cal-month"><?php echo $_cal_month; ?></div>
                            <div class="sh-appt-cal-day"><?php echo $_cal_day; ?></div>
                            <div class="sh-appt-cal-time"><?php echo $_cal_time; ?></div>
                        </div>
                        <div class="sh-appt-body">
                            <div class="sh-appt-body-row">
                                <div class="sh-appt-doctor"><?php echo html_escape(trim($appt['doctor_name'] . ' ' . $appt['doctor_surname'])); ?></div>
                                <span class="sh-appt-status <?php echo $_st_cls; ?>">
                                    <span class="dot"></span><?php echo $this->lang->line($_st_lbl); ?>
                                </span>
                            </div>
                            <div class="sh-appt-meta">
                                <?php
                                $_dept = !empty($appt['department_name']) ? html_escape($appt['department_name']) : '';
                                if (!empty($_dept)) echo $_dept;
                                if (!empty($_dept) && !empty($_appt_rel)) echo ' &middot; ';
                                if (!empty($_appt_rel)) echo '<span class="rel">' . html_escape($_appt_rel) . '</span>';
                                ?>
                            </div>
                            <?php if ($_has_chips): ?>
                            <div class="sh-appt-chips">
                                <?php if (!empty($appt['priority_name'])): ?>
                                <span class="sh-mini-list-tag"><?php echo html_escape($appt['priority_name']); ?></span>
                                <?php endif; ?>
                                <?php if ($_has_shift): ?>
                                    <?php $_shift_text = substr($appt['shift_day'], 0, 3) . ' ' . date('h:i A', strtotime($appt['shift_start'])) . ' - ' . date('h:i A', strtotime($appt['shift_end'])); ?>
                                <span class="sh-mini-list-tag"><?php echo html_escape($_shift_text); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($appt['source'])): ?>
                                <span class="sh-mini-list-tag"><?php echo html_escape($appt['source']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="sh-appt-action">
                            <a href="<?php echo site_url('patient/dashboard/appointment'); ?>" target="_blank" rel="noopener"
                               class="sh-mini-list-iconbtn"
                               title="<?php echo $this->lang->line('view_all'); ?>">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="sh-mini-empty">
                    <div class="sh-mini-empty-icon"><i class="fas fa-calendar-plus"></i></div>
                    <div class="sh-mini-empty-title"><?php echo $this->lang->line('no_upcoming_appointments'); ?></div>
                    <div class="sh-mini-empty-sub"><?php echo $this->lang->line('book_one_to_see_it_here'); ?></div>
                    <a href="<?php echo site_url('patient/dashboard/appointment'); ?>" class="sh-mini-empty-cta">
                        <?php echo $this->lang->line('book_appointment'); ?> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent prescriptions / Pending bills / Lab results -->
    <div class="row g-3 mb-3">

        <?php
        $_module_meta = array(
            'opd_patient'    => array('label' => 'opd',        'icon' => 'fa-stethoscope',  'cls' => 'sh-kpi-accent'),
            'ipd_patient'    => array('label' => 'ipd',        'icon' => 'fa-procedures',   'cls' => 'sh-kpi-blue'),
            'pharmacy_bill'  => array('label' => 'pharmacy',   'icon' => 'fa-mortar-pestle','cls' => 'sh-kpi-green'),
            'pathology_test' => array('label' => 'pathology',  'icon' => 'fa-flask',        'cls' => 'sh-kpi-amber'),
            'radiology_test' => array('label' => 'radiology',  'icon' => 'fa-microscope',   'cls' => 'sh-kpi-purple'),
            'blood_bank'     => array('label' => 'blood_bank', 'icon' => 'fa-tint',         'cls' => 'sh-kpi-red'),
            'ambulance_call' => array('label' => 'ambulance',  'icon' => 'fa-ambulance',    'cls' => 'sh-kpi-teal'),
        );
        $_total_balance = 0;
        if (!empty($pending_bills)) {
            foreach ($pending_bills as $_b) { $_total_balance += (float) $_b['balance_amount']; }
        }
        ?>
        <div class="col-lg-4">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <h6 class="sh-card-header-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo $this->lang->line('pending_bills'); ?></h6>
                    <?php if ($_total_balance > 0): ?>
                    <span class="sh-bill-total-pill"><?php echo $_currency_symbol . number_format($_total_balance, 2); ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($pending_bills)): ?>
                <ul class="sh-mini-list">
                    <?php foreach ($pending_bills as $bill): ?>
                        <?php $m = isset($_module_meta[$bill['type']]) ? $_module_meta[$bill['type']] : null; if (!$m) continue; ?>
                    <li class="sh-mini-list-item">
                        <div class="sh-mini-list-icon <?php echo $m['cls']; ?>"><i class="fas <?php echo $m['icon']; ?>"></i></div>
                        <div class="sh-mini-list-body">
                            <div class="sh-mini-list-title"><?php echo $this->lang->line($m['label']); ?></div>
                            <div class="sh-mini-list-meta"><?php echo (int) $bill['bill_count']; ?> <?php echo $this->lang->line('pending'); ?></div>
                        </div>
                        <span class="sh-mini-list-amount"><?php echo $_currency_symbol . number_format((float) $bill['balance_amount'], 2); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="sh-mini-empty">
                    <div class="sh-mini-empty-icon success"><i class="fas fa-circle-check"></i></div>
                    <div class="sh-mini-empty-title"><?php echo $this->lang->line('all_caught_up'); ?></div>
                    <div class="sh-mini-empty-sub"><?php echo $this->lang->line('no_pending_bills'); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($this->module_lib->hasPatientActive('opd')): ?>
        <div class="col-lg-4">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <h6 class="sh-card-header-title"><i class="fas fa-prescription-bottle-medical"></i> <?php echo $this->lang->line('last_5_opd_prescriptions'); ?></h6>
                </div>
                <?php if (!empty($recent_prescriptions)): ?>
                <?php
                $_opd_prefix     = $this->customlib->getPatientSessionPrefixByType('opd_no');
                $_rx_prefix      = $this->customlib->getPatientSessionPrefixByType('opd_prescription');
                ?>
                <ul class="sh-mini-list">
                    <?php foreach ($recent_prescriptions as $rx): ?>
                    <li class="sh-mini-list-item">
                        <div class="sh-mini-list-icon sh-kpi-green"><i class="fas fa-pills"></i></div>
                        <div class="sh-mini-list-body">
                            <div class="sh-mini-list-title is-mono">
                                <span><?php echo html_escape($_rx_prefix . $rx['id']); ?></span>
                            </div>
                            <div class="sh-mini-list-meta">
                                <?php
                                $_rx_meta  = array();
                                if (!empty($rx['opd_id'])) {
                                    $_rx_meta[] = html_escape($_opd_prefix . $rx['opd_id']);
                                }
                                $_rx_doctor = trim((!empty($rx['doctor_name']) ? $rx['doctor_name'] : '') . ' ' . (!empty($rx['doctor_surname']) ? $rx['doctor_surname'] : ''));
                                if (!empty($_rx_doctor)) {
                                    $_rx_meta[] = html_escape($_rx_doctor);
                                }
                                $_rx_date = !empty($rx['appointment_date']) ? $rx['appointment_date'] : (!empty($rx['prescription_date']) ? $rx['prescription_date'] : '');
                                if (!empty($_rx_date)) {
                                    $_rx_meta[] = $this->customlib->YYYYMMDDTodateFormat($_rx_date);
                                }
                                if (!empty($rx['symptoms'])) {
                                    $_rx_meta[] = html_escape($rx['symptoms']);
                                }
                                echo implode(' &middot; ', $_rx_meta);
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($rx['visit_id'])): ?>
                        <a href="javascript:void(0);"
                           onclick="printPrescription(<?php echo (int) $rx['visit_id']; ?>, <?php echo (int) $rx['opd_id']; ?>); return false;"
                           class="sh-mini-list-iconbtn"
                           title="<?php echo $this->lang->line('print'); ?>">
                            <i class="fa fa-print"></i>
                        </a>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="sh-mini-empty">
                    <div class="sh-mini-empty-icon"><i class="fas fa-prescription-bottle-medical"></i></div>
                    <div class="sh-mini-empty-title"><?php echo $this->lang->line('no_prescriptions_yet'); ?></div>
                    <div class="sh-mini-empty-sub"><?php echo $this->lang->line('prescription_history_will_appear'); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($this->module_lib->hasPatientActive('pathology') || $this->module_lib->hasPatientActive('radiology')): ?>
        <div class="col-lg-4">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <h6 class="sh-card-header-title"><i class="fas fa-vial"></i> <?php echo $this->lang->line('last_5_opd_lab_tests'); ?></h6>
                </div>
                <?php if (!empty($pending_labs)): ?>
                <?php
                $_lab_opd_prefix  = $this->customlib->getPatientSessionPrefixByType('opd_no');
                $_path_bill_prefix = $this->customlib->getPatientSessionPrefixByType('pathology_billing');
                $_radio_bill_prefix = $this->customlib->getPatientSessionPrefixByType('radiology_billing');
                ?>
                <ul class="sh-mini-list">
                    <?php foreach ($pending_labs as $lab): ?>
                            <?php $_is_radio    = ($lab['lab_type'] === 'radiology'); ?>
                            <?php $_lab_url     = site_url($_is_radio ? 'patient/dashboard/radioreport' : 'patient/dashboard/search'); ?>
                            <?php $_bill_prefix = $_is_radio ? $_radio_bill_prefix : $_path_bill_prefix; ?>
                        <li class="sh-mini-list-item">
                            <div class="sh-mini-list-icon <?php echo $_is_radio ? 'sh-kpi-purple' : 'sh-kpi-amber'; ?>">
                                <i class="fas <?php echo $_is_radio ? 'fa-x-ray' : 'fa-flask'; ?>"></i>
                            </div>
                            <div class="sh-mini-list-body">
                                <div class="sh-mini-list-title is-mono">
                                    <span><?php echo html_escape($_bill_prefix . $lab['id']); ?></span>
                                    <?php if (empty($lab['transaction_id'])): ?>
                                    <span class="sh-status-pill sh-status-pill-warn"><?php echo $this->lang->line('pending'); ?></span>
                                    <?php else: ?>
                                    <span class="sh-status-pill sh-status-pill-ok"><?php echo $this->lang->line('confirmed'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="sh-mini-list-meta">
                                    <?php
                                    $_meta = array();
                                    if (!empty($lab['opd_id']))      $_meta[] = html_escape($_lab_opd_prefix . $lab['opd_id']);
                                    if (!empty($lab['doctor_name'])) $_meta[] = html_escape($lab['doctor_name']);
                                    if (!empty($lab['date']))        $_meta[] = $this->customlib->YYYYMMDDTodateFormat($lab['date']);
                                    if (isset($lab['net_amount']))   $_meta[] = $_currency_symbol . number_format((float) $lab['net_amount'], 2);
                                    echo implode(' &middot; ', $_meta);
                                    ?>
                                </div>
                            </div>
                            <a href="<?php echo $_lab_url; ?>" target="_blank" rel="noopener"
                               class="sh-mini-list-iconbtn"
                               title="<?php echo $this->lang->line('view_reports'); ?>">
                                <i class="fa fa-eye"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="sh-mini-empty">
                    <div class="sh-mini-empty-icon"><i class="fas fa-vial"></i></div>
                    <div class="sh-mini-empty-title"><?php echo $this->lang->line('no_tests_pending'); ?></div>
                    <div class="sh-mini-empty-sub"><?php echo $this->lang->line('lab_requests_will_appear'); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Findings + Symptoms charts -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="sh-chart-card h-100">
                <div class="sh-chart-header">
                    <i class="fas fa-chart-bar"></i>
                    <span><?php echo $this->lang->line('top_ten_findings'); ?></span>
                    <span class="sh-chart-header-meta" id="finding-total-meta"></span>
                </div>
                <div class="p-3">
                    <div class="sh-chart-canvas-wrap">
                        <canvas id="finding-bar-chart"></canvas>
                        <div class="sh-chart-empty d-none" id="finding-bar-chart-empty">
                            <i class="fas fa-chart-bar"></i>
                            <p><?php echo $this->lang->line('no_record_found'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="sh-chart-card h-100">
                <div class="sh-chart-header">
                    <i class="fas fa-chart-pie"></i>
                    <span><?php echo $this->lang->line('top_ten_symptoms'); ?></span>
                    <span class="sh-chart-header-meta" id="symptom-total-meta"></span>
                </div>
                <div class="p-3">
                    <div class="sh-chart-canvas-wrap">
                        <canvas id="symptom-bar-chart"></canvas>
                        <div class="sh-chart-empty d-none" id="symptom-bar-chart-empty">
                            <i class="fas fa-chart-pie"></i>
                            <p><?php echo $this->lang->line('no_record_found'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<script type="text/javascript">
    // Mirrors the printPrescription helper from patient/profile.php
    // POSTs to patient/prescription/getPrescription/{visit_id}/{opd_id} with
    // print=print, then opens the returned HTML in a print popup window
    // (popup() helper is defined globally in layout/patient/footer.php).
    function printPrescription(id, opdid) {
        $.ajax({
            url: baseurl + 'patient/prescription/getPrescription/' + id + (opdid ? '/' + opdid : ''),
            type: 'POST',
            data: { payslipid: id, print: 'print' },
            success: function (result) { popup(result); }
        });
    }

    $(document).ready(function(){
        function shThemeColors() {
            var s = getComputedStyle(document.body);
            function v(name, fallback) {
                var x = s.getPropertyValue(name);
                return (x && x.trim()) ? x.trim() : fallback;
            }
            return {
                border:   v('--border',  '#E4E8ED'),
                muted:    v('--muted',   '#5F6B7A'),
                ink:      v('--ink',     '#1A2733'),
                surface:  v('--surface', '#FFFFFF'),
                link:     v('--link',    '#1B73E8')
            };
        }

        // Eye-comfort professional palette — desaturated mid-tones that
        // read pleasantly on both light and dark theme backgrounds.
        // Used by the symptoms pie (first 5 + Other reuses c.muted).
        function shPalette10(c) {
            return [
                '#5B7AA8', // slate-blue
                '#6FA486', // sage green
                '#C9A96E', // dusty amber
                '#B07A8E', // dusty rose
                '#7BA0A8', // muted teal
                '#9489B5', // soft lavender
                '#A88B6F', // taupe
                '#7B8A99', // steel grey
                '#A8946F', // ochre
                '#6E8090'  // pewter
            ];
        }
        // Single-color findings bar — same hue as palette[0] so both charts
        // feel coordinated.
        var SH_BAR_COLOR       = '#5B7AA8';
        var SH_BAR_HOVER_COLOR = '#3F5A82';

        function shSumDataset(data) {
            var total = 0;
            if (data && data.dataset) {
                data.dataset.forEach(function(ds){
                    if (ds.data && ds.data.length) {
                        ds.data.forEach(function(v){ total += parseFloat(v) || 0; });
                    }
                });
            }
            return total;
        }
        function shIsEmptyDataset(data) {
            return !data || !data.labels || !data.labels.length || shSumDataset(data) === 0;
        }
        function shToggleEmpty(canvasId, isEmpty) {
            var canvas = document.getElementById(canvasId);
            var empty  = document.getElementById(canvasId + '-empty');
            if (!canvas || !empty) return;
            if (isEmpty) {
                canvas.classList.add('d-none');
                empty.classList.remove('d-none');
            } else {
                canvas.classList.remove('d-none');
                empty.classList.add('d-none');
            }
        }
        function shSetMeta(metaId, total, suffix) {
            var el = document.getElementById(metaId);
            if (!el) return;
            el.innerHTML = total > 0
                ? '<strong>' + total + '</strong> ' + suffix
                : '';
        }
        function shTopNplusOther(data, n, otherColor, otherLabel) {
            if (!data || !data.dataset || !data.dataset.length || !data.labels) return data;
            var ds = data.dataset[0];
            if (!ds.data || ds.data.length <= n) return data;
            var topData   = ds.data.slice(0, n);
            var otherSum  = ds.data.slice(n).reduce(function(a,b){ return a + (parseFloat(b)||0); }, 0);
            var topLabels = data.labels.slice(0, n);
            var newColors = (Array.isArray(ds.backgroundColor) ? ds.backgroundColor.slice(0, n) : [])
                .concat([otherColor]);
            return {
                labels: topLabels.concat([otherLabel]),
                dataset: [{
                    data: topData.concat([otherSum]),
                    backgroundColor: newColors,
                    borderWidth: ds.borderWidth || 0
                }]
            };
        }

        $.ajax({
            url: baseurl + "patient/dashboard/yearchart",
            type: 'POST', data: {}, dataType: 'json',
            success: function(data) {
                var c = shThemeColors();
                if (data.dataset && data.dataset.length) {
                    data.dataset.forEach(function(ds){
                        ds.borderColor = ds.borderColor || c.link;
                        ds.pointBackgroundColor = ds.pointBackgroundColor || c.link;
                        if (ds.pointRadius == null) ds.pointRadius = 4;
                        if (ds.lineTension == null) ds.lineTension = 0.35;
                        if (ds.fill == null) ds.fill = true;
                    });
                }
                var ctx = document.getElementById("medical-history-chart").getContext("2d");
                new Chart(ctx, {
                    type: 'line',
                    data: { labels: data.labels, datasets: data.dataset },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            xAxes: [{ gridLines: { color: c.border }, ticks: { fontColor: c.muted, fontSize: 11 } }],
                            yAxes: [{ gridLines: { color: c.border }, ticks: { fontColor: c.muted, fontSize: 11, stepSize: 1 } }]
                        }
                    }
                });
            },
            error: function() { console.error('yearchart failed'); }
        });

        $.ajax({
            url: baseurl + "patient/dashboard/findingchart",
            type: 'POST', data: {}, dataType: 'json',
            success: function(data) {
                var c = shThemeColors();
                if (shIsEmptyDataset(data)) {
                    shToggleEmpty('finding-bar-chart', true);
                    shSetMeta('finding-total-meta', 0, 'total');
                    return;
                }
                shToggleEmpty('finding-bar-chart', false);
                shSetMeta('finding-total-meta', shSumDataset(data), 'total');

                if (data.dataset && data.dataset.length) {
                    data.dataset.forEach(function(ds){
                        ds.backgroundColor      = SH_BAR_COLOR;
                        ds.hoverBackgroundColor = SH_BAR_HOVER_COLOR;
                        ds.borderWidth          = 0;
                        ds.borderRadius         = 6;
                        ds.borderSkipped        = 'bottom';
                        ds.maxBarThickness      = 38;
                    });
                }
                var ctx_1 = document.getElementById("finding-bar-chart").getContext("2d");
                new Chart(ctx_1, {
                    type: 'bar',
                    data: { labels: data.labels, datasets: data.dataset },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        legend: { display: false },
                        layout: { padding: { top: 8, right: 8, bottom: 4, left: 4 } },
                        animation: { duration: 1100, easing: 'easeOutQuart' },
                        hover: { animationDuration: 250, mode: 'index', intersect: false },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false, drawBorder: false },
                                ticks: { fontColor: c.muted, fontSize: 10, maxRotation: 30, padding: 6 }
                            }],
                            yAxes: [{
                                gridLines: { color: c.border, drawBorder: false, zeroLineColor: c.border },
                                ticks: { fontColor: c.muted, fontSize: 11, stepSize: 2, padding: 8, beginAtZero: true }
                            }]
                        },
                        tooltips: {
                            backgroundColor: c.ink, titleFontColor: c.surface, bodyFontColor: c.surface,
                            titleFontSize: 11, bodyFontSize: 12, cornerRadius: 6,
                            xPadding: 10, yPadding: 8, displayColors: false,
                            caretSize: 5
                        }
                    }
                });
            },
            error: function() { console.error('findingchart failed'); }
        });

        $.ajax({
            url: baseurl + "patient/dashboard/symptomchart",
            type: 'POST', data: {}, dataType: 'json',
            success: function(data) {
                var c = shThemeColors();
                var p = shPalette10(c);
                var surface = (getComputedStyle(document.body).getPropertyValue('--surface') || '#fff').trim();
                if (shIsEmptyDataset(data)) {
                    shToggleEmpty('symptom-bar-chart', true);
                    shSetMeta('symptom-total-meta', 0, 'reported');
                    return;
                }

                // Total BEFORE consolidation so it reflects all reported symptoms
                shSetMeta('symptom-total-meta', shSumDataset(data), 'reported');
                shToggleEmpty('symptom-bar-chart', false);

                // Consolidate to top-5 + Other so the pie stays readable
                data = shTopNplusOther(data, 5, c.muted, 'Other');

                if (data.dataset && data.dataset.length) {
                    data.dataset.forEach(function(ds){
                        ds.backgroundColor = ds.backgroundColor && ds.backgroundColor.length ? ds.backgroundColor : p;
                        ds.borderWidth = 2;
                        ds.borderColor = surface;
                        ds.hoverBorderWidth = 0;
                        ds.hoverOffset = 8;
                    });
                }
                var ctx_2 = document.getElementById("symptom-bar-chart").getContext("2d");
                new Chart(ctx_2, {
                    type: 'pie',
                    data: { labels: data.labels, datasets: data.dataset },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        layout: { padding: 6 },
                        animation: { duration: 1200, easing: 'easeOutQuart', animateRotate: true, animateScale: true },
                        hover: { animationDuration: 250 },
                        legend: {
                            display: true, position: 'right',
                            labels: { fontColor: c.muted, fontSize: 11, boxWidth: 10, padding: 12, usePointStyle: true }
                        },
                        tooltips: {
                            backgroundColor: c.ink, titleFontColor: c.surface, bodyFontColor: c.surface,
                            titleFontSize: 11, bodyFontSize: 12, cornerRadius: 6,
                            xPadding: 10, yPadding: 8, caretSize: 5
                        }
                    }
                });
            },
            error: function() { console.error('symptomchart failed'); }
        });
    });
</script>

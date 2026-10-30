<?php
    $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
    $userdata            = $this->customlib->getUserData();
    $logged_in_User      = $this->customlib->getLoggedInUserData();
    $logged_in_User_Role = json_decode($this->customlib->getStaffRole());
    $permission_access = 0;
    if (($staff["user_type"] == "Super Admin") && $userdata["id"] == $staff["id"]) {
        $permission_access = 1;
    } elseif (($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view') && $staff["user_type"] != "Super Admin") || $userdata["id"] == $staff["id"]) {
        $permission_access = 1;
    }
?>

<?php if ($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) { ?>
<div class="offcanvas offcanvas-end sh-switcher" tabindex="-1" id="staffSwitcherOffcanvas" aria-labelledby="staffSwitcherOffcanvasLabel">
    <div class="offcanvas-header sh-switcher-header">
        <div class="sh-switcher-head-content">
            <h5 class="offcanvas-title sh-switcher-title" id="staffSwitcherOffcanvasLabel"><?php echo $this->lang->line('staff'); ?></h5>
            <span class="sh-switcher-subtitle"><?php echo count($stafflist); ?> <?php echo $this->lang->line('staff'); ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sh-switcher-tabs-wrap">
            <button type="button" class="sh-switcher-tab-arrow sh-switcher-tab-arrow-left" data-dir="-1" aria-label="Scroll left"><i class="fa fa-chevron-left"></i></button>
            <ul class="nav sh-switcher-tabs" role="tablist">
                <?php foreach ($roles as $role_key => $role_value) { ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link<?php if ($staff["role_id"] == $role_value["id"]) { echo " active"; } ?>" href="#role<?php echo $role_value["id"]; ?>" data-bs-toggle="tab" role="tab"><?php echo html_escape($role_value["name"]); ?></a>
                    </li>
                <?php } ?>
            </ul>
            <button type="button" class="sh-switcher-tab-arrow sh-switcher-tab-arrow-right" data-dir="1" aria-label="Scroll right"><i class="fa fa-chevron-right"></i></button>
        </div>
        <div class="tab-content sh-switcher-content">
            <?php foreach ($roles as $rolet_key => $rolet_value) {
                $role_staff = array_filter($stafflist, function ($s) use ($rolet_value) { return $s['role_id'] == $rolet_value['id']; });
            ?>
                <div class="tab-pane fade<?php if ($staff["role_id"] == $rolet_value["id"]) { echo " show active"; } ?>" id="role<?php echo $rolet_value['id']; ?>" role="tabpanel">
                    <?php if (empty($role_staff)) { ?>
                        <div class="sh-switcher-empty">
                            <i class="fa fa-user-circle-o"></i>
                            <span><?php echo $this->lang->line('no_record_found'); ?></span>
                        </div>
                    <?php } else { ?>
                        <div class="sh-switcher-list">
                            <?php foreach ($role_staff as $svalue) {
                                $sw_has_image = !empty($svalue["image"]) && strpos($svalue["image"], 'no_image') === false;
                                $image        = $sw_has_image ? $svalue['image'] : "no_image.png";
                                $is_current   = ($svalue["id"] == $staff["id"]);
                                if (!$sw_has_image) {
                                    $sw_full   = trim(($svalue['name'] ?? '') . ' ' . ($svalue['surname'] ?? ''));
                                    $sw_parts  = preg_split('/\s+/', $sw_full, -1, PREG_SPLIT_NO_EMPTY);
                                    $sw_inits  = count($sw_parts) === 0 ? '?' : (count($sw_parts) === 1
                                        ? mb_strtoupper(mb_substr($sw_parts[0], 0, 1))
                                        : mb_strtoupper(mb_substr($sw_parts[0], 0, 1) . mb_substr($sw_parts[count($sw_parts) - 1], 0, 1)));
                                }
                            ?>
                                <a href="<?php echo base_url("admin/staff/profile/" . $svalue["id"]); ?>" class="sh-switcher-item<?php if ($is_current) { echo " sh-switcher-current"; } ?>">
                                    <?php if ($sw_has_image): ?>
                                        <img src="<?php echo $this->media_storage->getImageURL("uploads/staff_images/" . $image); ?>" alt="" class="sh-switcher-avatar">
                                    <?php else: ?>
                                        <div class="sh-switcher-initials"><?php echo html_escape($sw_inits); ?></div>
                                    <?php endif; ?>
                                    <div class="sh-switcher-info">
                                        <span class="sh-switcher-name"><?php echo html_escape($svalue['name'] . " " . $svalue['surname']); ?></span>
                                        <span class="sh-switcher-id"><?php echo html_escape($svalue['employee_id']); ?></span>
                                    </div>
                                    <?php if ($is_current) { ?>
                                        <i class="fa fa-bookmark sh-switcher-current-icon"></i>
                                    <?php } ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php
$image         = $staff['image'];
$ph_has_image  = !empty($image) && strpos($image, 'no_image') === false;
$file          = $ph_has_image ? $image : "no_image.png";
if (!$ph_has_image) {
    $full_name      = trim(($staff['name'] ?? '') . ' ' . ($staff['surname'] ?? ''));
    $name_parts     = preg_split('/\s+/', $full_name, -1, PREG_SPLIT_NO_EMPTY);
    $ph_initials    = count($name_parts) === 0 ? '?' : (count($name_parts) === 1
        ? mb_strtoupper(mb_substr($name_parts[0], 0, 1))
        : mb_strtoupper(mb_substr($name_parts[0], 0, 1) . mb_substr($name_parts[count($name_parts) - 1], 0, 1)));
}
?>

<!-- ══ PROFILE HEADER CARD ══ -->
<?php
$barcod_path = FCPATH . "uploads/staff_id_card/barcodes/" . $id . ".png";
$qr_path     = FCPATH . "uploads/staff_id_card/qrcode/" . $id . ".png";
$has_barcode = file_exists($barcod_path);
$has_qr      = file_exists($qr_path);
?>
<div class="card mb-3 overflow-hidden sh-ph-card">
    <div class="card-body p-3 p-md-4 position-relative">

        <!-- Staff Switcher — top right -->
        <div class="sh-ph-top-right">
            <?php if ($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) { ?>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#staffSwitcherOffcanvas" aria-controls="staffSwitcherOffcanvas">
                <i class="fa fa-navicon me-1"></i><?php echo $this->lang->line('staff'); ?>
            </button>
            <?php } ?>
        </div>

        <!-- Avatar + Identity -->
        <div class="d-flex align-items-start gap-4">
            <div class="sh-profile-avatar-wrap flex-shrink-0">
                <?php if ($ph_has_image): ?>
                    <img class="sh-profile-avatar sh-ph-avatar-v2" src="<?php echo $this->media_storage->getImageURL("uploads/staff_images/" . $file); ?>" alt="<?php echo html_escape($staff['name']); ?>">
                <?php else: ?>
                    <div class="sh-ph-initials-v2"><?php echo html_escape($ph_initials); ?></div>
                <?php endif; ?>
                <div class="sh-avatar-overlay"><i class="fa fa-camera"></i></div>
            </div>
            <div class="flex-grow-1 sh-ph-identity">
                <h4 class="fw-bold mb-1 sh-ph-name"><?php echo html_escape($staff['name'] . " " . $staff['surname']); ?></h4>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge sh-ph-badge-role"><?php echo html_escape($staff['user_type']); ?></span>
                    <span class="badge sh-ph-badge-empid"><?php echo html_escape($staff['employee_id']); ?></span>
                    <?php if (!empty($staff['department'])) { ?>
                    <span class="badge sh-ph-badge-dept"><?php echo html_escape($staff['department']); ?></span>
                    <?php } ?>
                    <?php if ($staff['is_active'] == 1) { ?>
                        <span class="sh-status-active">● <?php echo $this->lang->line('active'); ?></span>
                    <?php } else { ?>
                        <span class="sh-status-inactive">● <?php echo $this->lang->line('inactive'); ?></span>
                    <?php } ?>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($staff['designation'])) { ?>
                    <span class="sh-ph-chip"><i class="fa fa-briefcase"></i> <?php echo html_escape($staff['designation']); ?></span>
                    <?php } ?>
                    <?php if (!empty($staff['contact_no'])) { ?>
                    <span class="sh-ph-chip"><i class="fa fa-phone"></i> <?php echo html_escape($staff['contact_no']); ?></span>
                    <?php } ?>
                    <?php if (!empty($staff['email'])) { ?>
                    <span class="sh-ph-chip"><i class="fa fa-envelope"></i> <?php echo html_escape($staff['email']); ?></span>
                    <?php } ?>
                    <?php if (!empty($staff['date_of_joining']) && $staff['date_of_joining'] != '0000-00-00') { ?>
                    <span class="sh-ph-chip"><i class="fa fa-calendar"></i> <?php echo $this->customlib->YYYYMMDDTodateFormat($staff['date_of_joining']); ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Action buttons + Barcode/QR (right corner) -->
        <?php
        $can_edit_staff = ($this->rbac->hasPrivilege('staff', 'can_edit') && $permission_access == 1);
        $show_codes     = $has_barcode || $has_qr;
        if ($can_edit_staff || $show_codes) {
        ?>
        <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 sh-ph-actions">
            <?php if ($can_edit_staff) { ?>
            <a href="<?php echo base_url('admin/staff/edit/' . $id); ?>" class="btn btn-sm btn-primary">
                <i class="fa fa-pencil me-1"></i><?php echo $this->lang->line('edit'); ?>
            </a>
            <a href="#" class="change_password btn btn-sm btn-outline-secondary">
                <i class="fa fa-key me-1"></i><?php echo $this->lang->line('staff_change_password'); ?>
            </a>
            <?php if ($userdata["id"] != $staff["id"]) {
                if ($staff["is_active"] == 1) {
                    if ($this->rbac->hasPrivilege('disable_staff', 'can_view')) { ?>
                        <a href="<?php echo base_url('admin/staff/disablestaff/' . $id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo $this->lang->line("staff_are_you_sure_you_want_to_disable_this_record"); ?>');">
                            <i class="fa fa-thumbs-o-down me-1"></i><?php echo $this->lang->line('staff_disable'); ?>
                        </a>
                    <?php }
                } else { ?>
                    <a href="<?php echo base_url('admin/staff/enablestaff/' . $id); ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('<?php echo $this->lang->line("staff_are_you_sure_you_want_to_enable_this_record"); ?>');">
                        <i class="fa fa-thumbs-o-up me-1"></i><?php echo $this->lang->line('staff_enable'); ?>
                    </a>
                    <?php if ($this->rbac->hasPrivilege('staff', 'can_delete') && $permission_access == 1 && $userdata["id"] != $staff["id"]) { ?>
                        <a href="<?php echo base_url('admin/staff/delete/' . $id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo $this->lang->line("staff_are_you_sure_you_want_to_delete_this_record"); ?>');">
                            <i class="fa fa-trash me-1"></i><?php echo $this->lang->line('delete'); ?>
                        </a>
                    <?php }
                }
            } ?>
            <?php } ?>

            <?php if ($show_codes) { ?>
            <div class="ms-auto d-none d-lg-flex align-items-center gap-2 sh-ph-codes">
                <?php if ($has_barcode) {
                    $barcod_image = base_url("uploads/staff_id_card/barcodes/" . $id . ".png") . "?" . time(); ?>
                    <a href="<?php echo $barcod_image; ?>" target="_blank">
                        <img src="<?php echo $barcod_image; ?>" width="76" height="28" class="object-fit-contain d-block" alt="Barcode">
                    </a>
                <?php } ?>
                <?php if ($has_qr) {
                    $qrcode_image = base_url("uploads/staff_id_card/qrcode/" . $id . ".png") . "?" . time(); ?>
                    <a href="<?php echo $qrcode_image; ?>" target="_blank">
                        <img src="<?php echo $qrcode_image; ?>" width="40" height="40" class="object-fit-contain d-block rounded sh-ph-qr-img" alt="QR Code">
                    </a>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

    </div><!-- /card-body -->
</div><!-- /profile header card -->

<!-- ══ OVERVIEW GRID ══ -->
<div class="card mb-3">
    <div class="card-header sh-overview-header">
        <i class="fa fa-th-list me-2 sh-link-icon"></i><?php echo $this->lang->line('staff_profile'); ?> — <?php echo $this->lang->line('overview'); ?>
    </div>
    <div class="bcard-grid">
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_department'); ?></div>
            <div class="v"><?php echo html_escape($staff['department']); ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_specialist'); ?></div>
            <div class="v">
                <?php if (!empty($staff_speciality)) {
                    foreach ($staff_speciality as $sv) { echo html_escape($sv->specialist_name) . " "; }
                } ?>
            </div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_epf_no'); ?></div>
            <div class="v"><?php echo html_escape($staff['epf_no']); ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_basic_salary'); ?></div>
            <div class="v"><?php echo html_escape($staff['basic_salary']); ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_contract_type'); ?></div>
            <div class="v"><?php if (array_key_exists($staff['contract_type'], $contract_type)) { echo html_escape($contract_type[$staff['contract_type']]); } ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_work_shift'); ?></div>
            <div class="v"><?php echo html_escape($staff['shift']); ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_work_location'); ?></div>
            <div class="v"><?php echo html_escape($staff['location']); ?></div>
        </div>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_date_of_joining'); ?></div>
            <div class="v"><?php if (!empty($staff["date_of_joining"]) && $staff["date_of_joining"] != '0000-00-00') { echo $this->customlib->YYYYMMDDTodateFormat($staff['date_of_joining']); } ?></div>
        </div>
        <?php if ($staff["is_active"] == 0) { ?>
        <div class="field">
            <div class="l"><?php echo $this->lang->line('staff_date_of_leaving'); ?></div>
            <div class="v"><?php if ($staff["date_of_leaving"] != '0000-00-00' && $staff['date_of_leaving']) { echo $this->customlib->YYYYMMDDTodateFormat($staff['date_of_leaving']); } ?></div>
        </div>
        <?php } ?>
    </div>
</div><!-- /overview grid -->

<!-- ══ TABS CARD ══ -->
<div class="card">
    <div class="card-body p-0">
        <ul class="nav nav-tabs sh-profile-tabs px-3 pt-2 border-bottom">
            <li class="nav-item">
                <a class="nav-link active" href="#activity" data-bs-toggle="tab">
                    <i class="fa fa-user me-1"></i><?php echo $this->lang->line('staff_profile'); ?>
                </a>
            </li>
            <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_view')) { ?>
            <li class="nav-item">
                <a class="nav-link" href="#payroll" data-bs-toggle="tab">
                    <i class="fa fa-money me-1"></i><?php echo $this->lang->line('staff_payroll'); ?>
                </a>
            </li>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('apply_leave', 'can_view')) { ?>
            <li class="nav-item">
                <a class="nav-link" href="#leaves" data-bs-toggle="tab">
                    <i class="fa fa-plane me-1"></i><?php echo $this->lang->line('staff_leaves'); ?>
                </a>
            </li>
            <?php } ?>
            <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) { ?>
            <li class="nav-item">
                <a class="nav-link" href="#attendance" data-bs-toggle="tab">
                    <i class="fa fa-calendar-check-o me-1"></i><?php echo $this->lang->line('staff_attendance'); ?>
                </a>
            </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="#documents" data-bs-toggle="tab">
                    <i class="fa fa-folder-open me-1"></i><?php echo $this->lang->line('staff_documents'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#timeline" data-bs-toggle="tab">
                    <i class="fa fa-history me-1"></i><?php echo $this->lang->line('staff_timeline'); ?>
                </a>
            </li>
        </ul>

        <div class="tab-content p-3">

            <!-- ── TAB: STAFF PROFILE ── -->
            <div class="tab-pane active" id="activity">
                <div class="card overflow-hidden">
                    <div class="card-header sh-overview-header"><i class="fa fa-id-card-o me-2 sh-link-icon"></i><?php echo $this->lang->line('staff_personal_information') ?: $this->lang->line('staff_profile'); ?></div>
                    <div class="card-body p-0">
                        <div class="sh-field-grid">

                            <!-- Basic Details (no section header — starts directly) -->
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_phone'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['contact_no']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_emergency_contact_number'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['emergency_contact_no']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_email'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['email']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_gender'); ?></div>
                                <div class="sh-fi-value"><?php echo $this->lang->line(strtolower($staff['gender'])) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_blood_group'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['blood_group']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_date_of_birth'); ?></div>
                                <div class="sh-fi-value"><?php echo (!empty($staff['dob'])) ? $this->customlib->YYYYMMDDTodateFormat($staff['dob']) : '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_marital_status'); ?></div>
                                <div class="sh-fi-value"><?php echo $this->lang->line(strtolower($staff['marital_status'])) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_father_name'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['father_name']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_mother_name'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['mother_name']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_qualification'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['qualification']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_work_experience'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['work_exp']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_specialization'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['specialization']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_note'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['note']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('pan_number'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['pan_number']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('national_identification_number'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['identification_number']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('local_identification_number'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['local_identification_number']) ?: '—'; ?></div>
                            </div>

                            <?php
                            $cutom_fields_data = get_custom_table_values($staff['id'], 'staff');
                            if (!empty($cutom_fields_data)) {
                                $cf_left = true;
                                foreach ($cutom_fields_data as $field_value) {
                                    $cf_class = $cf_left ? 'sh-field-item sh-fi-l' : 'sh-field-item';
                                    if ($field_value->type == 'link' && !empty($field_value->field_value)) {
                                        $cf_display = '<a href="' . html_escape($field_value->field_value) . '" target="_blank">' . html_escape($field_value->field_value) . '</a>';
                                    } elseif (is_string($field_value->field_value) && is_array(json_decode($field_value->field_value, true)) && json_last_error() == JSON_ERROR_NONE) {
                                        $cf_arr = json_decode($field_value->field_value);
                                        $cf_display = '<ul class="patient_custom_field mb-0">';
                                        foreach ($cf_arr as $v) { $cf_display .= '<li>' . html_escape($v) . '</li>'; }
                                        $cf_display .= '</ul>';
                                    } else {
                                        $cf_display = html_escape($field_value->field_value) ?: '—';
                                    }
                            ?>
                            <div class="<?php echo $cf_class; ?>">
                                <div class="sh-fi-label"><?php echo html_escape($field_value->name); ?></div>
                                <div class="sh-fi-value"><?php echo $cf_display; ?></div>
                            </div>
                            <?php $cf_left = !$cf_left; } } ?>

                            <!-- Section: Address -->
                            <div class="sh-fi-section"><?php echo $this->lang->line('address'); ?></div>
                            <div class="sh-field-item sh-fi-full">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_current_address'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['local_address']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-full">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_permanent_address'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['permanent_address']) ?: '—'; ?></div>
                            </div>

                            <!-- Section: Bank Account Details -->
                            <div class="sh-fi-section"><?php echo $this->lang->line('staff_bank_account_details'); ?></div>
                            <div class="sh-field-item sh-fi-full">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_account_title'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['account_title']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_bank_name'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['bank_name']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_bank_branch_name'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['bank_branch']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_bank_account_number'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['bank_account_no']) ?: '—'; ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><?php echo $this->lang->line('staff_ifsc_code'); ?></div>
                                <div class="sh-fi-value"><?php echo html_escape($staff['ifsc_code']) ?: '—'; ?></div>
                            </div>

                            <!-- Section: Social Media Links -->
                            <div class="sh-fi-section"><?php echo $this->lang->line('staff_social_media_link'); ?></div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><i class="fab fa-facebook sh-si-fb me-1"></i><?php echo $this->lang->line('staff_facebook_url'); ?></div>
                                <div class="sh-fi-value"><?php if (!empty($staff['facebook'])) { ?><a href="<?php echo html_escape($staff['facebook']); ?>" target="_blank"><?php echo html_escape($staff['facebook']); ?></a><?php } else { echo '—'; } ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><i class="fab fa-twitter sh-si-tw me-1"></i><?php echo $this->lang->line('staff_twitter_url'); ?></div>
                                <div class="sh-fi-value"><?php if (!empty($staff['twitter'])) { ?><a href="<?php echo html_escape($staff['twitter']); ?>" target="_blank"><?php echo html_escape($staff['twitter']); ?></a><?php } else { echo '—'; } ?></div>
                            </div>
                            <div class="sh-field-item sh-fi-l">
                                <div class="sh-fi-label"><i class="fab fa-linkedin sh-si-li me-1"></i><?php echo $this->lang->line('staff_linkedin_url'); ?></div>
                                <div class="sh-fi-value"><?php if (!empty($staff['linkedin'])) { ?><a href="<?php echo html_escape($staff['linkedin']); ?>" target="_blank"><?php echo html_escape($staff['linkedin']); ?></a><?php } else { echo '—'; } ?></div>
                            </div>
                            <div class="sh-field-item">
                                <div class="sh-fi-label"><i class="fab fa-instagram sh-si-ig me-1"></i><?php echo $this->lang->line('staff_instagram_url'); ?></div>
                                <div class="sh-fi-value"><?php if (!empty($staff['instagram'])) { ?><a href="<?php echo html_escape($staff['instagram']); ?>" target="_blank"><?php echo html_escape($staff['instagram']); ?></a><?php } else { echo '—'; } ?></div>
                            </div>

                        </div><!-- /.sh-field-grid -->
                    </div>
                </div>
            </div><!-- /tab-pane #activity -->

            <!-- ── TAB: PAYROLL ── -->
            <div class="tab-pane" id="payroll">
                <!-- KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="sh-kpi-icon" style="background:var(--green-soft);color:var(--green);"><i class="fa fa-check-circle"></i></div>
                                <div>
                                    <div class="sh-kpi-label"><?php echo $this->lang->line('staff_total_net_salary_paid'); ?></div>
                                    <div class="sh-kpi-value"><?php echo $currency_symbol . (!empty($salary["net_salary"]) ? number_format((float)$salary["net_salary"], 2, '.', '') : "0"); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="sh-kpi-icon" style="background:var(--blue-soft);color:var(--blue);"><i class="fa fa-money"></i></div>
                                <div>
                                    <div class="sh-kpi-label"><?php echo $this->lang->line('staff_total_gross_salary'); ?></div>
                                    <div class="sh-kpi-value"><?php
                                        if (!empty($salary["basic_salary"])) {
                                            $grosssalary = ($salary["basic_salary"] + $salary["earnings"] - $salary["deduction"]);
                                            echo $currency_symbol . number_format((float)$grosssalary, 2, '.', '');
                                        } else {
                                            echo $currency_symbol . "0";
                                        }
                                    ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="sh-kpi-icon" style="background:var(--teal-soft);color:var(--teal);"><i class="fa fa-plus-circle"></i></div>
                                <div>
                                    <div class="sh-kpi-label"><?php echo $this->lang->line('staff_total_earning'); ?></div>
                                    <div class="sh-kpi-value"><?php echo $currency_symbol . (!empty($salary["earnings"]) ? number_format((float)$salary["earnings"], 2, '.', '') : "0"); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="sh-kpi-icon" style="background:var(--red-soft);color:var(--red);"><i class="fa fa-minus-circle"></i></div>
                                <div>
                                    <div class="sh-kpi-label"><?php echo $this->lang->line('staff_total_deduction'); ?></div>
                                    <div class="sh-kpi-value"><?php echo $currency_symbol . number_format((float)$salary["deduction"], 2, '.', ''); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /kpi row -->

                <!-- Payslip Table -->
                <div class="table-responsive">
                    <div class="download_label"><?php echo $staff["name"] . " " . $staff["surname"] . " (" . $staff['employee_id'] . ")"; ?></div>
                    <table class="table table-hover table-striped example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('staff_payslip'); ?> #</th>
                                <th><?php echo $this->lang->line('month_year'); ?></th>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <th><?php echo $this->lang->line('mode'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('net_salary'); ?> <span>(<?php echo $currency_symbol; ?>)</span></th>
                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_payroll as $key => $payroll_value) {
                                if ($payroll_value["status"] == "paid") {
                                    $label = "class='badge bg-success'";
                                } elseif ($payroll_value["status"] == "generated") {
                                    $label = "class='badge bg-warning text-dark'";
                                } else {
                                    $label = "class='badge bg-secondary'";
                                } ?>
                                <tr>
                                    <td>
                                        <a data-bs-toggle="popover" href="#" class="detail_popover" title=""><?php echo $payroll_value['id']; ?></a>
                                        <div class="fee_detail_popover d-none" ><?php echo $payroll_value['remark']; ?></div>
                                    </td>
                                    <td><?php echo $this->lang->line($payroll_value['month']) . " - " . $payroll_value['year']; ?></td>
                                    <td><?php echo $this->customlib->YYYYMMDDTodateFormat($payroll_value['payment_date']); ?></td>
                                    <td><?php if (!empty($payroll_value['payment_mode'])) { echo $payment_mode[$payroll_value['payment_mode']]; } ?></td>
                                    <td><span <?php echo $label; ?>><?php echo $payroll_status[$payroll_value['status']]; ?></span></td>
                                    <td class="text-end fw-bold"><?php echo $payroll_value['net_salary']; ?></td>
                                    <td class="text-end noExport text-nowrap">
                                        <?php if ($payroll_value["status"] == "paid") { ?>
                                            <a href="#" onclick="getPayslip('<?php echo $payroll_value["id"]; ?>')" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('payslip_view'); ?>"><i class="fa fa-eye"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /tab-pane #payroll -->

            <!-- ── TAB: LEAVES ── -->
            <div class="tab-pane" id="leaves">
                <!-- Leave Type Cards -->
                <div class="row g-3 mb-4">
                    <?php foreach ($leavedetails as $ldkey => $ldvalue) {
                        if (!empty($ldvalue["alloted_leave"])) { ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="sh-leave-card">
                                <div class="sh-leave-icon"><i class="fa fa-plane"></i></div>
                                <div>
                                    <div class="sh-leave-name"><?php echo html_escape($ldvalue["type"]); ?> <span style="color:var(--muted);font-weight:400;">(<?php echo $ldvalue["alloted_leave"]; ?>)</span></div>
                                    <div class="sh-leave-pills">
                                        <span class="sh-lp sh-lp-used"><?php echo $this->lang->line('used'); ?>: <?php echo !empty($ldvalue["approve_leave"]) ? $ldvalue["approve_leave"] : 0; ?></span>
                                        <span class="sh-lp sh-lp-avail"><?php echo $this->lang->line('available'); ?>: <?php echo (int)$ldvalue["alloted_leave"] - (int)$ldvalue["approve_leave"]; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } } ?>
                </div>

                <!-- Leave Requests Table -->
                <div class="download_label"><?php echo $staff["name"] . " " . $staff["surname"] . " (" . $staff['employee_id'] . ")"; ?></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('leave_type'); ?></th>
                                <th><?php echo $this->lang->line('leave_date'); ?></th>
                                <th><?php echo $this->lang->line('days'); ?></th>
                                <th><?php echo $this->lang->line('apply_date'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th><?php echo $this->lang->line('approved_date'); ?></th>
                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_leaves as $key => $value) {
                                $label = "";
                                if ($value["status"] == "approve") {
                                    $label = "class='badge bg-success'";
                                } elseif ($value["status"] == "pending") {
                                    $label = "class='badge bg-warning text-dark'";
                                } elseif ($value["status"] == "disapprove") {
                                    $label = "class='badge bg-danger'";
                                } ?>
                                <tr>
                                    <td><?php echo $value["type"]; ?></td>
                                    <td><?php echo $this->customlib->YYYYMMDDTodateFormat($value['leave_from']) . " - " . $this->customlib->YYYYMMDDTodateFormat($value['leave_to']); ?></td>
                                    <td><?php echo $value["leave_days"]; ?></td>
                                    <td><?php echo $this->customlib->YYYYMMDDTodateFormat($value['date']); ?></td>
                                    <td><small class="text-capitalize" <?php echo $label; ?>><?php echo $status[$value["status"]];
                                        if ($superadmin_restriction == 'disabled' && $value['apply_by_role_id'] == 7) {
                                            echo '';
                                        } else {
                                            echo " " . $this->lang->line('by') . ' ' . composeStaffNameByString($value['apply_by_name'], $value['apply_by_surname'], $value['apply_by_employee_id']);
                                        }
                                    ?></small></td>
                                    <td><?php if ($value['approved_date']) { echo $this->customlib->YYYYMMDDTodateFormat($value['approved_date']); } ?></td>
                                    <td class="text-end noExport">
                                        <a href="#leavedetails" onclick="getRecord('<?php echo $value["id"]; ?>')" role="button" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a>
                                        <?php if (!empty($value['document_file'])) { ?>
                                            <a href="<?php echo base_url(); ?>admin/staff/download/<?php echo $value['id'] . "/" . $value['staff_id']; ?>" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /tab-pane #leaves -->

            <!-- ── TAB: ATTENDANCE ── -->
            <div class="tab-pane" id="attendance">
                <!-- Stat Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-present" id="present"><?php echo !empty($countAttendance[date("Y")]["present"]) ? $countAttendance[date("Y")]["present"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_present'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-late" id="late"><?php echo !empty($countAttendance[date("Y")]["late"]) ? $countAttendance[date("Y")]["late"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_late'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-absent" id="absent"><?php echo !empty($countAttendance[date("Y")]["absent"]) ? $countAttendance[date("Y")]["absent"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_absent'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-half" id="half_day"><?php echo !empty($countAttendance[date("Y")]["half_day"]) ? $countAttendance[date("Y")]["half_day"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_first_half'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-half" id="half_day_second_shift"><?php echo !empty($countAttendance[date("Y")]["half_day_second_shift"]) ? $countAttendance[date("Y")]["half_day_second_shift"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_second_half'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="sh-att-stat">
                            <div class="sh-att-num sh-att-holiday" id="holiday"><?php echo !empty($countAttendance[date("Y")]["holiday"]) ? $countAttendance[date("Y")]["holiday"] : "0"; ?></div>
                            <div class="sh-att-label"><?php echo $this->lang->line('total_holiday'); ?></div>
                        </div>
                    </div>
                </div><!-- /stat cards -->

                <!-- Year filter + Legend -->
                <div class="sh-att-filter-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="sh-qs-label mb-0"><?php echo $this->lang->line('year'); ?></label>
                        <select class="form-select form-select-sm sh-att-year-sel" name="year" onchange="ajax_attendance('<?php echo $staff["id"]; ?>', this.value)">
                            <?php foreach ($yearlist as $yearkey => $yearvalue) { ?>
                                <option <?php if ($yearvalue["year"] == date("Y")) { echo "selected"; } ?> value="<?php echo $yearvalue["year"]; ?>"><?php echo $yearvalue["year"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="sh-att-legend d-flex flex-wrap gap-2">
                        <?php foreach ($attendencetypeslist as $key_type => $value_type) {
                            $att_type = strtolower($value_type['type']); ?>
                            <span class="sh-att-legend-item"><?php echo $this->lang->line($att_type); ?>: <b class="sh-att-legend-val"><?php echo $value_type['key_value']; ?></b></span>
                        <?php } ?>
                    </div>
                </div>

                <!-- Attendance Matrix -->
                <div class="download_label"><?php echo $this->lang->line('staff_attendance_report'); ?> <?php echo $staff["name"] . " " . $staff["surname"]; ?></div>
                <div id="ajaxattendance" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('date_month'); ?></th>
                                <?php foreach ($monthlist as $monthkey => $monthvalue) { ?>
                                    <th><?php echo $monthvalue; ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody id="attendancetable">
                            <?php
                            $j = 0;
                            for ($i = 1; $i <= 31; $i++) { ?>
                                <tr>
                                    <td><?php echo $attendence_array[$j]; ?></td>
                                    <?php foreach ($monthlist as $key => $value) {
                                        $datemonth = date("m", strtotime($value));
                                        $dayscount  = intval(date('t', strtotime($value)));
                                        if ($i <= $dayscount) {
                                            $att_dates = date("Y") . "-" . $datemonth . "-" . sprintf("%02d", $i); ?>
                                            <td><span data-bs-toggle="popover" class="detail_popover" title=""><a href="#" class="sh-text-ink"><?php
                                                if (array_key_exists($att_dates, $resultlist)) { echo $resultlist[$att_dates]["key"]; }
                                            ?></a></span>
                                            <div class="fee_detail_popover d-none" ><?php echo $resultlist[$att_dates]["remark"]; ?></div>
                                            </td>
                                        <?php } else { echo "<td></td>"; }
                                    } ?>
                                </tr>
                            <?php $j++; } ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /tab-pane #attendance -->

            <!-- ── TAB: DOCUMENTS ── -->
            <div class="tab-pane" id="documents">
                <?php if ((empty($staff["resume"])) && (empty($staff["joining_letter"])) && (empty($staff["resignation_letter"])) && (empty($staff["other_document_file"]))) { ?>
                    <div class="alert alert-info"><?php echo $this->lang->line("no_record_found"); ?></div>
                <?php } else { ?>
                    <div class="row g-3">
                        <?php if (!empty($staff["resume"])) { ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="sh-doc-card">
                                <div class="sh-doc-icon"><i class="fa fa-file-text-o"></i></div>
                                <div class="sh-doc-title"><?php echo $this->lang->line('resume'); ?></div>
                                <div class="sh-doc-actions">
                                    <a href="<?php echo base_url(); ?>admin/staff/download_document/<?php echo $staff['id'] . "/1/"; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <a href="<?php echo base_url(); ?>admin/staff/doc_delete/<?php echo $staff['id'] . "/1/"; ?>" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>')"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($staff["joining_letter"])) { ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="sh-doc-card">
                                <div class="sh-doc-icon"><i class="fa fa-file-archive-o"></i></div>
                                <div class="sh-doc-title"><?php echo $this->lang->line('joining_letter'); ?></div>
                                <div class="sh-doc-actions">
                                    <a href="<?php echo base_url(); ?>admin/staff/download_document/<?php echo $staff['id'] . "/2/"; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <a href="<?php echo base_url(); ?>admin/staff/doc_delete/<?php echo $staff['id'] . "/2/"; ?>" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>')"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($staff["resignation_letter"])) { ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="sh-doc-card">
                                <div class="sh-doc-icon"><i class="fa fa-file-archive-o"></i></div>
                                <div class="sh-doc-title"><?php echo $this->lang->line('resignation_letter'); ?></div>
                                <div class="sh-doc-actions">
                                    <a href="<?php echo base_url(); ?>admin/staff/download_document/<?php echo $staff['id'] . "/3/"; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <a href="<?php echo base_url(); ?>admin/staff/doc_delete/<?php echo $staff['id'] . "/3/"; ?>" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>')"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($staff["other_document_file"])) { ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="sh-doc-card">
                                <div class="sh-doc-icon"><i class="fa fa-file-archive-o"></i></div>
                                <div class="sh-doc-title"><?php echo $this->lang->line('other_documents'); ?></div>
                                <div class="sh-doc-actions">
                                    <a href="<?php echo base_url(); ?>admin/staff/download_document/<?php echo $staff['id'] . "/4/"; ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <a href="<?php echo base_url(); ?>admin/staff/doc_delete/<?php echo $staff['id'] . "/4/"; ?>" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>')"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div><!-- /tab-pane #documents -->

            <!-- ── TAB: TIMELINE ── -->
            <div class="tab-pane" id="timeline">
                <div class="sh-timeline-header">
                    <span class="sh-timeline-label"><?php echo $this->lang->line('staff_timeline'); ?></span>
                    <?php if ($this->rbac->hasPrivilege('staff_timeline', 'can_add')) { ?>
                    <button id="myTimelineButton" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add'); ?></button>
                    <?php } ?>
                </div>
                <div id="timeline_list">
                    <?php if (empty($timeline_list)) { ?>
                        <div class="alert alert-info text-center"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } else { ?>
                        <div class="sh-timeline">
                            <?php foreach ($timeline_list as $key => $value) { ?>
                            <div class="sh-tl-item">
                                <span class="sh-tl-dot"></span>
                                <div class="sh-tl-card">
                                    <div class="sh-tl-head">
                                        <div class="sh-tl-meta">
                                            <span class="sh-tl-date-badge"><i class="fa fa-calendar me-1"></i><?php echo $this->customlib->YYYYMMDDTodateFormat($value['timeline_date']); ?></span>
                                            <span class="sh-tl-title"><?php echo html_escape($value['title']); ?></span>
                                        </div>
                                        <div class="sh-tl-actions">
                                            <?php if ($this->rbac->hasPrivilege('edittimeline', 'can_delete')) { ?>
                                                <a onclick="editstaffTimeline('<?php echo $value['id']; ?>')" class="btn btn-sm btn-light sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('staff_timeline', 'can_delete')) { ?>
                                                <a onclick="delete_timeline('<?php echo $value['id']; ?>')" class="btn btn-sm btn-light text-danger sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                            <?php } ?>
                                            <?php if (!empty($value["document"])) { ?>
                                                <a href="<?php echo base_url() . "admin/timeline/download_staff_timeline/" . $value["id"] . "/" . $value["document"]; ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if (!empty(trim(strip_tags($value['description'])))) { ?>
                                    <div class="sh-tl-body">
                                        <?php echo $value['description']; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- /tab-pane #timeline -->

        </div><!-- /tab-content -->
    </div><!-- /card-body -->
</div><!-- /tabs card -->


<!-- ══════════ MODALS (unchanged — all IDs and form inputs preserved) ══════════ -->

<div id="leavedetails" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="leavedetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leavedetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form role="form" id="leavedetails_form" action="">
                <input id="leave_request_id" name="leave_request_id" type="hidden" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('leave'); ?> <?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0">
                                        <tr>
                                            <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                                            <td width="35%"><span id='name'></span></td>
                                            <th width="15%"><?php echo $this->lang->line('staff_id'); ?></th>
                                            <td width="35%"><span id="employee_id"></span></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('leave'); ?></th>
                                            <td><span id='leave_from'></span> - <span id='leave_to'></span> (<span id='days'></span>)</td>
                                            <th><?php echo $this->lang->line('leave_type'); ?></th>
                                            <td><span id="leave_type"></span></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('status'); ?></th>
                                            <td><span id="status" class="text-capitalize"></span></td>
                                            <th><?php echo $this->lang->line('apply_date'); ?></th>
                                            <td><span id="applied_date"></span></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('reason'); ?></th>
                                            <td><span id="reason"></span></td>
                                            <th><?php echo $this->lang->line('note'); ?></th>
                                            <td><span id="remark"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="timelineform" name="timelineform" method="post" action="<?php echo base_url() . "admin/timeline/add_staff_timeline"; ?>" enctype="multipart/form-data">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="staff_id" value="<?php echo $staff["id"]; ?>" id="staff_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                        <input id="timeline_title" name="timeline_title" type="text" class="form-control form-control-sm" />
                                        <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat())); ?>" type="text" class="form-control form-control-sm date" />
                                        <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea id="timeline_desc" name="timeline_desc" class="form-control form-control-sm" rows="3"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input id="timeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control form-control-sm" value="<?php echo set_value('timeline_doc'); ?>" />
                                        <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                    </div>
                                    <div class="col-12 d-flex align-items-center gap-2">
                                        <label class="form-label form-label-sm mb-0"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                        <div class="form-check form-switch m-0">
                                            <input id="visible_check" name="visible_check" value="yes" type="checkbox" role="switch" class="form-check-input chk" checked="checked" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id='timelinebtn' data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Timeline -->
<div class="modal fade sh-modal sh-modal-accent" id="myTimelineEditModal" tabindex="-1" aria-labelledby="myTimelineEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineEditModalLabel"><?php echo $this->lang->line('edit_timeline'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input type="hidden" name="staff_id" id="estaffid" value="">
                <input type="hidden" name="timeline_id" id="etimelineid" value="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                                        <input id="etimelinetitle" name="timeline_title" type="text" class="form-control form-control-sm" />
                                        <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input type="text" name="timeline_date" class="form-control form-control-sm date" id="etimelinedate"/>
                                        <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea id="timelineedesc" name="timeline_desc" class="form-control form-control-sm" rows="3"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input id="etimeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control form-control-sm" value="<?php echo set_value('timeline_doc'); ?>" />
                                        <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                                    </div>
                                    <div class="col-12 d-flex align-items-center gap-2">
                                        <label class="form-label form-label-sm mb-0"><?php echo $this->lang->line('visible_to_this_person'); ?></label>
                                        <div class="form-check form-switch m-0">
                                            <input id="evisible_check" name="visible_check" value="yes" type="checkbox" role="switch" class="form-check-input chk" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_timelinebtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="scheduleModal" class="modal fade sh-modal sh-modal-accent bs-example-modal-lg" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel"></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-body_logindetail"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="payslipview" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="payslipviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payslipviewLabel"><?php echo $this->lang->line('details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span id="print"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="testdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="changepwdmodal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="changepwdmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changepwdmodalLabel"><i class="fa fa-key me-2 sh-icon-soft"></i><?php echo $this->lang->line('staff_change_password'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="changepass" action="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('staff_change_password'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('staff_password'); ?> <small class="req">*</small></label>
                                        <input type="password" class="form-control form-control-sm" name="new_pass" id="pass">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('staff_confirm_password'); ?> <small class="req">*</small></label>
                                        <input type="password" class="form-control form-control-sm" name="confirm_pass" id="pwd">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="changepassbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ══════════ SCRIPTS (unchanged) ══════════ -->
<script type="text/javascript">

    $(document).ready(function (e) {
        $("#changepass").on('submit', (function (e) {
            $("#changepassbtn").btnLoading();
            var staff_id = $("#staff_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url('admin/staff/change_password/'); ?>" + staff_id,
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#changepassbtn").btnReset();
                },
                error: function (e) { alert("Fail"); console.log(e); }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#edit_timeline").on('submit', (function (e) {
            $("#edit_timelinebtn").btnLoading();
            var staff_id = $("#staff_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/timeline/edit_staff_timeline"); ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#edit_timelinebtn").btnReset();
                },
                error: function (e) { alert("Fail"); console.log(e); }
            });
        }));
    });

    $("#myTimelineButton").click(function () {
        $("#reset").click();
        shModal('myTimelineModal').show();
    });

    $(document).ready(function (e) {
        $("#timelineform").on('submit', (function (e) {
            $("#timelinebtn").btnLoading();
            var staff_id = $("#staff_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/timeline/add_staff_timeline"); ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $.ajax({
                            url: '<?php echo base_url(); ?>admin/timeline/staff_timeline/' + staff_id,
                            success: function (res) {
                                $('#timeline_list').html(res);
                                shModal('myTimelineModal').toggle();
                            },
                            error: function () { alert("Fail"); }
                        });
                    }
                    $("#timelinebtn").btnReset();
                },
                error: function (e) { alert("Fail"); console.log(e); }
            });
        }));
    });

    $('#myTimelineModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(".dropify-clear").click();
    });

    function editstaffTimeline(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/editstaffTimeline',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy']); ?>';
                var dt = new Date(data.timeline_date).toString(date_format);
                $("#etimelineid").val(id);
                $("#estaffid").val(data.staff_id);
                $("#etimelinetitle").val(data.title);
                $("#etimelinedate").val(dt);
                $("#timelineedesc").val(data.description);
                if (data.status == '') {
                } else {
                    $("#evisible_check").attr('checked', true);
                }
                shModal('myTimelineEditModal').show();
            },
        });
    }

    function delete_timeline(id) {
        var staff_id = $("#staff_id").val();
        if (confirm('<?php echo $this->lang->line("delete_confirm"); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/timeline/delete_staff_timeline/' + id,
                success: function (res) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/timeline/staff_timeline/' + staff_id,
                        success: function (res) {
                            $('#timeline_list').html(res);
                            successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                        },
                        error: function () { alert("Fail"); }
                    });
                },
                error: function () { alert("Fail"); }
            });
        }
    }
</script>
<script>
    $(document).ready(function () {
        $(document).on('click', '.change_password', function () {
            shModal('changepwdmodal').show();
        });

        // Staff switcher: arrow-key tab navigation
        var $switcherTabs = $('.sh-switcher-tabs');
        function updateSwitcherArrows() {
            if (!$switcherTabs.length) return;
            var el = $switcherTabs[0];
            var atStart = el.scrollLeft <= 0;
            var atEnd = (el.scrollLeft + el.clientWidth) >= (el.scrollWidth - 1);
            var hasOverflow = el.scrollWidth > el.clientWidth;
            $('.sh-switcher-tab-arrow-left').prop('disabled', atStart || !hasOverflow);
            $('.sh-switcher-tab-arrow-right').prop('disabled', atEnd || !hasOverflow);
        }
        $('.sh-switcher-tab-arrow').on('click', function () {
            var dir = parseInt($(this).data('dir'), 10);
            $switcherTabs[0].scrollBy({ left: dir * 120, behavior: 'smooth' });
        });
        $switcherTabs.on('scroll', updateSwitcherArrows);
        $('#staffSwitcherOffcanvas').on('show.bs.offcanvas', function () {
            $('body').addClass('sh-switcher-open');
        }).on('shown.bs.offcanvas', function () {
            // Scroll active tab into view, then update arrow state
            var $active = $switcherTabs.find('.nav-link.active').closest('.nav-item');
            if ($active.length) {
                $active[0].scrollIntoView({ inline: 'center', block: 'nearest' });
            }
            updateSwitcherArrows();
        }).on('hidden.bs.offcanvas', function () {
            $('body').removeClass('sh-switcher-open');
        });

        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']); ?>';

        /* #timeline_date init removed - auto-init via class + event delegation */
    });

    function shFormatLeaveDate(dateStr, fmt) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var pad = function (n) { return n < 10 ? '0' + n : n; };
        return fmt
            .replace('yyyy', d.getFullYear())
            .replace('MM', pad(d.getMonth() + 1))
            .replace('dd', pad(d.getDate()));
    }

    function getRecord(id) {
        $('input:radio[name=status]').attr('checked', false);
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {
                var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy']); ?>';
                $('inputs[name="leave_request_id"]').val(result.id);
                $('#name').html(result.name + ' ' + result.surname);
                $('#leave_from').html(shFormatLeaveDate(result.leave_from, leavedate_format));
                $('#leave_to').html(shFormatLeaveDate(result.leave_to, leavedate_format));
                $('#leave_type').html(result.type);
                $('#reason').html(result.employee_remark);
                $('#applied_date').html(shFormatLeaveDate(result.date, leavedate_format));
                $('#days').html(result.leave_days + ' Days');
                $("#remark").html(result.admin_remark);
                $("#employee_id").html(' ' + result.employee_id);
                $("#status").html(' ' + result.status);
            }
        });
        bootstrap.Modal.getOrCreateInstance(document.getElementById('leavedetails'),{backdrop:'static',keyboard:false}).show();
    }

    function ajax_attendance(id, year) {
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + 'admin/staff/ajax_attendance/' + id,
            type: 'POST',
            dataType: "json",
            data: {id: id, year: year},
            success: function (result) {
                if (result.status == 1) {
                    $("#attendancetable").html(result.page);
                    $.each(result.countAttendance, function (key, value) {
                        $("#absent").html(value.absent);
                        $("#half_day").html(value.half_day);
                        $("#half_day_second_shift").html(value.half_day_second_shift);
                        $("#holiday").html(value.holiday);
                        $("#late").html(value.late);
                        $("#present").html(value.present);
                    });
                } else {
                    $("#attendancetable").html(result.error);
                }
            }
        });
    }

    function getPayslip(id) {
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                $("#print").html("<a href='#' class='btn btn-light btn-sm moprint' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                $("#testdata").html(result);
            }
        });
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payslipview'),{backdrop:'static',keyboard:false}).show();
    }

    function printData(id) {
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipPrint',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                popup(result);
            }
        });
    }
</script>

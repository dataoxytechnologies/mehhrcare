<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$userdata = $this->customlib->getUserData();
$logged_in_User = $this->customlib->getLoggedInUserData();
$logged_in_User_Role = json_decode($this->customlib->getStaffRole());
$superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];

$_sh_avatar_colors = [
    'Doctor'         => 'linear-gradient(135deg,#1967d2,#1a73e8)',
    'Nurse'          => 'linear-gradient(135deg,#00897b,#26a69a)',
    'Pharmacist'     => 'linear-gradient(135deg,#7b1fa2,#9c27b0)',
    'Super Admin'    => 'linear-gradient(135deg,#c62828,#e53935)',
    'Admin'          => 'linear-gradient(135deg,#c62828,#e53935)',
    'Receptionist'   => 'linear-gradient(135deg,#f57c00,#fb8c00)',
    'Lab Technician' => 'linear-gradient(135deg,#2e7d32,#43a047)',
];
$_sh_avatar_fallbacks = [
    'linear-gradient(135deg,#0277bd,#039be5)',
    'linear-gradient(135deg,#2e7d32,#43a047)',
    'linear-gradient(135deg,#558b2f,#7cb342)',
    'linear-gradient(135deg,#37474f,#546e7a)',
    'linear-gradient(135deg,#4527a0,#5e35b1)',
    'linear-gradient(135deg,#f57c00,#fb8c00)',
];
$_sh_badge_map = [
    'Doctor'      => 'role-doctor',
    'Nurse'       => 'role-nurse',
    'Pharmacist'  => 'role-pharmacist',
    'Super Admin' => 'role-admin',
    'Admin'       => 'role-admin',
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><i class="fa fa-user-times sh-title-icon"></i><?php echo $this->lang->line('staff_disabled_staff'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <a href="<?php echo base_url('admin/staff'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back'); ?>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('msg')) { ?>
                    <div><?php echo $this->session->flashdata('msg'); ?></div>
                <?php } ?>

                <!-- Filter bar -->
                <form role="form" action="<?php echo site_url('admin/staff/disablestafflist'); ?>" method="post">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('staff_role'); ?></label>
                                <select name="role" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($role as $key => $role_value) { ?>
                                        <option value="<?php echo $role_value['type']; ?>"><?php echo $role_value['type']; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('role'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
                                <input type="text" name="search_text" class="form-control" placeholder="<?php echo $this->lang->line('search_by_staff'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                            <div class="mb-3">
                                <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                    <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <?php if (isset($resultlist)) { ?>

                <!-- Results toolbar -->
                <div class="sh-results-toolbar">
                    <span class="sh-results-label"><?php echo $this->lang->line('disable_staff_list'); ?></span>
                    <div class="sh-view-switch">
                        <button class="sh-vs-btn active" onclick="shStaffSwitchView('card', this)">
                            <i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line('card_view'); ?>
                        </button>
                        <button class="sh-vs-btn" onclick="shStaffSwitchView('list', this)">
                            <i class="fa fa-list"></i> <?php echo $this->lang->line('list_view'); ?>
                        </button>
                    </div>
                </div>

                <!-- ─── CARD VIEW ─── -->
                <div id="sh-view-card">
                    <?php if (empty($resultlist)) { ?>
                        <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } else { ?>
                    <div class="row g-3">
                        <?php
                        $_sh_fi = 0;
                        foreach ($resultlist as $staff) {
                            if ($staff['user_type'] != 'Super Admin' || $superadmin_rest != 'disabled') {
                                $initials = strtoupper(substr($staff['name'], 0, 1) . substr($staff['surname'], 0, 1));
                                $av_grad  = isset($_sh_avatar_colors[$staff['user_type']])
                                    ? $_sh_avatar_colors[$staff['user_type']]
                                    : $_sh_avatar_fallbacks[$_sh_fi % count($_sh_avatar_fallbacks)];
                                $_sh_fi++;
                                $bdg = isset($_sh_badge_map[$staff['user_type']]) ? $_sh_badge_map[$staff['user_type']] : 'role-default';
                        ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="sh-staff-card">
                                <div class="sh-staff-card-body">
                                    <!-- sidebar -->
                                    <div class="sh-staff-sidebar" style="background:<?php echo $av_grad; ?>">
                                        <div class="sh-staff-av-wrap">
                                            <div class="sh-staff-av">
                                                <?php if (!empty($staff['image'])) { ?>
                                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/' . $staff['image']); ?>" alt="">
                                                <?php } else { ?>
                                                    <?php echo html_escape($initials); ?>
                                                <?php } ?>
                                            </div>
                                            <span class="sh-staff-online"></span>
                                        </div>
                                    </div>
                                    <!-- content -->
                                    <div class="sh-staff-content">
                                        <div class="sh-staff-head-row">
                                            <div class="sh-staff-name" data-bs-toggle="tooltip" title="<?php echo html_escape($staff['name'] . ' ' . $staff['surname']); ?>">
                                                <?php echo html_escape($staff['name'] . ' ' . $staff['surname']); ?>
                                            </div>
                                            <div class="sh-staff-role-row">
                                                <span class="sh-role-badge <?php echo $bdg; ?>"><?php echo html_escape($staff['user_type']); ?></span>
                                                <?php if (!empty($staff['department'])) { ?>
                                                <span class="sh-dept-badge" data-bs-toggle="tooltip" title="<?php echo html_escape($staff['department']); ?>"><?php echo html_escape($staff['department']); ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($staff['employee_id'])) { ?>
                                        <span class="sh-staff-eid-tag"># <?php echo html_escape($staff['employee_id']); ?></span>
                                        <?php } ?>
                                        <div class="sh-staff-rows">
                                            <?php if (!empty($staff['contact_no'])) { ?>
                                            <div class="sh-staff-row">
                                                <div class="sh-staff-ico sh-ico-a"><i class="fa fa-phone"></i></div>
                                                <div class="sh-staff-row-text">
                                                    <span class="sh-staff-row-label"><?php echo $this->lang->line('contact_no'); ?></span>
                                                    <span class="sh-staff-row-val"><?php echo html_escape($staff['contact_no']); ?></span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if (!empty($staff['designation'])) { ?>
                                            <div class="sh-staff-row">
                                                <div class="sh-staff-ico sh-ico-g"><i class="fa fa-suitcase"></i></div>
                                                <div class="sh-staff-row-text">
                                                    <span class="sh-staff-row-label"><?php echo $this->lang->line('designation'); ?></span>
                                                    <span class="sh-staff-row-val"><?php echo html_escape($staff['designation']); ?></span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if (!empty($staff['specialist_name'])) { ?>
                                            <div class="sh-staff-row">
                                                <div class="sh-staff-ico sh-ico-b"><i class="fa fa-stethoscope"></i></div>
                                                <div class="sh-staff-row-text">
                                                    <span class="sh-staff-row-label"><?php echo $this->lang->line('specialist_name'); ?></span>
                                                    <span class="sh-staff-row-val"><?php echo html_escape($staff['specialist_name']); ?></span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="sh-staff-card-actions">
                                    <?php if (($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) || ($userdata['id'] == $staff['id'])) { ?>
                                        <a href="<?php echo base_url() . 'admin/staff/profile/' . $staff['id']; ?>" class="sh-btn-view">
                                            <i class="fa fa-navicon"></i> <?php echo $this->lang->line('show'); ?>
                                        </a>
                                    <?php } ?>
                                    <?php if ($this->rbac->hasPrivilege('disable_staff', 'can_view') && $userdata['id'] != $staff['id']) { ?>
                                        <a href="<?php echo base_url() . 'admin/staff/enablestaff/' . $staff['id']; ?>" class="sh-btn-enable" onclick="return confirm('<?php echo $this->lang->line('staff_are_you_sure_you_want_to_enable_this_record'); ?>');">
                                            <i class="fa fa-thumbs-o-up"></i> <?php echo $this->lang->line('staff_enable'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } } ?>
                    </div>
                    <?php } ?>
                </div>

                <!-- ─── LIST VIEW ─── -->
                <div id="sh-view-list" class="d-none">
                    <div class="download_label"><?php echo $this->lang->line('disable_staff_list'); ?></div>
                    <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('staff_id'); ?></th>
                                <th><?php echo $this->lang->line('staff_name'); ?></th>
                                <th><?php echo $this->lang->line('staff_role'); ?></th>
                                <th><?php echo $this->lang->line('staff_department'); ?></th>
                                <th><?php echo $this->lang->line('staff_designation'); ?></th>
                                <th><?php echo $this->lang->line('staff_mobile_number'); ?></th>
                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $_sh_fi2 = 0;
                            foreach ($resultlist as $staff) {
                                if ($staff['user_type'] != 'Super Admin' || $superadmin_rest != 'disabled') {
                                $initials2 = strtoupper(substr($staff['name'], 0, 1) . substr($staff['surname'], 0, 1));
                                $av_grad2  = isset($_sh_avatar_colors[$staff['user_type']])
                                    ? $_sh_avatar_colors[$staff['user_type']]
                                    : $_sh_avatar_fallbacks[$_sh_fi2 % count($_sh_avatar_fallbacks)];
                                $_sh_fi2++;
                                $bdg2 = isset($_sh_badge_map[$staff['user_type']]) ? $_sh_badge_map[$staff['user_type']] : 'role-default';
                            ?>
                            <tr>
                                <td><?php echo html_escape($staff['employee_id']); ?></td>
                                <td>
                                    <div class="sh-tbl-name-cell">
                                        <div class="sh-tbl-avatar" style="background:<?php echo $av_grad2; ?>">
                                            <?php if (!empty($staff['image'])) { ?>
                                                <img src="<?php echo $this->media_storage->getImageURL('uploads/staff_images/' . $staff['image']); ?>" alt="">
                                            <?php } else { ?>
                                                <?php echo html_escape($initials2); ?>
                                            <?php } ?>
                                        </div>
                                        <span class="sh-tbl-name"><?php echo html_escape($staff['name']) . ' ' . html_escape($staff['surname']); ?></span>
                                    </div>
                                </td>
                                <td><span class="sh-role-badge <?php echo $bdg2; ?> m-0"><?php echo html_escape($staff['user_type']); ?></span></td>
                                <td><?php echo html_escape($staff['department']); ?></td>
                                <td><?php echo html_escape($staff['designation']); ?></td>
                                <td><?php echo html_escape($staff['contact_no']); ?></td>
                                <td class="text-end noExport">
                                    <?php if (($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) || ($userdata['id'] == $staff['id'])) { ?>
                                        <a href="<?php echo base_url() . 'admin/staff/profile/' . $staff['id']; ?>" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>">
                                            <i class="fa fa-reorder"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if ($this->rbac->hasPrivilege('disable_staff', 'can_view') && $userdata['id'] != $staff['id']) { ?>
                                        <a href="<?php echo base_url() . 'admin/staff/enablestaff/' . $staff['id']; ?>" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('staff_enable'); ?>" onclick="return confirm('<?php echo $this->lang->line('staff_are_you_sure_you_want_to_enable_this_record'); ?>');">
                                            <i class="fa fa-thumbs-o-up"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>

                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
function shStaffSwitchView(view, btn) {
    // Toggle the .d-none CLASS (markup uses it) — setting style.display can't override
    // .d-none's `display:none !important`, which is why List View showed empty.
    document.getElementById('sh-view-card').classList.toggle('d-none', view !== 'card');
    document.getElementById('sh-view-list').classList.toggle('d-none', view !== 'list');
    document.querySelectorAll('.sh-vs-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    if (view === 'list' && typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    }
}
</script>

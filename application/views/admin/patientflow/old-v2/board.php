<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Human-readable gender label ("M"/"Male" -> "Male").
 */
if (!function_exists('pf_ct_gender_label')) {
    function pf_ct_gender_label($gender)
    {
        $gender = trim((string) $gender);
        if ($gender === '') {
            return '';
        }
        $key = strtolower($gender);
        if ($key === 'm' || $key === 'male') {
            return 'Male';
        }
        if ($key === 'f' || $key === 'female') {
            return 'Female';
        }
        if ($key === 'o' || $key === 'other') {
            return 'Other';
        }
        return ucfirst($gender);
    }
}

/**
 * Age label for the patient panel: uses the stored age/year-month-day, and
 * falls back to the date of birth when the stored age is not filled in.
 */
if (!function_exists('pf_ct_age_label')) {
    function pf_ct_age_label($year, $month, $day, $dob)
    {
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;

        if ($year <= 0 && !empty($dob) && $dob !== '0000-00-00') {
            $ts = strtotime($dob);
            if ($ts) {
                $now = time();
                $year = (int) floor(($now - $ts) / 31557600);
                $diff = $now - $ts;
                $month = (int) floor(($diff - ($year * 31557600)) / 2629800);
                $day = 0;
            }
        }

        $age = '';
        if ($year > 0) {
            $age .= $year . ' Year' . ($year > 1 ? 's' : '');
        }
        if ($month > 0) {
            $age .= ($age !== '' ? ', ' : '') . $month . ' Month' . ($month > 1 ? 's' : '');
        }
        if ($day > 0) {
            $age .= ($age !== '' ? ', ' : '') . $day . ' Day' . ($day > 1 ? 's' : '');
        }
        return $age;
    }
}
?>
<style>
    .pf-visit-badge { min-width: 96px; font-weight: 600; letter-spacing: .3px; }
    .pf-token { font-weight: 700; font-size: 0.95rem; }
    .pf-token-no { font-weight: 700; font-size: 0.9rem; letter-spacing: .3px; }
    .bg-purple { background-color: #6f42c1 !important; }
</style>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-users sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('patient_flow'); ?></h3>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_add')) { ?>
            <!-- <a href="<?php echo site_url('admin/patientflow/register'); ?>" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_patient'); ?>
            </a> -->
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="pfOpenOpdFresh();"
                    title="<?php echo $this->lang->line('new_visit') ?: 'New Visit'; ?>">
                <i class="fas fa-file-medical"></i> <!-- <?php echo $this->lang->line('opd') ?: 'OPD'; ?> --> <?php echo $this->lang->line('add_patient') ?: 'Form'; ?>
            </button>
            <a href="<?php echo site_url('admin/patientflow/token_queue'); ?>" class="btn btn-sm btn-outline-info"
               title="<?php echo $this->lang->line('token_queue') ?: 'Token Queue'; ?>">
                <i class="fa fa-ticket"></i> <?php echo $this->lang->line('token_queue') ?: 'Token Queue'; ?>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('admin/patientflowsetting/setting?group=general_settings'); ?>" class="btn btn-sm btn-outline-secondary"
               title="<?php echo $this->lang->line('patient_flow_setting') ?: 'Setting'; ?>">
                <i class="fa fa-cog"></i> <?php echo $this->lang->line('patient_flow_setting') ?: 'Setting'; ?>
            </a>
        </div>
    </div>

    <?php if (!$pf_is_superadmin) { ?>
    <div class="card-body border-bottom py-2">
        <div class="d-flex align-items-center flex-wrap gap-2 small">
            <i class="fa fa-shield-alt text-primary"></i>
            <span><?php echo $this->lang->line('showing_my_levels'); ?></span>
            <?php if (empty($pf_my_levels)) { ?>
                <span class="badge bg-danger-subtle text-danger-emphasis"><?php echo $this->lang->line('no_levels_assigned'); ?></span>
            <?php } else { ?>
                <?php foreach ($pf_my_levels as $ml) { ?>
                    <span class="badge bg-primary-subtle text-primary-emphasis"><?php echo $ml->code . ' — ' . $ml->title; ?></span>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="card-body border-bottom">
        <form method="get" action="<?php echo site_url('admin/patientflow'); ?>" class="row g-3 align-items-end">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status, ENT_QUOTES); ?>">
            <div class="col-md-5 col-lg-4">
                <label class="form-label form-label-sm"><?php echo $this->lang->line('search') ?: 'Search'; ?></label>
                <input type="text" name="search" id="filter_search" class="form-control"
                       value="<?php echo isset($filter_search) ? htmlspecialchars($filter_search, ENT_QUOTES) : ''; ?>"
                       placeholder="<?php echo $this->lang->line('search_placeholder') ?: 'Visit no, token, OPD ID, UID, name, mobile, PBHP, billing ref'; ?>">
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label form-label-sm"><?php echo $this->lang->line('filter_level'); ?></label>
                <select name="level" id="filter_level" class="form-select">
                    <option value=""><?php echo $this->lang->line('all_levels'); ?></option>
                    <?php foreach ($levels as $l) { ?>
                    <option value="<?php echo $l->id; ?>" <?php if ((string)$filter_level === (string)$l->id) { echo 'selected'; } ?>>
                        <?php echo $l->code . ' — ' . $l->title; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-filter"></i> <?php echo $this->lang->line('filter'); ?>
                </button>
                <?php if (($filter_level !== null && $filter_level !== '') || !empty($filter_search)) { ?>
                <a href="<?php echo site_url('admin/patientflow') . ($filter_status !== 'active' ? '?status=' . urlencode($filter_status) : ''); ?>" class="btn btn-outline-secondary btn-sm" title="Clear filter">
                    <i class="fa fa-times"></i>
                </a>
                <?php } ?>
            </div>
        </form>
    </div>

    <?php
    $tab_params = array();
    if (!empty($filter_search)) {
        $tab_params['search'] = $filter_search;
    }
    if ($filter_level !== null && $filter_level !== '') {
        $tab_params['level'] = $filter_level;
    }
    $tab_url = function ($status) use ($tab_params) {
        $p = array_merge($tab_params, array('status' => $status));
        return site_url('admin/patientflow') . '?' . http_build_query($p);
    };
    ?>
    <div class="card-body border-bottom pt-2 pb-0">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link <?php echo ($filter_status === 'all') ? 'active' : ''; ?>" href="<?php echo $tab_url('all'); ?>">All</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($filter_status === 'active') ? 'active' : ''; ?>" href="<?php echo $tab_url('active'); ?>">Active</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($filter_status === 'completed') ? 'active' : ''; ?>" href="<?php echo $tab_url('completed'); ?>">Completed</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($filter_status === 'cancelled') ? 'active' : ''; ?>" href="<?php echo $tab_url('cancelled'); ?>">Cancelled</a></li>
        </ul>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover" id="pfBoardTable">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('visit_no'); ?></th>
                    <th><?php echo $this->lang->line('token') ?: 'Token'; ?></th>
                    <th><?php echo $this->lang->line('patient'); ?></th>
                    <th><?php echo $this->lang->line('pbhp_id'); ?></th>
                    <th class="text-center"><?php echo $this->lang->line('current_level'); ?></th>
                    <th><?php echo $this->lang->line('payment'); ?></th>
                    <th><?php echo $this->lang->line('programme_status') ?: 'Programme Status'; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('approval_status') ?: 'Approval'; ?></th>
                    <th><?php echo $this->lang->line('billing_reference') ?: 'Billing Ref'; ?></th>
                    <th class="text-center"><?php echo $this->lang->line('status') ?: 'Status'; ?></th>
                    <th class="text-end text-nowrap"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($visits as $v) { ?>
                            <tr>
                                <td class="align-middle">
                                    <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')): ?>
                                        <a href="<?php echo site_url('admin/patientflow/view/' . $v->id); ?>" class="text-decoration-none">
                                            <span class="pf-token"><?php echo $v->visit_no; ?></span>
                                        </a>
                                    <?php else: ?>
                                        <span class="pf-token"><?php echo $v->visit_no; ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($v->opd_id)) { ?>
                                    <div class="small text-muted">OPD ID: <?php echo (int) $v->opd_id; ?><?php if (!empty($v->opd_case_reference)) { ?> <span class="text-primary">(Visit #<?php echo (int) $v->opd_case_reference; ?>)</span><?php } ?></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($v->token_no) { ?>
                                    <a href="javascript:void(0)" class="pf-token-no pf-token-edit text-decoration-none"
                                       data-bs-toggle="modal" data-bs-target="#pfTokenStatusModal"
                                       data-visit="<?php echo (int) $v->id; ?>"
                                       data-status="<?php echo htmlspecialchars($v->token_status); ?>"
                                       data-paid="<?php echo ($v->payment_status === 'paid') ? '1' : '0'; ?>"
                                       data-patient="<?php echo htmlspecialchars($v->patient_name); ?>"
                                       data-token="<?php echo htmlspecialchars($v->token_no); ?>"
                                       title="<?php echo $this->lang->line('change_token_status') ?: 'Click to change token status'; ?>">
                                        <i class="fa fa-ticket text-primary"></i> <?php echo $v->token_no; ?>
                                    </a>
                                    <?php } else { ?>
                                    <a href="javascript:void(0)" class="pf-token-edit text-muted text-decoration-none"
                                       data-bs-toggle="modal" data-bs-target="#pfTokenStatusModal"
                                       data-visit="<?php echo (int) $v->id; ?>"
                                       data-status="<?php echo htmlspecialchars(!empty($v->token_status) ? $v->token_status : 'NOT_ISSUED'); ?>"
                                       data-paid="<?php echo ($v->payment_status === 'paid') ? '1' : '0'; ?>"
                                       data-patient="<?php echo htmlspecialchars($v->patient_name); ?>"
                                       data-token=""
                                       title="<?php echo $this->lang->line('change_token_status') ?: 'Click to change token status'; ?>">
                                        — <i class="fas fa-pencil-alt small"></i>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($v->token_status) && $v->token_status !== 'NOT_ISSUED') { ?>
                                    <?php
                                    $pf_token_badge_map = array(
                                        'ACTIVE'              => 'bg-success text-white',
                                        'WAITING'             => 'bg-warning text-dark',
                                        'CALLED'              => 'bg-primary text-white',
                                        'WAITING_SECOND_CALL' => 'bg-info text-white',
                                        'IN_PROCESS'          => 'bg-purple text-white',
                                        'CLOSED'              => 'bg-success-subtle text-success-emphasis',
                                        'NO_SHOW'             => 'bg-danger text-white',
                                        'SKIPPED'             => 'bg-dark text-white',
                                    );
                                    $pf_token_badge = isset($pf_token_badge_map[$v->token_status]) ? $pf_token_badge_map[$v->token_status] : 'bg-info-subtle text-info-emphasis';
                                    ?>
                                    <div class="mt-1"><span class="badge <?php echo $pf_token_badge; ?>" style="font-size:.7rem;font-weight:600;"><?php echo htmlspecialchars(str_replace('_', ' ', $v->token_status)); ?></span></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle">
                                    <?php if (!empty($v->patient_uuid)) { ?>
                                    <div class="small">
                                        <span class="">UID: <?php echo htmlspecialchars($v->patient_uuid); ?></span>
                                    </div>
                                    <?php } ?>
                                    <strong><?php echo $v->patient_name; ?></strong>
                                    <div class="small text-muted">
                                        <?php
                                        echo ($v->gender ? $v->gender . ' · ' : '') . ($v->age ? $v->age . 'Y' : '');
                                        if ($v->mobileno) { echo ' · ' . $v->mobileno; }
                                        ?>
                                    </div>
                                </td>
                                <td class="align-middle"><?php echo !empty($v->pbhp_id) ? '<span class="text-muted">' . $v->pbhp_id . '</span>' : '<span class="text-muted">—</span>'; ?></td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        <?php echo $v->level_code ? $v->level_code : '—'; ?>
                                    </span>
                                    <div class="small text-muted"><?php echo $v->level_title; ?></div>
                                </td>
                                <td class="align-middle">
                                    <?php
                                    $pf_paid = ($v->payment_status == 'paid');
                                    $pf_pay_badge_class = $pf_paid ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis';
                                    $pf_pay_label = $this->lang->line($pf_paid ? 'verified' : 'unverified');

                                    // Cash-received status shown just below payment status.
                                    $pf_cash_received = ((int) $v->cash_received === 1);
                                    $pf_cash_badge_class = $pf_cash_received ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis';
                                    $pf_cash_label = $pf_cash_received
                                        ? ($this->lang->line('cash_received') ?: 'Cash Received')
                                        : ($this->lang->line('cash_not_received') ?: 'Cash Not Received');
                                    ?>
                                        <span class="badge <?php echo $pf_pay_badge_class; ?>"><?php echo $pf_pay_label; ?></span>
                                        <div class="mt-1"><span class="badge <?php echo $pf_cash_badge_class; ?>"><?php echo $pf_cash_label; ?></span></div>
                                </td>
                                <td class="align-middle">
                                    <?php
                                    $pf_programme = !empty($v->programme_status) ? $v->programme_status : '';
                                    $pf_programme_class = $pf_programme === 'enroll' ? 'bg-success-subtle text-success-emphasis'
                                        : ($pf_programme === 'declined' ? 'bg-danger-subtle text-danger-emphasis'
                                        : ($pf_programme === 'deferred' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-secondary-subtle text-secondary-emphasis'));
                                    $pf_programme_label = $pf_programme
                                        ? ($this->lang->line('programme_' . $pf_programme) ?: ucfirst($pf_programme))
                                        : ($this->lang->line('not_set') ?: 'Not Set');
                                    ?>
                                    <span class="badge <?php echo $pf_programme_class; ?>"><?php echo $pf_programme_label; ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($v->approve_status == 'approved') { ?>
                                        <span class="badge bg-success-subtle text-success-emphasis"><i class="fa fa-check"></i> <?php echo $this->lang->line('approved') ?: 'Approved'; ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis"><?php echo $this->lang->line('unapproved') ?: 'Unapproved'; ?></span>
                                    <?php } ?>
                                </td>
                                    <?php
                                    // Ownership gate: only the role owning this visit's current
                                    // level may forward it or change its status (superadmin bypasses).
                                    $pf_owned = false;
                                    if ($pf_is_superadmin) {
                                        $pf_owned = true;
                                    } else {
                                        foreach ($pf_my_levels as $ml) {
                                            if ((int) $ml->id === (int) $v->current_level_id) { $pf_owned = true; break; }
                                        }
                                    }
                                    ?>
                                <td class="align-middle">
                                    <?php
                                    // R2 billing reference + status badges.
                                    $pf_billing_status = isset($v->billing_status) && $v->billing_status ? $v->billing_status : 'pending';
                                    $pf_billing_badges = array(
                                        'pending'   => 'bg-secondary-subtle text-secondary-emphasis',
                                        'billed'    => 'bg-warning-subtle text-warning-emphasis',
                                        'paid'      => 'bg-success-subtle text-success-emphasis',
                                        'cancelled' => 'bg-dark-subtle text-dark-emphasis',
                                    );
                                    $pf_billing_badge = isset($pf_billing_badges[$pf_billing_status]) ? $pf_billing_badges[$pf_billing_status] : $pf_billing_badges['pending'];
                                    // Ownership gate for billing management (R2 level owner / superadmin).
                                    $pf_can_manage_billing = false;
                                    if ((int) $v->level_no === 2) {
                                        if ($pf_is_superadmin) {
                                            $pf_can_manage_billing = true;
                                        } else {
                                            foreach ($pf_my_levels as $ml) {
                                                if ((int) $ml->id === (int) $v->current_level_id) { $pf_can_manage_billing = true; break; }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($v->billing_reference)) { ?>
                                    <div><span class="text-muted" style="font-family:monospace;"><?php echo htmlspecialchars($v->billing_reference); ?></span></div>
                                    <?php } else { ?>
                                    <div class="small text-muted"><?php echo $this->lang->line('not_generated') ?: 'Not generated'; ?></div>
                                    <?php } ?>
                                    <?php if (!empty($v->invoice_no)) { ?>
                                    <div class="small"><span class="text-primary" style="font-family:monospace;"><?php echo $this->lang->line('invoice_no') ?: 'Invoice No'; ?>: <?php echo htmlspecialchars($v->invoice_no); ?></span></div>
                                    <?php } ?>
                                    <div class="mt-1"><span class="badge <?php echo $pf_billing_badge; ?>"><?php echo ucfirst($pf_billing_status); ?></span></div>
                                    <?php if ($pf_can_manage_billing) { ?>
                                    <form method="post" action="<?php echo site_url('admin/patientflow/set_billing_status/' . $v->id); ?>" class="mt-1">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('billing_status') ?: 'Billing status'; ?>">
                                            <?php foreach ($billing_statuses as $pf_bs) { ?>
                                            <option value="<?php echo $pf_bs; ?>" <?php echo ($pf_bs === $pf_billing_status) ? 'selected' : ''; ?>><?php echo ucfirst($pf_bs); ?></option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?php
                                    $pf_visit_status = !empty($v->status) ? $v->status : 'active';
                                    $pf_status_badges = array(
                                        'active'    => 'bg-success-subtle text-success-emphasis',
                                        'completed' => 'bg-info-subtle text-info-emphasis',
                                        'cancelled' => 'bg-danger-subtle text-danger-emphasis',
                                    );
                                    $pf_status_badge = isset($pf_status_badges[$pf_visit_status]) ? $pf_status_badges[$pf_visit_status] : $pf_status_badges['active'];
                                    ?>
                                    <?php if ($pf_owned && (!empty($v->level_can_change_status) || !empty($pf_is_superadmin))) { ?>
                                    <form method="post" action="<?php echo site_url('admin/patientflow/set_visit_status/' . $v->id); ?>">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;display:inline-block;">
                                            <option value="active" <?php echo ($pf_visit_status === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="completed" <?php echo ($pf_visit_status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo ($pf_visit_status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                    <?php } else { ?>
                                    <span class="badge <?php echo $pf_status_badge; ?>"><?php echo ucfirst($pf_visit_status); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-end text-nowrap">
                                    <?php if (!empty($pf_can_create_pbhp) && !empty($v->level_can_create_pbhp) && $pf_programme === 'enroll' && empty($v->pbhp_id)) { ?>
                                    <a href="javascript:void(0)"
                                       class="btn btn-sm btn-outline-secondary w-100 mb-1 pf-pbhp-create" data-bs-toggle="tooltip"
                                       data-visit="<?php echo $v->id; ?>"
                                       data-patient="<?php echo htmlspecialchars($v->patient_name, ENT_QUOTES, 'UTF-8'); ?>"
                                       title="<?php echo $this->lang->line('create_pbhp_id') ?: 'Create PBHP ID'; ?>">
                                        <i class="fa fa-id-card-o"></i> &nbsp;<?php echo $this->lang->line('create_pbhp_id') ?: 'Create PBHP ID'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($pf_can_generate_token) && $pf_paid && empty($v->token_no)) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/generate_token/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-info w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('generate_token') ?: 'Generate Token'; ?>">
                                        <i class="fa fa-ticket"></i> &nbsp;<?php echo $this->lang->line('generate_token') ?: 'Generate Token'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if ($pf_is_cashier && !empty($v->level_can_change_payment) && !$pf_paid) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/mark_paid/' . $v->id); ?>"
                                       class="btn btn-sm btn-success w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('mark_as_paid') ?: 'Mark as Paid'; ?>">
                                        <i class="fa fa-money"></i> &nbsp;<?php echo $this->lang->line('mark_as_paid') ?: 'Mark as Paid'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($pf_can_cash_received) && !empty($v->level_can_cash_received)) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/toggle_cash_received/' . $v->id); ?>"
                                       class="btn btn-sm <?php echo $pf_cash_received ? 'btn-outline-secondary' : 'btn-outline-success'; ?> w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line($pf_cash_received ? 'clear_cash_received' : 'mark_cash_received') ?: ($pf_cash_received ? 'Clear Cash Received' : 'Mark Cash Received'); ?>">
                                        <i class="fa fa-money"></i> &nbsp;<?php echo $this->lang->line($pf_cash_received ? 'clear_cash_received' : 'mark_cash_received') ?: ($pf_cash_received ? 'Clear Cash Received' : 'Mark Cash Received'); ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($pf_can_programme_status) && !empty($v->level_can_change_programme_status)) { ?>
                                    <a href="javascript:void(0)" class="pf-programme-edit btn btn-sm btn-outline-info w-100 mb-1"
                                       data-visit="<?php echo (int) $v->id; ?>"
                                       data-status="<?php echo htmlspecialchars($pf_programme); ?>"
                                       data-patient="<?php echo htmlspecialchars($v->patient_name); ?>"
                                       title="<?php echo $this->lang->line('change_programme_status') ?: 'Change Programme Status'; ?>">
                                        <i class="fa fa-tasks"></i> &nbsp;<?php echo $this->lang->line('change_programme_status') ?: 'Change Programme Status'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if ($v->next_level_id && $pf_owned) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/forward/' . $v->id); ?>"
                                       class="btn btn-sm btn-primary w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('forward_to'); ?> <?php echo $v->next_title; ?>">
                                        <i class="fa fa-arrow-right"></i> &nbsp;<?php echo $this->lang->line('forward'); ?>
                                    </a>
                                    <?php } elseif (!$v->next_level_id) { ?>
                                        <!-- <span class="badge bg-success-subtle text-success-emphasis mb-1 d-block"><?php echo $this->lang->line('completed'); ?></span> -->
                                    <?php } else { ?>
                                        <span class="text-muted small d-block mb-1"><i class="fa fa-lock"></i></span>
                                    <?php } ?>
                                    <?php if ($v->prev_level_id && $pf_owned) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/backward/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-warning w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('back_to') ?: 'Send back to'; ?> <?php echo $v->prev_title ? $v->prev_title : 'previous step'; ?>">
                                        <i class="fa fa-arrow-left"></i> &nbsp;<?php echo $this->lang->line('back') ?: 'Back'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if ($v->level_can_approve && $pf_owned && $v->approve_status != 'approved') { ?>
                                    <a href="<?php echo site_url('admin/patientflow/approve/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-success w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('approve'); ?>">
                                        <i class="fa fa-check"></i> &nbsp;<?php echo $this->lang->line('approve'); ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (($v->level_code == 'L5') && $pf_owned) { ?>
                                    <!-- <a href="javascript:void(0)" class="btn btn-sm btn-primary w-100 mb-1"
                                       data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('save_opd') ?: 'Save OPD'; ?>"
                                       onclick="pfSetOpdPatient(<?php echo (int)$v->patient_id; ?>, '<?php echo addslashes((string)$v->patient_name); ?>'); holdModal('myModal');">
                                        <i class="fa fa-plus"></i> &nbsp;<?php echo $this->lang->line('save_opd') ?: 'Save OPD'; ?>
                                    </a> -->
                                    <?php } ?>
                                    <?php if (!empty($v->level_can_generate_billing_ref) && $pf_owned && $pf_can_generate_billing && empty($v->billing_reference)) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/generate_billing_reference/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-primary w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('generate_billing_ref') ?: 'Generate Billing Reference'; ?>">
                                        <i class="fa fa-barcode"></i> &nbsp;<?php echo $this->lang->line('generate_billing_ref') ?: 'Generate Billing Ref'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($v->level_can_generate_invoice) && $pf_owned && $pf_can_generate_invoice && empty($v->invoice_no)) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/generate_invoice/' . $v->id); ?>"
                                       class="btn btn-sm btn-outline-dark w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $this->lang->line('generate_invoice') ?: 'Generate Invoice'; ?>">
                                        <i class="fas fa-file-invoice"></i> &nbsp;<?php echo $this->lang->line('generate_invoice') ?: 'Generate Invoice'; ?>
                                    </a>
                                    <?php } ?>
                                    <?php if (!empty($v->billing_reference) && !empty($v->level_can_print_billing) && $pf_can_print_billing) { ?>
                                    <a href="<?php echo site_url('admin/patientflow/print_billing/' . $v->id); ?>" target="_blank"
                                       class="btn btn-sm btn-outline-secondary w-100 mb-1" data-bs-toggle="tooltip"
                                       title="<?php echo $pf_paid ? ($this->lang->line('print_invoice') ?: 'Print Invoice') : ($this->lang->line('print_billing_ref') ?: 'Print Billing Ref'); ?>">
                                        <i class="fa fa-print"></i> &nbsp;<?php echo $pf_paid ? ($this->lang->line('print_invoice') ?: 'Print Invoice') : ($this->lang->line('print_billing_ref') ?: 'Print Billing Ref'); ?>
                                    </a>
                                    <?php } ?>
                                    <button class="btn btn-sm btn-outline-info w-100" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#pfLogs<?php echo $v->id; ?>"
                                            aria-expanded="false" aria-controls="pfLogs<?php echo $v->id; ?>">
                                        <i class="fa fa-history"></i> &nbsp;<?php echo $this->lang->line('history') ?: 'History'; ?>
                                    </button>
                                     <?php
                                     $pf_ct_arr = array(
                                         'case_taking_type'        => (string) $v->case_taking_type,
                                         'case_taking_description' => (string) $v->case_taking_description,
                                     );
                                     foreach (array('ct_sex', 'ct_primary_concern', 'ct_concern_duration', 'ct_primary_system', 'ct_dominant_nature', 'ct_bod1', 'ct_fsh1', 'ct_nos1', 'ct_constitution_code', 'ct_assessment_certainty', 'ct_status', 'ct_tissue_structure', 'ct_report_info', 'ct_review_reason', 'ct_secondary_concerns', 'ct_additional_note', 'ct_previous_treatment', 'ct_programme_suggestion') as $pf_ct_n) {
                                         $pf_ct_arr[$pf_ct_n] = isset($v->$pf_ct_n) ? $v->$pf_ct_n : null;
                                     }
                                     $pf_ct_json = htmlspecialchars(json_encode($pf_ct_arr), ENT_QUOTES);
                                     // May this row's case taking be edited? Needs the
                                     // patient_flow / can_edit privilege, an existing case
                                     // taking, and either superadmin status OR ownership of
                                     // the visit's current level with the can_case_taking flag.
                                     // This mirrors the checks in save_case_taking().
                                     $pf_can_edit_ct = false;
                                     if ($this->rbac->hasPrivilege('patient_flow', 'can_edit')
                                         && !empty($v->case_taking_type)) {
                                         if (!empty($pf_is_superadmin)) {
                                             $pf_can_edit_ct = true;
                                         } elseif ($pf_owned && !empty($v->level_can_case_taking)) {
                                             $pf_can_edit_ct = true;
                                         }
                                     }
                                     ?>
                                     <?php if ($pf_owned && !empty($v->level_can_case_taking)) { ?>
                                     <button type="button"
                                            class="btn btn-sm btn-outline-success w-100 mb-1 pf-case-btn"
                                            data-bs-toggle="tooltip"
                                            data-visit="<?php echo (int) $v->id; ?>"
                                            data-patient-id="<?php echo (int) $v->patient_id; ?>"
                                            data-patient="<?php echo addslashes((string) $v->patient_name); ?>"
                                            data-patient-uuid="<?php echo addslashes((string) $v->patient_uuid); ?>"
                                            data-ct="<?php echo $pf_ct_json; ?>"
                                            title="<?php echo $this->lang->line('case_taking') ?: 'Case Taking'; ?>">
                                         <i class="fa fa-stethoscope"></i> &nbsp;<?php echo $this->lang->line('case_taking') ?: 'Case Taking'; ?>
                                     </button>
                                     <?php } ?>
                                     <?php if (!empty($v->case_taking_type) && (!empty($v->level_can_view_case_taking) || !empty($pf_is_superadmin))) { ?>
                                     <button type="button"
                                            class="btn btn-sm btn-outline-info w-100 mb-1 pf-case-view-btn"
                                            data-bs-toggle="tooltip"
                                            data-visit="<?php echo (int) $v->id; ?>"
                                            data-patient-uuid="<?php echo addslashes((string) $v->patient_uuid); ?>"
                                            data-patient="<?php echo addslashes((string) $v->patient_name); ?>"
                                            data-ct="<?php echo $pf_ct_json; ?>"
                                            data-by="<?php echo addslashes(trim(((string) $v->case_taking_by_name) . ' ' . (string) $v->case_taking_by_surname)); ?>"
                                            data-at="<?php echo addslashes((string) $v->case_taking_at); ?>"
                                             data-can-edit="<?php echo $pf_can_edit_ct ? '1' : '0'; ?>"

                                            title="<?php echo $this->lang->line('view_case_taking') ?: 'View Case Taking'; ?>">
                                         <i class="fa fa-eye"></i> &nbsp;<?php echo $this->lang->line('view_case_taking') ?: 'View Case Taking'; ?>
                                     </button>
                                     <?php } ?>
                                     <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_delete')) { ?>
                                     <form method="post" action="<?php echo site_url('admin/patientflow/delete_visit/' . $v->id); ?>" class="mt-1" onsubmit="return confirm('Delete this patient flow visit? This cannot be undone.');">
                                         <button type="submit" class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?: 'Delete'; ?>">
                                             <i class="fa fa-trash"></i> &nbsp;<?php echo $this->lang->line('delete') ?: 'Delete'; ?>
                                         </button>
                                     </form>
                                     <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" class="p-0 border-0" style="padding: 0px !important;">
                                    <div class="collapse" id="pfLogs<?php echo $v->id; ?>">
                                        <div class="p-3 bg-body-tertiary border-top small">
                                            <strong><?php echo $this->lang->line('visit_history') ?: 'Visit history'; ?></strong>
                                            <?php
                                            $logs = isset($visit_logs[$v->id]) ? $visit_logs[$v->id] : array();
                                            if (!$logs) { echo '<div class="text-muted mt-1">' . ($this->lang->line('no_logs_yet') ?: 'No steps yet.') . '</div>'; }
                                            else { ?>
                                            <ul class="list-unstyled mb-0 mt-2">
                                                <?php foreach ($logs as $lg) { ?>
                                                <li class="d-flex align-items-center gap-2 mb-1">
                                                    <i class="fa fa-circle text-success" style="font-size:8px;"></i>
                                                    <span class="text-muted text-nowrap"><?php echo date('d-M Y H:i', strtotime($lg->created_at)); ?></span>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis text-uppercase"><?php echo str_replace('_', ' ', htmlspecialchars($lg->action)); ?></span>
                                                    <?php if ($lg->from_code || $lg->to_code) { ?>
                                                    <span>
                                                        <?php echo $lg->from_code ? $lg->from_code : '—'; ?>
                                                        <i class="fa fa-long-arrow-right text-muted"></i>
                                                        <?php echo $lg->to_code ? $lg->to_code : '—'; ?>
                                                    </span>
                                                    <?php } ?>
                                                    <?php if ($lg->acted_by_name) { ?>
                                                    <span class="text-muted">· <?php echo htmlspecialchars($lg->acted_by_name); ?></span>
                                                    <?php } ?>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($visits)) { ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4"><?php echo $this->lang->line('no_patients_registered'); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div><div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('new_visit'); ?></h5>
                    <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax sh-inp-200" <?php
                            if ($disable_option == true) { echo "disabled"; }
                            ?> name='' id="addpatient_id">
                    </select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                        <a onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('new_patient'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                                <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                                <input name="email" id="pemail" type="hidden" class="form-control" />
                                <input name="mobileno" id="mobnumber" type="hidden" class="form-control" />
                                <input name="patient_name" id="patientname" type="hidden" class="form-control" />
                                <input name="password" id="password" type="hidden" class="form-control" />
                                <input name="organisation_id" id="organisation_id" type="hidden" class="form-control" />
                                <input name="insurance_validity" id="insurance_validity" type="hidden" class="form-control" />
                                <input name="consultant_doctor" type="hidden" value="<?php echo (int) $pf_default_doctor_id; ?>" />
                                <input name="payment_mode" type="hidden" value="Cash" />
                                <input name="insurance_id" id="insuranceid" type="hidden" class="form-control" />
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="sh-form-card mb-2" id="patientDetails" class="d-none">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title" id="listname"></span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <div class="sh-info-grid flex-grow-1">
                                                <div class="row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_guardian">
                                                        <small class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></small>
                                                        <span class="sh-info-value" id="guardian"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_gender">
                                                        <small class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></small>
                                                        <span class="sh-info-value" id="genders"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_blood">
                                                        <small class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></small>
                                                        <span class="sh-info-value" id="blood_group"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_marital">
                                                        <small class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></small>
                                                        <span class="sh-info-value" id="marital_status"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_age">
                                                        <small class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></small>
                                                        <span class="sh-info-value" id="age"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_phone">
                                                        <small class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></small>
                                                        <span class="sh-info-value" id="listnumber"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_email">
                                                        <small class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></small>
                                                        <span class="sh-info-value" id="email"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_address">
                                                        <small class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></small>
                                                        <span class="sh-info-value" id="address"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa">
                                                        <small class="sh-info-label"><i class="fas fa-shield-alt"></i> <?php echo $this->lang->line('tpa'); ?></small>
                                                        <span class="sh-info-value" id="organisation_name"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa_id">
                                                        <small class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></small>
                                                        <span class="sh-info-value" id="insurance_id"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_validity">
                                                        <small class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></small>
                                                        <span class="sh-info-value" id="validity"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_national_id">
                                                        <small class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></small>
                                                        <span class="sh-info-value" id="identification_number"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_allergies">
                                                        <small class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></small>
                                                        <span class="sh-info-value" id="allergies"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_note">
                                                        <small class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></small>
                                                        <span class="sh-info-value" id="note"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_barcode">
                                                        <small class="sh-info-label"><i class="fa fa-barcode"></i> <?php echo $this->lang->line('barcode'); ?></small>
                                                        <span><img id="getbarcode" class="pd-barcode-img" alt="" /></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_qrcode">
                                                        <small class="sh-info-label"><i class="fa fa-qrcode"></i> <?php echo $this->lang->line('qrcode'); ?></small>
                                                        <span><img id="getqrcode" class="pd-qrcode-img" alt="" /></span>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="opd-pd-photo-cell">
                                                    <img class="opd-pd-photo-lg" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" id="image" alt="<?php echo $this->lang->line('patient'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sh-form-card mb-2">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type') ; ?></label>
                                                    <div>
                                                        <select  name='symptoms_type'  id="act"  class="form-control select2 act w-100"   multiple >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                            <input name="rows[]" type="hidden" value="1">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li class="section-placeholder"><span><?php echo $this->lang->line('select') ; ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="esymptoms" name="symptoms" rows="3" ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_group'); ?></label>
                                                    <select id="opd_icd10_group_filter" class="form-control select2 w-100" >
                                                        <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                                        <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                        <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_diagnosis'); ?></label>
                                                    <select name="icd_code_ids[]" id="opd_icd_code_ids" class="form-control select2 w-100" multiple >
                                                        <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies"  rows="3" id="eknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <?php
                                                    echo display_custom_fields('opd');
                                                    ?>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-8-->
                                    <div class="col-lg-4 col-md-4 col-sm-4 ptt10">
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('visit_details'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?> <small class="req"> *</small></label>
                                                    <input id="datetimepicker" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name='case' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                        <select name="casualty" id="casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>                                                        
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div>
                                                        <input class="form-control" type='text' name='refference' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="d-block">&nbsp;</label>
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input apply_tpa" id="is_tpa" name="is_tpa" type="checkbox" value="1" autocomplete="off">
                                                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('consultant_doctor'); ?> &amp; <?php echo $this->lang->line('charges'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
												    <select name="charge_category" class="w-100 form-control charge_category select2">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($charge_category as $key => $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>">
                                                        <?php echo $value['name']; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>                                           
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge'); ?> <small class="req"> *</small></label>
                                                    <select name="charge_id" class="w-100 form-control charge select2">
                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                    </select>
                                                    <input type="hidden" class="form-control right-border-none" name="org_charge_amount" id="org_charge_amount" readonly autocomplete="off">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="amount" id="apply_charge" class="form-control apply_charge">    
                                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value='0' autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="apply_amount" readonly id="apply_amount" class="form-control">
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-4-->
                                </div><!--./row-->
                </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                <button type="submit" id="formaddbtn" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
            </div>
        </form>                
    </div>
<script type="text/javascript">
/* ===== Patientflow: OPD "New Visit" modal glue (mirrors admin/patient/search.php) ===== */

/* Open the OPD form as a FRESH form (no patient pre-selected) from the header button. */
function pfOpenOpdFresh() {
    var m = $('#myModal');
    m.removeAttr('data-pf-pid').removeAttr('data-pf-pname');

    // Reset any half-filled state from a previously cancelled open.
    $('#formadd').find('input:text, input:password, input:file, textarea').val('');
    $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
    $('#formadd').find('select option:selected').removeAttr('selected');
    $('#formadd').find('select[name="payment_mode"]').val('Cash').trigger('change');
    $('#formadd #patient_id').val('');
    $('#patientDetails').addClass('d-none');
    m.find('.charge_category').val('').trigger('change.select2');
    m.find('.charge').val('').empty().trigger('change');
    m.find('#apply_charge, #apply_amount').val('');
    m.find('.paid_amount').val('');

    holdModal('myModal');
}

function pfSetOpdPatient(pid, pname) {
    var m = $('#myModal');
    m.attr('data-pf-pid', pid).attr('data-pf-pname', pname);
}

// Initialize the modal's static select2 fields on page load (mirrors search.php).
$(function () {
    $('#myModal .charge_category, #myModal .charge, #myModal #consultant_doctor').select2({
        dropdownParent: $('#myModal')
    });
});

function holdModal(modalId) {
    (function () {
        var _el = document.getElementById(modalId);
        if (!_el) return;
        if (_el.parentNode !== document.body) { document.body.appendChild(_el); }
        bootstrap.Modal.getOrCreateInstance(_el, { backdrop: 'static', keyboard: false }).show();
    })();
}

function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function get_PatientDetails(id) {
    var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";
    $("#ajax_load").html("<center><img src='" + base_url + "'/></center>");
    var password = makeid(5);
    if (id == '') {
        $("#ajax_load").html("");
        $("#patientDetails").addClass('d-none');
    } else {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass('d-none');
                    $('#patient_unique_id').text(res.id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#listname').text(res.patient_name + " (" + res.id + ")");
                    $('#guardian').text(res.guardian_name);
                    $('#listnumber').text(res.mobileno);
                    $('#email').text(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    if (res.gender == 'Female') {
<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                        $("#antenatal_div").removeClass('d-none');
<?php } ?>
                    }
                    $('#patientname').val(res.patient_name);
                    $('#doctname').val(res.name + " " + res.surname);
                    $('#consultant_doctor').val(null).trigger('change');
                    $('.charge_category').val(null).trigger('change');
                    $('.charge').val(null).empty().trigger('change');
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
                }
            }
        });
    }
}
function reset_form(_modal_div) {
    _modal_div.find('.charge_category').val('').trigger('change.select2');
    _modal_div.find('.charge ').val('').empty().trigger('change');
    _modal_div.find('#percentage').val("");
    _modal_div.find('#apply_charge').val("");
    _modal_div.find('#standard_charge').val("");
    _modal_div.find('#schedule_charge').val("");
    _modal_div.find('#org_id').val("");
    _modal_div.find('#org_charge_amount').val("");
    _modal_div.find('#apply_amount').val("");
    _modal_div.find('#paid_amount').val("");
    _modal_div.find('#discount_percentage').val("");
}

function update_amount(object_model) {
    var apply_charge = object_model.find('.apply_charge').val();
    var discount_percentage = object_model.find('#discount_percentage').val();
    var price_with_discount = (parseFloat(apply_charge)) - (parseFloat(apply_charge) * discount_percentage / 100);
    var tax_percentage = object_model.find('#percentage').val();
    var apply_amount = (parseFloat(price_with_discount) * tax_percentage / 100) + price_with_discount;
    object_model.find('#apply_amount').val(apply_amount.toFixed(2));
    object_model.find('.paid_amount').val(apply_amount.toFixed(2));
}

function getchargecode(charge_category, charge_id) {
    var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
    if (charge_category != "") {
        $.ajax({
            url: base_url + 'admin/charges/getchargeDetails',
            type: "POST",
            data: { charge_category: charge_category },
            dataType: 'json',
            success: function (res) {
                div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
                $.each(res, function (i, obj) {
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
            }
        });
    }
}

// Reinitialize the patient AJAX Select2 after the modal becomes visible.
$('#myModal').off('shown.bs.modal.select2init').on('shown.bs.modal.select2init', function () {
    var $sel = $('#addpatient_id');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({
        ajax: {
            url: "<?php echo base_url(); ?>admin/patient/getPatientListAjax",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { searchTerm: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            cache: false
        },
        dropdownParent: $('#myModal')
    });
    $sel.focus();
});

// Pre-select the visit's patient in the OPD modal.
$('#myModal').off('shown.bs.modal.pfprefill').on('shown.bs.modal.pfprefill', function () {
    var m = $(this);
    var pid = m.attr('data-pf-pid');
    var pname = m.attr('data-pf-pname');
    m.removeAttr('data-pf-pid').removeAttr('data-pf-pname');
    if (pid && pname) {
        var $sel = $('#addpatient_id');
        $sel.empty().append(new Option(pname + ' (' + pid + ')', pid, true, true));
        $sel.val(pid);
        $sel.trigger('change');
    }
});
$(document).on('change', '#addpatient_id', function () {
    var div = $(this).closest('div.modal');
    $('#is_tpa').prop('checked', false);
    reset_form(div);
});

$(document).on('change', '#is_tpa', function () {
    var div = $(this).closest('div.modal');
    div.find('.charge_category').val(null).trigger('change');
    div.find('.charge').val(null).empty().trigger('change');
    div.find('#percentage,#apply_charge,#standard_charge,#schedule_charge,#org_id,#org_charge_amount,#apply_amount,#paid_amount,#discount_percentage').val('');
    div.find('select[name="payment_mode"]').val('Cash').trigger('change');
    div.find('select[name="payment_mode"] option[value="Cash"]').prop('selected', true);
});

$(document).on('select2:select', '.charge_category', function () {
    var charge_category = $(this).val();
    $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
    $('#percentage').val("");
    $('#apply_charge').val("");
    $('#standard_charge').val("");
    $('#schedule_charge').val("");
    $('#org_id').val("");
    $('#org_charge_amount').val("");
    $('#apply_amount').val("");
    $('#paid_amount').val("");
    $('#discount_percentage').val("");
    getchargecode(charge_category, "");
});

$(document).on('select2:select', '.charge', function () {
    var div = $(this).closest('div.modal');
    var charge = $(this).val();
    var patient_id = $("#patient_id").val();
    var apply_amount = 0;
    $('#org_charge_amount').val('');
    $('#discount_percentage').val('');
    var is_tpa = $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
    if (charge == '') {
        reset_form(div);
        return false;
    }
    $.ajax({
        url: '<?php echo base_url(); ?>admin/patient/getChargeById',
        type: "POST",
        data: { charge_id: charge, 'patient_id': patient_id, 'is_tpa': is_tpa },
        dataType: 'json',
        success: function (res) {
            if (res.status == 0) {
                errorMsg(res.msg);
            } else if (res.status == 2) {
                errorMsg(res.msg);
            }
            var tax = res.result.percentage;
            var quantity = div.find('#qty').val();
            div.find('#percentage').val(tax);
            div.find('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
            div.find('#standard_charge').val(res.result.standard_charge);
            div.find('#schedule_charge').val(res.result.org_charge);
            div.find('#org_id').val(res.result.org_charge_id);
            if (res.display_tpa_charge) {
                if (res.result.percentage == null) {
                    apply_amount = parseFloat(res.result.org_charge);
                } else {
                    apply_amount = (parseFloat(res.result.org_charge) * res.result.percentage / 100) + (parseFloat(res.result.org_charge));
                }
                div.find('#org_charge_amount').val(res.result.org_charge);
                div.find('#apply_charge').val(res.result.org_charge);
                div.find('#apply_amount').val(apply_amount.toFixed(2));
                div.find('.paid_amount').val(apply_amount.toFixed(2));
            } else {
                if (res.result.percentage == null) {
                    apply_amount = parseFloat(res.result.standard_charge);
                } else {
                    apply_amount = (parseFloat(res.result.standard_charge) * res.result.percentage / 100) + (parseFloat(res.result.standard_charge));
                }
                div.find('#standard_charge').val(res.result.standard_charge);
                div.find('#apply_charge').val(res.result.standard_charge);
                div.find('#apply_amount').val(apply_amount.toFixed(2));
                div.find('.paid_amount').val(apply_amount.toFixed(2));
            }
        }
    });
});

$(document).on('input paste keyup', '.apply_charge,.discount_percentage', function (e) {
    update_amount($(e.target).closest('div.modal'));
});
// Submit: create the OPD via the existing admin/patient endpoint, then reload the board.
$("#formadd").on('submit', (function (e) {
    e.preventDefault();
    var amount_to_be_paid = parseInt($("form#formadd #apply_amount").val());
    var amount_paying = parseInt($("form#formadd #paid_amount").val());
    if (amount_to_be_paid < amount_paying) {
        errorMsg("Invalid Amount");
        return false;
    }
    var $btn = $("button[type=submit][clicked=true]", "#formadd");
    if ($btn.length) { $btn.attr('disabled', true); }
    $.ajax({
        url: '<?php echo base_url(); ?>admin/patient',
        type: "POST",
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if ($btn.length) { $btn.attr('disabled', false); }
            if (data.status == "fail") {
                var message = "";
                $.each(data.error, function (index, value) { message += value; });
                errorMsg(message);
            } else {
                successMsg(data.message);
                var mEl = document.getElementById('myModal');
                if (mEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(mEl).hide();
                }

                // Enroll the patient into the patient flow at the first level (R1 only).
                var pf_pid = $("#formadd #patient_id").val();
                var pf_amt = $("#formadd #apply_amount").val();
                if (pf_pid) {
                    $.post('<?php echo base_url(); ?>admin/patientflow/enroll_from_opd',
                        { patient_id: pf_pid, amount: pf_amt },
                        function () { window.location.reload(); }
                    ).fail(function () { window.location.reload(); });
                } else {
                    window.location.reload();
                }
            }
        },
        error: function (xhr) {
            if ($btn.length) { $btn.attr('disabled', false); }
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        }
    });
}));

$("form#formadd button[type=submit]").click(function () {
    $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
    $(this).attr("clicked", "true");
});

$('#myModal').on('hidden.bs.modal', function () {
    $('#patientDetails').addClass('d-none');
    $('#formadd').find('input:text, input:password, input:file, textarea').val('');
    $('#formadd').find('select option:selected').removeAttr('selected');
    $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
    $('#formadd').find('select[name="payment_mode"]').val('Cash').trigger('change');
});
</script>

<!-- ===== Patientflow: New Patient modal (flow-specific partial, submits to the patient flow controller) ===== -->
<?php $this->load->view('admin/patientflow/patientaddmodal'); ?>

<!-- ===== Patientflow: Token generated popup ===== -->
<div class="modal fade" id="pfTokenModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content text-center">
            <div class="modal-header bg-success text-white justify-content-center">
                <h5 class="modal-title" style="padding-top: 12px; padding-bottom: 0px;">
                    <i class="fas fa-ticket-alt"></i> <?php echo $this->lang->line('token') ?: 'Token'; ?>
                </h5>
            </div>
            <div class="modal-body">
                <?php $pf_flash_token = $this->session->flashdata('token_no'); ?>
                <?php $pf_flash_token_patient = $this->session->flashdata('token_patient'); ?>
                <p class="mb-1 text-muted"><?php echo htmlspecialchars((string) $pf_flash_token_patient); ?></p>
                <h1 class="display-4 fw-bold text-primary mb-0" id="pf_token_no"><?php echo htmlspecialchars((string) $pf_flash_token); ?></h1>
                <p class="mb-0 text-muted small"><?php echo $this->lang->line('token_generated_hint') ?: 'Token generated - please proceed to the next counter.'; ?></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('close') ?: 'Close'; ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($pf_flash_token)) { ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    holdModal('pfTokenModal');
});
</script>
<?php } ?>

<!-- ===== Patientflow: Change Token Status modal (opened by clicking a token no) ===== -->
<div class="modal fade" id="pfTokenStatusModal" tabindex="-1" role="dialog" aria-labelledby="pfTokenStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pfTokenStatusModalLabel" style="padding-top: 12px; padding-bottom: 0px;">
                    <i class="fa fa-ticket"></i> <?php echo $this->lang->line('change_token_status') ?: 'Change Token Status'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pf_ts_visit_id" value="">
                <p class="mb-1 text-muted small" id="pf_ts_patient"></p>
                <p class="mb-2"><strong id="pf_ts_token_no"></strong></p>
                <div class="form-group" id="pf_ts_status_wrap">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('token_status'); ?> <small class="req"> *</small></label>
                    <select class="form-control" id="pf_ts_status"></select>
                </div>
                <p class="text-danger small mb-0 mt-2 d-none" id="pf_ts_notice"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('cancel') ?: 'Cancel'; ?>
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="pf_ts_save">
                    <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Allowed token status transitions (mirrors Patientflow_model::token_transitions()).
// Used only for the legacy warning notices; the selectbox itself lists every
// status (see PF_TOKEN_ALL below) regardless of the current state.
var PF_TOKEN_TRANSITIONS = {
    'NOT_ISSUED':          ['ACTIVE'],
    'ACTIVE':              ['WAITING'],
    'WAITING':             ['CALLED', 'NO_SHOW', 'SKIPPED'],
    'CALLED':              ['IN_PROCESS', 'NO_SHOW'],
    'WAITING_SECOND_CALL': ['CALLED', 'NO_SHOW'],
    'IN_PROCESS':          ['CLOSED'],
    'NO_SHOW':             ['WAITING'],
    'SKIPPED':             ['WAITING'],
    'CLOSED':              []
};

var PF_TOKEN_LABELS = {
    'NOT_ISSUED': 'Not Issued', 'ACTIVE': 'Activate', 'WAITING': 'Waiting',
    'CALLED': 'Called', 'IN_PROCESS': 'In Process', 'CLOSED': 'Closed',
    'NO_SHOW': 'No Show', 'SKIPPED': 'Skipped', 'WAITING_SECOND_CALL': 'Waiting Second Call'
};

// All token statuses, shown at once in the modal's selectbox (regardless of
// the current status / allowed transitions). The server still validates the
// transition when saving.
var PF_TOKEN_ALL = ['NOT_ISSUED', 'ACTIVE', 'WAITING', 'CALLED', 'IN_PROCESS', 'CLOSED', 'NO_SHOW', 'SKIPPED', 'WAITING_SECOND_CALL'];

// Fill the modal as the click lands; Bootstrap's data-bs-toggle="modal"
// data-bs-target="#pfTokenStatusModal" on the anchor then opens it natively.
$(document).on('click', '.pf-token-edit', function () {
    var status   = $(this).data('status') || 'NOT_ISSUED';
    var isPaid   = ($(this).data('paid') == 1);
    var token    = $(this).data('token') || '';

    // Relocate the modal to document.body (same as holdModal) so it cannot be
    // clipped by .table-responsive / card overflow.
    var $modal = $('#pfTokenStatusModal');
    if ($modal.parent()[0] && $modal.parent()[0] !== document.body) { $modal.appendTo('body'); }

    $('#pf_ts_visit_id').val($(this).data('visit'));
    $('#pf_ts_patient').text($(this).data('patient') || '');
    $('#pf_ts_token_no').text(token ? token : '<?php echo $this->lang->line('token_not_issued') ?: 'No token issued yet'; ?>');
    $('#pf_ts_notice').addClass('d-none').text('');

    var $sel = $('#pf_ts_status').empty();

    if (!token) {
        // No token generated yet — the status field is not applicable.
        $('#pf_ts_status_wrap').addClass('d-none');
        $('#pf_ts_notice').removeClass('d-none').text(
            '<?php echo $this->lang->line('token_not_generated_notice') ?: 'No token has been generated for this visit yet.'; ?>'
        );
        return;
    }

    // A token exists — show the selectbox populated with ALL statuses at once.
    $('#pf_ts_status_wrap').removeClass('d-none');
    $sel.append($('<option/>', { value: '', text: '<?php echo $this->lang->line('select') ?: 'Select'; ?>' }));
    $.each(PF_TOKEN_ALL, function (i, opt) {
        $sel.append($('<option/>', { value: opt, text: PF_TOKEN_LABELS[opt] || opt, selected: (opt === status) }));
    });

    // Legacy warning notices (kept as-is) — the selectbox is still populated.
    var options = PF_TOKEN_TRANSITIONS[status] || [];
    if (!options.length) {
        $('#pf_ts_notice').removeClass('d-none').text('<?php echo $this->lang->line('token_no_options') ?: 'This token has no further status changes available.'; ?>');
    } else if (status === 'NOT_ISSUED' && !isPaid) {
        $('#pf_ts_notice').removeClass('d-none').text('<?php echo $this->lang->line('token_payment_required') ?: 'Payment not confirmed - the token can only be activated after the payment is marked as paid.'; ?>');
    }
});

$(document).on('click', '#pf_ts_save', function () {
    var visitId = $('#pf_ts_visit_id').val();
    var status  = $('#pf_ts_status').val();
    if (!visitId || !status) {
        errorMsg('<?php echo $this->lang->line('select_token_status_first') ?: 'Please select a token status first.'; ?>');
        return;
    }

    var $btn = $(this).prop('disabled', true);
    $.post('<?php echo site_url('admin/patientflow/set_token_status'); ?>',
        { visit_id: visitId, status: status },
        function (response) {
            $btn.prop('disabled', false);
            if (response.status) {
                successMsg(response.message);
                var modalEl = document.getElementById('pfTokenStatusModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                errorMsg(response.message);
            }
        }, 'json'
    ).fail(function () {
        $btn.prop('disabled', false);
        errorMsg('<?php echo $this->lang->line('token_action_failed') ?: 'An error occurred while updating the token status.'; ?>');
    });
});
</script>

<!-- ===== Patientflow: Programme Status modal ===== -->
<div class="modal fade" id="pfProgrammeModal" tabindex="-1" role="dialog" aria-labelledby="pfProgrammeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="pfProgrammeModalLabel" style="padding-top: 12px; padding-bottom: 0px;">
                    <i class="fa fa-tasks"></i> <?php echo $this->lang->line('change_programme_status') ?: 'Change Programme Status'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pf_pg_visit_id" value="">
                <p class="mb-2 text-muted small" id="pf_pg_patient"></p>
                <div class="form-group">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('programme_status') ?: 'Programme Status'; ?> <small class="req"> *</small></label>
                    <select class="form-control" id="pf_pg_status">
                        <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                        <?php foreach ($programme_statuses as $pg_status) { ?>
                        <option value="<?php echo $pg_status; ?>"><?php echo $this->lang->line('programme_' . $pg_status) ?: ucfirst($pg_status); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('cancel') ?: 'Cancel'; ?>
                </button>
                <button type="button" class="btn btn-success btn-sm" id="pf_pg_save">
                    <i class="fa fa-check"></i> <?php echo $this->lang->line('save') ?: 'Save'; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fill + open the Programme Status modal the same way as the Change Token
// Status popup: relocate it to document.body first (so it cannot be clipped
// by .table-responsive / card overflow), then show it via JS.
$(document).on('click', '.pf-programme-edit', function () {
    var $modal = $('#pfProgrammeModal');
    if ($modal.parent()[0] && $modal.parent()[0] !== document.body) { $modal.appendTo('body'); }

    $('#pf_pg_visit_id').val($(this).data('visit') || '');
    $('#pf_pg_patient').text($(this).data('patient') || '');
    $('#pf_pg_status').val($(this).data('status') || '');

    var modalEl = document.getElementById('pfProgrammeModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

$(document).on('click', '#pf_pg_save', function () {
    var visitId = $('#pf_pg_visit_id').val();
    var status  = $('#pf_pg_status').val();
    if (!visitId || !status) {
        errorMsg('<?php echo $this->lang->line('select_token_status_first') ?: 'Please select a status first.'; ?>');
        return;
    }

    var $btn = $(this).prop('disabled', true);
    $.post('<?php echo site_url('admin/patientflow/change_programme_status'); ?>/' + visitId,
        { programme_status: status },
        function (response) {
            $btn.prop('disabled', false);
            if (response.status) {
                successMsg(response.message);
                var modalEl = document.getElementById('pfProgrammeModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                errorMsg(response.message);
            }
        }, 'json'
    ).fail(function () {
        $btn.prop('disabled', false);
        errorMsg('<?php echo $this->lang->line('pbhp_action_failed') ?: 'An error occurred while updating the programme status.'; ?>');
    });
});
</script>

<div class="modal fade" id="pfPbhpModal" tabindex="-1" role="dialog" aria-labelledby="pfPbhpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="pfPbhpModalLabel" style="padding-top: 12px; padding-bottom: 0px;">
                    <i class="fa fa-id-card-o"></i> <?php echo $this->lang->line('create_pbhp_id') ?: 'Create PBHP ID'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pf_pbhp_visit_id" value="">
                <p class="mb-2 text-muted small" id="pf_pbhp_patient"></p>
                <div class="form-group">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('pbhp_code') ?: 'PBHP Code'; ?> <small class="req"> *</small></label>
                    <select class="form-control" id="pf_pbhp_code">
                        <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                        <?php foreach ($pbhp_codes as $pbhp_label => $pbhp_code) { ?>
                        <option value="<?php echo $pbhp_code; ?>"><?php echo $pbhp_label . ' (' . $pbhp_code . ')'; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('cancel') ?: 'Cancel'; ?>
                </button>
                <button type="button" class="btn btn-success btn-sm" id="pf_pbhp_save">
                    <i class="fa fa-check"></i> <?php echo $this->lang->line('create_pbhp_id') ?: 'Create PBHP ID'; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Fill the PBHP modal as the click lands; Bootstrap's data-bs-toggle="modal"
// data-bs-target="#pfPbhpModal" on the anchor then opens it natively.
$(document).on('click', '.pf-pbhp-create', function () {
    var $modal = $('#pfPbhpModal');
    if ($modal.parent()[0] && $modal.parent()[0] !== document.body) { $modal.appendTo('body'); }

    $('#pf_pbhp_visit_id').val($(this).data('visit'));
    $('#pf_pbhp_patient').text($(this).data('patient') || '');
    $('#pf_pbhp_code').val('');

    var modalEl = document.getElementById('pfPbhpModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

$(document).on('click', '#pf_pbhp_save', function () {
    var visitId = $('#pf_pbhp_visit_id').val();
    var code    = $('#pf_pbhp_code').val();
    if (!visitId || !code) {
        errorMsg('<?php echo $this->lang->line('select_pbhp_code_first') ?: 'Please select a PBHP code first.'; ?>');
        return;
    }

    var $btn = $(this).prop('disabled', true);
    $.post('<?php echo site_url('admin/patientflow/create_pbhp'); ?>',
        { visit_id: visitId, pbhp_code: code },
        function (response) {
            $btn.prop('disabled', false);
            if (response.status) {
                successMsg(response.message + ' (' + response.pbhp_id + ')');
                var modalEl = document.getElementById('pfPbhpModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                errorMsg(response.message);
            }
        }, 'json'
    ).fail(function () {
        $btn.prop('disabled', false);
        errorMsg('<?php echo $this->lang->line('pbhp_action_failed') ?: 'An error occurred while creating the PBHP ID.'; ?>');
    });
});
</script>



<!-- ===== Patientflow: Case Taking modal (add/edit) ===== -->
<?php
// Constitution master options for the BOD/FSH/NOS slot selects + id->label
// maps used by the view modal. Empty-safe when masters have no rows yet.
$pf_ct_slots = array(
    'bod' => array('label' => 'Body Type', 'items' => isset($body_builds) ? $body_builds : array()),
    'fsh' => array('label' => 'Face Type', 'items' => isset($face_shapes) ? $face_shapes : array()),
    'nos' => array('label' => 'Nose Type', 'items' => isset($nose_types) ? $nose_types : array()),
);
$pf_lk = array();
foreach ($pf_ct_slots as $pf_key => $pf_slot) {
    $pf_lk[$pf_key] = array();
    foreach ($pf_slot['items'] as $pf_it) {
        $pf_lk[$pf_key][(string) (int) $pf_it->id] = $pf_it->code . ' — ' . $pf_it->name;
    }
}
?>
<!-- ===== Patientflow: Case Taking (add / edit) modal ===== -->
<div class="modal fade sh-modal sh-modal-nospace" id="pfCaseModal" tabindex="-1" aria-labelledby="pfCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="pf_ct_form" accept-charset="utf-8" method="post" onsubmit="return false;">

                <div class="modal-header">
                    <h5 class="modal-title" id="pfCaseModalLabel">
                        <i class="fa fa-stethoscope"></i> <span id="pf_ct_title_text"><?php echo $this->lang->line('case_taking') ?: 'Case Taking'; ?></span>
                    </h5>
                    <!-- <span class="sh-pf-meta" id="pf_ct_patient_name"></span> -->
                    <!-- <span class="sh-pf-meta" id="pf_ct_uuid"></span> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Scroll area intentionally wraps header+body only; footer stays pinned -->
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- ================= LEFT COLUMN (4/12) ================= -->
                            <div class="col-lg-4 col-md-4">
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-user"></i> <?php echo $this->lang->line('patient_information') ?: 'Patient Information'; ?></span>
                                    </div>
                                    <div class="sh-card-body">
                                        <div class="pf-ct-patient-head">
                                            <img id="pf_ct_pi_image" class="pf-ct-patient-photo" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" alt="<?php echo $this->lang->line('patient'); ?>">
                                            <div class="pf-ct-patient-id">
                                                <div class="pf-ct-patient-name" id="pf_ct_pi_name">&mdash;</div>
                                                <div class="pf-ct-patient-sub" id="pf_ct_pi_uid"></div>
                                            </div>
                                        </div>
                                        <div class="sh-info-grid pf-ct-info-grid">
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('age'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_age">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-birthday-cake"></i> <?php echo $this->lang->line('date_of_birth'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_dob">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_gender">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_marital">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_blood_group">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-user-friends"></i> <?php echo $this->lang->line('guardian_name'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_guardian">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-phone"></i> <?php echo $this->lang->line('phone'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_mobile">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-envelope"></i> <?php echo $this->lang->line('email'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_email">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-id-card"></i> <?php echo $this->lang->line('identification_number'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_idno">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item pf-ct-info-wide">
                                                <span class="sh-info-label"><i class="fas fa-map-marker-alt"></i> <?php echo $this->lang->line('address'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_address">&mdash;</span>
                                            </div>
                                            <div class="sh-info-item pf-ct-info-wide">
                                                <span class="sh-info-label"><i class="fas fa-heartbeat"></i> <?php echo $this->lang->line('known_allergies'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_allergies">&mdash;</span>
                                            </div>
                                            <!-- <div class="sh-info-item pf-ct-info-wide">
                                                <span class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('note'); ?></span>
                                                <span class="sh-info-value" id="pf_ct_pi_note">&mdash;</span>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.col-lg-4 -->

                            <!-- ================= RIGHT COLUMN (8/12) ================= -->
                            <div class="col-lg-8 col-md-8">

                                <input type="hidden" id="pf_ct_visit_id" value="">
                                <input type="hidden" id="pf_ct_is_edit" value="0">

                                <!-- Primary Concern & History -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-stethoscope"></i> Primary Concern &amp; History</span>
                                    </div>
                                    <div class="sh-card-body">
                                        <div class="row g-2">
                                            <div class="col-md-3"><label class="form-label form-label-sm">Sex <small class="req"> *</small></label>
                                                <select class="form-select" id="pf_ct_sex">
                                                    <option value="">Select</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select></div>
                                            <div class="col-md-3"><label class="form-label form-label-sm">Primary concern <small class="req"> *</small></label>
                                                <input type="text" class="form-control" id="pf_ct_primary_concern" maxlength="255" placeholder="Chief complaint / primary concern"></div>
                                            <div class="col-md-3"><label class="form-label form-label-sm">Duration of concern <small class="req"> *</small></label>
                                                <input type="text" class="form-control" id="pf_ct_concern_duration" maxlength="100" placeholder="e.g. 3 months"></div>
                                            <div class="col-md-3"><label class="form-label form-label-sm">Primary system / area <small class="req"> *</small></label>
                                                <input type="text" class="form-control" id="pf_ct_primary_system" maxlength="100" placeholder="e.g. Digestive"></div>
                                            <div class="col-md-3"><label class="form-label form-label-sm">Dominant nature <small class="req"> *</small></label>
                                                <input type="text" class="form-control" id="pf_ct_dominant_nature" maxlength="100" placeholder="e.g. Acute"></div>
                                            <div class="col-md-3"><label class="form-label form-label-sm"><?php echo $this->lang->line('case_taking_type') ?: 'Case Taking Type'; ?> <small class="req"> *</small></label>
                                                <select class="form-select" id="pf_ct_type">
                                                    <option value=""><?php echo $this->lang->line('select') ?: 'Select'; ?></option>
                                                    <option value="new"><?php echo $this->lang->line('case_new') ?: 'New Case'; ?></option>
                                                    <option value="follow_up"><?php echo $this->lang->line('case_follow_up') ?: 'Follow-up'; ?></option>
                                                    <option value="referral"><?php echo $this->lang->line('case_referral') ?: 'Referral'; ?></option>
                                                    <option value="emergency"><?php echo $this->lang->line('case_emergency') ?: 'Emergency'; ?></option>
                                                    <option value="review"><?php echo $this->lang->line('case_review') ?: 'Review'; ?></option>
                                                </select></div>
                                            <div class="col-md-6"><label class="form-label form-label-sm">Tissue / structure <small class="text-muted">(when relevant to broad area)</small></label>
                                                <input type="text" class="form-control" id="pf_ct_tissue_structure" maxlength="255"></div>
                                            <div class="col-md-6"><label class="form-label form-label-sm">Report information <small class="text-muted">(when reports exist)</small></label>
                                                <textarea class="form-control" id="pf_ct_report_info" rows="2"></textarea></div>
                                            <div class="col-12"><label class="form-label form-label-sm"><?php echo $this->lang->line('description') ?: 'Description'; ?></label>
                                                <textarea class="form-control" id="pf_ct_desc" rows="2" placeholder="<?php echo $this->lang->line('case_description_placeholder') ?: 'Enter case details here...'; ?>"></textarea></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Constitution -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-user-secret"></i> Constitution <small class="fw-normal text-muted">(one body build, one face shape, one nose type)</small></span>
                                    </div>
                                    <div class="sh-card-body">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label form-label-sm mb-0">Body Type <small class="req"> *</small></label>
                                                <select class="form-select form-select-sm" id="pf_ct_bod1">
                                                    <option value="">Select</option>
                                                    <?php foreach ($pf_ct_slots['bod']['items'] as $pf_it) { ?>
                                                    <option value="<?php echo (int) $pf_it->id; ?>"><?php echo htmlspecialchars($pf_it->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-sm mb-0">Face Type <small class="req"> *</small></label>
                                                <select class="form-select form-select-sm" id="pf_ct_fsh1">
                                                    <option value="">Select</option>
                                                    <?php foreach ($pf_ct_slots['fsh']['items'] as $pf_it) { ?>
                                                    <option value="<?php echo (int) $pf_it->id; ?>"><?php echo htmlspecialchars($pf_it->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label form-label-sm mb-0">Nose Type <small class="req"> *</small></label>
                                                <select class="form-select form-select-sm" id="pf_ct_nos1">
                                                    <option value="">Select</option>
                                                    <?php foreach ($pf_ct_slots['nos']['items'] as $pf_it) { ?>
                                                    <option value="<?php echo (int) $pf_it->id; ?>"><?php echo htmlspecialchars($pf_it->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3" style="display:none;">
                                                <label class="form-label form-label-sm mb-0">Constitution Code</label>
                                                <input type="text" class="form-control" id="pf_ct_constitution_code" readonly placeholder="Auto">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assessment & Status -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-clipboard-check"></i> Assessment &amp; Status</span>
                                    </div>
                                    <div class="sh-card-body">
                                        <div class="row g-2">
                                            <div class="col-md-4"><label class="form-label form-label-sm">Assessment certainty <small class="req"> *</small></label>
                                                <select class="form-select" id="pf_ct_assessment_certainty">
                                                    <option value="">Select</option>
                                                    <option value="Certain">Certain</option>
                                                    <option value="Provisional">Provisional</option>
                                                    <option value="Uncertain">Uncertain</option>
                                                </select></div>
                                            <div class="col-md-4"><label class="form-label form-label-sm">Case status <small class="req"> *</small></label>
                                                <select class="form-select" id="pf_ct_status">
                                                    <option value="">Select</option>
                                                    <option value="Open">Open</option>
                                                    <option value="In Review">In Review</option>
                                                    <option value="Completed">Completed</option>
                                                </select></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-6"><label class="form-label form-label-sm">Review Required reason <small class="text-muted">(mandatory when unresolved / overridden)</small></label>
                                                <textarea class="form-control" id="pf_ct_review_reason" rows="2"></textarea></div>
                                            <div class="col-6"><label class="form-label form-label-sm">Secondary concerns <small class="text-muted">(recorded only — does not generate the standard set)</small></label>
                                                <textarea class="form-control" id="pf_ct_secondary_concerns" rows="2"></textarea></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Supporting Information -->
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-folder-open-o"></i> Supporting Information <small class="fw-normal text-muted">(optional)</small></span>
                                    </div>
                                    <div class="sh-card-body">
                                        <div class="row g-2">
                                            <div class="col-md-4"><label class="form-label form-label-sm">Additional note</label>
                                                <textarea class="form-control" id="pf_ct_additional_note" rows="2"></textarea></div>
                                            <div class="col-md-4"><label class="form-label form-label-sm">Previous treatment</label>
                                                <input type="text" class="form-control" id="pf_ct_previous_treatment" maxlength="255"></div>
                                            <div class="col-md-4"><label class="form-label form-label-sm">Programme suggestion</label>
                                                <input type="text" class="form-control" id="pf_ct_programme_suggestion" maxlength="255"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sh-form-card p-0">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-user"></i> Patient History</span>
                                    </div>
                                    <div class="sh-card-body p-0" id="pf_ct_history_body">
                                        <div class="text-muted small">Loading visits...</div>
                                        <table class="table table-bordered table-sm d-none" id="pf_ct_history_table">
                                            <thead>
                                                <tr>
                                                    <th style="width:16%">Visit</th>
                                                    <th style="width:16%">Level</th>
                                                    <th style="width:16%">Token</th>
                                                    <th style="width:16%">Status</th>
                                                    <th style="width:18%">Created</th>
                                                    <th style="width:18%">Billing</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pf_ct_history_rows"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <p class="text-danger small mb-0 mt-2 d-none" id="pf_ct_notice"></p>

                            </div><!-- /.col-lg-8 -->
                        </div><!-- /.row -->
                    </div><!-- /.modal-body -->
                </div><!-- /.pup-scroll-area -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?php echo $this->lang->line('cancel') ?: 'Cancel'; ?>
                    </button>
                    <button type="button" class="btn btn-info" id="pf_ct_save">
                        <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- ===== Patientflow: View Case Taking modal ===== -->
<div class="modal fade sh-modal sh-modal-accent" id="pfCaseViewModal" tabindex="-1" aria-labelledby="pfCaseViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pfCaseViewModalLabel">
                    <i class="fa fa-stethoscope"></i> <?php echo $this->lang->line('view_case_taking') ?: 'View Case Taking'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted small" id="pf_ctv_patient"></p>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-stethoscope"></i> Case Summary</span>
                            </div>
                            <div class="sh-card-body">
                        <table class="table table-bordered table-sm mb-0">
                            <tr><th style="width:40%"><?php echo $this->lang->line('case_taking_type') ?: 'Case Taking Type'; ?></th><td id="pf_ctv_case_taking_type"></td></tr>
                            <tr><th>Constitution Code</th><td id="pf_ctv_ct_constitution_code"></td></tr>
                            <tr><th>Sex</th><td id="pf_ctv_ct_sex"></td></tr>
                            <tr><th>Primary concern</th><td id="pf_ctv_ct_primary_concern"></td></tr>
                            <tr><th>Duration of concern</th><td id="pf_ctv_ct_concern_duration"></td></tr>
                            <tr><th>Primary system / area</th><td id="pf_ctv_ct_primary_system"></td></tr>
                            <tr><th>Dominant nature</th><td id="pf_ctv_ct_dominant_nature"></td></tr>
                            <tr><th>Body Type</th><td id="pf_ctv_ct_bod1"></td></tr>
                            <tr><th>Face Type</th><td id="pf_ctv_ct_fsh1"></td></tr>
                            <tr><th>Nose Type</th><td id="pf_ctv_ct_nos1"></td></tr>
                            <tr><th>Assessment certainty</th><td id="pf_ctv_ct_assessment_certainty"></td></tr>
                        </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-clipboard-check"></i> Assessment &amp; Status</span>
                            </div>
                            <div class="sh-card-body">
                        <table class="table table-bordered table-sm mb-0">
                            <tr><th style="width:40%">Case status</th><td id="pf_ctv_ct_status"></td></tr>
                            <tr><th>Review Required reason</th><td id="pf_ctv_ct_review_reason"></td></tr>
                            <tr><th>Tissue / structure</th><td id="pf_ctv_ct_tissue_structure"></td></tr>
                            <tr><th>Report information</th><td id="pf_ctv_ct_report_info"></td></tr>
                            <tr><th>Secondary concerns</th><td id="pf_ctv_ct_secondary_concerns"></td></tr>
                            <tr><th><?php echo $this->lang->line('description') ?: 'Description'; ?></th><td id="pf_ctv_desc"></td></tr>
                            <tr><th>Additional note</th><td id="pf_ctv_ct_additional_note"></td></tr>
                            <tr><th>Previous treatment</th><td id="pf_ctv_ct_previous_treatment"></td></tr>
                            <tr><th>Programme suggestion</th><td id="pf_ctv_ct_programme_suggestion"></td></tr>
                            <tr><th><?php echo $this->lang->line('taken_by') ?: 'Taken By'; ?></th><td id="pf_ctv_by"></td></tr>
                            <tr><th><?php echo $this->lang->line('taken_at') ?: 'Taken At'; ?></th><td id="pf_ctv_at"></td></tr>
                        </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?php echo $this->lang->line('close') ?: 'Close'; ?>
                </button>
                <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_edit')) { ?>
                <button type="button" class="btn btn-warning" id="pf_ctv_edit">
                    <i class="fa fa-edit"></i> <?php echo $this->lang->line('edit_case_taking') ?: 'Edit Case Taking'; ?>
                </button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
// Labels for the case-taking modal title (add vs. edit mode).
var pfCtLbl = {
    add: <?php echo json_encode($this->lang->line('case_taking') ?: 'Case Taking'); ?>,
    edit: <?php echo json_encode($this->lang->line('edit_case_taking') ?: 'Edit Case Taking'); ?>
};
// Case-taking detail fields (must match case_taking_columns() in the model).
var pfCtCols = [
    'case_taking_type', 'case_taking_description',
    'ct_sex', 'ct_primary_concern', 'ct_concern_duration', 'ct_primary_system', 'ct_dominant_nature',
    'ct_bod1', 'ct_fsh1', 'ct_nos1',
    'ct_assessment_certainty', 'ct_status', 'ct_tissue_structure', 'ct_report_info',
    'ct_review_reason', 'ct_secondary_concerns', 'ct_additional_note', 'ct_previous_treatment', 'ct_programme_suggestion'
];
var pfCtLookup = <?php echo json_encode($pf_lk); ?>;
// Patient image + personal information per visit (used by the case-taking
// modal's column 4). Built from the visit rows already loaded on the board.
var pfCtPatients = <?php
$pf_bloodgroup = $this->config->item('bloodgroup');
$pf_patients   = array();
foreach ($visits as $pf_v) {
    $pf_patients[(int) $pf_v->id] = array(
        'image'         => $this->media_storage->getImageURL(
                                !empty($pf_v->image) ? $pf_v->image : 'uploads/patient_images/no_image.png'
                            ),
        'name'          => (string) $pf_v->patient_name,
        'patient_uuid'  => (string) $pf_v->patient_uuid,
        'age'           => pf_ct_age_label(
                                isset($pf_v->age) ? $pf_v->age : 0,
                                isset($pf_v->month) ? $pf_v->month : 0,
                                isset($pf_v->day) ? $pf_v->day : 0,
                                isset($pf_v->dob) ? $pf_v->dob : ''
                            ),
        'dob'           => (!empty($pf_v->dob) && $pf_v->dob !== '0000-00-00')
                            ? $this->customlib->YYYYMMDDTodateFormat($pf_v->dob) : '',
        'gender'        => (!empty($pf_v->gender) ? pf_ct_gender_label((string) $pf_v->gender) : ''),
        'blood_group'   => (!empty($pf_v->blood_group_name) ? (string) $pf_v->blood_group_name
                            : (!empty($pf_v->blood_group)
                                ? (isset($pf_bloodgroup[$pf_v->blood_group]) ? $pf_bloodgroup[$pf_v->blood_group] : (string) $pf_v->blood_group)
                                : '')),
        'guardian_name' => (string) $pf_v->guardian_name,
        'mobileno'      => (string) $pf_v->mobileno,
        'marital_status' => (!empty($pf_v->marital_status) ? ucfirst((string) $pf_v->marital_status) : ''),
        'email'         => (string) $pf_v->email,
        'identification_number' => (string) $pf_v->identification_number,
        'address'       => (string) $pf_v->address,
        'known_allergies' => (string) $pf_v->known_allergies,
        'note'          => (string) $pf_v->note,
    );
}
echo json_encode($pf_patients);
?>;
// Constitution master rows for the live code preview: gender + body + face + nose -> code.
var pfConstitutionRows = <?php
$pf_con_rows = array();
if (!empty($constitutions)) {
    foreach ($constitutions as $pf_cr) {
        $pf_con_rows[] = array(
            'g' => (string) $pf_cr->gender,
            'b' => (int) $pf_cr->body_build_id,
            'f' => (int) $pf_cr->face_shape_id,
            'n' => (int) $pf_cr->nose_type_id,
            'c' => (string) $pf_cr->code,
        );
    }
}
echo json_encode($pf_con_rows);
?>;
// Preview the constitution code from the currently selected sex/body/face/nose.
function pfCtPreviewCode() {
    var sex = $('#pf_ct_sex').val() || '';
    var g = (sex === 'M') ? 'Male' : (sex === 'F') ? 'Female' : '';
    var b = parseInt($('#pf_ct_bod1').val(), 10) || 0;
    var f = parseInt($('#pf_ct_fsh1').val(), 10) || 0;
    var n = parseInt($('#pf_ct_nos1').val(), 10) || 0;
    var code = '';
    if (g && b && f && n) {
        for (var i = 0; i < pfConstitutionRows.length; i++) {
            var r = pfConstitutionRows[i];
            if (r.g === g && r.b === b && r.f === f && r.n === n) { code = r.c; break; }
        }
    }
    $('#pf_ct_constitution_code').val(code);
}
$(document).on('change', '#pf_ct_sex, #pf_ct_bod1, #pf_ct_fsh1, #pf_ct_nos1', function () {
    pfCtPreviewCode();
});
// Input id per posted field; type/description keep their legacy ids.
function pfCtInputId(name) {
    if (name === 'case_taking_type') { return 'pf_ct_type'; }
    if (name === 'case_taking_description') { return 'pf_ct_desc'; }
    return 'pf_' + name;
}
function pfCtParse(btn) {
    var ct = $(btn).data('ct') || {};
    if (typeof ct === 'string') { try { ct = JSON.parse(ct); } catch (e) { ct = {}; } }
    return ct;
}

// Fill the modal's column 4 with the patient's image + personal information.
function pfCtSetInfo(id, val) {
    var v = (val === null || val === undefined) ? '' : String(val).trim();
    $('#' + id).text(v === '' ? '—' : v);
}
function pfCtRenderPatientInfo(visitId) {
    var p = pfCtPatients[visitId] || null;
    if (!p) {
        $('#pf_ct_pi_image').attr('src', '<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>');
        pfCtSetInfo('pf_ct_pi_name', '');
        pfCtSetInfo('pf_ct_pi_uid', '');
        ['pf_ct_pi_age', 'pf_ct_pi_dob', 'pf_ct_pi_gender', 'pf_ct_pi_marital', 'pf_ct_pi_blood_group',
         'pf_ct_pi_guardian', 'pf_ct_pi_mobile', 'pf_ct_pi_email', 'pf_ct_pi_idno',
        //  'pf_ct_pi_address', 'pf_ct_pi_allergies', 'pf_ct_pi_note'].forEach(function (f) {
         'pf_ct_pi_address', 'pf_ct_pi_allergies'].forEach(function (f) {
            pfCtSetInfo(f, '');
        });
        return;
    }
    $('#pf_ct_pi_image').attr('src', p.image || '');
    pfCtSetInfo('pf_ct_pi_name', p.name);
    pfCtSetInfo('pf_ct_pi_uid', p.patient_uuid ? 'UID: ' + p.patient_uuid : '');
    pfCtSetInfo('pf_ct_pi_age', p.age);
    pfCtSetInfo('pf_ct_pi_dob', p.dob);
    pfCtSetInfo('pf_ct_pi_gender', p.gender);
    pfCtSetInfo('pf_ct_pi_marital', p.marital_status);
    pfCtSetInfo('pf_ct_pi_blood_group', p.blood_group);
    pfCtSetInfo('pf_ct_pi_guardian', p.guardian_name);
    pfCtSetInfo('pf_ct_pi_mobile', p.mobileno);
    pfCtSetInfo('pf_ct_pi_email', p.email);
    pfCtSetInfo('pf_ct_pi_idno', p.identification_number);
    pfCtSetInfo('pf_ct_pi_address', p.address);
    pfCtSetInfo('pf_ct_pi_allergies', p.known_allergies);
    // pfCtSetInfo('pf_ct_pi_note', p.note);
}

// Shared: open the case-taking form, pre-filled with the given case data.
// isEdit = 0 -> initial take, isEdit = 1 -> edit of an existing case taking.
function pfCtOpenForm(opts) {
    var $modal = $('#pfCaseModal');
    if ($modal.parent()[0] && $modal.parent()[0] !== document.body) { $modal.appendTo('body'); }
    var isEdit = opts.isEdit ? 1 : 0;
    $('#pf_ct_visit_id').val(opts.visit || '');
    $('#pf_ct_is_edit').val(isEdit);
    $('#pf_ct_title_text').text(isEdit ? pfCtLbl.edit : pfCtLbl.add);
    // Column 4: patient's image + personal information.
    pfCtRenderPatientInfo(opts.visit);
    // Patient History: previous visits of this patient.
    pfCtLoadPatientHistory(opts.patientId || 0);
    var ct = opts.ct || {};
    for (var i = 0; i < pfCtCols.length; i++) {
        var key = pfCtCols[i];
        var v = ct[key];
        $('#' + pfCtInputId(key)).val((v === null || v === undefined) ? '' : v);
    }
    if (!$('#pf_ct_status').val()) { $('#pf_ct_status').val('Open'); } // default workflow status
    $('#pf_ct_notice').addClass('d-none').text('');
    pfCtPreviewCode();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfCaseModal')).show();
}

// Fetch and render previous visits of a patient in the Patient History panel.
function pfCtLoadPatientHistory(patientId) {
    var $body = $('#pf_ct_history_body');
    var $rows = $('#pf_ct_history_rows');
    var $table = $('#pf_ct_history_table');
    $body.find('.text-muted').removeClass('d-none').text('Loading visits...');
    $table.addClass('d-none');
    $rows.empty();
    if (!patientId) {
        $body.find('.text-muted').text('No patient history available.');
        return;
    }
    $.post('<?php echo site_url('admin/patientflow/get_patient_visits'); ?>', { patient_id: patientId },
        function (response) {
            $body.find('.text-muted').addClass('d-none');
            if (!response || !response.status || !response.visits || response.visits.length === 0) {
                $body.find('.text-muted').removeClass('d-none').text('No previous visits.');
                return;
            }
            $table.removeClass('d-none');
            for (var i = 0; i < response.visits.length; i++) {
                var v = response.visits[i];
                var created = v.created_at ? v.created_at : '—';
                var billing = (v.billing_reference || v.invoice_no || '—');
                var status = v.status || '—';
                var statusClass = (status === 'completed') ? 'text-success' : (status === 'cancelled') ? 'text-danger' : 'text-muted';
                var row = '<tr>' +
                    '<td>' + (v.visit_code || '#' + v.id) + '</td>' +
                    '<td><small>' + (v.level_code || '-') + '</small><br>' + (v.level_title || '-') + '</td>' +
                    '<td>' + (v.token_no || '—') + '</td>' +
                    '<td><span class="' + statusClass + '">' + status + '</span></td>' +
                    '<td><small>' + created + '</small></td>' +
                    '<td><small>' + billing + '</small></td>' +
                    '</tr>';
                $rows.append(row);
            }
        },
        'json'
    ).fail(function () {
        $body.find('.text-muted').removeClass('d-none').text('Failed to load visits.');
    });
}

$(document).on('click', '.pf-case-btn', function () {
    var $btn = $(this);
    pfCtOpenForm({
        isEdit: false,
        visit: $btn.data('visit'),
        patientId: $btn.data('patient-id'),
        patient: $btn.data('patient'),
        patientUuid: $btn.data('patient-uuid'),
        ct: pfCtParse(this),
    });
});

// Remember the row whose case taking is currently being viewed, so the
// "Edit" button in the view modal knows what to load into the form.
var pfCtViewCtx = null;

$(document).on('click', '.pf-case-view-btn', function () {
    var $btn = $(this);
    var $modal = $('#pfCaseViewModal');
    if ($modal.parent()[0] && $modal.parent()[0] !== document.body) { $modal.appendTo('body'); }
    $('#pf_ctv_patient').text($btn.data('patient') +' '+ $btn.data('patient-uuid') || '');
    var ct = pfCtParse(this);
    // Cache the context for the edit button.
    pfCtViewCtx = {
        visit: $btn.data('visit'),
        patient: $btn.data('patient'),
        patientUuid: $btn.data('patient-uuid'),
        canEdit: String($btn.data('can-edit')) === '1',
        ct: ct,
    };
    function pfCtPut(id, val) {
        var s = (val === null || val === undefined || val === '') ? '' : String(val);
        $('#' + id).text(s === '' ? '—' : s);
    }
    pfCtPut('pf_ctv_case_taking_type', ct.case_taking_type);
    pfCtPut('pf_ctv_desc', ct.case_taking_description);
    var scalar = ['ct_constitution_code', 'ct_sex', 'ct_primary_concern', 'ct_concern_duration', 'ct_primary_system', 'ct_dominant_nature', 'ct_assessment_certainty', 'ct_status', 'ct_review_reason', 'ct_tissue_structure', 'ct_report_info', 'ct_secondary_concerns', 'ct_additional_note', 'ct_previous_treatment', 'ct_programme_suggestion'];
    for (var i = 0; i < scalar.length; i++) { pfCtPut('pf_ctv_' + scalar[i], ct[scalar[i]]); }
    function pfCtSlot(pfx) {
        var map = pfCtLookup[pfx] || {};
        var val = ct['ct_' + pfx + '1'];
        if (val === null || val === undefined || val === '') { return ''; }
        return map[String(val)] || String(val);
    }
    pfCtPut('pf_ctv_ct_bod1', pfCtSlot('bod'));
    pfCtPut('pf_ctv_ct_fsh1', pfCtSlot('fsh'));
    pfCtPut('pf_ctv_ct_nos1', pfCtSlot('nos'));
    $('#pf_ctv_by').text($btn.data('by') || '—');
    $('#pf_ctv_at').text($btn.data('at') || '—');
    // Only offer the edit action when this row may actually be edited.
    if ($('#pf_ctv_edit').length) {
        $('#pf_ctv_edit').toggle(!!pfCtViewCtx.canEdit);
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfCaseViewModal')).show();
});

// "Edit" in the View Case Taking modal -> close the view and open the
// case-taking form pre-filled with the saved values, in edit mode.
$(document).on('click', '#pf_ctv_edit', function () {
    if (!pfCtViewCtx || !pfCtViewCtx.canEdit) { return; }
    var ctx = pfCtViewCtx;
    var viewEl = document.getElementById('pfCaseViewModal');
    var viewModal = bootstrap.Modal.getInstance(viewEl);
    if (viewModal) { viewModal.hide(); }
    // Wait for the view modal to finish hiding before opening the form modal.
    setTimeout(function () {
        pfCtOpenForm({
            isEdit: true,
            visit: ctx.visit,
            patient: ctx.patient,
            patientUuid: ctx.patientUuid,
            ct: ctx.ct,
        });
    }, 250);
});

$(document).on('click', '#pf_ct_save', function () {
    var visitId = $('#pf_ct_visit_id').val();
    if (!visitId) {
        $('#pf_ct_notice').removeClass('d-none').text('No visit selected.');
        return;
    }
    var missing = [];
    function pfCtNeed(id, label) {
        var v = $('#' + id).val();
        if (v === null || v === undefined || String(v).trim() === '') { missing.push(label); }
    }
    pfCtNeed('pf_ct_type', 'Case Taking Type');
    pfCtNeed('pf_ct_sex', 'Sex');
    pfCtNeed('pf_ct_primary_concern', 'Primary concern');
    pfCtNeed('pf_ct_concern_duration', 'Duration of concern');
    pfCtNeed('pf_ct_primary_system', 'Primary system / area');
    pfCtNeed('pf_ct_dominant_nature', 'Dominant nature');
    pfCtNeed('pf_ct_bod1', 'Body Type');
    pfCtNeed('pf_ct_fsh1', 'Face Type');
    pfCtNeed('pf_ct_nos1', 'Nose Type');
    var certainty = $('#pf_ct_assessment_certainty').val() || '';
    var ctStatus = $('#pf_ct_status').val() || '';
    if (!certainty) { missing.push('Assessment certainty'); }
    if (!ctStatus) { missing.push('Case status'); }
    if ((certainty && certainty !== 'Certain') || ctStatus === 'In Review') {
        pfCtNeed('pf_ct_review_reason', 'Review Required reason');
    }
    if (missing.length) {
        $('#pf_ct_notice').removeClass('d-none').text('Please fill in: ' + missing.join(', ') + '.');
        return;
    }
    $('#pf_ct_notice').addClass('d-none').text('');
    var payload = { visit_id: visitId, is_edit: ($('#pf_ct_is_edit').val() === '1' ? 1 : 0) };
    for (var i = 0; i < pfCtCols.length; i++) {
        payload[pfCtCols[i]] = $('#' + pfCtInputId(pfCtCols[i])).val();
    }
    var $btn = $(this).prop('disabled', true);
    $.post('<?php echo site_url('admin/patientflow/save_case_taking'); ?>', payload,
        function (response) {
            $btn.prop('disabled', false);
            if (response.status) {
                successMsg(response.message);
                var modalEl = document.getElementById('pfCaseModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                $('#pf_ct_notice').removeClass('d-none').text(response.message);
            }
        }, 'json'
    ).fail(function () {
        $btn.prop('disabled', false);
        errorMsg('<?php echo $this->lang->line('case_taking_action_failed') ?: 'An error occurred while saving case taking.'; ?>');
    });
});
</script>

<?php
/* Smart Hospital — Admin Dashboard
 * Variant B (Executive) — approved 2026-05-15
 * Visual spec: Wfdocs/dashboard-samples.html  (Variant B section)
 * Backup of previous calendar-based view: application/views/admin/dashboard_legacy_20260515.php
 *
 * Variable contract from Admin.php::dashboard():
 *   REAL (already in controller):
 *     $notifications, $systemnotifications, $mysqlVersion, $sqlMode
 *     $opd_income/$ipd_income/$pharmacy_income/$pathology_income/$radiology_income/
 *     $blood_bank_income/$ambulance_income/$general_income (formatted strings)
 *     $expense (object), $expences (raw amount), $yearly_collection, $yearly_expense, $total_month
 *     $jsonarr (doughnut series), $roles (staff role counts), $role (user type)
 *   STUB (TODO — wire from controller in follow-up):
 *     $total_revenue_mtd, $total_revenue_delta_pct, $total_revenue_spark
 *     $bed_occupancy_pct, $bed_occupancy_data, $bed_occupancy_spark
 *     $today_appointments_count, $today_appointments_by_status, $today_appointments_spark
 *     $outstanding_bills_amount, $outstanding_bills_count, $outstanding_overdue
 *     $next_appointments, $beds_by_ward
 *     $low_stock_pharmacy_count, $low_stock_critical
 *     $blood_bank_stock, $bb_issued_today, $bb_issued_week
 *     $recent_activity (UNION feed), $staff_attendance, $module_income_breakdown
 */
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();

// Safe defaults — controller will replace these when backend queries are wired.
$today_appointments_count    = isset($today_appointments_count)    ? $today_appointments_count    : 0;
$today_appointments_status   = isset($today_appointments_status)   ? $today_appointments_status   : array('confirmed'=>0,'pending'=>0,'cancelled'=>0);
$bed_occupancy_total         = isset($bed_occupancy_total)         ? (int)$bed_occupancy_total    : 0;
$bed_occupancy_occupied      = isset($bed_occupancy_occupied)      ? (int)$bed_occupancy_occupied : 0;
$bed_occupancy_available     = isset($bed_occupancy_available)     ? (int)$bed_occupancy_available : 0;
$bed_occupancy_unused        = isset($bed_occupancy_unused)        ? (int)$bed_occupancy_unused   : 0;
$bed_occupancy_pct           = $bed_occupancy_total > 0 ? min(100, round(($bed_occupancy_occupied / $bed_occupancy_total) * 100)) : 0;
$outstanding_amount          = isset($outstanding_amount)          ? $outstanding_amount          : 0;
$outstanding_count           = isset($outstanding_count)           ? (int)$outstanding_count      : 0;
$outstanding_overdue         = isset($outstanding_overdue)         ? (int)$outstanding_overdue    : 0;
$next_appointments           = isset($next_appointments)           ? $next_appointments           : array();
$beds_by_ward                = isset($beds_by_ward)                ? $beds_by_ward                : array();
$low_stock_count             = isset($low_stock_count)             ? (int)$low_stock_count        : 0;
$low_stock_critical_count    = isset($low_stock_critical_count)    ? (int)$low_stock_critical_count : 0;
$low_stock_critical_names    = isset($low_stock_critical_names)    ? $low_stock_critical_names    : '';
$medicine_expiring_soon      = isset($medicine_expiring_soon)      ? (int)$medicine_expiring_soon : 0;
$medicine_expiring_days      = isset($medicine_expiring_days)      ? (int)$medicine_expiring_days : 30;
$blood_bank_stock            = isset($blood_bank_stock)            ? $blood_bank_stock            : array();
$bb_issued_today             = isset($bb_issued_today)             ? (int)$bb_issued_today        : 0;
$bb_issued_week              = isset($bb_issued_week)              ? (int)$bb_issued_week         : 0;
$recent_activity             = isset($recent_activity)             ? $recent_activity             : array();
$staff_attendance            = isset($staff_attendance)            ? $staff_attendance            : array();
$module_income_breakdown     = isset($module_income_breakdown)     ? $module_income_breakdown     : array();
$module_income_last          = isset($module_income_last)          ? $module_income_last          : array();
$expences_last               = isset($expences_last)               ? (float)$expences_last        : 0;
$kpi_total_revenue           = isset($kpi_total_revenue)           ? $kpi_total_revenue           : 0;
$kpi_revenue_delta           = isset($kpi_revenue_delta)           ? $kpi_revenue_delta           : 0;

if (empty($blood_bank_stock)) {
    $blood_bank_stock = array(
        'O+'=>0,'A+'=>0,'B+'=>0,'AB+'=>0,
        'O-'=>0,'A-'=>0,'B-'=>0,'AB-'=>0,
    );
}
function bb_class($u) { if ($u <= 3) return 'low'; if ($u <= 5) return 'warn'; return ''; }
?>

<!-- ═══════════ Alert banners — sh-alert minimal-inline style ═══════════ -->
<div class="dash-alerts">
    <?php if (ENVIRONMENT != 'production') { ?>
        <div class="sh-alert warn alert alert-dismissible fade show" role="alert">
            <span class="ic"><i class="fa fa-exclamation-triangle"></i></span>
            <span class="txt">Environment set to <?php echo ENVIRONMENT; ?>! Don't forget to set back to production in index.php after testing.</span>
            <button type="button" class="x" data-bs-dismiss="alert" aria-label="Close"><i class="fa fa-times"></i></button>
        </div>
    <?php } ?>

    <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== FALSE) { ?>
        <div class="sh-alert danger alert alert-dismissible fade show" role="alert">
            <span class="ic"><i class="fa fa-database"></i></span>
            <span class="txt">Smart Hospital may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.</span>
            <button type="button" class="x" data-bs-dismiss="alert" aria-label="Close"><i class="fa fa-times"></i></button>
        </div>
    <?php } ?>

    <?php
    $show = false;
    $role = $this->customlib->getStaffRole();
    $role_id = json_decode($role)->id;
    foreach (array_slice($notifications, 0, 3) as $notice_key => $notice_value) {
        if ($role_id == 7) {
            $show = true;
        } elseif (date($this->customlib->getHospitalDateFormat()) >= $this->customlib->YYYYMMDDTodateFormat($notice_value->publish_date)) {
            $show = true;
        }
        ?>
        <div class="sh-alert success dashalert alert alert-dismissible fade show" role="alert">
            <span class="ic"><i class="fa fa-bell"></i></span>
            <span class="txt"><a href="<?php echo site_url('admin/notification') ?>"><?php echo html_escape($notice_value->title); ?></a></span>
            <button type="button" class="x alertclose close_notice" data-bs-dismiss="alert" aria-label="Close" data-noticeid="<?php echo (int)$notice_value->id; ?>"><i class="fa fa-times"></i></button>
        </div>
        <?php
    }
    ?>
</div>

<!-- ═══════════ Page header ═══════════ -->
<div class="dash-page-head">
    <div>
        <h1><?php echo $this->lang->line('dashboard'); ?></h1>
        <div class="crumb"><?php echo $this->lang->line('home'); ?> <i class="fa fa-angle-right mx-1"></i> <?php echo $this->lang->line('dashboard'); ?></div>
    </div>
</div>

<!-- ═══════════ Hero KPIs (4 tiles with sparkline + delta) ═══════════ -->
<div class="kpi-grid">

    <?php if ($this->rbac->hasPrivilege('dash_total_revenue', 'can_view')): ?>
    <div class="kpi s-green">
        <div class="kpi-label"><?php echo $this->lang->line('total_revenue'); ?> · <?php echo $this->lang->line('mtd'); ?></div>
        <div class="kpi-value"><?php echo $currency_symbol; ?> <?php echo number_format((float)$kpi_total_revenue, 0); ?></div>
        <div class="kpi-delta <?php echo $kpi_revenue_delta < 0 ? 'neg' : ($kpi_revenue_delta == 0 ? 'flat' : ''); ?>">
            <?php if ($kpi_revenue_delta > 0): ?><i class="fas fa-arrow-up"></i><?php elseif ($kpi_revenue_delta < 0): ?><i class="fas fa-arrow-down"></i><?php endif; ?>
            <?php echo abs((float)$kpi_revenue_delta); ?>% <?php echo $this->lang->line('vs_last_month'); ?>
        </div>
        <!-- TODO: render sparkline from $kpi_revenue_spark (last 7 days) -->
        <svg class="kpi-spark" viewBox="0 0 120 32" preserveAspectRatio="none" aria-hidden="true">
            <path class="fill" d="M0,26 L20,22 L40,24 L60,18 L80,16 L100,10 L120,8 L120,32 L0,32 Z"/>
            <path class="line" d="M0,26 L20,22 L40,24 L60,18 L80,16 L100,10 L120,8"/>
        </svg>
    </div>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')): ?>
    <a class="kpi s-green" href="<?php echo site_url('admin/patientflow'); ?>">
        <div class="kpi-label"><?php echo $this->lang->line('patient_flow') ?: 'Patient Flow'; ?> · <?php echo $this->lang->line('active') ?: 'Active'; ?></div>
        <div class="kpi-value"><?php echo (int) $pf_active_visits; ?></div>
        <div class="kpi-delta <?php echo $pf_pending_payment_count > 0 ? 'neg' : 'flat'; ?>">
            <?php echo (int) $pf_pending_payment_count; ?> <?php echo $this->lang->line('payment_pending') ?: 'payments pending'; ?>
        </div>
        <svg class="kpi-spark" viewBox="0 0 120 32" preserveAspectRatio="none" aria-hidden="true">
            <path class="fill" d="M0,24 L20,22 L40,20 L60,22 L80,16 L100,14 L120,12 L120,32 L0,32 Z"/>
            <path class="line" d="M0,24 L20,22 L40,20 L60,22 L80,16 L100,14 L120,12"/>
        </svg>
    </a>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('dash_today_appointments', 'can_view')): ?>
    <a class="kpi s-green" href="<?php echo site_url('admin/appointment'); ?>">
        <div class="kpi-label"><?php echo $this->lang->line('today_appointments'); ?></div>
        <div class="kpi-value"><?php echo $today_appointments_count; ?></div>
        <div class="kpi-delta">
            <?php echo (int)($today_appointments_status['confirmed'] ?? 0); ?> <?php echo $this->lang->line('confirmed'); ?> · <?php echo (int)($today_appointments_status['pending'] ?? 0); ?> <?php echo $this->lang->line('pending'); ?>
        </div>
        <svg class="kpi-spark" viewBox="0 0 120 32" preserveAspectRatio="none" aria-hidden="true">
            <path class="fill" d="M0,24 L20,22 L40,20 L60,22 L80,16 L100,14 L120,12 L120,32 L0,32 Z"/>
            <path class="line" d="M0,24 L20,22 L40,20 L60,22 L80,16 L100,14 L120,12"/>
        </svg>
    </a>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('dash_outstanding_bills', 'can_view')): ?>
    <div class="kpi s-red">
        <div class="kpi-label"><?php echo $this->lang->line('outstanding_bills'); ?></div>
        <div class="kpi-value"><?php echo $currency_symbol; ?> <?php echo number_format((float)$outstanding_amount, 0); ?></div>
        <div class="kpi-delta neg"><?php echo $outstanding_count; ?> <?php echo $this->lang->line('unpaid'); ?> · <?php echo $outstanding_overdue; ?> <?php echo $this->lang->line('overdue'); ?></div>
        <svg class="kpi-spark" viewBox="0 0 120 32" preserveAspectRatio="none" aria-hidden="true">
            <path class="fill" d="M0,14 L20,16 L40,18 L60,16 L80,20 L100,22 L120,24 L120,32 L0,32 Z"/>
            <path class="line" d="M0,14 L20,16 L40,18 L60,16 L80,20 L100,22 L120,24"/>
        </svg>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════ Operations snapshot (4 cards) ═══════════ -->
<div class="ops-grid">

    <?php if ($this->rbac->hasPrivilege('dash_today_appointments', 'can_view')): ?>
    <div class="ops-card">
        <div class="head">
            <div class="ic blue"><i class="fa fa-calendar-check-o"></i></div>
            <div class="lbl"><?php echo $this->lang->line('today_appointment'); ?></div>
            <a class="meta" href="<?php echo site_url('admin/appointment/index'); ?>"><?php echo $this->lang->line('view'); ?></a>
        </div>
        <div class="next-list">
            <?php if (empty($next_appointments)): ?>
                <div class="row muted"><span class="nm"><?php echo $this->lang->line('no_appointment_today'); ?></span></div>
            <?php else: foreach (array_slice($next_appointments, 0, 4) as $appt): ?>
                <div class="row"><span class="nm"><?php echo html_escape($appt['time'] ?? ''); ?> · <?php echo html_escape($appt['name'] ?? ''); ?></span><span class="badge-pill <?php echo html_escape($appt['badge_class'] ?? 'ok'); ?>"><?php echo html_escape($appt['type'] ?? ''); ?></span></div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')): ?>
    <div class="ops-card">
        <div class="head">
            <div class="ic amber"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="lbl"><?php echo $this->lang->line('pending_payments') ?: 'Pending Payments'; ?></div>
            <a class="meta" href="<?php echo site_url('admin/patientflow'); ?>"><?php echo $this->lang->line('manage'); ?></a>
        </div>
        <div class="big"><?php echo $currency_symbol; ?> <?php echo number_format((float) $pf_pending_payment_amount, 0); ?></div>
        <div class="sub"><?php echo (int) $pf_pending_payment_count; ?> <?php echo $this->lang->line('visits_awaiting_payment') ?: 'visits awaiting payment'; ?></div>
    </div>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('dash_medicines_stock', 'can_view')): ?>
    <div class="ops-card">
        <div class="head">
            <div class="ic amber"><i class="fas fa-pills"></i></div>
            <div class="lbl"><?php echo $this->lang->line('medicines_stock'); ?></div>
            <a class="meta" href="<?php echo site_url('admin/pharmacy/search'); ?>"><?php echo $this->lang->line('manage'); ?></a>
        </div>
        <div class="big"><?php echo $low_stock_count; ?></div>
        <div class="sub"><?php echo $this->lang->line('items_below_reorder'); ?> · <?php echo $low_stock_critical_count; ?> <?php echo $this->lang->line('critical'); ?></div>
        <?php if (!empty($low_stock_critical_names)): ?>
            <div class="warn-line"><i class="fas fa-exclamation-triangle"></i> <?php echo html_escape($low_stock_critical_names); ?> <?php echo $this->lang->line('running_low'); ?></div>
        <?php endif; ?>
        <?php if ($medicine_expiring_soon > 0): ?>
            <div class="sub"><i class="fas fa-hourglass-end"></i> <?php echo $medicine_expiring_soon; ?> <?php echo $this->lang->line('medicines_expiring_in'); ?> <?php echo $medicine_expiring_days; ?> <?php echo $this->lang->line('days'); ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($this->rbac->hasPrivilege('patient_flow', 'can_view')): ?>
    <div class="ops-card">
        <div class="head">
            <div class="ic amber"><i class="fas fa-users"></i></div>
            <div class="lbl"><?php echo $this->lang->line('doctor_queue') ?: 'Doctor Queue'; ?></div>
            <a class="meta" href="<?php echo site_url('admin/patientflow/token_queue'); ?>"><?php echo $this->lang->line('manage'); ?></a>
        </div>
        <div class="big"><?php echo (int) $pf_queue_active; ?><span class="unit"> / <?php echo (int) $pf_active_visits; ?></span></div>
        <div class="sub"><?php echo $this->lang->line('in_queue') ?: 'in queue'; ?> · <?php echo (int) $pf_completed_today; ?> <?php echo $this->lang->line('completed_today') ?: 'completed today'; ?></div>
    </div>
    <?php endif; ?>

</div>

<!-- ═══════════ Charts (Yearly Income vs Expense + Monthly Income Mix) ═══════════ -->
<div class="dash-chart-row">

    <?php if ($this->rbac->hasPrivilege('yearly_income_expense_chart', 'can_view')) { ?>
    <div class="dash-chart-card">
        <div class="dash-chart-card-h">
            <h3><?php echo $this->lang->line('yearly_income_expense'); ?></h3>
            <span class="sub"><?php echo $this->lang->line('trend_12_month'); ?></span>
        </div>
        <div class="dash-chart-card-body collapse show" id="chart-yearly-body">
            <div class="chart-summary">
                <div class="stat"><span class="k"><?php echo $this->lang->line('income_ytd'); ?></span><span class="v up"><?php echo $currency_symbol; ?> <?php echo number_format(array_sum($yearly_collection), 0); ?></span></div>
                <div class="stat"><span class="k"><?php echo $this->lang->line('expense_ytd'); ?></span><span class="v dn"><?php echo $currency_symbol; ?> <?php echo number_format(array_sum($yearly_expense), 0); ?></span></div>
                <?php $net = array_sum($yearly_collection) - array_sum($yearly_expense); ?>
                <div class="stat"><span class="k"><?php echo $this->lang->line('net'); ?></span><span class="v <?php echo $net >= 0 ? 'up' : 'dn'; ?>"><?php echo $currency_symbol; ?> <?php echo number_format($net, 0); ?></span></div>
            </div>
            <div class="dash-chart-canvas-wrap"><canvas id="lineChart"></canvas></div>
        </div>
    </div>
    <?php } ?>

    <?php if ($this->rbac->hasPrivilege('monthly_income_expense_chart', 'can_view')) { ?>
    <div class="dash-chart-card">
        <div class="dash-chart-card-h">
            <h3><?php echo $this->lang->line('monthly_income_overview'); ?></h3>
        </div>
        <div class="dash-chart-card-body collapse show" id="chart-monthly-body">
            <div class="doughnut-layout">
                <div class="doughnut-wrap">
                    <canvas id="pieChart"></canvas>
                    <div class="doughnut-center">
                        <?php /* Real monthly income total — NOT array_sum($jsonarr['value']), which sums percentages (=100). */ ?>
                        <?php $total_income_month = (float)$kpi_total_revenue; ?>
                        <span class="v"><?php echo $currency_symbol; ?> <?php echo number_format($total_income_month, 0); ?></span>
                        <span class="l"><?php echo isset($jsonarr['label']) ? count($jsonarr['label']) : 0; ?> <?php echo $this->lang->line('sources'); ?></span>
                    </div>
                </div>
                <ul class="donut-legend">
                    <?php
                    $palette = array('#1B73E8','#22A06B','#00B5AD','#F59E0B','#7E57C2','#AB47BC','#E42527','#FF6B35');
                    if (!empty($jsonarr['label']) && is_array($jsonarr['label'])):
                        foreach ($jsonarr['label'] as $i => $lbl):
                            $val = (float)($jsonarr['value'][$i] ?? 0);
                            $color = $palette[$i % count($palette)];
                    ?>
                        <li>
                            <span class="sw" style="--c:<?php echo $color; ?>"></span>
                            <span class="nm"><?php echo html_escape($lbl); ?></span>
                            <span class="val"><?php echo $val; ?>%</span>
                        </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php } ?>

</div>

<!-- ═══════════ Recent Activity (full-width table-card) ═══════════ -->
<?php if ($this->rbac->hasPrivilege('dash_recent_activity', 'can_view')): ?>
<div class="dash-table-card">
    <div class="dash-table-card-h">
        <h3><?php echo $this->lang->line('recent_activity'); ?></h3>
    </div>
    <table class="dash-tbl">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('user'); ?></th>
                <th><?php echo $this->lang->line('action'); ?></th>
                <th><?php echo $this->lang->line('detail'); ?></th>
                <th><?php echo $this->lang->line('module'); ?></th>
                <th class="num"><?php echo $this->lang->line('amount'); ?></th>
                <th class="num"><?php echo $this->lang->line('time'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_activity)): ?>
                <tr><td colspan="6" class="empty"><?php echo $this->lang->line('no_recent_activity'); ?></td></tr>
            <?php else: foreach ($recent_activity as $ev): ?>
                <tr>
                    <td><strong><?php echo html_escape($ev['actor'] ?? ''); ?></strong></td>
                    <td><?php echo html_escape($ev['action'] ?? ''); ?></td>
                    <td><?php echo html_escape($ev['detail'] ?? ''); ?></td>
                    <td><span class="badge-pill <?php echo html_escape($ev['module_class'] ?? 'info'); ?>"><?php echo html_escape($ev['module'] ?? ''); ?></span></td>
                    <td class="num"><?php echo !empty($ev['amount']) ? $currency_symbol . ' ' . number_format((float)$ev['amount'], 0) : '—'; ?></td>
                    <td class="num"><span class="meta-text"><?php echo html_escape($ev['time_ago'] ?? ''); ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- ═══════════ Staff & Attendance + Income by Module (2-col equal) ═══════════ -->
<div class="dash-2col-equal">

    <?php if ($this->rbac->hasPrivilege('staff_role_count_widget', 'can_view')) { ?>
    <div class="dash-table-card">
        <?php
        $total_headcount = 0; $total_present = 0;
        if (!empty($staff_attendance) && is_array($staff_attendance)) {
            foreach ($staff_attendance as $sa) {
                $total_headcount += (int)($sa['total'] ?? 0);
                $total_present   += (int)($sa['present'] ?? 0);
            }
        } else if (!empty($roles)) {
            foreach ($roles as $r) {
                $total_headcount += (int)($r['count'] ?? 0);
            }
        }
        $attend_pct = $total_headcount > 0 ? round(($total_present / $total_headcount) * 100) : 0;
        ?>
        <div class="dash-table-card-h">
            <h3><?php echo $this->lang->line('staff_attendance'); ?></h3>
            <span class="sub"><?php echo $total_present; ?> / <?php echo $total_headcount; ?> today · <?php echo $attend_pct; ?>%</span>
        </div>
        <table class="dash-tbl">
            <thead><tr><th><?php echo $this->lang->line('role'); ?></th><th class="num"><?php echo $this->lang->line('total'); ?></th><th class="num"><?php echo $this->lang->line('present'); ?></th><th><?php echo $this->lang->line('attendance'); ?></th></tr></thead>
            <tbody>
                <?php if (!empty($staff_attendance)): foreach ($staff_attendance as $sa):
                    $t = (int)($sa['total'] ?? 0);
                    $p = (int)($sa['present'] ?? 0);
                    $pct = $t > 0 ? round(($p / $t) * 100) : 0;
                    $cls = $pct >= 85 ? 'ok' : ($pct >= 70 ? 'warn' : 'err');
                    $bar = $pct >= 85 ? '' : ($pct >= 70 ? 'warn' : 'crit');
                ?>
                    <tr>
                        <td><strong><?php echo html_escape($sa['name'] ?? ''); ?></strong></td>
                        <td class="num"><?php echo $t; ?></td>
                        <td class="num"><span class="badge-pill <?php echo $cls; ?>"><?php echo $p; ?></span></td>
                        <td><span class="pbar <?php echo $bar; ?>"><span class="track <?php echo $bar; ?>"><i style="--w:<?php echo $pct; ?>%"></i></span><span class="pct"><?php echo $pct; ?>%</span></span></td>
                    </tr>
                <?php endforeach; elseif (!empty($roles)): foreach ($roles as $r): ?>
                    <tr>
                        <td><strong><?php echo html_escape($r['name'] ?? ''); ?></strong></td>
                        <td class="num"><?php echo (int)($r['count'] ?? 0); ?></td>
                        <td class="num"><span class="badge-pill muted">—</span></td>
                        <td><span class="meta-text"><?php echo $this->lang->line('attendance_pending'); ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <?php if ($this->rbac->hasPrivilege('dash_income_by_module', 'can_view')): ?>
    <div class="dash-table-card">
        <?php
        if (empty($module_income_breakdown)) {
            $module_income_breakdown = array();
            $income_modules = array(
                array('key'=>'ipd',        'name'=>$this->lang->line('ipd'),        'icon'=>'fas fa-procedures',         'ic_class'=>'blue',   'value'=>$ipd_income,        'module'=>'ipd'),
                array('key'=>'opd',        'name'=>$this->lang->line('opd'),        'icon'=>'fas fa-stethoscope',        'ic_class'=>'green',  'value'=>$opd_income,        'module'=>'opd'),
                array('key'=>'pharmacy',   'name'=>$this->lang->line('pharmacy'),   'icon'=>'fas fa-mortar-pestle',      'ic_class'=>'teal',   'value'=>$pharmacy_income,   'module'=>'pharmacy'),
                array('key'=>'radiology',  'name'=>$this->lang->line('radiology'),  'icon'=>'fas fa-microscope',         'ic_class'=>'amber',  'value'=>$radiology_income,  'module'=>'radiology'),
                array('key'=>'pathology',  'name'=>$this->lang->line('pathology'),  'icon'=>'fas fa-flask',              'ic_class'=>'violet', 'value'=>$pathology_income,  'module'=>'pathology'),
                array('key'=>'general',    'name'=>$this->lang->line('general'),    'icon'=>'fas fa-money-bill-wave',    'ic_class'=>'green',  'value'=>$general_income,    'module'=>'income'),
                array('key'=>'blood_bank', 'name'=>$this->lang->line('blood_bank'), 'icon'=>'fas fa-tint',               'ic_class'=>'red',    'value'=>$blood_bank_income, 'module'=>'blood_bank'),
                array('key'=>'ambulance',  'name'=>$this->lang->line('ambulance'),  'icon'=>'fas fa-ambulance',          'ic_class'=>'teal',   'value'=>$ambulance_income,  'module'=>'ambulance'),
            );
            $total = 0;
            foreach ($income_modules as &$m) {
                $m['value_num'] = (float)str_replace(array(',',$currency_symbol,' '), '', (string)$m['value']);
                $total += $m['value_num'];
            }
            unset($m);
            foreach ($income_modules as &$m) {
                $m['share'] = $total > 0 ? round(($m['value_num'] / $total) * 100, 1) : 0;
            }
            unset($m);
            $module_income_breakdown = $income_modules;
        }
        $total_month_income = 0;
        foreach ($module_income_breakdown as $m) { $total_month_income += (float)($m['value_num'] ?? 0); }
        ?>
        <div class="dash-table-card-h">
            <h3><?php echo $this->lang->line('income_by_module'); ?> · <?php echo date('M Y'); ?></h3>
            <span class="sub"><?php echo $currency_symbol; ?> <?php echo number_format($total_month_income, 0); ?> <?php echo $this->lang->line('total'); ?></span>
        </div>
        <table class="dash-tbl">
            <thead><tr><th><?php echo $this->lang->line('module'); ?></th><th class="num"><?php echo $this->lang->line('income'); ?></th><th class="num"><?php echo $this->lang->line('share'); ?></th><th class="num"><?php echo $this->lang->line('last_month'); ?></th></tr></thead>
            <tbody>
                <?php foreach ($module_income_breakdown as $m):
                    if (!$this->module_lib->hasActive($m['module'])) continue;
                    $lm_val = (float)($module_income_last[$m['key']] ?? 0);
                ?>
                    <tr>
                        <td><span class="ic <?php echo html_escape($m['ic_class']); ?>"><i class="<?php echo html_escape($m['icon']); ?>"></i></span><strong><?php echo html_escape($m['name']); ?></strong></td>
                        <td class="num"><?php echo $currency_symbol; ?> <?php echo $m['value']; ?></td>
                        <td class="num"><?php echo $m['share']; ?>%</td>
                        <td class="num"><?php echo $currency_symbol; ?> <?php echo number_format($lm_val, 0); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($this->module_lib->hasActive('expense') && isset($expences)): ?>
                    <tr>
                        <td><span class="ic red"><i class="fab fa-creative-commons-nc"></i></span><strong><?php echo $this->lang->line('expenses'); ?></strong></td>
                        <td class="num neg bold">−<?php echo $currency_symbol; ?> <?php echo number_format((float)$expences, 0); ?></td>
                        <td class="num"><span class="badge-pill muted">—</span></td>
                        <td class="num neg">−<?php echo $currency_symbol; ?> <?php echo number_format($expences_last, 0); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- ═══════════ Chart.js scripts (yearly line + monthly doughnut) ═══════════ -->
<script src="<?php echo base_url() ?>backend/js/Chart.bundle.js"></script>
<script src="<?php echo base_url() ?>backend/js/utils.js"></script>

<script type="text/javascript">
(function() {
    function tok(name) {
        return getComputedStyle(document.body).getPropertyValue('--' + name).trim();
    }

    var yearly_collection = <?php echo json_encode($yearly_collection); ?>;
    var yearly_expense    = <?php echo json_encode($yearly_expense); ?>;
    var MONTHS            = <?php echo json_encode($total_month); ?>;

    <?php if ($this->rbac->hasPrivilege('yearly_income_expense_chart', 'can_view')) { ?>
    var lineEl = document.getElementById('lineChart');
    if (lineEl) {
        var lineCtx = lineEl.getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [
                    <?php if ($this->module_lib->hasActive('income')) { ?>
                    {
                        label: '<?php echo $this->lang->line('income'); ?>',
                        data: yearly_collection,
                        borderColor: tok('green'),
                        backgroundColor: tok('green') + '22',
                        fill: true,
                        tension: 0.32,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: tok('green'),
                        pointBorderColor: tok('surface'),
                        pointBorderWidth: 1.5,
                        borderWidth: 2.5
                    },
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('expense')) { ?>
                    {
                        label: '<?php echo $this->lang->line('expenses'); ?>',
                        data: yearly_expense,
                        borderColor: tok('red'),
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.32,
                        borderDash: [5, 4],
                        pointRadius: 2.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: tok('red'),
                        pointBorderColor: tok('surface'),
                        pointBorderWidth: 1.5,
                        borderWidth: 2
                    }
                    <?php } ?>
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: { mode: 'index', intersect: false },
                hover: { mode: 'nearest', intersect: true },
                legend: { position: 'bottom', labels: { fontColor: tok('ink-2'), fontSize: 12 } },
                scales: {
                    xAxes: [{
                        gridLines: { color: tok('border-light'), zeroLineColor: tok('border') },
                        ticks: { fontColor: tok('muted'), fontSize: 11 }
                    }],
                    yAxes: [{
                        gridLines: { color: tok('border-light'), zeroLineColor: tok('border') },
                        ticks: {
                            fontColor: tok('muted'),
                            fontSize: 11,
                            beginAtZero: true,
                            callback: function(v) { return '<?php echo $currency_symbol; ?> ' + (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v); }
                        }
                    }]
                }
            }
        });
    }
    <?php } ?>

    <?php if ($this->rbac->hasPrivilege('monthly_income_expense_chart', 'can_view')) { ?>
    var pieEl = document.getElementById('pieChart');
    if (pieEl) {
        var pieCtx = pieEl.getContext('2d');
        var jsonarr = <?php echo json_encode($jsonarr); ?>;
        var palette = [tok('blue'), tok('green'), tok('teal'), tok('amber'), tok('violet'), tok('purple'), tok('red'), tok('orange')];
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: (jsonarr && jsonarr.label) || [],
                datasets: [{
                    data: (jsonarr && jsonarr.value) || [],
                    backgroundColor: palette,
                    borderColor: tok('surface'),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 64,
                legend: { display: false },
                animation: { animateScale: true, animateRotate: true }
            }
        });
    }
    <?php } ?>
})();
</script>

<script type="text/javascript">
$(document).ready(function () {
    if (window.bootstrap && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
            new bootstrap.Tooltip(el);
        });
    }

    $(document).on('click', '.close_notice', function () {
        var data = $(this).data();
        $.ajax({
            type: "POST",
            url: base_url + "admin/notification/read",
            data: { 'notice': data.noticeid },
            dataType: "json",
            success: function (data) {
                if (data.status == "fail") { errorMsg(data.msg); }
                else { successMsg(data.msg); }
            }
        });
    });
});
</script>

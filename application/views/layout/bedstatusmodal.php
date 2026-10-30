<?php
$bsm_total     = 0;
$bsm_available = 0;
$bsm_occupied  = 0;
$bsm_unused    = 0;
foreach ($bedlist as $b) {
    $bsm_total++;
    if ($b['is_active'] === 'yes')    $bsm_available++;
    elseif ($b['is_active'] === 'no') $bsm_occupied++;
    else                               $bsm_unused++;
}
// Capacity excludes 'unused' beds — same formula as dashboard getBedOccupancyStats().
$bsm_capacity = $bsm_available + $bsm_occupied;
$bsm_occ_pct  = $bsm_capacity > 0 ? round(($bsm_occupied / $bsm_capacity) * 100) : 0;
$bsm_dash     = $bsm_occ_pct . ' ' . (100 - $bsm_occ_pct);
?>

<!-- Summary strip -->
<div class="bsm-summary">

    <div class="bsm-stat">
        <div class="bsm-stat-ic total"><i class="fas fa-bed"></i></div>
        <div class="bsm-stat-text">
            <span class="bsm-count"><?php echo $bsm_total; ?></span>
            <span class="bsm-label"><?php echo $this->lang->line('total'); ?></span>
        </div>
    </div>

    <div class="bsm-stat">
        <div class="bsm-stat-ic avail"><i class="fas fa-check-circle"></i></div>
        <div class="bsm-stat-text">
            <span class="bsm-count bsm-count-green"><?php echo $bsm_available; ?></span>
            <span class="bsm-label"><?php echo $this->lang->line('available'); ?></span>
        </div>
    </div>

    <div class="bsm-stat">
        <div class="bsm-stat-ic occup"><i class="fas fa-user"></i></div>
        <div class="bsm-stat-text">
            <span class="bsm-count bsm-count-red"><?php echo $bsm_occupied; ?></span>
            <span class="bsm-label">Occupied</span>
        </div>
    </div>

    <div class="bsm-stat">
        <div class="bsm-stat-ic unused"><i class="fas fa-minus-circle"></i></div>
        <div class="bsm-stat-text">
            <span class="bsm-count"><?php echo $bsm_unused; ?></span>
            <span class="bsm-label"><?php echo $this->lang->line('unused'); ?></span>
        </div>
    </div>

    <?php if ($bsm_total > 0): ?>
    <div class="bsm-gauge">
        <svg width="52" height="52" viewBox="0 0 36 36" aria-hidden="true">
            <circle class="bsm-gauge-ring-bg"   cx="18" cy="18" r="15.9"/>
            <circle class="bsm-gauge-ring-fill" cx="18" cy="18" r="15.9"
                    style="stroke-dasharray:<?php echo $bsm_dash; ?>"/>
        </svg>
        <div class="bsm-gauge-info">
            <span class="bsm-gauge-pct"><?php echo $bsm_occ_pct; ?>%</span>
            <span class="bsm-gauge-lbl">Occupied</span>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.bsm-summary -->

<!-- Filter bar -->
<div class="bsm-filterbar">
    <div class="bsm-search">
        <i class="fas fa-search"></i>
        <input type="text" id="bsm-search-input"
               placeholder="<?php echo $this->lang->line('search'); ?>…">
    </div>
    <div class="bsm-fbtns">
        <button class="bsm-fbtn f-all" data-filter="all">
            <?php echo $this->lang->line('all'); ?>
            <span class="bsm-fbtn-badge"><?php echo $bsm_total; ?></span>
        </button>
        <button class="bsm-fbtn" data-filter="available">
            <?php echo $this->lang->line('available'); ?>
            <span class="bsm-fbtn-badge"><?php echo $bsm_available; ?></span>
        </button>
        <button class="bsm-fbtn" data-filter="occupied">
            Occupied
            <span class="bsm-fbtn-badge"><?php echo $bsm_occupied; ?></span>
        </button>
        <button class="bsm-fbtn" data-filter="unused">
            <?php echo $this->lang->line('unused'); ?>
            <span class="bsm-fbtn-badge"><?php echo $bsm_unused; ?></span>
        </button>
    </div>
</div>

<!-- Floor / ward / bed body -->
<div class="bsm-body">
<?php
$ward_stats = [];
foreach ($bedgroup_list as $bg) {
    $ward_stats[$bg['id']] = ['avail' => 0, 'occup' => 0, 'total' => 0];
}
foreach ($bedlist as $b) {
    $wid = $b['bedgroupid'];
    if (!isset($ward_stats[$wid])) continue;
    $ward_stats[$wid]['total']++;
    if ($b['is_active'] === 'yes')    $ward_stats[$wid]['avail']++;
    elseif ($b['is_active'] === 'no') $ward_stats[$wid]['occup']++;
}
?>
<?php foreach ($floor_list as $floor):
    $floor_wards = 0; $floor_beds = 0;
    foreach ($bedgroup_list as $bg) {
        if ($bg['fid'] != $floor['id']) continue;
        $floor_wards++;
        $floor_beds += $ward_stats[$bg['id']]['total'] ?? 0;
    }
?>
    <div class="bsm-floor">
        <div class="bsm-floor-title">
            <i class="fas fa-layer-group"></i>
            <?php echo html_escape($floor['name']); ?>
            <span class="bsm-floor-badge">
                <?php echo $floor_wards; ?> Wards &middot; <?php echo $floor_beds; ?> Beds
            </span>
        </div>
        <div class="bsm-wards">
        <?php foreach ($bedgroup_list as $bedgroup):
            if ($bedgroup['fid'] != $floor['id']) continue;
            $ws = $ward_stats[$bedgroup['id']];
        ?>
            <div class="bsm-ward">
                <div class="bsm-ward-header" style="border-left-color:<?php echo $bedgroup['color']; ?>">
                    <span class="bsm-ward-name"><?php echo html_escape($bedgroup['name']); ?></span>
                    <div class="bsm-ward-pills">
                        <span class="bsm-ward-pill pa">
                            <i class="fas fa-check"></i><?php echo $ws['avail']; ?>
                        </span>
                        <span class="bsm-ward-pill po">
                            <i class="fas fa-user"></i><?php echo $ws['occup']; ?>
                        </span>
                        <span class="bsm-ward-pill pt"><?php echo $ws['total']; ?></span>
                    </div>
                </div>
                <div class="bsm-beds">
                <?php foreach ($bedlist as $bed):
                    if ($bed['bedgroupid'] != $bedgroup['id']) continue;

                    if ($bed['is_active'] === 'no' && $bed['pid']):
                        $popup_html = '<strong>' . html_escape($bed['patient_name']) . '</strong><hr style="margin:4px 0">'
                            . '<small>'
                            . '<b>' . $this->lang->line('patient_id')   . ':</b> ' . html_escape($bed['patient_unique_id']) . '<br>'
                            . '<b>' . $this->lang->line('phone')         . ':</b> ' . html_escape($bed['mobileno'])          . '<br>'
                            . '<b>' . $this->lang->line('gender')        . ':</b> ' . $this->lang->line(strtolower($bed['gender'])) . '<br>'
                            . '<b>' . $this->lang->line('guardian_name') . ':</b> ' . html_escape($bed['guardian_name'])     . '<br>'
                            . '<b>' . $this->lang->line('consultant')    . ':</b> ' . html_escape($bed['staff'] . ' ' . $bed['surname'] . ' (' . $bed['employee_id'] . ')')
                            . '</small>';
                ?>
                    <a href="<?php echo base_url('admin/patient/ipdprofile/' . $bed['ipd_details_id']); ?>"
                       class="bsm-bed bsm-occupied"
                       data-bs-toggle="popover"
                       data-bs-trigger="hover focus"
                       data-bs-placement="top"
                       data-bs-html="true"
                       data-bs-content="<?php echo htmlspecialchars($popup_html, ENT_QUOTES); ?>">
                        <i class="fas fa-bed"></i>
                        <span class="bsm-bed-no"><?php echo html_escape($bed['name']); ?></span>
                        <span class="bsm-patient"><?php echo html_escape($bed['patient_name']); ?></span>
                    </a>

                <?php elseif ($bed['is_active'] === 'yes'): ?>
                    <a href="<?php echo base_url('admin/patient/ipdsearch/' . $bed['id'] . '/' . $bedgroup['id']); ?>"
                       class="bsm-bed bsm-available"
                       data-bs-toggle="tooltip"
                       data-bs-title="<?php echo $this->lang->line('available'); ?>">
                        <i class="fas fa-bed"></i>
                        <span class="bsm-bed-no"><?php echo html_escape($bed['name']); ?></span>
                        <span class="bsm-patient">&nbsp;</span>
                    </a>

                <?php else: ?>
                    <span class="bsm-bed bsm-unused"
                          data-bs-toggle="tooltip"
                          data-bs-title="<?php echo $this->lang->line('unused'); ?>">
                        <i class="fas fa-bed"></i>
                        <span class="bsm-bed-no"><?php echo html_escape($bed['name']); ?></span>
                        <span class="bsm-patient">&nbsp;</span>
                    </span>
                <?php endif; ?>
                <?php endforeach; ?>
                </div><!-- /.bsm-beds -->
            </div><!-- /.bsm-ward -->
        <?php endforeach; ?>
        </div><!-- /.bsm-wards -->
    </div><!-- /.bsm-floor -->
<?php endforeach; ?>
</div><!-- /.bsm-body -->

<!-- Legend footer -->
<div class="bsm-legend">
    <span class="bsm-legend-item">
        <span class="bsm-legend-dot la"></span>
        <?php echo $this->lang->line('available'); ?> &mdash; click to admit
    </span>
    <span class="bsm-legend-item">
        <span class="bsm-legend-dot lo"></span>
        Occupied &mdash; click to view IPD profile
    </span>
    <span class="bsm-legend-item">
        <span class="bsm-legend-dot lu"></span>
        <?php echo $this->lang->line('unused'); ?>
    </span>
    <span class="bsm-legend-tip">
        <i class="fas fa-info-circle me-1"></i>Hover occupied bed for patient details
    </span>
</div>

<script>
(function () {
    var ctx = document.getElementById('bed-status-body');
    if (!ctx) return;

    ctx.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el, { container: '#bedStatusModal' });
    });
    ctx.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el, { container: '#bedStatusModal' });
    });

    ctx.querySelectorAll('.bsm-fbtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            ctx.querySelectorAll('.bsm-fbtn').forEach(function (b) {
                b.classList.remove('f-all', 'f-avail', 'f-occup', 'f-unused');
            });
            var f = this.dataset.filter;
            if      (f === 'all')       this.classList.add('f-all');
            else if (f === 'available') this.classList.add('f-avail');
            else if (f === 'occupied')  this.classList.add('f-occup');
            else if (f === 'unused')    this.classList.add('f-unused');

            ctx.querySelectorAll('.bsm-bed').forEach(function (bed) {
                if (f === 'all') {
                    bed.style.display = '';
                } else if (f === 'available') {
                    bed.style.display = bed.classList.contains('bsm-available') ? '' : 'none';
                } else if (f === 'occupied') {
                    bed.style.display = bed.classList.contains('bsm-occupied') ? '' : 'none';
                } else if (f === 'unused') {
                    bed.style.display = bed.classList.contains('bsm-unused') ? '' : 'none';
                }
            });
        });
    });

    var searchInput = ctx.querySelector('#bsm-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase().trim();
            ctx.querySelectorAll('.bsm-bed').forEach(function (bed) {
                var text = bed.textContent.toLowerCase();
                bed.style.display = (!q || text.indexOf(q) !== -1) ? '' : 'none';
            });
        });
    }
})();
</script>

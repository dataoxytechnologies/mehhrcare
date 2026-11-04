<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="card-title mb-0">Stage Fee Template</h3>
            <small class="text-muted">Define standard amounts per Programme Group × Coverage × Stage. Billing uses these to auto-calculate payable amounts.</small>
        </div>
        <button type="button" class="btn btn-sm btn-primary" onclick="openSftModal(0)"><i class="fa fa-plus"></i> Add Entry</button>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('sft_success')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('sft_success'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('sft_error')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('sft_error'); ?></div>
        <?php } ?>
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Programme Group</th>
                    <th>Coverage</th>
                    <th>Stage</th>
                    <th>Quarter</th>
                    <th>Duration (days)</th>
                    <th>Standard Amount (₹)</th>
                    <th>Effective From</th>
                    <th>Effective To</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sft_allocations)): ?>
                <tr><td colspan="9" class="text-center text-muted">No stage fee entries yet. Add entries to enable automatic amount lookup during billing.</td></tr>
                <?php else: foreach ($sft_allocations as $a): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($a->group_code ?? '-'); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($a->group_name ?? ''); ?></small></td>
                    <td>
                        <?php
                        $cov_labels = ['ONE_PARTNER' => 'One Partner', 'BOTH_PARTNERS' => 'Both Partners', 'PER_PATIENT' => 'Per Patient'];
                        echo htmlspecialchars($cov_labels[$a->coverage] ?? $a->coverage ?? '-');
                        ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($a->stage_code ?? '-'); ?></strong></td>
                    <td><?php echo htmlspecialchars($a->quarter ?? '-'); ?></td>
                    <td><?php echo (int)($a->duration_days ?? 0); ?> days</td>
                    <td class="fw-semibold">₹<?php echo number_format((float)($a->standard_amount ?? 0), 2); ?></td>
                    <td><?php echo $a->effective_from ?? '-'; ?></td>
                    <td><?php echo $a->effective_to ? $a->effective_to : '<span class="text-muted">Open</span>'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSftModal(<?php echo (int)$a->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSftEntry(<?php echo (int)$a->id; ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stage Fee Template Modal -->
<div class="modal fade" id="sftModal" tabindex="-1" aria-labelledby="sftModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="sftForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="sftModalTitle" style="padding-top:12px;padding-bottom:0;">Add Stage Fee Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="sft_id" value="0">

                    <div class="alert alert-info py-2 mb-3" style="font-size:.82rem;">
                        <i class="fa fa-info-circle me-1"></i>
                        Each entry maps a <strong>Programme Group + Coverage + Stage</strong> to a <strong>Standard Amount</strong>.
                        The billing engine reads this directly — no manual entry per patient needed.
                    </div>

                    <div class="row form-row mb-3">
                        <div class="form-group col-md-6">
                            <label class="form-label">Programme Group <span class="text-danger">*</span></label>
                            <select name="group_id" id="sft_group_id" class="form-control" required onchange="sftFilterCoverage()">
                                <option value="">— Select Group —</option>
                                <?php if (!empty($sft_groups)): foreach ($sft_groups as $g): ?>
                                <option value="<?php echo (int)$g->id; ?>"
                                    data-code="<?php echo htmlspecialchars($g->code); ?>"
                                    data-model="<?php echo htmlspecialchars($g->coverage_model); ?>">
                                    <?php echo htmlspecialchars($g->code . ' — ' . $g->name); ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Coverage <span class="text-danger">*</span></label>
                            <select name="coverage" id="sft_coverage" class="form-control" required>
                                <option value="">— Select Group first —</option>
                                <option value="ONE_PARTNER" class="opt-partner">One Partner</option>
                                <option value="BOTH_PARTNERS" class="opt-partner">Both Partners</option>
                                <option value="PER_PATIENT" class="opt-per-patient">Per Patient</option>
                            </select>
                            <div class="form-text" id="sft_coverage_hint"></div>
                        </div>
                    </div>

                    <div class="row form-row mb-3">
                        <div class="form-group col-md-6">
                            <label class="form-label">Stage <span class="text-danger">*</span></label>
                            <select name="stage_id" id="sft_stage_id" class="form-control" required onchange="sftUpdateStageMeta()">
                                <option value="">— Select Stage —</option>
                                <?php if (!empty($sft_stages)): foreach ($sft_stages as $st): ?>
                                <option value="<?php echo (int)$st->id; ?>"
                                    data-code="<?php echo htmlspecialchars($st->code); ?>"
                                    data-quarter="<?php echo htmlspecialchars($st->quarter); ?>"
                                    data-days="<?php echo (int)$st->duration_days; ?>">
                                    <?php echo htmlspecialchars($st->code . ' (' . $st->quarter . ' · ' . $st->duration_days . ' days)'); ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Standard Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" name="standard_amount" id="sft_standard_amount" class="form-control" required placeholder="e.g. 6000">
                            </div>
                        </div>
                    </div>

                    <!-- Stage meta preview -->
                    <div class="alert alert-secondary py-2 mb-3" id="sft_stage_preview" style="display:none;font-size:.82rem;">
                        Stage: <strong id="sft_prev_code">—</strong> &nbsp;|&nbsp;
                        Quarter: <strong id="sft_prev_quarter">—</strong> &nbsp;|&nbsp;
                        Duration: <strong id="sft_prev_days">—</strong> days
                    </div>

                    <div class="row form-row mb-2">
                        <div class="form-group col-md-6">
                            <label class="form-label">Effective From <span class="text-danger">*</span></label>
                            <input type="date" name="effective_from" id="sft_effective_from" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Effective To <span class="text-muted">(leave blank = open-ended)</span></label>
                            <input type="date" name="effective_to" id="sft_effective_to" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var sftRows  = <?php echo json_encode(isset($sft_allocations) ? $sft_allocations : []); ?>;
var sftGroups = <?php echo json_encode(isset($sft_groups)     ? $sft_groups      : []); ?>;

function sftGetModal() {
    var el = document.getElementById('sftModal');
    if (window.bootstrap && bootstrap.Modal) return bootstrap.Modal.getOrCreateInstance(el);
    return null;
}
function showSftModal() {
    var m = sftGetModal();
    if (m) { m.show(); return; }
    if (window.jQuery && $('#sftModal').modal) { $('#sftModal').modal('show'); }
}
function hideSftModal() {
    var el = document.getElementById('sftModal');
    if (window.bootstrap && bootstrap.Modal) {
        var inst = bootstrap.Modal.getInstance(el);
        if (inst) inst.hide(); else { el.classList.remove('show'); el.style.display = 'none'; document.body.classList.remove('modal-open'); document.querySelectorAll('.modal-backdrop').forEach(function(b){b.remove();}); }
    } else if (window.jQuery && $('#sftModal').modal) { $('#sftModal').modal('hide'); }
}

function sftFilterCoverage(selectedValue) {
    var sel = document.getElementById('sft_group_id');
    var opt = sel.options[sel.selectedIndex];
    var model = opt ? opt.getAttribute('data-model') : '';
    var covSel = document.getElementById('sft_coverage');
    var hint = document.getElementById('sft_coverage_hint');
    var partnerOpts = covSel.querySelectorAll('.opt-partner');
    var perPatOpts  = covSel.querySelectorAll('.opt-per-patient');
    if (model === 'PARTNER_BASED') {
        partnerOpts.forEach(function(o){ o.style.display = ''; });
        perPatOpts.forEach(function(o){ o.style.display = 'none'; });
        if (!selectedValue) { covSel.value = 'ONE_PARTNER'; }
        hint.textContent = 'Partner-Based group: One Partner or Both Partners.';
    } else if (model === 'PER_PATIENT') {
        partnerOpts.forEach(function(o){ o.style.display = 'none'; });
        perPatOpts.forEach(function(o){ o.style.display = ''; });
        if (!selectedValue) { covSel.value = 'PER_PATIENT'; }
        hint.textContent = 'Per-Patient group: each patient enrolled individually.';
    } else {
        partnerOpts.forEach(function(o){ o.style.display = ''; });
        perPatOpts.forEach(function(o){ o.style.display = ''; });
        hint.textContent = '';
    }
    if (selectedValue) { covSel.value = selectedValue; }
}

function sftUpdateStageMeta() {
    var sel = document.getElementById('sft_stage_id');
    var opt = sel.options[sel.selectedIndex];
    var preview = document.getElementById('sft_stage_preview');
    if (opt && opt.value) {
        document.getElementById('sft_prev_code').textContent    = opt.getAttribute('data-code') || '—';
        document.getElementById('sft_prev_quarter').textContent = opt.getAttribute('data-quarter') || '—';
        document.getElementById('sft_prev_days').textContent    = opt.getAttribute('data-days') || '—';
        preview.style.display = '';
    } else {
        preview.style.display = 'none';
    }
}

function openSftModal(id) {
    document.getElementById('sft_id').value = 0;
    document.getElementById('sftModalTitle').textContent = 'Add Stage Fee Entry';
    document.getElementById('sftForm').reset();
    document.getElementById('sft_effective_from').value = '<?php echo date('Y-m-d'); ?>';
    document.getElementById('sft_stage_preview').style.display = 'none';
    document.getElementById('sft_coverage_hint').textContent = '';

    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_stage_fee_allocation'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                document.getElementById('sft_id').value = d.id;
                document.getElementById('sft_group_id').value = d.group_id;
                sftFilterCoverage(d.coverage);
                document.getElementById('sft_stage_id').value = d.stage_id;
                sftUpdateStageMeta();
                document.getElementById('sft_standard_amount').value = d.standard_amount;
                document.getElementById('sft_effective_from').value = d.effective_from || '';
                document.getElementById('sft_effective_to').value = d.effective_to || '';
                document.getElementById('sftModalTitle').textContent = 'Edit Stage Fee Entry';
                showSftModal();
            } else { alert(res.message); }
        }, 'json');
    } else {
        showSftModal();
    }
}

$('#sftForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_stage_fee_allocation'); ?>', $(this).serialize(), function(res) {
        if (res.status) { hideSftModal(); location.reload(); }
        else { alert(res.message); }
    }, 'json');
});

function deleteSftEntry(id) {
    if (!confirm('Delete this stage fee entry? This will affect billing calculations for this group/coverage/stage.')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_stage_fee_allocation'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}
</script>


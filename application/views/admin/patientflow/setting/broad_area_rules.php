<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Broad Area Rules</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openBroadRuleModal(0)"><i class="fa fa-plus"></i> Add Rule</button>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Concern Pattern</th>
                    <th>Applicability</th>
                    <th>Series / Result</th>
                    <th>Status</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($broad_rules)) { ?>
                <tr><td colspan="5" class="text-center text-muted">No broad area rules yet.</td></tr>
                <?php } else { foreach ($broad_rules as $br) {
                    $results = json_decode((string) $br->series_result, true);
                    if (!is_array($results)) { $results = array(); }
                    $badges = '';
                    foreach ($results as $r) {
                        $badgeClass = ($r === 'Review Required') ? 'badge bg-warning text-dark' : 'badge bg-info';
                        $badges .= '<span class="' . $badgeClass . ' me-1 mb-1">' . htmlspecialchars((string) $r) . '</span>';
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars((string) $br->concern_pattern); ?></td>
                    <td><?php echo htmlspecialchars((string) $br->applicability); ?></td>
                    <td><?php echo $badges !== '' ? $badges : '<span class="text-muted">—</span>'; ?></td>
                    <td><?php echo ((int) $br->is_active === 1) ? 'Active' : 'Inactive'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openBroadRuleModal(<?php echo (int) $br->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_broad_rule/' . (int) $br->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
<div class="modal fade" id="broadRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_broad_rule'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="broadRuleModalTitle">Add Broad Area Rule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_br_id" value="">
                <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Concern Pattern <small class="req">*</small></label>
                    <select name="concern_pattern[]" id="pf_br_pattern" class="form-control select2 w-100" multiple required>
                        <option value="Internal organs">Internal organs</option>
                        <option value="Liver">Liver</option>
                        <option value="Kidney">Kidney</option>
                        <option value="Urinary">Urinary</option>
                        <option value="Digestive">Digestive</option>
                        <option value="Metabolic">Metabolic</option>
                        <option value="Spine">Spine</option>
                        <option value="Bone">Bone</option>
                        <option value="Joint">Joint</option>
                        <option value="Muscle">Muscle</option>
                        <option value="Tendon">Tendon</option>
                        <option value="Ligament">Ligament</option>
                        <option value="Weak tissue">Weak tissue</option>
                        <option value="Bonding">Bonding</option>
                        <option value="Connective tissue">Connective tissue</option>
                        <option value="Repair">Repair</option>
                        <option value="Laxity">Laxity</option>
                        <option value="Uterus">Uterus</option>
                        <option value="Ovary">Ovary</option>
                        <option value="Cervix">Cervix</option>
                        <option value="Facial">Facial</option>
                        <option value="Unclear classification">Unclear classification</option>
                    </select>
                    <div class="form-text">Select one or more concern patterns. Stored as comma-separated values.</div>
                </div>
                    <div class="col-12">
                        <label class="form-label">Applicability <small class="req">*</small></label>
                        <select name="applicability" id="pf_br_applicability" class="form-select" required>
                            <option value="Male/general">Male / general</option>
                            <option value="Female">Female</option>
                            <option value="Female only">Female only</option>
                            <option value="Any">Any</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Series / Result <small class="req">*</small>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="pfBrAddResult()"><i class="fa fa-plus"></i> Add result</button>
                    </label>
                    <div id="pf_br_results_container" class="border rounded p-2 mb-2">
                        <!-- result rows injected here -->
                    </div>
                    <div class="form-text">Add one or more series codes (e.g. LKO-1, LKO-2) or "Review Required". Stored as JSON array.</div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Status</label>
                        <select name="is_active" id="pf_br_active" class="form-select">
                            <option value="1">Active</option><option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Rule</button>
            </div>
        </form>
    </div></div>
</div>
<script type="text/javascript">
var pfBroadRules = <?php echo json_encode(isset($broad_rules) ? $broad_rules : array()); ?>;
var pfBroadSeries = <?php echo json_encode(isset($broad_series) ? $broad_series : array()); ?>;

function pfBrResultHtml(resultValue) {
    var opts = '';
    for (var i = 0; i < pfBroadSeries.length; i++) {
        var s = pfBroadSeries[i];
        var sel = (String(s.code) === String(resultValue)) ? ' selected' : '';
        opts += '<option value="' + String(s.code) + '"' + sel + '>' + String(s.code) + '</option>';
    }
    var selRR = (String(resultValue) === 'Review Required') ? ' selected' : '';
    opts += '<option value="Review Required"' + selRR + '>Review Required</option>';
    return '<div class="row g-2 br-result-row align-items-end">' +
        '<div class="col-auto"><select name="results[]" class="form-select form-select-sm" required>' + opts + '</select></div>' +
        '<div class="col-auto"><button type="button" class="btn btn-outline-danger btn-sm" onclick="pfBrRemoveResult(this)"><i class="fa fa-trash"></i></button></div>' +
        '</div>';
}

function pfBrAddResult(preValue) {
    var container = document.getElementById('pf_br_results_container');
    var div = document.createElement('div');
    div.innerHTML = pfBrResultHtml(preValue || '');
    container.appendChild(div);
}

function pfBrRemoveResult(btn) {
    var row = btn.closest('.br-result-row');
    if (row) { row.remove(); }
    var container = document.getElementById('pf_br_results_container');
    if (container.children.length === 0) {
        pfBrAddResult();
    }
}

function openBroadRuleModal(id) {
    document.getElementById('pf_br_id').value = id ? id : '';
    document.getElementById('broadRuleModalTitle').textContent = id ? 'Edit Broad Area Rule' : 'Add Broad Area Rule';
    document.getElementById('pf_br_pattern').value = '';
    document.getElementById('pf_br_applicability').value = 'Male/general';
    document.getElementById('pf_br_active').value = '1';
    var container = document.getElementById('pf_br_results_container');
    container.innerHTML = '';

    if (id) {
        var row = null;
        for (var i = 0; i < pfBroadRules.length; i++) { if (String(pfBroadRules[i].id) === String(id)) { row = pfBroadRules[i]; break; } }
        if (row) {
            document.getElementById('pf_br_pattern').value = row.concern_pattern || '';
            var patternValues = [];
            var parts = String(row.concern_pattern || '').split(',');
            for (var p = 0; p < parts.length; p++) {
                var pv = parts[p].trim();
                if (pv !== '') { patternValues.push(pv); }
            }
            $('#pf_br_pattern').val(patternValues).trigger('change');
            document.getElementById('pf_br_applicability').value = row.applicability || 'Male/general';
            document.getElementById('pf_br_active').value = String(row.is_active);
            var results = [];
            try { results = JSON.parse(row.series_result || '[]'); } catch (e) { results = []; }
            if (!Array.isArray(results) || results.length === 0) { results = ['']; }
            for (var i = 0; i < results.length; i++) {
                pfBrAddResult(results[i]);
            }
        }
    } else {
        $('#pf_br_pattern').val([]).trigger('change');
        pfBrAddResult();
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('broadRuleModal')).show();
}

// Initialize Select2 for concern pattern multi-select
$(document).ready(function() {
    $('#pf_br_pattern').select2({
        placeholder: 'Select concern pattern(s)',
        allowClear: true,
        width: '100%'
    });
});
</script>
</div>
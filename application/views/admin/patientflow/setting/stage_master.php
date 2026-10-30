<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Stage Master</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openStageModal(0)"><i class="fa fa-plus"></i> Add Stage</button>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>
        <table class="table table-striped table-bordered table-hover example">
            <thead><tr><th>Stage Code</th><th>Quarter</th><th>Stage</th><th>Duration (days)</th><th>Operational rule</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (empty($stages)) { ?>
                <tr><td colspan="7" class="text-center text-muted">No stages yet.</td></tr>
                <?php } else { foreach ($stages as $st) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($st->code); ?></strong></td>
                    <td><?php echo htmlspecialchars($st->quarter); ?></td>
                    <td><?php echo htmlspecialchars($st->stage); ?></td>
                    <td><?php echo (int) $st->duration_days; ?></td>
                    <td><?php echo htmlspecialchars((string) $st->operational_rule); ?></td>
                    <td><?php echo ((int) $st->is_active === 1) ? 'Active' : 'Inactive'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openStageModal(<?php echo (int) $st->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_stage/' . (int) $st->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="stageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_stage'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="stageModalTitle">Add Stage</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_stage_id" value="">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Quarter <small class="req">*</small></label>
                    <select name="quarter" id="pf_stage_quarter" class="form-select" required onchange="pfStagePreview()">
                        <?php for ($q = 1; $q <= 10; $q++) { ?>
                        <option value="Q<?php echo $q; ?>">Q<?php echo $q; ?></option>
                        <?php } ?>
                    </select></div>
                    <div class="col-md-6"><label class="form-label">Stage <small class="req">*</small></label>
                    <select name="stage" id="pf_stage_stage" class="form-select" required onchange="pfStagePreview()">
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="S4">S4</option>
                    </select></div>
                </div>
                <div class="alert alert-info py-2 my-2">Code (auto): <strong id="pf_stage_preview">-</strong></div>
                <div class="mb-2"><label class="form-label">Duration (days) <small class="req">*</small></label>
                <input type="number" name="duration_days" id="pf_stage_days" class="form-control" required min="1" value="30"></div>
                <div class="mb-2"><label class="form-label">Operational rule</label>
                <input type="text" name="operational_rule" id="pf_stage_rule" class="form-control" maxlength="255"></div>
                <div class="mb-2"><label class="form-label">Status</label>
                <select name="is_active" id="pf_stage_active" class="form-select">
                    <option value="1">Active</option><option value="0">Inactive</option>
                </select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>
<script type="text/javascript">
var pfStages = <?php echo json_encode(isset($stages) ? $stages : array()); ?>;
function pfStagePreview() {
    var q = document.getElementById('pf_stage_quarter').value || '';
    var s = document.getElementById('pf_stage_stage').value || '';
    document.getElementById('pf_stage_preview').textContent = (q && s) ? (q + s) : '-';
}
function openStageModal(id) {
    document.getElementById('pf_stage_id').value = id ? id : '';
    document.getElementById('stageModalTitle').textContent = id ? 'Edit Stage' : 'Add Stage';
    var row = null;
    for (var i = 0; i < pfStages.length; i++) { if (String(pfStages[i].id) === String(id)) { row = pfStages[i]; break; } }
    document.getElementById('pf_stage_quarter').value = row ? row.quarter : 'Q1';
    document.getElementById('pf_stage_stage').value = row ? row.stage : 'S1';
    document.getElementById('pf_stage_days').value = row ? row.duration_days : 30;
    document.getElementById('pf_stage_rule').value = row ? (row.operational_rule || '') : '';
    document.getElementById('pf_stage_active').value = row ? String(row.is_active) : '1';
    pfStagePreview();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('stageModal')).show();
}
</script>
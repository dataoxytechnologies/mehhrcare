<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">F-Series Mapping (Series × Stage → F Medicine)</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openFMappingModal(0)"><i class="fa fa-plus"></i> Add Mapping</button>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Filter by Series</label>
                <select id="pf_f_series_id" class="form-select" onchange="pfFMappingFilter()">
                    <option value="0">All Series</option>
                    <?php if (!empty($broad_series)) { foreach ($broad_series as $bs) { ?>
                    <option value="<?php echo (int) $bs->id; ?>"<?php echo isset($filter_series_id) && $filter_series_id === (int) $bs->id ? ' selected' : ''; ?>><?php echo htmlspecialchars($bs->code); ?></option>
                    <?php } } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Filter by Stage</label>
                <select id="pf_f_stage_id" class="form-select" onchange="pfFMappingFilter()">
                    <option value="0">All Stages</option>
                    <?php if (!empty($stages)) { foreach ($stages as $st) { ?>
                    <option value="<?php echo (int) $st->id; ?>"><?php echo htmlspecialchars($st->code . ' - ' . $st->quarter . $st->stage); ?></option>
                    <?php } } ?>
                </select>
            </div>
        </div>
        <table class="table table-striped table-bordered table-hover example" id="pf_f_mapping_table">
            <thead><tr><th>Series</th><th>Stage</th><th>F Medicine</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (empty($f_mappings)) { ?>
                <tr><td colspan="5" class="text-center text-muted">No F-series mappings yet.</td></tr>
                <?php } else { foreach ($f_mappings as $fm) { ?>
                <tr data-series-id="<?php echo (int) $fm->series_id; ?>" data-stage-id="<?php echo (int) $fm->stage_id; ?>">
                    <td><?php echo htmlspecialchars($fm->scode); ?></td>
                    <td><?php echo htmlspecialchars($fm->stcode); ?></td>
                    <td><strong><?php echo htmlspecialchars($fm->mcode); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($fm->mident); ?></small></td>
                    <td><?php echo htmlspecialchars($fm->status); ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFMappingModal(<?php echo (int) $fm->id; ?>, <?php echo (int) $fm->series_id; ?>, <?php echo (int) $fm->stage_id; ?>, <?php echo (int) $fm->medicine_id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_f_mapping/' . (int) $fm->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="fMappingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_f_mapping'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="fMappingModalTitle">Add F-Series Mapping</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_fmap_id" value="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Series <small class="req">*</small></label>
                        <select name="series_id" id="pf_fmap_series_id" class="form-select" required>
                            <option value="">Select Series</option>
                            <?php if (!empty($broad_series)) { foreach ($broad_series as $bs) { ?>
                            <option value="<?php echo (int) $bs->id; ?>"><?php echo htmlspecialchars($bs->code); ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stage <small class="req">*</small></label>
                        <select name="stage_id" id="pf_fmap_stage_id" class="form-select" required>
                            <option value="">Select Stage</option>
                            <?php if (!empty($stages)) { foreach ($stages as $st) { ?>
                            <option value="<?php echo (int) $st->id; ?>"><?php echo htmlspecialchars($st->code . ' - ' . $st->quarter . $st->stage); ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">F Medicine <small class="req">*</small></label>
                        <select name="medicine_id" id="pf_fmap_medicine_id" class="form-select" required>
                            <option value="">Select F Medicine</option>
                            <?php if (!empty($f_medicines)) { foreach ($f_medicines as $fm) { ?>
                            <option value="<?php echo (int) $fm->id; ?>"><?php echo htmlspecialchars($fm->code . ' - ' . $fm->medicine_identity); ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Mapping</button>
            </div>
        </form>
    </div></div>
</div>
<script type="text/javascript">
function pfFMappingFilter() {
    var sid = document.getElementById('pf_f_series_id').value;
    var stid = document.getElementById('pf_f_stage_id').value;
    var rows = document.querySelectorAll('#pf_f_mapping_table tbody tr[data-series-id]');
    for (var i = 0; i < rows.length; i++) {
        var show = true;
        if (sid !== '0' && rows[i].getAttribute('data-series-id') !== sid) { show = false; }
        if (stid !== '0' && rows[i].getAttribute('data-stage-id') !== stid) { show = false; }
        rows[i].style.display = show ? '' : 'none';
    }
}
function openFMappingModal(id, series_id, stage_id, medicine_id) {
    document.getElementById('pf_fmap_id').value = id ? id : '';
    document.getElementById('fMappingModalTitle').textContent = id ? 'Edit F-Series Mapping' : 'Add F-Series Mapping';
    document.getElementById('pf_fmap_series_id').value = series_id ? series_id : '';
    document.getElementById('pf_fmap_stage_id').value = stage_id ? stage_id : '';
    document.getElementById('pf_fmap_medicine_id').value = medicine_id ? medicine_id : '';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('fMappingModal')).show();
}
</script>
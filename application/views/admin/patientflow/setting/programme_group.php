<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Programme Groups</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openProgrammeGroupModal(0)"><i class="fa fa-plus"></i> Add Group</button>
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
                    <th>Code</th>
                    <th>Name</th>
                    <th>Coverage Model</th>
                    <th>Status</th>
                    <th class="text-end text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($programme_groups_list)) { ?>
                <tr><td colspan="5" class="text-center text-muted">No programme groups yet.</td></tr>
                <?php } else { foreach ($programme_groups_list as $pg) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars((string) $pg->code); ?></td>
                    <td><?php echo htmlspecialchars((string) $pg->name); ?></td>
                    <td><?php echo htmlspecialchars((string) $pg->coverage_model); ?></td>
                    <td><?php echo ((int) $pg->is_active === 1) ? 'Active' : 'Inactive'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openProgrammeGroupModal(<?php echo (int) $pg->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_programme_group/' . (int) $pg->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="programmeGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_programme_group'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="programmeGroupModalTitle">Add Programme Group</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_pg_id" value="">
                <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Code <small class="req">*</small></label>
                    <input type="text" name="code" id="pf_pg_code" class="form-control" placeholder="e.g. HFT" required>
                    <div class="form-text">Unique code for the programme group (e.g. HFT, SBM).</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name <small class="req">*</small></label>
                    <input type="text" name="name" id="pf_pg_name" class="form-control" placeholder="e.g. Homeopathic Fertility Therapy" required>
                </div>
                </div>
                <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Coverage Model <small class="req">*</small></label>
                    <select name="coverage_model" id="pf_pg_coverage_model" class="form-select" required>
                        <option value="PARTNER_BASED">Partner Based (two patients share one programme)</option>
                        <option value="PER_PATIENT">Per Patient (each patient enrolled individually)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="pf_pg_is_active" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Group</button>
            </div>
        </form>
    </div></div>
</div>

<script type="text/javascript">
var pgGroups = <?php echo json_encode(isset($programme_groups) ? $programme_groups : array()); ?>;

function openProgrammeGroupModal(id) {
    document.getElementById('pf_pg_id').value = id ? id : '';
    document.getElementById('programmeGroupModalTitle').textContent = id ? 'Edit Programme Group' : 'Add Programme Group';
    document.getElementById('pf_pg_code').value = '';
    document.getElementById('pf_pg_name').value = '';
    document.getElementById('pf_pg_coverage_model').value = 'PARTNER_BASED';
    document.getElementById('pf_pg_is_active').value = '1';

    if (id) {
        var row = null;
        for (var i = 0; i < pgGroups.length; i++) {
            if (String(pgGroups[i].id) === String(id)) { row = pgGroups[i]; break; }
        }
        if (row) {
            document.getElementById('pf_pg_code').value = row.code || '';
            document.getElementById('pf_pg_name').value = row.name || '';
            document.getElementById('pf_pg_coverage_model').value = row.coverage_model || 'PARTNER_BASED';
            document.getElementById('pf_pg_is_active').value = String(row.is_active);
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('programmeGroupModal')).show();
}
</script>

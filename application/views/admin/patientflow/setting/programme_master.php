<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Programme Master</h3>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openProgrammeModal(0)">Programme</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="openSubcatModal(0)"><i class="fa fa-plus"></i> Add Subcategory</button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>
        <table class="table table-striped table-bordered table-hover example">
            <thead><tr><th>Subcategory Code</th><th>Subcategory</th><th>Programme</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (empty($subcategories)) { ?>
                <tr><td colspan="5" class="text-center text-muted">No subcategories yet.</td></tr>
                <?php } else { foreach ($subcategories as $s) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($s->code); ?></strong></td>
                    <td><?php echo htmlspecialchars($s->name); ?></td>
                    <td><?php echo htmlspecialchars(($s->pcode ? $s->pcode . ' - ' : '') . (string) $s->pname); ?></td>
                    <td><?php echo ((int) $s->is_active === 1) ? 'Active' : 'Inactive'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSubcatModal(<?php echo (int) $s->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_subcategory/' . (int) $s->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="programmeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_programme'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="programmeModalTitle">Programme</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_prog_id" value="">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Code <small class="req">*</small></label>
                    <input type="text" name="code" id="pf_prog_code" class="form-control" required maxlength="10"></div>
                    <div class="col-md-9"><label class="form-label">Programme <small class="req">*</small></label>
                    <input type="text" name="name" id="pf_prog_name" class="form-control" required maxlength="150"></div>
                </div>
                <hr>
                <h6 class="mb-2">Existing</h6>
                <div class="table-responsive"><table class="table table-sm table-bordered">
                    <thead><tr><th>Code</th><th>Programme</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                    <tbody id="pf_prog_list"></tbody>
                </table></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>
<div class="modal fade" id="subcatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_subcategory'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="subcatModalTitle">Add Subcategory</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_sub_id" value="">
                <div class="mb-2"><label class="form-label">Programme <small class="req">*</small></label>
                <select name="programme_id" id="pf_sub_prog" class="form-select" required></select></div>
                <div class="mb-2"><label class="form-label">Subcategory Code <small class="req">*</small></label>
                <input type="text" name="code" id="pf_sub_code" class="form-control" required maxlength="15"></div>
                <div class="mb-2"><label class="form-label">Subcategory <small class="req">*</small></label>
                <input type="text" name="name" id="pf_sub_name" class="form-control" required maxlength="150"></div>
                <div class="mb-2"><label class="form-label">Status</label>
                <select name="is_active" id="pf_sub_active" class="form-select">
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
var pfDelProgBase = '<?php echo site_url('admin/patientflowsetting/delete_programme'); ?>';
var pfProgs = <?php echo json_encode(isset($programmes) ? $programmes : array()); ?>;
var pfSubs = <?php echo json_encode(isset($subcategories) ? $subcategories : array()); ?>;
function openProgrammeModal(id) {
    document.getElementById('pf_prog_id').value = id ? id : '';
    document.getElementById('programmeModalTitle').textContent = id ? 'Edit Programme' : 'Add Programme';
    document.getElementById('pf_prog_code').value = '';
    document.getElementById('pf_prog_name').value = '';
    var html = '';
    if (!pfProgs.length) { html = '<tr><td colspan="4" class="text-center text-muted">No records.</td></tr>'; }
    for (var i = 0; i < pfProgs.length; i++) {
        html += '<tr><td><strong>' + pfProgs[i].code + '</strong></td><td>' + pfProgs[i].name + '</td><td>' + (String(pfProgs[i].is_active) === '0' ? 'Inactive' : 'Active') + '</td>';
        html += '<td class="text-end text-nowrap"><button type="button" class="btn btn-sm btn-outline-primary" onclick="pfEditProg(' + pfProgs[i].id + ')"><i class="fa fa-pencil"></i></button> ';
        html += '<a class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Are you sure?\')" href="' + pfDelProgBase + '/' + pfProgs[i].id + '"><i class="fa fa-trash"></i></a></td></tr>';
    }
    document.getElementById('pf_prog_list').innerHTML = html;
    if (id) { for (var j = 0; j < pfProgs.length; j++) { if (String(pfProgs[j].id) === String(id)) {
        document.getElementById('pf_prog_code').value = pfProgs[j].code || '';
        document.getElementById('pf_prog_name').value = pfProgs[j].name || '';
        break; } } }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('programmeModal')).show();
}
function pfEditProg(id) { openProgrammeModal(id); }
function openSubcatModal(id) {
    document.getElementById('pf_sub_id').value = id ? id : '';
    document.getElementById('subcatModalTitle').textContent = id ? 'Edit Subcategory' : 'Add Subcategory';
    var row = null;
    for (var i = 0; i < pfSubs.length; i++) { if (String(pfSubs[i].id) === String(id)) { row = pfSubs[i]; break; } }
    var sel = document.getElementById('pf_sub_prog');
    sel.innerHTML = '<option value="">-- Select --</option>';
    for (var k = 0; k < pfProgs.length; k++) {
        var o = document.createElement('option');
        o.value = pfProgs[k].id;
        o.textContent = pfProgs[k].code + ' - ' + pfProgs[k].name;
        if (row && String(row.programme_id) === String(pfProgs[k].id)) { o.selected = true; }
        sel.appendChild(o);
    }
    document.getElementById('pf_sub_code').value = row ? (row.code || '') : '';
    document.getElementById('pf_sub_name').value = row ? (row.name || '') : '';
    document.getElementById('pf_sub_active').value = row ? String(row.is_active) : '1';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('subcatModal')).show();
}
</script>
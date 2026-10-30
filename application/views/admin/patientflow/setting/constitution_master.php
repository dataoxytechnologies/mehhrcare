<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Constitution Master</h3>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openLookupModal('body',0)">Body Build</button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openLookupModal('face',0)">Face Shape</button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openLookupModal('nose',0)">Nose Type</button>
            <button type="button" class="btn btn-sm btn-primary" onclick="openConstitutionForm(0)"><i class="fa fa-plus"></i> Add Constitution</button>
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
            <thead><tr><th>Code</th><th>Gender</th><th>Body</th><th>Face</th><th>Nose</th><th>Variant</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (empty($constitutions)) { ?>
                <tr><td colspan="8" class="text-center text-muted">No constitutions yet.</td></tr>
                <?php } else { foreach ($constitutions as $c) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($c->code); ?></strong></td>
                    <td><?php echo htmlspecialchars($c->gender); ?></td>
                    <td><?php echo htmlspecialchars(($c->bcode ? $c->bcode . ' - ' : '') . (string) $c->bname); ?></td>
                    <td><?php echo htmlspecialchars(($c->fcode ? $c->fcode . ' - ' : '') . (string) $c->fname); ?></td>
                    <td><?php echo htmlspecialchars(($c->ncode ? $c->ncode . ' - ' : '') . (string) $c->nname); ?></td>
                    <td><?php echo (int) $c->variant; ?></td>
                    <td><?php echo ((int) $c->is_active === 1) ? 'Active' : 'Inactive'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openConstitutionForm(<?php echo (int) $c->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_constitution/' . (int) $c->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="lookupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_lookup'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="lookupModalTitle">Manage</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="lookup_type" id="pf_lookup_type" value="body">
                <input type="hidden" name="id" id="pf_lookup_id" value="">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Code <small class="req">*</small></label>
                    <input type="text" name="code" id="pf_lookup_code" class="form-control" required maxlength="10"></div>
                    <div class="col-md-6"><label class="form-label">Name <small class="req">*</small></label>
                    <input type="text" name="name" id="pf_lookup_name" class="form-control" required maxlength="100"></div>
                    <div class="col-md-3" id="pf_lookup_variant_wrap"><label class="form-label">Variant</label>
                    <input type="number" name="variant" id="pf_lookup_variant" class="form-control" value="1" min="1"></div>
                </div>
                <hr>
                <h6 class="mb-2">Existing</h6>
                <div class="table-responsive"><table class="table table-sm table-bordered">
                    <thead><tr><th>Code</th><th>Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                    <tbody id="pf_lookup_list"></tbody>
                </table></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>
<div class="modal fade" id="constitutionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_constitution'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="constitutionModalTitle">Add Constitution</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_const_id" value="">
                <div class="mb-2"><label class="form-label">Gender <small class="req">*</small></label>
                <select name="gender" id="pf_const_gender" class="form-select" required onchange="pfPreviewCode()">
                    <option value="Male">Male</option><option value="Female">Female</option>
                </select></div>
                <div class="mb-2"><label class="form-label">Body Build <small class="req">*</small></label>
                <select name="body_build_id" id="pf_const_body" class="form-select" required onchange="pfPreviewCode()"></select></div>
                <div class="mb-2"><label class="form-label">Face Shape <small class="req">*</small></label>
                <select name="face_shape_id" id="pf_const_face" class="form-select" required onchange="pfPreviewCode()"></select></div>
                <div class="mb-2"><label class="form-label">Nose Type <small class="req">*</small></label>
                <select name="nose_type_id" id="pf_const_nose" class="form-select" required onchange="pfPreviewCode()"></select></div>
                <div class="alert alert-info py-2 mb-2">Code (auto): <strong id="pf_code_preview">-</strong></div>
                <div class="mb-2"><label class="form-label">Description</label>
                <textarea name="description" id="pf_const_desc" class="form-control" rows="3"></textarea></div>
                <div class="mb-2"><label class="form-label">Status</label>
                <select name="is_active" id="pf_const_active" class="form-select">
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
var pfBody = <?php echo json_encode(isset($body_builds) ? $body_builds : array()); ?>;
var pfFace = <?php echo json_encode(isset($face_shapes) ? $face_shapes : array()); ?>;
var pfNose = <?php echo json_encode(isset($nose_types) ? $nose_types : array()); ?>;
var pfRows = <?php echo json_encode(isset($constitutions) ? $constitutions : array()); ?>;
var pfLookupTitles = {body: 'Body Build', face: 'Face Shape', nose: 'Nose Type'};
function pfLookupData(t) { return t === 'face' ? pfFace : (t === 'nose' ? pfNose : pfBody); }
function pfFillSelect(el, rows, sel) {
    el.innerHTML = '<option value="">-- Select --</option>';
    for (var i = 0; i < rows.length; i++) {
        var o = document.createElement('option');
        o.value = rows[i].id;
        o.textContent = rows[i].code + ' - ' + rows[i].name;
        o.setAttribute('data-code', rows[i].code);
        if (sel && String(sel) === String(rows[i].id)) { o.selected = true; }
        el.appendChild(o);
    }
}
function pfCodeOf(el) {
    var o = el.options[el.selectedIndex];
    return o && o.getAttribute ? (o.getAttribute('data-code') || '') : '';
}
function pfPreviewCode() {
    var g = document.getElementById('pf_const_gender').value === 'Female' ? 'F' : 'M';
    var b = pfCodeOf(document.getElementById('pf_const_body'));
    var f = pfCodeOf(document.getElementById('pf_const_face'));
    var n = pfCodeOf(document.getElementById('pf_const_nose'));
    document.getElementById('pf_code_preview').textContent = (b && f && n) ? (g + '-' + b + '-' + f + '-' + n) : '-';
}
function openConstitutionForm(id) {
    document.getElementById('pf_const_id').value = id ? id : '';
    document.getElementById('constitutionModalTitle').textContent = id ? 'Edit Constitution' : 'Add Constitution';
    var row = null;
    for (var i = 0; i < pfRows.length; i++) { if (String(pfRows[i].id) === String(id)) { row = pfRows[i]; break; } }
    document.getElementById('pf_const_gender').value = row ? row.gender : 'Male';
    pfFillSelect(document.getElementById('pf_const_body'), pfBody, row ? row.body_build_id : '');
    pfFillSelect(document.getElementById('pf_const_face'), pfFace, row ? row.face_shape_id : '');
    pfFillSelect(document.getElementById('pf_const_nose'), pfNose, row ? row.nose_type_id : '');
    document.getElementById('pf_const_desc').value = row ? (row.description || '') : '';
    document.getElementById('pf_const_active').value = row ? String(row.is_active) : '1';
    pfPreviewCode();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('constitutionModal')).show();
}
function openLookupModal(type, id) {
    document.getElementById('pf_lookup_type').value = type;
    document.getElementById('pf_lookup_id').value = id ? id : '';
    document.getElementById('lookupModalTitle').textContent = (id ? 'Edit ' : 'Add ') + (pfLookupTitles[type] || type);
    document.getElementById('pf_lookup_variant_wrap').style.display = (type === 'body') ? '' : 'none';
    document.getElementById('pf_lookup_code').value = '';
    document.getElementById('pf_lookup_name').value = '';
    document.getElementById('pf_lookup_variant').value = '1';
    var rows = pfLookupData(type), html = '';
    if (!rows.length) { html = '<tr><td colspan="4" class="text-center text-muted">No records.</td></tr>'; }
    for (var i = 0; i < rows.length; i++) {
        html += '<tr><td><strong>' + rows[i].code + '</strong></td><td>' + rows[i].name + '</td><td>' + (String(rows[i].is_active) === '0' ? 'Inactive' : 'Active') + '</td>';
        html += '<td class="text-end text-nowrap"><button type="button" class="btn btn-sm btn-outline-primary" onclick="pfEditLookup(\'' + type + '\',' + rows[i].id + ')"><i class="fa fa-pencil"></i></button> ';
        html += '<a class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Are you sure?\')" href="<?php echo site_url('admin/patientflowsetting/delete_lookup'); ?>/' + type + '/' + rows[i].id + '"><i class="fa fa-trash"></i></a></td></tr>';
    }
    document.getElementById('pf_lookup_list').innerHTML = html;
    if (id) {
        for (var j = 0; j < rows.length; j++) {
            if (String(rows[j].id) === String(id)) {
                document.getElementById('pf_lookup_code').value = rows[j].code || '';
                document.getElementById('pf_lookup_name').value = rows[j].name || '';
                if (rows[j].variant) { document.getElementById('pf_lookup_variant').value = rows[j].variant; }
                break;
            }
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('lookupModal')).show();
}
function pfEditLookup(type, id) { openLookupModal(type, id); }
</script>

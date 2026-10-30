<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .pf-formulation-tabs .nav-link { font-size: 0.92rem; padding: 8px 14px; }
    .pf-formulation-tabs .nav-link.active { background: #e8eef5; color: #1a73e8; font-weight: 600; border-color: #dee2e6 #dee2e6 #e8eef5; }
    .pf-tab-header { padding: 6px; display: flex; justify-content: space-between;}
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">FA/FB/FC/FD Mapping</h3>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>

        <ul class="nav nav-tabs pf-formulation-tabs mb-3" id="pfFormulationTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-fa" data-bs-toggle="tab" data-bs-target="#pf-tab-fa" type="button" role="tab">FA Medicines</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tab-fb" data-bs-toggle="tab" data-bs-target="#pf-tab-fb" type="button" role="tab">FB Medicines</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tab-fc" data-bs-toggle="tab" data-bs-target="#pf-tab-fc" type="button" role="tab">FC Medicines</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tab-fd" data-bs-toggle="tab" data-bs-target="#pf-tab-fd" type="button" role="tab">FD Medicines</button></li>
            <!-- <li class="nav-item" role="presentation"><button class="nav-link" id="tab-early" data-bs-toggle="tab" data-bs-target="#pf-tab-early" type="button" role="tab">Early Mapping</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tab-universal" data-bs-toggle="tab" data-bs-target="#pf-tab-universal" type="button" role="tab">Universal Mapping</button></li> -->
        </ul>

        <div class="tab-content" id="pfFormulationTabContent">
            <!-- FA -->
            <div class="tab-pane fade show active" id="pf-tab-fa" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>FA Medicines</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openFormulationMedicineModal(0, 'FA')"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_fa_table">
                        <thead><tr><th>Code</th><th>Identity</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($fa_medicines)) { echo '<tr><td colspan="4" class="text-center text-muted">None</td></tr>'; } else { foreach ($fa_medicines as $m) { ?>
                            <tr data-id="<?php echo (int) $m->id; ?>">
                                <td><strong><?php echo htmlspecialchars($m->code); ?></strong></td>
                                <td><?php echo htmlspecialchars($m->medicine_identity); ?></td>
                                <td><?php echo htmlspecialchars($m->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFormulationMedicineModal(<?php echo (int) $m->id; ?>, 'FA')"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_formulation_medicine/' . (int) $m->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this medicine?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- FB -->
            <div class="tab-pane fade" id="pf-tab-fb" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>FB Medicines</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openFormulationMedicineModal(0, 'FB')"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_fb_table">
                        <thead><tr><th>Code</th><th>Identity</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($fb_medicines)) { echo '<tr><td colspan="4" class="text-center text-muted">None</td></tr>'; } else { foreach ($fb_medicines as $m) { ?>
                            <tr data-id="<?php echo (int) $m->id; ?>">
                                <td><strong><?php echo htmlspecialchars($m->code); ?></strong></td>
                                <td><?php echo htmlspecialchars($m->medicine_identity); ?></td>
                                <td><?php echo htmlspecialchars($m->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFormulationMedicineModal(<?php echo (int) $m->id; ?>, 'FB')"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_formulation_medicine/' . (int) $m->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this medicine?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- FC -->
            <div class="tab-pane fade" id="pf-tab-fc" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>FC Medicines</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openFormulationMedicineModal(0, 'FC')"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_fc_table">
                        <thead><tr><th>Code</th><th>Identity</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($fc_medicines)) { echo '<tr><td colspan="4" class="text-center text-muted">None</td></tr>'; } else { foreach ($fc_medicines as $m) { ?>
                            <tr data-id="<?php echo (int) $m->id; ?>">
                                <td><strong><?php echo htmlspecialchars($m->code); ?></strong></td>
                                <td><?php echo htmlspecialchars($m->medicine_identity); ?></td>
                                <td><?php echo htmlspecialchars($m->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFormulationMedicineModal(<?php echo (int) $m->id; ?>, 'FC')"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_formulation_medicine/' . (int) $m->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this medicine?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- FD -->
            <div class="tab-pane fade" id="pf-tab-fd" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>FD Medicines</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openFormulationMedicineModal(0, 'FD')"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_fd_table">
                        <thead><tr><th>Code</th><th>Identity</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($fd_medicines)) { echo '<tr><td colspan="4" class="text-center text-muted">None</td></tr>'; } else { foreach ($fd_medicines as $m) { ?>
                            <tr data-id="<?php echo (int) $m->id; ?>">
                                <td><strong><?php echo htmlspecialchars($m->code); ?></strong></td>
                                <td><?php echo htmlspecialchars($m->medicine_identity); ?></td>
                                <td><?php echo htmlspecialchars($m->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFormulationMedicineModal(<?php echo (int) $m->id; ?>, 'FD')"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_formulation_medicine/' . (int) $m->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this medicine?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Early Mapping -->
            <div class="tab-pane fade" id="pf-tab-early" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>Early Mapping</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openEarlyMappingModal(0)"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_early_table">
                        <thead><tr><th>Pair Group</th><th>Stage</th><th>Session</th><th>Medicine</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($early_mappings)) { echo '<tr><td colspan="6" class="text-center text-muted">No early mappings.</td></tr>'; } else { foreach ($early_mappings as $em) { ?>
                            <tr data-id="<?php echo (int) $em->id; ?>">
                                <td><?php echo htmlspecialchars($em->pair_group); ?></td>
                                <td><?php echo htmlspecialchars($em->stcode); ?></td>
                                <td><?php echo htmlspecialchars($em->session); ?></td>
                                <td><strong><?php echo htmlspecialchars($em->mcode); ?></strong> - <?php echo htmlspecialchars($em->mident); ?></td>
                                <td><?php echo htmlspecialchars($em->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEarlyMappingModal(<?php echo (int) $em->id; ?>)"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_early_mapping/' . (int) $em->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this early mapping?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Universal Mapping -->
            <div class="tab-pane fade" id="pf-tab-universal" role="tabpanel">
                <div class="pf-tab-header">
                    <h3>Universal Mapping</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openUniversalMappingModal(0)"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_universal_table">
                        <thead><tr><th>Stage</th><th>Session</th><th>Medicine</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($universal_mappings)) { echo '<tr><td colspan="5" class="text-center text-muted">No universal mappings.</td></tr>'; } else { foreach ($universal_mappings as $um) { ?>
                            <tr data-id="<?php echo (int) $um->id; ?>">
                                <td><?php echo htmlspecialchars($um->stcode); ?></td>
                                <td><?php echo htmlspecialchars($um->session); ?></td>
                                <td><strong><?php echo htmlspecialchars($um->mcode); ?></strong> - <?php echo htmlspecialchars($um->mident); ?></td>
                                <td><?php echo htmlspecialchars($um->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openUniversalMappingModal(<?php echo (int) $um->id; ?>)"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_universal_mapping/' . (int) $um->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this universal mapping?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulation Medicine Modal (FA/FB/FC/FD) -->
<div class="modal fade" id="pfFormulationMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="pfFormulationMedicineForm" action="<?php echo site_url('admin/patientflowsetting/save_formulation_medicine'); ?>" method="post">
            <input type="hidden" name="id" id="pf_fm_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pfFormulationMedicineModalTitle">Add Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">Code</label><input type="text" name="code" id="pf_fm_code" class="form-control" maxlength="10" required></div>
                        <div class="col-md-8"><label class="form-label">Identity</label><input type="text" name="medicine_identity" id="pf_fm_ident" class="form-control" maxlength="200" required></div>
                        <div class="col-md-4"><label class="form-label">Series</label><input type="text" name="series" id="pf_fm_series" class="form-control" readonly></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select name="status" id="pf_fm_status" class="form-select">
                            <option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Locked">Locked</option>
                        </select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Early Mapping Modal -->
<div class="modal fade" id="pfEarlyMappingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="pfEarlyMappingForm" action="<?php echo site_url('admin/patientflowsetting/save_early_mapping'); ?>" method="post">
            <input type="hidden" name="id" id="pf_em_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pfEarlyMappingModalTitle">Add Early Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">Pair Group</label><input type="text" name="pair_group" id="pf_em_pair_group" class="form-control" maxlength="20" required></div>
                        <div class="col-md-4"><label class="form-label">Stage</label><select name="stage_id" id="pf_em_stage_id" class="form-select" required>
                            <option value="">Select Stage</option>
                            <?php if (!empty($stages)) { foreach ($stages as $s) { echo '<option value="' . (int) $s->id . '">' . htmlspecialchars($s->code) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Medicine (FA/FB)</label><select name="medicine_id" id="pf_em_medicine_id" class="form-select" required>
                            <option value="">Select Medicine</option>
                            <?php $all = array_merge((array)$fa_medicines, (array)$fb_medicines); if (!empty($all)) { foreach ($all as $m) { echo '<option value="' . (int) $m->id . '">' . htmlspecialchars($m->code) . ' - ' . htmlspecialchars($m->medicine_identity) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select name="status" id="pf_em_status" class="form-select">
                            <option value="Locked">Locked</option><option value="Active">Active</option><option value="Inactive">Inactive</option>
                        </select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Universal Mapping Modal -->
<div class="modal fade" id="pfUniversalMappingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="pfUniversalMappingForm" action="<?php echo site_url('admin/patientflowsetting/save_universal_mapping'); ?>" method="post">
            <input type="hidden" name="id" id="pf_um_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pfUniversalMappingModalTitle">Add Universal Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">Stage</label><select name="stage_id" id="pf_um_stage_id" class="form-select" required>
                            <option value="">Select Stage</option>
                            <?php if (!empty($stages)) { foreach ($stages as $s) { echo '<option value="' . (int) $s->id . '">' . htmlspecialchars($s->code) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Session</label><input type="text" name="session" id="pf_um_session" class="form-control" maxlength="20" required></div>
                        <div class="col-md-4"><label class="form-label">Medicine (FC/FD)</label><select name="medicine_id" id="pf_um_medicine_id" class="form-select" required>
                            <option value="">Select Medicine</option>
                            <?php $all = array_merge((array)$fc_medicines, (array)$fd_medicines); if (!empty($all)) { foreach ($all as $m) { echo '<option value="' . (int) $m->id . '">' . htmlspecialchars($m->code) . ' - ' . htmlspecialchars($m->medicine_identity) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select name="status" id="pf_um_status" class="form-select">
                            <option value="Locked">Locked</option><option value="Active">Active</option><option value="Inactive">Inactive</option>
                        </select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
var pfFaMeds = <?php echo json_encode(isset($fa_medicines) ? $fa_medicines : array()); ?>;
var pfFbMeds = <?php echo json_encode(isset($fb_medicines) ? $fb_medicines : array()); ?>;
var pfFcMeds = <?php echo json_encode(isset($fc_medicines) ? $fc_medicines : array()); ?>;
var pfFdMeds = <?php echo json_encode(isset($fd_medicines) ? $fd_medicines : array()); ?>;

function openFormulationMedicineModal(id, series) {
    document.getElementById('pf_fm_id').value = id ? id : '';
    document.getElementById('pfFormulationMedicineModalTitle').textContent = id ? 'Edit Medicine' : 'Add Medicine';
    document.getElementById('pf_fm_series').value = series;
    var meds = series === 'FA' ? pfFaMeds : series === 'FB' ? pfFbMeds : series === 'FC' ? pfFcMeds : pfFdMeds;
    var row = null;
    for (var i = 0; i < meds.length; i++) { if (String(meds[i].id) === String(id)) { row = meds[i]; break; } }
    document.getElementById('pf_fm_code').value = row ? row.code : '';
    document.getElementById('pf_fm_ident').value = row ? row.medicine_identity : '';
    document.getElementById('pf_fm_status').value = row ? row.status : 'Active';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfFormulationMedicineModal')).show();
}

function openEarlyMappingModal(id) {
    document.getElementById('pf_em_id').value = id ? id : '';
    document.getElementById('pfEarlyMappingModalTitle').textContent = id ? 'Edit Early Mapping' : 'Add Early Mapping';
    document.getElementById('pf_em_pair_group').value = '';
    document.getElementById('pf_em_stage_id').value = '';
    document.getElementById('pf_em_medicine_id').value = '';
    document.getElementById('pf_em_status').value = 'Locked';
    if (id) {
        var rows = document.querySelectorAll('#pf_early_table tbody tr[data-id]');
        for (var i = 0; i < rows.length; i++) {
            if (String(rows[i].getAttribute('data-id')) === String(id)) {
                var tds = rows[i].querySelectorAll('td');
                document.getElementById('pf_em_pair_group').value = tds[0].textContent.trim();
                document.getElementById('pf_em_stage_id').value = tds[1].textContent.trim();
                var medText = tds[3].textContent.trim();
                var sel = document.getElementById('pf_em_medicine_id');
                for (var j = 0; j < sel.options.length; j++) {
                    if (sel.options[j].text === medText) { sel.value = sel.options[j].value; break; }
                }
                document.getElementById('pf_em_status').value = tds[4].textContent.trim();
                document.getElementById('pf_em_id').value = id;
                break;
            }
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfEarlyMappingModal')).show();
}

function openUniversalMappingModal(id) {
    document.getElementById('pf_um_id').value = id ? id : '';
    document.getElementById('pfUniversalMappingModalTitle').textContent = id ? 'Edit Universal Mapping' : 'Add Universal Mapping';
    document.getElementById('pf_um_stage_id').value = '';
    document.getElementById('pf_um_session').value = '';
    document.getElementById('pf_um_medicine_id').value = '';
    document.getElementById('pf_um_status').value = 'Locked';
    if (id) {
        var rows = document.querySelectorAll('#pf_universal_table tbody tr[data-id]');
        for (var i = 0; i < rows.length; i++) {
            if (String(rows[i].getAttribute('data-id')) === String(id)) {
                var tds = rows[i].querySelectorAll('td');
                var stageText = tds[0].textContent.trim();
                var sel = document.getElementById('pf_um_stage_id');
                for (var j = 0; j < sel.options.length; j++) {
                    if (sel.options[j].text === stageText) { sel.value = sel.options[j].value; break; }
                }
                document.getElementById('pf_um_session').value = tds[1].textContent.trim();
                var medText = tds[2].textContent.trim();
                var sel2 = document.getElementById('pf_um_medicine_id');
                for (var j = 0; j < sel2.options.length; j++) {
                    if (sel2.options[j].text === medText) { sel2.value = sel2.options[j].value; break; }
                }
                document.getElementById('pf_um_status').value = tds[3].textContent.trim();
                document.getElementById('pf_um_id').value = id;
                break;
            }
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfUniversalMappingModal')).show();
}
</script>

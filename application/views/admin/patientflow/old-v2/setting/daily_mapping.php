<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .pf-daily-tabs .nav-link { font-size: 0.92rem; padding: 8px 14px; }
    .pf-daily-tabs .nav-link.active { background: #e8eef5; color: #1a73e8; font-weight: 600; border-color: #dee2e6 #dee2e6 #e8eef5; }
    .pf-tab-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 8px; }
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Daily Mapping</h3>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>

        <ul class="nav nav-tabs pf-daily-tabs mb-3" id="pfDailyTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-daily-early" data-bs-toggle="tab" data-bs-target="#pf-tab-daily-early" type="button" role="tab">Early Mapping (FA/FB)</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="tab-daily-universal" data-bs-toggle="tab" data-bs-target="#pf-tab-daily-universal" type="button" role="tab">Universal Mapping (FC/FD)</button></li>
        </ul>

        <div class="tab-content" id="pfDailyTabContent">
            <!-- Early Mapping -->
            <div class="tab-pane fade show active" id="pf-tab-daily-early" role="tabpanel">
                <div class="pf-tab-header">
                    <strong>Early Mapping</strong>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openDailyEarlyMappingModal(0)"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_daily_early_table">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openDailyEarlyMappingModal(<?php echo (int) $em->id; ?>)"><i class="fa fa-pencil"></i></button>
                                    <a href="<?php echo site_url('admin/patientflowsetting/delete_early_mapping/' . (int) $em->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this early mapping?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Universal Mapping -->
            <div class="tab-pane fade" id="pf-tab-daily-universal" role="tabpanel">
                <div class="pf-tab-header">
                    <strong>Universal Mapping</strong>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openDailyUniversalMappingModal(0)"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" id="pf_daily_universal_table">
                        <thead><tr><th>Stage</th><th>Session</th><th>Medicine</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
                        <tbody>
                            <?php if (empty($universal_mappings)) { echo '<tr><td colspan="5" class="text-center text-muted">No universal mappings.</td></tr>'; } else { foreach ($universal_mappings as $um) { ?>
                            <tr data-id="<?php echo (int) $um->id; ?>">
                                <td><?php echo htmlspecialchars($um->stcode); ?></td>
                                <td><?php echo htmlspecialchars($um->session); ?></td>
                                <td><strong><?php echo htmlspecialchars($um->mcode); ?></strong> - <?php echo htmlspecialchars($um->mident); ?></td>
                                <td><?php echo htmlspecialchars($um->status); ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openDailyUniversalMappingModal(<?php echo (int) $um->id; ?>)"><i class="fa fa-pencil"></i></button>
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
<!-- Early Mapping Modal -->
<div class="modal fade" id="pfDailyEarlyMappingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="pfDailyEarlyMappingForm" action="<?php echo site_url('admin/patientflowsetting/save_early_mapping'); ?>" method="post">
            <input type="hidden" name="id" id="pf_daily_em_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pfDailyEarlyMappingModalTitle">Add Early Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">Pair Group</label><input type="text" name="pair_group" id="pf_daily_em_pair_group" class="form-control" maxlength="20" required></div>
                        <div class="col-md-4"><label class="form-label">Stage</label><select name="stage_id" id="pf_daily_em_stage_id" class="form-select" required>
                            <option value="">Select Stage</option>
                            <?php if (!empty($stages)) { foreach ($stages as $s) { echo '<option value="' . (int) $s->id . '">' . htmlspecialchars($s->code) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Medicine (FA/FB)</label><select name="medicine_id" id="pf_daily_em_medicine_id" class="form-select" required>
                            <option value="">Select Medicine</option>
                            <?php $all = array_merge((array)$fa_medicines, (array)$fb_medicines); if (!empty($all)) { foreach ($all as $m) { echo '<option value="' . (int) $m->id . '">' . htmlspecialchars($m->code) . ' - ' . htmlspecialchars($m->medicine_identity) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select name="status" id="pf_daily_em_status" class="form-select">
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
<div class="modal fade" id="pfDailyUniversalMappingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="pfDailyUniversalMappingForm" action="<?php echo site_url('admin/patientflowsetting/save_universal_mapping'); ?>" method="post">
            <input type="hidden" name="id" id="pf_daily_um_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pfDailyUniversalMappingModalTitle">Add Universal Mapping</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">Stage</label><select name="stage_id" id="pf_daily_um_stage_id" class="form-select" required>
                            <option value="">Select Stage</option>
                            <?php if (!empty($stages)) { foreach ($stages as $s) { echo '<option value="' . (int) $s->id . '">' . htmlspecialchars($s->code) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Session</label><input type="text" name="session" id="pf_daily_um_session" class="form-control" maxlength="20" required></div>
                        <div class="col-md-4"><label class="form-label">Medicine (FC/FD)</label><select name="medicine_id" id="pf_daily_um_medicine_id" class="form-select" required>
                            <option value="">Select Medicine</option>
                            <?php $all = array_merge((array)$fc_medicines, (array)$fd_medicines); if (!empty($all)) { foreach ($all as $m) { echo '<option value="' . (int) $m->id . '">' . htmlspecialchars($m->code) . ' - ' . htmlspecialchars($m->medicine_identity) . '</option>'; } } ?>
                        </select></div>
                        <div class="col-md-4"><label class="form-label">Status</label><select name="status" id="pf_daily_um_status" class="form-select">
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
function openDailyEarlyMappingModal(id) {
    document.getElementById('pf_daily_em_id').value = id ? id : '';
    document.getElementById('pfDailyEarlyMappingModalTitle').textContent = id ? 'Edit Early Mapping' : 'Add Early Mapping';
    document.getElementById('pf_daily_em_pair_group').value = '';
    document.getElementById('pf_daily_em_stage_id').value = '';
    document.getElementById('pf_daily_em_medicine_id').value = '';
    document.getElementById('pf_daily_em_status').value = 'Locked';
    if (id) {
        var rows = document.querySelectorAll('#pf_daily_early_table tbody tr[data-id]');
        for (var i = 0; i < rows.length; i++) {
            if (String(rows[i].getAttribute('data-id')) === String(id)) {
                var tds = rows[i].querySelectorAll('td');
                document.getElementById('pf_daily_em_pair_group').value = tds[0].textContent.trim();
                document.getElementById('pf_daily_em_stage_id').value = '';
                var stageText = tds[1].textContent.trim();
                var selStage = document.getElementById('pf_daily_em_stage_id');
                for (var j = 0; j < selStage.options.length; j++) {
                    if (selStage.options[j].text === stageText) { selStage.value = selStage.options[j].value; break; }
                }
                var medText = tds[3].textContent.trim();
                var sel = document.getElementById('pf_daily_em_medicine_id');
                for (var j = 0; j < sel.options.length; j++) {
                    if (sel.options[j].text === medText) { sel.value = sel.options[j].value; break; }
                }
                document.getElementById('pf_daily_em_status').value = tds[4].textContent.trim();
                document.getElementById('pf_daily_em_id').value = id;
                break;
            }
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfDailyEarlyMappingModal')).show();
}

function openDailyUniversalMappingModal(id) {
    document.getElementById('pf_daily_um_id').value = id ? id : '';
    document.getElementById('pfDailyUniversalMappingModalTitle').textContent = id ? 'Edit Universal Mapping' : 'Add Universal Mapping';
    document.getElementById('pf_daily_um_stage_id').value = '';
    document.getElementById('pf_daily_um_session').value = '';
    document.getElementById('pf_daily_um_medicine_id').value = '';
    document.getElementById('pf_daily_um_status').value = 'Locked';
    if (id) {
        var rows = document.querySelectorAll('#pf_daily_universal_table tbody tr[data-id]');
        for (var i = 0; i < rows.length; i++) {
            if (String(rows[i].getAttribute('data-id')) === String(id)) {
                var tds = rows[i].querySelectorAll('td');
                var stageText = tds[0].textContent.trim();
                var sel = document.getElementById('pf_daily_um_stage_id');
                for (var j = 0; j < sel.options.length; j++) {
                    if (sel.options[j].text === stageText) { sel.value = sel.options[j].value; break; }
                }
                document.getElementById('pf_daily_um_session').value = tds[1].textContent.trim();
                var medText = tds[2].textContent.trim();
                var sel2 = document.getElementById('pf_daily_um_medicine_id');
                for (var j = 0; j < sel2.options.length; j++) {
                    if (sel2.options[j].text === medText) { sel2.value = sel2.options[j].value; break; }
                }
                document.getElementById('pf_daily_um_status').value = tds[3].textContent.trim();
                document.getElementById('pf_daily_um_id').value = id;
                break;
            }
        }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('pfDailyUniversalMappingModal')).show();
}
</script>

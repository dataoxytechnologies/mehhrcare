<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Medicine Master</h3>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <select id="pf_med_series_filter" class="form-select form-select-sm" style="width:auto;" onchange="pfMedFilter()">
                <option value="">All Series</option>
                <option value="F">F</option><option value="FA">FA</option>
                <option value="FB">FB</option><option value="FC">FC</option>
                <option value="FD">FD</option><option value="FX">FX</option>
                <option value="E">E</option>
            </select>
            <button type="button" class="btn btn-sm btn-primary" onclick="openMedicineModal(0)"><i class="fa fa-plus"></i> Add Medicine</button>
        </div>
    </div>
    <div class="card-body table-responsive">
        <?php if ($this->session->flashdata('success_msg')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error_msg')) { ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error_msg'); ?></div>
        <?php } ?>
        <table class="table table-striped table-bordered table-hover example" id="pf_medicine_table">
            <thead><tr><th>Code</th><th>Identity</th><th>Pharmacy</th><th>Series</th><th>Dose</th><th>Route</th><th>Timing</th><th>Frequency</th><th>Duration</th><th>Disp Qty</th><th>Container</th><th>Stock Ded</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (empty($medicines)) { ?>
                <tr><td colspan="14" class="text-center text-muted">No medicines yet.</td></tr>
                <?php } else { foreach ($medicines as $md) { ?>
                <tr data-series="<?php echo htmlspecialchars($md->series); ?>">
                    <td><strong><?php echo htmlspecialchars($md->code); ?></strong></td>
                    <td><?php echo htmlspecialchars($md->medicine_identity); ?></td>
                    <td><?php echo !empty($md->pharmacy_name) ? htmlspecialchars($md->pharmacy_name) : '<span class="text-muted">—</span>'; ?></td>
                    <td><?php echo htmlspecialchars($md->series); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->patient_dose); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->route_method); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->timing); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->frequency); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->duration_text); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->dispensing_qty_ml); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->container_vial); ?></td>
                    <td><?php echo htmlspecialchars((string) $md->stock_deduction_ml); ?></td>
                    <td><?php echo htmlspecialchars($md->status); ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openMedicineModal(<?php echo (int) $md->id; ?>)"><i class="fa fa-pencil"></i></button>
                        <a href="<?php echo site_url('admin/patientflowsetting/delete_medicine/' . (int) $md->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="medicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo site_url('admin/patientflowsetting/save_medicine'); ?>" method="post">
            <div class="modal-header"><h5 class="modal-title" id="medicineModalTitle">Add Medicine</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pf_med_id" value="">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Code <small class="req">*</small></label>
                    <input type="text" name="code" id="pf_med_code" class="form-control" required maxlength="10"></div>
                    <div class="col-md-6"><label class="form-label">Identity <small class="req">*</small></label>
                    <input type="text" name="medicine_identity" id="pf_med_ident" class="form-control" required maxlength="200"></div>
                    <div class="col-md-3"><label class="form-label">Series <small class="req">*</small></label>
                    <select name="series" id="pf_med_series" class="form-select" required>
                        <option value="F">F</option><option value="FA">FA</option>
                        <option value="FB">FB</option><option value="FC">FC</option>
                        <option value="FD">FD</option><option value="FX">FX</option>
                        <option value="E">E</option>
                    </select></div>
                    <div class="col-md-3"><label class="form-label">GP Code</label>
                    <input type="text" name="gp_code" id="pf_med_gp" class="form-control" maxlength="10"></div>
                    <div class="col-md-4"><label class="form-label">Indication</label>
                    <input type="text" name="indication" id="pf_med_ind" class="form-control" maxlength="200"></div>
                    <div class="col-md-5"><label class="form-label">Purpose</label>
                    <input type="text" name="purpose" id="pf_med_purpose" class="form-control" maxlength="200"></div>
                    <div class="col-md-3"><label class="form-label">Dose</label>
                    <input type="text" name="patient_dose" id="pf_med_dose" class="form-control" maxlength="50"></div>
                    <div class="col-md-4"><label class="form-label">Route</label>
                    <input type="text" name="route_method" id="pf_med_route" class="form-control" maxlength="100"></div>
                    <div class="col-md-5"><label class="form-label">Timing</label>
                    <input type="text" name="timing" id="pf_med_timing" class="form-control" maxlength="100"></div>
                    <div class="col-md-4"><label class="form-label">Frequency</label>
                    <input type="text" name="frequency" id="pf_med_freq" class="form-control" maxlength="100"></div>
                    <div class="col-md-4"><label class="form-label">Duration</label>
                    <input type="text" name="duration_text" id="pf_med_dur" class="form-control" maxlength="100"></div>
                    <div class="col-md-4"><label class="form-label">Status</label>
                    <select name="status" id="pf_med_status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Locked">Locked</option>
                    </select></div>
                    <div class="col-md-3"><label class="form-label">Disp Qty (ml)</label>
                    <input type="number" step="0.01" name="dispensing_qty_ml" id="pf_med_dq" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Container</label>
                    <input type="text" name="container_vial" id="pf_med_cont" class="form-control" maxlength="50"></div>
                    <div class="col-md-3"><label class="form-label">Stock Deduction (ml)</label>
                    <input type="number" step="0.01" name="stock_deduction_ml" id="pf_med_sd" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Use Note</label>
                    <input type="text" name="use_note" id="pf_med_use" class="form-control" maxlength="255"></div>
                    <div class="col-md-6"><label class="form-label">Control Note</label>
                    <input type="text" name="control_note" id="pf_med_ctrl" class="form-control" maxlength="255"></div>
                    <div class="col-md-6"><label class="form-label">Supply Status</label>
                    <input type="text" name="supply_status" id="pf_med_supply" class="form-control" maxlength="100"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>
<script type="text/javascript">
var pfMeds = <?php echo json_encode(isset($medicines) ? $medicines : array()); ?>;
function pfMedFilter() {
    var v = document.getElementById('pf_med_series_filter').value;
    var rows = document.querySelectorAll('#pf_medicine_table tbody tr[data-series]');
    for (var i = 0; i < rows.length; i++) {
        rows[i].style.display = (!v || rows[i].getAttribute('data-series') === v) ? '' : 'none';
    }
}
function openMedicineModal(id) {
    document.getElementById('pf_med_id').value = id ? id : '';
    document.getElementById('medicineModalTitle').textContent = id ? 'Edit Medicine' : 'Add Medicine';
    var row = null;
    for (var i = 0; i < pfMeds.length; i++) { if (String(pfMeds[i].id) === String(id)) { row = pfMeds[i]; break; } }
    var map = {code:'pf_med_code',medicine_identity:'pf_med_ident',series:'pf_med_series',gp_code:'pf_med_gp',indication:'pf_med_ind',purpose:'pf_med_purpose',patient_dose:'pf_med_dose',route_method:'pf_med_route',timing:'pf_med_timing',frequency:'pf_med_freq',duration_text:'pf_med_dur',dispensing_qty_ml:'pf_med_dq',container_vial:'pf_med_cont',stock_deduction_ml:'pf_med_sd',use_note:'pf_med_use',control_note:'pf_med_ctrl',supply_status:'pf_med_supply',status:'pf_med_status'};
    for (var f in map) {
        var el = document.getElementById(map[f]);
        if (!row) {
            if (f === 'series') { el.value = 'F'; }
            else if (f === 'status') { el.value = 'Active'; }
            else { el.value = ''; }
        } else { el.value = (row[f] === null || row[f] === undefined) ? '' : row[f]; }
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('medicineModal')).show();
}
</script>
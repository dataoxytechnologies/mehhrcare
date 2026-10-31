<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Consultation Fees</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openConsultationFeeModal(0)"><i class="fa fa-plus"></i> Add Fee Item</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Fee Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Base Amount</th>
                    <th>Basis</th>
                    <th>GST Type</th>
                    <th>GST Rate</th>
                    <th>Min Duration</th>
                    <th>Effective From</th>
                    <th>Status</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!isset($fees) || empty($fees)): ?>
                <tr><td colspan="11" class="text-center text-muted">No consultation or vial fees yet.</td></tr>
                <?php else: foreach ($fees as $f): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($f->fee_code ?? '-'); ?></strong></td>
                    <td><?php echo htmlspecialchars($f->item_name ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->category ?? '-'); ?></td>
                    <td><?php echo ($f->base_amount ?? 0) > 0 ? '$' . number_format($f->base_amount, 2) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($f->basis ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->gst_type ?? '-'); ?></td>
                    <td><?php echo ($f->gst_rate ?? 0) > 0 ? number_format($f->gst_rate, 2) . '%' : '-'; ?></td>
                    <td><?php echo $f->min_duration_minutes ? (int)$f->min_duration_minutes . ' min' : '-'; ?></td>
                    <td><?php echo htmlspecialchars($f->effective_from ?? '-'); ?></td>
                    <td><?php echo ($f->is_active ?? 0) == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openConsultationFeeModal(<?php echo (int)($f->id ?? 0); ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteConsultationFee(<?php echo (int)($f->id ?? 0); ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Consultation Fee Modal -->
<div class="modal fade" id="consultationFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="consultationFeeForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="consultationFeeModalTitle" style="padding-top:12px;padding-bottom:0;">Add Fee Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="cf_id" value="0">
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Fee Code <span class="text-danger">*</span></label>
                            <input type="text" name="fee_code" id="cf_fee_code" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" id="cf_item_name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category" id="cf_category" class="form-control">
                                <option value="CONSULTATION">Consultation</option>
                                <option value="VIAL">Vial</option>
                                <option value="ITEM">Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Base Amount</label>
                            <input type="number" step="0.01" name="base_amount" id="cf_base_amount" class="form-control" value="0">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Basis</label>
                            <select name="basis" id="cf_basis" class="form-control">
                                <option value="PER_PATIENT">Per Patient</option>
                                <option value="PER_CONSULTATION">Per Consultation</option>
                                <option value="PER_VIAL">Per Vial</option>
                                <option value="PER_DUPLICATE">Per Duplicate</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">GST Type</label>
                            <select name="gst_type" id="cf_gst_type" class="form-control">
                                <option value="EXEMPT">Exempt</option>
                                <option value="EXCLUSIVE">Exclusive</option>
                                <option value="INCLUSIVE">Inclusive</option>
                                <option value="CONFIGURED">Configured</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">GST Rate %</label>
                            <input type="number" step="0.01" name="gst_rate" id="cf_gst_rate" class="form-control" value="0">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Min Duration (min)</label>
                            <input type="number" name="min_duration_minutes" id="cf_min_duration_minutes" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Effective From</label>
                            <input type="date" name="effective_from" id="cf_effective_from" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Effective To</label>
                            <input type="date" name="effective_to" id="cf_effective_to" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Extra Time Chargeable</label>
                            <select name="extra_time_chargeable" id="cf_extra_time_chargeable" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Requires Enrolled Patient</label>
                            <select name="requires_enrolled_patient" id="cf_requires_enrolled_patient" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-8">
                            <label class="form-label">Rule Note</label>
                            <input type="text" name="rule_note" id="cf_rule_note" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Status</label>
                            <select name="is_active" id="cf_is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Fee Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openConsultationFeeModal(id) {
    $('#consultationFeeForm')[0].reset();
    $('#cf_id').val(0);
    $('#consultationFeeModalTitle').text('Add Fee Item');
    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_consultation_fee'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                $('#cf_id').val(d.id);
                $('#cf_fee_code').val(d.fee_code);
                $('#cf_item_name').val(d.item_name);
                $('#cf_category').val(d.category);
                $('#cf_base_amount').val(d.base_amount);
                $('#cf_basis').val(d.basis);
                $('#cf_gst_type').val(d.gst_type);
                $('#cf_gst_rate').val(d.gst_rate);
                $('#cf_min_duration_minutes').val(d.min_duration_minutes);
                $('#cf_effective_from').val(d.effective_from);
                $('#cf_effective_to').val(d.effective_to);
                $('#cf_extra_time_chargeable').val(d.extra_time_chargeable);
                $('#cf_requires_enrolled_patient').val(d.requires_enrolled_patient);
                $('#cf_rule_note').val(d.rule_note);
                $('#cf_is_active').val(d.is_active);
                $('#consultationFeeModalTitle').text('Edit Fee Item');
                if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(document.getElementById('consultationFeeModal')).show(); }
                else if (window.jQuery && $('#consultationFeeModal').modal) { $('#consultationFeeModal').modal('show'); }
            } else { alert(res.message); }
        }, 'json');
    } else {
        if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(document.getElementById('consultationFeeModal')).show(); }
        else if (window.jQuery && $('#consultationFeeModal').modal) { $('#consultationFeeModal').modal('show'); }
    }
}
$('#consultationFeeForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_consultation_fee'); ?>', $(this).serialize(), function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
});
function deleteConsultationFee(id) {
    if (!confirm('Are you sure you want to delete this fee item?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_consultation_fee'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}
</script>

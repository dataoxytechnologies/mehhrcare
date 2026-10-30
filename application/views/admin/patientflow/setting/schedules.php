<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Schedules</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openScheduleModal(0)"><i class="fa fa-plus"></i> Add Schedule</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Account ID</th>
                    <th>Quarter No</th>
                    <th>Stage From</th>
                    <th>Stage To</th>
                    <th>Label</th>
                    <th>Standard Amount</th>
                    <th>Discount %</th>
                    <th>Payable Amount</th>
                    <th>Status</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                <tr><td colspan="10" class="text-center text-muted">No pricing schedule yet.</td></tr>
                <?php else: foreach ($schedules as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s->account_id); ?></td>
                    <td><?php echo $s->quarter_no; ?></td>
                    <td><?php echo $s->stage_from; ?></td>
                    <td><?php echo $s->stage_to; ?></td>
                    <td><?php echo htmlspecialchars($s->label); ?></td>
                    <td><?php echo $s->standard_amount ? '$' . number_format($s->standard_amount, 2) : '-'; ?></td>
                    <td><?php echo $s->discount_percent ? number_format($s->discount_percent, 2) . '%' : '-'; ?></td>
                    <td><?php echo $s->payable_amount ? '$' . number_format($s->payable_amount, 2) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($s->status); ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openScheduleModal(<?php echo ($s->id ?? 0); ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(<?php echo ($s->id ?? 0); ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="scheduleForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="scheduleModalTitle" style="padding-top: 12px; padding-bottom: 0px;">Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" autocomplete="off"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="schedule_id" value="0">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Account <span class="text-danger">*</span></label>
                            <select class="form-control" name="account_id" id="schedule_account_id" required>
                                <option value="">-- Select Account --</option>
                                <?php if (!empty($accounts)): foreach ($accounts as $a): ?>
                                <option value="<?php echo (int) $a->id; ?>"><?php echo htmlspecialchars($a->uid . ' - ' . $a->programme_code . ' (' . $a->coverage . ')'); ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Quarter No <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quarter_no" id="schedule_quarter_no" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" id="schedule_label" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="form-label">Stage From</label>
                            <input type="number" class="form-control" name="stage_from" id="schedule_stage_from" value="0">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Stage To</label>
                            <input type="number" class="form-control" name="stage_to" id="schedule_stage_to" value="0">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Standard Amount</label>
                            <input type="number" step="0.01" class="form-control" name="standard_amount" id="schedule_standard_amount" value="0">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Discount %</label>
                            <input type="number" step="0.01" class="form-control" name="discount_percent" id="schedule_discount_percent" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Payable Amount</label>
                            <input type="number" step="0.01" class="form-control" name="payable_amount" id="schedule_payable_amount" value="0">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="schedule_status">
                                <option value="PENDING">Pending</option>
                                <option value="PAID">Paid</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function showScheduleModal() {
    var el = document.getElementById('scheduleModal');
    if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(el).show(); }
    else if (window.jQuery && $('#scheduleModal').modal) { $('#scheduleModal').modal('show'); }
}
function hideScheduleModal() {
    var el = document.getElementById('scheduleModal');
    if (window.bootstrap && bootstrap.Modal) { var inst = bootstrap.Modal.getInstance(el); if (inst) inst.hide(); }
    else if (window.jQuery && $('#scheduleModal').modal) { $('#scheduleModal').modal('hide'); }
}
function openScheduleModal(id) {
    $('#schedule_id').val(0);
    $('#scheduleModalTitle').text('Add Schedule');
    $('#scheduleForm')[0].reset();
    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_patient_fee_schedule'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                $('#schedule_id').val(d.id);
                $('#schedule_account_id').val(d.account_id);
                $('#schedule_quarter_no').val(d.quarter_no);
                $('#schedule_stage_from').val(d.stage_from);
                $('#schedule_stage_to').val(d.stage_to);
                $('#schedule_label').val(d.label);
                $('#schedule_standard_amount').val(d.standard_amount);
                $('#schedule_discount_percent').val(d.discount_percent);
                $('#schedule_payable_amount').val(d.payable_amount);
                $('#schedule_status').val(d.status);
                $('#scheduleModalTitle').text('Edit Schedule');
                showScheduleModal();
            } else { alert(res.message); }
        }, 'json');
    } else { showScheduleModal(); }
}
$('#scheduleForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_patient_fee_schedule'); ?>', $(this).serialize(), function(res) {
        if (res.status) { hideScheduleModal(); location.reload(); }
        else { alert(res.message); }
    }, 'json');
});
function deleteSchedule(id) {
    if (!confirm('Are you sure you want to delete this schedule?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_patient_fee_schedule'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}
</script>
    </div>
</div>
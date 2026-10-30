<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Fee Master</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openFeeModal(0)"><i class="fa fa-plus"></i> Add Fee Item</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead><tr><th>Code</th><th>Fee Type</th><th>Name</th><th>Group Code</th><th>Coverage</th><th>Amount</th><th>GST Type</th><th>GST Rate</th><th>Status</th><th class="text-end noExport">Action</th></tr></thead>
            <tbody>
                <?php if (!isset($fee_items) || empty($fee_items)) { ?>
                <tr><td colspan="10" class="text-center text-muted">No fee items yet.</td></tr>
                <?php } else { foreach ($fee_items as $f) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($f->fee_code); ?></strong></td>
                    <td><?php echo htmlspecialchars($f->fee_type ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->description ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->group_code ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($f->coverage ?? '-'); ?></td>
                    <td><?php echo ($f->consultation_fee ?? 0) > 0 ? '$' . number_format($f->consultation_fee, 2) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($f->gst_type ?? 'NONE'); ?></td>
                    <td><?php echo ($f->gst_rate ?? 0) > 0 ? number_format($f->gst_rate, 2) . '%' : '-'; ?></td>
                    <td><?php echo ($f->is_active ?? 0) == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openFeeModal(<?php echo ($f->id ?? 0); ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-<?php echo ($f->is_active ?? 0) == 1 ? 'warning' : 'success'; ?>" onclick="toggleFeeItem(<?php echo ($f->id ?? 0); ?>, <?php echo ($f->is_active ?? 0); ?>)"><i class="fa fa-<?php echo ($f->is_active ?? 0) ? 'eye-slash' : 'eye'; ?>"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFeeItem(<?php echo ($f->id ?? 0); ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Fee Item Modal (Bootstrap 5 markup) -->
<div class="modal fade" id="feeModal" tabindex="-1" aria-labelledby="feeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="feeForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="feeModalTitle" style="padding-top: 12px; padding-bottom: 0px;">Add Fee Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" autocomplete="off"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="fee_id" value="0">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="fee_type" id="fee_type" class="form-control" required>
                                <option value="ITEM">Item</option>
                                <option value="MILESTONE">Milestone</option>
                                <option value="STAGE">Stage</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Fee Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="fee_code" class="form-control" placeholder="e.g. CON-IC-N" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Group Code</label>
                            <input type="text" name="group_code" id="fee_group_code" class="form-control" placeholder="e.g. HFT">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="fee_name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Coverage</label>
                            <select name="coverage" id="fee_coverage" class="form-control">
                                <option value="">--</option>
                                <option value="ONE_PARTNER">One Partner</option>
                                <option value="BOTH_PARTNERS">Both Partners</option>
                                <option value="PER_PATIENT">Per Patient</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" name="amount" id="fee_amount" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">GST Type</label>
                            <select name="gst_type" id="fee_gst_type" class="form-control">
                                <option value="NONE">None</option>
                                <option value="EXEMPT">Exempt</option>
                                <option value="EXCLUSIVE">Exclusive</option>
                                <option value="INCLUSIVE">Inclusive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">GST Rate %</label>
                            <input type="number" name="gst_rate" id="fee_gst_rate" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Active</label>
                            <select name="is_active" id="fee_is_active" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Note</label>
                        <textarea name="note" id="fee_note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save Fee Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function getFeeModal() {
    // Bootstrap 5: get or create modal instance
    if (window.bootstrap && bootstrap.Modal) {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById('feeModal'));
    }
    // Fallback for Bootstrap 4 / jQuery
    if (window.jQuery && $('#feeModal').modal) {
        return null;
    }
    return null;
}

function showFeeModal() {
    var el = document.getElementById('feeModal');
    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (window.jQuery && $('#feeModal').modal) {
        $('#feeModal').modal('show');
    }
}

function hideFeeModal() {
    var el = document.getElementById('feeModal');
    if (window.bootstrap && bootstrap.Modal) {
        var inst = bootstrap.Modal.getInstance(el);
        if (inst) {
            inst.hide();
        } else {
            // No instance — hide manually as fallback
            el.classList.remove('show');
            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        }
    } else if (window.jQuery && $('#feeModal').modal) {
        $('#feeModal').modal('hide');
    }
}

function openFeeModal(id) {
    $('#fee_id').val(0);
    $('#feeModalTitle').text('Add Fee Item');
    $('#feeForm')[0].reset();
    $('#fee_type').val('ITEM');
    $('#fee_is_active').val('1');
    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_fee_item'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                $('#fee_id').val(d.id);
                $('#fee_type').val(d.fee_type);
                $('#fee_code').val(d.code);
                $('#fee_name').val(d.name);
                $('#fee_group_code').val(d.group_code);
                $('#fee_coverage').val(d.coverage);
                $('#fee_amount').val(d.amount);
                $('#fee_gst_type').val(d.gst_type);
                $('#fee_gst_rate').val(d.gst_rate);
                $('#fee_note').val(d.note);
                $('#fee_is_active').val(d.is_active);
                $('#feeModalTitle').text('Edit Fee Item');
                showFeeModal();
            } else {
                alert(res.message);
            }
        }, 'json');
    } else {
        showFeeModal();
    }
}

$('#feeForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_fee_item'); ?>', $(this).serialize(), function(res) {
        if (res.status) {
            hideFeeModal();
            location.reload();
        } else {
            alert(res.message);
        }
    }, 'json');
});

function toggleFeeItem(id, currentStatus) {
    if (!confirm('Are you sure you want to ' + (currentStatus ? 'deactivate' : 'activate') + ' this fee item?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/toggle_fee_item'); ?>', {id: id}, function(res) {
        if (res.status) {
            location.reload();
        } else {
            alert(res.message);
        }
    }, 'json');
}

function deleteFeeItem(id) {
    if (!confirm('Are you sure you want to delete this fee item?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_fee_item'); ?>', {id: id}, function(res) {
        if (res.status) {
            location.reload();
        } else {
            alert(res.message);
        }
    }, 'json');
}
</script>
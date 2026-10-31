<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- ===================== DISCOUNTS & CAMPAIGNS ===================== -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Discounts & Campaigns</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openCampaignModal(0)"><i class="fa fa-plus"></i> Add Campaign</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Reason</th>
                    <th>Discount %</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($discounts) && !empty($discounts)): ?>
                <?php foreach ($discounts as $d): ?>
                <tr>
                    <td><?php echo htmlspecialchars($d->campaign_code ?? $d->code ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($d->reason ?? '-'); ?></td>
                    <td><?php echo $d->discount_percent ? number_format($d->discount_percent, 2) . '%' : '-'; ?></td>
                    <td><?php echo $d->start_date ? $d->start_date : '-'; ?></td>
                    <td><?php echo $d->end_date ? $d->end_date : '-'; ?></td>
                    <td>
                        <?php
                        $status = strtoupper((string) ($d->status ?? ''));
                        if ($status === 'APPROVED') {
                            echo '<span class="badge bg-success">Approved</span>';
                        } elseif ($status === 'DRAFT') {
                            echo '<span class="badge bg-warning text-dark">Draft</span>';
                        } elseif ($status === 'REJECTED') {
                            echo '<span class="badge bg-danger">Rejected</span>';
                        } else {
                            echo '<span class="badge bg-secondary">' . htmlspecialchars($status ?: 'Unknown') . '</span>';
                        }
                        ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <?php if (($d->status ?? '') === 'DRAFT'): ?>
                            <button type="button" class="btn btn-sm btn-success" onclick="activateCampaign(<?php echo (int) ($d->id ?? 0); ?>)"><i class="fa fa-check"></i> Activate</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled><i class="fa fa-check"></i> Active</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCampaignModal(<?php echo ($d->id ?? 0); ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCampaign(<?php echo ($d->id ?? 0); ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No discount campaigns yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Campaign Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-labelledby="campaignModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="campaignForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="campaignModalTitle" style="padding-top: 12px; padding-bottom: 0px;">Add Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" autocomplete="off"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="campaign_id" value="0">
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Campaign Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="campaign_code" id="campaign_code" required>
                        </div>
                        <div class="form-group col-md-8">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="reason" id="campaign_reason" required>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount % <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="discount_percent" id="campaign_discount_percent" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" id="campaign_start_date" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="campaign_end_date" required>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Benefit Quarters</label>
                            <input type="number" class="form-control" name="benefit_quarters" id="campaign_benefit_quarters" value="2">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Payment Mode</label>
                            <select class="form-control" name="payment_mode" id="campaign_payment_mode">
                                <option value="MILESTONE">Milestone</option>
                                <option value="STAGEWISE">Stagewise</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Allow Stacking</label>
                            <select class="form-control" name="allow_stacking" id="campaign_allow_stacking">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Campaign Modal END -->

<!-- ===================== ASSISTANCE CATEGORIES ===================== -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Assistance Categories</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="openCategoryModal(0)"><i class="fa fa-plus"></i> Add Category</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover example">
            <thead>
                <tr>
                    <th>Category Code</th>
                    <th>Description</th>
                    <th>Discount %</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th class="text-end noExport">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($categories) && !empty($categories)): ?>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c->category_code ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($c->description ?? '-'); ?></td>
                    <td><?php echo $c->discount_percent ? number_format($c->discount_percent, 2) . '%' : '-'; ?></td>
                    <td><?php echo htmlspecialchars($c->group ?? '-'); ?></td>
                    <td><?php echo ($c->is_active ?? 0) == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                    <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCategoryModal(<?php echo ($c->id ?? 0); ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-<?php echo ($c->is_active ?? 0) == 1 ? 'warning' : 'success'; ?>" onclick="toggleCategory(<?php echo ($c->id ?? 0); ?>, <?php echo ($c->is_active ?? 0); ?>)"><i class="fa fa-<?php echo ($c->is_active ?? 0) ? 'eye-slash' : 'eye'; ?>"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory(<?php echo ($c->id ?? 0); ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No assistance categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Assistance Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="categoryForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="categoryModalTitle" style="padding-top: 12px; padding-bottom: 0px;">Add Assistance Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" autocomplete="off"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id" value="0">
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Group ID <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="group_id" id="category_group_id" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Category Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="category_code" id="category_code" maxlength="1" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount % <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="discount_percent" id="category_discount_percent" required>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Validity Quarters</label>
                            <input type="number" class="form-control" name="validity_quarters" id="category_validity_quarters" value="2">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Payment Mode</label>
                            <select class="form-control" name="payment_mode" id="category_payment_mode">
                                <option value="MILESTONE">Milestone</option>
                                <option value="STAGEWISE">Stagewise</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Active</label>
                            <select class="form-control" name="is_active" id="category_is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assistance Category Modal END -->

<script>
function showCampaignModal() {
    var el = document.getElementById('campaignModal');
    if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(el).show(); }
    else if (window.jQuery && $('#campaignModal').modal) { $('#campaignModal').modal('show'); }
}
function hideCampaignModal() {
    var el = document.getElementById('campaignModal');
    if (window.bootstrap && bootstrap.Modal) { var inst = bootstrap.Modal.getInstance(el); if (inst) inst.hide(); }
    else if (window.jQuery && $('#campaignModal').modal) { $('#campaignModal').modal('hide'); }
}
function openCampaignModal(id) {
    $('#campaign_id').val(0);
    $('#campaignModalTitle').text('Add Campaign');
    $('#campaignForm')[0].reset();
    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_discount_campaign'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                $('#campaign_id').val(d.id);
                $('#campaign_code').val(d.campaign_code);
                $('#campaign_reason').val(d.reason);
                $('#campaign_discount_percent').val(d.discount_percent);
                $('#campaign_start_date').val(d.start_date);
                $('#campaign_end_date').val(d.end_date);
                $('#campaign_benefit_quarters').val(d.benefit_quarters);
                $('#campaign_payment_mode').val(d.payment_mode);
                $('#campaign_allow_stacking').val(d.allow_stacking);
                $('#campaignModalTitle').text('Edit Campaign');
                showCampaignModal();
            } else { alert(res.message); }
        }, 'json');
    } else { showCampaignModal(); }
}
$('#campaignForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_discount_campaign'); ?>', $(this).serialize(), function(res) {
        if (res.status) { hideCampaignModal(); location.reload(); }
        else { alert(res.message); }
    }, 'json');
});
function activateCampaign(id) {
    if (!confirm('Activate this campaign so it becomes available in the programme billing popup?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/approve_campaign'); ?>/' + id, {approve: 1}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}

function deleteCampaign(id) {
    if (!confirm('Are you sure you want to delete this campaign?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_discount_campaign'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}

function showCategoryModal() {
    var el = document.getElementById('categoryModal');
    if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(el).show(); }
    else if (window.jQuery && $('#categoryModal').modal) { $('#categoryModal').modal('show'); }
}
function hideCategoryModal() {
    var el = document.getElementById('categoryModal');
    if (window.bootstrap && bootstrap.Modal) { var inst = bootstrap.Modal.getInstance(el); if (inst) inst.hide(); }
    else if (window.jQuery && $('#categoryModal').modal) { $('#categoryModal').modal('hide'); }
}
function openCategoryModal(id) {
    $('#category_id').val(0);
    $('#categoryModalTitle').text('Add Assistance Category');
    $('#categoryForm')[0].reset();
    if (id > 0) {
        $.post('<?php echo site_url('admin/patientflowsetting/get_assistance_category'); ?>', {id: id}, function(res) {
            if (res.status) {
                var d = res.data;
                $('#category_id').val(d.id);
                $('#category_group_id').val(d.group_id);
                $('#category_code').val(d.category_code);
                $('#category_discount_percent').val(d.discount_percent);
                $('#category_validity_quarters').val(d.validity_quarters);
                $('#category_payment_mode').val(d.payment_mode);
                $('#category_is_active').val(d.is_active);
                $('#categoryModalTitle').text('Edit Assistance Category');
                showCategoryModal();
            } else { alert(res.message); }
        }, 'json');
    } else { showCategoryModal(); }
}
$('#categoryForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?php echo site_url('admin/patientflowsetting/save_assistance_category'); ?>', $(this).serialize(), function(res) {
        if (res.status) { hideCategoryModal(); location.reload(); }
        else { alert(res.message); }
    }, 'json');
});
function toggleCategory(id, currentStatus) {
    if (!confirm('Are you sure you want to ' + (currentStatus ? 'deactivate' : 'activate') + ' this category?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/toggle_assistance_category'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}
function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    $.post('<?php echo site_url('admin/patientflowsetting/delete_assistance_category'); ?>', {id: id}, function(res) {
        if (res.status) { location.reload(); }
        else { alert(res.message); }
    }, 'json');
}
</script>
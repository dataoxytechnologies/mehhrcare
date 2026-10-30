<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    .pf-chain-step { white-space: normal; line-height: 1.9; }
    .pf-arrow { color: #94a3b8; font-size: 0.7rem; margin: 0 2px; }
    .pf-level-badge { min-width: 68px; font-weight: 600; letter-spacing: .3px; }
</style>

<div class="card">
    <?php if (!empty($unassigned_levels)) { ?>
    <div class="card-body border-bottom py-2">
        <div class="alert alert-warning mb-0 py-2 small d-flex align-items-start gap-2">
            <i class="fa fa-exclamation-triangle mt-1"></i>
            <div>
                <strong><?php echo $this->lang->line('levels_without_role'); ?></strong>
                <?php foreach ($unassigned_levels as $ul) { ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis ms-1"><?php echo $ul->code . ' — ' . $ul->title; ?></span>
                <?php } ?>
                <div class="text-muted"><?php echo $this->lang->line('levels_without_role_hint'); ?></div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-route sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('patient_flow'); ?></h3>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <?php if ($this->rbac->hasPrivilege('patient_flow_levels', 'can_add')) { ?>
            <button type="button" onclick="openLevelForm(0)" class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_level'); ?>
            </button>
            <?php } ?>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('chain'); ?></th>
                    <th><?php echo $this->lang->line('title'); ?></th>
                    <th><?php echo $this->lang->line('assigned_role'); ?></th>
                    <th><?php echo $this->lang->line('counter_type'); ?></th>
                    <th><?php echo $this->lang->line('approve'); ?></th>
                    <th class="text-end text-nowrap"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php
                                $byId = array();
                                foreach ($levels as $l) {
                                    $byId[$l->id] = $l;
                                }
                                foreach ($levels as $l) {
                                    // Build visible chain labels from root down to this node.
                                    $labels = array($l->code);
                                    $cursor = $l->parent_id;
                                    while ($cursor && isset($byId[$cursor])) {
                                        array_unshift($labels, $byId[$cursor]->code);
                                        $cursor = $byId[$cursor]->parent_id;
                                    }
                                    $chain = implode(' <i class="fa fa-arrow-right pf-arrow"></i> ', $labels);
                                ?>
                                <tr>
                                    <td class="pf-chain-step">
                                        <span class="badge bg-primary-subtle text-primary-emphasis pf-level-badge">
                                            <?php echo $l->code; ?>
                                        </span>
                                        <span class="text-muted small"><?php echo $chain; ?></span>
                                    </td>
                                    <td class="align-middle"><?php echo $l->title; ?></td>
                                    <td class="align-middle">
                                        <?php echo $l->role_name ? $l->role_name : '<span class="text-muted">—</span>'; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis"><?php echo $l->counter_type; ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php echo $l->can_approve ? '<span class="badge bg-success-subtle text-success-emphasis"><i class="fa fa-check"></i></span>' : '<span class="text-muted">—</span>'; ?>
                                    </td>
                                    <td class="align-middle text-end text-nowrap">
                                        <?php if ($this->rbac->hasPrivilege('patient_flow_levels', 'can_edit')) { ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"
                                                onclick="openLevelForm(<?php echo $l->id; ?>)">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <?php } ?>
                                        <?php if ($this->rbac->hasPrivilege('patient_flow_levels', 'can_delete')) { ?>
                                        <a href="<?php echo site_url('admin/patientflow/delete/' . $l->id); ?>"
                                           class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"
                                           onclick="return confirm('<?php echo $this->lang->line('confirm_delete'); ?>');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
<!-- ===================== Add / Edit Level Modal ===================== -->
<div class="modal fade sh-modal sh-modal-accent" id="addLevel" tabindex="-1" aria-labelledby="pfLevelModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/patientflow/save'); ?>" method="post" id="pfLevelForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-route me-2 sh-icon-soft"></i><span id="pfLevelModalTitle"><?php echo $this->lang->line('add_level'); ?></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="pf_id" value="">
                    <input type="hidden" name="phase" id="pf_phase" value="P1">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('code'); ?> <small class="req">*</small></label>
                            <input type="text" name="code" id="pf_code" class="form-control" placeholder="P1-L1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('level_no'); ?> <small class="req">*</small></label>
                            <input type="number" name="level_no" id="pf_level_no" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-12">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('title'); ?> <small class="req">*</small></label>
                            <input type="text" name="title" id="pf_title" class="form-control" placeholder="R1 Registration" required>
                        </div>
                    </div>
<div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('assigned_role'); ?></label>
                            <select name="role_id" id="pf_role_id" class="form-select">
                                <option value=""><?php echo $this->lang->line('select_role'); ?></option>
                                <?php foreach ($roles as $role) { ?>
                                    <option value="<?php echo $role['id']; ?>">
                                        <?php echo $role['name'] . ($role['is_system'] == 1 ? ' (system)' : ''); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('parent_level'); ?></label>
                            <select name="parent_id" id="pf_parent_id" class="form-select">
                                <option value=""><?php echo $this->lang->line('root_level'); ?></option>
                                <?php foreach ($levels as $l) { ?>
                                    <option value="<?php echo $l->id; ?>"><?php echo $l->code . ' — ' . $l->title; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label form-label-sm"><?php echo $this->lang->line('counter_type'); ?></label>
                            <select name="counter_type" id="pf_counter_type" class="form-select">
                                <?php foreach ($counter_types as $val => $label) { ?>
                                    <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="can_approve" id="pf_can_approve" value="1">
                                <label class="form-check-label" for="pf_can_approve"><?php echo $this->lang->line('can_approve'); ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var pfLevels = <?php echo json_encode($levels); ?>;

    function openLevelForm(id) {
        $('#pfLevelModalTitle').text(id
            ? '<?php echo $this->lang->line("edit_level"); ?>'
            : '<?php echo $this->lang->line("add_level"); ?>');
        $('#pfLevelForm')[0].reset();
        $('#pf_id').val(id ? id : '');
        $('#pf_phase').val('P1');
        if (id) {
            var row = null;
            for (var i = 0; i < pfLevels.length; i++) {
                if (parseInt(pfLevels[i].id) === parseInt(id)) { row = pfLevels[i]; break; }
            }
            if (row) {
                $('#pf_code').val(row.code);
                $('#pf_level_no').val(row.level_no);
                $('#pf_title').val(row.title);
                $('#pf_role_id').val(row.role_id ? String(row.role_id) : '');
                $('#pf_parent_id').val(row.parent_id ? String(row.parent_id) : '');
                $('#pf_counter_type').val(row.counter_type || '');
                if (parseInt(row.can_approve) === 1) { $('#pf_can_approve').prop('checked', true); }
            }
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addLevel'), { backdrop: 'static', keyboard: false }).show();
    }
    <?php if ($this->session->flashdata('success_msg')) { ?>
    toastr.success('<?php echo $this->session->flashdata('success_msg'); ?>');
    <?php } elseif ($this->session->flashdata('error_msg')) { ?>
    toastr.error('<?php echo $this->session->flashdata('error_msg'); ?>');
    <?php } ?>
</script>
<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('icd10_groups', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/icd10/icd10_groups'); ?>" href="<?php echo base_url(); ?>admin/icd10/icd10group"><?php echo $this->lang->line('icd10_groups'); ?></a></li>
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('icd10_codes', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/icd10/icd10_codes'); ?>" href="<?php echo base_url(); ?>admin/icd10"><?php echo $this->lang->line('icd10_code'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <?php if ($this->rbac->hasPrivilege('icd10_groups', 'can_view')) { ?>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo $this->lang->line('icd10_groups_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($this->rbac->hasPrivilege('icd10_groups', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#addGroupModal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_icd10_group'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('icd10_groups'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example setup-icd10-groups-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('group_name'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($groupsresult)) {
                                        foreach ($groupsresult as $row) { ?>
                                        <tr>
                                            <td><?php echo html_escape($row['group_name']); ?></td>
                                            <td><?php echo html_escape($row['description']); ?></td>
                                            <td class="text-end">
                                                <div class="rowoptionview mt-mius0">
                                                    <?php if ($this->rbac->hasPrivilege('icd10_groups', 'can_edit')) { ?>
                                                        <a onclick="editGroup(<?php echo $row['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('icd10_groups', 'can_delete')) { ?>
                                                        <a onclick="delete_recordByIdReload('admin/icd10/deletegroup/<?php echo $row['id']; ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

<!-- Add Group Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupLabel"><?php echo $this->lang->line('add_icd10_group'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_addgroup" action="<?php echo site_url('admin/icd10/addgroup'); ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('icd10_groups'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="add_group_name" class="form-label"><?php echo $this->lang->line('group_name'); ?> <small class="req">*</small></label>
                                        <input type="text" name="group_name" id="add_group_name" class="form-control" />
                                        <span class="text-danger" id="err_add_group_name"></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="add_description" class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" id="add_description" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_addgroupbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupLabel"><?php echo $this->lang->line('edit_icd10_group'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_editgroup" action="<?php echo site_url('admin/icd10/addgroup'); ?>">
                <input type="hidden" id="edit_grp_id" name="group_id" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('icd10_groups'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="edit_group_name" class="form-label"><?php echo $this->lang->line('group_name'); ?> <small class="req">*</small></label>
                                        <input type="text" name="group_name" id="edit_group_name" class="form-control" />
                                        <span class="text-danger" id="err_edit_group_name"></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="edit_description" class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editgroupbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editGroup(id) {
    $.ajax({
        url: '<?php echo base_url(); ?>admin/icd10/getgroup_data/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#edit_grp_id').val(data.id);
            $('#edit_group_name').val(data.group_name);
            $('#edit_description').val(data.description);
            shModal('editGroupModal').show();
        }
    });
}

$('#form_addgroup').on('submit', (function(e) {
    e.preventDefault();
    var btn = $('#form_addgroupbtn');
    btn.btnLoading();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            btn.btnReset();
            if (data.status == 'fail') {
                var message = '';
                $.each(data.error, function(index, value) { message += value; });
                errorMsg(message);
            } else {
                successMsg(data.message);
                window.location.reload(true);
            }
        },
        error: function() { btn.btnReset(); }
    });
}));

$('#form_editgroup').on('submit', (function(e) {
    e.preventDefault();
    var btn = $('#form_editgroupbtn');
    btn.btnLoading();
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            btn.btnReset();
            if (data.status == 'fail') {
                var message = '';
                $.each(data.error, function(index, value) { message += value; });
                errorMsg(message);
            } else {
                successMsg(data.message);
                window.location.reload(true);
            }
        },
        error: function() { btn.btnReset(); }
    });
}));

$(document).ready(function () {
    modal_click_disabled('addGroupModal');
    modal_click_disabled('editGroupModal');
});
</script>

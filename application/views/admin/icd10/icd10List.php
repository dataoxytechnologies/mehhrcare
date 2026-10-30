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

            <?php if ($this->rbac->hasPrivilege('icd10_codes', 'can_view')) { ?>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo $this->lang->line('icd10_codes_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($this->rbac->hasPrivilege('icd10_codes', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#addModal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_icd10_code'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('icd10_codes'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example setup-icd10-codes-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('icd10_code'); ?> </th>
                                        <th><?php echo $this->lang->line('description'); ?> </th>
                                        <th><?php echo $this->lang->line('group'); ?> </th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($codesresult)) {
                                        foreach ($codesresult as $row) { ?>
                                        <tr>
                                            <td><?php echo html_escape($row['icd_code']); ?></td>
                                            <td><?php echo html_escape($row['icd_description']); ?></td>
                                            <td><?php echo html_escape($row['group_name']); ?></td>
                                            <td class="text-end">
                                                <div class="rowoptionview mt-mius0">
                                                    <?php if ($this->rbac->hasPrivilege('icd10_codes', 'can_edit')) { ?>
                                                        <a onclick="editCode(<?php echo $row['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('icd10_codes', 'can_delete')) { ?>
                                                        <a onclick="delete_recordByIdReload('admin/icd10/delete/<?php echo $row['id']; ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
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

<!-- Add Code Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addModal" tabindex="-1" aria-labelledby="addIcdLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIcdLabel"><?php echo $this->lang->line('add_icd10_code'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_addcode" action="<?php echo site_url('admin/icd10/add'); ?>">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('icd10_code'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="add_group_id" class="form-label"><?php echo $this->lang->line('group'); ?> <small class="req">*</small></label>
                                        <select name="group_id" id="add_group_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                            <?php foreach ($groupsresult as $g) { ?>
                                                <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger" id="err_add_group_id"></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label for="add_icd_code" class="form-label"><?php echo $this->lang->line('icd10_code'); ?> <small class="req">*</small></label>
                                        <input type="text" name="icd_code" id="add_icd_code" class="form-control" />
                                        <span class="text-danger" id="err_add_icd_code"></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="add_icd_description" class="form-label"><?php echo $this->lang->line('description'); ?> <small class="req">*</small></label>
                                        <input type="text" name="icd_description" id="add_icd_description" class="form-control" />
                                        <span class="text-danger" id="err_add_icd_description"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_addcodebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Code Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editIcdLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIcdLabel"><?php echo $this->lang->line('edit_icd10_code'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_editcode" action="<?php echo site_url('admin/icd10/add'); ?>">
                <input type="hidden" id="edit_code_id" name="code_id" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('icd10_code'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="edit_group_id" class="form-label"><?php echo $this->lang->line('group'); ?> <small class="req">*</small></label>
                                        <select name="group_id" id="edit_group_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                            <?php foreach ($groupsresult as $g) { ?>
                                                <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger" id="err_edit_group_id"></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label for="edit_icd_code" class="form-label"><?php echo $this->lang->line('icd10_code'); ?> <small class="req">*</small></label>
                                        <input type="text" name="icd_code" id="edit_icd_code" class="form-control" />
                                        <span class="text-danger" id="err_edit_icd_code"></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="edit_icd_description" class="form-label"><?php echo $this->lang->line('description'); ?> <small class="req">*</small></label>
                                        <input type="text" name="icd_description" id="edit_icd_description" class="form-control" />
                                        <span class="text-danger" id="err_edit_icd_description"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="form_editcodebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCode(id) {
    $.ajax({
        url: '<?php echo base_url(); ?>admin/icd10/get_data/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#edit_code_id').val(data.id);
            $('#edit_icd_code').val(data.icd_code);
            $('#edit_icd_description').val(data.icd_description);
            $('#edit_group_id').val(data.group_id);
            shModal('editModal').show();
        }
    });
}

$('#form_addcode').on('submit', (function(e) {
    e.preventDefault();
    var btn = $('#form_addcodebtn');
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

$('#form_editcode').on('submit', (function(e) {
    e.preventDefault();
    var btn = $('#form_editcodebtn');
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
    modal_click_disabled('addModal');
    modal_click_disabled('editModal');
});
</script>

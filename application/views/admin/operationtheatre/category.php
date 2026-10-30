<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('operation', 'can_view')) { ?>
                            <li><a href="<?php echo site_url('admin/operationtheatre') ?>" class="<?php echo set_sidebar_Submenu('admin/operationtheatre/index'); ?>"><?php echo $this->lang->line('operation'); ?></a></li>
                        <?php } if ($this->rbac->hasPrivilege('operation_category', 'can_view')) { ?>
                            <li><a href="<?php echo site_url('admin/operationtheatre/category') ?>" class="<?php echo set_sidebar_Submenu('admin/operationtheatre/category'); ?>"><?php echo $this->lang->line('operation_category'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div><!--./col-md-3-->
            <?php if ($this->rbac->hasPrivilege('operation_category', 'can_view')) { ?>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('operation_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('operation_category', 'can_add')) { ?>
                            <a onclick="addModal()" class="btn btn-primary btn-sm addcategory"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_category'); ?></a>
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('operation_category_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($category_list)) {
                                        foreach ($category_list as $key => $value) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $value['category'] ?>
                                                </td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('operation_category', 'can_edit')) { ?>
                                                            <a href="javascript:void(0)" onclick="get(<?php echo $value['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('operation_category', 'can_delete')) { ?>
                                                            <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="deletecategory('<?php echo $value['id']; ?>')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
            <?php } ?>
        </div>



<!-- add multiple data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/operationtheatre/addmultiplecategory') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('operation_category'); ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover mb-0" id="tableID_vitals">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('operation_category'); ?><small class="req"> *</small></th>
                                            <th class="sh-w-46"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="set_row"></tbody>
                                </table>
                            </div>
                            <div class="p-3 pt-2 text-end">
                                <a class="btn btn-primary btn-sm" onclick="addrow()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="edit_category" tabindex="-1" aria-labelledby="edit_categoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_categoryLabel"><?php echo $this->lang->line('edit_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" action="<?php echo site_url('admin/operationtheatre/editcategory') ?>" method="post" accept-charset="utf-8">
                <input id="id" name="id" value="" type="hidden">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('operation_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label" for="category_name"><?php echo $this->lang->line('operation_category'); ?></label><small class="req"> *</small>
                                        <input class="form-control" id="category_name" name="category_name" value="<?php echo set_value('operation_category'); ?>">
                                        <span class="text-danger"><?php echo form_error('category_name'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formeditbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        modal_click_disabled('myModal');
        modal_click_disabled('edit_category');
    });

    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            e.preventDefault();
            $("#formaddbtn").btnLoading();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else if (data.status == 2) {
                        errorMsg(data.error);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        $('#formedit').on('submit', (function (e) {
            e.preventDefault();
            $("#formeditbtn").btnLoading();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditbtn").btnReset();
                },
                error: function () {}
            });
        }));
    });

    function get(id) {
        shModal('edit_category').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/operationtheatre/getcategory/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#category_name').val(result.category);
            }
        });
    }

    function addModal() {
        shModal('myModal').show();
        $('#formadd').trigger("reset");
        $('#set_row').html('');
        total_rows = 0;
        addrow();
    }
</script>
<script>
function deletecategory(id) {
    if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
        $.ajax({
            url: '<?php echo base_url()."admin/operationtheatre/deletecategory"; ?>',
            data: {id: id},
            type: "post",
            success: function (res) {
                toastr.success("<?php echo $this->lang->line('record_deleted') ?>", '', {
                    timeOut: 1000,
                    fadeOut: 1000,
                    onHidden: function () { window.location.reload(true); }
                });
            }
        });
    }
}
</script>
<script>
var total_rows = 0;
addrow();
function addrow() {
    var id = total_rows + 1;
    var div = "<tr id='category_row_"+id+"'><td><input type='hidden' name='total_rows[]' value='"+id+"'><input name='category_"+id+"' id='category_"+id+"' type='text' class='form-control' placeholder='<?php echo $this->lang->line('operation_category'); ?>' /></td><td class='text-center align-middle sh-w-46'><button type='button' data-rowid='"+id+"' class='closebtn delete_row btn btn-danger btn-sm'><i class='fa fa-remove'></i></button></td></tr>";
    $('#set_row').append(div);
    total_rows++;
}

$(document).on('click', '.delete_row', function (e) {
    if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")) {
        var del_row_id = $(this).data('rowid');
        $("#category_row_" + del_row_id).remove();
    }
});
</script>

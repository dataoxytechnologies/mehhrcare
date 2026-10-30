<div class="row">
            <div class="col-md-2">
                <?php $this->load->view('admin/referral/referralSidebar'); ?>
            </div>
            <div class="col-md-10"> 
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line("referral_category_list"); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('referral_category', 'can_add')) { ?>
                                <a onclick="addModal()" class="btn btn-primary btn-sm addcategory"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_category'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('referral_category_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered example setup-referral-category-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($category)) { ?>
                                    <?php } else {
                                        foreach ($category as $key => $value) {
                                    ?>
                                            <tr>
                                                <td><?php echo html_escape($value['name']) ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('referral_category', 'can_edit')) { ?>
                                                            <a href="javascript:void(0)" onclick="getRecord('<?php echo (int)$value['id'] ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('referral_category', 'can_delete')) { ?>
                                                            <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/referralcategory/delete/<?php echo (int)$value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
}
}
?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    

<!-- add multiple data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addmultiplerow" tabindex="-1" aria-labelledby="addmultiplerowLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addmultiplerowLabel"><?php echo $this->lang->line('add_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/referralcategory/add_multiple_category/') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('referral_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <label class="form-label"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                <div id="set_row"></div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary btn-sm mt-2" onclick="addrow()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formadd_multiple_btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- add multiple data modal -->

<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="myModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalEditLabel"><?php echo $this->lang->line("edit_category"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editcategory" method="post" accept-charset="utf-8">
                <input type="hidden" id="categoryid" name="categoryid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('referral_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input id="edit_name" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editcategorybtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getRecord(id) {
        shModal('myModalEdit').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralcategory/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#edit_name").val(data.name);
                $("#categoryid").val(id);
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    $(document).ready(function (e) {
        $('#editcategory').on('submit', (function (e) {
            $("#editcategorybtn").btnLoading();

            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcategory/update',
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
                    $("#editcategorybtn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });
    
    $(document).ready(function (e) {
        modal_click_disabled('addmultiplerow');
        modal_click_disabled('myModalEdit');
    });
</script>

<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;
    var div = "<div class='d-flex gap-2 mb-2' id='name_row_"+id+"'><input class='form-control' name='categoryid_"+id+"' id='categoryid_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='name_"+id+"' id='name_"+id+"' type='text' class='form-control' /><button type='button' data-rowid='"+id+"' class='btn btn-sm btn-outline-secondary delete_row flex-shrink-0'><i class='fa fa-times'></i></button></div>";
    $('#set_row').append(div);
    total_rows++;
}
    
$(document).on('click','.delete_row',function(e){
    if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
        var modal_=$(e.target).closest('div.modal');
        var del_row_id=$(this).data('rowid');
        $("#name_row_" + del_row_id).remove();             
    }        
});

$(document).ready(function (e) {
        $('#form_add_multiple').on('submit', (function (e) {
            $("#formadd_multiple_btn").btnLoading();
            e.preventDefault();
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
                    }else if(data.status==2){

                        errorMsg(data.error);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formadd_multiple_btn").btnReset();
                },
                error: function () {

                }
            });

        }));

    });

function addModal(){
    shModal('addmultiplerow').show();
    $('#form_add_multiple').trigger("reset");
    $('#set_row').html('');
    addrow();
}

</script>
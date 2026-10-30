<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <li><a href="<?php echo site_url('admin/visitorspurpose') ?>"><?php echo $this->lang->line('purpose'); ?></a></li>
                        <li><a href="<?php echo site_url('admin/complainttype') ?>" class="active"><?php echo $this->lang->line('complain_type'); ?></a></li>
                        <li><a href="<?php echo site_url('admin/source') ?>"><?php echo $this->lang->line('source'); ?></a></li>                        
                    </ul>
                </div>
            </div><!--./col-md-3-->
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('complaint_type_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('setup_front_office', 'can_add')) {?>
                            <a onclick="addModal()"  class="btn btn-primary btn-sm complain_type"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_complaint_type'); ?></a> <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('complaint_type_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible">
<table class="table table-hover table-striped table-bordered example sh-ex-cols-25-60-15">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('complain_type'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($complaint_type_list)) { ?>
                                <?php } else {
                                 foreach ($complaint_type_list as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $value['complaint_type'] ?></td>
                                        <td><?php echo $value['description']; ?></td>
                                        <td class="text-end">
                                            <div class="rowoptionview mt-mius0">
                                                <?php if ($this->rbac->hasPrivilege('setup_front_office', 'can_edit')) { ?>
                                                    <a href="javascript:void(0)" onclick="get(<?php echo (int)$value['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('setup_front_office', 'can_delete')) { ?>
                                                    <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_complainttype('<?php echo (int)$value['id']; ?>')">
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addmultiplerowLabel"><?php echo $this->lang->line('add_complaint_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/complainttype/add_multiple_complaint_type') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('complain_type'); ?></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover mb-0" id="tableID_vitals">
                                    <thead>
                                        <tr>
                                            <th class="sh-w-45p"><?php echo $this->lang->line('complain_type'); ?><small class="req"> *</small></th>
                                            <th><?php echo $this->lang->line('description'); ?></th>
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
                    <button type="submit" id="formadd_multiple_btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_complaint_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/complainttype/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" name="id" id="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('complain_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('complain_type'); ?></label><small class="req"> *</small>
                                        <input class="form-control" id="complaint_type" name="complaint_type" value="<?php echo set_value('complaint_type'); ?>">
                                        <span class="text-danger"><?php echo form_error('complaint_type'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description1" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editformaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
   
    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/complainttype/get_data/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#complaint_type').val(result.complaint_type);
                $('#description1').val(result.description);
            }
        });
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").btnLoading();
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
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").btnReset();

                },
                error: function () {

                }
            });
        }));
    });
</script>
<script>

    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

$(".complain_type").click(function(){
    $('#formadd').trigger("reset");
});

    $(document).ready(function () {
        modal_click_disabled('editmyModal');
        modal_click_disabled('addmultiplerow');
    });

    function delete_complainttype(id){
        delete_recordByIdReload('admin/complainttype/delete/'+id, '<?php echo $this->lang->line('delete_confirm') ?>')
    }
</script>
<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;    
    var div = "<tr id='name_row_"+id+"'><td><input type='hidden' name='total_rows[]' value='" + id + "'><input name='complaint_type_"+id+"' id='complaint_type_"+id+"' type='text' class='form-control' placeholder='<?php echo $this->lang->line('complain_type'); ?>' /></td><td><input name='description_"+id+"' id='description_"+id+"' type='text' class='form-control' placeholder='<?php echo $this->lang->line('description'); ?>'/></td><td class='text-center align-middle sh-w-46'><button type='button' data-rowid='"+id+"' class='closebtn delete_row btn btn-danger btn-sm'><i class='fa fa-remove'></i></button></td></tr>";
    $('#set_row').append(div);   
    total_rows++;      
}
    
$(document).on('click','.delete_row',function(e){
    if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
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
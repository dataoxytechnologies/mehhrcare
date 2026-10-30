<div class="row">
            <div class="col-md-2">
               <?php
$this->load->view('admin/onlineappointment/appointmentSidebar');
?> 
            </div><!--./col-md-3-->

            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('appointment_priority_list'); ?> </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                             <?php if ($this->rbac->hasPrivilege('appointment_priority', 'can_add')) {?>
                            <a onclick="addModal()"  class="btn btn-primary btn-sm addappointment"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_priority'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('appointment_priority_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-hover table-striped table-bordered example setup-priority-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('priority'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($appoint_priority_list)) { ?>
                                <?php
                                } else {
                                    foreach ($appoint_priority_list as $key => $value) { ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $value['appoint_priority'] ?></a>
                                                </td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('appointment_priority', 'can_edit')) {?>
                                                            <a href="javascript:void(0)" onclick="get(<?php echo $value['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php }?>
                                                        <?php if ($value['id'] > 1) { ?>
                                                            <?php if ($this->rbac->hasPrivilege('appointment_priority', 'can_delete')) {?>
                                                                <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_appointpriority('<?php echo $value['id']; ?>')">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            <?php } ?>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addmultiplerowLabel"><?php echo $this->lang->line('add_priority'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/appointpriority/add_multiple_priority') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('priority'); ?></span>
                            </div>
                            <div class="p-3">
                                <label class="form-label"><?php echo $this->lang->line('priority'); ?></label><small class="req"> *</small>
                                <div id="set_row"></div>
                                <div class="text-end">
                                    <a class="btn btn-primary btn-sm mt-2" onclick="addrow()" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formadd_multiple_btn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_priority'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/appointpriority/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('priority'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('priority'); ?></label><small class="req"> *</small>
                                        <input class="form-control" id="appoint_priority_edit" name="appoint_priority" value="<?php echo set_value('appoint_priority'); ?>"/>
                                        <span class="text-danger"><?php echo form_error('appoint_priority'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="editformaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
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
</script>

<script>
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
                    } else {

                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {

                }
            });


        }));

    });

    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/appointpriority/get_data/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#appoint_priority_edit').val(result.appoint_priority);
            }
        });
    }

    $(document).ready(function (e) {

        $('#editformadd').on('submit', (function (e) {
            e.preventDefault();
            $("#editformaddbtn").btnLoading();
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


$(".addappointment").click(function(){
    $('#formadd').trigger("reset");
});

    $(document).ready(function (e) {
        modal_click_disabled('addmultiplerow');
        modal_click_disabled('editmyModal');
    });

    function delete_appointpriority(id){
        delete_recordByIdReload('admin/appointpriority/delete/'+id, '<?php echo $this->lang->line('delete_confirm') ?>')
    }
</script>


<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;
    var div = "<div class='d-flex gap-2 mb-2' id='name_row_"+id+"'><input class='form-control' name='id_"+id+"' id='id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='appoint_priority_"+id+"' id='appoint_priority_"+id+"' type='text' class='form-control' /><button type='button' data-rowid='"+id+"' class='btn btn-sm btn-outline-secondary flex-shrink-0 delete_row'><i class='fa fa-times'></i></button></div>";
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
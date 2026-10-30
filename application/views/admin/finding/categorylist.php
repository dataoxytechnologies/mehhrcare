<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                   <ul class="tablists">
                          <?php
                        if ($this->rbac->hasPrivilege('finding', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/finding'); ?>"  href="<?php echo base_url(); ?>admin/finding" ><?php echo $this->lang->line('finding'); ?></a></li>
                        <?php } ?> 
                        <?php if ($this->rbac->hasPrivilege('finding_category', 'can_view')) { ?>
                            <li><a  class="<?php echo set_sidebar_Submenu('setup/finding/category'); ?>" href="<?php echo base_url(); ?>admin/finding/category" ><?php echo $this->lang->line('category'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo $this->lang->line('finding_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($this->rbac->hasPrivilege('finding_category', 'can_add')) { ?>
                                <a onclick="addModal()" class="btn btn-primary btn-sm finding_head"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_finding_category'); ?></a>
                            <?php } ?>     
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('finding_category_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
<table class="table table-striped table-bordered table-hover example sh-ex-cols-85-15">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($findingresult)) {
                                       
                                        foreach ($findingresult as $category) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $category['category'] ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('finding_category', 'can_edit')) { ?>
                                                            <a data-bs-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('finding_category', 'can_delete')) { ?>
                                                            <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/finding/deletefindingtype/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
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
            </div>
            <!-- right column -->
        </div>   <!-- /.row -->
    
</div>

<!-- add multiple data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addmultiplerow" tabindex="-1" aria-labelledby="addFindingCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFindingCategoryLabel"><?php echo $this->lang->line('add_finding_category') ;?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/finding/add_multiple_findingcategory') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('finding_category'); ?></span>
                            </div>
                            <div class="table-responsive overflow-visible">
                                <table class="table mb0 tblProducts sh-tbl-transparent" id="tableID_vitals">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('finding_category'); ?> <small class="req">*</small></th>
                                            <th class="sh-w-46"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="set_row"></tbody>
                                </table>
                            </div>
                            <div class="p-3 pt-2 text-end">
                                <a class="btn btn-primary btn-sm" onclick="addrow()" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></a>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editFindingCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFindingCategoryLabel"><?php echo $this->lang->line('edit_finding_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/finding/editcategory') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="finding_id" name="finding_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('finding_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('finding_category'); ?></label><small class="req"> *</small>
                                        <input id="findingcategory" name="finding_category" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('finding_category'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
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
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';    

    $("#print_div").click(function () {
        popup($('#exphead').html());
    });
</script>
<script>
    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
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
            dataType: 'Json',
            url: '<?php echo base_url(); ?>admin/finding/getfindingcategory/' + id,
            success: function (result) {  
                $('#finding_id').val(result.id);
                $('#findingcategory').val(result.category);
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
                dataType: 'Json',
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

$(".finding_head").click(function(){
	$('#formadd').trigger("reset");	
});

    $(document).ready(function (e) {
        modal_click_disabled('addmultiplerow');
        modal_click_disabled('editmyModal');
    });
</script>

<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;   
    var div = "<tr id='name_row_"+id+"'><td><input class='form-control' name='finding_id_"+id+"' id='finding_id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='finding_category_"+id+"' id='finding_category_"+id+"'  type='text' class='form-control'  /></td><td class='text-center align-middle sh-w-46'><button type='button' data-rowid='"+id+"' class='closebtn delete_row btn btn-danger btn-sm'><i class='fa fa-remove'></i></button></td></tr>";
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
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">					
                        <?php if ($this->rbac->hasPrivilege('item_category', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/itemcategory" class="active"><?php echo $this->lang->line('item_category'); ?></a></li>
                        <?php }?>
                        <?php if ($this->rbac->hasPrivilege('store', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/itemstore"><?php echo $this->lang->line('item_store'); ?></a></li>
                        <?php }?>
                        <?php if ($this->rbac->hasPrivilege('supplier', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/itemsupplier"><?php echo $this->lang->line('item_supplier'); ?></a></li>
                        <?php }?>
                    </ul>
                </div>
            </div>
			<?php if ($this->rbac->hasPrivilege('item_category', 'can_view')) {?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('item_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('item_category', 'can_add')) {?>
                            <a onclick="addModal()" class="btn btn-primary btn-sm itemcategory"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item_category'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body  ">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('item_category_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example setup-itemcategory-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item_category'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categorylist)) {
    ?>
                                        <?php
} else {
    $count = 1;
    foreach ($categorylist as $category) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                        <span>
                                                            <a href="#" data-bs-toggle="popover" class="detail_popover">
                                                                <?php echo $category['item_category'] ?>
                                                            </a>
                                                            <div class="fee_detail_popover d-none">
                                                            <?php
                                                            if ($category['description'] == "") { ?>
                                                                <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                            } else { ?>
                                                                <p class="text text-info"><?php echo $category['description']; ?></p>
                                                            <?php
                                                            } ?>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="rowoptionview mt-mius0">
                                                            <?php if ($this->rbac->hasPrivilege('item_category', 'can_edit')) { ?>
                                                                <a onclick="get(<?php echo $category['id']; ?>)" data-bs-target="#editmyModal" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                            <?php } if ($this->rbac->hasPrivilege('item_category', 'can_delete')) { ?>
                                                                <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/itemcategory/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_confirm') ?>')">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                </td>
                                            </tr>
                                            <?php
}
    $count++;
}
?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div>
			<?php }?>			
        </div>   <!-- /.row -->
    
</div>

<!-- add multiple data modal -->
<div class="modal fade sh-modal sh-modal-accent" id="addmultiplerow" tabindex="-1" aria-labelledby="addItemCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemCategoryLabel"><i class="fa fa-plus me-1"></i> <?php echo $this->lang->line('add_item_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_multiple" action="<?php echo site_url('admin/itemcategory/add_multiple_itemcategory') ?>" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_category'); ?></span>
                            </div>
                            <div class="table-responsive overflow-visible">
                                <table class="table tblProducts align-middle mb-0 sh-tbl-transparent" id="tableID_vitals">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('item_category'); ?><small class="req"> *</small></th>
                                            <th><?php echo $this->lang->line('description'); ?></th>
                                            <th class="sh-w-44"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="set_row"></tbody>
                                </table>
                            </div>
                            <div class="p-3 pt-2 text-end">
                                <a class="btn btn-primary btn-sm" onclick="addrow()" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
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


<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editItemCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemCategoryLabel"><i class="fa fa-pencil me-1"></i> <?php echo $this->lang->line('edit_item_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/itemcategory/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="cat_id" name="cat_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_category'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <input autofocus id="itemcategory1" name="itemcategory" type="text" class="form-control form-control-sm">
                                        <span class="text-danger"><?php echo form_error('itemcategory'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description1" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
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

<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>

<script>
    $(document).ready(function () {
        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var td = el.closest('td');
                    var inner = td ? td.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
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
    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/itemcategory/get_data/' + id,
            success: function (result) {
                $('#cat_id').val(result.id);
                $('#itemcategory1').val(result.item_category);
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

	$(".itemcategory").click(function(){
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
    var div = "<tr id='name_row_"+id+"'><td><input class='form-control form-control-sm' name='cat_id_"+id+"' id='cat_id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='itemcategory_"+id+"' id='itemcategory_"+id+"' type='text' class='form-control form-control-sm' /></td><td><input name='description_"+id+"' id='description_"+id+"' type='text' class='form-control form-control-sm'/></td><td class='sh-w-44'><button type='button' data-rowid='"+id+"' class='btn btn-sm btn-outline-secondary delete_row'><i class='fa fa-times'></i></button></td></tr>";
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
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('item_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/itemcategory" ><?php echo $this->lang->line('item_category'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('store', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/itemstore" class="active"><?php echo $this->lang->line('item_store'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('supplier', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/itemsupplier"><?php echo $this->lang->line('item_supplier'); ?></a></li>
                    <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('item_store_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('store', 'can_add')) { ?>
                            <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm itemstore"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item_store'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('item_store_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example setup-itemstore-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item_store_name'); ?></th>
                                        <th><?php echo $this->lang->line('item_stock_code'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($itemstorelist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($itemstorelist as $store) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover">
                                                        <?php echo $store['item_store'] ?>
                                                    </a>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
                                                        if ($store['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $store['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name"><?php echo $store['code'] ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('store', 'can_edit')) { ?>
                                                            <a data-bs-target="#editmyModal" onclick="get(<?php echo $store['id']; ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('store', 'can_delete')) { ?>
                                                            <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/itemstore/delete/<?php echo $store['id'] ?>', '<?php echo $this->lang->line('delete_confirm') ?>')">
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
        </div>   <!-- /.row -->
    
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addItemStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemStoreLabel"><?php echo $this->lang->line('add_item_store'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/itemstore/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_store'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('item_store_name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('item_stock_code'); ?></label>
                                        <input id="code" name="code" type="text" class="form-control" value="<?php echo set_value('code'); ?>" />
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editItemStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemStoreLabel"><?php echo $this->lang->line('edit_item_store'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/itemstore/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="store_id" name="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_store'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('item_store_name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name1" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('item_stock_code'); ?></label>
                                        <input id="code1" name="code" type="text" class="form-control" value="<?php echo set_value('code'); ?>" />
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description1" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
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
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/itemstore/get_data/' + id,
            success: function (result) {
                $('#store_id').val(result.id);
                $('#name1').val(result.item_store);
                $('#code1').val(result.code);
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

$(".itemstore").click(function(){
	$('#formadd').trigger("reset");
});
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
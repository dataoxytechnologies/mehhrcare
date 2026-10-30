<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('item_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/itemcategory" ><?php echo $this->lang->line('item_category'); ?></a></li>
                        <?php } if ($this->rbac->hasPrivilege('store', 'can_view')) {?>
                        <li><a href="<?php echo base_url(); ?>admin/itemstore"><?php echo $this->lang->line('item_store'); ?></a></li>
                        <?php } if ($this->rbac->hasPrivilege('supplier', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/itemsupplier" class="active"><?php echo $this->lang->line('item_supplier'); ?></a></li>
                    <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('item_supplier_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('supplier', 'can_add')) { ?>
                            <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm itemsupplier"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item_supplier'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('item_supplier_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example setup-itemsupplier-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item_supplier'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($itemsupplierlist)) {
                                    ?>
                                    <?php
                                    } else {
                                        $count = 1;
                                        foreach ($itemsupplierlist as $supplier) {
                                            ?>
                                            <tr> 
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover">
                                                        <?php echo $supplier['item_supplier'] ?>
                                                        <br>
                                                    </a>
                                                    <?php
                                                    if ($supplier['phone'] != "") {
                                                        ?>
                                                        <i class="fa fa-phone-square"></i> <?php echo $supplier['phone'] ?>
                                                        <br>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($supplier['email'] != "") {
                                                        ?>
                                                        <i class="fa fa-envelope"></i> <?php echo $supplier['email'] ?>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
                                                        if ($supplier['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $supplier['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php
                                                    if ($supplier['contact_person_name'] != "") {
                                                        ?>
                                                        <i class="fa fa-user"></i> <?php echo $supplier['contact_person_name'] ?>
                                                        <br>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($supplier['contact_person_phone'] != "") {
                                                        ?>
                                                        <i class="fa fa-phone-square"></i> <?php echo $supplier['contact_person_phone'] ?>
                                                        <br>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($supplier['contact_person_email'] != "") {
                                                        ?>
                                                        <i class="fa fa-envelope"></i> <?php echo $supplier['contact_person_email'] ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="mailbox-name">
                                                        <span>
                                                        <?php
                                                        if ($supplier['address'] != "") {
                                                            ?>
                                                            <i class="fa fa-building"></i> <?php echo $supplier['address'] ?>
                                                            <?php
                                                        }
                                                        ?>
                                                        </span>
                                                </td>
                                                <td class="text-end">
                                                        <div class="rowoptionview mt-mius0">
                                                            <?php if ($this->rbac->hasPrivilege('supplier', 'can_edit')) { ?>
                                                                <a onclick="get(<?php echo $supplier['id']; ?>)" data-bs-target="#editmyModal" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                            <?php } if ($this->rbac->hasPrivilege('supplier', 'can_delete')) { ?>
                                                                <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/itemsupplier/delete/<?php echo $supplier['id'] ?>', '<?php echo $this->lang->line('delete_confirm') ?>')">
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
            <!-- right column -->
        </div>   <!-- /.row -->
    
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addItemSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemSupplierLabel"><?php echo $this->lang->line('add_item_supplier'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/itemsupplier/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_supplier'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="phone" name="phone" type="text" class="form-control" value="<?php echo set_value('phone'); ?>" />
                                        <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('email'); ?></label>
                                        <input id="text" name="email" type="text" class="form-control" value="<?php echo set_value('email'); ?>" />
                                        <span class="text-danger"><?php echo form_error('email'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input id="contact_person_name" name="contact_person_name" type="text" class="form-control" value="<?php echo set_value('contact_person_name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input id="contact_person_phone" name="contact_person_phone" type="text" class="form-control" value="<?php echo set_value('contact_person_phone'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_phone'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_email'); ?></label>
                                        <input id="contact_person_email" name="contact_person_email" type="email" class="form-control" value="<?php echo set_value('contact_person_email'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_email'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo set_value('address'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editItemSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemSupplierLabel"><?php echo $this->lang->line('edit_item_supplier'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/itemsupplier/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="supp_id" name="supp_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item_supplier'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name1" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="phone1" name="phone" type="text" class="form-control" value="<?php echo set_value('phone'); ?>" />
                                        <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('email'); ?></label>
                                        <input id="email1" name="email" type="text" class="form-control" value="<?php echo set_value('email'); ?>" />
                                        <span class="text-danger"><?php echo form_error('email'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input id="contact_person_name1" name="contact_person_name" type="text" class="form-control" value="<?php echo set_value('contact_person_name'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input id="contact_person_phone1" name="contact_person_phone" type="text" class="form-control" value="<?php echo set_value('contact_person_phone'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_phone'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('contact_person_email'); ?></label>
                                        <input id="contact_person_email1" name="contact_person_email" type="email" class="form-control" value="<?php echo set_value('contact_person_email'); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_email'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control" id="address1" name="address" rows="3"><?php echo set_value('address'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
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
            url: '<?php echo base_url(); ?>admin/itemsupplier/get_data/' + id,
            success: function (result) {
                $('#supp_id').val(result.id);
                $('#name1').val(result.item_supplier);
                $('#phone1').val(result.phone);
                $('#email1').val(result.email);
                $('#description1').val(result.description);
                $('#address1').val(result.address);
                $('#contact_person_name1').val(result.contact_person_name);
                $('#contact_person_phone1').val(result.contact_person_phone);
                $('#contact_person_email1').val(result.contact_person_email);
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


$(".itemsupplier").click(function(){
	$('#formadd').trigger("reset");
});

    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
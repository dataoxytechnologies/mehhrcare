<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <li><a href="<?php echo base_url(); ?>admin/medicinecategory/medicine" class="active"> <th><?php echo $this->lang->line('medicine_category'); ?></th></a></li>
                        <li><a href="<?php echo base_url(); ?>admin/supplier/supplier" class="active"> <th><?php echo $this->lang->line('supplier'); ?></th></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('medicine_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('medicine_category', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_medicine_category'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls"></div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('medicine_category_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category_name'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($medicineCategory as $category) {
                                        ?>
                                        <tr>
                                            <td><?php echo $category['medicine_category']; ?></td>
                                            <td class="text-end">
                                                <?php if ($this->rbac->hasPrivilege('medicine_category', 'can_edit')) { ?>
                                                    <a data-bs-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <?php
                                                }
                                                if ($this->rbac->hasPrivilege('medicine_category', 'can_delete')) {
                                                    ?>
                                                    <a  class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordById('<?php echo base_url(); ?>admin/medicinecategory/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_message'); ?>')";>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mailbox-controls"></div>
                </div>
            </div> 
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add') . " " . $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                        <input autofocus name="medicine_category" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["medicine_category"]; } ?>">
                        <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/medicinecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                        <input autofocus id="medicine_category" name="medicine_category" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["medicine_category"]; } ?>">
                        <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                        <input type="hidden" id="id" name="medicinecategoryid">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            url: '<?php echo base_url(); ?>admin/medicinecategory/get_data/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#medicine_category').val(result.medicine_category);
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
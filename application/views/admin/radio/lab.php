<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
						<?php if ($this->rbac->hasPrivilege('radiology_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/lab/addlab" class="active"><?php echo $this->lang->line('radiology_category'); ?></a></li>
						<?php } if ($this->rbac->hasPrivilege('radiology_unit', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/lab/unit" class=""><?php echo $this->lang->line('unit'); ?></a></li>
						<?php } if ($this->rbac->hasPrivilege('radiology_parameter', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/lab/radioparameter" class=""><?php echo $this->lang->line('radiology_parameter'); ?></a></li>
						<?php } ?>
                    </ul>
                </div>
            </div>			
			<?php if ($this->rbac->hasPrivilege('radiology_category', 'can_view')) { ?>
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('radiology_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('radiology_category', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm radiology"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_radiology_category'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('radiology_category_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example setup-radio-category-fixed" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category_name'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($labName as $lab) {
                                        ?>
                                        <tr>
                                            <td><?php echo $lab['lab_name']; ?></td>
                                            <td class="text-end">
                                                <div class="rowoptionview mt-mius0">
                                                    <?php if ($this->rbac->hasPrivilege('radiology_category', 'can_edit')) { ?>
                                                        <a href="javascript:void(0)" onclick="get(<?php echo $lab['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('radiology_category', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/lab/delete/<?php echo $lab['id'] ?>" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
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
			<?php } ?>			
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_radiology_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/lab/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('radiology_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category_name'); ?></label><small class="req"> *</small>
                                        <input name="lab_name" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('lab_name'); ?></span>
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

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_radiology_category'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/lab/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="lab_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('radiology_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category_name'); ?></label><small class="req"> *</small>
                                        <input id="lab_name" name="lab_name" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["lab_name"]; } ?>">
                                        <span class="text-danger"><?php echo form_error('lab_name'); ?></span>
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
            url: '<?php echo base_url(); ?>admin/lab/get_data/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#lab_name').val(result.lab_name);
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

	$(".radiology").click(function(){
		$('#formadd').trigger("reset");
	});

    $(document).ready(function () {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
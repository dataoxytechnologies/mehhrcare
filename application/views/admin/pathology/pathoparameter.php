<div class="row">           
            <div class="col-md-2">
                <div class="card border0">
                      <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('pathology_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/pathologycategory/addcategory"><?php echo $this->lang->line('pathology_category'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('pathology_unit', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/pathologycategory/unit" class=""><?php echo $this->lang->line('unit'); ?></a></li>
                         <?php } if ($this->rbac->hasPrivilege('pathology_parameter', 'can_view')) { ?>
                        <li><a class="active" href="<?php echo base_url(); ?>admin/pathologycategory/pathoparameter" class=""><?php echo $this->lang->line('pathology_parameter'); ?></a></li>
                         <?php }  ?>
                    </ul>
                </div>
            </div>            
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('pathology_parameter_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('pathology_parameter', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm pathology"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_pathology_parameter'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('pathology_parameter_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example setup-patho-parameter-fixed" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('parameter_name'); ?></th>
                                        <th><?php echo $this->lang->line('reference_range'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($parameterName as $lab) {
                                        ?>
                                        <tr>
                                            <td><?php echo $lab['parameter_name']; ?></td>
                                            <td><?php echo $lab['reference_range']; ?></td>
                                            <td><?php echo $lab['unit_name']; ?></td>
                                            <td><?php echo $lab['description']; ?></td>
                                            <td class="text-end">
                                                <div class="rowoptionview mt-mius0">
                                                    <?php if ($this->rbac->hasPrivilege('pathology_parameter', 'can_edit')) { ?>
                                                        <a href="javascript:void(0)" onclick="get(<?php echo $lab['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('pathology_parameter', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/pathologycategory/deleteparameter/<?php echo $lab['id'] ?>" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
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
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_pathology_parameter'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/pathologycategory/addparameter') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('pathology_parameter'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('parameter_name'); ?></label><small class="req"> *</small>
                                        <input name="parameter_name" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('parameter_name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                        <input name="from_reference_range" placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                        <input name="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                        <select name="unit" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($unitname as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                    <div class="mb-0 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <input name="description" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_pathology_parameter'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/pathologycategory/addparameter') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="pathology_parameter_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('pathology_parameter'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('parameter_name'); ?></label><small class="req"> *</small>
                                        <input id="parameter_name" name="parameter_name" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('parameter_name'); ?></span>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                        <input name="from_reference_range" id="from_reference_range" placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                        <input name="to_reference_range" id="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                        <select name="unit" id="unit" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($unitname as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                    <div class="mb-0 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <input id="description" name="description" type="text" class="form-control">
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
            url: '<?php echo base_url(); ?>admin/pathologycategory/get_data_parameter/' + id,
            success: function (result) {
                var range = result.reference_range.split("-");
                $('#id').val(result.id);
                $('#parameter_name').val(result.parameter_name);
                $('#test_value').val(result.test_value);               
                $('#from_reference_range').val(range[0]);
                $('#to_reference_range').val(range[1]);
                $('#unit').val(result.unit);
                $('#description').val(result.description);
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
                        var message = " ";
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
                error: function () {}
            });
        }));
    });
	
	$(".pathology").click(function(){
		$('#formadd').trigger("reset");
	});

    $(document).ready(function () {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
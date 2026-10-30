<div class="row">   
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('leave_types', 'can_view')) { ?>
                            <li><a href="<?php echo base_url(); ?>admin/leavetypes" class="active"><?php echo $this->lang->line('leave_type'); ?></a></li>
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('department', 'can_view')) { ?>
                            <li><a href="<?php echo base_url(); ?>admin/department"><?php echo $this->lang->line('department'); ?></a></li>
                        <?php } if ($this->rbac->hasPrivilege('designation', 'can_view')) { ?>
                            <li><a href="<?php echo base_url(); ?>admin/designation/designation"><?php echo $this->lang->line('designation'); ?></a></li>
                        <?php } ?> 
                           <?php if ($this->rbac->hasPrivilege('specialist', 'can_view')) { ?>
                            <li><a href="<?php echo base_url(); ?>admin/specialist"><?php echo $this->lang->line('specialist'); ?></a></li>
                        <?php } ?>                   
                    </ul>
                </div>
            </div>			
			<?php if ($this->rbac->hasPrivilege('leave_types', 'can_view')) { ?>
            <div class="col-md-10">              
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('leave_type_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('leave_types', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm leavetype"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_leave_type'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('leave_type_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist"  data-export-title="<?php echo $this->lang->line('leave_type_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
										<th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>
			<?php } ?>			
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_leave_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/leavetypes/createLeaveType') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('leave_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" name="type" type="text" class="form-control" value="<?php if (isset($result)) { echo $result["type"]; } ?>" />
                                        <span class="text-danger"><?php echo form_error('type'); ?></span>
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
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_leave_type'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/leavetypes/leaveedit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="leavetypeid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('leave_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="type" name="type" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('type'); ?></span>
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

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {    
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/leavetypes/getleavetypesdatatable',{},[],100,[
            { "aTargets": [-1], "searchable": false, "bSortable": false, "sClass": "text-end" }
        ]);
    });
} ( jQuery ) )
</script>

<!-- //========datatable end===== --> 
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
            url: '<?php echo base_url(); ?>admin/leavetypes/get_type/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#type').val(result.type);
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

    function deleterecord(id)
    {
        delete_recordById('admin/leavetypes/leavedelete/' + id, '<?php echo $this->lang->line('delete_confirm'); ?>');
    }

    $(".leavetype").click(function(){
    	$('#formadd').trigger("reset");
    });
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
  
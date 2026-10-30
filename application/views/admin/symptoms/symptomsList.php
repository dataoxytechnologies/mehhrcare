<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                          <?php
                        if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/symptoms/symptoms_head'); ?>"  href="<?php echo base_url(); ?>admin/symptoms" ><?php echo $this->lang->line('symptoms_head'); ?></a></li>
                        <?php } ?> 
                        <?php if ($this->rbac->hasPrivilege('symptoms_type', 'can_view')) { ?>
                            <li><a  class="<?php echo set_sidebar_Submenu('setup/symptoms/symptoms_type'); ?>" href="<?php echo base_url(); ?>admin/symptoms/symptomstype" ><?php echo $this->lang->line('symptoms_type'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo $this->lang->line('symptoms_head_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm symptoms_head"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_symptoms_head'); ?></a>
                            <?php } ?>     
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('symptoms_head_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
						<?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
                            <table class="table table-striped table-bordered table-hover example setup-symptoms-head-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('symptoms_head'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms_type'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms_description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($symptomsresult)) { ?>
                                    <?php
                                    } else {
                                        foreach ($symptomsresult as $category) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $category['symptoms_title'] ?></td>
                                                <td class="mailbox-name"><?php echo $category['symptoms_type'] ?></td>
                                                <td class="mailbox-name"><?php echo nl2br($category['description']) ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_edit')) { ?>
                                                            <a data-bs-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('symptoms_head', 'can_delete')) { ?>
                                                            <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/symptoms/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
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
						<?php } ?>
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div>
			<?php } ?>
            <!-- right column -->
        </div>   <!-- /.row -->
    
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addSymptomsHeadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSymptomsHeadLabel"><?php echo $this->lang->line('add_symptoms_head'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/symptoms/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('symptoms_head'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="symptoms_title" class="form-label"><?php echo $this->lang->line('symptoms_head'); ?><small class="req"> *</small></label>
                                        <input autofocus="" id="symptoms_title" name="symptoms_title" type="text" class="form-control" value="<?php echo set_value('symptoms'); ?>" />
                                        <span class="text-danger"><?php echo form_error('symptoms'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?><small class="req"> *</small></label>
                                        <select name="type" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($symptomsresulttype as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['symptoms_type']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('type'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="description" class="form-label"><?php echo $this->lang->line('description'); ?></label>
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
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editSymptomsHeadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSymptomsHeadLabel"><?php echo $this->lang->line('edit_symptoms_head'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/symptoms/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="symptoms_id" name="symptoms_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('symptoms_head'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="symptomstitle" class="form-label"><?php echo $this->lang->line('symptoms_head'); ?><small class="req"> *</small></label>
                                        <input id="symptomstitle" name="symptoms_title" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('symptoms'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label for="symtype" class="form-label"><?php echo $this->lang->line('symptoms_type'); ?><small class="req"> *</small></label>
                                        <select name="type" id="symtype" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($symptomsresulttype as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['symptoms_type']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="description_edit" class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description_edit" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
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
            dataType: 'Json',
            url: '<?php echo base_url(); ?>admin/symptoms/get_data/' + id,
            success: function (result) {
           console.log(result.symptoms_type)
                $('#symptoms_id').val(result.id);
                $('#symptomstitle').val(result.symptoms_title);
                $('#symtype').val(result.type);
                $('#description_edit').val(result.description);
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
	
	$(".symptoms_head").click(function(){
		$('#formadd').trigger("reset");
	});
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
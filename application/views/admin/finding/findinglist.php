<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                          <?php
                        if ($this->rbac->hasPrivilege('finding', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/finding/finding_head'); ?>"  href="<?php echo base_url(); ?>admin/finding" ><?php echo $this->lang->line('finding'); ?></a></li>
                        <?php } ?> 
                        <?php if ($this->rbac->hasPrivilege('finding_category', 'can_view')) { ?>
                            <li><a  class="<?php echo set_sidebar_Submenu('setup/finding/category'); ?>" href="<?php echo base_url(); ?>admin/finding/category" ><?php echo $this->lang->line('category'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			
			<?php
                        if ($this->rbac->hasPrivilege('finding', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><?php echo $this->lang->line('finding_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
							 <?php
                        if ($this->rbac->hasPrivilege('finding', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm finding"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_finding'); ?></a>   
						<?php } ?>								
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('finding_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
<table class="table table-striped table-bordered table-hover example sh-ex-cols-25-20-40-15">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('finding'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('finding_description'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($findingresult)) {
                                        foreach ($findingresult as $category) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $category['name'] ?></td>
                                                <td class="mailbox-name"><?php echo $category['category'] ?></td>
                                                <td class="mailbox-name"><?php echo nl2br($category['description']) ?></td>
                                                <td class="text-end">
                                                    <div class="rowoptionview mt-mius0">
                                                        <?php if ($this->rbac->hasPrivilege('finding', 'can_edit')) { ?>
                                                            <a data-bs-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('finding', 'can_delete')) { ?>
                                                            <a class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/finding/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
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
			<?php } ?>
            <!-- right column -->
        </div>   <!-- /.row -->
    
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_finding'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/finding/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('finding'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('finding'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control" value="<?php echo set_value('finding'); ?>" />
                                        <span class="text-danger"><?php echo form_error('finding'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category'); ?></label><small class="req"> *</small>
                                        <select name="type" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($findingresulttype as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['category']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('type'); ?></span>
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
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_finding'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/finding/edit') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="finding_id" name="finding_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('finding'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('finding'); ?></label><small class="req"> *</small>
                                        <input id="findingtitle" name="name" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('finding'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('category'); ?></label><small class="req"> *</small>
                                        <select name="type" id="findingcategory" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($findingresulttype as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['category']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
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
        Popup($('#exphead').html());
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
            url: '<?php echo base_url(); ?>admin/finding/get_data/' + id,
            success: function (result) {
                $('#finding_id').val(result.id);
                $('#findingtitle').val(result.name);
                $('#findingcategory').val(result.finding_category_id);
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
	
$(".finding").click(function(){
	$('#formadd').trigger("reset");
});
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('editmyModal');
    });
</script>
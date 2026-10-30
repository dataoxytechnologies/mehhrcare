<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('content_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap addmeeting">
                            <?php if ($this->rbac->hasPrivilege('upload_content', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm uploadcontent"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('upload_content'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('content_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('content_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('content_title'); ?></th>
                                        <th><?php echo $this->lang->line('type'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th width="50%"><?php echo $this->lang->line('description'); ?></th>                  
                                        <th width="10%" class="text-end noExport"><?php echo $this->lang->line('action'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    

<div class="modal fade sh-modal sh-modal-branded" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('upload_content'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="upload_content" action="<?php echo site_url('admin/content') ?>" name="employeeform" method="post" enctype='multipart/form-data' accept-charset="utf-8">
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) {
                        echo $this->session->flashdata('msg');
                        $this->session->unset_userdata('msg');
                    }?>
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('content_title'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="content_title" name="content_title" type="text" class="form-control" value="<?php echo set_value('content_title'); ?>" />
                            <span class="text-danger"><?php echo form_error('content_title'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('content_type'); ?></label><small class="req"> *</small>
                            <input type="text" id="content_type" name="content_type" class="form-control">
                            <span class="text-danger"><?php echo form_error('content_type'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('upload_date'); ?></label>
                            <input id="upload_date" name="upload_date" type="text" class="form-control date" value="<?php echo set_value('upload_date'); ?>" />
                            <span class="text-danger"><?php echo form_error('upload_date'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label"><?php echo $this->lang->line('content_file'); ?></label><small class="req"> *</small>
                            <input class="filestyle form-control" data-height="40" type='file' name='file' id="file" size='20' />
                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                            <textarea class="form-control" id="description" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="upload_contentbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/content/delete/' + id,
                    success: function (res) {
                        successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                        window.location.reload(true);
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
        }
    } 
    
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

    $(document).ready(function (e) {
        $("#upload_content").on('submit', (function (e) {
            $("#upload_contentbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/content/add',
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
                    $("#upload_contentbtn").btnReset();
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function edit(id) {     
        $.ajax({
            url: '<?php echo base_url(); ?>admin/expense/getDataByid/' + id,
            success: function (data) {                
                $('#edit_expensedata').html(data);
            }
        });
    }

$(".uploadcontent").click(function(){
    $('#upload_content').trigger("reset");
    $(".dropify-clear").trigger("click");
});

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'myModaledit');
    });
</script>

<!-- //========datatable start===== -->
 <script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/content/getcontentdatatable');
    });
} ( jQuery ) )
</script> 
<!-- //========datatable end===== -->
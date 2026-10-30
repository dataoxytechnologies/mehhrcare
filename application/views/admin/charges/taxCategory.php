<div class="row"> 
            <div class="col-md-2">
                <div class="card border0">
                    <?php $this->load->view("admin/charges/sidebar"); ?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('tax_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('tax_category', 'can_add')) {?>
                                <a onclick="add()" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_tax_category'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('tax_category_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('percentage'); ?>(%)</th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
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
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><span id="title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/taxcategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" id="id" name="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('tax_category'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('percentage'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <input type="text" class="form-control right-border-none text-end" name="percentage" id="percentage" autocomplete="off">
                                            <span class="input-group-text">%</span>
                                        </div>
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

<script>
	function add(){
		$('#title').html('<?php echo $this->lang->line('add_tax_category'); ?>');
        $("#formadd").trigger('reset');
		shModal('myModal').show();
	}
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
                      shModal('myModal').hide();
                      $('.ajaxlist').DataTable().ajax.reload();
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });
	
 $(document).on('click','.edit_record',function(){
     var record_id=$(this).data('recordId');
     var btn = $(this);
 $.ajax({ 
            url: base_url+'admin/taxcategory/getDetails',
            type: "POST",
            data: {tax_id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.btnLoading();
                 },
            success: function (data) {
                     if (data.status == 0) {                     
                        errorMsg(message);
                    } else {
                    	$('#title').html('<?php echo $this->lang->line('edit_tax_category'); ?>');
                    	$('#id').val(data.id);
                    	$('#name').val(data.name);
                    	$('#percentage').val(data.percentage);
						shModal('myModal').show();
                    }
                 btn.btnReset();
            }, 
            error: function () {
               btn.btnReset();
                },
                complete: function(){
                 btn.btnReset();
   }
        });

 });
 
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
	
</script>
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/taxcategory/getDatatable',{},[],100,
    [{"bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right dt-head-right'}]);
    });
} ( jQuery ) )
</script>
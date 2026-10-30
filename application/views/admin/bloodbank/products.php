<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                      <li>
                        <a href="<?php echo base_url(); ?>admin/bloodbank/products" class="<?php echo set_sidebar_Submenu('admin/bloodbank/products'); ?>" ><?php echo $this->lang->line('products'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('product_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('blood_bank_product', 'can_add')) {?>
                                <a onclick="add()" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_product'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?= $this->lang->line('product_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('type'); ?></th>                                       
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
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
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/bloodbank/add_product') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input id="id" name="id" type="hidden" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('products'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('type'); ?></label><small class="req"> *</small>
                                        <select class="form-control" id="type" name="type">
                                            <option value=""><?php echo $this->lang->line('select');?></option>
                                            <?php foreach ($this->customlib->getblood_bank_type() as $key => $value) { ?>
                                                <option value="<?php echo $key;?>"><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control" />
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
		$('#title').html("<?php echo $this->lang->line('add_product'); ?>");
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
                        window.location.reload(true);
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
            url: base_url+'admin/bloodbank/getproductDetails',
            type: "POST",
            data: {id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.btnLoading();
                 },
            success: function (data) {
                     if (data.status == 0) {
                     
                        errorMsg(message);
                    } else {
                    	$('#title').html("<?php echo $this->lang->line('edit_product'); ?>");
                    	$('#id').val(data.id);
                    	$('#name').val(data.name);
                        $('#type').val(data.is_blood_group);
                        $('#volume').val(data.volume);
                        $('#unit').val(data.charge_unit_id);
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
        initDatatable('ajaxlist','admin/bloodbank/getproductlist',{},[],100,[
            { "aTargets": [-1], "searchable": false, "bSortable": false, "sClass": "text-end" }
        ]);
        $('.ajaxlist').on('draw.dt', function () {
            $(this).find('[data-bs-toggle="tooltip"]').each(function () {
                new bootstrap.Tooltip(this);
            });
        });
    });
} ( jQuery ) )
</script>
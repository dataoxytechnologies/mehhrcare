<div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <?php $this->load->view("admin/charges/sidebar"); ?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('charge_category_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('charge_category', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" id="add_charge_type_modal" class="btn btn-primary btn-sm charge_category"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charge_category'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('charge_category_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
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
                <h5 class="modal-title" id="myModalLabel"><span id="modal_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/chargecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8">
                <input type="hidden" name="id" value="0" class="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('charge_category_details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small>
                                        <select name="charge_type" class="form-control charge_type">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $charge_key => $charge_value) { ?>
                                                <option value="<?php echo $charge_value->id; ?>"><?php echo $charge_value->charge_type; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input autofocus="" id="name" name="name" type="text" class="form-control name" value="" />
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                                        <textarea name="description" class="form-control description"></textarea>
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
                      $('.ajaxlist').DataTable().ajax.reload();
                      shModal('myModal').hide();
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
            url: base_url+'admin/chargecategory/get_data',
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
                   $('.id').val(data.result.id);
                   $('.name').val(data.result.name);                   
                   $('.description').val(data.result.description);                
                   $('.charge_type option[value="'+data.result.charge_type_id+'"]').prop('selected', true);
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

</script>
<script type="text/javascript">
$('#myModal').on('hidden.bs.modal', function (e) {    
        $('#formadd').find('input:text').val(''); 
        $('#formadd').find('.description').val('');        
        $('.charge_type option:selected').prop('selected', false);      
        $("#formadd").find('input.id').val(0);
});

    $('#add_charge_type_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_charge_category'); ?>');
    })

    $(document).on('click','.edit_charge_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_charge_category'); ?>');
    })
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
</script>
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/chargecategory/getDatatable');
    });
} ( jQuery ) )
</script>
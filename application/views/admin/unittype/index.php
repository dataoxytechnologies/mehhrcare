<div class="row">
         <div class="col-md-2">
                <div class="card border0">
                    <?php $this->load->view("admin/charges/sidebar"); ?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card" id="tachelist">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('unit_type_list'); ?> </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        <?php if ($this->rbac->hasPrivilege('unit_type', 'can_add')) { ?>
                            <button type="button" data-record-id="0" class="btn btn-primary btn-sm addunittype add_unit_type_modal" id="load2" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing') ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_unit_type'); ?></button>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('unit_type_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('unit_type'); ?></th>
                                        <th class="text-end noExport sh-th-140"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mailbox-controls"></div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm400 modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitModalLabel"><span id="modal_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/unittype/add') ?>" method="post" accept-charset="utf-8">
                <input type="hidden" name="id" id="id" value="0">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('unit_type'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                        <input name="unit" id="unit" type="text" class="form-control">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" id="load2" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
 $(document).ready(function(){
    modal_click_disabled('unitModal');
 });
 
 $(document).on('click','.edit_unittype',function(){
    var record_id=$(this).data('recordId');
    var btn = $(this);
        $.ajax({
                url: base_url+'admin/unittype/getByUnitId',
                type: "POST",
                data: {'id':record_id},
                dataType: 'JSON',
                beforeSend: function(){
                    btn.btnLoading();
                },
                success: function (data) {

                if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        $('#unit').val(data.result.unit);
                        $('#id').val(data.result.id);
                        shModal('unitModal').show();
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

    $(document).on('click','.addunittype',function(){
       var record_id=$(this).data('recordId');
       shModal('unitModal').show();
    });

    $(document).on('click','.delect_record',function(e){
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
        var record_id=$(this).data('recordId');
          $.ajax({
                url: base_url+'admin/unittype/delete',
                type: "POST",
                data: {'id':record_id},
                dataType: 'JSON',
                beforeSend: function(){ 
                
                },
                success: function (data) {
                    if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                         successMsg(data.message);

                        $('.ajaxlist').DataTable().ajax.reload();
                    } 
                },
                error: function () {

                },
                complete: function(){ 
   }
            });
}
        });

    $('#unitModal').on('hidden.bs.modal', function (e) {
     $("form#formadd").find('input:text, input:password, input:file').val('');
    })
    $(document).on('submit','form#formadd',function(e){

            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var btn = form.find("button[type=submit]");

            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',
                    beforeSend: function(){
                 btn.btnLoading();
                 },

                success: function (data) {
               if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         shModal('unitModal').hide();
                        $('.ajaxlist').DataTable().ajax.reload();
                    }
                 btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                    btn.btnReset();
                }
            });
        });

    $('.add_unit_type_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_unit_type'); ?>');
    })

    $(document).on('click','.edit_unit_type_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_unit_type'); ?>');
    })
</script>
<script type="text/javascript">
    $('.aa').on('click', function() {
    var $this = $(this);
  $this.btnLoading();
    setTimeout(function() {
       $this.btnReset();
   }, 8000);
});
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/unittype/getdatatable');
    });
</script>
<!-- //========datatable end===== -->
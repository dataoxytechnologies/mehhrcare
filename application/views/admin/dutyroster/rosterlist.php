<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
   <div class="col-md-12">
    <div class="card">
     <div class="card-header ptbnull">
      <h3 class="card-title titlefix"><?php echo $this->lang->line("roster_list"); ?></h3>
      <div class="d-flex gap-2 align-items-center flex-wrap float-end">
	  <?php if ($this->rbac->hasPrivilege('roster_list', 'can_add')) {?>
       <a onclick="shModal('myModal').show()" id="add_roster_list" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_roster"); ?></a>
	  <?php } ?>
      </div>
     </div><!-- /.card-header -->
     <div class="card-body">
      <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line("roster_list"); ?>">
       <thead>
        <tr>
         <th><?php echo $this->lang->line('shift_name'); ?></th>
         <th><?php echo $this->lang->line('start_date'); ?></th>
         <th><?php echo $this->lang->line('end_date'); ?></th>
         <th><?php echo $this->lang->line('shift_start'); ?></th>
         <th><?php echo $this->lang->line('shift_end'); ?></th>
         <th><?php echo $this->lang->line('shift_hour'); ?></th>
         <th><?php echo $this->lang->line('roster_days'); ?></th>
         <th class="text-end noExport "><?php echo $this->lang->line('action'); ?></th>
        </tr>
       </thead>
       <tbody>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><i class="fa fa-list me-2"></i><span id="modal_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <input type="hidden" id="id" name="id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('shift_name'); ?> <small class="req">*</small></label>
                                        <select class="form-select form-select-sm" id="duty_roster_shift_id" name="duty_roster_shift_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach($shift_list as $key=>$value){ ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['shift_name'].' ('.$this->customlib->getHospitalTime_Format($value['shift_start']) .' - '. $this->customlib->getHospitalTime_Format($value['shift_end']).')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('start_date'); ?> <small class="req">*</small></label>
                                        <input name="duty_roster_start_date" id="duty_roster_start_date" type="text" class="form-control form-control-sm date" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('end_date'); ?> <small class="req">*</small></label>
                                        <input name="duty_roster_end_date" id="duty_roster_end_date" type="text" class="form-control form-control-sm date" />
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

<script type="text/javascript">

 $("#add_roster_list").click(function(){
     $('#formadd').trigger("reset");
     $('#modal_title').text('<?php echo $this->lang->line('add_roster'); ?>');  
 });
 
 $(document).on('click','.edit_roster_list_modal',function(){
     $('#modal_title').text('');
     $('#modal_title').text('<?php echo $this->lang->line('edit_roster'); ?>');
 });
 
 $(document).ready(function (e) {
         $("#formadd").on('submit', (function (e) {       
             e.preventDefault();
             $("#formaddbtn").btnLoading();
             $.ajax({
                 url: '<?php echo base_url(); ?>admin/dutyroster/add_roster_list',
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
                     }else if(data.status == 2){
                         errorMsg(data.error);
                     }else {
                        successMsg(data.message);
                        window.location.reload(true);
                     }
                 },
                 error: function () {
                 },
                 complete: function(){
                    $("#formaddbtn").btnReset();
                 }
             });
         }));
     });
 
    function delete_roster_list(id) {         
         if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
             $.ajax({
                 url: base_url + 'admin/dutyroster/delete_roster_list/' + id,
                 type: 'POST',
                 dataType: "json",
                 success: function (res) {
                     if (res.status == "fail") {
                         errorMsg(res.message);
                     } else {
                         successMsg("<?php echo $this->lang->line('delete_message') ?>");                        
                         window.location.reload(true);
                     }
                 }
             })
         }
    }
    
    $(function() {
        /* .time init removed - auto-init via class + event delegation */
    });     
 
    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input.schedule_charge').val(standard_charge);
    }    
    
    $(document).on('click','.edit_record',function(){
        var record_id=$(this).data('record_id');
        var btn = $(this);
        $.ajax({
             url: base_url+'admin/dutyroster/getRosterListDetails',
             type: "POST",
             data: {record_id: record_id},
             dataType: 'json',
             beforeSend: function(){
                  btn.btnLoading();
             },
             success: function (data) {
                if (data.status == 0) {                     
                     errorMsg(message);
                 } else {               
                     $('#id').val(data.result.id);
                     $('#duty_roster_shift_id').val(data.result.duty_roster_shift_id);
                     $('#duty_roster_start_date').val(data.result.roster_start_date);
                     $('#duty_roster_end_date').val(data.result.roster_end_date);
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


<!-- //========datatable start===== -->
<script type="text/javascript">
 ( function ( $ ) {
     'use strict';
     $(document).ready(function () {        
         initDatatable('ajaxlist','admin/dutyroster/getRosterListDatatable',{},[],100,[{"aTargets": [ -1,-1 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
     });
 } ( jQuery ) )
</script>
<!-- //========datatable end===== -->
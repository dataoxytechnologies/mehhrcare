<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('vital_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end"> 
                            <?php if ($this->rbac->hasPrivilege('vital', 'can_add')) {?>
                            <a onclick="openAddVital()" id="add_vital_modal" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_vital'); ?></a>                        
                            <?php } ?>
                        </div>                        
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('vital_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('reference_range'); ?></th>
                                    <th><?php echo $this->lang->line('unit'); ?></th>
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

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="vitalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="modal_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <input type="hidden" class="id" name="vital_id" id="vital_id">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('vital'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="vital_name" class="form-label"><?php echo $this->lang->line('vital_name'); ?></label><small class="req"> *</small>
                                        <input type="text" name="vital_name" id="vital_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label d-block"><?php echo $this->lang->line('reference_range'); ?></label>
                                        <div class="small text-muted mb-2">(<?php echo $this->lang->line('if_vital_is_having_single_value_rather_than_range_then_enter_only_from_textbox_value'); ?>)</div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input autofocus="" name="from_reference_range" id="from_reference_range" placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control" />
                                                <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                                            </div>
                                            <div class="col-md-6">
                                                <input name="to_reference_range" id="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control" />
                                                <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label for="unit" class="form-label"><?php echo $this->lang->line('unit'); ?></label>
                                        <input type="text" name="unit" id="unit" class="form-control">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
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
<!-- dd -->

<script type="text/javascript">
    $(function () {
    $('.select2').select2();
});

    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input.schedule_charge').val(standard_charge);
    }    
    
    function delete_vital(id) {         
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
            $.ajax({
                url: base_url + 'admin/vital/delete/' + id,
                 
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
</script>

<script type="text/javascript"> 
    
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {       
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vital/add_vital',
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

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        /* picker init removed - auto-init via class + event delegation in footer.php */
    });

 $(document).on('click','.edit_record',function(){
     var record_id=$(this).data('recordId');
     var btn = $(this);
    $('#unit').val('');     
 $.ajax({
            url: base_url+'admin/vital/getDetails',
            type: "POST",
            data: {vital_id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.btnLoading();
                 },
            success: function (data) {
                     if (data.status == 0) {                     
                        errorMsg(message);
                    } else {               
                    $('#vital_id').val(data.result.id);
                    $('#vital_name').val(data.result.name);
                    $('#from_reference_range').val(data.result.min_range);
                    $('#to_reference_range').val(data.result.max_range);
                    $('#unit').val(data.result.unit);
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
 
    function openAddVital() {
        $('#formadd')[0].reset();
        $("#formadd").find('input.id').val(0);
        $('#modal_title').text('<?php echo $this->lang->line('add_vital'); ?>');
        shModal('myModal').show();
    }

    $(document).ready(function (e) {
        modal_click_disabled('myModal');
    });
</script>
<script type="text/javascript">
     $('#myModal').on('hidden.bs.modal', function (e) {
        $('#formadd').find('input:text').val(''); 
        $('#formadd input:checkbox').removeAttr('checked');       
        $('.charge_type option:selected').prop('selected', false);
        $('.unit_type option:selected').prop('selected', false);
        $("#formadd").find('input.id').val(0);
        $('#charge_category').html('').select2({data: [{id: '', text: 'Select'}]});
    });

    $('#add_vital_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_vital'); ?>');
    })

    $(document).on('click','.edit_vital_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_vital'); ?>');
    })
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/vital/getDatatable',{},[],100,[
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
<!-- //========datatable end===== -->


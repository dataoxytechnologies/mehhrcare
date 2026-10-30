<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row g-0">
    <div class="col-md-12">
        <div class="card border-0 rounded-0">
            <?php $this->load->view('admin/report/_appointment');?>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0"><?php echo $this->lang->line('appointment_report'); ?></h5>
            </div>
            <div class="card-body pb-0">
                <form id="form1" action="" method="post">
                    <div class="row">
                            <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                                <select class="form-control" name="search_type"  id="search_type_select" onchange="showdate(this.value)">
                                                <option value=""><?php echo $this->lang->line('select')?></option> 
                                                    <?php foreach ($searchlist as $key => $search) { ?>
                                                        <option value="<?php echo $key ?>" <?php
                                                        if ((isset($search_type)) && ($search_type == $key)) {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $search ?></option>
                                                        <?php }?>
                                                </select>
                                                <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                            </div>
                                        </div> 

                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line("doctor"); ?></label>
                                                    <select class="form-control select2 w-100" onchange="getDoctorShift()" name="collect_staff" id="collect_staff_select">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctorlist as $dkey => $value) { ?>
                                                            <option value="<?php echo $value["id"] ?>"<?php
                                                        if ((isset($doctorlist_select)) && ($doctorlist_select == $value["id"])) {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $value["name"] . " " . $value["surname"] ." (". $value["employee_id"].")" ?></option>
                                                        <?php }?>
                                                    </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line("shift"); ?></label>
                                                    <select class="form-control select2 w-100" name="shift" id="shift">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line("appointment_priority"); ?></label>
                                                <select class="form-control select2 appointment_priority_select2 w-100" name='priority'>
                                                    <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"> 
                                                        <?php echo $dvalue["appoint_priority"]; ?>
                                                    </option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line("source"); ?></label>
                                                    <select class="form-control select2 w-100" name="appointment_type" id="appointment_type">
                                                        <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach($appointment_type as $typekey => $typevalue){ ?>
                                                        <option value="<?php echo $typekey; ?>"><?php echo $typevalue; ?></option>
                                                   <?php } ?>
                                                    </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line("status"); ?></label>
                                                <select class="form-control select2 w-100" name="appointment_status" id="appointment_status">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {  ?>
                                                    <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                          
                                        <div class="col-sm-6 col-md-3 d-none" id="fromdate">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                                <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_from"><?php echo form_error('date_from'); ?></span>
												 
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 d-none" id="todate">
                                            <div class="mb-3">
                                                <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                                <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_to"><?php echo form_error('date_to'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                                            <div class="mb-3">
                                                <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                                    <i class="fa fa-search"></i>
                                                    <?php echo $this->lang->line('search'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                </form>
            </div><!-- /.card-body -->

            <div class="card-body pt-0">
                <div class="download_label"><?php echo $this->lang->line('appointment_report'); ?></div>
                <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('appointment_report'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('doctor'); ?></th>
                                            <th><?php echo $this->lang->line('source'); ?></th>
                                            <?php 
                                            if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                             }
                                            ?> 
                                            <th class="text text-end"><?php echo $this->lang->line('discount').' (%)'; ?></th>
                                            <th class="text text-end"><?php echo $this->lang->line('fees').' ('.$currency_symbol.')'; ?></th>
                                            <th class="text text-end"><?php echo $this->lang->line('status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     </tbody>  
                                </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div><!-- /.col -->
</div><!-- /.row -->
<script type="text/javascript">
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').removeClass('d-none');
            $('#todate').removeClass('d-none');
        } else {
            $('#fromdate').addClass('d-none');
            $('#todate').addClass('d-none');
        }
    }
</script>
<script>
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
         emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/appointment/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                     $("#error_collect_staff").html('');
                     $("#error_date_from").html('');
                     $("#error_date_to").html('');
                        initDatatable('allajaxlist', 'admin/appointment/appointmentreports/',data.param,[],100,[
                            { "aTargets": [ -3 ] ,'sClass': 'dt-body-right'},
                            { "aTargets": [ -2 ] ,'sClass': 'dt-body-right'},
                            { "aTargets": [ -1 ] ,'sClass': 'dt-body-right', "bSortable": false}
                        ]);
                        
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

    function getDoctorShift(prev_val = 0){
        var doctor_id = $("#collect_staff_select").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            type: 'POST',
            url: base_url + "admin/onlineappointment/doctorshiftbyid",
            data: {doctor_id:doctor_id},
            dataType: 'json',
            success: function(res){
                $.each(res, function(i, list){
                    selected = list.id == prev_val ? "selected" : "";
                    select_box += "<option value='"+ list.id +"' "+selected+">"+ list.name +"</option>";
                });
                $("#shift").html(select_box);
           }
        });
    }
</script>
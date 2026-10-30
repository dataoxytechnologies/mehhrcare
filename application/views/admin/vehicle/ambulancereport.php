<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                 <?php $this->load->view('admin/report/_ambulance');?>
                 <div class="card-header ptbnull"></div>
                    <div class="card-header">                       
                        <h3 class="card-title"><?php echo $this->lang->line('ambulance_call_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post" class="">
                        <div class="card-body row pb-0">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3" >
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                         <option value=""><?php echo $this->lang->line('select'); ?></option> 
                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('collected_by'); ?></label>
                                    <select class="form-control select2 w-100" name="collect_staff">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($staffsearch as $dkey => $value) { ?>
                                            <option value="<?php echo $value["staffid"] ?>"<?php
                                        if ((isset($staffsearch_select)) && ($staffsearch_select == $value["staffid"])) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $value["staffname"] . " " . $value["staffsurname"] ." (". $value["employee_id"].")" ?></option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 d-none" id="fromdate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 d-none" id="todate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>                        
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('vehicle_model'); ?></label>
                                    <select name="vehicle_number" id="vehicle_number" class="form-control select2 " >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($vehiclelist as $key => $vehicle) {
                                                ?>
                                        <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex align-items-end">
                                <div class="mb-3">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2 checkbox-toggle">
                                        <i class="fa fa-search"></i>
                                        <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('ambulance_call_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line("ambulance_call_no"); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('contact_no'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                        <th><?php echo $this->lang->line('driver_name'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <?php 
                                        if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            }
                                        ?> 
                                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th> 
                                        <th class="text-end"><?php echo $this->lang->line('discount') ; ?></th> 
                                        <th class="text-end"><?php echo $this->lang->line('tax') ; ?></th> 
                                        <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th> 									
                                        <th class="text-end"><?php echo $this->lang->line('paid') . " (" . $currency_symbol . ")"; ?></th> 
                                        <th class="text-end"><?php echo $this->lang->line('balance') . " (" . $currency_symbol . ")"; ?></th>                                      
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
	</div>
 <script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
</script>

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
            url: '<?php echo base_url(); ?>admin/vehicle/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                console.log(data.param)
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                     $("#error_collect_staff").html('');
                    initDatatable('allajaxlist', 'admin/vehicle/ambulancereports/',data.param,[],100,
                        [
                       {  "sWidth": "100px", "orderable": false,"aTargets": [ -1,] ,'sClass': 'dt-body-right'},
                       {  "sWidth": "100px", "aTargets": [ -2,-3 ] ,'sClass': 'dt-body-right'}
                    
                    ]);
                }
            }
        });
        }
       ));
   });
} ( jQuery ) );
</script>
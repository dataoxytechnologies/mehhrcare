<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<div class="row">
            <div class="col-md-12">
                <div class="card">
                <?php $this->load->view('admin/report/_blood_bank');?>
                 <div class="card-header ptbnull"></div>
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('blood_donor_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="card-body row pb-0">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-4" >
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
                                                <?php }?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-4 d-none" id="fromdate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 d-none" id="todate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label> <?php echo $this->lang->line('blood_group'); ?></label>
                                    <select class="form-control select2 blood_group w-100" name="blood_group" id="blood_group">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php

                                        foreach ($stockbloodgroup as $blood_grp_value) {
                                            ?>
                                            <option value="<?php echo $blood_grp_value['id']; ?>"><?php echo $blood_grp_value['name']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select> 
                                </div>
                           </div>

                           <div class="col-sm-3">
                                <div class="mb-3">
                                    <label> <?php echo $this->lang->line('blood_donor'); ?></label>
                                    <select class="form-control select2 blood_donor w-100" name="blood_donor" id="blood_donor">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select> 
                                </div>
                           </div>

                            <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                                <div class="mb-3">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2 checkbox-toggle">
                                        <i class="fa fa-search"></i>
                                        <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body pt-0">
                        <div class="download_label"><?php echo $this->lang->line('blood_donor_report'); ?></div>
                        <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('blood_donor_report'); ?>">
                                    <thead>  
                                       <tr>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <th><?php echo $this->lang->line('bags'); ?></th>
                                        <th><?php echo $this->lang->line('donor_name'); ?></th>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <th><?php echo $this->lang->line('donate_date'); ?></th>
                                         <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                                <?php
                                            } 
                                        }
                                    ?> 
                                        <th><?php echo $this->lang->line('amount').'('.$currency_symbol.')'; ?></th>
                                        <th><?php echo $this->lang->line('discount'); ?></th>
                                        <th><?php echo $this->lang->line('tax'); ?></th>
                                        <th><?php echo $this->lang->line('net_amount').'('.$currency_symbol.')'; ?></th>
                                        <th><?php echo $this->lang->line('paid').'('.$currency_symbol.')'; ?></th>
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
    </div>
<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
</script>
<script type="text/javascript">
    $(document).ready(function (e) {
        showdate('<?php echo $search_type; ?>');
        var from_age =$("#from_age").val();
        var to_age="" ;
        var to_age = "<?php if(isset($_POST['to_age'])){ echo $_POST['to_age']; } ?>" ;
        setagerange(from_age,to_age);
    });

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
<script type="text/javascript">
    $("#from_age").change(function(){
        var from_age =$("#from_age").val();
        setagerange(from_age);
    });

    function setagerange(from_age,to_age=null)
    {
       
        $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/setagerange',
                type: "POST",
                data: {from_age:from_age,to_age:to_age},
                success: function (data) {
                    $("#to_age").html(data);
                } 
        });
    } 
</script>
<script>
    $(document).ready(function (e) {      
        emptyDatatable('allajaxlist', 'data');
    });
    
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/checkvalidationblooddonor',
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
 initDatatable('allajaxlist','admin/bloodbank/blooddonorreports',data.param,[],100,
            [
{ "sWidth": "55px", "bSortable": false, "aTargets": [ -1] ,'sClass': 'dt-head-right dt-body-right'},
{ "sWidth": "70px", "aTargets": [ -2 ] ,'sClass': 'dt-head-right dt-body-right'},
{ "sWidth": "85px", "aTargets": [ -3,-4 ] ,'sClass': 'dt-head-right dt-body-right'},
{ "sWidth": "65px", "aTargets": [ -5 ] ,'sClass': 'dt-head-right dt-body-right'},
{ "sWidth": "75px", "aTargets": [ 4 ] },
{ "sWidth": "120px", "aTargets": [ 2 ] },
{ "sWidth": "20px", "aTargets": [ 0 ] ,'sClass': 'dt-head-center dt-body-center'}
            ] 
            );
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

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
    $("#blood_group" ).change(function() {
      var div_data ="" ;
      var id = this.value;

        $('#blood_donor').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/get_donor_list/'+id,
            type: "POST",
            dataType: 'json',
            async: false,
            success: function (res) {

                $.each(res, function (i, obj)
                {
                    var sel= "";
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.donor_name + "</option>";
                });

              
                $("#blood_donor").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('#blood_donor').append(div_data);
                $("#blood_donor").select2();

                
            }
        });
   
 });

} ( jQuery ) );
</script>
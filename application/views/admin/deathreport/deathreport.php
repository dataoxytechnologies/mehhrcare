<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php $this->load->view('admin/report/_birth_death');?>
                    <div class="card-header ptbnull"></div>                    
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('death_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="card-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3 col-lg-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('search_type'); ?><small class="req"> *</small></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
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

                            <div class="col-sm-6 col-md-3 col-lg-3 d-none" id="fromdate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3 d-none" id="todate">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly"/>
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 col-lg-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control w-100"  name="gender" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach($gender as $key => $value ){ ?>
                                         <option value="<?php echo $key; ?>"><?php echo $value ; ?></option>
                                     <?php } ?>
                                          
                                    </select>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor'); ?></span>
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
                    <div class="card border0 clear">
                        <div class="card-header ptbnull"></div>
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('death_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('reference_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <th><?php echo $this->lang->line('death_date'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                       <!--  <th><?php echo $this->lang->line('date'); ?></th> -->
                                          <?php 
                                            if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            }
                                        ?> 
                                       <th><?php echo $this->lang->line('report'); ?></th>
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

<script type="text/javascript">
    $(document).ready(function (e) {
        emptyDatatable('allajaxlist', 'data');
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
<script>
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/checkvalidation',
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
                initDatatable('allajaxlist', 'admin/birthordeath/deathreports/',data.param,[],100,
                        [
                          {  "sWidth": "100px", "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-left'},
                        ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

</script>
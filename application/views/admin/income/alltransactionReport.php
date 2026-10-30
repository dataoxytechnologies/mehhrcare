<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_finance'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('transaction_report') ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end"></div>
            </div>
            <div class="card-body pb-0">
                <form id="form1" action="" method="post">
                    <div class="row">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                <select class="form-control" name="search_type" id="search_type_select" onchange="showdate(this.value)">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($searchlist as $key => $search) { ?>
                                        <option value="<?php echo $key ?>" <?php if ((isset($search_type)) && ($search_type == $key)) { echo "selected"; } ?>><?php echo $search ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('collected_by'); ?></label>
                                <select class="form-control select2 w-100" name="collect_staff" id="collect_staff_select">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($staffsearch as $dkey => $value) { ?>
                                        <option value="<?php echo $value["staffid"] ?>" <?php if ((isset($staffsearch_select)) && ($staffsearch_select == $value["staffid"])) { echo "selected"; } ?>><?php echo $value["staffname"] . " " . $value["staffsurname"] . " (" . $value["employee_id"] . ")"; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger" id="error_collect_staff"><?php echo form_error('collect_staff'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 d-none" id="fromdate">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                <input id="date_from" name="date_from" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" />
                                <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 d-none" id="todate">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                <input id="date_to" name="date_to" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" />
                                <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('select_head'); ?></label>
                                <select class="form-control select2 w-100" name="modules_select" id="modules_select">
                                    <?php foreach ($modules as $key => $search) { ?>
                                        <option value="<?php echo $key ?>" <?php if ((isset($modules_type)) && ($modules_type == $key)) { echo "selected"; } ?>><?php echo $search ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger" id="error_modules_staff"><?php echo form_error('modules_staff'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3 pe-md-3">
                            <div class="mb-3">
                                <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                    <i class="fa fa-search"></i>
                                    <?php echo $this->lang->line('search'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-0">
                <div class="download_label"><?php echo $this->lang->line('transaction_report'); ?></div>
                <table class="table table-striped table-bordered table-hover allajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('transaction_report'); ?>">
                    <thead>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th id="clmname"></th>
                        <th><?php echo $this->lang->line('reference'); ?></th>
                        <th><?php echo $this->lang->line('category'); ?></th>
                        <th id="collection-generated-clm"></th>
                        <th><?php echo $this->lang->line('payment_type'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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
            var formData = new FormData(this);
            formData.append('search', 'search_filter');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/income/checkvalidation',
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

                        if (($("#modules_select").val()) == 'payroll_report') {
                            $("#clmname").html('<?php echo $this->lang->line('staff_name'); ?>');
                            $("#collection-generated-clm").html('<?php echo $this->lang->line('generated_by'); ?>');
                        } else {
                            $("#clmname").html('<?php echo $this->lang->line('patient_name'); ?>');
                            $("#collection-generated-clm").html('<?php echo $this->lang->line('collected_by'); ?>');
                        }

                        initDatatable('allajaxlist', 'admin/income/transactionreports/', data.param, [], 100,
                            [
                                { "sWidth": "90px",  "aTargets": [0], 'sClass': 'dt-body-left' },
                                { "sWidth": "150px", "aTargets": [1], 'sClass': 'dt-body-left' },
                                { "sWidth": "150px", "aTargets": [-1], 'sClass': 'dt-body-right' }
                            ]);
                    }
                }
            });
        }));
    });
} ( jQuery ) );
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#clmname").html('<?php echo $this->lang->line('patient_name'); ?>');
        $("#collection-generated-clm").html('<?php echo $this->lang->line('collected_by'); ?>');
    });
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        popup(jQuery(elem).html());
    }
</script>

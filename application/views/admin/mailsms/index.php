<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card" >
                <?php $this->load->view('admin/report/_log'); ?>
                <div class="card-header ptbnull"></div>
                    <div class="card-header">                        
                        <h3 class="card-title"> <?php echo $this->lang->line('email_sms_log'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <form id="form1" action="" method="post">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('search_type'); ?></label>
                                        <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                            <option value=""><?php echo $this->lang->line('all') ?></option>
                                            <?php foreach ($searchlist as $key => $search) {
                                                ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if ((isset($search_type)) && ($search_type == $key)) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $search ?></option>
                                                    <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('search_type'); ?></span>
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
                    </div><!-- /.card-body -->
                        <div class="card-body table-responsive mailbox-messages pt-0">
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('email_sms_log'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('email'); ?></th>
                                        <th><?php echo $this->lang->line('sms'); ?></th>
                                        <th><?php echo $this->lang->line('group'); ?></th>
                                        <th><?php echo $this->lang->line('individual'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->                    
                </div>
            </div>         
            <div class="col-md-8">
            </div>          
        </div>
        <div class="row">           
            <div class="col-md-12">
            </div>
        </div>

<script type="text/javascript">

    $(document).ready(function () {
        /* #postdate init removed - auto-init via class + event delegation */
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });

    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        popup(jQuery(elem).html());
    }    
</script>

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function (e) {
        showdate('<?php echo $search_type; ?>');
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
            url: '<?php echo base_url(); ?>admin/mailsms/checkvalidation',
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
                  
                initDatatable('allajaxlist', 'admin/mailsms/mailsmsreports/',data.param,[],100,[{"bSortable": false, "aTargets": [2, 3, 4, 5] }]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

</script>
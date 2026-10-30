<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title titlefix"> <?php echo $this->lang->line('send_patient_credential'); ?></h3>
          </div><!-- /.card-header -->
          <div class="card-body">
            <form action="<?php echo site_url('admin/bulkmessage/sendbulkmail') ?>" method="POST" id="bulkmail">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><?php echo $this->lang->line('credential_type'); ?> <small class="req">*</small></label>
                        <select id="notification_type" name="notification_type" class="form-control">
                            <?php foreach ($notificationtype as $key => $notificationtype_value) {?>
                            <option value="<?php echo $key; ?>"><?php echo $notificationtype_value; ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover align-middle ajaxlist mb-0" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('patient_login_credential'); ?>">
                        <thead>
                            <tr>
                                <th class="sh-th-40 text-center align-middle">
                                    <input type="checkbox" name="checkAll" id="checkAll" title="<?php echo $this->lang->line('select_all'); ?>" class="sh-scale-130 sh-cursor-pointer">
                                </th>
                                <th><?php echo $this->lang->line('patient_id'); ?></th>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <th><?php echo $this->lang->line('email'); ?></th>
                                <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                <th><?php echo $this->lang->line('username'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('password'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('please_wait') ?>"><i class="fa fa-paper-plane me-1"></i> <?php echo $this->lang->line('send') ?></button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

<!-- //========datatable start===== -->
<script type="text/javascript">
  ( function ( $ ) {      
      'use strict';
      $(document).ready(function () {
          initDatatable('ajaxlist','admin/bulkmessage/getcredentialdatatable',[],[],100,[
              { "bSortable": false, "aTargets": [0], 'sClass': 'text-center'},
              { "bSortable": false, "aTargets": [-1], 'sClass': 'dt-body-right'}
          ]);
      });
  } ( jQuery ) )  
    
</script>
<!-- //========datatable end===== -->

<script type="text/javascript">
    $("#bulkmail").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var checkCount = $("input[name='patient[]']:checked").length;

        if (checkCount == 0)
        {
            alert("<?php echo $this->lang->line('at_least_one_patient_should_be_selected');?>");
        } else {
                var form = $(this);
                var url = form.attr('action');
                var submit_button = form.find(':submit');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    dataType: "JSON", // serializes the form's elements.
                    beforeSend: function () {
                        submit_button.btnLoading();
                    },
                    success: function (data)
                    {
                        var message = "";
                        if (!data.status) {
                            $.each(data.error, function (index, value) {
                                message += value;
                            });
                            errorMsg(message);
                        } else {
                            successMsg(data.message);
							window.location.reload(true);
                        }
                    },
                    error: function (xhr) { // if error occured
                        submit_button.btnReset();
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    },
                    complete: function () {
                        submit_button.btnReset();
                    }
                });
        }
    });

    $("input[name='checkAll']").click(function () {
        $("input[name='patient[]']").not(this).prop('checked', this.checked);
    });
</script>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
    <?php $this->load->view('setting/sidebar'); ?>

    <div class="col-md-10">
        <div class="card">
            <div class="card-header ptbnull">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('biometric_attendance_setting'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end"></div>
            </div>

            <form role="form" id="attendancetype_form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="sch_id" value="<?php echo (int)$result->id; ?>">

                <div class="card-body">
                    <div class="row g-3 align-items-center mb-3">
                        <label class="col-md-3 col-form-label"><?php echo $this->lang->line('biometric_attendance'); ?></label>
                        <div class="col-md-9">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="biometric" id="biometric_disabled" value="0" <?php echo (!$result->biometric) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="biometric_disabled"><?php echo $this->lang->line('disabled'); ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="biometric" id="biometric_enabled" value="1" <?php echo ($result->biometric) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="biometric_enabled"><?php echo $this->lang->line('enabled'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 align-items-start">
                        <label for="biometric_device" class="col-md-3 col-form-label"><?php echo $this->lang->line('devices_separate_by_comma'); ?></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="biometric_device" name="biometric_device" value="<?php echo html_escape($result->biometric_device); ?>">
                            <span class="text-danger"><?php echo form_error('biometric_device'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if ($this->rbac->hasPrivilege('attendance_setting', 'can_edit')) { ?>
                        <button type="button" class="btn btn-primary btn-sm float-end edit_attendance" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"><?php echo $this->lang->line('save'); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('attendance_setting'); ?></h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($list_attendance)) { ?>
                    <?php foreach ($list_attendance as $list_key => $list_value) { ?>
                        <div class="att-role-wrap">
                            <form method="POST" action="<?php echo site_url('schsettings/savestaffsetting'); ?>" class="update">
                                <div class="att-role-header">
                                    <div>
                                        <span class="role-label"><?php echo $this->lang->line('role'); ?>:</span>
                                        <span class="role-name"><?php echo html_escape($list_value['role']); ?></span>
                                    </div>
                                    <?php if ($this->rbac->hasPrivilege('attendance_setting', 'can_edit')) { ?>
                                        <button type="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i><?php echo $this->lang->line('update'); ?>"><?php echo $this->lang->line('update'); ?></button>
                                    <?php } ?>
                                </div>

                                <?php if (!empty($list_value['schedule'])) { ?>
                                    <div class="table-responsive">
                                        <table class="table att-table">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line('attendance_type'); ?></th>
                                                    <th><?php echo $this->lang->line('entry_from'); ?> (hh:mm:ss)</th>
                                                    <th><?php echo $this->lang->line('entry_upto'); ?> (hh:mm:ss)</th>
                                                    <th><?php echo $this->lang->line('total_hour'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $row = 1;
                                                if (!empty($attendance_type)) {
                                                    foreach ($attendance_type as $att_type_key => $att_type_value) {
                                                        $return_value = get_input_value($list_value['schedule'], $att_type_value->id);
                                                ?>
                                                    <tr>
                                                        <td class="att-type-cell">
                                                            <input type="hidden" name="row[]" value="<?php echo $row; ?>">
                                                            <input type="hidden" name="attendance_type_id_<?php echo $row; ?>" value="<?php echo (int)$att_type_value->id; ?>">
                                                            <input type="hidden" name="role_id_<?php echo $row; ?>" value="<?php echo (int)$list_value['role_id']; ?>">
                                                            <?php echo $this->lang->line($att_type_value->long_lang_name); ?>
                                                            <span class="att-key">(<?php echo $att_type_value->key_value; ?>)</span>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" name="entry_time_from_<?php echo $row; ?>" class="form-control entry_time_from timepicker valid" autocomplete="off" value="<?php echo html_escape($return_value['entry_time_from']); ?>">
                                                                <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" name="entry_time_to_<?php echo $row; ?>" class="form-control entry_time_to timepicker valid" autocomplete="off" value="<?php echo html_escape($return_value['entry_time_to']); ?>">
                                                                <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" name="total_institute_hour_<?php echo $row; ?>" class="form-control total_institute_hour timepicker valid" autocomplete="off" value="<?php echo html_escape($return_value['total_institute_hour']); ?>">
                                                                <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                        $row++;
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </form>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<?php
function get_input_value($array, $find_time) {
    if (!empty($array)) {
        foreach ($array as $array_key => $array_value) {
            if ($array_value->staff_attendence_type_id == $find_time) {
                return [
                    'entry_time_from' => $array_value->entry_time_from,
                    'entry_time_to' => $array_value->entry_time_to,
                    'total_institute_hour' => $array_value->total_institute_hour,
                ];
            }
        }
        return [
            'entry_time_from' => '',
            'entry_time_to' => '',
            'total_institute_hour' => '',
        ];
    }
} ?>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';

    $(".edit_attendance").on('click', function (e) {
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: '<?php echo site_url("schsettings/saveattendance") ?>',
            type: 'POST',
            data: $('#attendancetype_form').serialize(),
            dataType: 'json',

            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                }

                $this.btnReset();
            }
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        /* class-based datetimepicker init removed - use class auto-init via event delegation */
    });
    $(function() {
        /* class-based datetimepicker init removed - use class auto-init via event delegation */
    });

    $(document).on('submit', '.update', function(e) {
        var submit_btn = $(this).find("button[type=submit]");
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            dataType: "json",
            beforeSend: function() {
                submit_btn.btnLoading();
            },
            success: function(data) {
                if (data.status == 1) {
                    successMsg(data.message);
                } else {
                    var message = "";
                    $.each(data.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                }
                submit_btn.btnReset();
            },
            error: function(xhr) {
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {
                submit_btn.btnReset();
            }
        });
    });
</script>

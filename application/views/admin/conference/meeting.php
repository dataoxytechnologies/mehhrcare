<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title"><?php echo $this->lang->line('live_meeting'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap addmeeting ms-auto">
                            <?php if ($this->rbac->hasPrivilege('live_meeting', 'can_add')) {
    ?>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-online-timetable"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?> </button>
                                <?php
}?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php }?>

                        <div class="table-responsive">
                        <div class="download_label"><?php echo $this->lang->line('live_meeting'); ?></div>
                            <table class="table table-hover table-striped table-bordered ajaxlistmeeting" data-export-title="<?php echo $this->lang->line('live_meeting') ;?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('meeting_title'); ?></th>
                                        <th width="25%"><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('meeting_duration_minutes'); ?></th>
                                        <th><?php echo $this->lang->line('api_used'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th class="noExport" width="15%"><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport" width="80"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="modal-online-timetable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="form-addconference" action="<?php echo site_url('admin/zoom_conference/addMeeting'); ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $this->lang->line('add_live_meeting'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <input type="hidden" id="password" name="password">
                        <div class="d-flex gap-2 flex-wrap">

                            <!-- Card 1: Meeting Details -->
                            <div class="sh-meeting-details-col">
                                <div class="sh-form-card mb-0">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-video"></i> <?php echo $this->lang->line('live_meeting'); ?></span>
                                    </div>
                                    <div class="px-2 py-3">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label" for="title"><?php echo $this->lang->line('meeting_title'); ?> <small class="req"> *</small></label>
                                                <input type="text" class="form-control form-control-sm" id="title" name="title">
                                                <span class="text-danger" id="title_error"></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="date"><?php echo $this->lang->line('meeting_date'); ?> <small class="req"> *</small></label>
                                                <div class="input-group input-group-sm" id="meeting_date">
                                                    <input type="text" class="form-control datetime" name="date" />
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="duration"><?php echo $this->lang->line('meeting_duration_minutes'); ?> <small class="req"> *</small></label>
                                                <input type="number" class="form-control form-control-sm" id="duration" name="duration">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('host_video'); ?> <small class="req"> *</small></label>
                                                <div class="sh-radio-group">
                                                    <label class="sh-radio-option">
                                                        <input type="radio" name="host_video" value="1" checked>
                                                        <span><?php echo $this->lang->line('enabled'); ?></span>
                                                    </label>
                                                    <label class="sh-radio-option">
                                                        <input type="radio" name="host_video" value="0">
                                                        <span><?php echo $this->lang->line('disabled'); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label"><?php echo $this->lang->line('client_video'); ?> <small class="req"> *</small></label>
                                                <div class="sh-radio-group">
                                                    <label class="sh-radio-option">
                                                        <input type="radio" name="client_video" value="1" checked>
                                                        <span><?php echo $this->lang->line('enabled'); ?></span>
                                                    </label>
                                                    <label class="sh-radio-option">
                                                        <input type="radio" name="client_video" value="0">
                                                        <span><?php echo $this->lang->line('disabled'); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="description"><?php echo $this->lang->line('description'); ?></label>
                                                <textarea class="form-control form-control-sm" name="description" id="description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Staff List -->
                            <div class="sh-meeting-staff-col">
                                <div class="sh-form-card mb-0 h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff_list'); ?> <small class="req ms-1"> *</small></span>
                                    </div>
                                    <div class="meeting-staff-list">
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($staffList as $staff_key => $staff_value) {
                                                if ($staff_value['id'] == $logged_staff_id) { continue; } ?>
                                            <li class="meeting-staff-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="staff_<?php echo $staff_value['id']; ?>" value="<?php echo $staff_value['id']; ?>" name="staff[]">
                                                    <label class="form-check-label" for="staff_<?php echo $staff_value['id']; ?>">
                                                        <?php
                                                        $name = ($staff_value["surname"] == "") ? $staff_value["name"] : $staff_value["name"] . " " . $staff_value["surname"];
                                                        echo $name . " (" . $staff_value['user_type'] . " : " . $staff_value['employee_id'] . ")";
                                                        ?>
                                                    </label>
                                                </div>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal-chkstatus" class="modal fade sh-modal sh-modal-accent">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="zoom_details">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlistmeeting','admin/zoom_conference/getmeetingdatatable',[],[],100);
      
    });
} ( jQuery ) )
</script>
<script type="text/javascript">
      // Fetch the status content FIRST, then open the modal once #zoom_details is populated.
      $(document).on('click', '.js-open-livestatus', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('disabled')) { return; }
            var id = $btn.data('id');
            $btn.addClass('disabled');
            $('#zoom_details').html("");
            $.ajax({
                type: "POST",
                url: base_url + 'admin/zoom_conference/getlivestatus',
                data: {'id': id},
                dataType: "JSON",
                success: function (data) {
                   $('#zoom_details').html(data.page);
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-chkstatus')).show();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                },
                complete: function () { $btn.removeClass('disabled'); }
            });
        })

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

    $('#modal-online-timetable').on('shown.bs.modal', function (e) {
        var password = makeid(5);
        $('#password').val("").val(password);

    })

    //===========================form submit==========
    $("form#form-addconference").submit(function (event) {
        event.preventDefault();
        var $form = $(this),
                url = $form.attr('action');

        var $button = $form.find("button[type=submit]:focus");
        $.ajax({
            type: "POST",
            url: url,
            data: $form.serialize(),
            dataType: "JSON",
            beforeSend: function () {
                $button.btnLoading();
            },
            success: function (data) {				
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    shModal('modal-online-timetable').hide();
                    successMsg(data.message);
                    window.location.reload(true);
                }
                $button.btnReset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $button.btnReset();
            },
            complete: function (data) {
                $button.btnReset();
            }
        });
    })
    //================================================
    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    $('#modal-online-timetable').on('hidden.bs.modal', function () {

        $(this).find("input,textarea,select").not("input[type=radio]")
                .val('')
                .end();
        $(this).find("input[type=checkbox], input[type=radio]")
                .prop('checked', false);
        $('input:radio[name="host_video"][value="1"]').prop('checked', true);
        $('input:radio[name="client_video"][value="1"]').prop('checked', true);
    });
  
    $(document).on('change', '#role_id', function (e) {
        $('#staff_id').html("");
        var role_id = $(this).val();
        getEmployeeName(role_id)
    });

    function getEmployeeName(role) {
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "JSON",
            beforeSend: function () {
                $('#staff_id').html("");
                $('#staff_id').addClass('dropdownloading');
            },
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.id + "'>" + obj.name + " " + obj.surname + "</option>";
                });
                $('#staff_id').append(div_data);
            },
            complete: function () {
                $('#staff_id').removeClass('dropdownloading');
            }
        });
    }
</script>
<script type="text/javascript">
    $(document).on('change', '.chgstatus_dropdown', function () {
        $(this).parent('form.chgstatus_form').submit()

    });

    $("form.chgstatus_form").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON",
            success: function (data)
            {
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            }
        });
    });
</script>
<script type="text/javascript">
     $(document).on('click', 'a.join-btn', function(e){
         e.preventDefault();
         var id=$(this).data('id');
         var url = $(this).attr('href');
        $.ajax({
            url: "<?php echo site_url("admin/zoom_conference/add_history") ?>",
            type: "POST",
            data: {"id":id},
            dataType: 'json',

            beforeSend: function () {

            },
            success: function (res)
            {
                if (res.status == 0) {

                } else if(res.status == 1) {
                window.open(url, '_blank');
                }
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
            },
            complete: function () {

            }
        });
});

$(".addmeeting").click(function(){
    $('#form-addconference').trigger("reset");
}); 

    $(document).ready(function (e) {
        modal_click_disabled('modal-online-timetable', 'modal-chkstatus');
    });
</script>
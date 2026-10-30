<div class="wrapper">
    <!-- Content Header (Page header) -->
        <!-- Main content -->
        <div class="row">
                <!-- /.col -->
                <div class="col-md-9 col-sm-9">
                    <div class="card">
                        <div class="card-body">
                            <!-- THE CALENDAR -->
                            <div id="calendar"></div> 
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /. box -->
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="sh-form-card">
                        <div class="sh-card-header d-flex align-items-center justify-content-between">
                            <span class="sh-card-header-title"><i class="fa fa-tasks me-1 opacity-75"></i><?php echo $this->lang->line('to_do_list'); ?></span>
                            <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                            <button class="btn btn-sm btn-light" onclick="add_task()" title="<?php echo $this->lang->line('add_task'); ?>" data-bs-toggle="tooltip"><i class="fa fa-plus"></i></button>
                            <?php } ?>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($tasklist as $taskkey => $taskvalue) {
                                $done = ($taskvalue["is_active"] == 'yes');
                                ?>
                                <div class="list-group-item p-0 border-0 border-bottom">
                                    <div class="todo-item <?php echo $done ? 'todo-done' : 'todo-pending'; ?>">
                                        <input type="checkbox" class="form-check-input" <?php if ($done) echo 'checked'; ?>
                                            id="check<?php echo (int)$taskvalue["id"] ?>"
                                            onclick="markcomplete('<?php echo (int)$taskvalue["id"] ?>')"
                                            name="eventcheck"
                                            value="<?php echo (int)$taskvalue["id"]; ?>">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="todo-title<?php if ($done) echo ' done'; ?>">
                                                <?php echo html_escape($taskvalue["event_title"]); ?>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-1">
                                                <div class="todo-meta">
                                                    <i class="fa fa-calendar-o me-1"></i><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($taskvalue["start_date"])); ?>
                                                </div>
                                                <span class="todo-actions d-flex gap-1">
                                                    <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) { ?>
                                                        <a class="btn btn-sm btn-light" title="<?php echo $this->lang->line('edit') ?>" data-bs-toggle="tooltip" href="#"
                                                           onclick="edit_todo_task('<?php echo (int)$taskvalue["id"]; ?>'); return false;"><i class="fa fa-pencil"></i></a>
                                                    <?php } ?>
                                                    <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) { ?>
                                                        <a class="btn btn-sm btn-light" title="<?php echo $this->lang->line('delete') ?>" data-bs-toggle="tooltip" href="#"
                                                           onclick="deleteevent('<?php echo (int)$taskvalue["id"]; ?>', '<?php echo $this->lang->line('task ') ?>'); return false;"><i class="fa fa-remove"></i></a>
                                                    <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="todopagination px-3 py-2 border-top"><?php echo $this->pagination->create_links(); ?></div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>

<div id="newTask" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="newTaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form role="form" id="addtodo_form" method="post" enctype="multipart/form-data" action="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-tasks me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                        <input class="form-control" name="task_title" id="task-title">
                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input class="form-control date" type="text" autocomplete="off" name="task_date" placeholder="" id="task-date">
                                        <input class="form-control" type="hidden" name="eventid" id="taskid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <div id="permission">
                        <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                            <button type="submit" id="addtodo_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info submit_addtask"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="newEventModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="newEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEventModalLabel"><?php echo $this->lang->line('add_new_event'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form role="form" id="addevent_form" method="post" enctype="multipart/form-data" action="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-calendar-plus-o me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                        <input class="form-control" name="title" id="input-field">
                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" class="form-control" id="desc-field"></textarea>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('event_from'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            <input type="text" autocomplete="off" name="event_from" class="form-control event_from datetime">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('event_to'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            <input type="text" autocomplete="off" name="event_to" class="form-control event_to datetime">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('event_color'); ?></label>
                                        <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                                        <?php
                                        $i = 0;
                                        $colors = '';
                                        foreach ($event_colors as $color) {
                                            $color_selected_class = ($i == 0) ? 'cpicker-big' : 'cpicker-small';
                                            $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . ";'></div>";
                                            $i++;
                                        }
                                        echo '<div class="cpicker-wrapper">' . $colors . '</div>';
                                        ?>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label d-block"><?php echo $this->lang->line('event_type'); ?></label>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="event_type" value="public" id="evtype_public">
                                                <label class="form-check-label" for="evtype_public"><?php echo $this->lang->line('public'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="event_type" value="private" checked id="evtype_private">
                                                <label class="form-check-label" for="evtype_private"><?php echo $this->lang->line('private'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="event_type" value="sameforall" id="evtype_sameforall">
                                                <label class="form-check-label" for="evtype_sameforall"><?php echo $this->lang->line('all'); ?> <?php echo html_escape($role); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="event_type" value="protected" id="evtype_protected">
                                                <label class="form-check-label" for="evtype_protected"><?php echo $this->lang->line('protected'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                        <button type="submit" id="addevent_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info submit_addevent"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
 
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>

<div id="viewEventModal" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="viewEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEventModalLabel"><?php echo $this->lang->line('view_event'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form role="form" method="post" id="updateevent_form" enctype="multipart/form-data" action="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-calendar me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                        <input class="form-control" name="title" placeholder="" id="event_title">
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('event_description'); ?></label>
                                        <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('event_from'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            <input type="text" autocomplete="off" id="eventfrom" name="eventfrom" class="form-control event_from datetime">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('event_to'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                            <input type="text" autocomplete="off" id="eventto" name="eventto" class="form-control event_to datetime">
                                        </div>
                                    </div>
                                    <input type="hidden" name="eventid" id="eventid">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('event_color'); ?></label>
                                        <input type="hidden" name="eventcolor" autocomplete="off" placeholder="" id="event_color" class="form-control">
                                        <?php
                                        $i = 0;
                                        $colors = '';
                                        foreach ($event_colors as $color) {
                                            $colorid = trim($color, "#");
                                            $color_selected_class = ($i == 0) ? 'cpicker-big' : 'cpicker-small';
                                            $colors .= "<div id='c-" . $colorid . "' class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "'></div>";
                                            $i++;
                                        }
                                        echo '<div class="cpicker-wrapper selectevent">' . $colors . '</div>';
                                        ?>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label d-block"><?php echo $this->lang->line('event_type'); ?></label>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="eventtype" value="public" id="evtypev_public">
                                                <label class="form-check-label" for="evtypev_public"><?php echo $this->lang->line('public'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="eventtype" value="private" id="evtypev_private">
                                                <label class="form-check-label" for="evtypev_private"><?php echo $this->lang->line('private'); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="eventtype" value="sameforall" id="evtypev_sameforall">
                                                <label class="form-check-label" for="evtypev_sameforall"><?php echo $this->lang->line('all'); ?> <?php echo html_escape($role); ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="eventtype" value="protected" id="evtypev_protected">
                                                <label class="form-check-label" for="evtypev_protected"><?php echo $this->lang->line('protected'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) { ?>
                        <button type="button" id="delete_event" class="btn btn-outline-danger submit_delete"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete'); ?></button>
                    <?php } ?>
                    <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) { ?>
                        <button type="submit" id="updateevent_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info submit_update"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div> 

<!-- Page specific script -->
<script>

    function add_task() {
        $("#modal-title").html("<?php echo $this->lang->line('add_task'); ?>");
        $("#task-title").val('');
        $("#taskid").val('');
        shModal('newTask').show();
	}

    function edit_todo_task(eventid) {
        $.ajax({
            url: "<?php echo site_url("admin/calendar/gettaskbyid/") ?>" + eventid,
            type: "POST",
            data: {eventid: eventid},
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res)
            {					
                $("#modal-title").html("<?php echo $this->lang->line('edit_task'); ?>");
                $("#task-title").val(res.event_title);
                $("#taskid").val(eventid);
                $("#task-date").val(res.edit_start_date);						
                shModal('newTask').show();
                $('#permission').html('<?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) { ?><input type="submit" id="addtodo_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>"  class="btn btn-primary submit_addtask float-end" value="<?php echo $this->lang->line('save'); ?>"><?php } ?>');

                            }
                        });
                    }

                    $(document).ready(function (e) {
                        $("#addtodo_form").on('submit', (function (e) {
                            $("#addtodo_formbtn").button("loading");
                            e.preventDefault();
                            $.ajax({
                                url: "<?php echo site_url("admin/calendar/addtodo") ?>",
                                type: "POST",
                                data: new FormData(this),
                                dataType: 'json',
                                contentType: false,
                                cache: false,
                                processData: false,
                                success: function (res)
                                {
                                    if (res.status == "fail") {
                                        var message = "";
                                        $.each(res.error, function (index, value) {
                                            message += value;
                                        });
                                        errorMsg(message);
                                    } else {
                                        successMsg(res.message);
                                        window.location.reload(true);
                                    }
                                    $("#addtodo_formbtn").button("reset");
                                }
                            });
                        }));
                    });

                    function complete_event(id, status) {
                        $.ajax({
                            url: "<?php echo site_url("admin/calendar/markcomplete/") ?>" + id,
                            type: "POST",
                            data: {id: id, active: status},
                            dataType: 'json',
                            success: function (res)
                            {
                                if (res.status == "fail") {
                                    var message = "";
                                    $.each(res.error, function (index, value) {
                                        message += value;
                                    });
                                    errorMsg(message);
                                } else {
                                    successMsg(res.message);
                                    window.location.reload(true);
                                }
                            }
                        });
                    }

                    function markcomplete(id) {
                        $('#check' + id).change(function () {
                            if (this.checked) {
                                complete_event(id, 'yes');
                            } else {
                                complete_event(id, 'no');
                            }
                        });
                    }
                            
    $(document).ready(function (e) {
        modal_click_disabled('newTask', 'newEventModal', 'viewEventModal');
    });
</script>

<script>
$(document).ready(function () {

    var $calendar = $('#calendar');
    var today     = new Date();
    <?php
        $lang = $this->customlib->getMyLanguage();
        $lang_code = !empty($lang['short_code']) ? $lang['short_code'] : 'en';
        // Build the moment.js format string to match the hospital datetimepicker format
        $php_fmt = $this->customlib->getHospitalDateFormat(true, true);
        $moment_fmt = strtr($php_fmt, ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY', 'H' => 'HH', 'h' => 'hh', 'i' => 'mm', 'A' => 'A']);
    ?>
    var lang_code       = '<?php echo html_escape($lang_code); ?>';
    var sh_datetime_fmt = '<?php echo $moment_fmt; ?>';

    $calendar.fullCalendar({
        header: {
            left:   'prev,next,today',
            center: 'title',
            right:  'month,agendaWeek,agendaDay'
        },
        defaultDate:  today,
        defaultView:  'month',
        locale:       lang_code,
        selectable:   true,
        selectHelper: true,
        editable:     false,
        eventLimit:   true,
        views: {
            month: { titleFormat: 'MMMM YYYY' },
            week:  { titleFormat: 'MMMM D YYYY' },
            day:   { titleFormat: 'D MMM, YYYY' }
        },
        events: {
            url: '<?php echo site_url("admin/calendar/getevents"); ?>'
        },
        eventRender: function (event, element) {
            if (event.event_type !== 'task') {
                element.attr('title', event.title + (event.description ? ' - ' + event.description : ''));
            } else {
                element.attr('title', event.title);
            }
            element.attr('data-bs-toggle', 'tooltip');
            new bootstrap.Tooltip(element[0]);
        },
        dayClick: function (date) {
            // Format the clicked date into the hospital display format for the datetimepicker
            var formatted = date.format(sh_datetime_fmt);
            $('#addevent_form .event_from').val(formatted);
            $('#addevent_form .event_to').val(formatted);
            shModal('newEventModal').show();
        },
        eventClick: function (event) {
            $.ajax({
                url: '<?php echo site_url("admin/calendar/view_event/"); ?>' + event.id,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    $('#event_title').val(res.event_title);
                    $('#event_desc').val(res.event_description);
                    $('#eventfrom').val(res.startdate);
                    $('#eventto').val(res.enddate);
                    $('#eventid').val(res.id);
                    $('#event_color').val(res.event_color);
                    $('input[name="eventtype"][value="' + res.event_type + '"]').prop('checked', true);
                    // highlight selected color swatch (IDs are prefixed c- to avoid digit-start CSS selector issue)
                    $('.selectevent .cpicker').removeClass('cpicker-big').addClass('cpicker-small');
                    $('#c-' + res.colorid).removeClass('cpicker-small').addClass('cpicker-big');
                    shModal('viewEventModal').show();
                }
            });
            return false;
        }
    });

    // Add new event form submit
    $('#addevent_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '<?php echo site_url("admin/calendar/saveevent"); ?>',
            type: 'POST',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                if (res.status === 'fail') {
                    var msg = '';
                    $.each(res.error, function (k, v) { msg += v; });
                    errorMsg(msg);
                } else {
                    successMsg(res.message);
                    shModal('newEventModal').hide();
                    $calendar.fullCalendar('refetchEvents');
                }
            }
        });
    });

    // Update/delete event form submit
    $('#updateevent_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '<?php echo site_url("admin/calendar/updateevent"); ?>',
            type: 'POST',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                if (res.status === 'fail') {
                    var msg = '';
                    $.each(res.error, function (k, v) { msg += v; });
                    errorMsg(msg);
                } else {
                    successMsg(res.message);
                    shModal('viewEventModal').hide();
                    $calendar.fullCalendar('refetchEvents');
                }
            }
        });
    });

    $('#delete_event').on('click', function () {
        var id = $('#eventid').val();
        deleteevent(id, '<?php echo $this->lang->line('event'); ?>');
    });

    // Color picker — Add event modal
    $(document).on('click', '.calendar-cpicker', function () {
        var color = $(this).data('color');
        $('#eventcolor, #event_color').val(color);
        $(this).closest('.cpicker-wrapper').find('.cpicker').removeClass('cpicker-big').addClass('cpicker-small');
        $(this).removeClass('cpicker-small').addClass('cpicker-big');
    });
});

function deleteevent(id, label) {
    if (!id) return;
    Swal.fire({
        title: '<?php echo $this->lang->line('delete_confirm'); ?>',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: '<?php echo $this->lang->line('delete'); ?>',
        heightAuto: false
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: '<?php echo site_url("admin/calendar/delete_event/"); ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'fail') {
                        errorMsg(res.message);
                    } else {
                        successMsg(label + ' <?php echo $this->lang->line('delete_message'); ?>');
                        shModal('viewEventModal').hide();
                        $('#calendar').fullCalendar('refetchEvents');
                        window.location.reload(true);
                    }
                }
            });
        }
    });
}
</script>
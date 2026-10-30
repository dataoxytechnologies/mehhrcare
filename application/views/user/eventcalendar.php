<div class="row">
    <div class="col-md-9 col-sm-9">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-3">
        <div class="sh-form-card">
            <div class="sh-card-header d-flex align-items-center justify-content-between">
                <span class="sh-card-header-title"><i class="fa fa-tasks me-1 opacity-75"></i><?php echo $this->lang->line('to_do_list'); ?></span>
                <button class="btn btn-sm btn-light" onclick="add_task()" title="<?php echo $this->lang->line('add_task'); ?>" data-bs-toggle="tooltip"><i class="fa fa-plus"></i></button>
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($tasklist)) { ?>
                    <div class="list-group-item text-muted small text-center py-3"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } else {
                    foreach ($tasklist as $taskkey => $taskvalue) {
                        $done = ($taskvalue["is_active"] == 'yes');
                ?>
                <div class="list-group-item p-0 border-0 border-bottom">
                    <div class="todo-item <?php echo $done ? 'todo-done' : 'todo-pending'; ?>">
                        <input type="checkbox" class="form-check-input" <?php if ($done) echo 'checked'; ?>
                               id="check<?php echo (int)$taskvalue["id"]; ?>"
                               onclick="markcomplete('<?php echo (int)$taskvalue["id"]; ?>')"
                               name="eventcheck"
                               value="<?php echo (int)$taskvalue["id"]; ?>">
                        <div class="flex-grow-1 min-w-0">
                            <div class="todo-title<?php if ($done) echo ' done'; ?>">
                                <?php echo html_escape($taskvalue["event_title"]); ?>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div class="todo-meta">
                                    <i class="fa fa-calendar-o me-1"></i><?php echo $this->customlib->YYYYMMDDTodateFormat($taskvalue["start_date"]); ?>
                                </div>
                                <span class="todo-actions d-flex gap-1">
                                    <a class="btn btn-sm btn-light" title="<?php echo $this->lang->line('edit'); ?>" data-bs-toggle="tooltip" href="#"
                                       onclick="edit_todo_task('<?php echo (int)$taskvalue["id"]; ?>'); return false;"><i class="fa fa-pencil"></i></a>
                                    <a class="btn btn-sm btn-light" title="<?php echo $this->lang->line('delete'); ?>" data-bs-toggle="tooltip" href="#"
                                       onclick="deleteevent('<?php echo (int)$taskvalue["id"]; ?>', '<?php echo $this->lang->line('task'); ?>'); return false;"><i class="fa fa-remove"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } } ?>
            </div>
            <?php if (!empty($tasklist)) { ?>
            <div class="todopagination px-3 py-2 border-top"><?php echo $this->pagination->create_links(); ?></div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Add/Edit Task Modal -->
<div id="newTask" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="newTaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"><?php echo $this->lang->line('add') . ' ' . $this->lang->line('task'); ?></h5>
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
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info submit_addtask"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.js"></script>
<?php
$language = $this->customlib->getMyLanguage();
$language_name = $language["short_code"];
if ($language_name != 'en') { ?>
    <script src="<?php echo base_url() ?>backend/fullcalendar/dist/locale/<?php echo $language_name ?>.js"></script>
<?php } ?>

<script>
    $calendar = $('#calendar');
    var base_url = '<?php echo base_url() ?>';
    today = new Date();
    y = today.getFullYear();
    m = today.getMonth();
    d = today.getDate();
    var viewtitle = 'month';
    var pagetitle = "<?php if (isset($title)) { echo $title; } ?>";

    if (pagetitle == "Dashboard") {
        viewtitle = 'agendaWeek';
    }

    $calendar.fullCalendar({
        viewRender: function (view, element) {},
        header: {
            center: 'title',
            right: 'month,agendaWeek,agendaDay',
            left: 'prev,next,today'
        },
        defaultDate: today,
        defaultView: viewtitle,
        selectable: true,
        selectHelper: true,
        views: {
            month: { titleFormat: 'MMMM YYYY' },
            week:  { titleFormat: ' MMMM D YYYY' },
            day:   { titleFormat: 'D MMM, YYYY' }
        },
        timezone: "Asia/Kolkata",
        draggable: false,
        lang: '<?php echo $language_name ?>',
        editable: false,
        eventLimit: false,
        events: { url: base_url + 'user/calendar/getevents' },
        eventRender: function (event, element) {
            element.attr('title', event.title);
            element.attr('onclick', event.onclick);
            element.attr('data-bs-toggle', 'tooltip');
            if ((!event.url) && (event.event_type != 'task')) {
                element.attr('title', event.title + '-' + event.description);
            }
        },
        dayClick: function (date, jsEvent, view) {
            var d = date.format();
            if (!$.fullCalendar.moment(d).hasTime()) {
                d += ' 05:30';
            }
            return false;
        }
    });

    function add_task() {
        $("#modal-title").html("<?php echo $this->lang->line('add') . ' ' . $this->lang->line('task'); ?>");
        $("#task-title").val('');
        $("#taskid").val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('newTask'), {backdrop: 'static'}).show();
        $("#task-date").val('<?php echo date($this->customlib->getHospitalDateFormat()); ?>');
    }

    function edit_todo_task(eventid) {
        $.ajax({
            url: "<?php echo site_url('user/calendar/gettaskbyid/'); ?>" + eventid,
            type: "POST",
            data: {eventid: eventid},
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                var taskdate_format = '<?php echo strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']); ?>';
                $("#modal-title").html("<?php echo $this->lang->line('edit_task'); ?>");
                $("#task-title").val(res.event_title);
                $("#taskid").val(eventid);
                $("#task-date").val($.fullCalendar.moment(res.start_date).format(taskdate_format));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('newTask'), {backdrop: 'static'}).show();
            }
        });
    }

    $(document).ready(function (e) {
        $("#addtodo_form").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url('user/calendar/addtodo'); ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function (index, value) { message += value; });
                        errorMsg(message);
                    } else {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                }
            });
        }));
    });

    function complete_event(id, status) {
        $.ajax({
            url: "<?php echo site_url('user/calendar/markcomplete/'); ?>" + id,
            type: "POST",
            data: {id: id, active: status},
            dataType: 'json',
            success: function (res) {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) { message += value; });
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

    function deleteevent(id, msg) {
        if (typeof (id) == 'undefined') { return; }
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: base_url + 'user/calendar/delete_event/' + id,
                type: 'POST',
                dataType: "json",
                success: function (res) {
                    if (res.status == "fail") {
                        errorMsg(res.message);
                    } else {
                        successMsg(msg + "<?php echo $this->lang->line('delete_message'); ?>");
                        window.location.reload(true);
                    }
                }
            });
        }
    }
</script>

<?php
$url           = "";
$extracls      = "";
$label_display = $this->lang->line('join');
$label_type    = 'label-success';

if ($live->purpose == "consult" || $live->purpose == "meeting") {
    $name = composeStaffNameByString($live->create_by_name, $live->create_by_surname, $live->create_by_employee_id);
}

// Status pill — reuse the app's status vocabulary (0 awaited, 1 cancelled, 2 finished)
if ($live->status == 1) {
    $pill_class = 'sh-status-pill-danger';
    $pill_text  = $this->lang->line('cancelled');
} elseif ($live->status == 2) {
    $pill_class = 'sh-status-pill-muted';
    $pill_text  = $this->lang->line('finished');
} else {
    $pill_class = 'sh-status-pill-ok';
    $pill_text  = $this->lang->line('awaited');
}

if ($live->status == 0) {
    if ($live->created_id == $logged_staff_id) {
        $label_display = $this->lang->line('start_now');
    } else {
        $label_display = $this->lang->line('join_now');
    }
    $label_type = 'label-success';
}

$target = "";
if ($conference_setting->use_zoom_app) {
    $target = "_blank";
    if ($live->purpose == "consult") {
        $extracls = "join-btn";
        $url      = $live_url->start_url;
    } elseif ($live->purpose == "meeting") {
        if ($live->created_id == $logged_staff_id) {
            $url = $live_url->start_url;
        } else {
            $extracls = "join-btn";
            $url      = $live_url->join_url;
        }
    }
} else {
    if ($live->purpose == "consult") {
        $url = site_url('admin/zoom_conference/join/consult' . '/' . $live->id);
    } elseif ($live->purpose == "meeting") {
        $url = site_url('admin/zoom_conference/join/meeting' . '/' . $live->id);
    }
}

// Decide whether the join/start action should be shown
$show_action = false;
$action_id   = '';
if (($live->created_id == $logged_staff_id) && $live->purpose == "meeting") {
    $show_action   = true;
    $label_display = $this->lang->line('join');
} elseif (($live->created_id != $logged_staff_id) && $live->purpose == "meeting") {
    $show_action = true;
    $action_id   = $live->id;
} elseif ($live->purpose == "consult") {
    $show_action = true;
}
?>

<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-video"></i> <?php echo html_escape($live->title); ?></span>
        <span class="sh-status-pill <?php echo $pill_class; ?> ms-auto"><?php echo $pill_text; ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('host'); ?></span>
                <span class="sh-info-value highlight"><?php echo html_escape($name); ?></span>
            </div>
            <div class="col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($live->date)); ?></span>
            </div>
            <div class="col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('duration'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($live->duration); ?> min</span>
            </div>
        </div>
    </div>
    <?php if ($show_action) { ?>
        <div class="sh-card-actions">
            <a href="<?php echo $url; ?>" class="btn btn-success btn-sm <?php echo $extracls; ?>" <?php echo $action_id !== '' ? 'data-id="' . (int) $action_id . '"' : ''; ?> target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
(function () {
    var m = document.getElementById('modal-chkstatus');
    if (m) {
        var t = m.querySelector('.modal-title');
        if (t) { t.textContent = <?php echo json_encode($this->lang->line('status')); ?>; }
    }
})();
</script>

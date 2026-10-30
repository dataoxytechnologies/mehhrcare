<?php
// Host = the doctor the consultation is created for
$name = ($live->create_for_surname == "") ? $live->create_for_name : $live->create_for_name . " " . $live->create_for_surname;

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

// Join action — zoom-app links open in a new tab and go through the add_history handler (join-btn)
if ($conference_setting->use_zoom_app) {
    $join_url = $live_url->join_url;
    $extracls = 'join-btn';
    $target   = '_blank';
} else {
    $join_url = site_url('patient/dashboard/join/' . $live->id);
    $extracls = '';
    $target   = '';
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
    <div class="sh-card-actions">
        <a href="<?php echo $join_url; ?>" class="btn btn-success btn-sm <?php echo $extracls; ?>" data-id="<?php echo (int) $live->id; ?>" target="<?php echo $target; ?>">
            <i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join') . ' ' . $this->lang->line('now'); ?>
        </a>
    </div>
</div>

<?php
if (!function_exists('_sc_file_icon')) {
    function _sc_file_icon($ft) {
        $img_types = ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp'];
        if (in_array($ft, $img_types)) return ['fi-img', 'fa-image'];
        $map = [
            'pdf'  => ['fi-pdf',  'fa-file-pdf-o'],
            'doc'  => ['fi-doc',  'fa-file-word-o'],
            'docx' => ['fi-doc',  'fa-file-word-o'],
            'xls'  => ['fi-xls',  'fa-file-excel-o'],
            'xlsx' => ['fi-xls',  'fa-file-excel-o'],
            'ppt'  => ['fi-doc',  'fa-file-powerpoint-o'],
            'pptx' => ['fi-doc',  'fa-file-powerpoint-o'],
            'video'=> ['fi-vid',  'fa-play-circle'],
            'mp4'  => ['fi-vid',  'fa-play-circle'],
            'webm' => ['fi-vid',  'fa-play-circle'],
            '3gp'  => ['fi-vid',  'fa-play-circle'],
            '3g2'  => ['fi-vid',  'fa-play-circle'],
            'zip'  => ['fi-zip',  'fa-file-archive-o'],
            'rar'  => ['fi-zip',  'fa-file-archive-o'],
        ];
        return isset($map[$ft]) ? $map[$ft] : ['fi-def', 'fa-file-o'];
    }
}

$is_valid = isset($content->share_date)
    && strtotime(date('Y-m-d')) >= strtotime($content->share_date)
    && (IsNullOrEmptyString($content->valid_upto) || strtotime(date('Y-m-d')) <= strtotime($content->valid_upto));
?>

<div class="container-fluid px-3 py-3">

    <?php if (!$is_valid) { ?>
    <div class="alert alert-danger mb-0"><?php echo $this->lang->line('sorry_this_link_is_invalid_or_expired_please_contact_to_system_admin'); ?></div>
    <?php } else { ?>

    <!-- Card 1: Content Details -->
    <div class="sh-form-card mb-3">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-file-text me-1 opacity-75"></i><?php echo html_escape($content->title); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('upload_date'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($content->share_date, $timeformat); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('share_date'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($content->share_date, $timeformat); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('valid_upto'); ?></span>
                    <span class="sh-info-value">
                        <?php echo !IsNullOrEmptyString($content->valid_upto)
                            ? $this->customlib->YYYYMMDDTodateFormat($content->valid_upto, $timeformat)
                            : '<span class="text-muted">—</span>'; ?>
                    </span>
                </div>
                <?php if ($superadmin_restriction == 'enabled') { ?>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('shared_by'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->getStaffFullName($content->name, $content->surname, $content->employee_id); ?></span>
                </div>
                <?php } ?>
            </div>
            <?php if (!empty($content->description)) { ?>
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('description'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($content->description); ?></span>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Card 2: Attachments -->
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-paperclip me-1 opacity-75"></i><?php echo $this->lang->line('attachments'); ?></span>
            <?php if (!empty($content->upload_contents)) { ?>
            <span class="badge bg-secondary ms-auto"><?php echo count($content->upload_contents); ?></span>
            <?php } ?>
        </div>
        <div class="p-2">
            <?php if (!empty($content->upload_contents)) { ?>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($content->upload_contents as $cv) {
                    $ft     = $cv->file_type;
                    $ic     = _sc_file_icon($ft);
                    $is_img = $ic[0] === 'fi-img';
                ?>
                <div class="border rounded p-2 d-flex align-items-center gap-3">
                    <?php if ($is_img) { ?>
                        <img src="<?php echo $this->media_storage->getImageURL($cv->thumb_path . $cv->thumb_name); ?>" class="sc-ft-thumb" alt="">
                    <?php } else { ?>
                        <div class="sc-ft-icon <?php echo $ic[0]; ?>">
                            <i class="fa <?php echo $ic[1]; ?>"></i>
                        </div>
                    <?php } ?>

                    <div class="flex-grow-1 min-w-0">
                        <?php if ($ft === 'video') { ?>
                            <a href="<?php echo html_escape($cv->vid_url); ?>" target="_blank"
                               class="d-block fw-semibold small text-truncate"><?php echo html_escape($cv->vid_title); ?></a>
                        <?php } else { ?>
                            <span class="d-block fw-semibold small text-truncate"><?php echo html_escape($cv->real_name); ?></span>
                        <?php } ?>
                        <span class="sc-ft-mono"><?php echo strtoupper($ft); ?></span>
                    </div>

                    <?php if ($ft !== 'video') { ?>
                    <a href="<?php echo site_url('site/download_content/' . $cv->upload_content_id . '/' . $this->enc_lib->encrypt($cv->share_content_id)); ?>"
                       class="sc-dl-btn" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                        <i class="fa fa-download"></i>
                    </a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <p class="text-muted small mb-0 py-2"><?php echo $this->lang->line('no_record_found'); ?></p>
            <?php } ?>
        </div>
    </div>

    <?php } ?>

</div>

<script>
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>

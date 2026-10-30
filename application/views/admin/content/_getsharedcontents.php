<?php
if (!function_exists('_sc_file_icon')) {
    function _sc_file_icon($ft) {
        $img_types = ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp'];
        if (in_array($ft, $img_types)) return ['fi-img', 'fa-image'];
        $map = [
            'pdf'  => ['fi-pdf',  'fa-file-pdf'],
            'doc'  => ['fi-doc',  'fa-file-word'],
            'docx' => ['fi-doc',  'fa-file-word'],
            'xls'  => ['fi-xls',  'fa-file-excel'],
            'xlsx' => ['fi-xls',  'fa-file-excel'],
            'ppt'  => ['fi-doc',  'fa-file-powerpoint'],
            'pptx' => ['fi-doc',  'fa-file-powerpoint'],
            'video'=> ['fi-vid',  'fa-play-circle'],
            'mp4'  => ['fi-vid',  'fa-play-circle'],
            'webm' => ['fi-vid',  'fa-play-circle'],
            '3gp'  => ['fi-vid',  'fa-play-circle'],
            '3g2'  => ['fi-vid',  'fa-play-circle'],
            'zip'  => ['fi-zip',  'fa-file-archive'],
            'rar'  => ['fi-zip',  'fa-file-archive'],
        ];
        return isset($map[$ft]) ? $map[$ft] : ['fi-def', 'fa-file'];
    }
}

$user_list = [];
if (!empty($result_array)) {
    foreach ($result_array as $rv) {
        if ($rv->group_id != '') {
            $user_list[] = intval($rv->group_id) ? $rv->role_name : $rv->group_id;
        } elseif ($rv->staff_id != '') {
            $emp = $rv->staff_employee_id != '' ? ' (' . $rv->staff_employee_id . ')' : '';
            $user_list[] = $rv->staff_first_name . ' ' . $rv->staff_surname . $emp . ' (' . $rv->staff_role_name . ')';
        }
    }
}
?>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-info-circle me-1"></i><?php echo $this->lang->line('details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('share_date'); ?></div>
                <div class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($shared_contents->share_date); ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('valid_upto'); ?></div>
                <div class="sh-info-value">
                    <?php echo !IsNullOrEmptyString($shared_contents->valid_upto)
                        ? $this->customlib->YYYYMMDDTodateFormat($shared_contents->valid_upto)
                        : '<span class="text-muted">—</span>'; ?>
                </div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('shared_by'); ?></div>
                <div class="sh-info-value"><?php echo $this->customlib->getStaffFullName($shared_contents->name, $shared_contents->surname, $shared_contents->employee_id); ?></div>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('send_to'); ?></div>
                <div class="sh-info-value"><?php echo $this->lang->line($shared_contents->send_to); ?></div>
            </div>
        </div>
        <?php if (!IsNullOrEmptyString($shared_contents->description)): ?>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <div class="sh-info-label"><?php echo $this->lang->line('description'); ?></div>
                <div class="sh-info-value"><?php echo html_escape($shared_contents->description); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">

    <div class="col-md-8 ps-0">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-paperclip me-1"></i><?php echo $this->lang->line('attachments'); ?></span>
                <?php if (!empty($shared_contents->upload_contents)): ?>
                <span class="badge bg-secondary ms-auto"><?php echo count($shared_contents->upload_contents); ?></span>
                <?php endif; ?>
            </div>
            <div class="p-2">
            <?php if (!empty($shared_contents->upload_contents)): ?>
                <div class="d-flex flex-column gap-2">
                <?php foreach ($shared_contents->upload_contents as $cv):
                    $ft  = $cv->file_type;
                    $ic  = _sc_file_icon($ft);
                    $is_img = $ic[0] === 'fi-img';
                ?>
                    <div class="border rounded p-2 d-flex align-items-center gap-3">
                        <?php if ($is_img): ?>
                            <img src="<?php echo $this->media_storage->getImageURL($cv->thumb_path . $cv->thumb_name); ?>"
                                 class="sh-shared-thumb">
                        <?php else: ?>
                            <div class="sc-ft-icon <?php echo $ic[0]; ?>">
                                <i class="fa <?php echo $ic[1]; ?>"></i>
                            </div>
                        <?php endif; ?>

                        <div class="flex-grow-1 min-w-0">
                            <?php if ($ft === 'video'): ?>
                                <a href="<?php echo html_escape($cv->vid_url); ?>" target="_blank"
                                   class="d-block fw-semibold small text-truncate"><?php echo html_escape($cv->vid_title); ?></a>
                            <?php else: ?>
                                <span class="d-block fw-semibold small text-truncate"><?php echo html_escape($cv->real_name); ?></span>
                            <?php endif; ?>
                            <span class="sc-ft-mono"><?php echo strtoupper($ft); ?></span>
                        </div>

                        <?php if ($ft !== 'video'): ?>
                        <a href="<?php echo site_url('site/download_content/' . $cv->upload_content_id . '/' . $this->enc_lib->encrypt($cv->share_content_id)); ?>"
                           class="sc-dl-btn" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                            <i class="fa fa-download"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2"><?php echo $this->lang->line('no_record_found'); ?></p>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fa fa-users me-1"></i><?php echo $this->lang->line('shared_groups_users'); ?></span>
                <?php if (!empty($user_list)): ?>
                <span class="badge bg-secondary ms-auto"><?php echo count($user_list); ?></span>
                <?php endif; ?>
            </div>
            <div class="p-2">
            <?php if (!empty($user_list)): ?>
                <ul class="list-unstyled mb-0">
                <?php foreach ($user_list as $ul_item): ?>
                    <li class="d-flex align-items-center gap-2 py-1 border-bottom border-light">
                        <i class="fa fa-user-circle text-muted sh-icon-user-circle"></i>
                        <span class="small"><?php echo html_escape(ucfirst($ul_item)); ?></span>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted small mb-0 py-2"><?php echo $this->lang->line('no_record_found'); ?></p>
            <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
(function(){
    $('#viewShareModal .modal-body-inner').find('[data-bs-toggle="tooltip"]').tooltip();
})();
</script>

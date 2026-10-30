<?php
function _content_thumb_src($cv, $ms) {
    $t = $cv->file_type;
    if (in_array($t, ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp'])) {
        return $ms->getImageURL($cv->thumb_path . $cv->thumb_name);
    }
    return '';
}

function _is_image_thumb($file_type) {
    return in_array($file_type, ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp']);
}

function _file_icon_class($file_type) {
    $img_types = ['jpg','jpeg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','gif','tiff','tif','webp'];
    if (in_array($file_type, $img_types)) return ['fi-img', 'fa-image'];
    $map = [
        'pdf'  => ['fi-pdf',  'fa-file-pdf'],
        'doc'  => ['fi-doc',  'fa-file-word'],
        'docx' => ['fi-doc',  'fa-file-word'],
        'xls'  => ['fi-xls',  'fa-file-excel'],
        'xlsx' => ['fi-xls',  'fa-file-excel'],
        'ppt'  => ['fi-doc',  'fa-file-powerpoint'],
        'pptx' => ['fi-doc',  'fa-file-powerpoint'],
        'csv'  => ['fi-xls',  'fa-file-csv'],
        'video'=> ['fi-vid',  'fa-play-circle'],
        'mp4'  => ['fi-vid',  'fa-play-circle'],
        'webm' => ['fi-vid',  'fa-play-circle'],
        '3gp'  => ['fi-vid',  'fa-play-circle'],
        '3g2'  => ['fi-vid',  'fa-play-circle'],
        'flv'  => ['fi-vid',  'fa-play-circle'],
        'm4a'  => ['fi-vid',  'fa-play-circle'],
        'f4v'  => ['fi-vid',  'fa-play-circle'],
        'zip'  => ['fi-zip',  'fa-file-archive'],
        'rar'  => ['fi-zip',  'fa-file-archive'],
    ];
    return isset($map[$file_type]) ? $map[$file_type] : ['fi-def', 'fa-file'];
}
?>

<!-- ===================== GRID VIEW ===================== -->
<div class="grid_div <?php echo ($grid_view) ? "displayblock" : "displaynone" ?>">
<?php if (!empty($all_contents)) { ?>
<div class="sh-cnt-grid">
<?php foreach ($all_contents as $content_key => $content_value) {
    $is_img = _is_image_thumb($content_value->file_type);
    $thumb  = _content_thumb_src($content_value, $this->media_storage);
    $ic     = _file_icon_class($content_value->file_type);
    $ext    = strtoupper(ltrim($content_value->file_type, '.'));
    $fname  = ($content_value->file_type == "video") ? $content_value->vid_title : $content_value->real_name;
?>
<div class="sh-cnt-card top_list_div"
     data-record-id="<?php echo (int)$content_value->id; ?>"
     data-real_name="<?php echo html_escape($content_value->real_name); ?>"
     data-short_name="<?php echo $this->media_storage->fileview($content_value->img_name); ?>"
     data-type-id="<?php echo (int)$content_value->content_type_id; ?>"
     data-file-type="<?php echo html_escape($content_value->file_type); ?>"
     data-name="<?php echo ($content_value->file_type == "video") ? html_escape($content_value->vid_url) : html_escape($content_value->img_name); ?>"
     data-path="<?php echo html_escape($content_value->dir_path); ?>">

    <div class="sh-cnt-thumb div_image">
        <a href="javascript:void(0);" class="image_content">
            <?php if ($is_img && $thumb) { ?>
            <img src="<?php echo $thumb; ?>" alt="<?php echo html_escape($fname); ?>" class="sh-cnt-img">
            <?php } else { ?>
            <div class="sh-cnt-icon-wrap <?php echo $ic[0]; ?>">
                <i class="fa <?php echo $ic[1]; ?>"></i>
            </div>
            <?php } ?>
        </a>
        <div class="sh-cnt-chk-wrap" onclick="event.stopPropagation();">
            <input type="checkbox"
                   name="share_checkbox[]"
                   class="share_checkbox"
                   value="<?php echo (int)$content_value->id; ?>"
                   data-real_name="<?php echo html_escape($content_value->real_name); ?>"
                   data-name="<?php echo html_escape($content_value->img_name); ?>"
                   <?php echo make_selected($content_value->id, $selected_content) ? "checked" : ""; ?>>
        </div>
        <span class="sh-cnt-ext"><?php echo html_escape($ext); ?></span>
        <div class="sh-cnt-overlay">
            <div class="sh-cnt-ov-name" title="<?php echo html_escape($fname); ?>"><?php echo html_escape($fname); ?></div>
            <div class="sh-cnt-ov-meta">
                <i class="fa fa-user-o"></i> <?php echo $this->customlib->getStaffFullName($content_value->staff_name, $content_value->surname, $content_value->employee_id); ?>
                &middot;
                <i class="fa fa-clock-o"></i> <?php echo $this->customlib->dateyyyymmddToDateTimeformat($content_value->created_at); ?>
            </div>
        </div>
    </div>

    <div class="sh-cnt-footer" onclick="event.stopPropagation();">
        <span class="sh-cnt-fname" title="<?php echo html_escape($fname); ?>"><?php echo html_escape($fname); ?></span>
        <a href="<?php echo site_url('admin/content/download_content/' . $content_value->id); ?>"
           class="sh-btn-icon"
           data-bs-toggle="tooltip"
           title="<?php echo $this->lang->line('download'); ?>">
            <i class="fa fa-download"></i>
        </a>
        <?php if ($this->rbac->hasPrivilege('upload_share_content', 'can_delete')) { ?>
        <a href="#"
           class="sh-btn-icon del delete_file"
           data-record-id="<?php echo (int)$content_value->id; ?>"
           data-name="<?php echo ($content_value->file_type == "video") ? html_escape($content_value->vid_title) : html_escape($content_value->real_name); ?>"
           data-bs-toggle="modal"
           data-bs-target="#single-delete"
           title="<?php echo $this->lang->line('delete'); ?>">
            <i class="fa fa-trash-o"></i>
        </a>
        <?php } ?>
    </div>

</div>
<?php } ?>
</div>
<?php } else { ?>
<div class="alert alert-info mb-0">
    <i class="fa fa-info-circle me-1"></i> <?php echo $this->lang->line('no_record_found'); ?>
</div>
<?php } ?>
</div>

<!-- ===================== LIST VIEW ===================== -->
<div class="list_div <?php echo (!$grid_view) ? "displayblock" : "displaynone" ?>">
<?php if (!empty($all_contents)) { ?>
<div class="table-responsive">
<table class="sh-cnt-table table_contents">
    <thead>
        <tr>
            <th class="sh-th-32">#</th>
            <th class="sh-th-36"></th>
            <th><?php echo $this->lang->line('document'); ?></th>
            <th><?php echo $this->lang->line('content_type'); ?></th>
            <th><?php echo $this->lang->line('size'); ?></th>
            <th><?php echo $this->lang->line('upload_by'); ?></th>
            <th><?php echo $this->lang->line('created_on'); ?></th>
            <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($all_contents as $content_key => $content_value) {
        $ic_list  = _file_icon_class($content_value->file_type);
        $fname_list = ($content_value->file_type == "video") ? $content_value->vid_title : $content_value->real_name;
    ?>
    <tr data-record-id="<?php echo (int)$content_value->id; ?>"
        data-real_name="<?php echo html_escape($content_value->real_name); ?>"
        data-short_name="<?php echo $this->media_storage->fileview($content_value->img_name); ?>"
        data-type-id="<?php echo (int)$content_value->content_type_id; ?>"
        data-file-type="<?php echo html_escape($content_value->file_type); ?>"
        data-name="<?php echo ($content_value->file_type == "video") ? html_escape($content_value->vid_url) : html_escape($content_value->img_name); ?>"
        data-path="<?php echo html_escape($content_value->dir_path); ?>"
        class="sh-cursor-pointer">
        <td onclick="event.stopPropagation();">
            <input type="checkbox"
                   name="share_checkbox[]"
                   class="share_checkbox_list sh-checkbox-teal"
                   value="<?php echo (int)$content_value->id; ?>"
                   data-real_name="<?php echo html_escape($content_value->real_name); ?>"
                   data-name="<?php echo html_escape($content_value->img_name); ?>"
                   <?php echo make_selected($content_value->id, $selected_content) ? "checked" : ""; ?>>
            <input type="hidden" name="image_display" value="">
        </td>
        <td>
            <div class="sh-cnt-lt-ico <?php echo $ic_list[0]; ?>">
                <i class="fa <?php echo $ic_list[1]; ?>"></i>
            </div>
        </td>
        <td class="sh-fw-500">
            <?php if ($content_value->file_type == "video") { ?>
            <a href="<?php echo html_escape($content_value->vid_url); ?>" target="_blank"><?php echo html_escape($content_value->vid_title); ?></a>
            <?php } else { ?>
            <a href="javascript:void(0);"><?php echo html_escape($content_value->real_name); ?></a>
            <?php } ?>
        </td>
        <td><span class="sh-cnt-lt-badge"><?php echo html_escape($content_value->content_type); ?></span></td>
        <td class="sh-cnt-lt-mono"><?php echo ($content_value->file_type == "video") ? $this->lang->line('n_a') : format_file_size($content_value->file_size); ?></td>
        <td><?php echo $this->customlib->getStaffFullName($content_value->staff_name, $content_value->surname, $content_value->employee_id); ?></td>
        <td class="sh-cnt-lt-mono"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($content_value->created_at); ?></td>
        <td class="text-end text-nowrap" onclick="event.stopPropagation();">
            <a href="<?php echo site_url('admin/content/download_content/' . $content_value->id); ?>"
               class="sh-btn-icon d-inline-flex"
               data-bs-toggle="tooltip"
               title="<?php echo $this->lang->line('download'); ?>">
                <i class="fa fa-download"></i>
            </a>
            <?php if ($this->rbac->hasPrivilege('upload_share_content', 'can_delete')) { ?>
            <a href="#"
               class="sh-btn-icon del delete_file d-inline-flex ms-1"
               data-record-id="<?php echo (int)$content_value->id; ?>"
               data-name="<?php echo ($content_value->file_type == "video") ? html_escape($content_value->vid_title) : html_escape($content_value->real_name); ?>"
               data-bs-toggle="modal"
               data-bs-target="#single-delete"
               title="<?php echo $this->lang->line('delete'); ?>">
                <i class="fa fa-trash-o"></i>
            </a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
</div>
<?php } else { ?>
<div class="alert alert-info mb-0">
    <i class="fa fa-info-circle me-1"></i> <?php echo $this->lang->line('no_record_found'); ?>
</div>
<?php } ?>
</div>

<?php
function make_selected($find, $selected_content) {
    if (!empty($selected_content)) {
        if (in_array($find, $selected_content)) {
            return true;
        }
    }
    return false;
}
?>

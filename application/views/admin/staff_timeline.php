<?php if (!empty($result)) { ?>
    <div class="sh-timeline">
        <?php foreach ($result as $key => $value) { ?>
        <div class="sh-tl-item">
            <span class="sh-tl-dot"></span>
            <div class="sh-tl-card">
                <div class="sh-tl-head">
                    <div class="sh-tl-meta">
                        <span class="sh-tl-date-badge"><i class="fa fa-calendar me-1"></i><?php echo $this->customlib->YYYYMMDDTodateFormat($value['timeline_date']); ?></span>
                        <span class="sh-tl-title"><?php echo html_escape($value['title']); ?></span>
                    </div>
                    <div class="sh-tl-actions">
                        <?php if ($this->rbac->hasPrivilege('edittimeline', 'can_delete')) { ?>
                            <a onclick="editstaffTimeline('<?php echo $value['id']; ?>')" class="btn btn-sm btn-light sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php } ?>
                        <?php if ($this->rbac->hasPrivilege('staff_timeline', 'can_delete')) { ?>
                            <a onclick="delete_timeline('<?php echo $value['id']; ?>')" class="btn btn-sm btn-light text-danger sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                        <?php if (!empty($value["document"])) { ?>
                            <a href="<?php echo base_url() . "admin/timeline/download_staff_timeline/" . $value["id"] . "/" . $value["document"]; ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <?php if (!empty(trim(strip_tags($value['description'])))) { ?>
                <div class="sh-tl-body">
                    <?php echo $value['description']; ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="alert alert-info text-center"><?php echo $this->lang->line('no_record_found'); ?></div>
<?php } ?>

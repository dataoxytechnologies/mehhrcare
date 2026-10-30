<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title titlefix"><?php echo $this->lang->line('notice_board'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                    <?php if ($this->rbac->hasPrivilege('notice_board', 'can_add')) { ?>
                        <a href="<?php echo base_url() ?>admin/notification/add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> <?php echo $this->lang->line('post_new_message'); ?></a>
                    <?php } ?>
                    <?php if ($this->rbac->hasPrivilege('email_sms', 'can_view')) { ?>
                        <a href="<?php echo base_url(); ?>admin/mailsms/compose" class="btn btn-sm btn-primary"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('send_sms'); ?></a>
                        <a href="<?php echo base_url(); ?>admin/mailsms/composemail" class="btn btn-sm btn-primary"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('send_email'); ?></a>
                    <?php } ?>
                    <?php if ($this->rbac->hasPrivilege('send_credential', 'can_view')) { ?>
                        <a href="<?php echo base_url(); ?>admin/bulkmessage" class="btn btn-sm btn-primary"><i class="fa fa-key"></i> <?php echo $this->lang->line('send_credential'); ?></a>
                    <?php } ?>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($notificationlist)): ?>
                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php else: ?>
                    <div class="notice-list" id="accordion">
                        <?php foreach ($notificationlist as $key => $notification):
                            $role_name = $notification['role_name']; ?>

                        <div class="notice-card mb-2">
                            <!-- Notice header row -->
                            <div class="notice-header d-flex align-items-center gap-2">
                                <a class="notice-title flex-grow-1 collapsed"
                                   data-bs-toggle="collapse"
                                   data-bs-target="#collapse<?php echo (int)$notification['id']; ?>"
                                   href="#collapse<?php echo (int)$notification['id']; ?>"
                                   aria-expanded="false"
                                   data-msgid="<?php echo (int)$notification['id']; ?>">
                                    <i class="fa fa-chevron-right notice-arrow me-2"></i>
                                    <?php echo html_escape($notification['title']); ?>
                                </a>
                                <div class="notice-actions d-flex gap-1 flex-shrink-0">
                                    <?php if ($this->rbac->hasPrivilege('notice_board', 'can_edit') || $notification['created_id'] == $user_id): ?>
                                        <a href="<?php echo base_url() ?>admin/notification/edit/<?php echo (int)$notification['id'] ?>"
                                           class="btn btn-sm btn-secondary"
                                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($this->rbac->hasPrivilege('notice_board', 'can_delete') || $notification['created_id'] == $user_id): ?>
                                        <a onclick="delete_record(<?php echo (int)$notification['id'] ?>)"
                                           class="btn btn-sm btn-secondary"
                                           data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Collapsible body -->
                            <div id="collapse<?php echo (int)$notification['id']; ?>" class="collapse notice-body">
                                <!-- Message content -->
                                <div class="notice-content">
                                    <?php echo $notification['message']; ?>
                                </div>
                                <!-- Meta info bar -->
                                <div class="notice-meta-bar">
                                    <div class="notice-meta-item">
                                        <span class="meta-label"><i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('publish_date'); ?></span>
                                        <span class="meta-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($notification['publish_date']); ?></span>
                                    </div>
                                    <div class="notice-meta-item">
                                        <span class="meta-label"><i class="fa fa-calendar"></i> <?php echo $this->lang->line('notice_date'); ?></span>
                                        <span class="meta-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($notification['date']); ?></span>
                                    </div>
                                    <?php if (!empty($role_name)): ?>
                                    <div class="notice-meta-item">
                                        <span class="meta-label"><i class="fa fa-users"></i> <?php echo $this->lang->line('message_to'); ?></span>
                                        <ul class="notice-roles">
                                            <?php foreach ($role_name as $role_value): ?>
                                            <li><i class="fa fa-user"></i><?php echo html_escape($role_value['name']); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- .notice-card -->

                        <?php endforeach; ?>
                    </div><!-- .notice-list -->
                <?php endif; ?>
            </div><!-- .card-body -->
        </div><!-- .card -->
    </div>
</div>

<script>
    $(document).on('click', '.collapsed[data-msgid]', function () {
        var base_url = '<?php echo base_url() ?>';
        var notice = $(this).data('msgid');
        $.ajax({ type: "POST", url: base_url + "admin/notification/read", data: {'notice': notice}, dataType: "json" });
    });

    // Rotate chevron + highlight header when panel opens/closes
    $(document).on('show.bs.collapse', '.notice-body', function () {
        var $card = $(this).closest('.notice-card');
        $card.find('.notice-arrow').addClass('rotated');
        $card.addClass('open');
    });
    $(document).on('hide.bs.collapse', '.notice-body', function () {
        var $card = $(this).closest('.notice-card');
        $card.find('.notice-arrow').removeClass('rotated');
        $card.removeClass('open');
    });

    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/notification/delete/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () { alert("Fail"); }
            });
        }
    }
</script>

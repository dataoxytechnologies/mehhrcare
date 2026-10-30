<?php
$CI =& get_instance();
$CI->load->library('customlib');
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- Header -->
            <div class="sh-notif-head">
                <h3 class="ttl">
                    <?php echo $this->lang->line('notifications'); ?>
                    <span class="count"><?php echo (int)$unread_count; ?></span>
                </h3>
                <div class="tools">
                    <button type="button" class="btn btn-sm btn-light mark_all_read">
                        <i class="fas fa-check-double"></i> <?php echo $this->lang->line('mark_all_read'); ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary delete_all">
                        <i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_all'); ?>
                    </button>
                </div>
            </div>

            <?php if (!empty($notifications)) { ?>
            <!-- Toolbar -->
            <div class="sh-notif-toolbar">
                <div class="sh-notif-search">
                    <i class="fa fa-search"></i>
                    <input type="text" class="sh-notif-search-input" placeholder="<?php echo $this->lang->line('search_notifications'); ?>">
                </div>
                <div class="sh-notif-chips">
                    <button type="button" class="sh-notif-chip is-active" data-filter="all">
                        <?php echo $this->lang->line('all'); ?> <span class="n"><?php echo sizeof($notifications); ?></span>
                    </button>
                    <button type="button" class="sh-notif-chip" data-filter="unread">
                        <?php echo $this->lang->line('unread'); ?> <span class="n"><?php echo (int)$unread_count; ?></span>
                    </button>
                    <button type="button" class="sh-notif-chip" data-filter="read">
                        <?php echo $this->lang->line('read'); ?> <span class="n"><?php echo (int)$read_count; ?></span>
                    </button>
                </div>
            </div>
            <?php } ?>

            <?php if (empty($notifications)) { ?>
                <div class="sh-notif-empty">
                    <i class="fa fa-bell-slash-o"></i>
                    <div class="ttl"><?php echo $this->lang->line('no_record_found'); ?></div>
                </div>
            <?php } else { ?>

                <?php foreach ($notifications_by_day as $bucket_key => $rows) {
                    if (empty($rows)) { continue; }
                ?>
                <div class="sh-notif-group" data-bucket="<?php echo $bucket_key; ?>">
                    <div class="sh-notif-group-label">
                        <span class="lbl"><?php echo $this->lang->line($bucket_key); ?></span>
                        <span class="cnt"><?php echo sizeof($rows); ?></span>
                    </div>
                    <div class="sh-notif-tl">
                        <?php foreach ($rows as $result) {
                            $type      = $result['notification_type'];
                            $color_key = isset($type_color_map[$type]) ? $type_color_map[$type] : 'info';
                            $icon      = $CI->customlib->notification_icon($type);
                            if (empty($icon)) { $icon = 'fa fa-bell'; }
                            $is_unread = empty($result['readdone']) || $result['readdone'] !== 'no';

                            $title_key     = $this->lang->line($result['notification_title']);
                            $title_display = empty($title_key) ? $result['notification_title'] : $title_key;

                            $type_label_key = $this->lang->line($type);
                            $type_label     = empty($type_label_key) ? str_replace('_', ' ', $type) : $type_label_key;
                        ?>
                        <?php
                            $time_display = $CI->customlib->YYYYMMDDHisTodateFormat($result['date'], $CI->customlib->getHospitalTimeFormat());
                        ?>
                        <div class="sh-notif-item sh-notif-c-<?php echo $color_key; ?><?php echo $is_unread ? ' is-unread' : ''; ?>" data-noticeid="<?php echo (int)$result['id']; ?>" data-type="<?php echo html_escape($type); ?>">
                            <span class="dot"><i class="<?php echo $icon; ?>"></i></span>
                            <div class="sh-notif-card">
                                <div class="sh-notif-card-head">
                                    <p class="ttl">
                                        <?php if ($is_unread) { ?><span class="unread-dot"></span><?php } ?>
                                        <?php echo html_escape($title_display); ?>
                                        <span class="time-inline" title="<?php echo html_escape($result['date']); ?>"><?php echo $time_display; ?></span>
                                    </p>
                                    <div class="actions">
                                        <button type="button" class="iconbtn delete_one" title="<?php echo $this->lang->line('delete'); ?>" data-noticeid="<?php echo (int)$result['id']; ?>">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="sh-notif-expand">
                                    <?php if (!empty($result['notification_desc'])) { ?>
                                    <p class="desc"><?php echo html_escape($result['notification_desc']); ?></p>
                                    <?php } ?>
                                    <div class="meta">
                                        <span class="sh-notif-type-tag"><?php echo html_escape($type_label); ?></span>
                                        <?php if (!empty($result['notification_for'])) { ?>
                                            <span class="recipient"><?php echo html_escape($result['notification_for']); ?></span>
                                        <?php } ?>
                                        <span class="sep">·</span>
                                        <span class="read-receipt"><i class="fa fa-check"></i> <?php echo $this->lang->line('read'); ?></span>
                                        <span class="sep">·</span>
                                        <span class="time" title="<?php echo html_escape($result['date']); ?>"><?php echo $time_display; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

            <?php } ?>

            <div class="sh-notif-pager">
                <ul class="pagination">
                    <?php echo $this->pagination->create_links(); ?>
                </ul>
            </div>

        </div>
    </div>
</div>

<script>
    // ─── Preserved AJAX: mark notification as read ───
    function updateStatus(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/systemnotification/updateStatus/',
            type: 'POST',
            data: {id: id},
            dataType: 'json'
        });
    }

    function recountUnreadChips() {
        var unread = $('.sh-notif-item.is-unread').length;
        var read   = $('.sh-notif-item').length - unread;
        $('.sh-notif-chip[data-filter="all"] .n').text(unread + read);
        $('.sh-notif-chip[data-filter="unread"] .n').text(unread);
        $('.sh-notif-chip[data-filter="read"] .n').text(read);
        $('.sh-notif-head .count').text(unread);
    }

    function reapplyGroupVisibility() {
        $('.sh-notif-group').each(function () {
            var hasVisible = $(this).find('.sh-notif-item:visible').length > 0;
            $(this).toggle(hasVisible);
        });
    }

    $(function () {
        // ─── Auto-mark-read on item click (replaces old .accordianheader handler) ───
        // CSS handles expand animation: removing .is-unread reveals .sh-notif-expand smoothly.
        $(document).on('click', '.sh-notif-item', function (e) {
            if ($(e.target).closest('.iconbtn').length) { return; }
            var $item = $(this);
            if (!$item.hasClass('is-unread')) { return; }
            var id = $item.data('noticeid');
            updateStatus(id);
            $item.addClass('is-clearing');
            setTimeout(function () {
                $item.removeClass('is-unread is-clearing');
                $item.find('.unread-dot').remove();
                recountUnreadChips();
            }, 250);
        });

        // ─── Mark all read ───
        $(document).on('click', '.mark_all_read', function () {
            $('.sh-notif-item.is-unread').each(function () {
                var $item = $(this);
                updateStatus($item.data('noticeid'));
                $item.removeClass('is-unread');
                $item.find('.unread-dot').remove();
            });
            recountUnreadChips();
        });

        // ─── Delete all (preserved) ───
        $(document).on('click', '.delete_all', function () {
            delete_recordByIdReload('admin/systemnotification/deleteall');
        });

        // ─── Delete single item ───
        $(document).on('click', '.delete_one', function (e) {
            e.stopPropagation();
            var $btn  = $(this);
            var $item = $btn.closest('.sh-notif-item');
            var id    = $btn.data('noticeid');
            if (!confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete'); ?>')) { return; }
            $.ajax({
                url: '<?php echo base_url(); ?>admin/systemnotification/deleteone',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function (res) {
                    if (res && res.status == 1) {
                        $item.fadeOut(200, function () {
                            $item.remove();
                            recountUnreadChips();
                            reapplyGroupVisibility();
                        });
                    }
                }
            });
        });

        // ─── Filter chips (All / Unread / Read) ───
        $(document).on('click', '.sh-notif-chip', function () {
            $('.sh-notif-chip').removeClass('is-active');
            $(this).addClass('is-active');
            var filter = $(this).data('filter');
            $('.sh-notif-item').show();
            if (filter === 'unread') {
                $('.sh-notif-item').not('.is-unread').hide();
            } else if (filter === 'read') {
                $('.sh-notif-item.is-unread').hide();
            }
            reapplyGroupVisibility();
        });

        // ─── Live search ───
        $(document).on('input', '.sh-notif-search-input', function () {
            var q = $(this).val().toLowerCase().trim();
            if (!q) {
                $('.sh-notif-item').show();
            } else {
                $('.sh-notif-item').each(function () {
                    var t = ($(this).find('.ttl').text() + ' ' + $(this).find('.desc').text() + ' ' + $(this).find('.sh-notif-type-tag').text()).toLowerCase();
                    $(this).toggle(t.indexOf(q) !== -1);
                });
            }
            reapplyGroupVisibility();
        });
    });
</script>

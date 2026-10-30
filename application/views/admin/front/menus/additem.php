
<script src="https://ilikenwf.github.io/jquery.mjs.nestedSortable.js"></script>
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-8">
                <!-- Horizontal Form -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('add_menu_item'); ?></h3>
                    </div><!-- /.card-header -->
                    <!-- form start -->
                    <form id="form1" action="<?php echo site_url('admin/front/menus/additem/' . $result['slug']) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <input type="hidden" name="menu_id" value="<?php echo $result['id'] ?>">
                            <?php if ($this->session->flashdata('msg')) {?>
                                <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                                 ?>
                            <?php }?>
                            <?php
if (isset($error_message)) {
    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
}
?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('menu_item'); ?></label><small class="req"> *</small>
                                <input autofocus="" id="menu" name="menu" placeholder="" type="text" class="form-control"  value="<?php echo set_value('menu'); ?>" />
                                <span class="text-danger"><?php echo form_error('menu'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block"><?php echo $this->lang->line('external_url'); ?></label>
                                <div class="form-check form-switch d-inline-flex m-0">
                                    <input id="ext_url" name="ext_url" type="checkbox" role="switch" class="form-check-input ext_url_chk" value="1" <?php echo set_checkbox('ext_url', '1', (set_value('ext_url')) ? true : false); ?> />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block"><?php echo $this->lang->line('open_in_new_tab'); ?></label>
                                <div class="form-check form-switch d-inline-flex m-0">
                                    <input id="open_new_tab" name="open_new_tab" type="checkbox" role="switch" class="form-check-input chk" value="1" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('external_url_address'); ?></label>
                                <input id="ext_url_link" name="ext_url_link"  type="text" class="form-control"  value="<?php echo set_value('ext_url_link'); ?>" <?php echo (!set_value('ext_url')) ? 'disabled' : ''; ?>/>
                                <span class="text-danger"><?php echo form_error('ext_url_link'); ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('pages'); ?></label>
                                <select  id="page_id" name="page_id" class="form-control"  >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
foreach ($listPages as $page) {
    ?>
                                        <option value="<?php echo $page['id'] ?>"<?php if (set_value('page_id') == $page['id']) {
        echo "selected=selected";
    }
    ?>><?php echo $page['title'] ?></option>
                                        <?php
}
?>
                                </select>
                                <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                            </div>

                        </div><!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div><!--/.col (right) -->
            <!-- left column -->
            <div class="col-md-4">
                <!-- general form elements -->
                <div class="card" id="holist">
                    <div class="card-header ptbnull d-flex align-items-center">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('menu_item_list'); ?></h3>
                        <div class="ms-auto d-flex gap-1">
                            <?php foreach ($allMenus as $m): ?>
                            <a href="<?php echo site_url('admin/front/menus/additem/' . $m['slug']); ?>"
                               class="btn btn-sm <?php echo ($m['slug'] === $result['slug']) ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                <?php echo html_escape($m['menu']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div><!-- /.card-header -->
                    <form id="form1" action="<?php echo site_url('admin/front/menus/update') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <div class="mailbox-controls">
                                <div class="float-end">
                                </div><!-- /.float-end -->
                            </div>
                            <div class="menu-tree-wrap">
                                <ol class="sortable">
                                    <?php if (empty($listdropdown_Menus)): ?>
                                        <div class="menu-empty-state">
                                            <i class="fa fa-bars"></i>
                                            <?php echo $this->lang->line('no_record_found'); ?>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($listdropdown_Menus as $menu): ?>
                                            <li id="list_<?php echo $menu['id']; ?>">
                                                <div class="menu-item-row">
                                                    <button type="button"
                                                        class="chevron-btn <?php echo empty($menu['submenus']) ? 'no-children' : 'open'; ?>"
                                                        data-bs-target="sub_<?php echo $menu['id']; ?>"
                                                        title="<?php echo empty($menu['submenus']) ? 'No sub-items' : 'Toggle sub-items'; ?>">
                                                        <i class="fa fa-chevron-right"></i>
                                                    </button>
                                                    <span class="menu-item-label"><?php echo html_escape($menu['menu']); ?></span>
                                                    <?php if (!empty($menu['submenus'])): ?>
                                                        <span class="sub-count-badge"><?php echo count($menu['submenus']); ?></span>
                                                    <?php endif; ?>
                                                    <span class="menu-item-actions">
                                                        <a href="<?php echo site_url('admin/front/menus/edititem/' . $menu['slug'] . '/' . $top_menu); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                                        <?php if ($this->rbac->hasPrivilege('menus', 'can_delete')): ?>
                                                        <a href="#" class="btn btn-sm btn-light text-danger" data-bs-toggle="modal" data-bs-target="#confirm-delete" data-id="<?php echo $menu['id']; ?>" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($menu['submenus'])): ?>
                                                    <ol class="submenu-list" id="sub_<?php echo $menu['id']; ?>">
                                                        <?php foreach ($menu['submenus'] as $submenu_value): ?>
                                                            <li id="list_<?php echo $submenu_value['id']; ?>" class="no-nest">
                                                                <div class="sub-item-row">
                                                                    <span class="sub-item-label"><?php echo html_escape($submenu_value['menu']); ?></span>
                                                                    <span class="menu-item-actions">
                                                                        <a href="<?php echo site_url('admin/front/menus/edititem/' . $submenu_value['slug'] . '/' . $top_menu); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                                                        <?php if ($this->rbac->hasPrivilege('menus', 'can_delete')): ?>
                                                                        <a href="#" class="btn btn-sm btn-light text-danger" data-bs-toggle="modal" data-bs-target="#confirm-delete" data-id="<?php echo $submenu_value['id']; ?>" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ol>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ol>
                            </div>
                        </div><!-- /.card-body -->
                    </form>
                </div>
            </div><!--/.col (left) -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    

<script type="text/javascript">
    $(document).ready(function () {
        document.querySelectorAll('.delmodal').forEach(function(el){ bootstrap.Modal.getOrCreateInstance(el, {backdrop:'static', keyboard:false}); })

        $(document).on('click', '.chevron-btn:not(.no-children)', function (e) {
            e.stopPropagation();
            var $submenu = $('#' + $(this).data('bs-target'));
            $submenu.slideToggle(200);
            $(this).toggleClass('open');
        });

        $('#confirm-delete').on('show.bs.modal', function (e) {
            var data = $(e.relatedTarget).data();
            $('.del_menuid', this).val("");
            $('.del_menuid', this).val(data.id);
        });

        $('#confirm-delete').on('click', '.btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var id = $('.del_menuid').val();
            $.ajax({
                type: "post",
                url: '<?php echo site_url("admin/front/menus/deleteMenuItem") ?>',
                dataType: 'JSON',
                data: {'id': id},
                beforeSend: function () {
                    $modalDiv.addClass('modalloading');
                },
                success: function (data) {
                    if (data.status == 1) {
                        successMsg(data.message);
                        location.reload(true);

                    } else {
                        errorMsg(data.message);
                    }
                },
                complete: function () {

                    $modalDiv.removeClass('modalloading');

                }
            });
        });
    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        /* #postdate init removed - auto-init via class + event delegation */

        $('.ext_url_chk').change(function () {
            var c = this.checked ? 1 : 0;
            if (c) {
                $('#ext_url_link').prop("disabled", false);
            } else {
                $('#ext_url_link').prop("disabled", true);
            }
        });

        function normalizeMenuTree($root){
            $root.find('li').each(function(){
                var $li    = $(this);
                var liId   = ($li.attr('id') || '').replace('list_','');
                var $sub   = $li.children('ol').first();
                var $row   = $li.children('div').first();
                var $chev  = $row.find('.chevron-btn').first();
                var $badge = $row.find('.sub-count-badge').first();

                // Mark/unmark this li as a leaf based on its current depth.
                // A leaf (item inside a submenu-list) cannot accept further nesting
                // — without this flag, dragging a sibling onto the leaf makes the
                // plugin try to nest inside it, hit maxLevels, and revert.
                if ($li.parent().hasClass('submenu-list')) {
                    $li.addClass('no-nest');
                } else {
                    $li.removeClass('no-nest');
                }

                if ($sub.length && $sub.children('li').length){
                    $sub.addClass('submenu-list');
                    if (!$sub.attr('id') && liId) $sub.attr('id', 'sub_' + liId);
                    $sub.show();
                    var count = $sub.children('li').length;
                    $chev.removeClass('no-children').addClass('open')
                         .attr('title', 'Toggle sub-items');
                    if ($sub.attr('id')) $chev.attr('data-bs-target', $sub.attr('id'));
                    if ($badge.length){
                        $badge.text(count);
                    } else {
                        var $actions = $row.find('.menu-item-actions').first();
                        var $newBadge = $('<span class="sub-count-badge">'+count+'</span>');
                        if ($actions.length) $newBadge.insertBefore($actions);
                        else $row.append($newBadge);
                    }
                } else {
                    if ($sub.length && !$sub.children('li').length) $sub.remove();
                    $chev.addClass('no-children').removeClass('open')
                         .attr('title','No sub-items');
                    if ($badge.length) $badge.remove();
                }
            });
        }

        $('ol.sortable').nestedSortable({
            disableNestingClass: 'no-nest',
            forcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: 'li',
            maxLevels: 2,
            opacity: .6,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            placeholder: 'menu-placeholder',
            start: function (e, ui) {
                if (ui.placeholder) ui.placeholder.height(ui.helper.outerHeight() - 4);
            },
            stop: function (e, ui) {
                normalizeMenuTree($(this));
            },
            update: function () {
                var list = $(this).nestedSortable('toHierarchy');
                var urls = baseurl + "admin/front/menus/updateMenu";
                $.ajax({
                    url: urls,
                    type: 'post',
                    data: {order: list},

                    dataType: "html",
                    success: function (response) {

                    },
                    beforeSend: function () {

                    },
                    complete: function () {

                    }
                });
            }
        });
    });

</script>
<div class="delmodal modal fade sh-modal sh-modal-accent" id="confirm-delete" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <p><?php echo $this->lang->line('are_you_sure_want_to_delete_item_this_action_is_irreversible'); ?></p>
                    <p><?php echo $this->lang->line('do_you_want_to_proceed'); ?></p>
                    <p class="debug-url"></p>
                    <input type="hidden" name="del_menuid" class="del_menuid" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-danger btn-ok"><?php echo $this->lang->line('delete'); ?></a>
            </div>
        </div>
    </div>
</div>
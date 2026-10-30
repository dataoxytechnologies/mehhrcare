<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card" id="holist">
                    <div class="card-header ptbnull d-flex align-items-center justify-content-between">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('menu_list'); ?></h3>
                        <div class="ms-auto d-flex gap-1">
                            <?php if ($this->rbac->hasPrivilege('menus', 'can_add')) { ?>
                            <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm add_menu"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_menu'); ?></a>
                        <?php } ?>
                            </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('menu_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('title'); ?></th>

                                        <th class="text-end no-print">
                                            <?php echo $this->lang->line('action'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php if (empty($listMenus)) { ?>
                            <?php } else {
                                $count = 1;
                                foreach ($listMenus as $menu) {  ?>

                                    <tr id="<?php echo $menu["id"]; ?>">
                                        <td class="mailbox-name">
                                            <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $menu['menu'] ?></a>
                                            <div class="fee_detail_popover d-none" >
                                            <?php
                                            if ($menu['description'] == "") { ?>
                                                <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                            <?php } else {  ?>
                                                <p class="text text-info"><?php echo $menu['description']; ?></p>
                                            <?php }    ?>
                                            </div>
                                        </td>
                                        
                                        <td class="text-end no-print noExport">
                                            <div class="d-inline-flex gap-1">
                                                <?php if ($this->rbac->hasPrivilege('menus', 'can_add')) { ?>
                                                    <a href="<?php echo site_url('admin/front/menus/additem/' . $menu['slug']); ?>" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('add_menu_item'); ?>">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('menus', 'can_delete')) {
                                                    if ($menu['content_type'] != "default") { ?>
                                                        <a class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('<?php echo 'admin/front/menus/delete/' . $menu['id']; ?>', '<?php echo $this->lang->line('delete_message') ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                <?php } } ?>
                                            </div>
                                        </td>
                                            </tr>
                                        <?php 
                                    }
                                    $count++;
                                }
                                ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-mid modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_menu'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url(); ?>admin/front/menus/add" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label"><?php echo $this->lang->line('menu'); ?> <small class="req">*</small></label>
                                    <input autofocus="" id="menu" name="menu" type="text" class="form-control" value="<?php echo set_value('menu'); ?>" />
                                    <span class="text-danger"><?php echo form_error('menu'); ?></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div><!--./pup-scroll-area-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                }
            });
        }));
    });

    $(document).ready(function () {
        /* #postdate init removed - auto-init via class + event delegation */
    });

    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

$(".add_menu").click(function(){
    $('#formadd').trigger("reset");
});
</script>
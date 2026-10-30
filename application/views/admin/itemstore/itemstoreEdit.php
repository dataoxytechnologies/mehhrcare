<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="row">
            <div class="col-md-2">
                <div class="card border0">
                    <ul class="tablists">
                        <li><a href="<?php echo base_url(); ?>admin/itemcategory" class="active"><?php echo $this->lang->line('item_category'); ?></a></li>
                        <li><a href="<?php echo base_url(); ?>admin/itemstore"><?php echo $this->lang->line('item_store'); ?></a></li>
                        <li><a href="<?php echo base_url(); ?>admin/itemsupplier"><?php echo $this->lang->line('item_supplier'); ?></a></li>
                    </ul>
                </div>
            </div>
            <?php if ($this->rbac->hasPrivilege('store', 'can_add') || $this->rbac->hasPrivilege('store', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit_item_store'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/itemstore/edit/" . $id) ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="card-body">
                                <?php echo validation_errors(); ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('item_store_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="name" name="name" placeholder="<?= $this->lang->line('name') ?>" type="text" class="form-control" value="<?php echo set_value('itemstore', $itemstore['item_store']); ?>" />
                                    <span class="text-danger"><?php echo form_error('itemstore'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('item_store_code'); ?></label>
                                    <input id="code" name="code" placeholder="<?= $this->lang->line('code') ?>" type="text" class="form-control" value="<?php echo set_value('itemstore', $itemstore['code']); ?>" />
                                    <span class="text-danger"><?php echo form_error('itemstore'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="<?= $this->lang->line('enter') ?>"><?php echo set_value('description', $itemstore['description']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                            </div><!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('store', 'can_add') || $this->rbac->hasPrivilege('store', 'can_edit')) {
                echo "6";
            } else {
                echo "10";
            }
            ?>">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('item_store_list'); ?></h3>
                    </div><!-- /.card-header -->
                    <div class="card-body  ">
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('item_store_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item_store_name'); ?></th>
                                        <th><?php echo $this->lang->line('item_store_code'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($itemstorelist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($itemstorelist as $store) {
                                            ?>
                                            <tr>   
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover">
                                                        <?php echo $store['item_store'] ?>
                                                    </a>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
                                                        if ($store['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $store['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo $store['code'] ?>
                                                </td>
                                                <td class="mailbox-date float-end noExport">
                                                    <?php if ($this->rbac->hasPrivilege('store', 'can_edit')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemstore/edit/<?php echo $store['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('store', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemstore/delete/<?php echo $store['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
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
            </div>
        </div>   <!-- /.row -->
    


<script>
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
</script>

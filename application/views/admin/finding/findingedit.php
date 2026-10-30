<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    <!-- Main content -->
    <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('income_head', 'can_add') || $this->rbac->hasPrivilege('income_head', 'can_edit')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit_income_head'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/incomehead/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="card-body">
                                <?php echo validation_errors(); ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('income_head'); ?><small class="req"> *</small></label>
                                    <input autofocus="" id="incomehead" name="incomehead" placeholder="<?= $this->lang->line('income_head') ?>" type="text" class="form-control"  value="<?php echo set_value('incomehead', $incomehead['income_category']); ?>" />
                                    <span class="text-danger"><?php echo form_error('incomehead'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="<?= $this->lang->line('enter') ?>"><?php echo set_value('description', $incomehead['description']); ?></textarea>
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
            if ($this->rbac->hasPrivilege('income_head', 'can_add') || $this->rbac->hasPrivilege('income_head', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="card" id="exphead">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('income_head_list'); ?></h3>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('income_head_list'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('income_head'); ?></th>
                                        <th class="text-end no-print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categorylist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($categorylist as $category) {
                                            ?>
                                            <tr>                                               
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover">
                                                        <?php echo $category['income_category'] ?>
                                                    </a>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
                                                        if ($category['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $category['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-date float-end no-print">
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('income_head', 'can_edit')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/incomehead/edit/<?php echo $category['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('income_head', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/incomehead/delete/<?php echo $category['id'] ?>"class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
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
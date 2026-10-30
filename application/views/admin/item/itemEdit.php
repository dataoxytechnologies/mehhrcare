<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat();?>
<!-- Content Wrapper. Contains page content -->
<div class="row">
            <?php if ($this->rbac->hasPrivilege('item', 'can_add') || $this->rbac->hasPrivilege('item', 'can_edit')) {
    ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit_item'); ?></h3>
                        </div><!-- /.card-header -->
                        <form id="form1" action="<?php echo site_url('admin/item/edit/' . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" >
                            <div class="card-body">
                                <?php if ($this->session->flashdata('msg')) {?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php }?>
                                <?php
if (isset($error_message)) {
        echo "<div class='alert alert-danger'>" . $error_message . "</div>";
    }
    ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name', $item['name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('item_category'); ?></label><small class="req"> *</small>
                                    <select id="item_category_id" name="item_category_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
foreach ($itemcatlist as $item_category) {
        ?>
                                            <option value="<?php echo $item_category['id'] ?>"<?php
if (set_value('item_category_id', $item['item_category_id']) == $item_category['id']) {
            echo "selected = selected";
        }
        ?>><?php echo $item_category['item_category'] ?></option>

                                            <?php
$count++;
    }
    ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="<?= $this->lang->line('enter') ?>"><?php echo set_value('description', $item['description']); ?></textarea>
                                    <span class="text-danger"></span>
                                </div>
                            </div><!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php }?>
            <div class="col-md-<?php
if ($this->rbac->hasPrivilege('item', 'can_add') || $this->rbac->hasPrivilege('item', 'can_edit')) {
    echo "8";
} else {
    echo "12";
}
?> ">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('item_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('item_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('item'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('available_quantity'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($itemlist)) {
    ?>
                                        <?php
} else {
    foreach ($itemlist as $items) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $items['name'] ?></a>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
if ($items['description'] == "") {
            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
            ?>
                                                            <p class="text text-info"><?php echo $items['description']; ?></p>
                                                            <?php
}
        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name"><?php echo $items['item_category']; ?></td>
                                                <td class="mailbox-name"><?php echo $items['added_stock'] - $items['issued']; ?></td>
                                                <td class="mailbox-date float-end">
                                                    <?php if ($this->rbac->hasPrivilege('item', 'can_edit')) {?>
                                                        <a href="<?php echo base_url(); ?>admin/item/edit/<?php echo $items['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }if ($this->rbac->hasPrivilege('item', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/item/delete/<?php echo $items['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                    
                                                </td>
                                            </tr>
        <?php
    }
}
?>

                                            </tbody>
                                            </table><!-- /.table -->
                                            </div><!-- /.mail-box-messages -->
                                            </div><!-- /.card-body -->
                                            </div>
                                            </div><!--/.col (left) -->
                                            <!-- right column -->
                                            </div>
                                            <div class="row">
                                            <!-- left column -->
                                            <!-- right column -->
                                            <div class="col-md-12">
                                            </div><!--/.col (right) -->
                                            </div>   <!-- /.row -->
                                            
                                            

                                            <script type="text/javascript">
                                            $(document).ready(function () {
                                            var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

                                            /* #date init removed - auto-init via class + event delegation */

                                            $("#btnreset").click(function () {
                                            $("#form1")[0].reset();
                                            });

                                            });
                                            </script>
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
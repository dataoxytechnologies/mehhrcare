<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <?php
if ($this->rbac->hasPrivilege('income', 'can_add') || $this->rbac->hasPrivilege('income', 'can_edit')) {
    ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $this->lang->line('edit_income'); ?></h3>
                        </div><!-- /.card-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/income/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
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
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('income_head'); ?></label>
                                    <select autofocus="" id="inc_head_id" name="inc_head_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($incheadlist as $inchead) { ?>
                                        <option value="<?php echo $inchead['id'] ?>"<?php
                                        if ($income['inc_head_id'] == $inchead['id']) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $inchead['income_category'] ?></option>
                                        <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('inc_head_id'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                    <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo html_escape(set_value('name', $income['name'])); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('invoice_no'); ?></label>
                                    <input id="invoice_no" name="invoice_no" placeholder="" type="text" class="form-control"  value="<?php echo html_escape(set_value('invoice_no', $income['invoice_number'])); ?>" />
                                    <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date" value="<?php echo html_escape(set_value('date', $this->customlib->YYYYMMDDTodateFormat($income['date']))); ?>" readonly="readonly" autocomplete="off" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('amount'); ?><small class="req"> *</small></label>
                                    <input id="amount" name="amount" placeholder="" type="text" class="form-control"  value="<?php echo html_escape(set_value('amount', $income['amount'])); ?>" />
                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="documents" name="documents" placeholder="" type="file" class="filestyle form-control"  value="<?php echo set_value('documents'); ?>" />
                                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="<?= $this->lang->line('enter') ?>"><?php echo html_escape(set_value('description', $income['note'])); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
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
if ($this->rbac->hasPrivilege('income', 'can_add') || $this->rbac->hasPrivilege('income', 'can_edit')) {
    echo "8";
} else {
    echo "12";
}
?>">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('income_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('income_list'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('invoice_number'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('income_head'); ?></th>
                                        <th><?php echo $this->lang->line('amount'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($incomelist)) {
    ?>

                                        <?php
} else {
    foreach ($incomelist as $income) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo html_escape($income['name']) ?></a>
                                                    <div class="fee_detail_popover d-none">
                                                        <?php
if ($income['note'] == "") {
            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
            ?>
                                                            <p class="text text-info"><?php echo html_escape($income['note']); ?></p>
                                                            <?php
}
        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo html_escape($income["invoice_no"]); ?>
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo $this->customlib->YYYYMMDDTodateFormat($income['date']); ?></td>
                                                <td class="mailbox-name">
                                                    <?php
$income_head = $income['income_category'];
        echo html_escape($income_head);
        ?>
                                                </td>
                                                <?php
$inc_head_id = $income['inc_head_id'];
        $arr1        = str_split($inc_head_id);
        ?>

                                                <td class="mailbox-name"><?php echo ($currency_symbol . $income['amount']); ?></td>
                                                <td class="mailbox-date float-end">
                                                    <?php if ($income['documents']) {
            ?>
                                                        <a href="<?php echo base_url(); ?>admin/income/download/<?php echo html_escape($income['documents']) ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php }
        ?>

                                                    <?php
if ($this->rbac->hasPrivilege('income', 'can_edit')) {
            ?>
                                                        <a href="<?php echo base_url(); ?>admin/income/edit/<?php echo $income['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php
if ($this->rbac->hasPrivilege('income', 'can_delete')) {
            ?>
                                                        <a href="<?php echo base_url(); ?>admin/income/delete/<?php echo $income['id'] ?>" class="btn btn-secondary btn-sm"  data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php }?>
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
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    


<script type="text/javascript">
    // #date auto-initialized as Tempus Dominus 6 picker via .date class + event delegation in footer.php
    // Apply max date = today restriction (preserves old endDate:'+0d' behavior)
    $(document).ready(function () {
        var dateEl = document.getElementById('date');
        if (dateEl) {
            dateEl.addEventListener('focus', function() {
                if (dateEl._pickerInit) {
                    dateEl._pickerInit.updateOptions({
                        restrictions: { maxDate: new tempusDominus.DateTime() }
                    });
                }
            }, { once: true });
        }
    });
</script>
<script>
    $(document).ready(function () {
        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var td = el.closest('td');
                    var inner = td ? td.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
        });
    });
</script>
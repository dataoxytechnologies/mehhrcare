<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat();?>
<!-- Content Wrapper. Contains page content -->
<div class="row">
            <?php if ($this->rbac->hasPrivilege('item', 'can_view')) {  ?>
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card">
                        <div class="card-header ptbnull">
                            <h3 class="card-title titlefix"> <?php echo $this->lang->line('item_list'); ?></h3>
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                                <?php if ($this->rbac->hasPrivilege('item', 'can_add')) {?>
                                <a href="" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm additem"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item'); ?></a>
                                <?php }?>
                            </div><!-- /.ms-auto d-flex gap-1 -->
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive mailbox-messages">
                                <div class="download_label"><?php echo $this->lang->line('item_list'); ?></div>
                                <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('item_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('item'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <th><?php echo $this->lang->line('unit'); ?></th>
                                            <th><?php echo $this->lang->line('available_quantity'); ?></th>
                                            <th class="noExport"><?php echo $this->lang->line('description'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table><!-- /.table -->
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.card-body -->
                    </div>
                </div><!--/.col (left) -->
                <!-- right column -->
                <?php
}
?>
        </div>       
    

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemLabel"><?php echo $this->lang->line('add_item') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form1" action="<?php echo base_url() ?>admin/item/add" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item'); ?> <small class="req">*</small></label>
                                        <input autofocus id="name" name="name" type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <select id="item_category_id" name="item_category_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemcatlist as $item_category) { ?>
                                                <option value="<?php echo $item_category['id'] ?>"<?php if (set_value('item_category_id') == $item_category['id']) echo ' selected'; ?>><?php echo $item_category['item_category'] ?></option>
                                            <?php $count++; } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('unit'); ?> <small class="req">*</small></label>
                                        <input id="unit" name="unit" type="text" class="form-control form-control-sm" value="<?php echo set_value('unit'); ?>">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemLabel"><?php echo $this->lang->line('edit_item'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eform1" action="<?php echo base_url() ?>admin/item/edit" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item'); ?> <small class="req">*</small></label>
                                        <input autofocus id="ename" name="name" type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <select id="eitem_category_id" name="item_category_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemcatlist as $item_category) { ?>
                                                <option value="<?php echo $item_category['id'] ?>"<?php if (set_value('item_category_id') == $item_category['id']) echo ' selected'; ?>><?php echo $item_category['item_category'] ?></option>
                                            <?php $count++; } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('unit'); ?> <small class="req">*</small></label>
                                        <input id="eunit" name="unit" type="text" class="form-control form-control-sm" value="<?php echo set_value('unit'); ?>">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="edescription" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                        <input type="hidden" name="id" id="e_id">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    $(document).ready(function (e) {
        $('#form1').on('submit', (function (e) {
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

    function get_data(id) {       
        $.ajax({
            url: "<?php echo base_url() ?>admin/item/get_data/" + id,
            type: "POST",
            dataType: 'json',
            success: function (res) {
                $('#ename').val(res.name);
                $('#eunit').val(res.unit);
                $('#epurchase_price').val(res.purchase_price);
                $('#e_id').val(res.id);
                $('#eitem_category_id').val(res.item_category_id);
                $('#edescription').val(res.description);
                shModal('editmyModal').show();
            }
        });
    }

    $(document).ready(function (e) {
        $('#eform1').on('submit', (function (e) {
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

		function delete_record(id) {
            if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/item/delete/' + id,
                    success: function (res) {
                        successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                        window.location.reload(true);
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
            }
        }   

	$(".additem").click(function(){
		$('#form1').trigger("reset");
	});

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'editmyModal');
    });
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/item/getitemdatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('item_stock_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('item_stock', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm additemstock"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item_stock'); ?></a>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('issue_item', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/issueitem" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('issue_item'); ?> </a>  
                                <?php
                            }
                            if ($this->rbac->hasPrivilege('item', 'can_view')) {
                                ?>
                                <a href="<?php echo base_url(); ?>admin/item" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('item'); ?> </a> 
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->					
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible">
						 <?php if ($this->rbac->hasPrivilege('item_stock', 'can_view')) { ?>
                            <div class="download_label"><?php echo $this->lang->line('item_stock_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('item_stock_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('supplier'); ?></th>
                                        <th><?php echo $this->lang->line('store'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('total_quantity'); ?></th>
                                        <th><?php echo $this->lang->line('generated_by'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('purchase_price') ." (".$currency_symbol.")"; ?></th>
                                        <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
							<?php } ?>
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->					
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>       
    


<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_item_stock') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form1" action="<?php echo base_url() ?>admin/itemstock/add" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <select autofocus id="item_category_id" name="item_category_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemcatlist as $item_category) { ?>
                                            <option value="<?php echo $item_category['id'] ?>"<?php if (set_value('item_category_id') == $item_category['id']) echo ' selected'; ?>><?php echo $item_category['item_category'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item'); ?> <small class="req">*</small></label>
                                        <select id="item_id" name="item_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('supplier'); ?> <small class="req">*</small></label>
                                        <select id="supplier_id" name="supplier_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemsupplier as $itemsup) { ?>
                                            <option value="<?php echo $itemsup['id'] ?>"<?php if (set_value('supplier_id') == $itemsup['id']) echo ' selected'; ?>><?php echo $itemsup['item_supplier'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('supplier_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('store'); ?></label>
                                        <select id="store_id" name="store_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemstore as $itemstorel) { ?>
                                            <option value="<?php echo $itemstorel['id'] ?>"<?php if (set_value('store_id') == $itemstorel['id']) echo ' selected'; ?>><?php echo $itemstorel['item_store'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('store_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('quantity'); ?> <small class="req">*</small> <span id="item_unit" class="text-muted"></span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle sh-qty-toggle" type="button" data-bs-toggle="dropdown" id="symbol_btn">+</button>
                                                <ul class="dropdown-menu sh-qty-dropdown">
                                                    <li><a class="dropdown-item" href="#" data-value="+">+</a></li>
                                                    <li><a class="dropdown-item" href="#" data-value="-">-</a></li>
                                                </ul>
                                            </div>
                                            <input type="hidden" name="symbol" id="symbol" value="+">
                                            <input id="quantity" name="quantity" type="text" class="form-control form-control-sm" value="<?php echo set_value('quantity'); ?>">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('purchase_price'); ?> <small class="req">*</small></label>
                                        <input id="purchase_price" name="purchase_price" type="text" class="form-control form-control-sm" value="<?php echo set_value('purchase_price'); ?>">
                                        <span class="text-danger"><?php echo form_error('purchase_price'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="date" name="date" type="text" class="form-control form-control-sm date" value="<?php echo date($this->customlib->getHospitalDateFormat()); ?>" readonly>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input id="item_photo" name="item_photo" type="file" class="filestyle form-control form-control-sm" data-height="40" value="<?php echo set_value('item_photo'); ?>">
                                        <span class="text-danger"><?php echo form_error('item_photo'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="form1btn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_item_stock'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editform" action="<?php echo base_url() ?>admin/itemstock/update" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('item'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item_category'); ?> <small class="req">*</small></label>
                                        <select autofocus id="edititem_category_id" name="item_category_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemcatlist as $item_category) { ?>
                                            <option value="<?php echo $item_category['id'] ?>"><?php echo $item_category['item_category'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('item'); ?> <small class="req">*</small></label>
                                        <select id="edititem_id" name="item_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('item_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('supplier'); ?> <small class="req">*</small></label>
                                        <select id="editsupplier_id" name="supplier_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemsupplier as $itemsup) { ?>
                                            <option value="<?php echo $itemsup['id'] ?>"><?php echo $itemsup['item_supplier'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('supplier_id'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('store'); ?></label>
                                        <select id="editstore_id" name="store_id" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($itemstore as $itemstorel) { ?>
                                            <option value="<?php echo $itemstorel['id'] ?>"><?php echo $itemstorel['item_store'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('store_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('quantity'); ?> <small class="req">*</small> <span id="edit_item_unit" class="text-muted"></span></label>
                                        <div class="input-group input-group-sm">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle sh-qty-toggle" type="button" data-bs-toggle="dropdown" id="edit_symbol_btn">+</button>
                                                <ul class="dropdown-menu sh-qty-dropdown">
                                                    <li><a class="dropdown-item" href="#" data-value="+">+</a></li>
                                                    <li><a class="dropdown-item" href="#" data-value="-">-</a></li>
                                                </ul>
                                            </div>
                                            <input type="hidden" name="symbol" id="edit_symbol" value="+">
                                            <input id="editquantity" name="quantity" type="text" class="form-control form-control-sm" value="<?php echo set_value('quantity'); ?>">
                                            <input type="hidden" name="itemstockid" id="itemstockid">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('purchase_price'); ?> <small class="req">*</small></label>
                                        <input id="epurchase_price" name="purchase_price" type="text" class="form-control form-control-sm purchase_price" value="<?php echo set_value('purchase_price'); ?>">
                                        <span class="text-danger"><?php echo form_error('purchase_price'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="editdate" name="date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date'); ?>" readonly>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="editdescription" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input id="edititem_photo" name="item_photo" type="file" class="filestyle form-control form-control-sm" data-height="40" value="<?php echo set_value('item_photo'); ?>">
                                        <span class="text-danger"><?php echo form_error('item_photo'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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
<script type="text/javascript">
    $(document).ready(function () {
        var item_id_post = '<?php echo set_value('item_id') ?>';
        item_id_post = (item_id_post != "") ? item_id_post : 0;
        var item_category_id_post = '<?php echo set_value('item_category_id'); ?>';
        item_category_id_post = (item_category_id_post != "") ? item_category_id_post : 0;
        populateItem(item_id_post, item_category_id_post);
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
		
        /* #date init removed - auto-init via class + event delegation */

        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $(document).on('change', '#item_category_id', function (e) {
            $('#item_id').html("");
            var item_category_id = $(this).val();
            populateItem(0, item_category_id);
        });

        $(document).on('change', '#item_id,#edititem_id', function (e) {
            var item_category_id = $(this).val();            
            $.ajax({
                type: "GET",
                url: base_url + "admin/itemstock/getItemunit",
                data: {'id': item_category_id},
                dataType: "json",
                success: function (data) {
                    $('#item_unit').html("<?php echo $this->lang->line('in'); ?>" + " " + data.unit);
                    $('#edit_item_unit').html("<?php echo $this->lang->line('in'); ?>" + " " + data.unit);
                }
            });
        });

        $(document).on('change', '#edititem_category_id', function (e) {
            $('#edititem_id').html("");
            var item_category_id = $(this).val();
            populateItem(0, item_category_id, 'edititem_id');
        });
		
    });
	
    function populateItem(item_id_post, item_category_id_post, htmlid = 'item_id') {
        if (item_category_id_post != "") {
            $('#' + htmlid).html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/itemstock/getItemByCategory",
                data: {'item_category_id': item_category_id_post},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var select = "";
                        if (item_id_post == obj.id) {
                            var select = "selected=selected";
                        }
                        div_data += "<option value=" + obj.id + " " + select + ">" + obj.name + "</option>";
                    });
                    $('#' + htmlid).append(div_data);
                }
            });
		}
    }

    $(document).ready(function (e) {
        $('#form1').on('submit', (function (e) {
            $("#form1btn").btnLoading();
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
                    $("#form1btn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    $(document).ready(function (e) {
        $('#editform').on('submit', (function (e) {
            $("#editformbtn").btnLoading();
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
                    $("#editformbtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    function get_data(id) {
        $.ajax({
            url: "<?php echo base_url() ?>admin/itemstock/edit/" + id,
            type: "POST",
            dataType: "json",
            success: function (res) {              
                $("#edititem_category_id").val(res.item_category_id);
                $("#edititem_id").val(res.item_id);
                $("#editsupplier_id").val(res.supplier_id);
                $("#editstore_id").val(res.store_id); 				
				$("#edit_item_unit").html(res.unit); 				
                var text = res.quantity ;
                let letter = text.charAt(0);
                var result = text.slice(1);               
                
                var activeSym = (letter == '-') ? '-' : '+';
                $("#edit_symbol").val(activeSym);
                $("#edit_symbol_btn").text(activeSym);
                $("#editquantity").val(letter == '-' ? result : res.quantity);
                $('#editmyModal .sh-qty-dropdown .dropdown-item').removeClass('active');
                $('#editmyModal .sh-qty-dropdown .dropdown-item[data-value="' + activeSym + '"]').addClass('active');
                
                $("#editdate").val(res.date);               
                $("#epurchase_price").val(res.purchase_price);
                $("#editdescription").text(res.description);
                $("#itemstockid").val(res.id);
                populateItem(res.item_id, res.item_category_id, 'edititem_id');
                shModal('editmyModal').show();
               
            }
        });
    }	

    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/itemstock/delete/' + id,
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

	$(".additemstock").click(function(){
		$('#form1').trigger("reset");
		$(".dropify-clear").trigger("click");
		$('#symbol_btn').text('+');
		$('#symbol').val('+');
		$('#myModal .sh-qty-dropdown .dropdown-item').removeClass('active');
		$('#myModal .sh-qty-dropdown .dropdown-item[data-value="+"]').addClass('active');
	});

    $(document).on('click', '.sh-qty-dropdown .dropdown-item', function(e) {
        e.preventDefault();
        var val = $(this).data('value');
        var $group = $(this).closest('.input-group');
        $group.find('.sh-qty-toggle').text(val);
        $group.find('input[type=hidden]').not('#itemstockid').val(val);
        $(this).closest('.sh-qty-dropdown').find('.dropdown-item').removeClass('active');
        $(this).addClass('active');
    });

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'editmyModal');
        // Reinitialize Dropify when modals open (plugin fails on hidden elements)
        $('#myModal, #editmyModal').on('shown.bs.modal', function () {
            $(this).find('.filestyle').dropify();
        });
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/itemstock/getitemstockDatatable',[],[],100);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
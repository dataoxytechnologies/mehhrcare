<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('medicine_purchase_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('medicine_purchase', 'can_add')) { ?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpurchase"><i class="fa fa-plus"></i> <?php echo $this->lang->line('purchase_medicine'); ?></a> 
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('medicine_purchase_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('medicine_purchase_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('purchase_no'); ?></th>
                                    <th><?php echo $this->lang->line('purchase_date'); ?></th>
                                    <th><?php echo $this->lang->line('bill_no');?></th>
                                    <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('total')." (".$currency_symbol.")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('discount')." (".$currency_symbol.")"; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('tax')." (".$currency_symbol.")"; ?></th>
                                    <th class="noExport text-end"><?php echo $this->lang->line('net_amount')." (".$currency_symbol.")"; ?></th>                                  
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>                                                    
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <form id="bill" accept-charset="utf-8" method="post">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('purchase'); ?></h5>
                <div class="sh-supplier-select-wrap">
                    <select name="supplier_id" class="form-control select2 supplier_select2">
                        <option value=""><?php echo $this->lang->line('select_supplier'); ?></option>
                        <?php foreach ($supplierCategory as $dkey => $dvalue) { ?>
                            <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($supplier_select)) && ($supplier_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo $dvalue["supplier"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <input name="invoice_no" id="invoice_no" type="text" value="" class="form-control sh-inp-180" placeholder="<?php echo $this->lang->line('bill_no'); ?>"/>
                <input name="date" id="txtDate10" type="text" class="form-control datetime sh-inp-200" autocomplete="off" placeholder="<?php echo $this->lang->line('purchase_date'); ?>"/>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body sh-modal-canvas pb5">                    
                    <!-- hidden field preserved for JS -->
                    <input name="supplier_name" readonly hidden id="supplier_name" type="text" class="form-control"/>

                    <!-- Card 1: Medicine Table -->
                    <div class="sh-form-card mb-3">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_name'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered table-hover tblProducts mb-0" id="tableID">
                                    <thead>
                                    <tr class="white-space-nowrap">
                                        <th width="10%"><?php echo $this->lang->line('medicine_category'); ?><small class="req"> *</small></th>
                                        <th width="10%"><?php echo $this->lang->line('medicine_name'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('batch_no'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('expiry_month'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('mrp') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('batch_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th><?php echo $this->lang->line('sale_price') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('packing_qty'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('quantity'); ?><small class="req"> *</small></th>
                                        <th class="text-end"><?php echo $this->lang->line('purchase_price') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                                        <th class="text-end"><?php echo $this->lang->line('tax'); ?><small class="req"> *</small></th>
                                        <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr id="row0" class="white-space-nowrap">
                                        <td>
                                            <select class="form-control" name='medicine_category_id[]' onchange="getmedicine_name(this.value, '0')">
                                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?></span>
                                        </td>
                                        <td>
                                            <select class="form-control select2" onchange="getmedicinedetails(this.value, 0)" id="medicine_name0" name='medicine_name[]'>
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="text" name="batch_no[]" id="batchno" class="form-control">
                                            <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="text" name="expiry_date[]" id="expiry" class="form-control expiry_date">
                                            <span class="text-danger"><?php echo form_error('expiry_date[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="text" name="mrp[]" id="mrp" class="form-control">
                                            <span class="text-danger"><?php echo form_error('mrp[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.01" name="batch_amount[]" id="batch_amount" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="sale_rate[]" id="sale_price" class="form-control">
                                            <span class="text-danger"><?php echo form_error('sale_rate[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="1" name="packing_qty[]" id="packing_qty" class="form-control">
                                            <span class="text-danger"><?php echo form_error('packing_qty[]'); ?></span>
                                        </td>
                                        <td>
                                            <input type="text" name="quantity[]" onchange="multiply(0)" id="quantity0" class="form-control text-end quantity">
                                        </td>
                                        <td class="text-end">
                                            <input type="text" name="purchase_price[]" onchange="multiply(0)" id="purchase_price0" class="form-control text-end purchase_price">
                                            <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="input-group">
                                                <input type="text" class="form-control right-border-none purchase_tax" name="purchase_tax[]" id="purchase_tax0" autocomplete="off">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                        </td>
                                        <td class="text-end" width="10%">
                                            <input type="text" name="amount[]" id="amount0" class="form-control text-end amount" readonly>
                                            <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                        </td>
                                        <td>
                                            <button type="button" onclick="addMore()" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Cards 2 & 3: Note/File + Billing Summary + Payment -->
                    <div class="d-flex flex-wrap gap-3">
                        <!-- Card 2: Note + Attach -->
                        <div class="sh-card-wrap">
                            <div class="sh-form-card h-100">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('note'); ?></span>
                                </div>
                                <div class="px-2 py-3">
                                    <div class="mb-3">
                                        <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" name="file" id="file" class="form-control filestyle">
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Card 3: Billing Summary + Payment -->
                        <div class="sh-card-wrap">
                            <div class="sh-form-card h-100 overflow-hidden">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('total'); ?> (<?php echo $currency_symbol; ?>)</span>
                                    <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="total" id="total" class="form-control sh-summary-input text-end">
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('discount'); ?> (<?php echo $currency_symbol; ?>)</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="text" name="discount_percent" id="discount_percent" class="form-control sh-summary-input-pct text-end discount_percent" placeholder="%">
                                        <span class="text-muted small">%</span>
                                        <input type="text" name="discount" id="discount" value="0" class="form-control sh-summary-input text-end">
                                    </div>
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
                                    <input type="text" name="tax" value="0" id="tax" class="form-control sh-summary-input sh-summary-input-tax text-end">
                                </div>
                                <div class="sh-summary-row border-bottom">
                                    <span><?php echo $this->lang->line('net_amount'); ?></span>
                                    <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="net_amount" id="net_amount" class="form-control sh-summary-input text-end">
                                </div>
                                <div class="px-2 py-3">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                            <select class="form-control payment_mode" name="payment_mode">
                                                <?php foreach ($payment_mode as $key => $value) { ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)</label>
                                            <input type="text" id="payment_amount" class="form-control payment_amount text-end" readonly>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label"><?php echo $this->lang->line('payment_note'); ?></label>
                                            <textarea name="payment_note" class="form-control"></textarea>
                                            <span class="text-danger"><?php echo form_error('payment_note'); ?></span>
                                        </div>
                                    </div>
                                    <div class="cheque_div d-none" >
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-4">
                                                <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control" name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!--./modal-body-->
            </div><!--./pup-scroll-area-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('purchase_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_deletebill' class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="savesalerate" action="" method="post" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div id="reportdata"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <?php echo $this->customlib->getCSRF(); ?>
                    <?php if ($this->rbac->hasPrivilege('medicine_purchase', 'can_edit')) { ?>
                    <button type="submit" id="updatesalerate" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="edit_bill" tabindex="-1" aria-labelledby="editBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBillLabel"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('purchase'); ?></h5>
                <div class="sh-edit-supplier-wrap">
                    <select onchange="get_SupplierDetails(this.value)" class="form-control select2" id="editsupplier" name="supplier">
                        <option value=""><?php echo $this->lang->line('select_supplier'); ?></option>
                        <?php foreach ($supplierCategory as $dkey => $dvalue) { ?>
                            <option value="<?php echo $dvalue['id']; ?>"><?php echo html_escape($dvalue['supplier']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <input name="purchase_no" id="purchaseno" readonly type="text" class="form-control sh-inp-130" placeholder="<?php echo $this->lang->line('purchase_no'); ?>"/>
                <input name="invoice_no" id="invoicenoup" type="text" class="form-control sh-inp-180" placeholder="<?php echo $this->lang->line('invoice_number'); ?>"/>
                <input name="date" id="dateedit_supplier" type="text" value="" class="form-control datetime sh-inp-200" autocomplete="off" placeholder="<?php echo $this->lang->line('purchase_date'); ?>"/>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body sh-modal-canvas pb5" id="edit_bill_details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <input type="button" onclick="addEditTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info">
                <button type="submit" form="editbill" class="d-none" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="editbillsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ADD MEDICINE TPA CHARGES MODAL -->
<div class="modal fade sh-modal sh-modal-accent" id="view_tpa_charge_model" tabindex="-1" aria-labelledby="viewTpaChargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTpaChargeLabel"><?php echo $this->lang->line('medicine_tpa_charges'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="savetparate" action="" method="post" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div id="set_data"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <?php echo $this->customlib->getCSRF(); ?>
                    <button type="submit" id="addtparate" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ADD MEDICINE TPA CHARGES MODAL -->

<script type="text/javascript">
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';
	
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
	
    $(function () {
        $('#easySelectable').easySelectable();
    })

</script>
<script type="text/javascript">
            
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {

                                },
                                onSelected: function (el) {

                                },
                                onUnSelected: function (el) {

                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);
</script>

<script type="text/javascript">
            function holdModal(modalId) {
                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }
</script>

<script>
            function getmedicine_name(id, rowid) {
                var div_data = "";
                $("#medicine_name" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                $('#medicine_name' + rowid).select2("val", 'l');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
                    type: "POST",
                    data: {medicine_category_id: id},
                    dataType: 'json',
                    success: function (res) {
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            div_data += "<option value=" + obj.id + ">" + obj.medicine_name + "</option>";
                        });
                        $("#medicine_name" + rowid).html("<option value=''><?= $this->lang->line('select') ?></option>");
                        $('#medicine_name' + rowid).append(div_data);
                        $('#medicine_name' + rowid).select2("val", '');
                    }
                });
            }          

            function get_SupplierDetails(id) {
                $("#supplier_name").html("supplier_name");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/supplierDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            $('#supplier_name').val(res.supplier_person);
                            $('#supplierid').val(res.id);
                        } else {
                            $('#supplier_name').val('Null');

                        }
                    }
                });
            }

            // Helper: month/year-only picker config (TD 6) — for medicine expiry dates.
            // Format MUST stay 'MMM/yyyy' (named month abbr like "Dec/2026"):
            //   • Backend Pharmacy::convertMonthToNumber() parses via strtotime — needs a name, not a number.
            //   • Read-back uses PHP date('M/Y') which also emits "Dec/2026", so save/edit round-trips identically.
            // TD-6 token 'M' = numeric month (1–12); 'MMM' = abbreviated name. Do NOT change to 'M/yyyy'.
            function initMonthYearPicker(el) {
                if (el._pickerInit) return;
                el._pickerInit = new tempusDominus.TempusDominus(el, {
                    allowInputToggle: true,
                    container: document.body,
                    localization: { format: 'MMM/yyyy', locale: 'en-US' },
                    display: {
                        viewMode: 'months',
                        components: {
                            calendar: true, decades: true, year: true, month: true,
                            date: false, clock: false, hours: false, minutes: false, seconds: false
                        },
                        buttons: { today: false, clear: true, close: true },
                        theme: 'light',
                        icons: {
                            type: 'icons', date: 'fa fa-calendar',
                            up: 'fa fa-arrow-up', down: 'fa fa-arrow-down',
                            previous: 'fa fa-chevron-left', next: 'fa fa-chevron-right',
                            clear: 'fa fa-trash', close: 'fa fa-times'
                        }
                    }
                });
            }

            $(document).ready(function (e) {
                var el = document.getElementById('expiry');
                if (el) initMonthYearPicker(el);
            });
            function addMore() {
				
                var table = document.getElementById("tableID");
                var table_len = (table.rows.length);
                var id = parseInt(table_len - 1);
                var div = "<td><select class='form-control date' name='medicine_category_id[]' onchange='getmedicine_name(this.value," + id + ")'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></td><td><select class='form-control select2' name='medicine_name[]' onchange='getmedicinedetails(this.value," + id + ")' id='medicine_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='batch_no[]' id='batchno" + id + "' class='form-control batch_no'></td><td><input type='text' name='expiry_date[]' id='expiry" + id + "' class='form-control expiry_date'></td><td><input type='text' name='mrp[]' id='mrp" + id + "' class='form-control mrp'></td><td><input type='number' min='0' step='0.01' name='batch_amount[]' id='batch_amount" + id + "' class='form-control mrp'></td><td><input type='text' name='sale_rate[]' id='salerate" + id + "' class='form-control sale_rate'></td><td><input type='number' min='0' step='1' name='packing_qty[]' id='packingqty" + id + "' class='form-control packing_qty'></td><td><div class='input-group'><input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='getQuantity(" + id + ")' id='quantity" + id + "' class='form-control text-end quantity'></div></td><td><input type='text' onchange='multiply(" + id + ")' name='purchase_price[]' id='purchase_price" + id + "'  class='form-control text-end purchase_price'></td><td><div class=''><div class='input-group'><input type='text' change='multiply(" + id + ")' class='form-control right-border-none purchase_tax'  name='purchase_tax[]' id='purchase_tax" + id + "'  autocomplete='off'><span class='input-group-text '> %</span></div></div></td><td><input type='text' name='amount[]' id='amount" + id + "'  class='form-control text-end amount' readonly></td>";
                var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i></button></td></tr>";
                $('.select2').select2();

                document.querySelectorAll('.expiry_date').forEach(initMonthYearPicker);
            }
         
            function delete_row(id) {
                if (confirm("<?php echo $this->lang->line('are_you_sure'); ?>")) {
                    var table = document.getElementById("tableID");
                    var rowCount = table.rows.length;
                    $("#row" + id).remove();
                }
            }
          
            $(document).ready(function (e) {
                $("#bill").on('submit', (function (e) {
                    e.preventDefault();
                    var btn = $("#billsave");
                    btn.btnLoading();
                    var table = document.getElementById("tableID");
                    var rowCount = table.rows.length;
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/addBillSupplier',
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
                            $("#billsave").btnReset();
                        },
                        error: function () {}
                    }); 
                }));
            });

            $(document).ready(function (e) {
                document.querySelectorAll('.expiry_date').forEach(initMonthYearPicker);
            });

            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url() ?>admin/pharmacy/getSupplierDetails/' + id,
                    type: "GET",
                    data: {},
                    success: function (data) {
                        $('#reportdata').html(data);
                        $('#edit_deletebill').html("<?php if($this->rbac->hasPrivilege('medicine_purchase', 'can_view')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printData(" + id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?><?php if($this->rbac->hasPrivilege('medicine_purchase', 'can_delete')) { ?><a onclick='delete_bill(" + id + ")' href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                        holdModal('viewModal');
                    },
                });
            }

            function editPurchase(id) {
                $('#edit_bill_details').html('<div class="text-center sh-loading-pad-lg"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                holdModal('edit_bill');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/editSupplierBill/' + id,
                    type: 'GET',
                    success: function (data) {
                        $('#edit_bill_details').html(data);
                        var meta = $('#editBillMeta');
                        $('#editsupplier').val(meta.data('supplierId')).trigger('change');
                        $('#purchaseno').val(meta.data('purchaseNo'));
                        $('#invoicenoup').val(meta.data('invoiceNo'));
                        $('#dateedit_supplier').val(meta.data('date'));
                        // #dateedit_supplier auto-initialized via .datetime class + event delegation
                        // Month/year-only picker for medicine expiry — global delegation only handles .date/.datetime/etc.
                        document.querySelectorAll('#edit_bill_details .expires_date, #edit_bill_details .expiry_date').forEach(initMonthYearPicker);
                    }
                });
            }

            $(document).on('input paste keyup','.purchase_price,.quantity,.purchase_tax,.discount_percent', function(e){ 
                update_amount($(e.target).closest('div.modal'));
            });

            let update_amount=(__this)=>{
            var grandTotal = 0; 
            var total_tax_amount = 0;
            var $tblrows = __this.find(".tblProducts tbody tr");  
            var discount_percent=__this.find('#discount_percent').val();
     
            $tblrows.each(function (index) {
                    var $tblrow = $(this);  
                    let quantity = parseFloat($tblrow.find("td input.quantity").val());
                    let purchase_price = parseFloat($tblrow.find("td input.purchase_price").val());
                    let purchase_tax = parseFloat($tblrow.find("td input.purchase_tax").val());
                    let row_amount=(isNaN(quantity*purchase_price)) ? 0 : quantity*purchase_price;
                    $tblrow.find("td input.amount").val(row_amount)
                    grandTotal+=row_amount;
					var discount_amt = (purchase_price*discount_percent)/100;
                    total_tax_amount += (((purchase_price-discount_amt)*quantity)*purchase_tax)/100; 
            });
           
                __this.find('#total').val(grandTotal.toFixed(2));
                discount=(grandTotal * discount_percent / 100 );
                discount = (isNaN(discount)) ? 0 : discount;
                __this.find('#discount').val(discount.toFixed(2));		
                var net_amount=((grandTotal-discount)+total_tax_amount);  
               __this.find('#tax').val(total_tax_amount.toFixed(2));
               __this.find('#net_amount').val(net_amount.toFixed(2));
               __this.find('#payment_amount').val(net_amount.toFixed(2));       

   }

   function addTotal() {
                var total = 0;
                var tax_amount=0;
                var sale_price = document.getElementsByName('amount[]');
                var tax = document.getElementsByName('purchase_tax[]');
                for (var i = 0; i < sale_price.length; i++) {
                    var inp = sale_price[i];
                    var tax_inp = tax[i];
                    if (inp.value == '') {
                        var inpvalue = 0;
                    } else {
                        var inpvalue = inp.value;
                    }

                    if (tax_inp.value == '') {
                        var tax_inpvalue = 0;
                    } else {
                        var tax_inpvalue = tax_inp.value;
                    }

                    tax_amount +=parseFloat((inpvalue) * tax_inpvalue / 100);
                    total += parseFloat(inpvalue);
                }

                var discount_percent = $("#discount_percent").val();
                var tax_percent = $("#tax_percent").val();
                if (discount_percent != '') {
                    var discount = (total * discount_percent) / 100;
                    $("#discount").val(discount.toFixed(2));
                } else {
                    var discount = $("#discount").val();
                }
                
                $("#tax").val(tax_amount.toFixed(2));
                $("#total").val(total.toFixed(2));
                var tax = $("#tax").val();
                var net_amount = parseFloat(total) + parseFloat(tax) - parseFloat(discount); 
                var cnet_amount = net_amount.toFixed(2)
                $("#net_amount").val(cnet_amount);
                $("#payment_amount").val(cnet_amount);               
                var editdate = $("#txtDate10").val();
                $("#date_result").val(editdate);
                var invoiceno = $("#invoice_no").val();
                $("#invoiceno").val(invoiceno);
                $("#billsave").removeClass('d-none');
                $(".printsavebtn").removeClass('d-none');
            }           

            function getExpire(id) {
                var batch_no = $("#batch_no" + id).val();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getExpiryDate",
                    data: {'batch_no': batch_no},
                    dataType: 'json',
                    success: function (res) {
                        if (res != null) {
                            $('#expiry_date' + id).val(res.expiry_date);
                            getQuantity(id);
                        }
                    }
                });
            }            

            function getmedicinedetails(id, rowid) {
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getmedicinedetails",
                    data: {'pharmacy_id': id},
                    dataType: 'json',
                    success: function (res) {
                         if (res) {
                            $('#purchase_tax'+ rowid).val(res.vat);                           
                        } 
                    }
                });
            }

            function get_PatientDetails(id) {
                $("#patient_name").html("patient_name");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            $('#patient_name').val(res.patient_name);
                            $('#pharma_patientid').val(res.id);
                        } else {
                            $('#patient_name').val('Null');

                        }
                    }
                });
            } 

 $("#edit_bill").on('shown.bs.modal', function () {
    var $sel = $('#editsupplier');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({ dropdownParent: $('#edit_bill') });
});

 $("#edit_bill").on('hidden.bs.modal', function () {
    $('#editbillsave').addClass('d-none');
    $('#edit_bill_details').html('');
});

 $("#myModal").on('shown.bs.modal', function () {
    var $sel = $('.supplier_select2');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({ dropdownParent: $('#myModal') });
});

 $("#myModal").on('hidden.bs.modal', function(){
    $('.cheque_div').addClass('d-none');
      $(".filestyle").next(".dropify-clear").trigger("click");
    $("#bill").find('input:text, input:password, input:file, select, textarea').val('');
    $("#bill").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    $('#tableID tbody tr:not(:first)','#bill').remove();
    $("select[id^='medicine_name']").select2("val", "");
    $(".supplier_select2").select2("val", "");
  });

  $(document).on('change','.payment_mode',function(){ 
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pharmacy/getpharmacypurchaseDatatable',[],[],100,
            [
               {  "sWidth": "70px", "aTargets": [ 2 ] ,'sClass': 'dt-body-center'},
               {  "sWidth": "150px", "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
               {  "sWidth": "150px", "aTargets": [ -2,-3,-4 ] ,'sClass': 'dt-body-right'},
               
            ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/deleteSupplierBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    errorMsg('<?php echo $this->lang->line('something_went_wrong'); ?>');
                }
            });
        }
    }
	
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pharmacy/printSupplierDetails/' + id,
            type: 'GET',
            success: function (result) {
                popup(result);
            }
        });
    }
</script>
<script>
    $('.addpurchase').click(function () {
        // #txtDate10 auto-initialized as TD 6 datetime picker via .datetime class
        SHPicker.setDate('#txtDate10', new Date());
        SHPicker.onChange('#txtDate10', dateChanged);
    });

    function dateChanged(ev) {
            var $tblrows = $('.tblProducts').find("tbody tr");
            $tblrows.each(function (index) {
            var $tblrow = $(this);
            var _row_day = $tblrow.find(".days").val();
            if(_row_day !=""){

            //==============
            var report_day =  parseInt(_row_day, 10);
            var selected_date = SHPicker.getDate('#txtDate10');
            if (!selected_date) return;
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);
            // .report_date inputs already have .date class — set value via SHPicker
            var reportDateEl = $tblrow.find(".report_date")[0];
            if (reportDateEl) SHPicker.setDate(reportDateEl, newdate);
            //================            
                
            }        
            });
        }
		
    $("#savesalerate").on('submit', (function (e) {
      e.preventDefault();
      $.ajax({
          url: "<?php echo site_url("admin/pharmacy/update_sale_rate") ?>",
          type: "POST",
          data: new FormData(this),
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
          },
          success: function (res)
          {    
             if (res.status == "fail") {                
                  var message = "";
                  $.each(res.error, function (index, value) {
                      message += value;
                  });
                  errorMsg(message);
  
              } else {
                  successMsg(res.message);
              }
          },
          error: function (xhr) { // if error occured
              alert("Error occured.please try again");
          },
          complete: function () {
           
          }
      });
 }));                                            
</script>


<script>

  function view_tpa_charge_model(id) {
                $.ajax({
                    url: '<?php echo base_url() ?>admin/pharmacy/view_tpa_charge_model',
                    type: "POST",
                    data: {id:id},
                    success: function (data) {
                        $('#set_data').html(data);                        
                        holdModal('view_tpa_charge_model');
                    },
                });
            }


$("#savetparate").on('submit', (function (e) {
    e.preventDefault();

    // Validation: at least one TPA charge must be entered before saving
    var hasTpaCharge = false;
    $('#set_data input[type="text"][name^="schedule_charge_"]').each(function () {
        if ($.trim($(this).val()) !== "") {
            hasTpaCharge = true;
            return false;
        }
    });
    if (!hasTpaCharge) {
        errorMsg("<?php echo $this->lang->line('please_enter_tpa_charges'); ?>");
        return false;
    }

    $.ajax({
          url: "<?php echo site_url("admin/pharmacy/update_tpa_rate") ?>",
          type: "POST",
          data: new FormData(this),
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
          },
          success: function (res)
          {   
            if (res.status == "fail") {                
                var message = "";
                $.each(res.error, function (index, value) {
                    message += value;
                });
                errorMsg(message);
  
              }else {
                successMsg(res.message);
              }
          },
          error: function (xhr) { // if error occured
              alert("Error occured.please try again");
          },
          complete: function () {

            window.location.reload(true);

          }
      });
}));   

    $(document).on('input paste keyup','#discount', function(e){ 
        var discount_amount = $("#discount").val();     
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
        var total=$('#total').val();         
        var discount_percent=0;      
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));    
        update_amount_by_discount($(e.target).closest('div.modal'));
    });
    
    let update_amount_by_discount=(__this)=>{
         
        var grandTotal = 0; 
        let total_tax_amount = 0;
        var $tblrows = __this.find(".tblProducts tbody tr");  
        var discount_percent=__this.find('.discount_percent').val();
      
        $tblrows.each(function (index) {
            var $tblrow = $(this);  
            grandTotal += parseFloat($tblrow.find("td input.amount").val());                   
            total_amount_with_discount = $tblrow.find("td input.amount").val()-(($tblrow.find("td input.amount").val()*discount_percent)/100);
            total_tax_amount += parseFloat((total_amount_with_discount*$tblrow.find("td input.purchase_tax").val())/100);        
        });
   
        grandTotal=  isNaN(grandTotal) ? 0 : grandTotal;
        total_tax_amount=  isNaN(total_tax_amount) ? 0 : total_tax_amount;
        __this.find('.total').val(grandTotal.toFixed(2));
        discount=(grandTotal * discount_percent / 100 );
        let discount_amount= isNaN(discount) ? 0 : discount;         
        var net_amount=((grandTotal-discount_amount)+total_tax_amount);           
        __this.find('#tax').val(total_tax_amount.toFixed(2));
        __this.find('#net_amount').val(net_amount.toFixed(2));
        __this.find('#payment_amount').val(net_amount.toFixed(2));
        __this.find('#payamount').val(net_amount.toFixed(2));
        __this.find('#amount').val(net_amount.toFixed(2));
      
        $("#billsave").removeClass('d-none');
        $(".printsavebtn").removeClass('d-none');
    }



</script>

<!-- ========== PURCHASE RETURN MODAL ========== -->
<div class="modal fade sh-modal sh-modal-accent" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel"><?php echo $this->lang->line('purchase_return'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="purchaseReturnForm" method="post" enctype="multipart/form-data">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="supplier_bill_basic_id" id="ret_bill_basic_id">
                <input type="hidden" name="supplier_id"            id="ret_supplier_id">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card mt5">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('return_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-4 mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="text-danger"> *</small></label>
                                        <input type="text" name="return_date" id="ret_return_date" class="form-control datetime" autocomplete="off">
                                        <span class="text-danger" id="err_return_date"></span>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('reason'); ?><small class="text-danger"> *</small></label>
                                        <select name="reason" id="ret_reason" class="form-control" onchange="toggleReturnNote(this.value)">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <option value="Damaged"><?php echo $this->lang->line('damaged'); ?></option>
                                            <option value="Expired"><?php echo $this->lang->line('expired'); ?></option>
                                            <option value="Wrong Medicine"><?php echo $this->lang->line('wrong_medicine'); ?></option>
                                            <option value="Other"><?php echo $this->lang->line('other'); ?></option>
                                        </select>
                                        <span class="text-danger" id="err_reason"></span>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?> <small id="ret_note_required" class="text-danger d-none" > *</small></label>
                                        <input type="text" name="note" id="ret_note" class="form-control" placeholder="">
                                        <span class="text-danger" id="err_note"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="returnFormContent"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <?php if ($this->rbac->hasPrivilege('medicine_purchase', 'can_add')) { ?>
                        <button type="submit" id="btnSaveReturn" class="btn btn-info">
                            <i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                        </button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ========== /PURCHASE RETURN MODAL ========== -->

<!-- ========== RETURN HISTORY MODAL ========== -->
<div class="modal fade sh-modal sh-modal-accent" id="returnHistoryModal" tabindex="-1" aria-labelledby="returnHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnHistoryModalLabel"><?php echo $this->lang->line('return_history'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div id="returnHistoryContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ========== /RETURN HISTORY MODAL ========== -->

<script type="text/javascript">

    function purchaseReturn(id) {
        $('#returnFormContent').html('<div class="text-center sh-loading-pad"><i class="fa fa-spinner fa-spin"></i></div>');
        $('#ret_bill_basic_id').val(id);
        $('#ret_return_date').val('');
        $('#ret_reason').val('');
        $('#ret_note').val('');
        $('#err_return_date').text('');
        $('#err_reason').text('');

        $.ajax({
            url: base_url + 'admin/pharmacy/purchaseReturn/' + id,
            type: 'GET',
            success: function (data) {
                var $html = $(data);
                var supplierId = $html.filter('#return_supplier_id').val();
                if (!supplierId) {
                    supplierId = $html.find('#return_supplier_id').val();
                }
                $('#ret_supplier_id').val(supplierId);
                $('#returnFormContent').html($html);
                // #ret_return_date should have .datetime class — set value via SHPicker:
                SHPicker.setDate('#ret_return_date', new Date());
                holdModal('returnModal');
            },
            error: function () {
                $('#returnFormContent').html('<p class="text-danger text-center">Error loading data.</p>');
            }
        });
    }

    function toggleReturnNote(val) {
        if (val === 'Other') {
            $('#ret_note_required').removeClass('d-none');
            $('#ret_note').attr('placeholder', 'Please specify...');
        } else {
            $('#ret_note_required').addClass('d-none');
            $('#ret_note').attr('placeholder', '');
            $('#err_note').text('');
        }
    }

    $('#purchaseReturnForm').on('submit', function (e) {
        e.preventDefault();
        $('#err_return_date').text('');
        $('#err_reason').text('');
        $('#err_note').text('');

        if ($('#ret_reason').val() === 'Other' && $('#ret_note').val().trim() === '') {
            $('#err_note').text('Note is required when reason is Other.');
            return;
        }

        var btn = $('#btnSaveReturn');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line('processing'); ?>');
        $.ajax({
            url: base_url + 'admin/pharmacy/savePurchaseReturn',
            type: 'POST',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                if (res.status == 'fail') {
                    if (res.error && res.error.return_date) { $('#err_return_date').text(res.error.return_date); }
                    if (res.error && res.error.reason)      { $('#err_reason').text(res.error.reason); }
                    if (res.message) { errorMsg(res.message); }
                } else {
                    successMsg(res.message);
                    shModal('returnModal').hide();
                    window.location.reload(true);
                }
            },
            error: function () { errorMsg('Error occurred. Please try again.'); },
            complete: function () { btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>'); }
        });
    });

    function purchaseReturnHistory(id) {
        $('#returnHistoryContent').html('<div class="text-center sh-loading-pad"><i class="fa fa-spinner fa-spin"></i></div>');
        holdModal('returnHistoryModal');
        $.ajax({
            url: base_url + 'admin/pharmacy/getPurchaseReturnHistory/' + id,
            type: 'GET',
            success: function (data) {
                $('#returnHistoryContent').html(data);
            },
            error: function () {
                $('#returnHistoryContent').html('<p class="text-danger text-center">Error loading history.</p>');
            }
        });
    }

</script>
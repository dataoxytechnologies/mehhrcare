<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();

?>
 
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('donor_details'); ?>
                        </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('blood_donor', 'can_add')) {?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addblood"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_blood_donor'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                       <div class="download_label"><?php echo $this->lang->line('donor_details'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('donor_details'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('donor_name'); ?></th>
                                        <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('contact_no'); ?></th>
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <?php 
                                            if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th ><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                            }
                                        ?>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div> 

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_donor_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('donor_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('donor_name'); ?> <small class="req">*</small></label>
                                            <input type="text" name="donor_name" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('donor_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('date_of_birth'); ?> <small class="req">*</small></label>
                                            <input type="text" name="date_of_birth" class="form-control form-control-sm date">
                                            <span class="text-danger"><?php echo form_error('date_of_birth'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?> <small class="req">*</small></label>
                                            <select name="blood_group" class="form-control form-control-sm">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($bloodgroup as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('gender'); ?> <small class="req">*</small></label>
                                            <select class="form-control form-control-sm" name="gender">
                                                <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($genderList as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('father_name'); ?></label>
                                            <input type="text" name="father_name" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('contact_no'); ?></label>
                                            <input type="text" name="contact_no" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('contact_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('address'); ?></label>
                                            <textarea name="address" class="form-control form-control-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div><?php echo display_custom_fields('donor'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_donor_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('donor_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                                <div class="row g-2">
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('donor_name'); ?> <small class="req">*</small></label>
                                            <input type="text" name="donor_name" id="donor_name" value="<?php echo set_value('donor_name'); ?>" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('donor_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('date_of_birth'); ?> <small class="req">*</small></label>
                                            <input type="text" name="date_of_birth" id="date_of_birth" class="form-control form-control-sm date">
                                            <span class="text-danger"><?php echo form_error('date_of_birth'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?> <small class="req">*</small></label>
                                            <select id="blood_group" name="blood_group" class="form-control form-control-sm">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($bloodgroup as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('gender'); ?> <small class="req">*</small></label>
                                            <select class="form-control form-control-sm" id="gender" name="gender">
                                                <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($genderList as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('father_name'); ?></label>
                                            <input type="text" name="father_name" id="father_name" value="<?php echo set_value('father_name'); ?>" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('contact_no'); ?></label>
                                            <input type="text" name="contact_no" id="contact_no" value="<?php echo set_value('contact_no'); ?>" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('contact_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('address'); ?></label>
                                            <textarea name="address" id="address" class="form-control form-control-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div id="customfield"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="addBloodDetailModal" tabindex="-1" aria-labelledby="addBloodDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBloodDetailModalLabel"><?php echo $this->lang->line('bag_stock_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="donorblood" accept-charset="utf-8" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <input type="hidden" name="blood_donor_id" id="donor_id">
                        <input type="hidden" name="blood_bank_product" id="blood_bank_product">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('bag_stock_details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('donate_date'); ?> <small class="req">*</small></label>
                                            <input name="donate_date" type="text" class="form-control form-control-sm datetime">
                                            <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('bag_no'); ?> <small class="req">*</small></label>
                                            <input name="bag_no" type="text" class="form-control form-control-sm">
                                            <span class="text-danger"><?php echo form_error('bag_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('volume'); ?></label>
                                            <input id="volume" name="volume" type="text" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('unit_type'); ?></label>
                                            <select name="unit" id="unit" class="form-control form-control-sm unit_type">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($unit_type as $unit_type_key => $unit_type_value) { ?>
                                                <option value="<?php echo $unit_type_value->id; ?>"><?php echo $unit_type_value->unit; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('unit_type'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('lot'); ?></label>
                                            <input name="lot" type="text" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('charge_category'); ?> <small class="req">*</small></label>
                                            <select name="charge_category" id="charge_category" class="form-control form-control-sm select2 charge_category">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('charge_name'); ?> <small class="req">*</small></label>
                                            <select name="charge_id" id="code" class="form-control form-control-sm addcharge select2">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                                            <input type="text" name="standard_charge" id="addstandard_charge" class="form-control form-control-sm" value="<?php echo set_value('standard_charge'); ?>">
                                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="qty" id="qty" value="1">
                                    <div class="col-sm-12">
                                        <div class="mb-2">
                                            <label class="form-label small"><?php echo $this->lang->line('institution'); ?></label>
                                            <input name="institution" type="text" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('note'); ?></span>
                                    </div>
                                    <div class="px-2 py-3">
                                        <textarea name="note" rows="4" id="note" class="form-control form-control-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-flex-col">
                                <div class="sh-form-card h-100 overflow-hidden">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></span>
                                    </div>
                                    <div class="sh-summary-row border-bottom">
                                        <span><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></span>
                                        <input type="text" value="0" name="total" id="total" class="form-control sh-summary-input text-end total" readonly>
                                    </div>
                                    <div class="sh-summary-row border-bottom">
                                        <span><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="text" name="discount_percent" id="discount_percent" value="0" class="form-control sh-summary-input-pct text-end discount_percent">
                                            <span class="text-muted small">%</span>
                                            <input type="text" value="0" name="discount" id="discount" class="form-control sh-summary-input text-end discount">
                                        </div>
                                    </div>
                                    <div class="sh-summary-row border-bottom">
                                        <span><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></span>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="text" name="tax_percentage" id="tax_percentage" class="form-control sh-summary-input-pct text-end tax_percentage" readonly>
                                            <span class="text-muted small">%</span>
                                            <input type="text" value="0" name="tax" id="tax" class="form-control sh-summary-input text-end tax" readonly>
                                        </div>
                                    </div>
                                    <div class="sh-summary-row border-bottom">
                                        <span><?php echo $this->lang->line('net_amount'); ?></span>
                                        <input type="text" value="0" name="net_amount" id="net_amount" class="form-control sh-summary-input text-end net_amount" readonly>
                                    </div>
                                    <div class="px-2 py-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <label class="form-label small"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select class="form-control form-control-sm payment_mode" name="payment_mode">
                                                        <?php foreach ($payment_mode as $key => $value) { ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <label class="form-label small"><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label>
                                                    <input type="text" readonly name="payment_amount" id="payment_amount" class="form-control form-control-sm payment_amount text-end">
                                                    <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cheque_div mt-2 d-none" >
                                            <div class="d-flex gap-3">
                                                <div class="flex-fill">
                                                    <div class="mb-2">
                                                        <label class="form-label small"><?php echo $this->lang->line('cheque_no'); ?> <small class="req">*</small></label>
                                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="mb-2">
                                                        <label class="form-label small"><?php echo $this->lang->line('cheque_date'); ?> <small class="req">*</small></label>
                                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                                                    </div>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="mb-2">
                                                        <label class="form-label small"><?php echo $this->lang->line('attach_document'); ?></label>
                                                        <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="donorbloodbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('donor_details'); ?></h5>
                <div class="d-flex align-items-center gap-2" id="edit_delete"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
    })
        $(document).ready(function (e) {
        $("#qty").val(1);
        $('.select2').select2();
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
<script>
            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    $("#formaddbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/add',
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
                            $("#formaddbtn").btnReset();
                        },
                        error: function () {
                        }
                    });
                }));
            });
			
            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/update',
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
                            $("#formeditbtn").btnReset();
                        },
                        error: function () {

                        }
                    });
                }));
            });

            function getRecord(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getDetails',
                    type: "GET",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);					
						 
						$('#customfield').html(data.custom_fields_value); 
                        $("#donor_name").val(data.donor_name);
                        $("#age").val(data.age);
                        $("#month").val(data.month);
                        $("#blood_group").val(data.blood_group);
                        $("#gender").val(data.gender);
                        $("#father_name").val(data.father_name);
                        $("#address").val(data.address);
                        $("#city").val(data.city);
                        $("#state").val(data.state);
                        $("#contact_no").val(data.contact_no);
                        $("#institution").val(data.institution);
                        $("#lot").val(data.lot);
                        $("#bag_no").val(data.bag_no);
                        $("#quantity").val(data.quantity); 
                        $("#updateid").val(id);
                        $("#date_of_birth").val(data.dateofbirth);
                        $('select[id="blood_group"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                        $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                       
                        shModal('viewModal').hide();
                        holdModal('myModaledit');
                    },
                })
            }
			
            $(document).ready(function (e) {
                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
                /* picker init removed - auto-init via class + event delegation in footer.php */
            });

            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getDonorBloodBatch',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                         $('#reportdata').html(data.page);
                        $("#edit_delete").html("<a href='#' class='btn btn-sm btn-light' onclick='printData(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print')?>'><i class='fa fa-print'></i></a><?php if ($this->rbac->hasPrivilege('blood_donor', 'can_edit')) {?><a href='#' class='btn btn-sm btn-light' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit')?>'><i class='fa fa-pencil'></i></a><?php }if ($this->rbac->hasPrivilege('blood_donor', 'can_delete')) {?><a onclick='delete_record(" + id + ")' href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete')?>'><i class='fa fa-trash'></i></a><?php }?>");
                        holdModal('viewModal');
                    },
                });
            }

            function delete_record(id) {
                if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/delete/' + id,
                        type: "POST",
                        data: {id: ''},
                        dataType: 'json',
                        success: function (data) {
                            successMsg('<?php echo $this->lang->line('success_message'); ?>')
                            window.location.reload(true);
                        }
                    });
                }
            }

            function addDonorBlood(id,blood_bank_product_id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getBloodBank',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#donor_id").val(id);
                        $("#blood_bank_product").val(blood_bank_product_id);
                        $('.charge_category').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
						getcharge_category("blood_bank");
                        holdModal('addBloodDetailModal');
                    },
                })
            }
			
            $(document).ready(function (e) {
                $("#donorblood").on('submit', (function (e) {
                   var button_loading= $("#donorbloodbtn");

                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/donorCycle',
                        type: "POST",
                        data: new FormData(this),
                        dataType: 'json',
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function(){
                 button_loading.btnLoading();
                 },
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
                            $("#donorbloodbtn").btnReset();
                        },
                        error: function () {
                 button_loading.btnReset();
                },
  
                complete: function(){
                 button_loading.btnReset();
                }
                    });
                }));
            });
			
            function holdModal(modalId) {
                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }

$(".addblood").click(function(){
    $('#formadd').trigger("reset");
});

$(".addDonorBlood").click(function(){
    $('#donorblood').trigger("reset");
});
</script>
<script type="text/javascript">

    function getcharge_category(module){
        var div_data = "";
        $.ajax({
            url: base_url+'admin/charges/getchargebymodule',
            type: "POST",
            data: {module:module},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
    }

	$(document).on('select2:select','.charge_category',function(){
		var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");   
		$('.addcharge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
		getchargecode(charge_category,"");
	});

    function getchargecode(charge_category,charge_id) {
      
      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.addcharge').html(div_data);
                $(".addcharge").select2("val", charge_id);            
             
            }
        });
      }
    }

	$(document).on('select2:select','.addcharge',function(){
        var charge=$(this).val();
        var orgid="";      
     
      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    var quantity=$('#qty').val();
                    quantity=  (quantity == "")? 0 :quantity;
                     var total_amout=parseFloat(res.result.standard_charge) * quantity;
                    $('#total').val(total_amout);
                    $('#addstandard_charge').val(res.result.standard_charge);
                     var discount_percent= $('#discount_percent').val();
                    $('#tax_percentage').val(res.result.percentage);
                     var discount_amount = parseFloat(total_amout*discount_percent/100);
                     var tax = $('#tax_percentage').val();
                    var tax_amount=  parseFloat((total_amout-discount_amount) * tax / 100)
                  
                    $('#tax').val(tax_amount.toFixed(2));
                    var net_amout=(total_amout-discount_amount)+tax_amount;
                    $('#net_amount').val(net_amout.toFixed(2));
                    $('#payment_amount').val(net_amout.toFixed(2));                  
                }
            }
        });
 }); 

   $(document).on('change keyup input paste','#qty',function(){
        var quantity=$(this).val();
        var standard_charge=  $('#addstandard_charge').val();       
        var tax_percent=$('#tax_percentage').val();
        var total_charge=(standard_charge == "" )? 0 :standard_charge;
        console.log(total_charge);
        var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))? 0 : parseFloat(total_charge)*parseFloat(quantity); 
         $('#total').val(apply_charge);
        var discount_percent= $('#discount_percent').val();
        var discount_amount= isNaN((apply_charge*discount_percent)/100) ? 0 : (apply_charge*discount_percent)/100;
        var final_amount=apply_charge-discount_amount;
        console.log(final_amount);
        $('#discount').val(discount_amount);
        $('#tax').val(((final_amount*tax_percent)/100));
        $('#net_amount,#payment_amount').val(final_amount+((final_amount*tax_percent)/100));
    });

    $(document).on('change keyup input paste','#discount',function(){
         calculateAmt(false);

    });
	
    $(document).on('change keyup input paste','#addstandard_charge',function(){
        var standard_charge = $("#addstandard_charge").val();
        var qty = $("#qty").val();
        $("#total").val(standard_charge*qty);
        calculateAmt(false);
    });

    $(document).on('change keyup input paste','#discount_percent',function(){
        calculateAmt(true);
        });

        function calculateAmt(is_percentage){
        var tot_amt=parseFloat($('#total').val());
            if(is_percentage){
               var dis_per=$('#discount_percent').val();
               var dis_amt = parseFloat(tot_amt*dis_per/100);
               $('#discount').val(dis_amt.toFixed(2));
            }else{
                var dis_amt= parseFloat($('#discount').val());
                var dis_per=isNaN(((dis_amt*100)/tot_amt))?0:((dis_amt*100)/tot_amt);
                $('#discount_percent').val(dis_per.toFixed(2));
            }


        var tax_per= parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt-dis_amt)*tax_per/100);
        $('#tax').val(tax_amt.toFixed(2));
        var net_amt=isNaN(tax_amt+(tot_amt-dis_amt)) ? "" : (tax_amt+(tot_amt-dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
        }
		
    function printData(id) {     
        $.ajax({
            url: base_url + 'admin/bloodbank/getdonorDetails/' + id,
            type: 'POST',
            dataType: 'json',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
	
    $(document).on('click','.print_donor_tran',function(){
        var $this = $(this);
        var record_id=$this.data('recordid');
        var transation_id=$this.data('transation_id');
        $this.btnLoading();
        $.ajax({
          url: '<?php echo base_url(); ?>admin/bloodbank/printDonorTransaction',
          type: "POST",
          data:{'transaction_id':transation_id,'donor_id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();
              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  }); 

</script>

<script type="text/javascript">
    (function($){
        'use strict';
        $(document).ready(function(){
            initDatatable('ajaxlist','admin/bloodbank/getdonordatatable',{},[],100,[
            
            { "sWidth": "200px",  "aTargets": [ -1, -2, -3 ] },
            { "bSortable": false, "aTargets": [ -1 ], "sClass": "dt-body-right" }
			 
            ]);
        });
    }(jQuery))
</script>

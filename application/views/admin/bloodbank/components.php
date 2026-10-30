<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('components_list'); ?>
                        </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('blood_bank_components', 'can_add')) {?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addblood"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_components'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('components_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('components_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <th><?php echo $this->lang->line('lot'); ?></th>                                   
                                    <th class="text-end"><?php echo $this->lang->line('institution'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                     
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="componentsadd" accept-charset="utf-8" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_components'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('blood_group'); ?> &amp; <?php echo $this->lang->line('bag'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label small"><?php echo $this->lang->line('blood_group'); ?> <small class="req">*</small></label>
                                        <select class="form-control form-control-sm select2 blood_group" id="blood_bank_product_id" name="blood_bank_product_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($bloodgroup as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) { echo "selected"; } ?>><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small"><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></label>
                                        <select class="form-control form-control-sm select2 bag_no" name="blood_donor_cycle_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('components_name'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('components_name'); ?> <small class="req">*</small></th>
                                                <th><?php echo $this->lang->line('bag'); ?> <small class="req">*</small></th>
                                                <th><?php echo $this->lang->line('volume'); ?></th>
                                                <th><?php echo $this->lang->line('unit'); ?></th>
                                                <th><?php echo $this->lang->line('lot'); ?> <small class="req">*</small></th>
                                                <th><?php echo $this->lang->line('institution'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($components as $key => $value) { ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="select[]" value="<?php echo $key; ?>"> <?php echo $value; ?></label></div>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="bag_no_<?php echo $key; ?>" value=""></td>
                                                <td><input type="text" class="form-control form-control-sm" name="volume_<?php echo $key; ?>" value=""></td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="unit_<?php echo $key; ?>">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($unit_type as $typekey => $typevalue) { ?>
                                                            <option value="<?php echo $typevalue->id; ?>"><?php echo $typevalue->unit; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm" name="lot_<?php echo $key; ?>" value=""></td>
                                                <td><input type="text" class="form-control form-control-sm" name="institution_<?php echo $key; ?>" value=""></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
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

<!-- dd -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('donor_information') ; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" method="post">
                        <div class="modal-body">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small>
                                            <select id="blood_group" name="blood_group"  class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php
                                                    foreach ($bloodgroup as $key => $value) {
                                                        ?>
                                                    <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label> <?php echo $this->lang->line('gender'); ?></label>
                                            <select class="form-control" id="gender" name="gender">
                                                <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                    foreach ($genderList as $key => $value) {
                                                        ?>
                                                    <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('father_name'); ?></label>
                                            <input type="text" name="father_name"  id="father_name" value="<?php echo set_value('father_name'); ?>" class="form-control">
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label><?php echo $this->lang->line('contact_no'); ?></label>
                                            <input type="text" name="contact_no" id="contact_no" value="<?php echo set_value('contact_no'); ?>" class="form-control">
                                            <span class="text-danger"><?php echo form_error('contact_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="Address"><?php echo $this->lang->line('address'); ?></label>
                                            <textarea name="address"  id="address" value="<?php echo set_value('address'); ?>" class="form-control" ></textarea>
                                        </div>
                                    </div>
                                </div><!--./row-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
          </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addBloodDetailModal" tabindex="-1" aria-labelledby="addBloodDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBloodDetailModalLabel"><?php echo $this->lang->line('donor') . " " . $this->lang->line('blood') . " " . $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="donorblood" accept-charset="utf-8" method="post">
                    <div class="modal-body">
                            <input type="hidden" name="blood_donor_id" id="donor_id">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('donate_date') ; ?></label>
                                        <small class="req"> *</small>
                                        <input  name="donate_date" type="text" class="form-control date"/>
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div>                              
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('bag'); ?> </label> <small class="req"> *</small>
                                        <input  name="bag_no" type="text" class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('bag_no'); ?></span>
                                    </div>
                                </div>
                                  <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('lot'); ?> </label>
                                        <input  name="lot" type="text" class="form-control"/>
                                    </div>
                                </div>
                                  <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('type') ?></label><small class="req"> *</small>
                                         <select name="charge_type" class="form-control charge_type">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value->id; ?>">
                                                <?php echo $value->charge_type; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('charge_category') ?></label><small class="req"> *</small> 
                                        <select name="charge_category" id="charge_category" class="form-control select2 charge_category">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('charge_name') ?></label>      
                                            <select name="charge_id" id="code" class="form-control addcharge select2">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small> 
                                       <input type="text" name="qty" id="qty" class="form-control"> 
                                        <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                    </div>
                                </div>                                 
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('institution'); ?></label>
                                        <input  name="institution" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div><!--./row-->
                                 <div class="divider"></div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label><?php echo $this->lang->line('note'); ?></label>
                                                        <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--./col-sm-6-->
                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td width="60%" colspan="2" class="text-end ipdbilltable">
                                                    <input type="text" placeholder="<?= $this->lang->line('total') ?>" value="0" name="total" id="total" class="form-control total sh-bb-inp-sm" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-end ipdbilltable">
                                                        <h4 class="sh-bb-pct-label"> %</h4>
                                                <input type="text" placeholder="<?= $this->lang->line('discount') ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent sh-bb-inp-pct"/></td>
                                                    <td class="text-end ipdbilltable">
                                        <input type="text" placeholder="<?= $this->lang->line('discount') ?>" value="0" name="discount" id="discount" class="form-control discount sh-bb-inp-md"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-end ipdbilltable">
                                                        <h4 class="sh-bb-pct-label"> %</h4>
                                                <input type="text" placeholder="<?= $this->lang->line('tax') ?>" name="tax_percentage" id="tax_percentage" class="form-control tax_percentage sh-bb-inp-pct" readonly/></td>
                                                    <td class="text-end ipdbilltable">
                                                        <input type="text" placeholder="<?= $this->lang->line('tax') ?>" name="tax" value="0" id="tax" class="form-control tax sh-bb-inp-md" readonly/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-end ipdbilltable">
                                                        <input type="text" placeholder="<?= $this->lang->line('net_amount') ?>" value="0" name="net_amount" id="net_amount" class="form-control net_amount sh-bb-inp-sm" readonly/></td>
                                                </tr>
                                            </table>

                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                        <select class="form-control payment_mode" name="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) {
    ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
<?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                    </div>
                                </div>
                                   <div class="col-md-6">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('payment_amount'). " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="payment_amount" id="payment_amount" class="form-control payment_amount text-end">
                                         <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                    </div>
                                </div>
                              <div class="cheque_div d-none" >
                                 <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                 <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" class="filestyle form-control" name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                                    </div>
                                </div>
                              </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <textarea name="payment_note" id="note" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div><!--./row-->  
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="donorbloodbtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
            </div>
           </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('donor_information') ; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="edit_delete"></div>
            <div class="modal-body pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get">
                            <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered examples">
                                    <tr>
                                        <th><?php echo $this->lang->line('donor') . " " . $this->lang->line('name'); ?></th>
                                        <td><span id='donor_names'></span></td>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <td><span id="ages"></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <td><span id='blood_groups'></span></td>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><span id="genders"></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <td><span id="father_names"></span></td>
                                        <th><?php echo $this->lang->line('contact_no'); ?></th>
                                        <td><span id="contact_nos"></span>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <td><span id='addresss'></span></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
                <div id="reportdata"></div>
            </div>
        </div>
    </div> 
</div>

<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
    })
        $(document).ready(function (e) {

        $('.select2').select2();
    });

         $(document).on('select2:select','.blood_group',function(){
        var bloodgroup=$(this).val();
        getBloodGroupBagNos(bloodgroup,"");
   
    });
     function getBloodGroupBagNos(bloodgroup,bagno){
        console.log(bagno);
    var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
    $.ajax({
          url: base_url+'admin/bloodbank/getbatchbybloodgroup',
          type: "POST",
          data:{'bloodgroup':bloodgroup},
          dataType: 'json',
           beforeSend: function() {
          $('.bag_no').html("");
          },
          success: function(res) {
            console.log(res.batch_list);
              $.each(res.batch_list, function (i, obj)
                {             
                    var sel = "";
                    let volume = obj.volume != null ? obj.volume : "" ;
                    let unit = obj.charge_unit != null ? obj.charge_unit : "" ;
                    
                    if(volume != '' && unit!= ''){var sfsdfsdf =  " (" + volume + " " + unit + ") " ; }else{var sfsdfsdf = '';}
                    
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no  + " " + sfsdfsdf + "</option>";

                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagno);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

         },
         complete: function() {

       }
      });
    }
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
                $("#componentsadd").on('submit', (function (e) {
                    $("#formaddbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/addcomponents',
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
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
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
                    url: '<?php echo base_url(); ?>admin/bloodbank/getDetails',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $.ajax({
                            url: '<?php echo base_url(); ?>admin/bloodbank/getDonorBloodBatch',
                            type: "POST",
                            data: {blood_donor_id: id},
                            success: function (data) {
                                $('#reportdata').html(data);
                            },
                        });
                        $("#donor_names").html(data.donor_name);
                        $("#ages").html(data.age + " Year " + data.month + " Month");
                        $("#blood_groups").html(data.blood_group);
                        $("#genders").html(data.gender);
                        $("#father_names").html(data.father_name);
                        $("#contact_nos").html(data.contact_no);
                        $("#addresss").html(data.address);
                        $("#edit_delete").html("<?php if ($this->rbac->hasPrivilege('blood_donor', 'can_edit')) {?><a href='#' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip' title='' title='<?= $this->lang->line('edit') ?>'><i class='fa fa-pencil'></i></a><?php }if ($this->rbac->hasPrivilege('blood_donor', 'can_delete')) {?><a onclick='delete_record(" + id + ")'  href='#'  data-bs-toggle='tooltip'  title='<?= $this->lang->line('delete') ?>'><i class='fa fa-trash'></i></a><?php }?>");
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

            function addDonorBlood(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getBloodBank',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#donor_id").val(id);
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

 $("#myModal").on('hidden.bs.modal', function (e) {     
    $(".blood_group").select2("val", "");
    $(".bag_no").html('').select2({data: [{id: '', text: '<?php echo $this->lang->line('select'); ?>'}]});
     $('form#componentsadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#componentsadd').find('select option:selected').removeAttr('selected');
     $('form#componentsadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

$(".addDonorBlood").click(function(){
    $('#donorblood').trigger("reset");
});
</script>

<script type="text/javascript">
        $(document).on('change','.charge_type',function(){
        var charge_type=$(this).val();     
        $('.charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     getcharge_category(charge_type,"");
    });

    function getcharge_category(charge_type,charge_category) {
           var div_data = "";
           if(charge_type != ""){

        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type},
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
    }

 $(document).on('select2:select','.charge_category',function(){
    var charge_category=$(this).val();      
    $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");   
    $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
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
                     var total_amout=parseFloat(res.standard_charge) * quantity;
                    $('#total').val(total_amout);
                    $('#addstandard_charge').val(res.standard_charge);
                     var discount_percent= $('#discount_percent').val();
                    $('#tax_percentage').val(res.percentage);
                     var discount_amount = parseFloat(total_amout*discount_percent/100);
                     var tax = $('#tax_percentage').val();
                    var tax_amount=  parseFloat((total_amout-discount_amount) * tax / 100)
                    console.log(total_amout-discount_amount);
                    $('#tax').val(tax_amount);
                    $('#net_amount').val((total_amout-discount_amount)+tax_amount);                  
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

        $(document).on('change keyup input paste','#discount_percent',function(){
        calculateAmt(true);
        });

        function calculateAmt(is_percentage){
        var tot_amt=parseFloat($('#total').val());
            if(is_percentage){
               var dis_per=$('#discount_percent').val();
               var dis_amt = isNaN(parseFloat(tot_amt*dis_per/100)) ? 0 : parseFloat(tot_amt*dis_per/100);
               $('#discount').val(dis_amt.toFixed(2));
            }else{
        var dis_amt= parseFloat($('#discount').val());
         var dis_per=isNaN(((dis_amt*100)/tot_amt))?0:((dis_amt*100)/tot_amt);
         $('#discount_percent').val(dis_per.toFixed(2));
            }


        var tax_per= parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt-dis_amt)*tax_per/100);
        var net_amt=isNaN(tax_amt+(tot_amt-dis_amt))?"" :(tax_amt+(tot_amt-dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
        }

    function deleterecord(id) { 
            if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/deleteComponent/' + id,
                    type: "POST",
                    data: {id: ''},
                    dataType: 'json',
                    success: function (data) {
                        successMsg('<?php echo $this->lang->line('delete_message'); ?>')
                        window.location.reload(true);
                    }
                });
            }
    }

</script> 

<script type="text/javascript">
    (function($){
        'use strict';
        $(document).ready(function(){
            initDatatable('ajaxlist','admin/bloodbank/getcomponets');
        });
    }(jQuery))
</script>

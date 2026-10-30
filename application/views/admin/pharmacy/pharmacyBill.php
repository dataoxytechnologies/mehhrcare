<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">                        
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('pharmacy_bill'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {?>
                            <button type="button" class="btn btn-primary btn-sm generatebill" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('generate_bill'); ?></button>
                            <button type="button" class="btn btn-warning btn-sm sr_openbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-undo"></i> Sale Return</button>
                            <a href="<?php echo base_url(); ?>admin/pharmacy/salereturnbills" class="btn btn-secondary btn-sm"><i class="fa fa-list-alt"></i> Sale Return Bills</a>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('medicine', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/pharmacy/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('medicines'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">

                        <div class="download_label"><?php echo $this->lang->line('pharmacy_bill'); ?></div>
                     <div class="table-responsive">
                        <table id="pharmacyBillTable" class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('pharmacy_bill'); ?>"> <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('generated_by'); ?></th>
                                    <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                    <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }  
                                        }
                                    ?> 
                                    <th class="text-end"><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('tax') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('net_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('paid_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-end"><?php echo $this->lang->line('refund_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="noExport text-end"><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                </tr>
                            </thead>
                        </table>
                      </div>  
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_deletebill' class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
               <form id="bill" accept-charset="utf-8" method="post" onkeydown="return event.key != 'Enter';">
            <div class="modal-header">
                <h5 class="modal-title" id="billModalLabel"><?php echo $this->lang->line('generate_bill'); ?></h5>
                <input type="hidden" name="organisation_id" id="organisation_id">
                <input type="hidden" name="insurance_id" id="insurance_id">
                <input type="hidden" name="insurance_validity" id="insurance_validity">
                <select class="form-control patient_list_ajax sh-bill-patient-pick" id="addpatient_id" name="patient_id" onchange="get_PatientDetails(this.value)"></select>
                <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                <a onclick="holdModal('myModalpa')" id="add" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                <?php } ?>
                <div class="input-group sh-bill-rx-search">
                    <input type="hidden" name="is_prescription_no" id="is_prescription_no">
                    <input type="text" class="form-control" id="prescription_no" placeholder="<?php echo $this->lang->line('prescription_no'); ?>" name="prescription_no">
                    <button class="btn btn-secondary" type="button" id="search_prescription"><i class="fa fa-search"></i></button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input class="form-check-input apply_tpa mt-0" id="is_tpa" name="is_tpa" type="checkbox" value="1" autocomplete="off">
                    <label class="form-check-label text-white mb-0" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="tabinsetbottom sh-modal-info-strip pt5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4">
                                <label><?php echo $this->lang->line('bill_no'); ?>
                                    <input readonly name="bill_no" id="billno" type="text" class="transparentbg-border atm-readonly-input"/>
                                    <span class="text-danger"><?php echo form_error('bill_no'); ?></span>
                                </label>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-4">
                                <label><?php echo $this->lang->line('case_id'); ?><input readonly name="case_reference_id" id="case_reference_id" type="text" class="transparentbg-border atm-readonly-input"/>
                                <span class="text-danger"><?php echo form_error('case_reference_id'); ?></span></label>
                            </div>
                            <div class="col-lg-7 col-md-5 col-sm-4 text-end text-md-left">
                                <label><?php echo $this->lang->line('date'); ?>
                                    <input name="date" type="text" id="txtDate10" value="<?php echo date($this->customlib->getHospitalDateFormat(true, true)); ?>" class="transparentbg-border atm-readonly-input datetime"/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body sh-modal-canvas pb5">
                  <div id="load"></div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="saveprint" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sale Return Modal -->
<div class="modal fade sh-modal sh-modal-nospace" id="saleReturnModal" tabindex="-1" aria-labelledby="srModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="srForm" accept-charset="utf-8" method="post" onkeydown="return event.key != 'Enter';">
                <div class="modal-header">
                    <h5 class="modal-title" id="srModalLabel"><?php echo $this->lang->line('sale_return'); ?></h5>
                    <input type="hidden" name="sr_patient_id" id="sr_patient_id_val">
                    <select class="form-control patient_list_ajax sh-sr-patient-pick" id="sr_patient_id" name="sr_patient_id_display" disabled></select>
                    <div class="input-group sh-sr-bill-search">
                        <input type="text" class="form-control" id="sr_bill_search" placeholder="<?php echo $this->lang->line('bill_no'); ?> / <?php echo $this->lang->line('sale_bill_no'); ?>">
                        <button class="btn btn-secondary" type="button" id="sr_search_bill"><i class="fa fa-search"></i></button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="tabinsetbottom sh-modal-info-strip pt5">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4">
                                    <label><?php echo $this->lang->line('bill_no'); ?>
                                        <input readonly name="sr_return_no" id="sr_return_no" type="text" class="transparentbg-border atm-readonly-input"/>
                                    </label>
                                </div>
                                <div class="col-lg-10 col-md-5 col-sm-4 text-end text-md-left">
                                    <label><?php echo $this->lang->line('date'); ?>
                                        <input name="sr_date" type="text" id="sr_txtDate" value="" class="transparentbg-border atm-readonly-input"/>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body sh-modal-canvas pb5">
                        <div id="sr_load"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="sr_save" class="btn btn-info" disabled><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script id="medcat-template" type="text/template">
   <?php
foreach ($medicineCategory as $dkey => $med_cat_value) {
    ?>
    <option value='<?php echo $med_cat_value["id"]; ?>'>
        <?php echo $med_cat_value["medicine_category"] ?>
    </option>
    <?php
     }
   ?>
</script>

<script type="text/javascript">
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

    var total_rows=1;
    $(document).on('input paste keyup click','.apply_tpa,.tax_percent,.discount_percent,.qty,.medicine_category,.medicine_name,.batch_no,.price,.medicine_discount', function(e){
        update_amount($(e.target).closest('div.modal'));
    });
                
    function update_amount(__this){
            var discount_percent=__this.find('.discount_percent').val();            
            var tax_percent=__this.find('.tax_percent').val();
            var grandTotal = 0;      
            var discount = 0;      
            var tax = 0;
            var total_tax_amount=0;    
           
            var $tblrows = __this.find(".tblProducts tbody tr");
            $tblrows.each(function (index) {
                    var $tblrow = $(this);  
                    var qty = $tblrow.find(".qty").val();
                    var price = $tblrow.find(".price").val();
                    var tax_percentage = $tblrow.find(".medicine_tax").val();
                    var discount_percentage = $tblrow.find(".medicine_discount").val();
                    var discount_amount = isNaN((price * discount_percentage / 100 )) ? 0 : (price * discount_percentage / 100 );
                    var subTotal = parseInt(qty, 10) * parseFloat(price - discount_amount); 

                    if (!isNaN(subTotal)) {
                            $tblrow.find('.subtot').val(subTotal.toFixed(2));
                            
                            discount = isNaN((subTotal * discount_percent / 100 )) ? 0 : (subTotal * discount_percent / 100 );                          
                            var tax_amount=(((subTotal-discount)*tax_percentage) / 100 );                           
                            
                            total_tax_amount += isNaN(tax_amount) ? 0 : tax_amount;                       
                            var stval = parseFloat(subTotal.toFixed(2));
                            grandTotal += isNaN(stval) ? 0 : stval;                     
                    }else{
                        subTotal=0;
                         $tblrow.find('.subtot').val(subTotal.toFixed(2));     
                    }        
            });

                 discount=isNaN((grandTotal * discount_percent / 100 )) ? 0 : (grandTotal * discount_percent / 100 );                 
                var net_amount=((grandTotal-discount)+total_tax_amount);
               __this.find('.total').val(grandTotal.toFixed(2));
               __this.find('.discount').val(discount.toFixed(2));
               __this.find('.tax').val(total_tax_amount.toFixed(2));
               __this.find('.net_amount').val(net_amount.toFixed(2));
               __this.find('.payment_amount').val(net_amount.toFixed(2));
        }        
  
</script>
<script type="text/javascript">
$(document).on('keyup','#prescription_no', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
       getPrescriptionData();
    }
});

$(document).on('click','#search_prescription',function(){
 getPrescriptionData();
});

function getPrescriptionData(){
    var createModal=$('#billModal');
    var prescription_no=$("input[name=prescription_no]").val();
    
    if(prescription_no!=""){
  $.ajax({
        url: '<?php echo base_url(); ?>admin/pharmacy/getPrescriptionById',
        type: "POST",
        data:{'prescription_no':$("input[name=prescription_no]").val()},
        dataType: 'json',
         beforeSend: function() {
             createModal.addClass('modal_loading');
        },
        success: function(res) {    
      if(res.status === 0){
            $("#is_prescription_no").val("");
            $(".generatebill").trigger("click");
            $('#addpatient_id').select2("val", "");
            errorMsg(res.msg);
      }else{
            $("#is_prescription_no").val(prescription_no);

            $('#billModal .modal-body').html(res.page);
            $('#case_reference_id').val(res.case_reference_id);
            var option = new Option(res.patient_name+" ("+res.patient_id+")", res.patient_id, true, true);
            $("#bill .patient_list_ajax").append(option).trigger('change');
      
            total_rows=res.total_rows;
            if(res.total_rows <= 0){
                errorMsg("No prescription found");
            }
            if(res.total_rows > 0){
              var medicineTable=$("#billModal .modal-body").find('table.tblProducts');
                //=============
              medicineTable.find("tbody tr").each(function() {
                var medicine_obj = $(this).find("td select.medicine_name");
                var batch_obj = $(this).find("td select.batch_no");
                var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                var post_medicine_batch_detail_id = $(this).find("td input.post_medicine_batch_detail_id").val();
                var post_medicine_sale_price = $(this).find("td input.sale_price").val();
                var post_medicine_quantity = $(this).find("td input.quantity").val();
                var medicine_array = {};
                medicine_array['quantity'] = post_medicine_quantity;
                medicine_array['sale_price'] = post_medicine_sale_price;
                $('.select3').select2();
                $('.filestyle','#billModal').dropify();
                getMedicine(medicine_obj,'',post_medicine_id);
                $(this).find('.medicine_category_display').val(medicine_obj.find('option[value="'+post_medicine_id+'"]').attr('data-category') || '');
                getBatchNo(medicine_obj,post_medicine_id,post_medicine_batch_detail_id);
              });
            }
      }     
     
    createModal.removeClass('modal_loading');

        },
           error: function(xhr) { // if error occured
        alert("Error occured.please try again");
              createModal.removeClass('modal_loading'); 
    },
    complete: function() {
            createModal.removeClass('modal_loading');
    }
    });
}else{
    $("#is_prescription_no").val("");
    $(".generatebill").trigger("click");
    $('#addpatient_id').select2("val", "");
}
}

  $(document).on('click','.generatebill',function(){
    $("#is_prescription_no").val("");
       var createModal=$('#billModal');
       var $this = $(this);
       $this.btnLoading();
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/createBill',
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $this.btnLoading();
                            createModal.addClass('modal_loading');
                    },
                    success: function(res) {
                        $('#billModal #billno').val(res.bill_no);
                        $('#billModal .modal-body').html(res.page);
                        // If this regeneration came from a TPA toggle while editing an
                        // existing bill, restore the edit identity so the save UPDATES
                        // this bill (and deletes its old detail rows) instead of
                        // inserting a new one. Header fields (patient, case id) persist
                        // on their own; only these live inside the swapped modal-body.
                        if (window.pharmacyBillEditRestore) {
                            var _editCtx = window.pharmacyBillEditRestore;
                            window.pharmacyBillEditRestore = null;
                            var $billBody = $('#billModal .modal-body');
                            // Keep the original bill no; success() above set it to a
                            // freshly generated number meant for new bills.
                            $('#billModal #billno').val(_editCtx.bill_no);
                            $('#action_type', $billBody).val('update');
                            $('<input>').attr({type:'hidden', name:'pharmacy_bill_basic_id', id:'pharmacy_bill_basic_id', value:_editCtx.id}).appendTo($billBody);
                            $.each(_editCtx.previous_ids, function (i, pid) {
                                $('<input>').attr({type:'hidden', name:'previous_ids[]', value:pid}).appendTo($billBody);
                            });
                        }
                        //=====added===========
                        $("#apply_tpa").attr("checked",false);
                        //=====added===========
                        $("#organisation_id").val("");
                        $("#insurance_id").val("");
                        $("#insurance_validity").val(""); 
                        //==========================
                        $('.filestyle','#billModal').dropify();
                        $('#billModal .select3').select2({ dropdownParent: $('#billModal') });
                        shModal('billModal').show();
                        createModal.removeClass('modal_loading');
                    },
                        error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                        $this.btnReset();
                            createModal.removeClass('modal_loading');
                },
                complete: function() {
                        $this.btnReset();
                        createModal.removeClass('modal_loading');
                        // Drop any unconsumed edit-restore context (e.g. if the reload
                        // errored) so it can never leak into a later new-bill creation.
                        window.pharmacyBillEditRestore = null;
                }
                });
  });

   $(document).ready(function(){
        modal_click_disabled('viewModal', 'billModal');
        // .datetime inputs auto-initialized via event delegation in footer.php (TD 6)
   });
   
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });

    function get_PatienteditDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patienteditid').val(res.id);
                    $('#patienteditname').val(res.patient_name);
                }
            }
        });
    }

    $(document).on('select2:open','.medicine_name',function(){
        var $select = $(this);
        if ($select.find('option[value!=""]').length === 0) {
            getMedicine($select, '', 0);
        }
    });

   $(document).on('click','.add_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            shModal(payment_modal[0]).show(); 
            getPayments(record_id);
    });

    function getPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getPharmacyTransaction',
            type: "POST",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){

            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
             $('.filestyle','#addPaymentModal').dropify();
            payment_modal.removeClass('modal_loading');               
           
            },
             error: function () {

             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){

             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

      $(document).on('click','.print_receipt',function(){
      var $this = $(this);
      var record_id=$this.data('recordId')
      $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pharmacy/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.btnLoading();      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("Error occured.please try again");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

    function getMedicine(med_cat_obj,val,medicine_id){
      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
             async: false,
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
            },
            success: function (res) {
                var div_data="<option value=''><?= $this->lang->line('select') ?></option>";
                $.each(res, function (i, obj)
                {
                    var sel = "";
                            if (medicine_id == obj.id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + " data-category='" + (obj.category_name || '') + "'>" + obj.medicine_name + "</option>";
                });
           
                medicine_colomn.html(div_data);
                medicine_colomn.select2("val", medicine_id);               
            }
        });
} 

 $(document).on('select2:select','.medicine_name',function(){
     var category = $(this).find('option:selected').attr('data-category') || '';
     $(this).closest('tr').find('.medicine_category_display').val(category);
     getBatchNo($(this),$(this).val(),0);
    });

 function getBatchNo(med_obj,pharmacy_id,batch_id){       
        var batch_no=med_obj.closest('tr').find('.batch_no');      
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/getBatchNoList",
            data: {'pharmacy_id':pharmacy_id,'batch_id':batch_id},
            dataType: 'json',
             async: false,
               beforeSend: function() {
              batch_no.html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
            },
            success: function (res) {
                var div_data="<option value=''><?= $this->lang->line('select') ?></option>";
                $.each(res, function (i, obj)
                {
                            var sel = "";
                            if (batch_id == obj.id) {
                                sel = "selected";
                            }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.batch_no + "</option>";
                });
               batch_no.select2();
               batch_no.html(div_data);
               batch_no.select2("val", batch_id);
            }
        });
 }


 $(document).on('select2:select','.batch_no',function(){
    var medicine_details = {};
      getMedicineDetail($(this),$(this).val(),medicine_details);
    });

function getMedicineDetail(batch_obj,medicine_batch_detail_id,medicine_details){
      var current_row =batch_obj.closest('tr');
      let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
      // Use the bill patient select specifically. The .patient_list_ajax class is
      // shared with the (disabled/empty) sale-return patient select, so a class
      // lookup could return an empty patient_id and break TPA pricing.
      var patient_id= $('#addpatient_id').val();
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/getExpiryDate",
            data: {'medicine_batch_detail_id': medicine_batch_detail_id,patient_id:patient_id,is_tpa:is_tpa},
            dataType: 'JSON',
            asyn:true,
               beforeSend: function(res) {
            },
            success: function (res) { 
               
                if(res.status == 0){
                    errorMsg(res.msg);
                    current_row.find('.expiry').val('');
                    current_row.find('.exp_date').val('');
                    current_row.find('.available_qty').text('');
                    current_row.find('.medicine_tax').val('');
                    current_row.find('.price').val('');
                    current_row.find('.subtot').val('');
                }else{
                var quantity_remaining = parseInt(res.result.available_quantity) - parseInt(res.result.used_quantity);
                current_row.find('.expiry').val(res.result.expiry);
                current_row.find('.exp_date').val(res.result.expiry_date);
                current_row.find('.available_qty').text(quantity_remaining);
                current_row.find('.medicine_tax').val(res.result.tax);
                let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
                
                if($.isEmptyObject(medicine_details)){ //on new bill 
                    if(res.status == 2){
                        errorMsg(res.msg);
                    }
                    current_row.find('.available_quantity').val(quantity_remaining);
                    current_row.find('.price').val(res.result.sale_rate);
                    update_amount(batch_obj.closest('div.modal'));
                }else{ //on edit bill
                    var total_price=medicine_details.sale_price*medicine_details.quantity;
                    current_row.find('.available_quantity').val(parseInt(quantity_remaining)+parseInt(medicine_details.quantity));
                    current_row.find('.qty').val(medicine_details.quantity);
					current_row.find('.medicine_discount').val(medicine_details.discount);
                    //=======================
                    var discount_amount = isNaN((total_price * medicine_details.discount / 100 )) ? 0 : (total_price * medicine_details.discount / 100 );    
                    var subTotal = parseFloat(total_price - discount_amount); 
                    //=======================
                    current_row.find('.price').val(medicine_details.sale_price);
                    current_row.find('.subtot').val(subTotal.toFixed(2));
                }
            }
            }
        });
}

$(document).on('click','.add-record',function(){
var table = document.getElementById("tableID");
        var id = total_rows+1;
        var div = "<td><input type='hidden' name='total_rows[]' value='" + id + "'><select class='form-control select3 medicine_name' name='medicine_name_id_"+id+"'  id='medicine_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineName as $mvalue) { ?><option value='<?php echo $mvalue["id"]; ?>' data-category='<?php echo html_escape($mvalue["category_name"] ?? ""); ?>'><?php echo $mvalue["medicine_name"]; ?></option><?php } ?></select></td><td><input type='text' class='form-control medicine_category_display' readonly placeholder='-'></td><td><select name='batch_no_id_"+id+"' id='batch_no" + id + "' class='form-control batch_no select3'><option value='<?php echo set_value('batch_no'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='expire_date_"+id+"' readonly id='expire_date" + id + "' class='form-control exp_date'><input type='hidden' name='expiry_"+id+"' readonly id='expiry_" + id + "' class='form-control expiry'></td><td><div class='input-group'><input type='text' name='quantity_"+id+"' id='quantity" + id + "' class='form-control text-end qty'><span class='input-group-text text-danger available_qty' id='totalqty" + id + "'>&nbsp;&nbsp;</span></div><input type='hidden' class='available_quantity' name='available_quantity_"+id+"' id='available_quantity" + id + "'><input type='hidden' name='id[]' id='id" + id + "'></td><td><input type='hidden' name='sale_rate_price_"+id+"' id='sale_rate_price" + id + "'  class='form-control text-end sale_rate' readonly><input type='hidden' name='tpa_rate_"+id+"' id='tpa_rate" + id + "'  class='form-control text-end tpa_rate' readonly><input type='text' name='sale_price_"+id+"' id='sale_price" + id + "'  class='form-control text-end price' readonly></td><td><div class=''><div class='input-group'><input type='text' class='form-control right-border-none medicine_tax'  name='tax_1' readonly id='tax" + id + "' autocomplete='off'><span class='input-group-text'> %</span></div></div></td><td><div class=''><div class='input-group'><input type='text' class='form-control right-border-none medicine_discount'  name='mdiscount_" + id + "' id='mdiscount" + id + "'  autocomplete='off'><span class='input-group-text'> %</span></div></div></td><td><input type='text' name='amount_"+id+"' id='amount" + id + "' value='0.00' class='form-control text-end subtot' readonly></td>";
        var row =  "<tr id='row" + id + "'>" + div + "<td><button type='button' data-row-id='"+id+"' class='btn btn-sm btn-outline-danger delete_row'><i class='fa fa-times'></i></button></td></tr>";
        var $newRow = $(row).appendTo('#tableID');
        $newRow.find('.select3').select2({ dropdownParent: $('#billModal') });
        total_rows++;
});
 
  $(document).on('click','.delete_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');
            $("#row" + del_row_id).remove();
            update_amount(modal_);
        }        
  });

	$(document).on('click','.editdelete_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');    
            $("#row" + del_row_id).remove();
            update_amount(modal_);
            calculate_refund();
        }
	});

    $(document).on('keyup','.edit_refund_qty',function(e){
        var modal_=$(e.target).closest('div.modal');
        var row_id=$(this).data('rowid');
        var qty=$(this).val();
        var count_qty=0;
        var refund_qty_amount=0;
        var max_quantity=$('#valid_reund_quantity'+row_id).val();
        var refund_amount=$('#payment_refund_amount').val(); 
        var row_amount=$('#sale_price'+row_id).val();   
        update_amount(modal_);
        if(parseInt(max_quantity) < parseInt(qty)){
				errorMsg("<?php echo $this->lang->line('update_quantity_should_be_less_than_previous_quantity'); ?>");
         }else if (parseInt(qty) <= 0) {
                errorMsg("<?php echo $this->lang->line('quantity_must_be_greater_than_zero'); ?>");
         }else{
          calculate_refund();
         }
  });

    function calculate_refund(){
         let net_amount_edit=$('#net_amount').val();
         let prev_paid=$('#payment_paid_amount').val();     
         let prev_refund_amount=$('#prev_refund_amount').val();     
         let refund_amt= (parseFloat(prev_paid-prev_refund_amount) > parseFloat(net_amount_edit) ) ? (parseFloat(prev_paid-prev_refund_amount)- parseFloat(net_amount_edit)) : 0;
        $('#payment_refund_amount').val(refund_amt.toFixed(2));
    }

    function get_percentage(discountamt){
        var discount_amount=$("#discount").val();
        var discount_amount=(discount_amount != "") ? discount_amount: 0;
        var total=$('#total').val();
        var discount_percent=0;
        var net_amount=0;     
        var discount_amtt=0;
        var total_tax_amount=0;
        var grandTotal=0;
        discount_percent=((parseFloat(discount_amount)/parseFloat(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));
        var $tblrows = $(".tblProducts tbody tr");
            
        $tblrows.each(function (index) {
            var $tblrow = $(this);  
            var subTotal = $tblrow.find(".subtot").val();
            var tax_percentage = $tblrow.find(".medicine_tax").val();
            discount = isNaN((subTotal * discount_percent / 100 )) ? 0 : (subTotal * discount_percent / 100 );                   
            var tax_amount=(((subTotal-discount)*tax_percentage) / 100 );                           
            total_tax_amount += isNaN(tax_amount) ? 0 : tax_amount;      
            var stval = parseFloat(subTotal);
            grandTotal += isNaN(stval) ? 0 : stval;                                           
        });

        discount=isNaN((grandTotal * discount_percent / 100 )) ? 0 : (grandTotal * discount_percent / 100 );                 
        var net_amount=((grandTotal-discount)+total_tax_amount);

        $('.total').val(grandTotal.toFixed(2));
        $('.tax').val(total_tax_amount.toFixed(2));
        $('.net_amount').val(net_amount.toFixed(2));
        $('.payment_amount').val(net_amount.toFixed(2));
    }

    function amount_settlement(net_amount){
      var total=$('#total').val();
      var  tax=$('#tax').val();
      var total=(parseInt(total)+parseInt(tax));     
      var discount_amount=0;
      if(total>net_amount){
          discount_amount=(total-net_amount);
           $('#discount').val(discount_amount.toFixed(2));
      get_percentage(discount_amount);
      net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(tax));
        $('#net_amount').val(net_amount.toFixed(2));
        $('#payment_amount').val(net_amount.toFixed(2));
      }
    }

    $(document).ready(function (e) {
            modal_click_disabled('addPaymentModal')

 $("form#bill button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#bill").on('submit', (function (e) {
            e.preventDefault();
            let submit_btn_clicked = $("button[type=submit][clicked=true]",$(this));
            let click_btn_id = submit_btn_clicked.attr('id');       
            var form = $(this);            
            $.ajax({
                url: baseurl +'admin/pharmacy/addBill',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                  beforeSend: function() {
                  submit_btn_clicked.btnLoading();
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
                        if(click_btn_id == "billsave"){
                       
                        }else if (click_btn_id == "saveprint") {
                          _aftersave(data.insert_id);  
                        }

                        shModal('billModal').hide();
                        shModal('viewModal').hide();
                        $('.ajaxlist').DataTable().ajax.reload();
                        
                    }
                     submit_btn_clicked.btnReset();
                },
                error: function () {
                 submit_btn_clicked.btnReset();
                },
                complete: function(){
                    submit_btn_clicked.btnReset();
   }
            });
        }));
    });

    $('#billModal').on('hidden.bs.modal', function () {
      $("#addpatient_id").select2("val", "");
      $("#billno").val("");
      $("#date_pharmacy").val("");
      $("#prescription_no").val("");
      $("#case_reference_id").val("");
    });

    $('#viewModal').on('hidden.bs.modal', function () {
     $('#reportdata,#edit_deletebill').html("");
    });

    $('#addPaymentModal').on('hidden.bs.modal', function () {
    table.ajax.reload(null, false);
    });

    function viewDetail(id) {
        var view_modal=$('#viewModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id},
            dataType:"JSON",
            beforeSend: function(){
                $('#reportdata,#edit_deletebill').html("");
           shModal('viewModal').show();
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#reportdata').html(data.page);
                $('#edit_deletebill').html(data.actions);
                view_modal.removeClass('modal_loading');
            },
        });
    }
    
    $(document).on('click','.print_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.btnLoading();
            },
            complete: function(){
                $print_btn.btnReset();
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.btnReset();
            },
             error: function () {
                     $print_btn.btnReset();
                }
        });
    });

    function _aftersave(id)
    {
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
              
            },
            complete: function(){
               
            },
            success: function (data) {
                popup(data.page);                    
            },
             error: function () {                   

                }
        });
    };

    $(document).on('click','.edit_bill',function(){
        var editModal=$('#billModal');
        var record_id=$(this).data('recordId');
        var prescription_no=$(this).data('prescriptionId');

        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/editBill',
            type: "GET",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){
                $('#billModal .modal-body').html("");          
                editModal.addClass('modal_loading');
            },
            success: function (data) {

                $("#is_prescription_no").val(prescription_no);
                $("#prescription_no").val(prescription_no);
                $('#aaa #billno').val(data.paid_amount);
                $("#prescription_no").attr("readonly","readonly");
                $("#search_prescription").prop("disabled", true);
                // #txtDate10 auto-initialized as TD 6 datetime picker via .datetime class. Set value:
                SHPicker.setDate('#billModal #txtDate10', new Date(data.date));
                $('#billModal #billno').val(data.bill_no);
                if(data.organisation_id!=null && data.organisation_id!="" && data.organisation_id!=0){
                    $("#is_tpa").prop("checked",true);
                }else{
                    $("#is_tpa").prop("checked",false); 
                }
                
                $("#search_prescription").attr("disabled", "disabled");
                $('#billModal .modal-body').html(data.page);
                $('.select3').select2();
                total_rows=data.total_rows;
                $('#case_reference_id').val(data.case_reference_id);        

                $('<input>').attr({
                    type: 'hidden',
                    id: 'prev_refund_amount',
                    name: 'prev_refund_amount',
                    value:data.refund_amount
                }).appendTo('form#bill .modal-body');

                $('<input>').attr({
                    type: 'hidden',
                    id: 'payment_paid_amount',
                    name: 'payment_paid_amount',
                    value:data.paid_amount
                }).appendTo('form#bill .modal-body');

                $('#payment_refund_amount',$('#billModal')).val('0.00');  
           
                //=====================================          
                var option = new Option(data.patient_name+" ("+data.patient_id+")", data.patient_id, true, true);
                $("#bill .patient_list_ajax").append(option).trigger('change');
                
                //===================================

                $('.filestyle','#billModal').dropify();
                var medicineTable=$("#billModal .modal-body").find('table.tblProducts');
                //=============
                medicineTable.find("tbody tr").each(function() {
                console.log("test");

                var medicine_obj = $(this).find("td select.medicine_name");
                var batch_obj = $(this).find("td select.batch_no");
                var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                var post_medicine_batch_detail_id = $(this).find("td input.post_medicine_batch_detail_id").val();
                var post_medicine_sale_price = $(this).find("td input.sale_price").val();
                var post_medicine_quantity = $(this).find("td input.quantity").val();
			    var post_medicine_discount = $(this).find("td input.mdiscount").val();
                var medicine_array = {};
			    medicine_array['discount'] = post_medicine_discount;
                medicine_array['quantity'] = post_medicine_quantity;
                medicine_array['sale_price'] = post_medicine_sale_price;
                getMedicine(medicine_obj,'',post_medicine_id);
                $(this).find('.medicine_category_display').val(medicine_obj.find('option[value="'+post_medicine_id+'"]').attr('data-category') || '');
                getBatchNo(medicine_obj,post_medicine_id,post_medicine_batch_detail_id);
                getMedicineDetail(batch_obj,post_medicine_batch_detail_id,medicine_array);

            });
            shModal('billModal').show();
            editModal.removeClass('');
            },
            complete: function(){
            editModal.removeClass('modal_loading');
           }
        });
    });
    
    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });

$(document).on('select2:select', '#consultant_doctor',function (e) {   
      var reference_name = $("#consultant_doctor option:selected").text();
      $('#doctname').val(reference_name);
});
    function get_Docname(id) {
        $("#standard_charge").html("standard_charge");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#doctname').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function multiply(id) {
        var quantity = $('#quantity' + id).val();
        var availquantity = $('#available_quantity' + id).val();
        if (parseInt(quantity) > parseInt(availquantity)) {
            errorMsg('Order quantity should not be greater than available quantity');
        } else {
            
        }
        var sale_price = $('#sale_price' + id).val();
        var amount = quantity * sale_price;
        $('#amount' + id).val(amount);
    }

    function holdModal(modalId) {
       shModal(modalId).show();
       // .expire_date — month/year only picker (no day selection).
       // Format MUST be 'MMM/yyyy' (e.g. "Dec/2026"): backend Pharmacy::convertMonthToNumber()
       // parses via strtotime() which needs a month NAME, not a number. TD-6 'M' = 1–12 (numeric),
       // 'MMM' = Jan/Feb/… — do NOT change to 'M/yyyy' or saves will silently land on Jan.
       document.querySelectorAll('.expire_date').forEach(function(el) {
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
                       type: 'icons',
                       date: 'fa fa-calendar', up: 'fa fa-arrow-up', down: 'fa fa-arrow-down',
                       previous: 'fa fa-chevron-left', next: 'fa fa-chevron-right',
                       clear: 'fa fa-trash', close: 'fa fa-times'
                   }
               }
           });
       });
       generateBillNo();
    }
</script>
<script type="text/javascript">
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

            function showtextbox(value) {
                if (value != 'direct') {
                    $("#opd_ipd_no").show();
                } else {
                    $("#opd_ipd_no").hide();
                }
            }

$(".modalbtnpatient").click(function(){
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
});

 $(document).on('click','.delete-record',function(){
    var delete_id=$(this).data('recordId');
    var message = "<?php echo $this->lang->line('are_you_sure_you_want_to_delete_this'); ?>";
   if (confirm(message)) {
       $.ajax({
        url: base_url+'admin/pharmacy/deletePharmacyBill',
        type: "POST",
        data:{'id':delete_id},
        dataType: 'json',
         beforeSend: function() {
         
        },
        success: function(res) {     
       if(res.status == 1){
        window.location.reload(true);
       }else{

       }
        },
           error: function(xhr) { // if error occured
        alert("Error occured.please try again");          
    },
    complete: function() {
        
    }
    });
    } 
});  
</script>
 
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/pharmacy/getpharmacybillDatatable',[],[],100,
            [
            { "bSortable": false, "sWidth": "80px", "aTargets": [ -1 ], 'sClass': 'dt-body-right'},
            { "sWidth": "80px", "aTargets": [ -2,-3,-4,-5,-6,-7 ], 'sClass': 'dt-body-right'},
            { "sWidth": "90px", "aTargets": [ 2,3,4,5 ], 'sClass': 'dt-body-center'},
            { "sWidth": "60px", "aTargets": [ 0,1 ], 'sClass': 'dt-body-center'}
            ]
        );
        window.table.on('draw.dt', function () {
            ['nth-last-child(1)','nth-last-child(2)','nth-last-child(3)','nth-last-child(4)','nth-last-child(5)','nth-last-child(6)','nth-last-child(7)'].forEach(function(sel) {
                $('#pharmacyBillTable th:' + sel + ', #pharmacyBillTable td:' + sel).each(function () {
                    this.style.setProperty('width',     '80px', 'important');
                    this.style.setProperty('min-width', '80px', 'important');
                    this.style.setProperty('max-width', '80px', 'important');
                });
            });
        });
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    
        $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.btnLoading();
            $.ajax({
                url: form.attr('action'),
                type: "POST",             
               data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getPayments(pharmacy_bill_basic_id);
                        }
                     btn.btnReset();
                },
                error: function () {

                },
                complete: function(){
                 btn.btnReset();
   }
            });
        });  

        $(document).on('click','.delete_trans', function(e){
            if (confirm('<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>')){
            e.preventDefault();
            var record_id=$(this).data('recordId');         
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_partial_payment').val();
            var btn = $(this);       
            btn.btnLoading();
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id,'pharmacy_bill_basic_id':pharmacy_bill_basic_id},
                dataType: 'JSON',               
                success: function (data) {
                    successMsg(data.message);
                    getPayments(pharmacy_bill_basic_id);
                    btn.btnReset();
                },
                error: function () {
                    btn.btnReset();
                },
                complete: function(){
                    btn.btnReset();
                }
            });             
            }
        });          
</script>
<script>   
    
    $(".generatebill").click(function(){
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
</script>
<?php $this->load->view('admin/patient/patientaddmodal')?>


<script>

$('.patient_list_ajax').on('select2:select', function (e) {
    $(".generatebill").trigger("click");
    var addpatient_id=$("#addpatient_id").val();
    get_PatientDetails(addpatient_id);
});     

$(document).on("click", "#is_tpa", function(){
    // Toggling TPA regenerates the bill form (a blank create form) so the correct
    // price list is applied. When EDITING an existing bill, capture the edit
    // identity here first so it can be restored after the form reloads (see the
    // .generatebill success handler); otherwise the save would INSERT a new bill.
    // previous_ids[] is captured too so the bill's old medicine detail rows are
    // deleted on save instead of leaving orphan rows / double-counting stock.
    if ($('#billModal #action_type').val() === 'update') {
        window.pharmacyBillEditRestore = {
            id: $('#billModal #pharmacy_bill_basic_id').val(),
            bill_no: $('#billModal #billno').val(),
            previous_ids: $("#billModal input[name='previous_ids[]']").map(function () { return this.value; }).get()
        };
    } else {
        window.pharmacyBillEditRestore = null;
    }
    $(".generatebill").trigger("click");
    var addpatient_id=$("#addpatient_id").val();
    get_PatientDetails(addpatient_id);
});

$('#billModal').on('hidden.bs.modal', function () {
    var billModal= $('#billModal');
    $('#addpatient_id').select2("val", "");
    $('#billno,#prescription_no,#case_reference_id',billModal).val("");
    $("input:checkbox[name=is_tpa]").prop('checked',false);
    $("#organisation_id").val('');
    $("#insurance_id").val('');
    $("#insurance_validity").val('');
    $("#prescription_no").attr("readonly",false);
    $("#search_prescription").prop("disabled",false);
});

$('#billModal').on('shown.bs.modal', function () {
    var $sel = $('#addpatient_id');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({
        ajax: {
            url: base_url + 'admin/patient/getPatientListAjax',
            type: 'post',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { searchTerm: params.term }; },
            processResults: function (response) { return { results: response }; },
            cache: true
        },
        placeholder: '<?php echo $this->lang->line('select_patient'); ?>',
        dropdownParent: $('#billModal')
    });
});

 function get_PatientDetails(id) { 
    if(id!=""){
    setTimeout(function () {
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {   
                if (res) {
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);    
                } 
            },
            error: function () {
               
            },
            complete: function(){

            }
        });
    }, 1600); // Delay of 1500 milliseconds (1.5 seconds)
    }
}


$(document).on('select2:select', '.medicine_category, .medicine_name, .batch_no', function () {
    if ($(this).val() == "" || $(this).val() == "select") {
        var current_row =$(this).closest('tr');
        current_row.find('.expiry').val('');
        current_row.find('.exp_date').val('');
        current_row.find('.available_qty').text('');
        current_row.find('.medicine_tax').val('');
        current_row.find('.price').val('');
        current_row.find('.subtot').val('');
        current_row.find('.batch_no').select2("val","");
    }
});




</script>

<script type="text/javascript">
// =====================================================================
// SALE RETURN JS  (v2 - fixed)
// =====================================================================
var sr_total_rows = 1;

// Open Sale Return modal
$(document).on('click', '.sr_openbtn', function () {
    var $this = $(this);
    $this.btnLoading();
    $.ajax({
        url: '<?php echo base_url(); ?>admin/pharmacy/createSaleReturn',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            $('#sr_return_no').val(res.return_no);
            $('#sr_load').html(res.page);
            // init select2 and dropify inside modal
            $('.select3', '#saleReturnModal').select2();
            $('.filestyle', '#saleReturnModal').dropify();
            // set today's date
            var now = moment().format(datetime_format);
            $('#sr_txtDate').val(now);
            sr_total_rows = 1;
            shModal('saleReturnModal').show();
        },
        complete: function () { $this.btnReset(); }
    });
});


// Recalculate totals — mirrors original update_amount() exactly:
// Net Amount = (Σ(Rate×Qty − Item Disc) − Gross Discount) + Tax
// Tax is calculated on (subTotal − gross discount), i.e. AFTER all discounts
function sr_update_amount() {
    var discount_percent = parseFloat($('#saleReturnModal').find('.sr_discount_percent').val()) || 0;
    var grandTotal      = 0;
    var total_tax_amount = 0;

    $('#saleReturnModal').find('.srProducts tbody tr').each(function () {
        var $row         = $(this);
        var qty          = parseInt($row.find('.sr_qty').val(), 10)         || 0;
        var price        = parseFloat($row.find('.sr_price').val())         || 0;
        var item_disc    = parseFloat($row.find('.sr_medicine_discount').val()) || 0;
        var tax_pct      = parseFloat($row.find('.sr_medicine_tax').val())  || 0;

        var disc_amt     = isNaN(price * item_disc / 100) ? 0 : (price * item_disc / 100);
        var subTotal     = qty * (price - disc_amt);

        if (!isNaN(subTotal)) {
            $row.find('.sr_subtot').val(subTotal.toFixed(2));
            // gross discount portion for this row (used only for tax base)
            var row_gross_disc = isNaN(subTotal * discount_percent / 100) ? 0 : (subTotal * discount_percent / 100);
            var tax_amount     = ((subTotal - row_gross_disc) * tax_pct) / 100;
            total_tax_amount  += isNaN(tax_amount) ? 0 : tax_amount;
            grandTotal        += isNaN(subTotal)   ? 0 : subTotal;
        } else {
            $row.find('.sr_subtot').val('0.00');
        }
    });

    // final gross discount on grand total
    var discount   = isNaN(grandTotal * discount_percent / 100) ? 0 : (grandTotal * discount_percent / 100);
    var net_amount = (grandTotal - discount) + total_tax_amount;

    $('#sr_total').val(grandTotal.toFixed(2));
    $('#sr_discount').val(discount.toFixed(2));
    $('#sr_tax').val(total_tax_amount.toFixed(2));
    $('#sr_net_amount').val(net_amount.toFixed(2));
    $('#sr_payment_amount').val(net_amount.toFixed(2));
}

$(document).on('input keyup', '.sr_qty, .sr_medicine_discount, .sr_discount_percent', function () {
    sr_update_amount();
});

// Return Qty — allow only positive integers
$(document).on('keypress', '.sr_qty', function (e) {
    // Allow only digit keys (no decimal, no minus)
    if (!/\d/.test(String.fromCharCode(e.which))) return false;
});

$(document).on('input', '.sr_qty', function () {
    // Strip anything that is not a digit
    var cleaned = $(this).val().replace(/[^\d]/g, '');
    $(this).val(cleaned);
});

// Enable/disable Save button based on whether any return qty exceeds sale qty
function sr_validate_save_btn() {
    var hasError = false;
    $('#saleReturnModal').find('.srProducts tbody tr').each(function () {
        var sale_qty = parseInt($(this).find('.sr_sale_qty').val(), 10) || 0;
        var ret_qty  = parseInt($(this).find('.sr_qty').val(), 10) || 0;
        if (ret_qty < 0 || (sale_qty > 0 && ret_qty > sale_qty)) {
            hasError = true;
            return false; // break loop
        }
    });
    $('#sr_save').prop('disabled', hasError);
}

// Disable Save in real-time as user types
$(document).on('input', '.sr_qty', function () {
    sr_validate_save_btn();
});

// Return Qty must not exceed Sale Qty and must not be negative
$(document).on('change blur', '.sr_qty', function () {
    var $row     = $(this).closest('tr');
    var sale_qty = parseInt($row.find('.sr_sale_qty').val(), 10) || 0;
    var ret_qty  = parseInt($(this).val(), 10) || 0;

    if (ret_qty < 0) {
        $(this).val(0);
        sr_update_amount();
        errorMsg('Return Qty cannot be negative');
        sr_validate_save_btn();
        return;
    }
    if (sale_qty > 0 && ret_qty > sale_qty) {
        errorMsg('Return Qty cannot be greater than Sale Qty (' + sale_qty + ')');
    }
    sr_update_amount();
    sr_validate_save_btn();
});

// Show/hide cheque fields when payment mode changes (Sale Return modal only)
$('#saleReturnModal').on('change', '#sr_payment_mode', function () {
    if ($(this).val() === 'Cheque') {
        $('.sr_cheque_div', '#saleReturnModal').removeClass('d-none');
    } else {
        $('.sr_cheque_div', '#saleReturnModal').addClass('d-none');
    }
});

// ---- Medicine Name open → load all Medicines if empty ----
$(document).on('select2:open', '.sr_medicine_name', function () {
    var $select = $(this);
    if ($select.find('option[value!=""]').length === 0) {
        $select.html('<option value=""><?php echo $this->lang->line('loading'); ?></option>');
        $.ajax({
            url: base_url + 'admin/pharmacy/get_medicine_name',
            type: 'POST',
            data: { medicine_category_id: '' },
            dataType: 'json',
            async: false,
            success: function (res) {
                var opts = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                $.each(res, function (i, v) {
                    opts += '<option value="' + v.id + '" data-category="' + (v.category_name || '') + '">' + v.medicine_name + '</option>';
                });
                $select.html(opts).select2();
            }
        });
    }
});

// ---- Medicine Name → fill Category display + load Batch Numbers ----
$(document).on('select2:select', '.sr_medicine_name', function () {
    var $tr      = $(this).closest('tr');
    var category = $(this).find('option:selected').attr('data-category') || '';
    $tr.find('.sr_medicine_category_display').val(category);
    var med_id   = $(this).val();
    var bat_sel  = $tr.find('.sr_batch_no');

    bat_sel.html('<option value=""><?php echo $this->lang->line('loading'); ?></option>');
    $tr.find('.sr_price').val('');
    $tr.find('.sr_exp_date').val('');

    if (!med_id) return;
    $.ajax({
        url: base_url + 'admin/pharmacy/getBatchNoList',
        type: 'POST',
        data: { pharmacy_id: med_id, batch_id: 0 },
        dataType: 'json',
        success: function (res) {
            var opts = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.each(res, function (i, v) {
                opts += '<option value="' + v.id + '">' + v.batch_no + '</option>';
            });
            bat_sel.html(opts).select2();
        }
    });
});

// ---- Batch No → fill Expiry + Sale Price + Tax ----
$(document).on('select2:select', '.sr_batch_no', function () {
    var $tr      = $(this).closest('tr');
    var batch_id = $(this).val();
    if (!batch_id) return;
    $.ajax({
        url: base_url + 'admin/pharmacy/getExpiryDate',
        type: 'POST',
        data: { medicine_batch_detail_id: batch_id, patient_id: $('#sr_patient_id').val(), is_tpa: 0 },
        dataType: 'json',
        success: function (res) {
            if (res.status == 0) {
                errorMsg(res.msg);
                return;
            }
            $tr.find('.sr_exp_date').val(res.result.expiry_date);
            $tr.find('.sr_price').val(res.result.sale_rate);
            $tr.find('.sr_medicine_tax').val(res.result.tax);
            sr_update_amount();
        }
    });
});

// ---- Bill Number Search → auto-fill all medicine rows ----
function sr_loadBill() {
    var bill_no = $.trim($('#sr_bill_search').val()).replace(/^[a-zA-Z]+/, '');
    if (!bill_no) return;
    $('#saleReturnModal').addClass('modal_loading');
    $.ajax({
        url: base_url + 'admin/pharmacy/getSaleBillForReturn',
        type: 'POST',
        data: { bill_no: bill_no },
        dataType: 'json',
        success: function (res) {
            if (res.status == 0) {
                errorMsg(res.msg);
                return;
            }
            // Set patient
            var bill = res.bill;
            if (bill.patient_id) {
                $('#sr_patient_id_val').val(bill.patient_id);
                $('#sr_patient_name').val(bill.customer_name);
                var opt = new Option(bill.patient_name + ' (' + bill.patient_id + ')', bill.patient_id, true, true);
                $('#sr_patient_id').empty().append(opt).trigger('change');
            }
            // Store original bill id + prefill discount from original bill
            $('#sr_pharmacy_bill_basic_id').val(bill.id);
            $('#sr_discount_percent').val(bill.discount_percentage > 0 ? bill.discount_percentage : '');

            // Clear existing rows, rebuild from bill items
            $('#saleReturnModal').find('.srProducts tbody').empty();
            sr_total_rows = 0;

            $.each(res.items, function (i, item) {
                sr_total_rows++;
                var rid = sr_total_rows;
                var row = '<tr id="sr_row' + rid + '">' +
                    '<td>' +
                        '<input type="hidden" name="sr_total_rows[]" value="' + rid + '">' +
                        '<select class="form-control select3 sr_medicine_name" id="sr_medicine_name' + rid + '" name="sr_medicine_name_id_' + rid + '">' +
                            '<option value="' + item.medicine_id + '">' + item.medicine_name + '</option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="text" class="form-control sr_medicine_category_display" readonly value="' + (item.medicine_category || '') + '" placeholder="-"></td>' +
                    '<td>' +
                        '<select class="form-control sr_batch_no select3" id="sr_batch_no' + rid + '" name="sr_batch_no_id_' + rid + '">' +
                            '<option value="' + item.medicine_batch_detail_id + '">' + item.batch_no + '</option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="text" readonly name="sr_expire_date_' + rid + '" id="sr_expire_date' + rid + '" class="form-control form-control-sm sr_exp_date sr-readonly-input" value="' + (item.expiry ? moment(item.expiry).format('MMM/YYYY') : '') + '"/></td>' +
                    '<td><input type="text" name="sr_sale_qty_' + rid + '" class="form-control form-control-sm text-end sr_sale_qty sr-readonly-input" value="' + item.quantity + '" readonly></td>' +
                    '<td><input type="text" name="sr_quantity_' + rid + '" id="sr_quantity' + rid + '" class="form-control form-control-sm text-end sr_qty sr-qty-input" value="0"></td>' +
                    '<td><input type="text" name="sr_sale_price_' + rid + '" id="sr_sale_price' + rid + '" class="form-control form-control-sm text-end sr_price sr-readonly-input" value="' + item.sale_price + '" readonly></td>' +
                    '<td><div class="input-group input-group-sm"><input type="text" class="form-control right-border-none sr_medicine_tax sr-readonly-input" name="sr_tax_' + rid + '" id="sr_tax' + rid + '" value="' + (item.tax || 0) + '" readonly><span class="input-group-text">%</span></div></td>' +
                    '<td><div class="input-group input-group-sm"><input type="text" class="form-control right-border-none sr_medicine_discount" name="sr_mdiscount_' + rid + '" id="sr_mdiscount' + rid + '" value="' + (item.discount || 0) + '"><span class="input-group-text">%</span></div></td>' +
                    '<td><input type="text" name="sr_amount_' + rid + '" id="sr_amount' + rid + '" class="form-control form-control-sm text-end sr_subtot sr-readonly-input" value="' + item.amount + '" readonly></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger sr_delete_row" data-row-id="' + rid + '" data-bs-toggle="tooltip" title="<?= $this->lang->line('remove') ?>"><i class="fa fa-times"></i></button></td>' +
                    '</tr>';
                $('#saleReturnModal').find('.srProducts tbody').append(row);
            });

            $('.select3', '#saleReturnModal').select2();
            sr_update_amount();
            sr_validate_save_btn();

            // Render previous returns section (1 row per return + hidden detail row)
            var prevReturns = res.previous_returns || [];
            if (prevReturns.length > 0) {
                var prevRows = '';
                $.each(prevReturns, function (i, r) {
                    // Build medicine detail rows
                    var detailRows = '';
                    $.each(r.items || [], function (j, item) {
                        detailRows +=
                            '<tr class="sr-prev-item-row">' +
                            '<td class="sr-prev-td">' +
                                '<i class="fa fa-circle sr-prev-dot"></i>' +
                                '<strong>' + (item.medicine_name || '—') + '</strong>' +
                            '</td>' +
                            '<td class="sr-prev-td sr-prev-cell-muted">' + (item.batch_no || '—') + '</td>' +
                            '<td class="text-end sr-prev-td">' +
                                '<span class="sr-prev-badge-orange">' + (item.quantity || 0) + '</span>' +
                            '</td>' +
                            '<td class="text-end sr-prev-td">' + parseFloat(item.sale_price || 0).toFixed(2) + '</td>' +
                            '<td class="text-end sr-prev-td sr-prev-cell-dim">' + (item.discount || 0) + '%</td>' +
                            '<td class="text-end sr-prev-td fw-semibold">' + parseFloat(item.amount || 0).toFixed(2) + '</td>' +
                            '</tr>';
                    });

                    // Parent summary row
                    prevRows +=
                        '<tr class="sr-prev-parent" onclick="$(this).find(\'.sr_prev_eye\').trigger(\'click\')">' +
                        '<td class="sr-prev-td text-nowrap">' +
                            '<span class="sr-prev-badge-orange sr-prev-rno">' +
                                '&#8629; #' + r.return_no +
                            '</span>' +
                        '</td>' +
                        '<td class="sr-prev-td sr-prev-cell-muted text-nowrap small">' + r.date + '</td>' +
                        '<td class="text-end sr-prev-td sr-prev-cell-muted">' + parseFloat(r.total).toFixed(2) + '</td>' +
                        '<td class="text-end sr-prev-td sr-prev-cell-dim">' +
                            parseFloat(r.discount).toFixed(2) +
                            (r.discount_percentage > 0 ? ' <small class="sr-prev-cell-dim">(' + r.discount_percentage + '%)</small>' : '') +
                        '</td>' +
                        '<td class="text-end sr-prev-td sr-prev-cell-dim">' + parseFloat(r.tax).toFixed(2) + '</td>' +
                        '<td class="text-end sr-prev-td">' +
                            '<span class="sr-prev-badge-green">' +
                                parseFloat(r.net_amount).toFixed(2) +
                            '</span>' +
                        '</td>' +
                        '<td class="text-center sr-prev-td">' +
                            '<a href="#" class="sr_prev_eye sr-prev-eye-btn" data-bs-target="sr-prev-detail-' + i + '" ' +
                               'title="<?= $this->lang->line('toggle_medicine_details') ?>">' +
                                '<i class="fa fa-chevron-down"></i>' +
                            '</a>' +
                        '</td>' +
                        '</tr>' +
                        // Hidden detail accordion row
                        '<tr class="sr-prev-detail-row" id="sr-prev-detail-' + i + '" style="display:none;">' +
                        '<td colspan="7" class="sr-prev-detail-td">' +
                            '<div class="sr-prev-detail-wrap">' +
                                '<table class="table mb-0 sr-prev-detail-table">' +
                                    '<thead>' +
                                        '<tr class="sr-prev-detail-thead">' +
                                            '<th class="sr-prev-detail-th"><?= $this->lang->line('medicine') ?></th>' +
                                            '<th class="sr-prev-detail-th"><?= $this->lang->line('batch_no') ?></th>' +
                                            '<th class="text-end sr-prev-detail-th"><?= $this->lang->line('return_qty') ?></th>' +
                                            '<th class="text-end sr-prev-detail-th"><?= $this->lang->line('sale_price') ?></th>' +
                                            '<th class="text-end sr-prev-detail-th">Disc %</th>' +
                                            '<th class="text-end sr-prev-detail-th"><?= $this->lang->line('amount') ?></th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>' +
                                        (detailRows || '<tr><td colspan="6" class="text-center sr-prev-cell-dim py-2">No items found</td></tr>') +
                                    '</tbody>' +
                                '</table>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                });

                $('#sr_prev_returns_body').html(prevRows);
                $('#sr_prev_bill_no_label').text('Bill #' + (bill.bill_no || bill.id));
                $('#sr_prev_count_badge').text(prevReturns.length + ' return' + (prevReturns.length > 1 ? 's' : ''));
                $('#sr_prev_returns_section').show();
            } else {
                $('#sr_prev_returns_body').html('');
                $('#sr_prev_returns_section').hide();
            }

            // Lock medicine table fields — only Return Qty remains editable
            $('#saleReturnModal').find('.srProducts .sr_medicine_category').prop('disabled', true).trigger('change.select2');
            $('#saleReturnModal').find('.srProducts .sr_medicine_name').prop('disabled', true).trigger('change.select2');
            $('#saleReturnModal').find('.srProducts .sr_batch_no').prop('disabled', true).trigger('change.select2');
            $('#saleReturnModal').find('.srProducts .sr_medicine_discount').prop('readonly', true);

            // Populate doctor fields from bill and lock them
            $('#sr_doctor_name', '#saleReturnModal').val(bill.doctor_name || '').prop('readonly', true);
            $('#sr_consultant_doctor', '#saleReturnModal').prop('disabled', true).trigger('change');
        },
        complete: function () { $('#saleReturnModal').removeClass('modal_loading'); }
    });
}

$(document).on('click', '#sr_search_bill', function () { sr_loadBill(); });
$(document).on('keydown', '#sr_bill_search', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); sr_loadBill(); }
});

// Change 3: Reset modal completely on close
$('#saleReturnModal').on('hidden.bs.modal', function () {
    $('#srForm')[0].reset();
    $('#sr_load').html('');
    $('#sr_bill_search').val('');
    $('#sr_return_no').val('');
    $('#sr_txtDate').val('');
    $('#sr_patient_id_val').val('');
    $('#sr_patient_id').empty();
    sr_total_rows = 1;
    // Restore doctor fields to editable state
    $('#sr_doctor_name', '#saleReturnModal').prop('readonly', false);
    $('#sr_consultant_doctor', '#saleReturnModal').prop('disabled', false);
    // Hide previous returns section
    $('#sr_prev_returns_body').html('');
    $('#sr_prev_bill_no_label').text('');
    $('#sr_prev_returns_section').hide();
    // Reset save button to disabled — re-enabled after bill loads
    $('#sr_save').prop('disabled', true);
});

$('#saleReturnModal').on('shown.bs.modal', function () {
    var $sel = $('#sr_patient_id');
    if ($sel.hasClass('select2-hidden-accessible')) {
        $sel.select2('destroy');
    }
    $sel.select2({
        ajax: {
            url: base_url + 'admin/patient/getPatientListAjax',
            type: 'post',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { searchTerm: params.term }; },
            processResults: function (response) { return { results: response }; },
            cache: true
        },
        placeholder: '<?php echo $this->lang->line('select_patient'); ?>',
        dropdownParent: $('#saleReturnModal')
    });
});

// Add new medicine row
$(document).on('click', '.sr_add_row', function () {
    sr_total_rows++;
    var row_id = sr_total_rows;
    var new_row = '<tr id="sr_row' + row_id + '">' +
        '<td><input type="hidden" name="sr_total_rows[]" value="' + row_id + '">' +
        '<select class="form-control select3 sr_medicine_name" id="sr_medicine_name' + row_id + '" name="sr_medicine_name_id_' + row_id + '"><option value=""><?php echo $this->lang->line('select'); ?></option></select></td>' +
        '<td><input type="text" class="form-control sr_medicine_category_display" readonly placeholder="-"></td>' +
        '<td><select class="form-control sr_batch_no select3" id="sr_batch_no' + row_id + '" name="sr_batch_no_id_' + row_id + '"><option value=""><?php echo $this->lang->line('select'); ?></option></select></td>' +
        '<td><input type="text" readonly name="sr_expire_date_' + row_id + '" id="sr_expire_date' + row_id + '" class="form-control form-control-sm sr_exp_date sr-readonly-input"/></td>' +
        '<td><input type="text" name="sr_sale_qty_' + row_id + '" class="form-control form-control-sm text-end sr_sale_qty sr-readonly-input" readonly placeholder="—"></td>' +
        '<td><input type="text" name="sr_quantity_' + row_id + '" id="sr_quantity' + row_id + '" class="form-control form-control-sm text-end sr_qty sr-qty-input"></td>' +
        '<td><input type="text" name="sr_sale_price_' + row_id + '" id="sr_sale_price' + row_id + '" class="form-control form-control-sm text-end sr_price sr-readonly-input" readonly></td>' +
        '<td><div class="input-group input-group-sm"><input type="text" class="form-control right-border-none sr_medicine_tax sr-readonly-input" name="sr_tax_' + row_id + '" id="sr_tax' + row_id + '" value="0" readonly><span class="input-group-text">%</span></div></td>' +
        '<td><div class="input-group input-group-sm"><input type="text" class="form-control right-border-none sr_medicine_discount" name="sr_mdiscount_' + row_id + '" id="sr_mdiscount' + row_id + '" value="0"><span class="input-group-text">%</span></div></td>' +
        '<td><input type="text" name="sr_amount_' + row_id + '" id="sr_amount' + row_id + '" class="form-control form-control-sm text-end sr_subtot sr-readonly-input" value="0.00" readonly></td>' +
        '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger sr_delete_row" data-row-id="' + row_id + '" data-bs-toggle="tooltip" title="<?= $this->lang->line('remove') ?>"><i class="fa fa-times"></i></button></td>' +
        '</tr>';
    $('#saleReturnModal').find('.srProducts tbody').append(new_row);
    $('.select3', '#saleReturnModal').select2();
});

// Delete medicine row
$(document).on('click', '.sr_delete_row', function () {
    var row_id = $(this).data('row-id');
    if ($('#saleReturnModal').find('.srProducts tbody tr').length > 1) {
        $('#sr_row' + row_id).remove();
        sr_update_amount();
    }
});

// Save Sale Return form
$('#srForm').on('submit', function (e) {
    e.preventDefault();

    // Hard guard: block save if any return qty exceeds sale qty
    var hasQtyError = false;
    $('#saleReturnModal').find('.srProducts tbody tr').each(function () {
        var sale_qty = parseInt($(this).find('.sr_sale_qty').val(), 10) || 0;
        var ret_qty  = parseInt($(this).find('.sr_qty').val(), 10) || 0;
        if (ret_qty < 0 || (sale_qty > 0 && ret_qty > sale_qty)) {
            hasQtyError = true;
            return false;
        }
    });
    if (hasQtyError) {
        errorMsg('Return Qty cannot be greater than Sale Qty');
        return;
    }

    var $btn = $('#sr_save');
    $btn.btnLoading();

    // Temporarily enable disabled fields so FormData captures their values
    // (disabled inputs are excluded from FormData by the browser)
    var $locked = $('#saleReturnModal').find(':disabled');
    $locked.prop('disabled', false);
    var formData = new FormData(document.getElementById('srForm'));
    $locked.prop('disabled', true);

    $.ajax({
        url: '<?php echo base_url(); ?>admin/pharmacy/addSaleReturn',
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
            if (res.status === 'success') {
                successMsg(res.message);
                shModal('saleReturnModal').hide();
                $('.ajaxlist').DataTable().ajax.reload(null, false);
            } else {
                if (res.error && Object.keys(res.error).length > 0) {
                    var msg = '';
                    $.each(res.error, function (k, v) { msg += v + '<br>'; });
                    errorMsg(msg);
                } else {
                    errorMsg(res.message);
                }
            }
        },
        complete: function () { $btn.btnReset(); }
    });
});

// Chevron click — toggle accordion detail row
$(document).on('click', '.sr_prev_eye', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var targetId   = $(this).data('bs-target');
    var $icon      = $(this).find('i');
    var $detailRow = $('#' + targetId);
    var $wrap      = $detailRow.find('.sr-prev-detail-wrap');
    var isOpen     = $detailRow.is(':visible');

    // Close all other open rows
    $('.sr-prev-detail-row').not($detailRow).each(function () {
        $(this).find('.sr-prev-detail-wrap').slideUp(150, function () {
            $(this).closest('.sr-prev-detail-row').hide();
        });
    });
    $('.sr_prev_eye').not(this).removeClass('is-open')
        .find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

    if (isOpen) {
        $wrap.slideUp(200, function () { $detailRow.hide(); });
        $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        $(this).removeClass('is-open');
    } else {
        $detailRow.show();
        $wrap.hide().slideDown(250);
        $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        $(this).addClass('is-open');
    }
});
</script>

<!-- ========== SALE RETURN HISTORY MODAL ========== -->
<div class="modal fade sh-modal sh-modal-accent" id="saleReturnHistoryModal" tabindex="-1" aria-labelledby="saleReturnHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleReturnHistoryLabel"><?php echo $this->lang->line('return_history'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div id="saleReturnHistoryContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ========== /SALE RETURN HISTORY MODAL ========== -->

<script type="text/javascript">
    function saleReturnHistory(id) {
        $('#saleReturnHistoryContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i></div>');
        shModal('saleReturnHistoryModal').show();
        $.ajax({
            url: base_url + 'admin/pharmacy/getSaleReturnHistoryForBill/' + id,
            type: 'GET',
            success: function (data) {
                $('#saleReturnHistoryContent').html(data);
            },
            error: function () {
                $('#saleReturnHistoryContent').html('<p class="text-danger text-center">Error loading return history.</p>');
            }
        });
    }
</script>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ $('.generatebill').trigger('click'); shCleanUrlParam('action'); });</script>
<?php endif; ?>
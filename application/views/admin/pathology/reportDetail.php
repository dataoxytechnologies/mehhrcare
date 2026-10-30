<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row"> 
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('pathology_bill'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_add')) { ?>
                                <button type="button" class="btn btn-primary btn-sm assigntest" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('generate_bill'); ?></button>
                            <?php } ?> 
                            <?php if ($this->rbac->hasPrivilege('pathology_test', 'can_view')) { ?>   
                                <a data-bs-toggle=""href="<?php echo base_url(); ?>admin/pathology/search"  class="btn btn-primary btn-sm pathology"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('pathology_test'); ?></a> 
                            <?php } ?>                           
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover ajaxlist" id="testreport" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('pathology_bill'); ?>">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <!-- <th class="white-space-nowrap"><?php //echo $this->lang->line('reporting_date'); ?></th>  -->
                                <th><?php echo $this->lang->line('bill_date'); ?></th>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <th><?php echo $this->lang->line('generated_by'); ?></th>
                                <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                                <?php
                                    if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                            <th><?php echo $fields_value->name; ?></th>
                                            <?php
                                        }
                                    }
                                ?>
                                <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-end"><?php echo $this->lang->line('tax'). ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-end"><?php echo $this->lang->line('paid_amount'). ' (' . $currency_symbol . ')'; ?></th>
                                <th class="noExport text-end"><?php echo $this->lang->line('balance_amount'). ' (' . $currency_symbol . ')'; ?></th>
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

<div class="modal fade sh-modal sh-modal-nospace" id="assigntestModal" tabindex="-1" aria-labelledby="assigntestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bill" accept-charset="utf-8" method="post">
                <input type="hidden" name="doctor_id" id="doctorid">
                <div class="modal-header">
                    <h5 class="modal-title" id="assigntestModalLabel"><?php echo $this->lang->line('pathology_bill'); ?></h5>
                    <select class="form-control patient_list_ajax sh-bill-patient-pick" id="addpatient_id" name="patientid" onchange="get_PatientDetails(this.value)"></select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                    <a id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                    <div class="input-group sh-bill-rx-search">
                        <input type="hidden" name="is_prescription_no_exist" id="is_prescription_no_exist">
                        <input type="text" class="form-control" id="prescription_no" placeholder="<?php echo $this->lang->line('prescription_no'); ?>" name="prescription_no">
                        <button class="btn btn-secondary" type="button" id="search_prescription"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input class="form-check-input apply_tpa mt-0" type="checkbox" value="1" id="is_tpa" name="is_tpa">
                        <label class="form-check-label text-white mb-0" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="tabinsetbottom sh-modal-info-strip pt5">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4">
                                    <label class="d-flex align-items-center gap-1 mb-0 text-nowrap"><?php echo $this->lang->line('bill_no'); ?> <input readonly name="bill_no" class="transparentbg-border atm-readonly-input" id="billno" type="text"></label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-4">
                                    <label><?php echo $this->lang->line('case_id'); ?> <input readonly name="case_reference_id" id="case_reference_id" type="text" class="transparentbg-border atm-readonly-input"></label>
                                </div>
                                <div class="col-lg-7 col-md-5 col-sm-4 text-end">
                                    <label><?php echo $this->lang->line('date'); ?> <input name="date" id="txtDate10" type="text" value="<?php echo date($this->customlib->getHospitalDateFormat(true, true)); ?>" class="transparentbg-border atm-readonly-input datetime"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body sh-modal-canvas pb5"></div>
                </div>
                <div class="modal-footer">
                    <p id="demo"></p>
                    <input type="hidden" id="pathology_billing_id" name="pathology_billing_id" value="0">
                    <input type="hidden" id="organisation_id" name="organisation_id">
                    <input type="hidden" id="insurance_id" name="insurance_id">
                    <input type="hidden" id="insurance_validity" name="insurance_validity">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('report_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_delete' class="d-flex align-items-center gap-2">
                        <a href="#" data-bs-target="#edit_prescription" data-bs-toggle="modal" class="btn btn-sm btn-light" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="reportdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModalReport" tabindex="-1" aria-labelledby="viewModalReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalReportLabel"><?php echo $this->lang->line('report_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_deletereport' class="d-flex align-items-center gap-2">
                        <a href="#" data-bs-target="#edit_prescription" data-bs-toggle="modal" class="btn btn-sm btn-light" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="reportdatareport"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='action_detail_report_modal' class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportbilldata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="collectionModal" tabindex="-1" aria-labelledby="collectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/pathology/updatecollection'); ?>" method="POST" id="form-sample-collected">
            <div class="modal-header">
                <h5 class="modal-title" id="collectionModalLabel"><?php echo $this->lang->line('sample_collection'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <input type="hidden" name="pathology_report_id" value="0">
                    <input type="hidden" name="pathology_bill_id" value="0">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-eyedropper"></i><?php echo $this->lang->line('sample_collection'); ?></span>
                        </div>
                        <div class="px-3 py-3">
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('sample_collected_person_name'); ?></label><small class="req"> *</small>
                                <select class="form-control" name="collected_by" id="collected_by">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($pathologist as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo $this->lang->line('collected_date'); ?></label><small class="req"> *</small>
                                <input type="text" class="form-control date" name="collected_date" id="collected_date" autocomplete="off">
                            </div>
                            <div class="mb-0">
                                <label class="form-label"><?php echo $this->lang->line('pathology_center'); ?></label><small class="req"> *</small>
                                <input type="text" class="form-control" name="pathology_center" id="pathology_center">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <?php if ($this->rbac->hasPrivilege('pathology_add_edit_collection_person', 'can_edit')) { ?>
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                <?php } ?>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addReportModal" tabindex="-1" aria-labelledby="addReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/pathology/updatereport'); ?>" method="POST" id="form-report_param">
            <div class="modal-header">
                <h5 class="modal-title" id="addReportModalLabel"><?php echo $this->lang->line('add_edit_report'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <?php if ($this->rbac->hasPrivilege('pathology_add_edit_report', 'can_edit')) { ?>
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                <?php } ?>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModalbill" tabindex="-1" aria-labelledby="viewModalbillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalbillLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <div id='edit_deletebill' class="d-flex align-items-center gap-2"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script id="testpatho-template" type="text/template">
   <?php
foreach ($testlist as $dkey => $testlist_value) {
    ?>
    <option value='<?php echo $testlist_value["id"]; ?>'>
        <?php echo $testlist_value["test_name"]." (".$testlist_value["short_name"].")"  ?>
    </option>
    <?php
     }
   ?>
</script>

<script type="text/javascript">
    var total_rows=1;
    var patientParentModal = 'assigntestModal';
    var date_format_new = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';
    $(document).ready(function(){
 
           /* input[name="..."] datepicker init removed - add .date class to HTML for auto-init */
    });
        
    $(function () {
        
        $('.select2').select2()
    });
    $('#assigntestModal').on('shown.bs.modal', function () {
        var $sel = $('.patient_list_ajax');
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.select2({
            ajax: {
                url: '<?php echo base_url(); ?>admin/patient/getPatientListAjax',
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            },
            placeholder: '<?php echo $this->lang->line('select_patient'); ?>',
            dropdownParent: $('#assigntestModal')
        });
    });

    $('.patient_list_ajax').on('select2:select', function (e) {
            var createModal=$('#assigntestModal');
            var $this  =null;
        getInsertModalForm(createModal,$this);
      });

            function holdModal(modalId) {
                (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
            }

            $(document).on('click','.assigntest',function(){
            var createModal=$('#assigntestModal');
            var $this = $(this);
            getInsertModalForm(createModal,$this);
        }); 

        let getInsertModalForm=(createModal,__this_obj=null)=>{
			
			$('#pathology_billing_id').val('');
			
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/assigntestpatho',
                type: "POST",
                dataType: 'json',
                 beforeSend: function() {
                  (__this_obj === null)? "": __this_obj.btnLoading();
                     createModal.addClass('modal_loading');
                },
                success: function(res) {   
                  total_rows=res.total_rows; 
                  $('#assigntestModal #billno').val(res.bill_no);
                  $('#assigntestModal .modal-body').html(res.page);
                   $('.filestyle','#assigntestModal').dropify();
                    updateDate();
                  $(".test_name").select2();
                  $(".consultant_doctor").select2();
               shModal('assigntestModal').show();
                  createModal.removeClass('modal_loading');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   (__this_obj === null)? "": __this_obj.btnReset();
                   createModal.removeClass('modal_loading');
            },
            complete: function() {
                (__this_obj === null)? "": __this_obj.btnReset();
                 createModal.removeClass('modal_loading');
            }
            });
        }            

            $(document).on('click','.delete_pathology',function(){    
             if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {       
            var $this = $(this);
            var recordId = $this.data('recordId');
            $this.btnLoading();
            $.ajax({
                url: base_url+'admin/pathology/deletebill',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.btnLoading();
                    
                },
                success: function(res) {   
                    if (res.status == "fail") {
                        
                        errorMsg(res.message);
                    } else {
                        successMsg(res.message);
                        shModal('viewDetailReportModal').hide();
                         table.ajax.reload();
                    }

                  $this.btnReset();
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   $this.btnReset();
                    createModal.removeClass('modal_loading');
            },
            complete: function() {
                  $this.btnReset();            
            }
            });
             }
        });

        $(document).on('click','.edit_pathology',function(){
			
		var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';
		 
        /* datetimepicker init removed - auto-init via .datetime class */	
		
            var createModal=$('#assigntestModal');
            var $this = $(this);
            var recordId=$this.data('recordId');
            $this.btnLoading();
            $.ajax({
                url: base_url+'admin/pathology/editpathology',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.btnLoading();
                      createModal.addClass('modal_loading');
                },
                success: function(res) { 
                   
                    total_rows=res.total_rows; 
                    $('#assigntestModal #billno').val(res.bill_prefix+res.bill_no); 
                    $('#case_reference_id').val(res.case_reference_id);
                    $('#assigntestModal .modal-body').html(res.page);
                    $('.filestyle','#assigntestModal').dropify();                    
                    SHPicker.setDate('#txtDate10', new Date(res.pathology_date));
                    updateDate();
                    $('#assigntestModal .modal-body').find('.test_name').select2();
                    shModal('viewDetailReportModal').hide();
                    shModal('assigntestModal').show();                   
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#bill .patient_list_ajax").append(option).trigger('change'); 				  
					 
                    if(res.tpa_apply_status==1){
                        $("#is_tpa").prop('checked',true) ;
                    }else if(res.tpa_apply_status==0){
                        $("#is_tpa").prop('checked',false) ;  
                    }					
					 
					$("#prescription_no").attr("disabled", "disabled");
					$("#search_prescription").attr("disabled", "disabled");
                    $('#assigntestModal #is_prescription_no_exist').val(res.ipd_prescription_basic_id); 
                    $('#assigntestModal #prescription_no').val(res.ipd_prescription_basic_id); 
                    $("#pathology_billing_id").val(res.bill_no);                  
                    createModal.removeClass('modal_loading');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   $this.btnReset();
                    createModal.removeClass('modal_loading');
            },
            complete: function() {
                  $this.btnReset();
                     createModal.removeClass('modal_loading');
            }
            });
        });

        $('#load1').click(function(){
           $("#is_prescription_no_exist").val("");
           $("#prescription_no").removeAttr("readonly");
        });

        function gettestpathodetails(batch_obj,testid) {
           
            var current_row =batch_obj.closest('tr');
            let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
      
        var patient_id= $('.patient_list_ajax').val();
            $.ajax({
                type: "POST",
                url: base_url + "admin/pathology/gettestpathodetails",
                data: {'id': testid ,'patient_id':patient_id,'is_tpa':is_tpa},
                dataType: 'json',
                beforeSend: function() {
                        current_row.find('.taxpercent').val("");
                        current_row.find('.report_date').val("");
                        current_row.find('.days').val("");
                        current_row.find('.amount').val("");
                        current_row.find('.taxamount').val("");
                        update_amount(batch_obj.closest('div.modal'));
                },
                success: function (res) {
                    if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
                        current_row.find('.taxpercent').val(res.result.tax);
                        current_row.find('.days').val(res.result.report_days);
                        current_row.find('.amount').val(res.result.standard_charge);
                        var stnd_amt =  res.result.standard_charge;
                        var tax_per = res.result.tax;
                        var tax_amount = (stnd_amt*tax_per)/100;
                        current_row.find('.taxamount').val(tax_amount);
                        var day = res.result.report_days;
                        getdate(day,current_row.find('.report_date'));
                        update_amount(batch_obj.closest('div.modal'));
                        if(res.status == 2){
                            errorMsg(res.msg);
                        }
                    }
                }
            });
        }

    function getdate(day, datepicker_obj) {
        var report_day = parseInt(day, 10);
        var selected_date = SHPicker.getDate("#txtDate10");
        var newdate = new Date(selected_date);
        newdate.setDate(newdate.getDate() + report_day);
        if (!datepicker_obj.hasClass("hasDatepicker")) {
            /* datepicker_obj.datepicker() init removed - .date class auto-init */
        }
        datepicker_obj.each(function() { SHPicker.setDate(this, newdate); });
    }



        // function getdate(day,datepicker_obj) {
        //     var report_day =  parseInt(day, 10);
        //     var selected_date=SHPicker.getDate("#txtDate10");
        //     var newdate = new Date(selected_date);
        //     newdate.setDate(newdate.getDate() + report_day);
        //     datepicker_obj.each(function() { SHPicker.setDate(this, newdate); });           
        // }		
		
        $(document).ready(function(){
            modal_click_disabled('assigntestModal');
              /* datetimepicker init removed - auto-init via .datetime class */
       });
        
    $(document).on('click','.add-record',function(){
        var table = document.getElementById("tableID");
        
        total_rows=total_rows+1;
        var template=$("#testpatho-template").html();
        var div = "<td><input type='hidden' name='total_rows[]' value='" + total_rows + "'><input type='hidden' name='inserted_id_" + total_rows + "' value='0'><select class='form-control form-control-sm test_name select2' name='test_name_" + total_rows + "'><option value='<?php echo set_value('test_name_id'); ?>'><?php echo $this->lang->line('select') ?></option>" + template + "</select></td><td><input type='text' name='reportday_" + total_rows + "' id='reportday_" + total_rows + "' class='form-control form-control-sm text-end days' readonly></td><td><input type='text' name='reportdate_" + total_rows + "' id='reportdate_" + total_rows + "' class='form-control form-control-sm text-end report_date'></td><td><div class='input-group input-group-sm'><input type='text' name='taxpercent_" + total_rows + "' id='taxpercent_" + total_rows + "' class='form-control form-control-sm text-end taxpercent' autocomplete='off' readonly><span class='input-group-text'>%</span></div><input type='hidden' name='taxamount_" + total_rows + "' id='taxamount_" + total_rows + "' class='taxamount'></td><td class='text-end'><input type='text' name='amount_" + total_rows + "' id='amount_" + total_rows + "' class='form-control form-control-sm text-end amount' readonly></td>";
        var row = "<tr id='row" + total_rows + "'>" + div + "<td class='text-center align-middle'><button type='button' data-row-id='" + total_rows + "' class='btn btn-sm btn-outline-danger delete_rows'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID').append(row);
        updateDate();
        $('.test_name').select2();
         total_rows++;
    });

    $(document).on('click','.delete_rows',function(e){          
            var modal_=$(e.target).closest('div.modal');            
            var message = "<?php echo $this->lang->line('are_you_sure'); ?>";            
            if(confirm(message)){ 
                var modal_=$(e.target).closest('div.modal');
				var del_row_id=$(this).data('rowId');
				$("#row" + del_row_id).remove();
				update_amount(modal_);
            }
    });

    $(document).on('select2:select','.test_name',function(){
        var medicine_details = {};
        gettestpathodetails($(this),$(this).val());
    });

    $(document).on('input paste keyup','.tax_percent,.discount_percent,.qty,.medicine_category,.medicine_name,.batch_no,.price', function(e){ 
        update_amount($(e.target).closest('div.modal'));
    });

	let update_amount=(__this)=>{
		 
		var grandTotal = 0; 
        let total_tax_amount = 0;
        var $tblrows = __this.find(".tblProducts tbody tr");  
        var discount_percent=__this.find('.discount_percent').val();
      
        $tblrows.each(function (index) {
			var $tblrow = $(this);  
			grandTotal += parseFloat($tblrow.find("td input.amount").val());			       
			total_amount_with_discount = $tblrow.find("td input.amount").val()-(($tblrow.find("td input.amount").val()*discount_percent)/100);
			total_tax_amount += parseFloat((total_amount_with_discount*$tblrow.find("td input.taxpercent").val())/100);        
		});
   
        grandTotal=  isNaN(grandTotal) ? 0 : grandTotal;
        total_tax_amount=  isNaN(total_tax_amount) ? 0 : total_tax_amount;
		__this.find('.total').val(grandTotal.toFixed(2));
		discount=(grandTotal * discount_percent / 100 );
		let discount_amount= isNaN(discount) ? 0 : discount;
		__this.find('.discount').val(discount_amount.toFixed(2)); 
		var net_amount=((grandTotal-discount_amount)+total_tax_amount);           
		__this.find('.tax').val(total_tax_amount.toFixed(2));
		__this.find('.net_amount').val(net_amount.toFixed(2));
		__this.find('.payment_amount').val(net_amount.toFixed(2));
		__this.find('#payamount').val(net_amount.toFixed(2));
		__this.find('#amount').val(net_amount.toFixed(2));
      
		$("#billsave").show();
		$(".printsavebtn").show();
	}	
	
	$(document).on('input paste keyup','.discount', function(e){ 
			var discount_amount = $("#discount").val();     
			var discount_amount=(discount_amount != "") ?discount_amount: 0;
			var total=$('#total').val();		 
			var discount_percent=0;		 
			discount_percent=((parseFloat(discount_amount)/parseFloat(total))*100);
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
			total_tax_amount += parseFloat((total_amount_with_discount*$tblrow.find("td input.taxpercent").val())/100);        
		});
   
        grandTotal=  isNaN(grandTotal) ? 0 : grandTotal;
        total_tax_amount=  isNaN(total_tax_amount) ? 0 : total_tax_amount;
		__this.find('.total').val(grandTotal.toFixed(2));
		discount=(grandTotal * discount_percent / 100 );
		let discount_amount= isNaN(discount) ? 0 : discount;		 
		var net_amount=((grandTotal-discount_amount)+total_tax_amount);           
		__this.find('.tax').val(total_tax_amount.toFixed(2));
		__this.find('.net_amount').val(net_amount.toFixed(2));
		__this.find('.payment_amount').val(net_amount.toFixed(2));
		__this.find('#payamount').val(net_amount.toFixed(2));
		__this.find('#amount').val(net_amount.toFixed(2));
      
		$("#billsave").show();
		$(".printsavebtn").show();
	}

    function addTotal1() {
        var total = 0;
        var total_taxamt = 0;        
        var discount_percent = $("#discount_percent").val();     
        var medicineTable=$("#assigntestModal .modal-body").find('table.tblProducts');
        medicineTable.find("tbody tr").each(function() {

           let row_amount= $(this).find("td input.amount").val();
           let row_tax_percentage= $(this).find("td input.taxpercent").val();

        total += parseFloat(row_amount);
        total_taxamt += parseFloat(((row_amount-(row_amount*discount_percent)/100)*row_tax_percentage)/100);

        });
       
         if(total>0){
        if (discount_percent != '') {
            var discount = (total * discount_percent) / 100;
            $("#discount").val(discount.toFixed(2));
        } else {
            var discount = $("#discount").val();          
        }       

        $("#total").val(total.toFixed(2));
        var net_amount = (parseFloat(total) - parseFloat(discount))+ parseFloat(total_taxamt);
        var cnet_amount = net_amount.toFixed(2)
        $("#net_amount").val(cnet_amount);
        $("#amount").val(cnet_amount);
        $("#tax").val(total_taxamt.toFixed(2));
        $("#payamount").val(cnet_amount);
        $("#billsave").show();
        $(".printsavebtn").show();
		}       
    }	
	
    function dateChanged(ev) {            
            var $tblrows = $('.tblProducts').find("tbody tr");
            $tblrows.each(function (index) {
            var $tblrow = $(this);  
            var _row_day = $tblrow.find(".days").val();
            if(_row_day !=""){
           
            //==============
            var report_day =  parseInt(_row_day, 10);
            var selected_date=SHPicker.getDate("#txtDate10") ;
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);
            $tblrow.find(".report_date")
            .each(function() { SHPicker.setDate(this, newdate); }); 
            //================            
                
            }        
            });
    }    

	$("form#bill button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function (e) {
    $(document).on('submit','#bill',function(e){
            e.preventDefault();
            let submit_button = $("button[type=submit][clicked=true]",this);
            let submit_button_name=submit_button.attr('name');
            $.ajax({
                url: base_url+'admin/pathology/addBill',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
              beforeSend: function() {
                    submit_button.btnLoading();
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
                        table.ajax.reload();
                        shModal('assigntestModal').hide();
                        console.log(submit_button_name);
                        if(submit_button_name == "save_print"){
                          printData(data.insert_id);  
                        }
                    }
                    submit_button.btnReset();
                },
                   error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  submit_button.btnReset();
            },
            complete: function() {
              submit_button.btnReset();
            }

            });
		});
    });
    
    $(".assigntest").click(function(){		
		var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';
		 
        /* datetimepicker init removed - auto-init via .datetime class */
        SHPicker.setDate('#txtDate10', new Date());
        $('#txtDate10').on('dp.change', dateChanged);      
    });
    
    function get_Docname(id) {        
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#doctname').val(res.name + " " + res.surname + " (" + res.employee_id + ")");
                    $('#doctorid').val(res.id);
                } else {

                }
            }
        });
    }  

    function viewDetailReport(id,pathology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getReportDetails/' + id +'/'+pathology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_view')) { ?><a href='#' data-bs-toggle='tooltip' onclick='printData(" + id + "," + pathology_id + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                holdModal('viewModalReport');
            },
        });
    }
    
    $(document).on('click','.view_detail',function(){
        var id=$(this).data('recordId');
        $("#is_prescription_no_exist").val("");
        PatientPathologyDetails(id,$(this));
    });

    function PatientPathologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/pathology/getPatientPathologyDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
                modal_view.addClass('modal_loading');

               },
            success: function (data) {
				$('#viewDetailReportModal .modal-body').html(data.page);
				$('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
				// Init tooltips on the freshly-injected header action buttons (DOMContentLoaded init already ran).
				$('#viewDetailReportModal #action_detail_report_modal [data-bs-toggle="tooltip"]').each(function () {
					bootstrap.Tooltip.getOrCreateInstance(this);
				});
				shModal('viewDetailReportModal').show();
				modal_view.removeClass('modal_loading');
            },
            error: function(xhr) { // if error occured
				alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                modal_view.removeClass('modal_loading');
			},
			complete: function() {
                modal_view.removeClass('modal_loading');
			}
        });  
    }
 
    $(document).on('click','.add_collection',function(){
        $('#collected_by').val('');
		var id=$(this).data('recordId');
		var modal_view=$('#collectionModal');
		var $this = $(this);   
		$.ajax({
            url: base_url+'admin/pathology/getReportCollectionDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
            },
            success: function (data) {
            $("#collected_by").val(data.report.collection_specialist);
            $("#collectionModal .modal-body").find('input[name="pathology_report_id"]').val(data.report.id);
            $("#collectionModal .modal-body").find('input[name="pathology_bill_id"]').val(data.report.pathology_bill_id);
            $("#collectionModal .modal-body").find('input[name="pathology_center"]').val(data.report.pathology_center);
            // document.querySelectorAll('#collectionModal .modal-body input[name="collected_date"]').forEach(function(el) { SHPicker.setDate(el, new Date(data.report.collection_date)); });
            document.querySelectorAll('#collectionModal .modal-body input[name="collected_date"]').forEach(function(el) { SHPicker.setDate(el, new Date(data.report.collection_date)); });
            shModal('collectionModal').show();
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

  $(document).on('click','.add_report',function(){
       var id=$(this).data('recordId');
       var modal_view=$('#addReportModal');
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/pathology/getPathologyReportDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();
               },
            success: function (data) {       
            $('#addReportModal .modal-body').html(data.page);
            $('#addReportModal .filestyle').dropify();
            shModal('addReportModal').show();
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

$(document).on('click','.print_pathology_report',function(){
   var id=$(this).data('recordId');

   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printPatientReportDetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.btnLoading();
           },
        success: function (data) {       
      popup(data.page);
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

    $(document).on('click','.view_bill',function(){
       var id=$(this).data('recordId');
       var modal_view=$('#viewModalbill');
       var $this = $(this);
   
        $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
              $this.btnLoading();
            },
            success: function (data) { 
                $('#viewModalbill .modal-body').html(data.page);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_view')) { ?><a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-bs-toggle='tooltip' data-record-id="+id+"  data-placement='bottom'  title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                shModal('viewModalbill').show();
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

    $(document).on('click','.print_bill',function(){
    var id=$(this).data('recordId');      
        var $this = $(this);    
        $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.btnLoading();
               },
            success: function (data) {       
                popup(data.page);
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

    function printData(id){
         $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
            },
            success: function (data) {       
                popup(data.page);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");      
            },
            complete: function() {           
            
            }
        });
    }

    function deleterecord(id) {
        var url = '<?php echo base_url() ?>admin/pathology/deleteTestReport/' + id;
        var msg = "<?php echo $this->lang->line('delete_message') ?>";
        delete_recordById(url, msg)
    }

    function editTestReport(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
              
                $("#report_id").val(data.id);
                $("#charge_category_html").val(data.charge_category);
                $("#code_html").val(data.code);
                $("#charge_html").val(data.standard_charge);
                $("#customer_types").val(data.customer_type);
                $("#opdipd").val(data.opd_ipd_no);
                $("#edit_patient_name").val(data.patient_name);
                $("#edit_report_date").val(data.reporting_date);
                if (data.apply_charge == "") {
                    $("#apply_charge").val(data.standard_charge);
                } else {
                    $("#apply_charge").val(data.apply_charge);
                }
                $('select[id="edit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#edit_description").val(data.description);
                $(".select2").select2().select2('val', data.patient_id);
                shModal('viewModal').hide();
                holdModal('editTestReportModal');
            },
        })
    }
     
    $(document).ready(function (e) {
        $("#updatetest").on('submit', (function (e) {
            e.preventDefault();
            $("#updatetestbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/updateTestReport',
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
                    $("#updatetestbtn").btnReset();
                },
                error: function () {
                }
            });
        }));

        $(document).on('submit','#form-sample-collected',function(e){
            e.preventDefault();
          var pathology_bill_id=$(this).find('input[name="pathology_bill_id"]').val();
            var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/pathology/updatecollection',
                type: "POST",
                data: form.serialize(), 
                dataType: 'json',
                  beforeSend: function() {
                  clicked_btn.btnLoading();
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
                           clicked_btn.btnReset();
                           shModal('collectionModal').hide();
                           PatientPathologyDetails(pathology_bill_id,clicked_btn);
                    }
                },
                error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
           clicked_btn.btnReset();
    },
    complete: function() {
      clicked_btn.btnReset();
    }
            });
      }); 

    $(document).on('submit','#form-report_param',function(e){
        e.preventDefault();
        var pathology_bill_id=$(this).find('input[name="pathology_bill_id"]').val();
          var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/pathology/updatereportparam',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
        processData: false,
        contentType: false,
                  beforeSend: function() {
                  clicked_btn.btnLoading();
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
                        shModal('addReportModal').hide();
                         PatientPathologyDetails(pathology_bill_id,clicked_btn);
                    }
                   clicked_btn.btnReset();
                },
                error: function(xhr) { // if error occured
                  alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  clicked_btn.btnReset();
               },
               complete: function() {
                 clicked_btn.btnReset();
               }
            });
      });
    });

    $(document).ready(function (e) {
        $("#parameteradd").on('submit', (function (e) {
            e.preventDefault();
            $("#parameteraddbtn").btnLoading();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/parameteraddvalue',
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
                    $("#parameteraddbtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });
	
function updateDate(){
     /* .report_date datepicker init removed - .date class on inputs handles it */
}
</script>

<script type="text/javascript">
 $(document).on('click','#search_prescription',function(e){
    let modal_prescription_=$(e.target).closest('div.modal');
    getPrescriptionData(modal_prescription_);
	});
 
	function getPrescriptionData(modal_prescription_)
	{	
		let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
		var createModal=$('#assigntestModal');
        var prescription_no=$("input[name=prescription_no]").val();

        if(prescription_no!=""){
			$.ajax({
				url: '<?php echo base_url(); ?>admin/pathology/prescriptionBill',
				type: "POST",
				data:{'prescription_no':$("input[name=prescription_no]").val(),'date':$('#txtDate10').val(),is_tpa:is_tpa},
				dataType: 'json',
				beforeSend: function() {
					createModal.addClass('modal_loading');
				},
				success: function(res) {

                    if(res.status == 0){
                    var message = "";
                        $.each(res.error, function (index, value) {
                            message += value;
                        });

                        $("#is_prescription_no_exist").val("");
                        $(".assigntest").trigger("click");
                        $('#addpatient_id').select2("val", "");
                        errorMsg(message);
					}else{

                        if(res.patient_id == 0){
                        $("#is_prescription_no_exist").val("");
                        $(".assigntest").trigger("click");
                        $('#addpatient_id').select2("val", "");
						errorMsg("<?php echo $this->lang->line('no_prescription_found'); ?>");
					}else{  
                        $("#is_prescription_no_exist").val(prescription_no);
                        if(res.tpa_status==1){
                            errorMsg(res.msg);
                        }  

                        if(res.total_rows<=0){
                            errorMsg("<?php echo $this->lang->line('test_not_found'); ?>");
                        } 

                        var option = new Option(res.patient_name+" ("+res.patient_id+")", res.patient_id, true, true);
                        $("#bill .patient_list_ajax").append(option).trigger('change');

         
						$('#assigntestModal .modal-body').html(res.page);
						$('#case_reference_id').val(res.case_reference_id);
						$('.filestyle','#assigntestModal').dropify();
						// $("#addpatient_id").select2("val", '');
						$(".test_name").select2();
						updateDate();
						update_amount(modal_prescription_);
						total_rows=(res.total_rows <= 0) ? 1:res.total_rows;
						total_rows=res.total_rows;
						addappointmentModal(res.patient_id, 'assigntestModal');
					}
                    }				
					
				},
				error: function(xhr) { // if error occured
					alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
					createModal.removeClass('modal_loading'); 
				},
				complete: function() {
					createModal.removeClass('modal_loading');
				}
			});
        }else{
            errorMsg("<?php echo $this->lang->line('no_prescription_found'); ?>");
            $(".assigntest").trigger("click");
            $('#addpatient_id').select2("val", "");
        }
	}


    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });
    $(document).on('click','#addpatient_id',function(){
    
    });
$('#addpatient_id').on('select2:select', function (e) {
     $('#case_reference_id').val('');
     $('#prescription_no').val('');
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">

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
            url: '<?php echo base_url() ?>admin/pathology/getPathologyTransaction',
            type: "POST",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');  
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

    $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_partial_payment').val();
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
                         getPayments(pathology_billing_id);
                            table.ajax.reload();
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

         $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.btnLoading();
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/printTransaction',
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
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.btnReset();              
         },
              complete: function() {
                   $this.btnReset();                 
             }
      });
  });

          $(document).on('click','.delete_trans', function(e){
                     if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {   
            e.preventDefault();
            var record_id=$(this).data('recordId');         
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_partial_payment').val();
            var btn = $(this);       
            btn.btnLoading();
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id,'pathology_billing_id':pathology_billing_id},
                dataType: 'JSON',               
                success: function (data) {
                    successMsg(data.message);
                    getPayments(pathology_billing_id);
                    btn.btnReset();
                    table.ajax.reload();
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
 
    $('#assigntestModal').on('hidden.bs.modal', function () {
        var assigntestModal= $('#assigntestModal');
    $('#addpatient_id').select2("val", "");
    $('#billno,#prescription_no,#case_reference_id',assigntestModal).val("");
    $("input:checkbox[name=is_tpa]").prop('checked',false);
    SHPicker.setDate('#assigntestModal input[name="date"]', new Date());
    $("#organisation_id").val('');
    $("#insurance_id").val('');
    $("#insurance_validity").val(''); 
   });
   
    $(document).ready(function (e) {
        modal_click_disabled('viewDetailReportModal', 'addPaymentModal', 'collectionModal', 'addReportModal');
    });
</script>
<script>
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/pathology/getpathologybillDatatable',[],[],100,
            [
            { "bSortable": false, "sWidth": "80px", "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
            {  "sWidth": "80px", "aTargets": [ -2,-3 ] ,'sClass': 'dt-body-right'},
            { "aTargets": [ -4,-5,-6 ] ,'sClass': 'dt-body-right'},
            { "aTargets": [ 1 ] ,'sClass': 'dt-body-center'},
            { "sWidth": "120px", "aTargets": [ 3 ] }
            ]
            );
        window.table.on('draw.dt', function () {
            var lastCol = document.querySelector('#testreport colgroup col:last-child');
            if (lastCol) lastCol.style.setProperty('width', '80px', 'important');
            ['nth-last-child(1)','nth-last-child(2)','nth-last-child(3)'].forEach(function(sel) {
                $('#testreport th:' + sel + ', #testreport td:' + sel).each(function () {
                    this.style.setProperty('width',     '80px', 'important');
                    this.style.setProperty('min-width', '80px', 'important');
                    this.style.setProperty('max-width', '80px', 'important');
                });
            });
            $('#testreport th:nth-child(4), #testreport td:nth-child(4)').each(function () {
                this.style.setProperty('width',     '120px', 'important');
                this.style.setProperty('max-width', '120px', 'important');
            });
        });
    });
} ( jQuery ) )
</script>
<script>
    $(document).on('click','.print_parameter',function(){
   var id=$(this).data('recordId');
   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printtestparameterdetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.btnLoading();
           },
        success: function (data) {       
      popup(data.page);
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

    function get_percentage(discount_amount){
      var discount_amount=(discount_amount != "") ?discount_amount: 0;
      var total=$('#total').val();
      var  tax=$('#tax').val();
      var discount_percent=0;
      var net_amount=0;
      discount_percent=((parseFloat(discount_amount)/parseFloat(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));
     }

</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<script type="text/javascript">   
    function get_PatientDetails(id) { 
    if(id!=""){
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
            }
        });
    }
    }

    $(document).on("click", "#is_tpa", function(){
		// Preserve the bill id across the TPA reload. .assigntest reloads a fresh form and
		// getInsertModalForm() clears #pathology_billing_id — which otherwise turned an EDIT
		// into a new-record INSERT after toggling TPA. Restore it so the save still UPDATEs
		// the existing bill (old tests are cleaned up server-side in Pathology_model::addBill).
		var current_billing_id = $('#pathology_billing_id').val();
		$(".assigntest").trigger("click");
		$('#pathology_billing_id').val(current_billing_id);
		var addpatient_id=$("#addpatient_id").val();
		get_PatientDetails(addpatient_id);
    });

</script>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ $('.assigntest').trigger('click'); shCleanUrlParam('action'); });</script>
<?php endif; ?>
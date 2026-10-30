<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('referral_payment_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addpayment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_payment'); ?></a>
                            <?php } ?>

							<?php if ($this->rbac->hasPrivilege('referral_person', 'can_view')) { ?>
                                <a href="<?php echo site_url('admin/referral/person'); ?>" class="btn btn-primary btn-sm addpayment"><?php echo $this->lang->line('referral_person'); ?></a>
							<?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                        <div class="download_label"><?php echo $this->lang->line('referral_payment_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('payee'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('bill_amount').' ('. $currency_symbol .')'; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('commission_percentage'); ?> (%)</th>
                                        <th class="text-end"><?php echo $this->lang->line('commission_amount').' ('. $currency_symbol .')'; ?></th>
                                        <?php if ( ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) || ($this->rbac->hasPrivilege('referral_payment', 'can_delete'))  ) { ?>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
										<?php } ?>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                        if (empty($payment)) {
                                            ?>
                                            <?php
                                        } else {
                                            foreach ($payment as $key => $value) {
                                                ?>
                                            <tr>
                                                <td class="mailbox-name"><a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo html_escape($value['name']) ?></a></td>
                                                <td><?php echo composePatientName($value["patient_name"],$value["patient_id"]); ?></td>
                                                <td class="text-end"><?php echo html_escape($value["prefix"]).(int)$value["billing_id"]; ?></td>
                                                <td class="text-end"><?php echo amountFormat($value["bill_amount"]); ?></td>
                                                <td class="text-end"><?php echo html_escape($value["percentage"]); ?></td>
                                                <td class="text-end"><?php echo html_escape($value["amount"]); ?></td>
                                                <?php if ( ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) || ($this->rbac->hasPrivilege('referral_payment', 'can_delete'))  ) { ?>
                                                <td class="text-end noExport">
                                                    <div class="d-inline-flex gap-1">
                                                    <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo (int)$value['id'] ?>')" class="btn btn-sm btn-outline-primary" data-bs-target="#myModalEdit" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_delete')) { ?>
                                                        <a class="btn btn-sm btn-outline-danger sh-cursor-pointer" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete') ?>" onclick="delete_recordByIdReload('admin/referralpayment/delete/<?php echo (int)$value['id']; ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                    </div>
                                                </td>
                                                <?php } ?>
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
    

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addReferralPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReferralPaymentLabel"><?php echo $this->lang->line('add_referral_payment'); ?></h5>
                <select class="form-select form-select-sm patient_list_ajax" name="" id="addpatient_id">
                    <option value=""></option>
                </select>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addpayment" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <input name="patient_id" id="patient_id" type="hidden">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row g-3 sh-eq-card-row">
                            <div class="col-lg-7">
                                <div id="ajax_load" class="text-center w-100"></div>
                                <div class="sh-form-card" id="patientDetails" class="d-none">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-user"></i> <?php echo $this->lang->line('patient_details'); ?></span>
                                    </div>
                                    <div class="sh-info-grid">
                                        <div class="row g-0">
                                            <div class="col-8 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                                                <span class="sh-info-value highlight" id="listname"></span>
                                            </div>
                                            <div class="col-4 d-flex align-items-center justify-content-center p-2">
                                                <img class="modal-profile-user-img img-fluid d-none" src="" id="image" alt="User profile picture">
                                <div class="modal-profile-user-initials d-none" id="image_initials"></div>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></span>
                                                <span class="sh-info-value" id="guardian"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></span>
                                                <span class="sh-info-value" id="genders"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></span>
                                                <span class="sh-info-value" id="blood_group"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></span>
                                                <span class="sh-info-value" id="marital_status"></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></span>
                                                <span class="sh-info-value" id="age"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></span>
                                                <span class="sh-info-value" id="listnumber"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></span>
                                                <span class="sh-info-value" id="email"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></span>
                                                <span class="sh-info-value" id="address"></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></span>
                                                <span class="sh-info-value" id="allergies"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></span>
                                                <span class="sh-info-value" id="note"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></span>
                                                <span class="sh-info-value" id="insurance_id"></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></span>
                                                <span class="sh-info-value" id="insurance_validity"></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-12 sh-info-item">
                                                <span class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></span>
                                                <span class="sh-info-value" id="identification_number"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="sh-form-card">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('patient_type'); ?> <small class="req">*</small></label>
                                                <select name="patient_type" id="patient_type" class="form-select form-select-sm select2">
                                                    <option value=""><?php echo $this->lang->line('select_type'); ?></option>
                                                    <?php foreach ($type as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>"><?php echo $this->lang->line($value['name']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('bill_no_case_id'); ?> <small class="req">*</small></label>
                                                <select name="bill_no" id="bill_no_case_id" class="form-select form-select-sm select2">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('patient_bill_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req">*</small></label>
                                                <input class="form-control form-control-sm" id="bill_amount" name="bill_amount" type="text">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('payee'); ?> <small class="req">*</small></label>
                                                <select name="payee" id="payee" class="form-select form-select-sm select2">
                                                    <option value=""><?php echo $this->lang->line('select_payee'); ?></option>
                                                    <?php foreach ($person as $key => $value) { ?>
                                                    <option value="<?php echo (int)$value->person_id; ?>"><?php echo html_escape($value->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('commission_percentage'); ?> (%) <small class="req">*</small></label>
                                                <input oninput="calculate_commission()" class="form-control form-control-sm" id="percentage" name="percentage" type="text">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label form-label-sm"><?php echo $this->lang->line('commission_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req">*</small></label>
                                                <input class="form-control form-control-sm" id="commission_amount" name="commission_amount" type="text">
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
                    <button type="submit" id="addpaymentbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="editReferralPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm400">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReferralPaymentLabel"><i class="fa fa-pencil me-1"></i> <?php echo $this->lang->line('edit_referral_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editpayment" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <input id="paymentid" name="paymentid" type="hidden">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('commission_percentage'); ?> (%) <small class="req">*</small></label>
                                        <input id="commission_percentage" name="commission_percentage" type="text" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('commission_amount') . ' (' . $currency_symbol . ')'; ?> <small class="req">*</small></label>
                                        <input id="editcommission_amount" name="commission_amount" type="text" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editpaymentbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('.select2').select2();
    });
	
    $(document).ready(function (e) {
        $('#addpayment').on('submit', (function (e) {
            $("#addpaymentbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralpayment/add',
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
                            $('.' + index).html(value);
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addpaymentbtn").btnReset();
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
        }));
    });
 
    function getRecord(id) {
        shModal('myModalEdit').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#commission_percentage").val(data.percentage);
                $("#editcommission_amount").val(data.amount);
                $("#paymentid").val(id);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    $(document).ready(function (e) {
        $('#editpayment').on('submit', (function (e) {
            $("#editpaymentbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralpayment/update',
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
                    $("#editpaymentbtn").btnReset();
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
        }));
    });
</script>
<script>

    function renderReferralAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $('#image').attr('src', imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $('#image_initials').text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?' : parts.length === 1 ? parts[0].charAt(0) : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $('#image').addClass('d-none').removeAttr('src');
            $('#image_initials').text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    $('#myModal').on('shown.bs.modal', function () {
        var $sel = $('#addpatient_id');
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
            dropdownParent: $('#myModal')
        });
    });

	$('#addpatient_id').on('select2:select', function (e) {
     let id=$(this).val();
     $.ajax({
        url: base_url+'admin/patient/patientDetails',
        type: "POST",
        data: {id: id},
        dataType: 'json',
        beforeSend: function() {
        var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";
            $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        },
        success: function (res) {
            if (res) {
                $("#ajax_load").html("");
                $("#patientDetails").removeClass('d-none');
                $('#patient_unique_id').html(res.patient_unique_id);
                $('#patient_id').val(res.id);
                $('#listname').html(res.patient_name+" ("+res.id+")");
                $('#guardian').html(res.guardian_name);
                $('#listnumber').html(res.mobileno);
                $('#email').html(res.email);
                $("#age").html(res.patient_age);
                $("#insurance_id").html(res.insurance_id);
                $("#insurance_validity").html(res.insurance_validity);
                $("#identification_number").html(res.identification_number);
                $("#address").html(res.address);
                $("#note").html(res.note);
                $("#genders").html(res.gender);
                $("#marital_status").html(res.marital_status);
                $("#blood_group").html(res.blood_group_name);
                $("#allergies").html(res.known_allergies);
                renderReferralAvatar(res.image, res.patient_name);
            } else {
                $("#ajax_load").html("");
                $("#patientDetails").addClass('d-none');
            }
        },
        error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            $('#ajax_load').html("");
         },
        complete: function() {
            $('#ajax_load').html("");
        }
    });
});

</script>

<script>
    function getBillAmount(){
        type = $("#patient_type").val();
        patient_id = $("#patient_id").val();
		if(type != ''){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getBillAmount',
            type: "POST",
            data:{type:type,patient_id:patient_id},
            success: function (data) {
                $("#bill_amount").val(data);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
		}
    }

    function calculate_commission(){
        bill_amount = $("#bill_amount").val();
        percentage = $("#percentage").val();
        amount = (bill_amount*percentage)/100;
        $("#commission_amount").val(amount.toFixed(2));
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'myModalEdit');
    });
 
 $('#myModal').on('hidden.bs.modal', function () {
    $(".patient_list_ajax").select2("val", "");  
    $("#patientDetails").addClass('d-none');
    $("#patient_type").select2("val", "");   
    $("#bill_no_case_id").select2("val", "");
    $("#payee").select2("val", "");
    $('div #myModal #patientDetails').find('span').text("");
    $('form#addpayment').find('input:text, input:password, input:file, textarea').val('');
    $('form#addpayment').find('select option:selected').removeAttr('selected');
    $('form#addpayment').find('input:checkbox, input:radio').removeAttr('checked');   
});

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }
    
    $('#patient_type').on('select2:select', function (e) {   
        var div_data = "";
        $('#operation_name').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        var patient_id = $("#patient_id").val();
        var type = $(this).val();
        if (type > '0'){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getBillNo',
            type: "POST",
            dataType: 'json',
            data:{type:$(this).val(),patient_id:patient_id},
            success: function (data) {
             $.each(data, function (i, obj)
                { 
                    var name='';
                    var sel = "";
                   if(obj.case_id==null){
                    name=obj.prefixe_name+obj.bill_no;
                   }else{
                    name=obj.prefixe_name+obj.bill_no+"/"+obj.case_id;
                   }
                   
                    div_data += "<option value=" + obj.bill_no + " " + sel + ">" + name+ "</option>";
                });
                $("#bill_no_case_id").html("<option value=''><?php echo $this->lang->line('select')?></option>");
                $('#bill_no_case_id').append(div_data);           
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });        
      }
});

	$('#bill_no_case_id').on('select2:select', function (e) {
			var type=$('#patient_type').val();
			if(type != ''){
			$.ajax({
                url: base_url+'admin/referralpayment/getBillAmount',
                type: "POST",
                 dataType: 'json',
                data:{type:type,bill_no:$(this).val()},
                success: function (data) {
                   $('#bill_amount').val(data.total_bill);
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
			}
		});
</script> 

<script>
    $('#payee').on('select2:select', function (e) {
        let type = $("#patient_type").val();
        let  payee = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getCommission',
            type: "POST",
            data:{type:type,payee:payee},
            success: function (data) {               
				if(type != '' && payee != ''){
				  $("#percentage").val(data);
                calculate_commission();
				}else{
					 $("#percentage").val('');
				}				
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
     });
    function getPercentage(){
   
    }
</script>

<?php $this->load->view('admin/patient/patientaddmodal') ?>
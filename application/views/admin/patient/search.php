<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender_Patient();
?>
<div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <ul class="nav nav-pills sh-segmented-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active tab_button" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab" data-type="1"><?php echo $this->lang->line("todays_opd"); ?></button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tab_button" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab" data-type="2"><?php echo $this->lang->line("upcoming_opd"); ?></button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tab_button" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab" data-type="3"><?php echo $this->lang->line("old_opd"); ?></button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_1" type="button" role="tab"><?php echo $this->lang->line('patient_view'); ?></button>
                                </li>
                            </ul>
                            <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_add')) { ?>
                            <a onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpatient"><i class="fa fa-plus"></i>&nbsp; <?php echo $this->lang->line('add_patient'); ?></a>
                            <?php } ?>
                        </div>
                          <div class="tab-content">
                             <div class="tab-pane" id="tab_1">
 
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('opd_view'); ?>">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('name') ?></th>
                                                <th><?php echo $this->lang->line('patient_id'); ?></th>
                                                <th><?php echo $this->lang->line('guardian_name') ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>
                                                <th><?php echo $this->lang->line('phone'); ?></th>
                                                <th><?php echo $this->lang->line('generated_by'); ?></th>
                                                <th><?php echo $this->lang->line('consultant'); ?></th>
                                                <th><?php echo $this->lang->line('last_visit'); ?></th> 
												<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                                <th><?php echo $this->lang->line('is_antenatal'); ?></th> 
												<?php } ?>
                                                <th class="text-end"><?php echo $this->lang->line('total_recheckup'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                              </div>  
							  
                               <div class="tab-pane show active" id="tab_2">
    
                                   <div class="card-body table-responsive">                                   
                                   <table class="table table-striped table-bordered table-hover opd_ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('patient_view'); ?>">
                                        <thead>
                                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                                            <th><?php echo $this->lang->line('case_id'); ?></th>
                                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                            <th><?php echo $this->lang->line('generated_by'); ?></th>
                                            <th><?php echo $this->lang->line('consultant'); ?></th>
                                            <th><?php echo $this->lang->line('reference'); ?></th>
                                            <th><?php echo $this->lang->line('symptoms'); ?></th>
											<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                            <th><?php echo $this->lang->line('is_antenatal'); ?></th>
											<?php } ?>
                                            <?php 
                                                if (!empty($fields)) {
                                                    foreach ($fields as $fields_key => $fields_value) {
                                                        ?>
                                                        <th><?php echo $fields_value->name; ?></th>
                                                        <?php
                                                    } 
                                                }
                                            ?> 
                                            <th class="text-end noExport white-space-nowrap"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                               </div>
                        </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('new_visit'); ?></h5>
                    <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax sh-inp-200" <?php
                            if ($disable_option == true) { echo "disabled"; }
                            ?> name='' id="addpatient_id">
                    </select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                        <a onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('new_patient'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                                <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                                <input name="email" id="pemail" type="hidden" class="form-control" />
                                <input name="mobileno" id="mobnumber" type="hidden" class="form-control" />
                                <input name="patient_name" id="patientname" type="hidden" class="form-control" />
                                <input name="password" id="password" type="hidden" class="form-control" />
                                <input name="organisation_id" id="organisation_id" type="hidden" class="form-control" />
                                <input name="insurance_validity" id="insurance_validity" type="hidden" class="form-control" />
                                <input name="insurance_id" id="insuranceid" type="hidden" class="form-control" />
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="sh-form-card mb-2" id="patientDetails" class="d-none">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title" id="listname"></span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <div class="sh-info-grid flex-grow-1">
                                                <div class="row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_guardian">
                                                        <small class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></small>
                                                        <span class="sh-info-value" id="guardian"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_gender">
                                                        <small class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></small>
                                                        <span class="sh-info-value" id="genders"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_blood">
                                                        <small class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></small>
                                                        <span class="sh-info-value" id="blood_group"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_marital">
                                                        <small class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></small>
                                                        <span class="sh-info-value" id="marital_status"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_age">
                                                        <small class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></small>
                                                        <span class="sh-info-value" id="age"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_phone">
                                                        <small class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></small>
                                                        <span class="sh-info-value" id="listnumber"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_email">
                                                        <small class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></small>
                                                        <span class="sh-info-value" id="email"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_address">
                                                        <small class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></small>
                                                        <span class="sh-info-value" id="address"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa">
                                                        <small class="sh-info-label"><i class="fas fa-shield-alt"></i> <?php echo $this->lang->line('tpa'); ?></small>
                                                        <span class="sh-info-value" id="organisation_name"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_tpa_id">
                                                        <small class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></small>
                                                        <span class="sh-info-value" id="insurance_id"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_validity">
                                                        <small class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></small>
                                                        <span class="sh-info-value" id="validity"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_national_id">
                                                        <small class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></small>
                                                        <span class="sh-info-value" id="identification_number"></span>
                                                    </div>
                                                </div>
                                                <div class="sh-row-divider row g-0">
                                                    <div class="sh-info-item col-6 col-md-3" id="li_allergies">
                                                        <small class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></small>
                                                        <span class="sh-info-value" id="allergies"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3" id="li_note">
                                                        <small class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></small>
                                                        <span class="sh-info-value" id="note"></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_barcode">
                                                        <small class="sh-info-label"><i class="fa fa-barcode"></i> <?php echo $this->lang->line('barcode'); ?></small>
                                                        <span><img id="getbarcode" class="pd-barcode-img" alt="" /></span>
                                                    </div>
                                                    <div class="sh-info-item col-6 col-md-3 d-none" id="show_qrcode">
                                                        <small class="sh-info-label"><i class="fa fa-qrcode"></i> <?php echo $this->lang->line('qrcode'); ?></small>
                                                        <span><img id="getqrcode" class="pd-qrcode-img" alt="" /></span>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="opd-pd-photo-cell">
                                                    <img class="opd-pd-photo-lg" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" id="image" alt="<?php echo $this->lang->line('patient'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sh-form-card mb-2">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type') ; ?></label>
                                                    <div>
                                                        <select  name='symptoms_type'  id="act"  class="form-control select2 act w-100"   multiple >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                            <input name="rows[]" type="hidden" value="1">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li class="section-placeholder"><span><?php echo $this->lang->line('select') ; ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="esymptoms" name="symptoms" rows="3" ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_group'); ?></label>
                                                    <select id="opd_icd10_group_filter" class="form-control select2 w-100" >
                                                        <option value=""><?php echo $this->lang->line('select_group'); ?></option>
                                                        <?php if (!empty($icd10_groups)) { foreach ($icd10_groups as $g) { ?>
                                                        <option value="<?php echo $g['id']; ?>"><?php echo html_escape($g['group_name']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-9 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_diagnosis'); ?></label>
                                                    <select name="icd_code_ids[]" id="opd_icd_code_ids" class="form-control select2 w-100" multiple >
                                                        <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies"  rows="3" id="eknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-12">
                                                <div class="mb-3">
                                                    <?php
                                                    echo display_custom_fields('opd');
                                                    ?>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-8-->
                                    <div class="col-lg-4 col-md-4 col-sm-4 ptt10">
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('visit_details'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?> <small class="req"> *</small></label>
                                                    <input id="datetimepicker" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name='case' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                        <select name="casualty" id="casualty" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>                                                        
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div>
                                                        <input class="form-control" type='text' name='refference' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="d-block">&nbsp;</label>
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input apply_tpa" id="is_tpa" name="is_tpa" type="checkbox" value="1" autocomplete="off">
                                                        <label class="form-check-label" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('consultant_doctor'); ?> &amp; <?php echo $this->lang->line('charges'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('consultant_doctor'); ?> <small class="req"> *</small></label>
                                                    <div>
                                                        <select name='consultant_doctor' id="consultant_doctor" class="form-control select2" <?php
                                                            if ($disable_option == true) { echo "disabled";  }   ?> class="w-100"  >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($doctors as $dkey => $dvalue) {   ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                        if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                            echo "selected";
                                                                        }
                                                                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?>
                                                                            
                                                                </option>   
                                                            <?php } ?>
                                                        </select>
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="consultant_doctor"  value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
											<div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
												    <select name="charge_category" class="w-100 form-control charge_category select2">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($charge_category as $key => $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>">
                                                        <?php echo $value['name']; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>                                           
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('charge'); ?> <small class="req"> *</small></label>
                                                    <select name="charge_id" class="w-100 form-control charge select2">
                                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                                    </select>
                                                    <input type="hidden" class="form-control right-border-none" name="org_charge_amount" id="org_charge_amount" readonly autocomplete="off">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="amount" id="apply_charge" class="form-control apply_charge">    
                                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value='0' autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-text "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?> <small class="req"> *</small></label>
                                                    <input type="text" name="apply_amount" readonly id="apply_amount" class="form-control">
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                        <div class="sh-form-card mb-3">
                                            <div class="sh-card-header">
                                                <span class="sh-card-header-title"><i class="fas fa-credit-card"></i> <?php echo $this->lang->line('payment'); ?></span>
                                            </div>
                                            <div class="p-2"><div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select name="payment_mode" class="form-control payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) {
                                                            ?>
                                                            <option value="<?php echo $payment_key ?>" <?php
                                                                    if ($payment_key == 'Cash') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $payment_value ?></option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="pwd"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")" ; ?> <small class="req"> *</small></label>
                                                    <input type="text" name="paid_amount" id="paid_amount" class="form-control paid_amount">    
                                                    <span class="text-danger"><?php echo form_error('paid_amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="cheque_div d-none col-12" >
                                                <div class="row">
                                                <div class="col-md-6 ps-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                                </div>
                                                <div class="col-md-6 pe-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?> <small class="req"> *</small></label>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                                </div>
                                                <div class="col-sm-12 px-0">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control"   name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                                </div>
                                                </div>
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label>
                                                    <?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult"  class="form-control">
                                                        <?php foreach($yesno_condition as $yesno_key => $yesno_value) {  ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value; ?>
                                                            </option>
                                                            <?php } ?>
                                                    </select>                                        
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div> 
                                            <?php  } ?>
                                            <div class="col-sm-6" id="antenatal_div" class="d-none">
                                                <div class="mb-3">
                                                    <label>&nbsp;</label>
                                                    <div class="sh-check-box">
                                                        <input type="checkbox" name="is_for_antenatal" id="is_for_antenatal" value="1">
                                                        <label for="is_for_antenatal"><?php echo $this->lang->line('is_for_antenatal'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            </div></div>
                                        </div>
                                    </div><!--./col-md-4-->
                                </div><!--./row-->
                </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                <button type="submit" id="formaddbtn" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
            </div>
        </form>                
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8"  enctype="multipart/form-data" method="post">
            <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="patient_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" id="updateid" name="updateid">
                                        <input type="hidden" id="opdid" name="opdid">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('guardian_name'); ?></label>
                                        <input type="text" id="guardian_name" name="guardian_name" value="<?php echo set_value('guardian_name'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="gender" name="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {  ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                        <select name="marital_status" id="marital_status" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($marital_status as $mkey => $mvalue) {  ?>
                                                <option value="<?php echo $mkey ?>"><?php echo $mvalue ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
                                        <?php echo $this->lang->line('patient_photo'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                            <input type="hidden" name="patient_photo" id="patient_photo">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>  
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('email'); ?></label>
                                        <input type="text" id="email" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                    </div>
                                </div> 
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="contact" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label> <?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="blood_group" name="blood_group">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($bloodgroup as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('age'); ?></label>
                                        <div class="sh-clear">
                                            <input type="text" placeholder="<?= $this->lang->line('age') ?>" id="age" name="age" value="<?php echo set_value('age'); ?>" class="form-control sh-print-left-40" >
                                            <input type="text" placeholder="<?= $this->lang->line('month') ?>" id="month" name="month" value="<?php echo set_value('month'); ?>" class="form-control sh-print-left-56" >
                                        </div>
                                    </div>
                                </div>
                            </div><!--./row-->
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Prescription -->
<div class="modal fade sh-modal sh-modal-nospace" id="add_prescription" tabindex="-1" aria-labelledby="prescription_title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescription_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_prescription" class="modal-text-white" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                    </div><!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" name="save_print" value="save_print" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" name="save" value="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Add Prescription -->

<!-- -->
<div class="modal fade sh-modal sh-modal-accent" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescription' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescription"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="prescriptionviewmanual" tabindex="-1" aria-labelledby="prescriptionviewmanualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewmanualLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescriptionmanual' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescriptionmanual">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('visit_details'); ?></h5>
                <div id='edit_delete' class="d-flex align-items-center gap-2 ms-auto me-2">
                <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                    <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                    <?php
                }
                if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                    ?>
                    <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                <?php } ?>
            </div>
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

<div class="modal fade sh-modal sh-modal-accent" id="moveIPDModal" tabindex="-1" aria-labelledby="moveIPDModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveIPDModalLabel"><?php echo $this->lang->line('move_patient_to_ipd'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('admin/patient/moveopd') ?>" id="form_confirm-move" method="POST" accept-charset="utf-8">
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <p><?php echo $this->lang->line('some_text_in_the_modal'); ?></p>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info btn-ok"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('move'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="visitformedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('edit_visit_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="ajax_load"></div>
                    <input type="hidden" name="visitid" id="visitid" />
                    <input type="hidden" name="visit_transaction_id" id="visit_transaction_id" />
                    <input type="hidden" name="type" id="type" value="opd" />
                    <input type="hidden" name="opdid" id="edit_opdid">
                    <div class="row g-2">
                        <div class="col-lg-7">
                            <div class="sh-form-card h-100">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('symptoms'); ?></span>
                                </div>
                                <div class="p-2">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="mb-2 select2-full-width">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                <select name="symptoms_type[]" id="act" class="form-control form-control-sm select2 act" multiple>
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms'); ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control filterinput" type="text" autocomplete="off">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li class="section-placeholder"><span><?php echo $this->lang->line('select'); ?></span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                <textarea class="form-control form-control-sm" id="symptoms_description" name="symptoms"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                                <textarea rows="3" class="form-control form-control-sm" id="edit_revisit_note" name="revisit_note"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                <textarea name="known_allergies" rows="3" id="editknown_allergies" class="form-control form-control-sm"><?php echo set_value('address'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div id="customfield"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="sh-form-card mb-2">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
                                </div>
                                <div class="p-2">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                                                <input name="appointment_date" class="form-control form-control-sm datetime" id="appointmentdate" type="text" />
                                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                                <input class="form-control form-control-sm" type="text" name="case" id="edit_case" />
                                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                                <select name="casualty" id="edit_casualty" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                                <select name="old_patient" id="edit_oldpatient" class="form-control form-control-sm">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                    <option value="<?php echo $yesno_key ?>"><?php echo $yesno_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                                <input type="text" name="refference" class="form-control form-control-sm" id="edit_refference" />
                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                <select name="consultant_doctor" <?php if ($disable_option == true) { echo "disabled"; } ?> class="form-control form-control-sm select2" id="edit_consdoctor">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($doctors as $dkey => $dvvalue) { ?>
                                                    <option value="<?php echo $dvvalue["id"] ?>"><?php echo composeStaffNameByString($dvvalue["name"], $dvvalue["surname"], $dvvalue["employee_id"]); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php if ($disable_option == true) { ?>
                                                <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sh-form-card">
                                <div class="sh-card-header">
                                    <span class="sh-card-header-title"><?php echo $this->lang->line('payment'); ?></span>
                                </div>
                                <div class="p-2">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('payment_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="payment_date" id="edit_visit_payment_date" class="form-control form-control-sm datetime" autocomplete="off">
                                                <input type="hidden" id="edit_visit_payment_id" name="edit_payment_id">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></label><small class="req"> *</small>
                                                <input type="text" name="amount" id="edit_visit_payment" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control form-control-sm visit_payment_mode" name="payment_mode" id="visit_payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                    <option value="<?php echo $key ?>" <?php if ($key == 'cash') { echo "selected"; } ?>><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('payment_note'); ?></label>
                                                <input type="text" name="note" id="edit_payment_note" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="cheque_div d-none" >
                                                <div class="row g-2">
                                                    <div class="col-sm-6 ps-0">
                                                        <div class="mb-2">
                                                            <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                            <input type="text" name="cheque_no" id="edit_visit_cheque_no" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 pe-0">
                                                        <div class="mb-2">
                                                            <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                            <input type="text" name="cheque_date" id="edit_visit_cheque_date" class="form-control form-control-sm date">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 px-0">
                                                        <div class="mb-2">
                                                            <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                                            <input type="file" class="filestyle form-control form-control-sm" name="document">
                                                            <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($this->rbac->hasPrivilege('antenatal', 'can_edit')) { ?>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label class="form-label"><?php echo $this->lang->line('is_antenatal'); ?></label>
                                                <div>
                                                    <input type="checkbox" name="is_for_antenatal" value="1" id="is_antenatal">
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- end new added modal-->
<!-- -->
<script type="text/javascript">
     var prescription_rows=2;
     $(document).on('change','.visit_payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){     
       
       $(".date").trigger("change");
        $('.cheque_div').removeClass('d-none');

      }else{

        $('.cheque_div').addClass('d-none');
      }
    });
</script>

<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val},  
            dataType: 'JSON',
            beforeSend: function () {
                $('ul.section_ul').find('li:not(:first-child)').remove();
            },
            success: function (data) {
                section_ul.append(data.record);
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () {
            }
        });
    });
</script>

<script type="text/javascript">

    $(document).on('click', '.remove_row', function () {
        $this = $(this);
        $this.closest('.row').remove();
    });

    $(document).mouseup(function (e)
    {
        var container = $(".wrapper-dropdown-3"); // YOUR CONTAINER SELECTOR
        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $("div.wrapper-dropdown-3").removeClass('active');
        }
    });

    $(document).on('click', '.filterinput', function () {
        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }
    });

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });

    $(document).on('keyup', '.filterinput', function () {
        var valThis = $(this).val().toLowerCase();
        var closer_section = $(this).closest('div').find('.section_ul > li');

        var noresult = 0;
        if (valThis == "") {
            closer_section.show();
            noresult = 1;
            $('.no-results-found').remove();
        } else {
            closer_section.each(function () {
                var text = $(this).text().toLowerCase();
                var match = text.indexOf(valThis);
                if (match >= 0) {
                    $(this).removeClass('d-none');
                    noresult = 1;
                    $('.no-results-found').remove();
                } else {
                    $(this).addClass('d-none');
                }
            });
        }
        ;
        if (noresult == 0) {
            closer_section.append('<li class="no-results-found"><?php echo $this->lang->line('no_result_found'); ?></li>');
        }
    });
</script>

<script type="text/javascript">   
    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
    });

    $('#myModalpa').on('hidden.bs.modal', function (e) {
        $(this).find('#formaddpa')[0].reset();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2();
    })

    // Reinitialize the patient AJAX Select2 after the modal becomes visible,
    // because Select2 computes width=0 when the element is inside display:none modal.
    // .off/.on with namespace prevents duplicate handlers if this script re-runs.
    $('#myModal').off('shown.bs.modal.select2init').on('shown.bs.modal.select2init', function () {
        var $sel = $('#addpatient_id');
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.select2({
            ajax: {
                url: "<?= base_url(); ?>admin/patient/getPatientListAjax",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    $('#case_reference_idd').val('');
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: false
            },
            dropdownParent: $('#myModal')
        });
        $sel.focus();
    });

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function get_PatientDetails(id) { 

       var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";
       $("#ajax_load").html("<center><img src='" + base_url + "'/>");
       var password = makeid(5)
       if(id==''){
            $("#ajax_load").html("");
             $("#patientDetails").addClass('d-none');
       }else{    
           $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                
                if (res) {
                
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass('d-none');
                    $('#patient_unique_id').text(res.id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#revisit_password').val(password);
                    $('#listname').text(res.patient_name+" ("+res.id+")");
                    $('#guardian').text(res.guardian_name);
                    $('#listnumber').text(res.mobileno);
                    $('#email').text(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    if(res.gender=='Female'){
						<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) {  ?>
                        $("#antenatal_div").removeClass('d-none');
						<?php } ?>
                    }	
                    
                    $('#patientname').val(res.patient_name);                    
                    $('#age').text(res.patient_age);
                    // $('#as_of_date').html(res.as_of_date);					
                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").text(res.bp);
                    $("#symptoms").text(res.symptoms);
                    $("#known_allergies").text(res.known_allergies);
                    $("#insuranceid").val(res.insurance_id);
                    $("#insurance_id").text(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);
                    $("#organisation_id").val(res.organisation_id);
                    $('#is_tpa').prop('checked', res.organisation_id ? true : false);
                    $("#organisation_name").text(res.organisation_name);
                    $("#validity").text(res.insurance_validity);
                    $("#identification_number").text(res.identification_number);
                    $("#address").text(res.address);
                    $("#note").text(res.note);
                    $("#height").text(res.height);
                    $("#weight").text(res.weight);
                    $("#genders").text(res.gender);
                    $("#marital_status").text(res.marital_status);                  
                    $("#blood_group").text(res.blood_group_name);
                    $("#allergies").text(res.known_allergies);
                    
                    $("#image").attr("src",res.image+ '<?php echo img_time(); ?>');

                    if(!res.getbarcode){
                        $("#show_barcode").addClass('d-none');
                        $("#getbarcode").removeAttr("src");
                    }else{
                        $("#show_barcode").removeClass('d-none');
                        $("#getbarcode").attr("src", res.getbarcode);
                    }
                    if(!res.getqrcode){
                        $("#show_qrcode").addClass('d-none');
                        $("#getqrcode").removeAttr("src");
                    }else{
                        $("#show_qrcode").removeClass('d-none');
                        $("#getqrcode").attr("src", res.getqrcode);
                    }
                    
                    // Hide li items with no value
                    var spansToCheck = ['li_guardian','li_gender','li_blood','li_marital','li_age','li_phone','li_email','li_address','li_allergies','li_note','li_tpa','li_tpa_id','li_validity','li_national_id'];
                    $.each(spansToCheck, function(i, lid){
                        var $li = $('#'+lid);
                        var $spans = $li.find('span:not(.pd-info-label)');
                        var hasVal = false;
                        $spans.each(function(){ if($(this).text().trim()) hasVal = true; });
                        $li.toggle(hasVal);
                    });

                    $('#formadd').find('select[name="payment_mode"]').val('Cash').trigger('change');
                    $('#formadd').find('select[name="payment_mode"] option[value="Cash"]').prop('selected', true);
                    $('#consultant_doctor').val(null).trigger('change');
                    $('.charge_category').val(null).trigger('change');
                    $('.charge').val(null).empty().trigger('change');

                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
                }
            }
        });
       }
    }
   
    function get_Charges() {
        var charge =$('.charge').val();
        $('#org_charge_amount').val('');
        var apply_amount=0;
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;

        if(is_tpa==1){
            var organisation_id=$("#organisation_id").val();
        }else if(is_tpa==0){
            var organisation_id=0;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: organisation_id},
            dataType: 'json',
            success: function (res) {

                if (res) {                  
                    $('#percentage').val(res.percentage);
                    if (organisation_id) {
                        if(res.percentage ==null){                             
                            apply_amount=parseFloat(res.org_charge);  
                        }else{
                            apply_amount=(parseFloat(res.org_charge) * res.percentage/100)+(parseFloat(res.org_charge));
                        }
                       
                        $('#org_charge_amount').val(res.org_charge);
                        $('#apply_charge').val(res.org_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#standard_charge').val(res.standard_charge);
                        $('#paid_amount').val(apply_amount);
                    } else {
                        if(res.percentage ==null){
                            apply_amount=parseFloat(res.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.standard_charge) * res.percentage/100)+(parseFloat(res.standard_charge));
                        }
                     
                        $('#standard_charge').val(res.standard_charge);
                        $('#apply_charge').val(res.standard_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#paid_amount').val(apply_amount);
                    }
                } else {
                    $('#standard_charge').val('');
                    $('#apply_charge').val('');
                }
            }
        });
    }

    function get_Chargesrevisit(id) {
        $("#standard_chargerevisit").html("standard_charge");
        var orgid = $("#revisit_organisation").val();
        if (id == '') {
            id = $("#revisit_doctor").val();
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctCharge',
            type: "POST",
            data: {doctor: id, organisation: orgid},
            dataType: 'json',
            success: function (res) {
             
                if (res) {
                    if (orgid) {
                        $('#revisit_amount').val(res.org_charge);
                        $('#standard_chargerevisit').val(res.standard_charge);
                    } else {
                        $('#standard_chargerevisit').val(res.standard_charge);
                        $('#revisit_amount').val(res.standard_charge);
                    }
                 
                } else {
                    $('#standard_chargerevisit').val('');
                    $('#revisit_amount').val('');
                }
            }
        });
    }
   
	$(document).on('select2:select','.charge_category',function(){
       var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        $('#percentage').val("");
        $('#apply_charge').val("");
        $('#standard_charge').val("");
        $('#schedule_charge').val("");                
        $('#org_id').val("");
        $('#org_charge_amount').val("");
        $('#apply_charge').val("");
        $('#apply_amount').val("");
        $('#paid_amount').val("");  
        $('#discount_percentage').val("") ;                     

		getchargecode(charge_category,"");

	});

    function getchargecode(charge_category,charge_id) {    
      var div_data = "<option value=''><?php echo $this->lang->line('select') ?></option>";
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
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);             
            }
        });
      }
    }

    $(document).on('input paste keyup','.apply_charge,.discount_percentage', function(e){ 
		update_amount($(e.target).closest('div.modal'));
	});

	function update_amount(object_model){      
        
        let  apply_charge= object_model.find('.apply_charge').val();
        let  discount_percentage=object_model.find('#discount_percentage').val();
		let discount= (parseFloat(apply_charge) * discount_percentage/100);
		let price_with_discount=((parseFloat(apply_charge))-(parseFloat(apply_charge) * discount_percentage/100));
        let  tax_percentage=object_model.find('#percentage').val();        
           
                 apply_amount=(parseFloat(price_with_discount) * tax_percentage/100)+price_with_discount;      
                 object_model.find('#apply_amount').val(apply_amount.toFixed(2));            
                 object_model.find('.paid_amount').val(apply_amount.toFixed(2));            
           
    }

    $(document).on('select2:select','.charge_category',function(){
		let closetst_div=$(this).closest('div.modal'); 
	});

    reset_form=(_modal_div)=>{
        _modal_div.find('.charge_category').val('').trigger('change.select2');
        _modal_div.find('.charge ').val('').empty().trigger('change');
        _modal_div.find('#percentage').val("");
        _modal_div.find('#apply_charge').val("");
        _modal_div.find('#standard_charge').val("");
        _modal_div.find('#schedule_charge').val("");                
        _modal_div.find('#org_id').val("");
        _modal_div.find('#org_charge_amount').val("");
        _modal_div.find('#apply_charge').val("");
        _modal_div.find('#apply_amount').val("");
        _modal_div.find('#paid_amount').val("");  
        _modal_div.find('#discount_percentage').val("") ;                          
	}

    $(document).on('select2:closing','#addpatient_id',function(){
        setTimeout(function(){
            $('input[name="case"]', '#myModal').focus();
        }, 0);
    });

    $(document).on('change','#addpatient_id',function(){
        let closetst_div=$(this).closest('div.modal');
        $('#is_tpa').prop('checked', false);
        reset_form(closetst_div);
    });

    $(document).on('change','#is_tpa',function(){
        let closetst_div=$(this).closest('div.modal');
        closetst_div.find('.charge_category').val(null).trigger('change');
        closetst_div.find('.charge').val(null).empty().trigger('change');
        closetst_div.find('#percentage,#apply_charge,#standard_charge,#schedule_charge,#org_id,#org_charge_amount,#apply_amount,#paid_amount,#discount_percentage').val('');
        closetst_div.find('select[name="payment_mode"]').val('Cash').trigger('change');
        closetst_div.find('select[name="payment_mode"] option[value="Cash"]').prop('selected', true);
    });

    $(document).on('select2:select','.charge',function(){
        let closetst_div=$(this).closest('div.modal');
        var charge=$(this).val();
        var patient_id=$("#patient_id").val();
        var apply_amount=0;
        $('#org_charge_amount').val('');
         $('#discount_percentage').val('');   
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;

        if(charge==''){
            reset_form(closetst_div);
            return false;
        }
       $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, 'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            beforeSend: function() {
                
          },
            success: function (res) {

                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
                        if(res.status == 2){
                            errorMsg(res.msg);
                        }
                    var tax=res.result.percentage;
                    var quantity=closetst_div.find('#qty').val();
                    console.log(quantity);
                    closetst_div.find('#percentage').val(tax);
                    closetst_div.find('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
                    closetst_div.find('#standard_charge').val(res.result.standard_charge);
                    closetst_div.find('#schedule_charge').val(res.result.org_charge);                
                    closetst_div.find('#org_id').val(res.result.org_charge_id);

                    if(res.display_tpa_charge){
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.org_charge) * res.result.percentage/100)+(parseFloat(res.result.org_charge));
                        }

                        closetst_div.find('#org_charge_amount').val(res.result.org_charge);
                        closetst_div.find('#apply_charge').val(res.result.org_charge);
                        closetst_div.find('#apply_amount').val(apply_amount.toFixed(2));
                        closetst_div.find('#paid_amount').val(apply_amount.toFixed(2));    
                    }else{
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.standard_charge) * res.result.percentage/100)+(parseFloat(res.result.standard_charge));
                        }
                        
                        closetst_div.find('#apply_charge').val(res.result.standard_charge);
                        closetst_div.find('#apply_amount').val(apply_amount.toFixed(2));
                        closetst_div.find('#paid_amount').val(apply_amount.toFixed(2));
                       
                    }
                } 
            }
        }); 

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

<script type="text/javascript">
    $(document).ready(function (e) {
        $("form#formadd button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#formadd").on('submit', (function (e) {
      var form_valid=true;
             var amount_to_be_paid=parseInt($("form#formadd #apply_amount").val());
             var amount_paying= parseInt($("form#formadd #paid_amount").val());
             if(amount_to_be_paid < amount_paying){
                 errorMsg("Invalid Amount");
                return false;
             }

             var sub_btn_clicked = $("button[type=submit][clicked=true]");       
            
             var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                 sub_btn_clicked.btnLoading() ; 
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
                         if(sub_btn_clicked_name === "save_print") {
                           printVisitBill(data.opd_id);
                        }
                        $('.opd_ajaxlist').DataTable().ajax.reload( null, false );
                        shModal('myModal').hide();
                    }
                      sub_btn_clicked.btnReset() ; 

                },
                 error: function(xhr) { // if error occured
       alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
       sub_btn_clicked.btnReset()  ;
    },
    complete: function() {
        sub_btn_clicked.btnReset();  
    }
            }); 
        }));
    });    
    
    $(document).ready(function (e) {
        $(".printsavedata").on('click', (function (e) {            
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function (i, val) {
                postData.append(val.name, val.value);
            });           

            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_revisit',
                type: "POST",
                data: postData,
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
                        patientid = $("#pid").val();
                        printVisitBill(patientid, data.id);
                    }
                    $("#formrevisitbtn").btnReset();
                },
                error: function () {
                    
                }
            });            
        }));
    });

    function printVisitBill(opdid) {
    $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opdid},
                dataType: 'json',
                   beforeSend: function() {
            
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
    }

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
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

    /**/
    function getRecord(id) 
    {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {recordid: id},
            dataType: 'json',
            success: function (data) {
                $("#patientid").val(data.id);
                $("#patient_name").val(data.patient_name);
                $("#contact").val(data.mobileno);
                $("#email").val(data.email);
                $("#age").val(data.age);
                $("#bp").val(data.bp);
                $("#month").val(data.month);
                $("#guardian_name").val(data.guardian_name);
                $("#appointment_date").val(data.appointment_date);
                $("#case").val(data.case_type);
                $("#symptoms").val(data.symptoms);
                $("#known_allergies").val(data.known_allergies);
                $("#refference").val(data.refference);
                $("#amount").val(data.amount);
                $("#tax").val(data.tax);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#height").val(data.height);
                $("#weight").val(data.weight);
                $("#updateid").val(id);
                $('select[id="blood_group"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $('select[id="consultant_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="payment_mode"] option[value="' + data.payment_mode + '"]').attr("selected", "selected");
                $('select[id="casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
            },
        })
    }

    function holdModal(modalId) {
        (function(){
            var _el=document.getElementById(modalId);
            if(!_el) return;
            if(_el.parentNode !== document.body) { document.body.appendChild(_el); }
            bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();
        })();
    }

</script>

<script type="text/javascript">
$(document).ready(function () {
    $('.filestyle,.filestyle2').dropify();
});
</script>
<script type="text/javascript"> 

 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $("#patientDetails").addClass('d-none');
     $('.select2-selection__rendered').html("");
     $('.cheque_div').addClass('d-none');
     $('#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('#formadd').find('select option:selected').removeAttr('selected');
     $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
     $('#formadd').find('select[name="payment_mode"]').val('Cash').trigger('change');
     $('#formadd').find('select[name="payment_mode"] option[value="Cash"]').prop('selected', true);
 });

$(".modalbtnpatient").click(function(){		
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

 $(document).on('select2:opening', '.charge, .charge_category, #consultant_doctor, #opd_icd10_group_filter', function() {
    var $patient = $('#addpatient_id');
    if ($patient.hasClass('select2-hidden-accessible')) {
        $patient.select2('close');
    }
    $patient.trigger('blur');
    $patient.next('.select2-container').find('.select2-selection').attr('tabindex', '-1');
 });

 $(document).on('select2:closing', '.charge, .charge_category, #consultant_doctor, #opd_icd10_group_filter', function() {
    setTimeout(function(){
        $('#addpatient_id').next('.select2-container').find('.select2-selection').attr('tabindex', '0').removeClass('select2-container--focus');
    }, 100);
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
        initDatatable('ajaxlist','admin/patient/getopddatatable',[],[],100);
		
		initDatatable('opd_ajaxlist','admin/patient/getvisitwiseopddatatable/1',[],[],100,
                        [                           
                             {"sWidth": "110px","bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},                          
                          
                        ]);		
        
    });
} ( jQuery ) )
	
</script>
<script>
	$(document).ready(function() {
        $(document).on('click', '.tab_button', function() {
			var type = $(this).data('type');

            initDatatable('opd_ajaxlist','admin/patient/getvisitwiseopddatatable/'+type,[],[],100);

        });
    });
</script>
<!-- //========datatable end===== -->
 <?php $this->load->view('admin/patient/patientaddmodal'); ?>

 <!-- //========opd tab js===== -->
 <script>
     $(document).on('click','.print_visit_bill',function(){
       
    var opd_id=$(this).data('opdId');   
           var $this = $(this);     
     $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opd_id},
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

     function getRecord_id(visitid) {        
        $('#prescription_title').html('<?php echo $this->lang->line('add_prescription'); ?>');
         $.ajax({
            url: base_url+'admin/prescription/addopdPrescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                ['compose-textareaneww', 'compose-textareass'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('.filestyle').dropify();
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                shModal('add_prescription').show();
             },
              complete: function() {
                  if (window.CKEDITOR) {
                      CKEDITOR.replace('compose-textareaneww', { allowedContent: true });
                      CKEDITOR.replace('compose-textareass', { allowedContent: true });
                  }
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         }
        });
    }

    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescription/' + visitid ,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });

        holdModal('prescriptionview');
    }

    function viewmanual_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescriptionmanual/' + visitid ,
            success: function (res) {
                $("#getdetails_prescriptionmanual").html(res);
                $('#edit_deleteprescriptionmanual').html("<?php if ($this->rbac->hasPrivilege('manual_prescription', 'can_view')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printprescriptionmanual(" + visitid + ")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
                $('#edit_deleteprescriptionmanual [data-bs-toggle="tooltip"]').each(function() { new bootstrap.Tooltip(this); });
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });
        holdModal('prescriptionviewmanual');
    }

     $(document).on('click','.move_opd',function(e){
            var data = $(this).data();
            var this_modal=$('#moveIPDModal');
            $('.title', this_modal).text(data.opdId);
            $('.btn-ok', this_modal).data('recordId', data.recordId);
        var btn= $(this);
             $.ajax({
                url: base_url+'admin/patient/moveIpdForm',
                type: "POST",
                data: {'visit_details_id':data.recordId},
                dataType: 'json',
               beforeSend: function () {
               btn.btnLoading();

                },
                success: function (data) {
                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                   $('.modal-body',this_modal).html(data.page);
                   $('.modal-body',this_modal).find('.select2').select2();

                    }
                  btn.btnReset();

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.btnReset();
                },
                complete: function () {
                     btn.btnReset();
                }
            });
            holdModal('moveIPDModal');
    });
    
$(document).on('click','.get_opd_detail',function(){
    var visitid=$(this).data('recordId');
    var opdid = $(this).data('opdId');
           var $this = $(this);
     $.ajax({
                url: base_url+'admin/patient/getopdDetails',
                type: "POST",
                data: {visit_id: visitid,opd_id:opdid},
                dataType: 'json',
                   beforeSend: function() {
                  $this.btnLoading();
                   },
                success: function (data) {
                  if (!data || data.status != 1) {
                      alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                      return;
                  }
                  holdModal('viewModal');
                  var patient_id = data.patient_id;
                    $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('visit', 'can_edit')) { ?><a href='#' class='btn btn-sm btn-light' onclick='editRecord(" + visitid + ")' data-bs-target='#editModal' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('visit', 'can_delete')) { ?><a href='#'  class='btn btn-sm btn-light delete_opd' data-bs-toggle='tooltip' data-patient_id="+patient_id+" data-record-id="+opdid+" title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                    $('#edit_delete [data-bs-toggle="tooltip"]').each(function() { new bootstrap.Tooltip(this); });
                            $('#viewModal .modal-body').html(data.page);
                },

                 error: function(xhr) {
              console.error('getopdDetails error:', xhr.status, xhr.responseText);
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

    $(document).on('change', '.findinghead', function () {
        $this = $(this);
        var head_id = $(this).val();
        div_data="";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getfinding',
            data: {'head_id': head_id},
            
            success: function (res) {              
                $("#finding_description").val(res);               
            },
            
        });
    });

    $(document).on('click','.delete_opd',function(){
     let patient_id=$(this).data('patient_id');
     let id=$(this).data('recordId');

      if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: baseurl+'admin/patient/deleteOPD',
                type: "POST",
                data: {opdid: id,'patient_id':patient_id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    if(data.total_remain <= 0){
                        window.location.href = baseurl+'admin/patient/search';
                    }else{

                    window.location.reload(true);
                    }
                }
            })
        }
    });

     function editRecord(visitid) {
      
        $.ajax({ 
            url: '<?php echo base_url(); ?>admin/patient/getopdvisitdetails',
            type: "GET",
            data: {visitid: visitid},
            dataType: 'json',
            success: function (data) {
             
                $('#visitid').val(visitid);
                $('#visit_transaction_id').val(data.transaction_id);
                $('#customfield').html(data.custom_fields_value);
                $("#patientid").val(data.patient_id);
                $("#patientname").val(data.patient_name);
                $("#appointmentdate").val(data.appointment_date);
                $("#edit_case").val(data.case_type);
                $("#symptoms_description").val(data.symptoms);
                $("#edit_casualty").val(data.casualty);                
                $("#edit_refference").val(data.refference);
                $("#edit_revisit_note").html(data.note);
                $("#edit_amount").val(data.apply_charge);
                $('select[id="edit_oldpatient"] option[value="' + data.patient_old + '"]').attr("selected", "selected");               
                $("#edit_opdid").val(data.opdid);
              
                if(data.is_antenatal==1){
                     $("#is_antenatal").prop('checked',true);
                }
                
                 $("#editknown_allergies").val(data.visit_known_allergies);
                 $("#edit_visit_payment_date").val(data.payment_date);
                 $("#edit_visit_payment").val(data.amount);
                 $("#visit_payment_mode").val(data.payment_mode).prop('selected');
                 $(".visit_payment_mode").trigger('change');
                 $("#edit_visit_cheque_no").val(data.cheque_no);
                 $("#edit_visit_cheque_date").val(data.cheque_date);
                 $("#edit_payment_note").val(data.payment_note);
                 shModal("viewModal").hide(); 
                $('select[id="edit_consdoctor"] option[value="'+data.cons_doctor+'"]').attr("selected","selected");
                $(".select2").select2().select2('val', data.cons_doctor);
                holdModal('editModal');
            },
        });
    }
 </script>
 <script>
    function edit_prescription(id) {
        $("#prescription_title").html('<?php echo $this->lang->line('edit_prescription'); ?>');
        $.ajax({
            url: base_url+'admin/prescription/editopdPrescription',
            dataType:'JSON',
            data:{'prescription_id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                // Destroy before replacing HTML so CKEditor can restore original textareas cleanly
                ['compose-textareanew', 'compose-textareas'].forEach(function(id) {
                    if (window.CKEDITOR && CKEDITOR.instances[id]) { try { CKEDITOR.instances[id].destroy(true); } catch(e) {} }
                });
                shModal('prescriptionview').hide();
                $('.modal-body',"#add_prescription").html(res.page);
                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
                $('.modal-body',"#add_prescription").find('.filestyle,.filestyle2').dropify();
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });

                prescription_rows=medicineTable.find('tr').length+1;

                $('#tableID tr').each(function(){
                    var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                    var dosage_id = $(this).find("td input.post_dosage_id").val();
                    $(this).find('.medicine_name').select2().select2('val', post_medicine_id);
                    $(this).find('.medicine_dosage').select2().select2('val', dosage_id);
                })

                shModal('add_prescription').show();
                },

                complete: function() {
                    if (window.CKEDITOR) {
                        CKEDITOR.replace('compose-textareanew', { allowedContent: true });
                        CKEDITOR.replace('compose-textareas', { allowedContent: true });
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                }

            });
    }



 </script>
 <script>
    
    $(document).on('change', '.findingtype', function () {
        $this = $(this);

         var selected_id = $("#item_name").val();
        
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        var finding_id = $(this).val();        
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/findingbycategory',
            data: {'finding_id': finding_id,'selected_id':selected_id},
            dataType: 'JSON',
            
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
            },
            success: function (data) {
                section_ul.append(data.record);

            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () {

            }            
        });
    });

    $(document).on('change', '.findinghead', function () {

        $this = $(this);
        var head_id = $(this).val();       
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getfinding',
            data: {'head_id': head_id},            
            success: function (res) {              
                $("#finding_description").val(res);               
            },            
        });
    });
</script>
<script>
    $(document).ready(function (e) {
        $("#visitformedit").on('submit', (function (e) {
            $("#formeditbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/opd_detail_update',
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
</script>
<script>
    $(document).ready(function (e) {
               $("form#form_prescription button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#form_prescription").on('submit', (function (e) {         
           let _this_form=$(this);
             var sub_btn_clicked = $("button[type=submit][clicked=true]",_this_form);   
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
        
            e.preventDefault();
            if (window.CKEDITOR) {
                for (var inst in CKEDITOR.instances) { CKEDITOR.instances[inst].updateElement(); }
            }
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_opd_prescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                //cache: false,
                processData: false,
                  beforeSend: function() {
                 sub_btn_clicked.btnLoading() ; 
                 },
                success: function (data) {
                    if (data.status == "0") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                       
                       if(sub_btn_clicked_name == "save_print") {  
                                                  
                            printprescription(data.visitid,true);
                        }
                        successMsg(data.message);
                        $('.opd_ajaxlist').DataTable().ajax.reload( null, false );
                        shModal('add_prescription').hide();
                    }
                      sub_btn_clicked.btnReset()  ;
                },
                 error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                   sub_btn_clicked.btnReset()  ;
                },
                 complete: function() {
                     sub_btn_clicked.btnReset();  
                 }
            });
        }));
    });

    function printprescription(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/printPrescription' ,
            type: 'GET',
            data: { visitid: visitid },
            dataType:"JSON",
            success: function (result) {
                popup(result.page);
            }
        });
    }   

    function printprescriptionmanual(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/prescriptionmanual_print/' + visitid,
            type: 'POST',
            success: function (result) {
                popup(result);
            }
        });
    }
</script>

<script type="text/javascript">
   function getDosages(medicine_category_id){
    var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";
   var sss='<?php echo json_encode($category_dosage); ?>';
   var aaa=JSON.parse(sss);
  
   if (aaa[medicine_category_id]){
    $.each(aaa[medicine_category_id], function(key, item) 
    {
      dosage_opt+="<option value='"+item.id+"'>"+item.dosage+" ("+item.unit+")</option>";
    });
}
return dosage_opt;
   }
</script> 

<script>
     $(document).on('select2:select','.medicine_category',function(){      
      getMedicine($(this),$(this).val(),0);
       selected_medicine_category_id =$(this).val();   
       var medicine_dosage=getDosages(selected_medicine_category_id);
       $(this).closest('tr').find('.medicine_dosage').html(medicine_dosage);
    }); 
	
    $(document).on('select2:select','.medicine_name',function(){   
        var row_id_val= $(this).data('rowid');
        var medid= $(this).val();
        if(medid!=""){
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_stockinfo",
            data: {'pharmacy_id': $(this).val()},
            dataType: 'json',
            success: function (res) {
                $('#stock_info_'+row_id_val).html(res);
            }
        });
    }else{
        $('#stock_info_'+row_id_val).html("");

    }
    }); 

    function getMedicine(med_cat_obj,val,medicine_id){

      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('select') ?></option>");

            }, 
            success: function (res) {
                var div_data="<option value=''><?php echo $this->lang->line('select') ?></option>";
                $.each(res, function (i, obj)
                {
                    var sel = "";
                            if (medicine_id == obj.id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + ">" + obj.medicine_name + "</option>";
                });
           
                medicine_colomn.html(div_data);
                medicine_colomn.select2("val", medicine_id);
               
            }
        });
}    

    function getMedicineDosage(id) {        
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];       
        div_data = '';

        $("#search-dosage" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#search-dosage' + id).select2("val", +id);

        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_dosage",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.dosage + "'>" + obj.dosage +""+ obj.dosage +"</option>";
                });
                $("#search-dosage" + id).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#search-dosage' + id).append(div_data); 
                $('#search-dosage' + id).select2("val", '');

            }
        });
    }    
</script>
<script>

 var prescription_rows=0;
 $(document).on('click','.add-record',function(){
        var rowCount = $('#tableID tr').length;
        if(rowCount==0){
          prescription_rows=1
        }else{
          prescription_rows=rowCount+1;
        }

        var row = "<tr id='row" + prescription_rows + "'><td><input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><input type='hidden' name='medicine_cat_"+prescription_rows+"' value='1'><select class='form-control select2 medicine_name' data-rowId='"+prescription_rows+"' style='width:100%' name='medicine_"+prescription_rows+"' id='search-query"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineName as $mkey => $mvalue) { ?><option value='<?php echo $mvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($mvalue["medicine_name"])); ?></option><?php } ?></select><small id='stock_info_"+prescription_rows+"'></small></td><td><select class='form-control select2 medicine_dosage' style='width:100%' name='dosage_"+prescription_rows+"' id='search-dosage"+prescription_rows+"'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($dosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($dvalue["dosage"] . ' (' . $dvalue["unit"] . ')')); ?></option><?php } ?></select></td><td><select class='form-control select2 interval_dosage' style='width:100%' name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage"+prescription_rows+"'><option value='<?php echo set_value('interval_dosage_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($dvalue["name"])); ?></option><?php } ?></select></td><td><select class='form-control select2 duration_dosage' style='width:100%' name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage"+prescription_rows+"'><option value='<?php echo set_value('duration_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo str_replace(["\r", "\n"], ' ', addslashes($dvalue["name"])); ?></option><?php } ?></select></td><td><textarea name='instruction_"+prescription_rows+"' rows='1' class='form-control opd-instruction-area'></textarea></td><td class='text-center'><button type='button' data-row-id='"+prescription_rows+"' class='btn btn-sm btn-outline-danger closebtn delete_row_prescription'><i class='fa fa-remove'></i></button></td></tr>";
      $('#tableID').append(row).find('.select2').select2();
      
    }); 

//delete medicine row from prescription
    $(document).on('click','.delete_row_prescription',function(e){       
        var del_row_id=$(this).data('rowId');        
        var result = confirm("Delete Confirm?");
        if (result) {
            $("#row" + del_row_id).html("");
        }
  });
//delete medicine row from prescription


    function delete_row(id) {        
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;        
        var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");         
            if (result == true) {                 
                 $("#row" + id).html("");
            }        
    }
    
    function getBed(bed_group, bed = '') {
        var div_data = "";
        $('#bed_no').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
      
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bed/getbedbybedgroup',
            type: "POST",
            data: {bed_group: bed_group, active: 'yes'},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if ((bed != '') && (bed == obj.id)) {
                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
                });
                $("#bed_no").html("<option value=''><?= $this->lang->line('select') ?></option>");
                $('#bed_no').append(div_data);
                $("#bed_no").select2().select2('val', bed);
            }
        });
    }
    
    $("form#form_confirm-move").on('submit', (function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            if(confirm('<?php echo $this->lang->line('are_you_sure_want_to_move_patient'); ?>')) {
            var btn = $(this).find("button[type=submit]:focus" );
            var move_opd_id=btn.data('recordId');
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                btn.btnLoading();
                },
                success: function (data) {
                 
                    var move_id = data.move_id ;
                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                       $('.ajaxlistvisit').DataTable().ajax.reload();
                 window.location.assign("<?php echo base_url(); ?>admin/patient/ipdprofile/"+move_id); 
                    }
                  btn.btnReset();

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.btnReset();
                },
                complete: function () {
                     btn.btnReset();
                }
            });
            }
        }));
</script>

<!-- ICD-10 group filter (OPD Add) -->
<script>
$('#opd_icd10_group_filter').on('change', function() {
    var group_id = $(this).val();
    $.ajax({
        url: '<?php echo base_url(); ?>admin/icd10/get_codes_by_group',
        type: 'POST',
        data: { group_id: group_id },
        dataType: 'json',
        success: function(data) {
            var $select = $('#opd_icd_code_ids');
            $select.empty();
            $.each(data, function(i, c) {
                $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
            });
            $select.trigger('change.select2');
        }
    });
});
</script>

 <!-- //========opd tab js===== -->

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>

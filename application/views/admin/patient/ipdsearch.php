<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('ipd_patient'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('ipd_patient', 'can_add')) { ?>
                                <a data-bs-toggle="modal" onclick="holdModal('myModal')" id="addp" class="btn btn-primary btn-sm addpatient"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_patient'); ?></a> 
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('discharged_patients', 'can_view')) { ?>
                                <a  href="<?php echo base_url() ?>admin/patient/discharged_patients" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('discharged_patient'); ?></a> 
                            <?php } ?>
                        </div>    
                    </div><!-- /.card-header -->                   
                        <div class="card-body">
                            <div class="download_label"><?php echo $this->lang->line('ipd_patient'); ?></div>
                        <div class="table-responsive overflow-visible-lg">
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('ipd_patient'); ?>">
                                <thead>
                                    <tr> 
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('generated_by'); ?></th>
                                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <th><?php echo $this->lang->line('bed'); ?></th>
										<?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')) { ?>
                                        <th><?php echo $this->lang->line('is_antenatal'); ?></th>
										<?php } ?>
                                        <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            } 
                                        } ?> 
                                        <th class="text-end"><?php echo $this->lang->line('credit_limit') . " (" . $currency_symbol . ")"; ?></th>
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

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url("admin/patient/add_inpatient") ?>" enctype="multipart/form-data" method="post">
                <input id="patientuniqueid" name="patientunique_id" type="hidden" value="" />
                <input name="patient_id" id="patient_id" type="hidden" />
                <input name="email" id="pemail" type="hidden" />
                <input name="mobileno" id="pmobileno" type="hidden" />
                <input name="patient_name" id="patientname" type="hidden" />
                <input type="hidden" id="password" name="password">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_patient'); ?></h5>
                    <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax sh-inp-200" id="addpatient_id" name=''>
                    </select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                        <a data-bs-toggle="modal" id="addpip" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body">
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
                                                    <span class="sh-info-value" id="national_identification_number"></span>
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
                                            <img class="opd-pd-photo-lg d-none" src="<?php echo $this->media_storage->getImageURL('uploads/patient_images/no_image.png'); ?>" id="image" alt="<?php echo $this->lang->line('patient'); ?>">
                                            <div class="opd-pd-initials-lg d-none" id="image_initials" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sh-form-card mb-2">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('symptoms'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_type'); ?></label>
                                                    <select name='symptoms_type' id="act" class="form-control select2 act" multiple>
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>
                                            <input name="rows[]" type="hidden" value="1">
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul sh-dropdown-fullw">
                                                            <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="symptoms_description" name="symptoms"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('icd10_groups'); ?></label>
                                                    <select id="icd10_group_filter" class="form-control select2">
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
                                                    <select name="icd_code_ids[]" id="icd_code_ids" class="form-control select2" multiple>
                                                        <?php if (!empty($icd10_codes)) { foreach ($icd10_codes as $c) { ?>
                                                        <option value="<?php echo $c['id']; ?>">[<?php echo html_escape($c['icd_code']); ?>] <?php echo html_escape($c['icd_description']); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" class="form-control"><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="">
                                                <?php echo display_custom_fields('ipd'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- ./col-lg-8 -->
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="sh-form-card mb-3">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-calendar-alt"></i> <?php echo $this->lang->line('admission_date'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('admission_date'); ?><small class="req"> *</small></label>
                                                    <input id="datetimepicker" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('case'); ?></label>
                                                    <input class="form-control" type='text' name='case' />
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('tpa'); ?></label>
                                                    <input type="text" name="showorganisation_name" id="showorganisation_name" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('casualty'); ?></label>
                                                    <select name="casualty" id="casualty" class="form-control">
                                                        <option value="<?php echo $this->lang->line('yes'); ?>"><?php echo $this->lang->line('yes'); ?></option>
                                                        <option selected="" value="<?php echo $this->lang->line('no'); ?>"><?php echo $this->lang->line('no'); ?></option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('old_patient'); ?></label>
                                                    <select name="old_patient" class="form-control">
                                                        <option value="<?php echo $this->lang->line('yes'); ?>"><?php echo $this->lang->line('yes'); ?></option>
                                                        <option selected="" value="<?php echo $this->lang->line('no'); ?>"><?php echo $this->lang->line('no'); ?></option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('credit_limit') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></label>
                                                    <input class="form-control" type='text' name='credit_limit' value="<?php echo $setting[0]['credit_limit']; ?>" />
                                                    <span class="text-danger"><?php echo form_error('credit_limit'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('reference'); ?></label>
                                                    <input class="form-control" type='text' name='refference' />
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sh-form-card mb-3">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><i class="fas fa-user-md"></i> <?php echo $this->lang->line('consultant_doctor'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('consultant_doctor'); ?><small class="req"> *</small></label>
                                                    <select class="form-control select2" <?php if ($disable_option == true) { echo "disabled"; } ?> id='consultant_doctor' name='consultant_doctor'>
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php if ($disable_option == true) { ?>
                                                    <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select; ?>">
                                                    <?php } ?>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('bed_group'); ?></label>
                                                    <select class="form-control" name='bed_group_id' onchange="getBed(this.value)">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($bedgroup_list as $key => $bedgroup) { ?>
                                                        <option value="<?php echo $bedgroup["id"]; ?>"><?php echo $bedgroup["name"] . " - " . $bedgroup["floor_name"]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('bed_number'); ?><small class="req"> *</small></label>
                                                    <select class="form-control select2" name='bed_no' id='bed_no'>
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('bed_no'); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult" id="live_consult" class="form-control">
                                                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                        <option value="<?php echo $yesno_key; ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="col-sm-6" id="antenatal_div" class="d-none">
                                                <div class="mb-3">
                                                    <div class="sh-check-box">
                                                        <input type="checkbox" class="align-top" name="is_for_antenatal" id="is_for_antenatal" value="1">
                                                        <label for="is_for_antenatal"><?php echo $this->lang->line('is_for_antenatal'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- ./col-lg-4 -->
                        </div><!--./row--> 
                    </div>
                </div>
                <div class="modal-footer sticky-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- revisit -->
<div class="modal fade sh-modal sh-modal-accent" id="revisitModal" tabindex="-1" aria-labelledby="revisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisitModalLabel"><?php echo $this->lang->line('patient_information') ; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formrevisit" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label>
<?php echo $this->lang->line('patient') . " " . $this->lang->line('id'); ?></label> 
                                        <input id="revisit_id" disabled name="patient_id" placeholder="" type="text" class="form-control"  value="<?php echo set_value('roll_no'); ?>" />
                                        <span class="text-danger"><?php echo form_error('patient_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="revisit_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" name="id" id="pid">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="revisit_contact" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('appointment') . " " . $this->lang->line('date'); ?></label>
                                        <input id="revisit_date" name="appointment_date" placeholder="" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('case'); ?></label>
                                        <div><input class="form-control" type='text' id="revisit_case" name='revisit_case' />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('case'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('casualty'); ?></label>
                                        <div>
                                            <select name="casualty" id="revisit_casualty" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <option value="yes"><?php echo $this->lang->line('yes') ?></option>
                                                <option value="no"><?php echo $this->lang->line('no') ?></option>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('case'); ?></span></div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('old') . " " . $this->lang->line('patient'); ?></label>
                                        <div>
                                            <select name="old_patient" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <option value="yes"><?php echo $this->lang->line('yes') ?></option>
                                                <option value="no"><?php echo $this->lang->line('no') ?></option>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('case'); ?></span></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="email"><?php echo $this->lang->line('symptoms'); ?></label> 
                                        <textarea name="symptoms" id="revisit_symptoms" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                    </div> 
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label> 
                                        <textarea name="known_allergies" id="revisit_allergies" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                    </div> 
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="email"><?php echo $this->lang->line('address'); ?></label> 
                                        <textarea name="address" id="revisit_address" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                    </div> 
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                                        <textarea name="note" id="revisit_note" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                    </div>
                                </div>   
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('reference'); ?></label>
                                        <div><input class="form-control" id="revisit_refference" type='text' name='refference' />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('refference'); ?></span></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
                                                <?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?></label>
                                        <div><select class="form-control select2" <?php
                                                if ($disable_option == true) {
                                                    echo "disabled";
                                                }
                                                ?> name='consultant_doctor' id="revisit_doctor">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>   
<?php } ?>
                                            </select>
<?php if ($disable_option == true) { ?>
                                                <input type="hidden" name="consultant_doctor" value="<?php echo $doctor_select ?>">
<?php } ?>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('refference'); ?></span></div>
                                </div>  
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('amount'); ?></label> 
                                        <input name="amount" type="text" class="form-control" id="revisit_amount" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('tax'); ?></label> 
                                        <input type="text" name="tax" id="revisi_tax" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('payment') . " " . $this->lang->line('mode'); ?></label> 
                                        <select name="payment_mode" id="revisit_payment" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($payment_mode as $payment_key => $payment_value) {
    ?>
                                                <option value="<?php echo $payment_key ?>"><?php echo $payment_value ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><!--./row-->
                            <button type="submit" class="btn btn-info float-end"><?php $this->lang->line('save'); ?></button>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
        </div>
    </div>
</div>

<!-- dd -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('patient_information') ; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req red"> *</small> 
                                        <input id="patient_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" id="updateid" name="updateid">
                                        <input type="hidden" id="opdid" name="opdid">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('guardian_name'); ?></label>
                                        <input type="text" id="guardian_name" name="guardian_name" value="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="gender" name="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
    <?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                        <select name="marital_status" id="marital_status" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($marital_status as $mkey => $mvalue) {
    ?>
                                                <option value="<?php echo $mkey ?>"><?php echo $mvalue ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="contact" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('patient') . " " . $this->lang->line('photo'); ?></label>
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
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label> <?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="bloodgroup" name="blood_group">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($bloodgroup as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
    <?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('age'); ?></label>
                                        <div class="sh-age-input-row">
                                            <input type="text" placeholder="<?php echo $this->lang->line('year') ?>" name="age" id="age" class="form-control age-y" value="<?php echo set_value('age'); ?>">
                                            <input type="text" placeholder="<?= $this->lang->line('month') ?>" name="month"  id="month"value="<?php echo set_value('month'); ?>" class="form-control age-m">
                                        </div>
                                    </div>
                                </div>                                   
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('height'); ?></label>
                                        <input type="text" id="height" name="height" value="<?php echo set_value('height'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('weight'); ?></label>
                                        <input type="text" id="weight" name="weight" value="<?php echo set_value('weight'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
                                                <?php echo $this->lang->line('organisation'); ?></label>
                                        <div><select class="form-control" name='organisation' >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($organisation as $orgkey => $orgvalue) {
    ?>
                                                    <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>   
<?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="mb-3">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('credit_limit'); ?></label>
                                        <div><input type="text" name="credit_limit" id="credit_limit" class="form-control">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                    </div>
                                </div>
                            </div><!--./row-->
                            <button type="submit" class="btn btn-info float-end"><?php echo $this->lang->line('save'); ?></button>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
        </div>
    </div>
</div>

<script>
    var rows = 2;
    var link = 1;
	
    $(document).on('click', '.custom-select', function () {
        var currents = $(this);
        if (currents.parent().find('div.section_checkboxs').is(":visible")) {
            currents.parent().find('div.section_checkboxs').hide();
        } else {
            currents.parent().find('div.section_checkboxs').show();
        }
    });

    function toggleFillColor(obj) {
        if ($(obj).prop('checked') == true) {
            console.log($(obj).closest('li'));
            $(obj).closest('li').css("background-color", '#ddd');
        } else {
            $(obj).closest('li').css("background-color", '#FFF');
        }
    }

    $(document).on("click", ".checkbox", function (e) {
        var checkboxObj = $(this).children("input");
        toggleFillColor(checkboxObj);
    });

    $(document).click(function (e) {
        e.stopPropagation();
        var container = $(".a");
        //check if the clicked area is dropDown or not
        if (container.has(e.target).length === 0) {
            $("div.section_checkboxs").addClass('d-none');
        }
    })
</script>
<script type="text/javascript">
    $(document).on('click', '.add-btn', function () {
        var s = "";
        s += "<div class='row'>";
        s += "<input name='rows[]' type='hidden' value='" + rows + "'>";
        s += "<div class='col-md-6'>";
        s += "<div class='mb-3'>";
        s += "<label for='act'><?= $this->lang->line('act') ?></label>";
        s += "<select class='form-control act select2' id='act' name='act" + rows + "' data-row_id='" + rows + "'>";
        s += "<option value=''>--Select--</option>";
        s += $('#act-template').html();
        s += "</select>";
        s += "<small class='text text-danger help-inline'></small>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-5'>";
        s += "<label for='validationDefault02'><?= $this->lang->line('section') ?></label>";
        s += "<div id='dd' class='wrapper-dropdown-3'>";
        s += "<input class='form-control filterinput' type='text'>";
        s += "<ul class='dropdown scroll150 section_ul'>";
        s += "<li><label class='checkbox'>--Select--</label></li>";
        s += "</ul>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-1'>";
        s += "<div class='mb-3'>";
        s += "<label for='removebtn'>&nbsp;</label>";
        s += "<button type='button' class='form-control btn btn-sm btn-danger remove_row'><i class='fa fa-remove'></i></button>";
        s += "</div>";
        s += "</div>";
        s += "</div>";
        $(".multirow").append(s);
        $('.select2').select2();
        link = 2;
        rows++;
    });
</script>

<script type="text/html" id="act-template">    
   <?php foreach ($symptomsresulttype as $dkey => $dvalue) {   ?>
        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option> 
        <?php
    }
    ?>
</script>  

<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();        
        var row_id = $this.data('row_id');
        var section_ul = $(this).closest('div.row').find('ul.section_ul');

        var sel_option = "";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val, 'row_id': row_id},
            dataType: 'JSON',
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
                $("div.wrapper-dropdown-3").removeClass('active');
            },
            success: function (data) {           
                section_ul.append(data.record);
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
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
            closer_section.append('<li class="no-results-found">No results found.</li>');
        }
    });
</script>
<script type="text/javascript">

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
	
    $(function () {
        $('#easySelectable').easySelectable();
    });

    function add_more() {
        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var div = "<td><input type='text' name='date[]' class='form-control datetime'></td><td><select name='doctor[]' class='select2' ><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($doctors as $key => $value) { ?><option value='<?php echo $value["id"] ?>'><?php echo $value["name"] . ' ' . $value["surname"] ?></option><?php } ?></select></td><td><textarea name='instruction[]' class='form-control sh-instruction-textarea'></textarea></td><td><input type='text' name='insdate[]' class='form-control date'></td>";
        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
        $('.select2').select2();
    }

    function delete_row(id) {
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");
    }
</script>
<script type="text/javascript">
    /*
     Author: mee4dy@gmail.com
     */
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
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_inpatient',
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
                        shModal('myModal').hide();
                        $('.ajaxlist').DataTable().ajax.reload();
                    }
                    $("#formaddbtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formrevisit").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_revisit',
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
    $(document).ready(function (e) {
        $("#formaddip").on('submit', (function (e) {
            $("#formaddipbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addpatient',
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
                    $("#formaddipbtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });

    function renderIpdSearchAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl !== '' && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $("#image").attr("src", imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $("#image_initials").text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?'
                : parts.length === 1 ? parts[0].charAt(0)
                : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $("#image").addClass('d-none').removeAttr('src');
            $("#image_initials").text(initials.toUpperCase()).removeClass('d-none');
        }
    }

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
        
       if(id==''){ 
        $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
        }else{
        var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";

        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        var password = makeid(5);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {

                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass('d-none');
                    $('#patientuniqueid').val(res.patient_unique_id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#patientname').val(res.patient_name);
                    $('#pemail').val(res.email);
                    $('#pmobileno').val(res.mobileno);
                    $('#listname').html(res.patient_name+" ("+res.id+")");
                    $('#guardian').html(res.guardian_name);
                    $('#listnumber').html(res.mobileno);
                    $('#email').html(res.email);
                    if(res.gender=='Female'){
                        $("#antenatal_div").removeClass('d-none');
                    } else {
                        $("#antenatal_div").addClass('d-none');
                    }
                    $('#age').html(res.patient_age);
                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").html(res.bp);
                    $("#symptoms").html(res.symptoms);
                    $("#known_allergies").html(res.known_allergies);
                    $("#organisation_name").html(res.organisation_name);
                    $("#showorganisation_name").val(res.organisation_name);
                    $("#insurance_id").html(res.insurance_id);
                    $("#validity").html(res.insurance_validity);
                    $("#national_identification_number").html(res.identification_number);
                    $("#address").html(res.address);
                    $("#note").html(res.note);
                    $("#height").html(res.height);
                    $("#weight").html(res.weight);
                    $("#genders").html(res.gender);
                    $("#marital_status").html(res.marital_status);
                    $("#blood_group").html(res.blood_group_name);
                    $("#allergies").html(res.known_allergies);
                    renderIpdSearchAvatar(res.image, res.patient_name);
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
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass('d-none');
                }
            }
        });
         }
    }
	
    function getRecord(id) {

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getIpdDetails',
            type: "POST",
            data: {recordid: id},
            dataType: 'json',
            success: function (data) {
                $("#patientid").val(data.patient_unique_id);
                $("#patient_name").val(data.patient_name);
                $("#contact").val(data.mobileno);
                $("#email").val(data.email);
                $("#age").val(data.age);
                $("#bloodgroup").val(data.blood_group);
                $("#guardian_name").val(data.guardian_name);
                $("#appointment_date").val(data.appointment_date);
                $("#case").val(data.case_type);
                $("#symptoms").val(data.symptoms);
                $("#known_allergies").val(data.known_allergies);
                $("#refference").val(data.refference);
                $("#credit_limit").val(data.credit_limit);
                $("#amount").val(data.amount);
                $("#tax").val(data.tax);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#height").val(data.height);
                $("#weight").val(data.weight);
                $("#updateid").val(id);
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $('select[id="consultant_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $(".select2").select2().select2('val', data.cons_doctor);
                $('select[id="payment_mode"] option[value="' + data.payment_mode + '"]').attr("selected", "selected");
                $('select[id="casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
            },
        })
    }

    function get_symptoms(id) {
       
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getsymptoms',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {                   
                        $('#symptoms_description').val(res.description);                      
                } else{
                    $('#symptoms_description').val("");
                }  
            }
        });
    }

    function getRevisitRecord(id) {

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {recordid: id},
            dataType: 'json',
            success: function (data) {
                $("#revisit_id").val(data.patient_unique_id);
                $("#revisit_name").val(data.patient_name);
                $("#revisit_contact").val(data.mobileno);
                $("#revisit_date").val(data.appointment_date);
                $("#revisit_case").val(data.case_type);
                $("#pid").val(id);
                $("#revisit_allergies").val(data.known_allergies);
                $("#revisit_refference").val(data.refference);
                $("#revisit_amount").val(data.amount);
                $("#revisit_symptoms").val(data.symptoms);
                $("#revisi_tax").val(data.tax);
                $("#revisit_address").val(data.address);
                $("#revisit_note").val(data.note);
                $('select[id="revisit_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="revisit_payment"] option[value="' + data.payment_mode + '"]').attr("selected", "selected");
                $('select[id="revisit_casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
            },
        })
    }

    $(document).ready(function (e) {
        $("#consultant_register").on('submit', (function (e) {
    var doctor_id = $("#doctor_field").val();
    $("#doctor_set").val(doctor_id);    
            $("#consultant_registerbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_consultant_instruction',
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
                    $("#consultant_registerbtn").btnReset();
                },
                error: function () {
                }
            });
        }));
    });

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

    function add_inpatient(bed, bedgroup) {
        $('select[name="bed_group_id"] option[value="' + bedgroup + '"]').attr("selected", "selected");
        getBed(bedgroup, bed);      
        holdModal('myModal');
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId); if(_el) bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

    $(document).ready(function () {
        // Bootstrap 5 uses vanilla JS Popover (not jQuery plugin)
        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var a = el.closest('a');
                    var inner = a ? a.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
        });
    });

</script>
<script type="text/javascript">

    // Reinitialize patient AJAX Select2 after modal is visible (fixes 0-width computed in hidden modal)
    $('#myModal').on('shown.bs.modal', function () {
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
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            },
            dropdownParent: $('#myModal')
        });
        $sel.focus();
    });

   $('#myModal').on('hidden.bs.modal', function () {
     $("#consultant_doctor").select2("val", "");
     $(".act").select2("val", "");
     $(".patient_list_ajax ").select2("val", "");
     $("#patientDetails").addClass('d-none');
     $('#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('#formadd').find('select option:selected').removeAttr('selected');
     $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
   });

$(".modalbtnpatient").click(function(){	
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

function refreshmodal(){
	$('#formaddpa').trigger("reset");
	var table = document.getElementById("tableID");
    var table_len = (table.rows.length);
	for (i = 1; i < table_len; i++) {
		delete_row(i);
	}
}

// ICD-10 group filter
$('#icd10_group_filter').on('change', function() {
    var group_id = $(this).val();
    $.ajax({
        url: '<?php echo base_url(); ?>admin/icd10/get_codes_by_group',
        type: 'POST',
        data: { group_id: group_id },
        dataType: 'json',
        success: function(data) {
            var $select = $('#icd_code_ids');
            $select.empty();
            $.each(data, function(i, c) {
                $select.append('<option value="' + c.id + '">[' + c.icd_code + '] ' + c.icd_description + '</option>');
            });
            $select.trigger('change.select2');
        }
    });
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/patient/getipddatatable',[],[],100);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div>
    <input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
    <input type="hidden" name="case_reference_id" value="<?php echo $patient['case_reference_id']; ?>">
    <input type="hidden" name="opd_id" value="<?php echo $patient['opdid']; ?>">

    <div class="row g-2">

        <!-- LEFT: Patient Info + Symptoms (col-7) -->
        <div class="col-lg-7 border-end">

            <!-- Card 1: Patient Info -->
            <div class="sh-form-card mb-2">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('patient_details'); ?></span>
                </div>
                <div class="sh-info-grid">
                    <div class="row g-0">
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('patient'); ?></span>
                            <span class="sh-info-value highlight"><?php echo composePatientName($patient['patient_name'], $patient['patient_id']); ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('guardian'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['guardian_name']; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['gender'] ? $this->lang->line(strtolower($patient['gender'])) : '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['blood_group_name'] ?: '—'; ?></span>
                        </div>
                    </div>
                    <div class="row g-0 sh-row-divider">
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['marital_status'] ? $this->lang->line(strtolower($patient['marital_status'])) : '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                            <span class="sh-info-value"><?php echo $this->customlib->get_patient_current_age($patient['patient_id']); ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['mobileno'] ?: '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['email'] ?: '—'; ?></span>
                        </div>
                    </div>
                    <div class="row g-0 sh-row-divider">
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['organisation_name'] ?: '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['insurance_id'] ?: '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['insurance_validity'] ? $this->customlib->YYYYMMDDTodateFormat($patient['insurance_validity']) : '—'; ?></span>
                        </div>
                        <div class="col-6 col-md-3 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('national_identification_number'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['identification_number'] ?: '—'; ?></span>
                        </div>
                    </div>
                    <div class="row g-0 sh-row-divider">
                        <div class="col-12 sh-info-item">
                            <span class="sh-info-label"><?php echo $this->lang->line('any_known_allergies'); ?></span>
                            <span class="sh-info-value"><?php echo $patient['known_allergies'] ?: '—'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Symptoms -->
            <div class="sh-form-card">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('symptoms'); ?></span>
                </div>
                <div class="p-2">
                    <div class="row g-2">
                        <input name="rows[]" type="hidden" value="1">
                        <div class="col-sm-6">
                            <div class="mb-2 select2-full-width">
                                <label class="form-label"><?php echo $this->lang->line('symptoms_type'); ?></label>
                                <select name="symptoms_type" id="act" class="form-control form-control-sm select2 act" multiple>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($symptomsresulttype as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['symptoms_type']; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('symptoms_title'); ?></label>
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
                                <textarea class="form-control form-control-sm" id="move_ipd_symptoms" name="symptoms"><?php echo $patient['symptoms']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                <textarea name="note" rows="3" class="form-control form-control-sm"><?php echo set_value('note'); ?></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <?php echo display_custom_fields('ipd'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /col-lg-8 -->

        <!-- RIGHT: IPD Details (col-5) -->
        <div class="col-lg-5">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('ipd_details'); ?></span>
                </div>
                <div class="p-2">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('admission_date'); ?></label><small class="req"> *</small>
                                <input id="admission_date" name="appointment_date" type="text" class="form-control form-control-sm datetime" value="<?php echo $this->customlib->YYYYMMDDHisTodateFormat($patient['appointment_date']); ?>" />
                                <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('case'); ?></label>
                                <input class="form-control form-control-sm" type="text" name="case" value="<?php echo $patient['case_type']; ?>" />
                                <span class="text-danger"><?php echo form_error('case'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('casualty'); ?></label>
                                <select name="casualty" id="casualty" class="form-control form-control-sm">
                                    <option value="Yes" <?php echo ($patient['casualty'] == 'Yes') ? 'selected' : ''; ?>><?php echo $this->lang->line('yes'); ?></option>
                                    <option value="No"  <?php echo ($patient['casualty'] == 'No')  ? 'selected' : ''; ?>><?php echo $this->lang->line('no'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('old_patient'); ?></label>
                                <select name="old_patient" class="form-control form-control-sm">
                                    <option value="<?php echo $this->lang->line('yes'); ?>"><?php echo $this->lang->line('yes'); ?></option>
                                    <option selected value="<?php echo $this->lang->line('no'); ?>"><?php echo $this->lang->line('no'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('credit_limit') . ' (' . $currency_symbol . ')'; ?></label><small class="req"> *</small>
                                <input class="form-control form-control-sm" type="text" name="credit_limit" value="<?php echo $setting[0]['credit_limit']; ?>" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('reference'); ?></label>
                                <input class="form-control form-control-sm" type="text" name="refference" value="<?php echo $patient['refference']; ?>" />
                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2 select2-full-width">
                                <label class="form-label"><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                <select class="form-control form-control-sm select2" id="consultant_doctor" name="consultant_doctor">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue['id']; ?>" <?php echo set_select('consultant_doctor', $dvalue['id'], ($patient['cons_doctor'] == $dvalue['id']) ? true : false); ?>><?php echo $dvalue['name'] . ' ' . $dvalue['surname'] . ' (' . $dvalue['employee_id'] . ')'; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('bed_group'); ?></label>
                                <select class="form-control form-control-sm" name="bed_group_id" onchange="getBed(this.value,'','yes')">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($bedgroup_list as $key => $bedgroup) { ?>
                                    <option value="<?php echo $bedgroup['id']; ?>"><?php echo $bedgroup['name'] . ' - ' . $bedgroup['floor_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2 select2-full-width">
                                <label class="form-label"><?php echo $this->lang->line('bed_number'); ?></label><small class="req"> *</small>
                                <select class="form-control form-control-sm select2" name="bed_no" id="bed_no">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('bed_no'); ?></span>
                            </div>
                        </div>
                        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                        <div class="col-sm-6">
                            <div class="mb-2">
                                <label class="form-label"><?php echo $this->lang->line('live_consultation'); ?></label>
                                <select name="live_consult" id="live_consult" class="form-control form-control-sm">
                                    <option value="yes" <?php echo ($patient['live_consult'] == 'yes') ? 'selected' : ''; ?>><?php echo $this->lang->line('yes'); ?></option>
                                    <option value="no"  <?php echo ($patient['live_consult'] == 'no')  ? 'selected' : ''; ?>><?php echo $this->lang->line('no'); ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col-sm-6" id="antenatal_div">
                            <div class="mb-2">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_for_antenatal" id="is_for_antenatal" value="1"
                                        <?php if ($patient['is_antenatal'] == 1) { echo 'checked'; } ?>
                                        <?php if ($patient['is_antenatal'] == 1) { echo 'readonly'; } ?>
                                        autocomplete="off">
                                    <label class="form-check-label" for="is_for_antenatal"><?php echo $this->lang->line('is_for_antenatal'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /col-lg-4 -->

    </div><!-- /row -->
</div>

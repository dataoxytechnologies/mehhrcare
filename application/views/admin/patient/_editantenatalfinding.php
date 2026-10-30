<input type="hidden" name="edit_antenatal_id" value="<?php echo $result['antenatal_id']; ?>">
<input type="hidden" name="ipd" id="ipd" value="">
<input type="hidden" name="visit_detail_id" id="visit_detail_id" value="<?php echo $result['visit_details_id']; ?>">
<input type="hidden" name="anteexam_id" id="anteexam_id" value="<?php echo $result['anteexam_id']; ?>" />
<input type="hidden" name="antenatal_id" id="antenatal_id" value="<?php echo $result['antenatal_id']; ?>" />
<div class="row mt5">
        <!-- Card 1: History of Present Pregnancy -->
        <div class="col-lg-8 col-md-12">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('history_of_present_pregnancy'); ?></span>
                </div>
                <div class="p-2">
                    <div class="row mt5">
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('bleeding'); ?></label>
                            <input name="bleeding" id="bleeding" type="text" class="form-control form-control-sm" value="<?php echo $result['bleeding']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('headache'); ?></label>
                            <input name="headache" id="headache" type="text" class="form-control form-control-sm" value="<?php echo $result['headache']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('pain'); ?></label>
                            <input name="pain" id="pain" type="text" class="form-control form-control-sm" value="<?php echo $result['pain']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('constipation'); ?></label>
                            <input name="constipation" id="constipation" type="text" class="form-control form-control-sm" value="<?php echo $result['constipation']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('vomiting'); ?></label>
                            <input name="vomiting" id="vomiting" type="text" class="form-control form-control-sm" value="<?php echo $result['vomiting']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('cough'); ?></label>
                            <input name="cough" id="cough" type="text" class="form-control form-control-sm" value="<?php echo $result['cough']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('haemoroids'); ?></label>
                            <input name="haemoroids" id="haemoroids" type="text" class="form-control form-control-sm" value="<?php echo $result['haemoroids']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('discharge'); ?></label>
                            <input name="discharge" id="discharge" type="text" class="form-control form-control-sm" value="<?php echo $result['discharge']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('oedema'); ?></label>
                            <input name="oedema" id="oedema" type="text" class="form-control form-control-sm" value="<?php echo $result['oedema']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('urinary_symptoms'); ?></label>
                            <input name="urinary_symptoms" id="urinary_symptoms" type="text" class="form-control form-control-sm" value="<?php echo $result['urinary_symptoms']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('vaginal'); ?></label>
                            <input name="vaginal" id="vaginal" type="text" class="form-control form-control-sm" value="<?php echo $result['vaginal']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('weight'); ?></label>
                            <input name="weight" id="weight" type="text" class="form-control form-control-sm" value="<?php echo $result['antenatal_weight']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('height'); ?></label>
                            <input name="height" id="height" type="text" class="form-control form-control-sm" value="<?php echo $result['antenatal_height']; ?>" />
                        </div>
                        <div class="col-sm-3 col-3">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input name="antenatal_date" id="antenatal_date" type="text" class="form-control form-control-sm datetime" value="<?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['antenatal_date'], $time_format); ?>" />
                        </div>
                    </div>
                    <div class="row mt5">
                        <div class="col-md-6 col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('condition'); ?></label>
                            <textarea name="condition" id="condition" rows="2" class="form-control form-control-sm"><?php echo $result['general_condition']; ?></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('special_findings_and_remark'); ?></label>
                            <textarea name="special_finding_remarks" id="special_finding_remarks" rows="2" class="form-control form-control-sm"><?php echo $result['finding_remark']; ?></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('pelvic_examination'); ?></label>
                            <textarea name="pelvic_examination" id="pelvic_examination" rows="2" class="form-control form-control-sm"><?php echo $result['pelvic_examination']; ?></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('sp'); ?></label>
                            <textarea name="sp" id="sp" rows="2" class="form-control form-control-sm"><?php echo $result['sp']; ?></textarea>
                        </div>
                    </div>
                    <div class="row mt5">
                        <?php echo display_custom_fields('antenatal', $result['anteexam_id']); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2: Antenatal Examination -->
        <div class="col-lg-4 col-md-12">
            <div class="sh-form-card h-100">
                <div class="sh-card-header">
                    <span class="sh-card-header-title"><?php echo $this->lang->line('antenatal_examination'); ?></span>
                </div>
                <div class="p-2">
                    <div class="row mt5">
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('uter_size'); ?></label>
                            <input name="uter_size" id="uter_size" type="text" class="form-control form-control-sm" value="<?php echo $result['uter_size']; ?>" />
                            <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('uterus_size'); ?></label>
                            <input name="uterus_size" id="uterus_size" type="text" class="form-control form-control-sm" value="<?php echo $result['uterus_size']; ?>" />
                            <span class="text-danger"><?php echo form_error('case'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('presentation_position'); ?></label>
                            <input name="presentation_position" id="presentation_position" type="text" class="form-control form-control-sm" value="<?php echo $result['presentation_position']; ?>" />
                            <span class="text-danger"><?php echo form_error('case'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('presenting_part_to_brim'); ?></label>
                            <input name="presentation_brim" id="presentation_brim" type="text" class="form-control form-control-sm" value="<?php echo $result['brim_presentation']; ?>" />
                            <span class="text-danger"><?php echo form_error('case'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('foeta_heart'); ?></label>
                            <input name="foeta_heart" id="foeta_heart" type="text" class="form-control form-control-sm" value="<?php echo $result['foeta_heart']; ?>" />
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('blood_pressure'); ?></label>
                            <input name="blood_pressure" id="blood_pressure" type="text" class="form-control form-control-sm" value="<?php echo $result['blood_pressure']; ?>" />
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('antenatal_oedema'); ?></label>
                            <input name="antenatal_oedema" type="text" class="form-control form-control-sm" value="<?php echo $result['antenatal_oedema']; ?>" />
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('antenatal_weight'); ?></label>
                            <input name="antenatal_weight" type="text" class="form-control form-control-sm" value="<?php echo $result['antenatal_weight']; ?>" />
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('urine_sugar'); ?></label>
                            <input name="urine" type="text" class="form-control form-control-sm" value="<?php echo $result['urine_sugar']; ?>" />
                            <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('urine_albumen'); ?></label>
                            <input name="urine_aaibumen" type="text" class="form-control form-control-sm" value="<?php echo $result['urine']; ?>" />
                            <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('remark'); ?></label>
                            <textarea name="remark" id="remark" rows="2" class="form-control form-control-sm"><?php echo $result['remark']; ?></textarea>
                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1 small fw-medium"><?php echo $this->lang->line('next_visit'); ?></label>
                            <textarea name="next_visit" id="next_visit" rows="2" class="form-control form-control-sm"><?php echo $result['next_visit']; ?></textarea>
                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

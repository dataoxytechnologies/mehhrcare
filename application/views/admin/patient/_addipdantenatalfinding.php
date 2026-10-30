<input type="hidden" name="ipdid" id="ipdid" value="<?php echo $ipdid; ?>">

<div class="row g-3">
    <div class="col-lg-8">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fas fa-baby me-1"></i> <?php echo $this->lang->line('history_of_present_pregnancy'); ?></span>
            </div>
            <div class="px-2 py-3">
                <div class="row g-3">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('bleeding'); ?></label>
                            <input type="text" name="bleeding" id="bleeding" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('headache'); ?></label>
                            <input type="text" name="headache" id="headache" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('pain'); ?></label>
                            <input type="text" name="pain" id="pain" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('constipation'); ?></label>
                            <input type="text" name="constipation" id="constipation" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('urinary_symptoms'); ?></label>
                            <input type="text" name="urinary_symptoms" id="urinary_symptoms" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('vomiting'); ?></label>
                            <input type="text" name="vomiting" id="vomiting" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('cough'); ?></label>
                            <input type="text" name="cough" id="cough" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('vaginal'); ?></label>
                            <input type="text" name="vaginal" id="vaginal" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('discharge'); ?></label>
                            <input type="text" name="discharge" id="discharge" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('height'); ?></label>
                            <input type="text" name="height" id="height" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('oedema'); ?></label>
                            <input type="text" name="Oedema" id="Oedema" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('haemoroids'); ?></label>
                            <input type="text" name="haemoroids" id="haemoroids" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('weight'); ?></label>
                            <input type="text" name="weight" id="weight" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input type="text" name="antenatal_date" id="antenatal_date" class="form-control datetime">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('condition'); ?></label>
                            <textarea name="condition" id="condition" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('special_findings_and_remark'); ?></label>
                            <textarea name="special_finding_remarks" id="special_finding_remarks" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('pelvic_examination'); ?></label>
                            <textarea name="pelvic_examination" id="pelvic_examination" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('sp'); ?></label>
                            <textarea name="sp" id="sp" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sh-form-card h-100">
            <div class="sh-card-header">
                <span class="sh-card-header-title"><i class="fas fa-stethoscope me-1"></i> <?php echo $this->lang->line('antenatal_examination'); ?></span>
            </div>
            <div class="px-2 py-3">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('uter_size'); ?></label>
                            <input type="text" name="uter_size" id="uter_size" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('uterus_size'); ?></label>
                            <input type="text" name="uterus_size" id="uterus_size" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('presentation_position'); ?></label>
                            <input type="text" name="presentation_position" id="presentation_position" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('presenting_part_to_brim'); ?></label>
                            <input type="text" name="presentation_brim" id="presentation_brim" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('foeta_heart'); ?></label>
                            <input type="text" name="foeta_heart" id="foeta_heart" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('blood_pressure'); ?></label>
                            <input type="text" name="blood_pressure" id="blood_pressure" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('antenatal_oedema'); ?></label>
                            <input type="text" name="antenatal_oedema" id="antenatal_oedema" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('urine_sugar'); ?></label>
                            <input type="text" name="urine" id="urine" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('urine_aaibumen'); ?></label>
                            <input type="text" name="urine_aaibumen" id="urine_aaibumen" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('antenatal_weight'); ?></label>
                            <input type="text" name="antenatal_weight" id="antenatal_weight" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('remark'); ?></label>
                            <textarea name="remark" id="remark" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-label"><?php echo $this->lang->line('next_visit'); ?></label>
                            <textarea name="next_visit" id="next_visit" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <?php echo display_custom_fields('antenatal'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

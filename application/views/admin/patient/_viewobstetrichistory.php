<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<div class="sh-form-card m-2 mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-user me-1"></i><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo html_escape($result['patient_name']); ?> <span class="text-muted fw-normal">(<?php echo html_escape($result['patient_unique_id']); ?>)</span></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-hourglass-half me-1"></i><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-venus-mars me-1"></i><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result['gender'])); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-tint me-1"></i><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['blood_group']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-phone me-1"></i><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['mobileno']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-envelope me-1"></i><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['email']); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fas fa-history me-1"></i> <?php echo $this->lang->line('previous_obstetric_history'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('place_of_delivery'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['place_of_delivery']); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['pregnancy_complications']); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                    <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result['obstetric_gender'])); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('birth_status'); ?></span>
                    <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result['alive_dead'])); ?></span>
                </div>
                <?php if ($result['alive_dead'] == 'dead') { ?>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['date']); ?></span>
                </div>
                <?php } ?>
            </div>
            <div class="col-6 border-start">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('duration_of_pregnancy'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['pregnancy_duration']); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('birth_weight'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['birth_weight']); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('infant_feeding'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['infant_feeding']); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider"></div>
                <?php if ($result['alive_dead'] == 'dead') { ?>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('death_cause'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['death_cause']); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('previous_medical_history'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['previous_medical_history']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('special_instruction'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['special_instruction']); ?></span>
            </div>
        </div>
    </div>
</div>

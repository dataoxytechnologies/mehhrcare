<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result['gender'])); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['mobileno']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['email']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['guardian_name']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result['marital_status'])); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['blood_group_name']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['known_allergies']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('old_patient'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line($result['patient_old']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['address']); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('checkup_id'); ?></span>
                <span class="sh-info-value highlight"><?php echo html_escape($this->customlib->getPatientSessionPrefixByType('checkup_id')) . (int)$result['id']; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('opd_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($this->customlib->getPatientSessionPrefixByType('opd_no')) . (int)$result['opd_details_id']; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['case_reference_id']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('appointment_date'); ?></span>
                <span class="sh-info-value"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('case'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['case_type']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('casualty'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line($result['casualty']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['refference']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['organisation_name']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></span>
            </div>
        </div>
        <?php if (!empty($result['note'])): ?>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['note']); ?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($result['symptoms'])): ?>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('symptoms'); ?></span>
                <span class="sh-info-value"><?php echo nl2br(html_escape($result['symptoms'])); ?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($fields)): ?>
        <div class="row g-0 sh-row-divider">
            <?php foreach ($fields as $fields_value):
                if ($fields_value->type == 'link') {
                    $display_field = '<a href="' . html_escape($result[$fields_value->name]) . '" target="_blank">' . html_escape($result[$fields_value->name]) . '</a>';
                } else {
                    $display_field = html_escape($result[$fields_value->name]);
                }
            ?>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                <span class="sh-info-value"><?php echo $display_field; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

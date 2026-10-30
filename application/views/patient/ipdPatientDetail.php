<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-user me-1"></i><?php echo $this->lang->line('patient_details'); ?></span>
    </div>
    <div class="sh-info-grid">

        <div class="row g-0">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
            </div>
        </div>

        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo ($result['gender'] ? $this->lang->line(strtolower($result['gender'])) : '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['patient_age']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['blood_group_name']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                <span class="sh-info-value"><?php echo ($result['marital_status'] ? $this->lang->line(strtolower($result['marital_status'])) : '&mdash;'); ?></span>
            </div>
        </div>

        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['mobileno']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['email']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['guardian_name']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['address']) ?: '&mdash;'); ?></span>
            </div>
        </div>

        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['doctor_name']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('admission_date'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['date']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('bed_group'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['bed_group']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('bed_number'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['bed_name']) ?: '&mdash;'); ?></span>
            </div>
        </div>

        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('case'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['case_type']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['organisation_name']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('old_patient'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['patient_old']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['refference']) ?: '&mdash;'); ?></span>
            </div>
        </div>

        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['known_allergies']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('casualty'); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result['casualty']) ?: '&mdash;'); ?></span>
            </div>
            <div class="col-6 col-md-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('symptoms'); ?></span>
                <span class="sh-info-value"><?php echo ($result['symptoms'] ? $result['symptoms'] : '&mdash;'); ?></span>
            </div>
        </div>

        <?php if (!empty($fields)) { ?>
        <div class="row g-0 sh-row-divider">
            <?php foreach ($fields as $fields_key => $fields_value) { ?>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                <span class="sh-info-value"><?php echo (html_escape($result[$fields_value->name]) ?: '&mdash;'); ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

    </div>
</div>

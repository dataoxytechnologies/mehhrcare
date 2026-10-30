<div class="p-1">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
        </div>
        <div class="sh-info-grid">

            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                    <span class="sh-info-value highlight"><?php echo !empty($result['case_reference_id']) ? html_escape($result['case_reference_id']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('recheckup_id'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['id']) ? html_escape($this->customlib->getPatientSessionPrefixByType('checkup_id')) . (int)$result['id'] : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('opd_no'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['opd_details_id']) ? html_escape($this->customlib->getPatientSessionPrefixByType('opd_no')) . (int)$result['opd_details_id'] : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('old_patient'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['patient_old']) ? $this->lang->line($result['patient_old']) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                    <span class="sh-info-value highlight"><?php echo !empty($result['patient_id']) ? composePatientName($result['patient_name'], $result['patient_id']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['guardian_name']) ? html_escape($result['guardian_name']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['gender']) ? $this->lang->line(strtolower($result['gender'])) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['marital_status']) ? $this->lang->line(strtolower($result['marital_status'])) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['mobileno']) ? html_escape($result['mobileno']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['email']) ? html_escape($result['email']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['address']) ? html_escape($result['address']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['day']) ? $this->customlib->get_patient_current_age($result['patient_id']) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['blood_group_name']) ? html_escape($result['blood_group_name']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['known_allergies']) ? html_escape($result['known_allergies']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_date'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['appointment_date']) ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('case'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['case_type']) ? html_escape($result['case_type']) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('casualty'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['casualty']) ? $this->lang->line($result['casualty']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['refference']) ? html_escape($result['refference']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['organisation_name']) ? html_escape($result['organisation_name']) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['name']) ? composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['note']) ? html_escape($result['note']) : '—'; ?></span>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('symptoms'); ?></span>
                    <span class="sh-info-value"><?php echo !empty($result['symptoms']) ? nl2br(html_escape($result['symptoms'])) : '—'; ?></span>
                </div>
            </div>

            <?php if (!empty($fields)) { ?>
            <div class="row g-0 sh-row-divider">
                <?php foreach ($fields as $fields_value) {
                    $display_field = '';
                    if (!empty($result[$fields_value->name])) {
                        $display_field = $fields_value->type == 'link'
                            ? '<a href="' . html_escape($result[$fields_value->name]) . '" target="_blank">' . html_escape($result[$fields_value->name]) . '</a>'
                            : html_escape($result[$fields_value->name]);
                    }
                ?>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                    <span class="sh-info-value"><?php echo $display_field ?: '—'; ?></span>
                </div>
                <?php } ?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

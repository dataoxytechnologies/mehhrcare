<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="p-1">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('visit_details'); ?></span>
        </div>
        <div class="sh-info-grid">

            <!-- Row 1 -->
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                    <span class="sh-info-value highlight"><?php echo $result['case_reference_id'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('recheckup_id'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->getSessionPrefixByType('checkup_id') . $result['id']; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('opd_no'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $result['opd_details_id']; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('old_patient'); ?></span>
                    <span class="sh-info-value"><?php echo $this->lang->line($result['patient_old']) ?: '—'; ?></span>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                    <span class="sh-info-value highlight"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('guardian_name'); ?></span>
                    <span class="sh-info-value"><?php echo $result['guardian_name'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                    <span class="sh-info-value"><?php echo isset($result['gender']) ? $this->lang->line(strtolower($result['gender'])) : '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></span>
                    <span class="sh-info-value"><?php echo isset($result['marital_status']) ? $this->lang->line(strtolower($result['marital_status'])) : '—'; ?></span>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                    <span class="sh-info-value"><?php echo $result['mobileno'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                    <span class="sh-info-value"><?php echo $result['email'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                    <span class="sh-info-value"><?php echo $result['address'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']); ?></span>
                </div>
            </div>

            <!-- Row 4 -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                    <span class="sh-info-value"><?php echo $result['blood_group_name'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                    <span class="sh-info-value"><?php echo $result['known_allergies'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_date'); ?></span>
                    <span class="sh-info-value"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('case'); ?></span>
                    <span class="sh-info-value"><?php echo $result['case_type'] ?: '—'; ?></span>
                </div>
            </div>

            <!-- Row 5 -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('casualty'); ?></span>
                    <span class="sh-info-value"><?php echo $this->lang->line($result['casualty']) ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('reference'); ?></span>
                    <span class="sh-info-value"><?php echo $result['refference'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                    <span class="sh-info-value"><?php echo $result['organisation_name'] ?: '—'; ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                    <span class="sh-info-value"><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></span>
                </div>
            </div>

            <!-- Row 6: Note (full width) -->
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                    <span class="sh-info-value"><?php echo $result['note'] ?: '—'; ?></span>
                </div>
            </div>

            <!-- Row 7: Symptoms (full width) -->
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('symptoms'); ?></span>
                    <span class="sh-info-value"><?php echo $result['symptoms'] ? nl2br($result['symptoms']) : '—'; ?></span>
                </div>
            </div>

            <!-- Custom fields -->
            <?php if (!empty($fields)) { ?>
            <div class="row g-0 sh-row-divider">
                <?php foreach ($fields as $fields_key => $fields_value) {
                    $display_field = $result[$fields_value->name];
                    if ($fields_value->type == 'link') {
                        $display_field = '<a href="' . $result[$fields_value->name] . '" target="_blank">' . $result[$fields_value->name] . '</a>';
                    }
                ?>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $fields_value->name; ?></span>
                    <span class="sh-info-value"><?php echo $display_field ?: '—'; ?></span>
                </div>
                <?php } ?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

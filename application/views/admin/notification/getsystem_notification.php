<div class="sh-form-card mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('edit_notification'); ?></span>
    </div>
    <div class="p-3">
        <p class="lead_template"><?php echo $this->lang->line($record->event); ?></p>
        <input type="hidden" name="temp_id" value="<?php echo (int)$record->id; ?>">
        <div class="mb-3">
            <label class="form-label"><?php echo $this->lang->line('subject'); ?></label>
            <input type="text" name="template_subject" id="template_subject" class="form-control" value="<?php echo html_escape($record->subject); ?>">
            <div class="text text-danger template_subject_error"></div>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $this->lang->line('staff_message'); ?></label>
            <textarea id="form_message" name="staff_message" class="form-control" rows="7"><?php echo html_escape($record->staff_message); ?></textarea>
            <div class="text text-danger staff_message_error"></div>
            <div class="hide_in_read mt-2">
                <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                <b><?php echo html_escape($record->variables); ?></b>
            </div>
        </div>
        <?php if (!in_array($record->event, $is_patient_notification)): ?>
        <div class="mb-0">
            <label class="form-label"><?php echo $this->lang->line('patient_message'); ?></label>
            <textarea name="patient_message" class="form-control" rows="7"><?php echo html_escape($record->patient_message); ?></textarea>
            <div class="text text-danger patient_message_error"></div>
            <div class="hide_in_read mt-2">
                <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                <b><?php echo html_escape($record->variables); ?></b>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="sh-form-card mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('template'); ?></span>
    </div>
    <div class="p-3">
        <p class="lead_template"><?php echo $this->lang->line($record->type); ?></p>
        <input type="hidden" name="temp_id" value="<?php echo (int)$record->id; ?>">
        <div class="mb-3">
            <label class="form-label"><?php echo $this->lang->line('subject'); ?></label>
            <input type="text" name="template_subject" id="template_subject" class="form-control" value="<?php echo html_escape($record->subject); ?>">
            <div class="text text-danger template_subject_error"></div>
        </div>
        <div class="mb-3">
            <label class="form-label"><?php echo $this->lang->line('template_id'); ?></label>&nbsp;<small class="text-danger">(<?php echo $this->lang->line('this_field_require_for_smsgateway'); ?>)</small>
            <input type="text" name="template_id" id="template_id" class="form-control" value="<?php echo html_escape($record->template_id); ?>">
            <div class="text text-danger template_id_error"></div>
        </div>
        <?php if ($this->module_lib->hasModule('whatsapp_messaging') && $this->module_lib->hasActive('whatsapp_messaging')): ?>
            <?php if ($record->display_whatsapp): ?>
            <div class="mb-3">
                <label class="form-label"><?php echo $this->lang->line('whatsapp_template_id'); ?></label>&nbsp;<small class="text-danger">(<?php echo $this->lang->line('this_field_require_for_whatsapp'); ?>)</small>
                <input type="text" name="whatsapp_template_id" id="whatsapp_template_id" class="form-control" value="<?php echo html_escape($record->whatsapp_template_id); ?>">
                <div class="text text-danger whatsapp_template_id_error"></div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="mb-0">
            <label class="form-label"><?php echo $this->lang->line('template'); ?></label>
            <textarea id="form_message" name="template_message" class="form-control" rows="7"><?php echo html_escape($record->template); ?></textarea>
            <div class="text text-danger template_message_error"></div>
            <div class="hide_in_read mt-2">
                <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                <b><?php echo html_escape($record->variables); ?></b>
            </div>
        </div>
    </div>
</div>

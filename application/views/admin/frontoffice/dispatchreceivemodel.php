<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('to_title'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($data['to_title']); ?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('reference_no'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($data['reference_no']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($data['address']); ?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($data['note']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('from_title'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($data['from_title']); ?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($data['date']); ?></span>
            </div>
        </div>
    </div>
</div>

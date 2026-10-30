<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('complain'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['id'])){ echo html_escape($complaint_data['id']); }?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('complain_type'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['complaint_type'])){ echo html_escape($complaint_data['complaint_type']); }?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('source'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['source'])){ echo html_escape($complaint_data['source']); }?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('name'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['name'])){ echo html_escape($complaint_data['name']); }?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['contact'])){ echo html_escape($complaint_data['contact']); }?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['date'])){ echo $this->customlib->YYYYMMDDTodateFormat($complaint_data['date']); }?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('description'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['description'])){ echo html_escape($complaint_data['description']); }?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('action_taken'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['action_taken'])){ echo html_escape($complaint_data['action_taken']); }?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('assigned'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['assigned'])){ echo html_escape($complaint_data['assigned']); }?></span>
            </div>
            <div class="col-6 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('note'); ?></span>
                <span class="sh-info-value"><?php if(!empty($complaint_data['note'])){ echo html_escape($complaint_data['note']); }?></span>
            </div>
        </div>
    </div>
</div>

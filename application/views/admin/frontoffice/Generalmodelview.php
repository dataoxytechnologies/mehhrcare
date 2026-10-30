<div class="p-1">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-phone me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
        </div>
        <div class="sh-info-grid">

            <!-- Row 1: Name | Phone | Call Type | Call Duration -->
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('name'); ?></div>
                    <div class="sh-info-value highlight"><?php echo html_escape($Call_data['name']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('phone'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($Call_data['contact']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('call_type'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($this->lang->line(strtolower($Call_data['call_type']))) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('call_duration'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($Call_data['call_duration']) ?: '—'; ?></div>
                </div>
            </div>

            <!-- Row 2: Date | Follow Up Date | — | — -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($this->customlib->YYYYMMDDTodateFormat($Call_data['date'])) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('next_follow_up_date'); ?></div>
                    <div class="sh-info-value"><?php echo $Call_data['follow_up_date'] ? html_escape($this->customlib->YYYYMMDDTodateFormat($Call_data['follow_up_date'])) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label">&nbsp;</div>
                    <div class="sh-info-value">&nbsp;</div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label">&nbsp;</div>
                    <div class="sh-info-value">&nbsp;</div>
                </div>
            </div>

            <?php if (!empty($Call_data['description'])) { ?>
            <!-- Description -->
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('description'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($Call_data['description']); ?></div>
                </div>
            </div>
            <?php } ?>

            <?php if (!empty($Call_data['note'])) { ?>
            <!-- Note -->
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($Call_data['note']); ?></div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<div class="p-1">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-user me-1 opacity-75"></i><?php echo $this->lang->line('details'); ?></span>
        </div>
        <div class="sh-info-grid">

            <!-- Row 1: Purpose | Name | Phone | ID Card -->
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('purpose'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['purpose']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('name'); ?></div>
                    <div class="sh-info-value highlight"><?php echo html_escape($data['name']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('phone'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['contact']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('id_card'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['id_proof']) ?: '—'; ?></div>
                </div>
            </div>

            <!-- Row 2: Visit To | Related To | No. of Persons | Date -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('visit_to'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['visit_to']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('related_to'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['related_to']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('number_of_person'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['no_of_pepple']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($this->customlib->YYYYMMDDTodateFormat($data['date'])) ?: '—'; ?></div>
                </div>
            </div>

            <!-- Row 3: In Time | Out Time -->
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('in_time'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['in_time']) ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('out_time'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['out_time']) ?: '—'; ?></div>
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

            <?php if (!empty($data['note'])) { ?>
            <!-- Note row -->
            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('note'); ?></div>
                    <div class="sh-info-value"><?php echo html_escape($data['note']); ?></div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>

    <?php if (!empty($data['image'])) { ?>
    <div class="sh-form-card mt-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-paperclip me-1 opacity-75"></i><?php echo $this->lang->line('attach_document'); ?></span>
        </div>
        <div class="p-2">
            <a href="<?php echo site_url('admin/visitors/download/' . $data['id']); ?>" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1">
                <i class="fa fa-download text-primary"></i>
                <span><?php echo html_escape($data['image']); ?></span>
            </a>
        </div>
    </div>
    <?php } ?>

</div>

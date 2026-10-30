<div class="sh-form-card m-2 mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-user me-1"></i><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo html_escape($result->patient_name); ?> <span class="text-muted fw-normal">(<?php echo html_escape($result->id); ?>)</span></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('ipd_no'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->ipdid); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getPatientAge($result->age, $result->month, $result->day); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result->gender)); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->blood_group); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->mobileno); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->email); ?></span>
            </div>
            <div class="col-12 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->known_allergies); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card m-2 mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fas fa-baby me-1"></i> <?php echo $this->lang->line('history_of_present_pregnancy'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('bleeding'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->bleeding); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('headache'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->headache); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('pain'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->pain); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('constipation'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->constipation); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('urinary_symptoms'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->urinary_symptoms); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('vomiting'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->vomiting); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('cough'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->cough); ?></span>
                </div>
            </div>
            <div class="col-6 border-start">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('vaginal'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->vaginal); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('discharge'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->discharge); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('oedema'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->oedema); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('haemoroids'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->haemoroids); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('weight'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->antenatal_weight); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('height'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->antenatal_height); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                    <span class="sh-info-value"><?php if (!empty($result->antenatal_date) && $result->antenatal_date != '1970-01-01' && $result->antenatal_date != '0000-00-00') { echo $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date); } ?></span>
                </div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('condition'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->general_condition); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('special_findings_and_remark'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->finding_remark); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('pelvic_examination'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->pelvic_examination); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('sp'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->sp); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fas fa-stethoscope me-1"></i> <?php echo $this->lang->line('antenatal_examination'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('uter_size'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->uter_size); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('uterus_size'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->uterus_size); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('presentation_position'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->presentation_position); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('presenting_part_to_brim'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->brim_presentation); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('foeta_heart'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->foeta_heart); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('blood_pressure'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->blood_pressure); ?></span>
                </div>
            </div>
            <div class="col-6 border-start">
                <div class="sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('antenatal_oedema'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->antenatal_oedema); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('urine_sugar'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->urine_sugar); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('urine_aaibumen'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->urine); ?></span>
                </div>
                <div class="sh-info-item sh-row-divider">
                    <span class="sh-info-label"><?php echo $this->lang->line('antenatal_weight'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->antenatal_weight); ?></span>
                </div>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('remark'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->remark); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('next_visit'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->next_visit); ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    $('#edit_printfinding').html("<button class='btn btn-sm btn-light' onclick='printipdantenatalprescription(<?php echo $id; ?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></button>");
    <?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_edit')) { ?>
    $('#edit_editfinding').html("<button class='btn btn-sm btn-light' onclick='edit_ipdantenatalprescription(<?php echo $id; ?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></button>");
    <?php } ?>
    <?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_delete')) { ?>
    $('#edit_deletefinding').html("<button class='btn btn-sm btn-light text-danger' onclick='delete_ipdantenatal(<?php echo $id; ?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></button>");
    <?php } ?>

    $('#edit_printfinding [data-bs-toggle="tooltip"], #edit_editfinding [data-bs-toggle="tooltip"], #edit_deletefinding [data-bs-toggle="tooltip"]').each(function() { new bootstrap.Tooltip(this); });

    function delete_ipdantenatal(id) {
        var msg = '<?php echo $this->lang->line("are_you_sure"); ?>';
        if (confirm(msg)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/deleteipdantenatal/' + id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail");
                }
            });
        }
    }
</script>

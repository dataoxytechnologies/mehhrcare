<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();

$antenatal_date_display = '';
if (!empty($result->antenatal_date) && $result->antenatal_date != '1970-01-01' && $result->antenatal_date != '0000-00-00') {
    $antenatal_date_display = $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date);
}
?>

<div class="p-1">

    <!-- Card 1: Patient -->
    <div class="sh-form-card mb-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?></span>
        </div>
        <div class="sh-info-grid">

            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('opd_id'); ?></div>
                    <div class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $result->opd_details_id; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('checkup_id'); ?></div>
                    <div class="sh-info-value"><?php echo $this->customlib->getSessionPrefixByType('checkup_id') . $result->visit_details_id; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></div>
                    <div class="sh-info-value highlight"><?php echo $result->patient_name . ' (' . $result->id . ')'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('age'); ?></div>
                    <div class="sh-info-value"><?php echo $this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('gender'); ?></div>
                    <div class="sh-info-value"><?php echo $result->gender ? $this->lang->line(strtolower($result->gender)) : '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                    <div class="sh-info-value"><?php echo $result->blood_group ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('phone'); ?></div>
                    <div class="sh-info-value"><?php echo $result->mobileno ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('email'); ?></div>
                    <div class="sh-info-value"><?php echo $result->email ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></div>
                    <div class="sh-info-value"><?php echo $result->known_allergies ?: '—'; ?></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Card 2: Primary Examine -->
    <div class="sh-form-card mb-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('primary_examine'); ?></span>
        </div>
        <div class="sh-info-grid">

            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('bleeding'); ?></div>
                    <div class="sh-info-value"><?php echo $result->bleeding ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('headache'); ?></div>
                    <div class="sh-info-value"><?php echo $result->headache ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('pain'); ?></div>
                    <div class="sh-info-value"><?php echo $result->pain ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('constipation'); ?></div>
                    <div class="sh-info-value"><?php echo $result->constipation ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('vomiting'); ?></div>
                    <div class="sh-info-value"><?php echo $result->vomiting ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('cough'); ?></div>
                    <div class="sh-info-value"><?php echo $result->cough ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('vaginal'); ?></div>
                    <div class="sh-info-value"><?php echo $result->vaginal ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('weight'); ?></div>
                    <div class="sh-info-value"><?php echo $result->antenatal_weight ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('height'); ?></div>
                    <div class="sh-info-value"><?php echo $result->antenatal_height ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('date'); ?></div>
                    <div class="sh-info-value"><?php echo $antenatal_date_display ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('discharge'); ?></div>
                    <div class="sh-info-value"><?php echo $result->discharge ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('oedema'); ?></div>
                    <div class="sh-info-value"><?php echo $result->oedema ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('condition'); ?></div>
                    <div class="sh-info-value"><?php echo $result->general_condition ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('special_findings_and_remark'); ?></div>
                    <div class="sh-info-value"><?php echo $result->finding_remark ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('pelvic_examination'); ?></div>
                    <div class="sh-info-value"><?php echo $result->pelvic_examination ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('sp'); ?></div>
                    <div class="sh-info-value"><?php echo $result->sp ?: '—'; ?></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Card 3: Antenatal Examine -->
    <div class="sh-form-card mb-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('antenatal_examine'); ?></span>
        </div>
        <div class="sh-info-grid">

            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('uter_size'); ?></div>
                    <div class="sh-info-value"><?php echo $result->uter_size ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('uterus_size'); ?></div>
                    <div class="sh-info-value"><?php echo $result->uterus_size ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('presentation_position'); ?></div>
                    <div class="sh-info-value"><?php echo $result->presentation_position ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('presenting_part_to_brim'); ?></div>
                    <div class="sh-info-value"><?php echo $result->brim_presentation ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('foeta_heart'); ?></div>
                    <div class="sh-info-value"><?php echo $result->foeta_heart ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('blood_pressure'); ?></div>
                    <div class="sh-info-value"><?php echo $result->blood_pressure ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('vaginal'); ?></div>
                    <div class="sh-info-value"><?php echo $result->vaginal ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('antenatal_weight'); ?></div>
                    <div class="sh-info-value"><?php echo $result->antenatal_weight ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('antenatal_oedema'); ?></div>
                    <div class="sh-info-value"><?php echo $result->antenatal_oedema ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('urine_sugar'); ?></div>
                    <div class="sh-info-value"><?php echo $result->urine_sugar ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('urine_aaibumen'); ?></div>
                    <div class="sh-info-value"><?php echo $result->urine ?: '—'; ?></div>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('remark'); ?></div>
                    <div class="sh-info-value"><?php echo $result->remark ?: '—'; ?></div>
                </div>
            </div>

            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $this->lang->line('next_visit'); ?></div>
                    <div class="sh-info-value"><?php echo $result->next_visit ?: '—'; ?></div>
                </div>
            </div>

            <?php if (!empty($fields_prescription)) { ?>
            <div class="row g-0 sh-row-divider">
                <?php foreach ($fields_prescription as $fields_key => $fields_value) { ?>
                <div class="col-6 col-md-3 sh-info-item">
                    <div class="sh-info-label"><?php echo $fields_value->name; ?></div>
                    <div class="sh-info-value"><?php echo $result->{"$fields_value->name"} ?: '—'; ?></div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>

        </div>
    </div>

</div>

<script type="text/javascript">
	<?php if($module == 'opd'){ ?>
		<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
		$('#edit_printfinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='printantenatalprescription(<?php echo $id;?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_edit')) { ?>
		$('#edit_editfinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='edit_antenatalprescription(<?php echo $result->visit_details_id;?>)' data-bs-target='#edit_prescription' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a>");
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_delete')) { ?>
		$('#edit_deletefinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='delete_antenatal(<?php echo $result->visit_details_id;?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
		<?php } ?>
	<?php }elseif($module == 'ipd'){ ?>
		<?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')) { ?>
		$('#edit_printfinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='printantenatalprescription(<?php echo $id;?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_edit')) { ?>
		$('#edit_editfinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='edit_antenatalprescription(<?php echo $result->visit_details_id;?>)' data-bs-target='#edit_prescription' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a>");
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_delete')) { ?>
		$('#edit_deletefinding').html("<a class='btn btn-sm btn-light' href='javascript:void(0)' onclick='delete_antenatal(<?php echo $result->visit_details_id;?>)' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
		<?php } ?>
	<?php } ?>

	$('#edit_printfinding [data-bs-toggle="tooltip"], #edit_editfinding [data-bs-toggle="tooltip"], #edit_deletefinding [data-bs-toggle="tooltip"]').each(function() { new bootstrap.Tooltip(this); });

    function delete_antenatal(visit_id) {
        var msg = '<?php echo $this->lang->line("are_you_sure"); ?>';
        if (confirm(msg)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/antenatal/deleteantenatal/' + visit_id,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
</script>

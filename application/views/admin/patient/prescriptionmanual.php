<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<?php if (!empty($print_details['print_header'])) { ?>
<div class="mx-2 mt-2">
    <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>" class="img-fluid d-block w-100" >
</div>
<?php } ?>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_details'); ?></span>
        <span class="ms-auto d-flex align-items-center gap-2 text-muted small">
            <span><?php echo $this->lang->line('opd_no'); ?> <strong><?php echo html_escape($opd_prefix . $result['opd_details_id']); ?></strong></span>
            <span class="text-muted">|</span>
            <span><?php echo $this->lang->line('checkup_id'); ?> <strong><?php echo html_escape($this->customlib->getSessionPrefixByType('checkup_id') . $visitid); ?></strong></span>
            <span class="text-muted">|</span>
            <span><?php echo $this->lang->line('date'); ?>: <strong><?php echo date($this->customlib->getHospitalDateFormat(true, true)); ?></strong></span>
        </span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo html_escape($result['patient_name']); ?> <span class="fw-normal text-muted">(<?php echo html_escape($result['patientid']); ?>)</span></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-hourglass-half me-1"></i><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->get_patient_current_age($result['patientid']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-venus-mars me-1"></i><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['gender']); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-tint me-1"></i><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($blood_group_name); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-map-marker me-1"></i><?php echo $this->lang->line('address'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['address']); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-12 col-md-6 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-user-md me-1"></i><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['name'] . ' ' . $result['surname']); ?> <span class="text-muted fw-normal">(<?php echo html_escape($result['employee_id']); ?>)</span></span>
            </div>
            <div class="col-12 col-md-6 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-heartbeat me-1"></i><?php echo $this->lang->line('known_allergies'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result['known_allergies']); ?></span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function delete_prescription(id, opdid) {
        var msg = '<?php echo $this->lang->line('are_you_sure'); ?>';
        if (confirm(msg)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/prescription/deletePrescription/' + id + '/' + opdid,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }
    }
</script>

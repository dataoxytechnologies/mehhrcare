<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<?php if (!empty($print_details['print_header'])) { ?>
<div class="mx-2 mt-2">
    <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>" class="img-fluid d-block sh-print-header-img">
</div>
<?php } ?>

<div class="sh-form-card m-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-file-text me-1"></i> <?php echo $this->lang->line('prescription'); ?> <strong class="ms-1"><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_prescription') . $result->prescription_id; ?></strong></span>
        <?php if (!empty($result->pres_created_at)) { ?>
        <span class="ms-auto text-muted small"><?php echo $this->lang->line('date'); ?>: <strong><?php echo $this->customlib->utcToHospitalDateFormat($result->pres_created_at, true); ?></strong></span>
        <?php } ?>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($result->patient_name, $result->id); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-hourglass-half me-1"></i><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-venus-mars me-1"></i><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-tint me-1"></i><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo ($result->blood_group_name ?: '-'); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-phone me-1"></i><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo ($result->mobileno ?: '-'); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-envelope me-1"></i><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo ($result->email ?: '-'); ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-user-md me-1"></i><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo composeStaffNameByString($result->name, $result->surname, $result->employee_id); ?></span>
            </div>
            <?php if ($result->attachment != '') { ?>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><i class="fa fa-paperclip me-1"></i><?php echo $this->lang->line('document'); ?></span>
                <span class="sh-info-value">
                    <a href="<?php echo site_url('patient/prescription/downloadprescription/' . $result->prescription_id); ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download me-1"></i><?php echo $this->lang->line('download'); ?></a>
                </span>
            </div>
            <?php } ?>
        </div>
        <?php if (!empty($fields_prescription)) { ?>
        <div class="row g-0 sh-row-divider">
            <?php foreach ($fields_prescription as $fields_key => $fields_value) { ?>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fields_value->name); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->{$fields_value->name} ?? '-'); ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<?php if (!empty($result->header_note) && $result->header_note != null) { ?>
<div class="sh-form-card mx-2 mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-sticky-note me-1"></i> <?php echo $this->lang->line('header_note'); ?></span>
    </div>
    <div class="p-2"><?php echo $result->header_note; ?></div>
</div>
<?php } ?>

<?php if ($result->symptoms != '' || trim($result->finding_description) != '') { ?>
<div class="sh-form-card mx-2 mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-stethoscope me-1"></i> <?php echo $this->lang->line('symptoms'); ?> / <?php echo $this->lang->line('finding'); ?></span>
    </div>
    <div class="p-2">
        <div class="row g-3">
            <?php if ($result->symptoms != '') { ?>
            <div class="<?php echo (trim($result->finding_description) != '') ? 'col-md-6' : 'col-12'; ?>">
                <div class="fw-semibold mb-1 small text-muted"><?php echo $this->lang->line('symptoms'); ?></div>
                <div><?php echo nl2br($result->symptoms); ?></div>
            </div>
            <?php } ?>
            <?php if ($result->is_finding_print == 'yes' && trim($result->finding_description) != '') { ?>
            <div class="<?php echo ($result->symptoms != '') ? 'col-md-6' : 'col-12'; ?>">
                <div class="fw-semibold mb-1 small text-muted"><?php echo $this->lang->line('finding'); ?></div>
                <div><?php echo nl2br($result->finding_description); ?></div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if (!empty($result->medicines)) { ?>
<div class="sh-form-card mx-2 mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-medkit me-1"></i> <?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sh-ipd-prescription-th-num">#</th>
                        <th><i class="fa fa-tag me-1 text-muted"></i><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th><i class="fa fa-medkit me-1 text-muted"></i><?php echo $this->lang->line('medicine'); ?></th>
                        <th><i class="fa fa-tint me-1 text-muted"></i><?php echo $this->lang->line('dosage'); ?></th>
                        <th><i class="fa fa-clock-o me-1 text-muted"></i><?php echo $this->lang->line('dose_interval'); ?></th>
                        <th><i class="fa fa-calendar me-1 text-muted"></i><?php echo $this->lang->line('dose_duration'); ?></th>
                        <th><i class="fa fa-info-circle me-1 text-muted"></i><?php echo $this->lang->line('instruction'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $medsl = 0; foreach ($result->medicines as $pkey => $pvalue) { $medsl++; ?>
                    <tr>
                        <td><b><?php echo $medsl; ?></b></td>
                        <td><?php echo $pvalue->medicine_category; ?></td>
                        <td><?php echo $pvalue->medicine_name; ?></td>
                        <td><?php echo $pvalue->dosage . ' ' . $pvalue->unit; ?></td>
                        <td><?php echo $pvalue->dose_interval_name; ?></td>
                        <td><?php echo $pvalue->dose_duration_name; ?></td>
                        <td><?php echo $pvalue->instruction; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<?php
$has_pathology = false;
$has_radiology = false;
if (!empty($result->tests)) {
    foreach ($result->tests as $test_value) {
        if ($test_value->test_name != '')       $has_pathology = true;
        if ($test_value->radio_test_name != '') $has_radiology = true;
    }
}
if ($has_pathology || $has_radiology) { ?>
<div class="sh-form-card mx-2 mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-flask me-1"></i> <?php echo $this->lang->line('pathology_test'); echo ($has_pathology && $has_radiology) ? ' / ' . $this->lang->line('radiology_test') : ($has_radiology ? $this->lang->line('radiology_test') : ''); ?></span>
    </div>
    <div class="p-2">
        <div class="row g-3">
            <?php if ($has_pathology) { ?>
            <div class="<?php echo $has_radiology ? 'col-md-6' : 'col-12'; ?>">
                <div class="fw-semibold mb-1 small text-muted"><?php echo $this->lang->line('pathology_test'); ?></div>
                <?php $sl = 0; foreach ($result->tests as $test_value) {
                    if ($test_value->test_name != '') { $sl++; ?>
                <div class="sh-ipd-prescription-test-row"><?php echo $sl . '. ' . $test_value->test_name . ' (' . $test_value->short_name . ')'; ?></div>
                <?php } } ?>
            </div>
            <?php } ?>
            <?php if ($has_radiology) { ?>
            <div class="<?php echo $has_pathology ? 'col-md-6' : 'col-12'; ?>">
                <div class="fw-semibold mb-1 small text-muted"><?php echo $this->lang->line('radiology_test'); ?></div>
                <?php $slradiology = 0; foreach ($result->tests as $test_value) {
                    if ($test_value->test_name == '' && $test_value->radio_test_name != '') { $slradiology++; ?>
                <div class="sh-ipd-prescription-test-row"><?php echo $slradiology . '. ' . $test_value->radio_test_name . ' (' . $test_value->radio_short_name . ')'; ?></div>
                <?php } } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if (!empty($result->footer_note) && $result->footer_note != null) { ?>
<div class="sh-form-card mx-2 mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-sticky-note-o me-1"></i> <?php echo $this->lang->line('footer_note'); ?></span>
    </div>
    <div class="p-2"><?php echo $result->footer_note; ?></div>
</div>
<?php } ?>

<div class="footer-fixed printfooter mx-2">
    <?php if (!empty($print_details['print_footer'])) { echo $print_details['print_footer']; } ?>
</div>

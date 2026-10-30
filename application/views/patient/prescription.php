<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<?php if ($print == 'yes'): ?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('prescription'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>
<body>

<div class="fixed-print-header">
    <?php if (!empty($print_details['print_header'])): ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>" style="height:100px; width:100%; object-fit:cover;" class="img-fluid">
    <?php endif; ?>
</div>

<table class="table-print-full" width="100%">
    <thead><tr><td><div class="header-space">&nbsp;</div></td></tr></thead>
    <tbody><tr><td>
    <div class="content-body sh-px-12" >
    <div class="print-area">

        <div class="sh-print-title sh-text-upper-bold" >
            <?php echo $this->lang->line('prescription'); ?>
            <span class="sh-header-bold"><?php echo $this->customlib->getPatientSessionPrefixByType('opd_prescription') . $result->prescription_id; ?></span>
        </div>

        <!-- Patient & Prescription Info -->
        <div class="sh-print-info-block">
            <table class="sh-print-info-table">
                <colgroup>
                    <col style="width:16%"><col style="width:18%">
                    <col style="width:16%"><col style="width:18%">
                    <col style="width:16%"><col style="width:16%">
                </colgroup>
                <tr>
                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                    <td><?php echo composePatientName($result->patient_name, $result->id); ?></td>
                    <th><?php echo $this->lang->line('age'); ?></th>
                    <td><?php echo $this->customlib->get_patient_current_age($result->id); ?></td>
                    <th><?php echo $this->lang->line('gender'); ?></th>
                    <td><?php echo $this->lang->line(strtolower($result->gender)); ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                    <td><?php echo html_escape($result->blood_group_name); ?></td>
                    <th><?php echo $this->lang->line('phone'); ?></th>
                    <td><?php echo html_escape($result->mobileno); ?></td>
                    <th><?php echo $this->lang->line('email'); ?></th>
                    <td><?php echo html_escape($result->email); ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('opd_id'); ?></th>
                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') . $result->opd_detail_id; ?></td>
                    <th><?php echo $this->lang->line('checkup_id'); ?></th>
                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') . $result->visitid; ?></td>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <td><?php echo !empty($result->pres_created_at) ? $this->customlib->YYYYMMDDHisTodateFormat($result->pres_created_at) : '-'; ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                    <td colspan="3"><?php echo composeStaffNameByString($result->name, $result->surname, $result->doctor_id); ?></td>
                    <th><?php echo $this->lang->line('known_allergies'); ?></th>
                    <td><?php echo html_escape($result->known_allergies) ?: '-'; ?></td>
                </tr>
                <?php if (!empty($fields_prescription)): foreach ($fields_prescription as $fv): ?>
                <tr>
                    <th><?php echo html_escape($fv->name); ?></th>
                    <td colspan="5"><?php echo $result->{$fv->name}; ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </table>
        </div>

        <!-- Clinical Notes -->
        <?php if ($result->header_note != "" || $result->symptoms != "" || (trim($result->finding_description) != '' && $result->is_finding_print == 'yes')): ?>
        <div class="sh-print-section-title"><?php echo $this->lang->line('clinical_notes'); ?></div>
        <div style="font-size:12px; margin-bottom:12px;">
            <?php if ($result->header_note != ""): ?>
            <div style="margin-bottom:6px;"><?php echo $result->header_note; ?></div>
            <?php endif; ?>
            <?php if ($result->symptoms != ""): ?>
            <div style="margin-bottom:4px;"><strong><?php echo $this->lang->line('symptoms'); ?>:</strong> <?php echo nl2br(html_escape($result->symptoms)); ?></div>
            <?php endif; ?>
            <?php if (trim($result->finding_description) != '' && $result->is_finding_print == 'yes'): ?>
            <div><strong><?php echo $this->lang->line('finding'); ?>:</strong> <?php echo nl2br(html_escape($result->finding_description)); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Medicines -->
        <?php if ($result->medicines): ?>
        <div class="sh-print-section-title"><?php echo $this->lang->line('medicines'); ?></div>
        <table class="sh-print-table">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th style="width:18%;"><?php echo $this->lang->line('medicine'); ?></th>
                    <th style="width:14%;"><?php echo $this->lang->line('medicine_category'); ?></th>
                    <th style="width:13%;"><?php echo $this->lang->line('dosage'); ?></th>
                    <th style="width:17%;"><?php echo $this->lang->line('dose_interval'); ?></th>
                    <th style="width:17%;"><?php echo $this->lang->line('dose_duration'); ?></th>
                    <th><?php echo $this->lang->line('instruction'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $medsl = 0; foreach ($result->medicines as $pvalue): $medsl++; ?>
                <tr>
                    <td><?php echo $medsl; ?></td>
                    <td><?php echo html_escape($pvalue->medicine_name); ?></td>
                    <td><?php echo html_escape($pvalue->medicine_category); ?></td>
                    <td><?php echo html_escape($pvalue->dosage . ' ' . $pvalue->unit); ?></td>
                    <td><?php echo html_escape($pvalue->dose_interval_name); ?></td>
                    <td><?php echo html_escape($pvalue->dose_duration_name); ?></td>
                    <td><?php echo html_escape($pvalue->instruction); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Tests -->
        <?php if (!empty($result->tests)):
            $r = $p = 0;
            $patho_tests = $radio_tests = [];
            foreach ($result->tests as $tv) {
                if ($tv->test_name != "")       { $p = 1; $patho_tests[] = $tv; }
                if ($tv->radio_test_name != "") { $r = 1; $radio_tests[] = $tv; }
            }
            if ($p == 1 || $r == 1):
        ?>
        <div class="sh-print-section-title"><?php echo $this->lang->line('tests'); ?></div>
        <table width="100%">
            <tr>
                <?php if ($p == 1): ?>
                <td style="vertical-align:top; width:50%; padding-right:12px;">
                    <div class="sh-label-11-bold"><?php echo $this->lang->line('pathology_test'); ?></div>
                    <ol class="sh-list-12">
                        <?php foreach ($patho_tests as $tv): ?>
                        <li><?php echo html_escape($tv->test_name); ?> <small>(<?php echo html_escape($tv->short_name); ?>)</small></li>
                        <?php endforeach; ?>
                    </ol>
                </td>
                <?php endif; ?>
                <?php if ($r == 1): ?>
                <td style="vertical-align:top; width:50%;">
                    <div class="sh-label-11-bold"><?php echo $this->lang->line('radiology_test'); ?></div>
                    <ol class="sh-list-12">
                        <?php foreach ($radio_tests as $tv): ?>
                        <li><?php echo html_escape($tv->radio_test_name); ?> <small>(<?php echo html_escape($tv->radio_short_name); ?>)</small></li>
                        <?php endforeach; ?>
                    </ol>
                </td>
                <?php endif; ?>
            </tr>
        </table>
        <?php endif; endif; ?>

        <!-- Footer Note -->
        <?php if ($result->footer_note != ""): ?>
        <div style="margin-top:12px; font-size:12px;"><?php echo $result->footer_note; ?></div>
        <?php endif; ?>

    </div>
    </div>
    </td></tr></tbody>
    <tfoot><tr><td>
        <?php if (!empty($print_details['print_footer'])): ?>
        <div class="footer-space">&nbsp;</div>
        <?php endif; ?>
    </td></tr></tfoot>
</table>

<?php if (!empty($print_details['print_footer'])): ?>
<div class="footer-fixed">
    <?php echo $print_details['print_footer']; ?>
</div>
<?php endif; ?>

</body>
</html>

<?php else: ?>
<?php /* ── MODAL DISPLAY LAYOUT ── */ ?>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('patient'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo composePatientName($result->patient_name, $result->id); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->get_patient_current_age($result->id); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                <span class="sh-info-value"><?php echo $this->lang->line(strtolower($result->gender)); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->blood_group_name); ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->mobileno); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->email); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo composeStaffNameByString($result->name, $result->surname, $result->doctor_id); ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('known_allergies'); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->known_allergies); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('prescription'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('prescription'); ?> #</span>
                <span class="sh-info-value highlight"><?php echo $this->customlib->getPatientSessionPrefixByType('opd_prescription') . $result->prescription_id; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php echo !empty($result->pres_created_at) ? $this->customlib->YYYYMMDDHisTodateFormat($result->pres_created_at) : ''; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('opd_id'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') . $result->opd_detail_id; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('checkup_id'); ?></span>
                <span class="sh-info-value"><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') . $result->visitid; ?></span>
            </div>
        </div>
        <?php if (!empty($fields_prescription)): ?>
        <div class="row g-0 sh-row-divider">
            <?php foreach ($fields_prescription as $fv): ?>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo html_escape($fv->name); ?></span>
                <span class="sh-info-value"><?php echo html_escape($result->{$fv->name}); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($result->attachment != ""): ?>
    <div class="p-3 pt-0">
        <a href="<?php echo site_url('patient/prescription/downloadprescription/' . $result->prescription_id); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-download me-1"></i><?php echo $this->lang->line('download'); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if ($result->symptoms != "" || trim($result->finding_description) != "" || $result->header_note != ""): ?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('clinical_notes'); ?></span>
    </div>
    <div class="p-3">
        <?php if ($result->header_note != ""): ?>
        <div class="mb-2"><?php echo $result->header_note; ?></div>
        <?php endif; ?>
        <?php if ($result->symptoms != ""): ?>
        <div class="mb-2">
            <div class="sh-info-label mb-1"><?php echo $this->lang->line('symptoms'); ?></div>
            <div><?php echo nl2br(html_escape($result->symptoms)); ?></div>
        </div>
        <?php endif; ?>
        <?php if (trim($result->finding_description) != ""): ?>
        <div>
            <div class="sh-info-label mb-1"><?php echo $this->lang->line('finding'); ?></div>
            <div><?php echo nl2br(html_escape($result->finding_description)); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($result->medicines): ?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('medicines'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('medicine'); ?></th>
                    <th><?php echo $this->lang->line('dosage'); ?></th>
                    <th><?php echo $this->lang->line('dose_interval'); ?></th>
                    <th><?php echo $this->lang->line('dose_duration'); ?></th>
                    <th><?php echo $this->lang->line('instruction'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $medsl = 0; foreach ($result->medicines as $pvalue): $medsl++; ?>
                <tr>
                    <td><?php echo $medsl; ?></td>
                    <td>
                        <?php echo html_escape($pvalue->medicine_name); ?>
                        <?php if (!empty($pvalue->medicine_category)): ?>
                        <br><small class="text-muted"><?php echo html_escape($pvalue->medicine_category); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo html_escape($pvalue->dosage . " " . $pvalue->unit); ?></td>
                    <td><?php echo html_escape($pvalue->dose_interval_name); ?></td>
                    <td><?php echo html_escape($pvalue->dose_duration_name); ?></td>
                    <td><?php echo html_escape($pvalue->instruction); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($result->tests)):
    $r = $p = 0;
    $patho_tests = $radio_tests = [];
    foreach ($result->tests as $tv) {
        if ($tv->test_name != "") { $p = 1; $patho_tests[] = $tv; }
        if ($tv->radio_test_name != "") { $r = 1; $radio_tests[] = $tv; }
    }
    if ($p == 1 || $r == 1):
?>
<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('tests'); ?></span>
    </div>
    <div class="p-3">
        <div class="row g-3">
            <?php if ($p == 1): ?>
            <div class="col-md-6">
                <div class="sh-info-label mb-2"><?php echo $this->lang->line('pathology_test'); ?></div>
                <ol class="mb-0 ps-3 sh-fs-13" >
                    <?php foreach ($patho_tests as $tv): ?>
                    <li><?php echo html_escape($tv->test_name); ?> <small class="text-muted">(<?php echo html_escape($tv->short_name); ?>)</small></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>
            <?php if ($r == 1): ?>
            <div class="col-md-6">
                <div class="sh-info-label mb-2"><?php echo $this->lang->line('radiology_test'); ?></div>
                <ol class="mb-0 ps-3 sh-fs-13" >
                    <?php foreach ($radio_tests as $tv): ?>
                    <li><?php echo html_escape($tv->radio_test_name); ?> <small class="text-muted">(<?php echo html_escape($tv->radio_short_name); ?>)</small></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; endif; ?>

<?php if ($result->footer_note != ""): ?>
<div class="sh-form-card">
    <div class="p-3">
        <div class="sh-info-label mb-1"><?php echo $this->lang->line('note'); ?></div>
        <div class="sh-fs-13"><?php echo $result->footer_note; ?></div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

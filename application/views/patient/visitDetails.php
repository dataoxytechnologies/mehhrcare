<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$total           = 0;
$total_payment   = 0;
if (!empty($charges_detail)) {
    foreach ($charges_detail as $cv) { $total += $cv['amount']; }
}
if (!empty($payment_details)) {
    foreach ($payment_details as $pv) { if (!empty($pv['amount'])) $total_payment += $pv['amount']; }
}
?>
<div class="container-fluid px-3 py-3">
<?php if (empty($result)) { ?>
    <h4 class="text-center"><?php echo $this->lang->line('no_record_found'); ?></h4>
<?php } else {
    $image        = $result['image'];
    $vd_has_image = !empty($image) && strpos($image, 'no_image') === false;
    $file         = $vd_has_image ? $image : 'uploads/patient_images/no_image.png';
    if (!$vd_has_image) {
        $_vd_parts    = preg_split('/\s+/', trim($result['patient_name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $_vd_initials = count($_vd_parts) === 0 ? '?' : (count($_vd_parts) === 1
            ? mb_strtoupper(mb_substr($_vd_parts[0], 0, 1))
            : mb_strtoupper(mb_substr($_vd_parts[0], 0, 1) . mb_substr($_vd_parts[count($_vd_parts) - 1], 0, 1)));
    }
?>
<div class="opd-profile-wrap">

    <!-- ── Page header ───────────────────────────────────────────── -->
    <header class="page-head">
        <div class="ph-title-row">
            <div class="ph-title">
                <?php if ($vd_has_image): ?>
                <img src="<?php echo $this->media_storage->getImageURL($file); ?>" alt="Patient" class="ph-av" style="width:52px;height:52px;object-fit:cover;">
                <?php else: ?>
                <div class="ph-av"><?php echo html_escape($_vd_initials); ?></div>
                <?php endif; ?>
                <div>
                    <h1><?php echo composePatientName($result['patient_name'], $result['pid']); ?></h1>
                    <div class="sub">
                        <?php echo $this->lang->line(strtolower($result['gender'])); ?>
                        <span class="dot-sep"><?php echo $this->customlib->get_patient_current_age($result['pid']); ?></span>
                        <span class="dot-sep"><?php echo html_escape($result['mobileno']); ?></span>
                        <span class="dot-sep"><?php echo $this->lang->line('guardian'); ?>: <?php echo html_escape($result['guardian_name']); ?></span>
                    </div>
                </div>
            </div>
            <div class="ph-actions"></div>
        </div>

        <div class="bcard-grid sh-border-top-light" >
            <div class="field"><div class="l"><?php echo $this->lang->line('gender'); ?></div><div class="v"><?php echo $this->lang->line(strtolower($result['gender'])); ?></div></div>
            <div class="field"><div class="l"><?php echo $this->lang->line('age'); ?></div><div class="v"><?php echo $this->customlib->get_patient_current_age($result['pid']); ?></div></div>
            <div class="field"><div class="l"><?php echo $this->lang->line('guardian_name'); ?></div><div class="v"><?php echo html_escape($result['guardian_name']); ?></div></div>
            <div class="field"><div class="l"><?php echo $this->lang->line('phone'); ?></div><div class="v"><?php echo html_escape($result['mobileno']); ?></div></div>
            <div class="field"><div class="l"><?php echo $this->lang->line('case_id'); ?></div><div class="v"><?php echo html_escape($result['case_reference_id']); ?></div></div>
            <div class="field"><div class="l"><?php echo $this->lang->line('opd_no'); ?></div><div class="v"><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') . $opd_details_id; ?></div></div>
            <?php if (file_exists("./uploads/patient_id_card/barcodes/" . $patient_id . ".png")) { ?>
            <div class="field"><div class="l"><?php echo $this->lang->line('barcode'); ?></div><div class="v"><a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/" . $patient_id . ".png"); ?>" target="_blank"><img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/" . $patient_id . ".png"); ?>" width="90" height="24"></a></div></div>
            <?php } ?>
            <?php if (file_exists("./uploads/patient_id_card/qrcode/" . $patient_id . ".png")) { ?>
            <div class="field"><div class="l"><?php echo $this->lang->line('qrcode'); ?></div><div class="v"><a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/" . $patient_id . ".png"); ?>" target="_blank"><img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/" . $patient_id . ".png"); ?>" width="40" height="40"></a></div></div>
            <?php } ?>
        </div>

        <nav class="ph-tabs" role="tablist">
            <a class="active" data-bs-toggle="tab" data-bs-target="#overview"       href="#overview"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#activity"         href="#activity"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#medication"       href="#medication"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#labinvestigation" href="#labinvestigation"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#operationtheatre" href="#operationtheatre"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#charges"          href="#charges"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#payment"          href="#payment"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payment'); ?></a>
            <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
            <a data-bs-toggle="tab" data-bs-target="#live_consult"     href="#live_consult"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('live_consultation'); ?></a>
            <?php } ?>
            <a data-bs-toggle="tab" data-bs-target="#timeline"         href="#timeline"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a>
            <a data-bs-toggle="tab" data-bs-target="#vitals"           href="#vitals"><i class="fas fa-heartbeat"></i> <?php echo $this->lang->line('vitals'); ?></a>
        </nav>
    </header>

    <!-- ── Tab content ───────────────────────────────────────────── -->
    <div class="tab-content p-3">

        <!-- ── Overview ─────────────────────────────────────────── -->
        <div class="tab-pane show active" id="overview">
        <div class="rellist-wrap">

        <?php
        $has_allergy  = !empty($patientdetails['patient']['allergy']);
        $has_findings = !empty($patientdetails['patient']['findings']);
        $has_symptoms = !empty($patientdetails['patient']['symptoms']);
        if ($has_allergy || $has_findings || $has_symptoms) { ?>
        <div class="rellist" style="border-color:var(--red);">
            <div class="rellist-head" style="background:var(--red-soft);">
                <div class="l">
                    <div class="ic" style="background:var(--red);color:#fff;font-weight:700;font-size:14px;">!</div>
                    <div class="title sh-text-danger" ><?php echo $this->lang->line('known_allergies'); ?> / <?php echo $this->lang->line('findings'); ?> / <?php echo $this->lang->line('symptoms'); ?></div>
                </div>
            </div>
            <div class="rellist-body pad d-flex gap-2 flex-wrap">
                <?php if ($has_allergy) { foreach ($patientdetails['patient']['allergy'] as $row) { ?>
                <div style="padding:8px 12px;background:var(--red-soft);color:var(--red);border-radius:var(--radius-md);font-size:13px;"><?php echo html_escape($row['known_allergies']); ?></div>
                <?php } } ?>
                <?php if ($has_findings) { foreach ($patientdetails['patient']['findings'] as $row) { ?>
                <div style="padding:8px 12px;background:var(--amber-soft);color:var(--amber);border-radius:var(--radius-md);font-size:13px;"><?php echo html_escape($row['finding_description']); ?></div>
                <?php } } ?>
                <?php if ($has_symptoms) { foreach ($patientdetails['patient']['symptoms'] as $row) { ?>
                <div style="padding:8px 12px;background:var(--surface-2);color:var(--ink);border-radius:var(--radius-md);font-size:13px;"><?php echo html_escape($row['symptoms']); ?></div>
                <?php } } ?>
            </div>
        </div>
        <?php } ?>

        <!-- Bill Summary -->
        <div class="rellist">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('bill_summary'); ?></div>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body pad">
                <div class="row g-3">
                    <?php if ($this->module_lib->hasActive('opd') && $this->module_lib->hasPatientActive('opd')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('opd_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['opd']['opd_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['opd']['payment']['total_payment'], $graph['opd']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['opd']['opd_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('pharmacy') && $this->module_lib->hasPatientActive('pharmacy')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('pharmacy_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill(($graph['pharmacy']['payment']['total_payment'] - $graph['pharmacy']['payment_refund']['total_payment']), $graph['pharmacy']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('pathology') && $this->module_lib->hasPatientActive('pathology')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('pathology_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['pathology']['pathology_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['pathology']['payment']['total_payment'], $graph['pathology']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['pathology']['pathology_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('radiology') && $this->module_lib->hasPatientActive('radiology')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('radiology_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['radiology']['radiology_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['radiology']['payment']['total_payment'], $graph['radiology']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['radiology']['radiology_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('blood_bank') && $this->module_lib->hasPatientActive('blood_bank')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('blood_bank_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['blood_bank']['payment']['total_payment'], $graph['blood_bank']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                    <?php if ($this->module_lib->hasActive('ambulance') && $this->module_lib->hasPatientActive('ambulance')) { ?>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-1 sh-tag-heading" ><?php echo $this->lang->line('ambulance_billing_payment_graph'); ?></h6>
                        <p class="text-muted small mb-1"><?php echo $graph['ambulance']['ambulance_bill_payment_ratio']; ?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['ambulance']['payment']['total_payment'], $graph['ambulance']['bill']['total_bill']); ?></span></p>
                        <div class="progress sh-h-6" ><div class="progress-bar bg-info" style="width:<?php echo $graph['ambulance']['ambulance_bill_payment_ratio']; ?>%"></div></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Current Vitals -->
        <?php if (!empty($patientcurrentvital)) { ?>
        <div class="rellist">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('current_vitals'); ?></div>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body pad">
            <div class="row g-2">
            <?php
            $height = ""; $weight = ""; $bmi = ""; $class1 = "";
            foreach ($patientcurrentvital as $value) {
                $class = "badge bg-success";
                $vitalrange = $this->lang->line('normal');
                if (strpos($value['reference_range'], '-') !== false) {
                    $range = explode("-", $value['reference_range']);
                    $min_val = $range[0]; $max_val = $range[1];
                    if (strpos($value['patient_range'], '-') !== false) {
                        $vital = explode("-", $value['patient_range']);
                        $min_vital = $vital[0]; $max_vital = $vital[1];
                        $vitalrange = $this->lang->line('normal'); $class = "badge bg-success";
                        if ($min_vital < $min_val) { $vitalrange = $this->lang->line('low');  $class = "badge bg-danger"; }
                        if ($max_vital > $max_val) { $vitalrange = $this->lang->line('high'); $class = "badge bg-danger"; }
                    } else {
                        if ($value['patient_range'] < $min_val) { $vitalrange = $this->lang->line('low');  $class = "badge bg-danger"; }
                        if ($value['patient_range'] > $max_val) { $vitalrange = $this->lang->line('high'); $class = "badge bg-danger"; }
                    }
                }
                if ($value['id'] == '1' && $value['patient_range'] != "") {
                    $height = strpos($value['patient_range'], '-') !== false ? (float)explode("-", $value['patient_range'])[0] : (float)$value['patient_range'];
                }
                if ($value['id'] == '2' && $value['patient_range'] != "") {
                    $weight = strpos($value['patient_range'], '-') !== false ? (float)explode("-", $value['patient_range'])[0] : (float)$value['patient_range'];
                }
                if ($weight != "" && $height != "") {
                    $h1 = $height * 0.01; $bmiH = $h1 * $h1;
                    $bmi = $bmiH > 0 ? round($weight / $bmiH, 2) : '';
                    $class1 = "badge bg-success";
                }
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="sh-ipd-vital-card-sm">
                    <div class="sh-tag-muted"><?php echo html_escape($value['name']); ?></div>
                    <div class="sh-vital-value"><?php echo html_escape($value['patient_range']); ?><span style="font-size:11px;color:var(--muted);"> <?php echo html_escape($value['unit']); ?></span></div>
                    <div class="sh-caption-11-top"><span class="<?php echo $class; ?>"><?php echo $vitalrange; ?></span> <span class="text-muted sh-fs-10" ><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['messure_date'], $this->customlib->getHospitalTimeFormat()); ?></span></div>
                </div>
            </div>
            <?php } ?>
            <?php if ($weight != "" && $height != "") { ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="sh-ipd-vital-card-sm">
                    <div class="sh-tag-muted"><?php echo $this->lang->line('bmi'); ?></div>
                    <div class="sh-vital-value"><?php echo $bmi; ?></div>
                    <div class="sh-caption-11-top"><span class="<?php echo $class1; ?>"><?php echo $bmi; ?></span></div>
                </div>
            </div>
            <?php } ?>
            </div>
            </div>
        </div>
        <?php } ?>

        <!-- Consultant Doctor -->
        <div class="rellist">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('consultant_doctor'); ?></div>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body pad">
                <div class="d-flex flex-column gap-2">
                <?php if (!empty($patientdetails['patient']['doctor'])) {
                    foreach ($patientdetails['patient']['doctor'] as $value) { ?>
                <div class="d-flex align-items-center gap-2">
                    <img src="<?php echo $this->media_storage->getImageURL(!empty($value['image']) ? 'uploads/staff_images/' . $value['image'] : 'uploads/staff_images/no_image.png'); ?>"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--ink);"><?php echo html_escape($value['name'] . " " . $value['surname'] . " (" . $value['employee_id'] . ")"); ?></span>
                </div>
                    <?php } } else { ?>
                <div class="text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
                </div>
            </div>
        </div>

        <!-- Medication overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.5 12.5l8-8a4.94 4.94 0 1 1 7 7l-8 8a4.94 4.94 0 1 1-7-7Z"/><path d="m8.5 8.5 7 7"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('medication'); ?></div>
                    <?php if (!empty($medicationreport_overview)) { ?><div class="count"><?php echo count($medicationreport_overview); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($medicationreport_overview)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('medicine_name'); ?></th>
                        <th><?php echo $this->lang->line('dose'); ?></th>
                        <th><?php echo $this->lang->line('time'); ?></th>
                        <th><?php echo $this->lang->line('remark'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < $recent_record_count; $i++) {
                            if (!empty($medicationreport_overview[$i])) { ?>
                        <tr>
                            <td><?php if ($medicationreport_overview[$i]['date']) { echo $this->customlib->YYYYMMDDTodateFormat($medicationreport_overview[$i]['date']); } ?></td>
                            <td><?php echo $medicationreport_overview[$i]['medicine_name']; ?></td>
                            <td><?php echo $medicationreport_overview[$i]['medicine_dosage'] . " (" . $medicationreport_overview[$i]['unit'] . ")"; ?></td>
                            <td><?php echo $this->customlib->getHospitalTime_Format($medicationreport_overview[$i]['time']); ?></td>
                            <td><?php echo $medicationreport_overview[$i]['remark']; ?></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Lab Investigations overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2v7l-5 9a2 2 0 0 0 1.73 3h12.54A2 2 0 0 0 20 18L15 9V2"/><path d="M8 2h8M7 16h10"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('lab_investigation'); ?></div>
                    <?php if (!empty($investigations)) { ?><div class="count"><?php echo count($investigations); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($investigations)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('test_name'); ?></th>
                        <th><?php echo $this->lang->line('lab'); ?></th>
                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                        <th><?php echo $this->lang->line('expected_date'); ?></th>
                        <th><?php echo $this->lang->line('approved_by'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < $recent_record_count; $i++) {
                            if (!empty($investigations[$i])) { ?>
                        <tr>
                            <td><?php echo $investigations[$i]['test_name']; ?><br><small>(<?php echo $investigations[$i]['short_name']; ?>)</small></td>
                            <td><?php echo $this->lang->line($investigations[$i]['type']); ?></td>
                            <td>
                                <?php echo composeStaffNameByString($investigations[$i]['collection_specialist_staff_name'], $investigations[$i]['collection_specialist_staff_surname'], $investigations[$i]['collection_specialist_staff_employee_id']); ?><br>
                                <small><?php echo $investigations[$i]['test_center']; ?></small>
                                <?php if ($investigations[$i]['collection_date']) { ?><br><?php echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['collection_date']); } ?>
                            </td>
                            <td><?php if ($investigations[$i]['reporting_date']) { echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['reporting_date']); } ?></td>
                            <td>
                                <?php echo composeStaffNameByString($investigations[$i]['approved_by_staff_name'], $investigations[$i]['approved_by_staff_surname'], $investigations[$i]['approved_by_staff_employee_id']); ?>
                                <?php if ($investigations[$i]['parameter_update']) { ?><br><?php echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['parameter_update']); } ?>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Operations overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 7 10 10M7 17 17 7"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('operation'); ?></div>
                    <?php if (!empty($operation_theatre)) { ?><div class="count"><?php echo count($operation_theatre); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($operation_theatre)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('reference_no'); ?></th>
                        <th><?php echo $this->lang->line('operation_date'); ?></th>
                        <th><?php echo $this->lang->line('operation_name'); ?></th>
                        <th><?php echo $this->lang->line('operation_category'); ?></th>
                        <th><?php echo $this->lang->line('ot_technician'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < $recent_record_count; $i++) {
                            if (!empty($operation_theatre[$i])) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no') . $operation_theatre[$i]["id"]; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($operation_theatre[$i]["date"], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $operation_theatre[$i]["operation"]; ?></td>
                            <td><?php echo $operation_theatre[$i]["category"]; ?></td>
                            <td><?php echo $operation_theatre[$i]['ot_technician']; ?></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Charges overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('charges'); ?></div>
                    <?php if (!empty($charges_detail)) { ?><div class="count"><?php echo count($charges_detail); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($charges_detail)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('name'); ?></th>
                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $ov_total = 0;
                        for ($i = 0; $i < $recent_record_count; $i++) {
                            if (!empty($charges_detail[$i])) {
                                $cv = $charges_detail[$i];
                                $d_amt = amountFormat($cv['apply_charge'] * $cv['discount_percentage'] / 100);
                                $t_amt = amountFormat(($cv['apply_charge'] - $d_amt) * $cv['tax'] / 100);
                                $ov_total += $cv['amount']; ?>
                        <tr>
                            <td><?php echo $cv['name']; ?><div class="bill_item_footer text-muted"><?php echo $cv['note']; ?></div></td>
                            <td class="text-capitalize"><?php echo $cv['charge_type']; ?></td>
                            <td class="text-end"><?php echo $cv['standard_charge']; ?></td>
                            <td class="text-end"><?php echo $d_amt . " (" . $cv['discount_percentage'] . "%)"; ?></td>
                            <td class="text-end"><?php echo $t_amt . " (" . $cv['tax'] . "%)"; ?></td>
                            <td class="text-end"><?php echo $cv['apply_charge']; ?></td>
                            <td class="text-end"><?php echo $cv['amount']; ?></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Payment overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('payment'); ?></div>
                    <?php if (!empty($payment_details)) { ?><div class="count"><?php echo count($payment_details); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($payment_details)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                    </tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < $recent_record_count; $i++) {
                            if (!empty($payment_details[$i]) && !empty($payment_details[$i]['amount'])) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id') . $payment_details[$i]['id']; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment_details[$i]['payment_date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $payment_details[$i]['note']; ?></td>
                            <td class="text-capitalize"><?php echo $this->lang->line(strtolower($payment_details[$i]['payment_mode']));
                                if ($payment_details[$i]['payment_mode'] == "Cheque") {
                                    if ($payment_details[$i]['cheque_no'] != '') { echo "<br>" . $this->lang->line('cheque_no') . ": " . $payment_details[$i]['cheque_no']; }
                                    if ($payment_details[$i]['cheque_date'] != '' && $payment_details[$i]['cheque_date'] != '0000-00-00') { echo "<br>" . $this->lang->line('cheque_date') . ": " . $this->customlib->YYYYMMDDTodateFormat($payment_details[$i]['cheque_date']); }
                                } ?></td>
                            <td class="text-end"><?php echo $payment_details[$i]['amount']; ?></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Live Consultation overview -->
        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('live_consultation'); ?></div>
                    <?php if (!empty($visitconferences)) { ?><div class="count"><?php echo count($visitconferences); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body">
                <?php if (!empty($visitconferences)) { ?>
                <table class="table table-striped table-bordered table-hover mb-0">
                    <thead><tr>
                        <th><?php echo $this->lang->line('consultation_title'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('created_by'); ?></th>
                        <th><?php echo $this->lang->line('created_for'); ?></th>
                        <th><?php echo $this->lang->line('patient'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($visitconferences as $cv) { ?>
                        <tr>
                            <td><?php echo $cv->title; ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($cv->date)); ?></td>
                            <td><?php $n = ($cv->create_by_surname == "") ? $cv->create_by_name : $cv->create_by_name . " " . $cv->create_by_surname; echo $n . " (" . $cv->create_by_role_name . ": " . $cv->for_create_employee_id . ")"; ?></td>
                            <td><?php $n = ($cv->create_for_surname == "") ? $cv->create_for_name : $cv->create_for_name . " " . $cv->create_for_surname; echo $n . " (" . $cv->for_create_role_name . ": " . $cv->for_create_employee_id . ")"; ?></td>
                            <td><?php echo $cv->patient_name . " (" . $cv->patientid . ")"; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <!-- Timeline overview -->
        <div class="rellist collapsed">
            <div class="rellist-head">
                <div class="l">
                    <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="title"><?php echo $this->lang->line('timeline'); ?></div>
                    <?php if (!empty($timeline_list)) { ?><div class="count"><?php echo count($timeline_list); ?></div><?php } ?>
                </div>
                <div class="r">
                    <button class="expand" onclick="toggleRellist(this)">
                        <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                        <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                    </button>
                </div>
            </div>
            <div class="rellist-body pad">
                <?php if (!empty($timeline_list)) { ?>
                <ul class="overview-timeline">
                    <?php for ($i = 0; $i < $recent_record_count; $i++) {
                        if (!empty($timeline_list[$i])) { ?>
                    <li>
                        <div class="tl-date"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($timeline_list[$i]['timeline_date'])); ?></div>
                        <div class="tl-body">
                            <div class="tl-dot"></div>
                            <div>
                                <div class="tl-title"><?php echo $timeline_list[$i]['title']; ?></div>
                                <div class="tl-desc"><?php echo $timeline_list[$i]['description']; ?></div>
                                <?php if (!empty($timeline_list[$i]["document"])) { ?>
                                <a class="tl-dl" href="<?php echo base_url() . "patient/dashboard/download_patient_timeline/" . $timeline_list[$i]["id"]; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i> <?php echo $this->lang->line('download'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                    <?php } } ?>
                </ul>
                <?php } else { ?>
                <div class="text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                <?php } ?>
            </div>
        </div>

        </div><!-- /rellist-wrap -->
        </div><!-- /overview -->

        <!-- ── Visits (checkups) tab ─────────────────────────────── -->
        <div class="tab-pane card" id="activity">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('checkups'); ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                    <thead><tr>
                        <th><?php echo $this->lang->line('checkup_id'); ?></th>
                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                        <th><?php echo $this->lang->line('consultant'); ?></th>
                        <th><?php echo $this->lang->line('reference'); ?></th>
                        <th><?php echo $this->lang->line('symptoms'); ?></th>
                        <?php if (is_array($fields) || is_object($fields)) {
                            foreach ($fields as $fv) { ?>
                        <th><?php echo ucfirst($fv->name); ?></th>
                        <?php } } ?>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php if (!empty($visit_details)) {
                            foreach ($visit_details as $key => $visit) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') . $visit["id"]; ?></td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($visit['appointment_date'])); ?></td>
                            <td><?php echo composeStaffNameByString($visit["name"], $visit["surname"], $visit['employee_id']); ?></td>
                            <td><?php echo $visit['refference']; ?></td>
                            <td><?php echo $visit['symptoms']; ?></td>
                            <?php if (is_array($fields) || is_object($fields)) {
                                foreach ($fields as $fv) { ?>
                            <td><?php echo $visit[$fv->name]; ?></td>
                            <?php } } ?>
                            <td class="text-end">
                                <?php if ($visit["prescription"] == 'yes') { ?>
                                <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" onclick="view_prescription('<?php echo $visit["id"]; ?>')" title="<?php echo $this->lang->line('view_prescription'); ?>">
                                    <i class="fas fa-file-prescription"></i>
                                </a>
                                <?php } ?>
                                <?php if ($result['gender'] == 'Female' && $visit["is_antenatal"] == 1 && $visit['visit_details_id'] != '') { ?>
                                <a href="#" onclick="viewantenatal('<?php echo $visit["id"]; ?>')" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_antenatal_finding'); ?>"><img width="15" src="<?php echo base_url() . 'uploads/patient_images/ultrasound-machine.png'; ?>"></a>
                                <?php } ?>
                                <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" onclick="getRecord('<?php echo $visit["id"]; ?>')">
                                    <i class="fa fa-reorder"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Medication tab ────────────────────────────────────── -->
        <div class="tab-pane card" id="medication">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('medication'); ?></h3>
            </div>
            <div class="download_label"><?php echo composePatientName($result['patient_name'], $result['pid']) . " " . $this->lang->line('opd_details'); ?></div>
            <div class="opd-med-v2">
                <?php if (!empty($medication)):
                    $med_by_date = [];
                    foreach ($medication as $mv) { $med_by_date[$mv['date']][] = $mv; }
                    krsort($med_by_date);
                    foreach ($med_by_date as $group_date => $group_items):
                        $display_date = $this->customlib->YYYYMMDDTodateFormat($group_date);
                        $day_name     = $this->lang->line(strtolower(date('l', strtotime($group_date))));
                ?>
                <div class="med-date-group">
                    <div class="med-date-header">
                        <div class="med-date-badge"><i class="fa fa-calendar"></i> <?php echo $display_date; ?></div>
                        <span class="med-date-day"><?php echo $day_name; ?></span>
                        <div class="med-date-line"></div>
                    </div>
                    <div class="med-col-header">
                        <div class="med-name-col"><?php echo $this->lang->line('medicine_name'); ?></div>
                        <div class="med-doses-label"><?php echo $this->lang->line('dose'); ?></div>
                    </div>
                    <?php foreach ($group_items as $med_value):
                        foreach ($med_value['dosage'][$group_date] as $mkey => $mvalue): ?>
                    <div class="med-row">
                        <div class="med-name-col">
                            <div class="med-name"><?php echo html_escape($mvalue['name']); ?></div>
                        </div>
                        <div class="med-doses">
                            <?php foreach ($mvalue['dose_list'] as $didx => $dose): ?>
                            <div class="dose-chip">
                                <div class="dose-time"><i class="fa fa-clock-o"></i> <?php echo $this->customlib->getHospitalTime_Format($dose['time']); ?></div>
                                <div class="dose-amount"><?php echo html_escape($dose['medicine_dosage'] . ' ' . $dose['unit']); ?></div>
                                <?php if (!empty($dose['remark'])): ?><div class="dose-remark"><?php echo html_escape($dose['remark']); ?></div><?php endif; ?>
                                <div class="dose-creator"><?php echo html_escape($dose['staff_name'] . ' ' . $dose['staff_surname'] . ' (' . $dose['staff_employee_id'] . ')'); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; endforeach; ?>
                </div>
                <?php endforeach;
                else: ?>
                <div class="dataTables_empty">
                    No data available in table <br><br>
                    <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                    <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Lab Investigation tab ─────────────────────────────── -->
        <div class="tab-pane card" id="labinvestigation">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                    <thead><tr>
                        <th><?php echo $this->lang->line('test_name'); ?></th>
                        <th><?php echo $this->lang->line('lab'); ?></th>
                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                        <th><?php echo $this->lang->line('expected_date'); ?></th>
                        <th><?php echo $this->lang->line('approved_by'); ?></th>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($investigations as $row) { ?>
                        <tr>
                            <td><?php echo $row['test_name']; ?><br><small>(<?php echo $row['short_name']; ?>)</small></td>
                            <td><?php echo $this->lang->line($row['type']); ?></td>
                            <td>
                                <?php echo composeStaffNameByString($row['collection_specialist_staff_name'], $row['collection_specialist_staff_surname'], $row['collection_specialist_staff_employee_id']); ?><br>
                                <?php echo $row['test_center']; ?><br>
                                <?php if ($row['collection_date']) { echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?>
                            </td>
                            <td><?php if ($row['reporting_date']) { echo $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); } ?></td>
                            <td>
                                <?php echo composeStaffNameByString($row['approved_by_staff_name'], $row['approved_by_staff_surname'], $row['approved_by_staff_employee_id']); ?><br>
                                <?php if ($row['parameter_update']) { echo $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']); } ?>
                            </td>
                            <td class="text-end">
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary view_report" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>"
                                   data-record-id="<?php echo $row['report_id']; ?>" data-type-id="<?php echo $row['type']; ?>" data-test-id="<?php echo $row['test_name'] . ' (' . $row['short_name'] . ')'; ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Operation Theatre tab ─────────────────────────────── -->
        <div class="tab-pane card" id="operationtheatre">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('operation'); ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('opd_visit_details'); ?>">
                    <thead><tr>
                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                        <?php if (is_array($fields_ot) || is_object($fields_ot)) {
                            foreach ($fields_ot as $fv) { ?>
                        <th><?php echo ucfirst($fv->name); ?></th>
                        <?php } } ?>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php if (!empty($operation_theatre)) {
                            foreach ($operation_theatre as $ot) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no') . $ot["id"]; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($ot["date"], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $ot["operation"]; ?></td>
                            <td><?php echo $ot["category"]; ?></td>
                            <td><?php echo $ot['ot_technician']; ?></td>
                            <?php if (!empty($fields_ot)) {
                                foreach ($fields_ot as $fv) {
                                    $df = $ot[$fv->name];
                                    if ($fv->type == "link") { $df = "<a href=" . $ot[$fv->name] . " target='_blank'>" . $ot[$fv->name] . "</a>"; } ?>
                            <td><?php echo $df; ?></td>
                            <?php } } ?>
                            <td class="text-end">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary viewot" data-bs-toggle="tooltip" data-record-id="<?php echo $ot['id']; ?>" title="<?php echo $this->lang->line('show'); ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Charges tab ───────────────────────────────────────── -->
        <div class="tab-pane card" id="charges">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
            </div>
            <div class="download_label"><?php echo composePatientName($result['patient_name'], $result['pid']) . " " . $this->lang->line('opd_details'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead><tr>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('charge_name'); ?> / <?php echo $this->lang->line('charge_note'); ?></th>
                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                        <th><?php echo $this->lang->line('qty'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $tab_total = 0;
                        if (!empty($charges_detail)) {
                            foreach ($charges_detail as $cv) {
                                $d_amt = amountFormat($cv['apply_charge'] * $cv['discount_percentage'] / 100);
                                $t_amt = amountFormat(($cv['apply_charge'] - $d_amt) * $cv['tax'] / 100);
                                $tab_total += $cv['amount']; ?>
                        <tr>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($cv['date'])); ?></td>
                            <td class="text-capitalize"><?php echo $cv['name']; ?><div class="bill_item_footer text-muted"><?php echo $cv['note']; ?></div></td>
                            <td class="text-capitalize"><?php echo $cv['charge_type']; ?></td>
                            <td class="text-capitalize"><?php echo $cv['charge_category_name']; ?></td>
                            <td class="text-capitalize"><?php echo $cv['qty']; ?></td>
                            <td class="text-end"><?php echo $cv['standard_charge']; ?></td>
                            <td class="text-end"><?php echo $cv['apply_charge']; ?></td>
                            <td class="text-end"><?php echo !empty($cv['org_charge']) ? $cv['org_charge'] : '0.00'; ?></td>
                            <td class="text-end"><?php echo $d_amt . " (" . $cv['discount_percentage'] . "%)"; ?></td>
                            <td class="text-end"><?php echo $t_amt . " (" . $cv['tax'] . "%)"; ?></td>
                            <td class="text-end"><?php echo $cv['amount']; ?></td>
                            <td class="text-end">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary print_charge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" data-record-id="<?php echo $cv['id']; ?>">
                                    <i class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                    <tr class="table-active fw-semibold">
                        <td colspan="11" class="text-end"><?php echo $this->lang->line('total') . ": " . $currency_symbol . amountFormat($tab_total); ?><input type="hidden" id="charge_total" name="charge_total" value="<?php echo $tab_total; ?>"></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ── Payment tab ───────────────────────────────────────── -->
        <div class="tab-pane card" id="payment">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>
                <div class="box-tab-tools">
                    <button type="button" class="btn btn-sm btn-primary" data-result_id="<?php echo $result['id']; ?>" data-bs-toggle="modal" data-bs-target="#payMoney">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('make_payment'); ?>
                    </button>
                </div>
            </div>
            <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead><tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $tab_total_pay = 0;
                        if (!empty($payment_details)) {
                            foreach ($payment_details as $payment) {
                                if (!empty($payment['amount'])) { $tab_total_pay += $payment['amount']; } ?>
                        <tr>
                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id') . $payment['id']; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $payment['note']; ?></td>
                            <td><?php echo $this->lang->line(strtolower($payment['payment_mode'])) . "<br>";
                                if ($payment['payment_mode'] == "Cheque") {
                                    if ($payment['cheque_no'] != '') { echo $this->lang->line('cheque_no') . ": " . $payment['cheque_no'] . "<br>"; }
                                    if ($payment['cheque_date'] != '' && $payment['cheque_date'] != '0000-00-00') { echo $this->lang->line('cheque_date') . ": " . $this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']); }
                                } ?></td>
                            <td class="text-end"><?php echo $payment['amount']; ?></td>
                            <td class="text-end">
                                <?php if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "") { ?>
                                <a href="<?php echo site_url('patient/dashboard/downloadreceipt/' . $payment['id']); ?>" class="btn btn-sm btn-outline-secondary" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                <?php } ?>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary print_trans" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" data-module-type="opd" data-record-id="<?php echo $payment['id']; ?>"><i class="fa fa-print"></i></a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                    <?php if (!empty($payment_details)) { ?>
                    <tr class="table-active fw-semibold">
                        <td colspan="4" class="text-end"><?php echo $this->lang->line('total') . ": " . $currency_symbol . amountFormat($tab_total_pay); ?></td>
                        <td></td><td></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <!-- ── Live Consult tab ──────────────────────────────────── -->
        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
        <div class="tab-pane card" id="live_consult">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead><tr>
                        <th><?php echo $this->lang->line('consultation_title'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('created_by'); ?></th>
                        <th><?php echo $this->lang->line('created_for'); ?></th>
                        <th><?php echo $this->lang->line('patient'); ?></th>
                        <th><?php echo $this->lang->line('status'); ?></th>
                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($visitconferences)) { ?>
                        <tr><td colspan="7" class="dataTables_empty">
                            No data available in table <br><br>
                            <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                            <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                        </td></tr>
                        <?php } else {
                            foreach ($visitconferences as $cv) {
                            $return_response = isJSON($cv->return_response) ? json_decode($cv->return_response) : false; ?>
                        <tr>
                            <td>
                                <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $cv->title; ?></a>
                                <div class="fee_detail_popover d-none" >
                                    <?php if ($cv->description == "") { ?>
                                    <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                    <?php } else { ?>
                                    <p class="text text-info"><?php echo $cv->description; ?></p>
                                    <?php } ?>
                                </div>
                            </td>
                            <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($cv->date)); ?></td>
                            <td><?php
                                $n = ($cv->create_by_surname == "") ? $cv->create_by_name : $cv->create_by_name . " " . $cv->create_by_surname;
                                if ($cv->staff_create_by_role_id == 7) {
                                    if ($superadmin_restriction == 'enabled') { echo $n . " (" . $cv->create_by_employee_id . ")"; }
                                } else {
                                    if (IsNullOrEmptyString($cv->create_by_name)) { echo $this->lang->line('patient'); }
                                    else { echo $n . " (" . $cv->create_by_role_name . ": " . $cv->create_by_employee_id . ")"; }
                                } ?></td>
                            <td><?php $n = ($cv->create_for_surname == "") ? $cv->create_for_name : $cv->create_for_name . " " . $cv->create_for_surname;
                                echo $n . " (" . $cv->for_create_role_name . ": " . $cv->for_create_employee_id . ")"; ?></td>
                            <td><?php $n = ($cv->patient_name == "") ? $cv->patient_name : $cv->patient_name;
                                echo $n . " (" . $cv->patientid . ")"; ?></td>
                            <td>
                                <form class="chgstatus_form" method="POST" action="<?php echo site_url('admin/conference/chgstatus'); ?>">
                                    <input type="hidden" name="conference_id" value="<?php echo $cv->id; ?>">
                                    <select class="form-select form-select-sm chgstatus_dropdown" disabled name="chg_status">
                                        <option value="0" <?php if ($cv->status == 0) echo "selected"; ?>><?php echo $this->lang->line('awaited'); ?></option>
                                        <option value="1" <?php if ($cv->status == 1) echo "selected"; ?>><?php echo $this->lang->line('cancelled'); ?></option>
                                        <option value="2" <?php if ($cv->status == 2) echo "selected"; ?>><?php echo $this->lang->line('finished'); ?></option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <?php if ($cv->status == 0 && $return_response) { ?>
                                <a href="<?php echo $return_response->start_url; ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $this->lang->line('join'); ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>

        <!-- ── Timeline tab ──────────────────────────────────────── -->
        <div class="tab-pane card" id="timeline">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
            </div>
            <div class="timeline-header no-border">
                <div id="timeline_list">
                    <?php if (empty($timeline_list)): ?>
                    <div class="dataTables_empty">
                        No data available in table <br><br>
                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                    </div>
                    <?php else: ?>
                    <div class="rl-tl sh-pt-8" >
                        <?php foreach ($timeline_list as $value): ?>
                        <div class="rl-tl-item">
                            <div class="rl-tl-dot"></div>
                            <div class="rl-tl-card">
                                <div class="rl-tl-meta">
                                    <span class="rl-tl-date"><i class="far fa-clock me-1"></i><?php if ($value['timeline_date']) { echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); } ?></span>
                                    <span class="rl-tl-actions">
                                        <?php if (!empty($value['document'])): ?>
                                        <a class="btn btn-sm btn-secondary" href="<?php echo base_url() . 'patient/dashboard/download_patient_timeline/' . $value['id']; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                        <?php endif; ?>
                                        <?php if ($value['generated_users_type'] == 'patient'): ?>
                                        <a class="btn btn-sm btn-secondary" onclick="editTimeline('<?php echo $value['id']; ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                                        <a class="btn btn-sm btn-secondary text-danger" onclick="delete_timeline('<?php echo $value['id']; ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="rl-tl-title"><?php echo html_escape($value['title']); ?></div>
                                <?php if (!empty($value['description'])): ?><div class="rl-tl-body"><?php echo $value['description']; ?></div><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Vitals tab ────────────────────────────────────────── -->
        <div class="tab-pane card" id="vitals">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
            </div>
            <div class="table_inner table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr>
                        <th><?php echo $this->lang->line("date"); ?></th>
                        <?php foreach ($vital_list as $vl) { ?>
                        <th><?php echo $vl["name"]; ?><br>(<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>)</th>
                        <?php } ?>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($patient_vital_date)) { ?>
                        <tr><td colspan="<?php echo (is_array($vital_list) ? count($vital_list) : 0) + 1; ?>" class="dataTables_empty">
                            No data available in table <br><br>
                            <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                            <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                        </td></tr>
                        <?php } else {
                            foreach ($patient_vital_date as $pvd) { ?>
                        <tr>
                            <th><?php $messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
                                echo $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']); ?></th>
                            <?php foreach ($vital_list as $vl) {
                                $vid = $vl["id"]; ?>
                            <td class="tablehovericon">
                                <div class="relative">
                                    <?php foreach ($patientvital[$messure_date][$vid] as $pmi) {
                                        echo $pmi['patient_range'];
                                        $dt = $this->customlib->YYYYMMDDHisTodateFormat($pmi['messure_date'], $this->customlib->getHospitalTimeFormat());
                                        echo " (" . substr($dt, 10) . ")<br>";
                                    } ?>
                                </div>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /tab-content -->
</div><!-- /opd-profile-wrap -->
<?php } ?>

<!-- ── Modals ────────────────────────────────────────────────────── -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('visit_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="prescriptionview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id="edit_deleteprescription" class="d-flex align-items-center gap-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background" id="getdetails_prescription"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="findingview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('antenatal_finding'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="getdetails_finding"></div>
        </div>
    </div>
</div>

<div id="payMoney" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('make_payment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payment_form" class="form-horizontal modal_payment" method="POST">
                <div class="modal-body modal-background">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('make_payment'); ?></span>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="mb-3 col-12">
                                    <label class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                                    <input type="text" class="form-control" value="<?php echo amountFormat($total - $total_payment); ?>" name="deposit_amount" id="amount_total_paid">
                                    <input type="hidden" name="net_amount" value="<?php echo amountFormat($total - $total_payment); ?>">
                                    <input type="hidden" name="payment_for" value="opd">
                                    <input type="hidden" name="id" value="<?php echo !empty($visit_details[0]["opd_details_id"]) ? $visit_details[0]["opd_details_id"] : ''; ?>">
                                    <span id="deposit_amount_error" class="text-danger"><?php echo form_error('deposit_amount'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button id="pay_button" class="btn btn-primary"><?php echo $this->lang->line('add'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewDetailReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"><?php echo $this->lang->line('lab_investigation_details'); ?></h5>
                <div id="action_detail_report_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-background">
                <div id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="view_ot_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line('operation_details'); ?></h5>
                <div id="action_detail_modal"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background">
                    <div id="show_ot_data"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ── JavaScript ────────────────────────────────────────────────── -->
<script>
    function toggleRellist(btn) {
        btn.closest('.rellist').classList.toggle('collapsed');
    }

    function holdModal(modalId) {
        var el = document.getElementById(modalId);
        if (el) { bootstrap.Modal.getOrCreateInstance(el, {backdrop: 'static', keyboard: false}).show(); }
    }

    function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getantenatalprescription/' + visitid,
            success: function (res) { $("#getdetails_finding").html(res); },
            error: function () { alert("<?php echo $this->lang->line('fail'); ?>"); }
        });
        holdModal('findingview');
    }

    function getRecord(visitid) {
        $.ajax({
            url: base_url + 'patient/dashboard/getopdrecheckupDetails',
            type: "POST",
            data: {visit_id: visitid},
            dataType: 'json',
            success: function (data) {
                $('#viewModal .modal-body').html(data.page);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal')).show();
            },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); }
        });
    }

    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + visitid,
            success: function (res) {
                $("#edit_deleteprescription").html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>' onclick='printprescription(" + visitid + ")'><i class='fa fa-print'></i></a>");
                $("#getdetails_prescription").html(res);
                holdModal('prescriptionview');
            },
            error: function () { alert("<?php echo $this->lang->line('fail'); ?>"); }
        });
    }

    function printprescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + visitid,
            type: 'POST',
            data: {payslipid: visitid, print: 'yes'},
            success: function (result) { popup(result); }
        });
    }

    function delete_record(opdid) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOPD',
                type: "POST", data: {opdid: opdid}, dataType: 'json',
                success: function () {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            });
        }
    }

    function deleteOpdPatientDiagnosis(patient_id, id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOpdPatientDiagnosis/' + patient_id + '/' + id,
                success: function () {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            });
        }
    }

    function deleteOpdPatientDiagnosis1(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function () { successMsg(Msg); window.location.reload(true); }
            });
        }
    }

    function getMedicineName(id) {
        var category_selected = $("#medicine_cat" + id).val();
        div_data = '';
        $("#search-query" + id).html("<option value='l'><?php echo $this->lang->line('loading'); ?></option>");
        $('#search-query' + id).select2("val", 'l');
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_name",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) { div_data += "<option value='" + obj.medicine_name + "'>" + obj.medicine_name + "</option>"; });
                $("#search-query" + id).html("<option value=''><?= $this->lang->line('select') ?></option>");
                $('#search-query' + id).append(div_data);
                $('#search-query' + id).select2("val", '');
            }
        });
    }

    $(document).on('click', '.print_charge', function () {
        var $this = $(this), record_id = $this.data('recordId');
        $this.button('loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/printCharge',
            type: "POST", data: {'id': record_id, 'type': 'opd'}, dataType: 'json',
            success: function (res) { popup(res.page); },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    $(document).on('click', '.print_trans', function () {
        var $this = $(this), record_id = $this.data('recordId'), module_type = $(this).attr('data-module-type');
        $this.button('loading');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/printTransaction',
            type: "POST", data: {'id': record_id, 'module_type': module_type}, dataType: 'json',
            success: function (res) { popup(res.page); },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    $(document).on('click', '.view_report', function () {
        var id = $(this).data('recordId'), lab = $(this).data('typeId'), test = $(this).data('testId');
        getinvestigationparameter(id, $(this), lab, test);
    });

    function getinvestigationparameter(id, btn_obj, lab, test) {
        var $this = btn_obj, modal_view = $('#viewDetailReportModal');
        $.ajax({
            url: base_url + 'patient/dashboard/getinvestigationparameter',
            type: "POST", data: {'id': id, 'lab': lab}, dataType: 'json',
            beforeSend: function () { $this.button('loading'); modal_view.addClass('modal_loading'); },
            success: function (data) {
                $('#viewDetailReportModal .modal-body').html(data.page);
                $('#viewDetailReportModal #action_detail_report_modal').html(data.actions).find('a, button').not('.btn-close').addClass('btn btn-sm btn-light');
                $('#viewDetailReportModal #modal_head').html(test);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDetailReportModal'), {backdrop: 'static'}).show();
                modal_view.removeClass('modal_loading');
            },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.button('reset'); modal_view.removeClass('modal_loading'); },
            complete: function () { $this.button('reset'); modal_view.removeClass('modal_loading'); }
        });
    }

    $(document).on('click', '.viewot', function () {
        var $this = $(this), record_id = $this.data('recordId');
        $this.button('loading');
        $.ajax({
            url: base_url + 'patient/dashboard/otdetails',
            type: "POST", data: {ot_id: record_id}, dataType: 'json',
            success: function (data) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('view_ot_modal'), {backdrop: 'static'}).show();
                $('#show_ot_data').html(data.page);
                $('#action_detail_modal').html(data.actions).find('a, button').not('.btn-close').addClass('btn btn-sm btn-light');
            },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    $('#payMoney').on('hidden.bs.modal', function () { $(this).find('form').trigger('reset'); });

    $('#pay_button').click(function () {
        $.ajax({
            url: base_url + 'patient/pay/checkvalidate',
            type: "POST", data: new FormData($('#payment_form')[0]),
            dataType: 'json', cache: false, processData: false, contentType: false,
            success: function (data) {
                if (data.status == "fail") {
                    var message = ""; $.each(data.error, function (i, v) { message += v; }); errorMsg(message);
                } else {
                    window.location.replace(base_url + 'patient/pay');
                }
            }
        });
    });

    $(document).ready(function () {
        $("#add_payment").on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>patient/pay/opdpay',
                type: "POST", data: new FormData(this), dataType: 'json',
                contentType: false, cache: false, processData: false,
                beforeSend: function () { $("#add_paymentbtn").button("loading"); },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = ""; $.each(data.error, function (i, v) { message += v; }); errorMsg(message);
                    } else {
                        successMsg(data.message); window.location.reload(true);
                    }
                    $("#add_paymentbtn").button("reset");
                },
                error: function () { $("#add_paymentbtn").button('reset'); },
                complete: function () { $("#add_paymentbtn").button('reset'); }
            });
        });

        /* BS5 tab hash support */
        var hash = window.location.hash;
        if (hash) {
            var t = document.querySelector('.ph-tabs a[href="' + hash + '"]');
            if (t) { bootstrap.Tab.getOrCreateInstance(t).show(); }
        }
        $(document).on('shown.bs.tab', '.ph-tabs a[data-bs-toggle="tab"]', function (e) {
            history.replaceState(null, null, $(e.target).attr('href'));
        });
    });
</script>
</div>

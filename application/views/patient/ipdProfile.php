<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$patient_id = $id;
$case_reference_id=$result['case_reference_id'];
?>
<div class="container-fluid px-1 py-1">
    <?php
    $charge_total = 0;
    $bill_amount  = 0;
    if (!isset($result)) {
        echo "<h4 class='text-center'>" . $this->lang->line("no_record_found") . "</h4>";
    } else {
        foreach ($charges as $charge) {
            $charge_total += $charge["apply_charge"];
            $bill_amount = $charge_total - $paid_amount;
        }
    ?>

    <!-- Patient header (OPD-style) -->
    <?php
    $image       = $result['image'];
    $ipd_has_img = !empty($image) && strpos($image, 'no_image') === false;
    $file        = $ipd_has_img ? $image : 'uploads/patient_images/no_image.png';
    if (!$ipd_has_img) {
        $_ip_parts    = preg_split('/\s+/', trim($result['patient_name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $_ip_initials = count($_ip_parts) === 0 ? '?' : (count($_ip_parts) === 1
            ? mb_strtoupper(mb_substr($_ip_parts[0], 0, 1))
            : mb_strtoupper(mb_substr($_ip_parts[0], 0, 1) . mb_substr($_ip_parts[count($_ip_parts) - 1], 0, 1)));
    }
    ?>
    <div class="opd-profile-wrap">
        <header class="page-head">
            <div class="ph-title-row">
                <div class="ph-title">
                    <?php if ($ipd_has_img): ?>
                    <img src="<?php echo $this->media_storage->getImageURL($file); ?>" alt="Patient" class="ph-av sh-ipd-patient-avatar">
                    <?php else: ?>
                    <div class="ph-av"><?php echo html_escape($_ip_initials); ?></div>
                    <?php endif; ?>
                    <div>
                        <h1><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></h1>
                        <div class="sub">
                            <?php echo $this->lang->line(strtolower($result['gender'])); ?>
                            <span class="dot-sep"><?php echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']); ?></span>
                            <span class="dot-sep"><?php echo html_escape($result['mobileno']); ?></span>
                            <span class="dot-sep"><?php echo $this->lang->line('guardian'); ?>: <?php echo html_escape($result['guardian_name']); ?></span>
                        </div>
                    </div>
                </div>
                <div class="ph-actions">
                    <a class="btn btn-sm ph-act ph-act-profile" href="javascript:void(0)" onclick="getRecord('<?php echo $ipdid ?>')" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('profile') ?>">
                        <i class="fa fa-reorder"></i>
                    </a>
                    <a class="btn btn-sm ph-act ph-act-history" href="<?php echo base_url()?>patient/dashboard/patientipddetails" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('previous_ipd') ?>">
                        <i class="fa fa-hospital-o"></i>
                    </a>
                </div>
            </div>
            <div class="bcard-grid bcard-grid-5 sh-ipd-bcard-divider">
                <div class="field"><div class="l"><?php echo $this->lang->line('gender'); ?></div><div class="v"><?php echo $this->lang->line(strtolower($result['gender'])); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('age'); ?></div><div class="v"><?php echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('guardian_name'); ?></div><div class="v"><?php echo html_escape($result['guardian_name']); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('phone'); ?></div><div class="v"><?php echo html_escape($result['mobileno']); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('case_id'); ?></div><div class="v"><?php echo html_escape($result['case_reference_id']); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('ipd_no'); ?></div><div class="v"><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$result['ipdid']; ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('admission_date'); ?></div><div class="v"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $time_format); ?></div></div>
                <div class="field"><div class="l"><?php echo $this->lang->line('bed'); ?></div><div class="v"><?php echo html_escape($result['bed_name'] . " - " . $result['bedgroup_name'] . " - " . $result['floor_name']); ?></div></div>
                <?php if (file_exists("./uploads/patient_id_card/barcodes/" . $id . ".png")) { ?>
                <div class="field"><div class="l"><?php echo $this->lang->line('barcode'); ?></div><div class="v"><a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>" target="_blank"><img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>" width="90" height="24"></a></div></div>
                <?php } ?>
                <?php if (file_exists("./uploads/patient_id_card/qrcode/" . $id . ".png")) { ?>
                <div class="field"><div class="l"><?php echo $this->lang->line('qrcode'); ?></div><div class="v"><a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>" target="_blank"><img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>" width="40" height="40"></a></div></div>
                <?php } ?>
            </div>
            <div class="ph-tabs-wrap">
            <button class="ph-tabs-arrow d-none" id="ph_tabs_prev" type="button"><i class="fa fa-chevron-left"></i></button>
            <nav class="ph-tabs" id="ph_tabs_nav" role="tablist">
                <a class="active" data-bs-toggle="tab" data-bs-target="#overview" href="#overview"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview');?></a>
                <a data-bs-toggle="tab" data-bs-target="#nurse_note" href="#nurse_note"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('nurse_notes'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#medication" href="#medication"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#prescription" href="#prescription"><i class="fas fa-file-prescription"></i> <?php echo $this->lang->line('prescription'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#consultant_register" href="#consultant_register"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('consultant_register'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#labinvestigation" href="#labinvestigation"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#operationtheatre" href="#operationtheatre"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#charges" href="#charges"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#payment" href="#payment"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payment'); ?></a>
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <a data-bs-toggle="tab" data-bs-target="#live_consult" href="#live_consult"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('live_consultation'); ?></a>
                <?php } ?>
                <a data-bs-toggle="tab" data-bs-target="#bed_history" href="#bed_history"><i class="fas fa-procedures"></i> <?php echo $this->lang->line("bed_history"); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#timeline" href="#timeline"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#treatment_history" href="#treatment_history"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('treatment_history'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#vitals" href="#vitals"><i class="fas fa-heartbeat"></i> <?php echo $this->lang->line('vitals'); ?></a>
                <?php if($result['is_antenatal']==1){ ?>
                <a data-bs-toggle="tab" data-bs-target="#obstetric_history" href="#obstetric_history"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('previous_obstetric_history'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#post_antenatal" href="#post_antenatal"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('postnatal_history'); ?></a>
                <a data-bs-toggle="tab" data-bs-target="#addantenatal" href="#addantenatal"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('antenatal'); ?></a>
                <?php } ?>
            </nav>
            <button class="ph-tabs-arrow" id="ph_tabs_next" type="button"><i class="fa fa-chevron-right"></i></button>
            </div><!-- /.ph-tabs-wrap -->
        </header>

        <?php if (($bill_amount != 0) && ($bill_amount >= $result["ipdcredit_limit"])) { ?>
        <div class="alert alert-danger rounded-0 mb-0"><?php echo $this->lang->line('credit_limit_exeeded'); ?></div>
        <?php } ?>

        <div class="tab-content p-3">

            <!-- overview -->
            <div class="tab-pane show active" id="overview">
            <div class="rellist-wrap">

            <?php if (!empty($result['known_allergies']) || !empty($result['symptoms'])) { ?>
            <div class="rellist sh-patient-rellist-warn">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic red">!</div>
                        <div class="title sh-patient-allergies-title"><?php echo $this->lang->line('known_allergies'); ?></div>
                    </div>
                </div>
                <div class="rellist-body pad d-flex gap-2 flex-wrap">
                    <?php if (!empty($result['known_allergies'])) { ?>
                    <div class="rellist-pill red"><?php echo html_escape($result['known_allergies']); ?></div>
                    <?php } ?>
                    <?php if (!empty($result['symptoms'])) { ?>
                    <div class="rellist-pill amber"><?php echo html_escape($result['symptoms']); ?></div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

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
                    <div class="d-flex gap-4 align-items-start flex-wrap">
                        <div class="billing-graph-container sh-ipd-graph-container">
                            <div class="sh-ipd-graph-canvas-wrap"><canvas id="pieChart" class="sh-ipd-graph-canvas"><span></span></canvas></div>
                            <p class="small mb-0 mt-2 sh-ipd-credit-label"><?php echo $this->lang->line('credit_limit'); ?>: <?php echo $currency_symbol.$credit_limit; ?></p>
                            <p class="small mb-0 text-danger"><?php echo $this->lang->line('used_credit_limit'); ?>: <?php echo $currency_symbol.$used_credit_limit; ?></p>
                            <p class="small mb-0 sh-ipd-credit-balance"><?php echo $this->lang->line('balance_credit_limit'); ?>: <?php echo $currency_symbol.$balance_credit_limit; ?></p>
                        </div>
                        <div class="flex-grow-1">
                    <div class="row g-3">
                        <?php if ($this->module_lib->hasActive('ipd') && $this->module_lib->hasPatientActive('ipd')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('ipd_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['ipd']['ipd_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['ipd']['payment']['total_payment'],$graph['ipd']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['ipd']['ipd_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                        <?php if ($this->module_lib->hasActive('pharmacy') && $this->module_lib->hasPatientActive('pharmacy')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('pharmacy_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill(($graph['pharmacy']['payment']['total_payment']-$graph['pharmacy']['payment_refund']['total_payment']),$graph['pharmacy']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                        <?php if ($this->module_lib->hasActive('pathology') && $this->module_lib->hasPatientActive('pathology')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('pathology_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['pathology']['pathology_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['pathology']['payment']['total_payment'],$graph['pathology']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['pathology']['pathology_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                        <?php if ($this->module_lib->hasActive('radiology') && $this->module_lib->hasPatientActive('radiology')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('radiology_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['radiology']['radiology_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['radiology']['payment']['total_payment'],$graph['radiology']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['radiology']['radiology_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                        <?php if ($this->module_lib->hasActive('blood_bank') && $this->module_lib->hasPatientActive('blood_bank')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('blood_bank_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['blood_bank']['payment']['total_payment'],$graph['blood_bank']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                        <?php if ($this->module_lib->hasActive('ambulance') && $this->module_lib->hasPatientActive('ambulance')) { ?>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-1 sh-ipd-bill-chart-heading"><?php echo $this->lang->line('ambulance_billing_payment_graph'); ?></h6>
                            <p class="text-muted small mb-1"><?php echo $graph['ambulance']['ambulance_bill_payment_ratio'];?>%<span class="float-end"><?php echo $this->customlib->get_payment_bill($graph['ambulance']['payment']['total_payment'],$graph['ambulance']['bill']['total_bill']);?></span></p>
                            <div class="progress sh-ipd-progress-thin"><div class="progress-bar bg-info" style="width:<?php echo $graph['ambulance']['ambulance_bill_payment_ratio'];?>%"></div></div>
                        </div>
                        <?php } ?>
                    </div>
                        </div><!-- flex-grow-1 -->
                    </div><!-- d-flex billing -->
                </div>
            </div>

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
                $height=""; $weight=""; $bmi=""; $class1="";
                foreach($patientcurrentvital as $value){
                    $class="badge bg-success";
                    $vitalrange=$this->lang->line('normal');
                    $reference_range = $value['reference_range'];
                    if(strpos($value['reference_range'], '-') !== false){
                        $range = explode("-",$value['reference_range']);
                        $min_val = $range[0];
                        $max_val = $range[1];
                        if(strpos($value['patient_range'], '-') !== false){
                            $vital = explode("-",$value['patient_range']);
                            $min_vital = $vital[0];
                            $max_vital = $vital[1];
                            $vitalrange=$this->lang->line('normal');
                            $class="badge bg-success";
                            if($min_vital < $min_val){ $vitalrange=$this->lang->line('low'); $class="badge bg-danger"; }
                            if($max_vital > $max_val){ $vitalrange=$this->lang->line('high'); $class="badge bg-danger"; }
                        }else{
                            if($value['patient_range'] < $min_val){ $vitalrange=$this->lang->line('low'); $class="badge bg-danger"; }
                            if($value['patient_range'] > $max_val){ $vitalrange=$this->lang->line('high'); $class="badge bg-danger"; }
                        }
                    }
                    if(($value['id']=='1') && $value['patient_range']!=""){
                        if(strpos($value['patient_range'], '-') !== false){ $r=explode("-",$value['patient_range']); $height=(float)$r[0]; }
                        else{ $height=(float)$value['patient_range']; }
                    }
                    if(($value['id']=='2') && $value['patient_range']!=""){
                        if(strpos($value['patient_range'], '-') !== false){ $r=explode("-",$value['patient_range']); $weight=(float)$r[0]; }
                        else{ $weight=(float)$value['patient_range']; }
                    }
                    if($weight!="" && $height!=""){
                        $h1=$height*0.01; $bmiH=($h1*$h1);
                        if($bmiH>0){ $bmi=round($weight/$bmiH,2); }else{ $bmi=''; }
                        $class1="badge bg-success";
                    }
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="vital-card sh-ipd-vital-card">
                        <div class="sh-ipd-vital-label"><?php echo html_escape($value['name']); ?></div>
                        <div class="sh-ipd-vital-value"><?php echo html_escape($value['patient_range']); ?><span class="sh-ipd-vital-unit"> <?php echo html_escape($value['unit']); ?></span></div>
                        <div class="sh-ipd-vital-range"><span class="<?php echo $class; ?>"><?php echo $vitalrange; ?></span> <span class="text-muted sh-ipd-vital-date"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['messure_date'],$this->customlib->getHospitalTimeFormat()); ?></span></div>
                    </div>
                </div>
                <?php } ?>
                <?php if($weight!="" && $height!=""){ ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="vital-card sh-ipd-vital-card">
                        <div class="sh-ipd-vital-label"><?php echo $this->lang->line('bmi'); ?></div>
                        <div class="sh-ipd-vital-value"><?php echo $bmi; ?></div>
                        <div class="sh-ipd-vital-range"><span class="<?php echo $class1; ?>"><?php echo $bmi; ?></span></div>
                    </div>
                </div>
                <?php } ?>
                </div>
                </div>
            </div>
            <?php } ?>

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
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo $this->media_storage->getImageURL(!empty($result['doctor_image']) ? 'uploads/staff_images/'.$result['doctor_image'] : 'uploads/staff_images/no_image.png'); ?>"
                                 class="sh-ipd-doctor-avatar">
                            <span class="sh-ipd-doctor-name"><?php echo $result['name']." ".$result['surname']." (".$result['employee_id'].")"; ?></span>
                        </div>
                        <?php foreach ($doctors_ipd as $dkey => $dvalue) { ?>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo $this->media_storage->getImageURL(!empty($dvalue['image']) ? 'uploads/staff_images/'.$dvalue['image'] : 'uploads/staff_images/no_image.png'); ?>"
                                 class="sh-ipd-doctor-avatar">
                            <span class="sh-ipd-doctor-name"><?php echo $dvalue['ipd_doctorname']." ".$dvalue['ipd_doctorsurname']." (".$dvalue['employee_id'].")"; ?></span>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('nurse_notes'); ?></div>
                        <?php if(!empty($nurse_note)){ ?><div class="count"><?php echo count($nurse_note); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body pad">
                    <?php if (empty($nurse_note)) { ?>
                    <div class="text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } else { ?>
                    <ul class="overview-timeline">
                        <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($nurse_note[$i])) {
                            $nid = $nurse_note[$i]['id']; ?>
                        <li>
                            <div class="tl-date"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($nurse_note[$i]['date']); ?></div>
                            <div class="tl-body">
                                <div class="tl-dot"></div>
                                <div>
                                    <div class="tl-title"><?php echo $nurse_note[$i]['name'].' '.$nurse_note[$i]['surname']." ( ".$nurse_note[$i]['employee_id']." )"; ?></div>
                                    <div class="tl-desc"><?php echo $this->lang->line('note').": ".nl2br($nurse_note[$i]['note']); ?></div>
                                    <div class="tl-desc"><?php echo $this->lang->line('comment').": ".nl2br($nurse_note[$i]['comment']); ?></div>
                                    <?php foreach ($nursenote[$nid] as $ckey => $cvalue) {
                                        if (!empty($cvalue['staffname'])) {
                                            $comment_by=" (".$cvalue['staffname']." ".$cvalue['staffsurname']. ": ".$cvalue['employee_id'].")";
                                            $comment_date=$this->customlib->YYYYMMDDHisTodateFormat($cvalue['created_at'],$this->customlib->getHospitalTimeFormat());
                                        } ?>
                                    <div class="tl-desc"><?php echo nl2br($cvalue['comment_staff']); ?><span class="float-end text-muted small"><?php echo $comment_date." ".$comment_by; ?></span></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                        <?php } } ?>
                    </ul>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.5 12.5l8-8a4.94 4.94 0 1 1 7 7l-8 8a4.94 4.94 0 1 1-7-7Z"/><path d="m8.5 8.5 7 7"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('medication'); ?></div>
                        <?php if(!empty($medicationreport_overview)){ ?><div class="count"><?php echo count($medicationreport_overview); ?></div><?php } ?>
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
                            <th><?php echo $this->lang->line('date');?></th>
                            <th><?php echo $this->lang->line('medicine_name');?></th>
                            <th><?php echo $this->lang->line('dose');?></th>
                            <th><?php echo $this->lang->line('time');?></th>
                            <th><?php echo $this->lang->line('remark');?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if(!empty($medicationreport_overview[$i])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($medicationreport_overview[$i]['date']); ?></td>
                                <td><?php echo $medicationreport_overview[$i]['medicine_name']; ?></td>
                                <td><?php echo $medicationreport_overview[$i]['medicine_dosage']." (".$medicationreport_overview[$i]['unit'].")";?></td>
                                <td><?php echo $this->customlib->getHospitalTime_Format($medicationreport_overview[$i]['time']);?></td>
                                <td><?php echo $medicationreport_overview[$i]['remark'];?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('prescription'); ?></div>
                        <?php if(!empty($prescription_detail)){ ?><div class="count"><?php echo count($prescription_detail); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($prescription_detail)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('prescription_no'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('finding'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($prescription_detail[$i])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_prescription').$prescription_detail[$i]["id"]; ?></td>
                                <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($prescription_detail[$i]['date'])); ?></td>
                                <td><?php echo $prescription_detail[$i]['finding_description']; ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('consultant_register'); ?></div>
                        <?php if(!empty($consultant_register)){ ?><div class="count"><?php echo count($consultant_register); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($consultant_register)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('applied_date'); ?></th>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <th><?php echo $this->lang->line('instruction'); ?></th>
                            <th><?php echo $this->lang->line('instruction_date'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($consultant_register[$i])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($consultant_register[$i]['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                <td><?php echo $consultant_register[$i]["name"]." ".$consultant_register[$i]["surname"]." (".$consultant_register[$i]["employee_id"].")"; ?></td>
                                <td><?php echo nl2br($consultant_register[$i]["instruction"]); ?></td>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($consultant_register[$i]['ins_date']); ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2v7l-5 9a2 2 0 0 0 1.73 3h12.54A2 2 0 0 0 20 18L15 9V2"/><path d="M8 2h8M7 16h10"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('lab_investigations'); ?></div>
                        <?php if(!empty($investigations)){ ?><div class="count"><?php echo count($investigations); ?></div><?php } ?>
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
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if(!empty($investigations[$i])) { ?>
                            <tr>
                                <td><?php echo $investigations[$i]['test_name']; ?><br><small>(<?php echo $investigations[$i]['short_name']; ?>)</small></td>
                                <td><?php echo $this->lang->line($investigations[$i]['type']); ?></td>
                                <td>
                                    <?php echo composeStaffNameByString($investigations[$i]['collection_specialist_staff_name'],$investigations[$i]['collection_specialist_staff_surname'],$investigations[$i]['collection_specialist_staff_employee_id']); ?>
                                    <br><small><?php if($investigations[$i]['type']=='pathology'){ echo $this->lang->line('pathology'); }else{ echo $this->lang->line('radiology'); } ?>: <?php echo $investigations[$i]['test_center']; ?></small>
                                    <?php if($investigations[$i]['collection_date']){ ?><br><?php echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['collection_date']); }?>
                                </td>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['reporting_date']); ?></td>
                                <td>
                                    <?php echo composeStaffNameByString($investigations[$i]['approved_by_staff_name'],$investigations[$i]['approved_by_staff_surname'],$investigations[$i]['approved_by_staff_employee_id']); ?>
                                    <?php if($investigations[$i]['parameter_update']){ ?><br><?php echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['parameter_update']); }?>
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

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 7 10 10M7 17 17 7"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('operation'); ?></div>
                        <?php if(!empty($operation_theatre)){ ?><div class="count"><?php echo count($operation_theatre); ?></div><?php } ?>
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
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($operation_theatre[$i])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no').$operation_theatre[$i]["id"]; ?></td>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($operation_theatre[$i]["date"],$this->customlib->getHospitalTimeFormat()); ?></td>
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

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('charges'); ?></div>
                        <?php if(!empty($charges)){ ?><div class="count"><?php echo count($charges); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($charges)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('charge_type'); ?></th>
                            <th><?php echo $this->lang->line('charge_category'); ?></th>
                            <th><?php echo $this->lang->line('qty'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')'; ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($charges[$i])) {
                            $tax_amount=calculatePercent($charges[$i]['apply_charge'],$charges[$i]['tax']);
                            $taxamount=amountFormat($tax_amount); ?>
                            <tr>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($charges[$i]['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                <td class="text-capitalize"><?php echo $charges[$i]["name"]; ?><div class="text-muted small"><?php if($charges[$i]["note"]!=''){ echo $this->lang->line('charge_note').': '.$charges[$i]["note"]; } ?></div></td>
                                <td class="text-capitalize"><?php echo $charges[$i]["charge_type"]; ?></td>
                                <td class="text-capitalize"><?php echo $charges[$i]["charge_category_name"]; ?></td>
                                <td class="text-capitalize"><?php echo $charges[$i]['qty']." ".$charges[$i]["unit"]; ?></td>
                                <td class="text-end"><?php echo number_format($charges[$i]["amount"],2); ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('payment'); ?></div>
                        <?php if(!empty($payment_details)){ ?><div class="count"><?php echo count($payment_details); ?></div><?php } ?>
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
                            <th class="text-end"><?php echo $this->lang->line('paid_amount')." (".$currency_symbol.")"; ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if (!empty($payment_details[$i]) && !empty($payment_details[$i]['amount'])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment_details[$i]['id']; ?></td>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment_details[$i]['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                <td><?php echo $payment_details[$i]["note"]; ?></td>
                                <td class="text-capitalize"><?php echo $this->lang->line(strtolower($payment_details[$i]["payment_mode"]));
                                    if($payment_details[$i]['payment_mode']=="Cheque"){
                                        if($payment_details[$i]['cheque_no']!=''){ echo "<br>".$this->lang->line("cheque_no").": ".$payment_details[$i]['cheque_no']; }
                                        if($payment_details[$i]['cheque_date']!='' && $payment_details[$i]['cheque_date']!='0000-00-00'){ echo "<br>".$this->lang->line("cheque_date").": ".$this->customlib->YYYYMMDDTodateFormat($payment_details[$i]['cheque_date']); }
                                    } ?></td>
                                <td class="text-end"><?php echo $payment_details[$i]["amount"]; ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('live_consultation'); ?></div>
                        <?php if(!empty($ipdconferences)){ ?><div class="count"><?php echo count($ipdconferences); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($ipdconferences)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('consultation_title'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('created_by'); ?></th>
                            <th><?php echo $this->lang->line('created_for'); ?></th>
                            <th><?php echo $this->lang->line('patient'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if(!empty($ipdconferences[$i])){
                            $return_response=json_decode($ipdconferences[$i]->return_response); ?>
                            <tr>
                                <td><?php echo $ipdconferences[$i]->title; ?></td>
                                <td><?php echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($ipdconferences[$i]->date)); ?></td>
                                <td><?php $cname=($ipdconferences[$i]->create_by_surname=="")?$ipdconferences[$i]->create_by_name:$ipdconferences[$i]->create_by_name." ".$ipdconferences[$i]->create_by_surname; echo $cname." (".$ipdconferences[$i]->create_by_role_name.": ".$ipdconferences[$i]->create_by_employee_id.")"; ?></td>
                                <td><?php $fname=($ipdconferences[$i]->create_for_surname=="")?$ipdconferences[$i]->create_for_name:$ipdconferences[$i]->create_for_name." ".$ipdconferences[$i]->create_for_surname; echo $fname." (".$ipdconferences[$i]->create_for_role_name.": ".$ipdconferences[$i]->create_for_employee_id.")"; ?></td>
                                <td><?php echo $ipdconferences[$i]->patient_name." (".$ipdconferences[$i]->patient_unique_id.")"; ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2v7l-5 9a2 2 0 0 0 1.73 3h12.54A2 2 0 0 0 20 18L15 9V2"/><path d="M8 2h8M7 16h10"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('treatment_history'); ?></div>
                        <?php if(!empty($getipdoverviewtreatment)){ ?><div class="count"><?php echo count($getipdoverviewtreatment); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($getipdoverviewtreatment)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <th><?php echo $this->lang->line('symptoms'); ?></th>
                            <th><?php echo $this->lang->line('consultant'); ?></th>
                            <th><?php echo $this->lang->line('bed'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if(!empty($getipdoverviewtreatment[$i])) { ?>
                            <tr>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$getipdoverviewtreatment[$i]['ipdid']; ?></td>
                                <td><?php echo $getipdoverviewtreatment[$i]['symptoms']; ?></td>
                                <td><?php echo composeStaffNameByString($getipdoverviewtreatment[$i]['name'],$getipdoverviewtreatment[$i]['surname'],$getipdoverviewtreatment[$i]['employee_id']); ?></td>
                                <td><?php echo $getipdoverviewtreatment[$i]['bed_name']."-".$getipdoverviewtreatment[$i]['bedgroup_name']."-".$getipdoverviewtreatment[$i]['floor_name']; ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic teal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><line x1="12" y1="4" x2="12" y2="10"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('bed_history'); ?></div>
                        <?php if(!empty($bed_history)){ ?><div class="count"><?php echo count($bed_history); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body">
                    <?php if (!empty($bed_history)) { ?>
                    <table class="table table-striped table-bordered table-hover mb-0">
                        <thead><tr>
                            <th><?php echo $this->lang->line('bed_group'); ?></th>
                            <th><?php echo $this->lang->line('bed'); ?></th>
                            <th><?php echo $this->lang->line('from_date'); ?></th>
                            <th><?php echo $this->lang->line('to_date'); ?></th>
                            <th><?php echo $this->lang->line('active_bed'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php for ($i=0; $i<$recent_record_count; $i++) {
                            if(!empty($bed_history[$i])) { ?>
                            <tr>
                                <td><?php echo $bed_history[$i]->bed_group; ?></td>
                                <td><?php echo $bed_history[$i]->bed; ?></td>
                                <td><?php if($bed_history[$i]->from_date!=''){ echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($bed_history[$i]->from_date)); } ?></td>
                                <td><?php if($bed_history[$i]->to_date!=''){ echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($bed_history[$i]->to_date)); } ?></td>
                                <td><?php echo $this->lang->line($bed_history[$i]->is_active); ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="p-3 text-muted small"><?php echo $this->lang->line('no_record_found'); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="rellist collapsed">
                <div class="rellist-head">
                    <div class="l">
                        <div class="ic blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                        <div class="title"><?php echo $this->lang->line('timeline'); ?></div>
                        <?php if(!empty($timeline_list)){ ?><div class="count"><?php echo count($timeline_list); ?></div><?php } ?>
                    </div>
                    <div class="r">
                        <button class="expand" onclick="toggleRellist(this)">
                            <span class="expand-open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="18 15 12 9 6 15"/></svg></span>
                            <span class="expand-closed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 12 15 18 9"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="rellist-body pad">
                    <div id="timeline_list">
                    <?php if (!empty($timeline_list)) { ?>
                    <ul class="overview-timeline">
                        <?php for ($i=0; $i<$recent_record_count; $i++) {
                        if (!empty($timeline_list[$i])) { ?>
                        <li>
                            <div class="tl-date"><?php echo date($this->customlib->getHospitalDateFormat(true,true),strtotime($timeline_list[$i]['timeline_date'])); ?></div>
                            <div class="tl-body">
                                <div class="tl-dot"></div>
                                <div>
                                    <div class="tl-title"><?php echo $timeline_list[$i]['title']; ?></div>
                                    <div class="tl-desc"><?php echo $timeline_list[$i]['description']; ?></div>
                                    <?php if(!empty($timeline_list[$i]["document"])){ ?>
                                    <a class="tl-dl" href="<?php echo base_url()."patient/dashboard/download_patient_timeline/".$timeline_list[$i]["id"]; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i> <?php echo $this->lang->line('download'); ?></a>
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
            </div>

            </div><!-- /rellist-wrap -->
            </div><!-- /overview -->
            <!-- end overview --> 						  

                            <!-- Nurse Note -->                       
                            <div class="tab-pane card" id="nurse_note">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('nurse_notes'); ?></h3>
                                </div>
                                <div class="download_label"><?php echo $result['patient_name'] . "  (" . $this->customlib->getPatientSessionPrefixByType('ipd_no').$result['ipdid'] . ") " . $this->lang->line('ipd_details'); ?> </div>                                
                                <?php if (empty($nurse_note)) { ?>
                                    <div class="dataTables_empty">
                                        No data available in table <br><br>
                                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                    </div>
                                <?php } else { ?>
                                    <div class="nn-timeline">
                                        <?php foreach ($nurse_note as $key => $value) {
                                            $id = $value['id'];
                                            $note_date = date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['date']));
                                        ?>
                                        <div class="nn-item">
                                            <div class="nn-card">
                                                <div class="nn-card-header">
                                                    <span class="nn-date-badge">
                                                        <i class="fa fa-calendar"></i> <?php echo $note_date; ?>
                                                    </span>
                                                    <span class="nn-staff">
                                                        <i class="fa fa-user text-muted me-1"></i><?php echo $value['name']; ?>
                                                        <span class="nn-staff-id">(<?php echo $value['employee_id']; ?>)</span>
                                                    </span>
                                                </div>
                                                <div class="nn-card-body">
                                                    <div class="nn-section-label"><?php echo $this->lang->line('note'); ?></div>
                                                    <blockquote class="nn-note"><?php echo nl2br($value['note']); ?></blockquote>
                                                    <?php if (!empty($fields_nurse)) {
                                                        $has_fields = false; $fields_html = '';
                                                        foreach ($fields_nurse as $fields_value) {
                                                            if (!empty($fields_value->name) && !empty($value[$fields_value->name])) {
                                                                $has_fields = true;
                                                                $fields_html .= '<dt class="col-sm-4">' . $fields_value->name . '</dt>';
                                                                $fields_html .= '<dd class="col-sm-8">' . $value[$fields_value->name] . '</dd>';
                                                            }
                                                        }
                                                        if ($has_fields) { ?>
                                                            <dl class="row nn-fields mt-3 mb-0"><?php echo $fields_html; ?></dl>
                                                    <?php } } ?>
                                                    <?php if (!empty(trim(strip_tags($value['comment'])))) { ?>
                                                        <div class="nn-comment">
                                                            <div class="nn-section-label"><i class="fa fa-comment-o"></i> <?php echo $this->lang->line('comment'); ?></div>
                                                            <div><?php echo nl2br($value['comment']); ?></div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php foreach ($nursenote[$id] as $ckey => $cvalue) {
                                                    $c_by   = !empty($cvalue['staffname']) ? '(' . $cvalue['staffname'] . ' ' . $cvalue['staffsurname'] . ': ' . $cvalue['employee_id'] . ')' : '';
                                                    $c_date = !empty($cvalue['created_at']) ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($cvalue['created_at'])) : '';
                                                ?>
                                                <div class="nn-subcomment">
                                                    <div><i class="fa fa-reply text-muted me-1"></i><?php echo nl2br($cvalue['comment_staff']); ?></div>
                                                    <div class="nn-subcomment-meta text-end"><?php echo $c_date . ' ' . $c_by; ?></div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                                <!-- Consultant Register -->
                                <div class="tab-pane card" id="consultant_register">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('consultant_register'); ?></h3>
                                </div>
                                    <div class="download_label"><?php echo $this->lang->line('consultant_register'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example ">
                                            <thead>
                                            <th><?php echo $this->lang->line('applied_date'); ?></th>
                                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                            <th><?php echo $this->lang->line('instruction'); ?></th>
                                            <th><?php echo $this->lang->line('instruction_date'); ?></th>
                                            <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                {
                                                    foreach ($fields_consultant as $fields_key => $fields_value)
                                                    { ?>
                                                    <th><?php echo ucfirst($fields_value->name); ?></th>
                                                    <?php }
                                                }
                                            ?>
                                            </thead>
                                            <tbody>
                                                <?php
if (!empty($consultant_register)) {
        foreach ($consultant_register as $consultant_key => $consultant_value) {
            ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($consultant_value["date"], $this->customlib->getHospitalTimeFormat()); ?></td>
                                                            <td><?php echo composeStaffNameByString($consultant_value['name'], $consultant_value['surname'], $consultant_value['employee_id']); ?></td>
                                                            <td><?php echo nl2br($consultant_value["instruction"]); ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($consultant_value['ins_date']); ?></td>
                                                            <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                            {
                                                                foreach ($fields_consultant as $fields_key => $fields_value) {
                                                                    $display_field = $consultant_value[$fields_value->name];
                                                                  
                                                                        ?>
                                                                    <td>
                                                                        <?php echo $display_field; ?>                                                                                
                                                                    </td>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tr>
                                                        <?php
}
    }
    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Lab Investigation -->
                                <div class="tab-pane card" id="labinvestigation">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                                </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
											<thead>
												<tr>
													<th><?php echo $this->lang->line('test_name'); ?></th>
													<th><?php echo $this->lang->line('lab'); ?></th>
													<th><?php echo $this->lang->line('sample_collected'); ?></th>
													<th><?php echo $this->lang->line('expected_date'); ?></th>
													<th><?php echo $this->lang->line('approved_by'); ?></th>
													<th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
												</tr>
											</thead>
											<tbody id="">
												<?php foreach($investigations as $row ){ ?>
												<tr>
													<td><?php echo $row['test_name']; ?><br/><?php echo "(".$row['short_name'].")"; ?></td>
													<td><?php echo $this->lang->line($row['type']); ?></td>
													<td>
														<?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?><br/>
														<?php  echo $row['test_center'];  ?><br/><?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?>
													</td>                              
													<td>
														<?php if($row['reporting_date']){ echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); } ?>
													</td>
													<td class="text-left">
														<?php echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);?><br/><?php if($row['parameter_update']){  echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']); }?>                                         
													</td>
													<td class="text-end">
														<a href='javascript:void(0)'  data-loading-text='<i class="fa fa-reorder"></i>' data-record-id='<?php echo $row['report_id'];?>' data-type-id='<?php echo $row['type'];?>' data-test-id='<?php echo $row['test_name']. " (".$row['short_name'].")"; ?>'  class='btn btn-sm btn-outline-secondary view_report' data-bs-toggle="tooltip" title='<?php echo $this->lang->line("show"); ?>'><i class='fa fa-reorder'></i></a>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
                                    </div>
                                </div>                                
                                <!-- Timeline -->
                                <div class="tab-pane card" id="timeline">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
                                </div>
                                         
                                    <div class="timeline-header no-border">
                                        <div id="timeline_list">
                                            <?php
                                        if (empty($timeline_list)) { ?>
                                                <div class="dataTables_empty">
                                                    No data available in table <br><br>
                                                    <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                                    <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                                </div>
                                            <?php } else { ?>
                                                <ul class="timeline timeline-inverse">

                                                    <?php
                                                    foreach ($timeline_list as $key => $value) { ?>
                                                        <li class="time-label">
                                                            <span class="badge bg-primary"><?php  echo  date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); ?>
                                                            </span>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-list-alt text-primary"></i>
                                                            <div class="timeline-item">
                                                                <?php if (!empty($value["document"])) {?>
                                                                    <span class="time"><a class="defaults-c text-end" data-bs-toggle="tooltip" href="<?php echo base_url() . "patient/dashboard/download_patient_timeline/" . $value["id"] ?>" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
                                                                <?php }?>
                                                                 <?php if ($value["generated_users_type"] == 'patient') {?>
                                                                <span class="time"><a class="defaults-c text-end" data-bs-toggle="tooltip" title="" onclick="delete_timeline('<?php echo $value['id']; ?>')" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                                </span>
                                                                <span class="time"><a onclick="editTimeline('<?php echo $value['id']; ?>')" class="defaults-c text-end" data-bs-toggle="tooltip" title=""  title="<?php echo $this->lang->line('edit'); ?>">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>
                                                                </span> 
                                                                <?php } ?>
                                                                <h6 class="fw-semibold mb-1 sh-ipd-title-link"> <?php echo $value['title']; ?> </h6>
                                                                <div class="timeline-body">
                                                                    <?php echo $value['description']; ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php }?>
                                                    <li><i class="fa fa-clock-o text-muted"></i></li>
                                                <?php }?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--Prescription -->
                                <div class="tab-pane card" id="prescription">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('prescription'); ?></h3>
                                </div>
                                   <div class="download_label"><?php echo $this->lang->line('prescription'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>                                           
                                            <th><?php echo $this->lang->line('prescription_no'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('finding'); ?></th>
                                                <?php 
                                        if (!empty($fields_prescription)) {
                                            foreach ($fields_prescription as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }  
                                        }
                                    ?> 
                                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                            <tbody>
                                                 <?php
if (!empty($prescription_detail)) {
        foreach ($prescription_detail as $prescription_key => $prescription_value) {
            ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->getPatientSessionPrefixByType("ipd_prescription"). $prescription_value["id"] ?></td>
                                                            <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($prescription_value['date'])) ?></td>
                                                            <td><?php echo $prescription_value['finding_description']; ?></td>
                                                             <?php 
                                                             
                                                            if (!empty($fields_prescription)) {
                                                                $display_field = '';
                                                                foreach ($fields_prescription as $fields_key => $fields_value) {
                                                                    $display_field = $prescription_value[$fields_value->name] ?? '';
                                                                ?>
                                                                    <td><?php echo $display_field; ?></td>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                            <td class="text-end">
                                                                <a href="#prescription" class="btn btn-sm btn-outline-secondary" onclick="view_prescription('<?php echo $prescription_value["id"] ?>', '<?php echo $prescription_value["id"] ?>', '<?php echo "yes" ?>')"   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_prescription'); ?>">
                                                                    <i class="fas fa-file-prescription"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
}
    }
    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
                                
                                <!--Charges-->
                                <div class="tab-pane card" id="charges">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                                </div>
                                    <div class="download_label"><?php echo $this->lang->line('charges'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example ">
                                            <thead>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('charge_name'); ?> <br> <?php echo $this->lang->line('charge_note'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <th><?php echo $this->lang->line('qty'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
                                        <th class="text-end"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')';  ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                                        <th class="noExport text-end"><?php echo $this->lang->line('action'); ?></th>
                                        </thead>
                                            <tbody>
                                                <?php
                                               $total = 0;
                                            if (!empty($charges)) {

                                                foreach ($charges as $charge) {

                                                    $discount_amount = amountFormat(($charge['apply_charge']*$charge['discount_percentage']/100)) ;
                                                    $tax_amount = (($charge['apply_charge']-$discount_amount) *$charge['tax']/100);
                                                    $taxamount = amountFormat($tax_amount);
                                                    $total += $charge["amount"];
                                                    ?>
                                                        <tr>
                                                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($charge['date'])); ?></td>
                                                        <td class="text-capitalize"><?php echo $charge["name"] ?>
                                                                 <div class="bill_item_footer text-muted"> <?php echo $charge["note"] ?></div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charge["charge_type"] ?></td>
                                                        <td class="text-capitalize">
                                                            <?php echo $charge["charge_category_name"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charge['qty']//." ".$charge["unit"]; ?></td>
                                                        <td class="text-end"><?php echo $charge["standard_charge"] ?></td>
                                                        <td class="text-end"><?php echo number_format($charge["tpa_charge"], 2) ?></td>
                                                        <td class="text-end"><?php echo $discount_amount." (".$charge["discount_percentage"]."%) " ;?></td>             
                                                        <td class="text-end"><?php echo $taxamount."(".$charge["tax"]."%)"; ?></td>
                                                        <td class="text-end"><?php echo number_format($charge["apply_charge"], 2) ?></td>
                                                        <td class="text-end"><?php echo number_format($charge["amount"], 2) ?></td>
                                                        <td class="text-end"><a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary print_charge" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" data-record-id="<?php echo $charge['id']; ?>">
                                                            <i class="fa fa-print"></i></a>
                                                        </td>
                                                        </tr>
                                                    <?php }?>
                                               
                                            </tbody>
                                            <tr class="table-active fw-semibold">
                                                <td colspan='11' class="text-end"><?php echo $this->lang->line('total') . ": " .$currency_symbol.''.number_format($total,2); ?><input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                                </td>
                                                <td></td>
                                            </tr>
                                             <?php }?>
                                        </table>
                                    </div>
                                </div>
                        
                        <!--Live Consult-->
                        <div class="tab-pane card" id="live_consult">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
                            </div>
                            <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                    <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
if (empty($ipdconferences)) {
        ?>

                                        <?php
} else {
        foreach ($ipdconferences as $conference_key => $conference_value) {

            $return_response = json_decode($conference_value->return_response);
            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $conference_value->title; ?></a>
                                                    <div class="fee_detail_popover d-none" >
                                                        <?php
if ($conference_value->description == "") {
                ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
                ?>
                                                            <p class="text text-info"><?php echo $conference_value->description; ?></p>
                                                            <?php
}
            ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date)) ?>
                                                    </td>
                                                 <td class="mailbox-name">
                                                    <?php

            $name = ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname;

            if ($name == 'Super Admin') {
                echo $name. " (" . $conference_value->create_by_employee_id . ")";
                # code...
            } else {
                echo $name . " (" . $conference_value->create_by_employee_id . ")";
            }

            ?></td>

                                                <td class="mailbox-name">
                                                    <?php

            $name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
            
            echo $name . " (" . $conference_value->create_for_role_name . ": " . $conference_value->create_for_employee_id . ")";
            ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php

            $name = ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name;
            echo $name . " (" . $conference_value->patient_unique_id . ")";
            ?>

                                                </td>
                                              <td class="mailbox-name">
                                                <form class="chgstatus_form"  method="POST" action="<?php echo site_url('admin/conference/chgstatus') ?>">
                                                    <input type="hidden" name="conference_id"  value="<?php echo $conference_value->id; ?>">
                                                 <select class="form-select form-select-sm chgstatus_dropdown" disabled name="chg_status">
                                                     <option value="0" <?php if ($conference_value->status == 0) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('awaited'); ?></option>
                                                     <option value="1" <?php if ($conference_value->status == 1) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('cancelled'); ?> </option>
                                                     <option value="2" <?php if ($conference_value->status == 2) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('finished'); ?> </option>
                                                 </select>
                                                </form>
                                                </td>
                                                <td class="text-end">
                                                    <?php
if ($conference_value->status == 0) {
                ?>
                                        <a href="<?php echo $return_response->start_url; ?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $this->lang->line('join'); ?></a>
                                            <?php
}
            ?>
                                                </td>
                                            </tr>
                                            <?php
}
    }
    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>                        
                                <!--Bed History-->
                                <div class="tab-pane card" id="bed_history">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('bed_history'); ?></h3>
                                </div>
                                    <div class="download_label"><?php echo $this->lang->line("bed_history"); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <th><?php echo $this->lang->line('bed_group'); ?></th>
                                                <th><?php echo $this->lang->line('bed'); ?> </th>
                                                <th><?php echo $this->lang->line('from_date'); ?></th>
                                                <th><?php echo $this->lang->line('to_date'); ?></th>
                                                <th><?php echo $this->lang->line("active_bed"); ?></th>
                                            </thead>
                                            <tbody>
                                                <?php foreach($bed_history as $history){ ?>
                                                    <tr>
                                                        <td class="mailbox-name"><?php echo $history->bed_group; ?></td>
                                                        <td class="mailbox-name"><?php echo $history->bed; ?></td>
                                                        <td class="mailbox-name"><?php if($history->from_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->from_date)); } ?></td>
                                                        <td class="mailbox-name"><?php if($history->to_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->to_date)); } ?></td>
                                                        <td class="mailbox-name"><?php echo $this->lang->line($history->is_active); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                                <!--Payment-->
                                <div class="tab-pane card" id="payment">                                   
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>
                                    <div class="box-tab-tools">
                                        <?php if ($result["is_active"] == 'yes') {?>
                                        <button type="button" class="btn btn-info btn-sm" data-result_id="<?php echo $result['ipdid'] ?>" data-bs-toggle="modal" data-bs-target="#payMoney"><i class="fa fa-plus"></i> <?php echo $this->lang->line('make_payment'); ?></button>
                                        <?php }?>
                                    </div>
                                </div>
                                    <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th><?php echo $this->lang->line('note'); ?></th>
                                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                                <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                        <tbody>
                                    <?php
                                        $total_payment = 0;
                                        if (!empty($payment_details)) {
                                                $total_payment = 0;
                                                foreach ($payment_details as $payment) {
                                                    if (!empty($payment['amount'])) {
                                                        $total_payment += $payment['amount'];
                                                    }
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment["id"] ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                                            <td><?php echo $payment["note"] ?></td>
                                                            <td class="text-capitalize"><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";
                                                                             if($payment['payment_mode'] == "Cheque"){
                                                                                  if($payment['cheque_no']!=''){
                                                            echo $this->lang->line("cheque_no"). ": ".$payment['cheque_no'];                                                           
                                                         echo "<br>";
                                                     }
                                                         if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                                            echo $this->lang->line("cheque_date") .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                                        }                                                          

                                                          }
                                                         ?>
                                                        </td>
                                                           <td class="text-end"><?php echo $payment["amount"] ?></td> 
                                                           <td class="text-end"><?php if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {                                                       
                                                             ?>
                                                             <a href='<?php echo site_url('patient/dashboard/downloadreceipt/'.$payment['id']);?>' class='btn btn-sm btn-outline-secondary'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                                                             <?php
                                                         }
                                                                  ?>
         <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary print_trans" data-record-id="<?php echo $payment['id'] ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a></td>
                                                        </tr>
                                                <?php }?>
                                                    
                                                </tbody>
                                                    <tr class="table-active fw-semibold">
                                                         <td  class="text-end" colspan="4"><?php echo $this->lang->line('total'); ?> : </td>                                                      
                                                        <td  class="text-end"><?php echo $currency_symbol . number_format($total_payment,2); ?>
                                                        </td>
                                                        <td></td>
                                                    </tr>
    <?php } ?>
                                        </table>
                                    </div>
                                </div>

                                <!--- Treatment history tab---->
                            <div class="tab-pane card" id="treatment_history">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                                </div>
                                <div class="download_label"><?php echo $this->lang->line('treatment_history'); ?></div>
                                <div class="table-responsive">
                                 <table class="table table-striped table-bordered table-hover treatmentlist"  data-export-title="<?php echo $this->lang->line('treatment_history'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                         <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                        <th class="text-end" ><?php echo $this->lang->line('bed'); ?></th>                                        
                                    </thead>
                                    <tbody> 
                                    </tbody>
                                 </table>
                                </div><!--./table-responsive--> 
                            </div>
                            
                            <!--- Medication--> 
                            <div class="tab-pane card" id="medication">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('medication'); ?></h3>
                                </div>
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
                        
                        <!--- Operation theatre--> 
                        <div class="tab-pane card" id="operationtheatre">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('operation'); ?></h3>
                            </div>
                            <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                         <?php if (is_array($fields_ot) || is_object($fields))
                                            {
                                                foreach ($fields_ot as $fields_key => $fields_value)
                                                { ?>
                                                   <th><?php echo ucfirst($fields_value->name); ?></th>
                                                <?php }
                                            }
                                        ?>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($operation_theatre)) {
                                            foreach ($operation_theatre as $ot_key => $ot_value) {
                                                ?>  
                                                <tr>    
                                                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no').$ot_value["id"] ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHISTodateformat($ot_value["date"])?></td>
                                                    <td><?php echo $ot_value["operation"] ?></td>
                                                    <td><?php echo $ot_value["category"] ?></td>
                                                    <td><?php echo $ot_value['ot_technician'] ?></td>
                                                    <?php
                                                    if (!empty($fields_ot)) {
                                                        foreach ($fields_ot as $fields_key => $fields_value) {
                                                            $display_field = $ot_value[$fields_value->name];
                                                            if ($fields_value->type == "link") {
                                                                $display_field = "<a href=" . $ot_value[$fields_value->name] . " target='_blank'>" . $ot_value[$fields_value->name] . "</a>";
                                                            }
                                                            ?>
                                                            <td>
                                                                <?php echo $display_field; ?>
                                                            </td>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <td class="text-end">
                                                        <a href='#' data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" class='btn btn-sm btn-outline-secondary' onclick='viewdetail("<?php echo $ot_value['id']; ?>")'>  <i class='fa fa-reorder'></i> </a>  
                                                    </td>
                                                </tr>                                            
                                            <?php } } ?>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 

                            <!-- opstrical history  -->
                        <?php if($result['is_antenatal']==1){ ?>
                        <div class="tab-pane card" id="obstetric_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('previous_obstetric_history'); ?></h3>
                            </div>                            
                            <div class="impbtnview-t9">                                 
                            </div>                            
                            <div class="download_label"><?php echo $this->lang->line('previous_obstetric_history'); ?></div>
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('place_of_delivery'); ?></th>
                                            <th><?php echo $this->lang->line('duration_of_pregnancy'); ?></th>
                                            <th><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                                            <th><?php echo $this->lang->line('birth_weight'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('infant_feeding') ?></th>
                                            <th><?php echo $this->lang->line('birth_status') ?></th>
                                            <th><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('death_cause') ?></th>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                             foreach($obstetric_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $row['place_of_delivery']; ?></td>
                                                    <td><?php echo $row['pregnancy_duration']; ?></td>
                                                    <td><?php echo $row['pregnancy_complications']; ?></td>
                                                    <td><?php echo $row['birth_weight']; ?></td>
                                                    <td><?php echo $this->lang->line(strtolower($row['gender'])); ?></td>
                                                    <td><?php echo $row['infant_feeding']; ?></td>
                                                    <td><?php echo $this->lang->line($row['alive_dead']); ?></td>
                                                    <td><?php if($row['date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['date']); } ?></td>
                                                    <td><?php echo $row['death_cause']; ?></td>
                                                    <td class="text-end noExport">                                                    
                                                    <a href='javascript:void(0)' onclick="viewobstetric('<?php echo $row['id']; ?>')" data-record-id = "<?php echo $row['id']; ?>" class='btn btn-sm btn-outline-secondary edit_obstetric' data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_history'); ?>"><i class='fas fa fa-reorder'></i></a>                                                    
                                                    </td>
                                                </tr>
                                           <?php }  ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
                        <?php  }  ?>
                     <!-- opstrical history  -->

                     <!-- postnatal history -->
                      <div class="tab-pane card" id="post_antenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('postnatal_history'); ?></h3>
                            </div>
                            <div class="impbtnview-t9">                                 
                            </div>                            
                               <div class="download_label"><?php echo $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive overflow-visible">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('labor_time'); ?></th>
                                            <th><?php echo $this->lang->line('delivery_time'); ?></th>
                                            <th><?php echo $this->lang->line('routine_question'); ?></th>
                                            <th><?php echo $this->lang->line('general_remark'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($postnatal_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['labor_time']); ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['delivery_time']); ?></td>
                                                    <td><?php echo $row['routine_question']; ?></td>
                                                    <td><?php echo $row['general_remark']; ?></td>                                               
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                </table>
                            </div> 
                        </div>   
                        <!-- postnatal history -->

                        <!-- antinatal history -->
                          <div class="tab-pane card" id="addantenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('antenatal'); ?></h3>
                            </div>                            
                            <div class="download_label"><?php echo $this->lang->line('opd_details'); ?></div>                            
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('opd/ipd_no'); ?></th>
                                            <th><?php echo $this->lang->line('checkup_id'); ?></th>                                         
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($antenatallist as $row){ ?>
                                                <tr>
                                                    <?php if($row['status']=='ipd'){?>
                                                        <td><?php 
                                                        if($row['ipdid']!="" && $row['ipdid']!= null){ 
															echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$row['ipdid'];
                                                        }  ?></td>
                                                     <?php }else{ ?>
                                                        <td><?php
                                                         if($row['opd_detail_id']!="" && $row['opd_detail_id']!= null){
                                                    echo $opd_id = $this->customlib->getPatientSessionPrefixByType('opd_no').$row['opd_detail_id'];
                                                        } ?></td>
                                                     <?php } ?>
                                                    <td><?php 
                                                    if($row['visit_details_id']!="" && $row['visit_details_id']!=null){
														echo $this->customlib->getPatientSessionPrefixByType('checkup_id').$row['visit_details_id'];
                                                    } ?></td>                                                    
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['date']); ?></td>
                                                    <td class="text-end noExport">
                                                    <?php if($row['status']=='ipd'){ ?>                                                    
                                                    <a href='javascript:void(0)' onclick="viewipdantenatal('<?php echo $row['primary_id']; ?>')" data-record-id = "<?php echo $row['primary_id']; ?>" class='btn btn-sm btn-outline-secondary edit_obstetric' data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_history'); ?>"><i class='fas fa fa-reorder'></i></a>
                                                    <?php }else{ ?>
                                                    <a href='javascript:void(0)' onclick="viewantenatal('<?php echo $row['visit_details_id']; ?>')" data-record-id = "<?php echo $row['visit_details_id']; ?>" class='btn btn-sm btn-outline-secondary edit_obstetric' data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view_history'); ?>"><i class='fas fa fa-reorder'></i></a>
                                                    <?php } ?>
                                                </td>
                                                </tr>
                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div>
                        <!-- antinatal history -->
						
						<!-- vitals -->
						<div class="tab-pane card" id="vitals">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
                            </div>
                            <div class="timeline-header no-border">
                                <div id="vital_list">
                                    <?php
                                    if (empty($patient_vital_date)) {
                                        ?>
                                        <div class="table_inner">
                                            <table class="table table-striped table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line("date"); ?> </th>
                                                        <?php foreach($vital_list as $vl){ ?>
                                                        <th><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="<?php echo (is_array($vital_list) ? count($vital_list) : 0) + 1; ?>" class="dataTables_empty">
                                                        No data available in table <br><br>
                                                        <img src="<?php echo base_url('backend/images/addnewitem.svg'); ?>" width="150" alt=""><br><br>
                                                        <span class="text-success bolds"><i class="fa fa-arrow-left"></i> Search with different criteria.</span>
                                                    </td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } else {   ?>
										<div class="table_inner"> 
											<table class="table table-striped table-bordered ">												 
												<thead>
													<tr>
														<th><?php echo $this->lang->line("date"); ?> </th>													
														<?php foreach($vital_list as $vl){ ?>
														<th><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
														<?php } ?>
													</tr>
												</thead>
												<tbody> 													 
													<?php foreach($patient_vital_date as $pvd){ //$messure_date = $pvd['messure_date']?>
													<tr>
														<th><?php echo $date = $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']);
															$messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
														?></th>														
														<?php foreach($vital_list as $vl){ 														
															$id = $vl["id"]; 
															  ?>														
																<td class="tablehovericon">
                                                                    <div class="relative">
																	<?php 
																	foreach($patientvital[$messure_date][$id] as $pmi){				
																		echo $pmi['patient_range'];	
																		$datetime = $this->customlib->YYYYMMDDHisTodateFormat($pmi['messure_date'],$this->customlib->getHospitalTimeFormat()); 
																		$str2 = substr($datetime, 10);
																		echo  " (".$str2 .")";																					
																		echo "<br>";																		 
																	} 
																	?>	
																	</div>																
																</td>
															 
														<?php }  ?>															
													</tr>
													<?php } ?>												
													 
												</tbody>								   
											</table>
										</div>
									<?php } ?> 
                                </div>
                            </div>
                        </div> 
						<!-- vitals -->
            </div><!-- tab-content -->
    </div><!-- opd-profile-wrap -->
    <?php } ?>
</div><!-- end container-fluid -->

<div class="modal fade sh-modal sh-modal-branded" id="patient_discharge" tabindex="-1" aria-labelledby="patient_dischargeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patient_dischargeLabel"><?php echo $this->lang->line('patient_discharge'); ?></h5>
                <div id="allpayments_print" class="ms-auto me-2"></div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="patient_discharge_result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered sh-modal-autoheight">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="ipd_patient_detail"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="modal fade sh-modal sh-modal-branded" id="myTimelineModal" tabindex="-1" aria-labelledby="myTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineModalLabel"><?php echo $this->lang->line('add_timeline'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="add_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('title'); ?><small class="req"> *</small></label>
                            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id ;?>">
                            <input id="timeline_title" name="timeline_title" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat())); ?>" type="text" class="form-control date" />
                            <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                            <textarea id="timeline_desc" name="timeline_desc" class="form-control"></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input id="timeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                            <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="add_timelinebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Timeline -->
<div class="modal fade sh-modal sh-modal-branded" id="myTimelineEditModal" tabindex="-1" aria-labelledby="myTimelineEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myTimelineEditModalLabel"><?php echo $this->lang->line('edit_timeline'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <input type="hidden" name="patient_id" id="epatientid" value="">
                        <input type="hidden" name="timeline_id" id="etimelineid" value="">
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('title'); ?><small class="req"> *</small></label>
                            <input id="etimelinetitle" name="timeline_title" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input type="text" name="timeline_date" class="form-control date" id="etimelinedate" />
                            <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                            <textarea id="timelineedesc" name="timeline_desc" class="form-control"></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                            <input id="etimeline_doc_id" name="timeline_doc" type="file" class="filestyle form-control" data-height="40" value="<?php echo set_value('timeline_doc'); ?>" />
                            <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_timelinebtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="viewModalsummary" tabindex="-1" aria-labelledby="viewModalsummaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalsummaryLabel"><?php echo $this->lang->line('discharged') . " " . $this->lang->line('summary'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id='edit_deletebill'></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="reportdata"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered sh-modal-autoheight modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line('prescription'); ?></h5>
                <div id='edit_deleteprescription' class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescription"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="payMoney" class="modal fade sh-modal sh-modal-branded" tabindex="-1" aria-labelledby="payMoneyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payMoneyLabel"><?php echo $this->lang->line('make_payment') ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="payment_form" method="POST">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-0">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('make_payment') ?></span>
                            </div>
                            <div class="p-3">
                                <div class="mb-2">
                                    <label for="amount_total_paid" class="form-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                                    <input type="text" class="form-control" value="<?= $total-$total_payment; ?>" name="deposit_amount" id="amount_total_paid">
                                    <input type="hidden" value="<?= $total-$total_payment; ?>" name="net_amount" id="net_amount">
                                    <span id="deposit_amount_error" class="text-danger small"></span>
                                    <input type="hidden" name="payment_for" value="ipd">
                                    <input type="hidden" name="id" value="<?php echo $ipdid;?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="pay_button" class="btn btn-info make_payment"><?php echo $this->lang->line('add') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-branded" id="view_ot_modal" tabindex="-1" aria-labelledby="view_ot_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_ot_modalLabel"><?php echo $this->lang->line('operation_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id='action_detail_modal'></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div id="show_ot_data"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!--lab investigation modal-->
<div class="modal fade sh-modal sh-modal-branded" id="viewDetailReportModal" tabindex="-1" aria-labelledby="modal_head" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered sh-modal-autoheight">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id='action_detail_report_modal'></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end lab investigation modal-->

<!-- Obstetric prescription -->
<div class="modal fade sh-modal sh-modal-branded" id="viewobstetrichistory" tabindex="-1" aria-labelledby="viewobstetrichistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewobstetrichistoryLabel"><?php echo $this->lang->line('previous_obstetric_history'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span id='edit_printhistory'></span>
                    <span id='edit_edithistory'></span>
                    <span id='edit_deletehistory'></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Obstetric prescription -->

<!-- antenatal history modal -->
<div class="modal fade sh-modal sh-modal-branded" id="findingview" tabindex="-1" aria-labelledby="findingviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="findingviewLabel"><?php echo $this->lang->line('antenatal_finding'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span id='edit_printfinding'></span>
                    <span id='edit_editfinding'></span>
                    <span id='edit_deletefinding'></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_finding"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- antenatal history modal -->

<script type="text/javascript">
        $(document).on('click','.patient_discharge',function(){             
            var case_reference_id="<?php echo $case_reference_id;?>";
            var payment_modal=$('#patient_discharge');
            payment_modal.addClass('modal_loading');
            bootstrap.Modal.getOrCreateInstance(payment_modal[0]).show();
            $.ajax({
            url: base_url+'patient/dashboard/patient_discharge',
            type: "POST",
            data:{'module_type':'ipd','case_reference_id':case_reference_id},
            dataType: 'json',
        beforeSend: function() {
               }, 
        success: function (data) {
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#patient_discharge').dropify();
           $('.date','#patient_discharge').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

        error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");           
               
          },
        complete: function() {
                payment_modal.removeClass('modal_loading');          
          }
        });       
    });

     $(document).on('click','.print_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id');   
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'ipd'},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    function getRecord(ipdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getIpdDetails',
            type: "POST",
            data: {ipdid: ipdid},
            dataType: 'json',
            success: function (data) {
                $('#ipd_patient_detail').html(data.page);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static', keyboard: false}).show();
            },
        });
    }

   function getRecordsummary(id,ipdid) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getsummaryDetails',
            type: "POST",
            data: {id: id,ipdid:ipdid},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' data-bs-toggle='tooltip' onclick='printData(" + id + ","+ipdid+")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> ");
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModalsummary'), {backdrop: 'static', keyboard: false}).show();
            },
        });
    }

    $(document).ready(function (e) {
        $("#add_timeline").on('submit', (function (e) {
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $("#add_timelinebtn").button('loading');
            $.ajax({
                url: "<?php echo site_url("patient/dashboard/add_patient_timeline") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);                       
                        window.location.reload(true);
                    }
                    $("#add_timelinebtn").button('reset');
                },
                error: function (e) {
                    alert("Fail");
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#edit_timeline").on('submit', (function (e) {
            $("#edit_timelinebtn").button('loading');
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("patient/dashboard/edit_patient_timeline") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#edit_timelinebtn").button('reset');
                },
                error: function (e) {
                    alert("Fail");
                    console.log(e);
                }
            });
        }));
    });
    
    function editTimeline(id) {      
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/editTimeline',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                var dt = new Date(data.timeline_date).toString(date_format);
                $("#etimelineid").val(data.id);
                $("#epatientid").val(data.patient_id);
                $("#etimelinetitle").val(data.title);
                $("#etimelinedate").val(dt);
                $("#timelineedesc").val(data.description);
                if (data.status == '') {

                } else
                {
                    $("#evisible_check").attr('checked', true);
                }
             
                bootstrap.Modal.getOrCreateInstance(document.getElementById('myTimelineEditModal'), {backdrop: 'static', keyboard: false}).show();
                $('.filestyle').dropify();
            },
        });
    }

     function delete_timeline(id) {
       
        if (confirm('<?php echo $this->lang->line("delete_confirm") ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>patient/dashboard/delete_patient_timeline/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                }, error: function () {
                    alert("Fail")
                }
            });
        }
    }
	
     $(document).on('click','.print_charge',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printCharge',
          type: "POST",
          data:{'id':record_id,'type':'ipd'},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');
      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');
              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
    });

    $(function () {
        var hash = window.location.hash;
        if (hash) {
            var tabEl = document.querySelector('ul.nav-tabs a[href="' + hash + '"]');
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }
        $('.nav-tabs a').on('click', function () {
            var scrollmem = $('body').scrollTop();
            window.location.hash = this.hash;
            $('html,body').scrollTop(scrollmem);
        });
    });

    function view_prescription(id, ipdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getIPDPrescription/' + id + '/' + ipdid,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("Fail")
            }
        });

         $('#edit_deleteprescription').html("<a href='javascript:void(0)' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>' onclick='printprescription(" + id + "," + ipdid + ")'><i class='fa fa-print'></i></a>");
        bootstrap.Modal.getOrCreateInstance(document.getElementById('prescriptionview'), {backdrop: 'static', keyboard: false}).show();
    }

  function printprescription(id, opdid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/prescription/getIPDPrescription/' + id + '/' + opdid,
            type: 'POST',
            data: {payslipid: id, print: 'yes'},           
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

    function getcharge_category(id) {
        var div_data = "";
        $("#charge_category").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value=" + obj.name + ">" + obj.name + "</option>";
                });
                $('#charge_category').append(div_data);
            }
        });
    }

    $(document).on('click','.print_trans',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
       $this.button('loading');
        $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');
                 
             }
        });
    });

    function get_Charges(charge_category, orgid) {
        $("#standard_charge").html("standard_charge");
        $("#schedule_charge").html("schedule_charge");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/ipdCharge',
            type: "POST",
            data: {charge_category: charge_category, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#standard_charge').val(res.standard_charge);
                    $('#schedule_charge').val(res.org_charge);
                    $('#charge_id').val(res.id);
                    $('#org_id').val(res.org_charge_id);
                    if (res.org_charge == null) {
                        $('#apply_charge').val(res.standard_charge);
                    } else {
                        $('#apply_charge').val(res.org_charge);
                    }
                } else {
                    $('#standard_charge').val('0');
                    $('#schedule_charge').val('0');
                    $('#charge_id').val('0');
                    $('#org_id').val('0');
                }
            }
        });
    }

    function toggleRellist(btn) {
        btn.closest('.rellist').classList.toggle('collapsed');
    }

    function calculate() {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var tax = $("#tax").val();
        var gross_total = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax);
        var net_amount = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax) - parseInt(discount);
        $("#gross_total").val(gross_total);
        $("#net_amount").val(net_amount);
        $("#save_button").removeClass('d-none');
    }
</script>
<script type="text/javascript">
    function print(patientid, ipdid) {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var gross_total = $("#gross_total").val();
        var tax = $("#tax").val();
        var net_amount = $("#net_amount").val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/ipdBill/',
            type: 'POST',
            data: {patient_id: patientid, ipdid: ipdid, total_amount: total_amount, discount: discount, other_charge: other_charge, gross_total: gross_total, tax: tax, net_amount: net_amount},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }
	

    $(document).ready(function (e) {
        modal_click_disabled('viewModal', 'viewModalsummary', 'myTimelineEditModal', 'prescriptionview', 'findingview');
            $("#add_payment").on('submit', (function (e) {
                e.preventDefault();
            
            $.ajax({
                url: '<?php echo base_url(); ?>patient/pay/ipdpay',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
           
                 beforeSend: function(){
                 $("#add_paymentbtn").button("loading");
                 },
                   success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#add_paymentbtn").button("reset");
                },
                error: function () {
                 $("#add_paymentbtn").button('reset');
                },
  
                complete: function(){
                 $("#add_paymentbtn").button('reset');
                }
            });
        }));
    });

    $('.addtimeline').click(function(){
      $('.filestyle').dropify();
    })

     $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });
    

    $(document).on('click','.print_ot_bill',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
           
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/print_otdetails',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');
              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    function viewdetail(ot_id){
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/otdetails',
            type: "POST",
            data: {ot_id: ot_id},
            dataType: 'json',
            success: function (data) {
               $('#show_ot_data').html(data.page);
               $('#action_detail_modal').html(data.actions);
               bootstrap.Modal.getOrCreateInstance(document.getElementById('view_ot_modal'), {backdrop:'static'}).show();
            },
        });
     }

    $(document).on('click','.make_payment',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this).closest("form");
    var url = form.attr('action');    
  var formdata = new FormData($('#payment_form')[0]);
        $.ajax({
            url: base_url+'patient/pay/checkvalidate',
            type: "POST",
            data: formdata, 
            dataType: 'json',
            cache : false,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                  
                    window.location.replace(base_url+'patient/pay');
                }
            }
        })    
});
   
    $('#payMoney').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })   
</script>
<script>
     $(document).on('click','.view_report',function(){
         var id=$(this).data('recordId');
         var lab=$(this).data('typeId');
         var test = $(this).data('testId');
         getinvestigationparameter(id,$(this),lab,test);
       });

        function getinvestigationparameter(id,btn_obj,lab,test){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'patient/dashboard/getinvestigationparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
                modal_view.addClass('modal_loading');                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);              
             $('#viewDetailReportModal #modal_head').html(test);  
             bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDetailReportModal'), {backdrop:'static'}).show();
              modal_view.removeClass('modal_loading');
            },

             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.button('reset');
                modal_view.removeClass('modal_loading');          
           }
        });  
        }
</script>
<script type="text/javascript">
    $(document).on('click','.print_bill',function(){
    var id=$(this).data('recordId');
      
        var $this = $(this);
        var lab   = $(this).data('typeId');
        $.ajax({
            url: base_url+'patient/dashboard/printpathoparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               
               },
            success: function (data) {               
           
           popup(data.page);

            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });

    });
</script>
<script type="text/javascript">
( function ( $ ) {
     var id = "<?php echo $patient_id; ?>"; 
    
    'use strict';
    $(document).ready(function () {
       initDatatable('treatmentlist','patient/dashboard/getipdtreatmenthistory/'+id);
    });
} ( jQuery ) )
</script>
<script type="text/javascript">
     $(document).ready(function () {
       
           $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
               .columns.adjust()
               .responsive.recalc();
            });  

      }); 
</script>
<script src="<?php echo base_url()?>backend/js/Chart.min.js"></script>
<script type="text/javascript">
         Chart.types.Doughnut.extend({
            name: "DoughnutTextInside",
            showTooltip: function () {
                this.chart.ctx.save();
                Chart.types.Doughnut.prototype.showTooltip.apply(this, arguments);
                this.chart.ctx.restore();
            },
            draw: function () {
                Chart.types.Doughnut.prototype.draw.apply(this, arguments);

                var width = this.chart.width,
                        height = this.chart.height;
                var fontSize = (height / 190).toFixed(2);
                this.chart.ctx.font = fontSize + "em Verdana";
                this.chart.ctx.textBaseline = "middle";
                var text = "<?php echo $donut_graph_percentage; ?>%",
                        textX = Math.round((width - this.chart.ctx.measureText(text).width) / 2),
                        textY = height / 2;
                
                // Dark mode support: fetch --ink color for center text
                var inkColor = getComputedStyle(document.body).getPropertyValue('--ink').trim() || '#1A2733';
                this.chart.ctx.fillStyle = inkColor;
                this.chart.ctx.fillText(text, textX, textY);
            }
        });

         
        var used_credit_limit="";
        var balance_credit_limit="";

        used_credit_limit=<?php echo number_format($used_credit_limit, 2, '.', ''); ?>;
        balance_credit_limit=<?php echo number_format($balance_credit_limit, 2, '.', ''); ?>;
    
        // Theme-aware colors for chart slices
        var redColor = getComputedStyle(document.body).getPropertyValue('--red').trim() || '#E42527';
        var greenColor = getComputedStyle(document.body).getPropertyValue('--green').trim() || '#22A06B';

       var u_val = parseFloat(used_credit_limit) || 0;
        var b_val = parseFloat(balance_credit_limit) || 0;

        if ((u_val + b_val) > 0) {
            var data = [{
                    lebel: 'complete',
                    value: u_val.toFixed(2),
                    color: redColor
                }, {
                    value: b_val.toFixed(2),
                    color: greenColor
                }
            ];

            var DoughnutTextInsideChart = new Chart($('#pieChart')[0].getContext('2d')).DoughnutTextInside(data, {
                responsive: false,
                maintainAspectRatio: true
            });
        } else {
            $('#pieChart').replaceWith('<div class="text-muted small py-4 text-center"><?php echo $this->lang->line('no_record_found'); ?></div>');
        }
</script>

<script>
 function viewobstetric(id){
        $.ajax({
            url: base_url+'patient/antenatal/getobstetrichistory',
            dataType:'JSON',
            data:{'id':id} ,
            type:"POST",
            beforeSend: function() {
                  
            },
            success: function (res){               
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('viewobstetrichistory')).show();
                    $('.modal-body',"#viewobstetrichistory").html(res.page);         
                    $('#edit_printhistory').html( "<a href='#'' data-bs-toggle='tooltip' onclick='printobstetrichistory(" + id + ")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>" );                    
               }
           });
    }


     function printobstetrichistory(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/antenatal/printobstetrichistory' ,
            type: 'post',
            data: { id: id },
            dataType:"JSON",
            success: function (result) {
                popup(result.page);
            }
        });
    }

    function viewipdantenatal(antenatal_id) { 
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getipdantenatalfindings/' + antenatal_id ,
            success: function (res) {
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        bootstrap.Modal.getOrCreateInstance(document.getElementById('findingview'), {backdrop: 'static', keyboard: false}).show();
    }

     function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getantenatalprescription/' + visitid ,
            success: function (res) {
                console.log(res);
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        bootstrap.Modal.getOrCreateInstance(document.getElementById('findingview'), {backdrop: 'static', keyboard: false}).show();
    }


       function printipdantenatalprescription(ipdid) {      
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/antenatal/printipdantenatalprescription' ,
            type: 'GET',
            data: { ipdid: ipdid },
            dataType:"JSON",
            success: function (result) {
                popup(result.page);
            }
        });
    }

</script>

<script>
(function () {
    var nav  = document.getElementById('ph_tabs_nav');
    var prev = document.getElementById('ph_tabs_prev');
    var next = document.getElementById('ph_tabs_next');
    if (!nav || !prev || !next) return;

    function updateArrows() {
        prev.classList.toggle('d-none', nav.scrollLeft <= 2);
        next.classList.toggle('d-none', nav.scrollLeft + nav.clientWidth >= nav.scrollWidth - 2);
    }

    prev.addEventListener('click', function () { nav.scrollBy({ left: -200, behavior: 'smooth' }); });
    next.addEventListener('click', function () { nav.scrollBy({ left:  200, behavior: 'smooth' }); });
    nav.addEventListener('scroll', updateArrows);
    window.addEventListener('resize', updateArrows);
    updateArrows();
})();
</script>
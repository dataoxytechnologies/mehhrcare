<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<div class="container-fluid px-1 py-1">
<div class="opd-profile-wrap">

    <!-- ══ PAGE HEADER: patient banner + tab nav ══ -->
    <header class="page-head">
        <div class="ph-title-row">
            <div class="ph-title">
                <?php
                $image    = $result["image"];
                $file     = !empty($image) && strpos($image, 'no_image') === false ? $image : "";
                $_p_parts = preg_split('/\s+/', trim($result["patient_name"] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                $initials = count($_p_parts) === 0 ? '?' : (count($_p_parts) === 1
                    ? mb_strtoupper(mb_substr($_p_parts[0], 0, 1))
                    : mb_strtoupper(mb_substr($_p_parts[0], 0, 1) . mb_substr($_p_parts[count($_p_parts) - 1], 0, 1)));
                ?>
                <?php if ($file): ?>
                    <img src="<?php echo $this->media_storage->getImageURL(
                        $file,
                    ); ?>"
                         class="ph-av sh-profile-patient-avatar" alt="">
                <?php else: ?>
                    <div class="ph-av"><?php echo $initials; ?></div>
                <?php endif; ?>
                <div>
                    <h1>
                        <?php echo composePatientName(
                            $result["patient_name"],
                            $result["id"],
                        ); ?>
                    </h1>
                    <div class="sub">
                        <?php echo $this->lang->line(
                            strtolower($result["gender"]),
                        ); ?>
                        <span class="dot-sep"><?php echo $this->customlib->get_patient_current_age(
                            $result["id"],
                        ); ?></span>
                        <?php if (!empty($result["mobileno"])): ?>
                        <span class="dot-sep"><?php echo html_escape(
                            $result["mobileno"],
                        ); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($result["guardian_name"])): ?>
                        <span class="dot-sep"><?php echo $this->lang->line(
                            "guardian",
                        ); ?>: <?php echo html_escape(
    $result["guardian_name"],
); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <nav class="ph-tabs" role="tablist" id="profileTabsNav">
            <a class="active" data-bs-toggle="tab" data-bs-target="#overview" href="#overview" role="tab">
                <i class="fa fa-th me-1"></i><?php echo $this->lang->line(
                    "overview",
                ); ?>
            </a>
            <a data-bs-toggle="tab" data-bs-target="#activity" href="#activity" role="tab">
                <i class="far fa-caret-square-down me-1"></i><?php echo $this->lang->line(
                    "visits",
                ); ?>
            </a>
            <a data-bs-toggle="tab" data-bs-target="#labinvestigation" href="#labinvestigation" role="tab">
                <i class="fas fa-diagnoses me-1"></i><?php echo $this->lang->line(
                    "lab_investigation",
                ); ?>
            </a>
            <a data-bs-toggle="tab" data-bs-target="#treatment_history" href="#treatment_history" role="tab">
                <i class="fas fa-file-medical-alt me-1"></i><?php echo $this->lang->line(
                    "treatment_history",
                ); ?>
            </a>
            <a data-bs-toggle="tab" data-bs-target="#timeline" href="#timeline" role="tab">
                <i class="far fa-calendar-check me-1"></i><?php echo $this->lang->line(
                    "timeline",
                ); ?>
            </a>
            <a data-bs-toggle="tab" data-bs-target="#vital" href="#vital" role="tab">
                <i class="fas fa-heartbeat me-1"></i><?php echo $this->lang->line(
                    "vital",
                ); ?>
            </a>
        </nav>
    </header>

    <div class="tab-content p-3" id="profileTabsContent">

        <!-- ══ OVERVIEW TAB ══ -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">

            <!-- bcard: patient info grid -->
            <section class="bcard">
                <div class="bcard-head">
                    <div class="title"><?php echo $this->lang->line(
                        "patient",
                    ) ?:
                        "Patient"; ?></div>
                </div>
                <div class="bcard-grid">
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "patient_id",
                        ); ?></div>
                        <div class="v mono"><?php echo $result["id"]; ?></div>
                    </div>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "gender",
                        ); ?></div>
                        <div class="v"><?php echo $this->lang->line(
                            strtolower($result["gender"]),
                        ); ?></div>
                    </div>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "phone",
                        ); ?></div>
                        <div class="v"><?php echo html_escape(
                            $result["mobileno"],
                        ); ?></div>
                    </div>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "guardian_name",
                        ); ?></div>
                        <div class="v"><?php echo html_escape(
                            $result["guardian_name"],
                        ); ?></div>
                    </div>
                    <?php if (!empty($result["email"])): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "email",
                        ); ?></div>
                        <div class="v"><?php echo html_escape(
                            $result["email"],
                        ); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($result["dob"])): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "dob",
                        ); ?></div>
                        <div class="v"><?php echo $this->customlib->YYYYMMDDTodateFormat(
                            $result["dob"],
                        ); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($result["blood_group"])): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "blood_group",
                        ); ?></div>
                        <div class="v"><?php echo html_escape(
                            $result["blood_group"],
                        ); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($result["address"])): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "address",
                        ); ?></div>
                        <div class="v"><?php echo html_escape(
                            $result["address"],
                        ); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (
                        file_exists(
                            "./uploads/patient_id_card/barcodes/" . $id . ".png",
                        )
                    ): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "barcode",
                        ); ?></div>
                        <div class="v">
                            <a href="<?php echo $this->media_storage->getImageURL(
                                "./uploads/patient_id_card/barcodes/" . $id . ".png",
                            ); ?>" target="_blank">
                                <img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL(
                                    "./uploads/patient_id_card/barcodes/" . $id . ".png",
                                ); ?>" height="30">
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (
                        file_exists(
                            "./uploads/patient_id_card/qrcode/" . $id . ".png",
                        )
                    ): ?>
                    <div class="field">
                        <div class="l"><?php echo $this->lang->line(
                            "qrcode",
                        ); ?></div>
                        <div class="v">
                            <a href="<?php echo $this->media_storage->getImageURL(
                                "./uploads/patient_id_card/qrcode/" . $id . ".png",
                            ); ?>" target="_blank">
                                <img class="patient-id-img sh-qr-code" src="<?php echo $this->media_storage->getImageURL(
                                    "./uploads/patient_id_card/qrcode/" . $id . ".png",
                                ); ?>" width="50" height="50">
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- rellist cards -->
            <div class="rellist-wrap">

                <!-- Active Problems & Allergies -->
                <?php $hasProblems =
                    !empty($patientdetails["patient"]["allergy"]) ||
                    !empty($patientdetails["patient"]["findings"]) ||
                    !empty($patientdetails["patient"]["symptoms"]); ?>
                <?php if ($hasProblems): ?>
                <div class="rellist sh-patient-rellist-warn">
                    <div class="rellist-head sh-patient-rellist-head-warn">
                        <div class="l">
                            <div class="ic sh-patient-rellist-ic-warn">!</div>
                            <div class="title sh-patient-allergies-title"><?php echo $this->lang->line(
                                "known_allergies",
                            ); ?> &amp; <?php echo $this->lang->line(
     "findings",
 ); ?></div>
                        </div>
                    </div>
                    <div class="rellist-body pad">
                        <div class="sh-profile-grid-autofit-200">
                            <?php if (
                                !empty($patientdetails["patient"]["allergy"])
                            ): ?>
                            <div>
                                <div class="sh-profile-card-label"><?php echo $this->lang->line(
                                    "known_allergies",
                                ); ?></div>
                                <div class="sh-profile-flex-stack">
                                    <?php foreach (
                                        $patientdetails["patient"]["allergy"]
                                        as $row
                                    ): ?>
                                    <div class="sh-profile-flex-row-12">
                                        <span class="badge bg-danger"><?php echo $this->lang->line(
                                            "allergy",
                                        ); ?></span>
                                        <b><?php echo html_escape(
                                            $row["known_allergies"],
                                        ); ?></b>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (
                                !empty($patientdetails["patient"]["findings"])
                            ): ?>
                            <div>
                                <div class="sh-profile-card-label"><?php echo $this->lang->line(
                                    "findings",
                                ); ?></div>
                                <div class="sh-profile-flex-stack-12">
                                    <?php foreach (
                                        $patientdetails["patient"]["findings"]
                                        as $row
                                    ): ?>
                                    <div><?php echo html_escape(
                                        $row["finding_description"],
                                    ); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (
                                !empty($patientdetails["patient"]["symptoms"])
                            ): ?>
                            <div>
                                <div class="sh-profile-card-label"><?php echo $this->lang->line(
                                    "symptoms",
                                ); ?></div>
                                <div class="sh-profile-flex-stack-12">
                                    <?php foreach (
                                        $patientdetails["patient"]["symptoms"]
                                        as $row
                                    ): ?>
                                    <div><?php echo html_escape(
                                        $row["symptoms"],
                                    ); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Vitals (big metric tiles) -->
                <?php if (!empty($patientcurrentvital)): ?>
                <div class="rellist">
                    <div class="rellist-head">
                        <div class="l">
                            <div class="ic teal">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            </div>
                            <div class="title"><?php echo $this->lang->line(
                                "current_vitals",
                            ); ?></div>
                            <div class="count"><?php echo count(
                                $patientcurrentvital,
                            ); ?> <?php echo $this->lang->line(
     "readings",
 ); ?></div>
                        </div>
                    </div>
                    <div class="rellist-body pad">
                        <?php
                        $height = "";
                        $weight = "";
                        $vitalTiles = [];
                        foreach ($patientcurrentvital as $value) {
                            $borderColor = "var(--border)";
                            $labelColor = "var(--muted)";
                            $valColor = "inherit";
                            if (
                                strpos($value["reference_range"], "-") !== false
                            ) {
                                $range = explode(
                                    "-",
                                    $value["reference_range"],
                                );
                                $min_val = (float) $range[0];
                                $max_val = (float) $range[1];
                                if (
                                    strpos($value["patient_range"], "-") !==
                                    false
                                ) {
                                    $vital = explode(
                                        "-",
                                        $value["patient_range"],
                                    );
                                    $pv1 = (float) $vital[0];
                                    $pv2 = (float) $vital[1];
                                    if ($pv1 < $min_val || $pv2 > $max_val) {
                                        $borderColor = "var(--red)";
                                        $labelColor = "var(--red)";
                                        $valColor = "var(--red)";
                                    }
                                } else {
                                    $pv = (float) $value["patient_range"];
                                    if ($pv < $min_val || $pv > $max_val) {
                                        $borderColor = "var(--red)";
                                        $labelColor = "var(--red)";
                                        $valColor = "var(--red)";
                                    }
                                }
                            }
                            if (
                                $value["id"] == "1" &&
                                $value["patient_range"] != ""
                            ) {
                                $height =
                                    (float) (strpos(
                                        $value["patient_range"],
                                        "-",
                                    ) !== false
                                        ? explode(
                                            "-",
                                            $value["patient_range"],
                                        )[0]
                                        : $value["patient_range"]);
                            }
                            if (
                                $value["id"] == "2" &&
                                $value["patient_range"] != ""
                            ) {
                                $weight =
                                    (float) (strpos(
                                        $value["patient_range"],
                                        "-",
                                    ) !== false
                                        ? explode(
                                            "-",
                                            $value["patient_range"],
                                        )[0]
                                        : $value["patient_range"]);
                            }
                            $vitalTiles[] = [
                                "name" => $value["name"],
                                "val" => $value["patient_range"],
                                "unit" => $value["unit"],
                                "border" => $borderColor,
                                "lc" => $labelColor,
                                "vc" => $valColor,
                            ];
                        }
                        if ($weight !== "" && $height > 0) {
                            $h1 = $height * 0.01;
                            $bmi = round($weight / ($h1 * $h1), 1);
                            $vitalTiles[] = [
                                "name" => "BMI",
                                "val" => $bmi,
                                "unit" => "kg/m²",
                                "border" => "var(--border)",
                                "lc" => "var(--muted)",
                                "vc" => "inherit",
                            ];
                        }
                        ?>
                        <div class="sh-profile-grid-autofill-130">
                            <?php foreach ($vitalTiles as $tile): ?>
                            <div class="sh-vital-tile" style="--vital-border:<?php echo $tile[
                                "border"
                            ]; ?>;">
                                <div class="sh-vital-tile-label" style="--vital-label:<?php echo $tile[
                                    "lc"
                                ]; ?>;"><?php echo html_escape(
    $tile["name"],
); ?></div>
                                <div class="sh-vital-tile-value" style="--vital-value:<?php echo $tile[
                                    "vc"
                                ]; ?>;">
                                    <?php echo html_escape($tile["val"]); ?>
                                    <?php if (
                                        $tile["unit"]
                                    ): ?><span class="sh-profile-text-muted-11"><?php echo html_escape(
    $tile["unit"],
); ?></span><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Consultant Doctors -->
                <?php if (!empty($patientdetails["patient"]["doctor"])): ?>
                <div class="rellist">
                    <div class="rellist-head">
                        <div class="l">
                            <div class="ic violet">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div class="title"><?php echo $this->lang->line(
                                "consultant_doctor",
                            ); ?></div>
                            <div class="count"><?php echo count(
                                $patientdetails["patient"]["doctor"],
                            ); ?></div>
                        </div>
                    </div>
                    <div class="rellist-body pad">
                        <div class="sh-profile-flex-wrap-10">
                            <?php foreach (
                                $patientdetails["patient"]["doctor"]
                                as $dv
                            ):
                                $dimg = !empty($dv["image"])
                                    ? "uploads/staff_images/" . $dv["image"]
                                    : "uploads/staff_images/no_image.png"; ?>
                            <div class="sh-profile-doc-card">
                                <img src="<?php echo $this->media_storage->getImageURL(
                                    $dimg,
                                ); ?>" class="sh-profile-doc-avatar">
                                <div>
                                    <div class="sh-profile-doc-name"><?php echo html_escape(
                                        $dv["name"] . " " . $dv["surname"],
                                    ); ?></div>
                                    <div class="sh-profile-text-muted-11"><?php echo html_escape(
                                        $dv["employee_id"],
                                    ); ?></div>
                                </div>
                            </div>
                            <?php
                            endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Recent Visits -->
                <?php if (
                    !empty($patientdetails["patient"]["visitdetails"])
                ): ?>
                <div class="rellist">
                    <div class="rellist-head">
                        <div class="l">
                            <div class="ic">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                            </div>
                            <div class="title"><?php echo $this->lang->line(
                                "visit_details",
                            ); ?></div>
                            <div class="count"><?php echo count(
                                $patientdetails["patient"]["visitdetails"],
                            ); ?> <?php echo $this->lang->line(
     "visits",
 ); ?></div>
                        </div>
                        <div class="r">
                            <a href="#activity" data-bs-toggle="tab" data-bs-target="#activity" class="btn sm"><?php echo $this->lang->line(
                                "show_all",
                            ); ?> →</a>
                        </div>
                    </div>
                    <div class="rellist-body">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line(
                                        "opd_no",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "case_id",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "appointment_date",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "consultant",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "symptoms",
                                    ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vcount = 0;
                                foreach (
                                    $patientdetails["patient"]["visitdetails"]
                                    as $vd
                                ):

                                    if (++$vcount > 5) {
                                        break;
                                    }
                                    $opd_id =
                                        $this->customlib->getPatientSessionPrefixByType(
                                            "opd_no",
                                        ) . $vd["opd_id"];
                                    ?>
                                <tr>
                                    <td><?php echo $opd_id; ?></td>
                                    <td><?php echo $vd[
                                        "case_reference_id"
                                    ]; ?></td>
                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat(
                                        $vd["appointment_date"],
                                        $timeformat,
                                    ); ?></td>
                                    <td><?php echo composeStaffNameByString(
                                        $vd["name"],
                                        $vd["surname"],
                                        $vd["employee_id"],
                                    ); ?></td>
                                    <td class="sh-profile-cell-trunc-160"><?php if (
                                        $vd["symptoms"]
                                    ) {
                                        echo html_escape(
                                            mb_strimwidth(
                                                strip_tags($vd["symptoms"]),
                                                0,
                                                60,
                                                "…",
                                            ),
                                        );
                                    } ?></td>
                                </tr>
                                <?php
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Lab Investigation -->
                <?php if (!empty($investigations)): ?>
                <div class="rellist">
                    <div class="rellist-head">
                        <div class="l">
                            <div class="ic amber">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2v7l-5 9a2 2 0 0 0 1.73 3h12.54A2 2 0 0 0 20 18L15 9V2"/></svg>
                            </div>
                            <div class="title"><?php echo $this->lang->line(
                                "lab_investigation",
                            ); ?></div>
                            <div class="count"><?php echo count(
                                $investigations,
                            ); ?></div>
                        </div>
                        <div class="r">
                            <a href="#labinvestigation" data-bs-toggle="tab" data-bs-target="#labinvestigation" class="btn sm"><?php echo $this->lang->line(
                                "show_all",
                            ); ?> →</a>
                        </div>
                    </div>
                    <div class="rellist-body">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line(
                                        "test_name",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "lab",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "expected_date",
                                    ); ?></th>
                                    <th><?php echo $this->lang->line(
                                        "approved_by",
                                    ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ii = 0;
                                foreach ($investigations as $inv):
                                    if (++$ii > $recent_record_count) {
                                        break;
                                    } ?>
                                <tr>
                                    <td><?php echo html_escape(
                                        $inv["test_name"],
                                    ); ?> <small class="text-muted">(<?php echo html_escape(
     $inv["short_name"],
 ); ?>)</small></td>
                                    <td><?php echo $this->lang->line(
                                        $inv["type"],
                                    ); ?></td>
                                    <td><?php if ($inv["reporting_date"]) {
                                        echo $this->customlib->YYYYMMDDTodateFormat(
                                            $inv["reporting_date"],
                                        );
                                    } ?></td>
                                    <td><?php echo composeStaffNameByString(
                                        $inv["approved_by_staff_name"],
                                        $inv["approved_by_staff_surname"],
                                        $inv["approved_by_staff_employee_id"],
                                    ); ?></td>
                                </tr>
                                <?php
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Timeline preview -->
                <div class="rellist">
                    <div class="rellist-head">
                        <div class="l">
                            <div class="ic blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <div class="title"><?php echo $this->lang->line(
                                "timeline",
                            ); ?></div>
                            <?php if (!empty($timeline_list)): ?>
                            <div class="count"><?php echo count(
                                $timeline_list,
                            ); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="r">
                            <a href="#timeline" data-bs-toggle="tab" data-bs-target="#timeline" class="btn sm"><?php echo $this->lang->line(
                                "show_all",
                            ); ?> →</a>
                        </div>
                    </div>
                    <div class="rellist-body pad">
                        <?php if (empty($timeline_list)): ?>
                        <div class="sh-profile-text-muted-13"><?php echo $this->lang->line(
                            "no_record_found",
                        ); ?></div>
                        <?php else: ?>
                        <ul class="overview-timeline">
                            <?php
                            $i = 0;
                            foreach ($timeline_list as $tl):
                                if (++$i > 5) {
                                    break;
                                } ?>
                            <li>
                                <div class="tl-date"><?php echo date(
                                    $this->customlib->getHospitalDateFormat(
                                        true,
                                        true,
                                    ),
                                    strtotime($tl["timeline_date"]),
                                ); ?></div>
                                <div class="tl-body">
                                    <div class="tl-dot"></div>
                                    <div>
                                        <div class="tl-title"><?php echo html_escape(
                                            $tl["title"],
                                        ); ?></div>
                                        <?php if (
                                            !empty($tl["description"])
                                        ): ?>
                                        <div class="tl-desc"><?php echo html_escape(
                                            mb_strimwidth(
                                                strip_tags($tl["description"]),
                                                0,
                                                120,
                                                "…",
                                            ),
                                        ); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($tl["document"])): ?>
                                        <a class="tl-dl" href="<?php echo base_url() .
                                            "patient/dashboard/download_patient_timeline/" .
                                            $tl[
                                                "id"
                                            ]; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
    "download",
); ?>">
                                            <i class="fa fa-download"></i> <?php echo $this->lang->line(
                                                "download",
                                            ); ?>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php
                            endforeach;
                            ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- /.rellist-wrap -->
        </div><!-- /#overview -->

        <!-- ══ VISITS TAB ══ -->
        <div class="tab-pane fade bcard" id="activity" role="tabpanel">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line(
                    "visits",
                ); ?></h3>
            </div>
            <div class="download_label"><?php echo composePatientName(
                $result["patient_name"],
                $result["id"],
            ) .
                " " .
                $this->lang->line("visits"); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example mb-0" cellspacing="0" width="100%" data-export-title="<?php echo html_escape(composePatientName($result["patient_name"], $result["id"]) . " " . $this->lang->line("visits")); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line("opd_no"); ?></th>
                            <th><?php echo $this->lang->line("case_id"); ?></th>
                            <th><?php echo $this->lang->line(
                                "appointment_date",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "consultant",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "reference",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "symptoms",
                            ); ?></th>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fv): ?>
                            <th><?php echo html_escape($fv->name); ?></th>
                            <?php endforeach;;
                            } ?>
                            <th class="text-end noExport"><?php echo $this->lang->line(
                                "action",
                            ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($opd_details)) {
                            foreach ($opd_details as $v): ?>
                        <tr>
                            <td><a href="<?php echo base_url() .
                                "patient/dashboard/visitdetails/" .
                                $v[
                                    "opdid"
                                ]; ?>"><?php echo $this->customlib->getPatientSessionPrefixByType(
    "opd_no",
) . $v["opdid"]; ?></a></td>
                            <td><?php echo $v["case_reference_id"]; ?></td>
                            <td><?php echo date(
                                $this->customlib->getHospitalDateFormat(
                                    true,
                                    true,
                                ),
                                strtotime($v["appointment_date"]),
                            ); ?></td>
                            <td><?php echo composeStaffNameByString(
                                $v["name"],
                                $v["surname"],
                                $v["employee_id"],
                            ); ?></td>
                            <td><?php echo html_escape(
                                $v["refference"],
                            ); ?></td>
                            <!-- <td class="sh-profile-cell-trunc-180" title="<?php
                                //echo html_escape(strip_tags($v['symptoms']));
                                ?>"><?php
                                //if ($v['symptoms']) echo html_escape(mb_strimwidth(strip_tags($v['symptoms']), 0, 60, '…'));
                                ?></td> -->

                            <td class="sh-profile-cell-trunc-180"
    title="<?php echo html_escape(strip_tags($v["symptoms"] ?? "")); ?>">
    <?php if ($v["symptoms"]) {
        echo html_escape(
            mb_strimwidth(strip_tags($v["symptoms"] ?? ""), 0, 60, "…"),
        );
    } ?>
</td>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fv):
                                    $dv =
                                        $fv->type == "link"
                                            ? '<a href="' .
                                                html_escape($v[$fv->name]) .
                                                '" target="_blank">' .
                                                html_escape($v[$fv->name]) .
                                                "</a>"
                                            : html_escape($v[$fv->name]); ?>
                            <td><?php echo $dv; ?></td>
                            <?php
                                endforeach;;
                            } ?>
                            <td class="text-end text-nowrap">
                                <a href="javascript:void(0)"
                                   data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>'
                                   data-opd-id="<?php echo $v["opdid"]; ?>"
                                   data-record-id="<?php echo $v["id"]; ?>"
                                   class="btn btn-sm btn-outline-secondary print_visit_bill"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
                                       "print_bill",
                                   ); ?>">
                                    <i class="fa fa-print"></i>
                                </a>
                                <?php if ($v["prescription"] == "yes"): ?>
                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
                                       "view_prescription",
                                   ); ?>"
                                   onclick="view_prescription('<?php echo $v[
                                       "visit_id"
                                   ]; ?>')">
                                    <i class="fas fa-file-prescription"></i>
                                </a>
                                <?php endif; ?>
                                <a href="#" class="btn btn-sm btn-outline-secondary get_opd_detail"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
                                       "show",
                                   ); ?>"
                                   data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>'
                                   data-record_id="<?php echo $v["id"]; ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach;;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /#activity -->

        <!-- ══ LAB INVESTIGATION TAB ══ -->
        <div class="tab-pane fade bcard" id="labinvestigation" role="tabpanel">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line(
                    "lab_investigation",
                ); ?></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example mb-0" data-export-title="<?php echo html_escape(composePatientName($result["patient_name"], $result["id"]) . " " . $this->lang->line("lab_investigation")); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line(
                                "test_name",
                            ); ?></th>
                            <th><?php echo $this->lang->line("case_id"); ?></th>
                            <th><?php echo $this->lang->line("lab"); ?></th>
                            <th><?php echo $this->lang->line(
                                "sample_collected",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "expected_date",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "approved_by",
                            ); ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line(
                                "action",
                            ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($investigations as $inv): ?>
                        <tr>
                            <td><?php echo html_escape(
                                $inv["test_name"],
                            ); ?><br><small class="text-muted">(<?php echo html_escape(
    $inv["short_name"],
); ?>)</small></td>
                            <td><?php echo $inv["case_reference_id"]; ?></td>
                            <td><?php echo $this->lang->line(
                                $inv["type"],
                            ); ?></td>
                            <td><?php echo composeStaffNameByString(
                                $inv["collection_specialist_staff_name"],
                                $inv["collection_specialist_staff_surname"],
                                $inv["collection_specialist_staff_employee_id"],
                            ); ?><br>
                                <?php echo $inv["test_center"]; ?><br>
                                <?php if ($inv["collection_date"]) {
                                    echo $this->customlib->YYYYMMDDTodateFormat(
                                        $inv["collection_date"],
                                    );
                                } ?>
                            </td>
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat(
                                $inv["reporting_date"],
                            ); ?></td>
                            <td><?php echo composeStaffNameByString(
                                $inv["approved_by_staff_name"],
                                $inv["approved_by_staff_surname"],
                                $inv["approved_by_staff_employee_id"],
                            ); ?><br>
                                <?php if ($inv["parameter_update"]) {
                                    echo $this->customlib->YYYYMMDDTodateFormat(
                                        $inv["parameter_update"],
                                    );
                                } ?>
                            </td>
                            <td class="text-end">
                                <a href="javascript:void(0)"
                                   data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>'
                                   data-record-id="<?php echo $inv[
                                       "report_id"
                                   ]; ?>"
                                   data-type-id="<?php echo $inv["type"]; ?>"
                                   data-test-id="<?php echo html_escape(
                                       $inv["test_name"] .
                                           " (" .
                                           $inv["short_name"] .
                                           ")",
                                   ); ?>"
                                   class="btn btn-sm btn-outline-secondary view_report"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
                                       "show",
                                   ); ?>">
                                    <i class="fa fa-reorder"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /#labinvestigation -->

        <!-- ══ TREATMENT HISTORY TAB ══ -->
        <div class="tab-pane fade bcard" id="treatment_history" role="tabpanel">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line(
                    "treatment_history",
                ); ?></h3>
            </div>
            <div class="download_label"><?php echo composePatientName(
                $result["patient_name"],
                $result["id"],
            ) .
                " " .
                $this->lang->line("opd_details"); ?></div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover treatmentlist mb-0" data-export-title="<?php echo html_escape(composePatientName($result["patient_name"], $result["id"]) . " " . $this->lang->line("treatment_history")); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line("opd_no"); ?></th>
                            <th><?php echo $this->lang->line("case_id"); ?></th>
                            <th><?php echo $this->lang->line(
                                "appointment_date",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "symptoms",
                            ); ?></th>
                            <th><?php echo $this->lang->line(
                                "consultant",
                            ); ?></th>
                            <th class="text-end noExport"><?php echo $this->lang->line(
                                "action",
                            ); ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div><!-- /#treatment_history -->

        <!-- ══ TIMELINE TAB ══ -->
        <div class="tab-pane fade bcard" id="timeline" role="tabpanel">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line(
                    "timeline",
                ); ?></h3>
            </div>
            <div class="timeline-header no-border">
                <div id="timeline_list">
                    <?php if (empty($timeline_list)): ?>
                    <div class="alert alert-info m-3"><?php echo $this->lang->line(
                        "no_record_found",
                    ); ?></div>
                    <?php else: ?>
                    <ul class="timeline timeline-inverse">
                        <?php foreach ($timeline_list as $tl): ?>
                        <li class="time-label">
                            <span class="bg-blue"><?php echo date(
                                $this->customlib->getHospitalDateFormat(
                                    true,
                                    true,
                                ),
                                strtotime($tl["timeline_date"]),
                            ); ?></span>
                        </li>
                        <li>
                            <i class="fa fa-list-alt bg-blue"></i>
                            <div class="timeline-item">
                                <?php if (!empty($tl["document"])): ?>
                                <span class="time"><a href="<?php echo base_url() .
                                    "patient/dashboard/download_patient_timeline/" .
                                    $tl[
                                        "id"
                                    ]; ?>" data-bs-toggle="tooltip" title="<?php echo $this->lang->line(
    "download",
); ?>"><i class="fa fa-download"></i></a></span>
                                <?php endif; ?>
                                <h3 class="timeline-header text-aqua"><?php echo $tl[
                                    "title"
                                ]; ?></h3>
                                <div class="timeline-body"><?php echo $tl[
                                    "description"
                                ]; ?></div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        <li><i class="fa fa-clock-o bg-gray"></i></li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div><!-- /#timeline -->

        <!-- ══ VITAL TAB ══ -->
        <div class="tab-pane fade bcard" id="vital" role="tabpanel">
            <div class="box-tab-header">
                <h3 class="box-tab-title"><?php echo $this->lang->line(
                    "vitals",
                ); ?></h3>
            </div>
            <div id="vital_list" class="p-3">
                <?php if (empty($patient_vital_date)): ?>
                <div class="alert alert-info"><?php echo $this->lang->line(
                    "no_record_found",
                ); ?></div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line(
                                    "date",
                                ); ?></th>
                                <?php foreach ($vital_list as $vl): ?>
                                <th><?php echo html_escape(
                                    $vl["name"],
                                ); ?><br><small class="text-muted">(<?php echo html_escape(
    $vl["reference_range"],
); ?> <?php echo html_escape($vl["unit"]); ?>)</small></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patient_vital_date as $pvd):
                                $messure_date = date(
                                    "Y-m-d",
                                    strtotime($pvd["messure_date"]),
                                ); ?>
                            <tr>
                                <th><?php echo $this->customlib->YYYYMMDDTodateFormat(
                                    $pvd["messure_date"],
                                ); ?></th>
                                <?php foreach ($vital_list as $vl):
                                    $vid = $vl["id"]; ?>
                                <td>
                                    <?php if (
                                        !empty(
                                            $patientvital[$messure_date][$vid]
                                        )
                                    ): ?>
                                    <?php foreach (
                                        $patientvital[$messure_date][$vid]
                                        as $pmi
                                    ):

                                        $dt = $this->customlib->YYYYMMDDHisTodateFormat(
                                            $pmi["messure_date"],
                                            $this->customlib->getHospitalTimeFormat(),
                                        );
                                        $str = substr($dt, 10);
                                        ?>
                                    <?php echo html_escape(
                                        $pmi["patient_range"],
                                    ); ?> <small class="text-muted">(<?php echo $str; ?>)</small><br>
                                    <?php
                                    endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <?php
                                endforeach; ?>
                            </tr>
                            <?php
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div><!-- /#vital -->

    </div><!-- /.tab-content -->
</div><!-- /.opd-profile-wrap -->
</div><!-- /.container-fluid -->


<!-- ══ MODAL: Live Consultation Status ══ -->
<div id="modal-chkstatus" class="modal fade sh-modal sh-modal-branded" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="form-chkstatus" action="" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $this->lang->line(
                        "live_consultation",
                    ); ?></h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body" id="zoom_details"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ══ MODAL: Prescription View ══ -->
<div class="modal fade sh-modal sh-modal-accent" id="prescriptionview" tabindex="-1" aria-labelledby="prescriptionviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionviewLabel"><?php echo $this->lang->line(
                    "prescription",
                ); ?></h5>
                <div id="edit_deleteprescription" class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails_prescription"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line(
                    "close",
                ); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL: Visit Details ══ -->
<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $this->lang->line(
                    "visit_details",
                ); ?></h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line(
                                "visit_details",
                            ); ?></span>
                        </div>
                        <div class="sh-info-grid">
                            <div class="row g-0">
                                <div class="col-6 col-md-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "name",
                                    ); ?></span>
                                    <span class="sh-info-value highlight"><span id="patient_name"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "gender",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="gender"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "age",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="age"></span><span id="month"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "email",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="email"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "appointment_date",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="appointment_date"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "case",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="case"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "casualty",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="casualty"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-6 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "consultant_doctor",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="cons_doctor"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "reference",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="refference"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "payment_mode",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="payment_mode"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-12 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line(
                                        "symptoms",
                                    ); ?></span>
                                    <span class="sh-info-value"><span id="symptoms"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line(
                    "close",
                ); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL: Lab Investigation Report ══ -->
<div class="modal fade sh-modal sh-modal-branded" id="viewDetailReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id="action_detail_report_modal"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line(
                    "close",
                ); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + visitid,
            success: function (res) {
                $("#edit_deleteprescription").html(
                    "<a href='#' onclick='printPrescription(" + visitid + ")' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line(
                        "print",
                    ); ?>'><i class='fa fa-print'></i></a>"
                );
                $("#getdetails_prescription").html(res);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('prescriptionview'), {backdrop: 'static', keyboard: false}).show();
            },
            error: function () { alert("Fail"); }
        });
    }

    function getRecord(id, visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getDetails',
            type: "POST",
            data: {patient_id: id, visitid: visitid},
            dataType: 'json',
            success: function (data) {
                $("#patient_name").html(data.patient_name);
                $("#gender").html(data.gender);
                $("#casualty").html(data.casualty);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#age").html(data.age);
                $("#guardian_name").html(data.guardian_name);
                $("#appointment_date").html(data.appointment_date);
                $("#case").html(data.case_type);
                $("#symptoms").html(data.symptoms);
                $("#known_allergies").html(data.known_allergies);
                $("#refference").html(data.refference);
                $("#cons_doctor").html(data.doctor_name);
                $("#amount").html(data.amount);
                $("#tax").html(data.tax);
                $("#payment_mode").html(data.payment_mode);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#updateid").val(id);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static', keyboard: false}).show();
            }
        });
    }

    $('#print_id').show();
    function printPrescription(id, opdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + id + (opdid ? '/' + opdid : ''),
            type: 'POST',
            data: {payslipid: id, print: 'print'},
            success: function (result) { popup(result); }
        });
    }

    /* Live consultation modal */
    $('#modal-chkstatus').on('shown.bs.modal', function (e) {
        var $modalDiv = $(e.delegateTarget);
        var id = $(e.relatedTarget).data('id');
        $.ajax({
            type: "POST",
            url: '<?php echo site_url("patient/dashboard/getlivestatus"); ?>',
            data: {'id': id},
            dataType: "JSON",
            beforeSend: function () { $('#zoom_details').html(""); $modalDiv.addClass('modal_loading'); },
            success:  function (data) { $('#zoom_details').html(data.page); $modalDiv.removeClass('modal_loading'); },
            error:    function ()     { $modalDiv.removeClass('modal_loading'); },
            complete: function ()     { $modalDiv.removeClass('modal_loading'); }
        });
    });

    /* OPD detail click → viewModal */
    $(document).on('click', '.get_opd_detail', function () {
        var visitid = $(this).data('record_id');
        var $this   = $(this);
        $.ajax({
            url: base_url + 'patient/dashboard/getopdDetails',
            type: "POST",
            data: {visit_id: visitid},
            dataType: 'json',
            beforeSend: function () { $this.button('loading'); },
            success:  function (data) {
                $('#viewModal .modal-body').html(data.page);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'), {backdrop: 'static', keyboard: false}).show();
            },
            error:    function () { alert("<?php echo $this->lang->line(
                "error_occurred_please_try_again",
            ); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    /* Lab investigation report */
    $(document).on('click', '.view_report', function () {
        var id   = $(this).data('record-id');
        var lab  = $(this).data('type-id');
        var test = $(this).data('test-id');
        var $this = $(this);
        $.ajax({
            url: base_url + 'patient/dashboard/getinvestigationparameter',
            type: "POST",
            data: {'id': id, 'lab': lab},
            dataType: 'json',
            beforeSend: function () { $this.button('loading'); $('#viewDetailReportModal').addClass('modal_loading'); },
            success:  function (data) {
                $('#viewDetailReportModal .modal-body').html(data.page);
                $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
                $('#viewDetailReportModal #modal_head').html(test);
                // Tooltips on injected buttons are handled by the body-delegated tooltip in layout/footer.php.
                // Do NOT re-init here: a second new bootstrap.Tooltip() strips the native title attribute,
                // leaving the delegated handler with an empty title (tooltip silently disappears).
                bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDetailReportModal'), {backdrop: 'static', keyboard: false}).show();
                $('#viewDetailReportModal').removeClass('modal_loading');
            },
            error:    function () { alert("<?php echo $this->lang->line(
                "error_occurred_please_try_again",
            ); ?>"); $this.button('reset'); $('#viewDetailReportModal').removeClass('modal_loading'); },
            complete: function () { $this.button('reset'); $('#viewDetailReportModal').removeClass('modal_loading'); }
        });
    });

    /* Print pathology bill */
    $(document).on('click', '.print_bill', function () {
        var id  = $(this).data('record-id');
        var lab = $(this).data('type-id');
        var $this = $(this);
        $.ajax({
            url: base_url + 'patient/dashboard/printpathoparameter',
            type: "POST",
            data: {'id': id, 'lab': lab},
            dataType: 'json',
            beforeSend: function () { $this.button('loading'); },
            success:  function (data) { popup(data.page); },
            error:    function () { alert("<?php echo $this->lang->line(
                "error_occurred_please_try_again",
            ); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    /* Print visit bill */
    $(document).on('click', '.print_visit_bill', function () {
        var opd_id = $(this).data('opd-id');
        var $this  = $(this);
        $.ajax({
            url: base_url + 'patient/dashboard/printbill',
            type: "POST",
            data: {opd_id: opd_id},
            dataType: 'json',
            beforeSend: function () { $this.button('loading'); },
            success:  function (data) { popup(data.page); },
            error:    function () { alert("<?php echo $this->lang->line(
                "error_occurred_please_try_again",
            ); ?>"); $this.button('reset'); },
            complete: function () { $this.button('reset'); }
        });
    });

    /* Recalculate DataTable columns on tab switch */
    $(document).on('shown.bs.tab', '[data-bs-toggle="tab"]', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
    });

    /* Treatment history DataTable (AJAX) */
    $(document).ready(function () {
        modal_click_disabled('viewModal');
        modal_click_disabled('prescriptionview');
        modal_click_disabled('viewDetailReportModal');
        modal_click_disabled('modal-chkstatus');

        var id = "<?php echo $result["id"]; ?>";
        initDatatable('treatmentlist', 'patient/dashboard/getopdtreatmenthistory/' + id);

        /* Initialize Tooltips */
        $('[data-bs-toggle="tooltip"]').each(function() {
            bootstrap.Tooltip.getOrCreateInstance(this);
        });
    });

</script>

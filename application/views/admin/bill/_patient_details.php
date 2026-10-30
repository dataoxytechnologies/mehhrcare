<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if (!empty($result)) {
    $patient_id  = $result['patient_id'];
    $has_image   = !empty($result['image']) && strpos($result['image'], 'no_image') === false;
    $file        = $has_image ? $result['image'] : 'uploads/patient_images/no_image.png';
    $gender      = isset($result['gender']) ? strtolower(trim($result['gender'])) : '';
    $gender_lbl  = $gender ? $this->lang->line($gender) : '';
    $gender_cls  = ($gender === 'male') ? 'bill-badge-blue' : (($gender === 'female') ? 'bill-badge-pink' : '');
    $clean_name  = preg_replace('/\s*\([^)]*\)\s*/', ' ', (string) $result['patient_name']);
    $name_parts  = preg_split('/\s+/', trim($clean_name), -1, PREG_SPLIT_NO_EMPTY);
    if (empty($name_parts)) {
        $initials = '?';
    } elseif (count($name_parts) === 1) {
        $initials = mb_substr($name_parts[0], 0, 1);
    } else {
        $initials = mb_substr($name_parts[0], 0, 1) . mb_substr(end($name_parts), 0, 1);
    }
    $initials = mb_strtoupper($initials);
?>
<div class="bill-patient-panel">
    <div class="d-flex align-items-start gap-4 flex-wrap">

        <!-- ── Photo + summary ── -->
        <div class="bill-patient-photo text-center flex-shrink-0">
            <?php if ($has_image): ?>
                <img class="bill-patient-avatar"
                     src="<?php echo $this->media_storage->getImageURL($file); ?>" alt="">
            <?php else: ?>
                <div class="bill-patient-initials" aria-hidden="true"><?php echo html_escape($initials); ?></div>
            <?php endif; ?>
            <?php if ($gender_lbl): ?>
            <div class="mt-1">
                <span class="bill-gender-badge <?php echo $gender_cls; ?>"><?php echo $gender_lbl; ?></span>
            </div>
            <?php endif; ?>
            <button class="btn bill-btn-summary w-100 showbill mt-2"
                    data-case-id="<?php echo $case_id; ?>">
                <i class="fa fa-file-text-o me-1"></i><?php echo $this->lang->line('bill_summary'); ?>
            </button>
        </div>

        <!-- ── Info grid ── -->
        <div class="flex-grow-1 min-w-0">
            <div class="bill-info-grid">

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('case_id'); ?></div>
                    <div class="bii-value bii-accent"><?php echo $case_id; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('name'); ?></div>
                    <div class="bii-value"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('age'); ?></div>
                    <div class="bii-value"><?php echo $this->customlib->get_patient_current_age($result['patient_id']); ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('phone'); ?></div>
                    <div class="bii-value"><?php echo $result['mobileno'] ?: '—'; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('guardian_name'); ?></div>
                    <div class="bii-value"><?php echo $result['guardian_name'] ?: '—'; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('appointment_date'); ?></div>
                    <div class="bii-value"><?php
                        echo ($result['appointment_date'] != '' && $result['appointment_date'] != '0000-00-00')
                            ? $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->customlib->getHospitalTimeFormat())
                            : '—';
                    ?></div>
                </div>

                <?php if (!empty($result['credit_limit'])): ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('credit_limit') . ' (' . $currency_symbol . ')'; ?></div>
                    <div class="bii-value"><?php echo $result['credit_limit']; ?></div>
                </div>
                <?php endif; ?>

                <?php if ($result['opdid'] != '' && $result['opdid'] != 0): ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('opd_no'); ?></div>
                    <div class="bii-value bii-accent"><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $result['opdid']; ?></div>
                </div>
                <?php endif; ?>

                <?php if ($result['ipdid'] != '' && $result['ipdid'] != 0): ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('ipd_no'); ?></div>
                    <div class="bii-value bii-accent">
                        <?php echo $this->customlib->getSessionPrefixByType('ipd_no') . $result['ipdid']; ?>
                        <?php if ($result['discharged'] == 'yes'): ?>
                            <span class="badge bg-warning text-dark ms-1 sh-badge-sm"><?php echo $this->lang->line('discharged'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('admission_date'); ?></div>
                    <div class="bii-value"><?php
                        echo ($result['date'] != '' && $result['date'] != '0000-00-00')
                            ? $this->customlib->YYYYMMDDTodateFormat($result['date'])
                            : '—';
                    ?></div>
                </div>
                <div class="bii bii-wide">
                    <div class="bii-label"><?php echo $this->lang->line('bed'); ?></div>
                    <div class="bii-value"><?php echo $result['bed_name'] . ' · ' . $result['bedgroup_name'] . ' · ' . $result['floor_name']; ?></div>
                </div>
                <?php endif; ?>

                <?php if (file_exists("./uploads/patient_id_card/barcodes/$patient_id.png")): ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('barcode'); ?></div>
                    <div class="bii-value">
                        <a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/$patient_id.png"); ?>" target="_blank">
                            <img class="sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/barcodes/$patient_id.png"); ?>" width="100" height="32">
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (file_exists("./uploads/patient_id_card/qrcode/$patient_id.png")): ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('qrcode'); ?></div>
                    <div class="bii-value">
                        <a href="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/$patient_id.png"); ?>" target="_blank">
                            <img class="sh-qr-code" src="<?php echo $this->media_storage->getImageURL("./uploads/patient_id_card/qrcode/$patient_id.png"); ?>" width="44" height="44">
                        </a>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>
<?php } ?>

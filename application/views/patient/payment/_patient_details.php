<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$file = (!empty($result['image'])) ? $result['image'] : "uploads/patient_images/no_image.png";

/* Scannable patient identifiers — reuse the ID-card generator (Customlib::generatebarcode).
   Each call writes a Code128 PNG + a QR PNG keyed by patient id and returns the path.
   Wrapped defensively: if GD/Zend/QR generation fails we simply skip the scan row
   rather than blank out the whole (AJAX-loaded) patient card. */
$sh_pid      = isset($result['patient_id']) ? $result['patient_id'] : '';
$barcode_img = '';
$qrcode_img  = '';
if ($sh_pid !== '') {
    try {
        $barcode_img = $this->customlib->generatebarcode($sh_pid, 'barcode');
        $qrcode_img  = $this->customlib->generatebarcode($sh_pid, 'qrcode');
    } catch (Throwable $e) {
        $barcode_img = $qrcode_img = '';
    }
}
?>
<div class="sh-form-card h-100 mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-user me-1"></i><?php echo $this->lang->line('patient_details'); ?></span>
    </div>
    <div class="p-3 d-flex align-items-start gap-3">

        <div class="bill-patient-photo text-center flex-shrink-0">
            <img class="bill-patient-avatar" src="<?php echo $this->media_storage->getImageURL($file); ?>" alt="">
            <div class="mt-1 fw-semibold" style="font-size:12px"><?php echo composePatientName($result['patient_name'], $result['patient_id']); ?></div>
        </div>

        <div class="flex-grow-1 min-w-0">
            <div class="bill-info-grid">

                <?php if (isset($case_id)) { ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('case_id'); ?></div>
                    <div class="bii-value bii-accent"><?php echo $case_id; ?></div>
                </div>
                <?php } ?>

                <?php if (isset($result['appointment_date'])) { ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('appointment_date'); ?></div>
                    <div class="bii-value">
                        <?php echo ($result['appointment_date'] != '' && $result['appointment_date'] != '0000-00-00')
                            ? $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->customlib->getHospitalTimeFormat())
                            : '&mdash;'; ?>
                    </div>
                </div>
                <?php } ?>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('guardian_name'); ?></div>
                    <div class="bii-value"><?php echo $result['guardian_name'] ?: '&mdash;'; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('gender'); ?></div>
                    <div class="bii-value"><?php echo $result['gender'] ? $this->lang->line(strtolower($result['gender'])) : '&mdash;'; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('age'); ?></div>
                    <div class="bii-value"><?php echo $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']) ?: '&mdash;'; ?></div>
                </div>

                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('phone'); ?></div>
                    <div class="bii-value"><?php echo $result['mobileno'] ?: '&mdash;'; ?></div>
                </div>

                <?php if (isset($result['opdid']) && $result['opdid'] != '' && $result['opdid'] != 0) { ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('opd_no'); ?></div>
                    <div class="bii-value"><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') . $result['opdid']; ?></div>
                </div>
                <?php } ?>

                <?php if (!empty($qrcode_img)) { ?>
                <div class="bii">
                    <div class="bii-label"><i class="fa fa-qrcode me-1"></i><?php echo $this->lang->line('qr_code') ?: 'QR Code'; ?></div>
                    <div class="bii-value"><img class="sh-bii-qr" src="<?php echo $this->media_storage->getImageURL($qrcode_img); ?>" alt="QR <?php echo html_escape($sh_pid); ?>"></div>
                </div>
                <?php } ?>

                <?php if (!empty($barcode_img)) { ?>
                <div class="bii">
                    <div class="bii-label"><i class="fa fa-barcode me-1"></i><?php echo $this->lang->line('barcode') ?: 'Barcode'; ?></div>
                    <div class="bii-value"><img class="sh-bii-barcode" src="<?php echo $this->media_storage->getImageURL($barcode_img); ?>" alt="Barcode <?php echo html_escape($sh_pid); ?>"></div>
                </div>
                <?php } ?>

                <?php if (isset($result['ipdid']) && $result['ipdid'] != '' && $result['ipdid'] != 0) { ?>
                <div class="bii">
                    <div class="bii-label"><?php echo $this->lang->line('ipd_no'); ?></div>
                    <div class="bii-value">
                        <?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no') . $result['ipdid']; ?>
                        <?php if ($result['discharged'] == 'yes') { ?>
                            <span class="badge bg-warning text-dark ms-1"><?php echo $this->lang->line('discharged'); ?></span>
                        <?php } ?>
                    </div>
                </div>

                    <?php if (isset($result['credit_limit'])) { ?>
                    <div class="bii">
                        <div class="bii-label"><?php echo $this->lang->line('credit_limit') . ' (' . $currency_symbol . ')'; ?></div>
                        <div class="bii-value"><?php echo $result['credit_limit']; ?></div>
                    </div>
                    <?php } ?>

                    <?php if (isset($result['date'])) { ?>
                    <div class="bii">
                        <div class="bii-label"><?php echo $this->lang->line('admission_date'); ?></div>
                        <div class="bii-value">
                            <?php echo ($result['date'] != '' && $result['date'] != '0000-00-00')
                                ? $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat())
                                : '&mdash;'; ?>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if (isset($result['bed_name'])) { ?>
                    <div class="bii">
                        <div class="bii-label"><?php echo $this->lang->line('bed'); ?></div>
                        <div class="bii-value"><?php echo $result['bed_name'] . ' &ndash; ' . $result['bedgroup_name'] . ' &ndash; ' . $result['floor_name']; ?></div>
                    </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>

    </div>
</div>

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details['print_header'])) { ?>
            <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>" class="img-fluid sh-avatar-cover"
                >
    <?php } ?>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr>
            <td>
                <div class="header-space">&nbsp;</div>
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="content-body sh-px-12" >
                    <div class="print-area">

                        <!-- ① Document title -->
                        <div class="sh-print-title"><?php echo $this->lang->line('prescription'); ?></div>

                        <!-- ② Patient / prescription info block -->
                        <div class="sh-print-info-block">
                            <table class="sh-print-info-table">
                                <colgroup>
                                    <col style="width:16%">
                                    <col style="width:18%">
                                    <col style="width:16%">
                                    <col style="width:16%">
                                    <col style="width:16%">
                                    <col style="width:18%">
                                </colgroup>
                                <tr>
                                    <th><?php echo $this->lang->line('prescription'); ?></th>
                                    <td><?php echo $this->customlib->getSessionPrefixByType('ipd_prescription') . ($result->prescription_id ?: '-'); ?>
                                    </td>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <td><?php echo !empty($result->pres_created_at) ? $this->customlib->YYYYMMDDHisTodateFormat($result->pres_created_at) : '-'; ?>
                                    </td>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <td><?php echo ($result->patient_name ? composePatientName($result->patient_name, $result->id) : '-'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <td><?php echo $this->customlib->get_patient_current_age($result->id) ?: '-'; ?>
                                    </td>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <td><?php echo isset($result->gender) ? $this->lang->line(strtolower($result->gender)) : '-'; ?>
                                    </td>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <td><?php echo ($result->blood_group_name ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <td><?php echo ($result->mobileno ?: '-'); ?></td>
                                    <th><?php echo $this->lang->line('email'); ?></th>
                                    <td><?php echo ($result->email ?: '-'); ?></td>
                                    <th><?php echo $this->lang->line('prescribe_by'); ?></th>
                                    <td>
                                        <?php echo ($result->priscribe_by_name ? composeStaffNameByString($result->priscribe_by_name, $result->priscribe_by_surname, $result->priscribe_by_employee_id) : '-'); ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                    <td>
                                        <?php echo ($result->name ? composeStaffNameByString($result->name, $result->surname, $result->employee_id) : '-'); ?>
                                    </td>
                                    <th><?php echo $this->lang->line('generated_by'); ?></th>
                                    <td>
                                        <?php echo ($result->staff_name ? composeStaffNameByString($result->staff_name, $result->staff_surname, $result->staff_employee_id) : '-'); ?>
                                    </td>
                                    <th></th>
                                    <td></td>
                                </tr>

                                <?php if (!empty($fields_prescription)) {
                                    foreach ($fields_prescription as $cf_value) { ?>
                                                <tr>
                                                    <th><?php echo $cf_value->name; ?></th>
                                                    <td colspan="5"><?php echo ($result->{"$cf_value->name"} ?: '-'); ?></td>
                                                </tr>
                                        <?php }
                                } ?>
                            </table>
                        </div>

                        <!-- Divider for clarity -->
                        <div class="sh-section-divider"></div>

                        <!-- ③ Symptoms & Findings -->
                        <?php
                        $has_symptoms = ($result->symptoms != '');
                        $has_finding  = ($result->is_finding_print == 'yes' && trim($result->finding_description) != '');
                        if ($has_symptoms || $has_finding) {
                            $sf_titles = array();
                            if ($has_symptoms) { $sf_titles[] = $this->lang->line('symptoms'); }
                            if ($has_finding)  { $sf_titles[] = $this->lang->line('finding'); }
                            ?>
                                <div class="sh-print-section-title"><?php echo implode(' / ', $sf_titles); ?></div>
                                <div class="sh-flex-row-g20-mb10">
                                    <?php if ($has_symptoms) { ?>
                                            <div class="sh-flex-text">
                                                <?php if ($has_finding) { ?>
                                                        <div class="sh-print-sublabel"><?php echo $this->lang->line('symptoms'); ?></div>
                                                <?php } ?>
                                                <div class="sh-print-value"><?php echo nl2br($result->symptoms); ?></div>
                                            </div>
                                    <?php } ?>
                                    <?php if ($has_finding) { ?>
                                            <div class="sh-flex-text">
                                                <?php if ($has_symptoms) { ?>
                                                        <div class="sh-print-sublabel"><?php echo $this->lang->line('finding'); ?></div>
                                                <?php } ?>
                                                <div class="sh-print-value"><?php echo nl2br($result->finding_description); ?></div>
                                            </div>
                                    <?php } ?>
                                </div>
                        <?php } ?>

                        <!-- ④ Header note -->
                        <?php if (!empty($result->header_note)) { ?>
                                <div
                                    class="sh-text-11-pad">
                                    <?php echo $result->header_note; ?>
                                </div>
                        <?php } ?>

                        <!-- ⑤ Medicines -->
                        <?php if (!empty($result->medicines)) { ?>
                                <div class="sh-print-section-title"><?php echo $this->lang->line('medicines'); ?></div>
                                <table class="sh-print-table">
                                    <thead>
                                        <tr>
                                            <th style="width:4%">#</th>
                                            <th style="width:26%"><?php echo $this->lang->line('medicine'); ?></th>
                                            <th style="width:14%"><?php echo $this->lang->line('dosage'); ?></th>
                                            <th style="width:18%"><?php echo $this->lang->line('dose_interval'); ?></th>
                                            <th style="width:18%"><?php echo $this->lang->line('dose_duration'); ?></th>
                                            <th><?php echo $this->lang->line('instruction'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $medsl = 0;
                                        foreach ($result->medicines as $pvalue) {
                                            $medsl++; ?>
                                                <tr>
                                                    <td><?php echo $medsl; ?></td>
                                                    <td class="fw-bold"><?php echo $pvalue->medicine_name; ?></td>
                                                    <td><?php echo $pvalue->dosage . ' ' . $pvalue->unit; ?></td>
                                                    <td><?php echo $pvalue->dose_interval_name; ?></td>
                                                    <td><?php echo $pvalue->dose_duration_name; ?></td>
                                                    <td><?php echo $pvalue->instruction; ?></td>
                                                </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                        <?php } ?>

                        <!-- ⑥ Tests (Pathology & Radiology) -->
                        <?php if (!empty($result->tests)) {
                            $r = $p = 0;
                            foreach ($result->tests as $test_value) {
                                if ($test_value->test_name != '') {
                                    $p = 1;
                                }
                            }
                            foreach ($result->tests as $test_value) {
                                if ($test_value->radio_test_name != '') {
                                    $r = 1;
                                }
                            }
                            ?>
                                <div class="sh-print-section-title">
                                    <?php
                                    if ($p) {
                                        echo $this->lang->line('pathology_test');
                                    }
                                    if ($p && $r) {
                                        echo ' / ';
                                    }
                                    if ($r) {
                                        echo $this->lang->line('radiology_test');
                                    }
                                    ?>
                                </div>
                                <div class="sh-flex-row-g20-mb14">
                                    <?php if ($p) { ?>
                                            <div class="flex-fill">
                                                <div class="sh-print-sublabel"><?php echo $this->lang->line('pathology_test'); ?></div>
                                                <ul class="sh-list-inline-bold">
                                                    <?php $sl = 0;
                                                    foreach ($result->tests as $test_value) {
                                                        if ($test_value->test_name != '') {
                                                            $sl++; ?>
                                                                    <li class="sh-mb-3px">
                                                                        <?php echo $sl . '. ' . $test_value->test_name . ' (' . $test_value->short_name . ')'; ?>
                                                                    </li>
                                                            <?php }
                                                    } ?>
                                                </ul>
                                            </div>
                                    <?php } ?>
                                    <?php if ($r) { ?>
                                            <div class="flex-fill">
                                                <div class="sh-print-sublabel"><?php echo $this->lang->line('radiology_test'); ?></div>
                                                <ul class="sh-list-inline-bold">
                                                    <?php $slradiology = 0;
                                                    foreach ($result->tests as $test_value) {
                                                        if ($test_value->radio_test_name != '') {
                                                            $slradiology++; ?>
                                                                    <li class="sh-mb-3px">
                                                                        <?php echo $slradiology . '. ' . $test_value->radio_test_name . ' (' . $test_value->radio_short_name . ')'; ?>
                                                                    </li>
                                                            <?php }
                                                    } ?>
                                                </ul>
                                            </div>
                                    <?php } ?>
                                </div>
                        <?php } ?>

                        <!-- ⑦ Footer note -->
                        <?php if (!empty($result->footer_note)) { ?>
                                <div
                                    style="font-size:11px; color:#111; line-height:1.6; margin: 14px 0; border-top: 1px solid #f0f0f0; padding-top: 8px;">
                                    <?php echo $result->footer_note; ?>
                                </div>
                        <?php } ?>

                    </div>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td>
                <?php if (!empty($print_details['print_footer'])) { ?>
                        <div class="footer-space">&nbsp;</div>
                <?php } ?>
            </td>
        </tr>
    </tfoot>
</table>

<?php if (!empty($print_details['print_footer'])) { ?>
        <div class="footer-fixed">
            <?php echo $print_details['print_footer']; ?>
        </div>
<?php } ?>
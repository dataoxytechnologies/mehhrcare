<?php
include(APPPATH . 'views/admin/shared/_print_css.php');
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>"
             class="img-fluid sh-avatar-cover" >
    <?php } ?>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
            <div class="content-body sh-px-12" >
            <div class="print-area">

                <!-- ① Document title -->
                <div class="sh-print-title"><?php echo $this->lang->line('prescription'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('prescription'); ?> #</th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('ipd_prescription') . ($result->prescription_id ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php echo (!empty($result->presdate) ? $this->customlib->YYYYMMDDTodateFormat($result->presdate) : '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo composePatientName($result->patient_name, $result->id); ?></td>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            <th><?php echo $this->lang->line('weight'); ?></th>
                            <td><?php echo ($result->weight ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('bp'); ?></th>
                            <td><?php echo ($result->bp ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('phone'); ?></th>
                            <td><?php echo ($result->mobileno ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <td><?php echo ($result->blood_group_name ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('pulse'); ?></th>
                            <td><?php echo ($result->pulse ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('height'); ?></th>
                            <td><?php echo ($result->height ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('temperature'); ?></th>
                            <td><?php echo ($result->temperature ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('email'); ?></th>
                            <td><?php echo ($result->email ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo composeStaffNameByString($result->name, $result->surname, $result->employee_id); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('generated_by'); ?></th>
                            <td><?php echo composeStaffNameByString($result->staff_name, $result->staff_surname, $result->staff_employee_id); ?></td>
                            <th><?php echo $this->lang->line('prescribe_by'); ?></th>
                            <td><?php echo composeStaffNameByString($result->priscribe_by_name, $result->priscribe_by_surname, $result->priscribe_by_employee_id); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider -->
                <div class="sh-section-divider"></div>

                <!-- ③ Symptoms / Findings -->
                <?php if ($result->symptoms != '' || $result->is_finding_print == 'yes') { ?>
                <table width="100%" style="border-collapse:collapse; margin-bottom:12px;">
                    <tr>
                        <?php if ($result->symptoms != '') { ?>
                        <td style="vertical-align:top; padding-right:12px; <?php echo ($result->is_finding_print == 'yes') ? 'width:50%;' : ''; ?>">
                            <div class="sh-label-mini"><?php echo $this->lang->line('symptoms'); ?></div>
                            <div class="sh-text-11-5"><?php echo nl2br(html_escape($result->symptoms)); ?></div>
                        </td>
                        <?php } ?>
                        <?php if ($result->is_finding_print == 'yes') { ?>
                        <td class="align-top">
                            <div class="sh-label-mini"><?php echo $this->lang->line('finding'); ?></div>
                            <div class="sh-text-11-5"><?php echo nl2br(html_escape($result->finding_description)); ?></div>
                        </td>
                        <?php } ?>
                    </tr>
                </table>
                <?php } ?>

                <!-- Header note -->
                <?php if (!empty($result->header_note)) { ?>
                <div style="margin-bottom:12px; font-size:11.5px; color:#111;"><?php echo $result->header_note; ?></div>
                <?php } ?>

                <!-- ④ Medicines -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('medicines'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th style="width:15%"><?php echo $this->lang->line('medicine_category'); ?></th>
                            <th style="width:16%"><?php echo $this->lang->line('medicine'); ?></th>
                            <th class="sh-col-13 text-center"><?php echo $this->lang->line('dosage'); ?></th>
                            <th class="sh-col-16 text-center"><?php echo $this->lang->line('dose_interval'); ?></th>
                            <th class="sh-col-16 text-center"><?php echo $this->lang->line('dose_duration'); ?></th>
                            <th class="text-center"><?php echo $this->lang->line('instruction'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $medsl = 0; foreach ($result->medicines as $pkey => $pvalue) { $medsl++; ?>
                        <tr>
                            <td><?php echo $medsl; ?></td>
                            <td><?php echo html_escape($pvalue->medicine_category); ?></td>
                            <td><?php echo html_escape($pvalue->medicine_name); ?></td>
                            <td class="text-center"><?php echo html_escape($pvalue->dosage . ' ' . $pvalue->unit); ?></td>
                            <td class="text-center"><?php echo html_escape($pvalue->dose_interval_name); ?></td>
                            <td class="text-center"><?php echo html_escape($pvalue->dose_duration_name); ?></td>
                            <td class="text-center"><?php echo html_escape($pvalue->instruction); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- ⑤ Tests -->
                <?php if (!empty($result->tests)) { ?>
                <div class="sh-print-section-title sh-mt-12" ><?php echo $this->lang->line('tests'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th class="w-50"><?php echo $this->lang->line('pathology_test'); ?></th>
                            <th><?php echo $this->lang->line('radiology_test'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="align-top">
                                <?php $sl = 0; foreach ($result->tests as $test_key => $test_value) {
                                    if ($test_value->test_name != '') { $sl++; ?>
                                <div class="sh-text-11-line"><?php echo $sl . '. ' . html_escape($test_value->test_name) . ' (' . html_escape($test_value->short_name) . ')'; ?></div>
                                <?php } } ?>
                            </td>
                            <td class="align-top">
                                <?php $slradiology = 0; foreach ($result->tests as $test_key => $test_value) {
                                    if ($test_value->test_name == '') { $slradiology++; ?>
                                <div class="sh-text-11-line"><?php echo $slradiology . '. ' . html_escape($test_value->radio_test_name) . ' (' . html_escape($test_value->radio_short_name) . ')'; ?></div>
                                <?php } } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php } ?>

                <!-- Footer note -->
                <?php if (!empty($result->footer_note)) { ?>
                <div style="margin-top:12px; font-size:11.5px; color:#111;"><?php echo $result->footer_note; ?></div>
                <?php } ?>

            </div>
            </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr><td>
            <?php if (!empty($print_details[0]['print_footer'])) { ?>
                <div class="footer-space">&nbsp;</div>
            <?php } ?>
        </td></tr>
    </tfoot>
</table>

<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

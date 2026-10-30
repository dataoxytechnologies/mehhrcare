<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');
?>

<div class="fixed-print-header">
    <?php if (!empty($print_details['print_header'])) { ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>"
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

                <div class="sh-print-title"><?php echo $this->lang->line('prescription'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('prescription'); ?> #</th>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_prescription') . $result->prescription_id; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo (!empty($result->pres_created_at) ? $this->customlib->YYYYMMDDHisTodateFormat($result->pres_created_at) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <td><?php echo composePatientName($result->patient_name, $result->id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('blood_group'); ?></th>
                                <td><?php echo ($result->blood_group_name ?: '-'); ?></td>
                            </tr>
                            <?php if ($result->attachment != '' && $print != 'yes') { ?>
                            <tr>
                                <th><?php echo $this->lang->line('document'); ?></th>
                                <td><a href="<?php echo site_url('patient/prescription/downloadprescription/' . $result->prescription_id); ?>" class="btn btn-sm btn-light" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></td>
                            </tr>
                            <?php } ?>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('phone'); ?></th>
                                <td><?php echo ($result->mobileno ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('email'); ?></th>
                                <td><?php echo ($result->email ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                <td><?php echo composeStaffNameByString($result->name, $result->surname, $result->employee_id); ?></td>
                            </tr>
                            <?php if (!empty($fields_prescription)) {
                                foreach ($fields_prescription as $fields_key => $fields_value) { ?>
                            <tr>
                                <th><?php echo html_escape($fields_value->name); ?></th>
                                <td><?php echo html_escape($result->{$fields_value->name} ?? '-'); ?></td>
                            </tr>
                            <?php } } ?>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <?php if ($result->symptoms != '' || trim($result->finding_description) != '') {
                    $width = ($result->is_finding_print == 'yes' && trim($result->finding_description) != '') ? '50%' : '100%';
                ?>
                <div style="display:flex; gap:18px; margin-bottom:12px;">
                    <?php if ($result->symptoms != '') { ?>
                    <div style="width:<?php echo $width; ?>;">
                        <div class="sh-label-10-bold"><?php echo $this->lang->line('symptoms'); ?></div>
                        <div class="sh-fs-11"><?php echo nl2br($result->symptoms); ?></div>
                    </div>
                    <?php } ?>
                    <?php if ($result->is_finding_print == 'yes' && trim($result->finding_description) != '') { ?>
                    <div style="width:<?php echo $width; ?>;">
                        <div class="sh-label-10-bold"><?php echo $this->lang->line('finding'); ?></div>
                        <div class="sh-fs-11"><?php echo nl2br($result->finding_description); ?></div>
                    </div>
                    <?php } ?>
                </div>
                <div class="sh-divider-light"></div>
                <?php } ?>

                <?php if (!empty($result->header_note)) { ?>
                <div class="sh-mb-12-11"><?php echo $result->header_note; ?></div>
                <div class="sh-divider-light"></div>
                <?php } ?>

                <?php if (!empty($result->medicines)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('medicines'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:3%">#</th>
                            <th style="width:14%"><?php echo $this->lang->line('medicine_category'); ?></th>
                            <th style="width:13%"><?php echo $this->lang->line('medicine'); ?></th>
                            <th style="width:13%"><?php echo $this->lang->line('dosage'); ?></th>
                            <th style="width:14%"><?php echo $this->lang->line('dose_interval'); ?></th>
                            <th style="width:14%"><?php echo $this->lang->line('dose_duration'); ?></th>
                            <th><?php echo $this->lang->line('instruction'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $medsl = 0; foreach ($result->medicines as $pkey => $pvalue) { $medsl++; ?>
                        <tr>
                            <td><?php echo $medsl; ?></td>
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
                <?php } ?>

                <?php if (!empty($result->tests)) {
                    $r = $p = 0;
                    foreach ($result->tests as $test_value) {
                        if ($test_value->test_name != '') $p = 1;
                    }
                    foreach ($result->tests as $test_value) {
                        if ($test_value->test_name == '') $r = 1;
                    }
                ?>
                <div style="display:flex; gap:18px; margin-top:12px;">
                    <?php if ($p == 1) { ?>
                    <div class="w-50">
                        <div class="sh-print-section-title"><?php echo $this->lang->line('pathology_test'); ?></div>
                        <?php $sl = 0; foreach ($result->tests as $test_value) {
                            if ($test_value->test_name != '') { $sl++; ?>
                        <div style="font-size:11px; padding:2px 0;"><?php echo $sl . '. ' . $test_value->test_name . ' (' . $test_value->short_name . ')'; ?></div>
                        <?php } } ?>
                    </div>
                    <?php } ?>
                    <?php if ($r == 1) { ?>
                    <div class="w-50">
                        <div class="sh-print-section-title"><?php echo $this->lang->line('radiology_test'); ?></div>
                        <?php $slradiology = 0; foreach ($result->tests as $test_value) {
                            if ($test_value->test_name == '' && $test_value->radio_test_name != '') { $slradiology++; ?>
                        <div style="font-size:11px; padding:2px 0;"><?php echo $slradiology . '. ' . $test_value->radio_test_name . ' (' . $test_value->radio_short_name . ')'; ?></div>
                        <?php } } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if (!empty($result->footer_note)) { ?>
                <div class="sh-divider-light-12"></div>
                <div class="sh-fs-11"><?php echo $result->footer_note; ?></div>
                <?php } ?>

            </div>
            </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr><td>
            <?php if (!empty($print_details['print_footer'])) { ?>
                <div class="footer-space">&nbsp;</div>
            <?php } ?>
        </td></tr>
    </tfoot>
</table>

<?php if (!empty($print_details['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details['print_footer']; ?>
</div>
<?php } ?>

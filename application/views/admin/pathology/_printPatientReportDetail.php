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
                <div class="sh-print-title"><?php echo $this->lang->line('pathology_report'); ?></div>

                <!-- ② Patient / report info block -->
                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') . ($result->bill_no ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo (($result->patient_name || $result->patient_id) ? composePatientName($result->patient_name, $result->patient_id) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($result->case_reference_id ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($this->customlib->get_patient_current_age($result->patient_id) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo isset($result->gender) ? $this->lang->line(strtolower($result->gender)) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                <td><?php echo ($result->doctor_name ?: '-'); ?></td>
                            </tr>
                        </table>
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:40%"><col style="width:60%"></colgroup>
                            <?php if ($result->parameter_update) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('approve_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if ($result->collection_date) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('sample_collection_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?></td>
                            </tr>
                            <?php } ?>
                            <?php if ($result->reporting_date) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('expected_date'); ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th><?php echo $this->lang->line('collection_by'); ?></th>
                                <td><?php echo composeStaffNameByString($result->collection_specialist_staff_name, $result->collection_specialist_staff_surname, $result->collection_specialist_staff_employee_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('approved_by'); ?></th>
                                <td><?php echo composeStaffNameByString($result->approved_by_staff_name, $result->approved_by_staff_surname, $result->approved_by_staff_employee_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('pathology_center'); ?></th>
                                <td><?php echo ($result->pathology_center ?: '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Test name as section title -->
                <div class="sh-print-section-title"><?php echo html_escape($result->test_name) . ' (' . html_escape($result->short_name) . ')'; ?></div>

                <!-- ④ Parameters table -->
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('test_parameter_name'); ?></th>
                            <th style="width:25%"><?php echo $this->lang->line('reference_range'); ?></th>
                            <th style="width:20%"><?php echo $this->lang->line('report_value'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_counter = 1;
                        foreach ($result->pathology_parameter as $parameter_key => $parameter_value) {
                            $level_exceeded = check_report_level_exceed($parameter_value->reference_range, $parameter_value->range_from, $parameter_value->range_to, $parameter_value->pathology_report_value);
                        ?>
                        <tr <?php if ($level_exceeded) echo 'style="font-weight:700;"'; ?>>
                            <td><?php echo $row_counter; ?></td>
                            <td>
                                <?php echo $parameter_value->parameter_name; ?>
                                <?php if ($parameter_value->description != '') { ?>
                                <div class="sh-caption-xs"><?php echo $this->lang->line('description') . ': ' . $parameter_value->description; ?></div>
                                <?php } ?>
                            </td>
                            <td><?php echo $parameter_value->reference_range . ' ' . $parameter_value->unit_name; ?></td>
                            <td><?php echo ($level_exceeded)
                                ? "<span style='color:#dc2626;'>" . $parameter_value->pathology_report_value . ' ' . $parameter_value->unit_name . "</span>"
                                : (($parameter_value->pathology_report_value == '') ? '' : $parameter_value->pathology_report_value . ' ' . $parameter_value->unit_name);
                            ?></td>
                        </tr>
                        <?php $row_counter++; } ?>
                    </tbody>
                </table>

                <!-- ⑤ Result text -->
                <?php if ($result->pathology_result != '') { ?>
                <div class="sh-note-box">
                    <span class="fw-semibold"><?php echo $this->lang->line('result'); ?>: </span><?php echo nl2br(html_escape($result->pathology_result)); ?>
                </div>
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

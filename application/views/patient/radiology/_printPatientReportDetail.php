<?php include(APPPATH . 'views/admin/shared/_print_css.php'); ?>

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

                <div class="sh-print-title"><?php echo $this->lang->line('radiology_lab_investigation'); ?></div>

                <div class="sh-print-info-block">
                    <div class="sh-flex-gap18">
                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <td><?php echo $bill_prefix . $result->radiology_bill_id; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('patient'); ?></th>
                                <td><?php echo composePatientName($result->patient_name, $result->patient_id); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <td><?php echo ($result->case_reference_id ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('age'); ?></th>
                                <td><?php echo ($this->customlib->getPatientAge($result->age, $result->month, $result->day) ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <td><?php echo ($result->gender ? $this->lang->line(strtolower($result->gender)) : '-'); ?></td>
                            </tr>
                        </table>

                        <table class="sh-print-info-table w-50" >
                            <colgroup><col style="width:42%"><col style="width:58%"></colgroup>
                            <tr>
                                <th><?php echo $this->lang->line('date'); ?></th>
                                <td><?php echo ($result->created_at ? $this->customlib->YYYYMMDDTodateFormat($result->created_at) : '-'); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('collection_date'); ?></th>
                                <td><?php echo ($result->collection_date ? $this->customlib->YYYYMMDDTodateFormat($result->collection_date) : '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sh-section-divider"></div>

                <div class="sh-print-section-title">
                    <?php echo $result->test_name; ?>
                    <?php if (!empty($result->short_name)) { ?>
                        <span class="sh-btn-normal">(<?php echo html_escape($result->short_name); ?>)</span>
                    <?php } ?>
                </div>

                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th style="width:4%">#</th>
                            <th><?php echo $this->lang->line('test_parameter_name'); ?></th>
                            <th style="width:28%"><?php echo $this->lang->line('reference_range'); ?></th>
                            <th style="width:22%"><?php echo $this->lang->line('report_value'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_counter = 1;
                        foreach ($result->radiology_parameter as $parameter_key => $parameter_value) {
                            $level_exceeded = check_report_level_exceed($parameter_value->reference_range, $parameter_value->range_from, $parameter_value->range_to, $parameter_value->radiology_report_value);
                            $row_cls = '';
                            $str = explode('-', $parameter_value->reference_range);
                            $min_range = $str[0];
                            $max_range = isset($str[1]) ? $str[1] : null;
                            if (is_numeric($parameter_value->radiology_report_value) && ($parameter_value->radiology_report_value < $min_range || $parameter_value->radiology_report_value > $max_range)) {
                                $row_cls = 'bold';
                            }
                        ?>
                        <tr class="<?php echo $row_cls; ?>">
                            <td><?php echo $row_counter; ?></td>
                            <td>
                                <?php echo $parameter_value->parameter_name; ?>
                                <?php if ($parameter_value->description != '') { ?>
                                <div class="sh-caption-sm">
                                    <label class="fw-semibold"><?php echo $this->lang->line('description'); ?>:</label>
                                    <?php echo $parameter_value->description; ?>
                                </div>
                                <?php } ?>
                            </td>
                            <td><?php echo $parameter_value->reference_range . ' ' . $parameter_value->unit_name; ?></td>
                            <td>
                                <?php echo $level_exceeded
                                    ? '<span class="sh-text-danger-bold">' . $parameter_value->radiology_report_value . ' ' . $parameter_value->unit_name . '</span>'
                                    : ($parameter_value->radiology_report_value != '' ? $parameter_value->radiology_report_value . ' ' . $parameter_value->unit_name : ''); ?>
                            </td>
                        </tr>
                        <?php $row_counter++; } ?>

                        <?php if ($result->radiology_result != '') { ?>
                        <tr>
                            <td colspan="4">
                                <strong><?php echo $this->lang->line('result'); ?>:</strong>
                                <?php echo nl2br($result->radiology_result); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

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

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
include(APPPATH . 'views/admin/shared/_print_css.php');
if ($otdetails->opd_details_id != '') {
    $patient_name = $otdetails->opd_patient_name;
    $patient_id   = $otdetails->opd_patient_id;
    $case_id      = $otdetails->opd_case_id;
} else {
    $patient_name = $otdetails->ipd_patient_name;
    $patient_id   = $otdetails->ipd_patient_id;
    $case_id      = $otdetails->ipd_case_id;
}
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
                <div class="sh-print-title"><?php echo $this->lang->line('ot_details'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:16%"><col style="width:18%">
                            <col style="width:16%"><col class="w-50">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('patient'); ?></th>
                            <td><?php echo (($patient_name || $patient_id) ? composePatientName($patient_name, $patient_id) : '-'); ?></td>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <td><?php echo ($case_id ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ OT detail fields -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('operation_details'); ?></div>
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('reference_no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('operation_theater_reference_no') . ($otdetails->id ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('operation_name'); ?></th>
                            <td><?php echo ($otdetails->operation ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php echo ($otdetails->date ? $this->customlib->YYYYMMDDHisTodateFormat($otdetails->date, $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                            <th><?php echo $this->lang->line('operation_category'); ?></th>
                            <td><?php echo ($otdetails->category ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo composeStaffNameByString($otdetails->name, $otdetails->surname, $otdetails->employee_id); ?></td>
                            <th><?php echo $this->lang->line('assistant_consultant') . ' 1'; ?></th>
                            <td><?php echo ($otdetails->ass_consultant_1 ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('assistant_consultant') . ' 2'; ?></th>
                            <td><?php echo ($otdetails->ass_consultant_2 ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('anesthetist'); ?></th>
                            <td><?php echo ($otdetails->anesthetist ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('anaethesia_type'); ?></th>
                            <td><?php echo ($otdetails->anaethesia_type ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('ot_technician'); ?></th>
                            <td><?php echo ($otdetails->ot_technician ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('ot_assistant'); ?></th>
                            <td><?php echo ($otdetails->ot_assistant ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('remark'); ?></th>
                            <td><?php echo ($otdetails->remark ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('result'); ?></th>
                            <td colspan="3"><?php echo ($otdetails->result ?: '-'); ?></td>
                        </tr>
                        <?php if (!empty($fields)) {
                            foreach ($fields as $fields_key => $fields_value) {
                                $display_field = $otdetails->{$fields_value->name};
                                if ($fields_value->type == 'link') {
                                    $display_field = "<a href='" . $otdetails->{$fields_value->name} . "' target='_blank'>" . $otdetails->{$fields_value->name} . "</a>";
                                }
                        ?>
                        <tr>
                            <th><?php echo $fields_value->name; ?></th>
                            <td colspan="3"><?php echo $display_field; ?></td>
                        </tr>
                        <?php } } ?>
                    </table>
                </div>


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

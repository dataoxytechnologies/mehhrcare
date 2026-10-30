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
                <div class="sh-print-title"><?php echo $this->lang->line('obstetric_history'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <td><?php echo ($result['patient_name'] ?: '-') . ($result['patient_unique_id'] ? ' (' . $result['patient_unique_id'] . ')' : ''); ?></td>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->getPatientAge($result['age'], $result['month'], $result['day']) ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo ($result['gender'] ? $this->lang->line(strtolower($result['gender'])) : '-'); ?></td>
                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                            <td><?php echo ($result['blood_group'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('phone'); ?></th>
                            <td><?php echo ($result['mobileno'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('email'); ?></th>
                            <td><?php echo ($result['email'] ?: '-'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Obstetric history details -->
                <div class="sh-print-section-title"><?php echo $this->lang->line('obstetric_history'); ?></div>
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:20%"><col style="width:30%">
                            <col style="width:20%"><col style="width:30%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('place_of_delivery'); ?></th>
                            <td><?php echo ($result['place_of_delivery'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('duration_of_pregnancy'); ?></th>
                            <td><?php echo ($result['pregnancy_duration'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                            <td><?php echo ($result['pregnancy_complications'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('birth_weight'); ?></th>
                            <td><?php echo ($result['birth_weight'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo ($result['obstetric_gender'] ? $this->lang->line(strtolower($result['obstetric_gender'])) : '-'); ?></td>
                            <th><?php echo $this->lang->line('infant_feeding'); ?></th>
                            <td><?php echo ($result['infant_feeding'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('birth_status'); ?></th>
                            <td><?php echo ($result['alive_dead'] ? $this->lang->line(strtolower($result['alive_dead'])) : '-'); ?></td>
                            <?php if ($result['alive_dead'] == 'dead') { ?>
                            <th><?php echo $this->lang->line('alive') . ' / ' . $this->lang->line('dead') . ' ' . $this->lang->line('date'); ?></th>
                            <td><?php echo ($result['date'] ? $this->customlib->YYYYMMDDTodateFormat($result['date']) : '-'); ?></td>
                            <?php } else { ?>
                            <td colspan="2"></td>
                            <?php } ?>
                        </tr>
                        <?php if ($result['alive_dead'] == 'dead') { ?>
                        <tr>
                            <th><?php echo $this->lang->line('death_cause'); ?></th>
                            <td colspan="3"><?php echo ($result['death_cause'] ?: '-'); ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th><?php echo $this->lang->line('previous_medical_history'); ?></th>
                            <td colspan="3"><?php echo ($result['previous_medical_history'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('special_instruction'); ?></th>
                            <td colspan="3"><?php echo ($result['special_instruction'] ?: '-'); ?></td>
                        </tr>
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

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
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
                <div class="sh-print-title"><?php echo $this->lang->line('discharge_card'); ?></div>

                <!-- ② Patient info block -->
                <div class="sh-print-info-block">
                    <table class="sh-print-info-table">
                        <colgroup>
                            <col style="width:16%"><col style="width:18%">
                            <col style="width:16%"><col style="width:16%">
                            <col style="width:16%"><col style="width:18%">
                        </colgroup>
                        <tr>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <td><?php echo ($case_id ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                            <td><?php echo ($result['appointment_date'] != '' && $result['appointment_date'] != '0000-00-00' ? $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']) : '-'); ?></td>
                            <th></th><td></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <td><?php echo ($result['patient_name'] ? composePatientName($result['patient_name'], $result['patient_id']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('guardian_name'); ?></th>
                            <td><?php echo ($result['guardian_name'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('phone'); ?></th>
                            <td><?php echo ($result['mobileno'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo isset($result['gender']) ? $this->lang->line(strtolower($result['gender'])) : '-'; ?></td>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->get_patient_current_age($result['patient_id']) ?: '-'); ?></td>
                            <?php if ($result['opdid'] != '' && $result['opdid'] != 0) { ?>
                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $result['opdid']; ?></td>
                            <?php } elseif ($result['ipdid'] != '' && $result['ipdid'] != 0) { ?>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('ipd_no') . $result['ipdid']; ?></td>
                            <?php } else { ?>
                            <th></th><td></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                            <td><?php echo ($result['date'] != '' && $result['date'] != '0000-00-00' ? $this->customlib->YYYYMMDDHisTodateFormat($result['date']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('discharge_date'); ?></th>
                            <td><?php echo (!empty($discharge_card) && $discharge_card['discharge_date'] != '' ? $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['discharge_date']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('discharge_status'); ?></th>
                            <td><?php echo (!empty($discharge_card) ? $this->customlib->discharge_status($discharge_card['discharge_status']) : '-'); ?></td>
                        </tr>

                        <?php if (!empty($discharge_card) && $discharge_card['discharge_status'] == 1) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('death_date'); ?></th>
                            <td><?php echo ($discharge_card['death_date'] != '' ? $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['death_date']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('death_report'); ?></th>
                            <td colspan="3"><?php echo (!empty($deathrecord) ? html_escape($deathrecord['death_report']) : '-'); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if (!empty($discharge_card) && $discharge_card['discharge_status'] == 2) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('referral_date'); ?></th>
                            <td><?php echo ($discharge_card['refer_date'] != '' ? $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['refer_date']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('referral_hospital_name'); ?></th>
                            <td><?php echo ($discharge_card['refer_to_hospital'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('reason_for_referral'); ?></th>
                            <td><?php echo ($discharge_card['reason_for_referral'] ?: '-'); ?></td>
                        </tr>
                        <?php } ?>

                        <?php if (!empty($discharge_card['field_data'])) {
                            foreach ($discharge_card['field_data'] as $key => $value) { ?>
                        <tr>
                            <th><?php echo html_escape($value['name']); ?></th>
                            <td colspan="5"><?php echo html_escape($value['field_value'] ?: '-'); ?></td>
                        </tr>
                        <?php } } ?>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <!-- ③ Clinical summary -->
                <?php
                $has_clinical = !empty($discharge_card) && (
                    !empty($discharge_card['operation']) ||
                    !empty($discharge_card['diagnosis']) ||
                    !empty($discharge_card['investigations']) ||
                    !empty($discharge_card['treatment_home'])
                );
                if ($has_clinical) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('diagnosis'); ?> / <?php echo $this->lang->line('investigation'); ?></div>
                <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                    <?php if (!empty($discharge_card['operation'])) { ?>
                    <div class="sh-flex-col-45">
                        <div class="sh-caption-bold"><?php echo $this->lang->line('operation'); ?></div>
                        <div class="sh-value-cell"><?php echo nl2br(html_escape($discharge_card['operation'])); ?></div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($discharge_card['diagnosis'])) { ?>
                    <div class="sh-flex-col-45">
                        <div class="sh-caption-bold"><?php echo $this->lang->line('diagnosis'); ?></div>
                        <div class="sh-value-cell"><?php echo nl2br(html_escape($discharge_card['diagnosis'])); ?></div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($discharge_card['investigations'])) { ?>
                    <div class="sh-flex-col-45">
                        <div class="sh-caption-bold"><?php echo $this->lang->line('investigation'); ?></div>
                        <div class="sh-value-cell"><?php echo nl2br(html_escape($discharge_card['investigations'])); ?></div>
                    </div>
                    <?php } ?>
                    <?php if (!empty($discharge_card['treatment_home'])) { ?>
                    <div class="sh-flex-col-45">
                        <div class="sh-caption-bold"><?php echo $this->lang->line('treatment_at_home'); ?></div>
                        <div class="sh-value-cell"><?php echo nl2br(html_escape($discharge_card['treatment_home'])); ?></div>
                    </div>
                    <?php } ?>
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

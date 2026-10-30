<?php
$print_date = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('visit_details'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
    <style>
        /* Scoped: this report's header is a single title/subtitle line, so the
           shared 100px header/spacer (sh-print.css) leaves ~70px dead space.
           Override only for this view — bill prints still need the full height. */
        @media print {
            .fixed-print-header, .header-space { height: 36px; }
        }
        .fixed-print-header .report-title { display: block; line-height: 1.2; }
        .fixed-print-header .report-subtitle { display: block; line-height: 1.2; }
    </style>
</head>
<body>

<div class="fixed-print-header">
    <span class="report-title"><?php echo $this->lang->line('opd_visit_details'); ?></span>
    <span class="report-subtitle"><?php echo $this->lang->line('print'); ?>: <?php echo $print_date; ?></span>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
        <div class="content-body" style="padding-top: 10px;">

            <!-- Reference line -->
            <table width="100%" style="margin-bottom: 8px;">
                <tr>
                    <td><?php echo $this->lang->line('case_id'); ?>: <strong><?php echo html_escape($result['case_reference_id']); ?></strong>
                    &nbsp;&nbsp; <?php echo $this->lang->line('recheckup_id'); ?>: <strong><?php echo html_escape($this->customlib->getSessionPrefixByType('checkup_id') . $result['id']); ?></strong></td>
                    <td class="text-end"><?php echo $this->lang->line('opd_no'); ?>: <strong><?php echo html_escape($this->customlib->getSessionPrefixByType('opd_no') . $result['opd_details_id']); ?></strong></td>
                </tr>
            </table>

            <div style="border-bottom: 1px solid #ccc; margin-bottom: 8px;"></div>

            <!-- Patient & visit info -->
            <table class="info-table">
                <tbody>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('patient_name'); ?></td>
                        <td><?php echo html_escape(composePatientName($result['patient_name'], $result['patient_id'])); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('old_patient'); ?></td>
                        <td><?php echo html_escape($this->lang->line($result['patient_old'])); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('guardian_name'); ?></td>
                        <td><?php echo html_escape($result['guardian_name']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('gender'); ?></td>
                        <td><?php echo isset($result['gender']) ? html_escape($this->lang->line(strtolower($result['gender']))) : ''; ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('marital_status'); ?></td>
                        <td><?php echo isset($result['marital_status']) ? html_escape($this->lang->line(strtolower($result['marital_status']))) : ''; ?></td>
                        <td class="lbl"><?php echo $this->lang->line('phone'); ?></td>
                        <td><?php echo html_escape($result['mobileno']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('email'); ?></td>
                        <td><?php echo html_escape($result['email']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('address'); ?></td>
                        <td><?php echo html_escape($result['address']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('age'); ?></td>
                        <td><?php echo $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('blood_group'); ?></td>
                        <td><?php echo html_escape($result['blood_group_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('known_allergies'); ?></td>
                        <td><?php echo html_escape($result['known_allergies']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('appointment_date'); ?></td>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('case'); ?></td>
                        <td><?php echo html_escape($result['case_type']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('casualty'); ?></td>
                        <td><?php echo html_escape($this->lang->line($result['casualty'])); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('reference'); ?></td>
                        <td><?php echo html_escape($result['refference']); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('tpa'); ?></td>
                        <td><?php echo html_escape($result['organisation_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('consultant_doctor'); ?></td>
                        <td><?php echo html_escape(composeStaffNameByString($result['name'], $result['surname'], $result['employee_id'])); ?></td>
                        <td class="lbl"><?php echo $this->lang->line('note'); ?></td>
                        <td><?php echo html_escape($result['note']); ?></td>
                    </tr>
                    <?php if (!empty($result['symptoms'])) : ?>
                    <tr>
                        <td class="lbl"><?php echo $this->lang->line('symptoms'); ?></td>
                        <td colspan="3"><?php echo nl2br(html_escape($result['symptoms'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($fields)) : foreach ($fields as $field) :
                        $display_field = $field->type == 'link'
                            ? '<a href="' . html_escape($result[$field->name]) . '" target="_blank">' . html_escape($result[$field->name]) . '</a>'
                            : html_escape($result[$field->name]);
                    ?>
                    <tr>
                        <td class="lbl"><?php echo html_escape($field->name); ?></td>
                        <td colspan="3"><?php echo $display_field; ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <div class="divider mt-10"></div>

        </div>
        </td></tr>
    </tbody>
</table>

</body>
</html>

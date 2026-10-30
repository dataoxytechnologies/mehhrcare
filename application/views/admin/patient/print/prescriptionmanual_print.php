<?php
$print_date = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('prescription'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
    <?php include(APPPATH . 'views/admin/shared/_print_css.php'); ?>
</head>
<body>

<div class="fixed-print-header">
    <?php if (!empty($print_details['print_header'])) : ?>
        <img src="<?php echo $this->media_storage->getImageURL($print_details['print_header']); ?>" style="height:100px; width:100%; object-fit:cover;" class="img-fluid">
    <?php else : ?>
        <div style="padding: 8px 0;"><span style="font-size: 16px; font-weight: bold;"><?php echo $this->lang->line('prescription'); ?></span></div>
    <?php endif; ?>
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

            <!-- ② Patient / visit info block -->
            <div class="sh-print-info-block">
                <table class="sh-print-info-table">
                    <colgroup>
                        <col style="width:16%"><col style="width:18%">
                        <col style="width:16%"><col style="width:16%">
                        <col style="width:16%"><col style="width:18%">
                    </colgroup>
                    <tr>
                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                        <td><?php echo ($result['opd_details_id'] ? $opd_prefix . $result['opd_details_id'] : '-'); ?></td>
                        <th><?php echo $this->lang->line('checkup_id'); ?></th>
                        <td><?php echo ($visitid ? $this->customlib->getSessionPrefixByType('checkup_id') . $visitid : '-'); ?></td>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, true)); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                        <td><?php echo ($result['patient_name'] ? $result['patient_name'] . ' (' . $result['patientid'] . ')' : '-'); ?></td>
                        <th><?php echo $this->lang->line('age'); ?></th>
                        <td><?php echo ($this->customlib->get_patient_current_age($result['patientid']) ?: '-'); ?></td>
                        <th><?php echo $this->lang->line('gender'); ?></th>
                        <td><?php echo ($result['gender'] ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                        <td><?php echo ($blood_group_name ?: '-'); ?></td>
                        <th><?php echo $this->lang->line('address'); ?></th>
                        <td><?php echo ($result['address'] ?: '-'); ?></td>
                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                        <td><?php echo ($result['name'] ? $result['name'] . ' ' . $result['surname'] . ' (' . $result['employee_id'] . ')' : '-'); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('known_allergies'); ?></th>
                        <td colspan="5"><?php echo ($result['known_allergies'] ?: '-'); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Divider for clarity -->
            <div class="sh-section-divider"></div>

            <!-- Vitals -->
            <?php if (!empty($result['bp']) || !empty($result['height']) || !empty($result['weight']) || !empty($result['pulse']) || !empty($result['temperature']) || !empty($result['respiration'])) : ?>
            <div class="sh-print-section-title"><?php echo $this->lang->line('vitals'); ?></div>
            <table class="sh-print-table">
                <thead>
                    <tr>
                        <th class="sh-col-16 text-start"><?php echo $this->lang->line('bp'); ?></th>
                        <th class="sh-col-16 text-start"><?php echo $this->lang->line('height'); ?></th>
                        <th class="sh-col-16 text-start"><?php echo $this->lang->line('weight'); ?></th>
                        <th class="sh-col-16 text-start"><?php echo $this->lang->line('pulse'); ?></th>
                        <th class="sh-col-16 text-start"><?php echo $this->lang->line('temperature'); ?></th>
                        <th class="sh-col-20 text-start"><?php echo $this->lang->line('respiration'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold"><?php echo ($result['bp'] ?: '-'); ?></td>
                        <td class="fw-bold"><?php echo ($result['height'] ?: '-'); ?></td>
                        <td class="fw-bold"><?php echo ($result['weight'] ?: '-'); ?></td>
                        <td class="fw-bold"><?php echo ($result['pulse'] ?: '-'); ?></td>
                        <td class="fw-bold"><?php echo ($result['temperature'] ?: '-'); ?></td>
                        <td class="fw-bold"><?php echo ($result['respiration'] ?: '-'); ?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- The rest of the page is left blank for the doctor to write manually -->

        </div>
        </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr><td>
            <?php if (!empty($print_details['print_footer'])) : ?>
            <div class="footer-space">&nbsp;</div>
            <?php endif; ?>
        </td></tr>
    </tfoot>
</table>

<?php if (!empty($print_details['print_footer'])) : ?>
<div class="footer-fixed">
    <?php echo $print_details['print_footer']; ?>
</div>
<?php endif; ?>

</body>
</html>

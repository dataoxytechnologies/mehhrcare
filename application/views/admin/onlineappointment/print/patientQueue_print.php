<?php
$print_date = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('patient_queue'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>
<body>

<div class="fixed-print-header">
    <span class="report-title"><?php echo $this->lang->line('patient_queue'); ?></span>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
        <div class="content-body">

            <table class="info-table">
                <tr>
                    <td class="lbl"><?php echo $this->lang->line('doctor_name'); ?></td>
                    <td><?php echo html_escape($doctor_name); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('date'); ?></td>
                    <td><?php echo html_escape($date_display); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('print'); ?></td>
                    <td><?php echo $print_date; ?></td>
                </tr>
            </table>

            <?php if (!empty($resultlist)) : ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('appointment_s_no'); ?></th>
                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                        <th><?php echo $this->lang->line('phone'); ?></th>
                        <th><?php echo $this->lang->line('email'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('time'); ?></th>
                        <th><?php echo $this->lang->line('source'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($resultlist as $r) : ?>
                    <tr>
                        <td><?php echo $r['position']; ?></td>
                        <td><?php echo html_escape($r['patient_name']) . ' (' . html_escape($r['patient_unique_id']) . ')'; ?></td>
                        <td><?php echo html_escape($r['mobileno']); ?></td>
                        <td><?php echo html_escape($r['email']); ?></td>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($r['date'])); ?></td>
                        <td><?php echo $r['date'] ? date('h:i A', strtotime($r['date'])) : ''; ?></td>
                        <td <?php if ($r['source'] == 'Online') echo 'class="source-online"'; ?>>
                            <?php echo $this->lang->line(strtolower($r['source'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
                <p class="no-data text-center"><?php echo $this->lang->line('no_record_found'); ?></p>
            <?php endif; ?>

            <div class="divider mt-10"></div>

        </div>
        </td></tr>
    </tbody>
</table>

</body>
</html>

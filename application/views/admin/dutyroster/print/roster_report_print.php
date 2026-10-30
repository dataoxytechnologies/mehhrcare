<?php
$print_date = date($this->customlib->getHospitalDateFormat(true, false));
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('roster_report'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
    <style>
        /* Per-report override: the global .fixed-print-header/.header-space height
           (100px in sh-print.css) leaves a large empty gap above the table because
           this report's running header is a single title line. Tighten it here. */
        .fixed-print-header { display: flex; align-items: center; justify-content: center; }
        @media print {
            .fixed-print-header,
            .header-space { height: 36px; }
            .fixed-print-header { border-bottom: 1px solid #999; }
            .info-table { margin-bottom: 8px; }
        }
    </style>
</head>
<body>

<div class="fixed-print-header">
    <span class="report-title"><?php echo $this->lang->line('duty_roster'); ?></span>
</div>

<table class="table-print-full" width="100%">
    <thead>
        <tr><td><div class="header-space">&nbsp;</div></td></tr>
    </thead>
    <tbody>
        <tr><td>
        <div class="content-body">

            <!-- Info bar -->
            <table class="info-table">
                <tr>
                    <td class="lbl"><?php echo $this->lang->line('staff'); ?></td>
                    <td><?php echo !empty($staff_name) ? html_escape($staff_name) : $this->lang->line('all'); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('search_type'); ?></td>
                    <td><?php echo html_escape($search_type_label); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('date_from'); ?></td>
                    <td><?php echo html_escape($start_date_display); ?></td>
                    <td class="lbl"><?php echo $this->lang->line('date_to'); ?></td>
                    <td><?php echo html_escape($end_date_display); ?></td>
                </tr>
            </table>

            <!-- Roster table -->
            <?php if (!empty($roster_data)) : ?>
            <table class="print-table">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('staff'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('shift_start'); ?></th>
                        <th><?php echo $this->lang->line('shift_end'); ?></th>
                        <th><?php echo $this->lang->line('shift_hour'); ?></th>
                        <th><?php echo $this->lang->line('shift'); ?></th>
                        <th><?php echo $this->lang->line('department'); ?></th>
                        <th><?php echo $this->lang->line('floor'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($roster_data as $staff_rows) :
                    $i = 0;
                    foreach ($staff_rows as $val) :
                        $i++;
                        $duty_date = date('Y-m-d', strtotime($val['roster_duty_date']));
                ?>
                    <tr>
                        <td>
                        <?php if ($i == 1) :
                            echo html_escape($val['staff_name'] . ' ' . $val['staff_surname'] . ' (' . $val['employee_id'] . ')');
                        endif; ?>
                        </td>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($duty_date); ?></td>
                        <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_start']); ?></td>
                        <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_end']); ?></td>
                        <td><?php echo $val['shift_hour']; ?></td>
                        <td><?php echo html_escape($val['shift_name']); ?></td>
                        <td><?php echo html_escape($val['department_name']); ?></td>
                        <td><?php echo html_escape($val['floor_name']); ?></td>
                    </tr>
                <?php   endforeach;
                endforeach; ?>
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

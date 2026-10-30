<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="container-fluid px-1 py-1">

    <div class="card sh-patient-card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('previous_ipd'); ?></h3>
        </div>
        <div class="card-body p-3">

            <?php if (isset($resultlist)) { ?>
                <div class="download_label"><?php echo $this->lang->line('previous_ipd'); ?></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                <th><?php echo $this->lang->line('patient_id'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('phone'); ?></th>
                                <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                <th><?php echo $this->lang->line('bed'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('charges') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-end"><?php echo $this->lang->line('payment') . ' (' . $currency_symbol . ')'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($resultlist)) {
                                foreach ($resultlist as $patient) {
                                    $payment      = $patient['payment'];
                                    $credit_limit = $patient['credit_limit'];
                                    $charge       = $patient['charges'];
                                    $bill         = $patient['charges'] - $patient['payment'];
                                    $row_class    = ($bill >= $credit_limit) ? 'table-danger' : '';
                                    ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td>
                                            <a href="<?php echo base_url(); ?>patient/dashboard/ipdprofile/<?php echo (int) $patient['id']; ?>">
                                                <?php echo html_escape($patient['patient_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo html_escape($this->customlib->getPatientSessionPrefixByType('ipd_no')) . (int) $patient['id']; ?></td>
                                        <td><?php echo html_escape($patient['patient_id']); ?></td>
                                        <td><?php echo $this->lang->line(strtolower($patient['gender'])); ?></td>
                                        <td><?php echo html_escape($patient['mobileno']); ?></td>
                                        <td><?php echo html_escape(composeStaffNameByString($patient['name'], $patient['surname'], $patient['employee_id'])); ?></td>
                                        <td><?php echo html_escape($patient['bed_name']) . ' - ' . html_escape($patient['bedgroup_name']) . ' - ' . html_escape($patient['floor_name']); ?></td>
                                        <td class="text-end"><?php if ($patient['charges']) { echo number_format($patient['charges'], 2, '.', ''); } ?></td>
                                        <td class="text-end"><?php echo amountFormat($patient['payment']); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>

        </div>
    </div>

</div>

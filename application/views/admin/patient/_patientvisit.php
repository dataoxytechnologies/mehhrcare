<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div id="visit_report">

    <h4 class="text-center mb-3"><?= $this->lang->line("patient_visit_report"); ?></h4>

    <?php if (!empty($opd_data)) { ?>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('opd_report'); ?></h5>
            <div class="d-flex gap-2 ms-auto">
                <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" id="print" onclick="printDiv()"><i class="fa fa-print"></i></a>
                <a class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('export_to_excel'); ?>" id="btnExport" onclick="tablesToExcel(array1, array2, array3, array4, array5, array6, array7, 'myfile.xls')"><i class="fa fa-file-excel-o"></i></a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="1">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('checkup_id'); ?></th>
                            <th><?php echo $this->lang->line('doctor_name'); ?></th>
                            <th width="20%"><?php echo $this->lang->line('symptoms'); ?></th>
                            <th width="20%"><?php echo $this->lang->line('findings'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opd_data as $value) {
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php if ($value['appointment_date']) { echo $this->customlib->YYYYMMDDTodateFormat($value['appointment_date']); } ?></td>
                            <td><?php echo $this->customlib->getSessionPrefixByType('checkup_id') . $value['visit_id']; ?></td>
                            <td><?php echo composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']); ?></td>
                            <td><?php echo $value['symptoms']; ?></td>
                            <td><?php echo $value['finding_description']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($ipd_data)) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('ipd_report'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="2">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th width="8%"><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('doctor_name'); ?></th>
                            <th width="20%"><?php echo $this->lang->line('symptoms'); ?></th>
                            <th width="20%"><?php echo $this->lang->line('findings'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ipd_data as $key => $value) {
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?= $this->customlib->getSessionPrefixByType('ipd_no') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php if ($value['date']) { echo $this->customlib->YYYYMMDDTodateFormat($value['date']); } ?></td>
                            <td><?php echo $value['name'] . " " . $value['surname'] . "(" . $value['employee_id'] . ")"; ?></td>
                            <td><?php echo $value['symptoms']; ?></td>
                            <td><?php echo $value['finding_description']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($pharmacy_data)) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('pharmacy_details'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="3">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('refund_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line("balance_amount") . " (" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_discount = 0; $total_discount_percent = 0; $refund_amount = 0; $total_amount = 0; $total_tax = 0;
                        foreach ($pharmacy_data as $value) {
                            $balance_amount = ($value['net_amount'] - ($value['paid_amount'] - $value['refund_amount']));
                            $total_net     += $value['net_amount'];
                            $total_paid    += $value['paid_amount'];
                            $total_balance += $balance_amount;
                            $total_discount += $value['discount'];
                            $total_discount_percent += $value['discount_percentage'];
                            $refund_amount += $value['refund_amount'];
                            $total_amount  += $value['total'];
                            $total_tax     += $value['tax'];
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['total']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['discount']) . " (" . $value['discount_percentage'] . "%)"; ?></td>
                            <td class="text-end"><?php
                                $tax_percentage = amountFormat(($value['tax'] * 100) / ($value['total'] - $value['discount']));
                                echo amountFormat($value['tax']) . " (" . $tax_percentage . "%)";
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo number_format((float)$value['paid_amount'] - $value['refund_amount'], 2, '.', ''); ?></td>
                            <td class="text-end"><?php echo number_format((float)$value['refund_amount'], 2, '.', ''); ?></td>
                            <td class="text-end"><?php echo number_format((float)$balance_amount, 2, '.', ''); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" onclick="viewDetail(<?php echo $value['id']; ?>)" data-module-type="pharmacy" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_discount, 2) . ' (' . number_format($total_discount_percent, 2) . '%)'; ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($refund_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($pathology_data)) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('pathology_details'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="4">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (%)"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (%)"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . "(" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_discount = 0; $total_discount_percent = 0; $total_amount = 0; $total_tax = 0;
                        foreach ($pathology_data as $value) {
                            $balance_amount = $value['net_amount'] - $value['paid_amount'];
                            $total_net     += $value['net_amount'];
                            $total_paid    += $value['paid_amount'];
                            $total_balance += $balance_amount;
                            $total_discount += $value['discount'];
                            $total_discount_percent += $value['discount_percentage'];
                            $total_amount  += $value['total'];
                            $total_tax     += $value['tax'];
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['total']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['discount']) . ' (' . $value['discount_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php
                                $tax_percentage = number_format(($value['tax'] * 100) / ($value['total'] - $value['discount']), 2);
                                echo amountFormat($value['tax']) . ' (' . $tax_percentage . '%)';
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                            <td class="text-end"><?php echo number_format($balance_amount, 2); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm view_detail" data-module-type="pathology" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_discount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($radiology_data)) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('radiology_details'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="5">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . "(" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_discount = 0; $total_discount_percent = 0; $total_amount = 0; $total_tax = 0;
                        foreach ($radiology_data as $value) {
                            $balance_amount = $value['net_amount'] - $value['paid_amount'];
                            $total_net     += $value['net_amount'];
                            $total_paid    += $value['paid_amount'];
                            $total_balance += $balance_amount;
                            $total_discount += $value['discount'];
                            $total_discount_percent += $value['discount_percentage'];
                            $total_amount  += $value['total'];
                            $total_tax     += $value['tax'];
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('radiology_billing') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['total']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['discount']) . ' (' . $value['discount_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php
                                $tax_percentage = number_format(($value['tax'] * 100) / ($value['total'] - $value['discount']), 2);
                                echo amountFormat($value['tax']) . ' (' . $tax_percentage . ')';
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                            <td class="text-end"><?php echo number_format($balance_amount, 2); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm view_detail" data-module-type="radiology" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_discount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($blood_bank_data['blood_issue'])) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('blood_bank_issue_details'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="6">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('issue_date'); ?></th>
                            <th><?php echo $this->lang->line('donor_name'); ?></th>
                            <th><?php echo $this->lang->line('bags'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_discount = 0; $total_discount_percent = 0; $total_standard_charge = 0; $total_tax = 0;
                        foreach ($blood_bank_data['blood_issue'] as $key => $value) {
                            $tax = 0; $discount_amt = 0;
                            $balance_amount = $value['net_amount'] - $value['paid_amount'];
                            $total_net     += $value['net_amount'];
                            $total_paid    += $value['paid_amount'];
                            $total_balance += $balance_amount;
                            $total_discount += calculatePercent($value['net_amount'], $value['discount_percentage']);
                            $total_discount_percent += $value['discount_percentage'];
                            $total_standard_charge  += $value['standard_charge'];
                            $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value['id'];
                        ?>
                        <tr>
                            <td><?php echo $prefix; ?></td>
                            <td><?php echo $value['case_reference_id']; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $value['donor_name']; ?></td>
                            <td><?php echo $this->customlib->bag_string($value['bag_no'], $value['volume'], $value['charge_unit']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>
                            <td class="text-end"><?php
                                $discount_amt = calculatePercent($value['standard_charge'], $value['discount_percentage']);
                                echo amountFormat($discount_amt) . ' (' . $value['discount_percentage'] . '%)';
                            ?></td>
                            <td class="text-end"><?php
                                $tax = (($value['standard_charge'] - $discount_amt) * $value['tax_percentage']) / 100;
                                $total_tax += $tax;
                                echo amountFormat($tax) . ' (' . $value['tax_percentage'] . '%)';
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount'] - $value['paid_amount']); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm view_detail" data-module-type="blood_issue" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="4"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_standard_charge, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_discount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($blood_bank_data['component_issue'])) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('blood_bank_component_details'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="6">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('issue_date'); ?></th>
                            <th><?php echo $this->lang->line('donor_name'); ?></th>
                            <th><?php echo $this->lang->line('component'); ?></th>
                            <th><?php echo $this->lang->line('bags'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_discount = 0; $total_discount_percent = 0; $total_standard_charge = 0; $total_tax = 0;
                        foreach ($blood_bank_data['component_issue'] as $key => $value) {
                            $tax = 0; $discount_amt = 0;
                            $balance_amount = $value['net_amount'] - $value['paid_amount'];
                            $total_net     += $value['net_amount'];
                            $total_paid    += $value['paid_amount'];
                            $total_balance += $balance_amount;
                            $total_discount += calculatePercent($value['net_amount'], $value['discount_percentage']);
                            $total_discount_percent += $value['discount_percentage'];
                            $total_standard_charge  += $value['standard_charge'];
                            $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value['id'];
                        ?>
                        <tr>
                            <td><?php echo $prefix; ?></td>
                            <td><?php echo $value['case_reference_id']; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $value['donor_name']; ?></td>
                            <td><?php echo $value['component_name']; ?></td>
                            <td><?php echo $this->customlib->bag_string($value['bag_no'], $value['volume'], $value['charge_unit']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>
                            <td class="text-end"><?php
                                $discount_amt = calculatePercent($value['standard_charge'], $value['discount_percentage']);
                                echo amountFormat($discount_amt) . ' (' . $value['discount_percentage'] . '%)';
                            ?></td>
                            <td class="text-end"><?php
                                $tax = (($value['standard_charge'] - $discount_amt) * $value['tax_percentage']) / 100;
                                $total_tax += $tax;
                                echo amountFormat($tax) . ' (' . $value['tax_percentage'] . '%)';
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount'] - $value['paid_amount']); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm view_detail" data-module-type="component_issue" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="5"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_standard_charge, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_discount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($ambulance_data)) { ?>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title mb-0 titlefix"><?php echo $this->lang->line('ambulance_report'); ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover allajaxlist mb-0" id="7">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . "(" . $currency_symbol . ")"; ?></th>
                            <th><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_net = 0; $total_paid = 0; $total_balance = 0; $total_amount = 0; $discount_amount = 0; $total_tax = 0;
                        foreach ($ambulance_data as $value) {
                            $discount_amount += $value['discount'];
                            $total_amount    += $value['standard_charge'];
                            $balance_amount   = $value['net_amount'] - $value['paid_amount'];
                            $total_net       += $value['net_amount'];
                            $total_paid      += $value['paid_amount'];
                            $total_balance   += $balance_amount;
                            $case_id = $value['case_reference_id'] > 0 ? $value['case_reference_id'] : '';
                        ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value['id']; ?></td>
                            <td><?php echo $case_id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo $value['vehicle_model']; ?></td>
                            <td class="text-end"><?php echo amountFormat($value['standard_charge']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['discount']) . ' (' . $value['discount_percentage'] . '%)'; ?></td>
                            <td class="text-end"><?php
                                $tax = (($value['standard_charge'] - $value['discount']) * $value['tax_percentage']) / 100;
                                $total_tax += $tax;
                                echo amountFormat($tax) . ' (' . $value['tax_percentage'] . '%)';
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount']); ?></td>
                            <td class="text-end"><?php echo number_format($balance_amount, 2); ?></td>
                            <td><div class="rowoptionview"><a href="javascript:void(0)" data-loading-text=" " data-record-id="<?php echo $value['id']; ?>" class="btn btn-secondary btn-sm" onclick="viewDetailBill('<?php echo $value['id']; ?>')" data-module-type="ambulance" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a></div></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="3"></td>
                            <td><b><?= $this->lang->line("total_amount"); ?>:</b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($discount_amount, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_tax, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_net, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_paid, 2); ?></b></td>
                            <td class="text-end"><b><?php echo $currency_symbol . number_format($total_balance, 2); ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

</div>
<script>
    function viewDetailBill(id) {
        $.ajax({
            url: baseurl + 'admin/patient/getBillDetails/' + id,
            type: "GET",
            data: {id: id},
            success: function(data) {
                $('#reportdata').html(data);
                shModal('viewModalBill').show();
            },
        });
    }
</script>
<script>
    function viewDetail(id) {
        var view_modal = $('#viewModal');
        $.ajax({
            url: baseurl + 'admin/patient/getpharmacybilldetails/',
            type: "GET",
            data: {'id': id},
            dataType: "JSON",
            beforeSend: function() {
                $('#reportdata,#edit_deletebill').html("");
                shModal('viewModal').show();
                view_modal.addClass('modal_loading');
            },
            complete: function() {
                view_modal.removeClass('modal_loading');
            },
            success: function(data) {
                $('#pharmacy_reportdata').html(data.page);
                view_modal.removeClass('modal_loading');
            },
        });
    }
</script>

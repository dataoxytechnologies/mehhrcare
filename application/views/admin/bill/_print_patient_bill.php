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
                <div class="sh-print-title"><?php echo $this->lang->line('patient_bill'); ?></div>

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
                            <td><?php echo ($patient['appointment_date'] != '' && $patient['appointment_date'] != '0000-00-00' ? $this->customlib->YYYYMMDDHisTodateFormat($patient['appointment_date'], $this->customlib->getHospitalTimeFormat()) : '-'); ?></td>
                            <th></th><td></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <td><?php echo ($patient['patient_name'] ? composePatientName($patient['patient_name'], $patient['patient_id']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('guardian_name'); ?></th>
                            <td><?php echo ($patient['guardian_name'] ?: '-'); ?></td>
                            <th></th><td></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('gender'); ?></th>
                            <td><?php echo isset($patient['gender']) ? $this->lang->line(strtolower($patient['gender'])) : '-'; ?></td>
                            <th><?php echo $this->lang->line('age'); ?></th>
                            <td><?php echo ($this->customlib->get_patient_current_age($patient['patient_id']) ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('phone'); ?></th>
                            <td><?php echo ($patient['mobileno'] ?: '-'); ?></td>
                        </tr>
                        <?php if (!empty($patient['organisation_name']) || !empty($patient['insurance_id'])) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('tpa'); ?></th>
                            <td><?php echo ($patient['organisation_name'] ?: '-'); ?></td>
                            <th><?php echo $this->lang->line('tpa_validity'); ?></th>
                            <td><?php echo (isset($patient['insurance_validity']) && $patient['insurance_validity'] ? $this->customlib->YYYYMMDDTodateFormat($patient['insurance_validity']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('tpa_id'); ?></th>
                            <td><?php echo ($patient['insurance_id'] ?: '-'); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($patient['credit_limit'])) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('credit_limit') . ' (' . $currency_symbol . ')'; ?></th>
                            <td colspan="5"><?php echo amountFormat((float)$patient['credit_limit']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($patient['ipdid'] != '' && $patient['ipdid'] != 0) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <td>
                                <?php echo $this->customlib->getSessionPrefixByType('ipd_no') . $patient['ipdid']; ?>
                                <?php if ($patient['discharged'] == 'yes') { ?> <span class="sh-badge-warn"><?php echo $this->lang->line('discharged'); ?></span><?php } ?>
                            </td>
                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                            <td><?php echo ($patient['date'] != '' && $patient['date'] != '0000-00-00' ? $this->customlib->YYYYMMDDTodateFormat($patient['date']) : '-'); ?></td>
                            <th><?php echo $this->lang->line('bed'); ?></th>
                            <td><?php echo html_escape($patient['bed_name'] . ' - ' . $patient['bedgroup_name'] . ' - ' . $patient['floor_name']); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($patient['opdid'] != '' && $patient['opdid'] != 0) { ?>
                        <tr>
                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('opd_no') . $patient['opdid']; ?></td>
                            <th></th><td></td><th></th><td></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>

                <!-- Divider for clarity -->
                <div class="sh-section-divider"></div>

                <?php
                $total_amount = 0;
                $amount_paid  = 0;
                ?>

                <!-- ③ OPD Charges -->
                <?php if (!empty($opd_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('opd_charges'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('service'); ?></th>
                            <th style="width:16%"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th style="width:8%"><?php echo $this->lang->line('qty'); ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opd_data as $opd_key => $opd_value) {
                            $total_amount += (float)$opd_value['amount'];
                            $opd_discount = (float)$opd_value['apply_charge'] * ((float)$opd_value['discount_percentage'] / 100);
                            $opd_tax_base = (float)$opd_value['apply_charge'] - $opd_discount;
                            $opd_tax      = ($opd_tax_base * (float)$opd_value['tax']) / 100;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo html_escape($opd_value['name']); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$opd_value['apply_charge']); ?></td>
                            <td><?php echo html_escape($opd_value['qty'] . ' ' . $opd_value['unit']); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($opd_discount) . ' (' . $opd_value['discount_percentage'] . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($opd_tax) . ' (' . $opd_value['tax'] . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$opd_value['amount']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ④ IPD Charges -->
                <?php if (!empty($ipd_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('ipd_charges'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('service'); ?></th>
                            <th style="width:16%"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th style="width:8%"><?php echo $this->lang->line('qty'); ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ipd_data as $ipd_key => $ipd_value) {
                            $total_amount += (float)$ipd_value['amount'];
                            $ipd_discount = ((float)$ipd_value['apply_charge'] * (float)$ipd_value['discount_percentage']) / 100;
                            $ipd_tax_base = (float)$ipd_value['apply_charge'] - $ipd_discount;
                            $ipd_tax      = ($ipd_tax_base * (float)$ipd_value['tax']) / 100;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo html_escape($ipd_value['name']); ?></td>
                            <td><?php echo $currency_symbol . amountFormat((float)$ipd_value['apply_charge']); ?></td>
                            <td><?php echo html_escape($ipd_value['qty'] . ' ' . $ipd_value['unit']); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($ipd_discount) . ' (' . $ipd_value['discount_percentage'] . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($ipd_tax) . ' (' . $ipd_value['tax'] . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$ipd_value['amount']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑤ Pharmacy Bills -->
                <?php if (!empty($pharmacy_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('pharmacy_bill'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-12 sh-text-right"><?php echo $this->lang->line('refund') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pharmacy_data as $pharmacy_key => $pharmacy_value) {
                            $total_amount += (float)$pharmacy_value->net_amount;
                            $pharm_total    = (float)$pharmacy_value->total;
                            $pharm_discount = (float)$pharmacy_value->discount;
                            $pharm_tax_base = $pharm_total - $pharm_discount;
                            $pharm_tax_pct  = ($pharm_tax_base > 0) ? (((float)$pharmacy_value->tax * 100) / $pharm_tax_base) : 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $pharmacy_bill_prefix . $pharmacy_value->id; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($pharm_total); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($pharm_discount) . ' (' . amountFormat((float)$pharmacy_value->discount_percentage) . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$pharmacy_value->tax) . ' (' . amountFormat($pharm_tax_pct) . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$pharmacy_value->refund_amount); ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$pharmacy_value->net_amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑥ Pathology Bills -->
                <?php if (!empty($pathology_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('pathology_bill'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th style="width:8%"><?php echo $this->lang->line('qty'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pathology_data as $pathology_key => $pathology_value) {
                            $total_amount += (float)$pathology_value->net_amount;
                            $path_total    = (float)$pathology_value->total;
                            $path_discount = (float)$pathology_value->discount;
                            $path_base     = $path_total - $path_discount;
                            $path_tax_pct  = ($path_base > 0) ? (((float)$pathology_value->tax * 100) / $path_base) : 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $pathology_bill_prefix . $pathology_value->id; ?></td>
                            <td>1</td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($path_total); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($path_discount) . ' (' . amountFormat((float)$pathology_value->discount_percentage) . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$pathology_value->tax) . ' (' . amountFormat($path_tax_pct) . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$pathology_value->net_amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑦ Radiology Bills -->
                <?php if (!empty($radiology_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('radiology_bill'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th style="width:8%"><?php echo $this->lang->line('qty'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($radiology_data as $radiology_key => $radiology_value) {
                            $total_amount += (float)$radiology_value->net_amount;
                            $rad_total    = (float)$radiology_value->total;
                            $rad_discount = (float)$radiology_value->discount;
                            $rad_base     = $rad_total - $rad_discount;
                            $rad_tax_pct  = ($rad_base > 0) ? (((float)$radiology_value->tax * 100) / $rad_base) : 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $radiology_bill_prefix . $radiology_value->id; ?></td>
                            <td>1</td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($rad_total); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($rad_discount) . ' (' . amountFormat((float)$radiology_value->discount_percentage) . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$radiology_value->tax) . ' (' . amountFormat($rad_tax_pct) . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$radiology_value->net_amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑧ Blood Issue Bills -->
                <?php if (!empty($bloodissue_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('blood_issue'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th style="width:8%"><?php echo $this->lang->line('qty'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bloodissue_data as $blood_issue_key => $blood_issue_value) {
                            $blood_issue_value->standard_charge = $blood_issue_value->amount;
                            $total_amount += (float)$blood_issue_value->net_amount;
                            $blood_std      = (float)$blood_issue_value->standard_charge;
                            $blood_discount = calculatePercent($blood_std, (float)$blood_issue_value->discount_percentage);
                            $blood_tax      = calculatePercent($blood_std - $blood_discount, (float)$blood_issue_value->tax_percentage);
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $blood_bank_bill_prefix . $blood_issue_value->id; ?></td>
                            <td>1</td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($blood_std); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($blood_discount) . ' (' . $blood_issue_value->discount_percentage . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($blood_tax) . ' (' . $blood_issue_value->tax_percentage . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$blood_issue_value->net_amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑨ Component Issue Bills -->
                <?php if (!empty($componentissue_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('component_issue'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-18 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($componentissue_data as $componentissue_key => $componentissue_value) {
                            $total_amount += (float)$componentissue_value['net_amount'];
                            $comp_discount = calculatePercent((float)$componentissue_value['amount'], (float)$componentissue_value['discount_percentage']);
                            $comp_tax      = calculatePercent((float)$componentissue_value['amount'] - $comp_discount, (float)$componentissue_value['tax_percentage']);
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $blood_bank_bill_prefix . $componentissue_value['id']; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$componentissue_value['standard_charge']); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($comp_discount) . ' (' . $componentissue_value['discount_percentage'] . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($comp_tax) . ' (' . $componentissue_value['tax_percentage'] . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$componentissue_value['net_amount']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑩ Ambulance Bills -->
                <?php if (!empty($ambulance_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('ambulance'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th style="width:14%"><?php echo $this->lang->line('vehicle_number'); ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="sh-col-16 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ambulance_data as $ambulance_key => $ambulance_value) {
                            if ($ambulance_value['case_reference_id'] == $case_id) {
                                $total_amount += (float)$ambulance_value['net_amount'];
                                $amb_tax = (((float)$ambulance_value['amount'] - (float)$ambulance_value['discount']) * (float)$ambulance_value['tax_percentage']) / 100;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $ambulance_value['id']; ?></td>
                            <td><?php echo html_escape($ambulance_value['vehicle_no']); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$ambulance_value['amount']); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat((float)$ambulance_value['discount']) . ' (' . amountFormat((float)$ambulance_value['discount_percentage']) . '%)'; ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($amb_tax) . ' (' . amountFormat((float)$ambulance_value['tax_percentage']) . '%)'; ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$ambulance_value['net_amount']); ?></td>
                        </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑪ Transactions -->
                <?php if (!empty($transaction_data)) { ?>
                <div class="sh-print-section-title"><?php echo $this->lang->line('transactions'); ?></div>
                <table class="sh-print-table">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('transaction_id'); ?></th>
                            <th style="width:22%"><?php echo $this->lang->line('payment_date'); ?></th>
                            <th style="width:22%"><?php echo $this->lang->line('payment_mode'); ?></th>
                            <th class="sh-col-20 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaction_data as $transaction_key => $transaction_value) {
                            $amount_paid += (float)$transaction_value->amount;
                        ?>
                        <tr>
                            <td><?php echo $transaction_prefix . $transaction_value->id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction_value->payment_date); ?></td>
                            <td><?php echo ($transaction_value->payment_mode ? $this->lang->line(strtolower($transaction_value->payment_mode)) : '-'); ?></td>
                            <td class="sh-text-right fw-bold"><?php echo $currency_symbol . amountFormat((float)$transaction_value->amount); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>

                <!-- ⑫ Grand total summary -->
                <table class="sh-print-table sh-mt-12" >
                    <tbody>
                        <tr class="sh-row-first">
                            <td class="sh-col-80 sh-text-right fw-medium"><?php echo $this->lang->line('grand_total'); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($total_amount); ?></td>
                        </tr>
                        <tr>
                            <td class="sh-text-right fw-medium"><?php echo $this->lang->line('amount_paid'); ?></td>
                            <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($amount_paid); ?></td>
                        </tr>
                        <tr class="sh-row-total">
                            <td class="sh-text-right fw-medium"><?php echo $this->lang->line('balance_amount'); ?></td>
                            <td><?php echo $currency_symbol . amountFormat($total_amount - $amount_paid); ?></td>
                        </tr>
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

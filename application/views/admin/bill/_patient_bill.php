<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>

<!-- TPA Info -->
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fas fa-shield-alt me-1"></i><?php echo $this->lang->line('tpa'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa'); ?></span>
                <span class="sh-info-value"><?php if(isset($patient['organisation_name'])){ echo $patient['organisation_name']; } ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></span>
                <span class="sh-info-value"><?php if(isset($patient['insurance_validity'])){ echo $this->customlib->YYYYMMDDTodateFormat($patient['insurance_validity']); } ?></span>
            </div>
            <div class="col-6 col-md-4 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></span>
                <span class="sh-info-value"><?php if(isset($patient['insurance_id'])){ echo $patient['insurance_id']; } ?></span>
            </div>
        </div>
    </div>
</div>

<?php
$total_amount  = 0;
$amount_paid   = 0;
$amount_refund = 0;
?>

<!-- OPD Charges -->
<?php if(!empty($opd_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('opd_charges'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th width="20%"><?php echo $this->lang->line('service'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
                        <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
                        <th width="15%" class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($opd_data as $opd_key => $opd_value) {
                    $total_amount += $opd_value['amount']; ?>
                    <tr>
                        <td width="20%"><?php echo $opd_value['name']; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$opd_value['apply_charge']; ?></td>
                        <td width="10%"><?php echo $opd_value['qty']." ".$opd_value['unit']; ?></td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.amountFormat(($opd_value['apply_charge'] * ($opd_value['discount_percentage']/100)))." (".$opd_value['discount_percentage']."%) "; ?></td>
                        <td class="text-end"><?php
                            $tax_raw = ($opd_value["apply_charge"] - (($opd_value["apply_charge"] * $opd_value["discount_percentage"]) / 100));
                            $tax = (($tax_raw * $opd_value["tax"]) / 100);
                            echo $currency_symbol.amountFormat($tax)." (".$opd_value['tax']."%) ";
                        ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$opd_value['amount']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- IPD Charges -->
<?php if(!empty($ipd_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('ipd_charges'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('service'); ?></th>
                        <th><?php echo $this->lang->line('charge'); ?></th>
                        <th><?php echo $this->lang->line('qty'); ?></th>
                        <th width="15%" class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ipd_data as $ipd_key => $ipd_value) {
                    $total_amount += $ipd_value['amount']; ?>
                    <tr>
                        <td width="20%"><?php echo $ipd_value['name']; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$ipd_value['apply_charge']; ?></td>
                        <td width="10%"><?php echo $ipd_value['qty']." ".$ipd_value['unit']; ?></td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.amountFormat(($ipd_value['apply_charge'] * $ipd_value['discount_percentage'])/100)." (".$ipd_value['discount_percentage']."%) "; ?></td>
                        <td class="text-end" width="15%"><?php
                            $tax_raw = ($ipd_value["apply_charge"] - (($ipd_value["apply_charge"] * $ipd_value["discount_percentage"]) / 100));
                            $tax = (($tax_raw * $ipd_value["tax"]) / 100);
                            echo $currency_symbol.amountFormat($tax)." (".$ipd_value['tax']."%) ";
                        ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$ipd_value['amount']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Pharmacy Bill -->
<?php if(!empty($pharmacy_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('pharmacy_bill'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                        <th><?php echo $this->lang->line('charge'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('refund'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pharmacy_data as $pharmacy_key => $pharmacy_value) {
                    $total_amount += $pharmacy_value->net_amount; ?>
                    <tr>
                        <td width="20%" class="white-space-nowrap"><?php echo $pharmacy_bill_prefix.$pharmacy_value->id; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$pharmacy_value->total; ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$pharmacy_value->discount." (".$pharmacy_value->discount_percentage."%) "; ?></td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.$pharmacy_value->tax." (".amountFormat(($pharmacy_value->tax * 100) / ($pharmacy_value->total - $pharmacy_value->discount))."%) "; ?></td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.$pharmacy_value->refund_amount; ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$pharmacy_value->net_amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Pathology Bill -->
<?php if(!empty($pathology_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('pathology_bill'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                        <th><?php echo $this->lang->line('charge'); ?></th>
                        <th><?php echo $this->lang->line('qty'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pathology_data as $pathology_key => $pathology_value) {
                    $total_amount += $pathology_value->net_amount; ?>
                    <tr>
                        <td width="20%" class="white-space-nowrap"><?php echo $pathology_bill_prefix.$pathology_value->id; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$pathology_value->total; ?></td>
                        <td width="10%">1</td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.$pathology_value->discount." (".$pathology_value->discount_percentage."%) "; ?></td>
                        <td width="15%" class="text-end"><?php
                            if($pathology_value->total > 0){
                                echo $currency_symbol.$pathology_value->tax." (".amountFormat(($pathology_value->tax * 100) / ($pathology_value->total - $pathology_value->discount), 2)."%)";
                            }
                        ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$pathology_value->net_amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Radiology Bill -->
<?php if(!empty($radiology_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('radiology_bill'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                        <th><?php echo $this->lang->line('charge'); ?></th>
                        <th><?php echo $this->lang->line('qty'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($radiology_data as $radiology_key => $radiology_value) {
                    $total_amount += $radiology_value->net_amount; ?>
                    <tr>
                        <td width="20%" class="white-space-nowrap"><?php echo $radiology_bill_prefix.$radiology_value->id; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$radiology_value->total; ?></td>
                        <td width="10%">1</td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.$radiology_value->discount." (".$radiology_value->discount_percentage."%) "; ?></td>
                        <td class="text-end" width="15%"><?php
                            $discount_amt = ($radiology_value->total * $radiology_value->discount_percentage) / 100;
                            $div_by = $radiology_value->total - $discount_amt;
                            echo $currency_symbol.$radiology_value->tax." (".amountFormat(($radiology_value->tax * 100) / $div_by)."%)";
                        ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$radiology_value->net_amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Blood Issue -->
<?php if(!empty($bloodissue_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('blood_issue'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th width="20%"><?php echo $this->lang->line('bill_no'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
                        <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end" width="15%"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bloodissue_data as $blood_issue_key => $blood_issue_value) {
                    $total_amount += $blood_issue_value->net_amount;
                    $blood_issue_value->standard_charge = $blood_issue_value->amount;
                    $discount_amount = calculatePercent($blood_issue_value->standard_charge, $blood_issue_value->discount_percentage); ?>
                    <tr>
                        <td width="20%"><?php echo $blood_bank_bill_prefix.$blood_issue_value->id; ?></td>
                        <td width="20%"><?php echo $currency_symbol.$blood_issue_value->standard_charge; ?></td>
                        <td width="10%">1</td>
                        <td width="15%" class="text-end"><?php echo $discount_amount." (".$blood_issue_value->discount_percentage."%) "; ?></td>
                        <td width="15%" class="text-end"><?php echo $currency_symbol.calculatePercent(($blood_issue_value->standard_charge - $discount_amount), $blood_issue_value->tax_percentage)." (".$blood_issue_value->tax_percentage."%) "; ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$blood_issue_value->net_amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Component Issue -->
<?php if(!empty($componentissue_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('component_issue'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th width="20%"><?php echo $this->lang->line('bill_no'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
                        <th width="20%" class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th width="20%" class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($componentissue_data as $componentissue_key => $componentissue_value) {
                    $total_amount += $componentissue_value['net_amount'];
                    $discount_amount = calculatePercent($componentissue_value['amount'], $componentissue_value['discount_percentage']);
                    $tax_amount = calculatePercent(($componentissue_value['amount'] - $discount_amount), $componentissue_value['tax_percentage']); ?>
                    <tr>
                        <td width="20%"><?php echo $blood_bank_bill_prefix.$componentissue_value['id']; ?></td>
                        <td width="20%"><?php echo $currency_symbol.amountFormat($componentissue_value['standard_charge']); ?></td>
                        <td width="20%" class="text-end"><?php echo $discount_amount.' ('.$componentissue_value['discount_percentage'].'%)'; ?></td>
                        <td width="20%" class="text-end"><?php echo $tax_amount.' ('.$componentissue_value['tax_percentage'].'%)'; ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$componentissue_value['net_amount']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Ambulance -->
<?php if(!empty($ambulance_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('ambulance'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th width="20%"><?php echo $this->lang->line('bill_no'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
                        <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ambulance_data as $ambulance_key => $ambulance_value) {
                    if($ambulance_value['case_reference_id'] == $case_id){
                        $total_amount += $ambulance_value['net_amount']; ?>
                        <tr>
                            <td class="white-space-nowrap"><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing').$ambulance_value['id']; ?></td>
                            <td><?php echo $currency_symbol.amountFormat($ambulance_value['amount']); ?></td>
                            <td><?php echo $ambulance_value['vehicle_no']; ?></td>
                            <td class="text-end"><?php echo amountFormat($ambulance_value['discount']); ?> (<?php echo amountFormat($ambulance_value['discount_percentage']); ?>%)</td>
                            <td class="text-end"><?php
                                $tax = (($ambulance_value['amount'] - $ambulance_value['discount']) * $ambulance_value['tax_percentage']) / 100;
                                echo amountFormat($tax); ?> (<?php echo amountFormat($ambulance_value['tax_percentage']); ?>%)</td>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat($ambulance_value['net_amount']); ?></td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Transactions -->
<?php if(!empty($transaction_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('transactions'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('payment_date'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($transaction_data as $transaction_key => $transaction_value) {
                    $amount_paid += $transaction_value->amount; ?>
                    <tr>
                        <td width="20%" class="white-space-nowrap"><?php echo $transaction_prefix.$transaction_value->id; ?></td>
                        <td width="30%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction_value->payment_date); ?></td>
                        <td><?php if($transaction_value->payment_mode){ echo $this->lang->line(strtolower($transaction_value->payment_mode)); } ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$transaction_value->amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Refund -->
<?php if(!empty($refund_data)){ ?>
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('refund'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('payment_date'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($refund_data as $transaction_key => $transaction_value) {
                    $amount_refund += $transaction_value->amount; ?>
                    <tr>
                        <td width="20%" class="white-space-nowrap"><?php echo $transaction_prefix.$transaction_value->id; ?></td>
                        <td width="30%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction_value->payment_date); ?></td>
                        <td><?php echo $this->lang->line(strtolower($transaction_value->payment_mode)); ?></td>
                        <td class="text-end"><?php echo $currency_symbol.$transaction_value->amount; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Amount Summary -->
<div class="sh-form-card mb-2">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('amount_summary'); ?></span>
    </div>
    <div class="p-2">
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <table class="table table-hover table-sm mb-0">
                    <tbody>
                        <tr>
                            <th class="w-50"><?php echo $this->lang->line('grand_total'); ?>:</th>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat($total_amount); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('amount_paid'); ?>:</th>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat($amount_paid); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('refund_amount'); ?>:</th>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat($amount_refund); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('balance_amount'); ?>:</th>
                            <td class="text-end"><?php echo $currency_symbol.amountFormat(($total_amount - $amount_paid + $amount_refund)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

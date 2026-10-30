<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="p-1">

    <!-- Card 1: Purchase + Supplier Info -->
    <div class="sh-form-card mb-3">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_details'); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('purchase_no'); ?></span>
                    <span class="sh-info-value highlight"><?php echo $this->customlib->getSessionPrefixByType('purchase_no') . $result['id']; ?></span>
                </div>
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('bill_no'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['invoice_no']); ?></span>
                </div>
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('purchase_date'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat()); ?></span>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('supplier_name'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['supplier']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('supplier_contact'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['contact']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('contact_person'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['supplier_person']); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('contact_person_phone'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['supplier_person_contact']); ?></span>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('drug_license_number'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['supplier_drug_licence']); ?></span>
                </div>
                <div class="col-6 col-md-8 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('address'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result['address']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Medicines + TPA Charges (Tab layout) -->
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('medicine_tpa_charges'); ?></span>
        </div>
        <div class="p-2">
            <div class="d-flex gap-3">
                <!-- Left: Medicine tab nav -->
                <div style="min-width:200px; max-width:220px;">
                    <div class="nav flex-column nav-pills" id="tpa-medicine-tabs" role="tablist">
                        <?php $i = 1; foreach ($detail as $bill_key => $bill) { ?>
                        <button class="nav-link text-start<?php echo $i == 1 ? ' active' : ''; ?>"
                                id="tab-btn-<?php echo $bill_key; ?>"
                                data-bs-toggle="pill"
                                data-bs-target="#menu<?php echo $bill_key; ?>"
                                type="button" role="tab">
                            <div class="fw-semibold small"><?php echo html_escape($bill['medicine_name']); ?></div>
                            <div class="text-muted" style="font-size:0.72rem;"><?php echo html_escape($bill['batch_no']); ?> &middot; <?php echo html_escape($bill['medicine_category']); ?></div>
                        </button>
                        <?php $i++; } ?>
                    </div>
                </div>

                <!-- Right: Tab content -->
                <div class="flex-grow-1 tab-content" id="tpa-medicine-tabcontent">
                    <?php $j = 0; foreach ($detail as $bill_key => $bill) { ?>
                    <div class="tab-pane fade<?php echo $j == 0 ? ' show active' : ''; ?>" id="menu<?php echo $bill_key; ?>" role="tabpanel">
                        <div class="d-flex gap-3 flex-wrap">

                            <!-- Purchase Details card -->
                            <div style="flex:1; min-width:240px;">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('purchase_details'); ?></span>
                                    </div>
                                    <div class="sh-info-grid">
                                        <div class="row g-0">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('medicine_category'); ?></span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['medicine_category']); ?></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('medicine_name'); ?></span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['medicine_name']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('batch_no'); ?></span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['batch_no']); ?></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('expiry_month'); ?></span>
                                                <span class="sh-info-value"><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('mrp'); ?> (<?php echo $currency_symbol; ?>)</span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['mrp']); ?></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('batch_amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['batch_amount']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('sale_price'); ?> (<?php echo $currency_symbol; ?>)</span>
                                                <span class="sh-info-value">
                                                    <?php echo number_format($bill['sale_rate'], 2); ?>
                                                    <input type="hidden" name="salerate[]" value="<?php echo number_format($bill['sale_rate'], 2); ?>">
                                                    <input type="hidden" name="id[]" value="<?php echo $bill['id']; ?>">
                                                </span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('packing_qty'); ?></span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['packing_qty']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('quantity'); ?></span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['quantity']); ?></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?> (%)</span>
                                                <span class="sh-info-value"><?php echo html_escape($bill['tax']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-0 sh-row-divider">
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('purchase_price'); ?> (<?php echo $currency_symbol; ?>)</span>
                                                <span class="sh-info-value"><?php echo number_format($bill['purchase_price'], 2); ?></span>
                                            </div>
                                            <div class="col-6 sh-info-item">
                                                <span class="sh-info-label"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                                                <span class="sh-info-value"><?php echo number_format($bill['amount'], 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TPA Charges card -->
                            <div style="flex:1; min-width:240px;">
                                <div class="sh-form-card h-100">
                                    <div class="sh-card-header">
                                        <span class="sh-card-header-title"><?php echo $this->lang->line('scheduled_charges_for_tpa'); ?></span>
                                    </div>
                                    <div class="p-2">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="small text-muted"><?php echo $this->lang->line('sale_price'); ?>: <strong><?php echo $currency_symbol . ' ' . number_format($bill['sale_rate'], 2); ?></strong></span>
                                            <div>
                                                <input type="hidden" name="standard_charge_<?php echo $bill['id']; ?>" id="standard_charge_<?php echo $bill['id']; ?>" value="<?php echo $bill['sale_rate']; ?>">
                                                <input type="hidden" name="batch_id_<?php echo $bill['id']; ?>" id="batch_id_<?php echo $bill['id']; ?>" value="<?php echo $bill['id']; ?>">
                                                <button type="button" class="btn btn-sm btn-info" onclick="apply_to_all(<?php echo $bill['id']; ?>)"><?php echo $this->lang->line('apply_to_all'); ?></button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('tpa_name'); ?></th>
                                                        <th class="text-end" style="width:110px;"><?php echo $this->lang->line('charge'); ?> (<?php echo $currency_symbol; ?>)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($schedule as $category) {
                                                        $batchorgcharges = $this->customlib->getbatchorgcharges($bill['id'], $category['id']);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="orgnization_medicine_charge_id_<?php echo $category['id']; ?>_<?php echo $bill['id']; ?>" value="<?php echo isset($batchorgcharges->orgnization_medicine_charge_id) ? $batchorgcharges->orgnization_medicine_charge_id : ''; ?>">
                                                            <input type="hidden" name="schedule_charge_id_<?php echo $bill['id']; ?>[]" value="<?php echo $category['id']; ?>">
                                                            <?php echo html_escape($category['organisation_name']); ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <input type="text" name="schedule_charge_<?php echo $category['id']; ?>_<?php echo $bill['id']; ?>" id="schedule_charge_<?php echo $category['id']; ?>_<?php echo $bill['id']; ?>" class="form-control form-control-sm text-end schedule_charge_<?php echo $bill['id']; ?>" value="<?php echo isset($batchorgcharges->org_charge) ? $batchorgcharges->org_charge : ''; ?>">
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php $j++; } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function apply_to_all(batch_id) {
    var standard_charge = $('#standard_charge_' + batch_id).val();
    $('input.schedule_charge_' + batch_id).val(standard_charge);
}
</script>

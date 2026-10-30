<?php
if (!empty($result)) {
    $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="p-1">
    <div class="sh-form-card">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('charges_details'); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('charge_type'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->charge_type_name); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('charge_category'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->charge_category_name); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('name'); ?></span>
                    <span class="sh-info-value highlight"><?php echo html_escape($result->name); ?></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>)</span>
                    <span class="sh-info-value"><?php echo html_escape($result->standard_charge); ?></span>
                </div>
            </div>
            <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?> (%)</span>
                    <span class="sh-info-value"><?php echo html_escape($result->percentage); ?></span>
                </div>
                <div class="col-6 col-md-9 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('description'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($result->description) ?: '—'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($result->organisation_charges)) { ?>
    <div class="sh-form-card mt-3">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><?php echo $this->lang->line('scheduled_charges_for_tpa'); ?></span>
        </div>
        <div class="p-3">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('schedule_charge_name'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('charges'); ?> (<?php echo $currency_symbol; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result->organisation_charges as $org_charge_value) { ?>
                    <tr>
                        <td><?php echo html_escape($org_charge_value->organisation_name); ?></td>
                        <td class="text-end"><?php echo html_escape($org_charge_value->org_charge); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
</div>
<?php } ?>

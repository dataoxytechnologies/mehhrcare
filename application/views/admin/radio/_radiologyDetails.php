<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<div class="sh-form-card mb-3">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_details'); ?></span>
    </div>
    <div class="sh-info-grid">
        <div class="row g-0">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('test_name'); ?></span>
                <span class="sh-info-value highlight"><?php echo $result->test_name; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('short_name'); ?></span>
                <span class="sh-info-value"><?php echo $result->short_name; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('test_type'); ?></span>
                <span class="sh-info-value"><?php echo $result->test_type ?: '—'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('report_days'); ?></span>
                <span class="sh-info-value"><?php echo $result->report_days; ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('category_name'); ?></span>
                <span class="sh-info-value"><?php echo $result->lab_name ?: '—'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('sub_category'); ?></span>
                <span class="sh-info-value"><?php echo $result->sub_category ?: '—'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('charge_category'); ?></span>
                <span class="sh-info-value"><?php echo $result->charge_category_name ?: '—'; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tax_category'); ?></span>
                <span class="sh-info-value"><?php echo $result->apply_tax ?: '—'; ?></span>
            </div>
        </div>
        <div class="row g-0 sh-row-divider">
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('tax'); ?> (%)</span>
                <span class="sh-info-value"><?php echo $result->percentage; ?>%</span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>)</span>
                <span class="sh-info-value"><?php echo $currency_symbol . $result->standard_charge; ?></span>
            </div>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</span>
                <span class="sh-info-value"><?php echo $currency_symbol . amountFormat($result->standard_charge + calculatePercent($result->standard_charge, $result->percentage)); ?></span>
            </div>
        </div>
        <?php
        $cutom_fields_data = get_custom_table_values($result->id, 'radiologytest');
        if (!empty($cutom_fields_data)) { ?>
        <div class="row g-0 sh-row-divider">
            <?php foreach ($cutom_fields_data as $field_key => $field_value) { ?>
            <div class="col-6 col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $field_value->name; ?></span>
                <span class="sh-info-value">
                <?php
                if (is_string($field_value->field_value) && is_array(json_decode($field_value->field_value, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                    $field_array = json_decode($field_value->field_value);
                    echo "<ul class='patient_custom_field mb-0'>";
                    foreach ($field_array as $each_value) { echo "<li>" . $each_value . "</li>"; }
                    echo "</ul>";
                } else {
                    $display_field = $field_value->field_value;
                    if ($field_value->type == "link") {
                        $display_field = "<a href='" . $field_value->field_value . "' target='_blank'>" . $field_value->field_value . "</a>";
                    }
                    echo $display_field;
                }
                ?>
                </span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<?php if (!empty($result->radiology_parameter)) { ?>
<div class="sh-form-card">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('test_parameter_name'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-sm table-hover sh-tests-table mb-0">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('test_parameter_name'); ?></th>
                        <th><?php echo $this->lang->line('reference_range'); ?></th>
                        <th><?php echo $this->lang->line('unit'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result->radiology_parameter as $pathology_value) { ?>
                    <tr>
                        <td><?php echo $pathology_value->parameter_name; ?></td>
                        <td><?php echo $pathology_value->reference_range; ?></td>
                        <td><?php echo $pathology_value->unit_name; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

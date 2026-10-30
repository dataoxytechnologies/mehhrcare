<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$em = '&mdash;';
?>
<div class="sh-form-card mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><i class="fa fa-hospital-o me-1"></i><?php echo $this->lang->line('operation_details'); ?></span>
    </div>
    <div class="sh-info-grid">

        <div class="row">
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('reference_no'); ?></span>
                <span class="sh-info-value"><?php echo $operation_theater_reference_no . $otdetails->id ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('operation_name'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->operation ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('date'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->date ? date($this->customlib->getHospitalDateFormat(true, true), strtotime($otdetails->date)) : $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('operation_category'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->category ?: $em; ?></span>
            </div>
        </div>

        <div class="sh-row-divider"></div>

        <div class="row">
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('consultant_doctor'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->name ? $otdetails->name . ' ' . $otdetails->surname . ' (' . $otdetails->employee_id . ')' : $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('assistant_consultant') . ' 1'; ?></span>
                <span class="sh-info-value"><?php echo $otdetails->ass_consultant_1 ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('assistant_consultant') . ' 2'; ?></span>
                <span class="sh-info-value"><?php echo $otdetails->ass_consultant_2 ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('anesthetist'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->anesthetist ?: $em; ?></span>
            </div>
        </div>

        <div class="sh-row-divider"></div>

        <div class="row">
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('anaethesia_type'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->anaethesia_type ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('ot_technician'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->ot_technician ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('ot_assistant'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->ot_assistant ?: $em; ?></span>
            </div>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('remark'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->remark ?: $em; ?></span>
            </div>
        </div>

        <div class="sh-row-divider"></div>

        <div class="row">
            <div class="col-md-12 sh-info-item">
                <span class="sh-info-label"><?php echo $this->lang->line('result'); ?></span>
                <span class="sh-info-value"><?php echo $otdetails->result ?: $em; ?></span>
            </div>
        </div>

        <?php if (!empty($fields)) { ?>
        <div class="sh-row-divider"></div>
        <div class="row">
            <?php foreach ($fields as $fields_key => $fields_value) {
                $display_field = $otdetails->{$fields_value->name} ?: $em;
                if ($fields_value->type == "link" && $otdetails->{$fields_value->name}) {
                    $display_field = "<a href=" . $otdetails->{$fields_value->name} . " target='_blank'>" . $otdetails->{$fields_value->name} . "</a>";
                } ?>
            <div class="col-md-3 sh-info-item">
                <span class="sh-info-label"><?php echo $fields_value->name; ?></span>
                <span class="sh-info-value"><?php echo $display_field; ?></span>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

    </div>
</div>

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$no_pic = "uploads/patient_images/no_image.png";
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('birth_record'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
</head>
<body>

<?php if (!empty($print_details[0]['print_header'])) { ?>
<div class="fixed-print-header">
    <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>" class="img-fluid sh-avatar-cover" >
</div>
<?php } ?>

<table class="table-print-full" width="100%">
    <thead><tr><td><div class="header-space">&nbsp;</div></td></tr></thead>
    <tbody><tr><td>
    <div class="content-body p-1">

        <?php $has_photo = (!empty($result['child_pic']) && $result['child_pic'] !== $no_pic); ?>

        <?php if ($has_photo) { ?>
        <!-- 2-column header: photo (left) + title & meta (right) -->
        <table class="sh-print-header-2col">
            <tr>
                <td class="sh-ph-photo-cell">
                    <img src="<?php echo $this->media_storage->getImageURL($result['child_pic']); ?>" alt="<?php echo html_escape($result['child_name']); ?>">
                </td>
                <td class="sh-ph-info-cell">
                    <div class="sh-ph-title"><?php echo $this->lang->line('birth_record'); ?></div>
                    <table>
                        <tr>
                            <th><?php echo $this->lang->line('reference_no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $result['id']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('birth_date'); ?></th>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['birth_date']); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php } else { ?>
        <!-- Fallback (no photo): keep original title + meta layout -->
        <div class="sh-print-title"><?php echo $this->lang->line('birth_record'); ?></div>
        <table width="100%" style="margin-bottom:10px;font-size:11.5px;">
            <tr>
                <td><?php echo $this->lang->line('reference_no'); ?>: <strong><?php echo $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $result['id']; ?></strong></td>
                <td class="sh-text-right"><?php echo $this->lang->line('birth_date'); ?>: <strong><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['birth_date']); ?></strong></td>
            </tr>
        </table>
        <?php } ?>

        <!-- Child details -->
        <div class="sh-print-info-block">
            <table class="sh-print-info-table">
                <tr>
                    <th width="22%"><?php echo $this->lang->line('child_name'); ?></th>
                    <td width="28%"><?php echo html_escape($result['child_name']); ?></td>
                    <th width="22%"><?php echo $this->lang->line('gender'); ?></th>
                    <td width="28%"><?php echo isset($result['gender']) ? $this->lang->line(strtolower($result['gender'])) : ''; ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('weight'); ?></th>
                    <td><?php echo html_escape($result['weight']); ?></td>
                    <th><?php echo $this->lang->line('mother_name'); ?></th>
                    <td><?php echo html_escape($result['patient_name']) . ' (' . html_escape($result['patient_id']) . ')'; ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('case_id'); ?></th>
                    <td><?php echo html_escape($result['case_reference_id']); ?></td>
                    <th><?php echo $this->lang->line('father_name'); ?></th>
                    <td><?php echo html_escape($result['father_name']); ?></td>
                </tr>
                <tr>
                    <th><?php echo $this->lang->line('address'); ?></th>
                    <td colspan="3"><?php echo html_escape($result['address']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Custom fields -->
        <?php if (!empty($fields)) { ?>
        <div class="sh-print-section-title"><?php echo $this->lang->line('additional_details'); ?></div>
        <div class="sh-print-info-block">
            <table class="sh-print-info-table">
                <?php foreach ($fields as $fields_value) {
                    $display_field = $result[$fields_value->name] ?? '';
                    if ($fields_value->type == 'link') {
                        $display_field = '<a href="' . html_escape($display_field) . '" target="_blank">' . html_escape($display_field) . '</a>';
                    } else {
                        $display_field = html_escape($display_field);
                    } ?>
                <tr>
                    <th width="25%"><?php echo html_escape($fields_value->name); ?></th>
                    <td><?php echo $display_field; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <?php } ?>

    </div>
    </td></tr></tbody>
    <tfoot><tr><td>
    <?php if (!empty($print_details[0]['print_footer'])) { ?>
    <div class="footer-space">&nbsp;</div>
    <?php } ?>
    </td></tr></tfoot>
</table>

<?php if (!empty($print_details[0]['print_footer'])) { ?>
<div class="footer-fixed">
    <?php echo $print_details[0]['print_footer']; ?>
</div>
<?php } ?>

</body>
</html>

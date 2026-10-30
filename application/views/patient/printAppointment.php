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
        <tr>
            <td><div class="header-space">&nbsp;</div></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <div class="content-body sh-px-12" >
                <div class="print-area">

                    <div class="sh-print-title"><?php echo $this->lang->line('appointment_details'); ?></div>

                    <div class="sh-print-info-block">
                        <div class="sh-flex-gap18">
                            <table class="sh-print-info-table w-50" >
                                <colgroup>
                                    <col style="width:40%"><col style="width:60%">
                                </colgroup>
                                <tr>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <td><span id='patient_name_view'><?php echo html_escape($result['patients_name'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line("age"); ?></th>
                                    <td><?php echo ($this->customlib->get_patient_current_age($result['patient_id']) ?: '-'); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('email'); ?></th>
                                    <td><span id='emails_view'><?php echo html_escape($result['patient_email'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <td><span id="phones_view"><?php echo html_escape($result['patient_mobileno'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <td><span id="genders"><?php echo ($result['patients_gender'] ? $this->lang->line(strtolower($result['patients_gender'])) : '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('doctor'); ?></th>
                                    <td><?php echo html_escape(composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']) ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line("department"); ?></th>
                                    <td><?php echo html_escape($result["department_name"] ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('live_consultation'); ?></th>
                                    <td><?php echo ($result['live_consult'] ? $this->lang->line($result['live_consult']) : '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('collected_by'); ?></th>
                                    <td><span id="collected_by"><?php
                                        if (!empty($result['received_by']) && (int)$result['received_by'] > 0) {
                                            $staff_data = $this->staff_model->getstaff($result['received_by']);
                                            echo html_escape(($staff_data["name"] ?? '') . " " . ($staff_data["surname"] ?? '') . " (" . ($staff_data["employee_id"] ?? '') . ")");
                                        } else {
                                            echo '-';
                                        }
                                    ?></span></td>
                                </tr>
                            </table>

                            <table class="sh-print-info-table w-50" >
                                <colgroup>
                                    <col style="width:40%"><col style="width:60%">
                                </colgroup>
                                <tr>
                                    <th><?php echo $this->lang->line('appointment_no'); ?></th>
                                    <td><span id="appointmentno"><?php echo html_escape($result['appointment_no'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line("appointment_s_no"); ?></th>
                                    <td><?php echo html_escape($result['appointment_serial_no'] ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line("appointment_date"); ?></th>
                                    <td><?php echo html_escape($result["date"] ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('appointment_priority'); ?></th>
                                    <td><?php echo html_escape($result['appoint_priority'] ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('shift'); ?></th>
                                    <td><span id='patient_name_view'><?php echo html_escape($result['global_shift_name'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('slot'); ?></th>
                                    <td><span id="appointmentno"><?php echo html_escape($result['doctor_shift_name'] ?: '-') ?></span></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                    <td><?php echo ($result['payment_mode'] ? $this->lang->line(strtolower($result['payment_mode'])) : '-') ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                    <td><?php echo ($result['appointment_status'] ? $this->lang->line($result['appointment_status']) : '-') ?></td>
                                </tr>

                                <?php if (($result['payment_mode'] ?? '') == 'Cheque') { ?>
                                    <tr id="payrow">
                                        <th><?php echo $this->lang->line('cheque_no'); ?></th>
                                        <td><span id='spn_chequeno'><?php echo html_escape($result['cheque_no'] ?: '-') ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('cheque_date'); ?></th>
                                        <td><span id="spn_chequedate"><?php echo (!empty($result['cheque_date']) ? $this->customlib->YYYYMMDDTodateFormat($result['cheque_date'], $this->customlib->getHospitalTimeFormat()) : '-'); ?></span></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>

                        <?php if (!empty($result['message']) || !empty($fields)) { ?>
                            <div style="margin-top:10px;"></div>
                            <table class="sh-print-info-table">
                                <colgroup><col style="width:16%"><col style="width:84%"></colgroup>
                                <tr>
                                    <th><?php echo $this->lang->line('message'); ?></th>
                                    <td class="lh-normal"><?php echo html_escape($result['message'] ?: '-') ?></td>
                                </tr>
                                <?php if (!empty($fields)) { foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $result["$fields_value->name"] ?? '';
                                    if ($fields_value->type == "link") {
                                        $display_field = ($display_field !== '')
                                            ? "<a href=\"" . html_escape($display_field) . "\" target=\"_blank\">" . html_escape($display_field) . "</a>"
                                            : '-';
                                    } else {
                                        $display_field = html_escape($display_field ?: '-');
                                    }
                                ?>
                                    <tr>
                                        <th><?php echo html_escape($fields_value->name); ?></th>
                                        <td class="lh-normal"><?php echo $display_field; ?></td>
                                    </tr>
                                <?php } } ?>
                            </table>
                        <?php } ?>
                    </div>

                    <div class="sh-section-divider"></div>

                    <?php
                        $standard_amount = isset($result['standard_amount']) && $result['standard_amount'] !== "" ? (float)$result['standard_amount'] : 0.0;
                        $discount_percentage = isset($result['discount_percentage']) && $result['discount_percentage'] !== "" ? (float)$result['discount_percentage'] : 0.0;
                        $discount_amt = ($standard_amount * $discount_percentage) / 100;
                        $net_amount = $standard_amount - $discount_amt;
                        $paid_amount = isset($result['paid_amount']) && $result['paid_amount'] !== "" ? (float)$result['paid_amount'] : 0.0;
                        $tax_display = isset($result['tax']) && $result['tax'] !== "" ? $result['tax'] : 0;
                    ?>

                    <div class="sh-print-section-title"><?php echo $this->lang->line("payment_details"); ?></div>
                    <table class="sh-print-table">
                        <thead>
                            <tr>
                                <th style="width:35%"><?php echo $this->lang->line('transaction_id'); ?></th>
                                <th><?php echo $this->lang->line('source'); ?></th>
                                <th class="sh-col-22 sh-text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo html_escape($result['transaction_id'] ?: '-') ?></td>
                                <td><?php echo (!empty($result['source']) ? $this->lang->line(strtolower($result['source'])) : '-') ?></td>
                                <td class="sh-text-right"><?php echo $currency_symbol . amountFormat($standard_amount); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="p-0">
                                    <div style="border-bottom: 1px solid #cbd5e1;"></div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="sh-row-first">
                                <td colspan="2"><?php echo $this->lang->line('amount'); ?></td>
                                <td><?php echo $currency_symbol . amountFormat($standard_amount); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo $this->lang->line('discount'); ?></td>
                                <td><?php echo $currency_symbol . amountFormat($discount_amt) . ' (' . amountFormat($discount_percentage) . ' %)'; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo $this->lang->line('tax'); ?></td>
                                <td><?php echo $currency_symbol . amountFormat((float)$tax_display); ?></td>
                            </tr>
                            <tr class="sh-row-total">
                                <td colspan="2"><?php echo $this->lang->line('net_amount'); ?></td>
                                <td><?php echo $currency_symbol . amountFormat($net_amount); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo $this->lang->line('paid_amount'); ?></td>
                                <td><?php echo $currency_symbol . amountFormat($paid_amount); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                    <?php if (!empty($result['payment_note'])) { ?>
                    <div class="sh-note-box">
                        <span class="fw-semibold"><?php echo $this->lang->line('payment_note'); ?>: </span><?php echo html_escape($result['payment_note']); ?>
                    </div>
                    <?php } ?>

                </div>
                </div>
    </td></tr></tbody>
    <tfoot><tr><td>

    <?php
                    if (!empty($print_details[0]['print_footer'])) {
                        ?>
       <div class="footer-space">&nbsp;</div>
  <?php
}
?>



    </td></tr></tfoot>
  </table>
  <?php
                    if (!empty($print_details[0]['print_footer'])) {
                        ?>
  <div class="footer-fixed">
  
  <?php   echo $print_details[0]['print_footer'];?>
                
  </div>
  <?php
}
?>    
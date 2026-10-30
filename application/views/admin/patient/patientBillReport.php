<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_finance'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line("patient_bill_report"); ?></h3>
            </div>
            <div class="card-body pb-0">
                <form action="<?= base_url(); ?>admin/patient/patientbillreport" method="post">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label"><?php echo $this->lang->line('case_id'); ?> <small class="req">*</small></label>
                            <input id="case_reference_id" name="case_reference_id" type="text" class="form-control" value="<?php echo set_value('case_reference_id'); ?>" />
                            <span class="text-danger"><?php echo form_error('case_reference_id'); ?></span>
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                <i class="fa fa-search"></i>
                                <?php echo $this->lang->line('search'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-0" id="bill_report">
                <div id="printhead" class="d-none d-print-block"><h5 class="text-center"><?php echo $this->lang->line("patient_bill_report"); ?></h5></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 with-border pl0 mt-3 mb-2" id="headreport">
                    <h3 class="card-title mb-0"><?php if (!empty($charge_payment_data)) { echo composePatientName($charge_payment_data[0]->patient_name, $charge_payment_data[0]->patient_id) . ' ' . $this->lang->line("bill_report"); } ?></h3>
                    <div class="d-flex gap-2 align-items-center flex-wrap no-print">
                        <a class="btn btn-secondary btn-sm no-print" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('export_to_excel'); ?>" id="btnExport" onclick="exportToExcel()"><i class="fa fa-file-excel-o"></i></a>
                        <a class="btn btn-secondary btn-sm no-print" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>" id="print" onclick="printDiv()"><i class="fa fa-print"></i></a>
                    </div>
                </div>
                <div class="download_label"><?php echo $this->lang->line('ipd_report'); ?></div>
                <table class="table table-striped table-bordered table-hover allajaxlist" id="headerTable">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('module'); ?></th>
                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                            <th><?php echo $this->lang->line('ipd_no'); ?></th>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('payment_mode'); ?></th>
                            <th><?php echo $this->lang->line('payment_date'); ?></th>
                            <th class="text-end rtl-text-start"><?php echo $this->lang->line('payment_amount') . "(" . $currency_symbol . ")"; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($charge_payment_data)) {
                            $grand_total_charge  = 0;
                            $grand_total_payment = 0;
                            foreach ($charge_payment_data as $charge_payment) {
                                $bill_prefix = "";
                                if ($charge_payment->module == 'Pathology') {
                                    $bill_prefix = $this->customlib->getSessionPrefixByType('pathology_billing');
                                } else if ($charge_payment->module == 'Pharmacy') {
                                    $bill_prefix = $this->customlib->getSessionPrefixByType('pharmacy_billing');
                                } else if ($charge_payment->module == 'Radiology') {
                                    $bill_prefix = $this->customlib->getSessionPrefixByType('radiology_billing');
                                } else if ($charge_payment->module == 'Ambulance') {
                                    $bill_prefix = $this->customlib->getSessionPrefixByType('ambulance_call_billing');
                                } else if ($charge_payment->module == 'Blood Bank') {
                                    $bill_prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing');
                                }
                        ?>
                        <tr>
                            <td class="text-nowrap"><?php echo $charge_payment->module; ?></td>
                            <td class="text-nowrap"><?php if (isset($charge_payment->opd_id)) { echo $this->customlib->getSessionPrefixByType('opd_no') . $charge_payment->opd_id; } ?></td>
                            <td class="text-nowrap"><?php if (isset($charge_payment->ipd_id)) { echo $this->customlib->getSessionPrefixByType('ipd_no') . $charge_payment->ipd_id; } ?></td>
                            <td class="text-nowrap"><?php if (isset($charge_payment->bill_no)) { echo $bill_prefix . $charge_payment->bill_no; } ?></td>
                            <td>
                                <ul class="list-unstyled">
                                    <?php foreach ($charge_payment->payments as $payment) { ?>
                                        <li><?php
                                            if ($payment->payment_mode) {
                                                echo $this->lang->line(strtolower($payment->payment_mode));
                                                if ($payment->payment_mode == "Cheque") {
                                                    echo " No. " . $payment->cheque_no . "<br />" . $this->customlib->YYYYMMDDTodateFormat($payment->cheque_date);
                                                }
                                            }
                                        ?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-unstyled">
                                    <?php foreach ($charge_payment->payments as $payment) { ?>
                                        <li><?= $this->customlib->YYYYMMDDHisTodateFormat($payment->payment_date, $this->customlib->getHospitalTimeFormat());
                                            if ($payment->payment_mode == "Cheque") { echo "<br /><br />"; } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td class="text-end rtl-text-start">
                                <ul class="list-unstyled">
                                    <?php
                                    $total = 0;
                                    foreach ($charge_payment->payments as $payment) { ?>
                                        <li><?php
                                            $total += $payment->amount;
                                            echo $payment->amount;
                                            if ($payment->payment_mode == "Cheque") { echo "<br /><br />"; }
                                        ?></li>
                                    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-nowrap"><b><?php echo $this->lang->line("total_charge"); ?>: </b>
                                <b><?php
                                $charge = ($charge_payment->charge == '') ? "0" : $charge_payment->charge;
                                echo $currency_symbol . '' . $charge;
                                ?></b>
                            </td>
                            <td class="text-end rtl-text-start text-nowrap"><b><?= $this->lang->line("total_payment"); ?>: </b><b><?php echo $currency_symbol . '' . amountFormat($total); ?></b></td>
                            <?php $grand_total_charge += $charge; $grand_total_payment += $total; ?>
                        </tr>
                        <?php } // closes foreach
                        if (isset($grand_total_charge) && isset($grand_total_payment)) { ?>
                        <tr>
                            <td colspan="4"></td>
                            <td class="text-nowrap"><b><?php echo $this->lang->line("refund"); ?>: </b>
                                <b><?php
                                if (isset($total_refund_amount->payment_amount) && $total_refund_amount->payment_amount == '') {
                                    echo $currency_symbol . "0";
                                } else {
                                    echo $currency_symbol . '' . amountFormat($total_refund_amount->payment_amount);
                                }
                                ?></b>
                            </td>
                            <td class="text-nowrap"><b><?php echo $this->lang->line("grand_total_charge"); ?>: </b>
                                <b><?php echo ($grand_total_charge == '') ? $currency_symbol . "0" : $currency_symbol . '' . amountFormat($grand_total_charge); ?></b>
                            </td>
                            <td class="text-end rtl-text-start text-nowrap"><b><?php echo $this->lang->line("grand_total_payment"); ?>: </b>
                                <b><?php echo ($grand_total_payment == '') ? $currency_symbol . "0" : $currency_symbol . '' . amountFormat($grand_total_payment); ?></b>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr><td colspan="7"><?php echo $this->session->flashdata('no_record'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function printDiv() {
        var case_reference_id = document.getElementById('case_reference_id').value;
        if (!case_reference_id) { return; }
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientbillreport_print',
            type: 'POST',
            data: { case_reference_id: case_reference_id },
            success: function (result) {
                popup(result);
            }
        });
    }

    function exportToExcel() {
        var uri      = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
        var base64   = function (s) { return window.btoa(unescape(encodeURIComponent(s))); };
        var format   = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }); };
        var tab      = document.getElementById('headerTable');
        var colCount = tab.rows.length > 0 ? tab.rows[0].cells.length : 7;
        var title    = document.getElementById('printhead').textContent.trim();
        var tab_text = '<tr><td colspan="' + colCount + '" style="text-align:center;font-weight:bold;font-size:14pt;">' + title + '</td></tr><tr>';
        for (var j = 0; j < tab.rows.length; j++) {
            tab_text += tab.rows[j].innerHTML + "</tr>";
        }
        var ctx  = { worksheet: 'Worksheet', table: tab_text };
        var link = document.createElement("a");
        link.download = "Patient Bill Report.xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }
</script>

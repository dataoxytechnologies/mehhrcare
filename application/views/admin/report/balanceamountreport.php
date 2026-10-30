<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_finance'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('balance_amount_report'); ?></h3>
            </div>
            <div class="card-body pb-0">
                <form id="form111" action="<?php echo base_url('admin/report/balanceamountreport'); ?>" method="post" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("select_head"); ?></label>
                                <select class="form-control w-100" name="modules_type" id="modules_type">
                                    <?php foreach ($modules as $key => $search) {
                                        if ($key == "income" || $key == "expense" || $key == "payroll_report" || $key == "appointment") { continue; } ?>
                                        <option value="<?php echo $key ?>" <?php if ((isset($modules_type)) && ($modules_type == $key)) { echo "selected"; } ?>><?php echo $search ?></option>
                                    <?php } ?>
                                    <option value="blood_component" <?php if ((isset($modules_type)) && ($modules_type == "blood_component")) { echo "selected"; } ?>>Blood Component</option>
                                </select>
                                <span class="text-danger" id="error_modules_staff"><?php echo form_error('modules_staff'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line("patient_name"); ?></label>
                                <select class="form-control patient_list_ajax" id="addpatient_id" name="patient_id" onchange="set_patient()">
                                    <option><?php echo $patient_name; ?></option>
                                </select>
                                <input id="patient_id" name="patient_id" type="hidden" class="form-control" value="<?php echo $patient_id; ?>" />
                                <input id="patient_name" name="patient_name" type="hidden" class="form-control" value="<?php echo $patient_name; ?>" />
                                <span class="text-danger" id="error_patient_name"><?php echo form_error('search_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3 pe-md-3">
                            <div class="mb-3">
                                <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                    <i class="fa fa-search"></i>
                                    <?php echo $this->lang->line('search'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-0">
                <div class="download_label"><?php echo $this->lang->line('balance_amount_report'); ?></div>
                <table class="table table-striped table-bordered table-hover example" id="testreport" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('balance_amount_report'); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>
                            <th><?php echo $this->lang->line('case_id'); ?></th>
                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                            <th><?php echo $this->lang->line('generated_by'); ?></th>
                            <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('tax') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($balance_data)) {
                            $total              = 0;
                            $discount           = 0;
                            $tax                = 0;
                            $net_amount         = 0;
                            $paid_amount        = 0;
                            $balance            = 0;
                            $discount_percentage = 0;
                            $tax_percentage     = 0;
                            foreach ($balance_data as $key => $value) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType($value['prefix_type']) . $value['bill_no']; ?></td>
                            <td><?php echo $value['case_id']; ?></td>
                            <td><?php echo $value['patient_name'] . " (" . $value['patient_id'] . ")"; ?></td>
                            <td><?php echo $value['name'] . " " . $value['surname'] . " (" . $value['employee_id'] . ")"; ?></td>
                            <td><?php echo $value['doctor_name']; ?></td>
                            <td class="text-end"><?php echo amountFormat($value['total']); $total += $value['total']; ?></td>
                            <td class="text-end"><?php
                                $discount_percentage = ((float)$value['total'] != 0) ? ((float)$value['discount'] * 100) / (float)$value['total'] : 0;
                                echo amountFormat($value['discount']) . " (" . amountFormat($discount_percentage) . "%)";
                                $discount += $value['discount'];
                            ?></td>
                            <td class="text-end"><?php
                                $tax_percentage = ((float)$value['total'] != 0) ? ((float)$value['tax'] * 100) / ((float)$value['total'] - (float)$value['discount']) : 0;
                                echo amountFormat($value['tax']) . " (" . amountFormat($tax_percentage) . "%)";
                                $tax += $value['tax'];
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount']); $net_amount += $value['net_amount']; ?></td>
                            <td class="text-end"><?php echo amountFormat($value['paid_amount'] - $value['refund_amount']); $paid_amount += $value['paid_amount'] - $value['refund_amount']; ?></td>
                            <td class="text-end"><?php echo amountFormat($value['net_amount'] - $value['paid_amount']); $balance += ($value['net_amount'] - $value['paid_amount']); ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?php echo $this->lang->line('total_amount'); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($total, 2, '.', ''); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($discount, 2, '.', ''); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($tax, 2, '.', ''); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($net_amount, 2, '.', ''); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($paid_amount, 2, '.', ''); ?></th>
                            <th class="text-end"><?php echo $currency_symbol . number_format($balance, 2, '.', ''); ?></th>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.patient_list_ajax').select2({
            minimumInputLength: 2,
            ajax: {
                url: "<?php echo base_url('admin/patient/getPatientListAjax'); ?>",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    $('#case_reference_idd').val('');
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            }
        });
    });

    function set_patient() {
        var patient_id   = $("#addpatient_id").val();
        var patient_name = $("#addpatient_id option:selected").text();
        $("#patient_id").val(patient_id);
        $("#patient_name").val(patient_name);
    }
</script>

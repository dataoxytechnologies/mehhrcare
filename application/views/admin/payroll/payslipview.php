<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="sh-payslip" id="html-2-pdfwrapper">

    <?php if (!empty($print_details[0]['print_header'])) { ?>
    <div class="sh-payslip-header">
        <img src="<?php
            if (!empty($print_details[0]['print_header'])) {
                echo $this->media_storage->getImageURL($print_details[0]['print_header']);
            }
        ?>" class="img-fluid sh-avatar-cover" >
    </div>
    <?php } ?>

    <div class="sh-payslip-period">
        <?php echo $this->lang->line('payslip_for_the_period_of'); ?> <?php echo $this->lang->line($result["month"]); ?> <?php echo $result["year"]; ?>
    </div>

    <div class="sh-payslip-meta">
        <span class="sh-payslip-meta-num"><?php echo $this->lang->line('payslip'); ?> #<?php echo $result["id"]; ?></span>
        <span class="sh-payslip-meta-date">
            <?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('date'); ?>:
            <?php if (!empty($result['payment_date'])) { echo $this->customlib->YYYYMMDDTodateFormat($result['payment_date']); } ?>
        </span>
    </div>

    <div class="sh-payslip-empinfo">
        <div class="sh-payslip-ei-grid">
            <div class="sh-payslip-ei-item">
                <span class="sh-payslip-ei-label"><?php echo $this->lang->line('staff_id'); ?></span>
                <span class="sh-payslip-ei-value"><?php echo $result["employee_id"]; ?></span>
            </div>
            <div class="sh-payslip-ei-item">
                <span class="sh-payslip-ei-label"><?php echo $this->lang->line("name"); ?></span>
                <span class="sh-payslip-ei-value"><?php echo $result["name"] . " " . $result["surname"]; ?></span>
            </div>
            <div class="sh-payslip-ei-item">
                <span class="sh-payslip-ei-label"><?php echo $this->lang->line('department'); ?></span>
                <span class="sh-payslip-ei-value"><?php echo $result["department"]; ?></span>
            </div>
            <div class="sh-payslip-ei-item">
                <span class="sh-payslip-ei-label"><?php echo $this->lang->line('designation'); ?></span>
                <span class="sh-payslip-ei-value"><?php echo $result["designation"]; ?></span>
            </div>
        </div>
    </div>

    <div class="sh-payslip-body">
        <table class="sh-payslip-table">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('earning'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                    <th><?php echo $this->lang->line('deduction'); ?></th>
                    <th class="text-end"><?php echo $this->lang->line('amount'); ?> (<?php echo $currency_symbol; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $j = 0;
                foreach ($allowance as $key => $value) { ?>
                <tr>
                    <?php if (array_key_exists($j, $positive_allowance)) { ?>
                        <td><?php echo $positive_allowance[$j]["allowance_type"]; ?></td>
                        <td class="text-end"><?php echo amountFormat($positive_allowance[$j]["amount"]); ?></td>
                    <?php } else { ?><td></td><td></td><?php } ?>
                    <?php if (array_key_exists($j, $negative_allowance)) { ?>
                        <td><?php echo $negative_allowance[$j]["allowance_type"]; ?></td>
                        <td class="text-end"><?php echo amountFormat($negative_allowance[$j]["amount"]); ?></td>
                    <?php } else { ?><td></td><td></td><?php } ?>
                </tr>
                <?php $j++; } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th><?php echo $this->lang->line('total'); ?> <?php echo $this->lang->line('earning'); ?></th>
                    <th class="text-end"><?php echo $result["total_allowance"]; ?></th>
                    <th><?php echo $this->lang->line('total'); ?> <?php echo $this->lang->line('deduction'); ?></th>
                    <th class="text-end"><?php echo amountFormat($result["total_deduction"]); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="sh-payslip-summary">
        <?php if (!empty($result["payment_mode"])) { ?>
        <div class="sh-payslip-sum-row">
            <span><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('mode'); ?></span>
            <span>
                <?php echo $payment_mode[$result["payment_mode"]]; ?>
                <?php if ($result["payment_mode"] == 'Cheque') {
                    echo "&nbsp;&nbsp;<b>Cheque No.&nbsp;</b>" . $result["cheque_no"];
                    echo "&nbsp;&nbsp;<b>Cheque Date.</b>&nbsp;" . $this->customlib->YYYYMMDDTodateFormat($result["cheque_date"]);
                    if ($result["attachment"] != "") { ?>
                        <a href="<?php echo base_url() . "admin/payroll/download/" . $result["id"]; ?>" class="btn btn-secondary btn-sm ms-1" title="<?= $this->lang->line('download') ?>"><i class="fa fa-download"></i></a>
                    <?php }
                } ?>
            </span>
        </div>
        <?php } ?>
        <div class="sh-payslip-sum-row">
            <span><?php echo $this->lang->line('basic_salary'); ?> (<?php echo $currency_symbol; ?>)</span>
            <span><?php echo $result["basic"]; ?></span>
        </div>
        <div class="sh-payslip-sum-row">
            <span><?php echo $this->lang->line('gross_salary'); ?> (<?php echo $currency_symbol; ?>)</span>
            <span><?php echo amountFormat($result["basic"] + $result["total_allowance"] - $result["total_deduction"]); ?></span>
        </div>
        <?php if (!empty($result["tax"])) {
            $gross = $result["basic"] + $result["total_allowance"] - $result["total_deduction"];
            $tax_amount = ($gross * $result["tax"]) / 100; ?>
        <div class="sh-payslip-sum-row">
            <span><?php echo $this->lang->line('tax'); ?> (<?php echo $currency_symbol; ?>)</span>
            <span><?php echo amountFormat($tax_amount) . " (" . $result["tax"] . "% )"; ?></span>
        </div>
        <?php } ?>
        <div class="sh-payslip-sum-row sh-payslip-net">
            <span><?php echo $this->lang->line('net_salary'); ?> (<?php echo $currency_symbol; ?>)</span>
            <span><?php echo $result["net_salary"]; ?></span>
        </div>
    </div>

</div>

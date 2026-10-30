<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="print-area">

    <?php if (!empty($print_details[0]['print_header'])) { ?>
    <div class="mx-2 mt-2">
        <img src="<?php echo $this->media_storage->getImageURL($print_details[0]['print_header']); ?>" class="img-fluid d-block sh-print-header-img">
    </div>
    <?php } ?>

    <div class="sh-form-card mx-2 mt-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_details'); ?></span>
        </div>
        <div class="sh-info-grid">
            <div class="row g-0">
                <div class="col-12 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('patient'); ?></span>
                    <span class="sh-info-value highlight"><?php echo composePatientName($patient['patient_name'], $patient['patient_id']); ?></span>
                </div>
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('case_id'); ?></span>
                    <span class="sh-info-value"><?php echo html_escape($case_id); ?></span>
                </div>
                <?php if ($patient['date'] !== '') { ?>
                <div class="col-6 col-md-4 sh-info-item">
                    <span class="sh-info-label"><?php echo $this->lang->line('admission_date'); ?></span>
                    <span class="sh-info-value"><?php echo $this->customlib->YYYYMMDDTodateFormat($patient['date']); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="sh-form-card mx-2">
        <div class="sh-card-header">
            <span class="sh-card-header-title"><i class="fa fa-list me-1"></i> <?php echo $this->lang->line('charges'); ?></span>
        </div>
        <div class="p-2">
            <div class="table-responsive">
                <table class="print-table">
                    <thead>
                        <tr class="line">
                            <td>#</td>
                            <td><?php echo $this->lang->line('description'); ?></td>
                            <td><?php echo $this->lang->line('qty'); ?></td>
                            <td width="12%" class="text-end"><?php echo $this->lang->line('discount'); ?></td>
                            <td width="12%" class="text-end"><?php echo $this->lang->line('tax'); ?></td>
                            <td class="text-end text-rtl-left"><?php echo $this->lang->line('amount') . "(" . $currency_symbol . ")"; ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total_tax = $apply_charge = $amount = $total_discount = $dis_amt = 0; $s = 1; $discount_amount = 0;
                        foreach ($charge_details as $key => $value) {
                            $discount_amount = ((($value['apply_charge'] * $value['discount_percentage']) / 100));
                            $tax_amount      = (($value['apply_charge'] - $discount_amount) * $value['tax'] / 100);
                            $taxamount       = amountFormat($tax_amount);
                            if ($value['tax'] > 0) {
                                $tax = $taxamount;
                            } else {
                                $tax = 0;
                            }
                        ?>
                        <tr>
                            <td><?php echo $s++; ?></td>
                            <td><?php echo $value['charge_name'] ?><br><?php echo $value['note']; ?></td>
                            <td><?php echo $value['qty'] ?></td>
                            <td class="text-end"><?php
                                echo $dis_amt = amountFormat((($value['apply_charge'] * $value['discount_percentage']) / 100)) . ' (' . $value['discount_percentage'] . '%)';
                                $total_discount += amountFormat((($value['apply_charge'] * $value['discount_percentage']) / 100));
                            ?></td>
                            <td class="text-end"><?php echo amountFormat($tax) . ' (' . $value['tax'] . '%)'; ?></td>
                            <td class="text-end text-rtl-left"><?php echo amountFormat($value['amount']) ?></td>
                        </tr>
                        <?php
                            $apply_charge += $value['apply_charge'];
                            $amount       += $value['amount'];
                            $total_tax    += $tax;
                        } ?>
                        <tr>
                            <td colspan="5" class="text-end thick-line text-rtl-left"><?php echo $this->lang->line('net_amount'); ?></td>
                            <td class="text-end thick-line text-rtl-left"><?php echo $currency_symbol . amountFormat($apply_charge); ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('discount'); ?></td>
                            <td class="text-end no-line text-rtl-left"><?php
                                $discount_per = ($total_discount * 100) / $apply_charge;
                                echo $currency_symbol . amountFormat($total_discount) . ' (' . amountFormat($discount_per) . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('tax'); ?></td>
                            <td class="text-end no-line text-rtl-left"><?php
                                $tax_per = $total_tax * 100 / ($apply_charge - $total_discount);
                                echo $currency_symbol . amountFormat($total_tax) . ' (' . amountFormat($tax_per) . '%)';
                            ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('total'); ?></td>
                            <td class="text-end text-rtl-left"><?php echo $currency_symbol . amountFormat($amount); ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('paid'); ?></td>
                            <td class="text-end no-line text-rtl-left"><?php echo $currency_symbol . amountFormat($paid_amount['total_pay']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('due'); ?>:</td>
                            <td class="text-end text-rtl-left"><?php
                                if ($refund > 0) {
                                    echo $balance = amountFormat($refund + ($amount - $paid_amount['total_pay']));
                                } else {
                                    echo $currency_symbol . amountFormat($amount - $paid_amount['total_pay']);
                                }
                            ?></td>
                        </tr>
                        <?php if (($paid_amount['total_pay'] > $amount) || ($refund > 0)) { ?>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"><?php echo $this->lang->line('refund'); ?>:</td>
                            <td class="text-end text-rtl-left" id="replace_amount"><?php if ($refund > 0) { echo $currency_symbol . amountFormat($refund); } ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"></td>
                            <td class="text-end text-rtl-left"><a href="javascript:void(0);" data-loading-text='' class="btn btn-primary btn-sm payment_refund"><i class="fa fa-money"></i> <?php echo $this->lang->line('add_refund'); ?></a></td>
                        </tr>
                        <?php } ?>
                        <?php if (!empty($discharge_card)) { ?>
                        <tr>
                            <td colspan="5" class="text-end no-line text-rtl-left"></td>
                            <td class="text-end text-rtl-left"><a href="javascript:void(0);" data-loading-text='' class="btn btn-primary btn-sm view_dischargecard" data-type="bill" data-recordId="<?php echo $discharge_card['id']; ?>" data-case_id="<?php echo $case_id; ?>"><i class="fa fa-money"></i> <?php echo $this->lang->line('generate_discharge_card'); ?></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

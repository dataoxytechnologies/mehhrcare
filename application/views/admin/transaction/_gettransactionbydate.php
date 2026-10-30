<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="sh-form-card mb-0">
    <div class="sh-card-header">
        <span class="sh-card-header-title"><?php echo $this->lang->line('collection_list'); ?></span>
    </div>
    <div class="p-2">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover example mb-0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                        <th><?php echo $this->lang->line('collected_by'); ?></th>
                        <th class="text-end"><?php echo $this->lang->line('amount') . '(' . $currency_symbol . ')'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $dt_key => $dt_value) { ?>
                    <tr>
                        <td><?php echo html_escape($this->customlib->getSessionPrefixByType('transaction_id') . $dt_value->id); ?></td>
                        <td><?php echo html_escape($this->customlib->YYYYmmddTodateformat($dt_value->payment_date)); ?></td>
                        <td><?php if ($dt_value->payment_mode) { echo $this->lang->line(strtolower($dt_value->payment_mode)); } ?></td>
                        <td><?php echo html_escape(composeStaffNameByString($dt_value->name, $dt_value->surname, $dt_value->employee_id)); ?></td>
                        <td class="text-end"><?php echo amountFormat($dt_value->amount); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

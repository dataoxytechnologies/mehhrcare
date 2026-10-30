<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$date_format     = $this->customlib->getHospitalDateFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_finance'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('income_expense_balance_report'); ?></h3>
            </div>
            <div class="card-body pb-0">
                <form id="form_iebr" action="<?php echo base_url('admin/report/incomeexpensebalancereport'); ?>" method="post" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('search_type'); ?></label>
                                <select name="search_type" id="search_type" class="form-control" onchange="showdate(this.value)">
                                    <option value="today" <?php echo ($search_type == 'today') ? 'selected' : ''; ?>><?php echo $this->lang->line('today'); ?></option>
                                    <option value="this_week" <?php echo ($search_type == 'this_week') ? 'selected' : ''; ?>><?php echo $this->lang->line('this_week'); ?></option>
                                    <option value="last_week" <?php echo ($search_type == 'last_week') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_week'); ?></option>
                                    <option value="this_month" <?php echo ($search_type == 'this_month') ? 'selected' : ''; ?>><?php echo $this->lang->line('this_month'); ?></option>
                                    <option value="last_month" <?php echo ($search_type == 'last_month') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_month'); ?></option>
                                    <option value="last_3_month" <?php echo ($search_type == 'last_3_month') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_3_month'); ?></option>
                                    <option value="last_6_month" <?php echo ($search_type == 'last_6_month') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_6_month'); ?></option>
                                    <option value="last_12_month" <?php echo ($search_type == 'last_12_month') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_12_month'); ?></option>
                                    <option value="last_year" <?php echo ($search_type == 'last_year') ? 'selected' : ''; ?>><?php echo $this->lang->line('last_year'); ?></option>
                                    <option value="this_year" <?php echo ($search_type == 'this_year') ? 'selected' : ''; ?>><?php echo $this->lang->line('this_year'); ?></option>
                                    <option value="all_time" <?php echo ($search_type == 'all_time') ? 'selected' : ''; ?>><?php echo $this->lang->line('all_time'); ?></option>
                                    <option value="period" <?php echo ($search_type == 'period') ? 'selected' : ''; ?>><?php echo $this->lang->line('period'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3" id="fromdate" style="display:<?php echo ($search_type == 'period') ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_from'); ?> <small class="req">*</small></label>
                                <input type="text" class="form-control dateformat" name="date_from" id="date_from" autocomplete="off"
                                    value="<?php echo html_escape($date_from); ?>" />
                                <span id="err_date_from" class="text-danger" style="font-size:12px;display:none;"><?php echo $this->lang->line('date_from'); ?> is required</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3" id="todate" style="display:<?php echo ($search_type == 'period') ? 'block' : 'none'; ?>;">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_to'); ?> <small class="req">*</small></label>
                                <input type="text" class="form-control dateformat" name="date_to" id="date_to" autocomplete="off"
                                    value="<?php echo html_escape($date_to); ?>" />
                                <span id="err_date_to" class="text-danger" style="font-size:12px;display:none;"><?php echo $this->lang->line('date_to'); ?> is required</span>
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
                <div class="download_label"><?php echo $this->lang->line('income_expense_balance_report'); ?></div>
                <table class="table table-striped table-bordered table-hover example" id="iebr_table" cellspacing="0" width="100%"
                    data-export-title="<?php echo $this->lang->line('income_expense_balance_report'); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('type'); ?></th>
                            <th class="text-end"><?php echo $this->lang->line('income_in') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('expense_out') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-end"><?php echo $this->lang->line('overall_balance') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_income  = 0;
                        $total_expense = 0;

                        if (!empty($report_data)) {
                            foreach ($report_data as $row) {
                                $income_in   = (float)$row['income_in'];
                                $expense_out = (float)$row['expense_out'];
                                $total_income  += $income_in;
                                $total_expense += $expense_out;
                        ?>
                        <tr>
                            <td><?php echo ($row['record_date'] != '' && $row['record_date'] != '0000-00-00') ? $this->customlib->YYYYMMDDTodateFormat($row['record_date']) : '-'; ?></td>
                            <td><?php echo html_escape($row['inc_exp_head']); ?></td>
                            <td class="text-end"><?php echo amountFormat($income_in); ?></td>
                            <td class="text-end"><?php echo amountFormat($expense_out); ?></td>
                            <?php $row_balance = $income_in - $expense_out; ?>
                            <td class="text-end">
                                <?php if ($row_balance < 0): ?>
                                    <span class="text-danger"><?php echo amountFormat($row_balance); ?></span>
                                <?php else: ?>
                                    <?php echo amountFormat($row_balance); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } } ?>
                        <?php if (!empty($report_data)) { $net_balance = $total_income - $total_expense; ?>
                        <tr>
                            <td><strong><?php echo $this->lang->line('total_amount'); ?></strong></td>
                            <td></td>
                            <td class="text-end"><strong><?php echo $currency_symbol . number_format($total_income, 2, '.', ''); ?></strong></td>
                            <td class="text-end"><strong><?php echo $currency_symbol . number_format($total_expense, 2, '.', ''); ?></strong></td>
                            <td class="text-end">
                                <strong><?php echo $currency_symbol . number_format($net_balance, 2, '.', ''); ?></strong>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function showdate(value) {
    if (value == 'period') {
        $('#fromdate').removeClass('d-none');
        $('#todate').removeClass('d-none');
    } else {
        $('#fromdate').addClass('d-none');
        $('#todate').addClass('d-none');
        $('#err_date_from').hide();
        $('#err_date_to').hide();
    }
}

$('#form_iebr').on('submit', function(e) {
    if ($('#search_type').val() == 'period') {
        var valid = true;
        if ($('#date_from').val().trim() == '') {
            $('#err_date_from').show();
            valid = false;
        } else {
            $('#err_date_from').hide();
        }
        if ($('#date_to').val().trim() == '') {
            $('#err_date_to').show();
            valid = false;
        } else {
            $('#err_date_to').hide();
        }
        if (!valid) { e.preventDefault(); }
    }
});

$(document).ready(function () {
    var date_format = '<?php echo strtr($date_format, ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']); ?>';
});
</script>

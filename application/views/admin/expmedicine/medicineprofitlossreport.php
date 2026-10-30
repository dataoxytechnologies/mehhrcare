<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_pharmacy'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('medicine_profit_loss_report'); ?></h3>
            </div>
            <form id="form1" action="" method="post">
                <div class="card-body row pb-0">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('search_type'); ?></label> <small class="req"> *</small>
                            <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($searchlist as $key => $search) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $search; ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger" id="error_search_type"></span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('medicine_category'); ?></label>
                            <select name="medicine_category" class="form-control select2">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo html_escape($dvalue["medicine_category"]); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 d-none" id="fromdate">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('date_from'); ?></label> <small class="req"> *</small>
                            <input id="date_from" name="date_from" type="text" class="form-control date"
                                   value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 d-none" id="todate">
                        <div class="mb-3">
                            <label><?php echo $this->lang->line('date_to'); ?></label> <small class="req"> *</small>
                            <input id="date_to" name="date_to" type="text" class="form-control date"
                                   value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-auto d-flex align-items-end ps-md-3">
                        <div class="mb-3">
                            <button type="submit" name="search" value="search_filter" class="btn btn-primary d-inline-flex align-items-center gap-1 py-2">
                                <i class="fa fa-search"></i>
                                <?php echo $this->lang->line('search'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body table-responsive pt-0">
                <div class="download_label"><?php echo $this->lang->line('medicine_profit_loss_report'); ?></div>
                    <table class="table table-striped table-bordered table-hover allajaxlist"
                           data-export-title="<?php echo $this->lang->line('medicine_profit_loss_report'); ?>">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('medicine_name'); ?></th>
                                <th><?php echo $this->lang->line('medicine_category'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('total_purchased'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('avg_purchase_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end"><?php echo $this->lang->line('total_sold'); ?></th>
                                <th class="text-end"><?php echo $this->lang->line('avg_sale_price'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end"><?php echo $this->lang->line('revenue'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end"><?php echo $this->lang->line('cost'); ?> (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end">P&amp;L (<?php echo $currency_symbol; ?>)</th>
                                <th class="text-end">P&amp;L %</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.select2').select2();
        emptyDatatable('allajaxlist', 'data');
    });

    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').removeClass('d-none');
            $('#todate').removeClass('d-none');
        } else {
            $('#fromdate').addClass('d-none');
            $('#todate').addClass('d-none');
        }
    }

    $(document).ready(function () {
        $('#form1').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('search', 'search_filter');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/expmedicine/medicineprofitlosscheckvalidation',
                type: "POST",
                data: formData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        $.each(data.error, function (key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        $("#error_search_type").html('');
                        // Right-align numeric/amount body cells (cols 2-9) so they match the text-end headers.
                        initDatatable('allajaxlist', 'admin/expmedicine/medicineprofitlossreports/', data.param, [], 100,
                            [{ "bSortable": false, "aTargets": [-1] },
                             { "sClass": "text-end", "aTargets": [2, 3, 4, 5, 6, 7, 8, 9] }]);
                    }
                }
            });
        });
    });
</script>

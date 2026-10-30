<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <?php $this->load->view('admin/report/_finance'); ?>
            <div class="card-header ptbnull"></div>
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->lang->line('expense_group_report'); ?></h3>
                <div class="d-flex gap-2 align-items-center flex-wrap float-end"></div>
            </div>
            <div class="card-body pb-0">
                <form role="form" id="form" action="<?php echo site_url('admin/expense/getgroupreportparam') ?>" method="post">
                    <div class="row">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('search_type'); ?></label>
                                <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($searchlist as $key => $search) { ?>
                                        <option value="<?php echo $key ?>" <?php if ((isset($search_type)) && ($search_type == $key)) { echo "selected"; } ?>><?php echo $search ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 d-none" id="fromdate">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                <input id="date_from" name="date_from" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>" />
                                <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 d-none" id="todate">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                <input id="date_to" name="date_to" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>" />
                                <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                                <label><?php echo $this->lang->line('search_expense_head'); ?></label>
                                <select class="form-control" name="head">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($headlist as $heads) { ?>
                                        <option value="<?php echo $heads['id'] ?>" <?php if ((isset($head_id)) && ($head_id == $heads['id'])) { echo "selected"; } ?>><?php echo $heads['exp_category'] ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('search_type'); ?></span>
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
            <div class="card-body table-responsive pt-0" id="transfee">
                <table class="table table-striped table-bordered table-hover expense-list" data-export-title="<?php echo $this->lang->line('expense_group_report'); ?>">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('expense_head'); ?></th>
                            <th><?php echo $this->lang->line('expense_id'); ?></th>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('invoice_number'); ?></th>
                            <?php if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) { ?>
                                    <th><?php echo $fields_value->name; ?></th>
                                <?php }
                            } ?>
                            <th class="text-end"><?php echo $this->lang->line('amount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';

    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').removeClass('d-none');
            $('#todate').removeClass('d-none');
        } else {
            $('#fromdate').addClass('d-none');
            $('#todate').addClass('d-none');
        }
    }
</script>

<script>
    $(document).ready(function () {
        initDatatable('expense-list', 'admin/expense/dtexpensegroupreport');

        document.querySelectorAll('.detail_popover').forEach(function (el) {
            new bootstrap.Popover(el, {
                placement: 'right',
                trigger: 'hover',
                container: 'body',
                html: true,
                content: function () {
                    var td = el.closest('td');
                    var inner = td ? td.querySelector('.fee_detail_popover') : null;
                    return inner ? inner.innerHTML : '';
                }
            });
        });

        $(document).on('submit', '#form', function (e) {
            e.preventDefault();
            var $this = $(this).find("button[type=submit]:focus");
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data,
                beforeSend: function () {
                    $('[id^=error]').html("");
                    $this.btnLoading();
                },
                success: function (response) {
                    if (!response.status) {
                        $.each(response.error, function (key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        initDatatable('expense-list', 'admin/expense/dtexpensegroupreport', response.params);
                    }
                },
                error: function () {
                    $this.btnReset();
                },
                complete: function () {
                    $this.btnReset();
                }
            });
        });
    });
</script>

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>


<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- Card Header -->
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-semibold sh-fs-95">
                    <?php echo $this->lang->line('generate_payroll_for'); ?> :
                    <span style="color:var(--link)"><?php echo $this->lang->line(strtolower($month)); ?></span>
                </h5>
                <a href="<?php echo base_url() ?>admin/payroll" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <!-- Staff Profile -->
                    <div class="col-md-8">
                        <div class="h-100 border rounded p-3 sh-border-color" >
                            <div class="d-flex gap-3 align-items-start">
                                <?php
                                $image        = $result['image'];
                                $pr_has_image = !empty($image) && strpos($image, 'no_image') === false;
                                $file         = $pr_has_image ? $image : "no_image.png";
                                if (!$pr_has_image) {
                                    $pr_full  = trim(($result['name'] ?? '') . ' ' . ($result['surname'] ?? ''));
                                    $pr_parts = preg_split('/\s+/', $pr_full, -1, PREG_SPLIT_NO_EMPTY);
                                    $pr_inits = count($pr_parts) === 0 ? '?' : (count($pr_parts) === 1
                                        ? mb_strtoupper(mb_substr($pr_parts[0], 0, 1))
                                        : mb_strtoupper(mb_substr($pr_parts[0], 0, 1) . mb_substr($pr_parts[count($pr_parts) - 1], 0, 1)));
                                }
                                ?>
                                <?php if ($pr_has_image): ?>
                                <img class="payroll-staff-img"
                                     src="<?php echo $this->media_storage->getImageURL("uploads/staff_images/" . $file); ?>"
                                     alt="<?php echo html_escape($result['name']); ?>">
                                <?php else: ?>
                                <div class="payroll-staff-initials"><?php echo html_escape($pr_inits); ?></div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <table class="payroll-info-table">
                                        <tbody>
                                            <tr>
                                                <th><?php echo $this->lang->line("staff_name"); ?></th>
                                                <td><?php echo html_escape($result["name"] . " " . $result["surname"]); ?></td>
                                                <th><?php echo $this->lang->line('staff_id'); ?></th>
                                                <td><?php echo html_escape($result["employee_id"]); ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('staff_phone'); ?></th>
                                                <td><?php echo html_escape($result["contact_no"]); ?></td>
                                                <th><?php echo $this->lang->line('staff_email'); ?></th>
                                                <td><?php echo html_escape($result["email"]); ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('staff_epf_no'); ?></th>
                                                <td><?php echo html_escape($result["epf_no"]); ?></td>
                                                <th><?php echo $this->lang->line('staff_role'); ?></th>
                                                <td><?php echo html_escape($result["user_type"]); ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('staff_department'); ?></th>
                                                <td><?php echo html_escape($result["department"]); ?></td>
                                                <th><?php echo $this->lang->line('staff_designation'); ?></th>
                                                <td><?php echo html_escape($result["designation"]); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance -->
                    <div class="col-md-4">
                        <div class="h-100 border rounded overflow-hidden sh-border-color" >
                            <div class="px-3 py-2 border-bottom" style="background:var(--surface-2);border-color:var(--border)!important">
                                <span class="fw-semibold" style="color:var(--ink);font-size:.875rem">
                                    <?php echo $this->lang->line("attendance"); ?>
                                </span>
                            </div>
                            <div class="p-2">
                                <table class="table table-sm table-bordered mb-0 attend-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('month'); ?></th>
                                            <?php foreach ($attendanceType as $key => $value) {
                                                $lang = strtolower($value["type"]); ?>
                                                <th><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line($lang); ?>"><?php echo strip_tags($value["key_value"]); ?></span></th>
                                            <?php } ?>
                                            <th><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('approved_leave'); ?>">V</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($monthAttendance as $attendence_key => $attendence_value) { ?>
                                            <tr>
                                                <td><?php echo $this->lang->line(strtolower(date("F", strtotime($attendence_key)))); ?></td>
                                                <td><?php echo $attendence_value['present']; ?></td>
                                                <td><?php echo $attendence_value['late']; ?></td>
                                                <td><?php echo $attendence_value['absent']; ?></td>
                                                <td><?php echo $attendence_value['half_day']; ?></td>
                                                <td><?php echo $attendence_value['holiday']; ?></td>
                                                <td><?php echo $monthLeaves[date("m", strtotime($attendence_key))]; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /.card-body -->

            <!-- Earning / Deduction / Summary -->
            <form class="form-horizontal" action="<?php echo site_url('admin/payroll/payslip') ?>" method="post" id="employeeform">
                <input type="hidden" name="role" value="<?php echo $result["user_type"] ?>">

                <div class="card-body pt-0">
                    <div class="row g-3">

                        <!-- Earning -->
                        <div class="col-md-4">
                            <div class="border rounded sh-border-color" >
                                <div class="payroll-section-hdr">
                                    <h6><?php echo $this->lang->line('earning'); ?></h6>
                                    <button type="button" onclick="add_more()" class="btn-row-add" title="<?php echo $this->lang->line('add'); ?>">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="p-2 sh-scroll-230">
                                    <table class="w-100" id="tableID">
                                        <tr id="row0">
                                            <td><input type="text" class="form-control" id="allowance_type" name="allowance_type[]" placeholder="<?php echo $this->lang->line('type'); ?>"></td>
                                            <td><input type="text" id="allowance_amount" name="allowance_amount[]" class="form-control" value="0"></td>
                                            <td class="sh-col-32"><button type="button" onclick="delete_row(0)" class="closebtn" autocomplete="off"><i class="fa fa-remove"></i></button></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Deduction -->
                        <div class="col-md-4">
                            <div class="border rounded sh-border-color" >
                                <div class="payroll-section-hdr">
                                    <h6><?php echo $this->lang->line('deduction'); ?></h6>
                                    <button type="button" onclick="add_more_deduction()" class="btn-row-add" title="<?php echo $this->lang->line('add'); ?>">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="p-2 sh-scroll-230">
                                    <table class="w-100" id="tableID2">
                                        <tr id="deduction_row0">
                                            <td><input type="text" id="deduction_type" name="deduction_type[]" class="form-control" placeholder="<?php echo $this->lang->line('type'); ?>"></td>
                                            <td><input type="text" id="deduction_amount" name="deduction_amount[]" class="form-control" value="0"></td>
                                            <td class="sh-col-32"><button type="button" onclick="delete_deduction_row(0)" class="closebtn" autocomplete="off"><i class="fa fa-remove"></i></button></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll Summary -->
                        <div class="col-md-4">
                            <div class="border rounded sh-border-color" >
                                <div class="payroll-section-hdr">
                                    <h6><?php echo $this->lang->line('payroll_summary'); ?> (<?php echo $currency_symbol ?>)</h6>
                                    <button type="button" onclick="add_allowance()" class="btn btn-sm btn-outline-primary ms-auto sh-btn-calc">
                                        <i class="fa fa-calculator me-1"></i><?php echo $this->lang->line('calculate'); ?>
                                    </button>
                                </div>
                                <div>
                                    <div class="summary-row">
                                        <span class="summary-label"><?php echo $this->lang->line('basic_salary'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="basic" id="basic" type="text"
                                                   value="<?php echo !empty($result["basic_salary"]) ? $result["basic_salary"] : '0'; ?>">
                                            <span class="text-danger" id="err"><?php echo form_error('basic'); ?></span>
                                        </div>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label"><?php echo $this->lang->line('earning'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="total_allowance" id="total_allowance" type="text">
                                        </div>
                                    </div>
                                    <div class="summary-row is-deduct">
                                        <span class="summary-label"><?php echo $this->lang->line('deduction'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="total_deduction" id="total_deduction" type="text">
                                        </div>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label"><?php echo $this->lang->line('gross_salary'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="gross_salary" id="gross_salary" value="0" type="text">
                                        </div>
                                    </div>
                                    <div class="summary-row is-deduct">
                                        <span class="summary-label"><?php echo $this->lang->line('tax') . "(%)"; ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="tax_percent" id="tax_percent" value="0" type="text">
                                        </div>
                                    </div>
                                    <div class="summary-row is-deduct">
                                        <span class="summary-label"><?php echo $this->lang->line('tax'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control" name="tax" id="tax" value="0" type="text">
                                        </div>
                                    </div>
                                    <hr class="summary-divider">
                                    <div class="summary-row is-net">
                                        <span class="summary-label"><?php echo $this->lang->line('net_salary'); ?></span>
                                        <div class="summary-value">
                                            <input class="form-control greentest" name="net_salary" id="net_salary" type="text">
                                            <span class="text-danger"><?php echo form_error('net_salary'); ?></span>
                                            <input name="staff_id" value="<?php echo $result["id"]; ?>" type="hidden">
                                            <input name="month"    value="<?php echo $month; ?>"         type="hidden">
                                            <input name="year"     value="<?php echo $year; ?>"          type="hidden">
                                            <input name="status"   value="generated"                     type="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save -->
                        <div class="col-12 text-end">
                            <button type="submit" id="contact_submit" class="btn btn-primary">
                                <i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('save'); ?>
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div><!-- /.card -->
    </div>
</div>

<script type="text/javascript">
    function add_allowance() {
        var basic_pay = $("#basic").val();
        var allowance_type = document.getElementsByName('allowance_type[]');
        var allowance_amount = document.getElementsByName('allowance_amount[]');
        var total_allowance = 0;
        var deduction_type = document.getElementsByName('deduction_type[]');
        var deduction_amount = document.getElementsByName('deduction_amount[]');
        var total_deduction = 0;
        for (var i = 0; i < allowance_amount.length; i++) {
            var inp = allowance_amount[i];
            if (inp.value == '') {
                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }

            total_allowance += parseFloat(inpvalue);
        }

        for (var j = 0; j < deduction_amount.length; j++) {
            var inpd = deduction_amount[j];
            if (inpd.value == '') {
                var inpdvalue = 0;
            } else {
                var inpdvalue = inpd.value;
            }
            total_deduction += parseFloat(inpdvalue);
        }
        var tax_percent = $("#tax_percent").val();

        var gross_salary = parseFloat(basic_pay) + parseFloat(total_allowance) - parseFloat(total_deduction);

        if (tax_percent != '0') {
            var tax = (gross_salary * tax_percent) / 100;
            $("#tax").val(tax.toFixed(2));
        } else {
            var tax = $("#tax").val();
        }

        var net_salary = parseFloat(basic_pay) + parseFloat(total_allowance) - parseFloat(total_deduction) - parseFloat(tax);
        $("#total_allowance").val(total_allowance.toFixed(2));
        $("#total_deduction").val(total_deduction.toFixed(2));
        $("#total_allow").html(total_allowance.toFixed(2));
        $("#total_deduc").html(total_deduction.toFixed(2));
        $("#gross_salary").val(gross_salary.toFixed(2));
        $("#net_salary").val(net_salary.toFixed(2));
    }

    function add_more() {
        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'><td><input type='text' class='form-control' id='allowance_type' name='allowance_type[]' placeholder='<?php echo $this->lang->line("type"); ?>'></td><td><input type='text' class='form-control' id='allowance_amount' name='allowance_amount[]'  value='0'></td><td class='sh-col-32'><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
    }

    function delete_row(id) {
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).remove("");
    }

    function add_more_deduction() {
        var table = document.getElementById("tableID2");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var row = table.insertRow(table_len).outerHTML = "<tr id='deduction_row" + id + "'><td><input type='text' class='form-control' id='deduction_type' name='deduction_type[]' placeholder='<?php echo $this->lang->line("type"); ?>'></td><td><input type='text' id='deduction_amount' name='deduction_amount[]' class='form-control' value='0'></td><td class='sh-col-32'><button type='button' onclick='delete_deduction_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
    }

    function delete_deduction_row(id) {
        var table = document.getElementById("tableID2");
        var rowCount = table.rows.length;
        $("#deduction_row" + id).html("");
    }

    // Init tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
</script>

<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('staff_payroll'); ?></h3>             
                    </div>
                    <form id='form1' action="<?php echo site_url('admin/payroll') ?>"  method="post" accept-charset="utf-8">
                        <div class="card-body">
                            <div class="row"> 
                               <?php  if($this->session->flashdata('msg')){ ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line("staff_role"); ?></label>
                                        <select onchange="getEmployeeName(this.value)" id="role" name="role" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            $role_selected = $this->input->post('role', TRUE) ?: '';
                                            foreach ($classlist as $key => $class) {
                                                ?>
                                                <option value="<?php echo $class["type"] ?>" 
                                                <?php
                                                if ($class["type"] == $role_selected) {
                                                    echo "selected";
                                                }
                                                ?> ><?php print_r($class["type"]) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('month') ?></label>
                                        <select autofocus="" id="class_id" name="month" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($month)) {
                                                $month_selected = date("F", strtotime($month));
                                            } else {
                                                $month_selected = date("F", strtotime("-1 month"));
                                            }
                                            foreach ($monthlist as $m_key => $month_value) {
                                                ?>
                                                <option value="<?php echo $m_key ?>" <?php
                                                if ($month_selected == $m_key) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $this->lang->line(strtolower($month_value)); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label><?php echo $this->lang->line('year'); ?></label>
                                        <?php
                                        if (isset($year)) {
                                            $selected_year = $year;
                                        } else {
                                            $selected_year = date('Y');
                                        }
                                        ?>
                                        <select autofocus="" id="class_id" name="year" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <option  <?php
                                            if (date("Y", strtotime("-1 year")) == $selected_year) {
                                                echo "selected";
                                            }
                                            ?> value="<?php echo date("Y", strtotime("-1 year")) ?>"><?php echo date("Y", strtotime("-1 year")) ?></option>
                                            <option <?php
                                            if (date("Y") == $selected_year) {
                                                echo "selected";
                                            }
                                            ?>  value="<?php echo date("Y") ?>"><?php echo date("Y") ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" name="search" value="search" class="btn btn-primary btn-sm float-end"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>
                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="card border0 clear">
                            <div class="card-body table-responsive">
                                <div class="download_label"><?php echo $this->lang->line('staff_list'); ?></div>
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('staff_id'); ?></th>
                                            <th><?php echo $this->lang->line('staff_name'); ?></th>
                                            <th><?php echo $this->lang->line('staff_role'); ?></th>
                                            <th><?php echo $this->lang->line('staff_department'); ?></th>
                                            <th><?php echo $this->lang->line('staff_designation'); ?></th>
                                            <th><?php echo $this->lang->line('staff_phone'); ?></th>
                                            <th class="text-center"><?php echo $this->lang->line('status'); ?></th>
                                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>            
                                    <tbody>    
                                        <?php
                                        foreach ($resultlist as $staff) {
                                            $status = $staff["status"];

                                            if ($staff["status"] == "paid") {
                                                $label   = "class='badge sh-status-paid'";
                                                $wstatus = $payroll_status[$staff["status"]];
                                            } elseif ($staff["status"] == "generated") {
                                                $label   = "class='badge bg-warning'";
                                                $wstatus = $payroll_status[$staff["status"]];
                                            } else {
                                                $label   = "class='badge bg-secondary'";
                                                $wstatus = $payroll_status["not_generate"];
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $staff['employee_id']; ?></td>
                                                <td><?php echo $staff['name'] . " " . $staff['surname']; ?></td>
                                                <td><?php echo $staff['user_type']; ?></td>
                                                <td><?php echo $staff['department']; ?></td>
                                                <td><?php echo $staff['designation']; ?></td>
                                                <td><?php echo $staff['contact_no']; ?></td>
                                                <td class="text-center"><span <?php echo $label; ?>><?php echo $wstatus; ?></span></td>
                                                <td class="text-end noExport text-nowrap">
                                                    <div class="d-inline-flex gap-1 justify-content-end">
                                                    <?php if ($status == "paid") { ?>
                                                        <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                            <a class="btn btn-sm btn-outline-secondary sh-icon-btn" onclick="return confirm('<?php echo $this->lang->line('are_you_sure_you_want_to_revert_this_record') ?>')" href="<?php echo base_url() . "admin/payroll/revertpayroll/" . $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . $role_selected ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('revert'); ?>">
                                                                <i class="fa fa-undo"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <a href="#" onclick="getPayslip('<?php echo $staff["payslip_id"]; ?>')" class="btn btn-sm btn-outline-primary sh-icon-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('payslip_view'); ?>">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    <?php } elseif ($status == "generated") { ?>
                                                        <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_edit')) { ?>
                                                            <a href="<?php echo site_url('admin/payroll/edit/'.$staff["payslip_id"]) ?>" class="btn btn-sm btn-outline-primary sh-icon-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('edit') ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_delete')) { ?>
                                                            <a href="<?php echo base_url() ?>admin/payroll/deletepayroll/<?php echo $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . $role_selected ?>" class="btn btn-sm btn-outline-danger sh-icon-btn" onclick="return confirm('<?php echo $this->lang->line('are_you_sure_you_want_to_revert_this_record') ?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('revert'); ?>">
                                                                <i class="fa fa-undo"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                            <a href="#" onclick="getRecord('<?php echo $staff["id"] ?>', '<?php echo $year ?>')" class="btn btn-sm btn-primary sh-icon-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('proceed_to_payment'); ?>">
                                                                <i class="fa fa-money"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } elseif ($staff["payslip_id"] == 0) { ?>
                                                        <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                            <a class="btn btn-sm btn-primary sh-icon-btn" href="<?php echo base_url() . "admin/payroll/create/" . strtolower($month_selected) . "/" . $year . "/" . $staff["id"] ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $this->lang->line('generate'); ?> <?php echo $this->lang->line('staff_payroll'); ?>">
                                                                <i class="fa fa-cogs"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  
                    <?php
                }
                ?>
            </div>
            <form action="<?php echo base_url('admin/payroll/create') ?>" method="post" id="formsubmit">
                <input type="hidden" name="month" id="month">
                <input type="hidden" name="year" id="year">
                <input type="hidden" name="staffid" id="staffid">
            </form>
        </div>

<div id="payslipview" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="payslipviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payslipviewLabel"><?php echo $this->lang->line('details'); ?><span id="print" class="moprint-sm"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="testdata">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="proceedtopay" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="proceedtopayLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proceedtopayLabel"><?php echo $this->lang->line('proceed_to_pay'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <form role="form" id="schsetting_form" action="<?php echo site_url('admin/payroll/paymentSuccess') ?>" enctype="multipart/form-data">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('payment_details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('staff_name'); ?> </label>
                                        <input type="text" name="emp_name" readonly class="form-control" id="emp_name">
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('amount').' ('.$currency_symbol.')'; ?></label>
                                        <input type="text" name="amount" readonly class="form-control" id="amount">
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line("month") ?> <?php echo $this->lang->line('year'); ?></label>
                                        <input id="monthid" name="month" readonly placeholder="" type="text" class="form-control" />
                                        <input name="paymentmonth" placeholder="" type="hidden" class="form-control" />
                                        <input name="paymentyear" placeholder="" type="hidden" class="form-control" />
                                        <input name="paymentid" placeholder="" type="hidden" class="form-control" />
                                        <input name="staff_id" placeholder="" type="hidden" class="form-control" />
                                        <input name="staff_role" placeholder="" type="hidden" class="form-control" />
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('mode'); ?></label><span class="req"> *</span>
                                        <span id="remark"></span>
                                        <select name="payment_mode" id="payment_mode" class="form-control payment_mode">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($payment_mode as $pkey => $pvalue) { ?>
                                                <option value="<?php echo $pkey ?>"><?php echo $pvalue ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                    </div>
                                    <div class="cheque_div col-12 d-none">
                                        <div class="row">
                                            <div class="mb-3 col-12 col-sm-4">
                                                <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                            </div>
                                            <div class="mb-3 col-12 col-sm-4">
                                                <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                            </div>
                                            <div class="mb-3 col-12 col-sm-4">
                                                <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control" name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="payment_date" id="payment_date" class="form-control date" value="<?php echo date($this->customlib->getHospitalDateFormat()) ?>">
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <span id="remark"></span>
                                        <textarea name="remarks" class="form-control sh-no-resize"></textarea>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="submit" form="schsetting_form" class="btn btn-info submit_schsetting" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getRecord(id, year) {
        $('input[name="amount"]').val('');
        $('input[name="emp_name"]').val('');
        $('input[name="paymentid"]').val('');
        $('input[name="paymentmonth"]').val('');
        $('input[name="paymentyear"]').val('');
        $('#monthid').val('');

        var month = '<?php echo $month_selected ?>';
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/paymentRecord',
            type: 'POST',
            data: {staffid: id, month: month, year: year},
            dataType: "json",
            success: function (result) {
                $('input[name="amount"]').val(result.result.net_salary);
                $('input[name="emp_name"]').val(result.result.name + ' ' + result.result.surname + ' (' + result.result.employee_id + ')');
                $('input[name="paymentid"]').val(result.result.id);
                $('input[name="paymentmonth"]').val(month);
                $('input[name="paymentyear"]').val(year);
                $('input[name="staff_id"]').val(id);
                $('input[name="staff_role"]').val(result.result.role);
                $('#monthid').val(month + '-' + year);
            }
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('proceedtopay'),{backdrop:'static',keyboard:false}).show();
    }
    ;     

    function getPayslip(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                $("#print").html("<a href='#' data-bs-toggle='tooltip' class='float-end modal-title moprint' onclick='printData(" + id + ")'  title='<?php echo $this->lang->line('print') ?>'><i class='fa fa-print'></i></a>");
                $("#testdata").html(result);
            }
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('payslipview'),{backdrop:'static',keyboard:false}).show();
    }
    ;

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipPrint',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                popup(result);
            }
        });
    }
	
    function getEmployeeName(role) {

        var base_url = '<?php echo base_url() ?>';
        $("#name").html("<option value=''>select</option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });

                $('#name').append(div_data);
            }
        });
    }
	
    function create(id) {

        var month = '<?php echo $this->input->post('month', TRUE); ?>';
        var year  = '<?php echo $this->input->post('year', TRUE); ?>';

        $("#month").val(month);
        $("#year").val(year);
        $("#staffid").val(id);
        $("#formsubmit").submit();
    }

    $(document).on('submit', '#schsetting_form', function (e) {
            e.preventDefault();
       
        $.ajax({
            url: '<?php echo site_url("admin/payroll/paymentSuccess") ?>',
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,  
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }

                $("#formaddbtn").btnReset();
            }
        });
    });

</script>

<script type="text/javascript">
    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });

    // Initialize Bootstrap 5 tooltips (re-init on DataTable draw for pagination/sort/search)
    $(document).ready(function () {
        function initActionTooltips() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                var existing = bootstrap.Tooltip.getInstance(el);
                if (existing) { existing.dispose(); }
                new bootstrap.Tooltip(el);
            });
        }
        initActionTooltips();
        $('.example').on('draw.dt', initActionTooltips);
    });
</script>
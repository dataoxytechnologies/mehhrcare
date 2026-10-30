
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<!-- Main content -->
    <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('transaction_report') ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        </div>
                    </div>
                    <div class="card-body pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form role="form" action="<?php echo site_url('admin/income/transactionreport') ?>" method="post">
                                        <div class="card-body row">
                                            <?php echo $this->customlib->getCSRF(); ?>
                                            <div class="col-sm-6 col-md-3" >
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                                        <option value=""><?php echo $this->lang->line('all') ?></option>
                                                        <?php foreach ($searchlist as $key => $search) { ?>
                                                            <option value="<?php echo $key ?>" <?php
                                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $search ?></option>
                                                            <?php }?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3" >
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line('collected_by'); ?></label>
                                                    <select class="form-control select2 w-100"  name="collect_staff" >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($staffsearch as $dkey => $value) { ?>
                                                            <option value="<?php echo $value["staffid"] ?>"<?php
                                                        if ((isset($staffsearch_select)) && ($staffsearch_select == $value["staffid"])) {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $value["staffname"] . " " . $value["staffsurname"] ." (". $value["employee_id"].")" ?></option>
                                                        <?php }?>
                                                    </select>
                                                <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 d-none" id="fromdate">
                                            <div class="mb-3">
                                            <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 d-none" id="todate">
                                                <div class="mb-3">
                                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="col-sm-12">
                                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle float-end"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>  
                        </div>
                    </div>

<?php if (isset($parameter)) {
    ?>
                        <div class="tabsborderbg"></div>
                        <div class="nav-tabs-custom border0">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#all" data-bs-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('all') ?></a></li>
                                <?php
$j               = 0;
    $language_module = array(
        'opd_patient'    => 'opd_patient',
        'ipd_patient'    => 'ipd_patient',
        'pharmacy_bill'  => 'pharmacy_bill',
        'pathology_test' => 'pathology_test',
        'radiology_test' => 'radiology_test',
        'ot_patient'     => 'ot_patient',
        'ambulance_call' => 'blood_issue',
        'blood_issue'    => 'ambulance_call',
        'income'         => 'income',
        'expense'        => 'expense',
        'payroll_report' => 'payroll_report',
    );
    $permission_array = array('opd_patient', 'ipd_patient', 'pharmacy_bill', 'pathology_test', 'radiology_test', 'ot_patient', 'ambulance_call', 'blood_issue', 'income', 'expense', 'payroll_report');
    $report_module    = array('opd_report', 'ipd_report', 'pharmacy_bill_report', 'pathology_patient_report', 'radiology_patient_report', 'ot_report', 'ambulance_call', 'blood_donor_report', 'income', 'expense', 'payroll_report');
    foreach ($parameter as $ckey => $value) {
        if (($this->rbac->hasPrivilege($permission_array[$j], 'can_view')) || ($this->rbac->hasPrivilege($report_module[$j], 'can_view'))) {
            ?>
                                        <li><a href="#<?php echo $ckey ?>" data-bs-toggle="tab" aria-expanded="true"><span><?php echo $this->lang->line($language_module[$permission_array[$j]]); ?></span></a></li>
                                    <?php
}
        $j++;
    }
    ?>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="all">
                                    <div class="download_label"><?php echo $this->lang->line('transaction_report'); ?></div>
                                    <div>
                                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                            <thead>
                                            <th><?php echo $this->lang->line('name'); ?></th>
                                            <th><?php echo $this->lang->line('reference'); ?></th>
                                            <th><?php echo $this->lang->line('head'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('collected_by'); ?></th>
                                            <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                            </thead>
                                            <tbody>
                                                <?php
$j          = 0;
    $total      = 0;
    $class      = "";
    $refference = "";
    $prefix     = "";
    foreach ($resultlist as $key1 => $result2) {
       
        foreach ($result2 as $key4 => $result) {

        $generated_by = $result["bill_generated_by"];
        $staff_result = $this->staff_model->getstaff($generated_by);
        if ($staff_result["employee_id"]) {
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"] . " (" . $staff_result["employee_id"] . ")";
        } else {
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"];
        }
            $surname = "";
            if (!empty($result["amount"])) {
                $payment = $result["amount"];

                if (($key1 == 'Payroll')) {
                    $surname = $result["surname"];
                }
                if (($key1 == 'Payroll') || ($key1 == 'Expenses')) {
                    $class  = "text-danger";
                    $prefix = "-";
                    $total -= $payment;
                } else {
                    $total += $payment;
                }
            }
            $patient_id = "";
            if (isset($result["patient_unique_id"])) {
                $patient_id = " (" . $result["patient_unique_id"] . ")";
            }
            if (isset($result["reff"])) {
                $refference = $result["reff"];
            }

            if (!($this->rbac->hasPrivilege('opd_patient', 'can_view') || $this->rbac->hasPrivilege('opd_report', 'can_view')) && ($key1 == "OPD")) {

            } elseif (!($this->rbac->hasPrivilege('ipd_patient', 'can_view') || $this->rbac->hasPrivilege('ipd_report', 'can_view')) && ($key1 == "IPD")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('ot_patient', 'can_view') || $this->rbac->hasPrivilege('ot_report', 'can_view')) && ($key1 == "Operation Theatre")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('pharmacy_bill', 'can_view') || $this->rbac->hasPrivilege('pharmacy_bill_report', 'can_view')) && ($key1 == "Pharmacy")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('pathology_test', 'can_view') || $this->rbac->hasPrivilege('pathology_patient_report', 'can_view')) && ($key1 == "Pathology")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('radiology_test', 'can_view') || $this->rbac->hasPrivilege('radiology_patient_report', 'can_view')) && ($key1 == "Radiology")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('blood_issue', 'can_view') || $this->rbac->hasPrivilege('blood_donor_report', 'can_view')) && ($key1 == "Blood Bank")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('income', 'can_view')) && ($key1 == "General Income")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('expense', 'can_view')) && ($key1 == "Expenses")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('payroll_report', 'can_view')) && ($key1 == "Payroll")) {
                # code...
            } elseif (!($this->rbac->hasPrivilege('ambulance_call', 'can_view')) && ($key1 == "Ambulance")) {
                # code...
            } else {
                ?>
                                                            <tr>
                                                                <td><?php echo html_escape($result["patient_name"]) . " " . html_escape($patient_id) ?></td>
                                                                <td><?php echo html_escape($refference) ?></td>
                                                                <td class="text-capitalize"><?php echo html_escape($key1) ?></td>
                                                                <td><?php echo html_escape($result["generated_byname"]); ?></td>
                                                            </tr>
                                                        <?php
}
        }
        $j++;
    }
    ?>
                                            <tr class="box box-solid total-bg">
												<td></td>
												<td></td>
												<td></td>
												<td></td>
                                                <td></td>
                                                <td class="text-end"><?php echo $this->lang->line('total') . " : " . $currency_symbol . $total; ?>
                                                </td>
                                            </tr>
                                            </tbody>   
                                        </table>
                                    </div>
                                </div>
                                <?php
$i    = 0;
    $reff = 0;

    foreach ($parameter as $key => $value) {
        ?>
                                    <div class="tab-pane" id="<?php echo $key ?>">
                                        <div class="download_label"><?php echo $this->lang->line('transaction_report'); ?></div>
                                        <div class="card-body table-responsive">
                                            <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                                <thead>
                                                    <th><?php echo $this->lang->line('name'); ?></th>
                                                    <th><?php echo $this->lang->line('reference'); ?></th>
                                                    <th><?php echo $this->lang->line('head'); ?></th>
                                                    <th><?php echo $this->lang->line('date'); ?></th>
                                                <?php if (($key !='income') && ($key !='expense') && ($key !='payroll')) { ?>
                                                        <th>
                                                    <?php    echo $this->lang->line('collected_by'); ?>
                                                    </th>
                                                 <?php   } ?>
                                                    <th class="text-end"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                                </thead>
                                                <tbody>
                                                    <?php
$tot[$value["label"]] = 0;
        foreach ($value["resultList"] as $key2 => $transaction) {           
             $generated_by = $transaction["bill_generated_by"];
             $staff_result = $this->staff_model->getstaff($generated_by);
             if ($staff_result["employee_id"]) {
                 $transaction["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"] . " (" . $staff_result["employee_id"] . ")";
             } else {
                 $transaction["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"];
             }

            $surname = "";
            if ($value["label"] == "Payroll") {
                $surname = $transaction["surname"];
            }
            if (!empty($transaction["amount"])) {
                $payment = $transaction["amount"];
                $tot[$value["label"]] += $payment;
            }
            $patient_id = "";
            if (isset($transaction["patient_unique_id"])) {
                $patient_id = " (" . $transaction["id"] . ")";
            }
            if (isset($transaction["reff"])) {
                $reff = $transaction["reff"];
            }
            ?>
                                                        <tr>
                                                            <td><?php echo html_escape($transaction["patient_name"]) . " " . html_escape($surname) . " " . html_escape($patient_id) ?></td>
                                                            <td><?php echo html_escape($reff); ?></td>
                                                            <td class="text-capitalize"><?php echo html_escape($value["label"]) ?></td>
                                                    <?php if (($key !='income') && ($key !='expense') && ($key !='payroll')) { ?>
                                                        <td>
                                                      <?php echo html_escape($transaction["generated_byname"]); ?>
                                                        </td>
                                                   <?php  } ?>
                                                            <td class="text-end"><?php echo html_escape($payment) ?></td>
                                                        </tr>
        <?php }?>
														<tr class="box box-solid total-bg">
															<td></td>
															<td></td>
															<td></td>
															<td></td>
                                                            <?php if (($key !='income') && ($key !='expense') && ($key !='payroll')) { ?>
                                                            <td></td>
                                                            <?php } ?>
															<td class="text-end" ><?php echo $this->lang->line('total') . " : " . $currency_symbol . $tot[$value["label"]]; ?></td>
														</tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
        <?php $i++;
    }
    ?>
                            </div>
                        </div>
                    </div>
    <?php
}
?>

            </div>
        </div>   <!-- /.row -->
    
</div>

<!-- .date inputs auto-initialized as Tempus Dominus 6 pickers via event delegation in footer.php -->

<script type="text/javascript">
    $(document).ready(function () {
        $.extend($.fn.dataTable.defaults, {
            ordering: true,
            paging: false,
            bSort: true,
            info: true,
        });
    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';

    function printDiv(elem) {
        popup(jQuery(elem).html());
    }     
</script>

<script type="text/javascript">
    $(document).ready(function (e) {
        showdate('<?php echo $search_type; ?>');
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
</script>
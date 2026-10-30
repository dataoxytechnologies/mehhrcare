<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('blood_issue_details'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('blood_issue', 'can_add')) { ?>
                                <button type="button" class="btn btn-primary btn-sm issueblood" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('issue_blood'); ?></button>
                            <?php } ?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('blood_issue_details'); ?></div>
                         <div class="table-responsive">
                            <table id="bloodIssueTable" class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('blood_issue_details'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('issue_date'); ?></th>
                                        <th><?php echo $this->lang->line('received_to'); ?></th>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('donor_name'); ?></th>
                                        <th><?php echo $this->lang->line('bags'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?></th>
                                        <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                        ?>
                                                <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <th class="text-end"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
										<th class="text-end"><?php echo $this->lang->line('discount'); ?></th>
										<th class="text-end"><?php echo $this->lang->line('tax'); ?></th>
										<th class="text-end"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
										<th class="text-end"><?php echo $this->lang->line('paid_amount'). ' (' . $currency_symbol . ')'; ?></th>
										<th class="noExport text-end"><?php echo $this->lang->line('balance_amount'). ' (' . $currency_symbol . ')'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel"><?php echo $this->lang->line('payments'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formadd" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('issue_blood'); ?></h5>
                    <input type="hidden" id="organisation_id" name="organisation_id" />
                    <input type="hidden" id="insurance_id" name="insurance_id" />
                    <input type="hidden" id="insurance_validity" name="insurance_validity" />
                    <select class="form-control patient_list_ajax sh-bill-patient-pick" name='patient_id' id="addpatient_id" onchange="get_PatientDetails(this.value)"></select>
                    <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                    <a id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i> <?php echo $this->lang->line('new_patient'); ?></a>
                    <?php } ?>
                    <div class="input-group sh-bill-rx-search">
                        <input type="hidden" id="id_case_reference_id_exist" name="id_case_reference_id_exist">
                        <input type="text" class="form-control" id="case_reference_idd" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                        <button class="btn btn-secondary" type="button" id="search_case_reference_id"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input class="form-check-input apply_tpa mt-0" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_all()">
                        <label class="form-check-label text-white mb-0" for="is_tpa"><?php echo $this->lang->line('apply_tpa'); ?></label>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body pb5">
                    </div><!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    <button type="submit" id="formaddbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- dd -->
<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit') . ' ' . $this->lang->line('blood_issue_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <select onchange="get_PatienteditDetails(this.value)" class="form-control select2" id="erecieve_to" name='patient_id'>
                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                <?php foreach ($patients as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                                    if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                                                                        echo "selected";
                                                                                    }
                                                                                    ?>><?php echo $dvalue["patient_name"] . " ( " . ($dvalue["patient_unique_id"] ?? '') . ")" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div><!--./col-sm-9-->
                </div><!--./row-->
            </div>

            <form id="formedit" accept-charset="utf-8" method="post" class="">
                    <div class="modal-body">
                        <div class="row ptt10">
                            <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                            <input type="hidden" name="recieve_to" id="patienteditid" value="<?php echo set_value('id'); ?>">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('issue') . " " . $this->lang->line('date'); ?></label>
                                    <small class="req"> *</small>
                                    <input type="text" name="date_of_issue" id="date_of_issue" value="" class="form-control datetime">
                                    <span class="text-danger"><?php echo form_error('date_of_issue'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label for="exampleInputFile">
                                        <?php echo $this->lang->line('hospital_doctor') ; ?></label>
                                    <div>
                                        <select class="form-control select2" onchange="get_docEditname(this.value)" name='consultant_doctor' id="edit_consultant_doctor">
                                            <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($doctors as $dkey => $dvalue) {
                                            ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('doctor') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <input type="text" name="doctor" id="doctor" value="<?php echo set_value('doctor'); ?>" class="form-control">
                                </div>
                                <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('technician'); ?></label>
                                    <input type="text" name="technician" id="technician" value="<?php echo set_value('recieve_to'); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('donor') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <select class="form-control select2" onchange="getBloodGroup(this.value, 'blood_groupedit')" id="donorname" name='donor_name'>
                                        <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('donor') ?>
                                        </option>
                                        <?php foreach ($blooddonar as $dkey => $dvalue) {
                                        ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                                            if ((isset($blooddonar_select)) && ($blooddonar_select == $dvalue["id"])) {
                                                                                                echo "selected";
                                                                                            }
                                                                                            ?>><?php echo $dvalue["donor_name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('blood_group'); ?></label>
                                    <input type="text" name="blood_group" id="blood_groupedit" readonly="" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('lot'); ?></label>
                                    <input type="text" name="lot" class="form-control" id="lot" value="<?php echo set_value('lot'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label><?php echo $this->lang->line('bag'); ?></label>
                                    <input type="text" name="bag_no" class="form-control" id="bag_no" value="<?php echo set_value('bag_no'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label for="amount"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label>
                                    <small class="req"> *</small>
                                    <input name="amount" type="text" id="amount" value="<?php echo set_value('amount'); ?>" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="mb-3">
                                    <label for="remark"><?php echo $this->lang->line('remarks'); ?></label>
                                    <textarea name="remark" id="remark" value="<?php echo set_value('remark'); ?>" class="form-control"></textarea>
                                </div>
                            </div>
                        </div><!--./row-->
                    </div>
                </div>
                <div class="card-footer">
                    <div class="float-end ">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditbtn" class="btn btn-info float-end"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('blood_issue_details'); ?></h5>
                <div id="edit_delete" class="d-flex gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModalBill" tabindex="-1" aria-labelledby="viewModalBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalBillLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id='edit_deletebill'>
                <a href="#" data-bs-target="#edit_prescription" data-bs-toggle="modal" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                <a href="#" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
            </div>
            <div class="modal-body">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.select2').select2();
    })
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    $(document).on('change', '.payment_mode', function() {
        var mode = $(this).val();
        var $modal = $(this).closest('.modal');
        if (mode == "Cheque") {
            $('.filestyle', $modal).dropify();
            $('.cheque_div', $modal).removeClass('d-none');
        } else {
            $('.cheque_div', $modal).addClass('d-none');
        }
    });
    $(document).on('click', '.add_payment', function() {
        var record_id = $(this).data('recordId');
        var $add_btn = $(this);
        var payment_modal = $('#addPaymentModal');
        payment_modal.addClass('modal_loading');
        $('.filestyle', '#addPaymentModal').dropify();
        shModal(payment_modal[0]).show();
        getPayments(record_id);
    });

    function getPayments(record_id) {
        var payment_modal = $('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/bloodbank/getBloodbankTransaction',
            type: "POST",
            data: {
                'id': record_id
            },
            dataType: "JSON",
            beforeSend: function() {
              
            },
            success: function(data) {

                $('.modal-body', payment_modal).html(data.page);
                payment_modal.removeClass('modal_loading');
            },
            error: function() {
                payment_modal.removeClass('modal_loading');
            },
            complete: function() {
                payment_modal.removeClass('modal_loading');
            }
        });
    }

    $(document).on('submit', '#add_partial_payment', function(e) {
        e.preventDefault();
        var clicked_btn = $("button[type=submit]");
        var billing_id = $("input[name='billing_id']", '#add_partial_payment').val();

        var form = $(this);
        var btn = clicked_btn;
        btn.btnLoading();
        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'JSON',
            success: function(data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    table.ajax.reload();
                    getPayments(billing_id);
                }
                btn.btnReset();
            },
            error: function() {

            },
            complete: function() {
                btn.btnReset();
            }
        });
    });

    $(document).on('click', '.add_payment', function(e) {
        $('#add_payment').trigger("reset");
        var record_id = $(this).data('recordId');
        var payment_module = $(this).data('module');
        var caseid = $(this).data('caseid');
        var amount = $(this).data('totalamount');
        $('#amount').val(amount);
        $('#module_id').val(record_id);
        $('#module_name').val(payment_module);
        $('#case_reference_idd').val(caseid);
        shModal('myPaymentModal').show();
    });

    $(document).ready(function(e) {

        modal_click_disabled('myModal', 'addPaymentModal', 'viewModal', 'addPaymentModal')

        $("#add_payment").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: base_url + 'admin/bill/makepayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,

                beforeSend: function() {
                    $("#add_paymentbtn").btnLoading();
                },
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        shModal('myPaymentModal').hide();
                    }
                    $("#add_paymentbtn").btnReset();
                },
                error: function() {
                    $("#add_paymentbtn").btnReset();
                },
                complete: function() {
                    $("#add_paymentbtn").btnReset();
                }
            });
        }));
    });
	
    $(document).on('click', '.issueblood', function() {
        var issueModal = $('#myModal');
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: base_url + 'admin/bloodbank/issueblood',
            type: "POST",
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
                issueModal.addClass('modal_loading');
            },
            success: function(res) {
                $('.modal-body', issueModal).html(res.page);
                $('.filestyle', '#myModal').dropify();
                $("#qty").val(1);
                getcharge_category_module("blood_bank");
                $('.modal-body', issueModal).find('.select2').select2();
                shModal(issueModal[0]).show();
                issueModal.removeClass('modal_loading');
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();
                issueModal.removeClass('modal_loading');
            },
            complete: function() {
                $this.btnReset();
                issueModal.removeClass('modal_loading');
            }
        });
    });
</script>
<script type="text/javascript">
    function get_PatientDetails(id) {
    if(id!=""){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#patientid').val(res.id);
                }
            }
        });
    }
    }

    function get_PatienteditDetails(id) {
    if(id!=""){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#patienteditid').val(res.id);
                    console.log(res.id);
                }
            }
        });
    }
    }

    function get_Docname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {
                doctor: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#reference').val(res.name + " " + res.surname + " (" + res.employee_id + ")");
                } else {

                }
            }
        });
    }

    function get_docEditname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {
                doctor: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#doctor').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function printData(id) {
        $.ajax({
            url: base_url + 'admin/bloodbank/getBillDetails/' + id,
            type: 'POST',
            data: {
                id: id,
                print: 'yes'
            },
            success: function(result) {
                popup(result);
            }
        });
    }     

    $(document).ready(function(e) {

        $("form#formadd button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        $("#formadd").on('submit', (function(e) {
            // Prevent native submit FIRST — otherwise any JS error below skips
            // preventDefault() and the form does a full-page POST (page reload, no save).
            // The edit form (_editissueblood.php) has no `.filestyle` file input, so the
            // old `.prop('files')[0]` threw a TypeError here and reloaded the page on edit.
            e.preventDefault();

            var str = $("#formadd").serializeArray();
            var postData = new FormData();
            var fileEl    = $("#formadd .filestyle");
            var fileList  = fileEl.length ? fileEl.prop('files') : null;
            var file_data = (fileList && fileList.length) ? fileList[0] : null;
            if (file_data) {
                postData.append('document', file_data);
            }
            var case_reference_id = $("input[name=case_reference_id]").val();
            postData.append('case_reference_id', case_reference_id);
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);

            });

            var sub_btn_clicked = $("button[type=submit][clicked=true]");

            var sub_btn_clicked_name = sub_btn_clicked.attr('name');
           

            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssue',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    sub_btn_clicked.btnLoading();
                },
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        if (sub_btn_clicked_name === "save_print") {
                            printData(data.id);
                        }
                        table.ajax.reload(null, false);
                        shModal('myModal').hide();
                    }
                    sub_btn_clicked.btnReset();
                },
                error: function() {
                    sub_btn_clicked.btnReset();
                },
                complete: function() {
                    sub_btn_clicked.btnReset();

                }
            });
        }));
    });

    $(document).ready(function(e) {
        $("#formedit").on('submit', (function(e) {
            $("#formeditbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/updateIssue',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditbtn").btnReset();
                },
                error: function() {

                }
            });
        }));
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getIssueDetails',
            type: "POST",
            data: {
                bloodissue_id: id
            },
            dataType: 'json',
            success: function(data) {
                $("#id").val(data.id);
                $("#date_of_issue").val(data.date_of_issue);
                $("#patienteditid").val(data.recieve_to);
                $("#doctor").val(data.doctor);
                $("#technician").val(data.technician);
                $("#amount").val(data.amount);
                $("#lot").val(data.lot);
                $("#bag_no").val(data.bag_no);
                $("#remark").val(data.remark);
                $("#blood_groupedit").val(data.blood_group);
                $("#erecieve_to").select2().select2('val', data.recieve_to);
                $("#donorname").select2().select2('val', data.donor_name);
                $('select[id="edit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                shModal('viewModal').hide();
                shModal('viewModalBill').hide();
                holdModal('myModaledit');
            },
        })
    }

    function viewDetailBill(id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/bloodbank/getBillDetails/' + id,
            type: "GET",
            data: {
                id: id
            },
            success: function(data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_view')) { ?><a href='#' data-bs-toggle='tooltip' onclick='printData(" + id + ")'   title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?><?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_edit')) { ?><a href='#'' onclick='getRecord(" + id + ")' data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_edit')) { ?><a onclick='delete_bill(" + id + ")'  href='#'  data-bs-toggle='tooltip'  title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                holdModal('viewModalBill');
            },
        });
    }

    $('#viewModal').on('hidden.bs.modal', function() {
        $('.modal-body', $('#viewModal')).html("");
    });

    $(document).on('click', '.viewDetail', function() {
        var $viewModal = $('#viewModal');
        $viewModal.addClass('modal_loading');
        shModal('viewModal').show();
        $.ajax({
            url: base_url + 'admin/bloodbank/getBloodIssueDetail',
            type: "POST",
            data: {
                'blood_issue_id': $(this).data('recordId')
            },
            dataType: 'json',
            beforeSend: function() {

                $viewModal.addClass('modal_loading');
            },
            success: function(data) {
                $("#edit_delete", viewModal).html(data.action);
                $('.modal-body', viewModal).html(data.page);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $viewModal.removeClass('modal_loading');
            },
            complete: function(data) {
                $viewModal.removeClass('modal_loading');
            }
        });
    });

    $(document).on('click', '.edit_blood_issue', function() {
        var $this = $(this);
        var record_id = $this.data('recordId')
        $this.btnLoading();
        $.ajax({
            url: base_url + 'admin/bloodbank/editIssueBlood',
            type: "post",
            data: {
                'id': record_id
            },
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
            },
            success: function(res) {
                var editIssueBloodModal = $('#myModal');
                $('.modal-body', editIssueBloodModal).html(res.page);
                $('#id_case_reference_id_exist').val(res.case_id);
                $('#case_reference_idd').val(res.case_id);
                var body_modal = $('.modal-body', editIssueBloodModal);
                body_modal.find('.select2').select2();
                shModal('viewModal').hide();
                var patient_id = body_modal.find("input.post_patient_id").val();
                var patient_name = body_modal.find("input.post_patient_name").val();
                var post_bloodgroup = body_modal.find("input.post_blood_group").val();
                var post_blood_donor_cycle_id = body_modal.find("input.post_blood_donor_cycle_id").val();
                var post_charge_type_id = body_modal.find("input.post_charge_type_id").val();
                var post_charge_category_id = body_modal.find("input.post_charge_category_id").val();
                var post_charge_id = body_modal.find("input.post_charge_id").val();
                var bag_no = body_modal.find("input.post_bag_no").val();
                var post_quantity = body_modal.find("input.post_quantity").val();
                var option = new Option(patient_name, patient_id, true, true);
                $("#formadd .patient_list_ajax").append(option).trigger('change');
                // manually trigger the `select2:select` event
                $("#formadd .patient_list_ajax").trigger({
                    type: 'select2:select',
                    params: {
                        data: res
                    }
                });

                getBloodGroupBagNos(post_bloodgroup, post_blood_donor_cycle_id, bag_no,post_quantity);
                getcharge_category(post_charge_type_id, post_charge_category_id);
                getchargecode(post_charge_category_id, post_charge_id);
                
                if(res.organisation_id==null){
                    $("input:checkbox[name=is_tpa]").prop('checked',false);
                }else{
                    $("input:checkbox[name=is_tpa]").prop('checked',true);
                }    
                            
                shModal('myModal').show();
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();

            },
            complete: function() {
                $this.btnReset();
            }
        });
    });

    $(document).on('click', '.printIssueBill', function() {
        var $this = $(this);
        var record_id = $this.data('recordId')
        $this.btnLoading();
        $.ajax({
            url: base_url + 'admin/bloodbank/printBloodIssueBill',
            type: "POST",
            data: {
                'id': record_id
            },
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
            },
            success: function(res) {
                popup(res.page);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();
            },
            complete: function() {
                $this.btnReset();
            }
        });
    });
	
    $(document).on('click', '.delete_blood_issue', function() {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            var $this = $(this);
            var recordId = $this.data('recordId');
            $this.btnLoading();
            $.ajax({
                url: base_url + 'admin/bloodbank/deleteIssue/' + recordId,
                type: "GET",
                data: {},
                dataType: 'json',
                beforeSend: function() {
                    $this.btnLoading();
                },
                success: function(res) {
                    if (res.status == "fail") {
                        errorMsg(res.msg);
                    } else {
                        successMsg(res.msg);
                        shModal('viewModal').hide();
                        table.ajax.reload();
                    }
                    $this.btnReset();
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.btnReset();
                },
                complete: function() {
                    $this.btnReset();
                }
            });
        }
    });

    function holdModal(modalId) {
        (function(){
            var _el=document.getElementById(modalId);
            if(!_el) return;
            if(_el.parentNode !== document.body) { document.body.appendChild(_el); }
            bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();
        })();
    }

    function getBloodGroup(donorid, htmlid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getDonorBloodgroup',
            type: "POST",
            data: {
                donor_id: donorid
            },
            dataType: 'json',
            success: function(data) {
                $("#" + htmlid).val(data.blood_group);
            }
        });
    }

    $("#myModal").on('shown.bs.modal', function () {
        var $sel = $('#addpatient_id');
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.select2({
            ajax: {
                url: '<?php echo base_url(); ?>admin/patient/getPatientListAjax',
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { searchTerm: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            },
            placeholder: '<?php echo $this->lang->line('select_patient'); ?>',
            dropdownParent: $('#myModal')
        });
    });

    $("#myModal").on('hidden.bs.modal', function(e) {
        $('.patient_list_ajax').empty().trigger("change");
        $("#consultant_doctor").select2("val", "");
        $(".blood_group").select2("val", "");
        $(".bag_no").select2("val", "");
        $("#charge_category").select2("val", "");
        $(".addcharge").select2("val", "");
        $('#myModal #formadd').find('input:text, input:password, input:file, textarea').val('');
        $('#myModal #formadd').find('select option:selected').removeAttr('selected');
        $('#myModal #formadd').find('input:checkbox, input:radio').removeAttr('checked');
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(document).on('select2:select', '.blood_group', function() {
        var bloodgroup = $(this).val();
        getBloodGroupBagNos(bloodgroup, "", "","");

    });

    function getBloodGroupBagNos(bloodgroup, bagid, bag_no,quantity) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: base_url + 'admin/bloodbank/getbatchbybloodgroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no').html("");
            },
            success: function(res) {

                if (bagid != '') {
                    div_data += "<option value='" + bagid + "'  available_unit='" + quantity + "' >" + bag_no + " </option>";
                }

                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let val_unit = "";
                    let volume = obj.volume != null ? obj.volume : "";
                    let unit = obj.charge_unit != null ? obj.charge_unit : "";
                    if (volume != "" || unit != "") {
                        val_unit = " (" + volume + " " + unit + ")";
                    }

                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + val_unit + " </option>";

                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagid);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }
</script>

<script type="text/javascript">
    function getcharge_category(charge_type, charge_category) {
        var div_data = "";
        if (charge_type != "") {

            $.ajax({
                url: base_url + 'admin/charges/get_charge_category',
                type: "POST",
                data: {
                    charge_type: charge_type
                },
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                    $('.charge_category').append(div_data);
                    $('.charge_category').select2("val", charge_category);
                }
            });
        }
    }

    function getcharge_category_module(module) {
        var div_data = "";
        $.ajax({
            url: base_url + 'admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: module
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
    }
	
    $('.patient_list_ajax').on('select2:select', function(e) {
        var data = e.params.data;
        $.ajax({
            url: base_url + 'admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: data.id
            },
            dataType: 'json',
            success: function(res) {
                $('.blood_group ').select2('val', res.blood_bank_product_id);
                getBloodGroupBagNos(res.blood_bank_product_id, "", "","");
            }
        });
    });

    $(document).on('click', '.print_receipt', function() {
        var $this = $(this);
        var record_id = $this.data('recordId')
        $this.btnLoading();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/printTransaction',
            type: "POST",
            data: {
                'id': record_id
            },
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
            },
            success: function(res) {
                popup(res.page);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();
            },
            complete: function() {
                $this.btnReset();
            }
        });
    });

    $(document).on('click', '.delete_trans', function(e) {
        e.preventDefault();
        var record_id = $(this).data('recordId');
        var billing_id = $("input[name='billing_id']", '#add_partial_payment').val();

        if (confirm('<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>')) {
            var btn = $(this);
            btn.btnLoading();
            $.ajax({
                url: base_url + 'admin/transaction/deleteByID',
                type: "POST",
                data: {
                    'id': record_id
                },
                dataType: 'JSON',
                success: function(data) {
                    successMsg(data.message);
                    getPayments(billing_id);
                    btn.btnReset();
                },
                error: function() {
                    btn.btnReset();
                },
                complete: function() {
                    btn.btnReset();
                }
            });
        }
    });

    $(document).on('select2:select', '.charge_category', function() {

        var charge_category = $(this).val();
        $('#tax_percentage').val(0);
        $('#code').val("").trigger("change");
        $("#addstandard_charge").val(0);
        $("#total").val(0);
        $("#discount").val(0);
        $("#tax").val(0);
        $("#net_amount").val(0);
        $("#payment_amount").val(0);
        $("#discount_percent").val(0);
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        getchargecode(charge_category, "");
    });

    function getchargecode(charge_category, charge_id) {

        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if (charge_category != "") {
            $.ajax({
                url: base_url + 'admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {                   
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.addcharge').html(div_data);
                    $(".addcharge").select2("val", charge_id);
                }
            });
        }
    }

    function reset_all(){
    $('#addstandard_charge').val('');
    $('.addcharge').val('').trigger('change');
    $('.charge_category').val('').trigger('change');
    $(".printablea4").find('input').val(0);
    $("#payment_amount").val(0);
    }

    $(document).on('select2:select', '.addcharge', function() {
        var charge = $(this).val();
        var orgid = "";
        let is_tpa=  $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var patient_id= $('#addpatient_id').val();

        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodBankChargeById',
            type: "POST",
            data: {
                charge_id: charge,patient_id:patient_id,is_tpa:is_tpa
            },
            dataType: 'json',
            success: function(res) {
               
                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{  
                     if(res.display_tpa_charge){
                        var charge_amt=res.result.org_charge;
                    }else{
                        var charge_amt=res.result.standard_charge;
                    }          

                    var quantity = $('#qty').val();                    
                    quantity = (quantity == "") ? 0 : quantity;
                    var total_amout = parseFloat(charge_amt) * quantity;
                    $('#total').val(total_amout.toFixed(2));
                    $('#addstandard_charge').val(res.result.standard_charge);
                    var discount_percent = $('#discount_percent').val();

                    $('#tax_percentage').val(res.result.percentage);

                    var discount_amount = parseFloat(total_amout * discount_percent / 100);

                    $('#discount').val(discount_amount);//added

                    var tax = $('#tax_percentage').val();
                    var tax_amount = parseFloat((total_amout - discount_amount) * tax / 100)
                   
                    $("#tax").val(tax_amount.toFixed(2));
                    var net_amount = (total_amout - discount_amount) + tax_amount;
                    console.log(net_amount);
                    $('#net_amount,#payment_amount').val(net_amount.toFixed(2));
                    if(res.status == 2){
                            errorMsg(res.msg);
                        }
                }
            }
        });
    });

    $(document).on('change keyup input paste', '#discount', function() {
        calculateAmt(false);

    });

    $(document).on('change keyup input paste', '#discount_percent', function() {
        calculateAmt(true);
    });

    function calculateAmt(is_percentage) {
        var tot_amt = parseFloat($('#total').val());
        if (is_percentage) {
            var dis_per = $('#discount_percent').val();
            var dis_amt = parseFloat(tot_amt * dis_per / 100);
            $('#discount').val(dis_amt.toFixed(2));
        } else {
            var dis_amt = parseFloat($('#discount').val());
            var dis_per = isNaN(((dis_amt * 100) / tot_amt)) ? 0 : ((dis_amt * 100) / tot_amt);
            $('#discount_percent').val(dis_per.toFixed(2));
        }

        var tax_per = parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt - dis_amt) * tax_per / 100);
        $("#tax").val(isNaN(tax_amt) ? "0" : (tax_amt).toFixed(2));
        var net_amt = isNaN(tax_amt + (tot_amt - dis_amt)) ? "" : (tax_amt + (tot_amt - dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
    }

    $(document).on('click', '#search_case_reference_id', function() {
        var case_reference_id = $("input[name=case_reference_id]").val();
		if(case_reference_id != ''){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/' + case_reference_id,
            type: "POST",
            data: {
                case_reference_id: case_reference_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    let id = res.patient_id;
                    var option = new Option(res.patient_name, id, true, true);
                    $("#formadd .patient_list_ajax").append(option).trigger('change');
                    $("#formadd .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: {
                                id: res.patient_id,
                                patient_name: res.patient_name
                            }
                        }
                    });

                    $("#id_case_reference_id_exist").val(case_reference_id);
                } else {
                    $("#id_case_reference_id_exist").val("");
                    $(".issueblood").trigger("click");
                    $('#addpatient_id').select2("val", "");
                    errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
                }
            }
        });
		}else{
            $("#id_case_reference_id_exist").val("");
            $(".issueblood").trigger("click");
            $('#addpatient_id').select2("val", "");
			errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
		}
    });

    $(document).on('change keyup input paste', '#qty', function() {
        var quantity = $(this).val();
        var standard_charge = $('#addstandard_charge').val();
        var tax_percent = $('#tax_percentage').val();
        var total_charge = (standard_charge == "") ? 0 : standard_charge;
        console.log(total_charge);
        var apply_charge = isNaN(parseFloat(total_charge) * parseFloat(quantity)) ? 0 : parseFloat(total_charge) * parseFloat(quantity);
        $('#total').val(apply_charge);
        var discount_percent = $('#discount_percent').val();
        var discount_amount = isNaN((apply_charge * discount_percent) / 100) ? 0 : (apply_charge * discount_percent) / 100;
        var final_amount = apply_charge - discount_amount;
        console.log(tax_percent);
        $('#discount').val(discount_amount);
        $('#tax').val(((final_amount * tax_percent) / 100));
        $('#net_amount,#payment_amount').val(final_amount + ((final_amount * tax_percent) / 100));
    });

    $(document).on('change keyup input paste', '#addstandard_charge', function() {
        var standard_charge = $("#addstandard_charge").val();
        $("#total").val(standard_charge);
        calculateAmt(true);
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
    var table;
    (function($) {
        'use strict';
        $(document).ready(function() {
            table = initDatatable('ajaxlist', 'admin/bloodbank/getbloodissueDatatable', [], [], 100,
                [
                    { "sWidth": "50px",  "aTargets": [0, 1, 4, 5, 7], 'sClass': 'dt-body-left' },
                    { "sWidth": "110px", "aTargets": [2, 3, 6, 8],    'sClass': 'dt-body-left' },
                    { "sWidth": "80px",  "aTargets": [-6, -5, -4, -3, -2], 'sClass': 'dt-body-right' },
                    { "sWidth": "80px",  "bSortable": false, "aTargets": [-1], 'sClass': 'dt-body-right' }
                ]);
            table.on('draw.dt', function () {
                ['nth-last-child(1)','nth-last-child(2)','nth-last-child(3)','nth-last-child(4)','nth-last-child(5)','nth-last-child(6)'].forEach(function(sel) {
                    $('#bloodIssueTable th:' + sel + ', #bloodIssueTable td:' + sel).each(function () {
                        this.style.setProperty('width',     '80px', 'important');
                        this.style.setProperty('min-width', '80px', 'important');
                        this.style.setProperty('max-width', '80px', 'important');
                    });
                });
            });
        });
    }(jQuery))
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<script>
function get_PatientDetails(id) {    
  if(id!=""){       
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                    
            },
            success: function (res) {                
                if (res) {
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);
                }
            }
        });
    }
}
</script>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ $('.issueblood').trigger('click'); shCleanUrlParam('action'); });</script>
<?php endif; ?>
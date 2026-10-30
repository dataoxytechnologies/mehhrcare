<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('my_leaves'); ?></h3>
                        <small class="float-end">
                            <?php if ($this->rbac->hasPrivilege('apply_leave', 'can_add')) { ?>
                                <a href="#addleave" onclick="addLeave()" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('apply_leave'); ?></a>
                            <?php } if ($this->rbac->hasPrivilege('approve_leave_request', 'can_view')) { ?>
                                <a href="<?PHP echo base_url(); ?>admin/leaverequest/approveleaverequest" class="btn btn-primary btn-sm">
                                    <i class="fa fa-reorder"></i> <?php echo $this->lang->line('approve_leave_request'); ?></a>
                            <?php } ?>
                        </small>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-pane active table-responsive no-padding">
                                    <div class="download_label"><?php echo $this->lang->line('my_leaves'); ?></div>
                                    <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title ="<?php echo $this->lang->line('my_leaves'); ?>">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('staff'); ?></th>
                                        <th><?php echo $this->lang->line('leave_type'); ?></th>
                                        <th><?php echo $this->lang->line('leave_date'); ?></th>
                                        <th><?php echo $this->lang->line('days'); ?></th>
                                        <th><?php echo $this->lang->line('apply_date'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
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
            </div> 
		</div>

<div id="leavedetails" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="leavedetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leavedetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <input id="leave_request_id" name="leave_request_id" type="hidden" />
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                        </div>
                        <div class="sh-info-grid">
                            <div class="row g-0">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('name'); ?></span>
                                    <span class="sh-info-value highlight"><span id="name"></span> <small class="text-muted">(<span id="employee_id"></span>)</small></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('submitted_by'); ?></span>
                                    <span class="sh-info-value"><span id="appliedby"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('leave_type'); ?></span>
                                    <span class="sh-info-value"><span id="leave_type"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('apply_date'); ?></span>
                                    <span class="sh-info-value"><span id="applied_date"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('leave_from_date'); ?></span>
                                    <span class="sh-info-value"><span id="leave_from"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('leave_to_date'); ?></span>
                                    <span class="sh-info-value"><span id="leave_to"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('days'); ?></span>
                                    <span class="sh-info-value"><span id="days"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('reason'); ?></span>
                                    <span class="sh-info-value"><span id="remark"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-12 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('download'); ?></span>
                                    <span class="sh-info-value"><span id="download_file" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="addleave" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="addleaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addleaveLabel"><?php echo $this->lang->line('add_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form role="form" id="addleave_form" method="post" enctype="multipart/form-data" action="">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('leave'); ?> <?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('apply_date'); ?></label><small class="req"> *</small>
                                        <input type="text" id="applieddate" name="applieddate" value="<?php echo date($this->customlib->getHospitalDateFormat()) ?>" class="form-control date">
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('leave_type'); ?></label><small class="req"> *</small>
                                        <div id="leavetypeddl">
                                            <select name="leave_type" id="leave_type" class="form-control">
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('leave_type'); ?></span>
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('leave_from_date'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <div class="input-group-text sh-cal-trigger" onclick="$('#leave_from_date').focus();">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="leave_from_date" class="form-control date" id="leave_from_date">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-12 col-sm-6">
                                        <label><?php echo $this->lang->line('leave_to_date'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <div class="input-group-text sh-cal-trigger" onclick="$('#leave_to_date').focus();">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="leave_to_date" class="form-control date" id="leave_to_date">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label><?php echo $this->lang->line('reason'); ?></label>
                                        <textarea name="reason" id="reason" rows="4" class="form-control sh-no-resize"></textarea>
                                        <input type="hidden" name="leaverequestid" id="leaverequestid">
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" id="file" name="userfile" class="filestyle form-control">
                                        <input type="hidden" id="filename" name="filename">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                    <button type="submit" id="addleave_formbtn" class="btn btn-info submit_addLeave" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <input type="reset" name="resetbutton" id="resetbutton" class="d-none">
                    <button type="button" class="d-none" id="clearform" onclick="clearForm(this.form)" class="btn btn-primary submit_addLeave" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"> <?php echo $this->lang->line('clear'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    /*--dropify--*/
    $(document).ready(function () {
        // Basic
        $('.filestyle').dropify();
    });
    /*--end dropify--*/
</script>

<script type="text/javascript">
    $(document).ready(function () {
        getLeaveTypeDDL('<?php echo $staff_id ?>', '');
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

    function addLeave() {
        $('input[type=text]').val('');
        $('textarea[name="reason"]').text('');
        $("#resetbutton").click();
        $("#clearform").click();
        var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
        var date = '<?php echo date($this->customlib->getHospitalDateFormat()) ?>';
        $('input[type=text][name=applieddate]').val(date);
        $(".dropify-clear").trigger("click");
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addleave'),{backdrop:'static',keyboard:false}).show();
    }
	
    function deleterecord(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/leaverequest/deleteRecord',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg('<?php echo $this->lang->line('success_message'); ?>');
                    window.location.reload(true);
                }
            })
        }
    }

    function shFormatLeaveDate(dateStr, fmt) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var pad = function (n) { return n < 10 ? '0' + n : n; };
        return fmt
            .replace('yyyy', d.getFullYear())
            .replace('MM', pad(d.getMonth() + 1))
            .replace('dd', pad(d.getDate()));
    }

    function getRecord(id) {
        $("#download_file").html('');
        $('input:radio[name=status]').attr('checked', false);
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {
                console.log(result)
                var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                $('input[name="leave_request_id"]').val(result.id);
                $('#employee_id').html(result.employee_id);
                $('#name').html(result.name + ' ' + result.surname);
                $('#leave_from').html(shFormatLeaveDate(result.leave_from, leavedate_format));
                $('#leave_to').html(shFormatLeaveDate(result.leave_to, leavedate_format));
                $('#leave_type').html(result.type);
                $('#days').html(result.leave_days + ' <?php echo $this->lang->line('days'); ?>');
                $('#remark').html(result.employee_remark);
                $('#applied_date').html(shFormatLeaveDate(result.date, leavedate_format));
                $('#appliedby').html(result.applied_by);             
                $("#detailremark").text(result.admin_remark);
                if (result.document_file != "") {
                    var cl = "<i class='fa fa-download'></i>";
                    $("#download_file").html('<a href=' + base_url + 'admin/staff/download/' + result.id + '/' + result.staff_id + ' class=btn btn-secondary btn-sm  data-bs-toggle=tooltip >' + cl + '</a>');
                }
            }
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('leavedetails'),{backdrop:'static',keyboard:false}).show();
    }
    ;

    $(document).on('click', '.submit_schsetting', function (e) {
        var $this = $(this);
        $this.btnLoading();
        $.ajax({
            url: '<?php echo site_url("admin/leaverequest/leaveStatus") ?>',
            type: 'post',
            data: $('#leavedetails_form').serialize(),
            dataType: 'json',
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
                $this.btnReset();
            }
        });
    });

    function checkStatus(status) {
        if (status == 'approve') {
            $("#reason").addClass('d-none');
        } else if (status == 'pending') {
            $("#reason").addClass('d-none');
        } else if (status == 'disapprove') {
            $("#reason").removeClass('d-none');
        }
    }

    $(document).ready(function (e) {
        $("#addleave_form").on('submit', (function (e) {           
            $("#addleave_formbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/leaverequest/add_staff_leave") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data)
                {                    
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
                    $("#addleave_formbtn").btnReset();
                }
            });
        }));
    });

    function getEmployeeName(role) {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.id + "' >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });
                $('#empname').append(div_data);
            }
        });
    }

    function setEmployeeName(role, id = '') {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    if (obj.employee_id == id) {
                        ne = 'selected';
                    } else {
                        ne = "";
                    }

                    div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);
            }
        });
    }

    function getLeaveTypeDDL(id, lid = '') {
     
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/countLeave/' + id,
            type: 'POST',
            data: {lid: lid},           
            success: function (result) {
                $("#leavetypeddl").html(result);
            }
        });
    }
	
    function editRecord(id) {
        var leave_from = '05/01/2018';
        var leave_to = '05/10/2018';
        $("#resetbutton").click();
        $('textarea[name="reason"]').text('');
        $('textarea[name="remark"]').text('');
        $('input:radio[name=addstatus]').attr('checked', false);
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {
                leave_from = result.leavefrom;
                leave_to = result.leaveto;
                var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';

                setEmployeeName(result.user_type, result.employee_id);
                getLeaveTypeDDL(result.staff_id, result.lid);
                $('select[name="role"] option[value="' + result.user_type + '"]').attr("selected", "selected");
                $('input[name="applieddate"]').val(new Date(result.date).toString(leavedate_format));
                $('input[name="leavefrom"]').val(new Date(result.leave_from).toString(leavedate_format));
                $('input[name="filename"]').val(result.document_file);
                $('input[name="leavedates"]').val(new Date(result.leave_from).toString(leavedate_format) + '-' + new Date(result.leave_to).toString(leavedate_format));
                $('#leave_from_date').val(new Date(result.leave_from).toString(leavedate_format));
                $('#leave_to_date').val(new Date(result.leave_to).toString(leavedate_format));
                $('input[name="leaverequestid"]').val(id);
                $('textarea[name="reason"]').text(result.employee_remark);
                $('textarea[name="remark"]').text(result.admin_remark);
                if (result.status == 'approve') {
                    $('input:radio[name=addstatus]')[1].checked = true;
                } else if (result.status == 'pending') {
                    $('input:radio[name=addstatus]')[0].checked = true;
                } else if (result.status == 'disapprove') {
                    $('input:radio[name=addstatus]')[2].checked = true;
                }
            }
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('addleave'),{backdrop:'static',keyboard:false}).show();
    }
    ;

    function clearForm(oForm) {
        var elements = oForm.elements;
        for (i = 0; i < elements.length; i++) {
            field_type = elements[i].type.toLowerCase();
            switch (field_type) {
                case "text":
                case "password":
                case "hidden":
                    elements[i].value = "";
                    break;
                case "select-one":
                case "select-multi":
                    elements[i].selectedIndex = "";
                    break;
                default:
                    break;
            }
        }
    }
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/staff/getleaveapplyDatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
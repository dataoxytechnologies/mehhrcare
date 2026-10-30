<?php $call_type = $this->customlib->getCalltype();?>
    <div class="row">
            <!-- left column -->
            <div class="col-md-12"> 
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('phone_call_log_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm call_log"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_call_log'); ?> </a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('phone_call_log'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('phone_call_log'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('next_follow_up_date'); ?></th>
                                        <th><?php echo $this->lang->line('call_type'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div> 
    


<!-- new END -->
<div class="modal fade sh-modal sh-modal-accent" id="calldetails" tabindex="-1" aria-labelledby="calldetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calldetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_call_log'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_call_log'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input type="text" id="ename" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="econtact" value="<?php echo set_value('contact'); ?>" name="contact">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input id="edate" name="date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                                        <input id="efollow_up_date" name="follow_up_date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('follow_up_date'); ?>" readonly="readonly">
                                        <span class="text-danger"><?php echo form_error('follow_up_date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('call_duration'); ?></label>
                                        <input id="ecall_dureation" type="text" class="form-control form-control-sm" value="<?php echo set_value('call_dureation'); ?>" name="call_dureation">
                                        <span class="text-danger"><?php echo form_error('call_dureation'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('call_type'); ?> <small class="req">*</small></label>
                                        <div class="d-flex flex-wrap gap-3 mt-1">
                                            <?php $n = 1; foreach ($call_type as $key => $value) { $rid = $n++; ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="<?php echo $rid; ?>" name="call_type" value="<?php echo $key; ?>" <?php if (set_value('call_type') == $key) { ?> checked=""<?php } ?>>
                                                    <label class="form-check-label" for="<?php echo $rid; ?>"><?php echo $value; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('call_type'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="edescription" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="enote" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <input type="hidden" name="id" id="generalcall_id">
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_call_log'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_call_log'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('contact'); ?>" name="contact">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input id="date" name="date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                                        <input id="follow_up_date" name="follow_up_date" type="text" class="form-control form-control-sm date" value="<?php echo set_value('follow_up_date'); ?>" readonly="readonly">
                                        <span class="text-danger"><?php echo form_error('follow_up_date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('call_duration'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('call_dureation'); ?>" name="call_dureation">
                                        <span class="text-danger"><?php echo form_error('call_dureation'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('call_type'); ?> <small class="req">*</small></label>
                                        <div class="d-flex flex-wrap gap-3 mt-1">
                                            <?php $n = 1; foreach ($call_type as $key => $value) { $rid = 'add_ct_' . $n++; ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="<?php echo $rid; ?>" name="call_type" value="<?php echo $key; ?>" <?php if (set_value('call_type') == $key) { ?> checked=""<?php } ?>>
                                                    <label class="form-check-label" for="<?php echo $rid; ?>"><?php echo $value; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('call_type'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        /* date init removed - .date class triggers TD 6 via event delegation */
        /* edate init removed - .date class triggers TD 6 via event delegation */
    });

    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        /* follow_up_date init removed - .date class triggers TD 6 via event delegation */
        /* efollow_up_date init removed - .date class triggers TD 6 via event delegation */
    });

    function getRecord(id) {
        shModal('calldetails').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/generalcall/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }
    function delete_ById(id){
        if (confirm('<?php echo $this->lang->line('delete_confirm')?>')) {
         $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/generalcall/delete/' + id,
            success: function (result) {
                successMsg(result.msg);
                $('.ajaxlist').DataTable().ajax.reload(null, false);
            }
        });
     }
     } 
    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/generalcall/get_calls/' + id,
            success: function (result) {
                if (result.call_type == "Incoming") {
                    $('#1').prop("checked", true);
                } else {
                    $('#2').prop("checked", true);
                }
             
                $('#edate').val(result.date);
                $('#efollow_up_date').val(result.follow_up_date);
                $('#epurpose').val(result.purpose);
                $('#ename').val(result.name);
                $('#econtact').val(result.contact);
                $('#eedate').val(result.date);
                $('#edescription').val(result.description);
                $('#enote').val(result.note);
                $('#generalcall_id').val(result.id);
                $('#ecall_dureation').val(result.call_duration);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/generalcall/add',
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
                },
                error: function () {

                }
            });

        }));
    });

    $(document).ready(function (e) {
        $("#editformadd").on('submit', (function (e) {
            $("#editformaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/generalcall/edit',
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
                    $("#editformaddbtn").btnReset();

                },
                error: function () {

                }
            });

        }));
    });

$(".call_log").click(function(){
    $('#formadd').trigger("reset");
});

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'editmyModal', 'calldetails');
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/generalcall/getgeneralcalldatatable');
    });
} ( jQuery ) )
</script>
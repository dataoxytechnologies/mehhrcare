<div class="row">
            <div class="col-md-2">
                <?php
$this->load->view('admin/onlineappointment/appointmentSidebar');
?>
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('shift'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                        <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_add')){ ?>
                                <button onclick="addShiftModal()" class="btn btn-primary btn-sm addpayment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_shift'); ?></button>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('shift'); ?></div>
                            <table class="table table-hover table-striped table-bordered example setup-globalshift-fixed">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('time_from'); ?></th>
                                        <th><?php echo $this->lang->line('time_to'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($shift)){  
                                        foreach ($shift as $shift_key => $shift_value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-bs-toggle="popover" class="detail_popover"><?php echo $shift_value['name'] ?></a>
                                        </td>
                                        <td>
                                            <?php echo $shift_value['start_time'] ?>
                                        </td>
                                        <td>
                                            <?php echo $shift_value['end_time'] ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="rowoptionview mt-mius0">
                                                <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_edit')){ ?>
                                                    <a href="javascript:void(0)" onclick="getRecord('<?php echo (int)$shift_value['id'] ?>')" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_delete')){ ?>
                                                    <a href="javascript:void(0)" class="btn btn-default btn-sm" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/onlineappointment/deleteglobalshift/<?php echo (int)$shift_value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /.card-body -->
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_shift'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addshift" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('shift'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('time_from'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <input type="text" name="time_from" class="form-control time_from timepicker" id="time_from" value="">
                                            <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                        </div>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('time_to'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <input type="text" name="time_to" class="form-control time_to timepicker" id="time_to" value="">
                                            <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="addshiftbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModalEdit" tabindex="-1" aria-labelledby="myModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalEditLabel"><?php echo $this->lang->line('edit_shift'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editshift" method="post" accept-charset="utf-8">
                <input type="hidden" id="shiftid" name="shiftid">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('shift'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                        <input id="edit_name" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('time_from'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <input type="text" name="time_from" class="form-control time_from timepicker" id="edit_time_from" value="">
                                            <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                        </div>
                                    </div>
                                    <div class="mb-0 col-12">
                                        <label class="form-label"><?php echo $this->lang->line('time_to'); ?></label><small class="req"> *</small>
                                        <div class="input-group">
                                            <input type="text" name="time_to" class="form-control time_to timepicker" id="edit_time_to" value="">
                                            <div class="input-group-text"><span class="fa fa-clock-o"></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" id="editshiftbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Time picker init handled globally via event delegation in footer.php
    // (inputs have class="timepicker" — auto-initialized as time-only TD 6 picker)

    $(document).ready(function (e) {
        $('#addshift').on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/onlineappointment/addglobalshift',
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
                            $('.' + index).html(value);
                            message += value;
                        });

                        errorMsg(message);
                    }else if(data.status == "invalid"){
                        errorMsg(data.message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addshiftbtn").btnReset();
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $('#editshift').on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/onlineappointment/updateglobalshift',
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
                    }else if(data.status == "invalid"){
                        errorMsg(data.message);
                    }  else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editshiftbtn").btnReset();
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

     function getRecord(id) {
        shModal('myModalEdit').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/onlineappointment/getglobalshift/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#edit_name").val(data.name);
                $("#shiftid").val(id);
                $("#edit_time_from").val(data.start_time);
                $("#edit_time_to").val(data.end_time);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });
    }

    function addShiftModal(){
        $('#myModal form')[0].reset();
        shModal("myModal").show();
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal');
        modal_click_disabled('myModalEdit');
    });
</script>
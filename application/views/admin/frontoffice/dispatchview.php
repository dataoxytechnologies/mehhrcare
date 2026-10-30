<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('postal_dispatch_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                                <?php if ($this->rbac->hasPrivilege('postal_dispatch', 'can_add')) {?>
                                    <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm adddispatch"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_dispatch'); ?> </a>
                                <?php }?>
                            </div>
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('postal_dispatch_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('postal_dispatch_list') ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('to_title'); ?></th>
                                        <th><?php echo $this->lang->line('reference_no'); ?></th>
                                        <th><?php echo $this->lang->line('from_title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
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


<div class="modal fade sh-modal sh-modal-accent" id="receviedetails" tabindex="-1" aria-labelledby="receviedetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receviedetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="getdetails">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_dispatch'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('postal_dispatch_list'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('to_title'); ?></label> <small class="req"> *</small>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('to_title'); ?>" name="to_title">
                                        <span class="text-danger"><?php echo form_error('to_title'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_no'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('ref_no'); ?>" name="ref_no">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control form-control-sm" id="address" name="address" rows="3"><?php echo set_value('address'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('from_title'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('from_title'); ?>" name="from_title">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input class="filestyle form-control form-control-sm" type="file" name="file" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editmyModal" tabindex="-1" aria-labelledby="editmyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_dispatch'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('postal_dispatch_list'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('to_title'); ?></label> <small class="req"> *</small>
                                        <input type="text" id="efrom_title" class="form-control form-control-sm" value="<?php echo set_value('to_title'); ?>" name="to_title">
                                        <input type="hidden" name="id" id="id">
                                        <span class="text-danger"><?php echo form_error('to_title'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('reference_no'); ?></label>
                                        <input type="text" id="ref_no" class="form-control form-control-sm" value="<?php echo set_value('ref_no'); ?>" name="ref_no">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control form-control-sm" id="eaddress" name="address" rows="3"><?php echo set_value('address'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="enote" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('from_title'); ?></label>
                                        <input type="text" id="to_title" class="form-control form-control-sm" value="<?php echo set_value('from_title'); ?>" name="from_title">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="edate" name="date" placeholder="" type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input class="filestyle form-control form-control-sm" type="file" name="file" />
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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

        /* date_of_call init removed - .date class triggers TD 6 via event delegation */
    });
    function getRecord(id) {
        shModal('receviedetails').show();

        $.ajax({
            url: '<?php echo base_url(); ?>admin/dispatch/details/' + id + '/dispatch',
            success: function (result) {

                $('#getdetails').html(result);
            }
        });
    }

    function get(id) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editmyModal'),{backdrop:'static',keyboard:false}).show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/receive/get_receive/' + id,
            success: function (result) {

                $('#efrom_title').val(result.to_title);
                $('#ref_no').val(result.reference_no);
                $('#ename').val(result.address);
                $('#to_title').val(result.from_title);
                $('#edate').val(result.datedd);
                $('#eaddress').val(result.address);
                $('#enote').val(result.note);
                $('#id').val(result.id);
                $('#eaction_taken').val(result.action_taken);
                $('#eassigned').val(result.assigned);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/dispatch/add',
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
     function delete_ById(id){
        if (confirm('<?php echo $this->lang->line('delete_confirm')?>')) {
         $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/dispatch/imagedelete/' + id,
            success: function (result) {
                successMsg(result.msg);
                $('.ajaxlist').DataTable().ajax.reload(null, false);
            }
        });
     }
     }
    $(document).ready(function (e) {
        $("#editformadd").on('submit', (function (e) {
            $("#editformaddbtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/dispatch/editdispatch',
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

$(".adddispatch").click(function(){
    $('#formadd').trigger("reset");
    $(".dropify-clear").trigger("click");
});

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'receviedetails');
    });

</script>

<!-- //========datatable start===== -->
 <script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/dispatch/getdispatchdatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

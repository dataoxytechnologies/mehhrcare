<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('complain_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                                <?php if ($this->rbac->hasPrivilege('complain', 'can_add')) {?>
                                    <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm complain"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_complain'); ?> </a>
                                <?php }?>
                            </div>
                        </div><!-- /.ms-auto d-flex gap-1 -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('complain_list'); ?>">
                                <thead>
                                    <tr>
                                        <th width="60"><?php echo $this->lang->line('complain'); ?></th>
                                        <th><?php echo $this->lang->line('complain_type'); ?></th>
                                        <th><?php echo $this->lang->line('source'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
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
        </div>


<div class="modal fade sh-modal sh-modal-accent" id="complaintdetails" tabindex="-1" aria-labelledby="complaintdetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintdetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body modal-background" id="getdetails">
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
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_complain'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('complain'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('complain_type'); ?></label>
                                        <select name="complaint" class="form-control form-control-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaint_type as $key => $value) {?>
                                                <option value="<?php print_r($value['id']);?>" <?php if (set_value('complaint') == $value['complaint_type']) {?>selected=""<?php }?>><?php print_r($value['complaint_type']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('source'); ?></label>
                                        <select name="source" class="form-control form-control-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaintsource as $key => $value) {?>
                                                <option value="<?php echo $value['source']; ?>" <?php if (set_value('source') == $value['source']) {?>selected=""<?php }?>><?php echo $value['source']; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('complain_by'); ?></label> <small class="req"> *</small>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('contact'); ?>" name="contact">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label>
                                        <input type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" name="date" id="date" readonly autocomplete="off">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('action_taken'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('action_taken'); ?>" name="action_taken">
                                        <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('assigned'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('assigned'); ?>" name="assigned">
                                        <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                    <div class="col-md-6">
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
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('complain'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body modal-background">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('complain'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('complain_type'); ?></label>
                                        <select name="complaint" id="ecomplaint" class="form-control form-control-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaint_type as $key => $value) {?>
                                                <option value="<?php print_r($value['id']);?>" <?php if (set_value('complaint') == $value['id']) {?>selected=""<?php }?>><?php print_r($value['complaint_type']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('source'); ?></label>
                                        <select name="source" id="esource" class="form-control form-control-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaintsource as $key => $value) {?>
                                                <option value="<?php echo $value['source']; ?>" <?php if (set_value('source') == $value['source']) {?>selected=""<?php }?>><?php echo $value['source']; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('complain_by'); ?></label> <small class="req"> *</small>
                                        <input type="hidden" name="id" id="id">
                                        <input type="text" id="ename" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" id="econtact" class="form-control form-control-sm" value="<?php echo set_value('contact'); ?>" name="contact">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?></label>
                                        <input type="text" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" name="date" id="edate" readonly autocomplete="off">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control form-control-sm" id="edescription" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('action_taken'); ?></label>
                                        <input type="text" id="eaction_taken" class="form-control form-control-sm" value="<?php echo set_value('action_taken'); ?>" name="action_taken">
                                        <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('assigned'); ?></label>
                                        <input type="text" id="eassigned" class="form-control form-control-sm" value="<?php echo set_value('assigned'); ?>" name="assigned">
                                        <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="enote" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                    <div class="col-md-6">
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
                    <button type="submit" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' id="editformaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /.content-wrapper -->
<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        /* date init removed - .date class triggers TD 6 via event delegation */

        /* edate init removed - .date class triggers TD 6 via event delegation */
    });

    function getRecord(id) {

        shModal('complaintdetails').show();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }

    function get(id) {
        shModal('editmyModal').show();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/complaint/get_complaint/' + id,
            success: function (result) {
                $('#ecomplaint').val(result.complaint_type_id);
                $('#esource').val(result.source);
                $('#ename').val(result.name);
                $('#econtact').val(result.contact);
                $('#eedate').val(result.datedd);
                $('#edescription').val(result.description);
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
                url: '<?php echo base_url(); ?>admin/complaint/add',
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
                url: '<?php echo base_url(); ?>admin/complaint/edit',
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

 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $('form#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#formadd').find('select option:selected').removeAttr('selected');
     $('form#formadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

function delete_record(id) {
    if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/delete/' + id,
            success: function (res) {
                successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                window.location.reload(true);
            },
            error: function () {
                alert("Fail")
            }
        });
    }
}

function delete_image_record(id) {
    if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/imagedelete/' + id,
            success: function (res) {
                successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                window.location.reload(true);
            },
            error: function () {
                alert("Fail")
            }
        });
    }
}

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'complaintdetails', 'editmyModal');
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist', 'admin/complaint/getcomplaintdatatable', {}, [], 100, [
            { "bSortable": false, "aTargets": [-1], "sClass": "dt-body-right", "sWidth": "180px" },
            { "sWidth": "110px", "aTargets": [4] },
            { "sWidth": "90px",  "aTargets": [5] }
        ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

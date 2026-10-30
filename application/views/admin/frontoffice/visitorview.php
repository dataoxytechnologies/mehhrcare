<div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo $this->lang->line('visitor_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('visitor_book', 'can_add')) {?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addvisitor"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_visitor'); ?></a>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/generalcall" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('phone_call_log'); ?></a>
                            <?php }if (($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) || ($this->rbac->hasPrivilege('postal_receive', 'can_view'))) {?>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="dropdownMenu1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-reorder"></i> <?php echo $this->lang->line('postal'); ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" role="menu" aria-labelledby="dropdownMenu1">
                                        <?php if ($this->rbac->hasPrivilege('postal_receive', 'can_view')) {?>
                                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>admin/receive"><?php echo $this->lang->line('receive'); ?></a></li>
                                        <?php }if ($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {?>
                                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>admin/dispatch"><?php echo $this->lang->line('dispatch'); ?></a></li>
                                        <?php }?>
                                    </ul>
                                </div>
                            <?php }if ($this->rbac->hasPrivilege('complain', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/complaint" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('complain'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="download_label"><?php echo $this->lang->line('visitor_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table id="visitorTable" class="table table-hover table-striped table-bordered ajaxlist" width="100%" data-export-title="<?php echo $this->lang->line('visitor_list'); ?>"><thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('purpose'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('visit_to'); ?></th>
                                        <th><?php echo $this->lang->line('ipd_opd_staff'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('in_time'); ?></th>
                                        <th><?php echo $this->lang->line('out_time'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                              

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.card-body -->
                </div>
            </div><!--/.col (left) col-8 end-->
            <!-- right column -->
        </div>
    

<!-- new END -->
<div class="modal fade sh-modal sh-modal-accent" id="visitordetails" tabindex="-1" aria-labelledby="visitordetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visitordetailsLabel"><?php echo $this->lang->line('details'); ?></h5>
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
                <h5 class="modal-title" id="editmyModalLabel"><?php echo $this->lang->line('edit_visitor'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/visitors/edit') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('edit_visitor'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('purpose'); ?> <small class="req">*</small></label>
                                        <select name="purpose" id="purpose" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($Purpose as $key => $value) {?>
                                                <option value="<?php print_r($value['visitors_purpose']);?>"<?php if (set_value('purpose') == $value['visitors_purpose']) {?>selected=""<?php }?>><?php print_r($value['visitors_purpose']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input type="text" class="form-control form-control-sm" name="name" id="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="contact" name="contact">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('id_card'); ?></label>
                                        <input type="text" class="form-control form-control-sm" name="id_proof" id="id_proof">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('visit_to'); ?></label>
                                        <select name="visit_to" id="edit_visit_to" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($visit_to as $visit_to_key => $visit_to_value) {
                                                $selected = '';
                                                if (set_value('visit_to') == $visit_to_value) { $selected = 'selected'; }
                                            ?>
                                                <option value="<?php echo $visit_to_key; ?>" <?php echo $selected; ?>><?php echo $visit_to_value; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('visit_to'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('ipd_opd_staff'); ?></label>
                                        <select name="ipd_opd_staff" id="edit_ipd_opd" class="form-control form-control-sm select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('ipd_opd_staff'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('related_to'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="edit_related_to" name="related_to" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('number_of_person'); ?></label>
                                        <input type="text" class="form-control form-control-sm" name="pepples" id="pepples">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input type="text" id="edate" class="form-control form-control-sm date" name="date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('in_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="time" class="form-control form-control-sm timepicker" id="in_time" value="<?php echo set_value('time'); ?>">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('out_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="out_time" class="form-control form-control-sm timepicker" id="out_time" value="<?php echo set_value('out_time'); ?>">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <input type="hidden" id="editid" name="id">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" name="note" id="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input class="filestyle form-control form-control-sm" type="file" name="file">
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="editformaddbtn" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_visitor'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/visitors') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('add_visitor'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('purpose'); ?> <small class="req">*</small></label>
                                        <select name="purpose" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($Purpose as $key => $value) {?>
                                                <option value="<?php print_r($value['visitors_purpose']);?>"<?php if (set_value('purpose') == $value['visitors_purpose']) {?>selected=""<?php }?>><?php print_r($value['visitors_purpose']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('contact'); ?>" name="contact">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('id_card'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('id_proof'); ?>" name="id_proof">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('visit_to'); ?></label>
                                        <select name="visit_to" id="visit_to" class="form-select form-select-sm">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($visit_to as $visit_to_key => $visit_to_value) {?>
                                                <option value="<?php echo $visit_to_key; ?>"><?php echo $visit_to_value; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('visit_to'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><?php echo $this->lang->line('ipd_opd_staff'); ?></label>
                                        <select name="ipd_opd_staff" id="ipd_opd" class="form-control form-control-sm select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('ipd_opd_staff'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('related_to'); ?></label>
                                        <input type="text" class="form-control form-control-sm" id="related_to" name="related_to" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('number_of_person'); ?></label>
                                        <input type="text" class="form-control form-control-sm" value="<?php echo set_value('pepples'); ?>" name="pepples">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input type="text" id="date" class="form-control form-control-sm date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" name="date">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('in_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="time" class="form-control form-control-sm timepicker" id="stime_" value="<?php echo set_value('time'); ?>">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('out_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="out_time" class="form-control form-control-sm timepicker" id="stime_out" value="<?php echo set_value('out_time'); ?>">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control form-control-sm" id="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input class="filestyle form-control form-control-sm" type="file" name="file">
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

$(function () {
    $('.select2').select2()
});

function get(id) {
    shModal('editmyModal').show();
   
    $.ajax({
        dataType: 'json',
        url: '<?php echo base_url(); ?>admin/visitors/get_visitor/' + id,
        success: function (result) {
            $('#purpose').val(result.purpose),
            $('#name').val(result.name);
            $('#contact').val(result.contact);
            $('#id_proof').val(result.id_proof);
            $('#edit_visit_to').val(result.visit_to);
            $('#pepples').val(result.no_of_pepple);
            $('#edate').val(result.datedd);
            $('#in_time').val(result.in_time);
            $('#out_time').val(result.out_time);
            $('#note').val(result.note);
            $('#editid').val(result.id);
            $('#edit_related_to').val(result.related_to);
            populatevisitto(result.visit_to,result.ipd_opd_staff_id);
        }
    });
}

$(document).ready(function (e) {
    $("#formadd").on('submit', (function (e) {
        $("#formaddbtn").btnLoading();
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/add',
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
            url: '<?php echo base_url(); ?>admin/visitors/edit',
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

$(function () {
    $('.select2').select2()
  });

$(function () {
    /* .timepicker init removed - auto-init via event delegation */
});

function getRecord(id) {
shModal('visitordetails').show();
$.ajax({
    url: '<?php echo base_url(); ?>admin/visitors/details/' + id,
    success: function (result) {      
        $('#getdetails').html(result);
    }
});
}

function deletevisitor(id){
    var id = id;
    if(id !=''){
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/deletevisitor',
            type: 'post',
            data:{id:id},
            success: function (data) {
                successMsg("<?php echo $this->lang->line("delete_message") ?>");
                $('.ajaxlist').DataTable().ajax.reload(null, false);
            }
            });
        }
    }else{
        alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
    }
}

function deletevisitorimage(id){
    var id = id;
    if(id !=''){
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/deletevisitorimage',
            type: 'post',
            data:{id:id},
            success: function (data) {
                successMsg("<?php echo $this->lang->line("delete_message") ?>");
                $('.ajaxlist').DataTable().ajax.reload(null, false);
            }
            });
        }
    }else{
        alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
    }
}

</script>
<script type="text/javascript">
 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $("#ipd_opd").select2("val", "");
     $('form#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#formadd').find('select option:selected').removeAttr('selected');
     $('form#formadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'visitordetails', 'editmyModal');
    });
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/visitors/getvisitorsdatatable',[],[],100,[
            { "sWidth": "10%", "aTargets": [0] },
            { "sWidth": "12%", "aTargets": [1] },
            { "sWidth": "8%",  "aTargets": [2] },
            { "sWidth": "16%", "aTargets": [3] },
            { "sWidth": "9%",  "aTargets": [4] },
            { "sWidth": "9%",  "aTargets": [5] },
            { "sWidth": "5%",  "aTargets": [6] },
            { "sWidth": "5%",  "aTargets": [7] },
            { "bSortable": false, "sWidth": "26%", "aTargets": [-1], "sClass": "dt-body-right" }
        ]);
    });

    $(document).on('change', '#visit_to', function (e) {
        var visit_to = $('#visit_to').val();
        populatevisitto(visit_to,'');
    });

    $(document).on('change', '#edit_visit_to', function (e) {
        var visit_to = $('#edit_visit_to').val();
        populatevisitto(visit_to,'');
    });

    $(document).on('change', '#ipd_opd', function (e) {
        $('#related_to').val('');
        $('#related_to').val($("#ipd_opd option:selected" ).text());
    });

    $(document).on('change', '#edit_ipd_opd', function (e) {
        $('#edit_related_to').val('');
        $('#edit_related_to').val($("#edit_ipd_opd option:selected" ).text());
    });

} ( jQuery ) )

function populatevisitto(visit_to,ipd_opd_id) {
    var base_url = '<?php echo base_url() ?>';
    var div_data = '';
    $.ajax({
        type: "GET",
        url: base_url + "admin/visitors/get_ipd_opd_staff_list",
        data: {'visit_to': visit_to},
        dataType: "json",
        success: function (data) {
            $('#ipd_opd').empty();
            $('#edit_ipd_opd').empty();
            $('#ipd_opd').append('<option value=""><?php echo $this->lang->line('select'); ?></option>');
            $('#edit_ipd_opd').append('<option value=""><?php echo $this->lang->line('select'); ?></option>');
            $.each(data.data, function (i, obj)
            {
                var select = "";
                if (ipd_opd_id == obj.id) {
                    var select = "selected=selected";
                }

                if(data.visit_to == 'staff'){
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.name + ' '+ obj.surname+ ' ('+ obj.employee_id +')' + "</option>";
                }else if (data.visit_to == 'ipd_patient') {
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.patient_name + ' ('+ obj.patient_id +')' + ' (<?php echo $this->customlib->getSessionPrefixByType('ipd_no'); ?>'+ obj.id +')' + "</option>";
                }else if (data.visit_to == 'opd_patient') {
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.patient_name + ' ('+ obj.patient_id +')' + ' (<?php echo $this->customlib->getSessionPrefixByType('opd_no'); ?>'+ obj.id +')' + "</option>";
                }

                $('#edit_ipd_opd').append(div_data);
                $('#ipd_opd').append(div_data);
                $(".select2").select2().select2('val', ipd_opd_id);
            });            
        }

    });
}
</script>
<!-- //========datatable end===== -->

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>

<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('tpa_management'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end ms-auto">
                            <?php if ($this->rbac->hasPrivilege('organisation', 'can_add')) { ?>
                                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm organisation"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_tpa'); ?></a> 
                            <?php } ?>
                        </div>    
                    </div><!-- /.card-header -->
                   
                        <div class="card-body">
                            <div class="download_label"><?php echo $title; ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('tpa_management'); ?>">
                                <thead>
                                    <tr>
                                        <th class="sh-th-150"><?php echo $this->lang->line('name'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('code'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('phone'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('address'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('contact_person_name'); ?></th>
                                        <th class="sh-th-150"><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                        <th class="text-end noExport sh-th-150"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                </tbody>
                            </table>
                        </div>
                </div>  
            </div>
        </div>
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="addTpaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTpaLabel"><i class="fa fa-plus me-1"></i> <?php echo $this->lang->line('add_tpa'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label for="name" class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input id="name" name="name" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="code" class="form-label form-label-sm"><?php echo $this->lang->line('code'); ?> <small class="req">*</small></label>
                                        <input id="code" name="code" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('contact_no'); ?> <small class="req">*</small></label>
                                        <input name="contact_number" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea name="address" class="form-control form-control-sm" autocomplete="off"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input name="contact_person_name" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input name="contact_person_phone" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editTpaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTpaLabel"><i class="fa fa-pencil me-1"></i> <?php echo $this->lang->line('edit_tpa'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label for="ename" class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                        <input id="ename" name="ename" type="text" class="form-control form-control-sm" value="<?php echo set_value('ename'); ?>" autocomplete="off">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ecode" class="form-label form-label-sm"><?php echo $this->lang->line('code'); ?> <small class="req">*</small></label>
                                        <input id="ecode" name="ecode" type="text" class="form-control form-control-sm" value="<?php echo set_value('code'); ?>" autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="econtact_number" class="form-label form-label-sm"><?php echo $this->lang->line('contact_no'); ?> <small class="req">*</small></label>
                                        <input id="econtact_number" name="econtact_number" type="text" class="form-control form-control-sm" value="<?php echo set_value('econtact_number'); ?>" autocomplete="off">
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="eaddress" class="form-label form-label-sm"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea name="eaddress" id="eaddress" class="form-control form-control-sm" autocomplete="off"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="econtact_person_name" class="form-label form-label-sm"><?php echo $this->lang->line('contact_person_name'); ?></label>
                                        <input type="hidden" id="org_id" name="org_id">
                                        <input id="econtact_person_name" name="econtact_person_name" type="text" class="form-control form-control-sm" value="<?php echo set_value('econtact_person_name'); ?>" autocomplete="off">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="econtact_person_phone" class="form-label form-label-sm"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input id="econtact_person_phone" name="econtact_person_phone" type="text" class="form-control form-control-sm" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formeditbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function get_orgdata(id) {
        shModal('editModal').show()
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/get_data/' + id,
            dataType: 'json',
            success: function (res) {
                $('#org_id').val(res.id);	
                $('#ename').val(res.ename);
                $('#ecode').val(res.ecode);
                $('#econtact_number').val(res.econtact_number);
                $('#eaddress').val(res.eaddress);
                $('#econtact_person_name').val(res.econtact_person_name);
                $('#econtact_person_phone').val(res.econtact_person_phone);
            }
        });
    }
</script>
<script type="text/javascript">
            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    $("#formaddbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/tpamanagement/add_organisation',
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
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").btnLoading();
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/tpamanagement/edit',
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
                            $("#formeditbtn").btnReset();
                        },
                        error: function () {
                            
                        }
                    });
                }));
            });			
			
$(".organisation").click(function(){
	$('#formadd').trigger("reset");
});

    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'editModal');
    });
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/tpamanagement/gettpadatatable',[],[],100,[
            { "bSortable": false, "aTargets": [-1], "sClass": "dt-body-right", "sWidth": "150px" },
            { "sWidth": "150px", "aTargets": "_all" }
        ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
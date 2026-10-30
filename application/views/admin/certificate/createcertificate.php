<div class="row">           
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card" id="hroom">
                    <div class="card-header ptbnull d-flex align-items-center">
                        <h3 class="card-title titlefix"> <?php echo $this->lang->line('certificate_template_list'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap addmeeting ms-auto">
                             <?php
            if ($this->rbac->hasPrivilege('certificate', 'can_add')) {
                ?>
                <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm uploadcontent"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_certificate_template'); ?>
                            </a>
            <?php } ?>                            
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('certificate_template_list'); ?>; ?></div>
                            <table id="certificateTable" class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('certificate_template_list'); ?>" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('certificate_template_name'); ?></th>
                                        <th class="noExport"><?php echo $this->lang->line('background_image'); ?></th>
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
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    

<!-- Modal -->

<div class="modal fade sh-modal sh-modal-accent" id="myModalview" tabindex="-1" aria-labelledby="myModalviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalviewLabel"><?php echo $this->lang->line('view_certificate_template'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="certificate_detail">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_certificate_template'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('certificate_template_name'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="mb-2">
                                    <label class="form-label"><?php echo $this->lang->line('certificate_template_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="certificate_name" name="certificate_name" placeholder="" type="text" class="form-control form-control-sm" />
                                    <span class="text-danger"><?php echo form_error('certificate_name'); ?></span>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_left_text'); ?></label>
                                        <input id="left_header" name="left_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_center_text'); ?></label>
                                        <input id="center_header" name="center_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_right_text'); ?></label>
                                        <input id="right_header" name="right_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label"><?php echo $this->lang->line('body_text'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control form-control-sm" id="certificate_text" name="certificate_text" placeholder="" rows="3"></textarea>
                                    <span class="text-primary"> <?php echo $this->customlib->getCertificateVariables()?>
                                    </span>
                                    <span class="text-danger"><?php echo form_error('certificate_text'); ?></span>
                                </div>
                                <div class="row g-2 mb-0">
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_left_text'); ?></label>
                                        <input id="left_footer" name="left_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_center_text'); ?></label>
                                        <input id="center_footer" name="center_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_right_text'); ?></label>
                                        <input id="right_footer" name="right_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('certificate_design'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <input id="header_height" name="header_height" placeholder="<?php echo $this->lang->line('header_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="footer_height" name="footer_height" placeholder="<?php echo $this->lang->line('footer_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="content_height" name="content_height" placeholder="<?php echo $this->lang->line('body_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="content_width" name="content_width" placeholder="<?php echo $this->lang->line('body_width'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('patient_photo'); ?></label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input chk" type="checkbox" id="enable_patient_img" name="is_active_patient_img" value="1" onclick="valueChanged()">
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="enableImageDiv" hidden>
                                        <input id="image_height" name="image_height" placeholder="<?php echo $this->lang->line('photo_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label"><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="documents" placeholder="" type="file" class="filestyle form-control form-control-sm" data-height="40" name="background_image">
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

<div class="modal fade sh-modal sh-modal-accent" id="myModaledit" tabindex="-1" aria-labelledby="myModaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModaleditLabel"><?php echo $this->lang->line('edit_certificate_template'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formedit" method="post" accept-charset="utf-8">
                <input type="hidden" id="ecertificate_id" name="id" />
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('certificate_template_name'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="mb-2">
                                    <label class="form-label"><?php echo $this->lang->line('certificate_template_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="ecertificate_name" name="certificate_name" placeholder="" type="text" class="form-control form-control-sm" />
                                    <span class="text-danger"><?php echo form_error('certificate_name'); ?></span>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_left_text'); ?></label>
                                        <input id="eleft_header" name="left_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_center_text'); ?></label>
                                        <input id="ecenter_header" name="center_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('header_right_text'); ?></label>
                                        <input id="eright_header" name="right_header" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label"><?php echo $this->lang->line('body_text'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control form-control-sm" id="ecertificate_text" name="certificate_text" placeholder="" rows="3"></textarea>
                                    <span class="text-primary"> <?php echo $this->customlib->getCertificateVariables()?></span>
                                    <span class="text-danger"><?php echo form_error('certificate_text'); ?></span>
                                </div>
                                <div class="row g-2 mb-0">
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_left_text'); ?></label>
                                        <input id="eleft_footer" name="left_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_center_text'); ?></label>
                                        <input id="ecenter_footer" name="center_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"><?php echo $this->lang->line('footer_right_text'); ?></label>
                                        <input id="eright_footer" name="right_footer" placeholder="" type="text" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('certificate_design'); ?></span>
                            </div>
                            <div class="p-2">
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <input id="eheader_height" name="header_height" placeholder="<?php echo $this->lang->line('header_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="efooter_height" name="footer_height" placeholder="<?php echo $this->lang->line('footer_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="econtent_height" name="content_height" placeholder="<?php echo $this->lang->line('body_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <input id="econtent_width" name="content_width" placeholder="<?php echo $this->lang->line('body_width'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?php echo $this->lang->line('patient_photo'); ?></label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input chk" type="checkbox" id="eenable_patient_img" name="is_active_patient_img" value="1" onclick="valueChangededit()">
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="eenableImageDiv" hidden>
                                        <input id="eimage_height" name="image_height" placeholder="<?php echo $this->lang->line('photo_height'); ?>" type="text" class="form-control form-control-sm" min="0" />
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label"><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="edocuments" placeholder="" type="file" class="filestyle form-control form-control-sm" data-height="40" name="background_image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        window.table = initDatatable('ajaxlist','admin/certificate/getcertificatedatatable',[],[],100,[
            { "sWidth": "120px", "bSortable": false, "aTargets": [-1], 'sClass': 'dt-body-center' },
            { "sWidth": "130px", "aTargets": [-2], 'sClass': 'dt-body-center' }
        ]);
        window.table.on('draw.dt', function () {
            ['nth-last-child(1)','nth-last-child(2)'].forEach(function(sel, i) {
                var w = i === 0 ? '120px' : '130px';
                $('#certificateTable th:' + sel + ', #certificateTable td:' + sel).each(function () {
                    this.style.setProperty('width',     w, 'important');
                    this.style.setProperty('min-width', w, 'important');
                    this.style.setProperty('max-width', w, 'important');
                });
            });
        });
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->              
<script type="text/javascript">
    $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/certificate/create',
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
                        url: '<?php echo base_url(); ?>admin/certificate/edit',
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

    $(document).ready(function () {
        /* #postdate init removed - auto-init via class + event delegation */
    });
    
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        popup(jQuery(elem).html());
    }     
</script>
<script>
            $(document).ready(function () {
                $('.detail_popover').popover({
                    placement: 'right',
                    trigger: 'hover',
                    container: 'body',
                    html: true,
                    content: function () {
                    }
                });
            });

            function getRecord(id) {             
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/certificate/getcertificate',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                       $("#ecertificate_id").val(data.id);
                        $("#ecertificate_name").val(data.certificate_name);
                        $("#ecertificate_text").val(data.certificate_text);
                        $("#eleft_header").val(data.left_header);
                        $("#ecenter_header").val(data.center_header);
                        $("#eright_header").val(data.right_header);
                        $("#eleft_footer").val(data.left_footer);
                        $("#ecenter_footer").val(data.center_footer);
                        $("#eright_footer").val(data.right_footer);
                        $("#econtent_height").val(data.content_height);
                        $("#eheader_height").val(data.header_height);
                        $("#efooter_height").val(data.footer_height);
                        $("#econtent_width").val(data.content_width);
                        $("#eimage_height").val(data.enable_image_height);
                        $("#enote").text(data.note);
                        var enable_patient_img = data.enable_patient_image ;
                        if (enable_patient_img == 1) {
                            $( "#eenable_patient_img" ).prop( "checked", true );
                            $("#eenableImageDiv").show();                            
                        } else {
                            $( "#eenable_patient_img" ).prop( "checked", false );
                            $("#eenableImageDiv").hide();
                        }
                    },
                });
                 shModal('myModaledit').show();
            }

        function viewRecord(id) {          
            $.ajax({
                url: "<?php echo base_url('admin/certificate/view') ?>",
                method: "post",
                data: {certificateid: id},
                success: function (data) {
                    $('#certificate_detail').html(data);
                    shModal('myModalview').show();
                }
            });
        }

        function Delete_recored(id) {
            var txt;
               if (confirm("Are You Sure !")) {
                $.ajax({
                    url: "<?php echo base_url('admin/certificate/delete/') ?>"+id,
                    method: "post",
                    data: { },
                    success: function (data) {
                        window.location.reload(true);
                    }
                });
            }             
      }
</script>

<script type="text/javascript">
    function valueChanged()
    {
        if ($('#enable_patient_img').is(":checked"))
            $("#enableImageDiv").show();       
        else
            $("#enableImageDiv").hide();     
    }

    function valueChangededit()
    {
        if ($('#eenable_patient_img').is(":checked"))
            $("#eenableImageDiv").show();      
        else
            $("#eenableImageDiv").hide();        
    }
    
    $(document).ready(function (e) {
        modal_click_disabled('myModal', 'myModaledit', 'myModalview');
    });
</script>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title"><?php echo $this->lang->line('live_consult'); ?></h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap ms-auto">
                            <?php
if ($this->rbac->hasPrivilege('live_consultation', 'can_add')) {
    ?>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-online-timetable"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?> </button>
                             <?php
if ($conference_setting->use_doctor_api > 0) {
        ?>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-credential"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_credential'); ?></button>
                         <?php }}?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg') ?>
                         <?php $this->session->unset_userdata('msg'); }   ?>
                        <div class="table-responsive">
                        <div class="download_label"><?php echo $this->lang->line('live_consult'); ?></div>
                            <table class="table table-hover table-striped table-bordered ajaxlistconsult" data-export-title="<?php echo $this->lang->line('live_consult'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('api_used'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="modal-online-timetable" tabindex="-1" aria-labelledby="modal-online-timetable-label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-online-timetable-label"><?php echo $this->lang->line('live_consult'); ?></h5>
                <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax sh-maxw-220" <?php if ($disable_option == true) { echo "disabled"; } ?> name="" id="addpatient_id"></select>
                <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                    <a onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-light btn-sm text-nowrap"><i class="fa fa-plus"></i><span><?php echo $this->lang->line('new_patient'); ?></span></a>
                <?php } ?>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-addconference" accept-charset="utf-8" action="<?php echo base_url() . "admin/zoom_conference/addByOther" ?>" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input name="patient_id" id="patient_id" type="hidden" />
                            <input name="email" id="pemail" type="hidden" />
                            <input name="mobileno" id="mobnumber" type="hidden" />
                            <input name="patient_name" id="patientname" type="hidden" />
                            <input type="hidden" id="password" name="password">
                            <div class="row row-eq h-vh-lg-100-100">
                                <div class="col-lg-7 col-md-7 col-sm-12 ot-left-panel">
                                    <div id="ajax_load"></div>
                                    <div class="sh-form-card d-none" id="patientDetails">
                                        <div class="sh-card-header">
                                            <span class="sh-card-header-title"><?php echo $this->lang->line('patient_details'); ?></span>
                                        </div>
                                        <div class="sh-info-grid">
                                            <div class="row g-0">
                                                <div class="col-8 sh-info-item">
                                                    <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                                                    <span class="sh-info-value highlight" id="listname"></span>
                                                </div>
                                                <div class="col-4 d-flex align-items-center justify-content-center p-2">
                                                    <img class="modal-profile-user-img img-fluid d-none" src="" id="image" alt="User profile picture">
                                                    <div class="modal-profile-user-initials d-none" id="image_initials"></div>
                                                </div>
                                            </div>
                                            <div class="row g-0 sh-row-divider">
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-user-secret"></i> <?php echo $this->lang->line('guardian'); ?></span>
                                                    <span class="sh-info-value" id="guardian"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-venus-mars"></i> <?php echo $this->lang->line('gender'); ?></span>
                                                    <span class="sh-info-value" id="genders"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-tint"></i> <?php echo $this->lang->line('blood_group'); ?></span>
                                                    <span class="sh-info-value" id="blood_group"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-ring"></i> <?php echo $this->lang->line('marital_status'); ?></span>
                                                    <span class="sh-info-value" id="marital_status"></span>
                                                </div>
                                            </div>
                                            <div class="row g-0 sh-row-divider">
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-hourglass-half"></i> <?php echo $this->lang->line('age'); ?></span>
                                                    <span class="sh-info-value" id="age"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fa fa-phone-square"></i> <?php echo $this->lang->line('phone'); ?></span>
                                                    <span class="sh-info-value" id="listnumber"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email'); ?></span>
                                                    <span class="sh-info-value" id="email"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-street-view"></i> <?php echo $this->lang->line('address'); ?></span>
                                                    <span class="sh-info-value" id="address"></span>
                                                </div>
                                            </div>
                                            <div class="row g-0 sh-row-divider">
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-allergies"></i> <?php echo $this->lang->line('any_known_allergies'); ?></span>
                                                    <span class="sh-info-value" id="allergies"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('remarks'); ?></span>
                                                    <span class="sh-info-value" id="note"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-id-badge"></i> <?php echo $this->lang->line('tpa_id'); ?></span>
                                                    <span class="sh-info-value" id="tpa_id"></span>
                                                </div>
                                                <div class="col-6 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-calendar-check"></i> <?php echo $this->lang->line('tpa_validity'); ?></span>
                                                    <span class="sh-info-value" id="tpa_validity"></span>
                                                </div>
                                            </div>
                                            <div class="row g-0 sh-row-divider">
                                                <div class="col-12 sh-info-item">
                                                    <span class="sh-info-label"><i class="fas fa-fingerprint"></i> <?php echo $this->lang->line('national_identification_number'); ?></span>
                                                    <span class="sh-info-value" id="identification_number"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-12">
                                    <div class="sh-form-card">
                                        <div class="sh-card-header">
                                            <span class="sh-card-header-title"><?php echo $this->lang->line('live_consultation'); ?></span>
                                        </div>
                                        <div class="p-2">
                                            <div class="row g-3">
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('live_consultation_title') ; ?></label>
                                                        <input class="form-control form-control-sm" type="text" name="title" />
                                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('consultation_date'); ?> <small class="req"> *</small> </label>
                                                        <input id="date" name="date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat(true, true))); ?>" type="text" class="form-control form-control-sm datetime" />
                                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm" for="duration"><?php echo $this->lang->line('consultation_duration_minutes'); ?> <small class="req"> *</small> </label>
                                                        <input type="number" class="form-control form-control-sm" id="duration" name="duration">
                                                        <span class="text-danger"><?php echo form_error('duration'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('opd_ipd'); ?></label>
                                                        <select name="select_group" id="" onchange="getopdipd(this.value)" class="form-select form-select-sm module_type">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php foreach ($opd_ipd as $key => $opd_ipd_value) { ?>
                                                                <option value="<?php echo $key; ?>"><?php echo $opd_ipd_value; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('opd_ipd_no'); ?></label>
                                                        <select class="form-control select2 w-100" name="opdipd_id" id="opdipd_no">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('opdipd_no'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 visit_div d-none">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line("checkup_id"); ?> <small class="req"> *</small> </label>
                                                        <select class="form-control select2 w-100" name="visit_id" id="visit_no">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('visit_id'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?> <small class="req"> *</small> </label>
                                                        <select name="staff_id" id="consultant_doctor" class="form-control select2 w-100" <?php if ($disable_option == true) { echo "disabled"; } ?>>
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) { echo "selected"; } ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"], $dvalue["employee_id"]); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="staff_id" value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                        <span class="text-danger"><?php echo form_error('staff_id'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="sh-field-label"><?php echo $this->lang->line('host_video'); ?> <small class="req"> *</small></label>
                                                        <div class="sh-radio-group">
                                                            <label class="sh-radio-option">
                                                                <input type="radio" name="host_video" value="1" checked>
                                                                <span><?php echo $this->lang->line('enable'); ?></span>
                                                            </label>
                                                            <label class="sh-radio-option">
                                                                <input type="radio" name="host_video" value="0">
                                                                <span><?php echo $this->lang->line('disabled'); ?></span>
                                                            </label>
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('host_video'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="sh-field-label"><?php echo $this->lang->line('client_video'); ?> <small class="req"> *</small></label>
                                                        <div class="sh-radio-group">
                                                            <label class="sh-radio-option">
                                                                <input type="radio" name="client_video" value="1" checked>
                                                                <span><?php echo $this->lang->line('enable'); ?></span>
                                                            </label>
                                                            <label class="sh-radio-option">
                                                                <input type="radio" name="client_video" value="0">
                                                                <span><?php echo $this->lang->line('disabled'); ?></span>
                                                            </label>
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('client_video'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label class="form-label form-label-sm"><?php echo $this->lang->line('description'); ?></label>
                                                        <textarea class="form-control form-control-sm" name="description" id="description" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div><!--./pup-scroll-area-->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Saving..."><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div class="modal fade sh-modal sh-modal-accent" id="modal-credential" tabindex="-1" aria-labelledby="modal-credential-label" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form id="form-addcredential" action="<?php echo site_url('admin/zoom_conference/addcredential'); ?>" method="POST">
            <div class="modal-content mx-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-credential-label"><i class="fa fa-key me-1"></i> <?php echo $this->lang->line('add_credential'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('details'); ?></span>
                        </div>
                    <div class="p-2">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label form-label-sm" for="zoom_api_key"><?php echo $this->lang->line('zoom_api_key') ?> <small class="req"> *</small> </label>
                            <input type="text" class="form-control form-control-sm" id="zoom_api_key" name="zoom_api_key">
                            <span class="text text-danger" id="title_error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label form-label-sm" for="zoom_api_secret"><?php echo $this->lang->line('zoom_api_secret'); ?> <small class="req"> *</small> </label>
                            <input type="text" class="form-control form-control-sm" id="zoom_api_secret" name="zoom_api_secret">
                            <span class="text text-danger" id="title_error"></span>
                        </div>
                    </div>
                    </div><!--./p-2-->
                    </div><!--./sh-form-card-->
                </div>
                </div><!--./pup-scroll-area-->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" value="reset" id="submit-btn-credential" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating..."><?php echo $this->lang->line('reset') ?></button>
                    <button type="submit" class="btn btn-primary" value="save" id="submit-btn-credential" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving..."><?php echo $this->lang->line('save') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal-chkstatus" class="modal fade sh-modal sh-modal-accent" tabindex="-1" aria-labelledby="modal-chkstatus-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-chkstatus-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="zoom_details">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

     function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }
$(document).on('change', '.module_type', function () {
    var mode = $(this).val();
    if (mode == "opd") {
        $('.visit_div').removeClass('d-none');
    } else {
        $('.visit_div').addClass('d-none');
    }
});

$(document).on('change', '#opdipd_no', function () {
    getvisitdetailsid($(this).val());
});
	
    function getvisitdetailsid(opdid) {
        var visitcheckupno = "<?php echo $this->customlib->getSessionPrefixByType('checkup_id') ?>"
        var div_data = "";
        $('#visit_no').html("<option value=''><?php echo $this->lang->line('loading') ?></option>").trigger('change');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getVisitDetailsbyopdid',
            type: "POST",
            data: {opdid: opdid},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) {
                    div_data += "<option value=" + obj.id + ">" + visitcheckupno + obj.id + "</option>";
                });
                $("#visit_no").html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#visit_no').append(div_data);
                $("#visit_no").val('').trigger('change');
            }
        });
    }
	
    $('#modal-credential').on('shown.bs.modal', function (e) {
        var $modalDiv = $(e.delegateTarget);

        $.ajax({
            type: "POST",
            url: base_url + 'admin/zoom_conference/getcredential',
            data: {},
            dataType: "JSON",
            beforeSend: function () {
                $modalDiv.addClass('modal_loading');
            },
            success: function (data) {
                $('#zoom_api_key').val(data.zoom_api_key);
                $('#zoom_api_secret').val(data.zoom_api_secret);
                $modalDiv.removeClass('modal_loading');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $modalDiv.removeClass('modal_loading');
            },
            complete: function (data) {
                $modalDiv.removeClass('modal_loading');
            }
        });
    })

     $("form#form-addcredential").submit(function (event) {
            event.preventDefault();
            var $form = $(this),
                    url = $form.attr('action');
            var $button = $form.find("button[type=submit]:focus");
            var formData = $form.serializeArray();
            formData.push({name: 'button', value: $button.val()});
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType: "JSON",
                beforeSend: function () {
                    $button.btnLoading();
                },
                success: function (data) {
                    if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {

                        shModal('modal-credential').hide();
                        successMsg(data.message);
                    }
                    $button.btnReset();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $button.btnReset();
                },
                complete: function (data) {
                    $button.btnReset();
                }
            });
        })

        $(function () {
                $('#easySelectable').easySelectable();
                $('.select2').select2();
        })

        $('#modal-online-timetable').on('shown.bs.modal', function () {
            var $sel = $('#addpatient_id');
            if ($sel.hasClass('select2-hidden-accessible')) { $sel.select2('destroy'); }
            $sel.select2({
                ajax: {
                    url: "<?php echo base_url('admin/patient/getPatientListAjax'); ?>",
                    type: "post", dataType: 'json', delay: 250,
                    data: function (params) { return { searchTerm: params.term }; },
                    processResults: function (response) { return { results: response }; },
                    cache: true
                },
                placeholder: '<?php echo $this->lang->line('select_patient'); ?>',
                minimumInputLength: 2,
                dropdownParent: $('#modal-online-timetable')
            });
        });

    function renderConsultAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $('#image').attr('src', '<?php echo base_url() ?>' + imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $('#image_initials').text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?' : parts.length === 1 ? parts[0].charAt(0) : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $('#image').addClass('d-none').removeAttr('src');
            $('#image_initials').text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    function get_PatientDetails(id) {
        var base_url = "<?php echo $this->media_storage->getImageURL('backend/images/loading.gif') ?>";

        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        if(id==''){
            $("#ajax_load").html("");
            $("#patientDetails").addClass("d-none");
       }else{
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").removeClass("d-none");
                    $('#patient_unique_id').text(res.id);
                    $('#patient_id').val(res.id);
                    $('#listname').text(res.patient_name_formatted);
                    $('#guardian').text(res.guardian_name);
                    $('#listnumber').text(res.mobileno);
                    $('#email').text(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    $('#patientname').val(res.patient_name);
                    $('#age').text(res.patient_age+" "+res.as_of_date);
                    $('#tpa_validity').text(res.insurance_validity);
                    $('#tpa_id').text(res.insurance_id);
                    $('#identification_number').text(res.identification_number);
                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").text(res.bp);
                    $("#symptoms").text(res.symptoms);
                    $("#known_allergies").text(res.known_allergies);
                    $("#address").text(res.address);
                    $("#note").text(res.note);
                    $("#height").text(res.height);
                    $("#weight").text(res.weight);
                    $("#genders").text(res.gender);
                    $("#marital_status").text(res.marital_status);
                    $('#blood_group').text(res.blood_group_name);
                    $("#allergies").text(res.known_allergies);
                    renderConsultAvatar(res.image, res.patient_name);
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").addClass("d-none");
                }
            }
        });
    }
    }

    function getopdipd(opdipd_group) {
        if (opdipd_group == "opd") {
            var opdipdno = "<?php echo $this->customlib->getSessionPrefixByType('opd_no')?>";
        }else{
            var opdipdno = "<?php echo $this->customlib->getSessionPrefixByType('ipd_no')?>";
        }
        var pid = $('#patient_id').val();      
        var div_data = "";
        $('#opdipd_no').html("<option value=''><?php echo $this->lang->line('loading') ?></option>").trigger('change');

        $.ajax({
            url: baseurl+'admin/zoom_conference/getopdipd',
            type: "POST",
            data: {opdipd_group: opdipd_group, patient_id: pid},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) {
                    div_data += "<option value=" + obj.id + ">" + opdipdno + obj.id + "</option>";
                });
                $("#opdipd_no").html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#opdipd_no').append(div_data);
                $("#opdipd_no").val('').trigger('change');
            }
        });
    }

    (function ($) {
        //selectable html elements
        $.fn.easySelectable = function (options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function (el) {

                },
                onSelected: function (el) {

                },
                onUnSelected: function (el) {

                }
            }, options);
            el.on('dragstart', function (event) {
                event.preventDefault();
            });
            el.off('mouseover');
            el.addClass('easySelectable');
            if (options.state) {
                el.find(options.item).addClass('es-selectable');
                el.on('mousedown', options.item, function (e) {
                    $(this).trigger('start_select');
                    var offset = $(this).offset();
                    var hasClass = $(this).hasClass('es-selected');
                    var prev_el = false;
                    el.on('mouseover', options.item, function (e) {
                        if (prev_el == $(this).index())
                            return true;
                        prev_el = $(this).index();
                        var hasClass2 = $(this).hasClass('es-selected');
                        if (!hasClass2) {
                            $(this).addClass('es-selected').trigger('selected');
                            el.trigger('selected');
                            options.onSelecting($(this));
                            options.onSelected($(this));
                        } else {
                            $(this).removeClass('es-selected').trigger('unselected');
                            el.trigger('unselected');
                            options.onSelecting($(this))
                            options.onUnSelected($(this));
                        }
                    });
                    if (!hasClass) {
                        $(this).addClass('es-selected').trigger('selected');
                        el.trigger('selected');
                        options.onSelecting($(this));
                        options.onSelected($(this));
                    } else {
                        $(this).removeClass('es-selected').trigger('unselected');
                        el.trigger('unselected');
                        options.onSelecting($(this));
                        options.onUnSelected($(this));
                    }
                    var relativeX = (e.pageX - offset.left);
                    var relativeY = (e.pageY - offset.top);
                });
                $(document).on('mouseup', function () {
                    el.off('mouseover');
                });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);

    //===========================form submit==========
    $("form#form-addconference").submit(function (event) {
        event.preventDefault();

        var $form = $(this),
                url = $form.attr('action');
        var $button = $form.find("button[type=submit]:focus");
        $.ajax({
            type: "POST",
            url: url,
            data: $form.serialize(),
            dataType: "JSON",
            beforeSend: function () {
                $button.btnLoading();

            },
            success: function (data) {
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {

                    shModal('modal-online-timetable').hide();
                    successMsg(data.message);
                    window.location.reload(true);
                }
                $button.btnReset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $button.btnReset();
            },
            complete: function (data) {
                $button.btnReset();
            }
        });
    })

    //================================================
    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    $('#modal-online-timetable').on('hidden.bs.modal', function () {
        $(this).find("input,textarea,select").not("input[type=radio]")
                .val('')
                .end();
        $(this).find("input[type=checkbox], input[type=radio]")
                .prop('checked', false);
        $('input:radio[name="host_video"][value="1"]').prop('checked', true);
        $('input:radio[name="client_video"][value="1"]').prop('checked', true);
    });

    function Converttimeformat(time) {
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12)
            hrs = hrs + 12;
        if (format == "AM" && hrs == 12)
            hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10)
            hours = "0" + hours;
        if (mnts < 10)
            minutes = "0" + minutes;

        return {
            hours: hours,
            minutes: minutes,
            second: 0
        };
    }
</script>
<script type="text/javascript">

    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

    $('#modal-online-timetable').on('shown.bs.modal', function (e) {
        var password = makeid(5);
        $('#password').val("").val(password);
    });
    
    $(document).ready(function (e) {
        modal_click_disabled('modal-online-timetable', 'modal-chkstatus');
    });
    
        // Fetch the status content FIRST, then open the modal once #zoom_details is populated.
        $(document).on('click', '.js-open-livestatus', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('disabled')) { return; }
            var id = $btn.data('id');
            $btn.addClass('disabled');
            $('#zoom_details').html("");
            $.ajax({
                type: "POST",
                url: base_url + 'admin/zoom_conference/getlivestatus',
                data: {'id': id},
                dataType: "JSON",
                success: function (data) {
                    $('#zoom_details').html(data.page);
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-chkstatus')).show();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                },
                complete: function () { $btn.removeClass('disabled'); }
            });
        })
</script>
<script type="text/javascript">
       $('#modal-classteacher-timetable').on('shown.bs.modal', function (e) {
        $("#class_id",this).prop("selectedIndex", 0);
        $("#section_id",this).find('option:not(:first)').remove();
        var password = makeid(5);
        $('#password',this).val("").val(password);
    });

    $(document).on('change', '#form-addconference #class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, 0);
    });

    function getSectionByClass(class_id, section_id) {
        if (class_id != "") {
            $('#form-addconference #section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#form-addconference #section_id').addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#form-addconference #section_id').append(div_data);
                },
                complete: function () {
                    $('#form-addconference #section_id').removeClass('dropdownloading');
                }
            });
        }
    }
</script>
<script type="text/javascript">
    $(document).on('change','.chgstatus_dropdown',function(){
        $(this).parent('form.chgstatus_form').submit()
    });

    $("form.chgstatus_form").submit(function(e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           dataType:"JSON",
           success: function(data)
           {
               if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
           }
         });
});

$(".consultation").click(function(){
    $('#form-addconference').trigger("reset");
    $('#select2-addpatient_id-container').html('');
    $('#select2-consultant_doctor-container').html('');
});
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
   
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlistconsult','admin/zoom_conference/getconsultdatatable',[],[],100);
      
    });

} ( jQuery ) )
</script>
<?php $this->load->view('admin/patient/patientaddmodal')?>
<!-- //========datatable end===== -->
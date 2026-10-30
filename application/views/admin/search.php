<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$marital_status = $this->config->item('marital_status');
?>
<div class="row">
            <div class="col-md-12">
                <input type="hidden" name="search_text" id="search_text" value="<?php echo html_escape($search_text); ?>">
                <div class="card">
                    <div class="card-header ptbnull">
                        <h3 class="card-title titlefix"><?php echo form_error('Opd'); ?>
                            <?php
                            echo $this->lang->line('patient_list');
                            ?>
                        </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a href="javascript:void(0)" onclick="shModal('myModalpa').show()" id="addp" class="btn btn-primary btn-sm newpatient"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new_patient'); ?></a>
                            <?php
                            }
                            if ($this->rbac->hasPrivilege('patient_import', 'can_view')) {
                            ?>
                                <a data-bs-toggle="" href="<?php echo base_url() ?>admin/patient/import" id="addp" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> <?php echo $this->lang->line('import_patient'); ?></a>
                            <?php }
                            if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) {
                            ?>
                                <a href="<?php echo base_url() ?>admin/admin/disablepatient" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('disabled_patient_list'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?>
                            <div class="d-flex justify-content-end pb-3">
                                <button type="submit" class="btn btn-primary btn-sm delete_selected" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> "><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_selected'); ?></button>
                            </div>
                        <?php } ?>

                        <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?= $this->lang->line('patient_list'); ?>">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkAll"> #</th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                    <th><?php echo $this->lang->line('dead'); ?></th>
                                    <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                    ?>
                                            <th><?php echo html_escape(ucfirst($fields_value->name)); ?></th>
                                    <?php }
                                    } ?>
                                    <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade sh-modal sh-modal-nospace" id="myModal" tabindex="-1" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_head"></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id='edit_delete'>
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                        ?>
                            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line('close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
            <div class="modal-body">
                <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                    <input name="id" type="hidden" id="patientid">
                    <div class="bill-patient-panel">
                        <div class="d-flex align-items-center gap-4 flex-wrap">

                            <!-- Photo + patient name -->
                            <div class="bill-patient-photo text-center flex-shrink-0">
                                <?php $file = "uploads/patient_images/no_image.png"; ?>
                                <img class="bill-patient-avatar d-none" src="<?php echo $this->media_storage->getImageURL($file) ?>" id="image" alt="">
                                <div class="bill-patient-initials" id="image_initials" aria-hidden="true"></div>
                                <div class="mt-1 fw-semibold"><span id="patient_name"></span></div>
                            </div>

                            <!-- Info grid -->
                            <div class="flex-grow-1 min-w-0" id="Myinfo">
                                <div class="bill-info-grid">
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('guardian'); ?></div>
                                        <div class="bii-value"><span id="guardian"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('gender'); ?></div>
                                        <div class="bii-value"><span id="genders"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('blood_group'); ?></div>
                                        <div class="bii-value"><span id="blood_group"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('marital_status'); ?></div>
                                        <div class="bii-value"><span id="marital_status"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('age'); ?></div>
                                        <div class="bii-value"><span id="age"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('phone'); ?></div>
                                        <div class="bii-value"><span id="contact"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('email'); ?></div>
                                        <div class="bii-value"><span id="email"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('address'); ?></div>
                                        <div class="bii-value"><span id="address"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('any_known_allergies'); ?></div>
                                        <div class="bii-value"><span id="allergies"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('remarks'); ?></div>
                                        <div class="bii-value"><span id="note"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('tpa'); ?></div>
                                        <div class="bii-value"><span id="organisation_name"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                                        <div class="bii-value"><span id="insurance_id"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                                        <div class="bii-value"><span id="validity"></span></div>
                                    </div>
                                    <div class="bii">
                                        <div class="bii-label"><?php echo $this->lang->line('national_identification_number'); ?></div>
                                        <div class="bii-value"><span id="identification_number"></span></div>
                                    </div>
                                    <div class="bii" id="show_barcode">
                                        <div class="bii-label"><?php echo $this->lang->line('barcode'); ?></div>
                                        <div class="bii-value"><a href="" id="getbarcode_link" target="_blank"><img class="sh-qr-code" src="" id="getbarcode" width="100" height="32"></a></div>
                                    </div>
                                    <div class="bii" id="show_qrcode">
                                        <div class="bii-label"><?php echo $this->lang->line('qrcode'); ?></div>
                                        <div class="bii-value"><a href="" id="getqrcode_link" target="_blank"><img class="sh-qr-code" src="" id="getqrcode" width="44" height="44"></a></div>
                                    </div>
                                    <!-- Custom fields render here as additional .bii grid cells (see JS) -->
                                    <div id="field_data"></div>
                                </div>
                            </div><!-- ./flex-grow-1 -->

                        </div>
                    </div>
                    <div id="visit_report_id"></div>
                </form>
            </div>
            </div><!-- /pup-scroll-area -->
        </div>
    </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('edit_patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <input id="eupdateid" name="updateid" type="hidden" value="" />

                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_information'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                            <input id="ename" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>">
                                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('guardian_name'); ?></label>
                                            <input type="text" name="guardian_name" id="eguardian_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                            <input id="emobileno" autocomplete="off" name="contact" type="text" class="form-control" value="<?php echo set_value('mobileno'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('gender'); ?></label>
                                            <select class="form-control" name="gender" id="egenders">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($genderList as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                            <input type="text" name="dob" class="form-control date editpatient_dob">
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="calculate">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('age') . ' (' . $this->lang->line('yy_mm_dd') . ')'; ?><small class="req"> *</small></label>
                                            <div class="row g-1">
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="age_year" class="form-control patient_age_year">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" id="age_month" class="form-control patient_age_month">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" id="age_day" class="form-control patient_age_day">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('blood_group'); ?></label>
                                            <select class="form-control" id="blood_groups" name="blood_group">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($bloodgroup as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('marital_status'); ?></label>
                                            <select name="marital_status" id="marital_statuss" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($marital_status as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('marital_status') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('email'); ?></label>
                                            <input type="text" id="eemail" name="email" class="form-control" value="<?php echo set_value('email'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                            <input name="address" id="eaddress" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('national_identification_number'); ?></label>
                                            <input name="identification_number" id="edit_identification_number" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('patient_photo'); ?></label>
                                            <input class="filestyle form-control" type="file" name="file" id="exampleInputFile" size="20" data-height="26" data-default-file="<?php echo base_url(); ?>uploads/patient_images/no_image.png">
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fas fa-notes-medical me-1"></i> <?php echo $this->lang->line('additional_information'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('remarks'); ?></label>
                                            <textarea name="note" id="enote" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                            <textarea name="known_allergies" id="eknown_allergies" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa'); ?></label>
                                            <select class="form-control" name="organisation_id" id="edit_organisation_id">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($organisation as $orgkey => $orgvalue) { ?>
                                                <option value="<?php echo (int)$orgvalue['id']; ?>"><?php echo html_escape($orgvalue['organisation_name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa_id'); ?></label>
                                            <input name="insurance_id" id="edit_insurance_id" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa_validity'); ?></label>
                                            <input name="validity" id="insurance_validity" class="form-control date">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div id="customfield"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- //========datatable start===== -->
<script type="text/javascript">
    (function($) {
        'use strict';
        $(document).ready(function() {
            var search_text = $('#search_text').val();
            initDatatable('ajaxlist', 'admin/admin/getpatientdatatable', {
                'search_text': search_text
            }, [], 100, [{
                "bSortable": false,
                "aTargets": [-1,0]
            }]);

            $(document).on('draw.dt', '.ajaxlist', function() {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            });
        })
    }(jQuery))
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    function renderPatientAvatar(imageUrl, patientName) {
        var name = (patientName || '').replace(/\s*\([^)]*\)\s*/g, ' ').trim();
        var hasRealImage = imageUrl && imageUrl.indexOf('no_image') === -1;
        if (hasRealImage) {
            $("#image").attr("src", imageUrl + '<?php echo img_time(); ?>').removeClass('d-none');
            $("#image_initials").text('').addClass('d-none');
        } else {
            var parts = name.split(/\s+/).filter(Boolean);
            var initials = parts.length === 0 ? '?'
                : parts.length === 1 ? parts[0].charAt(0)
                : parts[0].charAt(0) + parts[parts.length - 1].charAt(0);
            $("#image").addClass('d-none').removeAttr('src');
            $("#image_initials").text(initials.toUpperCase()).removeClass('d-none');
        }
    }

    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }

    function holdModal(modalId) {
        (function(){var _el=document.getElementById(modalId);if(_el)bootstrap.Modal.getOrCreateInstance(_el,{backdrop:'static',keyboard:false}).show();})();;
    }

    function getpatientData(id) {
        $('#modal_head').html("<?php echo $this->lang->line('patient_details'); ?>");
        $.ajax({
            url: baseurl + 'admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {

                if (data.is_active == 'yes') {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='patient_deactive(" + id + ")' data-placement='bottom' title='<?php echo $this->lang->line('disable'); ?>'><i class='fa fa-thumbs-o-down'></i></a><?php } ?>";
                } else {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='patient_active(" + id + ")' data-placement='bottom' title='<?php echo $this->lang->line('enable'); ?>'><i class='fa fa-thumbs-o-up'></i></a> <?php }

                    if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' data-placement='bottom' onclick='delete_record(" + id + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a> <?php } ?>";
                }

                var table_html = '';
                $.each(data.field_data, function(i, obj) {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                    var name = obj.name;
                    table_html += "<div class='bii'><div class='bii-label'>" + capitalizeFirstLetter(name) + "</div><div class='bii-value'>" + field_value + "</div></div>";
                });

                $("#field_data").html(table_html);
                $("patientid").val(data.id);
                $("#patient_name").html(data.patient_name + " (" + data.id + ")");
                $("#guardian").html(data.guardian_name);
                $("#patients_id").html(data.patient_unique_id);
                $("#genders").html(data.language_gender);
                $("#marital_status").html(data.language_marital_status);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#address").html(data.address);
                $("#is_active").html(data.is_active);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $("#age").html(data.patient_age);
                $("#allergies").html(data.known_allergies); 
                $("#insurance_id").html(data.insurance_id);
                $("#validity").html(data.insurance_validity);
                $("#organisation_name").html(data.organisation_name);
                $('select[id="edit_organisation_id"] option[value="' + data.organisation_id + '"]').attr("selected", "selected");
                $("#identification_number").html(data.identification_number);
                $("#blood_group").html(data.blood_group_name);
                $("#note").html(data.note);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('patient', 'can_edit')) { ?><a href='#' class='btn btn-sm btn-light' onclick='editRecord(" + id + ")' data-bs-toggle='tooltip' data-placement='bottom' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?> " + link + "");

                renderPatientAvatar(data.image, data.patient_name);

                if (data.getbarcode == null) {
                    $("#show_barcode").addClass('hide');
                    $("#getbarcode").attr("src", '');
                    $("#getbarcode_link").attr("href", '');
                } else {
                    $("#show_barcode").removeClass('hide');
                    $("#getbarcode").attr("src", data.getbarcode);
                    $("#getbarcode_link").attr("href", data.getbarcode);
                }
                if (data.getqrcode == null) {
                    $("#show_qrcode").addClass('hide');
                    $("#getqrcode").attr("src", '');
                    $("#getqrcode_link").attr("href", '');
                } else {
                    $("#show_qrcode").removeClass('hide');
                    $("#getqrcode").attr("src", data.getqrcode);
                    $("#getqrcode_link").attr("href", data.getqrcode);
                }

                if ($.trim(table_html) === '') {
                    $('#field_data').hide();
                } else {
                    $('#field_data').show();
                }

                shModal('myModal').show();
                patientvisit(id);
            },
        });
    }

    var current_patient_id = 0;

    function patientvisit(id) {
        current_patient_id = id;
        $.ajax({
            url: baseurl + 'admin/patient/patientvisit',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $('#visit_report_id').html(data);
            }
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {

                $("#eupdateid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#ename").val(data.patient_name);
                $("#eguardian_name").val(data.guardian_name);
                $("#emobileno").val(data.mobileno);
                $("#eemail").val(data.email);
                $("#eaddress").val(data.address);
                $("#age_year").val(data.age);
                $("#age_month").val(data.month);
                $("#age_day").val(data.day);
                $(".editpatient_dob").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $("#edit_insurance_id").val(data.insurance_id);
                $("#insurance_validity").val(data.insurance_validity);
                $("#edit_identification_number").val(data.identification_number);
                $("#blood_group").html(data.blood_group_name);
                $(".dropify-render").find("img").attr("src",data.image);
                shModal('myModal').hide();
                shModal('editModal').show();

            },
        });
    }

    $(document).ready(function(e) {
        $("#formeditpa").on('submit', (function(e) {
            $("#formeditpabtn").btnLoading();
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
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
                    $("#formeditpabtn").btnReset();
                },
                error: function() {

                }
            });
        }));
    });

    function delete_record(id) {
        if (confirm(<?php echo "'" . $this->lang->line('patient_delete_alert_message') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePatient',
                type: "POST",
                data: {
                    delid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    shModal("myModal").hide();
                    table.ajax.reload();
                }
            })
        }
    }

    function patient_deactive(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_deactivate_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deactivePatient',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == "fail") {
                        var message = (data.message);
                        errorMsg(message);
                    } else {
                        successMsg(<?php echo "'" . $this->lang->line('record_disable') . "'"; ?>);
                        window.getpatientData(id);
                    }
                }
            })
        }
    }

    function CalculateAgeInQCe(DOB, txtAge, Txndate) {
        if (DOB.value != '') {
            now = new Date(Txndate)
            var txtValue = DOB;
            if (txtValue != null)
                dob = txtValue.split('/');
            if (dob.length === 3) {
                born = new Date(dob[2], dob[1] * 1 - 1, dob[0]);
                if (now.getMonth() == born.getMonth() && now.getDate() == born.getDate()) {
                    age = now.getFullYear() - born.getFullYear();
                } else {
                    age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
                }
                if (isNaN(age) || age < 0) {

                } else {
                    if (now.getMonth() > born.getMonth()) {
                        var calmonth = now.getMonth() - born.getMonth();
                    } else {
                        var calmonth = born.getMonth() - now.getMonth();
                    }
                    $("#eage_year").val(age);
                    $("#eage_month").val(calmonth);
                    return age;
                }
            }
        }
    }

    function patient_active(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_active_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/activePatient',
                type: "POST",
                data: {
                    activeid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('record_active') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }

    $(document).on('click', '.delete_selected', function() {
        var $this = $(this);
        let obj = [];
        $('input:checkbox.enable_delete').each(function() {
            (this.checked ? obj.push($(this).val()) : "");
        });
        if (confirm('<?php echo $this->lang->line('patient_delete_alert_message'); ?>')) {
            $.ajax({
                url: base_url + 'admin/patient/bulk_delete',
                type: "POST",
                dataType: 'json',
                data: {
                    'delete_id': obj
                },
                beforeSend: function() {
                    $this.btnLoading();
                },
                success: function(res) {
                    $this.btnReset();
                    if (res.status == 1) {
                        successMsg(res.msg);
                        table.ajax.reload();
                    } else {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>");
                    $this.btnReset();
                },
                complete: function() {
                    $this.btnReset();

                }
            });
        }
    });
</script>
<script type="text/javascript">
    $(".newpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $("input[name='checkAll']").click(function() {
        $("input[name='patient[]']").not(this).prop('checked', this.checked);
    });

    $(".editpatient_dob").on('changeDate', function(event, date) {
        var birth_date = $(".editpatient_dob").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientage',
            type: "POST",
            dataType: "json",
            data: {
                birth_date: birth_date
            },
            success: function(data) {
                $('.patient_age_year').val(data.year);
                $('.patient_age_month').val(data.month);
                $('.patient_age_day').val(data.day);
            }
        });
    });
</script>
<?php
$genderList = $this->customlib->getGender_Patient();
$marital_status = $this->config->item('marital_status');
?>


<div class="modal fade sh-modal sh-modal-accent" id="myModalpa" tabindex="-1" aria-labelledby="myModalpaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalpaLabel"><?php echo $this->lang->line('add_patient'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formaddpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="pup-scroll-area">
                    <div class="modal-body">

                        <div class="sh-form-card mb-3">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_information'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                            <input id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name'); ?>">
                                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('guardian_name'); ?></label>
                                            <input type="text" name="guardian_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('phone'); ?></label>
                                            <input id="number" autocomplete="off" name="mobileno" type="text" class="form-control" value="<?php echo set_value('mobileno'); ?>">
                                            <span class="text-danger"><?php echo form_error('mobileno'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('gender'); ?></label>
                                            <select class="form-control" name="gender" id="addformgender">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($genderList as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                            <input type="text" name="dob" id="birth_date" class="form-control date patient_dob">
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="calculate">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('age') . ' (' . $this->lang->line('yy_mm_dd') . ')'; ?><small class="req"> *</small></label>
                                            <div class="row g-1">
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="age_year" class="form-control patient_age_year">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" id="age_month" class="form-control patient_age_month">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" id="age_day" class="form-control patient_age_day">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('blood_group'); ?></label>
                                            <select name="blood_group" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($bloodgroup as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('marital_status'); ?></label>
                                            <select name="marital_status" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($marital_status as $mkey => $mvalue) { ?>
                                                <option value="<?php echo $mkey; ?>" <?php if (set_value('marital_status') == $mkey) echo "selected"; ?>><?php echo $mvalue; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('email'); ?></label>
                                            <input type="text" id="addformemail" name="email" class="form-control" value="<?php echo set_value('email'); ?>">
                                            <span class="text-danger"><?php echo form_error('email'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('address'); ?></label>
                                            <input name="address" class="form-control" value="<?php echo set_value('address'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('national_identification_number'); ?></label>
                                            <input name="identification_number" class="form-control" value="<?php echo set_value('identification_number'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('patient_photo'); ?></label>
                                            <input class="filestyle form-control" type="file" name="file" id="file" size="20" data-height="26">
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><i class="fas fa-notes-medical me-1"></i> <?php echo $this->lang->line('additional_information'); ?></span>
                            </div>
                            <div class="px-2 py-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('remarks'); ?></label>
                                            <textarea name="note" id="note" class="form-control" rows="3"><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                            <textarea name="known_allergies" class="form-control" rows="3"><?php echo set_value('known_allergies'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa'); ?></label>
                                            <select class="form-control" name="organisation_id">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($organisation as $orgkey => $orgvalue) { ?>
                                                <option value="<?php echo (int)$orgvalue['id']; ?>"><?php echo html_escape($orgvalue['organisation_name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa_id'); ?></label>
                                            <input name="insurance_id" class="form-control" value="<?php echo set_value('insurance_id'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $this->lang->line('tpa_validity'); ?></label>
                                            <input name="validity" class="form-control date" value="<?php echo set_value('validity'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <?php echo display_custom_fields('patient'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddpabtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-branded" id="viewDetailReportModal" tabindex="-1" aria-labelledby="viewDetailReportModalLabel">
    <div class="modal-dialog sh-modal-autoheight modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailReportModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div id='action_detail_report_modal'></div>
                    <button type="button" class="btn-close" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line('close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="reportbilldata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-branded" id="viewModalBill" tabindex="-1" aria-labelledby="viewModalBillLabel">
    <div class="modal-dialog sh-modal-autoheight modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalBillLabel"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h5>
                <button type="button" class="btn-close" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reportdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sh-modal sh-modal-branded" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel">
    <div class="modal-dialog sh-modal-autoheight modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('bill_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pharmacy_reportdata"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(e) {
        $("#formaddpa").on('submit', (function(e) {
            let clicked_submit_btn = $(this).closest('form').find(':submit');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addpatient',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    clicked_submit_btn.btnLoading();
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
                        window.location.reload(true);
                    }
                    clicked_submit_btn.btnReset();
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                    clicked_submit_btn.btnReset();
                },
                complete: function() {
                    clicked_submit_btn.btnReset();
                }
            });
        }));
    });

    function addappointmentModal(patient_id = '', modalid) {
        var div_data = '';
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: patient_id
            },
            dataType: 'json',
            success: function(data) {
                var option = new Option(data.patient_name + " (" + data.id + ")", data.id, true, true);
                $(".patient_list_ajax").append(option).trigger('change');
                shModal(modalid).show();
                holdModal(modalid);
            }
        })
    }
</script>
<script type="text/javascript">
    $(".patient_dob").on('changeDate', function(event, date) {
        var birth_date = $(".patient_dob").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientage',
            type: "POST",
            dataType: "json",
            data: {
                birth_date: birth_date
            },
            success: function(data) {
                $('.patient_age_year').val(data.year);
                $('.patient_age_month').val(data.month);
                $('.patient_age_day').val(data.day);
            }
        });
    });
</script>
<script>
    $(document).on('click', '.view_detail', function() {
        var id = $(this).data('recordId');
        var module_name = $(this).data('moduleType');
        PatientPathologyDetails(id, $(this), module_name);
    });

    function PatientPathologyDetails(id, btn_obj, module_name) {
        var modal_view = $('#viewDetailReportModal');
        var $this = btn_obj;
        $.ajax({
            url: base_url + 'admin/patient/getPatientPathologyDetails',
            type: "POST",
            data: {
                'id': id,
                'module_name': module_name
            },
            dataType: 'json',
            beforeSend: function() {
                $this.btnLoading();
                modal_view.addClass('modal_loading');

            },
            success: function(data) {

                $('#viewDetailReportModal .modal-body').html(data.page);
                $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
                shModal('viewDetailReportModal').show();
                modal_view.removeClass('modal_loading');
            },

            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.btnReset();
                modal_view.removeClass('modal_loading');
            },
            complete: function() {
                $this.btnReset();
                modal_view.removeClass('modal_loading');

            }
        });
    }
</script>
<script>
    function popup(data) {
        var base_url = '<?php echo base_url(); ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow
            ? frame1[0].contentWindow
            : (frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument);
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap5/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/all.css">');
        frameDoc.document.write('</head><body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        return true;
    }

    function printDiv() {
        if (!current_patient_id) { return; }
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientvisitreport_print',
            type: 'POST',
            data: { patient_id: current_patient_id },
            success: function(result) {
                popup(result);
            }
        });
    }
</script>
<script>
    var array1 = new Array();
    var array2 = new Array();
    var array3 = new Array();
    var array4 = new Array();
    var array5 = new Array();
    var array6 = new Array();
    var array7 = new Array();
    var n = 7; //Total table
    for (var x = 1; x <= n; x++) {
        array1[x - 1] = x;
        array2[x - 1] = x + 'th';
    }

    var tablesToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>',
            templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>',
            body = '<body>',
            tablevar = '<table>{table',
            tablevarend = '}</table>',
            bodyend = '</body></html>',
            worksheet = '<x:ExcelWorksheet><x:Name>',
            worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>',
            worksheetvar = '{worksheet',
            worksheetvarend = '}',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            },
            wstemplate = '',
            tabletemplate = '';

        return function(table, name, filename) {
            var tables = table;
            for (var i = 0; i < tables.length; ++i) {
                wstemplate += worksheet + worksheetvar + i + worksheetvarend + worksheetend;
                tabletemplate += tablevar + i + tablevarend;
            }

            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;
            var ctx = {};
            for (var j = 0; j < tables.length; ++j) {
                ctx['worksheet' + j] = name[j];
            }

            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                if (!exceltable) continue;
                ctx['table' + k] = exceltable.innerHTML;
            }

            window.location.href = uri + base64(format(allOfIt, ctx));
        }
    })();
</script>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModalpa').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>
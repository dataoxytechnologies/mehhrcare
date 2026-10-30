<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$marital_status = $this->config->item('marital_status');
?>
<div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title titlefix">
                            <?php echo $this->lang->line('disabled_patient_list');?>
                        </h3>
                        <div class="d-flex gap-2 align-items-center flex-wrap float-end addmeeting">
                            <a href="<?php echo base_url() ?>admin/admin/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('patient_list'); ?></a>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <div class="download_label"><?php
                            echo $this->lang->line('disabled_patient_list');
                            ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlistdisablepatient" data-export-title="<?php echo $this->lang->line('disabled_patient_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                     <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                         ?>
                                        <th><?php echo ucfirst($fields_value->name); ?></th>
                                    <?php } } ?>
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
<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('patient_details'); ?>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div id="edit_delete" class="d-flex gap-1"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <input name="id" type="hidden" id="patientid">

                    <div class="sh-form-card">
                        <!-- Card header: photo + name + guardian -->
                        <div class="sh-card-header">
                            <?php $file = "uploads/patient_images/no_image.png"; ?>
                            <img class="rounded-circle flex-shrink-0 d-none" src="<?php echo $this->media_storage->getImageURL($file) ?>" id="image" alt="Patient photo" width="44" height="44">
                            <div class="rounded-circle flex-shrink-0 sh-name-initials" id="image_initials" aria-hidden="true"></div>
                            <div>
                                <div class="sh-card-header-title" id="patient_name"></div>
                                <div class="text-muted sh-card-header-meta"><i class="fas fa-user-secret me-1"></i><span id="guardian"></span></div>
                            </div>
                        </div>

                        <!-- Info grid -->
                        <div class="sh-info-grid">
                            <!-- Row 1: Demographics -->
                            <div class="row g-0">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('gender'); ?></div>
                                    <div class="sh-info-value" id="genders">—</div>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></div>
                                    <div class="sh-info-value" id="blood_group">—</div>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('marital_status'); ?></div>
                                    <div class="sh-info-value" id="marital_status">—</div>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('age'); ?></div>
                                    <div class="sh-info-value" id="age">—</div>
                                </div>
                            </div>
                            <!-- Row 2: Contact -->
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('phone'); ?></div>
                                    <div class="sh-info-value" id="contact">—</div>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('email'); ?></div>
                                    <div class="sh-info-value" id="email">—</div>
                                </div>
                                <div class="col-12 col-md-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('address'); ?></div>
                                    <div class="sh-info-value" id="address">—</div>
                                </div>
                            </div>
                            <!-- Row 3: Medical -->
                            <div class="row g-0 sh-row-divider">
                                <div class="col-12 col-md-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('any_known_allergies'); ?></div>
                                    <div class="sh-info-value" id="allergies">—</div>
                                </div>
                                <div class="col-12 col-md-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('remarks'); ?></div>
                                    <div class="sh-info-value" id="note">—</div>
                                </div>
                            </div>
                            <!-- Row 4: TPA / NID -->
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_id'); ?></div>
                                    <div class="sh-info-value" id="tpa_id">—</div>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('tpa_validity'); ?></div>
                                    <div class="sh-info-value" id="tpa_validity">—</div>
                                </div>
                                <div class="col-12 col-md-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('national_identification_number'); ?></div>
                                    <div class="sh-info-value" id="national_identification_number">—</div>
                                </div>
                            </div>
                            <!-- Row 5: Barcode / QR -->
                            <div class="row g-0 sh-row-divider">
                                <div id="show_barcode" class="col-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('barcode'); ?></div>
                                    <div class="sh-info-value">
                                        <a href="" id="getbarcode_link" target="_blank"><img class="sh-qr-code" src="" id="getbarcode" width="100" height="35" /></a>
                                    </div>
                                </div>
                                <div id="show_qrcode" class="col-6 sh-info-item">
                                    <div class="sh-info-label"><?php echo $this->lang->line('qrcode'); ?></div>
                                    <div class="sh-info-value">
                                        <a href="" id="getqrcode_link" target="_blank"><img class="sh-qr-code" src="" id="getqrcode" width="60" height="60" /></a>
                                    </div>
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

<div class="modal fade sh-modal sh-modal-accent" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><?php echo $this->lang->line('patient_details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <input id="eupdateid" name="updateid" type="hidden" value="" />
                <div class="pup-scroll-area">
                <div class="modal-body">

                    <!-- Personal Info -->
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-user me-1"></i> <?php echo $this->lang->line('patient_details'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">

                                <!-- Row 1: Name | Guardian — 6+6=12 -->
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                                    <input id="ename" name="name" type="text" class="form-control form-control-sm" value="<?php echo set_value('name'); ?>" />
                                    <span class="text-danger small"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('guardian_name'); ?></label>
                                    <input type="text" name="guardian_name" id="eguardian_name" value="" class="form-control form-control-sm">
                                </div>

                                <!-- Row 2: Gender | DOB | Age (Y/M/D) — 3+3+6=12 -->
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-select form-select-sm" name="gender" id="egenders">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($genderList as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo 'selected'; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                    <input type="text" name="dob" class="form-control form-control-sm date editpatient_dob" />
                                </div>
                                <div class="col-md-6" id="calculate">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('age').' ('.$this->lang->line('yy_mm_dd').')'; ?> <small class="req">*</small></label>
                                    <div class="d-flex gap-1">
                                        <input type="text" id="age_year" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" value="" class="form-control form-control-sm patient_age_year">
                                        <input type="text" id="age_month" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" value="" class="form-control form-control-sm patient_age_month">
                                        <input type="text" id="age_day" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" value="" class="form-control form-control-sm patient_age_day">
                                    </div>
                                </div>

                                <!-- Row 3: Blood Group | Marital Status | Photo — 3+3+6=12 -->
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('blood_group'); ?></label>
                                    <select class="form-select form-select-sm" id="blood_groups" name="blood_group">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($bloodgroup as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) echo 'selected'; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger small"><?php echo form_error('blood_group'); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('marital_status'); ?></label>
                                    <select name="marital_status" id="maritalstatuss" class="form-select form-select-sm">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($marital_status as $key => $value): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('patient_photo'); ?></label>
                                    <input class="filestyle form-control form-control-sm" type="file" name="file" id="exampleInputFile" data-height="26" data-default-file="<?php echo base_url(); ?>uploads/patient_images/no_image.png">
                                    <span class="text-danger small"><?php echo form_error('file'); ?></span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-address-book me-1"></i> <?php echo $this->lang->line('contact'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">

                                <!-- Row 1: Phone | Email | Address — 3+3+6=12 -->
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('phone'); ?></label>
                                    <input id="emobileno" autocomplete="off" name="contact" type="text" class="form-control form-control-sm" value="<?php echo set_value('mobileno'); ?>" />
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('email'); ?></label>
                                    <input type="text" id="eemail" value="<?php echo set_value('email'); ?>" name="email" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('address'); ?></label>
                                    <input name="address" id="eaddress" class="form-control form-control-sm" />
                                </div>

                                <!-- Row 2: Remarks | Known Allergies — 6+6=12 -->
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('remarks'); ?></label>
                                    <textarea name="note" id="enote" class="form-control form-control-sm" rows="2"><?php echo set_value('note'); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                    <textarea name="known_allergies" id="eknown_allergies" class="form-control form-control-sm" rows="2"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Insurance -->
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><i class="fa fa-id-card me-1"></i> <?php echo $this->lang->line('tpa_details'); ?></span>
                        </div>
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('tpa_id'); ?></label>
                                    <input type="hidden" name="organisation_id" id="organisation_id">
                                    <input name="insurance_id" id="insurance_id" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('tpa_validity'); ?></label>
                                    <input name="validity" id="validity" class="form-control form-control-sm date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm"><?php echo $this->lang->line('national_identification_number'); ?></label>
                                    <input name="identification_number" id="identification_number" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom fields -->
                    <div id="customfield"></div>

                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle me-1"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {

                if (data.is_active == 'yes') {

                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='patient_deactive(" + id + ")' title='<?php echo $this->lang->line('disable'); ?>'><i class='fa fa-thumbs-o-down'></i></a><?php } ?>";

                } else {

                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_delete')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='patient_active(" + id + ")' title='<?php echo $this->lang->line('enable'); ?>'><i class='fa fa-thumbs-o-up'></i></a><?php } if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='delete_record(" + id + ")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>";

                }

                $("patientid").val(data.id);
                $("#patient_name").html(data.patient_name+" ("+data.id+")");
                $("#guardian").html(data.guardian_name);
                $("#patients_id").html(data.patient_unique_id);
                $("#genders").html(data.language_gender);
                $("#marital_status").html(data.marital_status);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#address").html(data.address);
                $("#is_active").html(data.is_active);
                $("#blood_group").html(data.blood_group_name);
                $("#age").html(data.patient_age);
                $("#allergies").html(data.known_allergies);
                $("#note").html(data.note);
                $("#tpa_id").html(data.insurance_id);
                $("#tpa_validity").html(data.insurance_validity);
                $("#national_identification_number").html(data.identification_number);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('patient', 'can_edit')) { ?><a href='#' class='btn btn-sm btn-light' onclick='editRecord(" + id + ")' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?>" + link + "<?php ?>");

                renderPatientAvatar(data.image, data.patient_name);

                if(data.getbarcode == null){
                    $("#show_barcode").hide();
                    $("#getbarcode").attr("src", '');
                    $("#getbarcode_link").attr("href", '');
                }else{
                    $("#show_barcode").show();
                    $("#getbarcode").attr("src", data.getbarcode);
                    $("#getbarcode_link").attr("href", data.getbarcode);
                }
                if(data.getqrcode == null){
                    $("#show_qrcode").hide();
                    $("#getqrcode").attr("src", '');
                    $("#getqrcode_link").attr("href", '');
                }else{
                    $("#show_qrcode").show();
                    $("#getqrcode").attr("src", data.getqrcode);
                    $("#getqrcode_link").attr("href", data.getqrcode);
                }
                holdModal('myModal');
            },
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
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
                $(".dropify-render").find("img").attr("src",data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="maritalstatuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                shModal('myModal').hide();

                $("#organisation_id").val(data.organisation_id);
                $("#insurance_id").val(data.insurance_id);
                $("#validity").val(data.insurance_validity);
                $("#identification_number").val(data.identification_number);
                holdModal('editModal');
            },
        });
    }

    $(document).ready(function (e) {
        $("#formeditpa").on('submit', (function (e) {
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
                    $("#formeditpabtn").btnReset();
                },
                error: function () {

                }
            });
        }));
    });

    function delete_record(id) {
        if (confirm(<?php echo "'".$this->lang->line('patient_delete_alert_message')."'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/delete/'+id,
                type: "POST",
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    shModal("myModal").hide();
                    table.ajax.reload();
                }
            })
        }
    }
        $(".editpatient_dob").on('changeDate', function(event, date) {
           var birth_date = $(".editpatient_dob").val();

            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/getpatientage',
                type: "POST",
                dataType: "json",
                data: {birth_date:birth_date},
                success: function (data) {

                  $('.patient_age_year').val(data.year);
                  $('.patient_age_month').val(data.month);
                  $('.patient_age_day').val(data.day);
                }
           });
    });

    function patient_deactive(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_deactivate_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deactivePatient',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('update_message') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }

    function patient_active(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_active_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/activePatient',
                type: "POST",
                data: {activeid: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('update_message') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
    (function ($) {
        'use strict';
        $(document).ready(function(){
            initDatatable('ajaxlistdisablepatient','admin/admin/getdisablepatientdatatable')
        })
    }(jQuery))

</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

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
                                                <option value="<?php echo $mvalue; ?>" <?php if (set_value('marital_status') == $mkey) echo "selected"; ?>><?php echo $mvalue; ?></option>
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
                                                <option value="<?php echo $orgvalue['id']; ?>"><?php echo $orgvalue['organisation_name']; ?></option>
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
<script type="text/javascript">
    $(document).ready(function (e) {
        $("#formaddpa").on('submit', (function (e) {
        let clicked_submit_btn= $(this).closest('form').find(':submit');
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
                 clicked_submit_btn.btnLoading() ;

                },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);

                        shModal("myModalpa").toggle();
                        addappointmentModal(data.id, (typeof patientParentModal !== 'undefined' ? patientParentModal : 'myModal'));
                    }
                        clicked_submit_btn.btnReset();
                },
                 error: function(xhr) {
        alert('<?php echo $this->lang->line("error_occurred_please_try_again"); ?>');
         clicked_submit_btn.btnReset() ;
             },
    complete: function() {
     clicked_submit_btn.btnReset() ;
    }
            });
        }));
    });

    function addappointmentModal(patient_id = '', modalid) {

        var div_data = '';
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {id:patient_id},
            dataType: 'json',
            success: function (data) {
                var option = new Option(data.patient_name+" ("+data.id+")", data.id, true, true);
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
                data: {birth_date:birth_date},
                success: function (data) {
                  $('.patient_age_year').val(data.year);
                  $('.patient_age_month').val(data.month);
                  $('.patient_age_day').val(data.day);
                }
           });
});
</script>

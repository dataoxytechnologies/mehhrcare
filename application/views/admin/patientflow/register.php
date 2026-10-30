<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-user-plus sh-icon-soft"></i>
            <h3 class="card-title titlefix mb-0"><?php echo $this->lang->line('add_patient'); ?></h3>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back'); ?>
            </a>
        </div>
    </div>

    <form action="<?php echo site_url('admin/patientflow/save_visit'); ?>" method="post" id="pfRegisterForm">
        <div class="card-body">

            <div id="pfDupAlert" class="alert alert-warning mt-0 mb-3 py-2 small" style="display:none;">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line('existing_patient_found') ?: 'An existing patient with this name/mobile is already registered — a new visit will be created for them. Use a different name/mobile to register a brand-new patient.'; ?>
                <span id="pfDupName"></span>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('name'); ?> <small class="req">*</small></label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('gender'); ?> <small class="req">*</small></label>
                    <select name="gender" id="gender" class="form-select" required>
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <option value="Male"><?php echo $this->lang->line('male'); ?></option>
                        <option value="Female"><?php echo $this->lang->line('female'); ?></option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('date_of_birth'); ?></label>
                    <input type="date" name="dob" id="dob" class="form-control">
                </div>
                <div class="col-md-2 col-lg">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('year'); ?> <small class="req">*</small></label>
                    <input type="number" name="age_year" id="age_year" class="form-control" min="0" value="0" required>
                </div>
                <div class="col-md-2 col-lg">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('month'); ?> <small class="req">*</small></label>
                    <input type="number" name="age_month" id="age_month" class="form-control" min="0" max="11" value="0" required>
                </div>
                <div class="col-md-2 col-lg">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('day'); ?> <small class="req">*</small></label>
                    <input type="number" name="age_day" id="age_day" class="form-control" min="0" max="30" value="0" required>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('phone'); ?></label>
                    <input type="text" name="mobileno" id="mobileno" class="form-control">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('email'); ?></label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('guardian_name'); ?></label>
                    <input type="text" name="guardian_name" id="guardian_name" class="form-control">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('marital_status'); ?></label>
                    <select name="marital_status" id="marital_status" class="form-select">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <option value="Single"><?php echo $this->lang->line('single') ?: 'Single'; ?></option>
                        <option value="Married"><?php echo $this->lang->line('married') ?: 'Married'; ?></option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('identification_number'); ?></label>
                    <input type="text" name="identification_number" id="identification_number" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('address'); ?></label>
                    <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('known_allergies'); ?></label>
                    <textarea name="known_allergies" id="known_allergies" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label form-label-sm"><?php echo $this->lang->line('note'); ?></label>
                    <textarea name="note" id="note" class="form-control" rows="1"></textarea>
                </div>
            </div>

            <?php if ($first_level) { ?>
            <div class="alert alert-info mt-3 mb-0 py-2 small">
                <i class="fa fa-info-circle"></i>
                <?php echo $this->lang->line('patient_will_enter'); ?>
                <strong><?php echo $first_level->code . ' — ' . $first_level->title; ?></strong>
            </div>
            <?php } ?>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?php echo site_url('admin/patientflow'); ?>" class="btn btn-outline-secondary"><?php echo $this->lang->line('cancel'); ?></a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
        </div>
    </form>
</div>

<script>
(function () {
    var $alert = $('#pfDupAlert');
    if (!$alert.length) { return; }
    function checkDup() {
        var name   = (($('#name').val() || '').trim());
        var mobile = (($('#mobileno').val() || '').trim());
        if (name.length < 2 && mobile.length < 5) { $alert.hide(); return; }
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getPatientListAjax',
            type: 'post',
            dataType: 'json',
            data: { searchTerm: mobile || name },
            beforeSend: function () { $alert.hide(); },
            success: function (res) {
                if (res && res.length && res[0].id) {
                    $('#pfDupName').text(' — ' + res[0].text);
                    $alert.show();
                } else {
                    $alert.hide();
                }
            }
        });
    }
    $('#pfRegisterForm').on('input change', '#name, #mobileno', checkDup);
})();
</script>
                </div>
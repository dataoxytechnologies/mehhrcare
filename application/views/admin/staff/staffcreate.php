<link href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>

<div class="pb-3">

<?php if ($this->session->flashdata('msg')) { ?>
<div><?php echo $this->session->flashdata('msg') ?></div>
<?php $this->session->unset_userdata('msg'); } ?>

<div class="sh-info-banner">
    <i class="fa fa-circle-info fa-fw"></i>
    <?php echo $this->lang->line('add_staff_message'); ?>
</div>

<form id="form1" action="<?php echo site_url('admin/staff/create') ?>" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
    <?php echo $this->customlib->getCSRF(); ?>

    <?php
    // SaaS quota errors shown prominently at the top of the form. The COUNT limit
    // (no_of_staff) is set on the virtual "validate_resource" field, which has no
    // input of its own — without this block its message is invisible. The STORAGE
    // limit is set on the "file" field; it is surfaced here too so a full-quota
    // save is obvious without scrolling. (The file field also keeps its own inline
    // error near the photo input.)
    $saas_quota_error = form_error('validate_resource') . form_error('file');
    if (!empty($saas_quota_error)) { ?>
        <div class="alert alert-danger"><?php echo $saas_quota_error; ?></div>
    <?php } ?>

    <div class="sh-form-stack">

    <!-- ══════════════════════════════════════════
         BASIC INFORMATION
    ══════════════════════════════════════════════ -->
    <div class="sh-form-card">
        <div class="sh-card-header">
            <p class="sh-card-header-title"><?php echo $this->lang->line('staff_basic_information'); ?></p>
            <a class="btn btn-sm btn-info ms-auto" href="<?php echo base_url(); ?>admin/staff/import" autocomplete="off">
                <?php echo $this->lang->line('import_staff') ?>
            </a>
        </div>
        <div class="sh-card-body">

            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_id'); ?> <span class="req">*</span></label>
                    <input autofocus id="employee_id" name="employee_id" type="text" class="form-control" value="<?php echo set_value('employee_id') ?>" />
                    <span class="text-danger"><?php echo form_error('employee_id'); ?></span>
                </div>
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_role'); ?> <span class="req">*</span></label>
                    <select id="role" name="role" class="form-select">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($roles as $key => $role) { ?>
                        <option value="<?php echo $role['id'] ?>" <?php echo set_select('role', $role['id'], set_value('role')); ?>><?php echo $role["name"] ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('role'); ?></span>
                </div>
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_designation'); ?></label>
                    <select id="designation" name="designation" class="form-select">
                        <option value=""><?php echo $this->lang->line('select') ?></option>
                        <?php foreach ($designation as $key => $value) { ?>
                        <option value="<?php echo $value["id"] ?>" <?php echo set_select('designation', $value['id'], set_value('designation')); ?>><?php echo $value["designation"] ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('designation'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_department'); ?></label>
                    <select id="department" name="department" class="form-select">
                        <option value=""><?php echo $this->lang->line('select') ?></option>
                        <?php foreach ($department as $key => $value) { ?>
                        <option value="<?php echo $value["id"] ?>" <?php echo set_select('department', $value['id'], set_value('department')); ?>><?php echo $value["department_name"] ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('department'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_specialist'); ?></label>
                    <select id="specialistOpt" name="specialist[]" class="form-select" multiple>
                        <?php foreach ($specialist as $key => $value) { ?>
                        <option value="<?php echo $value["id"] ?>" <?php echo set_select('specialist', $value['id'], set_value('specialist')); ?>><?php echo $value["specialist_name"] ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('specialist'); ?></span>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_first_name'); ?> <span class="req">*</span></label>
                    <input id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name') ?>" />
                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_last_name'); ?></label>
                    <input id="surname" name="surname" type="text" class="form-control" value="<?php echo set_value('surname') ?>" />
                    <span class="text-danger"><?php echo form_error('surname'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_father_name'); ?></label>
                    <input id="father_name" name="father_name" type="text" class="form-control" value="<?php echo set_value('father_name') ?>" />
                    <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_mother_name'); ?></label>
                    <input id="mother_name" name="mother_name" type="text" class="form-control" value="<?php echo set_value('mother_name') ?>" />
                    <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_gender'); ?> <span class="req">*</span></label>
                    <select class="form-select" name="gender">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($genderList as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php echo set_select('gender', $key, set_value('gender')); ?>><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('gender'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_marital_status'); ?></label>
                    <select class="form-select" name="marital_status">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($marital_status as $makey => $mavalue) { ?>
                        <option value="<?php echo $mavalue ?>" <?php echo set_select('marital_status', $mavalue, set_value('marital_status')); ?>><?php echo $mavalue; ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('marital_status'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_blood_group'); ?></label>
                    <select class="form-select" name="blood_group">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($bloodgroup as $bgkey => $bgvalue) { ?>
                        <option value="<?php echo $bgvalue ?>" <?php echo set_select('blood_group', $bgvalue, set_value('blood_group')); ?>><?php echo $bgvalue ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_date_of_birth'); ?> <span class="req">*</span></label>
                    <input id="dob" name="dob" type="text" class="form-control date" value="<?php echo set_value('dob') ?>" />
                    <span class="text-danger"><?php echo form_error('dob'); ?></span>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_date_of_joining'); ?></label>
                    <input id="date_of_joining" name="date_of_joining" type="text" class="form-control date" value="<?php echo set_value('date_of_joining') ?>" />
                    <span class="text-danger"><?php echo form_error('date_of_joining'); ?></span>
                </div>
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_phone'); ?></label>
                    <input id="mobileno" name="contactno" type="text" class="form-control" value="<?php echo set_value('contactno') ?>" />
                    <span class="text-danger"><?php echo form_error('contactno'); ?></span>
                </div>
                <div class="col-md-2">
                    <label class="sh-label"><?php echo $this->lang->line('staff_emergency_contact'); ?></label>
                    <input id="emgmobileno" name="emgcontactno" type="text" class="form-control" value="<?php echo set_value('emgcontactno') ?>" />
                    <span class="text-danger"><?php echo form_error('emgcontactno'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_email'); ?> <span class="req">*</span></label>
                    <input id="email" name="email" type="text" class="form-control" value="<?php echo set_value('email') ?>" />
                    <span class="text-danger"><?php echo form_error('email'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_photo'); ?></label>
                    <input type="file" name="file" id="file" class="filestyle" data-allowed-file-extensions="png jpg jpeg gif" data-max-file-size="2M" />
                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="sh-label"><?php echo $this->lang->line('staff_current_address'); ?></label>
                    <textarea name="address" class="form-control" rows="2"><?php echo set_value('address'); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="sh-label"><?php echo $this->lang->line('staff_permanent_address'); ?></label>
                    <textarea name="permanent_address" class="form-control" rows="2"><?php echo set_value('permanent_address'); ?></textarea>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_qualification'); ?></label>
                    <textarea id="qualification" name="qualification" class="form-control" rows="2"><?php echo set_value('qualification') ?></textarea>
                    <span class="text-danger"><?php echo form_error('qualification'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_work_experience'); ?></label>
                    <textarea id="work_exp" name="work_exp" class="form-control" rows="2"><?php echo set_value('work_exp') ?></textarea>
                    <span class="text-danger"><?php echo form_error('work_exp'); ?></span>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_specialization'); ?></label>
                    <textarea name="specialization" class="form-control" rows="2"><?php echo set_value('specialization'); ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="sh-label"><?php echo $this->lang->line('staff_note'); ?></label>
                    <textarea name="note" class="form-control" rows="2"><?php echo set_value('note'); ?></textarea>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="sh-label"><?php echo $this->lang->line('pan_number'); ?></label>
                    <input id="pan_number" name="pan_number" type="text" class="form-control" value="<?php echo set_value('pan_number') ?>" />
                </div>
                <div class="col-md-4">
                    <label class="sh-label"><?php echo $this->lang->line('national_identification_number'); ?></label>
                    <input id="identification_number" name="identification_number" type="text" class="form-control" value="<?php echo set_value('identification_number') ?>" />
                </div>
                <div class="col-md-4">
                    <label class="sh-label"><?php echo $this->lang->line('local_identification_number'); ?></label>
                    <input id="local_identification_number" name="local_identification_number" type="text" class="form-control" value="<?php echo set_value('local_identification_number') ?>" />
                </div>
            </div>

            <div class="row g-3">
                <?php echo display_custom_fields('staff'); ?>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════
         ADD MORE DETAILS (COLLAPSIBLE)
    ══════════════════════════════════════════════ -->
    <div class="sh-form-card">
        <button type="button" class="sh-collapse-btn"
                data-bs-toggle="collapse"
                data-bs-target="#moreDetailsCollapse"
                aria-expanded="false">
            <span class="toggle-dot"><i class="fa fa-plus fa-2xs"></i></span>
            <?php echo $this->lang->line('add_more_details'); ?>
            <span class="collapse-hint ms-1">— <?php echo $this->lang->line('staff_payroll'); ?> · <?php echo $this->lang->line('staff_leaves'); ?> · <?php echo $this->lang->line('staff_bank_account_details'); ?> · <?php echo $this->lang->line('staff_social_media_link'); ?> · <?php echo $this->lang->line('staff_upload_documents'); ?></span>
        </button>
        <div class="collapse" id="moreDetailsCollapse">
            <div class="sh-collapse-body">

                <!-- Payroll -->
                <div class="sh-sub-section">
                    <div class="sh-sub-title"><?php echo $this->lang->line('staff_payroll'); ?></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_epf_no'); ?></label>
                            <input id="epf_no" name="epf_no" type="text" class="form-control" value="<?php echo set_value('epf_no') ?>" />
                            <span class="text-danger"><?php echo form_error('epf_no'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_basic_salary'); ?></label>
                            <input type="text" class="form-control" name="basic_salary" value="<?php echo set_value('basic_salary') ?>" />
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_contract_type'); ?></label>
                            <select class="form-select" name="contract_type">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($contract_type as $key => $value) { ?>
                                <option value="<?php echo $key ?>" <?php echo set_select('contract_type', $key, set_value('contract_type')); ?>><?php echo $value ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('contract_type'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_work_shift'); ?></label>
                            <input id="shift" name="shift" type="text" class="form-control" value="<?php echo set_value('shift') ?>" />
                            <span class="text-danger"><?php echo form_error('shift'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_work_location'); ?></label>
                            <input id="location" name="location" type="text" class="form-control" value="<?php echo set_value('location') ?>" />
                            <span class="text-danger"><?php echo form_error('location'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Leaves -->
                <div class="sh-sub-section">
                    <div class="sh-sub-title"><?php echo $this->lang->line('staff_leaves'); ?></div>
                    <div class="row g-3">
                        <?php foreach ($leavetypeList as $key => $leave) { ?>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo html_escape($leave["type"]); ?></label>
                            <input name="leave_type[]" type="hidden" readonly class="form-control" value="<?php echo $leave['id'] ?>" />
                            <input name="alloted_leave_<?php echo $leave['id'] ?>" placeholder="<?php echo $this->lang->line('staff_number_of_leaves'); ?>" value="<?php echo set_value('alloted_leave_' . $leave['id']); ?>" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('alloted_leave'); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Bank Account Details -->
                <div class="sh-sub-section">
                    <div class="sh-sub-title"><?php echo $this->lang->line('staff_bank_account_details'); ?></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_account_title'); ?></label>
                            <input id="account_title" name="account_title" type="text" class="form-control" value="<?php echo set_value('account_title') ?>" />
                            <span class="text-danger"><?php echo form_error('account_title'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('bank_account_no'); ?></label>
                            <input id="bank_account_no" name="bank_account_no" type="text" class="form-control" value="<?php echo set_value('bank_account_no') ?>" />
                            <span class="text-danger"><?php echo form_error('bank_account_no'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_bank_name'); ?></label>
                            <input id="bank_name" name="bank_name" type="text" class="form-control" value="<?php echo set_value('bank_name') ?>" />
                            <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_ifsc_code'); ?></label>
                            <input id="ifsc_code" name="ifsc_code" type="text" class="form-control" value="<?php echo set_value('ifsc_code') ?>" />
                            <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label class="sh-label"><?php echo $this->lang->line('staff_bank_branch_name'); ?></label>
                            <input id="bank_branch" name="bank_branch" type="text" class="form-control" value="<?php echo set_value('bank_branch') ?>" />
                            <span class="text-danger"><?php echo form_error('bank_branch'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="sh-sub-section">
                    <div class="sh-sub-title"><?php echo $this->lang->line('staff_social_media_link'); ?></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="sh-label"><i class="fab fa-facebook-f me-1 sh-icon-fb"></i><?php echo $this->lang->line('staff_facebook_url'); ?></label>
                            <input name="facebook" type="text" class="form-control" value="<?php echo set_value('facebook') ?>" />
                            <span class="text-danger"><?php echo form_error('facebook'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="sh-label"><i class="fab fa-twitter me-1"></i><?php echo $this->lang->line('staff_twitter_url'); ?></label>
                            <input name="twitter" type="text" class="form-control" value="<?php echo set_value('twitter') ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="sh-label"><i class="fab fa-linkedin-in me-1 sh-icon-li"></i><?php echo $this->lang->line('staff_linkedin_url'); ?></label>
                            <input name="linkedin" type="text" class="form-control" value="<?php echo set_value('linkedin') ?>" />
                            <span class="text-danger"><?php echo form_error('linkedin'); ?></span>
                        </div>
                        <div class="col-md-6">
                            <label class="sh-label"><i class="fab fa-instagram me-1 sh-icon-ig"></i><?php echo $this->lang->line('staff_instagram_url'); ?></label>
                            <input name="instagram" type="text" class="form-control" value="<?php echo set_value('instagram') ?>" />
                        </div>
                    </div>
                </div>

                <!-- Upload Documents -->
                <div class="sh-sub-section">
                    <div class="sh-sub-title"><?php echo $this->lang->line('staff_upload_documents'); ?></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="sh-doc-table">
                                <thead><tr>
                                    <th class="sh-col-num">#</th>
                                    <th><?php echo $this->lang->line('staff_title'); ?></th>
                                    <th><?php echo $this->lang->line('staff_documents'); ?></th>
                                </tr></thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><?php echo $this->lang->line('staff_resume'); ?></td>
                                        <td>
                                            <input class="filestyle" type="file" name="first_doc" id="doc1">
                                            <span class="text-danger"><?php echo form_error('first_doc'); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><?php echo $this->lang->line('staff_resignation_letter'); ?></td>
                                        <td>
                                            <input class="filestyle" type="file" name="third_doc" id="doc3">
                                            <span class="text-danger"><?php echo form_error('third_doc'); ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="sh-doc-table">
                                <thead><tr>
                                    <th class="sh-col-num">#</th>
                                    <th><?php echo $this->lang->line('staff_title'); ?></th>
                                    <th><?php echo $this->lang->line('staff_documents'); ?></th>
                                </tr></thead>
                                <tbody>
                                    <tr>
                                        <td>2</td>
                                        <td><?php echo $this->lang->line('staff_joining_letter'); ?></td>
                                        <td>
                                            <input class="filestyle" type="file" name="second_doc" id="doc2">
                                            <span class="text-danger"><?php echo form_error('second_doc'); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><?php echo $this->lang->line('staff_other_documents'); ?><input type="hidden" name="fourth_title" class="form-control" placeholder="<?= $this->lang->line('staff_other_documents') ?>"></td>
                                        <td>
                                            <input class="filestyle" type="file" name="fourth_doc" id="doc4">
                                            <span class="text-danger"><?php echo form_error('fourth_doc'); ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="sh-form-footer">
        <button type="submit" class="btn-sh-save">
            <?php echo $this->lang->line('save'); ?>
        </button>
    </div>

    </div><!-- /sh-form-stack -->

</form>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>
<script>
$('#specialistOpt').multiselect({
    columns: 1,
    placeholder: 'Select Specialist',
    search: true
});
// File inputs use class="filestyle" — global ss.custom.js runs $('.filestyle').dropify()
// on document.ready (after dropify.min.js loads in footer). No per-page init needed here.
</script>

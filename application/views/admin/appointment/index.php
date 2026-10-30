<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender_Patient();
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-0 px-3">
          <ul class="nav nav-pills sh-segmented-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button" role="tab"><?php echo $this->lang->line("todays_appointment"); ?></button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button" role="tab"><?php echo $this->lang->line("upcoming_appointment"); ?></button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_3" type="button" role="tab"><?php echo $this->lang->line("old_appointment"); ?></button>
            </li>
          </ul>
          <div class="d-flex flex-wrap gap-2">
            <?php if ($this->rbac->hasPrivilege('appointment', 'can_add')) {?>
              <a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addappointment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_appointment'); ?></a>
            <?php }?>
            <?php if ($this->rbac->hasPrivilege('doctor_wise_appointment', 'can_view')) {?>
              <a href="<?php echo base_url("admin/onlineappointment/patientschedule"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('doctor_wise'); ?></a>
            <?php }?>
            <?php if ($this->rbac->hasPrivilege('patient_queue', 'can_view')) {?>
              <a href="<?php echo base_url("admin/onlineappointment/patientqueue"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('queue'); ?></a>
            <?php }?>
          </div>
        </div>

        <div class="tab-content">
          <div class="tab-pane show active" id="tab_1">
            <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover ajaxlist_todays" data-export-title="<?php echo $this->lang->line('appointment_details'); ?>">
                  <thead>
                    <tr>
                      <th width="120px"><?php echo $this->lang->line('patient_name'); ?></th>
                      <th width="120px"><?php echo $this->lang->line('appointment_no'); ?></th>
                      <th><?php echo $this->lang->line('date'); ?></th>
                      <th width="90px"><?php echo $this->lang->line('phone'); ?></th>
                      <th width="80px"><?php echo $this->lang->line('gender'); ?></th>
                      <th><?php echo $this->lang->line('doctor'); ?></th>
                      <th><?php echo $this->lang->line('source'); ?></th>
                      <th><?php echo $this->lang->line('priority'); ?></th>
                      <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                      <th><?php echo $this->lang->line('live_consultant'); ?></th>
                      <?php } ?>
                      <?php
                        if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {
                      ?>
                      <th ><?php echo $fields_value->name; ?></th>
                      <?php
                      }
                      }
                      ?>
                      <th><?php echo $this->lang->line('created_by'); ?></th>
                      <th><?php echo $this->lang->line('status'); ?></th>
                      <th><?php echo $this->lang->line('fees')." (".$currency_symbol.")"; ?></th>
                    <th><?php echo $this->lang->line('discount')." (%)"; ?></th>
                    <th><?php echo $this->lang->line('paid')." (".$currency_symbol.")"; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
            </div>
            </div>
          </div>

          <div class="tab-pane" id="tab_2">
            <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover ajaxlist_upcoming" data-export-title="<?php echo $this->lang->line('appointment_details'); ?>">
                  <thead>
                    <tr>
                      <th width="120px"><?php echo $this->lang->line('patient_name'); ?></th>
                      <th width="120px"><?php echo $this->lang->line('appointment_no'); ?></th>
                      <th><?php echo $this->lang->line('date'); ?></th>
                      <th width="90px"><?php echo $this->lang->line('phone'); ?></th>
                      <th width="80px"><?php echo $this->lang->line('gender'); ?></th>
                      <th><?php echo $this->lang->line('doctor'); ?></th>
                      <th><?php echo $this->lang->line('source'); ?></th>
                      <th><?php echo $this->lang->line('priority'); ?></th>
                      <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                      <th><?php echo $this->lang->line('live_consultant'); ?></th>
                      <?php } ?>
                      <?php
                        if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {
                      ?>
                      <th ><?php echo $fields_value->name; ?></th>
                      <?php
                      }
                      }
                      ?>
                      <th><?php echo $this->lang->line('created_by'); ?></th>
                      <th><?php echo $this->lang->line('status'); ?></th>
                      <th><?php echo $this->lang->line('fees')." (".$currency_symbol.")"; ?></th>
                      <th><?php echo $this->lang->line('discount')." (%)"; ?></th>
                      <th><?php echo $this->lang->line('paid')." (".$currency_symbol.")"; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
            </div>
            </div>
          </div>

          <div class="tab-pane" id="tab_3">
            <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover ajaxlist_old" data-export-title="<?php echo $this->lang->line('appointment_details'); ?>">
                  <thead>
                    <tr>
                      <th width="120px"><?php echo $this->lang->line('patient_name'); ?></th>
                      <th width="120px"><?php echo $this->lang->line('appointment_no'); ?></th>
                      <th><?php echo $this->lang->line('date'); ?></th>
                      <th width="90px"><?php echo $this->lang->line('phone'); ?></th>
                      <th width="80px"><?php echo $this->lang->line('gender'); ?></th>
                      <th><?php echo $this->lang->line('doctor'); ?></th>
                      <th><?php echo $this->lang->line('source'); ?></th>
                      <th><?php echo $this->lang->line('priority'); ?></th>
                      <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                      <th><?php echo $this->lang->line('live_consultant'); ?></th>
                      <?php } ?>
                      <?php
                        if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {
                      ?>
                      <th ><?php echo $fields_value->name; ?></th>
                      <?php
                      }
                      }
                      ?>
                      <th><?php echo $this->lang->line('created_by'); ?></th>
                      <th><?php echo $this->lang->line('status'); ?></th>
                      <th><?php echo $this->lang->line('fees')." (".$currency_symbol.")"; ?></th>
                      <th><?php echo $this->lang->line('discount')." (%)"; ?></th>
                      <th><?php echo $this->lang->line('paid')." (".$currency_symbol.")"; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
            </div>
            </div>
          </div>

        </div>
      </div>
  </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form id="formadd" accept-charset="utf-8" method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_appointment'); ?></h5>
        <div class="d-flex align-items-center gap-2 ms-auto me-2">
          <div class="sh-header-select-wrap">
            <select class="form-control form-control-sm patient_list_ajax" name="patient_id" id="addpatient_id"></select>
          </div>
          <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
          <a id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient btn btn-sm btn-light text-nowrap"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
          <?php } ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="pup-scroll-area">
        <div class="modal-body">

          <div class="sh-form-card">
            <div class="sh-card-header">
              <span class="sh-card-header-title"><?php echo $this->lang->line('appointment_details'); ?></span>
            </div>
            <div class="p-2">
              <div class="row g-2">
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('doctor'); ?></label><small class="req"> *</small>
                  <select class="form-control form-control-sm select2 doctor_select2" name="doctorid" onchange="getDoctorShift(this);getDoctorFees(this)" <?php if ((isset($disable_option)) && ($disable_option == true)) { echo 'disabled'; } ?> id="doctorid">
                    <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                    <option value="<?php echo $dvalue["id"]; ?>" <?php if ($doctor_select == $dvalue['id']) { echo 'selected'; } ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="charge_id" value="" id="charge_id" />
                  <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line("doctor_fees"); ?> (<?php echo $currency_symbol; ?>)</label><small class="req"> *</small>
                  <input type="text" name="amount" id="doctor_fees" class="form-control form-control-sm" readonly="readonly">
                  <span class="text-danger"><?php echo form_error('doctor_fees'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                  <select name="global_shift" id="global_shift" class="form-control form-control-sm select2" onchange="getShift()">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                  </select>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                  <input type="text" id="datetimepicker" name="date" class="form-control form-control-sm datetime">
                  <span class="text-danger"><?php echo form_error('date'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('slot'); ?></label><span class="req"> *</span>
                  <select name="slot" id="slot" onchange="validateTime(this)" class="form-control form-control-sm">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                  </select>
                  <span class="text-danger"><?php echo form_error('slot'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('appointment_priority'); ?></label>
                  <select class="form-control form-control-sm select2 appointment_priority_select2" name="priority">
                    <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["appoint_priority"]; ?></option>
                    <?php } ?>
                  </select>
                  <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('status'); ?><small class="req"> *</small></label>
                  <select name="appointment_status" class="form-control form-control-sm" id="appointment_status">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) { ?>
                    <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('discount_percentage'); ?></label>
                  <input type="text" name="discount_percentage" id="discount_percentage" class="form-control form-control-sm">
                </div>
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <div class="col-md-3">
                  <label class="form-label"><?php echo $this->lang->line('live_consultant_on_video_conference'); ?></label><small class="req"> *</small>
                  <select name="live_consult" id="live_consult" class="form-control form-control-sm">
                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                    <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                    <?php } ?>
                  </select>
                  <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                </div>
                <?php } ?>
                <div class="col-sm-4">
                  <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                  <select class="form-control form-control-sm payment_mode" name="payment_mode">
                    <?php foreach ($payment_mode as $key => $value) { ?>
                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                  </select>
                  <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                </div>
                <div class="col-12">
                  <label class="form-label"><?php echo $this->lang->line('message'); ?></label>
                  <textarea name="message" id="note" class="form-control form-control-sm"></textarea>
                  <span class="text-danger"><?php echo form_error('message'); ?></span>
                </div>
                <div class="col-sm-4 cheque_div d-none">
                  <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                  <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 cheque_div d-none">
                  <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                  <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                </div>
                <div class="col-sm-4 cheque_div d-none">
                  <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                  <input type="file" class="filestyle form-control form-control-sm" name="document">
                  <span class="text-danger"><?php echo form_error('document'); ?></span>
                </div>
                <div class="col-12">
                  <?php echo display_custom_fields('appointment'); ?>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
        <button type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
        <button type="submit" id="formaddbtn" name="save" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form id="rescheduleform" accept-charset="utf-8" method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="rescheduleModalLabel"><span id="model_title"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="pup-scroll-area">
        <div class="modal-body">
          <input type="hidden" name="appointment_id" id="appointment_id">

          <div class="sh-form-card">
            <div class="sh-card-header">
              <span class="sh-card-header-title"><?php echo $this->lang->line('appointment_details'); ?></span>
            </div>
            <div class="p-2">
              <div class="row g-2">
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('doctor'); ?></label><small class="req"> *</small>
                  <select class="form-control form-control-sm" onchange="getDoctorShift(this);getDoctorFeesEdit(this)" id="rdoctor" disabled>
                    <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                    <?php } ?>
                  </select>
                  <input type="hidden" id="rdoctor_id" name="rdoctor_id">
                  <span class="text-danger"><?php echo form_error('rdoctor'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line("doctor_fees") . ' (' . $currency_symbol . ')'; ?></label><small class="req"> *</small>
                  <input type="text" name="doctor_fees" id="rdoctor_fees_edit" class="form-control form-control-sm" readonly="readonly">
                  <span class="text-danger"><?php echo form_error('doctor_fees'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                  <select name="rglobal_shift" id="rglobal_shift_edit" onchange="getreschsduleShift()" class="form-control form-control-sm select2">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                  </select>
                  <span class="text-danger"><?php echo form_error('rglobal_shift'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('appointment_date'); ?></label><small class="req"> *</small>
                  <input type="text" id="rdates" name="appointment_date" class="form-control form-control-sm datetime" value="<?php echo set_value('dates'); ?>">
                  <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('slot'); ?></label><span class="req"> *</span>
                  <select name="rslot" id="rslot_edit" class="form-control form-control-sm" onchange="validateTime(this)">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                  </select>
                  <input type="hidden" id="rslot_edit_field" name="rslot_edit_field" />
                  <span class="text-danger"><?php echo form_error('rslot'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('appointment_priority'); ?></label>
                  <select class="form-control form-control-sm select2" name="priority" id="edit_appoint_priority">
                    <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["appoint_priority"]; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('discount_percentage'); ?></label>
                  <input type="text" name="discount_percentage" id="rdiscount_percentage" class="form-control form-control-sm">
                  <input type="hidden" name="rdiscount_percentage_hidden" id="rdiscount_percentage_hidden" class="form-control">
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('status'); ?><small class="req"> *</small></label>
                  <select name="edit_appointment_status" class="form-control form-control-sm" id="edit_appointment_status">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) { ?>
                    <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <div class="col-md-3">
                  <label class="form-label"><?php echo $this->lang->line('live_consultant_on_video_conference'); ?></label><small class="req"> *</small>
                  <select name="live_consult" id="edit_liveconsult" class="form-control form-control-sm">
                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                    <option value="<?php echo $yesno_key ?>" <?php if ($yesno_key == 'no') { echo "selected"; } ?>><?php echo $yesno_value ?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php } ?>
                <div class="col-12">
                  <label class="form-label"><?php echo $this->lang->line('message'); ?></label>
                  <textarea name="message" id="message" class="form-control form-control-sm"><?php echo set_value('message'); ?></textarea>
                  <span class="text-danger"><?php echo form_error('message'); ?></span>
                </div>
                <div id="customfield" class="col-12"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
        <button type="submit" id="rescheduleformbtn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('appointment_details'); ?></h5>
        <div class="d-flex align-items-center gap-2">
          <div id="edit_delete" class="d-flex align-items-center gap-2">
            <a href="#" data-bs-target="#rescheduleModal" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" onclick="if(window.currentAppointmentId){delete_record(window.currentAppointmentId);}return false;" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="pup-scroll-area">
        <div class="modal-body">
          <div class="sh-form-card mb-2">
            <div class="sh-info-grid">

              <!-- Row 1: Patient Name | Appointment No | Age | S/N -->
              <div class="row g-0">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                  <span class="sh-info-value highlight" id="patient_names"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('appointment_no'); ?></span>
                  <span class="sh-info-value highlight" id="appointmentno"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                  <span class="sh-info-value" id="patient_age"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('appointment_s_no'); ?></span>
                  <span class="sh-info-value" id="appointmentsno"></span>
                </div>
              </div>

              <!-- Row 2: Email | Date | Phone | Priority -->
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                  <span class="sh-info-value" id="emails"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('appointment_date'); ?></span>
                  <span class="sh-info-value" id="dating"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                  <span class="sh-info-value" id="phones"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('appointment_priority'); ?></span>
                  <span class="sh-info-value" id="appointpriority"></span>
                </div>
              </div>

              <!-- Row 3: Gender | Shift | Doctor | Slot -->
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                  <span class="sh-info-value" id="genders"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('shift'); ?></span>
                  <span class="sh-info-value" id="global_shift_view"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('doctor'); ?></span>
                  <span class="sh-info-value" id="doctors"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('slot'); ?></span>
                  <span class="sh-info-value text-capitalize" id="doctor_shift_view"></span>
                </div>
              </div>

              <!-- Row 4: Department | Amount | Source | Transaction ID -->
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('department'); ?></span>
                  <span class="sh-info-value" id="department_name"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('amount'); ?></span>
                  <span class="sh-info-value" id="pay_amount"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('source'); ?></span>
                  <span class="sh-info-value" id="source"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('transaction_id'); ?></span>
                  <span class="sh-info-value" id="trans_id"></span>
                </div>
              </div>

              <!-- Row 5 (conditional): Live Consult | Status -->
              <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('live_consultation'); ?></span>
                  <span class="sh-info-value" id="liveconsult"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('status'); ?></span>
                  <span class="sh-info-value" id="status"></span>
                </div>
              </div>
              <?php } ?>

              <!-- Row 6: Payment Note | Payment Mode | Collected By -->
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('payment_note'); ?></span>
                  <span class="sh-info-value" id="payment_note"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('payment_mode'); ?></span>
                  <span class="sh-info-value" id="payment_mode"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('collected_by'); ?></span>
                  <span class="sh-info-value" id="collected_by"></span>
                </div>
              </div>

              <!-- Row 7: Cheque (JS-toggled) -->
              <div class="row g-0 sh-row-divider" id="payrow" class="d-none">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('cheque_no'); ?></span>
                  <span class="sh-info-value" id="spn_chequeno"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('cheque_date'); ?></span>
                  <span class="sh-info-value" id="spn_chequedate"></span>
                </div>
              </div>

              <!-- Row 8: Document (JS-toggled) -->
              <div class="row g-0 sh-row-divider" id="paydocrow" class="d-none">
                <div class="col-12 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('document'); ?></span>
                  <span class="sh-info-value" id="spn_doc"></span>
                </div>
              </div>

              <!-- Row 9: Message (full width) -->
              <div class="row g-0 sh-row-divider">
                <div class="col-12 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('message'); ?></span>
                  <span class="sh-info-value" id="messages"></span>
                </div>
              </div>

              <!-- Custom Fields -->
              <div class="row g-0 sh-row-divider" id="field_data" class="d-none"></div>

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

<script>
   $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').removeClass('d-none');
      }else{
        $('.cheque_div').addClass('d-none');
      }
    });
</script>
<script type="text/javascript">
  $(function () {
    $('#easySelectable').easySelectable();
  })
</script>
<script type="text/javascript">
  $(function () {
    $('.select2').select2()
  });

  function holdModal(modalId) {
    var el = document.getElementById(modalId);
    if (el) bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false }).show();
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
</script>
<script type="text/javascript">
  $(document).ready(function (e) {

    $("form#formadd button[type=submit]").click(function() {            
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

  $("#formadd").on('submit', (function (e) {
    var did = $("#doctorid").val();
    $("#doctorinputid").val(did);
  
    var sub_btn_clicked = $("button[type=submit][clicked=true]");                  
    var sub_btn_clicked_name=sub_btn_clicked.attr('name');
    console.log(sub_btn_clicked_name);
      e.preventDefault();
      $.ajax({
        url: baseurl+'admin/appointment/add',
        type: "POST",
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
          beforeSend: function() {
       sub_btn_clicked.btnLoading();
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
          $('.ajaxlist_todays,.ajaxlist_upcoming,.ajaxlist_old').DataTable().ajax.reload();
         shModal('myModal').hide();
         if(sub_btn_clicked_name === "save_print") {                            
           printAppointment(data.appointment_id);
         }  
        }
       sub_btn_clicked.btnReset();
        },
        error: function () {
    sub_btn_clicked.btnReset();
      },
      complete: function() {
          sub_btn_clicked.btnReset();
    }
    });
  })); 
}); 

function printAppointment(id){
    $('#myModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $("#global_shift").select2().select2("val", '');
    });
    
    $.ajax({
            url: base_url+'admin/appointment/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
               beforeSend: function() {
                           
               },
            success: function (data) {      
           popup(data.page);
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");    
               
      },
      complete: function() {           
     
      }
        });
}
	
$(document).ready(function (e) {
$("#formedit").on('submit', (function (e) {
  $("#formeditbtn").btnLoading();
  e.preventDefault();
    $.ajax({
      url: baseurl+'admin/appointment/update',
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

  $("#datetimepicker").on("dp.change", function (e) {
    if($("#global_shift").val() != ''){
        getShift();
    }
  });

  $("#global_shift").on('select2:select', function () {
      getShift();
  });

  $("#dates").on("dp.change", function (e) {
    if($("#global_shift_edit").val() != ''){
        getShiftEdit();
    }
  });

  $("#rdates").on("dp.change", function (e) {
    if($("#rglobal_shift_edit").val() != ''){
        getreschsduleShift();
    }
  });

  $("#rescheduleform").on('submit', (function (e) {
     $("#rescheduleformbtn").btnLoading();
      e.preventDefault();
        $.ajax({
          url: baseurl+'admin/appointment/reschedule',
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
            $("#rescheduleformbtn").btnReset();
          },
          error: function () {

          }
        });
  }));
});

function get_PatientDetails(id) {
  $("#patient_name").html("patient_name");
  $('#gender option').removeAttr('selected');
  $.ajax({
    url: baseurl+'admin/patient/patientDetails',
    type: "POST",
    data: {id: id},
    dataType: 'json',
    success: function (res) {
      if (res) {
      $('#patient_name').val(res.patient_name);
      $('#patientid').val(res.id);      
      $('#guardian_name').text(res.guardian_name);
      $('#phone').val(res.mobileno);
      $('#email').val(res.email);
      $("#age").text(res.age);
      $("#bp").text(res.bp);
      $("#month").text(res.month);
      $("#symptoms").text(res.symptoms);
      $("#known_allergies").text(res.known_allergies);
      $("#address").text(res.address);
      $("#height").text(res.height);
      $("#weight").text(res.weight);
      $("#marital_status").text(res.marital_status);
      $('#gender option[value="'+res.gender+'"]').attr("selected","selected");
    } else {
      $('#patient_name').val('');
      $('#phone').val("");
      $('#email').val("");
      $("#note").val("");
    }
  }
  });
}

	function getBed(bed_group, bed = '', active, htmlid = 'bed_no') {
        var div_data = "";
        $('#' + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $("#" + htmlid).select2("val", 'l');
        $.ajax({
            url: baseurl+'admin/setup/bed/getbedbybedgroup',
            type: "POST",
            data: {bed_group: bed_group, bed_id: bed, active: active},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {                  
                    div_data += "<option value=" + obj.id + ">" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#' + htmlid).append(div_data);
                $("#" + htmlid).select2().select2('val', bed);
            }
        });
    }

    function viewreschedule(id,modeltype){ 
      
      if(modeltype == '1'){
        $('#model_title').html('<?php echo $this->lang->line('reschedule'); ?>');
      } else if (modeltype == '2'){
        $('#model_title').html('<?php echo $this->lang->line('approve_appointment'); ?>');  
      }
      
      shModal('rescheduleModal').show();
      $('#appointment_id').val(id);
      $('#rdiscount_percentage_hidden').val(0);
      $.ajax({
        url: baseurl+'admin/appointment/getDetailsAppointment',
        type: "GET",
        data: {appointment_id: id},
        dataType: 'json',
        success: function (data) {
            
			$('#customfield').html(data.custom_fields_value);
			$("#rdoctor").val(data.doctor);
			$("#rdates").val(data.date);         
			$("#rdoctor_id").val(data.doctor);		  
			$("#edit_appoint_priority").val(data.priority).trigger("change");
			$("#message").val(data.message); 
			$("#edit_appointment_status").val(data.appointment_status);				
			 
			if(data.appointment_status == 'approved'){
				$("#rdoctor_fees_edit").val(data.amount); 
				$("#rdiscount_percentage").val(data.discount_percentage);
        $("#rdiscount_percentage_hidden").val(data.discount_percentage);
				
			}else{
				$("#rdoctor_fees_edit").val('0'); 
				$("#rdiscount_percentage").val('0');
			}
			getDoctorShift("",data.doctor,data.shift_id);
			$('select[id="rdoctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
			$('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");	
			console.log(data);
			$("#rslot_edit").val(data.slot_id);
			$("#rslot_edit_field").val(data.slot_id);          
        }
      });
    }

function getRecord(id) {
 
  shModal('viewModal').hide();
  shModal('myModaledit').show();
  $.ajax({
    url: baseurl+'admin/appointment/getDetailsAppointment',
    type: "GET",
    data: {appointment_id: id},
    dataType: 'json',
    success: function (data) {
      $('#customfield').html(data.custom_fields_value);
      $("#id").val(data.id);
      $("#doctor").val(data.doctor).trigger("change");
      $("#dates").val(data.date); 
      $("#slot_edit_field").val(data.shift_id);
      getDoctorShift("",data.doctor,data.global_shift_id);
      $("#edit_appointment_no").val(data.appointment_no);
      $("#edit_appoint_priority").val(data.priority).trigger("change");
      $("#message").val(data.message);      
      if(data.patient_id == null){
        data.patient_id = ""
      }
      var option = new Option(data.patients_name, data.patient_id, true, true);
      $("#myModaledit .patient_list_ajax").append(option).trigger('change');
      $("#myModaledit .patient_list_ajax").trigger({
          type: 'select2:select',
          params: {
              data: data
          }
      });
      $('select[id="edit_gender"] option[value="' + data.patients_gender + '"]').attr("selected", "selected");
      $('select[id="doctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
      $('select[id="appointment_status"] option[value="' + data.appointment_status + '"]').attr("selected", "selected");
      $('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");
      $('select[id="edit_appoint_priority"] option[value="' + data.priority + '"]').attr("selected", "selected");

    },
  })
}

function viewDetail(id) {
  window.currentAppointmentId = id;
  shModal('viewModal').show();
  $.ajax({
    url: baseurl+'admin/appointment/getDetailsAppointment',
    type: "GET",
    data: {appointment_id: id},
    dataType: 'json',
    success: function (data) {
      var table_html = '';
      $.each(data.field_data, function (i, obj)
      {
        var field_value = (obj.field_value == null) ? "" : obj.field_value;
        var name = obj.name;
        if (obj.visible_on_patient_panel == 1) {
          table_html += '<div class="col-6 col-md-3 sh-info-item"><span class="sh-info-label">' + $('<span>').text(capitalizeFirstLetter(name))[0].outerHTML + '</span><span class="sh-info-value">' + $('<span>').text(field_value)[0].outerHTML + '</span></div>';
        }
      });
      if (table_html) {
        $("#field_data").html(table_html).show();
      } else {
        $("#field_data").addClass('d-none');
      }
  $("#dating").text(data.date);
  $("#appointmentno").text(data.appointment_no);
  $("#appointmentsno").text(data.appointment_serial_no);
  $("#patient_names").text(data.patients_name);
  $("#genders").text(data.patients_gender);
  $("#emails").text(data.patient_email);
  $("#appointpriority").text(data.appoint_priority);
  $("#phones").text(data.patient_mobileno);
  $("#doctors").text(data.name + " " + data.surname+" ("+data.employee_id+")");
  $("#department_name").text(data.department_name);
  $("#messages").text(data.message);
  $("#liveconsult").html(data.edit_live_consult);
  $("#global_shift_view").text(data.global_shift_name);
  $("#doctor_shift_view").text(data.doctor_shift_name);
  $("#source").text(data.source);
  if(data.amount > 0){
    $("#pay_amount").text(data.amount);
  }else{
    $("#pay_amount").text('');
  }
  $("#payment_mode").text(data.payment_mode);
  $("#trans_id").text(data.transaction_id);
  $("#payment_note").text(data.payment_note);
  $("#patient_age").text(data.patient_age);
  $("#collected_by").text(data.received_by);

  if(data.payment_mode=="Cheque"){
    $("#payrow").removeClass('d-none');
    $("#paydocrow").removeClass('d-none');
    $("#spn_chequeno").text(data.cheque_no);
    $("#spn_chequedate").text(data.cheque_date);
    $("#spn_doc").text(data.doc);
  }else{
    $("#payrow").addClass('d-none');
    $("#paydocrow").addClass('d-none');
    $("#spn_chequeno").text("");
    $("#spn_chequedate").text("");
  }

  var labelClass = "badge bg-danger-subtle text-danger";
  if (data.appointment_status == "approved") {
    labelClass = "badge bg-success-subtle text-success";
  } else if (data.appointment_status == "pending") {
    labelClass = "badge bg-warning-subtle text-warning";
  }

  if(data.appointment_status == "cancel"){
    $("#trans_id").text("");
  }

  $("#status").html($('<small>').addClass(labelClass).text(data.appointmentstatus));
  $("#edit_delete").html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printAppointment(" + id +")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php if ($this->rbac->hasPrivilege('appointment', 'can_delete')) {?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='delete_record(" + id +")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?> ");

  },
  });
}

function delete_record(id) {
  if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
    $.ajax({
      url: baseurl+'admin/appointment/delete/' + id,
      type: "POST",
      data: {patient_id: id},
      dataType: 'json',
      success: function (res) {
        if (res.status == 'success') {
          shModal('viewModal').hide();
          successMsg(res.message);
          $('.ajaxlist_todays,.ajaxlist_upcoming,.ajaxlist_old').DataTable().ajax.reload();
      }
      }
    })
  }
}

</script>
<script type="text/javascript">
  function askconfirm() {
    if (confirm("<?php echo $this->lang->line('approve_appointment'); ?>") ) {
      return true;
    } else {
      return false;
    }
  } 
  
  $('#myModal').on('shown.bs.modal', function () {
    var $sel = $('#addpatient_id');
    if ($sel.hasClass('select2-hidden-accessible')) { $sel.select2('destroy'); }
    $sel.select2({
        ajax: {
            url: "<?= base_url(); ?>admin/patient/getPatientListAjax",
            type: "post", dataType: 'json', delay: 250,
            data: function (params) { return { searchTerm: params.term }; },
            processResults: function (response) { return { results: response }; },
            cache: true
        },
        dropdownParent: $('#myModal')
    });
  });

  $('#myModal').on('hidden.bs.modal', function () {
    $(".appointment_priority_select2").select2("val", "");
    $(".doctor_select2").select2("val", "");
    $("#addpatient_id").select2("val", "");
    $('#formadd').find('input:text, input:password, input:file, textarea').val('');
    $('#formadd').find('select option:selected').removeAttr('selected');
    $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
  });

  $(".modalbtnpatient").click(function(){   
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
  });
  
  $(document).ready(function (e) {
      modal_click_disabled('myModal', 'viewModal', 'myModaledit', 'rescheduleModal');
  });
</script> 
<script type="text/javascript">

$("#appointment_status").change(function(){
  var appointment_status = $('#appointment_status').val();
      var doctor_id = $('#doctorid').val();    
      if(appointment_status == 'approved'){
        $.ajax({
            url: baseurl+'admin/appointment/getDoctorFees/',
            type: "POST",
            data: {doctor_id: doctor_id},
            dataType: 'json',
            success: function (res) {
              $("#doctor_fees").val(res.fees);
              $("#charge_id").val(res.charge_id);
          }
        });
      }else{
          $('#doctor_fees').val('0');
      }
});
  
  $("#edit_appointment_status").change(function(){

      var edit_appointment_status = $('#edit_appointment_status').val();
      var doctor_id = $('#rdoctor').val();
      if(edit_appointment_status == 'approved'){
        $.ajax({
            url: baseurl+'admin/appointment/getDoctorFees/',
            type: "POST",
            data: {doctor_id: doctor_id},
            dataType: 'json',
            success: function (res) {
              $("#rdoctor_fees_edit").val(res.fees);
              $("#charge_id_edit").val(res.charge_id);
              var rdiscount_percentage_hidden=$("#rdiscount_percentage_hidden").val();
              $("#rdiscount_percentage").val(rdiscount_percentage_hidden);			  
          }
        });
      }else{
          $('#rdoctor_fees_edit').val('0');
          $('#rdiscount_percentage').val('0');

      }
});

  function getDoctorFees(object){
    let doctor_id = object.value;
     $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      beforeSend: function() {
        $("#doctor_fees").val("");
        $("#charge_id").val("");
    },
      success: function (res) {
       if(res.status == 1){
        $("#doctor_fees").val(res.fees);
        $("#charge_id").val(res.charge_id);
       }        
      }
    })
  }

  function getDoctorFeesEdit(object){
    let doctor_id = object.value;
     $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      success: function (res) {
        $("#doctor_fees_edit").val(res.fees);
        $("#rdoctor_fees_edit").val(res.fees);
        $("#charge_id_edit").val(res.charge_id);
         $("#edit_appointment_status").trigger("change");
      }
    })
  }
</script>
<script>
  function getShift(){
      var div_data = "";
      var date = $("#datetimepicker").val();
      var doctor = $("#doctorid").val();
      var global_shift = $("#global_shift").val();
      if(!doctor || !date || !global_shift){ return; }
      $.ajax({
          url: baseurl+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#slot").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#slot').append(div_data);
          },
          error: function(xhr){ console.error('getShift failed', xhr.status, xhr.responseText); }
      });
  }

  function getShiftEdit(){

      var div_data = "";
      var date = $("#dates").val();
      var doctor = $("#doctor").val();
      var global_shift = $("#global_shift_edit").val();
      $.ajax({
          url: baseurl+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#slot_edit").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#slot_edit').append(div_data);
              $("#slot_edit").val($("#slot_edit_field").val()).trigger('change');
          }
      });
  }

  function getreschsduleShift(){
      var div_data = "";
      var date = $("#rdates").val();
      var doctor = $("#rdoctor").val();
      var global_shift = $("#rglobal_shift_edit").val();    
      $.ajax({
          url: baseurl+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#rslot_edit").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#rslot_edit').append(div_data);
              $("#rslot_edit").val($("#rslot_edit_field").val()).trigger('change');
          }
      });
  }

  function getDoctorShift(obj,doctor_id = null,global_shift_id=null){ 
        if(doctor_id == null){
          var doctor_id = obj.value;
        }
        var select = "";
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            type: 'POST',
            url: base_url + "admin/onlineappointment/doctorshiftbyid",
            data: {doctor_id:doctor_id},
            dataType: 'json',
            success: function(res){
                $.each(res, function(i, list){
                    select_box += "<option value='"+ list.id +"'>"+ list.name +"</option>";
                });
                $("#global_shift").html(select_box);
                $("#global_shift_edit").html(select_box);
                $("#rglobal_shift_edit").html(select_box);
                if(global_shift_id!=null){
                  $("#global_shift_edit").val(global_shift_id).trigger('change');
                  $("#rglobal_shift_edit").val(global_shift_id).trigger('change');				  
                }
           }
        });
    }

    function validateTime(obj){
		let id = obj.value;

		// Pick date from the form that contains the triggering slot element
		let $form = $(obj).closest('form');
		let date  = $form.find('input[name="date"], input[name="appointment_date"]').val() || '';
		if (!date) {
			date = $("#datetimepicker").val() || $("#rdates").val() || '';
		}

		if(id){
			$.ajax({
				url: baseurl+'admin/onlineappointment/getshiftbyid',
				type: "POST",
				data: {id:id,date:date},
				dataType: 'json',
				success: function(res){
					if (res.end_time && res.date) {
						var t = res.end_time.split(':');
						var d = res.date.split('-');
						var endDate = new Date(
							parseInt(d[0]),
							parseInt(d[1]) - 1,
							parseInt(d[2]),
							parseInt(t[0]),
							parseInt(t[1]),
							parseInt(t[2] || 0)
						);
						if (new Date() > endDate) {
							alert("<?php echo $this->lang->line("appointment_time_is_expired"); ?>");
						}
					}
				}
			});
		}      
    }
</script>
<script type="text/javascript">
( function ( $ ) {
  'use strict';
  $(document).ready(function () {
    initDatatable('ajaxlist_todays','admin/appointment/getappointmentdatatabletoday/',[],[],100);
    initDatatable('ajaxlist_upcoming','admin/appointment/getappointmentdatatableupcoming/',[],[],100);
    initDatatable('ajaxlist_old','admin/appointment/getappointmentdatatableold/',[],[],100);
	
  });
} ( jQuery ) ) 
</script>
 
<script>
$(document).ready(function () {
  /* initialize all tooltips after Bootstrap JS is loaded */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<?php if ($this->input->get('action') === 'add'): ?>
<script>$(function(){ shModal('myModal').show(); shCleanUrlParam('action'); });</script>
<?php endif; ?>
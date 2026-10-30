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
				<?php if ($this->rbac->hasPrivilege('appointment', 'can_add')) { ?>
				<a data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary btn-sm addappointment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_appointment'); ?></a>
				<?php } ?>
			</div>

          <div class="tab-content">
            <div class="tab-pane show active" id="tab_1">
			
				<div class="card-body">
           <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover ajaxlisttodays" data-export-title="<?php echo $this->lang->line('appointment_billing'); ?>" >
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
                    <th><?php echo $fields_value->name; ?></th>
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
              <table class="table table-striped table-bordered table-hover ajaxlistupcoming" data-export-title="<?php echo $this->lang->line('appointment_billing'); ?>" >
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
                    <th><?php echo $fields_value->name; ?></th>
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
              <table class="table table-striped table-bordered table-hover ajaxlistold" data-export-title="<?php echo $this->lang->line('appointment_billing'); ?>" >
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
                    <th><?php echo $fields_value->name; ?></th>
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
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_appointment'); ?></h5>
        <div class="d-flex align-items-center gap-2 ms-auto me-2">
          <div class="sh-header-select-wrap">
            <select class="form-control form-control-sm patient_list_ajax" form="formadd" id="addpatient_id" name="patient_id"></select>
          </div>
          <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
          <a data-bs-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient sh-newpatient-btn"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
          <?php } ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formadd" accept-charset="utf-8" method="post">
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
                    <select class="form-control form-control-sm select2 doctor_select2" name="doctorid" onchange="getDoctorShift(this);getDoctorFees(this)"
                      <?php if (isset($disable_option) && $disable_option == true) { echo 'disabled'; } ?>
                      name="doctor" id="doctorid">
                      <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                      <?php foreach ($doctors as $dkey => $dvalue) { ?>
                      <option value="<?php echo $dvalue['id']; ?>" <?php if ($doctor_select == $dvalue['id']) { echo 'selected'; } ?>><?php echo $dvalue['name'] . ' ' . $dvalue['surname'] . ' (' . $dvalue['employee_id'] . ')'; ?></option>
                      <?php } ?>
                    </select>
                    <input type="hidden" name="charge_id" value="" id="charge_id" />
                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label"><?php echo $this->lang->line('doctor_fees') . ' (' . $currency_symbol . ')'; ?></label><small class="req"> *</small>
                    <input type="text" name="amount" id="doctor_fees" class="form-control form-control-sm" readonly="readonly">
                    <span class="text-danger"><?php echo form_error('doctor_fees'); ?></span>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                    <select name="global_shift" id="global_shift" onchange="getShift()" class="form-control form-control-sm select2">
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
                      <option value="<?php echo $dvalue['id']; ?>"><?php echo $dvalue['appoint_priority']; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label"><?php echo $this->lang->line('status'); ?><small class="req"> *</small></label>
                    <select name="appointment_status" class="form-control form-control-sm" id="appointment_status">
                      <option value=""><?php echo $this->lang->line('select'); ?></option>
                      <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) { ?>
                      <option value="<?php echo $appointment_status_key; ?>"><?php echo $appointment_status_value; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label"><?php echo $this->lang->line('discount_percentage'); ?></label>
                    <input type="text" name="discount_percentage" id="discount_percentage" class="form-control form-control-sm">
                  </div>
                  <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                  <div class="col-md-3">
                    <label class="form-label"><?php echo $this->lang->line('live_consultant_on_video_conference'); ?></label>
                    <select name="live_consult" id="live_consult" class="form-control form-control-sm">
                      <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                      <option value="<?php echo $yesno_key; ?>" <?php if ($yesno_key == 'no') { echo 'selected'; } ?>><?php echo $yesno_value; ?></option>
                      <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                  </div>
                  <?php } ?>
                  <div class="col-sm-3">
                    <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                    <select class="form-control form-control-sm payment_mode" name="payment_mode">
                      <?php foreach ($payment_mode as $key => $value) { ?>
                      <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label"><?php echo $this->lang->line('message'); ?></label>
                    <textarea name="message" id="note" class="form-control form-control-sm"></textarea>
                    <span class="text-danger"><?php echo form_error('message'); ?></span>
                  </div>
                  <div class="cheque_div col-12 d-none" >
                    <div class="row g-2">
                      <div class="col-sm-4">
                        <label class="form-label"><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                        <input type="text" name="cheque_no" id="cheque_no" class="form-control form-control-sm">
                      </div>
                      <div class="col-sm-4">
                        <label class="form-label"><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                        <input type="text" name="cheque_date" id="cheque_date" class="form-control form-control-sm date">
                      </div>
                      <div class="col-sm-4">
                        <label class="form-label"><?php echo $this->lang->line('attach_document'); ?></label>
                        <input type="file" class="filestyle form-control form-control-sm" name="document">
                        <span class="text-danger"><?php echo form_error('document'); ?></span>
                      </div>
                    </div>
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
          <button type="submit" id="formaddprintbtn" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' name="save_print" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
          <button type="submit" id="formaddbtn" name="save" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- dd -->

<!-- ==========================reschedule modal added=================================== -->

<div class="modal fade sh-modal sh-modal-accent" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form id="rescheduleform" accept-charset="utf-8" method="post">
      <input type="hidden" name="appointment_id" id="appointment_id">
      <input type="hidden" id="rdoctor_id" name="rdoctor_id">
      <input type="hidden" id="edit_is_emergency" name="edit_is_emergency">
      <input type="hidden" id="charge_id_edit" name="charge_id_edit">
      <div class="modal-header">
        <h5 class="modal-title" id="rescheduleModalLabel"><span id="model_title"></span></h5>
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
                  <select class="form-control form-control-sm" onchange="getDoctorShift(this);getDoctorFeesEdit(this)" id="rdoctor" disabled>
                    <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] . " (" . $dvalue["employee_id"] . ")"; ?></option>
                    <?php } ?>
                  </select>
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
                <div class="col-sm-3" id="edit_payment_mode_div">
                  <label class="form-label"><?php echo $this->lang->line('payment_mode'); ?></label>
                  <select class="form-control form-control-sm edit_payment_mode" name="edit_payment_mode">
                    <?php foreach ($payment_mode as $key => $value) { ?>
                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php } ?>
                  </select>
                  <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                </div>
                <div class="col-sm-3">
                  <label class="form-label"><?php echo $this->lang->line('discount_percentage'); ?></label>
                  <input type="text" name="discount_percentage" id="rdiscount_percentage" class="form-control form-control-sm">
                  <input type="hidden" name="rdiscount_percentage_hidden" id="rdiscount_percentage_hidden">
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
            <a href="#" class="btn btn-sm btn-light" data-bs-target="#editModal" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
            <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" onclick="delete_recordById('<?php echo base_url(); ?>admin/appointment/delete/#', '<?php echo $this->lang->line('success_message') ?>')" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
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
                  <span class="sh-info-value" id="appointment_s_no"></span>
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

              <!-- Row 6: Payment Note | Payment Mode -->
              <div class="row g-0 sh-row-divider">
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('payment_note'); ?></span>
                  <span class="sh-info-value" id="payment_note"></span>
                </div>
                <div class="col-6 col-md-3 sh-info-item">
                  <span class="sh-info-label"><?php echo $this->lang->line('payment_mode'); ?></span>
                  <span class="sh-info-value" id="payment_mode"></span>
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

            </div>
          </div>
          <!-- Custom Fields -->
          <div class="sh-form-card mb-0 d-none" id="custom_fields_card">
            <div class="sh-card-header">
              <span class="sh-card-header-title"><?php echo $this->lang->line('custom_fields'); ?></span>
            </div>
            <div class="p-2">
              <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="field_data"></table>
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

<script>
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
  
  $("#edit_appointment_status_old").change(function(){

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
          }
        });
      }else{
          $('#rdoctor_fees_edit').val('0');
          $('#rdiscount_percentage').val('0');
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
    (function(){var _el=document.getElementById(modalId); if(_el) bootstrap.Modal.getOrCreateInstance(_el, {backdrop:'static', keyboard:false}).show();})();;
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

  $('.addappointment').click(function(){
      $('#formadd')[0].reset();
  });

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
      if(sub_btn_clicked_name === "save_print") {                            
           $("#formaddprintbtn").btnLoading();
       }else{
        $("#formaddbtn").btnLoading();
       }  

      e.preventDefault();
      $.ajax({
        url: base_url+'admin/appointment/add',
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
         $('.ajaxlisttodays,.ajaxlistupcoming,.ajaxlistold').DataTable().ajax.reload();
          shModal('myModal').hide();
          if(sub_btn_clicked_name === "save_print") {
           printAppointment(data.appointment_id);
         }else{
          successMsg(data.message);
         }
        }

        // reset buttons
        if(sub_btn_clicked_name === "save_print") {                            
           $("#formaddprintbtn").btnReset();
         }else{
            $("#formaddbtn").btnReset();
         }
        // reset buttons
        },
        error: function () {
      },
      complete: function() {           
      }
    });
  }));
});

function printAppointment(id){
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
      url: base_url+'admin/appointment/update',
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
    url: base_url+'admin/patient/patientDetails',
    type: "POST",
    data: {id: id},
    dataType: 'json',
    success: function (res) {
      if (res) {
      $('#patient_name').val(res.patient_name);
      $('#patientid').val(res.id);      
      $('#guardian_name').html(res.guardian_name);
      $('#phone').val(res.mobileno);
      $('#email').val(res.email);
      $("#age").html(res.age);
      $("#bp").html(res.bp);
      $("#month").html(res.month);
      $("#symptoms").html(res.symptoms);
      $("#known_allergies").html(res.known_allergies);
      $("#address").html(res.address);
      $("#height").html(res.height);
      $("#weight").html(res.weight);
      $("#marital_status").html(res.marital_status);
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
            url: base_url+'admin/setup/bed/getbedbybedgroup',
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

    function viewreschedule_old(id,modeltype){
        
		if(modeltype == '1'){
			$('#model_title').html('<?php echo $this->lang->line('reschedule'); ?>');
		} else if (modeltype == '2'){
			$('#model_title').html('<?php echo $this->lang->line('approve_appointment'); ?>');  
		}
      
		shModal('rescheduleModal').show();
		$('#appointment_id').val(id);
		$.ajax({
			url: baseurl+'admin/appointment/getDetailsAppointment',
			type: "GET",
			data: {appointment_id: id},
			dataType: 'json',
			success: function (data) {	  
		  
				$('#customfield').html(data.custom_fields_value);
					$("#rdoctor").val(data.doctor).trigger("change");
				$("#rdates").val(data.date);         
				$("#rdoctor_id").val(data.doctor);		  
				$("#edit_appoint_priority").val(data.priority).trigger("change");
				$("#message").val(data.message); 
				$("#edit_appointment_status").val(data.appointment_status);  
				$("#rdiscount_percentage").val(data.discount_percentage); 
				getDoctorShift("",data.doctor,data.shift_id);
				$('select[id="rdoctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
				$('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");		 
				$("#rslot_edit").val(data.slot_id);
				$("#rslot_edit_field").val(data.slot_id);         
				$("#rdoctor_fees_edit").val(data.standard_amount);         
          
			}
		});
    }

    function viewreschedule(id,modeltype){ 
      
      if(modeltype == '1'){
        $('#model_title').html('<?php echo $this->lang->line('reschedule'); ?>');
      } else if (modeltype == '2'){
        $('#model_title').html('<?php echo $this->lang->line('approve_appointment'); ?>');  
      }
      
      // shModal('rescheduleModal').show();
      $('#appointment_id').val(id);
      $('#rdiscount_percentage_hidden').val(0);
      var doctor_id;
      var payment_mode_type;
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
        $("#rslot_edit").val(data.slot_id);
        $("#rslot_edit_field").val(data.slot_id);      
        doctor_id=data.doctor;//added
        payment_mode_type=data.payment_mode_type;//added

        if(payment_mode_type=="" || payment_mode_type==null){
            $("#edit_payment_mode_div").removeClass('d-none');
        }else{
            $("#edit_payment_mode_div").addClass('d-none');
        }
      },
      complete:function(res){
        getDoctorFeeschargeIdEdit(doctor_id);//added
        shModal('rescheduleModal').show();
      }
      });
    }


  function getRecord(id) {
    shModal("viewModal").hide();
    shModal('myModaledit').show();
    $.ajax({
      url: baseurl+'admin/bill/get_appointment_detail',
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
  $('#custom_fields_card').hide().find('#field_data').empty();
  shModal('viewModal').show();
  $.ajax({
    url: baseurl+'admin/bill/get_appointment_detail',
    type: "GET",
    data: {appointment_id: id},
    dataType: 'json',
    success: function (data) {
      var table_html = '';
      var visible_rows = 0;
      $.each(data.field_data || [], function (i, obj) {
        var raw = (obj.field_value == null) ? '' : String(obj.field_value).trim();
        var is_patient = parseInt(obj.visible_on_patient_panel, 10) === 1;
        if (is_patient && raw !== '') {
          visible_rows++;
          table_html += "<tr><th width='15%'><span class='vcustom_name'>" + capitalizeFirstLetter(obj.name) + "</span></th><td width='85%'><span class='vcustom_value'>" + raw + "</span></td></tr>";
        }
      });
      $("#field_data").html(table_html);
      if (visible_rows > 0) {
        $('#custom_fields_card').show();
      } else {
        $('#custom_fields_card').hide();
      }
  $("#dating").html(data.date);  
  $("#appointmentno").html(data.appointment_no);
  $("#patient_names").html(data.patients_name);
  $("#genders").html(data.patients_gender);
  $("#emails").html(data.patient_email);
  $("#appointpriority").html(data.appoint_priority);
  $("#phones").html(data.patient_mobileno);
  $("#doctors").html(data.name + " " + data.surname+" ("+data.employee_id+")");
  $("#messages").html(data.message);
  $("#liveconsult").html(data.edit_live_consult);
  $("#global_shift_view").html(data.global_shift_name);
  $("#doctor_shift_view").html(data.doctor_shift_name);
  $("#source").html(data.source);
    
  if(data.amount > 0){
     $("#pay_amount").html('<?php echo $currency_symbol; ?>'+data.amount);
  }else{
    $("#pay_amount").html('');
  }
  
  $("#payment_mode").html(data.payment_mode);
  $("#trans_id").html(data.transaction_id);
  $("#payment_note").html(data.payment_note); 
  $("#patient_age").html(data.patient_age);
  $("#appointment_s_no").html(data.appointment_serial_no);
  $("#department_name").html(data.department_name);

  if(data.payment_mode=="Cheque"){
    $("#payrow").removeClass('d-none');
    $("#paydocrow").removeClass('d-none');
    $("#spn_chequeno").html(data.cheque_no);
    $("#spn_chequedate").html(data.cheque_date);
    $("#spn_doc").html(data.doc);
  }else{
    $("#payrow").addClass('d-none');
    $("#paydocrow").addClass('d-none');
    $("#spn_chequeno").html("");
    $("#spn_chequedate").html("");
  }
 
  var label = "";
   if (data.appointmentstatus == "Approved") {
    var label = "class='badge bg-success'";
  } else if (data.appointmentstatus == "Pending") {
    var label = "class='badge bg-warning'";
  } 
  else{
    var label = "class='badge bg-danger'";
  }  
  
  if(data.appointment_status == "cancel"){
    $("#trans_id").html("");
  }

  $("#status").html("<small " + label + " >" + data.appointmentstatus + "</small>");
  
  $("#edit_delete").html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='printAppointment(" + id +")' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php if ($this->rbac->hasPrivilege('appointment', 'can_delete')) {?><a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' onclick='delete_record(" + id +")' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?> ");

  },
  });
}

function delete_record(id) {
  if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
    $.ajax({
      url: base_url+'admin/appointment/delete/' + id,
      type: "POST",
      data: {patient_id: id},
      dataType: 'json',
      success: function (res) {
        if (res.status == 'success') {
        shModal('viewModal').hide();
        successMsg(res.message);
        $('.ajaxlisttodays,.ajaxlistupcoming,.ajaxlistold').DataTable().ajax.reload();
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
      modal_click_disabled('myModal', 'viewModal', 'myModaledit');
  });
</script> 
<script type="text/javascript">
  function getDoctorFees(object){
    let doctor_id = object.value;
     $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      success: function (res) {
        $("#doctor_fees").val(res.fees);
        $("#charge_id").val(res.charge_id);
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
      }
    })
  }

  function getDoctorFeeschargeIdEdit(doctor_id){
    $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      success: function (res) {
        $("#charge_id_edit").val(res.charge_id);
      }
    });
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
          url: base_url+'admin/onlineappointment/getShift',
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
          url: base_url+'admin/onlineappointment/getShift',
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
    let date = (obj.id === 'rslot_edit') ? $("#rdates").val() : $("#datetimepicker").val();
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
              var endDate = new Date(parseInt(d[0]), parseInt(d[1])-1, parseInt(d[2]), parseInt(t[0]), parseInt(t[1]), parseInt(t[2]||0));
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
    initDatatable('ajaxlisttodays','admin/bill/getappointmentdatatabletodays',[],[],100);
    initDatatable('ajaxlistupcoming','admin/bill/getappointmentdatatableupcoming',[],[],100);
    initDatatable('ajaxlistold','admin/bill/getappointmentdatatableold',[],[],100);
  });
} ( jQuery ) ) 
</script>

<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$_apt_has_image  = !empty($result['image']) && strpos($result['image'], 'no_image') === false;
$_apt_file       = $_apt_has_image ? $result['image'] : 'uploads/patient_images/no_image.png';
if (!$_apt_has_image) {
    $_apt_parts    = preg_split('/\s+/', trim($result['patient_name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $_apt_initials = count($_apt_parts) === 0 ? '?' : (count($_apt_parts) === 1
        ? mb_strtoupper(mb_substr($_apt_parts[0], 0, 1))
        : mb_strtoupper(mb_substr($_apt_parts[0], 0, 1) . mb_substr($_apt_parts[count($_apt_parts) - 1], 0, 1)));
}
$_apt_age        = $this->customlib->get_patient_current_age($result['id']);
$_apt_barcode    = './uploads/patient_id_card/barcodes/' . $id . '.png';
$_apt_qrcode     = './uploads/patient_id_card/qrcode/' . $id . '.png';
?>
<div class="container-fluid px-1 py-1">

    <!-- Patient welcome banner -->
    <div class="sh-welcome-banner">
        <div class="sh-profile-avatar-wrap">
            <?php if ($_apt_has_image): ?>
                <img src="<?php echo $this->media_storage->getImageURL($_apt_file); ?>"
                     alt="<?php echo html_escape($result['patient_name']); ?>"
                     class="sh-profile-avatar">
            <?php else: ?>
                <div class="sh-profile-avatar-initials"><?php echo html_escape($_apt_initials); ?></div>
            <?php endif; ?>
        </div>
        <div class="sh-welcome-text">
            <h2><?php echo html_escape($result['patient_name']); ?></h2>
            <p class="sub"><?php echo $this->lang->line('my_appointments'); ?></p>
            <div class="sh-welcome-meta">
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('patient_id'); ?></span>
                    <span class="val"><?php echo (int)$result['id']; ?></span>
                </div>
                <?php if (!empty($_apt_age)): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('age'); ?></span>
                    <span class="val"><?php echo html_escape($_apt_age); ?><?php if (!empty($result['gender'])): ?>, <?php echo html_escape($this->lang->line(strtolower($result['gender']))); endif; ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($result['mobileno'])): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('phone'); ?></span>
                    <span class="val"><?php echo html_escape($result['mobileno']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($result['email'])): ?>
                <div class="item">
                    <span class="lbl"><?php echo $this->lang->line('email'); ?></span>
                    <span class="val"><?php echo html_escape($result['email']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barcode / QR -->
        <?php if (file_exists($_apt_barcode) || file_exists($_apt_qrcode)): ?>
        <div class="d-flex gap-3 align-items-center flex-shrink-0">
            <?php if (file_exists($_apt_barcode)): ?>
            <a href="<?php echo $this->media_storage->getImageURL($_apt_barcode); ?>" target="_blank" rel="noopener">
                <img class="sh-qr-code" src="<?php echo $this->media_storage->getImageURL($_apt_barcode); ?>" width="100" height="40" alt="barcode">
            </a>
            <?php endif; ?>
            <?php if (file_exists($_apt_qrcode)): ?>
            <a href="<?php echo $this->media_storage->getImageURL($_apt_qrcode); ?>" target="_blank" rel="noopener" class="sh-welcome-qr-link">
                <img class="sh-qr-code sh-welcome-qr" src="<?php echo $this->media_storage->getImageURL($_apt_qrcode); ?>" width="50" height="50" alt="qr">
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- Appointments table card -->
    <div class="card sh-card-token">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0"><?php echo $this->lang->line('my_appointments'); ?></h3>
            <a href="#" onclick="getRecord(<?php echo json_encode((int)$result['id']); ?>, <?php echo json_encode((int)$result['is_active']); ?>)"
               class="btn btn-sm btn-primary"
               data-bs-target="#myModal" data-bs-toggle="modal">
                <i class="fa fa-plus me-1"></i><?php echo $this->lang->line('add_appointment'); ?>
            </a>
        </div>
        <div class="card-body p-3">
            <?php if ($this->session->flashdata('msg')) echo $this->session->flashdata('msg'); ?>

            <div class="download_label"><?php echo $this->lang->line('my_appointments'); ?></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover example">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('appointment_no'); ?></th>
                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                            <th><?php echo $this->lang->line('priority'); ?></th>
                            <th><?php echo $this->lang->line('specialist'); ?></th>
                            <th><?php echo $this->lang->line('doctor'); ?></th>
                            <th><?php echo $this->lang->line('status'); ?></th>
                            <th><?php echo $this->lang->line('message'); ?></th>
                            <?php if (!empty($fields)) foreach ($fields as $fv): ?>
                            <th><?php echo html_escape($fv->name); ?></th>
                            <?php endforeach; ?>
                            <th class="text-end noExport"><?php echo $this->lang->line('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resultlist)):
                            foreach ($resultlist as $appointment):
                                if ($appointment['appointment_status'] == 'approved') {
                                    $badge   = 'bg-success';
                                    $app_no  = $this->customlib->getPatientSessionPrefixByType('appointment') . $appointment['id'];
                                } elseif ($appointment['appointment_status'] == 'pending') {
                                    $badge   = 'bg-warning text-dark';
                                    $app_no  = $this->lang->line($appointment['appointment_status']);
                                } else {
                                    $badge   = 'bg-danger';
                                    $app_no  = $this->lang->line($appointment['appointment_status']);
                                }
                        ?>
                        <tr>
                            <td><?php echo $app_no; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($appointment['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php echo html_escape($appointment['priorityname']); ?></td>
                            <td>
                                <?php if ($appointment['specialist']) foreach ($appointment['staff_specialist_name'] as $k => $v):
                                    if (!empty($v)) echo html_escape($v['specialist_name']) . (count($appointment['staff_specialist_name']) != $k + 1 ? ', ' : '');
                                endforeach; ?>
                            </td>
                            <td><?php echo html_escape($appointment['name']) . ' ' . html_escape($appointment['surname']) . ' (' . html_escape($appointment['employee_id']) . ')'; ?></td>
                            <td><span class="badge <?php echo $badge; ?>"><?php echo $this->lang->line($appointment['appointment_status']); ?></span></td>
                            <td><?php echo html_escape($appointment['message']); ?></td>
                            <?php if (!empty($fields)) foreach ($fields as $fv):
                                $dv = $fv->type == 'link'
                                    ? '<a href="' . html_escape($appointment[$fv->name]) . '" target="_blank">' . html_escape($appointment[$fv->name]) . '</a>'
                                    : html_escape($appointment[$fv->name]); ?>
                            <td><?php echo $dv; ?></td>
                            <?php endforeach; ?>
                            <td class="text-end text-nowrap">
                                <?php if ($appointment['appointment_status'] == 'pending' && $payment_method && $appointment['source'] == 'Online'): ?>
                                <a href="<?php echo base_url(); ?>patient/onlineappointment/checkout/index/<?php echo (int)$appointment['id']; ?>"
                                   class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="<?php echo $this->lang->line('pay'); ?>">
                                    <?php echo $this->lang->line('pay'); ?>
                                </a>
                                <?php elseif ($appointment['appointment_status'] != 'pending'): ?>
                                <a href="javascript:void(0)"
                                   data-record-id="<?php echo (int)$appointment['id']; ?>"
                                   class="btn btn-sm btn-outline-secondary print_appointment_bill"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('print_bill'); ?>">
                                    <i class="fa fa-print"></i>
                                </a>
                                <?php endif; ?>
                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>"
                                   onclick="viewDetail(<?php echo (int)$appointment['id']; ?>)">
                                    <i class="fa fa-reorder"></i>
                                </a>
                                <?php if ($appointment['appointment_status'] == 'pending'): ?>
                                <a href="#" class="btn btn-sm btn-outline-danger"
                                   data-bs-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"
                                   onclick="delete_recordByIdReload_byPatient('deleteappointment/<?php echo (int)$appointment['id']; ?>')">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<!-- ══════════════ Add Appointment Modal ══════════════ -->
<div class="modal fade sh-modal sh-modal-branded" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('add_appointment'); ?></h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post">
                <input type="hidden" name="patient_id" id="patient_ids" value="<?php echo (int)$result['id']; ?>">
                <input type="hidden" name="patient_name" id="patient_names" value="<?php echo html_escape($result['patient_name']); ?>">
                <select id="gender" name="gender" class="d-none">
                    <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                    <option value="<?php echo html_escape($result['gender']); ?>"></option>
                </select>
                <input type="hidden" name="email" id="emails" value="<?php echo html_escape($result['email']); ?>">
                <input type="hidden" name="mobileno" id="phones" value="<?php echo html_escape($result['mobileno']); ?>">
                <input type="hidden" name="appointment_status" value="pending">
                <input type="hidden" id="slot_id" name="slot">
                <div class="pup-scroll-area">
                    <div class="modal-body">
                        <div class="row allotment_error"></div>
                        <div class="sh-form-card">
                            <div class="sh-card-header">
                                <span class="sh-card-header-title"><?php echo $this->lang->line('appointment_details'); ?></span>
                            </div>
                            <div class="p-3">
                                <div class="row g-3">
                                    <div class="col-sm-4">
                                        <label class="form-label"><?php echo $this->lang->line('date'); ?> <small class="req">*</small></label>
                                        <input type="text" id="dates" name="date" class="form-control date" value="<?php echo set_value('dates'); ?>">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label"><?php echo $this->lang->line('specialist'); ?> <small class="req">*</small></label>
                                        <select class="form-select" name="specialist" id="specialist" onchange="getdoctor(this.value)">
                                            <option value="<?php echo set_value('specialist'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($specialist as $sv): ?>
                                            <option value="<?php echo $sv['id']; ?>"><?php echo $sv['specialist_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label"><?php echo $this->lang->line('doctor'); ?> <small class="req">*</small></label>
                                        <select class="form-select" name="doctor" id="doctor" onchange="reset_all(),getDoctorShift()">
                                            <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?php echo $this->lang->line('shift'); ?> <small class="req">*</small></label>
                                        <select name="global_shift" onchange="getShift();" id="global_shift" class="form-select">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($global_shift as $gv): ?>
                                            <option value="<?php echo $gv['id']; ?>"><?php echo $gv['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?php echo $this->lang->line('slot'); ?> <small class="req">*</small></label>
                                        <select name="shift" onchange="getSlotByShift();validateTime(this)" id="shift_id" class="form-select">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label"><?php echo $this->lang->line('appointment_priority'); ?></label>
                                        <select class="form-select appointment_priority_select2" name="priority">
                                            <?php foreach ($appoint_priority_list as $pv): ?>
                                            <option value="<?php echo $pv['id']; ?>"><?php echo $pv['appoint_priority']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if ($this->module_lib->hasActive('live_consultation')): ?>
                                    <div class="col-sm-4">
                                        <label class="form-label"><?php echo $this->lang->line('live_consultation_on_video_conference'); ?> <small class="req">*</small></label>
                                        <select name="live_consult" id="live_consult" class="form-select">
                                            <?php foreach ($yesno_condition as $yk => $yv): ?>
                                            <option value="<?php echo $yk; ?>" <?php echo $yk == 'no' ? 'selected' : ''; ?>><?php echo $yv; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <label class="form-label"><?php echo $this->lang->line('message'); ?> <small class="req">*</small></label>
                                        <textarea name="message" id="message" class="form-control"><?php echo set_value('message'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('message'); ?></span>
                                    </div>
                                    <div><?php echo display_custom_fields_patient('appointment'); ?></div>
                                    <div class="col-md-12">
                                        <div class="form-group"><span id="slots_label"></span></div>
                                    </div>
                                    <div class="col-md-12"><div id="slot" class="sh-slot-grid"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" id="formaddbtn" class="btn btn-info">
                        <span class="normal-text"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('save'); ?></span>
                        <span class="loading-text d-none" ><i class="fa fa-spinner fa-spin me-1"></i><?php echo $this->lang->line('please_wait'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════ View Appointment Modal ══════════════ -->
<div class="modal fade sh-modal sh-modal-accent" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel"><?php echo $this->lang->line('appointment_details'); ?></h5>
                <div id="edit_delete" class="d-flex align-items-center gap-2 ms-auto me-2"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body">
                    <div class="sh-form-card">
                        <div class="sh-card-header">
                            <span class="sh-card-header-title"><?php echo $this->lang->line('appointment_details'); ?></span>
                        </div>
                        <div class="sh-info-grid">
                            <div class="row g-0">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('patient_name'); ?></span>
                                    <span class="sh-info-value highlight"><span id="patient_name_view"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_no'); ?></span>
                                    <span class="sh-info-value"><span id="appointmentno"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('age'); ?></span>
                                    <span class="sh-info-value"><span id="patient_age"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('gender'); ?></span>
                                    <span class="sh-info-value"><span id="genders"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('blood_group'); ?></span>
                                    <span class="sh-info-value"><span id="blood_group"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_date'); ?></span>
                                    <span class="sh-info-value"><span id="dating"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_s_no'); ?></span>
                                    <span class="sh-info-value"><span id="appointmentsno"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('appointment_priority'); ?></span>
                                    <span class="sh-info-value"><span id="appointpriority"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('phone'); ?></span>
                                    <span class="sh-info-value"><span id="phones_view"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('email'); ?></span>
                                    <span class="sh-info-value"><span id="emails_view"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('shift'); ?></span>
                                    <span class="sh-info-value"><span id="global_shift_view"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('slot'); ?></span>
                                    <span class="sh-info-value text-capitalize"><span id="doctor_shift_view"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('doctor'); ?></span>
                                    <span class="sh-info-value"><span id="doctors"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('department'); ?></span>
                                    <span class="sh-info-value"><span id="department_name"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('amount'); ?></span>
                                    <span class="sh-info-value highlight"><span id="pay_amount"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('payment_mode'); ?></span>
                                    <span class="sh-info-value"><span id="payment_mode"></span></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('status'); ?></span>
                                    <span class="sh-info-value"><span id="status"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('source'); ?></span>
                                    <span class="sh-info-value"><span id="source"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('payment_note'); ?></span>
                                    <span class="sh-info-value"><span id="payment_note"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('transaction_id'); ?></span>
                                    <span class="sh-info-value"><span id="trans_id"></span></span>
                                </div>
                            </div>
                            <?php if ($this->module_lib->hasActive('live_consultation')): ?>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('live_consultation'); ?></span>
                                    <span class="sh-info-value"><span id="liveconsult"></span></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="row g-0 sh-row-divider" id="cheque_section">
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('cheque_no'); ?></span>
                                    <span class="sh-info-value"><span id="spn_chequeno"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('cheque_date'); ?></span>
                                    <span class="sh-info-value"><span id="spn_chequedate"></span></span>
                                </div>
                                <div class="col-6 col-md-3 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('document'); ?></span>
                                    <span class="sh-info-value" id="spn_doc"></span>
                                </div>
                            </div>
                            <div class="row g-0 sh-row-divider">
                                <div class="col-12 sh-info-item">
                                    <span class="sh-info-label"><?php echo $this->lang->line('message'); ?></span>
                                    <span class="sh-info-value"><span id="messages"></span></span>
                                </div>
                            </div>
                            <!-- Custom Fields -->
                            <div class="row g-0 sh-row-divider" id="field_data"></div>
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

<script type="text/javascript">
    function getRecord(id, active) {
        $("#formaddbtn .normal-text").removeClass('d-none');
        $("#formaddbtn .loading-text").addClass('d-none');
        $("#formaddbtn").prop("disabled", false);
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getDetails',
            type: "POST",
            data: {patient_id: id, active: active},
            dataType: 'json',
            success: function (data) {
                $("#patient_ids").val(id);
                $("#patient_names").val(data.patient_name);
                $("#emails").val(data.email);
                $("#phones").val(data.mobileno);
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>patient/dashboard/addappointment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    $('.allotment_error').empty();
                    $("#formaddbtn .normal-text").addClass('d-none');
                    $("#formaddbtn .loading-text").removeClass('d-none');
                    $("#formaddbtn").prop("disabled", true);
                },
                success: function (data) {
                    if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (i, v) { message += v; });
                        errorMsg(message);
                    } else if (data.status == 2) {
                        $('.allotment_error').append($('<div/>').addClass("alert alert-info").text(data.msg));
                    } else if (data.status == 1) {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn .normal-text").removeClass('d-none');
                    $("#formaddbtn .loading-text").addClass('d-none');
                    $("#formaddbtn").prop("disabled", false);
                },
                error: function () {
                    $("#formaddbtn .normal-text").removeClass('d-none');
                    $("#formaddbtn .loading-text").addClass('d-none');
                    $("#formaddbtn").prop("disabled", false);
                },
                complete: function () {
                    $("#formaddbtn .normal-text").removeClass('d-none');
                    $("#formaddbtn .loading-text").addClass('d-none');
                    $("#formaddbtn").prop("disabled", false);
                }
            });
        });
    });

    function delete_recordByIdReload_byPatient(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function (res) {
                    successMsg(Msg);
                    window.location.reload(true);
                }
            });
        }
    }

    function escHtml(str) { var d = document.createElement('div'); d.appendChild(document.createTextNode(str || '')); return d.innerHTML; }

    function getdoctor(id, doc) {
        doc = doc || '';
        var div_data = "";
        $('#doctor').html("<option value='l'><?php echo $this->lang->line('loading'); ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getdoctor',
            type: "POST",
            data: {id: id, active: 'yes'},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) {
                    var sel = (doc !== '' && doc == obj.id) ? "selected" : "";
                    div_data += "<option value='" + parseInt(obj.id) + "' " + sel + ">" + escHtml(obj.name) + " " + escHtml(obj.surname) + " (" + escHtml(obj.employee_id) + ")</option>";
                });
                $("#doctor").html("<option value=''><?= $this->lang->line('select') ?></option>");
                $('#doctor').append(div_data);
            }
        });
    }

    function reset_all() { $("#slot").html(""); }

    function getDoctorShift(prev_val) {
        prev_val = prev_val || 0;
        var doctor_id = $("#doctor").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url('site/doctorshiftbyid'); ?>",
            data: {doctor_id: doctor_id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, list) {
                    var selected = list.id == prev_val ? "selected" : "";
                    select_box += "<option value='" + parseInt(list.id) + "' " + selected + ">" + escHtml(list.name) + "</option>";
                });
                $("#global_shift").html(select_box);
            }
        });
    }

    function getShift() {
        var date = $("#dates").val();
        var doctor = $("#doctor").val();
        var global_shift = $("#global_shift").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>site/getShift',
            type: "POST",
            data: {doctor: doctor, date: date, global_shift: global_shift},
            dataType: 'json',
            success: function (res) {
                if (res.length) {
                    $.each(res, function (i, list) {
                        select_box += "<option value='" + parseInt(list.id) + "'>" + escHtml(list.start_time) + " - " + escHtml(list.end_time) + "</option>";
                    });
                } else {
                    $("#slot").html("");
                }
                $("#shift_id").html(select_box);
            }
        });
    }

    function getSlotByShift() {
        var shift = $("#shift_id").val();
        var div_data = "";
        var date = $("#dates").val();
        var doctor = $("#doctor").val();
        var global_shift = $("#global_shift").val();
        if (shift !== '') {
            $.ajax({
                url: '<?php echo base_url(); ?>site/getSlotByShift',
                type: "POST",
                data: {shift: shift, doctor: doctor, date: date, global_shift: global_shift},
                dataType: 'json',
                success: function (res) {

                    $.each(res.result, function (i, obj) {
                        // Use 'slot' as base class and 'slot-selected' only for initial selection
                        var slotClass = 'slot ' + escHtml(obj.class); 
                        div_data += "<span id='slot_" + parseInt(i) + "' onclick='setSlot(" + parseInt(i) + ")' class='sh-slot-cursor " + slotClass + "' data-filled='" + escHtml(obj.filled) + "'>" + escHtml(obj.time) + "</span>";
                    });
                    $("#slot").html("");
                    $("#slots_label").html("<label><b><?php echo $this->lang->line('available_slots'); ?></b><small class='req'> *</small></label>");
                    if (div_data === "") {
                        div_data = '<div class="alert alert-danger" role="alert"><?php echo $this->lang->line('no_slot_available'); ?></div>';
                    }
                    $('#slot').html(div_data);
                }
            });
        } else {
            $('#slot').html("");
        }
    }

    function setSlot_old(id) {
        if ($("#slot_" + id).data("filled") === "filled") {
            alert("<?php echo $this->lang->line('not_available'); ?>");
        } else {
            $("#slot_id").val(id);
            $(".bg-primary").addClass("badge-success-soft");
            $(".bg-primary").removeClass(".bg-primary");
            $("#slot_" + id).removeClass("badge-success-soft");
            $("#slot_" + id).addClass("bg-primary");
        }
    }

    function setSlot(id) {
    var slot = $("#slot_" + id);
    
    if (slot.data("filled") === "filled") {
        alert("<?php echo $this->lang->line('not_available'); ?>");
        return;
    }

    $("#slot_id").val(id);

    // Remove previous selection
    $(".slot-selected").removeClass("slot-selected");

    // Highlight current slot
    slot.addClass("slot-selected");
}

    function viewDetail(id) {
        var modalEl = document.getElementById('viewModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop: 'static'});
        modal.show();
        $("#appointmentno").html(" ");
        $("#appointmentsno").html(" ");
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getDetailsAppointment',
            type: "POST",
            data: {appointment_id: id},
            dataType: 'json',
            success: function (data) {
                var table_html = '';
                $.each(data.field_data, function (i, obj) {
                    var field_value = obj.field_value != null ? obj.field_value : "";
                    if (obj.visible_on_patient_panel == 1) {
                        table_html += '<div class="col-6 col-md-3 sh-info-item"><span class="sh-info-label">' + escHtml(capitalizeFirstLetter(obj.name)) + '</span><span class="sh-info-value">' + escHtml(field_value) + '</span></div>';
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
                $("#patient_name_view").text(data.patients_name);
                $("#genders").text(data.patients_gender);
                $("#emails_view").text(data.patient_email);
                $("#appointpriority").text(data.appoint_priority);
                $("#phones_view").text(data.patient_mobileno);
                $("#doctors").text(data.name + " " + data.surname + " (" + data.employee_id + ")");
                $("#messages").text(data.message);
                $("#liveconsult").html(data.edit_live_consult);
                $("#global_shift_view").text(data.global_shift_name);
                $("#doctor_shift_view").text(data.doctor_shift_name);
                $("#source").text(data.source);
                $("#trans_id").text(data.transaction_id);
                $("#payment_note").text(data.payment_note);
                $("#patient_age").text(data.patient_age);
                $("#department_name").text(data.department_name);
                $("#blood_group").text(data.blood_group);

                $("#pay_amount").html('<?php echo $currency_symbol; ?>' + (data.paid_amount != null ? data.paid_amount : '0.00'));
                $("#payment_mode").html(data.payment_mode);

                if (data.payment_mode == "Cheque") {
                    $("#cheque_section").removeClass('d-none');
                    $("#spn_chequeno").text(data.cheque_no);
                    $("#spn_chequedate").text(data.cheque_date);
                    $("#spn_doc").html(data.doc);
                } else {
                    $("#cheque_section").addClass('d-none');
                    $("#spn_chequeno").html("");
                    $("#spn_chequedate").html("");
                }

                $("#edit_delete").html("<a href='#' class='btn btn-sm btn-light' data-bs-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>' onclick='printAppointment(" + parseInt(id) + ")'><i class='fa fa-print'></i></a>");

                var badgeCls = "";
                if (data.appointment_status == "Approved")      badgeCls = "bg-success";
                else if (data.appointment_status == "Pending")  badgeCls = "bg-warning text-dark";
                else if (data.appointment_status == "Cancel")   badgeCls = "bg-danger";

                if (data.appointment_status == "Cancel") { $("#trans_id").html(""); }

                $("#status").html("<span class='badge " + badgeCls + "'>" + escHtml(data.appointment_status) + "</span>");
            }
        });
    }

    function validateTime(obj) {
        var id = obj.value;
        var date = $("#dates").val();
        if (id) {
            $.ajax({
                url: baseurl + 'welcome/getshiftbyid',
                type: "POST",
                data: {id: id, date: date},
                dataType: 'json',
                success: function (res) {
                    if (res.end_time && res.date) {
                        var t = res.end_time.split(':');
                        var d = res.date.split('-');
                        var endDate = new Date(parseInt(d[0]), parseInt(d[1]) - 1, parseInt(d[2]), parseInt(t[0]), parseInt(t[1]), parseInt(t[2] || 0));
                        if (new Date() > endDate) {
                            alert("<?php echo $this->lang->line('appointment_time_is_expired'); ?>");
                        }
                    }
                }
            });
        }
    }

    $(document).on('click', '.print_appointment_bill', function () {
        var id = $(this).data('recordId');
        var $this = $(this);
        $this.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: base_url + 'patient/dashboard/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
            success: function (data) { popup(data.page); },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); },
            complete: function () { $this.prop("disabled", false).html('<i class="fa fa-print"></i>'); }
        });
    });

    function printAppointment(id) {
        $.ajax({
            url: base_url + 'patient/dashboard/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
            success: function (data) { popup(data.page); },
            error: function () { alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>"); }
        });
    }

    $(document).ready(function () {
        modal_click_disabled('myModal');
    });
</script>

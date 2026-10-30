<?php
$appt      = $static_pages['appointment'] ?? array();
$heading   = html_escape($appt['heading']    ?? $this->lang->line('appointment'));
$subhead   = html_escape($appt['subheading'] ?? '');
$note      = html_escape($appt['note']       ?? '');
$hs        = $home_sections ?? array();
$hero      = $hs['hero'] ?? array();
$emergency = !empty($hero['emergency_number']) ? $hero['emergency_number']
           : (!empty($school_setting->phone)   ? $school_setting->phone : '');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/tempus-dominus/css/tempus-dominus.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/tempus-dominus/js/popper.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/tempus-dominus/js/tempus-dominus.min.js"></script>

<!-- Page hero -->
<section class="page-hero compact">
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="csep">·</span>
      <strong><?php echo $this->lang->line('book_appointment'); ?></strong>
    </nav>
    <h1><?php echo $heading; ?></h1>
    <?php if($subhead): ?><p class="lede"><?php echo $subhead; ?></p><?php endif; ?>
  </div>
</section>

<!-- Booking form + aside -->
<section class="block" id="booking">
  <div class="container">

    <?php if(validation_errors()): ?>
    <div class="appt-warn-banner">
      <?php echo validation_errors(); ?>
    </div>
    <?php endif; ?>

    <div class="appt-grid">

      <!-- ── Form ── -->
      <form id="appointment_form" action="<?php echo site_url('form/appointment'); ?>" method="post" class="appt-form">

        <h2><?php echo $heading; ?></h2>
        <?php if($subhead): ?><p class="ah-sub"><?php echo $subhead; ?></p><?php endif; ?>

        <?php
          $custom_fields = display_custom_fields_patient('appointment');
          $has_custom    = !empty(trim(strip_tags($custom_fields)));
          $step_n        = 1;
        ?>

        <!-- Specialty & doctor -->
        <div class="appt-step appt-step-spaced"><span class="n"><?php echo $step_n++; ?></span><?php echo $this->lang->line('specialty_and_doctor') ?: 'Specialty &amp; doctor'; ?></div>

        <div class="appt-row">
          <label>
            <span><?php echo $this->lang->line('specialist'); ?> <span class="req">*</span></span>
            <select name="specialist" id="specialist" onchange="getdoctor(this.value)">
              <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
              <?php foreach($specialist as $sv): ?>
              <option value="<?php echo (int)$sv['id']; ?>" <?php if(set_value('specialist', $prefill_specialist ?? '') == $sv['id']) echo 'selected'; ?>>
                <?php echo html_escape($sv['specialist_name']); ?>
              </option>
              <?php endforeach; ?>
            </select>
            <?php if(!empty($prefill_specialist)): ?>
            <script>document.addEventListener('DOMContentLoaded', function(){ var el = document.getElementById('specialist'); if (el && el.value && typeof getdoctor === 'function') { getdoctor(el.value); } });</script>
            <?php endif; ?>
            <?php if(form_error('specialist')): ?><span class="appt-field-error"><?php echo form_error('specialist'); ?></span><?php endif; ?>
          </label>
          <label>
            <span><?php echo $this->lang->line('doctor'); ?> <span class="req">*</span></span>
            <select name="doctor" onchange="getDoctorShift()" id="doctor">
              <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
            </select>
            <?php if(form_error('doctor')): ?><span class="appt-field-error"><?php echo form_error('doctor'); ?></span><?php endif; ?>
          </label>
        </div>

        <!-- Pick a slot -->
        <div class="appt-step appt-step-spaced"><span class="n"><?php echo $step_n++; ?></span><?php echo $this->lang->line('pick_a_slot') ?: 'Pick a slot'; ?></div>

        <div class="appt-row">
          <label>
            <span><?php echo $this->lang->line('shift'); ?> <span class="req">*</span></span>
            <select name="global_shift" onchange="getShift();" id="global_shift">
              <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
              <?php if(!empty($global_shift)): foreach($global_shift as $gs): ?>
              <option value="<?php echo (int)$gs['id']; ?>" <?php if(set_value('global_shift') == $gs['id']) echo 'selected'; ?>>
                <?php echo html_escape($gs['name']); ?>
              </option>
              <?php endforeach; endif; ?>
            </select>
            <?php if(form_error('global_shift')): ?><span class="appt-field-error"><?php echo form_error('global_shift'); ?></span><?php endif; ?>
          </label>
          <label>
            <span><?php echo $this->lang->line('date'); ?> <span class="req">*</span></span>
            <div id="datetimepicker1">
              <input type="text" id="datepicker_input" autocomplete="off" readonly
                     name="date" value="<?php echo set_value('date'); ?>"
                     placeholder="<?php echo $this->lang->line('select_date'); ?>"/>
            </div>
            <?php if(form_error('date')): ?><span class="appt-field-error"><?php echo form_error('date'); ?></span><?php endif; ?>
          </label>
        </div>

        <input type="hidden" id="shift_id" name="shift" />
        <div id="shift"></div>

        <input type="hidden" id="slot_id" name="slot" />
        <div id="slot"></div>
        <?php if(form_error('slot')): ?><span class="appt-error-inline"><?php echo form_error('slot'); ?></span><?php endif; ?>

        <!-- Notes -->
        <div class="appt-step appt-step-spaced"><span class="n"><?php echo $step_n++; ?></span><?php echo $this->lang->line('anything_to_note') ?: 'Anything we should know?'; ?></div>

        <label>
          <span><?php echo $this->lang->line('message'); ?> <span class="req">*</span></span>
          <textarea name="message" rows="3" placeholder="<?php echo $this->lang->line('concern_placeholder') ?: 'e.g. recurring chest pain since last month; previous surgery in 2020'; ?>"><?php echo set_value('message'); ?></textarea>
          <?php if(form_error('message')): ?><span class="appt-field-error"><?php echo form_error('message'); ?></span><?php endif; ?>
        </label>

        <?php if($has_custom): ?>
        <div class="appt-step appt-step-spaced"><span class="n"><?php echo $step_n++; ?></span><?php echo $this->lang->line('patient_details') ?: 'Patient details'; ?></div>
        <div><?php echo $custom_fields; ?></div>
        <?php endif; ?>

        <button type="button" id="appt-book-btn" class="submit">
          <?php echo $this->lang->line('book_appointment'); ?>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>
        <?php if($note): ?><p class="appt-note-text"><?php echo $note; ?></p><?php endif; ?>

      </form>

      <!-- ── Aside ── -->
      <aside class="appt-aside">

        <!-- Your selection -->
        <div class="aside-card">
          <h5><?php echo $this->lang->line('your_selection') ?: 'Your selection'; ?></h5>
          <ul>
            <li>
              <span><?php echo $this->lang->line('hospital') ?: 'Hospital'; ?></span>
              <strong><?php echo html_escape($school_setting->name ?? ''); ?></strong>
            </li>
            <li id="aside-doctor-row" class="d-none">
              <span><?php echo $this->lang->line('doctor') ?: 'Doctor'; ?></span>
              <strong id="aside-doctor-name">—</strong>
            </li>
            <li id="aside-slot-row" class="d-none">
              <span><?php echo $this->lang->line('slot') ?: 'Slot'; ?></span>
              <strong id="aside-slot-time">—</strong>
            </li>
            <li id="aside-fees-row" class="d-none">
              <span><?php echo $this->lang->line('fees') ?: 'Est. fee'; ?></span>
              <strong id="aside-fees">—</strong>
            </li>
          </ul>
        </div>

        <!-- Need it now -->
        <?php if(!empty($emergency)): ?>
        <div class="aside-card appt-aside-dark">
          <h5 class="appt-aside-heading"><?php echo $this->lang->line('need_it_now') ?: 'Need it now?'; ?></h5>
          <p class="appt-aside-para">
            <?php echo $this->lang->line('walk_in_available') ?: 'Walk-in OPD is open during working hours. For ambulance and trauma, dial the emergency line below.'; ?>
          </p>
          <a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $emergency)); ?>"
             class="appt-phone-link">
            <?php echo html_escape($emergency); ?>
          </a>
        </div>
        <?php endif; ?>

      </aside>

    </div><!-- /.appt-grid -->
  </div>
</section>

<?php $patient_logged_in = !empty($this->session->userdata('patient')); ?>

<!-- ── Patient booking modal ───────────────────────────────── -->
<div class="appt-modal-overlay" id="apptModalOverlay" role="dialog" aria-modal="true" aria-labelledby="apptModalTitle">
  <div class="appt-modal">

    <div class="appt-modal-hdr">
      <h3 id="apptModalTitle"><?php echo $this->lang->line('complete_your_booking') ?: 'Complete your booking'; ?></h3>
      <button type="button" class="appt-modal-close" id="apptModalClose" aria-label="<?php echo $this->lang->line('close'); ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="appt-modal-body">

      <!-- Left: booking summary -->
      <div class="appt-modal-summary">
        <h5><?php echo $this->lang->line('your_selection') ?: 'Your selection'; ?></h5>
        <ul>
          <li>
            <span><?php echo $this->lang->line('hospital') ?: 'Hospital'; ?></span>
            <strong><?php echo html_escape($school_setting->name ?? ''); ?></strong>
          </li>
          <li>
            <span><?php echo $this->lang->line('doctor') ?: 'Doctor'; ?></span>
            <strong id="modal-doctor">—</strong>
          </li>
          <li>
            <span><?php echo $this->lang->line('date') ?: 'Date'; ?></span>
            <strong id="modal-date">—</strong>
          </li>
          <li>
            <span><?php echo $this->lang->line('slot') ?: 'Slot'; ?></span>
            <strong id="modal-slot">—</strong>
          </li>
          <li id="modal-fees-row" class="d-none">
            <span><?php echo $this->lang->line('fees') ?: 'Est. fee'; ?></span>
            <strong id="modal-fees">—</strong>
          </li>
        </ul>
      </div>

      <!-- Right: patient login / register (hidden if already logged in) -->
      <?php if(!$patient_logged_in): ?>
      <div class="appt-modal-auth">

        <!-- Hidden radios — controlled by JS toggle -->
        <input type="radio" form="appointment_form" name="patient_type" id="pt-new" value="new patient" checked class="appt-hidden-radio">
        <input type="radio" form="appointment_form" name="patient_type" id="pt-old" value="old patient" class="appt-hidden-radio">

        <div class="pt-toggle">
          <button type="button" class="pt-btn active" id="pt-new-btn" onclick="switchPatientType('new')">
            <?php echo $this->lang->line('new_patient') ?: 'New Patient'; ?>
          </button>
          <button type="button" class="pt-btn" id="pt-old-btn" onclick="switchPatientType('old')">
            <?php echo $this->lang->line('old_patient') ?: 'Existing Patient'; ?>
          </button>
        </div>

        <!-- NEW PATIENT form -->
        <div id="new-patient-form">
          <div class="appt-modal-row">
            <label>
              <span><?php echo $this->lang->line('patient_name') ?: 'Full name'; ?> <span class="req">*</span></span>
              <input type="text" form="appointment_form" name="patient_name" value="<?php echo set_value('patient_name'); ?>" placeholder="<?php echo $this->lang->line('enter_patient_name') ?: 'Full name'; ?>"/>
            </label>
            <label>
              <span><?php echo $this->lang->line('email'); ?> <span class="req">*</span></span>
              <input type="email" form="appointment_form" name="email" value="<?php echo set_value('email'); ?>" placeholder="email@example.com"/>
            </label>
          </div>
          <div class="appt-modal-row">
            <label>
              <span><?php echo $this->lang->line('gender'); ?> <span class="req">*</span></span>
              <select form="appointment_form" name="gender">
                <?php foreach($gender as $gk => $gv): ?>
                <option value="<?php echo $gk; ?>"><?php echo $gv; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              <span><?php echo $this->lang->line('phone'); ?> <span class="req">*</span></span>
              <input type="text" form="appointment_form" name="phone" value="<?php echo set_value('phone'); ?>" placeholder="<?php echo $this->lang->line('enter_phone') ?: 'Phone number'; ?>"/>
            </label>
          </div>
          <?php if($is_captcha): ?>
          <label>
            <span><?php echo $this->lang->line('captcha'); ?> <span class="req">*</span></span>
            <div class="captcha_image appt-captcha-spacer"><?php echo $captcha_image; ?></div>
            <input type="text" form="appointment_form" name="captcha_register" id="captcha_register" placeholder="<?php echo $this->lang->line('enter_captcha'); ?>"/>
          </label>
          <?php endif; ?>
        </div>

        <!-- OLD PATIENT (login) form -->
        <div id="old-patient-form" style="display:none">
          <input type="hidden" form="appointment_form" name="credential_status" id="credential_status" value="0">
          <label>
            <span><?php echo $this->lang->line('username'); ?> <span class="req">*</span></span>
            <input type="text" form="appointment_form" name="username" value="<?php echo set_value('username'); ?>" placeholder="<?php echo $this->lang->line('username'); ?>"/>
          </label>
          <label>
            <span><?php echo $this->lang->line('password'); ?> <span class="req">*</span></span>
            <input type="password" form="appointment_form" name="password" placeholder="<?php echo $this->lang->line('password'); ?>"/>
          </label>
          <?php if($is_captcha): ?>
          <label>
            <span><?php echo $this->lang->line('captcha'); ?> <span class="req">*</span></span>
            <div class="captcha_image appt-captcha-spacer"><?php echo $captcha_image; ?></div>
            <input type="text" form="appointment_form" name="captcha_login" id="captcha_login" placeholder="<?php echo $this->lang->line('enter_captcha'); ?>"/>
          </label>
          <?php endif; ?>
          <!-- 2FA verification panel -->
          <div id="verify-code-panel" style="display:none">
            <label>
              <span><?php echo $this->lang->line('verification_code') ?: 'Verification code'; ?> <span class="req">*</span></span>
              <input type="text" form="appointment_form" name="gauth_code" id="gauth_code" placeholder="6-digit code" autocomplete="off"/>
            </label>
          </div>
        </div>

      </div>
      <?php endif; ?>

    </div><!-- /.appt-modal-body -->

    <!-- Error display -->
    <div id="modal-errors" class="modal-errors" style="display:none;margin:12px 28px 0;"></div>

    <!-- Modal footer / submit -->
    <div class="appt-modal-foot">
      <button type="button" id="modal-submit-btn" class="appt-confirm appt-btn-full" onclick="submitAppointment()">
        <?php echo $this->lang->line('book_appointment'); ?>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
    </div>

  </div>
</div>

<!-- Three other ways -->
<section class="block soft">
  <div class="container">
    <header class="sec-head split">
      <div>
        <span class="kicker"><?php echo $this->lang->line('three_other_ways') ?: 'Three other ways'; ?></span>
        <h2><?php echo $this->lang->line('booking_however_day_works') ?: 'Booking, however <span class="grad">your day</span> works.'; ?></h2>
      </div>
    </header>
    <div class="quick-grid">
      <a class="qcard" href="#">
        <span class="qicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </span>
        <span class="qtitle"><?php echo $this->lang->line('walk_in_opd') ?: 'Walk-in OPD'; ?></span>
        <?php if($this->lang->line('walk_in_desc')): ?><span class="qsub"><?php echo $this->lang->line('walk_in_desc'); ?></span><?php endif; ?>
        <span class="qmore"><?php echo $this->lang->line('view_timings') ?: 'Working hours'; ?> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
      </a>
      <a class="qcard" href="<?php echo site_url('form/appointment'); ?>">
        <span class="qicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </span>
        <span class="qtitle"><?php echo $this->lang->line('online_appointment') ?: 'Online Appointment'; ?></span>
        <?php if($this->lang->line('online_appointment_desc')): ?><span class="qsub"><?php echo $this->lang->line('online_appointment_desc'); ?></span><?php endif; ?>
        <span class="qmore"><?php echo $this->lang->line('book_now') ?: 'Book now'; ?> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
      </a>
      <a class="qcard" href="<?php echo site_url('patient'); ?>">
        <span class="qicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="qtitle"><?php echo $this->lang->line('patient_portal') ?: 'Patient Portal'; ?></span>
        <?php if($this->lang->line('patient_portal_desc')): ?><span class="qsub"><?php echo $this->lang->line('patient_portal_desc'); ?></span><?php endif; ?>
        <span class="qmore"><?php echo $this->lang->line('login') ?: 'Login'; ?> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
      </a>
      <?php if(!empty($emergency)): ?>
      <a class="qcard featured" href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $emergency)); ?>">
        <span class="qicon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.12 13.1a19.86 19.86 0 0 1-1-8.93A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
        </span>
        <span class="qtitle"><?php echo $this->lang->line('emergency') ?: 'Emergency'; ?></span>
        <span class="qmore"><?php echo html_escape($emergency); ?></span>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<link rel="stylesheet" href="<?php echo base_url(); ?>backend/toast-alert/toastr.css">
<script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>
<script src="<?php echo base_url(); ?>backend/js/online-appointment.js"></script>
<script>
var global_date_format = "<?php echo $this->customlib->getJSDateFormat(); ?>";
var base_url           = "<?php echo base_url(); ?>";
var lang_select        = "— <?php echo $this->lang->line('select'); ?> —";
var lang_loading       = "<?php echo $this->lang->line('loading') ?: 'Loading…'; ?>";
var lang_no_slot       = "<?php echo $this->lang->line('no_slot_available'); ?>";

(function() {
  var el = document.getElementById('datepicker_input');
  if (!el || el._pickerInit) return;
  var minDate = new tempusDominus.DateTime();
  minDate.setHours(0, 0, 0, 0);
  el._pickerInit = new tempusDominus.TempusDominus(el, {
    allowInputToggle: true,
    container: document.body,
    display: {
      components: { calendar: true, date: true, month: true, year: true, decades: true, clock: false, hours: false, minutes: false, seconds: false },
      buttons: { today: true, clear: true, close: true },
      theme: 'light'
    },
    localization: { format: global_date_format, locale: 'en-US' },
    restrictions: { minDate: minDate }
  });
  el.addEventListener(tempusDominus.Namespace.events.change, function() { getShift(); });
})();

// ── Book button: validate then open modal (or submit directly if logged in) ──
document.getElementById('appt-book-btn').addEventListener('click', function() {
  var doctor  = document.getElementById('doctor').value;
  var date    = document.getElementById('datepicker_input').value;
  var shift   = document.getElementById('global_shift').value;
  var slot    = document.getElementById('slot_id').value;
  if (!doctor || !date || !shift || slot === '') { return; }
  <?php if($patient_logged_in): ?>
  submitAppointment();
  <?php else: ?>
  openApptModal();
  <?php endif; ?>
});

// ── Modal open / close ───────────────────────────────────────
function openApptModal() {
  var doctorSel  = document.getElementById('doctor');
  var dateVal    = document.getElementById('datepicker_input').value;
  var slotLabel  = document.getElementById('aside-slot-time');
  var feesLabel  = document.getElementById('aside-fees');
  var feesRow    = document.getElementById('aside-fees-row');

  if (doctorSel && doctorSel.selectedIndex > 0)
    document.getElementById('modal-doctor').textContent = doctorSel.options[doctorSel.selectedIndex].text;
  if (dateVal)
    document.getElementById('modal-date').textContent = dateVal;
  var st = slotLabel ? slotLabel.textContent.trim() : '—';
  document.getElementById('modal-slot').textContent = st && st !== '—' ? st : '—';
  if (feesLabel && feesRow && feesRow.style.display !== 'none') {
    document.getElementById('modal-fees').textContent = feesLabel.textContent;
    document.getElementById('modal-fees-row').style.display = '';
  }

  document.getElementById('apptModalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeApptModal() {
  document.getElementById('apptModalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

// ── New / old patient toggle ─────────────────────────────────
function switchPatientType(type) {
  var isNew = type === 'new';
  document.getElementById('new-patient-form').style.display = isNew ? '' : 'none';
  document.getElementById('old-patient-form').style.display = isNew ? 'none' : '';
  document.getElementById('pt-new-btn').classList.toggle('active', isNew);
  document.getElementById('pt-old-btn').classList.toggle('active', !isNew);
  var r = document.getElementById(isNew ? 'pt-new' : 'pt-old');
  if (r) r.checked = true;
  clearModalErrors();
}

// ── Submit ────────────────────────────────────────────────────
function submitAppointment() {
  var btn      = document.getElementById('modal-submit-btn') || document.getElementById('appt-book-btn');
  var origHtml = btn ? btn.innerHTML : '';
  if (btn) { btn.disabled = true; btn.innerHTML = '<span style="opacity:.6">…</span>'; }

  var form = document.getElementById('appointment_form');
  fetch(form.action, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: new FormData(form)
  })
  .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
  .then(function(data) {
    if (btn) { btn.disabled = false; btn.innerHTML = origHtml; }

    if (data.status == 1) {
      window.location.replace(base_url + 'patient/dashboard/appointment');
      return;
    }

    if (data.status == 2) {
      var ci = document.getElementById('credential_status');
      if (ci) ci.value = '1';
      var vp = document.getElementById('verify-code-panel');
      if (vp) vp.style.display = '';
      var sb = document.getElementById('modal-submit-btn');
      if (sb) sb.innerHTML = '<?php echo addslashes($this->lang->line('verify_login') ?: 'Verify'); ?> <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
      refreshCaptcha();
      return;
    }

    refreshCaptcha();
    if (data.error) showModalErrors(data.error);
  })
  .catch(function() {
    if (btn) { btn.disabled = false; btn.innerHTML = origHtml; }
    showModalErrors({ err: '<?php echo addslashes($this->lang->line("something_went_wrong") ?: "Something went wrong. Please try again."); ?>' });
  });
}

function showModalErrors(errors) {
  var el = document.getElementById('modal-errors');
  if (!el) return;
  var html = '';
  Object.keys(errors).forEach(function(k) { if (errors[k]) html += '<p style="margin:0 0 4px;">' + errors[k] + '</p>'; });
  el.innerHTML = html;
  el.style.display = html ? '' : 'none';
  if (html) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function clearModalErrors() {
  var el = document.getElementById('modal-errors');
  if (el) { el.innerHTML = ''; el.style.display = 'none'; }
}

// ── Overlay / ESC close ──────────────────────────────────────
(function() {
  var overlay = document.getElementById('apptModalOverlay');
  var closeBtn = document.getElementById('apptModalClose');
  if (closeBtn) closeBtn.addEventListener('click', closeApptModal);
  if (overlay)  overlay.addEventListener('click', function(e) { if (e.target === overlay) closeApptModal(); });
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeApptModal(); });
})();
</script>

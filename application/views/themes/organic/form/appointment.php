<?php
$appt    = $static_pages['appointment'] ?? array();
$heading = html_escape($appt['heading']    ?? $this->lang->line('appointment'));
$subhead = html_escape($appt['subheading'] ?? '');
$note    = html_escape($appt['note']       ?? '');
$hs      = $home_sections ?? array();
$hero    = $hs['hero'] ?? array();
$emergency = !empty($hero['emergency_number']) ? $hero['emergency_number']
           : (!empty($school_setting->phone)   ? $school_setting->phone : '');
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/tempus-dominus/css/tempus-dominus.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/tempus-dominus/js/popper.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/tempus-dominus/js/tempus-dominus.min.js"></script>

<!-- Page hero -->
<section class="page-hero compact">
  <span class="ph-blob" aria-hidden="true"></span>
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="sep">·</span>
      <span class="here"><?php echo $this->lang->line('book_appointment'); ?></span>
    </nav>
    <h1><?php echo $heading; ?></h1>
    <p class="lede"><?php echo $subhead ?: ($this->lang->line('appt_hero_lede') ?: 'Slots refresh every 60 s — no payment to book · free reschedule · WhatsApp reminder.'); ?></p>
  </div>
</section>


<!-- Booking form -->
<section class="appt-form-section">
  <div class="container">

    <?php if(validation_errors()): ?>
    <div class="appt-warn-banner">
      <?php echo validation_errors(); ?>
    </div>
    <?php endif; ?>

    <div class="appt-form-grid">

      <!-- ── Form column ── -->
      <div>
        <span class="kicker"><?php echo $this->lang->line('book_now') ?: 'Book now'; ?></span>
        <h2 class="appt-hero-heading"><?php echo $heading; ?></h2>
        <p class="appt-hero-para">
          <?php echo $subhead ?: ($this->lang->line('appt_form_intro') ?: 'A doctor will read your notes before you arrive — no repeating yourself at the desk.'); ?>
        </p>
        <?php if($note): ?><p class="appt-note-text"><?php echo $note; ?></p><?php endif; ?>

        <form id="appointment_form" action="<?php echo site_url('form/appointment'); ?>" method="post"
              class="contact-form">

          <!-- Specialist + Doctor -->
          <div class="cf-row">
            <div class="cf-field">
              <label for="f-specialist"><?php echo $this->lang->line('specialist'); ?> <span class="req">*</span></label>
              <select id="f-specialist" name="specialist" onchange="getdoctor(this.value)">
                <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
                <?php foreach($specialist as $sv): ?>
                <option value="<?php echo (int)$sv['id']; ?>" <?php if(set_value('specialist', $prefill_specialist ?? '') == $sv['id']) echo 'selected'; ?>>
                  <?php echo html_escape($sv['specialist_name']); ?>
                </option>
                <?php endforeach; ?>
              </select>
              <?php if(!empty($prefill_specialist)): ?>
              <script>document.addEventListener('DOMContentLoaded', function(){ var el = document.getElementById('f-specialist'); if (el && el.value && typeof getdoctor === 'function') { getdoctor(el.value); } });</script>
              <?php endif; ?>
              <?php if(form_error('specialist')): ?>
              <span class="appt-field-error"><?php echo form_error('specialist'); ?></span>
              <?php endif; ?>
            </div>
            <div class="cf-field">
              <label for="doctor"><?php echo $this->lang->line('doctor'); ?> <span class="req">*</span></label>
              <select name="doctor" onchange="getDoctorShift()" id="doctor">
                <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
              </select>
              <?php if(form_error('doctor')): ?>
              <span class="appt-field-error"><?php echo form_error('doctor'); ?></span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Shift + Date -->
          <div class="cf-row">
            <div class="cf-field">
              <label for="global_shift"><?php echo $this->lang->line('shift'); ?> <span class="req">*</span></label>
              <select name="global_shift" onchange="getShift();" id="global_shift">
                <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
                <?php if(!empty($global_shift)): foreach($global_shift as $gs): ?>
                <option value="<?php echo (int)$gs['id']; ?>" <?php if(set_value('global_shift') == $gs['id']) echo 'selected'; ?>>
                  <?php echo html_escape($gs['name']); ?>
                </option>
                <?php endforeach; endif; ?>
              </select>
              <?php if(form_error('global_shift')): ?>
              <span class="appt-field-error"><?php echo form_error('global_shift'); ?></span>
              <?php endif; ?>
            </div>
            <div class="cf-field">
              <label for="datepicker_input"><?php echo $this->lang->line('date'); ?> <span class="req">*</span></label>
              <div id="datetimepicker1">
                <input type="text" id="datepicker_input" autocomplete="off" readonly
                       name="date" value="<?php echo set_value('date'); ?>"
                       placeholder="<?php echo $this->lang->line('select_date'); ?>"/>
              </div>
              <?php if(form_error('date')): ?>
              <span class="appt-field-error"><?php echo form_error('date'); ?></span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Slots — book-card wrapper -->
          <input type="hidden" id="shift_id" name="shift" />
          <div id="shift"></div>

          <div id="slot-card" class="book-card d-none" >
            <div class="bc-head">
              <strong><?php echo $this->lang->line('available_slots') ?: 'Available time slots'; ?></strong>
              <span class="bc-pick" id="bc-doctor-label">—</span>
            </div>
            <div id="slot"></div>
            <div class="bc-foot" id="bc-date-label" class="d-none"></div>
          </div>
          <div id="slot-empty"><?php echo $this->lang->line('no_slot_available'); ?></div>

          <?php if(form_error('slot')): ?>
          <span class="appt-field-error"><?php echo form_error('slot'); ?></span>
          <?php endif; ?>
          <input type="hidden" id="slot_id" name="slot" />

          <!-- Message -->
          <div class="cf-field appt-cf-spaced">
            <label><?php echo $this->lang->line('message'); ?> <span class="req">*</span></label>
            <textarea name="message" rows="3"
                      placeholder="<?php echo $this->lang->line('concern_placeholder') ?: 'e.g. recurring chest pain; previous surgery in 2020'; ?>"><?php echo set_value('message'); ?></textarea>
            <?php if(form_error('message')): ?>
            <span class="appt-field-error"><?php echo form_error('message'); ?></span>
            <?php endif; ?>
          </div>

          <!-- Custom fields -->
          <?php echo display_custom_fields_patient('appointment'); ?>

        </form>
      </div>

      <!-- ── Summary sidebar ── -->
      <aside class="appt-summary">
        <h3><?php echo $this->lang->line('your_booking') ?: 'Your booking'; ?></h3>
        <dl>
          <div class="summary-row">
            <dt><?php echo $this->lang->line('hospital') ?: 'Hospital'; ?></dt>
            <dd><?php echo html_escape($school_setting->name ?? ''); ?></dd>
          </div>
          <div class="summary-row" id="aside-doctor-row" class="d-none">
            <dt><?php echo $this->lang->line('doctor') ?: 'Doctor'; ?></dt>
            <dd id="aside-doctor-name">—</dd>
          </div>
          <div class="summary-row" id="aside-slot-row" class="d-none">
            <dt><?php echo $this->lang->line('slot') ?: 'Date &amp; time'; ?></dt>
            <dd id="aside-slot-time">—</dd>
          </div>
          <div class="summary-row" id="aside-fees-row" class="d-none">
            <dt><?php echo $this->lang->line('fees') ?: 'Est. fee'; ?></dt>
            <dd id="aside-fees">—</dd>
          </div>
        </dl>

        <button type="button" id="appt-book-btn" class="appt-confirm">
          <?php echo $this->lang->line('book_appointment'); ?>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>

        <?php if(!empty($emergency)): ?>
        <div class="appt-aside-divider">
          <p class="appt-emergency-label"><?php echo $this->lang->line('emergency') ?: 'Emergency'; ?></p>
          <a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/', '', $emergency)); ?>"
             class="appt-phone-link">
            <?php echo html_escape($emergency); ?>
          </a>
        </div>
        <?php endif; ?>

      </aside>

    </div><!-- /.appt-form-grid -->
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

      <!-- Right: patient login / register OR logged-in confirmation -->
      <?php if($patient_logged_in):
        $p_name = $this->session->userdata('patient')['name'] ?? '';
      ?>
      <div class="appt-modal-auth">
        <div class="appt-op-card">
          <div class="appt-op-label">
            <?php echo $this->lang->line('booking_as') ?: 'Booking as'; ?>
          </div>
          <div class="appt-op-name"><?php echo html_escape($p_name); ?></div>
          <a href="<?php echo site_url('site/logout'); ?>" class="appt-op-logout">
            <?php echo $this->lang->line('not_you_logout') ?: 'Not you? Logout'; ?>  →
          </a>
        </div>
      </div>
      <?php else: ?>
      <div class="appt-modal-auth">

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
              <input type="text" form="appointment_form" name="patient_name" value="<?php echo set_value('patient_name'); ?>" placeholder="<?php echo $this->lang->line('full_name') ?: 'Full name'; ?>"/>
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

    <div id="modal-errors" class="modal-errors d-none" ></div>

    <div class="appt-modal-foot">
      <button type="button" id="modal-submit-btn" class="appt-confirm" onclick="submitAppointment()">
        <?php echo $this->lang->line('book_appointment'); ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </button>
    </div>

  </div>
</div>

<link rel="stylesheet" href="<?php echo base_url(); ?>backend/toast-alert/toastr.css">
<script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>
<script>
var global_date_format = "<?php echo $this->customlib->getJSDateFormat(); ?>";
var base_url     = "<?php echo base_url(); ?>";
var lang_select  = "— <?php echo $this->lang->line('select'); ?> —";
var lang_loading = "<?php echo $this->lang->line('loading') ?: 'Loading…'; ?>";
var lang_no_slot = "<?php echo $this->lang->line('no_slot_available'); ?>";
</script>
<script src="<?php echo base_url(); ?>backend/js/online-appointment.js"></script>
<script>
(function() {
  // Date picker — init on the INPUT itself (not the wrapper) for reliable click toggle
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

  // Show/hide the book-card and empty message based on slot content
  var origLoadSlots = window.loadSlots;
  window.loadSlots = function(shift, doctor, date, global_shift) {
    origLoadSlots(shift, doctor, date, global_shift);
    // update bc-doctor-label from the doctor select
    var doctorSel = document.getElementById('doctor');
    var bcLabel   = document.getElementById('bc-doctor-label');
    if (doctorSel && bcLabel && doctorSel.selectedIndex > 0) {
      bcLabel.textContent = doctorSel.options[doctorSel.selectedIndex].text;
    }
    // update bc-date-label
    var bcDate = document.getElementById('bc-date-label');
    if (bcDate && date) {
      bcDate.textContent = date;
      bcDate.style.display = '';
    }
    // show the card, hide empty message
    document.getElementById('slot-card').style.display   = '';
    document.getElementById('slot-empty').style.display  = 'none';
  };

  // Original clearSlots also hides card
  var origClear = window.clearSlots;
  window.clearSlots = function() {
    origClear();
    document.getElementById('slot-card').style.display   = 'none';
    document.getElementById('slot-empty').style.display  = '';
    var bcLabel = document.getElementById('bc-doctor-label');
    if (bcLabel) bcLabel.textContent = '—';
    var bcDate  = document.getElementById('bc-date-label');
    if (bcDate)  { bcDate.textContent = ''; bcDate.style.display = 'none'; }
  };
})();

// ── Book button: validate then open modal (or submit directly if logged in) ──
document.getElementById('appt-book-btn').addEventListener('click', function() {
  var specialist = document.getElementById('f-specialist');
  var doctor     = document.getElementById('doctor');
  var dateInp    = document.getElementById('datepicker_input');
  var shiftSel   = document.getElementById('global_shift');
  var slot       = document.getElementById('slot_id').value;

  var missing = [];
  if (specialist && !specialist.value) missing.push('Specialist');
  if (doctor && !doctor.value)         missing.push('Doctor');
  if (shiftSel && !shiftSel.value)     missing.push('Shift');
  if (dateInp && !dateInp.value)       missing.push('Date');
  if (slot === '')                     missing.push('Slot');

  if (missing.length) {
    if (typeof toastr !== 'undefined' && toastr.warning) {
      toastr.warning('Please select: ' + missing.join(', '));
    } else {
      alert('Please select: ' + missing.join(', '));
    }
    var firstMissing = specialist && !specialist.value ? specialist
                     : doctor && !doctor.value         ? doctor
                     : shiftSel && !shiftSel.value     ? shiftSel
                     : dateInp && !dateInp.value       ? dateInp
                     : null;
    if (firstMissing) firstMissing.focus();
    return;
  }
  openApptModal();
});

// ── Modal open / close ───────────────────────────────────────
function openApptModal() {
  var doctorSel = document.getElementById('doctor');
  var dateVal   = document.getElementById('datepicker_input').value;
  var slotLabel = document.getElementById('aside-slot-time');
  var feesLabel = document.getElementById('aside-fees');
  var feesRow   = document.getElementById('aside-fees-row');

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
      if (sb) sb.innerHTML = '<?php echo addslashes($this->lang->line('verify_login') ?: 'Verify'); ?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
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
  var overlay  = document.getElementById('apptModalOverlay');
  var closeBtn = document.getElementById('apptModalClose');
  if (closeBtn) closeBtn.addEventListener('click', closeApptModal);
  if (overlay)  overlay.addEventListener('click', function(e) { if (e.target === overlay) closeApptModal(); });
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeApptModal(); });
})();
</script>

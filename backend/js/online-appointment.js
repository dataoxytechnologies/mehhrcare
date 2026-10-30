// online-appointment.js — slot booking for new themes (atrium / organic)
// Uses native fetch — no jQuery dependency.

var base_url = base_url || '';
var lang_select  = lang_select  || '— Select —';
var lang_loading = lang_loading || 'Loading…';
var lang_no_slot = lang_no_slot || 'No slots available for this date.';

/* ── helpers ────────────────────────────────────────────────── */
function _post(url, params) {
    var body = new URLSearchParams();
    Object.keys(params).forEach(function(k) { body.append(k, params[k]); });
    return fetch(base_url + url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded',
                   'X-Requested-With': 'XMLHttpRequest' },
        body: body.toString()
    }).then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    });
}

/* ── 1. Load doctors by specialist ─────────────────────────── */
function getdoctor(id, doc) {
    doc = doc || '';
    var sel = document.getElementById('doctor');
    if (!id) {
        sel.innerHTML = '<option value="">' + lang_select + '</option>';
        clearSlots();
        return;
    }
    sel.innerHTML = '<option value="">' + lang_loading + '</option>';
    _post('site/getdoctor', {id: id, active: 'yes'})
        .then(function(res) {
            var html = '<option value="">' + lang_select + '</option>';
            res.forEach(function(obj) {
                var sel2 = (doc !== '' && doc == obj.id) ? ' selected' : '';
                html += '<option value="' + parseInt(obj.id) + '"' + sel2 + '>' + obj.name + '</option>';
            });
            sel.innerHTML = html;
        })
        .catch(function() { sel.innerHTML = '<option value="">' + lang_select + '</option>'; });
    clearSlots();
}

/* ── 2. Load shifts for a doctor ────────────────────────────── */
function getDoctorShift(prev_val) {
    prev_val = prev_val || 0;
    var doctor_id = document.getElementById('doctor').value;
    var shiftSel  = document.getElementById('global_shift');
    if (!doctor_id) { clearSlots(); return; }
    _post('site/doctorshiftbyid', {doctor_id: doctor_id})
        .then(function(res) {
            var html = '<option value="">' + lang_select + '</option>';
            res.forEach(function(list) {
                var sel2 = (list.id == prev_val) ? ' selected' : '';
                html += '<option value="' + parseInt(list.id) + '"' + sel2 + '>' + list.name + '</option>';
            });
            shiftSel.innerHTML = html;
        })
        .catch(function() {});
    // Update aside with doctor name
    var doctorSel = document.getElementById('doctor');
    var sel = doctorSel && doctorSel.selectedIndex > 0 ? doctorSel.options[doctorSel.selectedIndex] : null;
    if (sel) { asideSet('doctor', sel.text); }
    clearSlots();
}

/* ── 3. Load time slots for selected shift + date ─────────── */
function getShift() {
    var date         = document.getElementById('datepicker_input').value;
    var doctor       = document.getElementById('doctor').value;
    var global_shift = document.getElementById('global_shift').value;
    var slotDiv      = document.getElementById('slot');
    var shiftDiv     = document.getElementById('shift');

    slotDiv.innerHTML  = '';
    shiftDiv.innerHTML = '';
    document.getElementById('slot_id').value = '';

    if (!date || !global_shift || !doctor) return;

    slotDiv.innerHTML = '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0;">' + lang_loading + '</p>';

    _post('site/getShift', {doctor: doctor, date: date, global_shift: global_shift})
        .then(function(res) {
            slotDiv.innerHTML = '';
            if (res && res.length) {
                var shift = res[0];
                document.getElementById('shift_id').value = shift.id;
                if (shift.start_time || shift.end_time) {
                    shiftDiv.innerHTML = '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0 10px;">' +
                        (shift.start_time || '') + (shift.end_time ? ' – ' + shift.end_time : '') + '</p>';
                }
                loadSlots(shift.id, doctor, date, global_shift);
            } else {
                slotDiv.innerHTML = '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0;">' + lang_no_slot + '</p>';
            }
        })
        .catch(function() {
            slotDiv.innerHTML = '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0;">' + lang_no_slot + '</p>';
        });
}

/* ── 4. Load individual time slots ───────────────────────────── */
function loadSlots(shift, doctor, date, global_shift) {
    document.getElementById('shift_id').value = shift;
    _post('site/getSlotByShift', {shift: shift, doctor: doctor, date: date, global_shift: global_shift})
        .then(function(data) {
            var slotDiv = document.getElementById('slot');
            if (!data.result || !data.result.length) {
                slotDiv.innerHTML = '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0;">' + lang_no_slot + '</p>';
                return;
            }
            var html = '<div class="slots">';
            data.result.forEach(function(obj, i) {
                var isOff   = obj.filled === 'filled';
                var cls     = isOff ? 'slot off' : 'slot';
                var onclick = isOff ? '' : ' onclick="setSlot(' + i + ',this)"';
                var fill    = obj.filled ? ' data-filled="filled"' : '';
                html += '<div class="' + cls + '" id="slot_' + i + '"' + fill + onclick + '>' + obj.time + '</div>';
            });
            html += '</div>';
            slotDiv.innerHTML = html;
            if (data.fees)     { asideSet('fees', data.fees); }
            if (data.duration) { asideSet('duration', data.duration); }
        })
        .catch(function() {
            document.getElementById('slot').innerHTML =
                '<p style="font-size:13px;color:var(--muted,#888);margin:4px 0;">' + lang_no_slot + '</p>';
        });
}

/* ── 5. Select a time slot ───────────────────────────────────── */
function setSlot(i, el) {
    document.querySelectorAll('#slot .slot').forEach(function(s) { s.classList.remove('on'); });
    if (el && el.dataset.filled === 'filled') { return; }
    if (el) { el.classList.add('on'); }
    document.getElementById('slot_id').value = i;
    if (el) { asideSet('slot', el.textContent.trim()); }
}

/* ── 6. Refresh captcha ──────────────────────────────────────── */
function refreshCaptcha() {
    fetch(base_url + 'site/refreshCaptcha', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.text(); })
    .then(function(html) {
        var el = document.querySelector('.captcha_image');
        if (el) el.innerHTML = html;
        var inp = document.getElementById('captcha_login');
        if (inp) inp.value = '';
    });
}

/* ── Helpers ─────────────────────────────────────────────────── */
function clearSlots() {
    document.getElementById('slot').innerHTML  = '';
    document.getElementById('shift').innerHTML = '';
    document.getElementById('slot_id').value   = '';
    asideClear('slot');
    asideClear('fees');
}

function asideSet(key, val) {
    var row  = document.getElementById('aside-' + key + '-row');
    var name = document.getElementById('aside-' + key + '-name') ||
               document.getElementById('aside-' + key + '-time') ||
               document.getElementById('aside-' + key);
    if (row)  { row.style.display = ''; }
    if (name) { name.textContent = val; }
}

function asideClear(key) {
    var row  = document.getElementById('aside-' + key + '-row');
    var name = document.getElementById('aside-' + key + '-name') ||
               document.getElementById('aside-' + key + '-time') ||
               document.getElementById('aside-' + key);
    if (row)  { row.style.display = 'none'; }
    if (name) { name.textContent = '—'; }
}

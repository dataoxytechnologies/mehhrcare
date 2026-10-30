<!-- ═══════════════════ PAGE CONTENT END ═══════════════════ -->
</main><!-- /.content -->

<!-- Bootstrap 5.3.3 JS Bundle (includes Popper.js) -->
<script src="<?php echo base_url('backend/bootstrap5/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- jQuery shim: bridges BS3/BS4 $.fn.popover() and $.fn.tooltip() calls to BS5 native API -->
<script>
(function($) {
    if (typeof bootstrap === 'undefined') return;
    $.fn.popover = function(options) {
        return this.each(function() {
            if (typeof options === 'string') { var inst = bootstrap.Popover.getInstance(this); if (inst) inst[options](); }
            else if (!bootstrap.Popover.getInstance(this)) { new bootstrap.Popover(this, options || {}); }
        });
    };
    $.fn.tooltip = function(options) {
        return this.each(function() {
            if (typeof options === 'string') { var inst = bootstrap.Tooltip.getInstance(this); if (inst) inst[options](); }
            else if (!bootstrap.Tooltip.getInstance(this)) { new bootstrap.Tooltip(this, options || {}); }
        });
    };
    $.fn.modal = function(options) {
        return this.each(function() {
            if (typeof options === 'string') { var inst = bootstrap.Modal.getInstance(this); if (inst) inst[options](); else { inst = new bootstrap.Modal(this); inst[options](); } }
            else { new bootstrap.Modal(this, options || {}); }
        });
    };
    /* BS3 stubs — methods removed in BS5, no-op so old plugins don't throw */
    $.fn.affix     = $.fn.affix     || function() { return this; };
    $.fn.scrollspy = $.fn.scrollspy || function() { return this; };
    /* Always override $.fn.button to bypass BS5's Button plugin which binds a
       component instance and conflicts with already-bound Tooltip (one-instance
       per element rule). Handle BS3 'loading'/'reset' AND BS5 'toggle' here
       without creating a Bootstrap component instance. */
    $.fn.button = function(action) {
        return this.each(function() {
            var $el = $(this);
            if (action === 'loading') {
                $el.data('orig-html', $el.html()).prop('disabled', true)
                   .html($el.data('loading-text') || '<i class="fa fa-spinner fa-spin me-1"></i>');
            } else if (action === 'reset') {
                $el.prop('disabled', false).html($el.data('orig-html') || $el.html());
            } else if (action === 'toggle') {
                this.classList.toggle('active');
                this.setAttribute('aria-pressed', this.classList.contains('active'));
            }
        });
    };
}(jQuery));
</script>

<!-- hospital-custom-bs5.js: BS5-compatible polyfills (shModal helper, btnLoading/btnReset, etc.)
     Must load AFTER Bootstrap bundle. Mirrors admin's load pattern at layout/footer.php:256. -->
<script src="<?php echo base_url('backend/js/hospital-custom-bs5.js'); ?>?v=<?php echo filemtime(FCPATH.'backend/js/hospital-custom-bs5.js'); ?>"></script>

<!-- sh-bubble flash-message wrappers — override the toastr versions defined just above in
     hospital-custom-bs5.js so patient-panel save/edit/delete toasts use the same bottom-right
     bubble design as admin. Renders via window.shBubble (header polling IIFE); if that didn't
     load, the toastr wrappers stay in place. Must run AFTER hospital-custom-bs5.js (last wins). -->
<script type="text/javascript">
(function () {
    if (!window.shBubble) { return; }
    var make = window.shBubble.make, present = window.shBubble.present;
    window.successMsg = function(msg) {
        present(make({ kind: 'success', iconClass: 'fa fa-check',                title: 'Success',  message: msg }), 4000);
    };
    window.errorMsg = function(msg) {
        present(make({ kind: 'error',   iconClass: 'fa fa-exclamation-circle',   title: 'Error',    message: msg }), 8000);
    };
    window.warningMsg = function(msg) {
        present(make({ kind: 'warn',    iconClass: 'fa fa-exclamation-triangle', title: 'Warning',  message: msg }), 6000);
    };
    window.infoMsg = function(msg) {
        present(make({ kind: 'info',    iconClass: 'fa fa-info-circle',          title: 'Heads up', message: msg }), 5000);
    };
})();
</script>

<!-- Sidebar toggle (collapse/expand) — persists state in localStorage -->
<script>
(function() {
    if (localStorage.getItem('sh_sidebar_collapsed') === '1') {
        document.body.classList.add('sidebar-collapsed');
    }
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('sidebarToggle');
        if (btn) {
            btn.addEventListener('click', function() {
                // QW-02 / mobile-sidebar (2026-05-18): viewport-aware toggle.
                // ≤768px → off-canvas drawer; desktop → collapse to icon-only rail.
                if (window.matchMedia('(max-width: 768px)').matches) {
                    document.body.classList.toggle('sidebar-mobile-open');
                } else {
                    var collapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sh_sidebar_collapsed', collapsed ? '1' : '0');
                }
            });
        }
        // Backdrop click-to-close (matching admin pattern at layout/footer.php).
        document.addEventListener('click', function(e) {
            if (!document.body.classList.contains('sidebar-mobile-open')) return;
            if (e.target.closest('.sh-sidebar')) return;
            if (e.target.closest('#sidebarToggle')) return;
            document.body.classList.remove('sidebar-mobile-open');
        });

        /* Auto-populate data-tooltip on each nav-link for collapsed tooltip */
        document.querySelectorAll('.sh-sidebar .sh-nav > .nav-item > .nav-link').forEach(function(link) {
            var span = link.querySelector('span');
            if (span) { var label = span.textContent.trim(); if (label) link.setAttribute('data-tooltip', label); }
        });

        /* Auto-detect active nav item by matching href against current URL */
        (function detectActiveNav() {
            var currentPath = window.location.pathname.replace(/\/+$/, '');
            var links = document.querySelectorAll('.sh-sidebar .sh-nav .nav-item > .nav-link[href]');
            var bestLi = null, bestLen = 0;
            links.forEach(function(a) {
                var href = a.getAttribute('href');
                if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
                var hrefPath;
                try { hrefPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, ''); }
                catch(e) { hrefPath = href.replace(/\/+$/, ''); }
                if (!hrefPath) return;
                if (currentPath === hrefPath || currentPath.indexOf(hrefPath + '/') === 0) {
                    if (hrefPath.length > bestLen) { bestLi = a.closest('.nav-item'); bestLen = hrefPath.length; }
                }
            });
            if (bestLi) {
                bestLi.classList.add('active');
                var link = bestLi.querySelector(':scope > .nav-link');
                if (link) link.classList.add('active');
            }
        })();

        /* Body-attached tooltip for collapsed sidebar */
        var railTip = document.createElement('div');
        railTip.className = 'sh-rail-tooltip';
        document.body.appendChild(railTip);
        document.querySelectorAll('.sh-sidebar .sh-nav > .nav-item > .nav-link').forEach(function(link) {
            link.addEventListener('mouseenter', function() {
                if (!document.body.classList.contains('sidebar-collapsed')) return;
                var label = link.getAttribute('data-tooltip');
                if (!label) return;
                var rect = link.getBoundingClientRect();
                railTip.textContent = label;
                railTip.style.left = (rect.right + 12) + 'px';
                railTip.style.top  = (rect.top + rect.height / 2) + 'px';
                railTip.classList.add('show');
            });
            link.addEventListener('mouseleave', function() { railTip.classList.remove('show'); });
        });
    });
})();
</script>

<!-- SH Chrome: injects variant switcher (A/B/C) -->
<script src="<?php echo base_url('backend/js/sh-chrome.js'); ?>"></script>

<!-- NProgress -->
<script src="<?php echo base_url('backend/dist/js/nprogress.js'); ?>"></script>

<!-- Dropify (file upload dropzones) -->
<script src="<?php echo base_url('backend/dist/js/dropify.min.js'); ?>"></script>

<!-- DataTables core + BS5 skin -->
<script src="<?php echo base_url('backend/dist/datatables/js/jquery.dataTables.min-stu.par.js'); ?>"></script>
<script src="<?php echo base_url('backend/plugins/datatables/dataTables.bootstrap5.min.js'); ?>"></script>

<!-- DataTables extensions -->
<script src="<?php echo base_url('backend/dist/datatables/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/jszip.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/pdfmake.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/vfs_fonts.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.html5.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.print.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.colVis.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/ss.custom.js'); ?>?v=20260624b"></script>

<!-- Toastr notifications -->
<script src="<?php echo base_url('backend/toast-alert/toastr.js'); ?>"></script>

<!-- SweetAlert2 -->
<script src="<?php echo base_url('backend/sweet-alert/sweetalert2.min.js'); ?>"></script>

<!-- FullCalendar -->
<script src="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/fullcalendar/dist/locale-all.js'); ?>"></script>

<!-- Date/time picker — Tempus Dominus 6 (BS5 native, single library) -->
<!-- Popper.js MUST load BEFORE Tempus Dominus (TD 6 needs window.Popper global) -->
<?php
$language      = $this->customlib->getLanguage();
$language_name = $language['short_code'];
// Map patient language → TD 6 locale code (English is built-in default)
$td_locale_map_p = [
    'ar' => 'ar', 'es' => 'es', 'fr' => 'fr', 'de' => 'de',
    'it' => 'it', 'nl' => 'nl', 'pl' => 'pl', 'pt' => 'pt-PT',
    'ru' => 'ru', 'tr' => 'tr', 'zh' => 'zh-CN'
];
$td_locale_file_p = isset($td_locale_map_p[$language_name]) ? $td_locale_map_p[$language_name] : null;
?>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/popper.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/tempus-dominus.min.js'); ?>"></script>
<?php if ($td_locale_file_p && file_exists(FCPATH.'backend/plugins/tempus-dominus/js/locales/'.$td_locale_file_p.'.js')): ?>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/locales/'.$td_locale_file_p.'.js'); ?>"></script>
<?php endif; ?>

<!-- Bootstrap Select JS -->
<script src="<?php echo base_url('backend/dist/js/bootstrap-select.min.js'); ?>"></script>

<?php if ($language_name != 'en'): ?>
<script src="<?php echo base_url('backend/dist/js/locale/' . $language_name . '.js'); ?>"></script>
<?php endif; ?>

<!-- Bootstrap Select init for language switcher -->
<script>
$(function(){ $('.languageselectpicker').selectpicker(); });
</script>

<!-- Sidebar tooltip init (BS5) -->
<script>
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').each(function() { bootstrap.Tooltip.getOrCreateInstance(this); });
});
</script>

<!-- Global date/time picker initialization — Tempus Dominus 6 (settings-driven from sch_settings) -->
<script type="text/javascript">
(function () {
    // SETTINGS-DRIVEN VALUES (all from sch_settings table via Customlib)
    var phpDateFormat     = '<?php echo $this->customlib->getHospitalDateFormat(); ?>';
    var phpDateTimeFormat = '<?php echo $this->customlib->getHospitalDateFormat(true, true); ?>';
    var is24Hour          = <?php
        $tf = $this->customlib->getHospitalTimeFormat();
        echo ($tf === '24-hour' || $tf === true || $tf === 1 || $tf === '1') ? 'true' : 'false';
    ?>;
    var tdLocale          = '<?php echo isset($td_locale_file_p) && $td_locale_file_p ? $td_locale_file_p : 'en-US'; ?>';

    // PHP → Tempus Dominus 6 format mapping (uses Unicode control chars as collision-proof sentinels)
    function phpToTd(fmt) {
        return fmt
            .replace(/A/g, '')
            .replace(/a/g, '')
            .replace(/M(?![a-z])/g, '')
            .replace(/i/g, '')
            .replace(/h/g, '')
            .replace(/H/g, '')
            .replace(/m/g, '')
            .replace(/d/g, '')
            .replace(/Y/g, '	')
            .replace(//g, 'T')
            .replace(//g, 't')
            .replace(//g, 'MMM')
            .replace(//g, 'mm')
            .replace(//g, 'hh')
            .replace(//g, 'HH')
            .replace(//g, 'MM')
            .replace(//g, 'dd')
            .replace(/	/g, 'yyyy');
    }
    var tdDateFormat     = phpToTd(phpDateFormat);
    var tdDateTimeFormat = phpToTd(phpDateTimeFormat);
    var tdHourCycle      = is24Hour ? 'h23' : 'h12';
    var initFlag = '_pickerInit';

    function baseConfig(format) {
        return {
            allowInputToggle: true,
            container: document.body, // append to body — escapes modal stacking context
            localization: { format: format, locale: tdLocale },
            display: {
                buttons: { today: true, clear: true, close: true },
                theme: 'light',
                icons: {
                    type: 'icons', time: 'fa fa-clock', date: 'fa fa-calendar',
                    up: 'fa fa-arrow-up', down: 'fa fa-arrow-down',
                    previous: 'fa fa-chevron-left', next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check', clear: 'fa fa-trash', close: 'fa fa-times'
                }
            }
        };
    }

    function initDatePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateFormat);
        cfg.display.components = { calendar: true, decades: true, year: true, month: true, date: true,
            clock: false, hours: false, minutes: false, seconds: false };
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
    }
    function initDateTimePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateTimeFormat);
        cfg.display.components = { calendar: true, decades: true, year: true, month: true, date: true,
            clock: true, hours: true, minutes: true, seconds: false };
        cfg.localization.hourCycle = tdHourCycle;
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
    }
    function initTimePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(is24Hour ? 'HH:mm' : 'hh:mm T');
        cfg.display.components = { calendar: false, decades: false, year: false, month: false, date: false,
            clock: true, hours: true, minutes: true, seconds: false };
        cfg.display.viewMode = 'clock';
        cfg.localization.hourCycle = tdHourCycle;
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
    }
    function initDateRangePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateFormat);
        cfg.display.components = { calendar: true, decades: true, year: true, month: true, date: true,
            clock: false, hours: false, minutes: false, seconds: false };
        cfg.dateRange = true;
        cfg.multipleDatesSeparator = ' to ';
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
    }

    // Initial init for existing inputs
    document.querySelectorAll('input.date').forEach(initDatePicker);
    document.querySelectorAll('input.datetime').forEach(initDateTimePicker);
    document.querySelectorAll('input.timepicker').forEach(initTimePicker);
    document.querySelectorAll('input.daterange').forEach(initDateRangePicker);

    // Event delegation for dynamically added inputs
    document.body.addEventListener('focusin', function (e) {
        var t = e.target;
        if (!(t instanceof HTMLInputElement)) return;
        if (t.classList.contains('date'))            initDatePicker(t);
        else if (t.classList.contains('datetime'))   initDateTimePicker(t);
        else if (t.classList.contains('timepicker')) initTimePicker(t);
        else if (t.classList.contains('daterange'))  initDateRangePicker(t);
    });

    // Backward-compat shim removed (2026-05-04) — patient-side views also migrated.
    // Use SHPicker.* helpers (defined in admin layout/footer.php) for any new picker code.
})();
</script>

<!-- Color picker widget -->
<script type="text/javascript">
$('body').on('click', '.cpicker', function () {
    if ($(this).hasClass('cpicker-big')) { return false; }
    $(this).parents('.cpicker-wrapper').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
    $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
    if ($(this).hasClass('kanban-cpicker')) {
        $(this).parents('.panel-heading-bg').css('background', $(this).data('color'));
        $(this).parents('.panel-heading-bg').css('border', '1px solid ' + $(this).data('color'));
    } else if ($(this).hasClass('calendar-cpicker')) {
        $('body').find('input[name="eventcolor"]').val($(this).data('color'));
    }
});
</script>

<!-- Calendar complete_event helper -->
<script type="text/javascript">
function complete_event(id, status) {
    $.ajax({
        url: '<?php echo site_url("user/calendar/markcomplete/"); ?>' + id,
        type: 'POST', data: { id: id, active: status }, dataType: 'json',
        success: function(res) {
            if (res.status == 'fail') {
                var message = ''; $.each(res.error, function(i, v) { message += v; }); errorMsg(message);
            } else { successMsg(res.message); window.location.reload(true); }
        }
    });
}
function markc(id) {
    $('#newcheck' + id).change(function() {
        complete_event(id, this.checked ? 'yes' : 'no');
    });
}
</script>

<!-- delete_recordByIdReload helper -->
<script type="text/javascript">
function delete_recordByIdReload(url) {
    if (confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete'); ?>')) {
        $.ajax({
            url: baseurl + url, dataType: 'json',
            success: function(res) {
                if (res.status == 1) { successMsg(res.msg); setTimeout(function() { window.location.reload(); }, 500); }
                else { errorMsg(res.msg); }
            },
            error: function() { alert('Something Went Wrong'); }
        });
    }
}
</script>

<!-- Print popup helper (updated to BS5 CSS) -->
<script type="text/javascript">
function popup(data, winload) {
    winload = winload || false;
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write('<html><head><title></title>');
    newWin.document.write('<link rel="preconnect" href="https://fonts.googleapis.com">');
    newWin.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">');
    newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/bootstrap5/css/bootstrap.min.css">');
    newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/font-awesome.min.css">');
    newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/ionicons.min.css">');
    newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/sh-print.css">');
    newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/iCheck/flat/blue.css">');
    newWin.document.write('</head>');
    newWin.document.write('<body onload="window.print()">');
    newWin.document.write(data);
    newWin.document.write('</body></html>');
    newWin.document.close();
    setTimeout(function() { newWin.close(); if (winload) { window.location.reload(true); } }, 5000);
    return true;
}
</script>

<!-- Flash messages from session (CI flash data → toastr) -->
<script type="text/javascript">
$(document).ready(function() {
<?php if ($this->session->flashdata('success_msg')): ?>
    successMsg('<?php echo addslashes($this->session->flashdata('success_msg')); ?>');
    <?php $this->session->unset_userdata('success_msg'); ?>
<?php elseif ($this->session->flashdata('error_msg')): ?>
    errorMsg('<?php echo addslashes($this->session->flashdata('error_msg')); ?>');
    <?php $this->session->unset_userdata('error_msg'); ?>
<?php elseif ($this->session->flashdata('warning_msg')): ?>
    infoMsg('<?php echo addslashes($this->session->flashdata('warning_msg')); ?>');
    <?php $this->session->unset_userdata('warning_msg'); ?>
<?php elseif ($this->session->flashdata('info_msg')): ?>
    warningMsg('<?php echo addslashes($this->session->flashdata('info_msg')); ?>');
    <?php $this->session->unset_userdata('info_msg'); ?>
<?php endif; ?>
});
</script>

<!-- Session modal (BS5) -->
<div class="modal fade" id="user_sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
  <form action="#" id="form_modal_usersession">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sessionModalLabel"><?php echo $this->lang->line('session'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body user_sessionmodal_body pb-0"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary submit_usersession">
            <span class="normal-text"><?php echo $this->lang->line('save'); ?></span>
            <span class="loading-text" style="display:none;"><i class="fa fa-spinner fa-spin me-1"></i><?php echo $this->lang->line('please_wait'); ?></span>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php
/* IPD prescription auto-open on page load */
if (isset($ipdnpres_data) && !empty($ipdnpres_data) && isset($ipdnpres_data['ipdnpres_data']['patient_id'])) {
    $datetime_format_js = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy', 'H' => 'hh', 'i' => 'mm']);
    ?>
<script type="text/javascript">
var datetime_format = '<?php echo $datetime_format_js; ?>';
var ipdnpres_data = '<?php echo json_encode($ipdnpres_data['ipdnpres_data']); ?>';
var data = JSON.parse(ipdnpres_data);
view_prescription(data.presid, data.id, 'yes');
</script>
<?php } ?>

</body>
</html>

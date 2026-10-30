<!-- ═══════════════════ PAGE CONTENT END ═══════════════════ -->
</main><!-- /.content -->

<!-- Bootstrap 5.3.3 JS Bundle (includes Popper.js) -->
<script src="<?php echo base_url('backend/bootstrap5/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Guard: patch Bootstrap Modal.getOrCreateInstance so data-bs-toggle="modal" without
     data-bs-target doesn't crash (Bootstrap passes null element → _initializeBackDrop fails).
     The onclick="holdModal(...)" on those elements still fires normally. -->
<script>
(function() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) return;
    var _orig = bootstrap.Modal.getOrCreateInstance;
    bootstrap.Modal.getOrCreateInstance = function(element, config) {
        if (!element) return { show: function(){}, hide: function(){}, toggle: function(){} };
        return _orig.call(this, element, config);
    };
})();
</script>

<!-- jQuery shim: bridges BS3/BS4 $.fn.popover() and $.fn.tooltip() to BS5 native API -->
<script>
(function($) {
    if (typeof bootstrap === 'undefined') return;
    $.fn.popover = function(options) {
        return this.each(function() {
            if (typeof options === 'string') {
                var inst = bootstrap.Popover.getInstance(this);
                if (inst) inst[options]();
            } else {
                new bootstrap.Popover(this, options || {});
            }
        });
    };
    $.fn.tooltip = function(options) {
        return this.each(function() {
            if (typeof options === 'string') {
                var inst = bootstrap.Tooltip.getInstance(this);
                if (inst) inst[options]();
            } else {
                new bootstrap.Tooltip(this, options || {});
            }
        });
    };
}(jQuery));
</script>

<!-- Sidebar toggle (collapse/expand) — persists state in localStorage -->
<script>
(function() {
  // Restore collapsed state on initial load (before paint to prevent flash)
  if (localStorage.getItem('sh_sidebar_collapsed') === '1') {
    document.body.classList.add('sidebar-collapsed');
  }
  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('sidebarToggle');
    if (btn) {
      btn.addEventListener('click', function() {
        // QW-02 / mobile-sidebar (2026-05-18): at ≤768px the toggle opens an
        // off-canvas drawer instead of collapsing the rail. Mobile state is
        // not persisted (it should close on next page load).
        if (window.matchMedia('(max-width: 768px)').matches) {
          document.body.classList.toggle('sidebar-mobile-open');
        } else {
          var collapsed = document.body.classList.toggle('sidebar-collapsed');
          localStorage.setItem('sh_sidebar_collapsed', collapsed ? '1' : '0');
        }
      });
    }
    // Click backdrop to close mobile drawer (the ::before pseudo sits at z-index 1055
    // covering everything except the sidebar at z-index 1060; we detect clicks on it
    // via "click happened outside .sh-sidebar while body.sidebar-mobile-open").
    document.addEventListener('click', function(e) {
      if (!document.body.classList.contains('sidebar-mobile-open')) return;
      if (e.target.closest('.sh-sidebar')) return;       // click inside drawer — ignore
      if (e.target.closest('#sidebarToggle')) return;    // click on toggle — toggle handler runs
      document.body.classList.remove('sidebar-mobile-open');
    });

    // Auto-populate data-tooltip on each top-level nav-link
    // (extracts the <span> text for collapsed-state hover tooltip)
    document.querySelectorAll('.sh-sidebar .sh-nav > .nav-item > .nav-link').forEach(function(link) {
      var span = link.querySelector('span');
      if (span) {
        var label = span.textContent.trim();
        if (label) link.setAttribute('data-tooltip', label);
      }
    });


    // Auto-detect active submenu by matching each submenu link's href against current URL.
    // Defer to sidebar.php's data-submenu auto-detect (runs earlier) and any server-side
    // set_SubSubmenu()/set_Submenu() highlights — those are more reliable for ambiguous URLs
    // where one link's href is a prefix of another section's URL (e.g., /admin/income vs
    // /admin/income/alltransactionreport).
    (function detectActiveSubmenu() {
      var serverActive = document.querySelector('.sh-sidebar .sh-subnav .nav-item.active');
      if (serverActive) {
        // Server (set_Submenu) already highlighted the active item. Make sure its
        // parent panel is open, then scroll it into view — otherwise this path
        // returned without scrolling and the sidebar stayed pinned at the top.
        var sub = serverActive.closest('.sh-subnav');
        // Open via the .show class only — CSS maps .sh-subnav.show -> display:block.
        // Do NOT also set inline style.display: the sidebar accordion closes panels by
        // removing the .show class, and an inline display:block would survive that and
        // keep this panel visually open when another menu group is opened.
        if (sub) { sub.classList.add('show'); }
        requestAnimationFrame(function() {
          var scrollEl = document.querySelector('.sh-sidebar-scroll');
          if (!scrollEl) return;
          var liRect = serverActive.getBoundingClientRect();
          var elRect = scrollEl.getBoundingClientRect();
          if (liRect.top < elRect.top || liRect.bottom > elRect.bottom) {
            // Centre the active item using rendered positions (offsetParent-agnostic).
            var target = scrollEl.scrollTop + (liRect.top - elRect.top) - (scrollEl.clientHeight / 2) + (liRect.height / 2);
            scrollEl.scrollTop = Math.max(0, target);
          }
        });
        return;
      }
      var currentPath = window.location.pathname.replace(/\/+$/, '');  // strip trailing slash
      var subLinks = document.querySelectorAll('.sh-sidebar .sh-subnav .nav-item > .nav-link[href]');
      var bestLi = null;
      var bestLen = 0;

      subLinks.forEach(function(a) {
        var href = a.getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return;

        // Convert href to absolute path (strip protocol+host if present)
        var hrefPath;
        try {
          var url = new URL(href, window.location.origin);
          hrefPath = url.pathname.replace(/\/+$/, '');
        } catch (e) {
          hrefPath = href.replace(/\/+$/, '');
        }
        if (!hrefPath) return;

        // Match: current path must start with hrefPath (handles sub-actions like /edit/3)
        if (currentPath === hrefPath || currentPath.indexOf(hrefPath + '/') === 0) {
          if (hrefPath.length > bestLen) {
            bestLi = a.closest('.nav-item');
            bestLen = hrefPath.length;
          }
        }
      });

      if (bestLi) {
        // Mark submenu item active
        bestLi.classList.add('active');
        var subLink = bestLi.querySelector(':scope > .nav-link');
        if (subLink) subLink.classList.add('active');

        // Open parent submenu container
        var subnavUl = bestLi.closest('.sh-subnav');
        if (subnavUl) {
          subnavUl.classList.add('show'); // class only — see note above; no inline display
          // Mark parent top-level nav-item as active too
          var topLi = subnavUl.closest('.sh-nav > .nav-item') || subnavUl.parentElement;
          if (topLi && topLi.classList) {
            topLi.classList.add('active');
            var topLink = topLi.querySelector(':scope > .nav-link');
            if (topLink) {
              topLink.classList.add('active');
              topLink.setAttribute('aria-expanded', 'true');
            }
          }
        }

        // Auto-scroll active item into view (centered) — runs after layout is ready
        requestAnimationFrame(function() {
          var scrollEl = document.querySelector('.sh-sidebar-scroll');
          if (!scrollEl) return;
          var liRect = bestLi.getBoundingClientRect();
          var elRect = scrollEl.getBoundingClientRect();
          // Only scroll if active item is outside visible area
          if (liRect.top < elRect.top || liRect.bottom > elRect.bottom) {
            var offset = bestLi.offsetTop - (scrollEl.clientHeight / 2) + (bestLi.offsetHeight / 2);
            scrollEl.scrollTop = Math.max(0, offset);
          }
        });
      } else {
        // No active submenu — try to scroll active top-level nav-item into view
        requestAnimationFrame(function() {
          var activeTop = document.querySelector('.sh-sidebar .sh-nav > .nav-item.active, .sh-sidebar .sh-nav > .nav-item > .nav-link.active');
          var scrollEl = document.querySelector('.sh-sidebar-scroll');
          if (!activeTop || !scrollEl) return;
          var li = activeTop.closest('.nav-item');
          if (!li) return;
          var liRect = li.getBoundingClientRect();
          var elRect = scrollEl.getBoundingClientRect();
          if (liRect.top < elRect.top || liRect.bottom > elRect.bottom) {
            var offset = li.offsetTop - (scrollEl.clientHeight / 2) + (li.offsetHeight / 2);
            scrollEl.scrollTop = Math.max(0, offset);
          }
        });
      }
    })();

    // Body-attached overlays for collapsed sidebar (escape overflow contexts):
    //   • leaf items  -> name tooltip
    //   • parent items (have a .sh-subnav) -> interactive flyout listing child links
    var railTip = document.createElement('div');
    railTip.className = 'sh-rail-tooltip';
    document.body.appendChild(railTip);

    var railFlyout = document.createElement('div');
    railFlyout.className = 'sh-rail-flyout';
    document.body.appendChild(railFlyout);

    var railHideTimer = null;
    function hideRailOverlays() { railTip.classList.remove('show'); railFlyout.classList.remove('show'); }
    function scheduleRailHide() { railHideTimer = setTimeout(hideRailOverlays, 180); }
    function cancelRailHide() { if (railHideTimer) { clearTimeout(railHideTimer); railHideTimer = null; } }

    // Keep the flyout open while the pointer is over it (bridges the icon→panel gap).
    railFlyout.addEventListener('mouseenter', cancelRailHide);
    railFlyout.addEventListener('mouseleave', scheduleRailHide);

    document.querySelectorAll('.sh-sidebar .sh-nav > .nav-item').forEach(function(item) {
      var link = item.querySelector(':scope > .nav-link');
      if (!link) return;
      var subnav = item.querySelector(':scope > .sh-subnav');

      link.addEventListener('mouseenter', function() {
        if (!document.body.classList.contains('sidebar-collapsed')) return;
        cancelRailHide();
        var rect = link.getBoundingClientRect();

        if (subnav) {
          // Parent: show flyout with the parent label + cloned child links (preserves hrefs/RBAC).
          var label = link.getAttribute('data-tooltip') || '';
          railTip.classList.remove('show');
          railFlyout.innerHTML = '<div class="sh-rail-flyout-title">' + label + '</div><ul>' + subnav.innerHTML + '</ul>';
          railFlyout.style.left = (rect.right + 6) + 'px';
          railFlyout.style.top  = rect.top + 'px';
          railFlyout.classList.add('show');
          // Clamp into viewport if it would overflow the bottom edge.
          var fr = railFlyout.getBoundingClientRect();
          if (fr.bottom > window.innerHeight - 8) {
            railFlyout.style.top = Math.max(8, window.innerHeight - 8 - fr.height) + 'px';
          }
        } else {
          // Leaf: simple name tooltip.
          var lbl = link.getAttribute('data-tooltip');
          if (!lbl) return;
          railFlyout.classList.remove('show');
          railTip.textContent = lbl;
          railTip.style.left = (rect.right + 12) + 'px';
          railTip.style.top  = (rect.top + rect.height / 2) + 'px';
          railTip.classList.add('show');
        }
      });

      item.addEventListener('mouseleave', function() {
        if (subnav) { scheduleRailHide(); } else { railTip.classList.remove('show'); }
      });
    });
  });
})();
</script>

<!-- SH Chrome: injects module rail + topbar using data-chrome attribute on <body> -->
<script src="<?php echo base_url('backend/js/sh-chrome.js'); ?>"></script>

<!-- NProgress (page load progress bar) -->
<script src="<?php echo base_url('backend/dist/js/nprogress.js'); ?>"></script>

<!-- Dropify (file upload dropzones) -->
<script src="<?php echo base_url('backend/dist/js/dropify.min.js'); ?>"></script>

<!-- DataTables core + BS5 skin -->
<script src="<?php echo base_url('backend/dist/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/plugins/datatables/dataTables.bootstrap5.min.js'); ?>"></script>

<!-- DataTables extensions: buttons, export, responsive -->
<script src="<?php echo base_url('backend/dist/datatables/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/jszip.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/pdfmake.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/vfs_fonts.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.html5.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.print.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/buttons.colVis.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/dist/datatables/js/dataTables.responsive.min.js'); ?>"></script>

<!-- ss.custom.js — contains initDatatable() used by ALL server-side tables -->
<script src="<?php echo base_url('backend/dist/datatables/js/ss.custom.js'); ?>?v=20260624b"></script>

<!-- moment.js — must load before datetime-moment.js and datetimepicker -->
<script src="<?php echo base_url('backend/dist/js/moment.min.js'); ?>"></script>

<!-- DataTables datetime sorting (moment.js integration) -->
<script src="<?php echo base_url('backend/dist/datatables/js/datetime-moment.js'); ?>"></script>

<!-- Select2 -->
<script src="<?php echo base_url('backend/plugins/select2/select2.full.min.js'); ?>"></script>
<!-- Global Select2-in-modal fix (auto-injects dropdownParent for any .select2() target inside a .modal) -->
<script src="<?php echo base_url('backend/js/sh-select2-modal-fix.js'); ?>"></script>

<!-- CKEditor (replaces bootstrap3-wysihtml5) -->
<!-- ckeditor.js auto-loads plugins/ckeditor/config.js (dark-aware: sets bodyClass=variant-b + keeps default contents.css). -->
<!-- ckeditor_config.js is Front-CMS-specific (font-awesome contentsCss, no dark bodyClass) and is loaded by -->
<!-- admin/front/pages/create.php & edit.php via customConfig — do NOT load it globally here or it clobbers the dark config. -->
<script src="<?php echo base_url('backend/plugins/ckeditor/ckeditor.js'); ?>"></script>
<!-- Syncs the editor iframe background/text to the live theme tokens (handles nightshift/midnight/oled/custom). -->
<script src="<?php echo base_url('backend/js/sh-ckeditor-theme.js'); ?>"></script>

<!-- Date/time picker — Tempus Dominus 6 (BS5 native, single library for all picker types) -->
<!-- Popper.js MUST be loaded BEFORE Tempus Dominus (TD 6 requires window.Popper global; -->
<!-- Bootstrap bundle includes Popper internally but does NOT expose it globally) -->
<?php
  $language_name = isset($this->session->userdata('hospitaladmin')['language']['language'])
      ? $this->session->userdata('hospitaladmin')['language']['language'] : 'english';
  // Map app language → TD 6 locale code (only for non-English; English is built-in default)
  $td_locale_map = [
      'arabic' => 'ar',  'spanish' => 'es', 'french' => 'fr', 'german' => 'de',
      'italian' => 'it', 'dutch' => 'nl',  'polish' => 'pl', 'portuguese' => 'pt-PT',
      'russian' => 'ru', 'turkish' => 'tr', 'chinese' => 'zh-CN'
  ];
  $td_locale_file = isset($td_locale_map[$language_name]) ? $td_locale_map[$language_name] : null;
?>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/popper.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/tempus-dominus.min.js'); ?>"></script>
<?php if ($td_locale_file && file_exists(FCPATH.'backend/plugins/tempus-dominus/js/locales/'.$td_locale_file.'.js')): ?>
<script src="<?php echo base_url('backend/plugins/tempus-dominus/js/locales/'.$td_locale_file.'.js'); ?>"></script>
<?php endif; ?>

<!-- Toastr notifications -->
<script src="<?php echo base_url('backend/toast-alert/toastr.js'); ?>"></script>

<!-- SweetAlert2 -->
<script src="<?php echo base_url('backend/fullcalendar/sweetalert2.all.js'); ?>"></script>

<!-- FullCalendar -->
<script src="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo base_url('backend/fullcalendar/dist/locale-all.js'); ?>"></script>

<!-- Hospital custom JS — BS5-compatible version -->
<script src="<?php echo base_url('backend/js/hospital-custom-bs5.js'); ?>?v=<?php echo filemtime(FCPATH.'backend/js/hospital-custom-bs5.js'); ?>"></script>

<!-- DataTables tooltip init (from original footer) -->
<script type="text/javascript">
$(document).ready(function () {
    $(".dt-body-right a").tooltip();
    // Event-delegated tooltip + popover init — handles DataTables AJAX rows, modals, and any other dynamically-injected content.
    // Bound to DIFFERENT ancestor elements because in BS5 Popover extends Tooltip and shares DATA_KEY storage, so binding both
    // to <body> silently drops the second one. <body> for tooltip, <html> (documentElement) for popover keeps them separated.
    try {
        if (!bootstrap.Tooltip.getInstance(document.body)) {
            new bootstrap.Tooltip(document.body, { selector: '[data-bs-toggle="tooltip"]' });
        }
    } catch (e) {}
    try {
        if (!bootstrap.Popover.getInstance(document.documentElement)) {
            new bootstrap.Popover(document.documentElement, { selector: '[data-bs-toggle="popover"]' });
        }
    } catch (e) {}
});
</script>

<!-- Global date/time picker initialization — Tempus Dominus 6 (settings-driven from sch_settings) -->
<script type="text/javascript">
(function () {
    // SETTINGS-DRIVEN VALUES (all from sch_settings table via Customlib)
    var phpDateFormat     = '<?php echo $this->customlib->getHospitalDateFormat(); ?>';                  // e.g., 'd-m-Y'
    var phpDateTimeFormat = '<?php echo $this->customlib->getHospitalDateFormat(true, true); ?>';        // 24h: 'd-m-Y H:i' / 12h: 'd-m-Y h:i A'
    var is24Hour          = <?php
        // Explicit string comparison — sch_settings.time_format stores "24-hour" / "12-hour" strings.
        // Both strings are PHP-truthy, so a simple boolean check would always evaluate to 24-hour.
        $tf = $this->customlib->getHospitalTimeFormat();
        echo ($tf === '24-hour' || $tf === true || $tf === 1 || $tf === '1') ? 'true' : 'false';
    ?>; // drives hourCycle config
    var tdLocale          = '<?php echo isset($td_locale_file) && $td_locale_file ? $td_locale_file : 'en-US'; ?>';

    // PHP → Tempus Dominus 6 format mapping (TD 6 has its OWN tokens, NOT Luxon).
    // Uses Unicode control chars (-	) as collision-proof sentinels.
    // CRITICAL: TD 6 uses 'T'/'t' for AM/PM (NOT 'A'/'a' which are literal chars).
    function phpToTd(fmt) {
        return fmt
            // Pass 1: PHP tokens → unique sentinel chars
            .replace(/A/g, '')          // PHP 'A' = AM/PM uppercase
            .replace(/a/g, '')          // PHP 'a' = am/pm lowercase
            .replace(/M(?![a-z])/g, '') // PHP 'M' = 3-letter month (Jan, Feb)
            .replace(/i/g, '')          // PHP 'i' = minutes
            .replace(/h/g, '')          // PHP 'h' = 12-hour
            .replace(/H/g, '')          // PHP 'H' = 24-hour
            .replace(/m/g, '')          // PHP 'm' = numeric month
            .replace(/d/g, '')          // PHP 'd' = day
            .replace(/Y/g, '	')          // PHP 'Y' = 4-digit year
            // Pass 2: sentinels → TD 6 tokens
            .replace(//g, 'T')          // → AM/PM uppercase
            .replace(//g, 't')          // → am/pm locale default
            .replace(//g, 'MMM')        // → 3-letter month name
            .replace(//g, 'mm')         // → minutes 2-digit
            .replace(//g, 'hh')         // → 12-hour 2-digit
            .replace(//g, 'HH')         // → 24-hour 2-digit
            .replace(//g, 'MM')         // → month 2-digit
            .replace(//g, 'dd')         // → day 2-digit
            .replace(/	/g, 'yyyy');      // → year 4-digit
    }
    var tdDateFormat     = phpToTd(phpDateFormat);
    var tdDateTimeFormat = phpToTd(phpDateTimeFormat);
    var tdHourCycle      = is24Hour ? 'h23' : 'h12';

    // Track initialized inputs to prevent re-init
    var initFlag = '_pickerInit';

    // Common base config for all picker variants
    function baseConfig(format) {
        return {
            allowInputToggle: true,  // input click bhi picker open karega (icon ke saath)
            container: document.body, // picker dropdown body level pe append (escapes modal stacking context — fixes z-index behind modal issue)
            localization: {
                format: format,
                locale: tdLocale
            },
            display: {
                buttons: { today: true, clear: true, close: true },
                theme: 'light',
                icons: {
                    type: 'icons',
                    time: 'fa fa-clock',
                    date: 'fa fa-calendar',
                    up: 'fa fa-arrow-up',
                    down: 'fa fa-arrow-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                }
            }
        };
    }

    // ---- DATE-ONLY (.date) — clock components disabled ----
    // Bridge: Tempus Dominus 6 fires 'change.td'; re-fire as legacy events so old handlers work.
    function attachDpChangeBridge(el) {
        el.addEventListener('change.td', function(e) {
            if (!window.jQuery) return;
            var detail = {date: e.detail && e.detail.date};
            $(el).trigger('dp.change', [detail]);   // Bootstrap Datetimepicker legacy
            $(el).trigger('changeDate', [detail]);  // Bootstrap Datepicker legacy
        });
    }

    function initDatePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateFormat);
        cfg.display.components = {
            calendar: true, decades: true, year: true, month: true, date: true,
            clock: false, hours: false, minutes: false, seconds: false
        };
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
        attachDpChangeBridge(el);
    }

    // ---- DATETIME (.datetime) — full date + time, hourCycle from sch_settings ----
    function initDateTimePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateTimeFormat);
        cfg.display.components = {
            calendar: true, decades: true, year: true, month: true, date: true,
            clock: true, hours: true, minutes: true, seconds: false
        };
        cfg.localization.hourCycle = tdHourCycle;
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
        attachDpChangeBridge(el);
    }

    // ---- TIME-ONLY (.timepicker) — calendar disabled, hourCycle from settings ----
    function initTimePicker(el) {
        if (el[initFlag]) return;
        var timeFormat = is24Hour ? 'HH:mm' : 'hh:mm T';
        var cfg = baseConfig(timeFormat);
        cfg.display.components = {
            calendar: false, decades: false, year: false, month: false, date: false,
            clock: true, hours: true, minutes: true, seconds: false
        };
        cfg.display.viewMode = 'clock';
        cfg.localization.hourCycle = tdHourCycle;
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
        attachDpChangeBridge(el);
    }

    // ---- DATERANGE (.daterange) — built-in range mode ----
    function initDateRangePicker(el) {
        if (el[initFlag]) return;
        var cfg = baseConfig(tdDateFormat);
        cfg.display.components = {
            calendar: true, decades: true, year: true, month: true, date: true,
            clock: false, hours: false, minutes: false, seconds: false
        };
        cfg.dateRange = true;
        cfg.multipleDatesSeparator = ' to ';
        el[initFlag] = new tempusDominus.TempusDominus(el, cfg);
        attachDpChangeBridge(el);
    }

    // Initialize on existing elements (page load)
    document.querySelectorAll('input.date').forEach(initDatePicker);
    document.querySelectorAll('input.datetime').forEach(initDateTimePicker);
    document.querySelectorAll('input.timepicker').forEach(initTimePicker);
    document.querySelectorAll('input.daterange').forEach(initDateRangePicker);

    // Event delegation for dynamically added inputs (preserves existing pattern from old footer)
    document.body.addEventListener('focusin', function (e) {
        var t = e.target;
        if (!(t instanceof HTMLInputElement)) return;
        if (t.classList.contains('date'))            initDatePicker(t);
        else if (t.classList.contains('datetime'))   initDateTimePicker(t);
        else if (t.classList.contains('timepicker')) initTimePicker(t);
        else if (t.classList.contains('daterange'))  initDateRangePicker(t);
    });

    // ----------------------------------------------------------------
    // PUBLIC API: window.SHPicker — modern helpers for per-page code
    // Use these instead of old jQuery API:
    //   OLD: $('#x').datepicker('update', date)        →  NEW: SHPicker.setDate('#x', date)
    //   OLD: $('#x').data('DateTimePicker').date()     →  NEW: SHPicker.getDate('#x')
    //   OLD: $('#x').timepicker('setTime', '09:00 AM') →  NEW: SHPicker.setDate('#x', '09:00 AM')
    //   OLD: $('#x').on('changeDate', fn)              →  NEW: SHPicker.onChange('#x', fn)
    // ----------------------------------------------------------------
    window.SHPicker = {
        getInstance: function(elOrSelector) {
            var el = (typeof elOrSelector === 'string')
                ? document.querySelector(elOrSelector) : elOrSelector;
            if (!el) return null;
            if (!el[initFlag]) {
                if (el.classList.contains('date'))            initDatePicker(el);
                else if (el.classList.contains('datetime'))   initDateTimePicker(el);
                else if (el.classList.contains('timepicker')) initTimePicker(el);
                else if (el.classList.contains('daterange'))  initDateRangePicker(el);
                else                                          initDatePicker(el); // fallback
            }
            return el[initFlag] || null;
        },
        setDate: function(elOrSelector, dateOrTimeStr) {
            var instance = this.getInstance(elOrSelector);
            if (!instance) return;
            var d;
            if (dateOrTimeStr instanceof Date) {
                d = dateOrTimeStr;
            } else if (typeof dateOrTimeStr === 'string' && /^\d{1,2}:\d{2}/.test(dateOrTimeStr)) {
                var m = dateOrTimeStr.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i);
                if (m) {
                    d = new Date();
                    var h = parseInt(m[1], 10), mi = parseInt(m[2], 10);
                    if (m[3]) {
                        var ampm = m[3].toUpperCase();
                        if (ampm === 'PM' && h < 12) h += 12;
                        if (ampm === 'AM' && h === 12) h = 0;
                    }
                    d.setHours(h, mi, 0, 0);
                }
            } else {
                d = new Date(dateOrTimeStr);
            }
            if (!d || isNaN(d)) return;
            instance.dates.setValue(new tempusDominus.DateTime(d));
        },
        getDate: function(elOrSelector) {
            var el = (typeof elOrSelector === 'string')
                ? document.querySelector(elOrSelector) : elOrSelector;
            if (!el) return null;
            var instance = el[initFlag];
            if (instance && instance.dates && instance.dates.lastPicked) {
                var picked = instance.dates.lastPicked;
                return (picked instanceof Date) ? picked : new Date(picked);
            }
            if (el.value) {
                var d = new Date(el.value);
                if (!isNaN(d)) return d;
            }
            return null;
        },
        setRestriction: function(elOrSelector, opts) {
            var instance = this.getInstance(elOrSelector);
            if (!instance) return;
            var restrictions = {};
            if (opts.minDate) restrictions.minDate = new tempusDominus.DateTime(opts.minDate);
            if (opts.maxDate) restrictions.maxDate = new tempusDominus.DateTime(opts.maxDate);
            instance.updateOptions({ restrictions: restrictions });
        },
        onChange: function(elOrSelector, callback) {
            var instance = this.getInstance(elOrSelector);
            if (!instance) return;
            var el = (typeof elOrSelector === 'string')
                ? document.querySelector(elOrSelector) : elOrSelector;
            instance.subscribe(tempusDominus.Namespace.events.change, function(e) {
                callback({ date: e.date, oldDate: e.oldDate, target: el });
            });
        }
    };

    // Backward-compat shim removed (2026-05-04) — all 104 view files migrated to modern SHPicker API.
    // Use SHPicker.setDate() / SHPicker.getDate() / SHPicker.onChange() / SHPicker.setRestriction()
    // for any new picker code instead of legacy jQuery $.fn.datepicker() / .data('DateTimePicker').
})();
</script>

<!-- Flash message from session (CI flash data → toastr) -->
<?php
  $flash_success = $this->session->flashdata('success');
  $flash_error   = $this->session->flashdata('error');
  $flash_info    = $this->session->flashdata('info');
  if (is_array($flash_success)) { $flash_success = implode(' ', array_filter($flash_success)); }
  if (is_array($flash_error))   { $flash_error   = implode(' ', array_filter($flash_error));   }
  if (is_array($flash_info))    { $flash_info     = implode(' ', array_filter($flash_info));    }
?>
<?php if ($flash_success): ?>
<script>$(function(){ successMsg('<?php echo addslashes($flash_success); ?>'); }); </script>
<?php endif; ?>
<?php if ($flash_error): ?>
<script>$(function(){ errorMsg('<?php echo addslashes($flash_error); ?>'); }); </script>
<?php endif; ?>
<?php if ($flash_info): ?>
<script>$(function(){ infoMsg('<?php echo addslashes($flash_info); ?>'); }); </script>
<?php endif; ?>

<!-- Notification polling — parity with patient panel: live count badge + toastr bubbles + sound -->
<?php if ($this->session->userdata('hospitaladmin')): ?>
<?php
/* Poll interval from hospital settings, default 60s (mirrors patient header) */
$_notif_setting = $this->setting_model->getHospitalDetail();
$_poll_seconds  = (!empty($_notif_setting->notification_poll_interval) && (int)$_notif_setting->notification_poll_interval > 0)
    ? (int)$_notif_setting->notification_poll_interval : 60;
$_poll_ms = $_poll_seconds * 1000;
?>
<!-- Notification bubble <style> block migrated to backend/css/sh-theme.css (2026-05-27) -->
<script type="text/javascript">
(function () {
    var POLL_MS   = <?php echo $_poll_ms; ?>;
    var POLL_URL  = baseurl + 'admin/systemnotification/pollNotifications';
    var NOTIF_URL = baseurl + 'admin/systemnotification';
    var LS_KEY    = 'admin_notif_last_check';

    var pageLoadTime  = new Date().toISOString();
    var stored        = localStorage.getItem(LS_KEY);
    var lastCheckTime = (stored && stored > pageLoadTime) ? stored : pageLoadTime;
    localStorage.setItem(LS_KEY, lastCheckTime);

    /* Single shared AudioContext. Mobile/desktop browsers start it SUSPENDED and
       only allow it to be unlocked inside a real user-gesture handler — a poll
       timer is not a gesture. So we create it once and resume() it on the first
       tap/click/keypress; after that the polled beep can play. (Previously a new,
       never-unlocked context was created per call, so mobile stayed silent.) */
    var shAudioCtx = null;
    function getAudioCtx() {
        if (!shAudioCtx) {
            try { shAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
            catch(e) { return null; }
        }
        return shAudioCtx;
    }
    function unlockAudio() {
        var ctx = getAudioCtx();
        if (!ctx) return;
        if (ctx.state === 'suspended') { ctx.resume(); }
        /* zero-volume blip fully unlocks the context on iOS Safari */
        try {
            var osc = ctx.createOscillator(), g = ctx.createGain();
            g.gain.value = 0; osc.connect(g); g.connect(ctx.destination);
            osc.start(); osc.stop(ctx.currentTime + 0.01);
        } catch(e) {}
        ['touchstart','touchend','click','keydown'].forEach(function(ev) {
            document.removeEventListener(ev, unlockAudio, true);
        });
    }
    ['touchstart','touchend','click','keydown'].forEach(function(ev) {
        document.addEventListener(ev, unlockAudio, true);
    });

    function playSound() {
        try {
            var ctx = getAudioCtx();
            if (!ctx) return;
            if (ctx.state === 'suspended') { ctx.resume(); }
            [0, 0.18].forEach(function(delay) {
                var osc  = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine'; osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.12, ctx.currentTime + delay);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
                osc.start(ctx.currentTime + delay);
                osc.stop(ctx.currentTime + delay + 0.3);
            });
        } catch(e) {}
    }

    function updateBadge(count) {
        var dot = document.getElementById('sh-notif-dot');
        if (!dot) return;
        if (count > 0) {
            dot.textContent = (count > 99) ? '99+' : count;
            dot.style.display = '';
        } else {
            dot.style.display = 'none';
        }
    }

    function isAlreadyShown(id) { return !!localStorage.getItem('a_notif_s_' + id); }
    function markAsShown(id) {
        localStorage.setItem('a_notif_s_' + id, '1');
        var keys = [];
        for (var k in localStorage) { if (k.indexOf('a_notif_s_') === 0) { keys.push(k); } }
        if (keys.length > 100) {
            keys.sort().slice(0, keys.length - 100).forEach(function(k) { localStorage.removeItem(k); });
        }
    }

    function escapeHtml(s) { return $('<div/>').text(s == null ? '' : String(s)).html(); }

    function getBubbleStage() {
        var stage = document.getElementById('sh-bubble-stage');
        if (!stage) { stage = document.createElement('div'); stage.id = 'sh-bubble-stage'; document.body.appendChild(stage); }
        return stage;
    }

    function makeBubble(opts) {
        var b = document.createElement('div');
        b.className = 'sh-bubble' + (opts.kind ? ' sh-bubble--' + opts.kind : '');
        var metaHtml = opts.viewHref
            ? '<div class="sh-bubble-meta"><span>just now</span><a href="' + opts.viewHref + '" class="sh-bubble-view">View</a></div>'
            : '';
        /* Message body is rendered as HTML (matches prior toastr behavior). Controllers
           send pre-formatted strings like "<p>Field required</p>" or "<br>"-separated
           multi-error lists. Title stays escaped — it's a fixed string from our code. */
        b.innerHTML =
            '<div class="sh-bubble-icon"><i class="' + (opts.iconClass || 'fa fa-bell') + '"></i></div>' +
            '<div class="sh-bubble-body">' +
                '<h4 class="sh-bubble-ttl">' + escapeHtml(opts.title) + '</h4>' +
                '<div class="sh-bubble-msg">' + (opts.message || '') + '</div>' +
                metaHtml +
            '</div>' +
            '<button class="sh-bubble-x" aria-label="Dismiss"><i class="fa fa-times"></i></button>';
        return b;
    }

    function presentBubble(b, dismissAfter) {
        var stage = getBubbleStage();
        stage.appendChild(b);
        requestAnimationFrame(function() { b.classList.add('show'); });
        var dur = dismissAfter || 8000;
        var dismissTimer = setTimeout(function() { dismissBubble(b); }, dur);
        b.addEventListener('mouseenter', function() { clearTimeout(dismissTimer); });
        b.addEventListener('mouseleave', function() { dismissTimer = setTimeout(function() { dismissBubble(b); }, 3000); });
        b.querySelector('.sh-bubble-x').addEventListener('click', function() { clearTimeout(dismissTimer); dismissBubble(b); });
    }

    function dismissBubble(b) { b.classList.remove('show'); setTimeout(function() { b.remove(); }, 300); }

    function showToast(notif) {
        if (isAlreadyShown(notif.id)) { return; }
        markAsShown(notif.id);
        presentBubble(makeBubble({
            iconClass: notif.icon_class || 'fa fa-bell',
            title:     notif.notification_title,
            message:   notif.notification_desc,
            viewHref:  NOTIF_URL
        }));
    }

    function poll() {
        $.ajax({
            type: 'POST', url: POLL_URL, data: { last_check: lastCheckTime }, dataType: 'json',
            success: function(res) {
                if (!res || res.status !== 1) { return; }
                updateBadge(res.unread_count);
                if (res.new_notifications && res.new_notifications.length > 0) {
                    var toShow = res.new_notifications.slice(0, 3);
                    var extra  = res.new_notifications.length - 3;
                    var hasNew = false;
                    $.each(toShow, function(i, n) { if (!isAlreadyShown(n.id)) { hasNew = true; } showToast(n); });
                    if (extra > 0) {
                        presentBubble(makeBubble({
                            iconClass: 'fa fa-bell',
                            title:     extra + ' more notifications',
                            message:   'Open the notification center to see all.',
                            viewHref:  NOTIF_URL
                        }));
                    }
                    if (hasNew) { playSound(); }
                    var latest   = res.new_notifications[res.new_notifications.length - 1];
                    var latestTs = new Date(latest.date.replace(' ', 'T') + 'Z');
                    latestTs.setSeconds(latestTs.getSeconds() + 1);
                    lastCheckTime = latestTs.toISOString().slice(0, 19).replace('T', ' ');
                    localStorage.setItem(LS_KEY, lastCheckTime);
                }
            }
        });
    }

    /* Override the global flash-message wrappers so save/edit/delete toasts use the
       sh-bubble system (Variant 1: title + message, bottom-right, type-colored icon
       block). Originally defined in hospital-custom-bs5.js as toastr wrappers. */
    window.successMsg = function(msg) {
        presentBubble(makeBubble({
            kind: 'success', iconClass: 'fa fa-check',
            title: 'Success', message: msg
        }), 4000);
    };
    window.errorMsg = function(msg) {
        presentBubble(makeBubble({
            kind: 'error', iconClass: 'fa fa-exclamation-circle',
            title: 'Error', message: msg
        }), 8000);
    };
    window.warningMsg = function(msg) {
        presentBubble(makeBubble({
            kind: 'warn', iconClass: 'fa fa-exclamation-triangle',
            title: 'Warning', message: msg
        }), 6000);
    };
    window.infoMsg = function(msg) {
        presentBubble(makeBubble({
            kind: 'info', iconClass: 'fa fa-info-circle',
            title: 'Heads up', message: msg
        }), 5000);
    };

    var timer;
    setTimeout(function() {
        poll();
        timer = setInterval(poll, POLL_MS);
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) { clearInterval(timer); }
            else { setTimeout(function() { poll(); }, 1500); timer = setInterval(poll, POLL_MS); }
        });
    }, 3000);
})();
</script>
<?php endif; ?>

<?php echo isset($extra_js) ? $extra_js : ''; ?>

<script>
/* Global popup() — opens print HTML in a hidden iframe and triggers the browser print dialog.
   Equivalent to the patient panel's layout/patient/footer.php version. */
if (typeof popup === 'undefined') {
    function popup(data) {
        var base_url = '<?php echo base_url(); ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow
            ? frame1[0].contentWindow
            : (frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument);
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title></title>');
        frameDoc.document.write('<link rel="preconnect" href="https://fonts.googleapis.com">');
        frameDoc.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap5/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/all.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/sh-print.css">');
        frameDoc.document.write('</head><body onload="window.print()">');
        frameDoc.document.write(data);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();
        setTimeout(function () { frame1.remove(); }, 3000);
        return true;
    }
}
</script>
<!-- Bed Status Modal (populated via getbedstatus() AJAX) -->
<div class="modal fade sh-modal sh-modal-accent" id="bedStatusModal" tabindex="-1" aria-labelledby="bedStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bedStatusModalLabel"><?php echo $this->lang->line('bed_status'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body" id="bed-status-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- License-registration modals (opened by .purchasemodal click or hospital-custom.js auto-show when SHLK is empty) -->
<?php $this->load->view('layout/routine_update'); ?>
<?php $this->load->view('layout/addon_update'); ?>

<!-- Multi-branch switch modal (trigger button is in header.php; same visibility guard) -->
<?php
$tb_userdata_role = $this->customlib->getUserData();
if (isset($tb_userdata_role['role_id']) && $tb_userdata_role['role_id'] == 7) {
    if (($this->module_lib->hasModule('multi_branch') && $this->module_lib->hasActive('multi_branch')) || $this->db->multi_branch) {
        $this->load->view('layout/multi_branch');
    }
}
?>

</body>
</html>

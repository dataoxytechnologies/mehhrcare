/* ============================================================
   SMART HOSPITAL · CHROME INJECTOR  (sh-chrome.js)
   Adapted from sh_new_theme/_chrome.js for live CodeIgniter app.
   Injects: left module rail + top bar + variant switcher.

   Usage: <body class="variant-a" data-chrome="dashboard|opd|ipd|pharmacy|billing|patients">
   ============================================================ */
(function () {
  /* ---- SVG icon library ---- */
  const ICON = {
    home:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    patients: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    opd:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    ipd:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>',
    pharmacy: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.5l8-8a4.94 4.94 0 1 1 7 7l-8 8a4.94 4.94 0 1 1-7-7Z"/><path d="m8.5 8.5 7 7"/></svg>',
    lab:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v7l-5 9a2 2 0 0 0 1.73 3h12.54A2 2 0 0 0 20 18L15 9V2"/><path d="M8 2h8M7 16h10"/></svg>',
    billing:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
    reports:  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>',
    analytics:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="15"/></svg>',
    settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    search:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
    bell:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>',
    help:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/></svg>',
    cal:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
    chat:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    plus:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>',
  };

  /* ---- Navigation rail items — hrefs map to CI routes ---- */
  const RAIL = [
    { id: 'home',     icon: 'home',     label: 'Home',     href: SH_BASE + 'admin/admin/dashboard' },
    { id: 'patients', icon: 'patients', label: 'Patients', href: SH_BASE + 'admin/patient' },
    { id: 'opd',      icon: 'opd',      label: 'OPD',      href: SH_BASE + 'admin/appointment' },
    { id: 'ipd',      icon: 'ipd',      label: 'IPD',      href: SH_BASE + 'admin/patient/ipdlist' },
    { id: 'pharmacy', icon: 'pharmacy', label: 'Pharmacy', href: SH_BASE + 'admin/pharmacy' },
    { id: 'lab',      icon: 'lab',      label: 'Lab',      href: SH_BASE + 'admin/pathology' },
    { sep: true },
    { id: 'billing',  icon: 'billing',  label: 'Billing',  href: SH_BASE + 'admin/bill' },
    { id: 'reports',  icon: 'reports',  label: 'Reports',  href: SH_BASE + 'admin/report' },
    { id: 'analytics',icon: 'analytics',label: 'Analytics',href: SH_BASE + 'admin/admin/dashboard' },
  ];

  /* ---- Module topbar config: which tabs appear per module ---- */
  const MODULES = {
    dashboard: { id: 'home',     icon: 'home',     label: 'Home',    tabs: [] },
    patients:  { id: 'patients', icon: 'patients', label: 'Patients',tabs: [] },
    opd:       { id: 'opd',      icon: 'opd',      label: 'OPD',     tabs: [
                   { l: 'All Visits',  a: false },
                   { l: 'Today',       a: true  },
                   { l: 'Waiting',     a: false },
                   { l: 'In-consult',  a: false },
                   { l: 'Completed',   a: false },
                 ] },
    ipd:       { id: 'ipd',      icon: 'ipd',      label: 'IPD',     tabs: [
                   { l: 'All Admissions',    a: false },
                   { l: 'Admitted',          a: true  },
                   { l: 'Ready to Discharge',a: false },
                   { l: 'Discharged',        a: false },
                 ] },
    pharmacy:  { id: 'pharmacy', icon: 'pharmacy', label: 'Pharmacy',tabs: [
                   { l: 'All Bills', a: false },
                   { l: 'Today',     a: true  },
                   { l: 'Pending',   a: false },
                   { l: 'Stock',     a: false },
                 ] },
    billing:   { id: 'billing',  icon: 'billing',  label: 'Invoices',tabs: [] },
    lab:       { id: 'lab',      icon: 'lab',       label: 'Lab',     tabs: [] },
    reports:   { id: 'reports',  icon: 'reports',   label: 'Reports', tabs: [] },
  };

  /* ---- Rail HTML builder ---- */
  function railHTML(active) {
    const items = RAIL.map(function (n) {
      if (n.sep) return '<div class="mrail-sep"></div>';
      const cls = (n.id === active) ? ' active' : '';
      return '<a class="mrail-item' + cls + '" href="' + n.href + '" title="' + n.label + '">' +
             ICON[n.icon] +
             '<span class="lbl">' + n.label + '</span>' +
             '</a>';
    }).join('');

    return [
      '<aside class="mrail">',
        '<div class="mrail-logo">',
          '<div class="lg">M</div>',
        '</div>',
        '<div class="mrail-modules">', items, '</div>',
        '<div class="mrail-foot">',
          '<a class="mrail-item" href="' + SH_BASE + 'admin/admin/setting" title="Settings">',
            ICON.settings,
            '<span class="lbl">Setup</span>',
          '</a>',
          '<div class="mrail-user"><div class="av" id="sh-rail-av">SA</div></div>',
        '</div>',
      '</aside>',
    ].join('');
  }

  /* ---- Topbar HTML builder ---- */
  function topbarHTML(moduleKey) {
    const mod = MODULES[moduleKey] || MODULES.dashboard;
    const tabs = (mod.tabs || []).map(function (t) {
      return '<a class="' + (t.a ? 'active' : '') + '" href="#">' + t.l + '</a>';
    }).join('');

    return [
      '<header class="topbar">',
        '<div class="tb-module">',
          '<div class="ic">', ICON[mod.icon], '</div>',
          mod.label,
          '<span class="caret">▾</span>',
        '</div>',
        '<nav class="tb-tabs">', tabs, '</nav>',
        '<div class="tb-right">',
          '<div class="tb-search">',
            ICON.search,
            '<input placeholder="Search (/)" />',
            '<kbd>/</kbd>',
          '</div>',
          '<div class="tb-divider"></div>',
          '<button class="tb-btn" title="Quick add">', ICON.plus, '</button>',
          '<button class="tb-btn" title="Calendar" onclick="window.location=\'' + SH_BASE + 'admin/appointment\'">', ICON.cal, '</button>',
          '<button class="tb-btn" title="Chat">', ICON.chat, '</button>',
          '<button class="tb-btn tb-notif" title="Notifications" id="sh-notif-btn">', ICON.bell, '<span class="dot" id="sh-notif-dot" style="display:none"></span></button>',
          '<button class="tb-btn" title="Help">', ICON.help, '</button>',
        '</div>',
      '</header>',
    ].join('');
  }

  function applyVariant(v) {
    document.body.classList.remove('variant-a', 'variant-b', 'variant-c');
    document.body.classList.add('variant-' + v);
    document.querySelectorAll('.sh-nav-variant-btn').forEach(function (b) {
      b.classList.toggle('active', b.dataset.v === v);
    });
    try { localStorage.setItem('sh-variant', v); } catch (e) {}
    if (typeof SH_BASE !== 'undefined') {
      var fd = new FormData();
      fd.append('variant', v);
      if (typeof SH_CSRF_NAME !== 'undefined') { fd.append(SH_CSRF_NAME, SH_CSRF_TOKEN); }
      fetch(SH_BASE + 'site/save_variant', { method: 'POST', body: fd });
    }
  }

  /* ---- Collapse/expand for .rellist sections ---- */
  function setupRelListCollapse() {
    document.querySelectorAll('.rellist-head').forEach(function (h) {
      h.addEventListener('click', function (e) {
        if (e.target.closest('button:not(.expand)')) return;
        h.parentElement.classList.toggle('collapsed');
      });
    });
  }

  /* ---- Mount everything ---- */
  function mount() {
    const root = document.querySelector('[data-chrome]');
    if (!root) { return; }

    var sidebar = document.querySelector('.sh-sidebar');

    setupRelListCollapse();

    /* Populate rail avatar initials from page if available */
    const avEl = document.getElementById('sh-rail-av');
    if (avEl && typeof SH_USER_INITIALS !== 'undefined') {
      avEl.textContent = SH_USER_INITIALS;
    }

  }

  // Expose globally so navbar onclick="applyVariant('x')" can reach it
  window.applyVariant = applyVariant;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();

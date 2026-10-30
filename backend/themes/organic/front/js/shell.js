/* ─────────────────────────────────────────────────────────
   Organic Wellness · shared chrome injector
   Injects topbar, header, theme-switch, footer into pages
   that declare <div data-shell="..."></div> placeholders.

   Single source-of-truth for nav copy and links lives in
   SH_NAV below. When porting to CodeIgniter 3 this maps
   1:1 to application/config/nav.php.
   ───────────────────────────────────────────────────────── */
(function () {
  'use strict';

  /* ── Nav data ──────────────────────────────────────────── */

  const SH_NAV = {
    brand: {
      name: 'Smart',
      sep: '·',
      tail: 'Hospital',
      tag: 'a kinder kind of visit',
    },
    topbar: {
      emergency: { label: 'Emergency · 24×7', phone: '1800-1025-511', tel: '18001025511' },
      links: [
        { key: 'portal',   label: 'Patient Portal', icon: 'user',     href: '#' },
        { key: 'reports',  label: 'Lab Reports',    icon: 'document', href: '#' },
        { key: 'doctors',  label: 'For Doctors',    icon: 'heart',    href: '#' },
        { key: 'careers',  label: 'Careers',        icon: 'briefcase',href: '#' },
      ],
      languages: ['EN', 'हिं', 'मरा'],
    },
    primary: [
      {
        key: 'care', label: 'Care', megaSize: 'mega-3',
        columns: [
          {
            head: 'Departments',
            items: [
              { emoji:'❤', bg:'#FCE2DA', color:'#C75A40', title:'Cardiology',       sub:'14 specialists · cath lab II',        href:'departments.html' },
              { emoji:'🦴', bg:'#E4ECDC', color:'#3F5040', title:'Orthopaedics',     sub:'9 specialists · joint replacement',   href:'departments.html' },
              { emoji:'👁', bg:'#E2EEEB', color:'#1E4549', title:'Ophthalmology',    sub:'7 specialists · LASIK · paediatric',  href:'departments.html' },
              { emoji:'🦷', bg:'#FBE6CB', color:'#8C5C16', title:'Dental Surgery',   sub:'11 specialists · sedation dentistry', href:'departments.html' },
              { emoji:'👂', bg:'#FCE2DA', color:'#C75A40', title:'ENT & Head-Neck',  sub:'6 specialists · cochlear implant',    href:'departments.html' },
              { emoji:'🩺', bg:'#E4ECDC', color:'#3F5040', title:'General Physician',sub:'22 doctors · walk-in OPD',            href:'departments.html' },
            ],
            more: { label: 'View all 14 departments →', href: 'departments.html' },
          },
          {
            head: 'Diagnostics & Labs',
            items: [
              { emoji:'🧪', bg:'#E4ECDC', color:'#3F5040', title:'Pathology Lab',          sub:'220+ tests · NABL accredited',     href:'#' },
              { emoji:'🩻', bg:'#E2EEEB', color:'#1E4549', title:'Radiology & Imaging',    sub:'3T MRI · 256-slice CT · X-ray',    href:'#' },
              { emoji:'🫀', bg:'#FBE6CB', color:'#8C5C16', title:'Cardiac Diagnostics',    sub:'ECG · Echo · TMT · Holter',        href:'#' },
              { emoji:'🧬', bg:'#FCE2DA', color:'#C75A40', title:'Health Check-ups',       sub:"Master · Cardiac · Women's",       href:'#' },
              { emoji:'🚑', bg:'#E4ECDC', color:'#3F5040', title:'Home Sample Collection', sub:'Free pickup · 2 hr slots',         href:'#' },
            ],
            more: { label: 'All 220+ tests →', href: '#' },
          },
          {
            type: 'feature',
            featCard: {
              tag: 'Featured · this month', title: 'Comprehensive Health Check-up',
              desc: '68 tests, doctor consult, report walk-through. Slots from 7 am.',
              price: '₹1,499', priceLabel: 'From',
              cta: 'Book →', href: 'appointment.html',
            },
            featStat: { big: '96%', small: 'same-day appointments fulfilled' },
          },
        ],
      },
      {
        key: 'patients', label: 'Patients', megaSize: 'mega-3',
        columns: [
          {
            head: 'Visit us',
            items: [
              { emoji:'📅', bg:'#E4ECDC', color:'#3F5040', title:'Book an appointment', sub:'Live slots · no payment to book', href:'appointment.html' },
              { emoji:'🔍', bg:'#FCE2DA', color:'#C75A40', title:'Find a doctor',       sub:'By name, specialty or city',      href:'departments.html' },
              { emoji:'🏥', bg:'#FBE6CB', color:'#8C5C16', title:'Our locations',       sub:'3 hospitals · 12 clinics',        href:'contact.html' },
              { emoji:'💬', bg:'#E2EEEB', color:'#1E4549', title:'Telemedicine',        sub:'Video consult from home',         href:'#' },
            ],
          },
          {
            head: 'Self-service',
            items: [
              { emoji:'👤', bg:'#E4ECDC', color:'#3F5040', title:'Patient portal',  sub:'Records, history, family profiles', href:'#' },
              { emoji:'📄', bg:'#FCE2DA', color:'#C75A40', title:'Lab reports',     sub:'Download or share via WhatsApp',    href:'#' },
              { emoji:'💊', bg:'#FBE6CB', color:'#8C5C16', title:'E-prescriptions', sub:'Refill in two taps',                 href:'#' },
              { emoji:'🏷', bg:'#E2EEEB', color:'#1E4549', title:'Insurance & TPA', sub:'Cashless on 38 insurers',           href:'#' },
            ],
          },
          {
            type: 'feature',
            featCard: {
              variant: 'alt',
              tag: 'Patient Portal', title: "Sign in to manage your family's care.",
              desc: 'All records, prescriptions, and reports — for you and your dependants.',
              cta: 'Sign in →', href: '#',
            },
          },
        ],
      },
      {
        key: 'about', label: 'About', megaSize: 'mega-2',
        columns: [
          {
            head: 'The hospital',
            items: [
              { emoji:'🌿', bg:'#E4ECDC', color:'#3F5040', title:'About us',                sub:'A 63-year-old practice in Mumbai', href:'about.html' },
              { emoji:'🎯', bg:'#FCE2DA', color:'#C75A40', title:'Vision & Mission',        sub:'What we measure ourselves by',     href:'vision-mission.html' },
              { emoji:'🏆', bg:'#FBE6CB', color:'#8C5C16', title:'Awards & Accreditation',  sub:'NABH · NABL · JCI',                href:'#' },
              { emoji:'📰', bg:'#E2EEEB', color:'#1E4549', title:'Press & Newsroom',        sub:'Coverage and announcements',       href:'#' },
            ],
          },
          {
            head: 'Stories',
            items: [
              { emoji:'🖼', bg:'#E4ECDC', color:'#3F5040', title:'Gallery',       sub:'Inside our hospitals & clinics', href:'gallery.html' },
              { emoji:'💬', bg:'#FCE2DA', color:'#C75A40', title:'Testimonials',  sub:'Honest words from real visits',  href:'testimonials.html' },
              { emoji:'❓', bg:'#FBE6CB', color:'#8C5C16', title:'FAQs',          sub:'The things people actually ask', href:'faqs.html' },
              { emoji:'📍', bg:'#E2EEEB', color:'#1E4549', title:'Contact us',    sub:'Address, phone, departments',    href:'contact.html' },
            ],
          },
        ],
      },
      {
        key: 'resources', label: 'Resources', megaSize: 'mega-3',
        columns: [
          {
            head: "What's on",
            items: [
              { emoji:'📅', bg:'#E4ECDC', color:'#3F5040', title:'Annual calendar', sub:'Camps, drives, screenings',     href:'calendar.html' },
              { emoji:'🎤', bg:'#FCE2DA', color:'#C75A40', title:'Events & lectures',sub:'Public talks & workshops',     href:'events.html' },
              { emoji:'🎓', bg:'#FBE6CB', color:'#8C5C16', title:'CME & fellowships',sub:'For practising clinicians',    href:'#' },
            ],
          },
          {
            head: 'Read & learn',
            items: [
              { emoji:'📚', bg:'#E4ECDC', color:'#3F5040', title:'Health library',  sub:'Conditions explained simply', href:'#' },
              { emoji:'📝', bg:'#FCE2DA', color:'#C75A40', title:'The Smart blog',  sub:'Notes from our doctors',      href:'blog.html' },
              { emoji:'🍎', bg:'#E2EEEB', color:'#1E4549', title:'Diet & nutrition',sub:'Plans by our dietitians',     href:'#' },
            ],
          },
          {
            type: 'feature',
            featCard: {
              variant: 'upcoming',
              tag: 'Upcoming · 22 May', title: 'Living with diabetes — public lecture',
              desc: 'Dr P. Mehta · 6:30 pm · Auditorium I · Free admission',
              cta: 'Reserve a seat →', href: 'events.html',
            },
          },
        ],
      },
      { key: 'contact', label: 'Contact', href: 'contact.html' },
    ],
    cta: {
      phone: '1800-1025-511', tel: '18001025511',
      book: { label: 'Book a visit', href: 'appointment.html' },
    },
    footer: {
      blurb: 'A multi-speciality network and diagnostic chain across Mumbai, Pune, Thane & Vasai. NABH + NABL accredited.',
      newsletter: { placeholder: 'Your email', button: 'Subscribe', fine: 'A monthly note. No marketing nonsense.' },
      cols: [
        { head: 'Care',      links: [
          { label: 'Departments',      href: 'departments.html' },
          { label: 'Doctors',          href: 'departments.html#doctors' },
          { label: 'Appointment',      href: 'appointment.html' },
          { label: 'Annual calendar',  href: 'calendar.html' },
          { label: 'Events',           href: 'events.html' },
        ]},
        { head: 'Hospital',  links: [
          { label: 'About',             href: 'about.html' },
          { label: 'Vision & mission',  href: 'vision-mission.html' },
          { label: 'Gallery',           href: 'gallery.html' },
          { label: 'Testimonials',      href: 'testimonials.html' },
          { label: 'FAQs',              href: 'faqs.html' },
        ]},
        { head: 'Practical', links: [
          { label: 'Patient portal',    href: '#' },
          { label: 'Lab reports',       href: '#' },
          { label: 'Insurance & TPA',   href: '#' },
          { label: 'Careers',           href: '#' },
          { label: 'Contact',           href: 'contact.html' },
        ]},
        { head: 'Find us',   address: 'Andheri West, Mumbai — 400 058<br/>+91 22 4000 1962<br/>care@smarthospital.in' },
      ],
      bottom: ['© 2026 Smart Hospital · Designed in Mumbai with care.', 'Set in Plus Jakarta Sans.'],
    },
  };

  /* ── Icon SVGs ─────────────────────────────────────────── */

  const ICONS = {
    user:      '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    document:  '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>',
    heart:     '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/></svg>',
    briefcase: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
    chev:      '<svg class="chev" width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>',
    chevDown:  '<svg width="9" height="9" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>',
    phone:     '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    arrow:     '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>',
    arrowSm:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>',
    logo:      '<svg class="logo" viewBox="0 0 56 56" aria-hidden="true"><path d="M10 28 q9 -22 18 0 q9 22 18 0" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/><circle cx="28" cy="28" r="3.2" fill="currentColor"/></svg>',
  };

  /* ── Renderers ─────────────────────────────────────────── */

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  }[c]));

  function renderTopbar() {
    const { emergency, links, languages } = SH_NAV.topbar;
    const linksHtml = links.map((l) => `
      <a href="${esc(l.href)}" data-key="${esc(l.key)}">
        ${ICONS[l.icon] || ''}
        ${esc(l.label)}
      </a>`).join('');
    const langsHtml = languages.map((lang, i) =>
      i === 0 ? esc(lang) : `<span class="lang-sep">·</span> ${esc(lang)}`
    ).join(' ');
    return `
      <div class="topbar">
        <div class="container topbar-row">
          <a class="topbar-emerg" href="tel:${esc(emergency.tel)}">
            <span class="pulse-dot"></span>
            <strong>${esc(emergency.label)}</strong>
            <span class="sep">·</span>
            <span>${esc(emergency.phone)}</span>
          </a>
          <nav class="topbar-links" aria-label="Quick links">
            ${linksHtml}
            <button class="lang">
              ${langsHtml}
              ${ICONS.chevDown}
            </button>
          </nav>
        </div>
      </div>`;
  }

  function renderMegaItem(it) {
    const style = `background:${esc(it.bg)};color:${esc(it.color)}`;
    return `
      <a class="mega-item" href="${esc(it.href)}">
        <span class="mi-icon" style="${style}">${esc(it.emoji)}</span>
        <span class="mi-text">
          <strong>${esc(it.title)}</strong>
          <em>${esc(it.sub)}</em>
        </span>
      </a>`;
  }

  function renderMegaColumn(col) {
    if (col.type === 'feature') {
      const f = col.featCard;
      const variantClass = f.variant ? ` ${esc(f.variant)}` : '';
      const meta = (f.price)
        ? `<div class="feat-meta">
             <span class="feat-price">${esc(f.priceLabel || 'From')} <strong>${esc(f.price)}</strong></span>
             <span class="feat-cta">${esc(f.cta)}</span>
           </div>`
        : `<span class="feat-cta">${esc(f.cta)}</span>`;
      const stat = col.featStat
        ? `<div class="feat-stat"><span>${esc(col.featStat.big)}</span><small>${esc(col.featStat.small)}</small></div>`
        : '';
      return `
        <div class="mega-col mega-feat">
          <a class="feat-card${variantClass}" href="${esc(f.href)}">
            <span class="feat-tag">${esc(f.tag)}</span>
            <strong class="feat-title">${esc(f.title)}</strong>
            <em class="feat-desc">${esc(f.desc)}</em>
            ${meta}
          </a>
          ${stat}
        </div>`;
    }
    const items = col.items.map(renderMegaItem).join('');
    const more = col.more
      ? `<a class="mega-more" href="${esc(col.more.href)}">${esc(col.more.label)}</a>`
      : '';
    return `
      <div class="mega-col">
        <h6 class="mega-head">${esc(col.head)}</h6>
        ${items}
        ${more}
      </div>`;
  }

  function renderNavItem(item, activeKey) {
    const isActive = item.key === activeKey ? ' active' : '';
    if (!item.columns) {
      return `
        <div class="nav-item${isActive}">
          <a href="${esc(item.href || '#')}" class="nav-link">${esc(item.label)}</a>
        </div>`;
    }
    const mega = `
      <div class="mega ${esc(item.megaSize)}">
        ${item.columns.map(renderMegaColumn).join('')}
      </div>`;
    return `
      <div class="nav-item has-mega${isActive}">
        <a href="#" class="nav-link">
          ${esc(item.label)}
          ${ICONS.chev}
        </a>
        ${mega}
      </div>`;
  }

  function renderHeader(activeKey) {
    const { brand, primary, cta } = SH_NAV;
    const navHtml = primary.map((it) => renderNavItem(it, activeKey)).join('');
    return `
      <header class="hdr">
        <div class="container hdr-row">
          <a href="index.html" class="brand">
            ${ICONS.logo}
            <span class="brand-text">
              <span class="wmark">${esc(brand.name)}<span>${esc(brand.sep)}</span>${esc(brand.tail)}</span>
              <span class="wmark-tag">${esc(brand.tag)}</span>
            </span>
          </a>
          <nav class="nav" aria-label="Primary">${navHtml}</nav>
          <div class="hdr-cta">
            <a href="tel:${esc(cta.tel)}" class="phone">
              ${ICONS.phone}
              ${esc(cta.phone)}
            </a>
            <a href="${esc(cta.book.href)}" class="cta-blob">
              ${esc(cta.book.label)}
              ${ICONS.arrow}
            </a>
          </div>
        </div>
      </header>`;
  }

  function renderThemeSwitch() {
    return `
      <aside class="theme-switch" aria-label="Colour theme">
        <span class="ts-label">palette</span>
        <button data-set="garden"   style="--c:#3F5040" aria-label="Garden — green"></button>
        <button data-set="pebble"   style="--c:#3A4540" aria-label="Pebble — slate"></button>
        <button data-set="sky"      style="--c:#1E4549" aria-label="Sky — teal"></button>
        <button data-set="midnight" style="--c:#15191A" aria-label="Midnight — dark"></button>
      </aside>`;
  }

  function renderFooter() {
    const f = SH_NAV.footer;
    const cols = f.cols.map((col) => {
      if (col.address) {
        return `<div><h5>${esc(col.head)}</h5><p>${col.address}</p></div>`;
      }
      const links = col.links.map((l) =>
        `<a href="${esc(l.href)}">${esc(l.label)}</a>`).join('');
      return `<div><h5>${esc(col.head)}</h5>${links}</div>`;
    }).join('');
    const brand = SH_NAV.brand;
    return `
      <footer class="ftr">
        <div class="container">
          <div class="ftr-top">
            <div class="ftr-brand">
              <div class="brand">
                ${ICONS.logo}
                <span class="wmark">${esc(brand.name)}<span>${esc(brand.sep)}</span>${esc(brand.tail)}</span>
              </div>
              <p>${esc(f.blurb)}</p>
              <div class="newsletter">
                <input type="email" placeholder="${esc(f.newsletter.placeholder)}"/>
                <button>${esc(f.newsletter.button)}</button>
              </div>
              <span class="news-fine">${esc(f.newsletter.fine)}</span>
            </div>
            <div class="cols">${cols}</div>
          </div>
          <div class="ftr-bot">
            <span>${esc(f.bottom[0])}</span>
            <span>${esc(f.bottom[1])}</span>
          </div>
        </div>
        <span class="ftr-blob" aria-hidden="true"></span>
      </footer>`;
  }

  /* ── Mounters ──────────────────────────────────────────── */

  function inject(slot, html) {
    const el = document.querySelector(`[data-shell="${slot}"]`);
    if (!el) return;
    el.outerHTML = html;
  }

  function mountChrome() {
    const activeKey = document.body.getAttribute('data-page-nav') || '';
    inject('topbar',       renderTopbar());
    inject('header',       renderHeader(activeKey));
    inject('theme-switch', renderThemeSwitch());
    inject('footer',       renderFooter());
  }

  /* ── Theme switcher ────────────────────────────────────── */

  function wireThemeSwitch() {
    const root = document.documentElement;
    const buttons = document.querySelectorAll('.theme-switch button');
    if (!buttons.length) return;
    const setActive = (theme) => {
      root.setAttribute('data-theme', theme);
      buttons.forEach((b) => b.classList.toggle('on', b.dataset.set === theme));
      try { localStorage.setItem('sh-theme', theme); } catch (e) {}
    };
    buttons.forEach((b) => b.addEventListener('click', () => setActive(b.dataset.set)));
    let initial = root.getAttribute('data-theme') || 'garden';
    try {
      const saved = localStorage.getItem('sh-theme');
      if (saved) initial = saved;
    } catch (e) {}
    setActive(initial);
  }

  /* ── Page-level filter handlers ───────────────────────── */

  function wireFilters() {
    document.querySelectorAll('[data-filter-root]').forEach((root) => {
      const chips = root.querySelectorAll('[data-filter-chip]');
      const items = root.querySelectorAll('[data-filter-item]');
      if (!chips.length || !items.length) return;
      const apply = (cat) => {
        chips.forEach((c) => c.classList.toggle('is-active', c.dataset.filterChip === cat));
        items.forEach((it) => {
          const cats = (it.dataset.filterItem || '').split(/\s+/);
          const show = cat === 'all' || cats.includes(cat);
          it.style.display = show ? '' : 'none';
        });
      };
      chips.forEach((c) => c.addEventListener('click', (e) => {
        e.preventDefault();
        apply(c.dataset.filterChip);
      }));
      apply('all');
    });
  }

  /* ── FAQ category tabs (separate from filter chips: scopes
        accordion list to the active category) ───────────── */

  function wireFaqTabs() {
    document.querySelectorAll('[data-faq-tabs]').forEach((tabsRoot) => {
      const tabs = tabsRoot.querySelectorAll('[data-faq-tab]');
      const groups = document.querySelectorAll('[data-faq-group]');
      if (!tabs.length || !groups.length) return;
      const show = (cat) => {
        tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.faqTab === cat));
        groups.forEach((g) => {
          g.style.display = (cat === 'all' || g.dataset.faqGroup === cat) ? '' : 'none';
        });
      };
      tabs.forEach((t) => t.addEventListener('click', (e) => {
        e.preventDefault();
        show(t.dataset.faqTab);
      }));
      show('all');
    });
  }

  /* ── Boot ──────────────────────────────────────────────── */

  function boot() {
    mountChrome();
    wireThemeSwitch();
    wireFilters();
    wireFaqTabs();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  // Expose for debugging / future use.
  window.SH_NAV = SH_NAV;
})();

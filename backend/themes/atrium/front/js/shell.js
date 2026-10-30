/* Atrium · shared shell (topbar + header + footer + theme switcher)
   Each page stays slim by including this script and these mount points:
     <div data-shell="topbar"></div>
     <div data-shell="header"></div>
     <div data-shell="footer"></div>
   Active nav state is driven by <body data-page="…"> values:
     home | specialties | departments | doctors | patients | locations
     resources | events | gallery | calendar | testimonials | faq
     about | vision | contact | appointment
*/
(function () {
  // ── Theme switcher (floating) ────────────────────────────
  const themeHTML = `
  <aside class="theme-switch" aria-label="Colour theme">
    <span class="ts-label">Theme</span>
    <div class="ts-row">
      <button data-set="aurora"  style="--c:#0BAA8E" aria-label="Aurora — fresh teal"></button>
      <button data-set="saffron" style="--c:#C97B1F" aria-label="Saffron — warm amber"></button>
      <button data-set="royal"   style="--c:#5746E2" aria-label="Royal — indigo"></button>
      <button data-set="onyx"    style="--c:#0B0F12" aria-label="Onyx — dark"></button>
    </div>
  </aside>`;

  // ── Topbar ───────────────────────────────────────────────
  const topbarHTML = `
  <div class="topbar">
    <div class="container topbar-row">
      <div class="tb-l">
        <span class="tb-item">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          Andheri West, Mumbai
        </span>
        <span class="vsep"></span>
        <span class="tb-item">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          Open today · 08:00–22:00 · OPD wait 4 min
        </span>
      </div>
      <div class="tb-r">
        <a class="tb-link" href="./appointment.html">Patient portal</a>
        <a class="tb-link" href="#">Lab reports</a>
        <a class="tb-link" href="#">Insurance</a>
        <a class="tb-link" href="#">EN · हिं · मरा</a>
        <a class="tb-emerg" href="tel:18001025511">
          <span class="tb-pulse"></span>
          Emergency · 1800·1025·511
        </a>
      </div>
    </div>
  </div>`;

  // ── Header / nav ─────────────────────────────────────────
  // Each top-level nav item has a key, used to apply the .active class.
  function header(activeKey) {
    const k = (key) => activeKey === key ? 'active' : '';
    return `
    <header class="hdr">
      <div class="container hdr-row">
        <a href="./index.html" class="brand">
          <span class="brand-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3v18M3 12h18"/>
            </svg>
          </span>
          <span class="brand-text">
            <span class="brand-name">Smart Hospital</span>
            <span class="brand-tag">Multi-speciality · since 1962</span>
          </span>
        </a>

        <nav class="nav" aria-label="Primary">
          <div class="nav-item ${k('specialties')} has-mega">
            <a href="./departments.html" class="nav-link">Specialties
              <svg class="chev" width="9" height="9" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>
            </a>
            <div class="mega mega-3">
              <div class="mega-col">
                <h6 class="mega-head">Departments</h6>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></span><span class="mt"><strong>Cardiology</strong><em>14 specialists · Cath lab II</em></span></a>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12s3-7 9-7 9 7 9 7-3 7-9 7-9-7-9-7z"/><circle cx="12" cy="12" r="3"/></svg></span><span class="mt"><strong>Neurology</strong><em>9 specialists · 24/7 stroke unit</em></span></a>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V8M18 22V8M6 8a3 3 0 0 1-3-3V3h6v2a3 3 0 0 1-3 3zM18 8a3 3 0 0 1-3-3V3h6v2a3 3 0 0 1-3 3z"/></svg></span><span class="mt"><strong>Orthopaedics</strong><em>11 specialists · MAKO robotic</em></span></a>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12s3-7 9-7 9 7 9 7-3 7-9 7-9-7-9-7z"/><circle cx="12" cy="12" r="3"/></svg></span><span class="mt"><strong>Ophthalmology</strong><em>7 specialists · Femto LASIK</em></span></a>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6M12 16v6M2 12h6M16 12h6"/></svg></span><span class="mt"><strong>Oncology</strong><em>16 specialists · LINAC suite</em></span></a>
              </div>
              <div class="mega-col">
                <h6 class="mega-head">Diagnostics &amp; care</h6>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></span><span class="mt"><strong>Pathology Lab</strong><em>220+ tests · NABL</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span><span class="mt"><strong>Radiology</strong><em>3T MRI · 256-slice CT · PET</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="3"/><path d="M19 11c0 7-7 12-7 12s-7-5-7-12a7 7 0 0 1 14 0z"/></svg></span><span class="mt"><strong>Health Check-ups</strong><em>Master · Cardiac · Women's</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span class="mt"><strong>Home Collection</strong><em>Free · 2-hour slots</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg></span><span class="mt"><strong>Telemedicine</strong><em>Video consult · 7am–11pm</em></span></a>
              </div>
              <a class="mega-feat" href="./calendar.html">
                <span class="ftag">Featured · this month</span>
                <span class="ftit">Comprehensive Health Check</span>
                <span class="fdesc">68 tests, doctor consult and a full report walk-through. Same-day reports.</span>
                <span class="fbar"><em>From</em><strong>₹1,499</strong></span>
              </a>
            </div>
          </div>

          <div class="nav-item ${k('patients')} has-mega">
            <a href="./appointment.html" class="nav-link">Patients
              <svg class="chev" width="9" height="9" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>
            </a>
            <div class="mega mega-2">
              <div class="mega-col">
                <h6 class="mega-head">Visit</h6>
                <a class="mega-item" href="./appointment.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg></span><span class="mt"><strong>Book appointment</strong><em>Live slots · no upfront pay</em></span></a>
                <a class="mega-item" href="./departments.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6"/><path d="M21 21l-5.5-5.5"/></svg></span><span class="mt"><strong>Find a doctor</strong><em>By name, specialty, city</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg></span><span class="mt"><strong>Telemedicine</strong><em>Video consult from home</em></span></a>
              </div>
              <div class="mega-col">
                <h6 class="mega-head">Self-service</h6>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 14h6M9 18h4"/></svg></span><span class="mt"><strong>Lab reports</strong><em>WhatsApp · email · download</em></span></a>
                <a class="mega-item" href="#"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 22a8 8 0 0 1 16 0"/></svg></span><span class="mt"><strong>Patient portal</strong><em>Records, history, family</em></span></a>
                <a class="mega-item" href="./faqs.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r="0.6"/></svg></span><span class="mt"><strong>FAQs &amp; insurance</strong><em>Cashless on 38 insurers</em></span></a>
              </div>
            </div>
          </div>

          <div class="nav-item ${k('about')} has-mega">
            <a href="./about.html" class="nav-link">About
              <svg class="chev" width="9" height="9" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>
            </a>
            <div class="mega mega-2">
              <div class="mega-col">
                <h6 class="mega-head">The hospital</h6>
                <a class="mega-item" href="./about.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V8l9-5 9 5v13"/><path d="M9 21V12h6v9"/></svg></span><span class="mt"><strong>About us</strong><em>Story · leadership · numbers</em></span></a>
                <a class="mega-item" href="./vision-mission.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg></span><span class="mt"><strong>Vision &amp; mission</strong><em>Why we exist · how we work</em></span></a>
                <a class="mega-item" href="./testimonials.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span><span class="mt"><strong>Testimonials</strong><em>14,200 verified stories</em></span></a>
              </div>
              <div class="mega-col">
                <h6 class="mega-head">Engage</h6>
                <a class="mega-item" href="./events.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg></span><span class="mt"><strong>Events</strong><em>Camps, talks, screenings</em></span></a>
                <a class="mega-item" href="./calendar.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg></span><span class="mt"><strong>Annual calendar</strong><em>2026 health awareness diary</em></span></a>
                <a class="mega-item" href="./blog.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 14h6M9 18h4"/></svg></span><span class="mt"><strong>Reads</strong><em>Articles by our doctors</em></span></a>
                <a class="mega-item" href="./gallery.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/></svg></span><span class="mt"><strong>Gallery</strong><em>Pavilions, people, moments</em></span></a>
                <a class="mega-item" href="./faqs.html"><span class="mega-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><circle cx="12" cy="17" r="0.6"/></svg></span><span class="mt"><strong>FAQs</strong><em>Answers in plain language</em></span></a>
              </div>
            </div>
          </div>

          <div class="nav-item ${k('events')}"><a href="./events.html" class="nav-link">Events</a></div>
          <div class="nav-item ${k('contact')}"><a href="./contact.html" class="nav-link">Contact</a></div>
        </nav>

        <div class="hdr-cta">
          <button class="btn-search" aria-label="Search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M21 21l-5.5-5.5"/></svg>
          </button>
          <a href="./appointment.html" class="btn-fill">Book a visit
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </header>`;
  }

  // ── Footer ───────────────────────────────────────────────
  const footerHTML = `
  <footer class="ftr">
    <div class="container">
      <div class="ftr-grid">
        <div class="ftr-mast">
          <a href="./index.html" class="brand">
            <span class="brand-mark">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg>
            </span>
            <span class="brand-text">
              <span class="brand-name">Smart Hospital</span>
              <span class="brand-tag">Multi-speciality · since 1962</span>
            </span>
          </a>
          <p class="ftr-tag">A multi-speciality hospital network and diagnostic chain across Mumbai, Pune, Thane &amp; Vasai. NABH and NABL accredited; quarterly outcomes published.</p>
          <ul class="ftr-press">
            <li>NABH</li><li>JCI</li><li>NABL</li><li>ISO 9001</li><li>CAHO India</li>
          </ul>
        </div>

        <div class="ftr-col">
          <h5>Care</h5>
          <a href="./departments.html">Departments</a>
          <a href="./departments.html">Doctors</a>
          <a href="./appointment.html">Appointments</a>
          <a href="./calendar.html">Health checks</a>
          <a href="./calendar.html">Annual calendar</a>
        </div>
        <div class="ftr-col">
          <h5>Patients</h5>
          <a href="#">Patient portal</a>
          <a href="#">Lab reports</a>
          <a href="./faqs.html">Insurance &amp; TPA</a>
          <a href="./faqs.html">FAQs</a>
          <a href="#">Estimates</a>
        </div>
        <div class="ftr-col">
          <h5>Hospital</h5>
          <a href="./about.html">About</a>
          <a href="./vision-mission.html">Vision &amp; mission</a>
          <a href="./events.html">Events</a>
          <a href="./gallery.html">Gallery</a>
          <a href="./blog.html">Reads</a>
          <a href="./testimonials.html">Testimonials</a>
        </div>
        <div class="ftr-col">
          <h5>Reach us</h5>
          <p>8, Veera Desai, Andheri W<br/>Mumbai 400 058</p>
          <p><strong>+91 22 4000 1962</strong><br/>care@smarthospital.in</p>
          <p>24×7 emergency<br/><strong>1800·1025·511</strong></p>
        </div>
      </div>

      <div class="ftr-bot">
        <span>© 2026 Smart Hospital · NABH &amp; NABL accredited.</span>
        <span>
          <a href="#">Privacy</a>
          <a href="#">Terms</a>
          <a href="#">Patients' rights</a>
          <a href="#">Sitemap</a>
        </span>
      </div>
    </div>
  </footer>`;

  // ── Mount ────────────────────────────────────────────────
  function mount() {
    const page = document.body.dataset.page || '';
    const t  = document.querySelector('[data-shell="topbar"]');
    const h  = document.querySelector('[data-shell="header"]');
    const f  = document.querySelector('[data-shell="footer"]');
    const ts = document.querySelector('[data-shell="theme"]');
    if (t)  t.outerHTML  = topbarHTML;
    if (h)  h.outerHTML  = header(page);
    if (f)  f.outerHTML  = footerHTML;
    if (ts) ts.outerHTML = themeHTML;

    // Theme switcher — click to set, persist via data-theme.
    const root = document.documentElement;
    document.querySelectorAll('.theme-switch button').forEach(b => {
      b.addEventListener('click', () => {
        root.setAttribute('data-theme', b.dataset.set);
        document.querySelectorAll('.theme-switch button').forEach(x => x.classList.remove('on'));
        b.classList.add('on');
      });
    });
    const initial = root.getAttribute('data-theme') || 'aurora';
    const initBtn = document.querySelector('.theme-switch button[data-set="' + initial + '"]');
    if (initBtn) initBtn.classList.add('on');

    // Hero card tab visual toggle (booking widget)
    document.querySelectorAll('.hc-tab').forEach(t => {
      t.addEventListener('click', e => {
        e.preventDefault();
        const tabs = t.closest('.hc-tabs');
        if (!tabs) return;
        tabs.querySelectorAll('.hc-tab').forEach(x => x.classList.remove('on'));
        t.classList.add('on');
      });
    });

    // Filter chips (visual)
    document.querySelectorAll('.chips').forEach(group => {
      group.querySelectorAll('.chip').forEach(c => {
        c.addEventListener('click', () => {
          group.querySelectorAll('.chip').forEach(x => x.classList.remove('on'));
          c.classList.add('on');
        });
      });
    });

    // Slot picker (visual)
    document.querySelectorAll('.slots').forEach(group => {
      group.querySelectorAll('.slot').forEach(s => {
        if (s.classList.contains('off')) return;
        s.addEventListener('click', () => {
          group.querySelectorAll('.slot').forEach(x => x.classList.remove('on'));
          s.classList.add('on');
        });
      });
    });

    // FAQ side scroll-spy (simple: click → set .on)
    document.querySelectorAll('.faq-side a').forEach(a => {
      a.addEventListener('click', () => {
        document.querySelectorAll('.faq-side a').forEach(x => x.classList.remove('on'));
        a.classList.add('on');
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();

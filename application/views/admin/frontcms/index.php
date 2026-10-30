<?php
$hs       = $home_sections_decoded;
$hero     = isset($hs['hero'])          ? $hs['hero']          : array();
$tiles    = isset($hs['quick_tiles'])   ? $hs['quick_tiles']   : array();
$depts    = isset($hs['departments'])   ? $hs['departments']   : array();
$docs_sec = isset($hs['doctors'])       ? $hs['doctors']       : array();
$stats    = isset($hs['stats'])         ? $hs['stats']         : array();
$locs     = isset($hs['locations'])     ? $hs['locations']     : array();
$marq     = isset($hs['marquee'])       ? $hs['marquee']       : array();
$how      = isset($hs['how_it_works'])  ? $hs['how_it_works']  : array();
if (empty($how)) {
    $how = array(
        'title'        => 'Three steps. No paperwork.',
        'step_1_title' => 'Pick a slot',
        'step_1_desc'  => 'Choose your doctor, day and time. No signup required, no payment to book — just confirm and we will hold the slot.',
        'step_2_title' => 'Meet the specialist',
        'step_2_desc'  => 'In-person at the hospital or a video consult from home. Past reports, history and family profiles — all on one screen.',
        'step_3_title' => 'Get reports & care',
        'step_3_desc'  => 'Lab reports on WhatsApp same-day. Follow-ups, refills and billing — all from your patient portal, no chasing.',
    );
}
$testi    = isset($hs['testimonials'])  ? $hs['testimonials']  : array();
$tpas     = isset($hs['tpas'])          ? $hs['tpas']          : array();
$cta_sec  = isset($hs['cta'])           ? $hs['cta']           : array();

$sp      = $static_pages_decoded;
$sp_appt = isset($sp['appointment'])     ? $sp['appointment']     : array();
$sp_cal  = isset($sp['annual_calendar']) ? $sp['annual_calendar'] : array();
$sp_404  = isset($sp['show_404'])        ? $sp['show_404']        : array();

$stat_items  = array_values(isset($stats['items']) ? $stats['items'] : array());
$loc_items   = array_values(isset($locs['items'])  ? $locs['items']  : array());
$testi_items = array_values(isset($testi['items']) ? $testi['items'] : array());
$tpas_items  = array_values(isset($tpas['items'])  ? $tpas['items']  : array());

// How It Works steps: prefer new items[] format, fall back to legacy step_N_title/desc keys
$step_items = array();
if (!empty($how['items']) && is_array($how['items'])) {
    foreach ($how['items'] as $it) {
        $step_items[] = array(
            'title' => isset($it['title']) ? $it['title'] : '',
            'desc'  => isset($it['desc'])  ? $it['desc']  : '',
        );
    }
}
if (empty($step_items)) {
    for ($i = 1; $i <= 9; $i++) {
        $t = isset($how['step_'.$i.'_title']) ? $how['step_'.$i.'_title'] : '';
        $d = isset($how['step_'.$i.'_desc'])  ? $how['step_'.$i.'_desc']  : '';
        if ($t !== '' || $d !== '') {
            $step_items[] = array('title' => $t, 'desc' => $d);
        }
    }
}
$step_items = array_values($step_items);
if (empty($tpas_items) && empty($tpas)) {
    $tpas_items = array(
        array('name' => 'Star Health'),
        array('name' => 'HDFC Ergo'),
        array('name' => 'ICICI Lombard'),
        array('name' => 'Bajaj Allianz'),
        array('name' => 'Niva Bupa'),
        array('name' => 'Care Health'),
        array('name' => 'SBI General'),
        array('name' => 'Tata AIG'),
        array('name' => 'Aditya Birla'),
        array('name' => 'Manipal Cigna'),
        array('name' => 'New India'),
        array('name' => 'Reliance General'),
    );
}
if (empty($testi_items) && empty($testi)) {
    $testi_items = array(
        array('name' => 'Ramesh Kumar',  'quote' => 'The doctors took great care of my mother during her stay. The staff was kind, the facilities clean, and the discharge process was smooth and quick.'),
        array('name' => 'Priya Sharma',  'quote' => 'Booking the appointment online was simple and the consultation was very thorough. Dr. explained everything in detail without rushing.'),
        array('name' => 'Anil Verma',    'quote' => 'Best hospital experience my family has had. Nursing staff is attentive and hygiene standards are top-notch. Highly recommend.'),
        array('name' => 'Meera Iyer',    'quote' => 'I was nervous before my surgery, but the team made me feel safe at every step. Follow-up care has been excellent too.'),
    );
}
$tile_count  = 4;
$marq_items  = isset($marq['items']) && is_array($marq['items']) ? implode("\n", $marq['items']) : '';
if (empty($marq_items) && empty($marq)) {
    $marq_items = implode("\n", array(
        'NABL accredited lab',
        'NABH certified hospital',
        '96% same-day appointments',
        'Reports in 4 hours',
        'Cashless on 38 insurers',
        '24×7 emergency care',
        'Free pickup & drop for diagnostics',
    ));
}

$cur_theme  = isset($frontcmslist->theme)       ? $frontcmslist->theme       : '';
$cur_color  = isset($frontcmslist->theme_color) ? $frontcmslist->theme_color : 'aurora';
$cur_layout = isset($frontcmslist->home_layout) ? $frontcmslist->home_layout : 'hospital';

// Per-layout hero media: merge stored overrides over the shared defaults so each
// layout's two fields show the real current URL (editable + clearable).
$hm_all  = (isset($hs['hero_media']) && is_array($hs['hero_media'])) ? $hs['hero_media'] : array();
$hm_def  = function_exists('hero_media_defaults') ? hero_media_defaults() : array();
$hm_vals = array();
foreach ($hm_def as $hm_k => $hm_d) {
    $hm_vals[$hm_k] = array(
        'bg_image' => isset($hm_all[$hm_k]['bg_image']) ? $hm_all[$hm_k]['bg_image'] : $hm_d['bg_image'],
        'bg_video' => isset($hm_all[$hm_k]['bg_video']) ? $hm_all[$hm_k]['bg_video'] : $hm_d['bg_video'],
    );
}
$pv_hero_image = isset($hm_vals[$cur_layout]['bg_image']) ? $hm_vals[$cur_layout]['bg_image'] : '';

$cms_pages_js = array();
if (!empty($cms_pages)) {
    foreach ($cms_pages as $p) {
        $cms_pages_js[(int)$p['id']] = array(
            'id'               => (int)$p['id'],
            'title'            => $p['title'],
            'layout_type'      => isset($p['layout_type'])       ? $p['layout_type']       : 'blank',
            'page_section_data'=> isset($p['page_section_data']) ? $p['page_section_data'] : '{}',
        );
    }
}
?>
<!-- Front CMS styles moved to backend/css/sh-theme.css (P32a — see "Front CMS" section) -->

<div class="row">
    <?php $this->load->view('setting/sidebar')?>
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom _bs3-ptbnull py-3">
                <h3 class="card-title mb-0 fw-semibold _bs3-titlefix fcms-page-h3"><?php echo $this->lang->line('front_cms_setting'); ?></h3>
            </div>

            <!-- Tab Nav -->
            <ul class="nav nav-tabs frontcms-tabs px-2 pt-1 mb-0" id="cmsTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button"><i class="fas fa-cog me-1"></i><?php echo $this->lang->line('general'); ?></button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-theme" type="button"><i class="fas fa-palette me-1"></i><?php echo $this->lang->line('theme_and_layout'); ?></button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sections" type="button"><i class="fas fa-th-large me-1"></i><?php echo $this->lang->line('home_sections'); ?></button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pages" type="button"><i class="fas fa-file-alt me-1"></i><?php echo $this->lang->line('page_sections'); ?></button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-social" type="button"><i class="fas fa-share-alt me-1"></i><?php echo $this->lang->line('social'); ?></button></li>
            </ul>

            <form role="form" id="custom" action="<?php echo site_url('admin/frontcms') ?>" class="form-horizontal frontcms-form" method="post" enctype="multipart/form-data" novalidate>
                <div class="card-body p-4">

                    <?php if ($this->session->flashdata('msg')): ?>
                        <?php echo $this->session->flashdata('msg'); $this->session->unset_userdata('msg'); ?>
                    <?php endif; ?>

                    <input type="hidden" name="id" value="<?php echo (int)$frontcmslist->id ?>">

                    <div class="tab-content" id="cmsTabContent">

<!-- ══════════════════════════════
     TAB 1 — GENERAL
══════════════════════════════ -->
<div class="tab-pane fade show active" id="tab-general" role="tabpanel">
  <?php
    $g_mod_on  = (int)$frontcmslist->is_active_front_cms
               + (int)$frontcmslist->is_active_online_appointment
               + (int)$frontcmslist->is_active_sidebar
               + (int)$frontcmslist->is_active_rtl;
    $g_sb_opts = json_decode($frontcmslist->sidebar_options);
    if (!is_array($g_sb_opts)) { $g_sb_opts = array(); }
    $g_sw_on   = count($g_sb_opts);
    $g_cms_on  = ((int)$frontcmslist->is_active_front_cms == 1);
    $g_ga_set  = !empty($frontcmslist->google_analytics);
  ?>
  <div class="row">
    <div class="col-12">

      <div class="fcms-gen-grid">
        <div>

      <!-- ─── Modules card ─── -->
      <div class="fcms-modcard">
        <div class="fcms-modcard-h">
          <div class="ic"><i class="fas fa-th-large"></i></div>
          <div class="ti">
            <div class="nm"><?php echo $this->lang->line('frontend_modules'); ?></div>          </div>        </div>
        <div class="fcms-modcard-body">

          <div class="fcms-mod-row">            <div class="fcms-mod-body">
              <div class="nm"><?php echo $this->lang->line('front_cms'); ?></div>            </div>
            <div class="form-check form-switch _bs3-material-switch">
              <input id="enable_frontcms" name="is_active_front_cms" type="checkbox" role="switch" class="form-check-input _bs3-chk" value="1" <?php echo set_checkbox('is_active_front_cms', '1', (set_value('is_active_front_cms', $frontcmslist->is_active_front_cms) == 1) ? true : false); ?>>
            </div>
          </div>

          <div class="fcms-mod-row">            <div class="fcms-mod-body">
              <div class="nm"><?php echo $this->lang->line('online_appointment'); ?></div>            </div>
            <div class="form-check form-switch _bs3-material-switch">
              <input id="enable_online_appointment" name="is_active_online_appointment" type="checkbox" role="switch" class="form-check-input _bs3-chk" value="1" <?php echo set_checkbox('is_active_online_appointment', '1', (set_value('is_active_online_appointment', $frontcmslist->is_active_online_appointment) == 1) ? true : false); ?>>
            </div>
          </div>

          <div class="fcms-mod-row">            <div class="fcms-mod-body">
              <div class="nm"><?php echo $this->lang->line('sidebar'); ?></div>            </div>
            <div class="form-check form-switch _bs3-material-switch">
              <input id="enable_sidebar" name="is_active_sidebar" type="checkbox" role="switch" class="form-check-input _bs3-chk" value="1" <?php echo set_checkbox('is_active_sidebar', '1', (set_value('is_active_sidebar', $frontcmslist->is_active_sidebar) == 1) ? true : false); ?>>
            </div>
          </div>

          <div class="fcms-mod-row">            <div class="fcms-mod-body">
              <div class="nm"><?php echo $this->lang->line('language_rtl_text_mode'); ?></div>            </div>
            <div class="form-check form-switch _bs3-material-switch">
              <input id="enable_rtl" name="is_active_rtl" type="checkbox" role="switch" class="form-check-input _bs3-chk" value="1" <?php echo set_checkbox('is_active_rtl', '1', (set_value('is_active_rtl', $frontcmslist->is_active_rtl) == 1) ? true : false); ?>>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── Branding card ─── -->
      <div class="fcms-modcard">
        <div class="fcms-modcard-h">
          <div class="ic"><i class="fas fa-image"></i></div>
          <div class="ti">
            <div class="nm"><?php echo $this->lang->line('branding'); ?></div>          </div>
        </div>
        <div class="fcms-modcard-body">

          <div class="fcms-upload">
            <div class="fcms-upload-thumb">
              <?php if (!empty($frontcmslist->logo)): ?>
                <img src="<?php echo html_escape($this->customlib->getBaseUrl().$frontcmslist->logo); ?>" alt="logo" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block';">
                <i class="fas fa-image" style="display:none;"></i>
              <?php else: ?>
                <i class="fas fa-image"></i>
              <?php endif; ?>
            </div>
            <div class="fcms-upload-body">
              <div class="nm"><?php echo $this->lang->line('logo'); ?></div>
              <div class="sb"><?php echo $this->lang->line('logo_dimensions_hint'); ?></div>
              <input type="file" class="filestyle form-control-file" name="logo" data-height="40" data-default-file="<?php echo html_escape($this->customlib->getBaseUrl().$frontcmslist->logo); ?>">
              <span class="text-danger d-block mt-1"><?php echo form_error('logo'); ?></span>
            </div>
          </div>

          <div class="fcms-upload">
            <div class="fcms-upload-thumb fav">
              <?php if (!empty($frontcmslist->fav_icon)): ?>
                <img src="<?php echo html_escape($this->customlib->getBaseUrl().$frontcmslist->fav_icon); ?>" alt="favicon" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block';">
                <i class="fas fa-star fcms-star" style="display:none;"></i>
              <?php else: ?>
                <i class="fas fa-star fcms-star"></i>
              <?php endif; ?>
            </div>
            <div class="fcms-upload-body">
              <div class="nm"><?php echo $this->lang->line('favicon'); ?></div>
              <div class="sb"><?php echo $this->lang->line('favicon_dimensions_hint'); ?></div>
              <input type="file" class="filestyle form-control-file" name="fav_icon" data-height="40" data-default-file="<?php echo html_escape($this->customlib->getBaseUrl().$frontcmslist->fav_icon); ?>">
            </div>
          </div>

          <div class="fcms-field">
            <label class="form-label"><?php echo $this->lang->line('footer_text'); ?></label>
            <input type="text" class="form-control" name="footer_text" value="<?php echo html_escape(set_value('footer_text', $frontcmslist->footer_text)) ?>">
          </div>

        </div>
      </div>

        </div>
        <div>

      <!-- ─── Sidebar Widgets card ─── -->
      <div class="fcms-modcard">
        <div class="fcms-modcard-h">
          <div class="ic"><i class="fas fa-columns"></i></div>
          <div class="ti">
            <div class="nm"><?php echo $this->lang->line('sidebar_option'); ?></div>          </div>        </div>
        <div class="fcms-modcard-body">
          <div class="d-flex gap-2 flex-wrap">
            <label class="fcms-checkpill" title="When checked, shows the Latest News block on the home page (below the hero section). Source: News &amp; Notice → cms_notice table.">
              <input type="checkbox" name="sidebar_options[]" value="news" <?php echo set_checkbox('sidebar_options[]', 'news', (set_value('sidebar_options[]', in_array("news", $g_sb_opts)) == 1) ? true : false); ?>>
              <span class="ix"><i class="fas fa-check"></i></span>
              <span><?php echo $this->lang->line('news'); ?></span>
            </label>
            <label class="fcms-checkpill" title="When checked, adds a 'Feedback' link in the site header and footer that points to the /page/complain CMS page.">
              <input type="checkbox" name="sidebar_options[]" value="complain" <?php echo set_checkbox('sidebar_options[]', 'complain', (set_value('sidebar_options[]', in_array("complain", $g_sb_opts)) == 1) ? true : false); ?>>
              <span class="ix"><i class="fas fa-check"></i></span>
              <span><?php echo $this->lang->line('complain'); ?></span>
            </label>
          </div>        </div>
      </div>

      <!-- ─── Analytics card ─── -->
      <div class="fcms-modcard">
        <div class="fcms-modcard-h">
          <div class="ic"><i class="fas fa-chart-line"></i></div>
          <div class="ti">
            <div class="nm"><?php echo $this->lang->line('analytics'); ?></div>          </div>        </div>
        <div class="fcms-modcard-body">
          <div class="fcms-field m-0">
            <label class="form-label"><?php echo $this->lang->line('google_analytics'); ?> <small>Paste the <code>&lt;script&gt;</code> tag from your GA dashboard</small></label>
            <textarea class="form-control" name="google_analytics" rows="5" placeholder="<?php echo html_escape($this->lang->line('google_analytics_placeholder')); ?>"><?php echo set_value('google_analytics', $frontcmslist->google_analytics) ?></textarea>
            <div class="hint"><i class="fas fa-info-circle"></i><?php echo html_escape($this->lang->line('google_analytics_help')); ?></div>
          </div>
        </div>
      </div>

        </div>
      </div>

    </div>
  </div>
</div><!-- /tab-general -->


<!-- ══════════════════════════════
     TAB 2 — THEME & LAYOUT
══════════════════════════════ -->
<div class="tab-pane fade" id="tab-theme" role="tabpanel">

  <!-- Page head with live indicator -->
  <div class="fcms2-pagehead">
    <div class="lhs">
      <h4>Theme &amp; Layout</h4>    </div>
    <span class="fcms2-livepill"><span class="dot"></span><span id="fcms2-live-summary">Currently live · …</span></span>
  </div>

  <!-- ── Two-column shell: config (L) + preview (R) ── -->
  <div class="fcms2-shell">

    <div class="fcms2-config">

      <!-- 1. Theme -->
      <section class="fcms2-sec">
        <header class="fcms2-sec-head">
          <div class="lhs">
            <span class="fcms2-sec-num"><i class="fas fa-check"></i></span>
            <div>
              <h3 class="fcms2-sec-title">Theme</h3>            </div>
          </div>
          <span class="fcms2-sec-cur">Picked: <b id="fcms2-cur-theme">…</b></span>
        </header>

        <div class="fcms2-theme-cards" data-group="theme">
          <?php foreach ($active_themes as $slug => $theme_cfg): ?>
          <?php $is_selected = ($cur_theme === $slug); ?>
          <div class="fcms2-tcard<?php echo $is_selected ? ' on' : ''; ?>" data-theme="<?php echo html_escape($slug); ?>">
            <input type="radio" name="theme" value="<?php echo html_escape($slug); ?>" class="d-none" <?php echo $is_selected ? 'checked' : ''; ?>>
            <div class="fcms2-tcard-prev preview-<?php echo html_escape($slug); ?>" style="<?php echo $slug === 'atrium' ? 'background:linear-gradient(160deg,#0ea5e9 0%,#0a3a5c 100%)' : ($slug === 'organic' ? 'background:linear-gradient(160deg,#6b8e6f 0%,#1f2a20 100%)' : ''); ?>">
              <h4 class="tc-name"><?php echo html_escape($theme_cfg['name']); ?></h4>
              <span class="tc-tag"><?php echo $slug === 'atrium' ? 'Confident · clinical · bright' : ($slug === 'organic' ? 'Soft · natural · warm' : 'Custom theme'); ?></span>
            </div>
            <div class="fcms2-tcard-foot">
              <span>6 home layouts</span>
              <span class="fcms2-tcard-status"></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- 2. Color palette -->
      <section class="fcms2-sec">
        <header class="fcms2-sec-head">
          <div class="lhs">
            <span class="fcms2-sec-num"><i class="fas fa-check"></i></span>
            <div>
              <h3 class="fcms2-sec-title">Colour palette</h3>            </div>
          </div>
          <span class="fcms2-sec-cur">Picked: <b id="fcms2-cur-color">…</b></span>
        </header>

        <?php foreach ($active_themes as $slug => $theme_cfg): ?>
        <?php if (empty($theme_cfg['colors'])) continue; ?>
        <div class="fcms2-palette-group<?php echo $slug === $cur_theme ? ' on' : ''; ?>" data-palette-of="<?php echo html_escape($slug); ?>">
          <?php foreach ($theme_cfg['colors'] as $color_name => $color_hex): ?>
          <?php $is_selected_color = ($cur_color === $color_name && $cur_theme === $slug); ?>
          <button type="button" class="fcms2-swatch<?php echo $is_selected_color ? ' on' : ''; ?>" data-color="<?php echo html_escape($color_name); ?>" data-color-hex="<?php echo html_escape($color_hex); ?>">
            <span class="dot" style="background:<?php echo html_escape($color_hex); ?>"></span>
            <?php echo ucfirst(html_escape($color_name)); ?>
          </button>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <input type="hidden" name="theme_color" id="fcms-color-input" value="<?php echo html_escape($cur_color); ?>">
      </section>

      <!-- 3. Home Layout -->
      <section class="fcms2-sec">
        <header class="fcms2-sec-head">
          <div class="lhs">
            <span class="fcms2-sec-num"><i class="fas fa-check"></i></span>
            <div>
              <h3 class="fcms2-sec-title">Home page layout</h3>            </div>
          </div>
          <span class="fcms2-sec-cur">Picked: <b id="fcms2-cur-layout">…</b></span>
        </header>

        <div class="fcms2-layouts" data-group="layout">

          <?php
            $layouts = array(
              'hospital'   => array('icon'=>'🏥', 'title'=>'Hospital',          'h1'=>'Care, when you need it',         'h2'=>'Booking · Departments · Doctors · Stats',           'tiles'=>'🩺,🦴,👶,🧪,🩻,🏥'),
              'specialist' => array('icon'=>'🫀', 'title'=>'Specialist Clinic', 'h1'=>'One specialty, done well',       'h2'=>'Doctor card · Sub-specialties · Insurance',         'tiles'=>'🫀,🩺,💊,📋,📞,🏥'),
              'dental'     => array('icon'=>'🦷', 'title'=>'Dental &amp; Cosmetic','h1'=>'Honest dentistry, no guesswork', 'h2'=>'EMI from ₹999/month · Sedation available',          'tiles'=>'🦷,✨,😬,🔩,😴,👶'),
              'eye'        => array('icon'=>'👁️', 'title'=>'Eye Care &amp; LASIK', 'h1'=>'See clearly, in 15 minutes',     'h2'=>'LASIK eligibility · EMI · Insurance',               'tiles'=>'👁,👓,💧,🔬,⚕,🏥'),
              'diagnostic' => array('icon'=>'🔬', 'title'=>'Diagnostic Centre', 'h1'=>'Tests in. Reports out, fast.',   'h2'=>'220+ tests · Free pickup · NABL',                   'tiles'=>'🧪,🩻,🩺,💉,🔬,📋'),
              'cardiology' => array('icon'=>'❤',  'title'=>'Cardiology Clinic', 'h1'=>'Hearts. Looked at carefully.',   'h2'=>'Doctor-led · ECG / Echo / TMT · Specs',             'tiles'=>'❤,🫀,💉,🩺,🩻,⚕'),
            );
            foreach ($layouts as $lkey => $L):
              $is_sel = ($cur_layout === $lkey);
          ?>
          <div class="fcms2-lcard<?php echo $is_sel ? ' on' : ''; ?>"
               data-layout="<?php echo html_escape($lkey); ?>"
               data-h1="<?php echo html_escape($L['h1']); ?>"
               data-h2="<?php echo html_escape($L['h2']); ?>"
               data-tiles="<?php echo html_escape($L['tiles']); ?>"
               data-title="<?php echo $L['title']; ?>">
            <input type="radio" name="home_layout" value="<?php echo html_escape($lkey); ?>" class="d-none" <?php echo $is_sel ? 'checked' : ''; ?>>
            <div class="fcms2-lthumb <?php echo html_escape($lkey); ?>">
              <div class="lt-nav"></div><div class="lt-hero"></div>
              <div class="lt-tile"></div><div class="lt-tile b"></div><div class="lt-tile c"></div>
            </div>
            <div class="fcms2-lcard-body">
              <div><span class="fcms2-licon"><?php echo $L['icon']; ?></span><h4 class="fcms2-ltitle"><?php echo $L['title']; ?></h4></div>
            </div>
          </div>
          <?php endforeach; ?>

        </div>

        <!-- Per-layout hero media (relocated here from Home Sections) -->
        <div class="fcms2-hm">
          <div class="fcms2-hm-head">
            <span class="fcms2-sec-num"><i class="fas fa-film"></i></span>
            <h3 class="fcms2-sec-title m-0">Hero media</h3>
          </div>
          <?php foreach ($hm_def as $hm_k => $hm_d):
              $hm_sel = ($cur_layout === $hm_k);
              $hm_lbl = isset($layouts[$hm_k]['title']) ? $layouts[$hm_k]['title'] : ucfirst($hm_k);
          ?>
          <div class="fcms2-hm-group" data-layout="<?php echo html_escape($hm_k); ?>"<?php echo $hm_sel ? '' : ' class="d-none"'; ?>>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label"><?php echo $hm_lbl; ?> — Background image URL</label>
                <input type="text" class="form-control" data-hm="image" name="home_section_data[hero_media][<?php echo html_escape($hm_k); ?>][bg_image]" value="<?php echo html_escape($hm_vals[$hm_k]['bg_image']); ?>" placeholder="https://...">
              </div>
              <div class="col-md-6">
                <label class="form-label"><?php echo $hm_lbl; ?> — Background video URL <span class="fcms-label-hint">(blank = image only)</span></label>
                <input type="text" class="form-control" name="home_section_data[hero_media][<?php echo html_escape($hm_k); ?>][bg_video]" value="<?php echo html_escape($hm_vals[$hm_k]['bg_video']); ?>" placeholder="https://...mp4">
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

    </div><!-- /.fcms2-config -->

    <!-- Live preview pane (sticky on the right) -->
    <aside class="fcms2-preview">
      <div class="fcms2-pv-head">
        <h3>Live preview</h3>
        <div class="fcms2-pv-toggle" id="fcms2-pv-toggle">
          <button type="button" class="on" data-mode="mobile"><i class="fas fa-mobile-alt me-1"></i>Mobile</button>
          <button type="button" data-mode="desk"><i class="fas fa-desktop me-1"></i>Desktop</button>
        </div>
      </div>
      <div class="fcms2-pv-body">
        <div class="fcms2-device" id="fcms2-pv-device">
          <div class="fcms2-pv-nav" id="fcms2-pv-nav">
            <span class="pvnav-logo"><i class="fas fa-plus-circle"></i> Hospital</span>
            <span class="pvnav-menu"><i></i><i></i><i></i><i></i></span>
            <span class="pvnav-cta">Book</span>
          </div>
          <div class="fcms2-pv-hero" id="fcms2-pv-hero">
            <div class="pv-hero-photo"<?php if (!empty($pv_hero_image)): ?> style="background-image:url('<?php echo html_escape($pv_hero_image); ?>')"<?php endif; ?>></div>
            <div class="pv-hero-inner">
              <h4 id="fcms2-pv-h1">…</h4>
              <p id="fcms2-pv-h2">…</p>
              <div class="pv-hero-btns"><span class="pv-btn-primary">Book Appointment</span><span class="pv-btn-ghost">Find a Doctor</span></div>
            </div>
          </div>
          <div class="fcms2-pv-tiles" id="fcms2-pv-tiles"></div>
          <div class="fcms2-pv-strip">Our Specialties</div>
          <div class="fcms2-pv-cards">
            <div class="fcms2-pv-card pv-doc"><span class="pv-doc-ava"></span><div class="b1"></div><div class="b2"></div></div>
            <div class="fcms2-pv-card pv-doc"><span class="pv-doc-ava"></span><div class="b1"></div><div class="b2"></div></div>
            <div class="fcms2-pv-card pv-doc"><span class="pv-doc-ava"></span><div class="b1"></div><div class="b2"></div></div>
            <div class="fcms2-pv-card pv-doc"><span class="pv-doc-ava"></span><div class="b1"></div><div class="b2"></div></div>
          </div>
        </div>
      </div>
      <div class="fcms2-pv-foot">
        Previewing <strong id="fcms2-pv-foot-text">…</strong><br/>
        Click anywhere on the left to update this preview.
      </div>
    </aside>

  </div><!-- /.fcms2-shell -->

  <!-- Save bar — appears when something is changed -->
  <div class="fcms2-savebar hidden" id="fcms2-savebar">
    <div class="fcms2-savebar-l">
      <span class="dot"></span>
      <div>
        <span><b id="fcms2-diff-count">0 changes</b> ready to save</span>
        <div class="fcms2-diff fcms2-diff-spaced" id="fcms2-diff-text"></div>
      </div>
    </div>
    <div class="fcms2-savebar-r">
      <button type="button" class="btn fcms2-btn-discard" id="fcms2-btn-discard">Discard</button>
      <span class="fcms2-savehint"><i class="fas fa-arrow-down me-1"></i>Click <b>Save</b> at the bottom to apply</span>
    </div>
  </div>

  <?php // Hidden bootstrap data for the JS state machine ?>
  <script type="application/json" id="fcms2-bootstrap"><?php
    $palettes = array();
    foreach ($active_themes as $slug => $theme_cfg) {
      if (empty($theme_cfg['colors'])) continue;
      $palettes[$slug] = array_keys($theme_cfg['colors']);
    }
    $color_hex_map = array();
    foreach ($active_themes as $slug => $theme_cfg) {
      if (empty($theme_cfg['colors'])) continue;
      foreach ($theme_cfg['colors'] as $cn => $ch) {
        $color_hex_map[$cn] = $ch;
      }
    }
    $theme_labels = array();
    foreach ($active_themes as $slug => $theme_cfg) {
      $theme_labels[$slug] = $theme_cfg['name'];
    }
    echo json_encode(array(
      'live'         => array('theme'=>$cur_theme, 'color'=>$cur_color, 'layout'=>$cur_layout),
      'palettes'     => $palettes,
      'colorHex'     => $color_hex_map,
      'themeLabels'  => $theme_labels,
    ));
  ?></script>

</div><!-- /tab-theme -->


<!-- ══════════════════════════════
     TAB 3 — HOME SECTIONS
══════════════════════════════ -->
<div class="tab-pane fade" id="tab-sections" role="tabpanel">

  <!-- .hsec-* styles moved to backend/css/sh-theme.css (P32a — Front CMS Block 2) -->

  <div class="hsec-shell">
    <aside class="hsec-side">
      <div class="hsec-side-head">
        <span><b id="hsecVisibleCount">9</b>&nbsp;of 11 visible</span>
        <button type="button" class="hsec-reset-btn" onclick="hsecResetOrder()"><i class="fas fa-undo me-1"></i>Reset order</button>
        <button type="button" class="btn btn-danger btn-sm ms-2" onclick="hsecResetToDefault()"><i class="fas fa-history me-1"></i>Reset to Default</button>
      </div>
      <div class="hsec-side-help"><i class="fas fa-arrows-alt"></i> Drag <strong>⋮⋮</strong> to reorder · click a row to edit fields</div>
      <ul class="hsec-list" id="hsecList">
        <?php
          $hero_show  = isset($hero['show'])     ? $hero['show']     : true;
          $tiles_show = isset($tiles['show'])    ? $tiles['show']    : true;
          $depts_show = isset($depts['show'])    ? $depts['show']    : true;
          $docs_show  = isset($docs_sec['show']) ? $docs_sec['show'] : true;
          $stats_show = isset($stats['show'])    ? $stats['show']    : true;
          $locs_show  = isset($locs['show'])     ? $locs['show']     : true;
          $marq_show  = isset($marq['show'])     ? $marq['show']     : false;
          $how_show   = isset($how['show'])      ? $how['show']      : false;
          $testi_show = isset($testi['show'])    ? $testi['show']    : true;
          $tpas_show  = isset($tpas['show'])     ? $tpas['show']     : true;
          $cta_show   = isset($cta_sec['show'])  ? $cta_sec['show']  : true;

          // Resolve section order from saved data (only reorderable sections; hero is pinned-first elsewhere)
          $hsec_default_order = array('marquee','quick_tiles','departments','doctors','how_it_works','stats','locations','testimonials','tpas','cta');
          $hsec_saved = (isset($hs['order']) && is_array($hs['order'])) ? array_values($hs['order']) : array();
          $hsec_order = array();
          foreach ($hsec_saved as $k) { if (in_array($k, $hsec_default_order, true) && !in_array($k, $hsec_order, true)) { $hsec_order[] = $k; } }
          foreach ($hsec_default_order as $k) { if (!in_array($k, $hsec_order, true)) { $hsec_order[] = $k; } }

          $hsec_meta = array(
            'quick_tiles'  => array('label'=>'Quick Action Tiles',      'icon'=>'th',              'color'=>'orange', 'sub'=>'4 action tiles',          'show'=>$tiles_show),
            'departments'  => array('label'=>'Departments / Specialties','icon'=>'stethoscope',  'color'=>'violet', 'sub'=>'Auto from DB · 12 items', 'show'=>$depts_show),
            'doctors'      => array('label'=>'Featured Doctors',         'icon'=>'user-md',         'color'=>'sky',    'sub'=>'Auto from Staff',         'show'=>$docs_show),
            'stats'        => array('label'=>'Stats / Counters',         'icon'=>'chart-bar',       'color'=>'green',  'sub'=>'Animated counters',       'show'=>$stats_show),
            'locations'    => array('label'=>'Locations / Branches',     'icon'=>'map-marker-alt',  'color'=>'red',    'sub'=>'Multi-branch cards',      'show'=>$locs_show),
            'marquee'      => array('label'=>'Marquee Strip',            'icon'=>'stream',          'color'=>'purple', 'sub'=>'Rolling banner',          'show'=>$marq_show),
            'how_it_works' => array('label'=>'How It Works',             'icon'=>'list-ol',         'color'=>'amber',  'sub'=>'Patient journey · '.max(count($step_items),3).' steps', 'show'=>$how_show),
            'testimonials' => array('label'=>'Testimonials',             'icon'=>'quote-left',      'color'=>'pink',   'sub'=>'Patient quotes',          'show'=>$testi_show),
            'tpas'         => array('label'=>'TPAs / Insurance',         'icon'=>'shield-alt',      'color'=>'teal',   'sub'=>'Logo strip',              'show'=>$tpas_show),
            'cta'          => array('label'=>'Booking / CTA Banner',     'icon'=>'calendar-check',  'color'=>'yellow', 'sub'=>'Footer banner',           'show'=>$cta_show),
          );
        ?>

        <!-- Hero: pinned, always first, not draggable -->
        <li class="hsec-item hsec-pinned active <?php echo $hero_show ? '' : 'disabled'; ?>" data-key="hero">
          <span class="hsec-pin"><i class="fas fa-thumbtack" title="Pinned — always first"></i></span>
          <span class="hsec-num">★</span>
          <span class="hsec-ic ic-blue"><i class="fas fa-image"></i></span>
          <div class="hsec-nm">Hero Banner<small>Pinned · always renders first</small></div>
          <input type="hidden" name="home_section_data[hero][show]" value="0">
          <input class="form-check-input hsec-show-toggle" type="checkbox" name="home_section_data[hero][show]" value="1" <?php echo $hero_show ? 'checked' : ''; ?> title="Show on home page">
        </li>

        <?php foreach ($hsec_order as $idx => $key): $m = $hsec_meta[$key]; ?>
        <li class="hsec-item <?php echo $m['show'] ? '' : 'disabled'; ?>" data-key="<?php echo $key; ?>">
          <span class="hsec-drag" title="Drag to reorder">⋮⋮</span>
          <span class="hsec-num"><?php echo $idx + 1; ?></span>
          <span class="hsec-ic ic-<?php echo $m['color']; ?>"><i class="fas fa-<?php echo $m['icon']; ?>"></i></span>
          <div class="hsec-nm"><?php echo html_escape($m['label']); ?><small><?php echo html_escape($m['sub']); ?></small></div>
          <input type="hidden" name="home_section_data[<?php echo $key; ?>][show]" value="0">
          <input class="form-check-input hsec-show-toggle" type="checkbox" name="home_section_data[<?php echo $key; ?>][show]" value="1" <?php echo $m['show'] ? 'checked' : ''; ?> title="Show on home page">
        </li>
        <?php endforeach; ?>
      </ul>

      <!-- Hidden order inputs (re-rendered by JS on drag end). Only reorderable sections — hero is fixed first. -->
      <div id="hsecOrderInputs" class="d-none">
        <?php foreach ($hsec_order as $k): ?>
        <input type="hidden" name="home_section_data[order][]" value="<?php echo html_escape($k); ?>">
        <?php endforeach; ?>
      </div>
    </aside>

    <main class="hsec-canvas">

      <!-- Panel: Hero -->
      <div class="hsec-panel active" data-panel="hero">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-blue"><i class="fas fa-image"></i></span>
          <div>
            <h3>Hero Banner <span class="pinned-tag"><i class="fas fa-thumbtack me-1"></i>Pinned</span></h3>
            <div class="sub">Top-of-page banner with headline, CTAs and a video/image background — always renders first.</div>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Main Headline</label>
            <input type="text" class="form-control" name="home_section_data[hero][headline]" value="<?php echo html_escape(isset($hero['headline']) ? $hero['headline'] : 'World-class care, close to home'); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Sub-headline</label>
            <input type="text" class="form-control" name="home_section_data[hero][subheadline]" value="<?php echo html_escape(isset($hero['subheadline']) ? $hero['subheadline'] : ''); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Primary CTA Button</label>
            <input type="text" class="form-control" name="home_section_data[hero][cta_primary]" value="<?php echo html_escape(isset($hero['cta_primary']) ? $hero['cta_primary'] : 'Book Appointment'); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Secondary CTA Button</label>
            <input type="text" class="form-control" name="home_section_data[hero][cta_secondary]" value="<?php echo html_escape(isset($hero['cta_secondary']) ? $hero['cta_secondary'] : 'Find a Doctor'); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Emergency Number</label>
            <input type="text" class="form-control" name="home_section_data[hero][emergency_number]" value="<?php echo html_escape(isset($hero['emergency_number']) ? $hero['emergency_number'] : ''); ?>">
          </div>
        </div>

        <hr class="my-4">
        <div class="fcms-auto-info mb-3"><i class="fas fa-heart"></i>The fields below are used by the <strong>Cardiology layout</strong> only.</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Hero Badge Text</label>
            <input type="text" class="form-control" name="home_section_data[hero][badge_text]" value="<?php echo html_escape(isset($hero['badge_text']) ? $hero['badge_text'] : ''); ?>" placeholder="22 years · 6,800 angioplasties · 0 readmits">
          </div>
          <div class="col-12"><label class="form-label fw-bold mb-1">Trust strip (3 metric items)</label></div>
          <?php for($i=1; $i<=3; $i++): ?>
          <div class="col-md-2"><label class="form-label">Item <?php echo $i; ?> — Value</label><input type="text" class="form-control" name="home_section_data[hero][trust_<?php echo $i; ?>_value]" value="<?php echo html_escape(isset($hero['trust_'.$i.'_value']) ? $hero['trust_'.$i.'_value'] : ''); ?>" placeholder="9.8/10"></div>
          <div class="col-md-2"><label class="form-label">Item <?php echo $i; ?> — Label</label><input type="text" class="form-control" name="home_section_data[hero][trust_<?php echo $i; ?>_label]" value="<?php echo html_escape(isset($hero['trust_'.$i.'_label']) ? $hero['trust_'.$i.'_label'] : ''); ?>" placeholder="2,140 verified reviews"></div>
          <?php endfor; ?>

          <div class="col-12"><label class="form-label fw-bold mb-1 mt-2">Doctor Card (right side)</label></div>
          <div class="col-md-4"><label class="form-label">Photo URL</label><input type="text" class="form-control" name="home_section_data[hero][doc_photo]" value="<?php echo html_escape(isset($hero['doc_photo']) ? $hero['doc_photo'] : ''); ?>"></div>
          <div class="col-md-4"><label class="form-label">Name</label><input type="text" class="form-control" name="home_section_data[hero][doc_name]" value="<?php echo html_escape(isset($hero['doc_name']) ? $hero['doc_name'] : ''); ?>" placeholder="Prof. Rahul Bhatt, MD DM"></div>
          <div class="col-md-4"><label class="form-label">Role</label><input type="text" class="form-control" name="home_section_data[hero][doc_role]" value="<?php echo html_escape(isset($hero['doc_role']) ? $hero['doc_role'] : ''); ?>" placeholder="Senior Interventional Cardiologist"></div>
          <?php for($i=1; $i<=3; $i++): ?>
          <div class="col-md-2"><label class="form-label">Stat <?php echo $i; ?> — Value</label><input type="text" class="form-control" name="home_section_data[hero][doc_stat_<?php echo $i; ?>_value]" value="<?php echo html_escape(isset($hero['doc_stat_'.$i.'_value']) ? $hero['doc_stat_'.$i.'_value'] : ''); ?>" placeholder="22"></div>
          <div class="col-md-2"><label class="form-label">Stat <?php echo $i; ?> — Label</label><input type="text" class="form-control" name="home_section_data[hero][doc_stat_<?php echo $i; ?>_label]" value="<?php echo html_escape(isset($hero['doc_stat_'.$i.'_label']) ? $hero['doc_stat_'.$i.'_label'] : ''); ?>" placeholder="Years"></div>
          <?php endfor; ?>
          <div class="col-12"><label class="form-label">Credentials (comma-separated)</label><input type="text" class="form-control" name="home_section_data[hero][doc_creds]" value="<?php echo html_escape(isset($hero['doc_creds']) ? $hero['doc_creds'] : ''); ?>" placeholder="St George's London, FRCP, TAVI · Cath lab II"></div>
          <div class="col-md-6"><label class="form-label">Card CTA Text</label><input type="text" class="form-control" name="home_section_data[hero][doc_cta]" value="<?php echo html_escape(isset($hero['doc_cta']) ? $hero['doc_cta'] : ''); ?>" placeholder="Book a slot today"></div>
          <div class="col-md-6"><label class="form-label">Next slot</label><input type="text" class="form-control" name="home_section_data[hero][doc_next]" value="<?php echo html_escape(isset($hero['doc_next']) ? $hero['doc_next'] : ''); ?>" placeholder="Next: 4:30 pm"></div>
          <div class="col-12"><label class="form-label">Fine print</label><input type="text" class="form-control" name="home_section_data[hero][doc_fine]" value="<?php echo html_escape(isset($hero['doc_fine']) ? $hero['doc_fine'] : ''); ?>" placeholder="No upfront payment · cashless on 38 insurers"></div>

          <div class="col-12"><label class="form-label fw-bold mb-1 mt-2">ECG ribbon (bottom strip)</label></div>
          <div class="col-md-6"><label class="form-label">Left text</label><input type="text" class="form-control" name="home_section_data[hero][ecg_left]" value="<?php echo html_escape(isset($hero['ecg_left']) ? $hero['ecg_left'] : ''); ?>" placeholder="Cath lab on-call 24×7 · Bandra West"></div>
          <div class="col-md-6"><label class="form-label">Right text</label><input type="text" class="form-control" name="home_section_data[hero][ecg_right]" value="<?php echo html_escape(isset($hero['ecg_right']) ? $hero['ecg_right'] : ''); ?>" placeholder="Tap-to-call · 1800·HEART·11"></div>
        </div>
      </div>

      <!-- Panel: Quick Tiles -->
      <div class="hsec-panel" data-panel="quick_tiles">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-orange"><i class="fas fa-th"></i></span>
          <div><h3>Quick Action Tiles</h3><div class="sub">Four action tiles shown below the hero — book, login, packages, emergency.</div></div>
        </div>
        <?php $tile_defaults = array(
          1 => array('Book an appointment', '', 'See slots'),
          2 => array('View lab reports', '', 'Open portal'),
          3 => array('Health check-ups', '', 'View packages'),
          4 => array('Emergency & trauma', '', 'Call now'),
        ); ?>
        <div class="row g-3">
          <?php foreach($tile_defaults as $n => $def): ?>
          <div class="col-md-4"><label class="form-label">Tile <?php echo $n; ?> — Title</label><input type="text" class="form-control" name="home_section_data[quick_tiles][tile_<?php echo $n; ?>_title]" value="<?php echo html_escape(isset($tiles['tile_'.$n.'_title']) ? $tiles['tile_'.$n.'_title'] : $def[0]); ?>"></div>
          <div class="col-md-4"><label class="form-label">Tile <?php echo $n; ?> — Subtitle</label><input type="text" class="form-control" name="home_section_data[quick_tiles][tile_<?php echo $n; ?>_sub]" value="<?php echo html_escape(isset($tiles['tile_'.$n.'_sub']) ? $tiles['tile_'.$n.'_sub'] : $def[1]); ?>"></div>
          <div class="col-md-4"><label class="form-label">Tile <?php echo $n; ?> — More link</label><input type="text" class="form-control" name="home_section_data[quick_tiles][tile_<?php echo $n; ?>_more]" placeholder="<?php echo html_escape($def[2]); ?>" value="<?php echo html_escape(isset($tiles['tile_'.$n.'_more']) ? $tiles['tile_'.$n.'_more'] : ''); ?>"></div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Panel: Departments -->
      <div class="hsec-panel" data-panel="departments">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-violet"><i class="fas fa-stethoscope"></i></span>
          <div><h3>Departments / Specialties <span class="fcms-auto-tag"><i class="fas fa-database me-1"></i>Auto</span></h3><div class="sub">Pulled from <strong>Admin → Departments</strong> automatically.</div></div>
        </div>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[departments][kicker]" value="<?php echo html_escape(isset($depts['kicker']) ? $depts['kicker'] : 'Centres of excellence'); ?>"></div>
          <div class="col-md-4"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[departments][title]" value="<?php echo html_escape(isset($depts['title']) ? $depts['title'] : 'Our Specialties'); ?>"></div>
          <div class="col-md-4"><label class="form-label">Subtitle</label><input type="text" class="form-control" name="home_section_data[departments][subtitle]" value="<?php echo html_escape(isset($depts['subtitle']) ? $depts['subtitle'] : ''); ?>"></div>
          <div class="col-md-3"><label class="form-label">Display Count</label><input type="number" class="form-control" name="home_section_data[departments][count]" value="<?php echo (int)(isset($depts['count']) ? $depts['count'] : 12); ?>" min="4" max="24"></div>
        </div>
      </div>

      <!-- Panel: Doctors -->
      <div class="hsec-panel" data-panel="doctors">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-sky"><i class="fas fa-user-md"></i></span>
          <div><h3>Featured Doctors <span class="fcms-auto-tag"><i class="fas fa-database me-1"></i>Auto</span></h3><div class="sub">Pulled from staff marked as Doctors. Manage in <strong>Admin → Staff</strong>.</div></div>
        </div>
        <div class="row g-3">
          <div class="col-md-5"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[doctors][kicker]" value="<?php echo html_escape(isset($docs_sec['kicker']) ? $docs_sec['kicker'] : ''); ?>"></div>
          <div class="col-md-5"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[doctors][title]" value="<?php echo html_escape(isset($docs_sec['title']) ? $docs_sec['title'] : 'Our Doctors'); ?>"></div>
          <div class="col-md-2"><label class="form-label">Display Count</label><input type="number" class="form-control" name="home_section_data[doctors][count]" value="<?php echo (int)(isset($docs_sec['count']) ? $docs_sec['count'] : 4); ?>" min="2" max="12"></div>
        </div>
      </div>

      <!-- Panel: Stats -->
      <div class="hsec-panel" data-panel="stats">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-green"><i class="fas fa-chart-bar"></i></span>
          <div><h3>Stats / Counters</h3><div class="sub">Animated trust metrics — beds, surgeries, recovery rate, etc.</div></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[stats][kicker]" value="<?php echo html_escape(isset($stats['kicker']) ? $stats['kicker'] : ''); ?>"></div>
          <div class="col-md-8"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[stats][title]" value="<?php echo html_escape(isset($stats['title']) ? $stats['title'] : ''); ?>"></div>
        </div>
        <label class="form-label">Stat Items</label>
        <div class="fcms-hint-text">Value · Label · Trend (optional)</div>
        <div id="fcms-stat-rows">
          <?php foreach ($stat_items as $i => $stat): ?>
          <div class="fcms-stat-row">
            <input type="text" class="form-control" name="home_section_data[stats][items][<?php echo $i; ?>][value]" placeholder="500+" value="<?php echo html_escape(isset($stat['value']) ? $stat['value'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[stats][items][<?php echo $i; ?>][label]" placeholder="Beds" value="<?php echo html_escape(isset($stat['label']) ? $stat['label'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[stats][items][<?php echo $i; ?>][trend]" placeholder="Trend (optional)" value="<?php echo html_escape(isset($stat['trend']) ? $stat['trend'] : ''); ?>">
            <button type="button" class="fcms-remove-btn" onclick="fcmsRemoveStatRow(this)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddStatRow()"><i class="fas fa-plus me-1"></i> Add Stat</button>
      </div>

      <!-- Panel: Locations -->
      <div class="hsec-panel" data-panel="locations">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-red"><i class="fas fa-map-marker-alt"></i></span>
          <div><h3>Locations / Branches</h3><div class="sub">Multi-branch cards with hours, phone &amp; map links.</div></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[locations][kicker]" value="<?php echo html_escape(isset($locs['kicker']) ? $locs['kicker'] : ''); ?>"></div>
          <div class="col-md-8"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[locations][title]" value="<?php echo html_escape(isset($locs['title']) ? $locs['title'] : 'Our Locations'); ?>"></div>
        </div>
        <label class="form-label">Location Items</label>
        <div class="fcms-hint-text">Name · Tag · Address · Hours · Phone · Map URL</div>
        <div id="fcms-loc-rows">
          <?php foreach ($loc_items as $i => $loc): ?>
          <div class="fcms-stat-row fcms-stat-row-locations">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][name]" placeholder="Branch name" value="<?php echo html_escape(isset($loc['name']) ? $loc['name'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][tag]" placeholder="OPD &amp; diagnostics" value="<?php echo html_escape(isset($loc['tag']) ? $loc['tag'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][address]" placeholder="Full address" value="<?php echo html_escape(isset($loc['address']) ? $loc['address'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][hours]" placeholder="Mon–Sat" value="<?php echo html_escape(isset($loc['hours']) ? $loc['hours'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][phone]" placeholder="Phone" value="<?php echo html_escape(isset($loc['phone']) ? $loc['phone'] : ''); ?>">
            <input type="text" class="form-control" name="home_section_data[locations][items][<?php echo $i; ?>][map_url]" placeholder="Map URL" value="<?php echo html_escape(isset($loc['map_url']) ? $loc['map_url'] : ''); ?>">
            <button type="button" class="fcms-remove-btn" onclick="fcmsRemoveLocRow(this)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddLocRow()"><i class="fas fa-plus me-1"></i> Add Location</button>
      </div>

      <!-- Panel: Marquee -->
      <div class="hsec-panel" data-panel="marquee">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-purple"><i class="fas fa-stream"></i></span>
          <div><h3>Marquee Strip</h3><div class="sub">Rolling announcement banner — camps, awareness, hospital updates.</div></div>
        </div>
        <label class="form-label">Items <small class="text-muted fw-normal">(one per line)</small></label>
        <textarea class="form-control fcms-textarea-marquee" name="home_section_data[marquee][items]" rows="10" placeholder="NABL accredited&#10;NABH certified&#10;96% same-day appointments&#10;Reports in 4 hours&#10;Cashless on 38 insurers"><?php echo html_escape($marq_items); ?></textarea>
      </div>

      <!-- Panel: How It Works -->
      <div class="hsec-panel" data-panel="how_it_works">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-amber"><i class="fas fa-list-ol"></i></span>
          <div><h3>How It Works</h3><div class="sub">Patient journey steps — add as many as you need. <strong>3–6 steps</strong> works best.</div></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-12"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[how_it_works][title]" value="<?php echo html_escape(isset($how['title']) ? $how['title'] : 'Three steps. No paperwork.'); ?>"></div>
        </div>
        <label class="form-label">Step Items</label>
        <div class="fcms-hint-text">Step number is auto-generated. Title · Description</div>
        <div id="fcms-step-rows">
          <?php foreach ($step_items as $i => $st): ?>
          <div class="fcms-stat-row fcms-stat-row-2col-top">
            <input type="text" class="form-control" name="home_section_data[how_it_works][items][<?php echo $i; ?>][title]" placeholder="Step title (e.g. Pick a slot)" value="<?php echo html_escape(isset($st['title']) ? $st['title'] : ''); ?>">
            <textarea class="form-control" rows="2" name="home_section_data[how_it_works][items][<?php echo $i; ?>][desc]" placeholder="Step description..."><?php echo html_escape(isset($st['desc']) ? $st['desc'] : ''); ?></textarea>
            <button type="button" class="fcms-remove-btn" onclick="fcmsRemoveStepRow(this)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddStepRow()"><i class="fas fa-plus me-1"></i> Add Step</button>
      </div>

      <!-- Panel: Testimonials -->
      <div class="hsec-panel" data-panel="testimonials">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-pink"><i class="fas fa-quote-left"></i></span>
          <div><h3>Testimonials</h3><div class="sub">Patient quotes with star ratings — up to 5 cards.</div></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[testimonials][kicker]" value="<?php echo html_escape(isset($testi['kicker']) ? $testi['kicker'] : ''); ?>"></div>
          <div class="col-md-8"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[testimonials][title]" value="<?php echo html_escape(isset($testi['title']) ? $testi['title'] : 'What Patients Say'); ?>"></div>
        </div>
        <label class="form-label">Testimonial Items</label>
        <div class="fcms-hint-text">Patient/customer name · Quote</div>
        <div id="fcms-testi-rows">
          <?php foreach ($testi_items as $i => $t): ?>
          <div class="fcms-stat-row fcms-stat-row-2col-top">
            <input type="text" class="form-control" name="home_section_data[testimonials][items][<?php echo $i; ?>][name]" placeholder="Patient name" value="<?php echo html_escape(isset($t['name']) ? $t['name'] : ''); ?>">
            <textarea class="form-control" rows="2" name="home_section_data[testimonials][items][<?php echo $i; ?>][quote]" placeholder="Their feedback / quote..."><?php echo html_escape(isset($t['quote']) ? $t['quote'] : ''); ?></textarea>
            <button type="button" class="fcms-remove-btn" onclick="fcmsRemoveTestiRow(this)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddTestiRow()"><i class="fas fa-plus me-1"></i> Add Testimonial</button>
      </div>

      <!-- Panel: TPAs -->
      <div class="hsec-panel" data-panel="tpas">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-teal"><i class="fas fa-shield-alt"></i></span>
          <div><h3>TPAs / Insurance Partners</h3><div class="sub">Cashless cover logo strip — insurance partners and TPAs.</div></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label">Kicker</label><input type="text" class="form-control" name="home_section_data[tpas][kicker]" placeholder="Cashless cover" value="<?php echo html_escape(isset($tpas['kicker']) ? $tpas['kicker'] : 'Cashless cover'); ?>"></div>
          <div class="col-md-8"><label class="form-label">Section Title</label><input type="text" class="form-control" name="home_section_data[tpas][title]" placeholder="Cashless on 38 insurers &amp; all major TPAs." value="<?php echo html_escape(isset($tpas['title']) ? $tpas['title'] : 'Cashless on 38 insurers & all major TPAs.'); ?>"></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-12"><label class="form-label">Subtitle</label><textarea rows="2" class="form-control" name="home_section_data[tpas][subtitle]" placeholder="Bring the card. We handle the paperwork..."><?php echo html_escape(isset($tpas['subtitle']) ? $tpas['subtitle'] : 'Bring the card. We handle the paperwork. Pre-authorisation in under 90 minutes, on average — including weekends.'); ?></textarea></div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6"><label class="form-label">CTA text</label><input type="text" class="form-control" name="home_section_data[tpas][cta_text]" placeholder="Check your insurer" value="<?php echo html_escape(isset($tpas['cta_text']) ? $tpas['cta_text'] : 'Check your insurer'); ?>"></div>
          <div class="col-md-6"><label class="form-label">CTA URL</label><input type="text" class="form-control" name="home_section_data[tpas][cta_url]" placeholder="/frontend/contactus or absolute URL" value="<?php echo html_escape(isset($tpas['cta_url']) ? $tpas['cta_url'] : ''); ?>"></div>
        </div>
        <label class="form-label">Insurer / TPA Items</label>
        <div class="fcms-hint-text">Just the partner name (e.g., "Star Health"). Add as many as you need.</div>
        <div id="fcms-tpa-rows">
          <?php foreach ($tpas_items as $i => $tp): ?>
          <div class="fcms-stat-row fcms-stat-row-single">
            <input type="text" class="form-control" name="home_section_data[tpas][items][<?php echo $i; ?>][name]" placeholder="Insurer / TPA name" value="<?php echo html_escape(isset($tp['name']) ? $tp['name'] : ''); ?>">
            <button type="button" class="fcms-remove-btn" onclick="fcmsRemoveTpaRow(this)"><i class="fas fa-times"></i></button>
          </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddTpaRow()"><i class="fas fa-plus me-1"></i> Add Insurer / TPA</button>
      </div>

      <!-- Panel: CTA -->
      <div class="hsec-panel" data-panel="cta">
        <div class="hsec-panel-head">
          <span class="hsec-ic ic-yellow"><i class="fas fa-calendar-check"></i></span>
          <div><h3>Booking / CTA Banner</h3><div class="sub">Footer call-to-action banner driving appointments.</div></div>
        </div>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Title</label><input type="text" class="form-control" name="home_section_data[cta][title]" value="<?php echo html_escape(isset($cta_sec['title']) ? $cta_sec['title'] : 'Ready to Book?'); ?>"></div>
          <div class="col-md-6"><label class="form-label">Subtitle</label><input type="text" class="form-control" name="home_section_data[cta][subtitle]" value="<?php echo html_escape(isset($cta_sec['subtitle']) ? $cta_sec['subtitle'] : ''); ?>"></div>
          <div class="col-md-4"><label class="form-label">Button Text</label><input type="text" class="form-control" name="home_section_data[cta][button_text]" value="<?php echo html_escape(isset($cta_sec['button_text']) ? $cta_sec['button_text'] : 'Book Now'); ?>"></div>
          <div class="col-md-4"><label class="form-label">Bullet 1</label><input type="text" class="form-control" name="home_section_data[cta][bullet_1]" value="<?php echo html_escape(isset($cta_sec['bullet_1']) ? $cta_sec['bullet_1'] : ''); ?>"></div>
          <div class="col-md-4"><label class="form-label">Bullet 2</label><input type="text" class="form-control" name="home_section_data[cta][bullet_2]" value="<?php echo html_escape(isset($cta_sec['bullet_2']) ? $cta_sec['bullet_2'] : ''); ?>"></div>
          <div class="col-md-4"><label class="form-label">Bullet 3</label><input type="text" class="form-control" name="home_section_data[cta][bullet_3]" value="<?php echo html_escape(isset($cta_sec['bullet_3']) ? $cta_sec['bullet_3'] : ''); ?>"></div>
        </div>
      </div>

    </main>
  </div><!-- /hsec-shell -->

  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
  (function(){
    var list      = document.getElementById('hsecList');
    var orderBox  = document.getElementById('hsecOrderInputs');
    var visCount  = document.getElementById('hsecVisibleCount');
    var panels    = document.querySelectorAll('.hsec-panel');
    if (!list) return;

    var DEFAULT_ORDER = <?php echo json_encode($hsec_default_order); ?>;

    function renumber(){
      var n = 0;
      Array.prototype.forEach.call(list.children, function(li){
        if (li.classList.contains('hsec-pinned')) return;
        n++;
        var num = li.querySelector('.hsec-num');
        if (num) num.textContent = n;
      });
    }

    function rebuildOrderInputs(){
      var html = '';
      Array.prototype.forEach.call(list.children, function(li){
        if (li.classList.contains('hsec-pinned')) return;
        var k = li.getAttribute('data-key');
        html += '<input type="hidden" name="home_section_data[order][]" value="' + k + '">';
      });
      orderBox.innerHTML = html;
    }

    function updateVisibleCount(){
      var on = 0;
      Array.prototype.forEach.call(list.children, function(li){
        var cb = li.querySelector('.hsec-show-toggle');
        if (cb && cb.checked) on++;
      });
      visCount.textContent = on;
    }

    function activate(key){
      Array.prototype.forEach.call(list.children, function(li){
        li.classList.toggle('active', li.getAttribute('data-key') === key);
      });
      Array.prototype.forEach.call(panels, function(p){
        p.classList.toggle('active', p.getAttribute('data-panel') === key);
      });
    }

    // click row to switch active panel (but not when clicking the checkbox or drag handle)
    list.addEventListener('click', function(e){
      if (e.target.classList.contains('form-check-input')) return;
      if (e.target.classList.contains('hsec-drag')) return;
      var li = e.target.closest('.hsec-item');
      if (!li) return;
      activate(li.getAttribute('data-key'));
    });

    // single source of truth: sidebar checkbox is the actual form input
    list.addEventListener('change', function(e){
      if (!e.target.classList.contains('hsec-show-toggle')) return;
      var li = e.target.closest('.hsec-item');
      if (li) li.classList.toggle('disabled', !e.target.checked);
      updateVisibleCount();
    });

    // Sortable.js with pinned-hero guard
    new Sortable(list, {
      handle: '.hsec-drag',
      animation: 180,
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      filter: '.hsec-pinned',
      preventOnFilter: true,
      onMove: function(evt){
        if (evt.related && evt.related.classList.contains('hsec-pinned')) return false;
        return true;
      },
      onEnd: function(evt){
        renumber();
        rebuildOrderInputs();
        evt.item.classList.remove('flash');
        void evt.item.offsetWidth;
        evt.item.classList.add('flash');
      }
    });

    window.hsecResetToDefault = function(){
      if (!confirm('This will reset ALL Home Section content to factory defaults. Theme and other settings will NOT change. Continue?')) return;
      var btn = document.querySelector('[onclick="hsecResetToDefault()"]');
      if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Resetting…'; }
      var _rd = {}; if (typeof SH_CSRF_NAME !== 'undefined') _rd[SH_CSRF_NAME] = SH_CSRF_TOKEN;
      $.post('<?php echo site_url('admin/frontcms/reset_home_sections'); ?>', _rd, function(res){
        if (res && res.status === 'success') {
          location.reload();
        } else {
          alert('Reset failed: ' + (res.error || 'Unknown error'));
          if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-history me-1"></i>Reset to Default'; }
        }
      }, 'json').fail(function(){ alert('Reset failed. Please try again.'); if (btn) { btn.disabled = false; } });
    };

    window.hsecResetOrder = function(){
      DEFAULT_ORDER.forEach(function(key){
        var li = list.querySelector('.hsec-item[data-key="' + key + '"]');
        if (li) list.appendChild(li);
      });
      renumber();
      rebuildOrderInputs();
    };

    updateVisibleCount();
  })();
  </script>

</div><!-- /tab-sections -->


<!-- ══════════════════════════════
     TAB 4 — PAGE SECTIONS
══════════════════════════════ -->
<div class="tab-pane fade" id="tab-pages" role="tabpanel">

  <!-- Section A: Special theme pages -->
  <div class="fcms-section-title mt-0">Special Pages <span class="fcms-section-subtitle">— theme-controlled (not CMS pages)</span></div>
  <div class="row g-3 mb-4">

    <div class="col-md-4">
      <div class="card h-100 fcms-page-card-light">
        <div class="card-header py-2 px-3 d-flex align-items-center gap-2">
          <span class="hsec-ic ic-blue"><i class="fas fa-calendar-check"></i></span>
          <div><strong class="fcms-strong-13">Appointment Page</strong><span class="fcms-meta-small">URL: /appointment</span></div>
          <span class="badge ms-auto fcms-badge-success">Dynamic form</span>
        </div>
        <div class="card-body p-3">
          <div class="fcms-auto-info is-sm mb-3"><i class="fas fa-info-circle"></i>The form is fully dynamic. Only heading text is editable here.</div>
          <div class="mb-2"><label class="form-label">Page Heading</label><input type="text" class="form-control" name="static_pages_data[appointment][heading]" value="<?php echo html_escape(isset($sp_appt['heading']) ? $sp_appt['heading'] : 'Book an Appointment'); ?>"></div>
          <div class="mb-2"><label class="form-label">Sub-heading <small class="text-muted fw-normal">(optional)</small></label><input type="text" class="form-control" name="static_pages_data[appointment][subheading]" value="<?php echo html_escape(isset($sp_appt['subheading']) ? $sp_appt['subheading'] : ''); ?>"></div>
          <div><label class="form-label">Booking Note <small class="text-muted fw-normal">(shown below form)</small></label><input type="text" class="form-control" name="static_pages_data[appointment][note]" value="<?php echo html_escape(isset($sp_appt['note']) ? $sp_appt['note'] : ''); ?>"></div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 fcms-page-card-light">
        <div class="card-header py-2 px-3 d-flex align-items-center gap-2">
          <span class="hsec-ic ic-amber"><i class="fas fa-calendar-alt"></i></span>
          <div><strong class="fcms-strong-13">Annual Calendar</strong><span class="fcms-meta-small">URL: /annual-calendar</span></div>
          <span class="badge ms-auto fcms-badge-success">Auto from DB</span>
        </div>
        <div class="card-body p-3">
          <div class="fcms-auto-info is-sm mb-3"><i class="fas fa-info-circle"></i>Holidays, Activities &amp; Vacations managed in <strong>Admin → Holiday</strong>.</div>
          <div><label class="form-label">Page Heading</label><input type="text" class="form-control" name="static_pages_data[annual_calendar][heading]" value="<?php echo html_escape(isset($sp_cal['heading']) ? $sp_cal['heading'] : 'Annual Calendar'); ?>"></div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 fcms-page-card-light">
        <div class="card-header py-2 px-3 d-flex align-items-center gap-2">
          <span class="hsec-ic ic-red"><i class="fas fa-exclamation-triangle"></i></span>
          <div><strong class="fcms-strong-13">404 Error Page</strong><span class="fcms-meta-small">Shown when a page is not found</span></div>
        </div>
        <div class="card-body p-3">
          <div class="mb-2"><label class="form-label">Heading</label><input type="text" class="form-control" name="static_pages_data[show_404][heading]" value="<?php echo html_escape(isset($sp_404['heading']) ? $sp_404['heading'] : 'Page Not Found'); ?>"></div>
          <div><label class="form-label">Message</label><input type="text" class="form-control" name="static_pages_data[show_404][message]" value="<?php echo html_escape(isset($sp_404['message']) ? $sp_404['message'] : "The page you're looking for doesn't exist."); ?>"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- Section B: CMS Pages -->
  <div class="fcms-section-title">CMS Pages <span class="fcms-section-subtitle">— layout type &amp; structured content per page</span></div>

  <div class="row g-4">
    <div class="col-12">
      <div class="alert alert-secondary py-2 fcms-alert-compact">
        <i class="fas fa-info-circle me-1"></i>
        Select a page to update its layout. <strong>Events / Gallery / Notice</strong> pages are auto-list — content is managed via <strong>CMS → Programs</strong>.
      </div>
    </div>

    <!-- Left: page list -->
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
          <strong class="fcms-strong-13-inline">CMS Pages <span class="fcms-count-small">(<?php echo count($cms_pages ?? array()); ?>)</span></strong>
          <a href="<?php echo site_url('admin/front/page'); ?>" class="btn btn-xs btn-outline-primary fcms-btn-mini"><i class="fas fa-plus me-1"></i>New Page</a>
        </div>
        <div class="px-3 py-2 fcms-list-header">
          <div class="position-relative">
            <i class="fas fa-search fcms-search-icon"></i>
            <input type="text" id="fcms-page-search" oninput="fcmsFilterPages(this.value)" class="form-control form-control-sm fcms-search-input" placeholder="Search pages...">
          </div>
          <div id="fcms-page-empty" class="fcms-page-empty-hint d-none" ><i class="fas fa-info-circle me-1"></i>No matches</div>
        </div>
        <div class="list-group list-group-flush fcms-page-list-scroll" id="fcms-page-list">
          <?php if (!empty($cms_pages)): foreach ($cms_pages as $p): ?>
          <?php $p_layout = isset($p['layout_type']) ? $p['layout_type'] : 'blank'; ?>
          <?php
          $badge_styles = array(
            'blank'   => 'background:#f0f2f5;color:#667085;',
            'contact' => 'background:#dde3ff;color:#4361ee;',
            'faq'     => 'background:#fef3c7;color:#92400e;',
            'team'    => 'background:#f3e8ff;color:#7c3aed;',
          );
          $bs = isset($badge_styles[$p_layout]) ? $badge_styles[$p_layout] : $badge_styles['blank'];
          ?>
          <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2"
            data-page-id="<?php echo (int)$p['id']; ?>"
            data-page-title="<?php echo html_escape($p['title']); ?>"
            data-page-layout="<?php echo html_escape($p_layout); ?>"
            data-page-sections="<?php echo html_escape(isset($p['page_section_data']) ? $p['page_section_data'] : '{}'); ?>"
            onclick="fcmsLoadPage(this)">
            <?php echo html_escape($p['title']); ?>
            <span class="badge" style="font-size:10px;<?php echo $bs; ?>"><?php echo ucfirst(html_escape($p_layout)); ?></span>
          </button>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>

    <!-- Right: page editor -->
    <div class="col-md-8">
      <div class="card" id="fcms-page-editor-card">
        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
          <strong class="fcms-strong-13-inline" id="fcms-page-editor-title">Select a page</strong>
        </div>
        <div class="card-body p-3" id="fcms-page-editor-body" class="d-none">
          <input type="hidden" id="fcms-edit-page-id" value="">

          <!-- Layout type pills -->
          <div class="mb-3" id="fcms-layout-type-row">
            <label class="form-label">Page Layout Type</label>
            <div class="btn-group d-flex flex-wrap gap-1" role="group">
              <input type="radio" class="btn-check" name="fcms_layout_type" id="lt-blank"   value="blank"   autocomplete="off"><label class="btn btn-outline-secondary rounded" for="lt-blank">Standard / Blank</label>
              <input type="radio" class="btn-check" name="fcms_layout_type" id="lt-contact" value="contact" autocomplete="off"><label class="btn btn-outline-secondary rounded" for="lt-contact"><i class="fas fa-map-marker-alt me-1"></i>Contact</label>
              <input type="radio" class="btn-check" name="fcms_layout_type" id="lt-faq"     value="faq"     autocomplete="off"><label class="btn btn-outline-secondary rounded" for="lt-faq"><i class="fas fa-question-circle me-1"></i>FAQ</label>
              <input type="radio" class="btn-check" name="fcms_layout_type" id="lt-team"    value="team"    autocomplete="off"><label class="btn btn-outline-secondary rounded" for="lt-team"><i class="fas fa-users me-1"></i>Our Team</label>
            </div>
            <div class="form-text">Changing layout type changes how this page renders on the front end.</div>
          </div>

          <!-- Blank -->
          <div class="fcms-page-type-panel" id="fcms-panel-blank">
            <div class="fcms-auto-info"><i class="fas fa-edit"></i>This page uses standard CKEditor content. <a href="<?php echo site_url('admin/front/page'); ?>">Open full page editor</a> to edit the HTML content.</div>
          </div>

          <!-- Contact -->
          <div class="fcms-page-type-panel" id="fcms-panel-contact">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Address</label><textarea class="form-control" id="fcms-contact-address" rows="2" placeholder="123 Medical Street..."></textarea></div>
              <div class="col-md-6"><label class="form-label">Office Hours</label><textarea class="form-control" id="fcms-contact-hours" rows="2" placeholder="Mon–Sat: 9 AM – 6 PM"></textarea></div>
              <div class="col-md-6"><label class="form-label">Phone Number(s)</label><input type="text" class="form-control" id="fcms-contact-phone" placeholder="+91 98765 43210"></div>
              <div class="col-md-6"><label class="form-label">Email Address</label><input type="email" class="form-control" id="fcms-contact-email" placeholder="info@hospital.com"></div>
              <div class="col-12"><label class="form-label">Google Maps Embed URL <small class="text-muted fw-normal">(the src= value from Maps embed code)</small></label><input type="text" class="form-control" id="fcms-contact-map" placeholder="https://www.google.com/maps/embed?pb=..."></div>
            </div>
          </div>

          <!-- FAQ -->
          <div class="fcms-page-type-panel" id="fcms-panel-faq">
            <div id="fcms-faq-items"></div>
            <button type="button" class="fcms-add-btn mt-2" onclick="fcmsAddFaqItem()"><i class="fas fa-plus me-1"></i> Add Question</button>
          </div>

          <!-- Team -->
          <div class="fcms-page-type-panel" id="fcms-panel-team">
            <div class="fcms-auto-info mb-3"><i class="fas fa-info-circle"></i>Staff cards are auto-pulled from the database. You control the heading and display count.</div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Page Heading</label><input type="text" class="form-control" id="fcms-team-heading" placeholder="Meet Our Team"></div>
              <div class="col-md-6"><label class="form-label">Sub-heading</label><input type="text" class="form-control" id="fcms-team-subheading" placeholder="Experienced specialists..."></div>
              <div class="col-md-3"><label class="form-label">Display Count</label><input type="number" class="form-control" id="fcms-team-count" value="8" min="4" max="24"></div>
            </div>
          </div>

          <!-- Auto-list info -->
          <div class="fcms-page-type-panel" id="fcms-panel-autolist">
            <div class="fcms-auto-info is-success-strong">
              <i class="fas fa-check-circle"></i>
              <strong>Auto-list page — no layout editor needed.</strong><br>
              <span>Content items are managed via <strong>CMS → Programs → Events / Gallery / Notice</strong>.<br>
              Page heading is set via <strong>CMS → Pages</strong> (Title field).<br>
              Pagination and display are handled automatically.</span>
            </div>
          </div>

          <div class="mt-3 d-flex justify-content-end" id="fcms-page-save-row">
            <button type="button" class="btn btn-primary btn-sm px-4" onclick="fcmsSavePageSection()">
              <i class="fas fa-save me-1"></i> Save Page Layout
            </button>
          </div>
        </div>
        <div class="card-body text-center text-muted py-5 fcms-editor-placeholder" id="fcms-page-editor-placeholder">
          <i class="fas fa-hand-point-left me-2"></i>Select a page from the list to edit its layout
        </div>
      </div>
    </div>
  </div>

</div><!-- /tab-pages -->


<!-- ══════════════════════════════
     TAB 5 — SOCIAL
══════════════════════════════ -->
<div class="tab-pane fade" id="tab-social" role="tabpanel">
  <?php
    $s_rows = array(
      array('name'=>'fb_url',        'val'=>$frontcmslist->fb_url,        'label'=>$this->lang->line('facebook_url'),    'icon'=>'fab fa-facebook-f',   'bg'=>'#1877f2',                                                                'host'=>'facebook.com',  'ph'=>'https://facebook.com/your-page'),
      array('name'=>'twitter_url',   'val'=>$frontcmslist->twitter_url,   'label'=>$this->lang->line('twitter_url'),     'icon'=>'fab fa-twitter',    'bg'=>'#0f0f0f',                                                                'host'=>'x.com',         'ph'=>'https://x.com/your-handle'),
      array('name'=>'youtube_url',   'val'=>$frontcmslist->youtube_url,   'label'=>$this->lang->line('youtube_url'),     'icon'=>'fab fa-youtube',      'bg'=>'#ff0000',                                                                'host'=>'youtube.com',   'ph'=>'https://youtube.com/@your-channel'),
      array('name'=>'linkedin_url',  'val'=>$frontcmslist->linkedin_url,  'label'=>$this->lang->line('linkedin_url'),    'icon'=>'fab fa-linkedin-in',  'bg'=>'#0a66c2',                                                                'host'=>'linkedin.com',  'ph'=>'https://linkedin.com/company/your-page'),
      array('name'=>'instagram_url', 'val'=>$frontcmslist->instagram_url, 'label'=>$this->lang->line('instagram_url'),   'icon'=>'fab fa-instagram',    'bg'=>'linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)',          'host'=>'instagram.com', 'ph'=>'https://instagram.com/your-handle'),
      array('name'=>'pinterest_url', 'val'=>$frontcmslist->pinterest_url, 'label'=>$this->lang->line('pinterest_url'),   'icon'=>'fab fa-pinterest-p',  'bg'=>'#e60023',                                                                'host'=>'pinterest.com', 'ph'=>'https://pinterest.com/your-handle'),
      array('name'=>'google_plus',   'val'=>$frontcmslist->google_plus,   'label'=>$this->lang->line('google_plus_url'), 'icon'=>'fab fa-google',       'bg'=>'#ea4335',                                                                'host'=>'g.page',        'ph'=>'https://g.page/your-business'),
    );
    $s_filled = 0;
    foreach ($s_rows as $r) { if (!empty($r['val'])) { $s_filled++; } }
  ?>
  <div class="row">
    <div class="col-lg-7">

      <div class="fcms-summary">
        <div class="ic <?php echo $s_filled > 0 ? '' : 'dim'; ?>"><i class="fas fa-link"></i></div>
        <div class="tx"><b><?php echo $s_filled; ?> of 7</b> social channels linked &middot; empty channels are hidden on the public site</div>
      </div>

      <div class="fcms-modcard">
        <div class="fcms-modcard-h">
          <div class="ic"><i class="fas fa-share-alt"></i></div>
          <div class="ti">
            <div class="nm">Social Media URLs</div>          </div>
          <span class="meta <?php echo $s_filled > 0 ? 'on' : ''; ?>"><?php echo $s_filled; ?> / 7 linked</span>
        </div>
        <div class="fcms-modcard-body">

          <?php foreach ($s_rows as $r): $is_on = !empty($r['val']); ?>
          <div class="fcms-soc-row">
            <div class="fcms-soc-icon" style="background:<?php echo $r['bg']; ?>;"><i class="<?php echo $r['icon']; ?>"></i></div>
            <div class="fcms-soc-body">
              <div class="fcms-soc-label">
                <span class="dot <?php echo $is_on ? 'on' : 'off'; ?>"></span>
                <span><?php echo $r['label']; ?></span>
                <span class="url"><?php echo $r['host']; ?></span>
              </div>
              <div class="fcms-soc-input">
                <span class="pfx"><i class="fas fa-link fcms-pfx-icon"></i></span>
                <input type="url" name="<?php echo $r['name']; ?>" value="<?php echo html_escape(set_value($r['name'], $r['val'])); ?>" placeholder="<?php echo html_escape($r['ph']); ?>">
              </div>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </div>
</div><!-- /tab-social -->


                    </div><!-- /tab-content -->
                </div><!-- /card-body -->

                <?php if ($this->rbac->hasPrivilege('front_cms_setting', 'can_edit')): ?>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-5"><i class="fa fa-check-circle me-1"></i><?php echo $this->lang->line('save'); ?></button>
                    <span class="custom_loader"></span>
                </div>
                <?php endif; ?>

            </form>
        </div><!-- /.card -->
    </div>
</div>

<!-- Front CMS theme adaptation layer moved to backend/css/sh-theme.css (P32a — Front CMS Block 3) -->

<script>
// ── Theme colors config (from PHP) ──
const fcmsThemeColors = <?php echo json_encode(array_map(function($t){ return isset($t['colors']) ? $t['colors'] : array(); }, $active_themes)); ?>;
let fcmsCurrentTheme = '<?php echo html_escape($cur_theme ?: 'atrium'); ?>';
let fcmsStatRowCount = <?php echo count($stat_items); ?>;
let fcmsLocRowCount  = <?php echo count($loc_items); ?>;
let fcmsTestiRowCount = <?php echo count($testi_items); ?>;
let fcmsTpaRowCount   = <?php echo count($tpas_items); ?>;
let fcmsStepRowCount  = <?php echo count($step_items); ?>;

// ── v2: Theme & Layout state machine ────────────────
(function(){
    const root = document.getElementById('fcms2-bootstrap');
    if (!root) return;
    let cfg = {};
    try { cfg = JSON.parse(root.textContent || '{}'); } catch(e) { cfg = {}; }
    const LIVE = cfg.live || {theme:'atrium', color:'aurora', layout:'hospital'};
    const PICK = Object.assign({}, LIVE);
    const PALETTES    = cfg.palettes    || {};
    const COLOR_HEX   = cfg.colorHex    || {};
    const THEME_LABEL = cfg.themeLabels || {};

    const cap = (s) => s ? s.charAt(0).toUpperCase()+s.slice(1) : '';
    const $  = (s,r=document)=>r.querySelector(s);
    const $$ = (s,r=document)=>Array.from(r.querySelectorAll(s));

    // Theme cards
    $$('.fcms2-tcard').forEach(card => {
        card.addEventListener('click', () => {
            PICK.theme = card.dataset.theme;
            // If picked theme has its own palette and the current colour is not in it, fall back to first
            const list = PALETTES[PICK.theme] || [];
            if (list.length && list.indexOf(PICK.color) === -1) PICK.color = list[0];
            render();
        });
    });

    // Colour swatches
    $$('.fcms2-swatch').forEach(sw => {
        sw.addEventListener('click', (e) => { e.preventDefault(); PICK.color = sw.dataset.color; render(); });
    });

    // Layout cards
    $$('.fcms2-lcard').forEach(lc => {
        lc.addEventListener('click', () => { PICK.layout = lc.dataset.layout; render(); });
    });

    // Live-update the preview photo while typing the visible layout's hero image URL
    $$('.fcms2-hm-group input[data-hm="image"]').forEach(inp => {
        inp.addEventListener('input', () => {
            const grp = inp.closest('.fcms2-hm-group');
            if (grp && grp.dataset.layout === PICK.layout) {
                const pv = document.querySelector('#fcms2-pv-hero .pv-hero-photo');
                if (pv) pv.style.backgroundImage = inp.value ? "url('"+inp.value+"')" : '';
            }
        });
    });

    // Mobile / Desktop preview toggle
    $$('#fcms2-pv-toggle button').forEach(b => {
        b.addEventListener('click', () => {
            $$('#fcms2-pv-toggle button').forEach(x=>x.classList.remove('on'));
            b.classList.add('on');
            $('#fcms2-pv-device').classList.toggle('desk', b.dataset.mode === 'desk');
        });
    });

    // Discard
    const discardBtn = $('#fcms2-btn-discard');
    if (discardBtn) discardBtn.addEventListener('click', () => {
        PICK.theme = LIVE.theme; PICK.color = LIVE.color; PICK.layout = LIVE.layout;
        render();
    });

    // Mix any hex toward black ~50% for the gradient end-stop
    function mixToDark(hex) {
        const h = (hex||'#0ea5e9').replace('#','');
        const r = parseInt(h.substring(0,2),16) || 0;
        const g = parseInt(h.substring(2,4),16) || 0;
        const b = parseInt(h.substring(4,6),16) || 0;
        const m = (n) => Math.max(0, Math.round(n * 0.35)).toString(16).padStart(2,'0');
        return '#'+m(r)+m(g)+m(b);
    }

    function render() {
        // Theme cards selection + Live tag + selected mark + actual radio checked
        $$('.fcms2-tcard').forEach(c => {
            const t = c.dataset.theme;
            c.classList.toggle('on', t === PICK.theme);
            const radio = c.querySelector('input[type=radio]');
            if (radio) radio.checked = (t === PICK.theme);
            const status = c.querySelector('.fcms2-tcard-status');
            if (status) {
                status.innerHTML = '';
                if (t === LIVE.theme) {
                    status.innerHTML = '<span class="fcms2-livetag"><span class="dot"></span>Live now</span>';
                } else if (t === PICK.theme) {
                    status.innerHTML = '<i class="fas fa-check-circle me-1"></i>Selected';
                }
            }
        });

        // Show only the active theme's palette
        $$('.fcms2-palette-group').forEach(g => g.classList.toggle('on', g.dataset.paletteOf === PICK.theme));

        // Palette swatches
        $$('.fcms2-swatch').forEach(s => {
            s.classList.toggle('on', s.dataset.color === PICK.color);
            // Add LIVE mini-tag on the swatch that is live, only inside the live theme's palette
            const grp = s.closest('.fcms2-palette-group');
            const themeOfPalette = grp ? grp.dataset.paletteOf : '';
            const existing = s.querySelector('.live-mini');
            if (existing) existing.remove();
            if (themeOfPalette === LIVE.theme && s.dataset.color === LIVE.color) {
                const m = document.createElement('span');
                m.className = 'live-mini';
                m.innerHTML = '<i class="fas fa-circle" style="font-size:5px;color:#12b76a;margin-right:3px"></i>LIVE';
                s.appendChild(m);
            }
        });

        // Update hidden colour input + checked state of theme radios already done above
        const hidden = $('#fcms-color-input');
        if (hidden) hidden.value = PICK.color;

        // Layout cards
        $$('.fcms2-lcard').forEach(c => {
            const l = c.dataset.layout;
            c.classList.toggle('on', l === PICK.layout);
            const radio = c.querySelector('input[type=radio]');
            if (radio) radio.checked = (l === PICK.layout);
            const existing = c.querySelector('.fcms2-lpill');
            if (existing) existing.remove();
            if (l === LIVE.layout) {
                const p = document.createElement('span');
                p.className = 'fcms2-lpill live'; p.textContent = 'Live now';
                c.appendChild(p);
            } else if (l === PICK.layout) {
                const p = document.createElement('span');
                p.className = 'fcms2-lpill on'; p.textContent = 'Selected';
                c.appendChild(p);
            }
        });

        // Per-layout hero media: show only the picked layout's fields + sync the preview photo
        $$('.fcms2-hm-group').forEach(g => { g.style.display = (g.dataset.layout === PICK.layout) ? '' : 'none'; });
        const _hmImg = document.querySelector('.fcms2-hm-group[data-layout="'+PICK.layout+'"] input[data-hm="image"]');
        const _pvPhoto = document.querySelector('#fcms2-pv-hero .pv-hero-photo');
        if (_pvPhoto) _pvPhoto.style.backgroundImage = (_hmImg && _hmImg.value) ? "url('"+_hmImg.value+"')" : '';

        // Section header "Picked" labels
        const tlbl = THEME_LABEL[PICK.theme] || cap(PICK.theme);
        const clbl = cap(PICK.color);
        const lcardEl = $('.fcms2-lcard[data-layout="'+PICK.layout+'"]');
        const llbl = lcardEl ? (lcardEl.dataset.title || cap(PICK.layout)) : cap(PICK.layout);
        if ($('#fcms2-cur-theme'))  $('#fcms2-cur-theme').textContent  = tlbl;
        if ($('#fcms2-cur-color'))  $('#fcms2-cur-color').textContent  = clbl;
        if ($('#fcms2-cur-layout')) $('#fcms2-cur-layout').innerHTML   = llbl;

        // Top live summary stays anchored to LIVE state
        const liveTitleEl = $('.fcms2-lcard[data-layout="'+LIVE.layout+'"]');
        const llive = liveTitleEl ? (liveTitleEl.dataset.title || cap(LIVE.layout)) : cap(LIVE.layout);
        if ($('#fcms2-live-summary')) {
            $('#fcms2-live-summary').innerHTML = 'Currently live · ' + (THEME_LABEL[LIVE.theme] || cap(LIVE.theme)) + ' · ' + cap(LIVE.color) + ' · ' + llive;
        }

        // Live preview
        const colorHex = COLOR_HEX[PICK.color] || '#0ea5e9';
        const grad = 'linear-gradient(135deg, '+colorHex+', '+mixToDark(colorHex)+')';
        if ($('#fcms2-pv-nav'))  $('#fcms2-pv-nav').style.background  = grad;
        if ($('#fcms2-pv-hero')) $('#fcms2-pv-hero').style.background = grad;
        if ($('#fcms2-pv-device')) $('#fcms2-pv-device').style.setProperty('--pv-accent', colorHex);
        if ($('#fcms2-pv-h1'))   $('#fcms2-pv-h1').textContent = lcardEl ? (lcardEl.dataset.h1||'') : '';
        if ($('#fcms2-pv-h2'))   $('#fcms2-pv-h2').textContent = lcardEl ? (lcardEl.dataset.h2||'') : '';
        const tilesStr = lcardEl ? (lcardEl.dataset.tiles||'') : '';
        const tiles = tilesStr ? tilesStr.split(',') : [];
        if ($('#fcms2-pv-tiles')) $('#fcms2-pv-tiles').innerHTML = tiles.map(t=>'<div class="fcms2-pv-tile">'+t+'</div>').join('');
        $$('.fcms2-pv-card .b1').forEach(b => b.style.background = colorHex);
        if ($('#fcms2-pv-foot-text')) $('#fcms2-pv-foot-text').innerHTML = tlbl + ' · ' + clbl + ' · ' + llbl;

        // Save bar diff
        const changes = [];
        if (PICK.theme  !== LIVE.theme)  changes.push({k:'Theme',  o:THEME_LABEL[LIVE.theme]||cap(LIVE.theme), n:tlbl});
        if (PICK.color  !== LIVE.color)  changes.push({k:'Colour', o:cap(LIVE.color), n:clbl});
        if (PICK.layout !== LIVE.layout) changes.push({k:'Layout', o:llive, n:llbl});
        const sb = $('#fcms2-savebar');
        if (sb) {
            if (!changes.length) {
                sb.classList.add('hidden');
            } else {
                sb.classList.remove('hidden');
                $('#fcms2-diff-count').textContent = changes.length + (changes.length === 1 ? ' change' : ' changes');
                $('#fcms2-diff-text').innerHTML = changes.map(c => c.k+' <s>'+c.o+'</s> → <b>'+c.n+'</b>').join(' &nbsp;·&nbsp; ');
            }
        }

        fcmsCurrentTheme = PICK.theme;
    }

    // First paint
    render();
})();

// ── Stat rows ──
function fcmsAddStatRow() {
    const row = document.createElement('div');
    row.className = 'fcms-stat-row';
    row.innerHTML = '<input type="text" class="form-control" name="home_section_data[stats][items][' + fcmsStatRowCount + '][value]" placeholder="e.g. 500+">'
        + '<input type="text" class="form-control" name="home_section_data[stats][items][' + fcmsStatRowCount + '][label]" placeholder="Label">'
        + '<input type="text" class="form-control" name="home_section_data[stats][items][' + fcmsStatRowCount + '][trend]" placeholder="Trend (optional)">'
        + '<button type="button" class="fcms-remove-btn" onclick="fcmsRemoveStatRow(this)"><i class="fas fa-times"></i></button>';
    document.getElementById('fcms-stat-rows').appendChild(row);
    fcmsStatRowCount++;
}
function fcmsRemoveStatRow(btn) { btn.closest('.fcms-stat-row').remove(); }

// ── Location rows ──
function fcmsAddLocRow() {
    const i = fcmsLocRowCount;
    const row = document.createElement('div');
    row.className = 'fcms-stat-row';
    row.style.gridTemplateColumns = '1fr 1fr 2fr 1fr 1fr 1fr 36px';
    row.innerHTML = '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][name]" placeholder="Branch name">'
        + '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][tag]" placeholder="OPD &amp; diagnostics">'
        + '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][address]" placeholder="Full address">'
        + '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][hours]" placeholder="Mon-Sat">'
        + '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][phone]" placeholder="Phone">'
        + '<input type="text" class="form-control" name="home_section_data[locations][items][' + i + '][map_url]" placeholder="Map URL">'
        + '<button type="button" class="fcms-remove-btn" onclick="fcmsRemoveLocRow(this)"><i class="fas fa-times"></i></button>';
    document.getElementById('fcms-loc-rows').appendChild(row);
    fcmsLocRowCount++;
}
function fcmsRemoveLocRow(btn) { btn.closest('.fcms-stat-row').remove(); }

// ── Testimonial rows ──
function fcmsAddTestiRow() {
    const i = fcmsTestiRowCount;
    const row = document.createElement('div');
    row.className = 'fcms-stat-row';
    row.style.gridTemplateColumns = '1fr 3fr 36px';
    row.style.alignItems = 'start';
    row.innerHTML = '<input type="text" class="form-control" name="home_section_data[testimonials][items][' + i + '][name]" placeholder="Patient name">'
        + '<textarea class="form-control" rows="2" name="home_section_data[testimonials][items][' + i + '][quote]" placeholder="Their feedback / quote..."></textarea>'
        + '<button type="button" class="fcms-remove-btn" onclick="fcmsRemoveTestiRow(this)"><i class="fas fa-times"></i></button>';
    document.getElementById('fcms-testi-rows').appendChild(row);
    fcmsTestiRowCount++;
}
function fcmsRemoveTestiRow(btn) { btn.closest('.fcms-stat-row').remove(); }

// ── TPA / Insurance rows ──
function fcmsAddTpaRow() {
    const i = fcmsTpaRowCount;
    const row = document.createElement('div');
    row.className = 'fcms-stat-row';
    row.style.gridTemplateColumns = '1fr 36px';
    row.innerHTML = '<input type="text" class="form-control" name="home_section_data[tpas][items][' + i + '][name]" placeholder="Insurer / TPA name">'
        + '<button type="button" class="fcms-remove-btn" onclick="fcmsRemoveTpaRow(this)"><i class="fas fa-times"></i></button>';
    document.getElementById('fcms-tpa-rows').appendChild(row);
    fcmsTpaRowCount++;
}
function fcmsRemoveTpaRow(btn) { btn.closest('.fcms-stat-row').remove(); }

// ── How It Works step rows ──
function fcmsAddStepRow() {
    const i = fcmsStepRowCount;
    const row = document.createElement('div');
    row.className = 'fcms-stat-row';
    row.style.gridTemplateColumns = '1fr 3fr 36px';
    row.style.alignItems = 'start';
    row.innerHTML = '<input type="text" class="form-control" name="home_section_data[how_it_works][items][' + i + '][title]" placeholder="Step title">'
        + '<textarea class="form-control" rows="2" name="home_section_data[how_it_works][items][' + i + '][desc]" placeholder="Step description..."></textarea>'
        + '<button type="button" class="fcms-remove-btn" onclick="fcmsRemoveStepRow(this)"><i class="fas fa-times"></i></button>';
    document.getElementById('fcms-step-rows').appendChild(row);
    fcmsStepRowCount++;
}
function fcmsRemoveStepRow(btn) { btn.closest('.fcms-stat-row').remove(); }

// ── CMS page section editor ──
const fcmsCmsPageData = <?php echo json_encode($cms_pages_js); ?>;

function fcmsFilterPages(query) {
    const q = (query || '').trim().toLowerCase();
    const buttons = document.querySelectorAll('#fcms-page-list button');
    let visible = 0;
    buttons.forEach(b => {
        const title = (b.getAttribute('data-page-title') || '').toLowerCase();
        const layout = (b.getAttribute('data-page-layout') || '').toLowerCase();
        const match = q === '' || title.indexOf(q) !== -1 || layout.indexOf(q) !== -1;
        b.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const empty = document.getElementById('fcms-page-empty');
    if (empty) empty.style.display = (visible === 0 && q !== '') ? 'block' : 'none';
}

function fcmsLoadPage(btn) {
    document.querySelectorAll('#fcms-page-list button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const pageId    = btn.getAttribute('data-page-id');
    const pageTitle = btn.getAttribute('data-page-title');
    const layout    = btn.getAttribute('data-page-layout') || 'blank';
    const sectionsRaw = btn.getAttribute('data-page-sections') || '{}';
    let sections = {};
    try { sections = JSON.parse(sectionsRaw); } catch(e) {}

    document.getElementById('fcms-page-editor-title').textContent = pageTitle;
    document.getElementById('fcms-edit-page-id').value = pageId;
    document.getElementById('fcms-page-editor-placeholder').style.display = 'none';
    document.getElementById('fcms-page-editor-body').style.display = 'block';

    // Set layout type radio
    const radio = document.getElementById('lt-' + layout);
    if (radio) radio.checked = true;

    // Show correct panel
    document.querySelectorAll('.fcms-page-type-panel').forEach(p => p.classList.remove('active'));
    const panel = document.getElementById('fcms-panel-' + layout);
    if (panel) panel.classList.add('active');
    else {
        const defPanel = document.getElementById('fcms-panel-blank');
        if (defPanel) defPanel.classList.add('active');
    }

    // Populate fields
    if (layout === 'contact') {
        document.getElementById('fcms-contact-address').value = sections.address || '';
        document.getElementById('fcms-contact-hours').value   = sections.hours   || '';
        document.getElementById('fcms-contact-phone').value   = sections.phone   || '';
        document.getElementById('fcms-contact-email').value   = sections.email   || '';
        document.getElementById('fcms-contact-map').value     = sections.map_embed || '';
    } else if (layout === 'faq') {
        const container = document.getElementById('fcms-faq-items');
        container.innerHTML = '';
        const items = Array.isArray(sections.faq) ? sections.faq : [];
        items.forEach(item => fcmsAddFaqItem(item.question || '', item.answer || ''));
    } else if (layout === 'team') {
        document.getElementById('fcms-team-heading').value    = sections.heading    || '';
        document.getElementById('fcms-team-subheading').value = sections.subheading || '';
        document.getElementById('fcms-team-count').value      = sections.count      || 8;
    }

    // Auto-list pages: hide save button and layout picker
    const isAutoList = (layout === 'autolist');
    document.getElementById('fcms-layout-type-row').style.display = isAutoList ? 'none' : '';
    document.getElementById('fcms-page-save-row').style.display   = isAutoList ? 'none' : '';
}

// Layout type pill change
document.querySelectorAll('[name="fcms_layout_type"]').forEach(input => {
    input.addEventListener('change', function(){
        document.querySelectorAll('.fcms-page-type-panel').forEach(p => p.classList.remove('active'));
        const panel = document.getElementById('fcms-panel-' + this.value);
        if (panel) panel.classList.add('active');
    });
});

// ── FAQ items ──
let fcmsFaqCount = 0;
function fcmsAddFaqItem(question, answer) {
    const item = document.createElement('div');
    item.className = 'fcms-faq-item';
    item.innerHTML = '<button type="button" class="fcms-remove-btn" onclick="this.closest(\'.fcms-faq-item\').remove()"><i class="fas fa-times"></i></button>'
        + '<div class="mb-2"><label class="form-label">Question</label><input type="text" class="form-control" id="fcms-faq-q-' + fcmsFaqCount + '" value="' + (question || '').replace(/"/g,'&quot;') + '" placeholder="Enter question..."></div>'
        + '<div><label class="form-label">Answer</label><textarea class="form-control" id="fcms-faq-a-' + fcmsFaqCount + '" rows="2" placeholder="Enter answer...">' + (answer || '') + '</textarea></div>';
    document.getElementById('fcms-faq-items').appendChild(item);
    fcmsFaqCount++;
}

// ── AJAX save page section ──
function fcmsSavePageSection() {
    const pageId = document.getElementById('fcms-edit-page-id').value;
    if (!pageId) return;
    const layoutType = document.querySelector('[name="fcms_layout_type"]:checked');
    const lt = layoutType ? layoutType.value : 'blank';

    let sectionData = {};
    if (lt === 'contact') {
        sectionData = {
            address:   document.getElementById('fcms-contact-address').value,
            hours:     document.getElementById('fcms-contact-hours').value,
            phone:     document.getElementById('fcms-contact-phone').value,
            email:     document.getElementById('fcms-contact-email').value,
            map_embed: document.getElementById('fcms-contact-map').value,
        };
    } else if (lt === 'faq') {
        const items = [];
        document.querySelectorAll('#fcms-faq-items .fcms-faq-item').forEach(function(item) {
            const q = item.querySelector('input[type=text]');
            const a = item.querySelector('textarea');
            if (q && a) items.push({ question: q.value, answer: a.value });
        });
        sectionData = { faq: items };
    } else if (lt === 'team') {
        sectionData = {
            heading:    document.getElementById('fcms-team-heading').value,
            subheading: document.getElementById('fcms-team-subheading').value,
            count:      document.getElementById('fcms-team-count').value,
        };
    }

    const formData = new FormData();
    formData.append('page_id', pageId);
    formData.append('layout_type', lt);
    // Flatten sectionData for PHP array POST
    Object.keys(sectionData).forEach(function(k) {
        if (Array.isArray(sectionData[k])) {
            sectionData[k].forEach(function(item, i) {
                Object.keys(item).forEach(function(ik) {
                    formData.append('page_section_data[' + k + '][' + i + '][' + ik + ']', item[ik]);
                });
            });
        } else {
            formData.append('page_section_data[' + k + ']', sectionData[k]);
        }
    });

    fetch('<?php echo site_url('admin/frontcms/save_page_section'); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(resp) {
        if (resp.status === 'success') {
            // Update the list item badge
            const activeBtn = document.querySelector('#fcms-page-list button.active');
            if (activeBtn) {
                activeBtn.setAttribute('data-page-layout', lt);
                const badge = activeBtn.querySelector('.badge');
                if (badge) badge.textContent = lt.charAt(0).toUpperCase() + lt.slice(1);
            }
            const saveBtn = document.querySelector('#fcms-page-save-row button');
            if (saveBtn) {
                const orig = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-check me-1"></i> Saved!';
                saveBtn.classList.replace('btn-primary', 'btn-success');
                setTimeout(function(){ saveBtn.innerHTML = orig; saveBtn.classList.replace('btn-success', 'btn-primary'); }, 2000);
            }
        }
    })
    .catch(function(e){ console.error('Save page section failed', e); });
}
</script>

<script>
// ── Restore the tab the user was on after a save redirect ──
(function(){
    var KEY  = 'frontcmsActiveTab';
    var form = document.getElementById('custom');
    var tabs = document.getElementById('cmsTab');
    if (!form || !tabs) return;

    // Remember which tab was active when the form is submitted
    form.addEventListener('submit', function(){
        var active = tabs.querySelector('.nav-link.active');
        if (active && active.getAttribute('data-bs-target')) {
            try { sessionStorage.setItem(KEY, active.getAttribute('data-bs-target')); } catch(e) {}
        }
    });

    // Restore must wait until Bootstrap (loaded by layout/footer.php after this view) is available
    function restore(){
        var saved = null;
        try { saved = sessionStorage.getItem(KEY); } catch(e) {}
        if (!saved) return;
        try { sessionStorage.removeItem(KEY); } catch(e) {}
        var btn = tabs.querySelector('.nav-link[data-bs-target="' + saved + '"]');
        if (!btn) return;
        if (window.bootstrap && bootstrap.Tab) {
            try { bootstrap.Tab.getOrCreateInstance(btn).show(); return; } catch(e) {}
        }
        btn.click();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restore);
    } else {
        // Fallback if DOMContentLoaded already fired
        setTimeout(restore, 0);
    }
})();
</script>

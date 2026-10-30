<!DOCTYPE html>
<html lang="<?php echo isset($lang_dir) ? $lang_dir : 'en'; ?>" <?php echo (!empty($rtl_mode)) ? 'dir="rtl"' : ''; ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $this->customlib->getAppName(); ?> · Smart Hospital</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Fonts: Inter + Roboto + Nunito (Theme Studio font-family choices) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto:wght@400;500;700;900&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- 1. Bootstrap 5.3.3 (LTR/RTL swap based on language is_rtl) -->
  <?php if (!empty($rtl_mode)): ?>
    <link href="<?php echo base_url('backend/bootstrap5/css/bootstrap.rtl.min.css'); ?>" rel="stylesheet">
  <?php else: ?>
    <link href="<?php echo base_url('backend/bootstrap5/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <?php endif; ?>

  <!-- 2. Design tokens (CSS vars for all 3 variants) -->
  <link href="<?php echo base_url('backend/css/sh-tokens.css'); ?>" rel="stylesheet">

  <!-- 2b. Theme Studio per-user overlay — must load AFTER sh-tokens.css. -->
  <?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>

  <!-- 3. DataTables CSS -->
  <link href="<?php echo base_url('backend/dist/datatables/css/jquery.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/buttons.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/plugins/datatables/dataTables.bootstrap5.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/responsive.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/rowReorder.dataTables.min.css'); ?>" rel="stylesheet">

  <!-- 4. Font Awesome 5 + FA 4 fallback -->
  <link href="<?php echo base_url('backend/dist/css/all.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/css/font-awesome.min.css'); ?>" rel="stylesheet">

  <!-- 5. Bootstrap Select CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('backend/dist/css/bootstrap-select.min.css'); ?>">

  <!-- 6. Date/time picker CSS — Tempus Dominus 6 (BS5 native, single library) -->
  <link href="<?php echo base_url('backend/plugins/tempus-dominus/css/tempus-dominus.min.css'); ?>" rel="stylesheet">

  <!-- 7. Toastr CSS -->
  <link href="<?php echo base_url('backend/toast-alert/toastr.css'); ?>" rel="stylesheet">

  <!-- 8. Dropify + NProgress -->
  <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/dropify.min.css'); ?>">
  <link href="<?php echo base_url('backend/dist/css/nprogress.css'); ?>" rel="stylesheet">

  <!-- 9. FullCalendar -->
  <link rel="stylesheet" href="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.print.min.css'); ?>" media="print">

  <!-- 10. Plugin CSS: iCheck, ionicons, flags (datepicker/daterangepicker removed — replaced by Tempus Dominus 6 above) -->
  <link rel="stylesheet" href="<?php echo base_url('backend/plugins/iCheck/flat/blue.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/ionicons.min.css'); ?>">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">

  <!-- 11. SH Theme — MUST load AFTER all plugin CSS so overrides win -->
  <link href="<?php echo base_url('backend/css/sh-theme.css'); ?>?v=<?php echo filemtime(FCPATH.'backend/css/sh-theme.css'); ?>" rel="stylesheet">

  <!-- 12. SH Patient RTL overrides — loaded LAST when RTL is active so all RTL rules win -->
  <?php if (!empty($rtl_mode)): ?>
    <link href="<?php echo base_url('backend/css/sh-patient-rtl.css'); ?>?v=<?php echo filemtime(FCPATH.'backend/css/sh-patient-rtl.css'); ?>" rel="stylesheet">
  <?php endif; ?>

  <!-- Favicon -->
  <?php
  $logoresult = $this->customlib->getLogoImage();
  if (!empty($logoresult['mini_logo'])) {
      $logo_image = 'uploads/hospital_content/logo/' . $logoresult['image'];
      $mini_logo  = base_url('uploads/hospital_content/logo/' . $logoresult['mini_logo']);
  } else {
      $logo_image = 'uploads/hospital_content/logo/s_logo.png';
      $mini_logo  = base_url('backend/images/s-favican.png');
  }
  ?>
  <link href="<?php echo $mini_logo; ?>" rel="shortcut icon" type="image/x-icon">

  <!-- jQuery — must load before Bootstrap JS and all plugins -->
  <script src="<?php echo base_url('backend/custom/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('backend/dist/js/jquery-ui.min.js'); ?>"></script>
  <!-- datejs library removed — was loaded but never used in project code -->
  <!-- hospital-custom-bs5.js moved to footer (must load AFTER Bootstrap bundle, see footer.php) -->
  <script src="<?php echo base_url('backend/dist/js/moment.min.js'); ?>"></script>
  <script src="<?php echo base_url('backend/dist/js/bootstrap-select.min.js'); ?>"></script>

  <script type="text/javascript">
    var baseurl       = '<?php echo base_url(); ?>';
    var base_url      = baseurl;
    var SH_BASE       = baseurl;
    var SH_CSRF_NAME  = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var SH_CSRF_TOKEN = '<?php echo $this->security->get_csrf_hash(); ?>';
    var chk_validate  = '';
  </script>
  <!-- Header inline <style> block migrated to backend/css/sh-theme.css (2026-05-27) -->
</head>
<body class="variant-<?php echo isset($sh_theme_tokens) ? theme_preset_to_variant($sh_theme_tokens['theme_preset']) : $this->customlib->getCurrentVariant(); ?>" data-preset="<?php echo isset($sh_theme_tokens) ? htmlspecialchars($sh_theme_tokens['theme_preset']) : 'clinical'; ?>" data-chrome="patient">
<script>
/* Sidebar collapsed-state restore — runs BEFORE sidebar paints to prevent flash */
(function(){try{if(localStorage.getItem('sh_sidebar_collapsed')==='1'){document.body.classList.add('sidebar-collapsed');}}catch(e){}})();
</script>

<?php $this->load->view('layout/patient/sidebar'); ?>

<?php
/* Topbar data */
$_patient_image  = isset($this->patient_data['image']) ? $this->patient_data['image'] : '';
$_pt_has_image   = !empty($_patient_image) && strpos($_patient_image, 'no_image') === false;
$_patient_file   = $_pt_has_image ? $_patient_image : 'uploads/patient_images/no_image.png';
$_patient_name   = $this->customlib->getPatientSessionUserName();
if (!$_pt_has_image) {
    $_pt_parts    = preg_split('/\s+/', trim($_patient_name), -1, PREG_SPLIT_NO_EMPTY);
    $_pt_initials = count($_pt_parts) === 0 ? '?' : (count($_pt_parts) === 1
        ? mb_strtoupper(mb_substr($_pt_parts[0], 0, 1))
        : mb_strtoupper(mb_substr($_pt_parts[0], 0, 1) . mb_substr($_pt_parts[count($_pt_parts) - 1], 0, 1)));
}

/* Notification badge count */
$systemnotifications = $this->notification_model->getPatientUnreadNotification();
$_notif_count = (int)sizeof($systemnotifications);
?>
<header class="topbar">
  <!-- Sidebar toggle -->
  <button type="button" class="tb-toggle" id="sidebarToggle" title="<?= $this->lang->line('toggle_sidebar') ?>" aria-label="Toggle sidebar">
    <i class="fa fa-bars"></i>
  </button>

  <!-- Current module name -->
  <div class="tb-module">
    <div class="ic"><i class="fa fa-home sh-tb-home-icon"></i></div>
	
    <?php
      // Topbar module label — derive from the active sidebar item (`top_menu`
      // session value, the same source the sidebar highlights on). Translate via
      // the language file so it stays i18n-driven; fall back to a prettified key
      // only when no matching lang line exists.
      $_tb_module = $this->session->userdata('top_menu');
      if (!empty($_tb_module)) {
          $_tb_label = $this->lang->line($_tb_module, FALSE);
          echo $_tb_label ? $_tb_label : ucfirst(str_replace('_', ' ', $_tb_module));
      } else {
          echo $this->lang->line('dashboard') ?: 'Home';
      }
    ?>
  </div>

  <div class="sh-tb-flex-spacer"></div>

  <!-- Right action area -->
  <div class="tb-right">

    <!-- Icon rail — recessed plate grouping status/locale icons -->
    <div class="tb-rail">

    <!-- Mobile-only "More" menu: surfaces Chat / Calendar hidden from the bar on small screens -->
    <?php
    $_pt_more_chat = ($this->module_lib->hasActive('chat') && $this->module_lib->hasPatientActive('chat'));
    $_pt_more_cal  = ($this->module_lib->hasActive('calendar_to_do_list') && $this->module_lib->hasPatientActive('calendar_to_do_list'));
    if ($_pt_more_chat || $_pt_more_cal): ?>
    <div class="dropdown tb-more-wrap">
      <button class="tb-btn tb-more-btn" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('more') ?: 'More'; ?>" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dr">
        <?php if ($_pt_more_chat): ?>
        <li><a class="dropdown-item" href="<?php echo site_url('patient/chat'); ?>"><i class="fa fa-comments me-2"></i><?php echo $this->lang->line('chat'); ?></a></li>
        <?php endif; ?>
        <?php if ($_pt_more_cal): ?>
        <li><a class="dropdown-item" href="<?php echo base_url('user/calendar/'); ?>"><i class="fa fa-calendar me-2"></i><?php echo $this->lang->line('calendar'); ?></a></li>
        <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>

    <!-- Language switcher -->
    <?php
    $_pt_lang_cc = 'us';
    $_pt_sess    = $this->session->userdata('patient');
    $_pt_lid     = 0;
    if (!empty($_pt_sess['patient_id'])) {
        $_pt_dl = $this->setting_model->get_patientlang($_pt_sess['patient_id']);
        if (!empty($_pt_dl) && !empty($_pt_dl['lang_id'])) $_pt_lid = (int)$_pt_dl['lang_id'];
    }
    if (!$_pt_lid) { $_pt_gl = $this->setting_model->get(); $_pt_lid = !empty($_pt_gl[0]['lang_id']) ? (int)$_pt_gl[0]['lang_id'] : 0; }
    if ($_pt_lid) {
        $_pt_lr = $this->db->select('country_code')->from('languages')->where('id', $_pt_lid)->get()->row_array();
        if (!empty($_pt_lr['country_code'])) $_pt_lang_cc = strtolower($_pt_lr['country_code']);
    }
    ?>
    <div class="dropdown">
      <button class="tb-btn tb-btn-flag" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('language'); ?>">
        <img src="https://flagcdn.com/w20/<?php echo htmlspecialchars($_pt_lang_cc); ?>.png"
             srcset="https://flagcdn.com/w40/<?php echo htmlspecialchars($_pt_lang_cc); ?>.png 2x"
             width="20" height="15" alt="<?php echo htmlspecialchars(strtoupper($_pt_lang_cc)); ?>"
             onerror="this.outerHTML='<i class=\'fa fa-globe\' style=\'font-size:15px;\'></i>'">
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-lang">
        <?php $this->load->view('patient/languageSwitcher'); ?>
      </ul>
    </div>

    <?php if ($this->module_lib->hasActive('chat') && $this->module_lib->hasPatientActive('chat')): ?>
    <a class="tb-btn" href="<?php echo site_url('patient/chat'); ?>" title="<?php echo $this->lang->line('chat'); ?>">
      <i class="fa fa-comments"></i>
      <?php if (function_exists('chat_couter') && chat_couter() > 0): ?><span class="dot"></span><?php endif; ?>
    </a>
    <?php endif; ?>

    <a class="tb-btn" href="<?php echo base_url('patient/systemnotifications'); ?>" title="<?php echo $this->lang->line('notifications'); ?>">
      <i class="fa fa-bell"></i>
      <span class="dot" id="sh-notif-dot"<?php echo ($_notif_count > 0) ? '' : ' style="display:none"'; ?>></span>
    </a>

    <?php if ($this->module_lib->hasActive('calendar_to_do_list') && $this->module_lib->hasPatientActive('calendar_to_do_list')): ?>
    <a class="tb-btn" href="<?php echo base_url('user/calendar/'); ?>" title="<?php echo $this->lang->line('calendar'); ?>">
      <i class="fa fa-calendar"></i>
    </a>
    <?php endif; ?>

	<!-- Pending custom forms -->
	<?php if ($this->module_lib->hasModule('survey_form')): ?>
	<?php if ($this->module_lib->hasActive('survey_form') && $this->module_lib->hasPatientActive('survey_form')): ?>
    <?php 
    $_cf_pending = $this->customlib->get_pending_patient_forms();
    $_cf_count   = count($_cf_pending);
    if ($_cf_count > 0):
    ?>
    <div class="dropdown">
      <button class="tb-btn" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('pending_forms'); ?>">
        <i class="fa fa-file-text-o"></i>
        <span class="tb-badge"><?php echo $_cf_count; ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-cf">
        <li class="dropdown-header d-flex justify-content-between align-items-center">
          <span><?php echo $this->lang->line('pending_forms'); ?> (<?php echo $_cf_count; ?>)</span>
          <a href="<?php echo site_url('patient/survey'); ?>"><?php echo $this->lang->line('view_all'); ?></a>
        </li>
        <?php foreach (array_slice($_cf_pending, 0, 5) as $_cf_form): ?>
        <li>
          <a class="dropdown-item" href="<?php echo site_url('patient/survey/fill/' . $_cf_form['id']); ?>">
            <i class="fa fa-file-text-o me-2"></i><?php echo html_escape($_cf_form['title']); ?>
            <?php if (!empty($_cf_form['end_date']) && $_cf_form['end_date'] !== '0000-00-00'): ?>
            <small class="text-muted d-block"><?php echo $this->lang->line('deadline'); ?>: <?php echo $this->customlib->YYYYMMDDTodateFormat($_cf_form['end_date']); ?></small>
            <?php endif; ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>	
	<?php endif; ?>
	<?php endif; ?>
			
    </div><!-- /.tb-rail -->

    <!-- Patient signet: avatar + name + role -->
    <div class="dropdown">
      <a class="tb-signet" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <?php if ($_pt_has_image): ?>
        <img src="<?php echo $this->media_storage->getImageURL($_patient_file); ?>"
             alt="<?php echo html_escape($_patient_name); ?>">
        <?php else: ?>
        <div class="tb-signet-initials"><?php echo html_escape($_pt_initials); ?></div>
        <?php endif; ?>
        <div class="tb-signet-text">
          <span class="tb-signet-name"><?php echo html_escape($_patient_name); ?></span>
          <span class="tb-signet-role"><?php echo $this->lang->line('patient'); ?></span>
        </div>
        <i class="fa fa-angle-down"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-user">
        <li class="px-3 py-2 border-bottom">
          <div class="fw-bold text-capitalize"><?php echo html_escape($_patient_name); ?></div>
          <div class="text-muted small"><?php echo $this->lang->line('patient'); ?></div>
        </li>
        <?php if ($this->module_lib->hasModule('google_authenticator') && $this->module_lib->hasActive('google_authenticator') && $this->customlib->checkPatientPanelGoogleAuthenticator()): ?>
        <li><a class="dropdown-item" href="<?php echo site_url('patient/gauthenticate/setup'); ?>"><i class="fa fa-cog me-2"></i><?php echo $this->lang->line('settings'); ?></a></li>
        <?php endif; ?>
        <li><a class="dropdown-item" href="<?php echo base_url('user/user/changepass'); ?>"><i class="fa fa-key me-2"></i><?php echo $this->lang->line('change_password'); ?></a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="<?php echo base_url('patient/appearance'); ?>"><i class="fa fa-paint-brush me-2"></i><?php echo html_escape($this->lang->line('theme') ?: 'Theme'); ?></a></li>
        <li><hr class="dropdown-divider"></li>
        <?php if ($this->customlib->patientDeleteAccount() == 'enabled'): ?>
        <li><a href="javascript:void(0)" class="dropdown-item text-danger" id="deleteAccountBtn"><i class="fa fa-trash me-2"></i>Delete Account</a></li>
        <li><hr class="dropdown-divider"></li>
        <?php endif; ?>
        <li><a class="dropdown-item text-danger" href="<?php echo base_url('site/logout'); ?>"><i class="fa fa-sign-out me-2"></i><?php echo $this->lang->line('logout'); ?></a></li>
      </ul>
    </div>

  </div>
</header>

<?php if ($this->customlib->patientDeleteAccount() == 'enabled'): ?>
<script>
$('#deleteAccountBtn').on('click', function() {
    if (confirm('<?php echo $this->lang->line('delete_account_confirm_msg'); ?>')) {
        $.ajax({
            url: '<?php echo site_url("patient/dashboard/deleteAccount"); ?>',
            type: 'POST',
            data: { <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    window.location.href = '<?php echo site_url("site/userlogin"); ?>';
                } else {
                    alert(response.message);
                }
            }
        });
    }
});
</script>
<?php endif; ?>

<script type="text/javascript">
function set_patient_language(lang_id) {
    $.ajax({
        type: 'POST',
        url: baseurl + 'patient/dashboard/user_language/' + lang_id,
        data: {},
        success: function() {
            successMsg('<?php echo $this->lang->line("status_change_successfully"); ?>');
            window.location.reload(true);
        }
    });
}
function defoult(id) {
    $.ajax({
        type: 'POST',
        url: baseurl + 'patient/defoult_language/' + id,
        data: {},
        success: function() {
            successMsg('<?php echo $this->lang->line("status_change_successfully"); ?>');
            window.location.reload(true);
        }
    });
}
</script>

<?php
/* Notification polling config */
$_notif_setting = $this->setting_model->getHospitalDetail();
$_poll_seconds  = (!empty($_notif_setting->notification_poll_interval) && (int)$_notif_setting->notification_poll_interval > 0)
    ? (int)$_notif_setting->notification_poll_interval : 60;
$_poll_ms = $_poll_seconds * 1000;
?>
<script type="text/javascript">
(function () {
    var POLL_MS   = <?php echo $_poll_ms; ?>;
    var POLL_URL  = baseurl + 'patient/systemnotifications/pollNotifications';
    var NOTIF_URL = baseurl + 'patient/systemnotifications';
    var LS_KEY    = 'patient_notif_last_check';

    var pageLoadTime  = new Date().toISOString();
    var stored        = localStorage.getItem(LS_KEY);
    var lastCheckTime = (stored && stored > pageLoadTime) ? stored : pageLoadTime;
    localStorage.setItem(LS_KEY, lastCheckTime);

    /* Single shared AudioContext, unlocked on first user gesture — mobile browsers
       start it suspended and a poll timer can't unlock it. See admin footer.php. */
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
        if (dot) dot.style.display = (count > 0) ? '' : 'none';
    }

    function isAlreadyShown(id) { return !!localStorage.getItem('p_notif_s_' + id); }
    function markAsShown(id) {
        localStorage.setItem('p_notif_s_' + id, '1');
        var keys = [];
        for (var k in localStorage) { if (k.indexOf('p_notif_s_') === 0) { keys.push(k); } }
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
        /* Message rendered as HTML (matches prior toastr behaviour); title stays escaped. */
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

    /* Expose the renderer so the footer can override the global successMsg/errorMsg/...
       wrappers with sh-bubble versions AFTER hospital-custom-bs5.js (toastr) loads. */
    window.shBubble = { make: makeBubble, present: presentBubble };

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

    var timer;
    setTimeout(function() {
        timer = setInterval(poll, POLL_MS);
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) { clearInterval(timer); }
            else { setTimeout(function() { poll(); }, 1500); timer = setInterval(poll, POLL_MS); }
        });
    }, 3000);
})();
</script>

<main class="content">
<!-- ═══════════════════ PAGE CONTENT START ═══════════════════ -->

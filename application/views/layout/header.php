<!DOCTYPE html>
<html lang="<?php echo isset($lang_dir) ? $lang_dir : 'en'; ?>" <?php echo (isset($rtl_mode) && $rtl_mode) ? 'dir="rtl"' : ''; ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? htmlspecialchars($title) : $this->customlib->getAppName(); ?> · Mehhr Care</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Fonts: Inter + Roboto + Nunito (Theme Studio font-family choices) + JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto:wght@400;500;700;900&family=Nunito:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- 1. Bootstrap 5.3.3 — framework base (LTR/RTL swap based on language is_rtl) -->
  <?php if (!empty($rtl_mode)): ?>
    <link href="<?php echo base_url('backend/bootstrap5/css/bootstrap.rtl.min.css'); ?>" rel="stylesheet">
  <?php else: ?>
    <link href="<?php echo base_url('backend/bootstrap5/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <?php endif; ?>

  <!-- 2. Design tokens (CSS custom properties for all 3 variants) — after BS5 -->
  <link href="<?php echo base_url('backend/css/sh-tokens.css'); ?>" rel="stylesheet">

  <!-- 3. Theme Studio per-user overlay — must load AFTER sh-tokens.css so user-customized
       --accent / --link / --ink / --font / --text-scale / --density-y win by source order. -->
  <?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>

  <!-- 4. DataTables CSS (core + buttons + responsive + BS5 skin) -->
  <link href="<?php echo base_url('backend/dist/datatables/css/jquery.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/buttons.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/plugins/datatables/dataTables.bootstrap5.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/responsive.dataTables.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/datatables/css/rowReorder.dataTables.min.css'); ?>" rel="stylesheet">

  <!-- 5. Font Awesome 5 (fas/fab/far icons) + FA 4 fallback (fa icons) -->
  <link href="<?php echo base_url('backend/dist/css/all.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('backend/dist/css/font-awesome.min.css'); ?>" rel="stylesheet">

  <!-- 6. Select2 CSS -->
  <link href="<?php echo base_url('backend/plugins/select2/select2.min.css'); ?>" rel="stylesheet">

  <!-- 7. Date/time picker CSS — Tempus Dominus 6 (BS5 native, single library) -->
  <link href="<?php echo base_url('backend/plugins/tempus-dominus/css/tempus-dominus.min.css'); ?>" rel="stylesheet">

  <!-- 8. Toastr notifications CSS -->
  <link href="<?php echo base_url('backend/toast-alert/toastr.css'); ?>" rel="stylesheet">

  <!-- 9. Dropify (file upload), NProgress (progress bar) -->
  <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/dropify.min.css'); ?>">
  <link href="<?php echo base_url('backend/dist/css/nprogress.css'); ?>" rel="stylesheet">

  <!-- 10. FullCalendar -->
  <link rel="stylesheet" href="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('backend/fullcalendar/dist/fullcalendar.print.min.css'); ?>" media="print">

  <!-- 11. Bootstrap Select -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('backend/dist/css/bootstrap-select.min.css'); ?>">

  <!-- 12. Plugin CSS: iCheck, colorpicker, ionicons (datepicker/daterangepicker removed — replaced by Tempus Dominus 6 above) -->
  <link rel="stylesheet" href="<?php echo base_url('backend/plugins/iCheck/flat/blue.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('backend/plugins/colorpicker/bootstrap-colorpicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('backend/dist/css/ionicons.min.css'); ?>">

  <!-- 3. SH Theme (layout shell + BS5 overrides + custom components) — MUST load AFTER all plugin CSS so our overrides win -->
  <link href="<?php echo base_url('backend/css/sh-theme.css'); ?>?v=<?php echo filemtime(FCPATH.'backend/css/sh-theme.css'); ?>" rel="stylesheet">

  <!-- 3b. SH Dashboard styles — loaded ONLY on admin/admin/dashboard page (Variant B layout) -->
  <?php if (isset($module) && $module === 'dashboard'): ?>
    <link href="<?php echo base_url('backend/css/sh-dashboard.css'); ?>?v=<?php echo filemtime(FCPATH.'backend/css/sh-dashboard.css'); ?>" rel="stylesheet">
  <?php endif; ?>

  <!-- 14. SH Admin RTL overrides — loaded LAST when RTL is active so all RTL rules win -->
  <?php if (!empty($rtl_mode)): ?>
    <link href="<?php echo base_url('backend/css/sh-admin-rtl.css'); ?>?v=<?php echo filemtime(FCPATH.'backend/css/sh-admin-rtl.css'); ?>" rel="stylesheet">
  <?php endif; ?>

  <!-- 13. Favicon -->
  <?php
    $logoresult = $this->customlib->getLogoImage();
    $mini_logo = (!empty($logoresult['mini_logo']))
        ? base_url('uploads/hospital_content/logo/' . $logoresult['mini_logo'])
        : base_url('backend/images/s-favican.png');
  ?>
  <link href="<?php echo $mini_logo; ?>" rel="shortcut icon" type="image/x-icon">

  <!-- jQuery — must load before Bootstrap JS and all plugins -->
  <script src="<?php echo base_url('backend/custom/jquery.min.js'); ?>"></script>

  <!-- jQuery UI (sortable, draggable, datepicker) -->
  <script src="<?php echo base_url('backend/dist/js/jquery-ui.min.js'); ?>"></script>

  <!-- Color picker -->
  <script src="<?php echo base_url('backend/plugins/colorpicker/bootstrap-colorpicker.js'); ?>"></script>

  <!-- Date utilities (datejs library removed during TD 6 migration — was loaded but never used in project code) -->

  <!-- SH Base URL (available to all inline scripts and sh-chrome.js) -->
  <script>
    var baseurl    = '<?php echo base_url(); ?>';
    var SH_BASE    = baseurl;
    var SH_CSRF_NAME  = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var SH_CSRF_TOKEN = '<?php echo $this->security->get_csrf_hash(); ?>';
    <?php if (isset($user_data) && is_array($user_data)): ?>
    var SH_USER_INITIALS = '<?php
      $name = isset($user_data['name']) ? $user_data['name'] : '';
      $parts = explode(' ', trim($name));
      $initials = '';
      if (count($parts) >= 2) { $initials = strtoupper(substr($parts[0],0,1).substr($parts[1],0,1)); }
      elseif (count($parts) === 1 && strlen($parts[0]) > 0) { $initials = strtoupper(substr($parts[0],0,2)); }
      echo htmlspecialchars($initials);
    ?>';
    <?php endif; ?>
    var chk_validate = '<?php echo $this->config->item('SHLK'); ?>';
  </script>

  <?php echo isset($extra_head) ? $extra_head : ''; ?>
  <script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>
  <!-- Header inline <style> block migrated to backend/css/sh-theme.css (2026-05-27) -->
</head>
<body class="variant-<?php echo isset($sh_theme_tokens) ? theme_preset_to_variant($sh_theme_tokens['theme_preset']) : $this->customlib->getCurrentVariant(); ?>" data-preset="<?php echo isset($sh_theme_tokens) ? htmlspecialchars($sh_theme_tokens['theme_preset']) : 'clinical'; ?>" data-chrome="<?php echo isset($module) ? htmlspecialchars($module) : 'dashboard'; ?>">
<script>
/* Sidebar collapsed-state restore — runs BEFORE sidebar paints to prevent flash/flicker */
(function(){try{if(localStorage.getItem('sh_sidebar_collapsed')==='1'){document.body.classList.add('sidebar-collapsed');}}catch(e){}})();
</script>

<!-- Admin sidebar (RBAC-aware server-side navigation) -->
<?php $this->load->view('layout/sidebar'); ?>

<!-- ═══════════════════ TOP BAR (server-rendered, functional) ═══════════════════ -->
<?php
  $userdata_tb = $this->customlib->getUserData();
  $tb_image = !empty($userdata_tb['image'])
      ? 'uploads/staff_images/' . $userdata_tb['image']
      : 'uploads/staff_images/no_image.png';
  $tb_name = isset($userdata_tb['name']) ? $userdata_tb['name'] : '';
  $tb_role = isset($userdata_tb['user_type']) ? $userdata_tb['user_type'] : '';
  $tb_id   = isset($userdata_tb['id']) ? $userdata_tb['id'] : '';

  /* Resolve active language country_code for flag display */
  $tb_lang_cc = 'us';
  $_ls = $this->session->userdata('hospitaladmin');
  $_lid = 0;
  if (!empty($_ls['id'])) {
      $sl = $this->setting_model->get_stafflang($_ls['id']);
      if (!empty($sl['lang_id']) && $sl['lang_id'] != 0) $_lid = (int)$sl['lang_id'];
  }
  if (!$_lid) { $gl = $this->setting_model->get(); $_lid = !empty($gl[0]['lang_id']) ? (int)$gl[0]['lang_id'] : 0; }
  if ($_lid) {
      $lr = $this->db->select('country_code')->from('languages')->where('id', $_lid)->get()->row_array();
      if (!empty($lr['country_code'])) $tb_lang_cc = strtolower($lr['country_code']);
  }
?>
<header class="topbar">
  <!-- Sidebar toggle (hamburger) -->
  <button type="button" class="tb-toggle" id="sidebarToggle" title="<?php echo $this->lang->line('toggle_sidebar') ?: 'Toggle sidebar'; ?>" aria-label="Toggle sidebar">
    <i class="fa fa-bars"></i>
  </button>

  <!-- Module name -->
  <div class="tb-module">
    <div class="ic"><i class="fa fa-home"></i></div>
    <?php
      // Topbar module label — pull the translated string from the language file
      // (same source the sidebar uses). Fall back to a prettified $module key
      // only when no matching lang line exists, so labels stay i18n-driven.
      if (isset($module)) {
          $module_label = $this->lang->line($module, FALSE);
          echo $module_label ? $module_label : ucfirst(str_replace('_', ' ', $module));
      } else {
          echo 'Home';
      }
    ?>
  </div>

  <!-- Spacer -->
  <div class="sh-tb-flex-spacer"></div>

  <!-- Patient search -->
  <?php if ($this->rbac->hasPrivilege('patient', 'can_view')) { ?>
  <form class="tb-patient-search sh-tb-search-form" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
    <?php echo $this->customlib->getCSRF(); ?>
    <label class="tb-search mb-0">
      <input type="text" name="search_text" placeholder="<?php echo $this->lang->line('search_by_name') ?: 'Search By Patient Name'; ?>" autocomplete="off">
      <button type="submit" class="tb-search-submit">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </label>
  </form>
  <?php } ?>

  <!-- ═══════════════════ QUICK ADD (+) ═══════════════════ -->
  <?php
  /* Render the + button only if user has at least ONE of the 12 add permissions.
     Each module name matches the destination page's RBAC gate exactly. */
  $qa_can_any = (
       $this->rbac->hasPrivilege('patient', 'can_add')              // Add Patient
    || $this->rbac->hasPrivilege('opd_patient', 'can_add')          // New OPD Patient
    || $this->rbac->hasPrivilege('ipd_patient', 'can_add')          // New IPD Patient
    || $this->rbac->hasPrivilege('appointment', 'can_add')          // New Appointment
    || $this->rbac->hasPrivilege('generate_bill', 'can_view')       // New Bill
    || $this->rbac->hasPrivilege('pharmacy_bill', 'can_add')        // Pharmacy Sale
    || $this->rbac->hasPrivilege('pathology_bill', 'can_add')       // Pathology Test
    || $this->rbac->hasPrivilege('radiology_bill', 'can_add')       // Radiology Test
    || $this->rbac->hasPrivilege('blood_issue', 'can_add')          // Blood Issue
    || $this->rbac->hasPrivilege('ambulance_call', 'can_add')       // Ambulance Call
    || $this->rbac->hasPrivilege('visitor_book', 'can_add')         // Visitor
    || $this->rbac->hasPrivilege('income', 'can_add')               // Income
    || $this->rbac->hasPrivilege('expense', 'can_add')              // Expense
  );
  ?>
  <?php if ($qa_can_any): ?>
  <div class="sh-qa-wrap" id="shQuickAddWrap">
    <button type="button" class="tb-btn tb-btn-plus" id="shQuickAddBtn"
            title="<?php echo $this->lang->line('quick_add'); ?> (Ctrl+K)"
            aria-label="<?php echo $this->lang->line('quick_add'); ?>"
            aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-plus"></i>
    </button>
    <div class="sh-qa-backdrop" id="shQuickAddBackdrop" aria-hidden="true"></div>
    <div class="sh-qa-panel" id="shQuickAddPanel" role="dialog"
         aria-label="<?php echo $this->lang->line('quick_add'); ?>">
      <div class="sh-qa-head">
        <h5 class="ttl"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('quick_add'); ?></h5>
        <span class="kbd-chip">Ctrl+K</span>
      </div>
      <div class="sh-qa-search-wrap">
        <i class="fa fa-search si"></i>
        <input type="text" class="sh-qa-search-in" id="shQuickAddSearch"
               placeholder="<?php echo $this->lang->line('search_quick_add'); ?>"
               autocomplete="off">
      </div>
      <div class="sh-qa-list" id="shQuickAddList">

        <?php if ($this->rbac->hasPrivilege('patient', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/admin/search'); ?>?action=add"
           data-search="add patient new register registration general">
          <span class="ic qa-blue"><i class="fas fa-user-plus"></i></span>
          <span class="lbl"><?php echo $this->lang->line('add_patient'); ?></span>
          <span class="mod"><?php echo $this->lang->line('patient_list'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/patient/search'); ?>?action=add"
           data-search="opd patient new outpatient visit consultation">
          <span class="ic qa-teal"><i class="fas fa-stethoscope"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('opd_patient'); ?></span>
          <span class="mod"><?php echo $this->lang->line('opd_patient'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('ipd_patient', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/patient/ipdsearch'); ?>?action=add"
           data-search="ipd patient new inpatient admission">
          <span class="ic qa-blue"><i class="fas fa-procedures"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('ipd_patient'); ?></span>
          <span class="mod"><?php echo $this->lang->line('ipd_patient'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('appointment', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/appointment'); ?>?action=add"
           data-search="appointment new schedule doctor booking">
          <span class="ic qa-teal"><i class="fa fa-calendar-check-o"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('appointment'); ?></span>
          <span class="mod"><?php echo $this->lang->line('appointment'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('generate_bill', 'can_view')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/bill/dashboard'); ?>"
           data-search="bill invoice new charge billing">
          <span class="ic qa-green"><i class="fa fa-file-text-o"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('bill'); ?></span>
          <span class="mod"><?php echo $this->lang->line('bill'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/pharmacy/bill'); ?>?action=add"
           data-search="pharmacy sale bill medicine drug invoice new">
          <span class="ic qa-amber"><i class="fas fa-mortar-pestle"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('pharmacy_bill'); ?></span>
          <span class="mod"><?php echo $this->lang->line('pharmacy_bill'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/pathology/gettestreportbatch'); ?>?action=add"
           data-search="pathology test lab new diagnostic">
          <span class="ic qa-violet"><i class="fas fa-flask"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('pathology_test'); ?></span>
          <span class="mod"><?php echo $this->lang->line('pathology_test'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('radiology_bill', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/radio/gettestreportbatch'); ?>?action=add"
           data-search="radiology test imaging xray scan new diagnostic">
          <span class="ic qa-violet"><i class="fas fa-microscope"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('radiology_test'); ?></span>
          <span class="mod"><?php echo $this->lang->line('radiology_test'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('blood_issue', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/bloodbank/issue'); ?>?action=add"
           data-search="blood issue bank new transfusion">
          <span class="ic qa-red"><i class="fas fa-tint"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('blood_issue'); ?></span>
          <span class="mod"><?php echo $this->lang->line('blood_issue'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/vehicle/getcallambulance'); ?>?action=add"
           data-search="ambulance call new dispatch transport emergency">
          <span class="ic qa-red"><i class="fas fa-ambulance"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('ambulance_call'); ?></span>
          <span class="mod"><?php echo $this->lang->line('ambulance_call'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('visitor_book', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/visitors'); ?>?action=add"
           data-search="visitor log new front office entry">
          <span class="ic qa-teal"><i class="fa fa-id-badge"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('visitor'); ?></span>
          <span class="mod"><?php echo $this->lang->line('visitor'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('income', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/income'); ?>?action=add"
           data-search="income new payment received finance">
          <span class="ic qa-green"><i class="fa fa-arrow-down"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('income'); ?></span>
          <span class="mod"><?php echo $this->lang->line('income'); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($this->rbac->hasPrivilege('expense', 'can_add')): ?>
        <a class="sh-qa-item" href="<?php echo site_url('admin/expense'); ?>?action=add"
           data-search="expense new payment paid finance">
          <span class="ic qa-red"><i class="fa fa-arrow-up"></i></span>
          <span class="lbl"><?php echo $this->lang->line('new'); ?> <?php echo $this->lang->line('expense'); ?></span>
          <span class="mod"><?php echo $this->lang->line('expense'); ?></span>
        </a>
        <?php endif; ?>

      </div>
      <div class="sh-qa-empty" id="shQuickAddEmpty">
        <i class="fa fa-search"></i>
        <?php echo $this->lang->line('no_quick_actions_match'); ?>
      </div>
      <div class="sh-qa-foot">
        <div class="keys">
          <span><span class="kbd">&uarr;</span><span class="kbd">&darr;</span> <?php echo $this->lang->line('quick_add_hint_navigate'); ?></span>
          <span><span class="kbd">Enter</span> <?php echo $this->lang->line('quick_add_hint_open'); ?></span>
          <span><span class="kbd">Esc</span> <?php echo $this->lang->line('quick_add_hint_close'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Right action buttons -->
  <div class="tb-right">
    <!-- Icon rail — recessed plate grouping status/locale icons -->
    <div class="tb-rail">
    <!-- Mobile-only "More" menu: surfaces icons hidden from the bar on small screens (Bed Status / Chat / Calendar) -->
    <div class="dropdown tb-more-wrap">
      <button class="tb-btn tb-more-btn" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('more') ?: 'More'; ?>" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dr">
        <?php if ($this->rbac->hasPrivilege('bed_status', 'can_view')) { ?>
        <li><a class="dropdown-item" href="#" onclick="getbedstatus(); return false;"><i class="fas fa-bed me-2"></i><?php echo $this->lang->line('bed_status'); ?></a></li>
        <?php } ?>
        <?php if ($this->module_lib->hasActive('chat') && $this->rbac->hasPrivilege('chat', 'can_view')) { ?>
        <li><a class="dropdown-item" href="<?php echo site_url('admin/chat'); ?>"><i class="fa fa-comments me-2"></i><?php echo $this->lang->line('chat'); ?></a></li>
        <?php } ?>
        <?php if ($this->module_lib->hasActive('calendar_to_do_list') && $this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) { ?>
        <li><a class="dropdown-item" href="<?php echo base_url('admin/calendar/events'); ?>"><i class="fa fa-calendar me-2"></i><?php echo $this->lang->line('calendar'); ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php if ($this->rbac->hasPrivilege('language_switcher', 'can_view')) { ?>
    <div class="dropdown">
      <button class="tb-btn tb-btn-flag" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('language'); ?>">
        <img src="https://flagcdn.com/w20/<?php echo htmlspecialchars($tb_lang_cc); ?>.png"
             srcset="https://flagcdn.com/w40/<?php echo htmlspecialchars($tb_lang_cc); ?>.png 2x"
             width="20" height="15" alt="<?php echo htmlspecialchars(strtoupper($tb_lang_cc)); ?>"
             class="sh-tb-flag-img"
             onerror="this.outerHTML='<i class=\'fa fa-globe\'></i>'">
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-lang">
        <?php $this->load->view('admin/language/languageSwitcher'); ?>
      </ul>
    </div>
    <?php } ?>

    <?php
    $tb_userdata_role = $this->customlib->getUserData();
    if(isset($tb_userdata_role['role_id']) && $tb_userdata_role['role_id'] == 7){
        if (($this->module_lib->hasModule('multi_branch') && $this->module_lib->hasActive('multi_branch')) || $this->db->multi_branch) { ?>
    <button class="tb-btn" data-bs-toggle="modal" data-bs-target="#multiBranchSwitchModal" title="<?php echo $this->lang->line('switch_branch'); ?>">
      <i class="fa fa-exchange"></i>
    </button>
    <?php }} ?>

    <?php if ($this->rbac->hasPrivilege('bed_status', 'can_view')) { ?>
    <button class="tb-btn" id="beddata" onclick="getbedstatus()" title="<?php echo $this->lang->line('bed_status'); ?>">
      <i class="fas fa-bed"></i>
    </button>
    <?php } ?>

    <?php if ($this->module_lib->hasActive('chat') && $this->rbac->hasPrivilege('chat', 'can_view')) { ?>
    <a class="tb-btn" href="<?php echo site_url('admin/chat'); ?>" title="<?php echo $this->lang->line('chat'); ?>">
      <i class="fa fa-comments"></i>
      <?php if(function_exists('chat_couter') && chat_couter() > 0) { ?><span class="dot"></span><?php } ?>
    </a>
    <?php } ?>

    <?php if ($this->module_lib->hasActive('calendar_to_do_list') && $this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) { ?>
    <a class="tb-btn" href="<?php echo base_url('admin/calendar/events'); ?>" title="<?php echo $this->lang->line('calendar'); ?>">
      <i class="fa fa-calendar"></i>
    </a>
    <?php } ?>

	<?php
    // Custom Form — pending staff forms alert (ported from BS3 header, rebuilt as a BS5 rail dropdown).
    // Data comes from customlib->get_pending_staff_forms() (each row: id, title, end_date).
    if ($this->module_lib->hasModule('survey_form')) {
		if ($this->rbac->hasPrivilege('survey_form', 'can_view')) {
        $_cf_pending = $this->customlib->get_pending_staff_forms();
        $_cf_count   = count($_cf_pending);
        if ($_cf_count > 0) { ?>
    <div class="dropdown">
      <button class="tb-btn position-relative" data-bs-toggle="dropdown" title="<?php echo $this->lang->line('pending_forms'); ?>">
        <i class="fa fa-file-text-o"></i>
        <span class="tb-badge"><?php echo $_cf_count > 99 ? '99+' : $_cf_count; ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-cf">
        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
          <span class="fw-bold"><?php echo $this->lang->line('pending_forms'); ?> (<?php echo $_cf_count; ?>)</span>
          <a href="<?php echo site_url('admin/survey/staff_forms'); ?>" class="small"><?php echo $this->lang->line('view_all'); ?></a>
        </li>
        <?php foreach (array_slice($_cf_pending, 0, 5) as $_cf_form) { ?>
        <li>
          <a class="dropdown-item text-wrap" href="<?php echo site_url('admin/survey/staff_fill/' . $_cf_form['id']); ?>">
            <i class="fa fa-file-text-o me-2"></i><?php echo html_escape($_cf_form['title']); ?>
            <?php if (!empty($_cf_form['end_date'])) { ?>
            <small class="text-muted d-block"><?php echo $this->lang->line('deadline'); ?>: <?php echo $this->customlib->YYYYMMDDTodateFormat($_cf_form['end_date']); ?></small>
            <?php } ?>
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php } } } ?>	
	
    <?php if ($this->rbac->hasPrivilege('notification_center', 'can_view')) {
        $systemnotifications = $this->notification_model->getCountUnreadNotification();
        $_notif_count = (int) $systemnotifications->count;
    ?>
    <a class="tb-btn position-relative" href="<?php echo base_url('admin/systemnotification'); ?>" title="<?php echo $this->lang->line('notifications'); ?>">
      <i class="fa fa-bell"></i>
      <?php // Badge always rendered (hidden when 0) so the footer poller can show/update it live. ?>
      <span class="tb-badge" id="sh-notif-dot"<?php echo ($_notif_count > 0) ? '' : ' style="display:none"'; ?>><?php echo $_notif_count > 99 ? '99+' : $_notif_count; ?></span>
    </a>
    <?php } ?>
    </div><!-- /.tb-rail -->

    <!-- User signet: Profile + Password + Logout -->
    <div class="dropdown">
      <a class="tb-signet" href="#" data-bs-toggle="dropdown">
        <img src="<?php echo $this->media_storage->getImageURL($tb_image); ?>" alt="">
        <div class="tb-signet-text">
          <span class="tb-signet-name"><?php echo html_escape($tb_name); ?></span>
          <?php if (!empty($tb_role)): ?><span class="tb-signet-role"><?php echo html_escape($tb_role); ?></span><?php endif; ?>
        </div>
        <i class="fa fa-angle-down"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end sh-tb-dropdown-user">
        <li class="px-3 py-2 border-bottom">
          <div class="fw-bold text-capitalize"><?php echo html_escape($tb_name); ?></div>
          <div class="text-muted small"><?php echo html_escape($tb_role); ?></div>
        </li>
        <li><a class="dropdown-item" href="<?php echo base_url('admin/staff/profile/' . $tb_id); ?>"><i class="fa fa-user me-2"></i><?php echo $this->lang->line('profile'); ?></a></li>
        <li><a class="dropdown-item" href="<?php echo base_url('admin/admin/changepass'); ?>"><i class="fa fa-key me-2"></i><?php echo $this->lang->line('change_password'); ?></a></li>
        <li><hr class="dropdown-divider"></li>
        <?php
          // Theme Studio link — superadmin (theme_studio.can_view) goes to the
          // hospital-wide brand setter; everyone else gets their personal Appearance page.
          // RBAC is the single source of truth (no hardcoded role_id check).
          if ($this->rbac->hasPrivilege('theme_studio', 'can_view')) {
            $_theme_href  = base_url('admin/themestudio');
            $_theme_label = $this->lang->line('backend_theme') ?: 'Backend Theme';
          } else {
            $_theme_href  = base_url('admin/appearance');
            $_theme_label = $this->lang->line('appearance') ?: 'Appearance';
          }
        ?>
        <li><a class="dropdown-item" href="<?php echo $_theme_href; ?>"><i class="fa fa-paint-brush me-2"></i><?php echo html_escape($_theme_label); ?></a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?php echo base_url('site/logout'); ?>"><i class="fa fa-sign-out me-2"></i><?php echo $this->lang->line('logout'); ?></a></li>
      </ul>
    </div>
  </div>
</header>

<!-- ═══════════════════ QUICK ADD (+) BEHAVIOR ═══════════════════ -->
<script>
(function () {
  var wrap = document.getElementById('shQuickAddWrap');
  if (!wrap) return;
  var btn      = document.getElementById('shQuickAddBtn');
  var panel    = document.getElementById('shQuickAddPanel');
  var backdrop = document.getElementById('shQuickAddBackdrop');
  var search   = document.getElementById('shQuickAddSearch');
  var list     = document.getElementById('shQuickAddList');
  var empty    = document.getElementById('shQuickAddEmpty');
  var items    = Array.prototype.slice.call(list.querySelectorAll('.sh-qa-item'));
  var focusIdx = 0;

  function visibleItems() {
    return items.filter(function (el) { return el.style.display !== 'none'; });
  }

  function refocus() {
    items.forEach(function (el) { el.classList.remove('is-focus'); });
    var vis = visibleItems();
    if (vis.length === 0) {
      empty.style.display = 'block';
      list.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    list.style.display  = '';
    if (focusIdx >= vis.length) focusIdx = 0;
    if (focusIdx < 0)           focusIdx = vis.length - 1;
    vis[focusIdx].classList.add('is-focus');
    if (vis[focusIdx].scrollIntoView) {
      vis[focusIdx].scrollIntoView({ block: 'nearest' });
    }
  }

  function openPanel() {
    wrap.classList.add('is-open');
    btn.setAttribute('aria-expanded', 'true');
    search.value = '';
    items.forEach(function (el) { el.style.display = ''; });
    focusIdx = 0;
    refocus();
    setTimeout(function () { try { search.focus(); } catch (e) {} }, 50);
  }

  function closePanel() {
    wrap.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
  }

  function togglePanel() {
    if (wrap.classList.contains('is-open')) { closePanel(); } else { openPanel(); }
  }

  btn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    togglePanel();
  });
  backdrop.addEventListener('click', closePanel);

  /* Live search filter — matches data-search attribute + visible text */
  search.addEventListener('input', function () {
    var q = (search.value || '').trim().toLowerCase();
    items.forEach(function (el) {
      var hay = ((el.getAttribute('data-search') || '') + ' ' + el.textContent).toLowerCase();
      el.style.display = (!q || hay.indexOf(q) !== -1) ? '' : 'none';
    });
    focusIdx = 0;
    refocus();
  });

  /* Keyboard inside search input */
  search.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { e.preventDefault(); closePanel(); return; }
    if (e.key === 'ArrowDown') { e.preventDefault(); focusIdx++; refocus(); return; }
    if (e.key === 'ArrowUp')   { e.preventDefault(); focusIdx--; refocus(); return; }
    if (e.key === 'Enter') {
      e.preventDefault();
      var vis = visibleItems();
      if (vis.length > 0 && vis[focusIdx]) { window.location.href = vis[focusIdx].href; }
      return;
    }
  });

  /* Global shortcuts: Ctrl+K (or Cmd+K) opens panel; Esc closes when open */
  document.addEventListener('keydown', function (e) {
    var k = (e.key || '').toLowerCase();
    if ((e.ctrlKey || e.metaKey) && k === 'k') {
      e.preventDefault();
      togglePanel();
    } else if (k === 'escape' && wrap.classList.contains('is-open')) {
      closePanel();
    }
  });
})();
</script>

<main class="content">
<?php if ($this->config->item('SHLK') == '') { ?>
<div class="sh-license-banner">
  <span class="sh-license-banner-icon"><i class="fa fa-exclamation"></i></span>
  <span class="sh-license-banner-text">Alert! You are using unregistered version of Smart Hospital. Please <a href="#" class="purchasemodal">click here</a> to register your purchase code for Smart Hospital.</span>
</div>
<?php } ?>
<!-- ═══════════════════ PAGE CONTENT START ═══════════════════ -->

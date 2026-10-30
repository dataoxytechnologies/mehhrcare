<?php
$titleresult = $this->customlib->getTitleName();
$logoresult  = $this->customlib->getLogoImage();
$title_name  = !empty($titleresult["name"]) ? $titleresult["name"] : "Hospital Name";
$mini_logo   = !empty($logoresult["mini_logo"]) ? "uploads/hospital_content/logo/" . $logoresult["mini_logo"] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title_name; ?></title>
<link rel="shortcut icon" href="<?php echo base_url(); ?>backend/images/s-favican.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-login.css">
</head>
<body class="variant-<?php echo isset($sh_variant) ? htmlspecialchars($sh_variant) : 'a'; ?>">

<div class="lp-wrap">

  <!-- LEFT: Brand / pitch -->
  <div class="lp-brand">

    <div class="lp-logo-row">
      <div class="lp-logo-mark">
        <?php if (!empty($mini_logo)): ?>
          <img src="<?php echo $this->media_storage->getImageURL($mini_logo); ?>" alt="<?php echo $title_name; ?>">
        <?php else: ?>
          <?php echo strtoupper(substr($title_name, 0, 1)); ?>
        <?php endif; ?>
      </div>
      <div class="lp-logo-name"><?php echo html_escape($title_name); ?></div>
    </div>

    <div class="lp-pitch">
      <div class="lp-badge"><span class="dot"></span><?php echo $this->lang->line('admin_portal') ?: 'Admin Portal'; ?></div>
    </div>

    <div class="lp-tiny">&copy; <?php echo date('Y'); ?> <?php echo html_escape($title_name); ?> &middot; <?php echo $this->lang->line('all_rights_reserved') ?: 'All rights reserved'; ?></div>
  </div>

  <!-- RIGHT: Reset password form -->
  <div class="lp-form-panel">
    <div class="lp-card">
      <h2><?php echo $this->lang->line('reset_password'); ?></h2>

      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form action="<?php echo site_url('admin/resetpassword/' . $verification_code); ?>" method="post" class="lp-form">
        <?php echo $this->customlib->getCSRF(); ?>

        <div>
          <label class="lp-label" for="ar-password"><?php echo $this->lang->line('password'); ?></label>
          <input type="password" id="ar-password" name="password" class="lp-input" placeholder="<?php echo $this->lang->line('password'); ?>" autocomplete="new-password">
          <span class="text-danger"><?php echo form_error('password'); ?></span>
        </div>

        <div>
          <label class="lp-label" for="ar-confirm-password"><?php echo $this->lang->line('confirm_password'); ?></label>
          <input type="password" id="ar-confirm-password" name="confirm_password" class="lp-input" placeholder="<?php echo $this->lang->line('confirm_password'); ?>" autocomplete="new-password">
          <span class="text-danger"><?php echo form_error('confirm_password'); ?></span>
        </div>

        <button type="submit" class="lp-btn-submit"><?php echo $this->lang->line('submit'); ?></button>
      </form>

      <div class="lp-card-alt">
        <a href="<?php echo site_url('site/login'); ?>" class="lp-back-link">
          <svg viewBox="0 0 16 16" width="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10 4L6 8l4 4"/></svg>
          <?php echo $this->lang->line('admin_login'); ?>
        </a>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

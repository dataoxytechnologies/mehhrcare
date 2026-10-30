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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto:wght@400;500;700;900&family=Nunito:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-login.css">
<?php if (isset($sh_theme_tokens)) { echo theme_render_style_block($sh_theme_tokens); } ?>
</head>
<body class="variant-<?php echo isset($sh_theme_tokens) ? theme_preset_to_variant($sh_theme_tokens['theme_preset']) : (isset($sh_variant) ? htmlspecialchars($sh_variant) : 'a'); ?>" data-preset="<?php echo isset($sh_theme_tokens) ? htmlspecialchars($sh_theme_tokens['theme_preset']) : 'clinical'; ?>">

<!-- patient-layout: form panel gets order:-1 via CSS, placing it LEFT -->
<div class="lp-wrap patient-layout">

  <!-- RIGHT: Brand / pitch (green patient variant) -->
  <div class="lp-brand patient">

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
      <div class="lp-badge"><span class="dot"></span><?php echo $this->lang->line('patient_portal') ?: 'Patient Portal'; ?></div>

      <?php if (!empty($notice)): ?>
        <div class="lp-news">
          <div class="lp-news-label"><?php echo $this->lang->line('what_is_new_in'); ?> <?php echo html_escape($name); ?></div>
          <?php foreach ($notice as $notice_value): ?>
            <div class="lp-news-item">
              <?php if (!empty($notice_value['created_at']) && strtotime($notice_value['created_at']) > 0): ?>
                <div class="lp-news-meta"><?php echo date('d M Y', strtotime($notice_value['created_at'])); ?></div>
              <?php endif; ?>
              <h4><?php echo html_escape($notice_value['title']); ?></h4>
              <?php
              $string = strip_tags($notice_value['description']);
              if (strlen($string) > 120) {
                $stringCut = substr($string, 0, 120);
                $endPoint  = strrpos($stringCut, ' ');
                $string    = ($endPoint ? substr($stringCut, 0, $endPoint) : $stringCut);
                $string    = html_escape($string) . '...';
              } else {
                $string = html_escape($string);
              }
              $read_more_url = html_escape(site_url('read/' . rawurlencode($notice_value['slug'])));
              ?>
              <p><?php echo $string; ?></p>
              <a href="<?php echo $read_more_url; ?>" class="lp-news-read-more" target="_blank" rel="noopener">
                <?php echo $this->lang->line('read_more'); ?>
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 4l4 4-4 4"/></svg>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="lp-tiny">© <?php echo date('Y'); ?> <?php echo html_escape($title_name); ?> · <?php echo $this->lang->line('patient_portal') ?: 'Patient Portal'; ?> · <?php echo $this->lang->line('all_rights_reserved') ?: 'All rights reserved'; ?></div>
  </div>

  <!-- LEFT (via order:-1): Forgot password form -->
  <div class="lp-form-panel">
    <div class="lp-card">
      <h2><?php echo $this->lang->line('reset_your_password') ?: 'Reset your password'; ?></h2>

      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form action="<?php echo site_url('site/ufpassword'); ?>" method="post" class="lp-form">
        <?php echo $this->customlib->getCSRF(); ?>

        <div>
          <label class="lp-label" for="uf-email"><?php echo $this->lang->line('email'); ?></label>
          <input type="text" id="uf-email" name="username" class="lp-input" placeholder="patient@example.com" autocomplete="email">
          <span class="text-danger"><?php echo form_error('username'); ?></span>
        </div>

        <input type="hidden" name="user" value="patient">

        <button type="submit" class="lp-btn-submit"><?php echo $this->lang->line('submit'); ?></button>
      </form>

      <div class="lp-card-alt">
        <a href="<?php echo site_url('site/userlogin'); ?>" class="lp-back-link">
          <svg viewBox="0 0 16 16" width="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10 4L6 8l4 4"/></svg>
          <?php echo $this->lang->line('back_to_patient_login') ?: 'Back to Patient sign in'; ?>
        </a>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

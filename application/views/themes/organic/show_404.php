<?php
$p404    = $static_pages['show_404'] ?? array();
$heading = html_escape($p404['heading'] ?? $this->lang->line('page_not_found'));
$message = html_escape($p404['message'] ?? 'The page you\'re looking for doesn\'t exist or has been moved.');
?>
<section class="page-hero compact">
  <span class="ph-blob" aria-hidden="true"></span>
  <div class="container front-404">
    <p class="front-404-num">404</p>
    <h1 class="front-404-heading"><?php echo $heading; ?></h1>
    <p class="front-404-message"><?php echo $message; ?></p>
    <a href="<?php echo site_url('frontend'); ?>" class="pill pill-primary">
      <?php echo $this->lang->line('go_to_home'); ?>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>

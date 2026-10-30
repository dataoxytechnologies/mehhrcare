<?php
$cta_sec  = isset($home_sections['cta']) ? $home_sections['cta'] : array();
$appt_url = site_url('form/appointment');
?>
<?php if(($cta_sec['show'] ?? true) && $front_setting->is_active_online_appointment): ?>
<section class="book">
  <div class="container book-grid">
    <div class="book-l">
      <?php if(!empty($cta_sec['title'])): ?><h2><?php echo html_escape($cta_sec['title']); ?></h2><?php endif; ?>
      <?php if(!empty($cta_sec['subtitle'])): ?><p><?php echo html_escape($cta_sec['subtitle']); ?></p><?php endif; ?>
      <ul class="bullet-list">
        <?php if(!empty($cta_sec['bullet_1'])): ?><li><span>✓</span> <?php echo html_escape($cta_sec['bullet_1']); ?></li><?php endif; ?>
        <?php if(!empty($cta_sec['bullet_2'])): ?><li><span>✓</span> <?php echo html_escape($cta_sec['bullet_2']); ?></li><?php endif; ?>
        <?php if(!empty($cta_sec['bullet_3'])): ?><li><span>✓</span> <?php echo html_escape($cta_sec['bullet_3']); ?></li><?php endif; ?>
      </ul>
      <a href="<?php echo $appt_url; ?>" class="pill pill-primary cta-pill-spaced">
        <?php echo html_escape($cta_sec['button_text'] ?? $this->lang->line('book_appointment')); ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

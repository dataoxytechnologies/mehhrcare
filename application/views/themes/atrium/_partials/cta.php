<?php
$cta_sec  = isset($home_sections['cta']) ? $home_sections['cta'] : array();
$appt_url = site_url('form/appointment');
?>
<?php if(($cta_sec['show'] ?? true)): ?>
<section class="cta-band">
  <div class="container">
    <div class="cta-card">
      <div class="cta-text">
        <?php if(!empty($cta_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($cta_sec['kicker']); ?></span><?php endif; ?>
        <h2><?php echo html_escape($cta_sec['title'] ?? $this->lang->line('book_appointment')); ?></h2>
        <?php if(!empty($cta_sec['subtitle'])): ?><p><?php echo html_escape($cta_sec['subtitle']); ?></p><?php endif; ?>
        <?php
          $bullets = array_filter(array(
            $cta_sec['bullet_1'] ?? '',
            $cta_sec['bullet_2'] ?? '',
            $cta_sec['bullet_3'] ?? '',
          ));
        ?>
        <?php if(!empty($bullets)): ?>
        <ul class="cta-bullets">
          <?php foreach($bullets as $b): ?><li><?php echo html_escape($b); ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a href="<?php echo $appt_url; ?>" class="btn-fill lg">
        <?php echo html_escape($cta_sec['button_text'] ?? $this->lang->line('book_appointment')); ?>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

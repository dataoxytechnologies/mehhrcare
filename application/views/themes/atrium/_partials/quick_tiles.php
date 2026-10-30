<?php
$quick    = isset($home_sections['quick_tiles']) ? $home_sections['quick_tiles'] : array();
$hero     = isset($home_sections['hero']) ? $home_sections['hero'] : array();
$appt_url = site_url('form/appointment');
?>
<?php if(($quick['show'] ?? true)): ?>
<section class="quicks">
  <div class="container">
    <div class="quick-grid">
      <?php
        $arrow_svg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
      ?>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a class="qcard" href="<?php echo $appt_url; ?>">
        <span class="qicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg></span>
        <span class="qtitle"><?php echo html_escape($quick['tile_1_title'] ?? $this->lang->line('book_appointment')); ?></span>
        <span class="qsub"><?php echo html_escape($quick['tile_1_sub'] ?? ''); ?></span>
        <?php if(!empty($quick['tile_1_more'])): ?><span class="qmore"><?php echo html_escape($quick['tile_1_more']); ?>  <?php echo $arrow_svg; ?></span><?php endif; ?>
      </a>
      <?php endif; ?>
      <a class="qcard" href="<?php echo site_url('patient'); ?>">
        <span class="qicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 14h6M9 18h4"/></svg></span>
        <span class="qtitle"><?php echo html_escape($quick['tile_2_title'] ?? $this->lang->line('patient_login')); ?></span>
        <span class="qsub"><?php echo html_escape($quick['tile_2_sub'] ?? ''); ?></span>
        <?php if(!empty($quick['tile_2_more'])): ?><span class="qmore"><?php echo html_escape($quick['tile_2_more']); ?>  <?php echo $arrow_svg; ?></span><?php endif; ?>
      </a>
      <a class="qcard" href="<?php echo site_url('frontend'); ?>">
        <span class="qicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="11" r="3"/><path d="M19 11c0 7-7 12-7 12s-7-5-7-12a7 7 0 0 1 14 0z"/></svg></span>
        <span class="qtitle"><?php echo html_escape($quick['tile_3_title'] ?? $this->lang->line('health_check_ups')); ?></span>
        <span class="qsub"><?php echo html_escape($quick['tile_3_sub'] ?? ''); ?></span>
        <?php if(!empty($quick['tile_3_more'])): ?><span class="qmore"><?php echo html_escape($quick['tile_3_more']); ?>  <?php echo $arrow_svg; ?></span><?php endif; ?>
      </a>
      <?php
        $tile4_href = !empty($hero['emergency_number'])
          ? 'tel:' . preg_replace('/[^0-9+]/', '', $hero['emergency_number'])
          : site_url('frontend');
      ?>
      <a class="qcard featured" href="<?php echo html_escape($tile4_href); ?>">
        <span class="qicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg></span>
        <span class="qtitle"><?php echo html_escape($quick['tile_4_title'] ?? $this->lang->line('emergency')); ?></span>
        <span class="qsub"><?php echo html_escape($quick['tile_4_sub'] ?? ''); ?></span>
        <?php if(!empty($quick['tile_4_more'])): ?><span class="qmore"><?php echo html_escape($quick['tile_4_more']); ?>  <?php echo $arrow_svg; ?></span><?php endif; ?>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

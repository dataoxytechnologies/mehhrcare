<?php $test_sec = isset($home_sections['testimonials']) ? $home_sections['testimonials'] : array(); ?>
<?php if(($test_sec['show'] ?? true) && !empty($test_sec['items'])): ?>
<section class="testi">
  <div class="container">
    <header class="sec-head center">
      <?php if(!empty($test_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($test_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($test_sec['title'] ?? $this->lang->line('testimonials')); ?></h2>
    </header>
    <div class="testi-grid">
      <?php foreach($test_sec['items'] as $t): if(empty($t['name']) && empty($t['quote'])) continue; ?>
      <div class="tcard">
        <div class="tcard-stars">★★★★★</div>
        <p class="tcard-text"><?php echo html_escape(mb_substr(trim($t['quote'] ?? ''), 0, 220)); ?></p>
        <div class="tcard-who"><strong><?php echo html_escape($t['name'] ?? ''); ?></strong></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

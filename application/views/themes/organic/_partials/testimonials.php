<?php $test_sec = isset($home_sections['testimonials']) ? $home_sections['testimonials'] : array(); ?>
<?php if(($test_sec['show'] ?? true) && !empty($test_sec['items'])): ?>
<section class="testi">
  <div class="container">
    <div class="head-row">
      <?php if(!empty($test_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($test_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($test_sec['title'] ?? $this->lang->line('testimonials')); ?></h2>
    </div>
    <div class="testi-grid">
      <?php foreach($test_sec['items'] as $t): if(empty($t['name']) && empty($t['quote'])) continue; ?>
      <article class="t-card">
        <p>"<?php echo html_escape(mb_substr(trim($t['quote'] ?? ''), 0, 220)); ?>"</p>
        <footer>
          <span class="t-av"><?php echo strtoupper(mb_substr($t['name'] ?? '', 0, 1)); ?></span>
          <div><strong><?php echo html_escape($t['name'] ?? ''); ?></strong></div>
        </footer>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

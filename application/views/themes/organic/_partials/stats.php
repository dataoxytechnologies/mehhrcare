<?php $stats_sec = isset($home_sections['stats']) ? $home_sections['stats'] : array(); ?>
<?php if(($stats_sec['show'] ?? true) && !empty($stats_sec['items'])): ?>
<section class="stats">
  <div class="container stats-grid">
    <div>
      <?php if(!empty($stats_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($stats_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($stats_sec['title'] ?? ''); ?></h2>
    </div>
    <ul>
      <?php foreach($stats_sec['items'] as $stat): ?>
      <li><strong><?php echo html_escape($stat['value'] ?? ''); ?></strong><span><?php echo html_escape($stat['label'] ?? ''); ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

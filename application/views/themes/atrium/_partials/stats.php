<?php $stats_sec = isset($home_sections['stats']) ? $home_sections['stats'] : array(); ?>
<?php if(($stats_sec['show'] ?? true) && !empty($stats_sec['items'])): ?>
<section class="stats">
  <div class="container">
    <header class="stats-head">
      <?php if(!empty($stats_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($stats_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($stats_sec['title'] ?? ''); ?></h2>
    </header>
    <ol class="stats-grid">
      <?php foreach($stats_sec['items'] as $stat): ?>
      <li class="stat">
        <span class="stat-num"><?php echo html_escape($stat['value'] ?? ''); ?></span>
        <span class="stat-lbl"><?php echo html_escape($stat['label'] ?? ''); ?></span>
        <?php if(!empty($stat['trend'])): ?><span class="stat-trend"><?php echo html_escape($stat['trend']); ?></span><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php $marq = isset($home_sections['marquee']) ? $home_sections['marquee'] : array(); ?>
<?php if(($marq['show'] ?? true) && !empty($marq['items'])): ?>
<div class="marquee">
  <div class="marquee-track">
    <?php foreach($marq['items'] as $item): ?>
    <span><?php echo html_escape($item); ?></span><span>·</span>
    <?php endforeach; ?>
    <?php foreach($marq['items'] as $item): ?>
    <span><?php echo html_escape($item); ?></span><span>·</span>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

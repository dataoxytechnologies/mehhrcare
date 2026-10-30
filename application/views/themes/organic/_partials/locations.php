<?php $loc_sec = isset($home_sections['locations']) ? $home_sections['locations'] : array(); ?>
<?php if(($loc_sec['show'] ?? true) && !empty($loc_sec['items'])): ?>
<section class="locs">
  <div class="container">
    <div class="head-row">
      <?php if(!empty($loc_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($loc_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($loc_sec['title'] ?? $this->lang->line('our_locations')); ?></h2>
    </div>
    <div class="loc-grid">
      <?php foreach($loc_sec['items'] as $loc): if(empty($loc['name'])) continue; ?>
      <a class="loc-card" <?php echo !empty($loc['map_url']) ? 'href="' . html_escape($loc['map_url']) . '" target="_blank" rel="noopener"' : 'href="#"'; ?>>
        <div class="loc-card-head">
          <?php if(!empty($loc['tag'])): ?><span class="loc-card-tag"><?php echo html_escape($loc['tag']); ?></span><?php endif; ?>
          <?php if(!empty($loc['hours'])): ?><span class="loc-card-hours"><?php echo html_escape($loc['hours']); ?></span><?php endif; ?>
        </div>
        <h3 class="loc-card-name"><?php echo html_escape($loc['name']); ?></h3>
        <?php if(!empty($loc['address'])): ?><p class="loc-card-addr"><?php echo html_escape($loc['address']); ?></p><?php endif; ?>
        <footer class="loc-card-foot">
          <?php if(!empty($loc['phone'])): ?>
          <span class="loc-card-phone">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
            <?php echo html_escape($loc['phone']); ?>
          </span>
          <?php endif; ?>
          <?php if(!empty($loc['map_url'])): ?>
          <span class="loc-card-arrow">→</span>
          <?php endif; ?>
        </footer>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

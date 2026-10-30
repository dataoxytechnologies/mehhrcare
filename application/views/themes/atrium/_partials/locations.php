<?php $loc_sec = isset($home_sections['locations']) ? $home_sections['locations'] : array(); ?>
<?php if(($loc_sec['show'] ?? true) && !empty($loc_sec['items'])): ?>
<section class="locs">
  <div class="container">
    <header class="sec-head split">
      <div>
        <?php if(!empty($loc_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($loc_sec['kicker']); ?></span><?php endif; ?>
        <h2><?php echo html_escape($loc_sec['title'] ?? $this->lang->line('our_locations')); ?></h2>
      </div>
    </header>
    <div class="loc-grid">
      <?php foreach($loc_sec['items'] as $loc): if(empty($loc['name'])) continue; ?>
      <a class="loc" <?php echo !empty($loc['map_url']) ? 'href="' . html_escape($loc['map_url']) . '" target="_blank" rel="noopener"' : 'href="#"'; ?>>
        <div class="loc-top">
          <?php if(!empty($loc['tag'])): ?><span class="loc-tag-pill"><?php echo html_escape($loc['tag']); ?></span><?php endif; ?>
          <?php if(!empty($loc['hours'])): ?><span class="loc-hours"><?php echo html_escape($loc['hours']); ?></span><?php endif; ?>
        </div>
        <h3 class="loc-name"><?php echo html_escape($loc['name']); ?></h3>
        <?php if(!empty($loc['address'])): ?>
        <p class="loc-addr">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><?php echo html_escape($loc['address']); ?></span>
        </p>
        <?php endif; ?>
        <footer class="loc-foot">
          <?php if(!empty($loc['phone'])): ?>
          <span class="loc-phone">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
            <?php echo html_escape($loc['phone']); ?>
          </span>
          <?php endif; ?>
          <?php if(!empty($loc['map_url'])): ?>
          <span class="loc-cta">
            <?php $dir_lbl = $this->lang->line('directions'); echo html_escape($dir_lbl ?: 'Directions'); ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
          <?php endif; ?>
        </footer>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

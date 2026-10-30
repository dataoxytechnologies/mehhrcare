<?php $dept_sec = isset($home_sections['departments']) ? $home_sections['departments'] : array(); ?>
<?php if(($dept_sec['show'] ?? true) && !empty($home_specialists)):
  $dept_limit = (int)($dept_sec['count'] ?? 12);
  $specs_to_render = $dept_limit > 0 ? array_slice($home_specialists, 0, $dept_limit) : $home_specialists;
?>
<section class="specs" id="departments">
  <div class="container">
    <header class="sec-head center">
      <?php if(!empty($dept_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($dept_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($dept_sec['title'] ?? $this->lang->line('our_departments')); ?></h2>
      <?php if(!empty($dept_sec['subtitle'])): ?><p class="sec-sub"><?php echo html_escape($dept_sec['subtitle']); ?></p><?php endif; ?>
    </header>
    <div class="specs-grid">
      <?php foreach($specs_to_render as $sp):
        $doc_count = (int)($sp['doctor_count'] ?? 0);
      ?>
      <div class="spec">
        <span class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
        <span class="spec-name"><?php echo html_escape(ucfirst($sp['specialist_name'])); ?></span>
        <?php if($doc_count > 0): ?>
        <span class="spec-stat"><strong><?php echo $doc_count; ?></strong> <?php echo $doc_count === 1 ? $this->lang->line('specialist') : $this->lang->line('specialists'); ?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

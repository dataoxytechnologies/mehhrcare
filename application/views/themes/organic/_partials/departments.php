<?php $dept_sec = isset($home_sections['departments']) ? $home_sections['departments'] : array(); ?>
<?php if(($dept_sec['show'] ?? true) && !empty($home_specialists)):
  $dept_limit = (int)($dept_sec['count'] ?? 12);
  $specs_to_render = $dept_limit > 0 ? array_slice($home_specialists, 0, $dept_limit) : $home_specialists;
?>
<section class="depts" id="departments">
  <div class="container">
    <div class="head-row">
      <?php if(!empty($dept_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($dept_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($dept_sec['title'] ?? $this->lang->line('our_departments')); ?></h2>
      <?php if(!empty($dept_sec['subtitle'])): ?><p class="head-sub"><?php echo html_escape($dept_sec['subtitle']); ?></p><?php endif; ?>
    </div>
    <div class="pill-cloud">
      <?php foreach($specs_to_render as $sp): ?>
      <span class="dpill">
        <?php echo html_escape(ucfirst($sp['specialist_name'])); ?>
      </span>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
$doc_sec      = isset($home_sections['doctors']) ? $home_sections['doctors'] : array();
$skip_doctors = !empty($organic_skip_doctors);
$appt_url     = site_url('form/appointment');
?>
<?php if(!$skip_doctors && ($doc_sec['show'] ?? true) && !empty($home_doctors)):
  $doc_limit = (int)($doc_sec['count'] ?? 4);
  $docs_to_render = $doc_limit > 0 ? array_slice($home_doctors, 0, $doc_limit) : $home_doctors;
?>
<section class="docs">
  <div class="container">
    <div class="head-row split">
      <div>
        <?php if(!empty($doc_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($doc_sec['kicker']); ?></span><?php endif; ?>
        <h2><?php echo html_escape($doc_sec['title'] ?? $this->lang->line('our_doctors')); ?></h2>
      </div>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a href="<?php echo $appt_url; ?>" class="ghost-link"><?php echo $this->lang->line('book_appointment'); ?> →</a>
      <?php endif; ?>
    </div>
    <div class="doc-grid">
      <?php foreach($docs_to_render as $doc):
        $photo   = !empty($doc['image']) ? base_url('uploads/staff_images/' . $doc['image']) : '';
        $name    = html_escape(trim($doc['name'] . ' ' . $doc['surname']));
        $doc_sub = !empty($doc['designation']) ? $doc['designation'] : ($doc['department_name'] ?? '');
      ?>
      <article class="doc">
        <div class="doc-photo">
          <svg class="doc-photo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="8" r="4"/><path d="M4 22a8 8 0 0 1 16 0"/></svg>
          <?php if($photo): ?>
          <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" onerror="this.style.display='none'"/>
          <?php endif; ?>
        </div>
        <h4><?php echo $name; ?></h4>
        <?php if(!empty($doc_sub)): ?><p class="doc-spec"><?php echo html_escape($doc_sub); ?></p><?php endif; ?>
        <div class="doc-rows">
          <?php if(!empty($doc['work_exp'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('experience'); ?></span><strong><?php echo html_escape($doc['work_exp']); ?></strong></div>
          <?php endif; ?>
          <?php if(!empty($doc['qualification'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('qualification'); ?></span><strong><?php echo html_escape($doc['qualification']); ?></strong></div>
          <?php endif; ?>
          <?php if(!empty($doc['specialization'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('specialization'); ?></span><strong><?php echo html_escape($doc['specialization']); ?></strong></div>
          <?php endif; ?>
        </div>
        <div class="doc-foot">
          <?php if($front_setting->is_active_online_appointment): ?>
          <a href="<?php echo $appt_url; ?>"><?php echo $this->lang->line('book_appointment'); ?> →</a>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
$doc_sec  = isset($home_sections['doctors']) ? $home_sections['doctors'] : array();
$appt_url = site_url('form/appointment');
?>
<?php if(($doc_sec['show'] ?? true) && !empty($home_doctors) && ($atrium_skip_doctors ?? false) === false):
  $doc_limit = (int)($doc_sec['count'] ?? 4);
  $docs_to_render = $doc_limit > 0 ? array_slice($home_doctors, 0, $doc_limit) : $home_doctors;
?>
<section class="docs">
  <div class="container">
    <header class="sec-head split">
      <div>
        <?php if(!empty($doc_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($doc_sec['kicker']); ?></span><?php endif; ?>
        <h2><?php echo html_escape($doc_sec['title'] ?? $this->lang->line('our_doctors')); ?></h2>
      </div>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a class="link-arrow" href="<?php echo $appt_url; ?>"><?php echo $this->lang->line('book_appointment'); ?>  →</a>
      <?php endif; ?>
    </header>
    <div class="doc-grid">
      <?php foreach($docs_to_render as $doc):
        $photo = !empty($doc['image']) ? base_url('uploads/staff_images/' . $doc['image']) : '';
        $name  = html_escape(trim($doc['name'] . ' ' . $doc['surname']));
        $sub   = !empty($doc['designation']) ? $doc['designation'] : ($doc['department_name'] ?? '');
        $fee   = isset($doc['consult_fee']) ? (float)$doc['consult_fee'] : 0;
      ?>
      <a class="doc" href="<?php echo $appt_url; ?>">
        <div class="doc-photo">
          <?php if($photo): ?>
          <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" onerror="this.style.display='none'"/>
          <?php else: ?>
          <svg class="doc-photo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="8" r="4"/><path d="M4 22a8 8 0 0 1 16 0"/></svg>
          <?php endif; ?>
          <div class="doc-info">
            <h3 class="doc-name"><?php echo $name; ?></h3>
            <?php if(!empty($sub)): ?><span class="doc-spec"><?php echo html_escape($sub); ?></span><?php endif; ?>
          </div>
        </div>
        <div class="doc-body">
          <?php if(!empty($doc['work_exp'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('experience'); ?></span><strong><?php echo html_escape($doc['work_exp']); ?></strong></div>
          <?php endif; ?>
          <?php if(!empty($doc['qualification'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('qualification'); ?></span><strong><?php echo html_escape($doc['qualification']); ?></strong></div>
          <?php endif; ?>
          <?php if(!empty($doc['specialization'])): ?>
          <div class="doc-row"><span><?php echo $this->lang->line('specialization'); ?></span><strong><?php echo html_escape($doc['specialization']); ?></strong></div>
          <?php endif; ?>
          <div class="doc-cta">
            <?php if($fee > 0): ?>
            <span class="docfee"><?php echo isset($school_setting->currency_symbol) ? $school_setting->currency_symbol : ''; ?><?php echo number_format($fee, 0); ?> <em>/ <?php echo $this->lang->line('visit'); ?></em></span>
            <?php else: ?>
            <span></span>
            <?php endif; ?>
            <span class="docbook"><?php echo $this->lang->line('book_appointment'); ?> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

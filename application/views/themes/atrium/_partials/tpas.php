<?php $tpas_sec = isset($home_sections['tpas']) ? $home_sections['tpas'] : array(); ?>
<?php if(($tpas_sec['show'] ?? true) && !empty($tpas_sec['items'])): ?>
<section class="ins">
  <div class="container ins-row">
    <div class="ins-l">
      <?php if(!empty($tpas_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($tpas_sec['kicker']); ?></span><?php endif; ?>
      <h3><?php echo html_escape($tpas_sec['title'] ?? 'Cashless cover'); ?></h3>
      <?php if(!empty($tpas_sec['subtitle'])): ?><p><?php echo html_escape($tpas_sec['subtitle']); ?></p><?php endif; ?>
      <?php if(!empty($tpas_sec['cta_text'])): ?>
      <a class="link-arrow" href="<?php echo html_escape($tpas_sec['cta_url'] ?: '#'); ?>"><?php echo html_escape($tpas_sec['cta_text']); ?>  →</a>
      <?php endif; ?>
    </div>
    <ul class="ins-grid">
      <?php foreach($tpas_sec['items'] as $tp): if(empty($tp['name'])) continue; ?>
      <li><?php echo html_escape($tp['name']); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

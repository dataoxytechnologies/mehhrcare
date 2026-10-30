<?php
$hs        = $home_sections ?? array();
$hero      = $hs['hero']     ?? array();
$appt_url  = site_url('form/appointment');

$bg_video  = $hero['bg_video'] ?? '';
$bg_image  = $hero['bg_image'] ?? '';
$badge     = $hero['badge_text'] ?? '';

$creds_str = $hero['doc_creds'] ?? '';
$creds     = array_values(array_filter(array_map('trim', explode(',', $creds_str))));
?>

<?php if(($hero['show'] ?? true)): ?>
<section class="hero cardio">
  <div class="hero-media">
    <?php if(!empty($bg_video)): ?>
    <video autoplay muted loop playsinline preload="auto" poster="<?php echo html_escape($bg_image); ?>">
      <source src="<?php echo html_escape($bg_video); ?>" type="video/mp4"/>
    </video>
    <?php endif; ?>
    <img class="hero-poster" alt="" src="<?php echo html_escape($bg_image); ?>" onerror="this.style.display='none'"/>
  </div>

  <div class="container hero-content">
    <div class="hero-l">
      <?php if(!empty($badge)): ?>
      <span class="hero-badge"><span class="pulse"></span> <?php echo html_escape($badge); ?></span>
      <?php endif; ?>
      <h1><?php echo html_escape($hero['headline'] ?? 'Specialist cardiology care, doctor-led.'); ?></h1>
      <?php if(!empty($hero['subheadline'])): ?>
      <p class="hero-lede"><?php echo html_escape($hero['subheadline']); ?></p>
      <?php endif; ?>
      <div class="hero-actions">
        <?php if($front_setting->is_active_online_appointment): ?>
        <a href="<?php echo $appt_url; ?>" class="btn-fill lg">
          <?php echo html_escape($hero['cta_primary'] ?? $this->lang->line('book_appointment')); ?>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <?php endif; ?>
        <?php if(!empty($hero['cta_secondary'])): ?>
        <a href="#departments" class="btn-line lg"><?php echo html_escape($hero['cta_secondary']); ?></a>
        <?php endif; ?>
      </div>
      <?php
        $has_trust = false;
        for($i=1; $i<=3; $i++){ if(!empty($hero['trust_'.$i.'_value']) || !empty($hero['trust_'.$i.'_label'])){ $has_trust = true; break; } }
      ?>
      <?php if($has_trust): ?>
      <div class="hero-trust">
        <?php for($i=1; $i<=3; $i++):
          $val = $hero['trust_'.$i.'_value'] ?? '';
          $lbl = $hero['trust_'.$i.'_label'] ?? '';
          if(empty($val) && empty($lbl)) continue;
        ?>
          <span><?php if(!empty($val)): ?><strong><?php echo html_escape($val); ?></strong><?php endif; ?> <?php echo html_escape($lbl); ?></span>
          <?php if($i < 3): ?><span class="vs"></span><?php endif; ?>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </div>

    <?php
      $has_doc_card = !empty($hero['doc_name']) || !empty($hero['doc_photo']) || !empty($hero['doc_cta']);
    ?>
    <?php if($has_doc_card): ?>
    <div class="hero-r">
      <div class="doc-card">
        <div class="dc-top">
          <?php if(!empty($hero['doc_photo'])): ?>
          <div class="dc-photo">
            <img src="<?php echo html_escape($hero['doc_photo']); ?>" alt="<?php echo html_escape($hero['doc_name'] ?? ''); ?>" onerror="this.style.display='none'"/>
          </div>
          <?php endif; ?>
          <div>
            <?php if(!empty($hero['doc_name'])): ?><h3 class="dc-name"><?php echo html_escape($hero['doc_name']); ?></h3><?php endif; ?>
            <?php if(!empty($hero['doc_role'])): ?><span class="dc-role"><?php echo html_escape($hero['doc_role']); ?></span><?php endif; ?>
          </div>
        </div>
        <?php
          $has_doc_stats = false;
          for($i=1; $i<=3; $i++){ if(!empty($hero['doc_stat_'.$i.'_value']) || !empty($hero['doc_stat_'.$i.'_label'])){ $has_doc_stats = true; break; } }
        ?>
        <?php if($has_doc_stats): ?>
        <div class="dc-divider"></div>
        <div class="dc-stats">
          <?php for($i=1; $i<=3; $i++):
            $val = $hero['doc_stat_'.$i.'_value'] ?? '';
            $lbl = $hero['doc_stat_'.$i.'_label'] ?? '';
            if(empty($val) && empty($lbl)) continue;
          ?>
          <div class="dc-stat">
            <strong><?php echo html_escape($val); ?></strong>
            <span><?php echo html_escape($lbl); ?></span>
          </div>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php if(!empty($creds)): ?>
        <div class="dc-creds">
          <?php foreach($creds as $cred): ?>
          <span class="dc-cred"><?php echo html_escape($cred); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if(!empty($hero['doc_cta'])): ?>
        <a href="<?php echo $appt_url; ?>" class="dc-cta">
          <span><?php echo html_escape($hero['doc_cta']); ?></span>
          <?php if(!empty($hero['doc_next'])): ?><em><?php echo html_escape($hero['doc_next']); ?></em><?php endif; ?>
        </a>
        <?php endif; ?>
        <?php if(!empty($hero['doc_fine'])): ?>
        <span class="dc-fine"><?php echo html_escape($hero['doc_fine']); ?></span>
        <?php endif; ?>
      </div>
      <?php $this->load->view('themes/atrium/_latest-news'); ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if(!empty($hero['ecg_left']) || !empty($hero['ecg_right'])): ?>
  <div class="ecg-ribbon">
    <span><?php if(!empty($hero['ecg_left'])): ?><span class="ecg-dot"></span> <?php echo html_escape($hero['ecg_left']); ?><?php endif; ?></span>
    <svg viewBox="0 0 200 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" preserveAspectRatio="none">
      <path d="M0 12 L40 12 L48 12 L52 4 L58 20 L64 8 L70 16 L76 12 L120 12 L128 12 L132 4 L138 20 L144 8 L150 16 L156 12 L200 12"/>
    </svg>
    <span><?php echo html_escape($hero['ecg_right'] ?? ''); ?></span>
  </div>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php echo $this->load->view('themes/atrium/_home-shared-sections', array(), true); ?>

<?php
$hs       = $home_sections ?? array();
$hero     = $hs['hero']         ?? array();
$quick    = $hs['quick_tiles']  ?? array();
$dept_sec = $hs['departments']  ?? array();
$doc_sec  = $hs['doctors']      ?? array();
$stats_sec= $hs['stats']        ?? array();
$test_sec = $hs['testimonials'] ?? array();
$cta_sec  = $hs['cta']          ?? array();
$appt_url = site_url('form/appointment');
$bg_video = $hero['bg_video'] ?? '';
$bg_image = $hero['bg_image'] ?? '';
?>

<?php if(($hero['show'] ?? true)): ?>
<section class="hero hero-spec">
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
      <h1><?php echo html_escape($hero['headline'] ?? 'Specialist care, personalised to you'); ?></h1>
      <p class="hero-lede"><?php echo html_escape($hero['subheadline'] ?? ''); ?></p>
      <div class="hero-actions">
        <?php if($front_setting->is_active_online_appointment): ?>
        <a href="<?php echo $appt_url; ?>" class="btn-fill lg">
          <?php echo html_escape($hero['cta_primary'] ?? $this->lang->line('book_appointment')); ?>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php if(!empty($home_doctors[0])): $doc = $home_doctors[0];
      $photo = !empty($doc['image']) ? base_url('uploads/staff/' . $doc['image']) : '';
      $name  = html_escape(trim($doc['name'] . ' ' . $doc['surname']));
    ?>
    <div class="hero-r">
      <div class="hero-card doc-card-hero">
        <?php if($photo): ?>
        <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" class="doc-card-img" onerror="this.style.display='none'"/>
        <?php endif; ?>
        <h3 class="doc-card-name"><?php echo $name; ?></h3>
        <p><?php echo html_escape($doc['department_name'] ?? ''); ?></p>
        <?php if(!empty($doc['qualification'])): ?><p><?php echo html_escape($doc['qualification']); ?></p><?php endif; ?>
        <?php if($front_setting->is_active_online_appointment): ?>
        <a href="<?php echo $appt_url; ?>" class="hc-cta hc-cta-block"><?php echo $this->lang->line('book_appointment'); ?></a>
        <?php endif; ?>
      </div>
      <?php $this->load->view('themes/atrium/_latest-news'); ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php
// Shared sections — same as hospital layout
echo $this->load->view('themes/atrium/_home-shared-sections', array(), true);
?>

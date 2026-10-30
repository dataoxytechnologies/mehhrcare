<?php
$hs        = $home_sections     ?? array();
$hero      = $hs['hero']         ?? array();
$pop_tests = $hs['popular_tests']?? array();
$appt_url  = site_url('form/appointment');
// Diagnostic layout skips the doctors section
$atrium_skip_doctors = true;
$bg_video = $hero['bg_video'] ?? '';
$bg_image = $hero['bg_image'] ?? '';
?>

<?php if(($hero['show'] ?? true)): ?>
<section class="hero hero-diag">
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
      <h1><?php echo html_escape($hero['headline'] ?? 'Lab reports in 4 hours. Accurate. NABL.'); ?></h1>
      <p class="hero-lede"><?php echo html_escape($hero['subheadline'] ?? ''); ?></p>
      <div class="hero-actions">
        <a href="<?php echo site_url('form/appointment'); ?>" class="btn-fill lg">
          <?php echo html_escape($hero['cta_primary'] ?? $this->lang->line('book_test')); ?>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
    <div class="hero-r">
      <div class="hero-card">
        <div class="hc-tabs"><span class="hc-tab on"><?php echo $this->lang->line('search_tests'); ?></span></div>
        <form class="hc-body" action="<?php echo site_url('frontend'); ?>" method="get">
          <div class="hc-row">
            <label>
              <span><?php echo $this->lang->line('test_or_package'); ?></span>
              <input type="text" name="q" placeholder="e.g. CBC · Thyroid · Vitamin D"/>
            </label>
          </div>
          <button type="submit" class="hc-cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M21 21l-5.5-5.5"/></svg>
            <?php echo $this->lang->line('search'); ?>
          </button>
        </form>
      </div>
      <?php $this->load->view('themes/atrium/_latest-news'); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(($pop_tests['show'] ?? true) && !empty($pop_tests['items'])): ?>
<section class="quicks">
  <div class="container">
    <header class="sec-head center sec-head-mb24">
      <h2><?php echo html_escape($pop_tests['title'] ?? $this->lang->line('popular_tests')); ?></h2>
    </header>
    <div class="quick-grid">
      <?php foreach($pop_tests['items'] as $t): ?>
      <a class="qcard" href="<?php echo $appt_url; ?>">
        <span class="qtitle"><?php echo html_escape($t['name'] ?? ''); ?></span>
        <?php if(!empty($t['tag'])): ?><span class="qicon qtag-accent"><?php echo html_escape($t['tag']); ?></span><?php endif; ?>
        <?php if(!empty($t['discount'])): ?><span class="qsub qsub-discount"><?php echo html_escape($t['discount']); ?></span><?php endif; ?>
        <span class="qmore qmore-price"><?php echo html_escape($t['price'] ?? ''); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php echo $this->load->view('themes/atrium/_home-shared-sections', array('atrium_skip_doctors' => true), true); ?>

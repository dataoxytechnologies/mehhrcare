<?php
$hs        = $home_sections ?? array();
$hero      = $hs['hero']         ?? array();
$pop_tests = $hs['popular_tests'] ?? array();
$appt_url  = site_url('form/appointment');
$bg_video  = $hero['bg_video'] ?? '';
$bg_image  = $hero['bg_image'] ?? '';
?>

<?php if(($hero['show'] ?? true)): ?>
<section class="hero-fb">
  <div class="hero-fb-media">
    <?php if(!empty($bg_video)): ?>
    <video autoplay muted loop playsinline preload="auto" poster="<?php echo html_escape($bg_image); ?>">
      <source src="<?php echo html_escape($bg_video); ?>" type="video/mp4"/>
    </video>
    <?php endif; ?>
    <img class="hero-fb-poster" alt="" src="<?php echo html_escape($bg_image); ?>" onerror="this.style.display='none'"/>
  </div>
  <div class="container hero-fb-content">
    <div class="hero-fb-l">
      <span class="hero-fb-badge"><span class="dot"></span><?php echo html_escape($hero['badge_text'] ?? 'Diagnostics · NABL accredited'); ?></span>
      <h1><?php echo html_escape($hero['headline'] ?? 'Tests, made transparent'); ?></h1>
      <p class="hero-fb-lede"><?php echo html_escape($hero['subheadline'] ?? 'Lab and imaging with same-day reports, home collection, and clear pricing.'); ?></p>
      <div class="hero-fb-actions">
        <a href="<?php echo $appt_url; ?>" class="pill pill-primary">
          <?php echo html_escape($hero['cta_primary'] ?? 'Book a test'); ?>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <?php if(!empty($hero['cta_secondary'])): ?>
        <a href="#popular-tests" class="pill pill-ghost"><?php echo html_escape($hero['cta_secondary']); ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php if($front_setting->is_active_online_appointment): ?>
    <div class="hero-fb-r">
      <div class="hero-fb-card">
        <div class="hc-tabs">
          <span class="hc-tab">Home collection</span>
        </div>
        <form action="<?php echo $appt_url; ?>" method="get">
          <div class="hc-row">
            <label for="hf-test"><?= $this->lang->line('test_or_package') ?></label>
            <input type="text" id="hf-test" name="q" placeholder="e.g. Thyroid profile, MRI brain"/>
          </div>
          <button type="submit" class="hc-cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M21 21l-5.5-5.5"/></svg>
            <?php echo $this->lang->line('check_availability'); ?>
          </button>
        </form>
      </div>
      <?php $this->load->view('themes/organic/_latest-news'); ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if(!empty($pop_tests) && is_array($pop_tests)): ?>
<section class="organic-pop-tests" id="popular-tests">
  <div class="container">
    <div class="head-row">
      <span class="kicker">Popular tests</span>
      <h2><?= $this->lang->line('most_booked_this_week') ?></h2>
    </div>
    <div class="grid">
      <?php foreach($pop_tests as $t): ?>
      <a class="card" href="<?php echo $appt_url; ?>">
        <span class="name"><?php echo html_escape($t['title'] ?? ''); ?></span>
        <?php if(!empty($t['subtitle'])): ?><span class="meta"><?php echo html_escape($t['subtitle']); ?></span><?php endif; ?>
        <span class="price">
          <?php if(!empty($t['discount'])): ?><span class="strike"><?php echo html_escape($t['discount']); ?></span><?php endif; ?>
          <?php echo html_escape($t['price'] ?? ''); ?>
        </span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php echo $this->load->view('themes/organic/_home-shared-sections', array('organic_skip_doctors' => true), true); ?>

<?php
$hs       = $home_sections ?? array();
$hero     = $hs['hero']       ?? array();
$appt_url = site_url('form/appointment');
$bg_video = $hero['bg_video'] ?? '';
$bg_image = $hero['bg_image'] ?? '';
?>

<?php if(($hero['show'] ?? true)): ?>
<section class="hero hero-eye">
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
      <h1><?php echo html_escape($hero['headline'] ?? 'See the world clearly.'); ?></h1>
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
    <?php if($front_setting->is_active_online_appointment): ?>
    <div class="hero-r">
      <div class="hero-card">
        <div class="hc-tabs"><span class="hc-tab on"><?php echo $this->lang->line('check_eligibility'); ?></span></div>
        <form class="hc-body" action="<?php echo $appt_url; ?>" method="get">
          <div class="hc-row">
            <label>
              <span><?php echo $this->lang->line('select_procedure'); ?></span>
              <select name="procedure">
                <option value=""><?= $this->lang->line('lasik') ?></option>
                <option value=""><?= $this->lang->line('smile') ?></option>
                <option value=""><?= $this->lang->line('cataract') ?></option>
                <option value=""><?= $this->lang->line('glaucoma') ?></option>
              </select>
            </label>
          </div>
          <button type="submit" class="hc-cta"><?php echo $this->lang->line('check_eligibility'); ?></button>
        </form>
      </div>
      <?php $this->load->view('themes/atrium/_latest-news'); ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php echo $this->load->view('themes/atrium/_home-shared-sections', array(), true); ?>

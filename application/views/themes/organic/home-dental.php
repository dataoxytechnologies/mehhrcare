<?php
$hs       = $home_sections ?? array();
$hero     = $hs['hero'] ?? array();
$appt_url = site_url('form/appointment');
$bg_video = $hero['bg_video'] ?? '';
$bg_image = $hero['bg_image'] ?? '';
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
      <span class="hero-fb-badge"><span class="dot"></span><?php echo html_escape($hero['badge_text'] ?? 'Dental · Family practice'); ?></span>
      <h1><?php echo html_escape($hero['headline'] ?? 'Smiles, gently shaped'); ?></h1>
      <p class="hero-fb-lede"><?php echo html_escape($hero['subheadline'] ?? 'Calm, kid-friendly dentistry for the whole family — from check-ups to cosmetic care.'); ?></p>
      <div class="hero-fb-actions">
        <?php if($front_setting->is_active_online_appointment): ?>
        <a href="<?php echo $appt_url; ?>" class="pill pill-primary">
          <?php echo html_escape($hero['cta_primary'] ?? $this->lang->line('book_appointment')); ?>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <?php endif; ?>
        <?php if(!empty($hero['cta_secondary'])): ?>
        <a href="#departments" class="pill pill-ghost"><?php echo html_escape($hero['cta_secondary']); ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php if($front_setting->is_active_online_appointment): ?>
    <div class="hero-fb-r">
      <div class="hero-fb-card">
        <div class="hc-tabs">
          <span class="hc-tab"><?php echo $this->lang->line('book_appointment'); ?></span>
        </div>
        <form action="<?php echo $appt_url; ?>" method="get">
          <div class="hc-row">
            <label for="hf-spec"><?php echo $this->lang->line('specialist'); ?></label>
            <select id="hf-spec" name="specialist">
              <option value="">— <?php echo $this->lang->line('select'); ?> —</option>
              <?php if(!empty($home_specialists)): foreach($home_specialists as $s): ?>
              <option value="<?php echo (int)$s['id']; ?>"><?php echo html_escape($s['specialist_name']); ?></option>
              <?php endforeach; endif; ?>
            </select>
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

<?php echo $this->load->view('themes/organic/_home-shared-sections', array(), true); ?>

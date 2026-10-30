<?php
$hospital_name = html_escape($school_setting->name ?? 'Smart Hospital');
$footer_text   = !empty($front_setting->footer_text) ? html_escape($front_setting->footer_text) : ('&copy; ' . date('Y') . ' ' . $hospital_name);
$logo_url      = !empty($front_setting->logo) ? html_escape($this->customlib->getBaseUrl() . $front_setting->logo) : '';
?>
<footer class="ftr">
  <div class="container">
    <div class="ftr-top">
      <div class="ftr-brand">
        <a href="<?php echo site_url('frontend'); ?>" class="brand">
          <?php if($logo_url): ?>
          <img src="<?php echo $logo_url; ?>" alt="<?php echo $hospital_name; ?>" class="brand-logo-sm">
          <?php else: ?>
          <svg class="logo" viewBox="0 0 56 56" aria-hidden="true">
            <path d="M10 28 q9 -22 18 0 q9 22 18 0" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
            <circle cx="28" cy="28" r="3.2" fill="currentColor"/>
          </svg>
          <span class="brand-text"><span class="wmark"><?php echo $hospital_name; ?></span></span>
          <?php endif; ?>
        </a>
        <?php if(!empty($school_setting->description)): ?>
        <p><?php echo html_escape($school_setting->description); ?></p>
        <?php endif; ?>
      </div>

      <?php if(!empty($footer_menus)): ?>
      <div class="cols">
        <?php
        $cols = array_chunk($footer_menus, ceil(count($footer_menus) / 3));
        foreach($cols as $col): ?>
        <div>
          <?php foreach($col as $item): ?>
            <?php if(!empty($item['submenus'])): ?>
            <h5><?php echo html_escape($item['menu']); ?></h5>
            <?php foreach($item['submenus'] as $child):
              $c_url = $child['ext_url'] ? $child['ext_url_link'] : site_url($child['page_url'] ?? '');
            ?>
            <a href="<?php echo $c_url; ?>"><?php echo html_escape($child['menu']); ?></a>
            <?php endforeach; ?>
            <?php else:
              $i_url = $item['ext_url'] ? $item['ext_url_link'] : site_url($item['page_url'] ?? '');
            ?>
            <a href="<?php echo $i_url; ?>"><?php echo html_escape($item['menu']); ?></a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="ftr-bot">
      <span><?php echo $footer_text; ?></span>
      <?php /* Feedback / Contact Us are menu-driven: add them in Front CMS → Menus → Bottom Menu. */ ?>
      <span class="ftr-social">
        <?php $this->view('/themes/organic/social_media'); ?>
      </span>
    </div>
  </div>
  <span class="ftr-blob" aria-hidden="true"></span>
</footer>

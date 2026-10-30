<?php
$hospital_name = html_escape($school_setting->name ?? 'Smart Hospital');
$footer_text   = !empty($front_setting->footer_text) ? html_escape($front_setting->footer_text) : ('&copy; ' . date('Y') . ' ' . $hospital_name);
$logo_url      = !empty($front_setting->logo) ? html_escape($this->customlib->getBaseUrl() . $front_setting->logo) : '';
?>
<footer class="ftr">
  <div class="container">
    <div class="ftr-grid">
      <div class="ftr-mast">
        <a href="<?php echo site_url('frontend'); ?>" class="brand">
          <?php if($logo_url): ?>
          <img src="<?php echo $logo_url; ?>" alt="<?php echo $hospital_name; ?>" class="brand-logo-sm">
          <?php else: ?>
          <span class="brand-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg>
          </span>
          <span class="brand-text">
            <span class="brand-name"><?php echo $hospital_name; ?></span>
          </span>
          <?php endif; ?>
        </a>
        <?php if(!empty($school_setting->description)): ?>
        <p class="ftr-tag"><?php echo html_escape($school_setting->description); ?></p>
        <?php endif; ?>
      </div>

      <?php if(!empty($footer_menus)): ?>
        <?php
        $cols = array_chunk($footer_menus, ceil(count($footer_menus) / 3));
        foreach($cols as $col): ?>
        <div class="ftr-col">
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
      <?php endif; ?>

      <?php /* Contact Us & Feedback are menu-driven: add them in Front CMS → Menus → Bottom Menu
               (link a Pages item to the contact-us / complain form-builder page). No hardcoded columns. */ ?>
    </div>

    <div class="ftr-bot">
      <span><?php echo $footer_text; ?></span>
      <span class="ftr-social">
        <?php $this->view('/themes/atrium/social_media'); ?>
      </span>
    </div>
  </div>
</footer>

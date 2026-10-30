<?php
$hs          = $home_sections ?? array();
$hero        = $hs['hero'] ?? array();
$emergency   = !empty($hero['emergency_number']) ? $hero['emergency_number'] : '';
$hospital_name = html_escape($school_setting->name ?? 'Smart Hospital');
$logo_url    = !empty($front_setting->logo) ? html_escape($this->customlib->getBaseUrl() . $front_setting->logo) : '';
?>
<!-- Header -->
<header class="hdr">
  <div class="container hdr-row">
    <a href="<?php echo site_url('frontend'); ?>" class="brand">
      <?php if($logo_url): ?>
      <img src="<?php echo $logo_url; ?>" alt="<?php echo $hospital_name; ?>" class="brand-logo-md">
      <?php else: ?>
      <svg class="logo" viewBox="0 0 56 56" aria-hidden="true">
        <path d="M10 28 q9 -22 18 0 q9 22 18 0" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
        <circle cx="28" cy="28" r="3.2" fill="currentColor"/>
      </svg>
      <span class="brand-text">
        <span class="wmark"><?php echo $hospital_name; ?></span>
      </span>
      <?php endif; ?>
    </a>

    <nav class="nav" aria-label="Primary">
      <?php if(!empty($main_menus)):
        $current_uri  = trim($this->uri->uri_string(), '/');
        $current_host = $_SERVER['HTTP_HOST'] ?? '';
        // Active when menu URL matches current path. Works for both CMS pages (page_url)
        // and admin-configured external/internal URLs (ext_url_link, e.g. "form/appointment").
        $menu_active = function($menu) use ($current_uri, $current_host) {
            // Home menu: active when on root ('') or 'frontend' route — its page_url is empty
            if (!empty($menu['is_homepage']) && ($current_uri === '' || $current_uri === 'frontend')) {
                return true;
            }
            $candidates = array();
            if (!empty($menu['page_url']))     $candidates[] = $menu['page_url'];
            if (!empty($menu['ext_url_link'])) $candidates[] = $menu['ext_url_link'];
            foreach ($candidates as $raw) {
                $u = $raw;
                // Strip protocol + host if it points to this site
                if (preg_match('#^https?://([^/]+)(/.*)?$#i', $u, $m)) {
                    if ($current_host && strcasecmp($m[1], $current_host) !== 0) continue;
                    $u = $m[2] ?? '';
                }
                $u = trim((string)$u, '/');
                if ($u === '') continue;
                if ($current_uri === $u) return true;
                if (strpos($current_uri, $u . '/') === 0) return true;
            }
            return false;
        };
      ?>
        <?php foreach($main_menus as $menu):
          $m_url = $menu['ext_url'] ? $menu['ext_url_link'] : site_url($menu['page_url'] ?? '');
          $m_tab = $menu['open_new_tab'] ? ' target="_blank"' : '';
          $m_active = $menu_active($menu);
          if (!$m_active && !empty($menu['submenus'])) {
              foreach ($menu['submenus'] as $_c) {
                  if ($menu_active($_c)) { $m_active = true; break; }
              }
          }
        ?>
          <?php if(!empty($menu['submenus'])): ?>
          <div class="nav-item has-mega<?php if($m_active) echo ' active'; ?>">
            <a href="<?php echo $m_url; ?>"<?php echo $m_tab; ?> class="nav-link">
              <?php echo html_escape($menu['menu']); ?>
              <svg class="chev" width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>
            </a>
            <div class="mega mega-2">
              <div class="mega-col">
                <?php foreach($menu['submenus'] as $child):
                  $c_url = $child['ext_url'] ? $child['ext_url_link'] : site_url($child['page_url'] ?? '');
                  $c_tab = $child['open_new_tab'] ? ' target="_blank"' : '';
                ?>
                <a class="mega-item" href="<?php echo $c_url; ?>"<?php echo $c_tab; ?>>
                  <span class="mi-text"><strong><?php echo html_escape($child['menu']); ?></strong></span>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php else: ?>
          <div class="nav-item<?php if($m_active) echo ' active'; ?>">
            <a href="<?php echo $m_url; ?>"<?php echo $m_tab; ?> class="nav-link"><?php echo html_escape($menu['menu']); ?></a>
          </div>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </nav>

    <div class="hdr-cta">
      <?php /* Feedback / Contact Us are menu-driven: add them in Front CMS → Menus. No hardcoded link. */ ?>
      <?php if($emergency): ?>
      <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $emergency); ?>" class="phone">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <?php echo html_escape($emergency); ?>
      </a>
      <?php endif; ?>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a href="<?php echo site_url('form/appointment'); ?>" class="cta-blob">
        <?php echo $this->lang->line('book_appointment'); ?>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
      <?php endif; ?>
      <button class="hdr-burger" id="hdrBurger" aria-label="<?php echo $this->lang->line('menu') ?: 'Menu'; ?>" aria-expanded="false" aria-controls="mobNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<!-- Mobile nav drawer -->
<div class="mob-nav" id="mobNav" aria-hidden="true">
  <div class="mob-nav-inner">
    <div class="mob-nav-head">
      <span class="mob-brand-name"><?php echo $hospital_name; ?></span>
      <button class="mob-nav-close" id="mobNavClose" aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <nav class="mob-nav-links">
      <?php if(!empty($main_menus)): foreach($main_menus as $menu):
        $m_url = $menu['ext_url'] ? $menu['ext_url_link'] : site_url($menu['page_url'] ?? '');
        $m_tab = $menu['open_new_tab'] ? ' target="_blank"' : '';
        $mm_active = $menu_active($menu);
        if (!$mm_active && !empty($menu['submenus'])) {
            foreach ($menu['submenus'] as $_c) {
                if ($menu_active($_c)) { $mm_active = true; break; }
            }
        }
      ?>
      <a href="<?php echo $m_url; ?>"<?php echo $m_tab; ?> class="mob-link<?php if($mm_active) echo ' active'; ?>"><?php echo html_escape($menu['menu']); ?></a>
      <?php if(!empty($menu['submenus'])): foreach($menu['submenus'] as $child):
        $c_url = $child['ext_url'] ? $child['ext_url_link'] : site_url($child['page_url'] ?? '');
        $c_tab = $child['open_new_tab'] ? ' target="_blank"' : '';
        $mc_active = $menu_active($child);
      ?>
      <a href="<?php echo $c_url; ?>"<?php echo $c_tab; ?> class="mob-link sub<?php if($mc_active) echo ' active'; ?>"><?php echo html_escape($child['menu']); ?></a>
      <?php endforeach; endif; ?>
      <?php endforeach; endif; ?>
    </nav>
    <div class="mob-nav-foot">
      <a href="<?php echo site_url('site/userlogin'); ?>" class="mob-link mob-link-noborder"><?php echo $this->lang->line('patient_portal') ?: 'Patient portal'; ?></a>
      <?php if($front_setting->is_active_online_appointment): ?>
      <a href="<?php echo site_url('form/appointment'); ?>" class="cta-blob mob-cta-full">
        <?php echo $this->lang->line('book_appointment'); ?>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="mob-nav-overlay" id="mobNavOverlay"></div>

<script>
(function(){
  var burger  = document.getElementById('hdrBurger');
  var nav     = document.getElementById('mobNav');
  var close   = document.getElementById('mobNavClose');
  var overlay = document.getElementById('mobNavOverlay');
  function openNav()  { nav.classList.add('open'); overlay.classList.add('show'); document.body.style.overflow='hidden'; burger.setAttribute('aria-expanded','true');  nav.setAttribute('aria-hidden','false'); }
  function closeNav() { nav.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow=''; burger.setAttribute('aria-expanded','false'); nav.setAttribute('aria-hidden','true'); }
  if(burger)  burger.addEventListener('click', openNav);
  if(close)   close.addEventListener('click', closeNav);
  if(overlay) overlay.addEventListener('click', closeNav);
})();
</script>

<!-- Priority+ nav: overflow items collapse into "More" dropdown -->
<script>
(function(){
  var nav = document.querySelector('.hdr .nav');
  if (!nav) return;
  var moreLabel = <?php echo json_encode($this->lang->line('more') ?: 'More'); ?>;

  var originalItems = [];
  Array.prototype.forEach.call(nav.children, function(el){
    if (el.classList && el.classList.contains('nav-item')) originalItems.push(el);
  });
  if (!originalItems.length) return;

  var moreItem = document.createElement('div');
  moreItem.className = 'nav-item has-mega nav-more';
  moreItem.innerHTML =
    '<a href="javascript:void(0)" class="nav-link" aria-haspopup="true" aria-expanded="false">' +
      moreLabel +
      '<svg class="chev" width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M2 3.5L5 6.5L8 3.5"/></svg>' +
    '</a>' +
    '<div class="mega mega-2 nav-more-mega"><div class="mega-col"></div></div>';
  moreItem.style.display = 'none';
  nav.appendChild(moreItem);
  var moreCol = moreItem.querySelector('.mega-col');

  function escHTML(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
  function getGap(){var s=window.getComputedStyle(nav);return parseFloat(s.gap||s.columnGap||'0')||0;}
  function getLabel(item){
    var link=item.querySelector('.nav-link');if(!link)return '';
    var f=link.firstChild;
    if(f&&f.nodeType===3&&f.nodeValue.trim())return f.nodeValue.trim();
    return link.textContent.trim();
  }
  function reset(){
    originalItems.forEach(function(item){
      if(item.parentNode!==nav) nav.insertBefore(item,moreItem);
      item.style.display='';
    });
    moreCol.innerHTML='';
    moreItem.style.display='none';
  }
  function moveToMore(item){
    var label=getLabel(item);
    var subs=item.querySelectorAll('.mega .mega-item');
    if(subs.length){
      var head=document.createElement('div');
      head.className='mega-head nav-more-head';
      head.textContent=label;
      moreCol.appendChild(head);
      Array.prototype.forEach.call(subs,function(sm){moreCol.appendChild(sm.cloneNode(true));});
    }else{
      var origLink=item.querySelector('.nav-link');
      var a=document.createElement('a');
      a.className='mega-item';
      a.href=origLink&&origLink.getAttribute('href')||'#';
      var tgt=origLink&&origLink.getAttribute('target');
      if(tgt) a.setAttribute('target',tgt);
      a.innerHTML='<span class="mi-text"><strong>'+escHTML(label)+'</strong></span>';
      moreCol.appendChild(a);
    }
    item.style.display='none';
  }
  function recalc(){
    reset();
    if(nav.offsetParent===null||nav.clientWidth===0){nav.classList.add('nav-ready');return;}
    var navW=nav.clientWidth, gap=getGap();
    var total=0;
    originalItems.forEach(function(it,i){total+=it.offsetWidth+(i>0?gap:0);});
    if(total<=navW){nav.classList.add('nav-ready');return;}
    moreItem.style.display='';
    var moreW=moreItem.offsetWidth+gap;
    var avail=navW-moreW;
    var cum=0, splitAt=originalItems.length;
    for(var i=0;i<originalItems.length;i++){
      var w=originalItems[i].offsetWidth+(i>0?gap:0);
      if(cum+w>avail){splitAt=i;break;}
      cum+=w;
    }
    if(splitAt<1) splitAt=1;
    for(var j=splitAt;j<originalItems.length;j++) moveToMore(originalItems[j]);
    nav.classList.add('nav-ready');
  }
  function schedule(){setTimeout(recalc,0);}
  if(document.readyState==='complete') schedule();
  else window.addEventListener('load',schedule);
  document.addEventListener('DOMContentLoaded',function(){setTimeout(recalc,50);});
  if(document.fonts&&document.fonts.ready) document.fonts.ready.then(function(){setTimeout(recalc,50);});
  // Fallback: ensure nav is visible even if recalc never runs (e.g. JS error elsewhere)
  setTimeout(function(){nav.classList.add('nav-ready');}, 800);
  var rt;
  window.addEventListener('resize',function(){clearTimeout(rt);rt=setTimeout(recalc,100);});
})();
</script>

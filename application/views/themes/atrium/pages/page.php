<?php
$layout_type   = $page['layout_type']   ?? 'blank';
$page_sections = json_decode($page['page_section_data'] ?? '{}', true) ?: array();
$fimg          = $page['feature_image'] ?? '';
$page_title    = html_escape($page['title'] ?? '');
?>

<!-- Page hero -->
<section class="page-hero compact">
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="csep">·</span>
      <strong><?php echo $page_title; ?></strong>
    </nav>
    <h1><?php echo $page_title; ?></h1>
  </div>
</section>

<?php if($layout_type === 'contact'): ?>
<!-- Contact layout -->
<section class="block">
  <div class="container">
    <div class="cnt-grid">

      <div class="cnt-side">
        <?php if(!empty($page_sections['address'])): ?>
        <div class="cnt-block">
          <h5><?php echo $this->lang->line('address'); ?></h5>
          <p><?php echo nl2br(html_escape($page_sections['address'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['phone'])): ?>
        <div class="cnt-block">
          <h5><?php echo $this->lang->line('phone'); ?></h5>
          <strong><a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/','',$page_sections['phone'])); ?>"><?php echo html_escape($page_sections['phone']); ?></a></strong>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['email'])): ?>
        <div class="cnt-block">
          <h5><?php echo $this->lang->line('email'); ?></h5>
          <strong><a href="mailto:<?php echo html_escape($page_sections['email']); ?>"><?php echo html_escape($page_sections['email']); ?></a></strong>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['hours'])): ?>
        <div class="cnt-block">
          <h5><?php echo $this->lang->line('hours'); ?></h5>
          <p><?php echo nl2br(html_escape($page_sections['hours'])); ?></p>
        </div>
        <?php endif; ?>
      </div>

      <?php if(!empty($page_sections['map_embed'])): ?>
      <div class="cnt-card cnt-card-flush">
        <div class="map-embed-wrap"><?php echo $page_sections['map_embed']; ?></div>
      </div>
      <?php else: ?>
      <div class="cnt-card">
        <h3><?php echo $page_title; ?></h3>
        <p class="csub"><?php echo $this->lang->line('get_in_touch') ?: 'Get in touch with us.'; ?></p>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php elseif($layout_type === 'faq'): ?>
<!-- FAQ layout -->
<section class="block">
  <div class="container">
    <div class="faq-wrap">

      <?php if(!empty($page_sections['faq']) && is_array($page_sections['faq'])): ?>
      <nav class="faq-side">
        <h6><?php echo $this->lang->line('questions') ?: 'Questions'; ?></h6>
        <?php foreach($page_sections['faq'] as $i => $item): if(empty($item['question'])) continue; ?>
        <a href="#fq-<?php echo $i; ?>"><?php echo html_escape(substr($item['question'], 0, 46)); ?><?php if(strlen($item['question']) > 46) echo '…'; ?></a>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>

      <div class="faq-list">
        <div class="faq-cat">
          <?php if(!empty($page_sections['faq']) && is_array($page_sections['faq'])): ?>
          <?php foreach($page_sections['faq'] as $i => $item): if(empty($item['question'])) continue; ?>
          <details class="q" id="fq-<?php echo $i; ?>" <?php if($i===0) echo 'open'; ?>>
            <summary><?php echo html_escape($item['question']); ?></summary>
            <div class="q-body">
              <p><?php echo html_escape($item['answer'] ?? ''); ?></p>
            </div>
          </details>
          <?php endforeach; ?>
          <?php else: ?>
          <p class="text-muted"><?php echo $this->lang->line('no_record_found'); ?></p>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php elseif($layout_type === 'team'): ?>
<!-- Team layout -->
<?php
$this->load->model('Staff_model');
$team_count = (int)($page_sections['display_count'] ?? 12);
$this->db->select('staff.id, staff.name, staff.surname, staff.image, staff.qualification, department.department_name');
$this->db->from('staff');
$this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'inner');
$this->db->join('roles', 'roles.id = staff_roles.role_id', 'inner');
$this->db->join('department', 'department.id = staff.department_id', 'left');
$this->db->where('roles.name', 'Doctor');
$this->db->where('staff.is_active', 1);
$this->db->order_by('staff.id', 'ASC');
$this->db->limit($team_count);
$team_members = $this->db->get()->result_array();
?>
<section class="block">
  <div class="container">
    <?php if(!empty($page_sections['subheading'])): ?>
    <p class="page-subheading"><?php echo html_escape($page_sections['subheading']); ?></p>
    <?php endif; ?>
    <div class="lead-grid">
      <?php foreach($team_members as $m):
        $photo = !empty($m['image']) ? base_url('uploads/staff/' . $m['image']) : '';
        $name  = html_escape(trim($m['name'] . ' ' . $m['surname']));
      ?>
      <div class="lead">
        <div class="lead-photo">
          <?php if($photo): ?>
          <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" onerror="this.style.display='none'"/>
          <?php endif; ?>
        </div>
        <div class="lead-info">
          <h4><?php echo $name; ?></h4>
          <span class="role"><?php echo html_escape($m['department_name'] ?? ''); ?></span>
          <?php if(!empty($m['qualification'])): ?>
          <p class="lbio"><?php echo html_escape($m['qualification']); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php else: ?>
<!-- Blank layout — CKEditor content + events / gallery / notice -->
<?php
if($page_form) {
    if(!empty($form)) { ?>
    <section class="block">
      <div class="container container-narrow">
        <?php if(validation_errors()): ?>
        <div class="form-error-banner"><?php echo validation_errors(); ?></div>
        <?php endif;
        if($this->session->flashdata('msg')): ?>
        <div class="form-flash-banner"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php endif;
        $form_content  = $this->form_builder->open_form(array('action' => '', 'id' => 'open'));
        $defaults_object_or_array_from_db = NULL;
        $form_content .= "<input type='hidden' value='{$form_name}' name='form_name'/>";
        $form_content .= $this->form_builder->build_form_horizontal($form, $defaults_object_or_array_from_db);
        $form_content .= $this->form_builder->close_form();
        $replace_frm   = '[form-builder:' . $form_name . ']';
        echo str_replace($replace_frm, $form_content, $page['description']); ?>
      </div>
    </section>
    <?php }
} else {
    $_desc_text     = trim(strip_tags((string) ($page['description'] ?? '')));
    $_title_text    = trim((string) ($page['title'] ?? ''));
    $_desc_redundant = ($_desc_text === '') || (strcasecmp($_desc_text, $_title_text) === 0);
    $_has_gallery   = !empty($page['category_content']) && array_key_exists('gallery', $page['category_content']);
    if (!$_desc_redundant || !empty($fimg)) {
        echo '<section class="block' . ($_has_gallery ? ' tight' : '') . '"><div class="container">';
        if($fimg): echo '<img src="' . html_escape($fimg) . '" alt="" class="hero-img" onerror="this.style.display:\'none\'"/>'; endif;
        if (!$_desc_redundant) {
            echo '<div class="art-body">' . $page['description'] . '</div>';
        }
        echo '</div></section>';
    }
}
?>

<input type="hidden" name="page_content_type" id="page_content_type" value="<?php echo html_escape($page_content_type ?? ''); ?>">

<div id="postList">
<?php if(!empty($page['category_content'])): foreach($page['category_content'] as $pck => $pcv): ?>

  <?php if($pck === 'events'):
    // Bypass paginated $pcv so chip counts and the featured card cover every event.
    $this->load->model('cms_program_model');
    $all_events = $this->cms_program_model->getByCategory('events');
    $all_events = is_array($all_events) ? $all_events : array();
    // Drop XSS-cleaned junk rows.
    $all_events = array_values(array_filter($all_events, function($e) {
        $blob = ($e['title'] ?? '') . ' ' . ($e['description'] ?? '');
        return stripos($blob, '[removed]') === false;
    }));
    // Upcoming first.
    usort($all_events, function($a, $b) {
        return strtotime($a['event_start'] ?? '') <=> strtotime($b['event_start'] ?? '');
    });
    $total_events = count($all_events);
    $featured     = !empty($all_events) ? $all_events[0] : null;
    $rest_events  = array_slice($all_events, 1);
    $cat_counts = array();
    foreach ($rest_events as $_ev) {
        $_c = trim((string)($_ev['category'] ?? ''));
        if ($_c !== '') { $cat_counts[$_c] = ($cat_counts[$_c] ?? 0) + 1; }
    }
    $rest_count = count($rest_events);
  ?>
  <?php if ($total_events === 0): ?>
  <section class="block">
    <div class="container">
      <p class="empty-events"><?php echo $this->lang->line('no_record_found') ?: 'No upcoming events.'; ?></p>
    </div>
  </section>
  <?php else: ?>

  <?php if ($featured):
      $ff = $featured['feature_image'] ?: $this->media_storage->getImageURL('uploads/gallery/gallery_default.png');
      $f_start = !empty($featured['event_start']) ? strtotime($featured['event_start']) : null;
      $f_when  = $f_start ? date('D, d M · H:i', $f_start) : '';
  ?>
  <section class="block tight section-pb-0">
    <div class="container">
      <header class="sec-head split sec-head-mb28">
        <div>
          <span class="kicker"><?php echo html_escape($this->lang->line('featured') ?: 'Featured'); ?></span>
          <h2><?php echo html_escape($featured['title']); ?></h2>
        </div>
        <a class="link-arrow" href="<?php echo site_url($featured['url']); ?>"><?php echo html_escape($this->lang->line('view_details') ?: 'View details'); ?> →</a>
      </header>
      <div class="loc loc-grid-2col">
        <div class="loc-photo cover-tall-360">
          <?php if(!empty($featured['category'])): ?><span class="loc-flag"><?php echo html_escape($featured['category']); ?></span><?php endif; ?>
          <?php if($f_when): ?><span class="loc-tag"><?php echo html_escape($f_when); ?></span><?php endif; ?>
          <img src="<?php echo html_escape($ff); ?>" alt="<?php echo html_escape($featured['title']); ?>" class="cover-img-contain" onerror="this.style.display='none'"/>
        </div>
        <div class="loc-body loc-body-padded">
          <span class="kicker"><?php echo html_escape($this->lang->line('upcoming_event') ?: 'Upcoming event'); ?></span>
          <h3 class="loc-name loc-name-large"><?php echo html_escape($featured['title']); ?></h3>
          <p class="loc-addr loc-addr-15"><?php echo html_escape(substr(strip_tags($featured['description']), 0, 220)); ?></p>
          <ul class="loc-meta loc-meta-2col">
            <?php if($f_when): ?>
            <li><span><?php echo html_escape($this->lang->line('when') ?: 'When'); ?></span><strong><?php echo html_escape($f_when); ?></strong></li>
            <?php endif; ?>
            <?php if(!empty($featured['event_venue'])): ?>
            <li><span><?php echo html_escape($this->lang->line('where') ?: 'Where'); ?></span><strong><?php echo html_escape($featured['event_venue']); ?></strong></li>
            <?php endif; ?>
          </ul>
          <div class="loc-actions loc-actions-start">
            <a href="<?php echo site_url($featured['url']); ?>" class="btn-fill"><?php echo html_escape($this->lang->line('view_details') ?: 'View details'); ?>
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($rest_events)): ?>
  <section class="block soft" id="upcoming-events">
    <div class="container" data-evt-filter-root>
      <header class="sec-head split">
        <div>
          <span class="kicker"><?php echo html_escape($this->lang->line('upcoming_events') ?: 'Upcoming events'); ?></span>
          <h2><?php echo (int)$total_events; ?> <span class="grad"><?php echo html_escape(strtolower($this->lang->line('events') ?: 'events')); ?></span>.</h2>
        </div>
        <?php if (!empty($cat_counts)): ?>
        <div class="chips m-0">
          <button type="button" class="chip on" data-evt-chip="all"><?php echo html_escape($this->lang->line('all') ?: 'All'); ?><em>· <?php echo (int)$rest_count; ?></em></button>
          <?php foreach ($cat_counts as $cat_name => $cat_n): ?>
          <button type="button" class="chip" data-evt-chip="<?php echo html_escape(strtolower($cat_name)); ?>"><?php echo html_escape($cat_name); ?><em>· <?php echo (int)$cat_n; ?></em></button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </header>

      <div class="evt-grid">
        <?php foreach($rest_events as $ev):
          $ef        = $ev['feature_image'] ?: $this->media_storage->getImageURL('uploads/gallery/gallery_default.png');
          $ev_start  = !empty($ev['event_start']) ? strtotime($ev['event_start']) : null;
          $ev_cat    = trim((string)($ev['category'] ?? ''));
          $ev_catkey = $ev_cat !== '' ? strtolower($ev_cat) : '';
          $ev_time   = $ev_start ? date('H:i', $ev_start) : '';
        ?>
        <a href="<?php echo site_url($ev['url']); ?>" class="evt no-link-deco"
           data-evt-item="<?php echo html_escape($ev_catkey); ?>">
          <div class="evt-photo">
            <img src="<?php echo html_escape($ef); ?>" alt="<?php echo html_escape($ev['title']); ?>" onerror="this.style.display='none'"/>
            <?php if($ev_start): ?>
            <span class="evt-date">
              <span class="d"><?php echo date('d', $ev_start); ?></span>
              <span class="mo"><?php echo $this->lang->line(strtolower(date('M', $ev_start))) ?: date('M', $ev_start); ?></span>
            </span>
            <?php endif; ?>
            <?php if($ev_cat !== ''): ?><span class="evt-tag"><?php echo html_escape($ev_cat); ?></span><?php endif; ?>
          </div>
          <div class="evt-body">
            <h3><?php echo html_escape($ev['title']); ?></h3>
            <?php if($ev_time !== '' || !empty($ev['event_venue'])): ?>
            <div class="evt-meta">
              <?php if($ev_time !== ''): ?>
              <span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <?php echo html_escape($ev_time); ?>
              </span>
              <?php endif; ?>
              <?php if(!empty($ev['event_venue'])): ?>
              <span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo html_escape($ev['event_venue']); ?>
              </span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            <p class="evt-desc"><?php echo html_escape(substr(strip_tags($ev['description']), 0, 110)); ?></p>
            <div class="evt-foot">
              <span class="reg"><?php echo $this->lang->line('view_details'); ?> →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <script>
  (function(){
    var root = document.querySelector('[data-evt-filter-root]');
    if (!root) return;
    var chips = root.querySelectorAll('[data-evt-chip]');
    var items = root.querySelectorAll('[data-evt-item]');
    function apply(cat){
      chips.forEach(function(c){ c.classList.toggle('on', c.dataset.evtChip === cat); });
      items.forEach(function(it){
        var k = it.dataset.evtItem || '';
        it.style.display = (cat === 'all' || k === cat) ? '' : 'none';
      });
    }
    chips.forEach(function(c){ c.addEventListener('click', function(e){ e.preventDefault(); apply(c.dataset.evtChip); }); });
  })();
  </script>

  <?php endif; ?>

  <?php elseif($pck === 'notice'): ?>
  <section class="block">
    <div class="container container-narrow-760">
      <div class="notice-list-stack">
        <?php foreach((is_array($pcv) ? $pcv : array()) as $n): ?>
        <a href="<?php echo site_url($n['url']); ?>"
           class="notice-card-link"
           onmouseover="this.style.borderColor='var(--accent)';this.style.transform='translateY(-2px)';"
           onmouseout="this.style.borderColor='var(--line)';this.style.transform='';">
          <h4 class="notice-title"><?php echo html_escape($n['title']); ?></h4>
          <p class="notice-desc"><?php echo html_escape(substr(strip_tags($n['description']), 0, 120)); ?></p>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php elseif($pck === 'gallery'):
    // Load ALL galleries (bypass controller pagination) so filter chips cover every gallery.
    $this->load->model('cms_program_model');
    $all_galleries = $this->cms_program_model->getByCategory('gallery');
    $all_galleries = is_array($all_galleries) ? $all_galleries : array();
    $galleries_with_photos = array();
    $total_photos = 0;
    foreach ($all_galleries as $gal_row) {
        $photos = $this->cms_program_model->front_cms_program_photos($gal_row['id']);
        $photos = is_array($photos) ? $photos : array();
        $count  = count($photos);
        if ($count === 0) { continue; }
        $total_photos += $count;
        $galleries_with_photos[] = array(
            'id'          => (int) $gal_row['id'],
            'title'       => $gal_row['title'],
            'description' => $gal_row['description'] ?? '',
            'photos'      => $photos,
            'count'       => $count,
        );
    }
  ?>
  <section class="block tight gallery-filter-section">
    <div class="container">
      <div class="chips" role="tablist" aria-label="Gallery filter">
        <button type="button" class="chip on" data-filter="all"><?php echo $this->lang->line('all') ?: 'All'; ?><em>· <?php echo (int)$total_photos; ?></em></button>
        <?php foreach($galleries_with_photos as $g): ?>
        <button type="button" class="chip" data-filter="<?php echo (int)$g['id']; ?>"><?php echo html_escape($g['title']); ?><em>· <?php echo (int)$g['count']; ?></em></button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="block tight">
    <div class="container">
      <?php foreach($galleries_with_photos as $g): if(empty(trim(strip_tags($g['description'])))) continue; ?>
      <div class="gallery-desc" data-desc-for="<?php echo (int)$g['id']; ?>" hidden>
        <h3><?php echo html_escape($g['title']); ?></h3>
        <div class="gallery-desc-body"><?php echo $g['description']; ?></div>
      </div>
      <?php endforeach; ?>

      <div class="gal-grid" id="galleryGrid">
        <?php foreach($galleries_with_photos as $g): foreach($g['photos'] as $photo):
          $thumb_url = $this->media_storage->getImageURL($photo->thumb_path . $photo->img_name);
          $full_url  = $this->media_storage->getImageURL($photo->dir_path . $photo->img_name);
          $is_video  = (isset($photo->file_type) && $photo->file_type === 'video');
        ?>
        <button type="button"
                class="gal-item gallery-tile bg-cover-center<?php echo $is_video ? ' gal-vid' : ''; ?>"
                data-gallery-id="<?php echo (int)$g['id']; ?>"
                data-full="<?php echo html_escape($full_url); ?>"
                data-caption="<?php echo html_escape($g['title']); ?>"
                aria-label="<?php echo html_escape($g['title']); ?>"
                style="background-image:url('<?php echo html_escape($thumb_url); ?>')">
          <span class="gal-cap"><strong><?php echo html_escape($g['title']); ?></strong></span>
        </button>
        <?php endforeach; endforeach; ?>
      </div>

      <?php if($total_photos === 0): ?>
      <p class="empty-generic"><?php echo $this->lang->line('no_record_found'); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <div id="galleryLightbox" class="gal-lightbox" hidden role="dialog" aria-modal="true" aria-label="Image viewer">
    <button type="button" class="gal-lb-close" aria-label="Close">&times;</button>
    <button type="button" class="gal-lb-prev" aria-label="Previous">&#8249;</button>
    <button type="button" class="gal-lb-next" aria-label="Next">&#8250;</button>
    <figure class="gal-lb-figure">
      <img id="galLbImg" src="" alt="" />
      <figcaption id="galLbCap"></figcaption>
    </figure>
  </div>

  <!-- Gallery + lightbox styles moved to backend/themes/atrium/front/css/pages.css (2026-05-26) -->

  <script>
  (function(){
    var section = document.querySelector('.gallery-filter-section');
    var grid = document.getElementById('galleryGrid');
    if (!section || !grid) return;
    var chips = section.querySelectorAll('.chip');
    var descs = document.querySelectorAll('.gallery-desc');
    var tiles = grid.querySelectorAll('.gallery-tile');

    function applyFilter(value){
      chips.forEach(function(c){ c.classList.toggle('on', c.getAttribute('data-filter') === value); });
      descs.forEach(function(d){ d.hidden = (value === 'all') ? true : (d.getAttribute('data-desc-for') !== value); });
      tiles.forEach(function(t){
        var keep = (value === 'all') || (t.getAttribute('data-gallery-id') === value);
        t.classList.toggle('is-hidden', !keep);
      });
    }
    chips.forEach(function(c){
      c.addEventListener('click', function(){ applyFilter(c.getAttribute('data-filter')); });
    });

    var lb = document.getElementById('galleryLightbox');
    var lbImg = document.getElementById('galLbImg');
    var lbCap = document.getElementById('galLbCap');
    var visibleTiles = [];
    var lbIdx = 0;

    function getVisible(){ return Array.prototype.filter.call(tiles, function(t){ return !t.classList.contains('is-hidden'); }); }
    function show(){
      var t = visibleTiles[lbIdx]; if (!t) return;
      lbImg.src = t.getAttribute('data-full') || '';
      lbImg.alt = t.getAttribute('aria-label') || '';
      lbCap.textContent = t.getAttribute('data-caption') || '';
    }
    function open(idx){
      visibleTiles = getVisible();
      if (!visibleTiles.length) return;
      lbIdx = idx; show();
      lb.hidden = false;
      document.body.style.overflow = 'hidden';
    }
    function close(){
      lb.hidden = true; lbImg.src = '';
      document.body.style.overflow = '';
    }
    function nav(d){
      if (!visibleTiles.length) return;
      lbIdx = (lbIdx + d + visibleTiles.length) % visibleTiles.length;
      show();
    }
    tiles.forEach(function(t){
      t.addEventListener('click', function(){
        var visible = getVisible();
        var idx = visible.indexOf(t);
        if (idx >= 0) open(idx);
      });
    });
    lb.querySelector('.gal-lb-close').addEventListener('click', close);
    lb.querySelector('.gal-lb-prev').addEventListener('click', function(){ nav(-1); });
    lb.querySelector('.gal-lb-next').addEventListener('click', function(){ nav(1); });
    lb.addEventListener('click', function(e){ if (e.target === lb) close(); });
    document.addEventListener('keydown', function(e){
      if (lb.hidden) return;
      if (e.key === 'Escape') close();
      else if (e.key === 'ArrowLeft') nav(-1);
      else if (e.key === 'ArrowRight') nav(1);
    });
  })();
  </script>

  <?php endif; ?>
<?php endforeach; endif; ?>
</div>

<?php if(!empty($page['category_content']) && !in_array(($page_content_type ?? ''), array('gallery','events'), true)): ?>
<?php echo $this->ajax_pagination->create_links(); ?>
<script>
function searchFilter(page_num) {
    page_num = page_num ? page_num : 0;
    var pct = document.getElementById('page_content_type');
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>frontend/welcome/ajaxPaginationData/' + page_num,
        data: 'page=' + page_num + '&page_content_type=' + (pct ? pct.value : ''),
        success: function(html) { document.getElementById('postList').innerHTML = html; }
    });
}
</script>
<?php endif; ?>
<?php endif; ?>

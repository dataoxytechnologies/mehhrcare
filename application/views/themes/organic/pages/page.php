<?php
$layout_type   = $page['layout_type']   ?? 'blank';
$page_sections = json_decode($page['page_section_data'] ?? '{}', true) ?: array();
$fimg          = $page['feature_image'] ?? '';
$page_title    = html_escape($page['title'] ?? '');
?>

<!-- Page hero -->
<section class="page-hero compact">
  <span class="ph-blob" aria-hidden="true"></span>
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="sep">·</span>
      <span class="here"><?php echo $page_title; ?></span>
    </nav>
    <h1><?php echo $page_title; ?></h1>
  </div>
</section>

<?php if($layout_type === 'contact'): ?>
<!-- Contact layout -->
<section class="contact-form-section">
  <div class="container">
    <div class="contact-form-grid">

      <!-- Info column -->
      <div class="contact-info">
        <?php if(!empty($page_sections['address'])): ?>
        <div class="contact-info-card">
          <h5><?php echo $this->lang->line('address'); ?></h5>
          <p><?php echo nl2br(html_escape($page_sections['address'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['phone'])): ?>
        <div class="contact-info-card">
          <h5><?php echo $this->lang->line('phone'); ?></h5>
          <p><a href="tel:<?php echo html_escape(preg_replace('/[^0-9+]/','',$page_sections['phone'])); ?>"><?php echo html_escape($page_sections['phone']); ?></a></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['email'])): ?>
        <div class="contact-info-card">
          <h5><?php echo $this->lang->line('email'); ?></h5>
          <p><a href="mailto:<?php echo html_escape($page_sections['email']); ?>"><?php echo html_escape($page_sections['email']); ?></a></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($page_sections['hours'])): ?>
        <div class="contact-info-card">
          <h5><?php echo $this->lang->line('hours'); ?></h5>
          <p><?php echo nl2br(html_escape($page_sections['hours'])); ?></p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Map column -->
      <?php if(!empty($page_sections['map_embed'])): ?>
      <div class="contact-form contact-form-flush">
        <div class="map-embed-wrap"><?php echo $page_sections['map_embed']; ?></div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php elseif($layout_type === 'faq'): ?>
<!-- FAQ layout -->
<section class="faqs-list-section">
  <div class="container">
    <?php if(!empty($page_sections['faq']) && is_array($page_sections['faq'])): ?>
    <div class="faq-group">
      <p class="faq-group-head"><?php echo $this->lang->line('frequently_asked_questions') ?: 'Frequently Asked Questions'; ?></p>
      <?php foreach($page_sections['faq'] as $i => $item): if(empty($item['question'])) continue; ?>
      <details class="faq-details" <?php if($i===0) echo 'open'; ?>>
        <summary class="faq-summary">
          <?php echo html_escape($item['question']); ?>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" class="flex-none"><path d="M6 9l6 6 6-6"/></svg>
        </summary>
        <div class="faq-content">
          <?php echo html_escape($item['answer'] ?? ''); ?>
        </div>
      </details>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-muted"><?php echo $this->lang->line('no_record_found'); ?></p>
    <?php endif; ?>
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
$cover_classes = array('doc-1','doc-2','doc-3','doc-4','doc-5','doc-6');
?>
<section class="about-team">
  <div class="container">
    <?php if(!empty($page_sections['subheading'])): ?>
    <p class="page-subheading"><?php echo html_escape($page_sections['subheading']); ?></p>
    <?php endif; ?>
    <div class="doc-grid">
      <?php foreach($team_members as $i => $m):
        $photo = !empty($m['image']) ? base_url('uploads/staff/' . $m['image']) : '';
        $name  = html_escape(trim($m['name'] . ' ' . $m['surname']));
        $cc    = $cover_classes[$i % count($cover_classes)];
      ?>
      <div class="doc">
        <div class="doc-photo <?php echo $cc; ?>">
          <?php if($photo): ?>
          <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" onerror="this.style.display='none'"/>
          <?php endif; ?>
        </div>
        <h4><?php echo $name; ?></h4>
        <p class="doc-spec"><?php echo html_escape($m['department_name'] ?? ''); ?></p>
        <?php if(!empty($m['qualification'])): ?>
        <p class="doc-bio"><?php echo html_escape($m['qualification']); ?></p>
        <?php endif; ?>
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
    <section class="section-hero-tall">
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
    $desc_plain = trim(strip_tags($page['description'] ?? ''));
    $title_norm = trim(strip_tags($page['title']       ?? ''));
    // Skip the prose block when there's no meaningful description (or it's just the page title)
    $has_meaningful_desc = $desc_plain !== '' && strcasecmp($desc_plain, $title_norm) !== 0 && mb_strlen($desc_plain) > 12;
    if ($fimg || $has_meaningful_desc) {
        echo '<section class="' . ($has_meaningful_desc ? 'section-hero-tall' : 'section-hero-short') . '"><div class="container">';
        if ($fimg) { echo '<img src="' . html_escape($fimg) . '" alt="" class="hero-img-centered" onerror="this.style.display:\'none\'"/>'; }
        if ($has_meaningful_desc) { echo '<div class="prose">' . $page['description'] . '</div>'; }
        echo '</div></section>';
    }
}
?>

<input type="hidden" name="page_content_type" id="page_content_type" value="<?php echo html_escape($page_content_type ?? ''); ?>">

<div id="postList">
<?php if(!empty($page['category_content'])): foreach($page['category_content'] as $pck => $pcv): ?>

  <?php if($pck === 'events'):
    // Bypass paginated $pcv so the featured card and full grid see every event.
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
  ?>
  <section class="events-section section-pt-32">
    <div class="container">
      <?php if($total_events === 0): ?>
      <p class="empty-events"><?php echo $this->lang->line('no_record_found') ?: 'No upcoming events.'; ?></p>
      <?php else: ?>

      <?php if($featured):
          $ff      = $featured['feature_image'] ?: $this->media_storage->getImageURL('uploads/gallery/gallery_default.png');
          $f_start = !empty($featured['event_start']) ? strtotime($featured['event_start']) : null;
          $f_when  = $f_start ? date('D, d M · H:i', $f_start) : '';
      ?>
      <div class="head-row split head-row-mb24">
        <div>
          <span class="kicker"><?php echo html_escape($this->lang->line('upcoming_event') ?: 'Up next'); ?></span>
          <h2><?php echo html_escape($this->lang->line('upcoming_events') ?: 'Upcoming events'); ?> <em><?php echo html_escape(strtolower($this->lang->line('programs') ?: '& programs')); ?></em>.</h2>
        </div>
      </div>

      <article class="event-featured event-featured-mb48">
        <div class="ef-cover cover-tall-360">
          <img src="<?php echo html_escape($ff); ?>" alt="<?php echo html_escape($featured['title']); ?>" class="cover-img-contain" onerror="this.style.display='none'"/>
          <span class="ef-tag"><?php echo html_escape($this->lang->line('featured') ?: 'Featured'); ?><?php echo $f_start ? ' · ' . html_escape(date('d M Y', $f_start)) : ''; ?></span>
        </div>
        <div class="ef-body">
          <h3><?php echo html_escape($featured['title']); ?></h3>
          <p><?php echo html_escape(substr(strip_tags($featured['description']), 0, 240)); ?></p>
          <div class="ef-meta">
            <?php if(!empty($featured['event_venue'])): ?><span><?php echo html_escape($featured['event_venue']); ?></span><?php endif; ?>
            <?php if($f_when !== ''): ?><span><?php echo html_escape($f_when); ?></span><?php endif; ?>
            <?php if(!empty($featured['category'])): ?><span><?php echo html_escape($featured['category']); ?></span><?php endif; ?>
          </div>
          <div class="ef-actions">
            <a href="<?php echo site_url($featured['url']); ?>" class="pill pill-primary"><?php echo html_escape($this->lang->line('view_details') ?: 'View details'); ?></a>
          </div>
        </div>
      </article>
      <?php endif; ?>

      <?php if(!empty($rest_events)): ?>
      <h3 class="section-h3-style"><?php echo html_escape($this->lang->line('more_upcoming') ?: 'More upcoming'); ?></h3>
      <div class="event-grid">
        <?php foreach($rest_events as $i => $ev):
          $ef = $ev['feature_image'] ?: $this->media_storage->getImageURL('uploads/gallery/gallery_default.png');
          $cover_classes_ev = array('cover-1','cover-2','cover-3','cover-4','cover-5','cover-6');
          $cc = $cover_classes_ev[$i % 6];
          $ev_start = !empty($ev['event_start']) ? strtotime($ev['event_start']) : null;
        ?>
        <a href="<?php echo site_url($ev['url']); ?>" class="event-card no-link-deco <?php echo $cc; ?>">
          <div class="ec-cover">
            <img src="<?php echo html_escape($ef); ?>" alt="<?php echo html_escape($ev['title']); ?>" onerror="this.style.display='none'"/>
            <?php if(!empty($ev['category'])): ?><span class="ec-tag"><?php echo html_escape($ev['category']); ?></span><?php endif; ?>
          </div>
          <div class="ec-body">
            <?php if($ev_start): ?><span class="ec-date"><?php echo strtoupper(date('d M Y · H:i', $ev_start)); ?></span><?php endif; ?>
            <h4><?php echo html_escape($ev['title']); ?></h4>
            <p><?php echo html_escape(substr(strip_tags($ev['description']), 0, 100)); ?></p>
            <div class="ec-foot">
              <?php if(!empty($ev['event_venue'])): ?><span class="ec-loc"><?php echo html_escape($ev['event_venue']); ?></span><?php endif; ?>
              <span class="ec-link"><?php echo $this->lang->line('view_details'); ?> →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>

  <?php elseif($pck === 'notice'): ?>
  <section class="cta-banner-section">
    <div class="container">
      <div class="events-past-list">
        <?php foreach((is_array($pcv) ? $pcv : array()) as $n): ?>
        <div class="events-past-row">
          <span class="ep-date"><?php echo html_escape($n['publish_date'] ?? ''); ?></span>
          <div>
            <h5><?php echo html_escape($n['title']); ?></h5>
            <p class="ep-meta"><?php echo html_escape(substr(strip_tags($n['description']), 0, 120)); ?></p>
          </div>
          <a href="<?php echo site_url($n['url']); ?>"><?php echo $this->lang->line('view_details'); ?> →</a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php elseif($pck === 'gallery'):
    // Load every gallery (bypass paginated $pcv) so chips cover all of them.
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
  <section class="gallery-section">
    <div class="container">
      <div data-filter-root>
        <div class="filter-chips" role="tablist" aria-label="Gallery filter">
          <button type="button" class="chip is-active" data-filter-chip="all"><?php echo $this->lang->line('all') ?: 'All'; ?><span class="count"><?php echo (int)$total_photos; ?></span></button>
          <?php foreach($galleries_with_photos as $g): ?>
          <button type="button" class="chip" data-filter-chip="g<?php echo (int)$g['id']; ?>"><?php echo html_escape($g['title']); ?><span class="count"><?php echo (int)$g['count']; ?></span></button>
          <?php endforeach; ?>
        </div>

        <?php foreach($galleries_with_photos as $g): if(empty(trim(strip_tags($g['description'])))) continue; ?>
        <div class="gallery-desc" data-desc-for="g<?php echo (int)$g['id']; ?>" hidden>
          <h3><?php echo html_escape($g['title']); ?></h3>
          <div class="gallery-desc-body"><?php echo $g['description']; ?></div>
        </div>
        <?php endforeach; ?>

        <div class="gal-grid sh-gal-uniform" id="galleryGrid">
          <?php
          foreach($galleries_with_photos as $g):
            foreach($g['photos'] as $photo):
              $thumb_url = $this->media_storage->getImageURL($photo->thumb_path . $photo->img_name);
              $full_url  = $this->media_storage->getImageURL($photo->dir_path . $photo->img_name);
          ?>
          <button type="button"
                  class="gal-item gallery-tile"
                  data-filter-item="g<?php echo (int)$g['id']; ?>"
                  data-full="<?php echo html_escape($full_url); ?>"
                  data-caption="<?php echo html_escape($g['title']); ?>"
                  aria-label="<?php echo html_escape($g['title']); ?>">
            <div class="gi-square">
              <img src="<?php echo html_escape($thumb_url); ?>" alt="<?php echo html_escape($g['title']); ?>" onerror="this.style.display='none'"/>
            </div>
            <span class="gi-cap"><?php echo html_escape($g['title']); ?></span>
          </button>
          <?php
            endforeach;
          endforeach;
          ?>
        </div>

        <?php if($total_photos === 0): ?>
        <p class="empty-generic"><?php echo $this->lang->line('no_record_found'); ?></p>
        <?php endif; ?>
      </div>
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

  <!-- Gallery + lightbox styles moved to backend/themes/organic/front/css/pages.css (2026-05-26) -->

  <script>
  (function(){
    var root = document.querySelector('[data-filter-root]');
    if (!root) return;
    var chips = root.querySelectorAll('[data-filter-chip]');
    var descs = root.querySelectorAll('.gallery-desc');
    var grid  = document.getElementById('galleryGrid');
    var tiles = grid ? grid.querySelectorAll('[data-filter-item]') : [];

    // Chip → toggle gallery description panel (tile hide/show is handled by shell.js wireFilters)
    function syncDescs(value){
      Array.prototype.forEach.call(descs, function(d){
        d.hidden = (value === 'all') ? true : (d.getAttribute('data-desc-for') !== value);
      });
    }
    Array.prototype.forEach.call(chips, function(c){
      c.addEventListener('click', function(){ syncDescs(c.getAttribute('data-filter-chip')); });
    });

    // Lightbox
    var lb = document.getElementById('galleryLightbox');
    if (!lb) return;
    var lbImg = document.getElementById('galLbImg');
    var lbCap = document.getElementById('galLbCap');
    var visibleTiles = [];
    var lbIdx = 0;
    function getVisible(){
      return Array.prototype.filter.call(tiles, function(t){ return t.style.display !== 'none'; });
    }
    function show(){
      var t = visibleTiles[lbIdx]; if (!t) return;
      lbImg.src = t.getAttribute('data-full') || '';
      lbImg.alt = t.getAttribute('aria-label') || '';
      lbCap.textContent = t.getAttribute('data-caption') || '';
    }
    function openAt(idx){
      visibleTiles = getVisible();
      if (!visibleTiles.length) return;
      lbIdx = idx; show();
      lb.hidden = false;
      document.body.style.overflow = 'hidden';
    }
    function closeLb(){
      lb.hidden = true; lbImg.src = '';
      document.body.style.overflow = '';
    }
    function nav(d){
      if (!visibleTiles.length) return;
      lbIdx = (lbIdx + d + visibleTiles.length) % visibleTiles.length;
      show();
    }
    Array.prototype.forEach.call(tiles, function(t){
      t.addEventListener('click', function(){
        var visible = getVisible();
        var idx = visible.indexOf(t);
        if (idx >= 0) openAt(idx);
      });
    });
    lb.querySelector('.gal-lb-close').addEventListener('click', closeLb);
    lb.querySelector('.gal-lb-prev').addEventListener('click', function(){ nav(-1); });
    lb.querySelector('.gal-lb-next').addEventListener('click', function(){ nav(1); });
    lb.addEventListener('click', function(e){ if (e.target === lb) closeLb(); });
    document.addEventListener('keydown', function(e){
      if (lb.hidden) return;
      if (e.key === 'Escape') closeLb();
      else if (e.key === 'ArrowLeft') nav(-1);
      else if (e.key === 'ArrowRight') nav(1);
    });
  })();
  </script>

  <?php endif; ?>
<?php endforeach; endif; ?>
</div>

<?php if(!empty($page['category_content']) && !in_array(($page_content_type ?? ''), array('gallery','events'), true)): ?>
<div class="pagination-wrap"><div class="container"><?php echo $this->ajax_pagination->create_links(); ?></div></div>
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

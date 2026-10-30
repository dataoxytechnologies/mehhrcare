<?php
$fimg      = $page['feature_image'] ?? '';
$pub_date  = !empty($page['publish_date']) ? date($this->customlib->YYYYMMDDTodateFormat($page['publish_date'])) : '';
$ptype     = strtolower(trim((string)($page['type'] ?? '')));
$ev_start  = !empty($page['event_start']) ? strtotime($page['event_start']) : null;
$ev_end    = !empty($page['event_end'])   ? strtotime($page['event_end'])   : null;
$ev_venue  = trim((string)($page['event_venue'] ?? ''));
$is_event  = ($ptype === 'events') || $ev_start || $ev_venue !== '';
$photos    = !empty($page['page_contents']) && is_array($page['page_contents']) ? $page['page_contents'] : array();
$is_gallery = ($ptype === 'gallery') || !empty($photos);
$ev_when   = '';
if ($ev_start) {
    $ev_when = date('D, d M Y', $ev_start);
    if ($ev_end && date('Ymd', $ev_end) !== date('Ymd', $ev_start)) {
        $ev_when .= ' – ' . date('d M Y', $ev_end);
    }
}
// When the meta strip (or publish-date strip) is shown, the cover's -72px overlap
// would clip the meta — give the hero extra bottom padding to clear it.
$_has_meta_strip = ($is_event && ($ev_when !== '' || $ev_venue !== '')) || (!$is_event && $pub_date);
$_hero_pad_class = ($_has_meta_strip && $fimg) ? ' hero-pad-bottom' : '';
?>
<section class="art-hero compact<?php echo $_hero_pad_class; ?>">
  <div class="container">
    <div class="art-hero-row">
      <div>
        <nav class="crumbs" aria-label="Breadcrumb">
          <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
          <span class="csep">·</span>
          <?php if ($is_event): ?>
          <a href="<?php echo site_url('page/events'); ?>"><?php echo html_escape($this->lang->line('events') ?: 'Events'); ?></a>
          <span class="csep">·</span>
          <?php elseif ($is_gallery): ?>
          <a href="<?php echo site_url('page/gallery'); ?>"><?php echo html_escape($this->lang->line('gallery') ?: 'Gallery'); ?></a>
          <span class="csep">·</span>
          <?php endif; ?>
          <strong><?php echo html_escape($page['title'] ?? ''); ?></strong>
        </nav>
        <?php if ($is_event): ?>
        <span class="kicker"><?php echo html_escape($this->lang->line('upcoming_event') ?: 'Event'); ?></span>
        <?php endif; ?>
        <h1><?php echo html_escape($page['title'] ?? ''); ?></h1>
        <?php if ($is_event && ($ev_when !== '' || $ev_venue !== '')): ?>
        <div class="art-byline art-byline-row">
          <?php if ($ev_when !== ''): ?>
          <span class="meta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <?php echo html_escape($ev_when); ?>
          </span>
          <?php endif; ?>
          <?php if ($ev_venue !== ''): ?>
          <span class="meta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <?php echo html_escape($ev_venue); ?>
          </span>
          <?php endif; ?>
          <?php if ($pub_date && !$is_event): ?>
          <span class="meta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <?php echo $pub_date; ?>
          </span>
          <?php endif; ?>
        </div>
        <?php elseif ($pub_date): ?>
        <div class="art-byline">
          <span class="meta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <?php echo $pub_date; ?>
          </span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php if($fimg): ?>
<div class="container">
  <div class="art-cover art-cover-bg">
    <img src="<?php echo html_escape($fimg); ?>" alt="<?php echo html_escape($page['title'] ?? ''); ?>" class="cover-img-contain" onerror="this.style.display='none'"/>
  </div>
</div>
<?php endif; ?>

<section class="art-section">
  <div class="container art-narrow">
    <div class="art-body">
      <?php echo $page['description'] ?? ''; ?>
    </div>
  </div>
</section>

<?php if ($is_gallery && !empty($photos)): ?>
<section class="block tight section-pt-0">
  <div class="container">
    <header class="sec-head sec-head-mb28">
      <span class="kicker"><?php echo html_escape($this->lang->line('gallery') ?: 'Gallery'); ?></span>
      <h2><?php echo (int)count($photos); ?> <span class="grad"><?php echo html_escape(strtolower($this->lang->line('photos') ?: 'photos')); ?></span></h2>
    </header>
    <div class="gal-grid" id="readGalleryGrid">
      <?php foreach ($photos as $ph):
        $thumb = $this->media_storage->getImageURL($ph->thumb_path ?? $ph->dir_path ?? '');
        $full  = $this->media_storage->getImageURL($ph->dir_path ?? '');
        if (!$thumb && !$full) { continue; }
        $img = $thumb ?: $full;
      ?>
      <button type="button" class="gal-item gallery-tile bg-cover-center"
              data-full="<?php echo html_escape($full ?: $img); ?>"
              data-caption="<?php echo html_escape($page['title'] ?? ''); ?>"
              style="background-image:url('<?php echo html_escape($img); ?>')">
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

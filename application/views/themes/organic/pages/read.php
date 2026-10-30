<?php
$fimg     = $page['feature_image'] ?? '';
$pub_date = !empty($page['publish_date']) ? date($this->customlib->YYYYMMDDTodateFormat($page['publish_date'])) : '';
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
?>
<div class="post-header">
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="sep">·</span>
      <?php if ($is_event): ?>
      <a href="<?php echo site_url('page/events'); ?>"><?php echo html_escape($this->lang->line('events') ?: 'Events'); ?></a>
      <span class="sep">·</span>
      <?php elseif ($is_gallery): ?>
      <a href="<?php echo site_url('page/gallery'); ?>"><?php echo html_escape($this->lang->line('gallery') ?: 'Gallery'); ?></a>
      <span class="sep">·</span>
      <?php endif; ?>
      <span class="here"><?php echo html_escape($page['title'] ?? ''); ?></span>
    </nav>
    <?php if ($is_event && ($ev_when !== '' || $ev_venue !== '')): ?>
    <div class="meta-bar meta-bar-row">
      <?php if ($ev_when !== ''): ?>
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        <?php echo html_escape($ev_when); ?>
      </span>
      <?php endif; ?>
      <?php if ($ev_venue !== ''): ?>
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        <?php echo html_escape($ev_venue); ?>
      </span>
      <?php endif; ?>
    </div>
    <?php elseif ($pub_date): ?>
    <div class="meta-bar meta-bar-simple">
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        <?php echo $pub_date; ?>
      </span>
    </div>
    <?php endif; ?>
    <h1><?php echo html_escape($page['title'] ?? ''); ?></h1>
  </div>
</div>

<?php if($fimg): ?>
<div class="post-cover">
  <div class="container">
    <img src="<?php echo html_escape($fimg); ?>" alt="<?php echo html_escape($page['title'] ?? ''); ?>" class="cover-img-contain" onerror="this.style.display='none'"/>
  </div>
</div>
<?php endif; ?>

<div class="post-body<?php echo empty($fimg) ? ' no-cover' : ''; ?>">
  <div class="container">
    <div class="prose post-narrow">
      <?php echo $page['description'] ?? ''; ?>
    </div>
  </div>
</div>

<?php if ($is_gallery && !empty($photos)): ?>
<section class="events-section section-pt-0">
  <div class="container">
    <div class="head-row head-row-mb28">
      <span class="kicker"><?php echo html_escape($this->lang->line('gallery') ?: 'Gallery'); ?></span>
      <h2 class="photo-count-h2">
        <?php echo (int)count($photos); ?> <em class="em-accent"><?php echo html_escape(strtolower($this->lang->line('photos') ?: 'photos')); ?></em>
      </h2>
    </div>
    <div class="gal-grid sh-gal-uniform">
      <?php foreach ($photos as $ph):
        $thumb = $this->media_storage->getImageURL($ph->thumb_path ?? $ph->dir_path ?? '');
        $full  = $this->media_storage->getImageURL($ph->dir_path ?? '');
        if (!$thumb && !$full) { continue; }
        $img = $thumb ?: $full;
      ?>
      <button type="button" class="gal-item gi-square bg-cover-center gallery-tile-btn"
              data-full="<?php echo html_escape($full ?: $img); ?>"
              style="background-image:url('<?php echo html_escape($img); ?>')">
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

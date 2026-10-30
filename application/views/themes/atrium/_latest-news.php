<?php
$notices = isset($banner_notices) ? $banner_notices : array();

// Respect Sidebar Options → "news" checkbox in admin/frontcms
$sb_enabled = isset($front_setting->sidebar_options)
    ? (array) json_decode($front_setting->sidebar_options)
    : array();
if (!in_array('news', $sb_enabled, true)) return;

if (empty($notices)) return;
?>
<div class="hero-news">
  <div class="hero-news-head">
    <h3><?php echo $this->lang->line('latest_news'); ?></h3>
    <a href="<?php echo site_url('cms_program'); ?>"><?php echo $this->lang->line('view_all'); ?>
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </a>
  </div>
  <ul class="hero-news-list">
    <?php foreach ($notices as $n):
      $d = !empty($n['created_at']) ? $n['created_at']
         : (!empty($n['date']) ? $n['date'] : null);
      $day_num   = $d ? date('d', strtotime($d)) : '';
      $month_key = $d ? strtolower(date('F', strtotime($d))) : '';
      $month_str = $month_key ? $this->lang->line($month_key) : '';
      $url = site_url('read/' . (isset($n['slug']) ? $n['slug'] : ''));
    ?>
    <li>
      <a href="<?php echo $url; ?>" class="hero-news-item">
        <?php if ($d): ?>
        <div class="hero-news-date"><?php echo html_escape($day_num); ?><small><?php echo html_escape($month_str); ?></small></div>
        <?php endif; ?>
        <div class="hero-news-body">
          <div class="hero-news-title"><?php echo html_escape(isset($n['title']) ? $n['title'] : ''); ?></div>
          <div class="hero-news-meta"><?php echo $this->lang->line('read_more'); ?>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </div>
        </div>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</div>

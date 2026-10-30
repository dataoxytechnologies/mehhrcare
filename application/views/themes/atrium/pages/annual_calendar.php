<?php
$sp      = $static_pages['annual_calendar'] ?? array();
$heading = html_escape($sp['heading'] ?? $this->lang->line('annual_calendar'));

// Merge all events sorted by date
$all_events = array();
if(!empty($getholidays)) {
    foreach($getholidays as $v) {
        $all_events[] = array(
            'date'  => $v['from_date'],
            'date2' => null,
            'label' => $v['description'],
            'type'  => 'holiday',
        );
    }
}
if(!empty($getactivity)) {
    foreach($getactivity as $v) {
        $all_events[] = array(
            'date'  => $v['from_date'],
            'date2' => null,
            'label' => $v['description'],
            'type'  => 'activity',
        );
    }
}
if(!empty($getvacation)) {
    foreach($getvacation as $v) {
        $all_events[] = array(
            'date'  => $v['from_date'],
            'date2' => $v['to_date'],
            'label' => $v['description'],
            'type'  => 'vacation',
        );
    }
}
// Filter out invalid / epoch-zero dates (junk DB rows)
$min_year = (int)date('Y') - 1;
$all_events = array_filter($all_events, function($e) use ($min_year) {
    if (empty($e['date'])) return false;
    $ts = strtotime($e['date']);
    return $ts && (int)date('Y', $ts) >= $min_year;
});
usort($all_events, function($a, $b) { return strcmp($a['date'], $b['date']); });

// Group by month (Y-m key, so events stay year-aware)
$by_month = array();
foreach($all_events as $ev) {
    $mk = date('Y-m', strtotime($ev['date']));
    $by_month[$mk][] = $ev;
}

// Headline year (most-recent year that has events; falls back to current year)
$headline_year = date('Y');
if(!empty($by_month)) {
    $last_key = array_key_last($by_month);
    $headline_year = (int)substr($last_key, 0, 4);
}

$type_labels = array(
    'holiday'  => $this->lang->line('holiday'),
    'activity' => $this->lang->line('activity'),
    'vacation' => $this->lang->line('vacation'),
);
$type_color = array(
    'holiday'  => 'var(--accent)',
    'activity' => 'var(--gold)',
    'vacation' => 'var(--muted)',
);

$months_short = array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
?>

<!-- Calendar styles moved to backend/themes/atrium/front/css/pages.css (2026-05-26) -->

<!-- Page hero -->
<section class="page-hero compact">
  <div class="container">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="<?php echo site_url('frontend/welcome/index'); ?>"><?php echo $this->lang->line('home') ?: 'Home'; ?></a>
      <span class="csep">/</span>
      <strong><?php echo $heading; ?></strong>
    </nav>
    <h1><?php echo $heading; ?></h1>
    <p class="lede cal-legend">
      <?php if(!empty($getholidays)): ?>
      <span class="cal-legend-item">
        <span class="cal-legend-dot cal-dot-accent"></span>
        <?php echo count($getholidays); ?> <?php echo $this->lang->line('holiday'); ?>
      </span>
      <?php endif; ?>
      <?php if(!empty($getactivity)): ?>
      <span class="cal-legend-item">
        <span class="cal-legend-dot cal-dot-gold"></span>
        <?php echo count($getactivity); ?> <?php echo $this->lang->line('activity'); ?>
      </span>
      <?php endif; ?>
      <?php if(!empty($getvacation)): ?>
      <span class="cal-legend-item">
        <span class="cal-legend-dot cal-dot-muted"></span>
        <?php echo count($getvacation); ?> <?php echo $this->lang->line('vacation'); ?>
      </span>
      <?php endif; ?>
    </p>
  </div>
</section>

<section class="cal-section" id="annual-calendar">
  <div class="container">

    <div class="head-row split">
      <div>
        <span class="kicker"><?php echo $headline_year; ?></span>
        <h2><?php echo $heading; ?></h2>
      </div>
    </div>

    <div class="cal-months" role="tablist" id="cal-month-filter">
      <button type="button" class="cal-month-btn is-active" data-month="all" role="tab" aria-selected="true"><?php echo $this->lang->line('all') ?: 'All'; ?></button>
      <?php foreach($months_short as $mn => $mkey):
        $mlabel = $this->lang->line($mkey);
        if(!$mlabel) $mlabel = date('M', mktime(0,0,0,$mn,1));
      ?>
      <button type="button" class="cal-month-btn" data-month="<?php echo $mn; ?>" role="tab" aria-selected="false"><?php echo $mlabel; ?></button>
      <?php endforeach; ?>
    </div>

    <div id="cal-month-blocks">
    <?php if(empty($by_month)): ?>
    <p class="cal-empty cal-empty-muted"><?php echo $this->lang->line('no_record_found'); ?></p>

    <?php else: foreach($by_month as $month_key => $events):
      $month_ts   = strtotime($month_key . '-01');
      $month_num  = (int)date('n', $month_ts);
      $month_name = date('F', $month_ts);
      $year       = date('Y', $month_ts);
      $count      = count($events);
    ?>
    <div class="cal-month-block" data-month="<?php echo $month_num; ?>">
      <h3 class="cal-month-title">
        <?php echo $this->lang->line(strtolower($month_name)) ?: $month_name; ?> <?php echo $year; ?>
        <em>· <?php echo $count; ?> <?php echo $count == 1 ? ($this->lang->line('event') ?: 'event') : ($this->lang->line('events') ?: 'events'); ?></em>
      </h3>
      <div class="cal-events-list">
        <?php foreach($events as $ev):
          $ts = strtotime($ev['date']);
          $tc = $type_color[$ev['type']] ?? 'var(--accent)';
          $tl = $type_labels[$ev['type']] ?? $ev['type'];
          $day_lc = strtolower(date('D', $ts));
          $mon_lc = strtolower(date('M', $ts));
        ?>
        <article class="cal-event">
          <div class="cal-date">
            <span class="day"><?php echo $this->lang->line($day_lc) ?: date('D', $ts); ?></span>
            <span class="num"><?php echo date('d', $ts); ?></span>
            <span class="mon"><?php echo $this->lang->line($mon_lc) ?: date('M', $ts); ?></span>
          </div>
          <div class="cal-event-body">
            <h4><?php echo html_escape($ev['label']); ?></h4>
            <?php if($ev['date2']): ?>
            <p>
              <?php echo date($this->customlib->YYYYMMDDTodateFormat($ev['date'])); ?>
              &ndash;
              <?php echo date($this->customlib->YYYYMMDDTodateFormat($ev['date2'])); ?>
            </p>
            <?php endif; ?>
            <div class="cal-meta">
              <span style="color:<?php echo $tc; ?>;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="3"/></svg>
                <?php echo html_escape($tl); ?>
              </span>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; endif; ?>
    </div>

    <p class="cal-no-results cal-no-results-styled d-none" ><?php echo $this->lang->line('no_record_found'); ?></p>

  </div>
</section>

<script>
(function(){
  var filter = document.getElementById('cal-month-filter');
  if(!filter) return;
  var blocks = document.querySelectorAll('#cal-month-blocks .cal-month-block');
  var noResults = document.querySelector('#annual-calendar .cal-no-results');

  filter.addEventListener('click', function(e){
    var btn = e.target.closest('.cal-month-btn');
    if(!btn) return;
    var month = btn.getAttribute('data-month');

    filter.querySelectorAll('.cal-month-btn').forEach(function(b){
      var on = b === btn;
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });

    var visible = 0;
    blocks.forEach(function(blk){
      var show = (month === 'all') || (blk.getAttribute('data-month') === month);
      blk.style.display = show ? '' : 'none';
      if(show) visible++;
    });
    if(noResults) noResults.style.display = (blocks.length && visible === 0) ? '' : 'none';
  });
})();
</script>

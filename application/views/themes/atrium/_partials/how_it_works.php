<?php
$how_sec = isset($home_sections['how_it_works']) ? $home_sections['how_it_works'] : array();

// Build step list — prefer new items[] format, fall back to legacy step_N_title/desc
$steps = array();
if (!empty($how_sec['items']) && is_array($how_sec['items'])) {
    foreach ($how_sec['items'] as $it) {
        $t = isset($it['title']) ? $it['title'] : '';
        $d = isset($it['desc'])  ? $it['desc']  : '';
        if ($t !== '' || $d !== '') {
            $steps[] = array('title' => $t, 'desc' => $d);
        }
    }
}
if (empty($steps)) {
    for ($i = 1; $i <= 9; $i++) {
        $t = isset($how_sec['step_'.$i.'_title']) ? $how_sec['step_'.$i.'_title'] : '';
        $d = isset($how_sec['step_'.$i.'_desc'])  ? $how_sec['step_'.$i.'_desc']  : '';
        if ($t !== '' || $d !== '') {
            $steps[] = array('title' => $t, 'desc' => $d);
        }
    }
}
?>
<?php if(($how_sec['show'] ?? false) && !empty($steps)): ?>
<section class="how">
  <div class="container">
    <header class="sec-head center">
      <?php if(!empty($how_sec['kicker'])): ?><span class="kicker"><?php echo html_escape($how_sec['kicker']); ?></span><?php endif; ?>
      <h2><?php echo html_escape($how_sec['title'] ?? $this->lang->line('how_it_works')); ?></h2>
    </header>
    <div class="steps">
      <?php foreach ($steps as $i => $st): ?>
      <div class="step">
        <span class="step-no"><?php echo $i + 1; ?></span>
        <h3><?php echo html_escape($st['title']); ?></h3>
        <p><?php echo html_escape($st['desc']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

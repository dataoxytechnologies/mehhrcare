<?php
$how = isset($home_sections['how_it_works']) ? $home_sections['how_it_works'] : array();

// Build step list — prefer new items[] format, fall back to legacy step_N_title/desc
$steps = array();
if (!empty($how['items']) && is_array($how['items'])) {
    foreach ($how['items'] as $it) {
        $t = isset($it['title']) ? $it['title'] : '';
        $d = isset($it['desc'])  ? $it['desc']  : '';
        if ($t !== '' || $d !== '') {
            $steps[] = array('title' => $t, 'desc' => $d);
        }
    }
}
if (empty($steps)) {
    for ($i = 1; $i <= 9; $i++) {
        $t = isset($how['step_'.$i.'_title']) ? $how['step_'.$i.'_title'] : '';
        $d = isset($how['step_'.$i.'_desc'])  ? $how['step_'.$i.'_desc']  : '';
        if ($t !== '' || $d !== '') {
            $steps[] = array('title' => $t, 'desc' => $d);
        }
    }
}
?>
<?php if(($how['show'] ?? true) && !empty($steps)): ?>
<section class="how">
  <div class="container">
    <div class="head-row">
      <span class="kicker"><?php echo $this->lang->line('how_it_works'); ?></span>
      <h2><?php echo html_escape($how['title'] ?? 'Three steps. No paperwork.'); ?></h2>
    </div>
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

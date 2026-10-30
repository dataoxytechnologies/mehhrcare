<?php
/**
 * Shared appearance card partial.
 *
 * Matches Wfdocs/theme-studio-patient-prefs-sample.html design fidelity:
 *   - Rich preset cards with mock UI (mock-bar + button + badges) per preset
 *   - sub-head h3 + section-divider pattern (no inner sh-form-card wrappers)
 *   - Hospital-default star on the default preset + legend below grid
 *   - Text size: 4 A-mark buttons with progressive font size visual
 *   - Density: 3 buttons with line-density visual indicator
 *
 * Required vars from parent view:
 *   $user_pref, $hospital_theme, $save_url, $reset_url
 *
 * Lives INSIDE a .card-body. Both admin/appearance + patient/appearance use this.
 */
$active_preset  = !empty($user_pref['theme_preset']) ? $user_pref['theme_preset'] : ($hospital_theme['theme_preset'] ?? 'clinical');
$active_text    = !empty($user_pref['text_size'])    ? $user_pref['text_size']    : ($hospital_theme['text_size']    ?? 'normal');
$active_density = !empty($user_pref['density'])      ? $user_pref['density']      : ($hospital_theme['density']      ?? 'comfortable');
$hospital_default_preset = $hospital_theme['theme_preset'] ?? 'clinical';
?>

<!-- Appearance picker styles moved to backend/css/sh-theme.css -->

<!-- ===== Section 1: Choose a theme ===== -->
<div class="sh-app-sub-head"><h3><?= html_escape($this->lang->line('choose_a_theme')) ?></h3></div>

<div class="sh-app-preset-grid" id="shPresetGrid">
  <?php
    $preset_meta = [
      'clinical'   => ['name' => $this->lang->line('preset_clinical'),   'sub' => 'Light · Teal',         'btn' => 'Save', 'b1' => 'Active',   'b2' => 'Pending', 'b3' => 'Critical'],
      'slate'      => ['name' => $this->lang->line('preset_slate'),      'sub' => 'Light · Indigo',       'btn' => 'Save', 'b1' => 'Active',   'b2' => 'Pending', 'b3' => 'Overdue'],
      'midnight'   => ['name' => $this->lang->line('preset_midnight'),   'sub' => 'Dark · Easy on eyes',  'btn' => 'View', 'b1' => 'Stable',   'b2' => 'Watch',   'b3' => 'Critical'],
      'oled'       => ['name' => $this->lang->line('preset_oled'),       'sub' => 'Pure black · Phones',  'btn' => 'Open', 'b1' => 'Stable',   'b2' => 'Watch',   'b3' => 'Critical'],
      'nightshift' => ['name' => $this->lang->line('preset_nightshift'), 'sub' => 'Warm · Sepia',         'btn' => 'Find', 'b1' => 'Waiting',  'b2' => 'OPD',     'b3' => 'ER'],
    ];
    foreach ($preset_meta as $key => $m):
      $is_default = ($key === $hospital_default_preset);
  ?>
  <div class="sh-app-preset-card preset-<?= $key ?> <?= ($active_preset === $key) ? 'active' : '' ?>" data-preset="<?= $key ?>">
    <i class="sh-app-preset-check fa fa-check"></i>
    <?php if ($is_default): ?><i class="sh-app-hospital-star fa fa-star" title="<?= html_escape($this->lang->line('hospital_default')) ?>"></i><?php endif; ?>
    <div class="sh-app-mock-bar"><span class="sh-app-mock-dot"></span><span class="sh-app-mock-line"></span></div>
    <div class="sh-app-mock-body">
      <div class="sh-app-mock-row"><span class="sh-app-mock-btn"><?= html_escape($m['btn']) ?></span><span class="sh-app-mock-badge"><?= html_escape($m['b1']) ?></span></div>
      <div class="sh-app-mock-row"><span class="sh-app-mock-badge warn"><?= html_escape($m['b2']) ?></span><span class="sh-app-mock-badge danger"><?= html_escape($m['b3']) ?></span></div>
    </div>
    <div class="sh-app-preset-meta"><strong><?= html_escape($m['name']) ?></strong><small><?= html_escape($m['sub']) ?></small></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="sh-app-default-legend">
  <i class="fa fa-star"></i>
  <span><?= html_escape($this->lang->line('hospital_default')) ?></span>
</div>

<hr class="sh-app-section-divider">

<!-- ===== Section 2: Text size ===== -->
<div class="sh-app-sub-head"><h3><?= html_escape($this->lang->line('text_size_label')) ?></h3></div>
<div class="sh-app-size-row" id="shTextSizeRow">
  <?php
    $size_marks = ['compact' => '11px', 'normal' => '15px', 'comfort' => '19px', 'large' => '24px'];
    foreach (['compact','normal','comfort','large'] as $size):
  ?>
    <button type="button" class="sh-app-size-btn <?= ($active_text === $size) ? 'active' : '' ?>" data-size="<?= $size ?>">
      <span class="sh-app-a-mark" style="font-size:<?= $size_marks[$size] ?>;">A</span>
      <span class="sh-app-a-label"><?= html_escape($this->lang->line('text_size_' . $size)) ?></span>
    </button>
  <?php endforeach; ?>
</div>

<hr class="sh-app-section-divider">

<!-- ===== Section 3: Density ===== -->
<div class="sh-app-sub-head"><h3><?= html_escape($this->lang->line('density')) ?></h3></div>
<div class="sh-app-density-row" id="shDensityRow">
  <?php
    $density_lines = ['compact' => 4, 'comfortable' => 3, 'spacious' => 2];
    foreach (['compact','comfortable','spacious'] as $d):
  ?>
    <button type="button" class="sh-app-density-btn <?= ($active_density === $d) ? 'active' : '' ?>" data-density="<?= $d ?>">
      <div class="sh-app-lines"><?php for ($i = 0; $i < $density_lines[$d]; $i++) echo '<span></span>'; ?></div>
      <span><?= html_escape($this->lang->line('density_' . $d)) ?></span>
    </button>
  <?php endforeach; ?>
</div>

<!-- ===== Save bar ===== -->
<div class="sh-app-save-bar">
  <div class="sh-app-save-info">
    <span class="sh-app-dirty-pill clean" id="shDirtyPill"><i class="fa fa-check"></i> <?= html_escape($this->lang->line('theme_saved')) ?></span>
  </div>
  <button type="button" class="btn btn-outline-secondary btn-sm" id="shResetBtn"><i class="fa fa-undo me-1"></i><?= html_escape($this->lang->line('reset_to_hospital_default')) ?></button>
  <button type="button" class="btn btn-primary btn-sm" id="shSaveBtn"><i class="fa fa-check me-1"></i><?= html_escape($this->lang->line('save_changes')) ?></button>
</div>

<script>
(function(){
  const PRESETS = {
    clinical:   <?= json_encode(theme_full_palette('clinical')) ?>,
    slate:      <?= json_encode(theme_full_palette('slate')) ?>,
    midnight:   <?= json_encode(theme_full_palette('midnight')) ?>,
    oled:       <?= json_encode(theme_full_palette('oled')) ?>,
    nightshift: <?= json_encode(theme_full_palette('nightshift')) ?>
  };
  const TEXT_SCALE = { compact: '0.9', normal: '1', comfort: '1.1', large: '1.25' };
  const DENSITY    = { compact: '0.5rem', comfortable: '0.75rem', spacious: '1rem' };

  function markDirty(){
    const p = document.getElementById('shDirtyPill');
    p.classList.remove('clean');
    p.innerHTML = '<i class="fa fa-exclamation-circle"></i> <?= html_escape($this->lang->line('unsaved_changes')) ?>';
  }
  function markClean(){
    const p = document.getElementById('shDirtyPill');
    p.classList.add('clean');
    p.innerHTML = '<i class="fa fa-check"></i> <?= html_escape($this->lang->line('theme_saved')) ?>';
  }
  function setActive(group, target){
    document.querySelectorAll(group).forEach(b => b.classList.remove('active'));
    target.classList.add('active');
  }

  // Preset cards
  document.querySelectorAll('#shPresetGrid .sh-app-preset-card').forEach(btn => {
    btn.addEventListener('click', function(){
      const p = this.dataset.preset;
      const bundle = PRESETS[p]; if (!bundle) return;
      document.documentElement.setAttribute('data-preset', p);
      Object.entries(bundle).forEach(([token, value]) => {
        document.body.style.setProperty(token, value);
      });
      setActive('#shPresetGrid .sh-app-preset-card', this);
      markDirty();
    });
  });

  // Text size buttons
  document.querySelectorAll('#shTextSizeRow .sh-app-size-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const s = this.dataset.size;
      // Must set on :root, not body. --fs-* tokens (sh-tokens.css) declare
      // their var(--text-scale) on :root, so substitution happens at :root.
      document.documentElement.style.setProperty('--text-scale', TEXT_SCALE[s] || '1');
      setActive('#shTextSizeRow .sh-app-size-btn', this);
      markDirty();
    });
  });

  // Density buttons
  document.querySelectorAll('#shDensityRow .sh-app-density-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const d = this.dataset.density;
      document.body.style.setProperty('--density-y', DENSITY[d] || '0.75rem');
      setActive('#shDensityRow .sh-app-density-btn', this);
      markDirty();
    });
  });

  // Save handler
  document.getElementById('shSaveBtn').addEventListener('click', function(){
    const data = {
      theme_preset: document.querySelector('#shPresetGrid .sh-app-preset-card.active')?.dataset.preset,
      text_size:    document.querySelector('#shTextSizeRow .sh-app-size-btn.active')?.dataset.size,
      density:      document.querySelector('#shDensityRow .sh-app-density-btn.active')?.dataset.density,
    };
    if (typeof SH_CSRF_NAME !== 'undefined') data[SH_CSRF_NAME] = SH_CSRF_TOKEN;
    $.post('<?= $save_url ?>', data, function(res){
      if (res && res.status === 'success') {
        markClean();
        setTimeout(() => location.reload(), 800);
      } else {
        alert(<?= json_encode($this->lang->line('save_failed')) ?> + ': ' + JSON.stringify(res.error || ''));
      }
    }, 'json');
  });

  // Reset handler
  document.getElementById('shResetBtn').addEventListener('click', function(){
    if (!confirm(<?= json_encode($this->lang->line('reset_confirm')) ?>)) return;
    const data = {};
    if (typeof SH_CSRF_NAME !== 'undefined') data[SH_CSRF_NAME] = SH_CSRF_TOKEN;
    $.post('<?= $reset_url ?>', data, function(res){
      if (res && res.status === 'success') location.reload();
      else alert(<?= json_encode($this->lang->line('reset_failed')) ?>);
    }, 'json');
  });
})();
</script>

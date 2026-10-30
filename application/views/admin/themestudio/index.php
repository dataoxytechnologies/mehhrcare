<?php
/**
 * Theme Studio — superadmin hospital brand setter.
 * Visual design: Wfdocs/theme-studio-sample.html (approved 2026-05-15).
 *
 * Vars from Themestudio::index():
 *   $hospital_theme — current hospital_theme_settings row (array)
 *   $title          — page title
 */
$ht = $hospital_theme ?? [];
$active_preset  = $ht['theme_preset']  ?? 'clinical';
$primary_color  = $ht['primary_color'] ?? '#00B5AD';
$font_color     = $ht['font_color']    ?? '#0F172A';
$font_family    = $ht['font_family']   ?? 'Inter';
$corner_radius  = $ht['corner_radius'] ?? 'soft';
$text_size      = $ht['text_size']     ?? 'normal';
$density        = $ht['density']       ?? 'comfortable';
?>

<div class="row">
  <?php $this->load->view('setting/sidebar'); ?>
  <div class="col-md-10">
    <div class="card">
          <div class="card-header ptbnull">
            <h3 class="card-title titlefix">
              <?= html_escape($title) ?>
            </h3>
          </div>
          <div class="card-body">

        <!-- ===== Section 1: Presets ===== -->
        <div class="ts-section-head">
          <h3><?= html_escape($this->lang->line('choose_a_theme')) ?></h3>
        </div>

        <div class="ts-preset-grid" id="tsPresetGrid">
          <?php
            $preset_meta = [
              'clinical'   => ['name' => $this->lang->line('preset_clinical'),   'sub' => 'Default · Light teal',  'btn' => 'Save',    'b1' => 'Active',   'b2' => 'Pending', 'b3' => 'Critical'],
              'slate'      => ['name' => $this->lang->line('preset_slate'),      'sub' => 'Light · Indigo',        'btn' => 'Save',    'b1' => 'Active',   'b2' => 'Pending', 'b3' => 'Overdue'],
              'midnight'   => ['name' => $this->lang->line('preset_midnight'),   'sub' => 'Dark · ICU low-light',  'btn' => 'Examine', 'b1' => 'Stable',   'b2' => 'Watch',   'b3' => 'Critical'],
              'oled'       => ['name' => $this->lang->line('preset_oled'),       'sub' => 'Pure black · Tablet',   'btn' => 'Open',    'b1' => 'Stable',   'b2' => 'Watch',   'b3' => 'Critical'],
              'nightshift' => ['name' => $this->lang->line('preset_nightshift'), 'sub' => 'Sepia · Amber',         'btn' => 'Find',    'b1' => 'Waiting',  'b2' => 'OPD',     'b3' => 'ER'],
            ];
            foreach ($preset_meta as $key => $m):
          ?>
          <div class="ts-preset-card preset-<?= $key ?> <?= ($active_preset === $key) ? 'active' : '' ?>" data-preset="<?= $key ?>">
            <i class="ts-preset-check fa fa-check"></i>
            <div class="ts-mock-bar"><span class="ts-mock-dot"></span><span class="ts-mock-line"></span></div>
            <div class="ts-mock-body">
              <div class="ts-mock-row"><span class="ts-mock-btn"><?= html_escape($m['btn']) ?></span><span class="ts-mock-badge"><?= html_escape($m['b1']) ?></span></div>
              <div class="ts-mock-row"><span class="ts-mock-badge warn"><?= html_escape($m['b2']) ?></span><span class="ts-mock-badge danger"><?= html_escape($m['b3']) ?></span></div>
            </div>
            <div class="ts-preset-meta"><strong><?= html_escape($m['name']) ?></strong><small><?= html_escape($m['sub']) ?></small></div>
          </div>
          <?php endforeach; ?>
        </div>

        <hr class="ts-section-divider">

        <!-- ===== Section 2: Customize ===== -->
        <div class="ts-section-head">
          <h3><?= html_escape($this->lang->line('customize')) ?></h3>
        </div>

        <div class="ts-customize-grid">

          <!-- Primary color -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('primary_color')) ?> <small>— buttons &amp; active</small></span>
            <div class="ts-swatches" id="tsPrimarySwatches">
              <?php foreach (['#00B5AD','#1B73E8','#4F46E5','#059669','#F59E0B','#DC2626'] as $c):
                $isActive = (strcasecmp($primary_color, $c) === 0); ?>
                <div class="ts-swatch-btn <?= $isActive ? 'active' : '' ?>" data-color="<?= $c ?>" style="background:<?= $c ?>"></div>
              <?php endforeach; ?>
              <div class="ts-swatch-btn custom" id="tsPrimaryCustomBtn" title="Custom color"><i class="fa fa-eye-dropper"></i></div>
            </div>
            <div class="ts-hex-row">
              <input type="text" class="ts-hex-input" id="tsPrimaryHex" value="<?= html_escape(strtoupper($primary_color)) ?>" maxlength="7">
              <input type="color" id="tsPrimaryPicker" class="ts-color-picker" value="<?= html_escape($primary_color) ?>">
            </div>
            <div class="ts-collision-warning" id="tsCollisionWarning">
              <i class="fa fa-exclamation-triangle"></i>
              <div>
                <strong id="tsCollisionTitle">This primary is close to your danger color.</strong>
                <div id="tsCollisionDetail">Save and Delete buttons may be hard to tell apart.</div>
              </div>
            </div>
          </div>

          <!-- Font color -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('font_color')) ?> <small>— headings &amp; links</small></span>
            <div class="ts-swatches" id="tsFontSwatches">
              <?php foreach (['#0F172A','#1B73E8','#7C3AED','#0B2545','#1E293B','#00B5AD'] as $c):
                $isActive = (strcasecmp($font_color, $c) === 0); ?>
                <div class="ts-swatch-btn <?= $isActive ? 'active' : '' ?>" data-color="<?= $c ?>" style="background:<?= $c ?>"></div>
              <?php endforeach; ?>
              <div class="ts-swatch-btn custom" id="tsFontCustomBtn" title="Custom color"><i class="fa fa-eye-dropper"></i></div>
            </div>
            <div class="ts-hex-row">
              <input type="text" class="ts-hex-input" id="tsFontHex" value="<?= html_escape(strtoupper($font_color)) ?>" maxlength="7">
              <input type="color" id="tsFontPicker" class="ts-color-picker" value="<?= html_escape($font_color) ?>">
            </div>
          </div>

          <!-- Font family -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('font_family')) ?></span>
            <div class="ts-font-grid" id="tsFontFamilyGrid">
              <?php foreach (['Inter','Roboto','Nunito'] as $f): ?>
                <button type="button" class="ts-font-btn <?= ($font_family === $f) ? 'active' : '' ?>" data-font="<?= $f ?>" style="font-family:'<?= $f ?>',sans-serif;">
                  <span class="ts-a-mark">Aa</span><span class="ts-a-name"><?= $f ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Corner style -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('corner_style')) ?></span>
            <div class="ts-radius-row" id="tsRadiusRow">
              <?php foreach (['sharp','soft','rounded'] as $c): ?>
                <button type="button" class="ts-radius-btn <?= ($corner_radius === $c) ? 'active' : '' ?>" data-corner="<?= $c ?>">
                  <div class="ts-shape"></div><span><?= html_escape($this->lang->line('corner_' . $c)) ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Text size -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('text_size_label')) ?></span>
            <div class="ts-size-row" id="tsSizeRow">
              <?php
                $size_marks = ['compact' => '11px', 'normal' => '14px', 'comfort' => '17px', 'large' => '20px'];
                foreach (['compact','normal','comfort','large'] as $s):
              ?>
                <button type="button" class="ts-size-btn <?= ($text_size === $s) ? 'active' : '' ?>" data-size="<?= $s ?>">
                  <span class="ts-a-mark" style="font-size:<?= $size_marks[$s] ?>;">A</span>
                  <span class="ts-a-label"><?= html_escape($this->lang->line('text_size_' . $s)) ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Density -->
          <div class="ts-setting-group">
            <span class="ts-setting-label"><?= html_escape($this->lang->line('density')) ?></span>
            <div class="ts-density-row" id="tsDensityRow">
              <?php
                $density_lines = ['compact' => 4, 'comfortable' => 3, 'spacious' => 2];
                foreach (['compact','comfortable','spacious'] as $d):
              ?>
                <button type="button" class="ts-density-btn <?= ($density === $d) ? 'active' : '' ?>" data-density="<?= $d ?>">
                  <div class="ts-lines"><?php for ($i = 0; $i < $density_lines[$d]; $i++) echo '<span></span>'; ?></div>
                  <span><?= html_escape($this->lang->line('density_' . $d)) ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

        </div>

          </div><!-- /.card-body -->
          <div class="card-footer">
            <!-- .card-footer is display:flex (sh-theme.css); ms-auto pushes the lone button right -->
            <button type="button" class="btn btn-primary btn-sm ms-auto" id="tsSaveBtn"><?= html_escape($this->lang->line('save_changes')) ?></button>
          </div><!-- /.card-footer -->
    </div><!-- /.card -->
  </div><!-- /.col-md-10 -->
</div><!-- /.row -->

<script>
(function(){
  // ===== Preset token bundles — sourced from theme_full_palette() in PHP
  //       so live preview and server-side emission stay in lockstep.
  const PRESETS = {
    clinical:   <?= json_encode(theme_full_palette('clinical')) ?>,
    slate:      <?= json_encode(theme_full_palette('slate')) ?>,
    midnight:   <?= json_encode(theme_full_palette('midnight')) ?>,
    oled:       <?= json_encode(theme_full_palette('oled')) ?>,
    nightshift: <?= json_encode(theme_full_palette('nightshift')) ?>
  };
  const TEXT_SCALE = { compact: '0.9', normal: '1', comfort: '1.1', large: '1.25' };
  const DENSITY    = { compact: '0.5rem', comfortable: '0.75rem', spacious: '1rem' };
  // 3 corner tokens — covers buttons (--radius), cards (--radius-md), modals (--radius-lg)
  const CORNERS = {
    sharp:   { r: '0',    rmd: '0',    rlg: '0' },
    soft:    { r: '4px',  rmd: '6px',  rlg: '8px' },
    rounded: { r: '8px',  rmd: '12px', rlg: '16px' }
  };

  // Set CSS vars on body (not html) — helper's style block targets
  // body[class*="variant-"] which shadows any html-level inline vars.
  const root = document.body;
  let isDirty = false;

  // ===== WCAG helpers =====
  function hexToRgb(h){ h=h.replace('#',''); if(h.length===3)h=h.split('').map(c=>c+c).join(''); const n=parseInt(h,16); return {r:(n>>16)&255,g:(n>>8)&255,b:n&255}; }
  function contrastInk(hex){ const {r,g,b}=hexToRgb(hex); return ((0.299*r+0.587*g+0.114*b)/255)>0.6?'#0F172A':'#FFFFFF'; }
  function relLum(hex){ const {r,g,b}=hexToRgb(hex); const lin=c=>{const v=c/255;return v<=0.03928?v/12.92:Math.pow((v+0.055)/1.055,2.4);}; return 0.2126*lin(r)+0.7152*lin(g)+0.0722*lin(b); }
  function contrastRatio(a,b){ const L1=relLum(a),L2=relLum(b); const [hi,lo]=L1>L2?[L1,L2]:[L2,L1]; return (hi+0.05)/(lo+0.05); }
  function rgbToHue(hex){ const {r,g,b}=hexToRgb(hex); const rN=r/255,gN=g/255,bN=b/255; const max=Math.max(rN,gN,bN),min=Math.min(rN,gN,bN),d=max-min; if(d===0)return 0; let h; if(max===rN)h=((gN-bN)/d)%6; else if(max===gN)h=(bN-rN)/d+2; else h=(rN-gN)/d+4; h=Math.round(h*60); return h<0?h+360:h; }
  function hueDist(h1,h2){ const d=Math.abs(h1-h2)%360; return d>180?360-d:d; }

  // Contrast badges removed from the UI — kept as no-ops so existing callers
  // (applyPrimary / applyFont / applyPreset / init) don't break.
  function updateContrastBadge(hex){ /* badge removed */ }
  function updateFontContrastBadge(hex){ /* badge removed */ }
  function checkCollision(hex){
    const warn = document.getElementById('tsCollisionWarning');
    const title = document.getElementById('tsCollisionTitle');
    const detail = document.getElementById('tsCollisionDetail');
    const {r,g,b} = hexToRgb(hex);
    const max=Math.max(r,g,b), min=Math.min(r,g,b);
    if ((max===0 ? 0 : (max-min)/max) < 0.15) { warn.classList.remove('show'); return; }
    const h = rgbToHue(hex);
    if (hueDist(h, rgbToHue('#DC2626')) <= 25) {
      title.textContent = 'This primary is close to your danger / delete color.';
      detail.textContent = 'Save and Delete buttons may be hard to tell apart.';
      warn.classList.add('show');
    } else if (hueDist(h, rgbToHue('#22A06B')) <= 25) {
      title.textContent = 'This primary is close to your success color.';
      detail.textContent = 'Primary buttons and "Discharged" badges may look identical.';
      warn.classList.add('show');
    } else {
      warn.classList.remove('show');
    }
  }

  // ===== Dirty state =====
  // Status pill removed from the UI; the isDirty flag still drives the
  // beforeunload "unsaved changes" prompt, so keep it — just skip the pill DOM.
  function markDirty(){
    isDirty = true;
    const p = document.getElementById('tsDirtyPill');
    if (!p) return;
    p.classList.remove('clean');
    p.innerHTML = '<i class="fa fa-circle-exclamation"></i> <?= html_escape($this->lang->line('unsaved_changes')) ?>';
  }
  function markClean(){
    isDirty = false;
    const p = document.getElementById('tsDirtyPill');
    if (!p) return;
    p.classList.add('clean');
    p.innerHTML = '<i class="fa fa-check"></i> <?= html_escape($this->lang->line('theme_saved')) ?>';
  }
  window.addEventListener('beforeunload', (e) => { if (isDirty) { e.preventDefault(); e.returnValue = ''; } });

  function setActive(group, target){
    document.querySelectorAll(group).forEach(b => b.classList.remove('active'));
    if (target) target.classList.add('active');
  }
  function syncSwatchActive(containerId, color){
    const c = document.getElementById(containerId);
    c.querySelectorAll('.ts-swatch-btn').forEach(b => b.classList.remove('active'));
    const m = Array.from(c.querySelectorAll('.ts-swatch-btn[data-color]'))
                   .find(b => b.dataset.color.toLowerCase() === color.toLowerCase());
    if (m) m.classList.add('active');
  }

  // ===== Bindings =====
  // Mirror of theme_preset_to_variant() in theme_helper.php. The variant CLASS
  // (not just data-preset) carries color-scheme: dark (sh-tokens.css: body.variant-b).
  // color-scheme is a real property, NOT a --token, so inline body vars can't override
  // it — the class MUST be swapped or a dark→light switch leaves color-scheme:dark stuck,
  // rendering native widgets/text white on the now-light card ("blank" until Save reloads).
  const PRESET_VARIANT = { clinical:'a', slate:'a', nightshift:'a', midnight:'b', oled:'b' };

  function applyPreset(presetName, target) {
    const bundle = PRESETS[presetName]; if (!bundle) return;
    document.documentElement.setAttribute('data-preset', presetName);
    // Keep the body variant class in lockstep with the preset so color-scheme follows.
    root.classList.remove('variant-a', 'variant-b', 'variant-c');
    root.classList.add('variant-' + (PRESET_VARIANT[presetName] || 'a'));
    // Apply the FULL token bundle — bg, surface, border, ink, accent, accent-ink,
    // accent-soft, accent-dark, link, muted, rowhover, rail, topbar etc. So
    // clicking Midnight actually turns the whole chrome dark, not just the accent.
    Object.entries(bundle).forEach(([token, value]) => {
      root.style.setProperty(token, value);
    });
    setActive('#tsPresetGrid .ts-preset-card', target);
    // Sync the color pickers + contrast badges to the preset's accent/ink defaults
    const accent = bundle['--accent'];
    const ink    = bundle['--ink'];
    if (accent) {
      document.getElementById('tsPrimaryHex').value = accent.toUpperCase();
      document.getElementById('tsPrimaryPicker').value = accent;
      syncSwatchActive('tsPrimarySwatches', accent);
      updateContrastBadge(accent);
      checkCollision(accent);
    }
    if (ink) {
      document.getElementById('tsFontHex').value = ink.toUpperCase();
      document.getElementById('tsFontPicker').value = ink;
      syncSwatchActive('tsFontSwatches', ink);
      updateFontContrastBadge(ink);
    }
    markDirty();
  }
  document.querySelectorAll('#tsPresetGrid .ts-preset-card').forEach(c => c.addEventListener('click', function(){
    applyPreset(this.dataset.preset, this);
  }));

  function applyPrimary(hex){
    root.style.setProperty('--accent', hex);
    root.style.setProperty('--link',   hex);
    document.getElementById('tsPrimaryHex').value = hex.toUpperCase();
    document.getElementById('tsPrimaryPicker').value = hex;
    updateContrastBadge(hex);
    checkCollision(hex);
    markDirty();
  }
  function applyFont(hex){
    root.style.setProperty('--ink', hex);
    document.getElementById('tsFontHex').value = hex.toUpperCase();
    document.getElementById('tsFontPicker').value = hex;
    updateFontContrastBadge(hex);
    markDirty();
  }

  // Swatch click
  function bindSwatch(containerId, applyFn){
    document.getElementById(containerId).querySelectorAll('.ts-swatch-btn:not(.custom)').forEach(btn => {
      btn.addEventListener('click', () => { setActive('#' + containerId + ' .ts-swatch-btn', btn); applyFn(btn.dataset.color); });
    });
  }
  bindSwatch('tsPrimarySwatches', applyPrimary);
  bindSwatch('tsFontSwatches', applyFont);

  // Hex input + native picker
  document.getElementById('tsPrimaryHex').addEventListener('change', (e) => {
    let v = e.target.value.startsWith('#') ? e.target.value : '#' + e.target.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(v)) { applyPrimary(v); document.querySelectorAll('#tsPrimarySwatches .ts-swatch-btn').forEach(b => b.classList.remove('active')); }
  });
  document.getElementById('tsFontHex').addEventListener('change', (e) => {
    let v = e.target.value.startsWith('#') ? e.target.value : '#' + e.target.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(v)) { applyFont(v); document.querySelectorAll('#tsFontSwatches .ts-swatch-btn').forEach(b => b.classList.remove('active')); }
  });
  document.getElementById('tsPrimaryPicker').addEventListener('input', (e) => { applyPrimary(e.target.value); document.querySelectorAll('#tsPrimarySwatches .ts-swatch-btn').forEach(b => b.classList.remove('active')); });
  document.getElementById('tsFontPicker').addEventListener('input', (e) => { applyFont(e.target.value); document.querySelectorAll('#tsFontSwatches .ts-swatch-btn').forEach(b => b.classList.remove('active')); });
  document.getElementById('tsPrimaryCustomBtn').addEventListener('click', () => document.getElementById('tsPrimaryPicker').click());
  document.getElementById('tsFontCustomBtn').addEventListener('click', () => document.getElementById('tsFontPicker').click());

  // Font family
  document.querySelectorAll('#tsFontFamilyGrid .ts-font-btn').forEach(btn => btn.addEventListener('click', function(){
    root.style.setProperty('--font', "'" + this.dataset.font + "', system-ui, sans-serif");
    setActive('#tsFontFamilyGrid .ts-font-btn', this); markDirty();
  }));

  // Corner / size / density button groups
  document.querySelectorAll('#tsRadiusRow .ts-radius-btn').forEach(btn => btn.addEventListener('click', function(){
    const c = CORNERS[this.dataset.corner] || CORNERS.soft;
    root.style.setProperty('--radius',    c.r);
    root.style.setProperty('--radius-md', c.rmd);
    root.style.setProperty('--radius-lg', c.rlg);
    setActive('#tsRadiusRow .ts-radius-btn', this); markDirty();
  }));
  document.querySelectorAll('#tsSizeRow .ts-size-btn').forEach(btn => btn.addEventListener('click', function(){
    document.documentElement.style.setProperty('--text-scale', TEXT_SCALE[this.dataset.size] || '1');
    setActive('#tsSizeRow .ts-size-btn', this); markDirty();
  }));
  document.querySelectorAll('#tsDensityRow .ts-density-btn').forEach(btn => btn.addEventListener('click', function(){
    root.style.setProperty('--density-y', DENSITY[this.dataset.density] || '0.75rem');
    setActive('#tsDensityRow .ts-density-btn', this); markDirty();
  }));


  // Save handler
  document.getElementById('tsSaveBtn').addEventListener('click', function(){
    const data = {
      theme_preset:  document.querySelector('#tsPresetGrid .ts-preset-card.active')?.dataset.preset,
      primary_color: document.getElementById('tsPrimaryHex').value,
      font_color:    document.getElementById('tsFontHex').value,
      font_family:   document.querySelector('#tsFontFamilyGrid .ts-font-btn.active')?.dataset.font || 'Inter',
      corner_radius: document.querySelector('#tsRadiusRow .ts-radius-btn.active')?.dataset.corner,
      text_size:     document.querySelector('#tsSizeRow .ts-size-btn.active')?.dataset.size,
      density:       document.querySelector('#tsDensityRow .ts-density-btn.active')?.dataset.density,
    };
    if (typeof SH_CSRF_NAME !== 'undefined') data[SH_CSRF_NAME] = SH_CSRF_TOKEN;
    $.post('<?= base_url('admin/themestudio/save') ?>', data, function(res){
      if (res && res.status === 'success') {
        markClean();
        // Auto-reload so the user sees the actual server-rendered theme,
        // not just the live-preview JS overlay. Confirms persistence visually.
        setTimeout(() => location.reload(), 500);
      } else {
        alert(<?= json_encode($this->lang->line('save_failed')) ?> + ': ' + JSON.stringify(res.error || ''));
      }
    }, 'json');
  });

  // Init contrast badges from current saved values
  updateContrastBadge(document.getElementById('tsPrimaryHex').value);
  updateFontContrastBadge(document.getElementById('tsFontHex').value);
})();
</script>

<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Theme Studio cascade helper.
 *
 * Resolves the merged token set per request (hospital defaults + optional
 * per-user overrides), maps presets to the underlying variant-a/b/c body
 * class, and renders the inline <style> override block that gets injected
 * between sh-tokens.css and sh-theme.css in every layout.
 *
 * Re-resolved per request — NO session caching. When superadmin changes
 * the hospital brand, users without their own preferences see the new
 * brand immediately on their next page load.
 */

if (!function_exists('theme_default_tokens')) {
    function theme_default_tokens()
    {
        return [
            'theme_preset'  => 'clinical',
            'primary_color' => '#00B5AD',
            'font_color'    => '#0F172A',
            'font_family'   => 'Inter',
            'corner_radius' => 'rounded',
            'text_size'     => 'normal',
            'density'       => 'comfortable',
        ];
    }
}

if (!function_exists('theme_hex_to_rgb')) {
    function theme_hex_to_rgb($hex)
    {
        $hex = ltrim((string) $hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return null;
        }
        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}

if (!function_exists('theme_darken_hex')) {
    function theme_darken_hex($hex, $percent = 14)
    {
        $rgb = theme_hex_to_rgb($hex);
        if ($rgb === null) return $hex;
        $factor = max(0, min(100, (int) $percent)) / 100;
        $r = (int) max(0, round($rgb[0] * (1 - $factor)));
        $g = (int) max(0, round($rgb[1] * (1 - $factor)));
        $b = (int) max(0, round($rgb[2] * (1 - $factor)));
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }
}

if (!function_exists('theme_soften_hex')) {
    function theme_soften_hex($hex, $alpha = 0.14)
    {
        $rgb = theme_hex_to_rgb($hex);
        if ($rgb === null) return $hex;
        return 'rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',' . number_format((float) $alpha, 2) . ')';
    }
}

if (!function_exists('theme_resolve_tokens')) {
    function theme_resolve_tokens($user_id = null, $user_type = null)
    {
        $CI =& get_instance();
        $CI->load->model('hospital_theme_settings_model');
        $hospital = $CI->hospital_theme_settings_model->get();
        if (empty($hospital)) {
            $hospital = theme_default_tokens();
        }

        if (!$user_id || !$user_type) {
            return $hospital;
        }

        $CI->load->model('user_theme_preference_model');
        $user = $CI->user_theme_preference_model->getByUser($user_id, $user_type);
        if (empty($user)) {
            return $hospital;
        }

        // If the user picked a different preset than the hospital's, drop the
        // hospital's picker values (primary_color, font_color, font_family) —
        // those were authored for the hospital's preset and break contrast
        // when forced onto a preset with a different light/dark posture.
        // Example: hospital saved font_color=#0F172A (near-black) for clinical
        // light theme; if patient picks midnight (dark), keeping that ink
        // produces near-black text on a near-black background.
        if (!empty($user['theme_preset']) && $user['theme_preset'] !== ($hospital['theme_preset'] ?? null)) {
            unset($hospital['primary_color'], $hospital['font_color'], $hospital['font_family']);
        }

        foreach (['theme_preset', 'text_size', 'density'] as $k) {
            if (!empty($user[$k])) {
                $hospital[$k] = $user[$k];
            }
        }
        return $hospital;
    }
}

if (!function_exists('theme_full_palette')) {
    /**
     * Full token bundle for each preset — bg, surface, border, ink, accent,
     * accent-ink, accent-soft, accent-dark, muted, border-strong, rowhover.
     * These map verbatim to the existing sh-tokens.css per-variant blocks
     * so dramatic light/dark/sepia switching works end-to-end.
     *
     * Mirrors Wfdocs/theme-studio-sample.html PRESETS object — kept here
     * server-side so JS live preview + server emission read from one source.
     */
    function theme_full_palette($preset)
    {
        $palettes = [
            'clinical' => [
                '--bg' => '#F7FAFC', '--surface' => '#FFFFFF', '--surface-2' => '#F1F5F9', '--surface-3' => '#E2E8F0',
                '--ink' => '#0F172A', '--ink-2' => '#334155',
                '--muted' => '#64748B', '--faint' => '#94A3B8',
                '--border' => '#E2E8F0', '--border-light' => '#F1F5F9', '--border-strong' => '#CBD5E1',
                '--accent' => '#00B5AD', '--accent-ink' => '#FFFFFF', '--accent-soft' => '#D4F1EF', '--accent-dark' => '#008B85',
                '--link' => '#00B5AD', '--link-2' => '#008B85',
                '--rowhover' => '#F1F5F9',
                '--rail' => '#FFFFFF', '--rail-ink' => '#64748B', '--rail-ink-active' => '#0F172A',
                '--topbar' => '#FFFFFF', '--topbar-ink' => '#0F172A',
            ],
            'slate' => [
                '--bg' => '#F1F5F9', '--surface' => '#FFFFFF', '--surface-2' => '#E2E8F0', '--surface-3' => '#CBD5E1',
                '--ink' => '#1E293B', '--ink-2' => '#334155',
                '--muted' => '#64748B', '--faint' => '#94A3B8',
                '--border' => '#CBD5E1', '--border-light' => '#E2E8F0', '--border-strong' => '#94A3B8',
                '--accent' => '#4F46E5', '--accent-ink' => '#FFFFFF', '--accent-soft' => '#E0E7FF', '--accent-dark' => '#3730A3',
                '--link' => '#4F46E5', '--link-2' => '#3730A3',
                '--rowhover' => '#E2E8F0',
                '--rail' => '#FFFFFF', '--rail-ink' => '#64748B', '--rail-ink-active' => '#1E293B',
                '--topbar' => '#FFFFFF', '--topbar-ink' => '#1E293B',
            ],
            'midnight' => [
                '--bg' => '#0F172A', '--surface' => '#1E293B', '--surface-2' => '#334155', '--surface-3' => '#475569',
                '--ink' => '#E2E8F0', '--ink-2' => '#CBD5E1',
                '--muted' => '#94A3B8', '--faint' => '#64748B',
                '--border' => 'rgba(255,255,255,.22)', '--border-light' => 'rgba(255,255,255,.12)', '--border-strong' => 'rgba(255,255,255,.34)',
                '--accent' => '#4EA2FF', '--accent-ink' => '#0F172A', '--accent-soft' => 'rgba(78,162,255,.18)', '--accent-dark' => '#1B73E8',
                '--link' => '#4EA2FF', '--link-2' => '#7FB9FF',
                '--rowhover' => 'rgba(255,255,255,.04)',
                '--rail' => '#1E293B', '--rail-ink' => '#94A3B8', '--rail-ink-active' => '#E2E8F0',
                '--topbar' => '#1E293B', '--topbar-ink' => '#E2E8F0',
            ],
            'oled' => [
                '--bg' => '#000000', '--surface' => '#0A0A0A', '--surface-2' => '#1A1A1A', '--surface-3' => '#2A2A2A',
                '--ink' => '#F1F5F9', '--ink-2' => '#CBD5E1',
                '--muted' => '#94A3B8', '--faint' => '#64748B',
                '--border' => 'rgba(255,255,255,.26)', '--border-light' => 'rgba(255,255,255,.14)', '--border-strong' => 'rgba(255,255,255,.38)',
                '--accent' => '#60A5FA', '--accent-ink' => '#000000', '--accent-soft' => 'rgba(96,165,250,.16)', '--accent-dark' => '#3B82F6',
                '--link' => '#60A5FA', '--link-2' => '#93C5FD',
                '--rowhover' => 'rgba(255,255,255,.04)',
                '--rail' => '#0A0A0A', '--rail-ink' => '#94A3B8', '--rail-ink-active' => '#F1F5F9',
                '--topbar' => '#0A0A0A', '--topbar-ink' => '#F1F5F9',
            ],
            'nightshift' => [
                '--bg' => '#2A1F14', '--surface' => '#3D2F1F', '--surface-2' => '#5C4730', '--surface-3' => '#7A5E3F',
                '--ink' => '#F5E6D3', '--ink-2' => '#E0CFB5',
                '--muted' => '#BFA98F', '--faint' => '#8C7B65',
                '--border' => 'rgba(245,230,211,.14)', '--border-light' => 'rgba(245,230,211,.07)', '--border-strong' => 'rgba(245,230,211,.24)',
                '--accent' => '#F59E0B', '--accent-ink' => '#1A0F05', '--accent-soft' => 'rgba(245,158,11,.20)', '--accent-dark' => '#D97706',
                '--link' => '#F59E0B', '--link-2' => '#D97706',
                '--rowhover' => 'rgba(245,230,211,.06)',
                '--rail' => '#3D2F1F', '--rail-ink' => '#BFA98F', '--rail-ink-active' => '#F5E6D3',
                '--topbar' => '#3D2F1F', '--topbar-ink' => '#F5E6D3',
            ],
        ];
        return isset($palettes[$preset]) ? $palettes[$preset] : $palettes['clinical'];
    }
}

if (!function_exists('theme_preset_to_variant')) {
    /**
     * Maps a Theme Studio preset name to the underlying variant-a/b/c body class
     * that provides the base bg/surface/ink palette.
     *
     *   clinical    -> a (light, teal accent default)
     *   slate       -> a (light, slate accent default)
     *   nightshift  -> a (light, sepia accent default)
     *   midnight    -> b (dark)
     *   oled        -> b (dark, pure black bg via additional preset overrides)
     *
     * Anything unknown falls back to 'a'.
     */
    function theme_preset_to_variant($preset)
    {
        $map = [
            'clinical'   => 'a',
            'slate'      => 'a',
            'nightshift' => 'a',
            'midnight'   => 'b',
            'oled'       => 'b',
        ];
        return isset($map[$preset]) ? $map[$preset] : 'a';
    }
}

if (!function_exists('theme_render_style_block')) {
    function theme_render_style_block($tokens)
    {
        $text_scale_map = ['compact' => '0.9', 'normal' => '1', 'comfort' => '1.1', 'large' => '1.25'];
        $density_map    = ['compact' => '0.5rem', 'comfortable' => '0.75rem', 'spacious' => '1rem'];
        // Corner radius — MUST stay in lock-step with the JS CORNERS object in
        // admin/themestudio/index.php (the live-preview path). Three tokens cover
        // buttons (--radius), cards (--radius-md), modals (--radius-lg). Without
        // this, corner_radius saves to the DB but never reaches the rendered
        // page, so the saved theme silently falls back to sh-tokens.css defaults.
        $corner_map = [
            'sharp'   => ['--radius' => '0',   '--radius-md' => '0',    '--radius-lg' => '0'],
            'soft'    => ['--radius' => '4px', '--radius-md' => '6px',  '--radius-lg' => '8px'],
            'rounded' => ['--radius' => '8px', '--radius-md' => '12px', '--radius-lg' => '16px'],
        ];

        $preset_name = $tokens['theme_preset'] ?? 'clinical';
        $scale       = isset($text_scale_map[$tokens['text_size'] ?? 'normal']) ? $text_scale_map[$tokens['text_size']] : '1';
        $dy          = isset($density_map[$tokens['density'] ?? 'comfortable']) ? $density_map[$tokens['density']] : '0.75rem';
        $preset_safe = htmlspecialchars($preset_name, ENT_QUOTES);

        // Pull the full token palette for the saved preset (bg, surface, ink, border, accent, etc.)
        $palette = theme_full_palette($preset_name);

        // Picker overrides — only apply when the resolver actually passed a
        // value. Hardcoded fallbacks would clobber the preset palette: e.g.
        // an unset font_color defaulting to #0F172A turns midnight's light
        // ink into near-black on a near-black background. When the keys are
        // absent (because the cascade dropped them due to a user preset
        // override), the preset's native values win.
        if (!empty($tokens['primary_color'])) {
            $primary = $tokens['primary_color'];
            $palette['--accent']      = $primary;
            $palette['--accent-dark'] = theme_darken_hex($primary, 14);
            $palette['--accent-soft'] = theme_soften_hex($primary, 0.14);
            $palette['--link']        = $primary;
            $palette['--link-2']      = theme_darken_hex($primary, 14);
        }
        if (!empty($tokens['font_color'])) {
            $palette['--ink'] = $tokens['font_color'];
        }

        // Typography + density vars. Font family only overrides when set,
        // otherwise leave the existing --font (or whatever sh-tokens.css
        // declares) so the preset's intended typography stays intact.
        if (!empty($tokens['font_family'])) {
            $font = htmlspecialchars($tokens['font_family'], ENT_QUOTES);
            $palette['--font'] = "'$font', system-ui, -apple-system, sans-serif";
        }
        $palette['--density-y']   = $dy;

        // Corner radius tokens — resolved from the saved corner_radius value.
        // Emitted on body[class*="variant-"] (same as every other token) so they
        // override the per-variant defaults in sh-tokens.css for all descendants.
        $corner = $corner_map[$tokens['corner_radius'] ?? 'soft'] ?? $corner_map['soft'];
        foreach ($corner as $token => $value) {
            $palette[$token] = $value;
        }

        // Build the CSS rule body from the merged palette. NO htmlspecialchars:
        // <style> contents are parsed as CSS, not HTML — escaping quotes to
        // &#039; mangles font-family values like 'Roboto'. Controller validates
        // all inbound values (in_list / regex), so palette values are safe.
        $css_decls = '';
        foreach ($palette as $token => $value) {
            $css_decls .= "  {$token}: {$value};\n";
        }

        return <<<HTML
<style id="sh-theme-vars">
/* Theme Studio per-request overlay. Higher source order than sh-tokens.css
   variant-a/b/c blocks; same specificity (body[class*="variant-"]) so this wins. */
:root { --text-scale: {$scale}; }
body[class*="variant-"] {
{$css_decls}}
/* --text-scale lives on :root so that --fs-* token definitions (also on :root in
   sh-tokens.css) resolve their var(--text-scale) correctly. Setting it on body would
   bake in the wrong value when :root computes those tokens. */
/* --density-y consumption: vertical padding on table cells + card bodies.
   No-op when var falls back; takes effect when density buttons change the var. */
.table > :not(caption) > * > * { padding-top: var(--density-y, 0.5rem) !important; padding-bottom: var(--density-y, 0.5rem) !important; }
.card > .card-body { padding-top: calc(var(--density-y, 0.75rem) * 1.5); padding-bottom: calc(var(--density-y, 0.75rem) * 1.5); }
</style>
<script>document.documentElement.setAttribute('data-preset','{$preset_safe}');</script>
HTML;
    }
}

/**
 * Per-layout hero media defaults.
 *
 * Single source of truth for the built-in hero background image + video of
 * each of the 6 home-page layouts. Consumed by:
 *   - the Front CMS admin form (pre-fill, so the admin sees the real URL), and
 *   - resolve_hero_media() below (front-side fallback when a layout has no
 *     stored override yet).
 *
 * Defaults are theme-agnostic (atrium + organic shared the same per-layout
 * URLs). An empty bg_video means that layout is image-only by default.
 */
if (!function_exists('hero_media_defaults')) {
    function hero_media_defaults()
    {
        return [
            'hospital'   => [
                'bg_image' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1920&q=80&auto=format&fit=crop',
                'bg_video' => 'https://videos.pexels.com/video-files/6130116/6130116-hd_1920_1080_30fps.mp4',
            ],
            'specialist' => [
                'bg_image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=1920&q=80&auto=format&fit=crop',
                'bg_video' => '',
            ],
            'dental'     => [
                'bg_image' => 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?w=1920&q=80&auto=format&fit=crop',
                'bg_video' => '',
            ],
            'eye'        => [
                'bg_image' => 'https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=1920&q=80&auto=format&fit=crop',
                'bg_video' => 'https://videos.pexels.com/video-files/7580479/7580479-uhd_4096_2160_25fps.mp4',
            ],
            'diagnostic' => [
                'bg_image' => 'https://images.pexels.com/videos/4468981/free-video-4468981.jpg?auto=compress&cs=tinysrgb&w=1920',
                'bg_video' => 'https://videos.pexels.com/video-files/4468981/4468981-hd_1920_1080_25fps.mp4',
            ],
            'cardiology' => [
                'bg_image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1920&q=80&auto=format&fit=crop',
                'bg_video' => 'https://videos.pexels.com/video-files/7088514/7088514-hd_1920_1080_25fps.mp4',
            ],
        ];
    }
}

/**
 * Resolve the picked layout's hero media into the legacy $hero shape.
 *
 * The home views read $hero['bg_image'] / $hero['bg_video']. This injects the
 * active layout's media into those keys so the views need no per-layout logic.
 *
 * Semantics (the "blank = image" rule):
 *   - layout key ABSENT (admin never saved the feature) -> built-in default
 *   - key present (even empty string) -> used verbatim, so a cleared video
 *     field yields image-only on the front.
 */
if (!function_exists('resolve_hero_media')) {
    function resolve_hero_media($home_sections, $layout)
    {
        if (!is_array($home_sections)) {
            $home_sections = [];
        }
        $defaults = hero_media_defaults();
        $def      = isset($defaults[$layout]) ? $defaults[$layout] : ['bg_image' => '', 'bg_video' => ''];
        $stored   = isset($home_sections['hero_media'][$layout]) && is_array($home_sections['hero_media'][$layout])
                  ? $home_sections['hero_media'][$layout]
                  : null;

        if (!isset($home_sections['hero']) || !is_array($home_sections['hero'])) {
            $home_sections['hero'] = [];
        }
        $home_sections['hero']['bg_image'] = ($stored !== null && array_key_exists('bg_image', $stored))
                                           ? $stored['bg_image'] : $def['bg_image'];
        $home_sections['hero']['bg_video'] = ($stored !== null && array_key_exists('bg_video', $stored))
                                           ? $stored['bg_video'] : $def['bg_video'];
        return $home_sections;
    }
}

if (!function_exists('front_theme_primary_hex')) {
    /**
     * Primary accent colour (hex) of the currently active FRONT theme, read
     * from front_cms_settings (theme + theme_color). Read-only — no writes,
     * no schema changes.
     *
     * Palette → accent hex is copied verbatim from each theme's stylesheet:
     *   organic → --accent-1  (backend/themes/organic/front/css/styles.css)
     *   atrium  → --accent     (backend/themes/atrium/front/css/styles.css)
     * Legacy themes carry a single fixed accent. Anything unresolved returns
     * $fallback so callers never render a broken colour.
     *
     * Used by the public form (surveyform/fill) and the admin form preview so
     * both match the live front-site branding from one source of truth.
     */
    function front_theme_primary_hex($fallback = '#3c8dbc')
    {
        $CI =& get_instance();
        $CI->load->model('Frontcms_setting_model');
        $fs = $CI->Frontcms_setting_model->get();
        if (!is_object($fs)) {
            return $fallback;
        }

        $theme   = strtolower(trim((string)($fs->theme ?? '')));
        $palette = strtolower(trim((string)($fs->theme_color ?? '')));

        // Named-palette themes (organic + atrium). Palette names don't overlap.
        $palette_map = [
            // organic — primary is --accent-1 (also the theme's --feature-bg)
            'garden' => '#3F5040', 'pebble' => '#3A4540', 'sky' => '#1E4549', 'midnight' => '#B6D9B0',
            // atrium — primary is --accent
            'aurora' => '#0BAA8E', 'saffron' => '#C97B1F', 'royal' => '#5746E2', 'onyx' => '#3DDFC2',
        ];
        if ($palette !== '' && isset($palette_map[$palette])) {
            return $palette_map[$palette];
        }

        // Legacy single-accent themes, keyed by theme name.
        $legacy_map = [
            'material_pink'  => '#bd0745',
            'turquoise_blue' => '#016a8a',
            'sky_blue'       => '#3399ff',
            'white_gray'     => '#444444',
        ];
        if ($theme !== '' && isset($legacy_map[$theme])) {
            return $legacy_map[$theme];
        }

        return $fallback;
    }
}

<?php
// Shared home sections for all Atrium layouts.
// Renders each section as its own partial in the order saved by admin (Frontcms → Home Sections tab).
// `home_sections[order]` is an array of section keys; missing/unknown keys fall back to the default order below.

$hs = isset($home_sections) ? $home_sections : array();

$default_order = array(
    'marquee', 'quick_tiles', 'departments', 'doctors', 'how_it_works',
    'stats', 'locations', 'testimonials', 'tpas', 'cta',
);

$saved_order = (isset($hs['order']) && is_array($hs['order'])) ? array_values($hs['order']) : array();
$order = array();
foreach ($saved_order as $k) {
    if (in_array($k, $default_order, true) && !in_array($k, $order, true)) {
        $order[] = $k;
    }
}
foreach ($default_order as $k) {
    if (!in_array($k, $order, true)) { $order[] = $k; }
}

foreach ($order as $section_key) {
    $this->load->view('themes/atrium/_partials/' . $section_key);
}

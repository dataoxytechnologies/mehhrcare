<?php
$layout  = $home_layout ?? 'hospital';
$allowed = array('hospital', 'specialist', 'dental', 'eye', 'diagnostic', 'cardiology');
$layout  = in_array($layout, $allowed) ? $layout : 'hospital';
echo $this->load->view('themes/atrium/home-' . $layout, array(), true);

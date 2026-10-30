<?php
$layout  = $home_layout ?? 'hospital';
$allowed = array('hospital', 'specialist', 'dental', 'eye', 'diagnostic', 'cardiology');
$layout  = in_array($layout, $allowed, true) ? $layout : 'hospital';
echo $this->load->view('themes/organic/home-' . $layout, array(), true);

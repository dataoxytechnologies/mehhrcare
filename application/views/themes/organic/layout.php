<!doctype html>
<html lang="en" dir="<?php echo (isset($front_setting->is_active_rtl) && $front_setting->is_active_rtl) ? 'rtl' : 'ltr'; ?>" data-theme="<?php echo html_escape($theme_color); ?>">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php echo html_escape($school_setting->name ?? 'Smart Hospital'); ?></title>
<?php if(!empty($front_setting->fav_icon)): ?>
<link rel="icon" href="<?php echo html_escape($this->customlib->getBaseUrl() . $front_setting->fav_icon); ?>">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,400..700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo $base_assets_url; ?>front/css/styles.css?v=<?php echo filemtime(FCPATH.'backend/themes/organic/front/css/styles.css'); ?>" />
<link rel="stylesheet" href="<?php echo $base_assets_url; ?>front/css/pages.css?v=<?php echo filemtime(FCPATH.'backend/themes/organic/front/css/pages.css'); ?>" />
<link rel="stylesheet" href="<?php echo $base_assets_url; ?>front/css/components.css?v=<?php echo filemtime(FCPATH.'backend/themes/organic/front/css/components.css'); ?>" />
<?php if(isset($front_setting->is_active_rtl) && $front_setting->is_active_rtl): ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-front-rtl.css" />
<?php endif; ?>
<?php if(!empty($front_setting->google_analytics)) echo $front_setting->google_analytics; ?>
</head>
<body>
<?php echo $header; ?>
<main>
<?php echo $content; ?>
</main>
<?php echo $footer; ?>
<script src="<?php echo $base_assets_url; ?>front/js/shell.js"></script>
</body>
</html>

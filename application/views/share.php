<?php
    // Theme Studio integration — public page, no logged-in user, so we read
    // the hospital's default tokens. $sh_theme_tokens is auto-loaded by
    // MY_Controller::__construct() via $this->load->vars().
    $sh_tokens   = isset($sh_theme_tokens) && is_array($sh_theme_tokens) ? $sh_theme_tokens : theme_resolve_tokens();
    $sh_variant  = theme_preset_to_variant($sh_tokens['theme_preset'] ?? 'clinical');

    $logoresult = $this->customlib->getLogoImage();
    if (!empty($logoresult['mini_logo'])) {
        $mini_logo = base_url() . 'uploads/hospital_content/logo/' . $logoresult['mini_logo'];
    } else {
        $mini_logo = base_url() . 'backend/images/s-favican.png';
    }
    if (!empty($logoresult['image'])) {
        $logo_image = 'uploads/hospital_content/logo/' . $logoresult['image'];
    } else {
        $logo_image = 'uploads/hospital_content/logo/s_logo.png';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#424242" />
    <title><?php echo $this->customlib->getAppName(); ?></title>

    <link href="<?php echo $mini_logo; ?>" rel="shortcut icon" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/all.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-tokens.css">
    <?php echo theme_render_style_block($sh_tokens); ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/css/sh-share.css">
</head>
<body class="variant-<?php echo htmlspecialchars($sh_variant, ENT_QUOTES); ?>">

<?php
$is_valid = isset($share_data->share_date)
    && strtotime(date('Y-m-d')) >= strtotime($share_data->share_date)
    && (IsNullOrEmptyString($share_data->valid_upto)
        || (!IsNullOrEmptyString($share_data->valid_upto) && strtotime(date('Y-m-d')) <= strtotime($share_data->valid_upto)));
?>

<div class="share-page">
    <div class="share-card">

        <div class="share-card__head">
            <img class="share-card__logo" src="<?php echo $this->media_storage->getImageURL($logo_image); ?>" alt="<?php echo $this->customlib->getAppName(); ?>">
            <?php if ($is_valid): ?>
                <h1 class="share-card__title"><?php echo $share_data->title; ?></h1>
            <?php endif; ?>
        </div>

        <?php if ($is_valid): ?>

            <div class="share-card__meta">
                <span class="share-chip">
                    <i class="fas fa-calendar-day"></i>
                    <b><?php echo $this->lang->line('upload_date'); ?></b> :
                    <?php echo $this->customlib->YYYYMMDDTodateFormat($share_data->share_date); ?>
                </span>
                <span class="share-chip">
                    <i class="fas fa-clock"></i>
                    <b><?php echo $this->lang->line('valid_upto'); ?></b> :
                    <?php echo $this->customlib->YYYYMMDDTodateFormat($share_data->valid_upto); ?>
                </span>
                <span class="share-chip">
                    <i class="fas fa-user-md"></i>
                    <b><?php echo $this->lang->line('shared_by'); ?></b> :
                    <?php echo $share_data->name . ' ' . $share_data->surname . ' (' . $share_data->employee_id . ')'; ?>
                </span>
            </div>

            <div class="share-card__body">
                <?php if (!empty($share_data->upload_contents)) : ?>

                    <?php foreach ($share_data->upload_contents as $content_value) :
                        $ft = strtolower($content_value->file_type);
                        $image_exts = ['gif','jpeg','jpg','jpe','jp2','j2k','jpf','jpg2','jpx','jpm','mj2','mjp2','png','tiff','tif'];
                        $video_exts = ['3g2','3gp','mp4','m4a','f4v','flv','webm'];

                        if (in_array($ft, ['xls','xlsx'], true))          { $cls = 'is-xls'; $icon = 'fa-file-excel';      $label = strtoupper($ft); }
                        elseif (in_array($ft, ['ppt','pptx'], true))      { $cls = 'is-ppt'; $icon = 'fa-file-powerpoint'; $label = strtoupper($ft); }
                        elseif (in_array($ft, ['doc','docx'], true))      { $cls = 'is-doc'; $icon = 'fa-file-word';       $label = strtoupper($ft); }
                        elseif ($ft === 'csv')                            { $cls = 'is-csv'; $icon = 'fa-file-csv';        $label = 'CSV'; }
                        elseif ($ft === 'pdf')                            { $cls = 'is-pdf'; $icon = 'fa-file-pdf';        $label = 'PDF'; }
                        elseif ($ft === 'text/plain' || $ft === 'txt')    { $cls = 'is-txt'; $icon = 'fa-file-alt';        $label = 'TXT'; }
                        elseif (in_array($ft, ['zip','rar'], true))       { $cls = 'is-zip'; $icon = 'fa-file-archive';    $label = strtoupper($ft); }
                        elseif (in_array($ft, $image_exts, true))         { $cls = 'is-img'; $icon = 'fa-image';           $label = strtoupper($ft); }
                        elseif (in_array($ft, $video_exts, true))         { $cls = 'is-vid'; $icon = 'fa-video';           $label = strtoupper($ft); }
                        elseif ($ft === 'video')                          { $cls = 'is-vid'; $icon = 'fa-video';           $label = 'VIDEO'; }
                        else                                              { $cls = 'is-file'; $icon = 'fa-file';            $label = strtoupper($ft); }
                    ?>

                        <?php if ($content_value->file_type == 'video') : ?>
                            <a class="share-file" href="<?php echo $content_value->vid_url; ?>" target="_blank" rel="noopener">
                                <div class="share-file__icon <?php echo $cls; ?>"><i class="fas <?php echo $icon; ?>"></i></div>
                                <div class="share-file__name"><?php echo $content_value->vid_title; ?></div>
                                <div class="share-file__type"><?php echo $label; ?></div>
                                <div class="share-file__download"><i class="fas fa-external-link-alt"></i></div>
                            </a>
                        <?php else : ?>
                            <a class="share-file" title="<?php echo $this->lang->line('download'); ?>" href="<?php echo site_url('site/download_content/' . $content_value->upload_content_id . '/' . $this->enc_lib->encrypt($content_value->share_content_id)); ?>">
                                <div class="share-file__icon <?php echo $cls; ?>">
                                    <?php if (in_array($ft, $image_exts, true) && !empty($content_value->thumb_name)) : ?>
                                        <img src="<?php echo $this->media_storage->getImageURL($content_value->thumb_path . $content_value->thumb_name); ?>" alt="">
                                    <?php else : ?>
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="share-file__name"><?php echo $this->media_storage->fileview($content_value->img_name); ?></div>
                                <div class="share-file__type"><?php echo $label; ?></div>
                                <div class="share-file__download"><i class="fas fa-arrow-down"></i></div>
                            </a>
                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php else : ?>

                    <div class="share-empty">
                        <div class="share-empty__icon"><i class="fas fa-folder-open"></i></div>
                        <p class="share-empty__text"><?php echo $this->lang->line('no_record_found'); ?></p>
                    </div>

                <?php endif; ?>
            </div>

        <?php else : ?>

            <div class="share-expired">
                <div class="share-expired__icon"><i class="fas fa-unlink"></i></div>
                <h2 class="share-expired__title"><?php echo $this->lang->line('this_link_is_invalid_or_expired'); ?></h2>
            </div>

        <?php endif; ?>

        <div class="share-card__foot">
            &copy; <?php echo date('Y'); ?> <?php echo $this->customlib->getAppName(); ?>
        </div>

    </div>
</div>

<script src="<?php echo base_url(); ?>backend/bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>

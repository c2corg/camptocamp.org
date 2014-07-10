<?php
use_helper('MyMinify');
$static_base_url = sfConfig::get('app_static_url');
?>
<!doctype html>
<html lang="<?php echo __('meta_language') ?>">
<head>
    <?php echo include_http_metas(); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo minify_get_combined_files_url(array('/static/css/default.css', '/static/css/popup.css')); ?>" />
    <!-- FIXME this is hacky but we don't want to have a min width of 1000px for our popup when embbedded in 3rd party website -->
    <style>
      html, body { min-width: 200px; }
    </style>
    <title><?php echo __('Mini card Camptocamp.org') ?></title>
</head>
<body>
<div class="popup_content external">
    <?php echo $sf_data->getRaw('sf_content');
    $logo = '<div class="popup_logo logo_mini"></div>';
    echo link_to($logo, '@homepage', array('target' => '_blank'));
    ?>
</div>
</body>
</html>

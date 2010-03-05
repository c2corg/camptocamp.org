<?php
$static_base_url = sfConfig::get('app_static_url');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <?php echo include_http_metas(); ?>
    <link rel="shortcut icon" href="<?php echo $static_base_url ?>/static/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url ?>/static/css/main.css?<?php echo sfSVN::getHeadRevision('main.css') ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $static_base_url ?>/static/css/popup.css?<?php echo sfSVN::getHeadRevision('popup.css') ?>" />
    <title><?php echo __('Mini card Camptocamp.org') ?></title>
</head>
<body>
<div id="popup_contentDiv" class="external">
    <?php echo $sf_data->getRaw('sf_content');
    $logo = '<div id="popup_logo" class="logo_mini"></div>';
    echo link_to($logo, '@homepage', array('target' => '_blank'));
    ?>
</div>
</body>
</html>

<?php 
$sf_response->addStylesheet(PUN_STATIC_URL . '/static/css/main.css');
$sf_response->addStylesheet(PUN_STATIC_URL . '/static/css/menu.css');
$sf_response->addStylesheet(PUN_STATIC_URL . '/static/css/handheld.css', array('media' => 'handheld'));
$sf_response->addStylesheet(PUN_STATIC_URL . '/sfModalBoxPlugin/css/modalbox.css');

$sf_response->addJavascript(PUN_STATIC_URL . '/sfPrototypePlugin/js/prototype.js', 'head_first');
$sf_response->addJavascript(PUN_STATIC_URL . '/sfPrototypePlugin/js/scriptaculous.js', 'head');
$sf_response->addJavascript(PUN_STATIC_URL . '/static/js/submit.js');

sfLoader::loadHelpers(array('Helper', 'MyMinify', 'Asset'));
minify_include_stylesheets(!PUN_DEBUG, PUN_DEBUG);
minify_include_head_javascripts(!PUN_DEBUG, PUN_DEBUG);
?>
<!--[if !IE]>-->
<link type="text/css" rel="stylesheet" media="only screen and (max-device-width: 480px)" href="<?php echo PUN_STATIC_URL; ?>/static/css/handheld.css?<?php echo sfSVN::getHeadRevision('handheld.css') ?>" />
<!--<![endif]-->
<!--[if lt IE 7]>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo PUN_STATIC_URL; ?>/static/css/ie.css?<?php echo sfSVN::getHeadRevision('ie.css') ?>" />
<![endif]-->
<!--[if IE 7]>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo PUN_STATIC_URL; ?>/static/css/ie7.css?<?php echo sfSVN::getHeadRevision('ie7.css') ?>" />
<![endif]-->
<link href="<?php echo PUN_STATIC_URL; ?>/static/images/favicon.ico" rel="shortcut icon"/>

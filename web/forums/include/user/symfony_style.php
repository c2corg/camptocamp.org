<?php
$mobile_version = c2cTools::mobileVersion();
 
$sf_response->addStylesheet('/static/css/main.css', 'first');
$sf_response->addStylesheet('/static/css/img.css');
if (!$mobile_version)
{
    $sf_response->addStylesheet('/static/css/img_s.css');
    $sf_response->addStylesheet('/static/css/menu.css');
    $sf_response->addStylesheet('/static/css/modalbox.css');
}
else
{
    $sf_response->addStylesheet('/static/css/mobile.css', 'last');
}

$sf_response->addJavascript('/static/js/prototype.js', 'head_first');
$sf_response->addJavascript('/static/js/effects.js', 'head');
$sf_response->addJavascript('/static/js/controls.js', 'head');
$sf_response->addJavascript('/static/js/submit.js');

sfLoader::loadHelpers(array('Helper', 'MyMinify', 'Asset'));
$debug = defined('PUN_DEBUG');
minify_include_main_stylesheets(!$debug, $debug);
minify_include_head_javascripts(!$debug, $debug);

if (!$mobile_version): ?>
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="all" href="<?php echo PUN_STATIC_URL . '/' . sfTimestamp::getTimestamp(PUN_STATIC_URL . '/static/css/ie6.css'); ?>/static/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo PUN_STATIC_URL . '/' . sfTimestamp::getTimestamp(PUN_STATIC_URL . '/static/css/ie7.css'); ?>/static/css/ie7.css" /><![endif]-->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?php else: ?>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<link rel="apple-touch-icon" href="<?php echo PUN_STATIC_URL; ?>/static/images/apple-touch-icon.png" />
<link rel="apple-touch-icon-precomposed" href="<?php echo PUN_STATIC_URL; ?>/static/images/apple-touch-icon.png" />
<?php endif; ?>
<link href="<?php echo PUN_STATIC_URL; ?>/static/images/favicon.ico" rel="shortcut icon"/>

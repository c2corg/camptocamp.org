<?php
$mobile_version = c2cTools::mobileVersion();

if (!$mobile_version)
{
    $sf_response->addStylesheet('/static/css/default.css', 'first');
    $sf_response->addStylesheet('/static/css/menu.css');
    $sf_response->addStylesheet('/static/css/modalbox.css');
}
else
{
    $sf_response->addStylesheet('/static/css/mobile.css', 'last');
}

if (sfContext::getInstance()->getUser()->getCulture() == 'en')
{
    $sf_response->addStylesheet('/static/css/ac.css');
}

$sf_response->addJavascript('/static/js/prototype.js', 'head_first');
$sf_response->addJavascript('/static/js/effects.js', 'head');
$sf_response->addJavascript('/static/js/controls.js', 'head');
$sf_response->addJavascript('/static/js/submit.js');
if ($mobile_version)
{
    $sf_response->addJavascript('/static/js/viewport_fix.js');
}

sfLoader::loadHelpers(array('Helper', 'MyMinify', 'Asset'));
$debug = defined('PUN_DEBUG');

if ($mobile_version): ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script type="text/javascript">
(function(m){var l='<?php echo trim(minify_get_main_stylesheets(!$debug, $debug)); ?>',r=window.devicePixelRatio||1;
if(r>1){l=l.replace(m,m+'@'+(r>=2?2:1.5)+'x');}document.write(l);})('mobile');
</script>
<?php else:
minify_include_main_stylesheets(!$debug, $debug);
endif;

minify_include_head_javascripts(!$debug, $debug);

if (!$mobile_version): ?>
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php echo  minify_get_combined_files_url('/static/css/ie7.css') ?>" /><![endif]-->
<!--[if lt IE 9]><script src="<?php echo minify_get_combined_files_url(array('/static/js/html5shiv.js','/static/js/autofocus.js', '/static/js/indexof.js')) ?>"></script><![endif]-->
<link rel="alternate" media="only screen and (max-width: 640px)" href="http://<?php echo sfConfig::get('app_mobile_version_host').$_SERVER['REQUEST_URI']; ?>" />
<?php else: ?>
<link rel="apple-touch-icon" href="<?php echo PUN_STATIC_URL; ?>/static/images/apple-touch-icon.png" />
<link rel="apple-touch-icon-precomposed" href="<?php echo PUN_STATIC_URL; ?>/static/images/apple-touch-icon.png" />
<link rel="canonical" href="http://<?php echo sfConfig::get('app_classic_version_host').$_SERVER['REQUEST_URI']; ?>" />
<?php endif; ?>
<link href="<?php echo PUN_STATIC_URL; ?>/static/images/favicon.ico" rel="shortcut icon" />

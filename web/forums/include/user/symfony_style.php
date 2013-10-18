<?php
$mobile_version = c2cTools::mobileVersion();

if (!$mobile_version)
{
    $sf_response->addStylesheet('/static/css/default.css', 'first');
    $sf_response->addStylesheet('/static/css/menu.css');
    $sf_response->addStylesheet('/static/css/modal.css');
}
else
{
    $sf_response->addStylesheet('/static/css/mobile.css', 'last');
}

if ($sf_user->getCulture() == 'en')
{
    $sf_response->addStylesheet('/static/css/ac.css');
}

sfLoader::loadHelpers(array('Helper', 'MyMinify', 'Asset'));
$debug = defined('PUN_DEBUG');

if ($mobile_version): ?>
<meta name="viewport" content="initial-scale=1" />
<script type="text/javascript">
(function(m,w){var l='<?php echo trim(minify_get_main_stylesheets(!$debug, $debug)); ?>',r=1
w.devicePixelRatio?r=w.devicePixelRatio:"matchMedia"in w&&w.matchMedia&&(w.matchMedia("(min-resolution: 2dppx)").matches||w.matchMedia("(min-resolution: 192dpi)").matches?r=2:(w.matchMedia("(min-resolution: 1.5dppx)").matches||w.matchMedia("(min-resolution: 144dpi)").matches)&&(r=1.5))
if(r>1){l=l.replace(m,m+'@'+(r>=2?2:1.5)+'x');}document.write(l);})('mobile',window);
</script>
<?php else:
minify_include_main_stylesheets(!$debug, $debug);
endif;

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

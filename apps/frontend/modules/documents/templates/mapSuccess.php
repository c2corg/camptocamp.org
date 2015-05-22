<?php
use_helper('Javascript');
$lang = $sf_user->getCulture();
$connected = $sf_user->isConnected() ? 'true' : 'false';

if ($debug)
{
    include_partial('documents/map_lib_include_debug');
}
else
{
    use_stylesheet('/static/css/carto_base.css', 'custom');
    use_stylesheet('/static/css/popup.css', 'custom');
    use_javascript('/static/js/carto/build/carto.min.js', 'maps');
}
use_stylesheet('/static/css/carto.css', 'custom');
use_stylesheet('/static/css/viewer.css', 'custom');
use_javascript("/static/js/carto/build/lang-$lang.min.js", 'maps');
use_javascript('/static/js/popup.js', 'maps');
use_javascript('/static/js/carto/viewer.js', 'maps');

echo javascript_tag("var map_connected = $connected;");
?>

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo  sfConfig::get('app_static_url') ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>
</div>

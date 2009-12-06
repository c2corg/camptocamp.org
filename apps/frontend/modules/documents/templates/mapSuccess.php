<?php
$app_static_url = sfConfig::get('app_static_url');

use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'first');
use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'first');
use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'first');

use_stylesheet($app_static_url . '/static/js/mapfish/MapFishApi/css/api.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/c2corgApi/css/api.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/css/c2corg.css', 'last');

if ($debug) {
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/prototype/ext-prototype-adapter.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/config.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/firefoxfix.js', 'last');

    use_javascript($app_static_url . '/static/js/mapfish/mfbase/openlayers/lib/OpenLayers.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/geoext/lib/GeoExt.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/mapfish/MapFish.js', 'last');

    use_javascript($app_static_url . '/static/js/mapfish/geoportal/GeoportalMin.js', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/mapfish_api.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Measure.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ZoomToExtent.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Permalink.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ArgParser.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/c2corg_api.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/ArgParser.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/tooltip.js', 'last');

    // FIXME: adapt to current language
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/openlayers/lib/OpenLayers/Lang/fr.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/mapfish/lang/fr.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/source/locale/ext-lang-fr.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/lang/fr.js', 'last');

    use_javascript($app_static_url . '/static/js/mapfish/app/layout.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/app/query.js', 'last');
} else {
    use_javascript($app_static_url . '/static/js/mapfish/build/c2corgApi.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/build/app.js', 'last');
}
?>

<div id="mapinfo">
  <div id="mousepos"></div>
  <div id="scale"></div>
</div>

<div id="linkContainer" style="display:none;" class="exportContainer"></div>

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo $app_static_url ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>

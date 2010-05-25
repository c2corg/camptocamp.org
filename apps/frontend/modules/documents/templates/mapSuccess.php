<?php
use_helper('Javascript');
$app_static_url = sfConfig::get('app_static_url');
$lang = $sf_user->getCulture();

use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/openlayers/theme/default/style.css', 'last');

use_stylesheet($app_static_url . '/static/js/mapfish/MapFishApi/css/api.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/c2corgApi/css/api.css', 'last');
use_stylesheet($app_static_url . '/static/js/mapfish/css/c2corg.css', 'last');

// tooltip popup content JS & CSS files
use_stylesheet($app_static_url . '/static/css/popup.css', 'last');
use_javascript($app_static_url . '/static/js/popup.js', 'last');

echo javascript_tag("lang = '$lang';");

use_javascript('http://maps.google.com/maps?file=api&v=3&key=' . sfConfig::get('app_gmaps_key'));
use_javascript('http://api.ign.fr/api?v=1.0beta4-m&key=' . sfConfig::get('app_geoportail_key') . '&includeEngine=false');     

if ($debug) {
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/config.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/firefoxfix.js', 'nominify');

    use_javascript($app_static_url . '/static/js/mapfish/mfbase/openlayers/lib/OpenLayers.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/geoext/lib/GeoExt.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/mapfish/MapFish.js', 'nominify');
    
    if ($lang != 'eu') {
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/openlayers/lib/OpenLayers/Lang/$lang.js", 'nominify');
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/ext/source/locale/ext-lang-$lang.js", 'nominify');
    }
    if (!in_array($lang, array('ca', 'eu'))) {
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/mapfish/lang/$lang.js", 'nominify');
    }

    use_javascript($app_static_url . '/static/js/mapfish/geoportal/GeoportalMin.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/geoportal/Logo.js', 'nominify');
    
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/mapfish_api.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Measure.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ZoomToExtent.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Permalink.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ArgParser.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/GeonamesSearchCombo.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/c2corg_api.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/ArgParser.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/Permalink.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/tooltip.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/app/layout.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/app/query.js', 'nominify');
} else {
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all.js', 'nominify');
    
    use_javascript($app_static_url . '/static/js/mapfish/build/c2corgApi.js', 'nominify');
    use_javascript($app_static_url . '/static/js/mapfish/build/app.js', 'nominify');
}

include_partial('documents/map_i18n');
?>

<div id="mapinfo">
  <div id="mousepos"></div>
  <div id="scale"></div>
</div>

<div id="linkContainer" style="display:none;" class="exportContainer"></div>

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo $app_static_url ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>

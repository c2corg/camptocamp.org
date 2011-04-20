<?php
use_helper('Javascript');
$lang = $sf_user->getCulture();

use_stylesheet('/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'last');
use_stylesheet('/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'last');
use_stylesheet('/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'last');
use_stylesheet('/static/js/mapfish/mfbase/openlayers/theme/default/style.css', 'last');

use_stylesheet('/static/js/mapfish/MapFishApi/css/api.css', 'last');
use_stylesheet('/static/js/mapfish/c2corgApi/css/api.css', 'last');
use_stylesheet('/static/js/mapfish/css/c2corg.css', 'last');

// tooltip popup content JS & CSS files
use_stylesheet('/static/css/popup.css', 'last');
use_javascript('/static/js/popup.js', 'last');

echo javascript_tag("lang = '$lang';");

use_javascript('http://maps.google.com/maps?file=api&v=2&sensor=false&key=' . sfConfig::get('app_google_maps_key'));
use_javascript('http://api.ign.fr/api?v=1.1-m&key=' . sfConfig::get('app_geoportail_key') . '&includeEngine=false');     

if ($debug) {
    use_javascript('/static/js/ie9mapfix.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/config.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/firefoxfix.js', 'maps');

    use_javascript('/static/js/mapfish/mfbase/openlayers/lib/OpenLayers.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/geoext/lib/GeoExt.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/mapfish/MapFish.js', 'maps');
    
    if ($lang != 'eu') {
        use_javascript("/static/js/mapfish/mfbase/openlayers/lib/OpenLayers/Lang/$lang.js", 'maps');
        use_javascript("/static/js/mapfish/mfbase/ext/source/locale/ext-lang-$lang.js", 'maps');
    }
    if (!in_array($lang, array('ca', 'eu'))) {
        use_javascript("/static/js/mapfish/mfbase/mapfish/lang/$lang.js", 'maps');
    }

    use_javascript('/static/js/mapfish/geoportal/GeoportalMin.js', 'maps');
    use_javascript('/static/js/mapfish/geoportal/Logo.js', 'maps');
    
    use_javascript('/static/js/mapfish/MapFishApi/js/mapfish_api.js', 'maps');
    use_javascript('/static/js/mapfish/MapFishApi/js/Measure.js', 'maps');
    use_javascript('/static/js/mapfish/MapFishApi/js/ZoomToExtent.js', 'maps');
    use_javascript('/static/js/mapfish/MapFishApi/js/Permalink.js', 'maps');
    use_javascript('/static/js/mapfish/MapFishApi/js/ArgParser.js', 'maps');
    use_javascript('/static/js/mapfish/MapFishApi/js/GeonamesSearchCombo.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/c2corg_api.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/ArgParser.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/Permalink.js', 'maps');
    use_javascript('/static/js/mapfish/c2corgApi/js/tooltip.js', 'maps');
    use_javascript('/static/js/mapfish/app/layout.js', 'maps');
    use_javascript('/static/js/mapfish/app/query.js', 'maps');
} else {
    use_javascript('/static/js/ie9mapfix.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/ext/ext-all.js', 'maps');
    
    use_javascript('/static/js/mapfish/build/c2corgApi.js', 'maps');
    use_javascript('/static/js/mapfish/build/app.js', 'maps');
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

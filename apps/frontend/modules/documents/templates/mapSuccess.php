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

if ($debug) {
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/config.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/firefoxfix.js', 'last');

    use_javascript($app_static_url . '/static/js/mapfish/mfbase/openlayers/lib/OpenLayers.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/geoext/lib/GeoExt.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/mapfish/MapFish.js', 'last');
    
    if ($lang != 'eu') {
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/openlayers/lib/OpenLayers/Lang/$lang.js", 'last');
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/ext/source/locale/ext-lang-$lang.js", 'last');
    }
    if (!in_array($lang, array('ca', 'eu'))) {
        use_javascript($app_static_url . "/static/js/mapfish/mfbase/mapfish/lang/$lang.js", 'last');
    }

    use_javascript($app_static_url . '/static/js/mapfish/geoportal/GeoportalMin.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/geoportal/Logo.js', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/mapfish_api.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Measure.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ZoomToExtent.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/Permalink.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/ArgParser.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/MapFishApi/js/GeonamesSearchCombo.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/c2corg_api.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/ArgParser.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/c2corgApi/js/tooltip.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/app/layout.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/app/query.js', 'last');
} else {
    // TODO: use cachefly cdn?
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all.js', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/build/c2corgApi.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/build/app.js', 'last');
}

echo javascript_tag("
c2corg_map_translations = {
    'Map data': \"" . __('Map data') . "\",
    'Search': \"" . __('Search') . "\",
    'Help': \"" . __('Help') . "\",
    'help detail': \"" . __('map help text') . "\",
    'no item selected': \"" . __('no item selected on map') . "\",
    'Expand map': \"" . __('Expand map') . "\",
    'Reduce map': \"" . __('Reduce map') . "\",
    'longitude / latitude: ': \"" . __('longitude / latitude: ') . "\",
    'c2c data': \"" . __('c2c map data') . "\",
    'summits': \"" . __('summits') . "\",
    'parkings': \"" . __('parkings') . "\",
    'huts': \"" . __('huts') . "\",
    'sites': \"" . __('sites') . "\",
    'users': \"" . __('users') . "\",
    'images': \"" . __('images') . "\",
    'routes': \"" . __('routes') . "\",
    'outings': \"" . __('outings') . "\",
    'ranges': \"" . __('ranges') . "\",
    'maps': \"" . __('maps') . "\",
    'areas': \"" . __('areas') . "\",
    'countries': \"" . __('countries') . "\",
    'admin boundaries': \"" . __('admin_limits') . "\",
    'Backgrounds': \"" . __('backgrounds') . "\",
    'Physical': \"" . __('relief') . "\",
    'Hybrid': \"" . __('hybrid') . "\",
    'Normal': \"" . __('Google maps') . "\",
    'OpenStreetMap': 'OpenStreetMap',
    'IGN maps': \"" . __('IGN maps') . "\",
    'IGN orthos': \"" . __('IGN orthos') . "\",
    'Clear': \"" . __('Clear') . "\",
    'max extent': \"" . __('max extent') . "\",
    'pan': \"" . __('pan') . "\",
    'zoom box': \"" . __('zoom in') . "\",
    'previous': \"" . __('previous map') . "\",
    'next': \"" . __('next map') . "\",
    'length measure': \"" . __('length measure') . "\",
    'Measure': \"" . __('Distance') . "\",
    'map query': \"" . __('map query') . "\",
    'Go to...': \"" . __('Go to...') . "\",
    'Please wait...': \"" . __(' loading...') . "\",\n" .
    '\'${nb_items} items. Click to show info\': "' . __('${nb_items} items. Click to show info') . "\",
    'Map URL': \"" . __('Map URL') . "\"
};");
?>

<div id="mapinfo">
  <div id="mousepos"></div>
  <div id="scale"></div>
</div>

<div id="linkContainer" style="display:none;" class="exportContainer"></div>

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo $app_static_url ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>

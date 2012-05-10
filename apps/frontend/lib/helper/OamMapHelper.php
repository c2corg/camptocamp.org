<?php
use_helper('Javascript', 'I18N');

function show_georef_map($lon, $lat, $lang, $layer)
{
    include_partial('documents/map_i18n');
    
    if (empty($layer))
    {
        $layer = sfContext::getInstance()->getModuleName();
    }
    $js = "var mapLang = '$lang',
lon_field_id = 'lon',
lat_field_id = 'lat',
layersList = ['$layer'],
mapContainer = 'georef_container';";
    if (!$lon && !$lat && !in_array($layer, array('sites', 'users', 'images', 'portals'))) // TODO this is broken with ie, do not launch it automatically in this case
    {
        $js .= "\nif(!Prototype.Browser.IE) {Event.observe(window, 'load', function(){c2corg.docGeoref.init(0,0);});}";
    }
    $html = javascript_tag($js);
    
    $html .= '<div class="section" id="georef_container" style="display:none;">';
    $html .= '<div id="map" style="height:400px;width:100%">';
    $html .= '<div id="mapLoading">'.image_tag(sfConfig::get('app_static_url') . '/static/images/indicator.gif');
    $html .= __('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '<div id="scale"></div>';
    $html .= '<div id="fake_clear"></div>';
    $html .= '</div>';

    return $html;
}

function _loadJsOamTools()
{
    $response = sfContext::getInstance()->getResponse();
    
    use_stylesheet('/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'last');
    use_stylesheet('/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'last');
    use_stylesheet('/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'last');
    use_stylesheet('/static/js/mapfish/mfbase/openlayers/theme/default/style.css', 'last');

    use_stylesheet('/static/js/mapfish/MapFishApi/css/api.css', 'last');
    use_stylesheet('/static/js/mapfish/c2corgApi/css/api.css', 'last');

    // it is not possible to load google maps api v2 asynchronously since it uses document.write
    // upgrade to v3 to enable (using &callback=some_function param) TODO
    // We use api <= 3.6 https://github.com/openlayers/openlayers/commit/b17c7b69f25ce0ddbaf720f91b7d48328b005831
    //use_javascript('http://maps.googleapis.com/maps/api/js?v=3.6&sensor=false&key=' . sfConfig::get('app_google_maps_key'));
    use_javascript('http://maps.google.com/maps?file=api&v=2&sensor=false&key=' . sfConfig::get('app_google_maps_key'));

    // FIXME following files will only be loaded by internet explorer when in async mode using conditional comments
    // (extjs 2 cannot be loaded async with ie, it uses document.write)
    use_javascript('/static/js/ie9mapfix.js', 'maps');
    use_javascript('/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'last');
    use_javascript('/static/js/mapfish/mfbase/ext/ext-all.js', 'last');
    //use_javascript('/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'maps');
    
    use_javascript('/static/js/mapfish/build/c2corgApi.js', 'last');
    use_javascript('/static/js/docgeoref.js', 'last');
    
    // FIXME: use "maps" instead of "last"?

}

_loadJsOamTools();
?>

<?php
use_helper('Form', 'Javascript');

function show_map($container_div, $document)
{
    // define div identifiers
    $map_container_div_id   = $container_div . '_section_container';
    $static_base_url = sfConfig::get('app_static_url');
    
    // TODO: get interface language
    
    // TODO: handle non point objects
    $lon = $document->get('lon');
    $lat = $document->get('lat');
    $zoom = 12;
    $html = javascript_tag("var objectCoords = { lon: $lon, lat: $lat, zoom: $zoom };");
    
    $html .= '<div class="section" id="' . $map_container_div_id . '"><div class="article_contenu">';
    $html .= '<div id="map" style="height:300px;width:100%">';
    $html .= '<div id="mapLoading"><img src="' . $app_static_url . '/static/images/indicator.gif" alt="" />';
    $html .= _('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '<div id="scale"></div>';
    $html .= '<div id="fake_clear"></div>';
    $html .= '</div></div>';

    return $html;
}

function _loadJsMapTools()
{
    $response = sfContext::getInstance()->getResponse();

    $static_base_url = sfConfig::get('app_static_url');

    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/openlayers/theme/default/style.css', 'last');

    use_stylesheet($app_static_url . '/static/js/mapfish/MapFishApi/css/api.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/c2corgApi/css/api.css', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all.js', 'last');
    //use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/build/c2corgApi.js', 'last');
    use_javascript($app_static_url . '/static/js/docmap.js', 'last');
}

_loadJsMapTools();
?>

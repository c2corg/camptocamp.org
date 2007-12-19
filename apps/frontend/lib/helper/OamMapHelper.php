<?php
use_helper('Javascript', 'I18N');

function show_oam($lon, $lat)
{
    if ($lon && $lat) 
    {
        $js_code = 
        "orig_lon = $lon;
         orig_lat = $lat;";
    }
    else 
    {
        $lon = 0; 
        $lat = 0;
        $js_code = '';
    }
    
	$js_code .= "
        lon_field_id   = 'lon';
        lat_field_id   = 'lat';
        revert_btn_id  = 'revert_btn';
        update_btn_id  = 'update_btn';
        mapping_div_id = 'mapping';
    ";

    $html = '<div id="buttons" style="height: 25px;">';
    $html .= '<button id="update_btn" style="display: none;" onclick="update_point(\''.__('Empty coordinates: please update longitude and/or latitude').'\'); return false;">'.__('update map').'</button>';
    $html .= ($lon && $lat) ? '<button id="revert_btn" style="display: none;" onclick="revert(); return false;">'.__('revert').'</button>' : '';
    $html .= '</div>';
    $html .= '<div style="width: 424px; height: 300px" id="map"></div>';
    $html .= '<div style="z-index: 1000; margin: 5px;"><input type="checkbox" id="osm" checked="checked" onclick="toggle_osm(this.checked);" />';
    $html .= __('Display OpenStreetMap data') . '</div>';
    $html .= javascript_tag($js_code);

    return $html;
}

function _loadJsOamTools()
{
    $response = sfContext::getInstance()->getResponse();

    // OpenLayers
    $response->addJavascript('/static/js/openlayers_sfl/OpenLayers', 'first');
    //$response->addJavascript('/static/js/openlayers/lib/OpenLayers', 'first'); // for debugging purpose
    
    // App-specific
    $response->addJavascript('/static/js/oam_mapping', 'first');

    $response->addStyleSheet('/static/css/openlayers.css', '', array('media' => 'all'));
}

_loadJsOamTools();
?>

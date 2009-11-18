<?php
use_helper('Form', 'Javascript');

function show_map($search, $layers, $objects_to_mark, $search_url, $container_div, $tip_close)
{
    // define div identifiers
    $map_container_div_id   = $container_div . '_section_container';
    $map_div_id             = 'map';
    $layer_tree_div_id      = 'article_droite_boxs';
    $search_form_id         = 'search_form';
    
	$query_url = url_for('@query?module=documents');   
    $json_service = new Services_JSON();
    $layers_json = $json_service->encode($layers);
    $objects_to_mark = is_null($objects_to_mark) ? 'null' : "'$objects_to_mark'";
	
	$js_code = "
        div_id_container        = '$container_div';
        div_id_map_container    = '$map_container_div_id';
        div_id_map              = '$map_div_id';
        div_id_tree             = '$layer_tree_div_id';
        tip_close               = '$tip_close';
        form_id_search          = '$search_form_id';
        query_url               = '$query_url';
        layers_json             = $layers_json;
        objects_to_mark         = $objects_to_mark;
        search_url              = '$search_url';
    ";
    $js_code .= 'query_activated = ' . ($search ? 'true' : 'false') . ';';

    $html  = javascript_tag($js_code);
    $html .= '<div style="display:none;" id="' . $map_container_div_id . '"><div class="article_contenu">';
    if ($search)
    {
        $html .= label_for('activate_map_search', __('Activate map search'));
        $html .= checkbox_tag('activate_map_search', '1', false, array("onchange" => "toggle_query(this); return false;"));
    }
    $html .= '<div id="article_gauche_carte" class="carte">';
    $html .= '<div id="' . $map_div_id . '" style="height:100%;width:100%"></div>';
    $html .= '</div>';
    $html .= '<div id="' . $layer_tree_div_id . '"></div>';
    $html .= '<div id="fake_clear"></div>';
    $html .= '</div></div>';

    return $html;
}

function _loadJsMapTools()
{
    $response = sfContext::getInstance()->getResponse();

    $static_base_url = sfConfig::get('app_static_url');
    $prototype_url = $static_base_url . sfConfig::get('sf_prototype_web_dir') . '/js/';

    // scriptaculous & prototype: no need, it is loaded for modalbox in each page (menu)
    //$response->addJavascript($prototype_url . 'prototype.js?', 'head_first');
    //$response->addJavascript($prototype_url . 'scriptaculous.js?', 'head');
    

    // added 'first' to solve conflict with scriptaculous autocompletion:
    
    // OpenLayers
    $response->addJavascript($static_base_url . '/static/js/openlayers_sfl/OpenLayers.js', 'head');
    //$response->addJavascript($static_base_url . '/static/js/openlayers/lib/OpenLayers.js', 'head'); // for debugging purpose
    
    // CartoWeb
    $response->addJavascript($static_base_url . '/static/js/cartoweb/lib/CartoWeb.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/cartoweb/lib/LayerManager.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/cartoweb/lib/Search.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/cartoweb/lib/Query.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/cartoweb/lib/Query/Extent.js', 'first');
    
    // App-specific
    $response->addJavascript($static_base_url . '/static/js/MousePositionLonLat.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/mapping.js', 'head'); // MUST be in head

    $response->addStyleSheet($static_base_url . '/static/css/openlayers.css', '', array('media' => 'all'));

    // Minify fails to handle the following file because it's on the web, not local !
    // This is the reason why SfMinifyHelper has been customized to our needs:
    // if the helper detects the inclusion of an http file, it loads it separately.
    $gmap_js = 'http://maps.google.com/maps?file=api&amp;v=2&amp;key='.sfConfig::get('app_gmaps_key');
    $response->addJavascript($gmap_js, 'first');
}

_loadJsMapTools();
?>

<?php
use_helper('Form', 'Javascript');

function show_map($container_div, $document, $lang, $layers_list = null, $height = null, $center = null, $has_geom = null)
{
    include_partial('documents/map_i18n');

    // define div identifiers
    $map_container_div_id   = $container_div . '_section_container';
    $app_static_url = sfConfig::get('app_static_url');
    
    $objects_list = array();
    if ($has_geom or is_null($has_geom))
    {
        if ($document->get('geom_wkt') != null)
        {
            $objects_list[] = sprintf("{id: %d, type: '%s', wkt: '%s'}",
                                      $document->get('id'), $document->get('module'), $document->get('geom_wkt'));
        }
    }
    if ($document->get('module') == 'routes')
    {
        foreach(array('summits', 'parkings', 'huts') as $type)
        {
            if (!isset($document->$type)) continue;
            _addAssociatedDocsWithGeom($document->$type, $objects_list);
        }
    }
    
    if (is_null($layers_list))
    {
        $layers_list = '[]';
    } else {
        if (!is_array($layers_list))
        {
            $layers_list = str_replace(' ', '', $layers_list);
            $layers_list = explode(',', $layers_list);
        }
        $layers_list = "['" . implode("','", $layers_list) . "']";
    }
    
    if (is_null($height))
    {
        $height = 300;
    }
    else
    {
        $height = min(800, max(300, $height));
    }
    
    if (is_null($center))
    {
        $init_center = '[]';
    }
    else
    {
        if (!is_array($center))
        {
            $center = str_replace(' ', '', $center);
            $center = explode(',', $center);
        }
        $init_center = '[' . implode(', ', $center) . ']';
    }
    
    $html = javascript_tag("var mapLang = '$lang',
    objectsToShow = [" . implode(', ', $objects_list) . "],
    layersList = $layers_list,
    initCenter = $init_center;");
    
    $html .= '<div class="section" id="' . $map_container_div_id . '"><div class="article_contenu">';
    $html .= '<div id="map" style="height:' . $height . 'px;width:100%">';
    $html .= '<div id="mapLoading"><img src="' . $app_static_url . '/static/images/indicator.gif" alt="" />';
    $html .= __('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '<div id="scale"></div>';
    $html .= '<div id="fake_clear"></div>';
    $html .= '</div></div>';

    return $html;
}

function _addAssociatedDocsWithGeom($docs, &$objects_list)
{
    foreach ($docs as $doc)
    {
        if (!empty($doc['pointwkt']))
        {
            $objects_list[] = sprintf("{id: %d, type: '%s', wkt: '%s'}",
                                      $doc['id'], $doc['module'], $doc['pointwkt']);
        }
    }
}

function _loadJsMapTools()
{
    $response = sfContext::getInstance()->getResponse();

    $app_static_url = sfConfig::get('app_static_url');

    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/ext-all.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/ext/resources/css/xtheme-gray.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/geoext/resources/css/gxtheme-gray.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/mfbase/openlayers/theme/default/style.css', 'last');

    use_stylesheet($app_static_url . '/static/css/popup.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/MapFishApi/css/api.css', 'last');
    use_stylesheet($app_static_url . '/static/js/mapfish/c2corgApi/css/api.css', 'last');
    
    use_javascript('http://maps.google.com/maps?file=api&v=3&key=' . sfConfig::get('app_gmaps_key'));
    use_javascript('http://api.ign.fr/api?v=1.0beta4-m&key=' . sfConfig::get('app_geoportail_key') . '&includeEngine=false');
    
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/adapter/ext/ext-base.js', 'last');
    use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all.js', 'last');
    //use_javascript($app_static_url . '/static/js/mapfish/mfbase/ext/ext-all-debug.js', 'last');
    
    use_javascript($app_static_url . '/static/js/mapfish/build/c2corgApi.js', 'last');
    use_javascript($app_static_url . '/static/js/popup.js', 'last');
    use_javascript($app_static_url . '/static/js/docmap.js', 'last');
}

_loadJsMapTools();
?>

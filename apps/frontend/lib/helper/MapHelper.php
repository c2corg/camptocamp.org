<?php
use_helper('Form', 'JavascriptQueue');

function show_map($container_div, $document, $lang, $layers_list = null, $height = null, $center = null, $has_geom = null)
{
    // define div identifiers
    $map_container_div_id   = $container_div . '_section_container';
    $app_static_url = sfConfig::get('app_static_url');
    
    $objects_list = array();
    if ($has_geom or is_null($has_geom))
    {
        if ($document->get('geom_wkt') != null)
        {
            // FIXME
            // When using polygons in openlayers, we have a bug preventing to pan the map when mouse cursor is above
            // a polygon object
            // As a workaround, we have replaced polygons by linestrings in javascript code. This was not working well with
            // multipolygons.
            // Eventually we directly modify this helper to replace MULTIPOLYGON by MULTILINESTRINGS
            $geom = $document->get('geom_wkt');
            if (substr($geom, 0, 7) == 'POLYGON')
            {
                $geom = str_replace('POLYGON', 'MULTILINESTRING', $geom);
            }
            if (substr($geom, 0, 12) == 'MULTIPOLYGON')
            {
                $geom = str_replace(array('MULTIPOLYGON', '((', '))'), array('MULTILINESTRING', '(', ')'), $geom);
            }
            $objects_list[] = _convertObjectToGeoJSON($document->get('id'), $document->get('module'), $geom, $document->getRaw('name'));
        }
    }

    // we display possible associated docs
    foreach(array('summits', 'parkings', 'huts') as $type)
    {
        if (!isset($document->$type)) continue;
        _addAssociatedDocsWithGeom($document->$type, $objects_list);
    }
    
    if (is_null($layers_list))
    {
        $layers_list = '[]';
    }
    else
    {
        if (!is_array($layers_list))
        {
            $layers_list = str_replace(' ', '', $layers_list);
            $layers_list = explode(',', $layers_list);
        }
        $layers_list = "['" . implode("','", $layers_list) . "']";
    }
    
    if (is_null($height))
    {
        $height = 400;
    }
    else
    {
        $height = min(800, max(400, $height));
    }
    
    if (is_null($center))
    {
        $init_center = '[]';
    }
    else
    {
        if (!is_array($center))
        {
            $init_center = str_replace(' ', '', $center);
        }
        else
        {
            $init_center = implode(', ', $center);
        }
        $init_center = '[' . $init_center . ']';
    }
    
    $html  = '<section class="section" id="' . $map_container_div_id . '"><div class="article_contenu">';
    $html .= '<div id="map" style="height:' . $height . 'px;width:auto">';
    $html .= '<div id="mapLoading" style="position:absolute">'.image_tag($app_static_url . '/static/images/indicator.gif');
    $html .= __('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '</div></section>';

    $async_map = sfConfig::get('app_async_map', false) &&
                 !sfContext::getInstance()->getRequest()->getParameter('debug', false);

    $js = "
      C2C.map_init = function() {
        c2corg.Map({
          div: 'map',
          lang: '$lang',
          loading: 'mapLoading',
          layers: $layers_list,
          center: $init_center,
          features: " . _makeFeatureCollection($objects_list) . "
        });
       };";

    // asynchronous map loading
    if ($async_map)
    {
        use_helper('MyMinify');
        $c2c_script_url = minify_get_combined_files_url(
          array('/static/js/carto/build/carto.min.js', "/static/js/carto/build/lang-$lang.min.js",
                '/static/js/popup.js', '/static/js/carto/embedded.js'),
          (bool) sfConfig::get('app_minify_debug'));

        // Ext.onReady doesn't seem to fire if extjs is loaded after the dom has been loaded
        // (but works in chrome???)
        // so we manually trigger it to be sure
        $js .= "
        C2C.async_map_init = function() {
          $.ajax({
            url: '$c2c_script_url',
            dataType: 'script',
            cache: true
          }).done(function() {
            C2C.map_init();
            Ext.EventManager.fireDocReady();
          });
        };";
    }

    $html .= javascript_queue($js);

    return $html;
}

// build a geojson representation of a document
// FIXME it may seem strange to decode geoPHP output, but it is easier for manipulation
//and escaping to keep it as an array and to json_encode at the end. We also need the internal
// representation to be translated to something suitable with geoJson too...
function _convertObjectToGeoJSON($id, $module, $wkt, $name) {
    return array(
        'type' => 'Feature',
        'geometry' => json_decode(geoPHP::load($wkt, 'wkt')->out('json')),
        'id' => $id,
        'properties' => array(
            'module' => $module,
            'name' => $name,
            'label' => in_array($module, array('images', 'sites', 'users', 'huts',
                       'parkings', 'products', 'summits')) ? 'true' : 'false'
        ));
}

function _makeFeatureCollection($features) {
    return json_encode(array(
        'type' => 'FeatureCollection',
        'features' => $features
    ));
}

function _addAssociatedDocsWithGeom($docs, &$objects_list)
{
    foreach ($docs as $doc)
    {
        if (!empty($doc['pointwkt']))
        {
            $objects_list[] = _convertObjectToGeoJSON($doc['id'], $doc['module'], $doc['pointwkt'], $doc->getRaw('name'));
        }
    }
}

function _loadJsMapTools()
{
    $debug = sfContext::getInstance()->getRequest()->getParameter('debug', false);
    $async_map = sfConfig::get('app_async_map', false);
    $lang = sfContext::getInstance()->getUser()->getCulture();

    if ($debug)
    {
        include_partial('documents/map_lib_include_debug');
    }
    else
    {
        use_stylesheet('/static/css/carto_base.css', 'custom');
        use_stylesheet('/static/css/popup.css', 'custom');
        if (!$async_map) use_javascript('/static/js/carto/build/carto.min.js', 'maps');
    }

    use_stylesheet('/static/css/carto.css', 'custom');

    if (!$async_map || $debug)
    {
        use_javascript("/static/js/carto/build/lang-$lang.min.js", 'maps');
        use_javascript('/static/js/popup.js', 'maps');
        use_javascript('/static/js/carto/embedded.js', 'maps');
    }
}

_loadJsMapTools();

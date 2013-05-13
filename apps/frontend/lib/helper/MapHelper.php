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
            $objects_list[] = _convertObjectToGeoJSON($document->get('id'), $document->get('module'), $geom);
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
        function map_init() {
          c2corg.Map({
            div: 'map',
            lang: '$lang',
            loading: 'mapLoading',
            layers: $layers_list,
            center: $init_center,
            features: " . _makeFeatureCollection($objects_list) . "
          });
        }";

    // asynchronous map loading
    if ($async_map)
    {
        use_helper('MyMinify');
        $c2c_script_url = minify_get_combined_files_url(
          array('/static/js/carto/build/app.js', "/static/js/carto/build/lang-$lang.js", '/static/js/carto/embedded.js'),
          (bool) sfConfig::get('app_minify_debug'));

        $js .= "
            function c2c_asyncload(jsurl) {
              var a = document.createElement('script'),
              h = document.getElementsByTagName('head')[0];
              a.async = 1; a.src = jsurl; h.appendChild(a);
            }
            function map_load_async() {
                c2c_asyncload('$c2c_script_url');
            }";
    }

    $html .= javascript_tag($js);

/*
// TODO: asynchronous map loading
    if (sfConfig::get('app_async_map', true))
    {
        use_helper('MyMinify');
        // FIXME if using ie for async load, set $debug to true, because minifying the js currently breaks ie
        $c2c_script_url = minify_get_combined_files_url(array('/static/js/carto/build/xapi.js',
                                                              '/static/js/carto/build/lang-fr.js',
                                                              '/static/js/carto/docmap.js'),
                                                        (bool)sfConfig::get('app_minify_debug'));

        // FIXME extjs uses document.write with ie, so we cannot for the moment use async loading with ie
        $html .= javascript_tag('
if (!Prototype.Browser.IE) { var c2corgloadMapAsync = true; }
function c2c_asyncload(jsurl) { var a = document.createElement(\'script\'), h = document.getElementsByTagName(\'head\')[0]; a.async = 1; a.src = jsurl; h.appendChild(a); }
function asyncloadmap() { if (!Prototype.Browser.IE) { c2c_asyncload(\''.$c2c_script_url.'\'); }}');
    }
*/

    return $html;
}

function _convertObjectToGeoJSON($id, $module, $wkt) {
    return sprintf('{ "type": "Feature", "geometry": %s, "id": %d, ' .
                   '"properties": { "module": "%s", "name": "%s" } }',
                   geoPHP::load($wkt, 'wkt')->out('json'),
                   $id, $module, "FIXME");
}

function _makeFeatureCollection($features) {
    return '{ "type": "FeatureCollection", "features": [' . implode(',', $features) . '] }';
}

function _addAssociatedDocsWithGeom($docs, &$objects_list)
{
    foreach ($docs as $doc)
    {
        if (!empty($doc['pointwkt']))
        {
            $objects_list[] = _convertObjectToGeoJSON($doc['id'], $doc['module'], $doc['pointwkt']);
        }
    }
}

function _loadJsMapTools()
{
    $debug = sfContext::getInstance()->getRequest()->getParameter('debug', false);
    $async_map = sfConfig::get('app_async_map', false);
    $lang = sfContext::getInstance()->getUser()->getCulture();

    if ($debug) {
        include_partial('documents/map_lib_include_debug');
    } else {
        use_stylesheet('/static/js/carto/build/app.css', 'custom');
        if (!$async_map) use_javascript('/static/js/carto/build/app.js', 'maps');
    }

    use_stylesheet('/static/js/carto/carto.css', 'custom'); // FIXME: build CSS

    if (!$async_map)
    {
        use_javascript("/static/js/carto/build/lang-$lang.js", 'maps');
        use_javascript('/static/js/carto/embedded.js', 'maps');
    }
}

_loadJsMapTools();

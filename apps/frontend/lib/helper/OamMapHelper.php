<?php
use_helper('JavascriptQueue', 'I18N');

function show_georef_map($lon, $lat, $layer)
{
    $lang = sfContext::getInstance()->getUser()->getCulture();

    if (empty($layer))
    {
        $layer = sfContext::getInstance()->getModuleName();
    }
    $html  = '<div class="section" id="georef_container" style="display:none;">';
    $html .= '<div id="map" style="height:400px;width:100%">';
    $html .= '<div id="mapLoading">'.image_tag(sfConfig::get('app_static_url') . '/static/images/indicator.gif');
    $html .= __('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '</div>';

    $async_map = sfConfig::get('app_async_map', false) &&
                 !sfContext::getInstance()->getRequest()->getParameter('debug', false);

    $js = "
        C2C.map_init = function() {
            Ext.get('georef_container').show();
            var lon = Ext.getDom('lon') && Ext.getDom('lon').value || 0;
            var lat = Ext.getDom('lat') && Ext.getDom('lat').value || 0;
            c2corg.Map({
                div: 'map',
                lang: '$lang',
                loading: 'mapLoading',
                layers: ['$layer'],
                connected: true,
                georef: {
                    initialState: {
                        lon: lon,
                        lat: lat,
                        zoom: 15
                    },
                    callback: function(lonlat) {
                        if (lonlat) {
                            Ext.getDom('lon').value = Math.round(lonlat.lon*1E6)/1E6;
                            Ext.getDom('lat').value = Math.round(lonlat.lat*1E6)/1E6;
                        } else {
                            Ext.getDom('lon').value = '';
                            Ext.getDom('lat').value = '';
                        }
                        c2corg.coords.update_degminsec('lon');
                        c2corg.coords.update_degminsec('lat');
                    }
                }
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

        $js .= "C2C.async_map_init = function() {
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

    // if coordinates not set, open automatically open the map
    if (!$lon && !$lat && !in_array($layer, array('sites', 'users', 'images', 'portals')))
    {
        if ($async_map)
        {
            $js .= "C2C.async_map_init()";
        }
        else
        {
            $js .= "$(window).load(C2C.map_init)";
        }
    }

    $html .= javascript_queue($js);
    
    return $html;
}

function _loadJsOamTools()
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

_loadJsOamTools();
?>

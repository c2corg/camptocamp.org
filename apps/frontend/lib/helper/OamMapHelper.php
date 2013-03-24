<?php
use_helper('Javascript', 'I18N');

function show_georef_map($lon, $lat, $lang, $layer)
{
    include_partial('documents/map_i18n');
    
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

    $html .= javascript_tag("
        openGeorefMap = function(lon, lat) {
            Ext.get('georef_container').show();
            c2corg.Map({
                div: 'map',
                lang: '$lang',
                loading: 'mapLoading',
                layers: ['$layer'],
                georef: {
                    initialState: {
                        lon: lon,
                        lat: lat,
                        zoom: 15
                    },
                    callback: function(lonlat) {
                        if (lonlat) {
                            $('lon').value = Math.round(lonlat.lon*1E6)/1E6;
                            $('lat').value = Math.round(lonlat.lat*1E6)/1E6;
                        } else {
                            $('lon').value = '';
                            $('lat').value = '';
                        }
                        c2corg.coords.update_degminsec('lon');
                        c2corg.coords.update_degminsec('lat');
                    }
                }
            });
        };
    "); 
    // TODO: update marker position if coords are changed by hand in form

    if (!$lon && !$lat && !in_array($layer, array('sites', 'users', 'images', 'portals'))) // TODO this is broken with ie, do not launch it automatically in this case
    {
        $html .= javascript_tag("if(!Prototype.Browser.IE) {Event.observe(window, 'load', function(){openGeorefMap(0,0);});}");
    }
    
    return $html;
}

function _loadJsOamTools()
{
    $debug = sfContext::getInstance()->getRequest()->getParameter('debug', false);
    $lang = sfContext::getInstance()->getUser()->getCulture();
    if ($debug) {
        include_partial('documents/map_lib_include_debug');
    } else {
        use_stylesheet('/static/js/carto/build/app.css', 'custom');
        use_javascript('/static/js/carto/build/app.js', 'maps');
    }
    use_stylesheet('/static/js/carto/carto.css', 'custom'); // FIXME: build CSS
    use_javascript("/static/js/carto/build/lang-$lang.js", 'maps');
    use_javascript('/static/js/carto/embedded.js', 'maps');
}

_loadJsOamTools();
?>

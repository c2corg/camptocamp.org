var map = null;
var wmsTiled = null;
var wmsUntiled = null;
var query = null;
var marker_layer = null;
var vector_layer = null;
var google_layers = {}; 
var wkt_parser = null;
var map_initialized = false;
var highlightedFeature = null;

var tooltip_size = new OpenLayers.Size(220, 155);
var meters_around_point = 10000; // 10 kilometers

var wms_url = "/cgi-bin/mapserv_c2corg?";

var featureStyle = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
featureStyle.graphicWidth = 32;
featureStyle.graphicHeight = 32;
featureStyle.graphicOpacity = 1;
featureStyle.graphicXOffset = -(featureStyle.graphicWidth / 2);
featureStyle.graphicYOffset = -featureStyle.graphicHeight;
featureStyle.externalGraphic = "/static/images/mapmarker.png";

var featureStyleHighlight = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
featureStyleHighlight.graphicWidth = 50;
featureStyleHighlight.graphicHeight = 50;
featureStyleHighlight.graphicOpacity = 1;
featureStyleHighlight.externalGraphic = "/static/images/mapmarker-big.png";
featureStyleHighlight.graphicXOffset = -(featureStyleHighlight.graphicWidth / 2);
featureStyleHighlight.graphicYOffset = -featureStyleHighlight.graphicHeight;
featureStyleHighlight.fillColor = "blue";
featureStyleHighlight.strokeColor = "blue";
featureStyleHighlight.cursor = "pointer";



/*
 * These variables must be set prior to calling initialize_map()
 */

var div_id_container = null;      /* id of container div */
var div_id_map_container = null;  /* id of map container div */
var div_id_map = null;            /* id of map div */
var div_id_tree = null;           /* id of layer tree div */
var tip_close = null;             /* button tip string */
var form_id_search = null;        /* id of search form */
var query_url = null;
var layers_json = null;
var objects_to_mark = null;       /* objects to highlight: "id:wkt" */
var query_activated = false;      /* boolean value indicating whether query is on or off */
var search_url = null;


/*
 *
 * Public functions
 * 
 *    used from within HTML
 *
 */


/**
 * Start the map and its layers
 */
function initialize_map() {

    var layers_obj = eval(layers_json);

    __load_map(div_id_map, layers_obj);
    /* map is marked initialized once for good */
    map_initialized = true;

    if (objects_to_mark != null)
        __create_markers(objects_to_mark);

    if (query_activated) {
        query = new CartoWeb.Query.Extent(
            search_url,
            __ajax_search_pre_callback, // defined in search.js
            __ajax_search_post_callback, // defined in search.js
            search,
            null,
            null
        );
        map.addControl(query);
    }
}

function toggle_query(checkbox) {
    if (checkbox.checked) {
        __activate_query();
    } else {
        __deactivate_query();
    }
}

function highlight_object(fid) {
    if (!__map_is_visible()) {
        // make the map visible
        toggleView(div_id_container, true, tip_close);
    }
    __highlight_object(__getFeatureByFid(fid));
}

/*
 *
 * Private functions
 *
 *   used from this file and from search.js
 *
 */

function __highlight_object(feature) {
    if (feature == null)
        return;

    if (feature != highlightedFeature) {
        if (highlightedFeature != null) {
            highlightedFeature.style = vector_layer.style;
            vector_layer.drawFeature(highlightedFeature);
        }
        feature.style = featureStyleHighlight;    
        vector_layer.drawFeature(feature);
        highlightedFeature = feature;
    }
    var zoom;
    var bounds = highlightedFeature.geometry.getBounds();
    if (bounds.getWidth() == 0 && bounds.getHeight() == 0) {
        // point
        bounds.left -= meters_around_point;
        bounds.bottom -= meters_around_point;
        bounds.right += meters_around_point;
        bounds.top += meters_around_point;
        zoom = __get_zoom_for_extent(bounds);
    } else {
        zoom = map.getZoom();
    }
    map.setCenter(bounds.getCenterLonLat(), zoom, false, false, true);
    // scroll to the results section
    var div = __map_is_visible() ? div_id_map_container : div_id_search_results;
    new Effect.ScrollTo(div, {offset: -35});
}

function __getFeatureByFid(fid) {
    for (var i = 0; i < vector_layer.features.length; i++) {
        var f = vector_layer.features[i];
        if (f.fid == fid) {
            return f;
        }
    }
    return null;
}

function __map_is_visible() {
     return map_initialized && $(div_id_map_container).visible();
}

function __load_map(div_id, layers_obj) {
    var tiledLayers = {};
    var untiledLayers = {};
    var visibleTiledLayers = [];
    var visibleUntiledLayers = [];

    for (var id in layers_obj) {
        var layer = layers_obj[id];
        if (layer.google != null && layer.google == true) {
            // google layer
            google_layers[id] = layer;
        } else if (layer.tiled != null && layer.tiled == true) {
            // wms tiled layer
            tiledLayers[id] = layer;
            if (layer.visible != null && layer.visible == true) {
                visibleTiledLayers.push(id);
            }
        } else if (layer.tiled != null) {
            // wms untiled layer
            untiledLayers[id] = layer;
            if (layer.visible == true) {
                visibleUntiledLayers.push(id);
            }
        }
    }
    
    var options = {
        controls: [],
        projection: "EPSG: 900913",
        units: "m",
        maxResolution: 156543.0339,
        maxExtent: new OpenLayers.Bounds(-20037508, -136554022,
                                         20037508, 136554022)
    };

    OpenLayers.ImgPath = "/static/images/openlayers/";
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;

    /* create map */
    map = new OpenLayers.Map($(div_id) , options);
    
    /* create google layers */
    for (var id in google_layers) {
        var layer = google_layers[id];
        var layer_type = eval(layer.type);
        var layer_obj = new OpenLayers.Layer.Google(id,
            {type: layer_type, sphericalMercator: true});
        layer.layer_obj = layer_obj;
        map.addLayer(layer_obj);
    }

    /* create untiled WMS layer */ 
    wmsUntiled = new OpenLayers.Layer.WMS.Untiled("untiled", wms_url,
        {layers: visibleUntiledLayers, format: 'image/gif', transparent: true},
        {isBaseLayer: false, buffer: 0, reproject:false}
    );
    wmsUntiled.setVisibility(visibleUntiledLayers.length > 0);
    map.addLayer(wmsUntiled);
    
    /* create tiled WMS layer */ 
    wmsTiled = new OpenLayers.Layer.WMS("tiled", wms_url,
        {layers: visibleTiledLayers, format: 'image/gif', transparent: true},
        {isBaseLayer: false, buffer: 0, reproject:false}
    );
    wmsTiled.setVisibility(visibleTiledLayers.length > 0);
    map.addLayer(wmsTiled);
    
    /* create a vector layer */
    vector_layer = new OpenLayers.Layer.Vector("vector", {style: featureStyle});
    map.addLayer(vector_layer);
    
    /* create a WKT parser */
    wkt_parser = new OpenLayers.Format.WKT();

    /* define controls */
    map.setCenter(new OpenLayers.LonLat(1211912, 6112432), 4);
    map.addControl(new OpenLayers.Control.Navigation());
    map.addControl(new OpenLayers.Control.PanZoomBar());
    var one_google_layer;
    for (var id in google_layers) {
        one_google_layer = google_layers[id].layer_obj;
        break;
    }
    map.addControl(new MousePositionLonLat(one_google_layer));
    
    /* define select feature control */
    var select = new OpenLayers.Control.SelectFeature(vector_layer, {
        callbacks: {
            'click': __click_handler_feature,
            'down': null,
            'up': null
        }
    });
    map.addControl(select);
    select.activate();
   
    /* register an onclick event on map */
    map.events.register('click', map, __click_handler_xy);
    
    /* create layer trees */
    __init_gmap_layer_tree(div_id_tree);
    new CartoWeb.LayerManager(wmsUntiled, untiledLayers, div_id_tree, wms_url);
    new CartoWeb.LayerManager(wmsTiled, tiledLayers, div_id_tree, wms_url);
}

function __switch_gmap_layer(evt) {
    var input = OpenLayers.Event.element(evt);
    if (input.disabled) {
        return;
    }
    input.checked = true;
    map.setBaseLayer(google_layers[input.id].layer_obj);
}

function __init_gmap_layer_tree(id) {
    var form = document.createElement("form");
    form.id = 'GmapLayerTreeForm';
    form.style.marginBottom = '10px';
    $(id).appendChild(form);

    var text;
    var radio_to_check = null;

    for (var layer_id in google_layers) {
        var layer = google_layers[layer_id];

        var radio = document.createElement("input");
        radio.id = layer_id;
        radio.type = "radio";
        radio.value = layer_id;
        radio.name = "gmap";
        OpenLayers.Event.observe(radio, 'click', __switch_gmap_layer);

        if (layer.type == 'G_NORMAL_MAP') {
            radio_to_check = radio;
        }

        var label = document.createElement("label");
        label.setAttribute("for", layer_id);

        text = document.createTextNode(layer.name);
        label.appendChild(text);

        form.appendChild(radio);
        form.appendChild(label);
    }

    if (radio_to_check != null) {
        radio_to_check.checked = true;
    }
}

function __click_handler_xy(evt) {
    var lonlat = map.getLonLatFromViewPortPx(evt.xy);
    __query_by_xy(lonlat);
}

function __click_handler_feature(feature) {
    if (feature.lonlat)
        __query_by_id(feature.fid, feature.lonlat);
}

function __query_by_xy(lonlat) {
    // init url params
    var params = "";
    
    // add xy
    params += "x=" + lonlat.lon + "&y=" + lonlat.lat;
    
    // add bbox
    params += "&bbox=" + map.getExtent().toBBOX();
    
    // add map width and height
    var size = map.getCurrentSize();
    params += '&width=' + size.w + '&height=' + size.h;
    
    // add requested layer
    params += '&layers=' + wmsUntiled.params.LAYERS.join();
    
    __query_common(params, lonlat);
}

function __query_by_id(id, lonlat) { 
    var params = "id=" + id;
    __query_common(params, lonlat);
}

function __query_common(query_url_params, lonlat) {

    // remove all popups before anything
    __remove_all_popups();

    // callback to be called on ajax request complete
    function __ajax_query_callback(request) {
        var json = eval('(' + request.responseText + ')');
        if (!json || !json['html'] || json['html'] == '')
            return;
        
        var tooltip = new OpenLayers.Popup.AnchoredBubble(
            "__tooltip__",
            lonlat,
            tooltip_size,
            null,
            null,
            true
        );
        
        tooltip.setContentHTML(json['html']);
        tooltip.setOpacity(0.8);
        
        map.addPopup(tooltip, true /* exclusive */);
    }
    
    // init url
    var url = query_url + "?" + query_url_params;
    
    new OpenLayers.Ajax.Request(url,
        {
            onComplete: __ajax_query_callback
        } 
    );
}

function __activate_query() {
    if (query) {
        // activate query
        query.activate();
        // and attach it to the search object
        if (search) {
            search.searchObject = query;
        }
    }
}

function __deactivate_query() {
    if (query) {
        // deactivate query
        query.deactivate();
        // and deattach it from the search object
        if (search) {
            search.searchObject = null;
        }
    }
}

function __query_is_activated() {
    return (query && query.active);
}

function __remove_all_popups() {
    for(var i = 0; i < map.popups.length; i++) {
        map.removePopup(map.popups[i]);
    }
}

function __get_zoom_for_extent(extent) {
    var view_size = this.map.getSize();
    var ideal_resolution = Math.max(
        extent.getWidth() / view_size.w,
        extent.getHeight() / view_size.h
    );
    return __get_zoom_for_resolution(ideal_resolution);
}

function __get_zoom_for_resolution(resolution) {
    var zoom = 0;
    for (var i = map.baseLayer.resolutions.length - 1; i >= 0; i--) {
        var  res = map.baseLayer.resolutions[i];
        if (res >= resolution) {
            zoom = i;
            break;
        }
    }
    return zoom;
}

/**
 * This function is called from __ajax_search_callback which is defined
 * in search.js
 */
function __create_markers(string) {

    if (string == null)
        return;

    if (!map_initialized) {
        objects_to_mark = string;
        return;
    }
        
    var features = string.split(';');
    if (features == null)
        return;

    var highlightedFid = (highlightedFeature != null) ?
        highlightedFeature.fid : -1;

    // destroy old features
    highlightedFeature = null;
    vector_layer.destroyFeatures();
    
    var finalBounds = null;
        
    while (features.length > 0) {
        
        var feature = features.pop();
        if (feature == null)
            return;
        
        // format: "fid:wkt"    
        var _tmp = feature.split(':');
        if (_tmp.length < 2)
            continue;
            
        var fid = _tmp[0];
        var wkt = _tmp[1];

        if (!wkt || wkt == '')
            continue;
        
        var f = wkt_parser.read(wkt);
        if (!f || !f.geometry)
            continue;

        var geom = f.geometry;

        var lonlat = (geom.CLASS_NAME == "OpenLayers.Geometry.Point") ?
            new OpenLayers.LonLat(geom.x, geom.y) : null;
            
        f.fid = fid;
        f.lonlat = lonlat;
     
        var bounds = geom.getBounds().clone();
        if (finalBounds == null) {
            finalBounds = bounds;
        } else {
            finalBounds.extend(bounds);
        }

        vector_layer.addFeatures(f);
        // highlight the feature if an old feature with the same id was
        // previously highlighted
        if (fid == highlightedFid) {
            __highlight_object(f);
        }
    }

    if (!__query_is_activated() && finalBounds != null) {
        var deltaX;
        var deltaY;
        var width = finalBounds.getWidth();
        var height = finalBounds.getHeight();
        if (width == 0 && height == 0) {
            // single point case
            deltaX = meters_around_point;
            deltaY = meters_around_point;
        } else {
            deltaX = width * 10. / 100.;
            deltaY = height * 10. / 100.;
        }
        finalBounds.left -= deltaX;
        finalBounds.bottom -= deltaY;
        finalBounds.right += deltaX;
        finalBounds.top += deltaY;
        // do not use zoomToExtent to avoid triggering movestart/end events
        map.setCenter(
            finalBounds.getCenterLonLat(),
            __get_zoom_for_extent(finalBounds),
            false, false, true /* no event */);
    }
}

var map, oam_layer, osm_layer, c2corg, geom, feature, orig_lon, orig_lat;
var default_zoom = 11;
var loaded = false;
var wms_url = "/cgi-bin/mapserv_c2corg?";

function init_oam(lon, lat){
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
    OpenLayers.ImgPath = '/static/images/openlayers/';
    map = new OpenLayers.Map( 'map', 
        { 
        maxResolution: 1.40625/2,
        numZoomLevels: 22, 
        controls: [  
            new OpenLayers.Control.PanZoom(), 
            new OpenLayers.Control.Navigation()
            ]
        } 
    );
    
    /* create OpenAerialMap tiled WMS layer */ 
    oam_layer = new OpenLayers.Layer.WMS( "OpenAerialMap", 
             [
              "http://oam1.hypercube.telascience.org/tiles/",
              "http://oam2.hypercube.telascience.org/tiles/",
              "http://oam3.hypercube.telascience.org/tiles/"
             ],
                 {layers: 'openaerialmap'}, {'wrapDateLine': true, buffer: 0} );
                 
    map.addLayer(oam_layer);
    
    /* create untiled WMS layer with c2corg objects */ 
    c2corg = new OpenLayers.Layer.WMS("C2Corg", wms_url,
        {layers: 'sites,parkings,huts,summits', format: 'image/gif', transparent: true},
        {singleTile: true, isBaseLayer: false, buffer: 0, visibility: true, reproject:false}
    );
    map.addLayer(c2corg);
    
    /* create OpenStreetMap tiled WMS layer */ 
    osm_layer = new OpenLayers.Layer.WMS( "OpenStreetMap", 
             [
              "http://osm1.hypercube.telascience.org/tiles/",
              "http://osm2.hypercube.telascience.org/tiles/",
              "http://osm3.hypercube.telascience.org/tiles/"
             ],
                 {layers: 'osm-4326-hybrid'},
                 {isBaseLayer: false, visibility: true, buffer: 0}); //, alpha: true});
    map.addLayer(osm_layer);
    
    // add vector layer
    vectors = new OpenLayers.Layer.Vector("Object");
    map.addLayer(vectors);
    
    // allow feature dragging (but DragFeature control must be included in OL_sfl, and that is not currently the case !)
    /*
    var drag_control = new OpenLayers.Control.DragFeature(vectors);
    map.addControl(drag_control);
    drag_control.activate();
    */
    
    // in any case, be ready to receive a short clic in order to pinpoint object
    map.events.register('click', map, function(evt) { 
        var lonlat = map.getLonLatFromPixel(evt.xy);  
        clear_create_point_and_zoom(lonlat.lon, lonlat.lat, false);
        // then, update lon and lat fields in form.
        document.getElementById(lon_field_id).value = Math.round(lonlat.lon*1E6)/1E6;
        document.getElementById(lat_field_id).value = Math.round(lonlat.lat*1E6)/1E6;
        update_degminsec(lon_field_id);
        update_degminsec(lat_field_id);
        if (document.getElementById(revert_btn_id))
        {
            document.getElementById(revert_btn_id).style.display = "";
        }
	});
    
    // set center
    if (lon && lat) { 
        clear_create_point_and_zoom(lon, lat, default_zoom);
    } else {
        map.zoomToExtent(new OpenLayers.Bounds(2, 42, 13, 48));
    }
}

// called when button clicked to view position of entered (lon,lat)
function update_point(msg)
{
    var lon = document.getElementById(lon_field_id).value;
    var lat = document.getElementById(lat_field_id).value;
    if (lon && lat)
    {
        clear_create_point_and_zoom(lon, lat, map.getZoom());
        document.getElementById(update_btn_id).style.display = "none";
    }
    else
    {
        alert(msg);
    }
}

// called to revert (lon,lat) to original values
function revert()
{
    document.getElementById(lon_field_id).value = orig_lon;
    document.getElementById(lat_field_id).value = orig_lat;
    update_degminsec(lon_field_id);
    update_degminsec(lat_field_id);
    clear_create_point_and_zoom(orig_lon, orig_lat, map.getZoom());
    if (document.getElementById(revert_btn_id))
    {
        document.getElementById(revert_btn_id).style.display = "none";
    }
}

function clear_create_point_and_zoom(lon, lat, zoom)
{
    geom = new OpenLayers.Geometry.Point(lon, lat);
    feature = new OpenLayers.Feature.Vector(geom);
    vectors.destroyFeatures();
    vectors.addFeatures([feature]);
    if (zoom) map.setCenter(new OpenLayers.LonLat(lon, lat), zoom);
}

function toggle_update_btn(){
    if (document.getElementById(lon_field_id).value && document.getElementById(lat_field_id).value)
    {
        document.getElementById(update_btn_id).style.display = "";
    }
}

function init_mapping(lon, lat){
    if (!loaded)
    {
        loaded = true;
        init_oam(lon,lat);
        document.getElementById(mapping_div_id).style.display = "";
    }
}

function toggle_osm(state){
    osm_layer.setVisibility(state);
}

//// decimal degrees <-> deg/min/sec conversion tools

function update_decimal_coord(field)
{
    deg = parseInt($(field + '_deg').value);
    if (isNaN(deg)) 
    {
        deg = 0;
    }
    if (deg < 0)
    {
        sign = -1;
        deg = -1 * deg;
    }
    else
    {
        sign = 1;
    }
    min = parseInt($(field + '_min').value);
    if (isNaN(min))
    {
        min = 0;
    }
    sec = parseFloat($(field + '_sec').value);
    if (isNaN(sec))
    {
        sec = 0;
    }
    $(field).value = sign * Math.round(1000000 * (deg + min/60 + sec/3600)) / 1000000;
}

function update_degminsec(field)
{
    degreesTemp = parseFloat($(field).value);
    if (isNaN(degreesTemp))
    {
        return;
    }
    if (degreesTemp < 0)
    {
        sign = -1;
        degreesTemp = -1 * degreesTemp;
    }
    else
    {
        sign = 1;
    }
    degrees     = Math.floor(degreesTemp);

    minutesTemp = degreesTemp - degrees;
    minutesTemp = 60.0 * minutesTemp;
    minutes     = Math.floor(minutesTemp);

    secondsTemp = minutesTemp - minutes;
    secondsTemp = 60.0 * secondsTemp;
    seconds     = Math.round(100 * secondsTemp) / 100;

    $(field + '_deg').value = sign * degrees;
    $(field + '_min').value = minutes;
    $(field + '_sec').value = seconds;
}

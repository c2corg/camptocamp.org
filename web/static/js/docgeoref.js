Ext.namespace("c2corg");

c2corg.docGeoref = (function() {
    
    var map_already_loaded = false, api, bbar, markerLayer, marker,
        georefControl, originalLonlat;
    
    var createMapPanel = function() {
        bbar = api.createBbar({id: 'emb_layer_select'});
        tbar = new Ext.Toolbar({
            items: [ OpenLayers.i18n('Click on the map to'), {
                text: OpenLayers.i18n('update coordinates'),
                enableToggle: true,
                toggleGroup: 'georef',
                pressed: true,
                handler: function() {
                    api.tooltip.deactivate();
                    api.tooltipTest.deactivate();
                    georefControl.activate();
                }
            },{
                text: OpenLayers.i18n('show objects info'),
                enableToggle: true,
                toggleGroup: 'georef',
                handler: function() {
                    api.tooltip.activate();
                    api.tooltipTest.activate();
                    georefControl.deactivate();
                }
            }, ' | ', {
                text: OpenLayers.i18n('Reset georef'),
                handler: function() {
                    if (originalLonlat) {
                        updateLonlat(originalLonlat, true);
                        api.map.setCenter(
                            originalLonlat.clone().transform(
                                api.epsg4326,
                                api.map.getProjection()
                            ),
                            12
                        );
                    } else {
                        if (marker) {
                            marker.destroy();
                        }
                        $(lon_field_id).value = $(lat_field_id).value = '';
                        update_degminsec(lon_field_id);
                        update_degminsec(lat_field_id);
                    }
                }
            }, '->', new c2corg.GeoNamesSearchCombo({
                api: api,
                width: 200,
                emptyText: OpenLayers.i18n('Go to...')
            })] 
        });
        return Ext.apply(api.createMapPanel(), {
            id: 'mappanel',
            margins: '0 0 0 0',
            region: 'center',
            border: false,
            tbar: tbar,
            bbar: bbar
        });
        
    };
    
    var createLayerTree = function() {
        var treeOptions = (layersList.length > 0) ? {layers: layersList} : {};
        treeOptions.id = 'c2c_layers';
        return Ext.apply(api.createLayerTree(treeOptions), {
            region: 'west',
            width: 250,
            border: false,
            collapsible: true,
            collapseMode: 'mini',
            split: true     
        });
    };
    
    var updateMarker = function(lonlat, reproject) {
        if (marker) {
            marker.destroy();
        }
        
        var lonlat2 = lonlat.clone();
        if (reproject) {
            lonlat2.transform(api.epsg4326, api.map.getProjection());
        }
        
        var size = new OpenLayers.Size(21,25);
        var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
        var icon = new OpenLayers.Icon('/static/js/mapfish/mfbase/openlayers/img/marker.png', size, offset);
        marker = new OpenLayers.Marker(new OpenLayers.LonLat(lonlat2.lon, lonlat2.lat), icon);
        markerLayer.addMarker(marker);
    };
    
    var updateLonlat = function(lonlat, reproject) {
        updateMarker(lonlat, reproject);
        
        var lonlat2 = lonlat.clone();
        if (!reproject) {
            lonlat2.transform(api.map.getProjection(), api.epsg4326);
        }
        $(lon_field_id).value = Math.round(lonlat2.lon*1E6)/1E6;
        $(lat_field_id).value = Math.round(lonlat2.lat*1E6)/1E6;
        update_degminsec(lon_field_id);
        update_degminsec(lat_field_id);
    };
    
    var addGeorefControl = function() {
        markerLayer = new OpenLayers.Layer.Markers("Markers");
        api.map.addLayer(markerLayer);
        
        georefControl = new c2corg.Georef({
            api: api,
            callback: updateLonlat
        });
        api.map.addControl(georefControl);
        georefControl.activate();
    };
    
    return {
        init: function(lon, lat) {
            // do not init if already loaded or if section is closed
            if (map_already_loaded) {
              return;
            }
            
            Ext.get(mapContainer).show();
            
            originalLonlat = (lon & lat) ? new OpenLayers.LonLat(lon, lat) : null;
            
            api = new c2corg.API({lang: mapLang});
            api.createMap({
                easting: originalLonlat ? originalLonlat.lon : 7,
                northing: originalLonlat ? originalLonlat.lat : 45.5,
                zoom: originalLonlat ? 12 : 6
            });
            addGeorefControl();
            
            if (originalLonlat) {
                updateMarker(originalLonlat.clone().transform(api.epsg4326, api.map.getProjection()));
            }
            
            new Ext.Panel({
                applyTo: 'map',
                layout: 'border',
                border: false,
                cls: 'embeddedMap',
                items: [ createMapPanel(), createLayerTree() ]
            });
            
            api.tooltip.deactivate();
            api.tooltipTest.deactivate();
            
            // hide loading message
            Ext.removeNode(Ext.getDom('mapLoading'));
            
            map_already_loaded = true;
        }
    };
})();

c2corg.Georef = OpenLayers.Class(OpenLayers.Control, {                
    defaultHandlerOptions: {
        'single': true,
        'double': false,
        'pixelTolerance': 0,
        'stopSingle': false,
        'stopDouble': false
    },
    
    callback: null,
    api: null,

    initialize: function(options) {
        this.handlerOptions = OpenLayers.Util.extend(
            {}, this.defaultHandlerOptions
        );
        OpenLayers.Control.prototype.initialize.apply(
            this, arguments
        );
        
        this.api = options.api || null;
        this.callback = options.callback || null;
        
        this.handler = new OpenLayers.Handler.Click(
            this, {
                'click': this.trigger,
                scope: this
            }, this.handlerOptions
        );
    }, 

    trigger: function(e) {
        var lonlat = this.api.map.getLonLatFromViewPortPx(e.xy);
        if (this.callback) {
            this.callback(lonlat);
        }
    }
});

//// decimal degrees <-> deg/min/sec conversion tools

function update_decimal_coord(field)
{
    var sign;
    var deg = parseInt($(field + '_deg').value, 10);
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
    var min = parseFloat($(field + '_min').value);
    if (isNaN(min))
    {
        min = 0;
    }
    var sec = parseFloat($(field + '_sec').value);
    if (isNaN(sec))
    {
        sec = 0;
    }
    $(field).value = sign * Math.round(1000000 * (deg + min/60 + sec/3600)) / 1000000;
}

function update_degminsec(field)
{
    // deal with commas instead of points
    $(field).value = ($(field).value).replace(',', '.');
    
    if ($(field).value === '') {
        $(field + '_deg').value = $(field + '_min').value = $(field + '_sec').value = '';
        return;
    }

    var sign;
    var degreesTemp = parseFloat($(field).value);
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
    var degrees = Math.floor(degreesTemp);

    var minutesTemp = degreesTemp - degrees;
    minutesTemp = 60.0 * minutesTemp;
    var minutes     = Math.floor(minutesTemp);

    var secondsTemp = minutesTemp - minutes;
    secondsTemp = 60.0 * secondsTemp;
    var seconds     = Math.round(100 * secondsTemp) / 100;

    $(field + '_deg').value = sign * degrees;
    $(field + '_min').value = minutes;
    $(field + '_sec').value = seconds;
}

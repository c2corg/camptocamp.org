/**
 * @requires plugins/Tool.js
 * @requires OpenLayers/Control.js
 * @include OpenLayers/Layer/Markers.js
 * @include OpenLayers/Marker.js
 * @include OpenLayers/Icon.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.GeoRef = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_georef",

    callback: null,
    initialState: null,
    initialLonlat: null,

    markerLayer: null,
    marker: null,

    init: function() {
        c2corg.plugins.GeoRef.superclass.init.apply(this, arguments);
        this.target.on('ready', this.viewerReady, this);
    },

    viewerReady: function() {
        var map = this.target.mapPanel.map;

        // display picto if lat lon exist
        this.markerLayer = new OpenLayers.Layer.Markers("Markers");
        map.addLayer(this.markerLayer);

        if (this.initialState && this.initialState.lon & this.initialState.lat) {
            this.initialLonlat = new OpenLayers.LonLat(this.initialState.lon, this.initialState.lat);
            this.initialLonlat.transform("EPSG:4326", map.getProjection());
            map.setCenter(this.initialLonlat, this.initialState.zoom);

            this.createMarker(this.initialLonlat); 
        }

        // watch lat lon fields and update markers if needed
        var inputs = ['lon', 'lon_deg', 'lon_min', 'lon_sec', 'lat', 'lat_deg', 'lat_min', 'lat_sec'];
        Ext.select(inputs).on('change', function(evt, elm) {
            var lon = Ext.getDom('lon').value;
            var lat = Ext.getDom('lat').value;
            if (!isNaN(lon) && !isNaN(lat)) {
                //move map
                var position = new OpenLayers.LonLat(lon, lat);
                position.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
                map.setCenter(position);
                // move marker
                this.createMarker(position);
            }
        }, this);
    },

    createMarker: function(lonlat) {
        if (this.marker) {
            this.marker.destroy();
        }
        var size = new OpenLayers.Size(21,25);
        var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
        var icon = new OpenLayers.Icon(OpenLayers.ImgPath + 'marker.png', size, offset);
        this.marker = new OpenLayers.Marker(lonlat, icon);
        this.markerLayer.addMarker(this.marker);
    },

    updateLonlat: function(lonlat) {
        this.createMarker(lonlat);
        var lonlat2 = lonlat.clone();
        lonlat2.transform(this.target.mapPanel.map.getProjection(), "EPSG:4326");
        this.callback(lonlat2);
    },

    addActions: function() {
        var control = new c2corg.controls.Click({
            client: this,
            callback: this.updateLonlat,
            callbackName: "updateLonlat"
        });
        var actions = [];
        actions.push(new GeoExt.Action(Ext.apply({
            allowDepress: true,
            enableToggle: true,
            pressed: true,
            map: this.target.mapPanel.map,
            text: OpenLayers.i18n("Georef Tool"),
            tooltip: OpenLayers.i18n("Click on the map to locate item"),
            toggleGroup: this.toggleGroup,
            control: control
        }, this.actionConfig)));

        actions.push(new Ext.Action({
            text: OpenLayers.i18n("Reset georef"),
            tooltip: OpenLayers.i18n("Cancel changes"),
            handler: function() {
                if (this.initialLonlat) {
                    var map = this.target.mapPanel.map;
                    this.createMarker(this.initialLonlat);
                    this.callback(
                        this.initialLonlat.clone().transform(
                            map.getProjection(), "EPSG:4326"
                        )
                    );
                    map.setCenter(this.initialLonlat, this.initialZoom);                                       
                } else {
                    if (this.marker) {
                        this.marker.destroy();
                        this.marker = null;
                    }
                    this.callback(null);
                }
            },
            scope: this
        }));

        return c2corg.plugins.GeoRef.superclass.addActions.apply(this, [actions]);
    }
});

Ext.preg(c2corg.plugins.GeoRef.prototype.ptype, c2corg.plugins.GeoRef);

Ext.namespace("c2corg.controls");

c2corg.controls.Click = OpenLayers.Class(OpenLayers.Control, {
    defaultHandlerOptions: {
        'single': true,
        'double': false,
        'pixelTolerance': 0,
        'stopSingle': false,
        'stopDouble': false
    },

    client: null,
    callbackName: null,
    
    initialize: function(options) {
        this.handlerOptions = OpenLayers.Util.extend(
            {}, this.defaultHandlerOptions
        );
        OpenLayers.Control.prototype.initialize.apply(
            this, arguments
        ); 
        this.callbackName = options.callbackName || null;
        this.client = options.client || null;
        this.handler = new OpenLayers.Handler.Click(
            this, {
                'click': this.onClick,
                scope: this
            }, this.handlerOptions
        );
    }, 

    onClick: function(evt) {
        var lonlat = this.map.getLonLatFromViewPortPx(evt.xy);
        if (this.client && this.callbackName) {
            // just to make sure the callback uses the calling plugin as scope, not the control itself
            this.client[this.callbackName](lonlat);
        }
    }
});

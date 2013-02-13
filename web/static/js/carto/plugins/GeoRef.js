/**
 * @requires plugins/Tool.js
 * @requires OpenLayers/Control.js
 * @include OpenLayers/Layer/Markers.js
 * @include OpenLayers/Marker.js
 * @include OpenLayers/Icon.js
 * @include i18n.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.GeoRef = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_georef",

    callback: null,
    initialState: null,

    markerLayer: null,
    marker: null,

    init: function() {
        c2corg.plugins.GeoRef.superclass.init.apply(this, arguments);
        this.target.on('ready', this.viewerReady, this);
    },

    viewerReady: function() {
        var map = this.target.mapPanel.map;

        this.markerLayer = new OpenLayers.Layer.Markers("Markers");
        map.addLayer(this.markerLayer);

        if (this.initialState && this.initialState.lon & this.initialState.lat) {
            var lonlat = new OpenLayers.LonLat(this.initialState.lon, this.initialState.lat);
            lonlat = lonlat.transform("EPSG:4326", map.getProjection());
            map.setCenter(lonlat, this.initialState.zoom);

            this.createMarker(lonlat); 
        }
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
        this.callback(lonlat.transform(this.target.mapPanel.map.getProjection(),
                                       "EPSG:4326"));
    },

    addActions: function() {
        var control = new c2corg.controls.Click({
            client: this,
            callback: this.updateLonlat,
            callbackName: "updateLonlat"
        });
        var action = new GeoExt.Action(Ext.apply({
            allowDepress: true,
            enableToggle: true,
            map: this.target.mapPanel.map,
            text: c2corg.i18n("Georef"),
            toggleGroup: this.toggleGroup,
            control: control
        }, this.actionConfig));
        return c2corg.plugins.GeoRef.superclass.addActions.apply(this, [action]);
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
                scope: this,
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

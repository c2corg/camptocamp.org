/*
 * @requires plugins/Tool.js
 * @include OpenLayers/Layer/Vector.js
 * @include OpenLayers/Format/GeoJSON.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.ShowFeatures = Ext.extend(gxp.plugins.Tool, {
    ptype: "c2corg_showfeatures",

    features: null,
    layer: null,
    map: null,
    pointZoomLevel: 15,

    init: function() {
        c2corg.plugins.ShowFeatures.superclass.init.apply(this, arguments);
        this.target.on('ready', this.viewerReady, this);
    },

    viewerReady: function() {
        if (this.features) {
            this.map = this.target.mapPanel.map;
            this.createVectorLayer();

            var format = new OpenLayers.Format.GeoJSON(),
                features = format.read(this.features, "FeatureCollection");
            this.layer.addFeatures(features);

            if (features.length > 1 || 
                !(features[0].geometry instanceof OpenLayers.Geometry.Point)) { 
                this.map.zoomToExtent(this.layer.getDataExtent());
            } else {
                var point = features[0].geometry,
                    lonlat = new OpenLayers.LonLat(point.x, point.y);
                this.map.setCenter(lonlat, this.pointZoomLevel); 
            }
        }
    },

    createVectorLayer: function() {
        var styleMap = new OpenLayers.StyleMap({
            "default": new OpenLayers.Style({
                strokeColor: "yellow",
                strokeWidth: 2,
                fillColor: "yellow",
                fillOpacity: 0.1,
                graphicWidth: 16,
                graphicHeight: 16,
                externalGraphic: "/static/images/modules/${module}_mini.png",
                cursor: "pointer"
            })
        });

        this.layer = new OpenLayers.Layer.Vector("features", {
            displayInLayerSwitcher: false,
            styleMap: styleMap
        });
        this.map.addLayer(this.layer);
    }
});

Ext.preg(c2corg.plugins.ShowFeatures.prototype.ptype, c2corg.plugins.ShowFeatures);

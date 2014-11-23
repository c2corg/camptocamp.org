/*
 * @requires plugins/Tool.js
 * @include OpenLayers/Layer/Vector.js
 * @include OpenLayers/Format/GeoJSON.js
 * @include c2corg/config/styles.js
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
        if (this.features && this.features.features.length) {
            this.map = this.target.mapPanel.map;

            // add features to the map
            this.createVectorLayer();

            var format = new OpenLayers.Format.GeoJSON(),
                features = format.read(this.features, "FeatureCollection");

            this.layer.addFeatures(features);

            // define behaviour on hover
            // note that this will only work if no c2c object layer is selected (see LayerTree.js)
            var hoverCtrl = new OpenLayers.Control.SelectFeature(this.layer, {
                hover: true,
                highlightOnly: true,
                renderIntent: "temporary"
            });
            this.map.addControl(hoverCtrl);
            hoverCtrl.activate();
 
            // center and zoom map
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
        var styleMap = c2corg.styleMap('embeddedfeature', {
            points: { pointRadius: 10 },
            lines: { strokeWidth: 3 }
        });

        this.layer = new OpenLayers.Layer.Vector("features", {
            displayInLayerSwitcher: false,
            isBaseLayer: false,
            styleMap: styleMap
        });
        this.map.addLayer(this.layer);
    }
});

Ext.preg(c2corg.plugins.ShowFeatures.prototype.ptype, c2corg.plugins.ShowFeatures);

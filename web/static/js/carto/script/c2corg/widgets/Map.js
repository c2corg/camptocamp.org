/**
 * @requires OpenLayers/Map.js
 */

/**
 * Overrides OpenLayers.Map.setBaseLayer()
 */
OpenLayers.Map.prototype.setBaseLayer = function(newBaseLayer) {

    if (newBaseLayer != this.baseLayer) {
 
        // ensure newBaseLayer is already loaded
        if (OpenLayers.Util.indexOf(this.layers, newBaseLayer) != -1) {

            // preserve center and scale when changing base layers
            var center = this.getCenter();
            var oldResolution = this.getResolution();
            var newResolution = OpenLayers.Util.getResolutionFromScale(
                this.getScale(), newBaseLayer.units
            );

            // make the old base layer invisible 
            if (this.baseLayer != null && !this.allOverlays) {
                this.baseLayer.setVisibility(false);
            }

            var oldProjection = this.getProjection();

            // set new baselayer
            this.baseLayer = newBaseLayer;
 
            // Increment viewRequestID since the baseLayer is 
            // changing. This is used by tiles to check if they should 
            // draw themselves.
            this.viewRequestID++;
            if(!this.allOverlays || this.baseLayer.visibility) {
                this.baseLayer.setVisibility(true);
                // Layer may previously have been visible but not in range.
                // In this case we need to redraw it to make it visible.
                if (this.baseLayer.inRange === false) {
                    this.baseLayer.redraw();
                }
            }

            var newProjection = this.getProjection();
            var hasProjectionChanged = (oldProjection != newProjection);

            // recenter the map
            if (center != null) {

                 if (hasProjectionChanged) {
                     // reproject new center of the map
                     center.transform(oldProjection, newProjection);
                }

                // new zoom level derived from old scale
                var newZoom = this.getZoomForResolution(
                    newResolution || this.resolution, true 
                );
                // zoom and force zoom change
                this.setCenter(center, newZoom, false, hasProjectionChanged && oldResolution != newResolution);
            }

            if (hasProjectionChanged) {
                // reproject vector layers
               // this.updateVectorLayers();
            }

            this.events.triggerEvent("changebaselayer", {
                layer: this.baseLayer
            });
        }     
    }
};

OpenLayers.Map.prototype.updateVectorLayers = function(layers) {
    if (!this.baseLayer || !this.baseLayer.projection) return;

    layers = layers ? [layers] : this.layers;
    var bl = this.baseLayer;

    for (var i = 0, len = layers.length; i < len; i++) {
        var layer = layers[i];
        // for every vector layer...
        if (layer && layer instanceof OpenLayers.Layer.Vector) {
            var lp = layer.projection;
            // if its projection is different from the one of the current base layer...
            if (!bl.projection.equals(lp)) {
                // reproject all its features
                for (var j = 0, flen = layer.features.length; j < flen; j++) {
                    var geom = layer.features[j].geometry;
                    if (!geom) continue;
                    if (geom instanceof OpenLayers.Geometry.Point ||
                        geom instanceof OpenLayers.Geometry.Collection) {
                        geom.transform(lp, bl.projection);
                    }
                    // other geometry types are not yet supported
                }
                // then update layer projection
                layer.projection = bl.projection;
                layer.redraw();
            }
        }
    }
};

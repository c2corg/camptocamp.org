/**
 * @requires OpenLayers/Layer/Google/v3.js
 */

/**
 * Overrides OpenLayers.Layer.Google.v3's setGMapVisibility to fix a problem
 * with GMaps tiles not displayed at page loading since Google Maps API 3.21
 * is out.
 * See https://github.com/openlayers/openlayers/issues/1450
 * TODO: remove if change is applied in OpenLayers.
 */
OpenLayers.Layer.Google.v3['setGMapVisibility'] = function(visible) {
    var cache = OpenLayers.Layer.Google.cache[this.map.id];
    var map = this.map;
    if (cache) {
        var type = this.type;
        var layers = map.layers;
        var layer;
        for (var i=layers.length-1; i>=0; --i) {
            layer = layers[i];
            if (layer instanceof OpenLayers.Layer.Google &&
                        layer.visibility === true && layer.inRange === true) {
                type = layer.type;
                visible = true;
                break;
            }
        }
        var container = this.mapObject.getDiv();
        if (visible === true) {
            if (container.parentNode !== map.div) {
                if (!cache.rendered) {
                    var me = this;
                    google.maps.event.addListenerOnce(this.mapObject, 'tilesloaded', function() {
                        cache.rendered = true;
                        me.setGMapVisibility(me.getVisibility());
                        me.moveTo(me.map.getCenter());
                    });
                }
                map.div.appendChild(container);
                cache.googleControl.appendChild(map.viewPortDiv);
                google.maps.event.trigger(this.mapObject, 'resize');
            }
            this.mapObject.setMapTypeId(type);                
        } else if (cache.googleControl.hasChildNodes()) {
            map.div.appendChild(map.viewPortDiv);
            map.div.removeChild(container);
        }
    }
};

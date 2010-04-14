/**
 * This class' purpose is to be used in place of OpenLayers.Control.MousePosition
 * so that the mouse position is displayed in EPSG:4326 (lon/lat) as opposed to
 * EPSG:900913.
 *
 * This class inherits from OpenLayers.Control.MousePosition and overrides the
 * redraw() method.
 */

MousePositionLonLat = OpenLayers.Class.create();
MousePositionLonLat.prototype = 
    OpenLayers.Class.inherit(OpenLayers.Control.MousePosition, {
    
    layer: null, // Google Mercator layer

    initialize: function(layer, options) {
        this.layer = layer;
        if (!options) { options = {}; }
        OpenLayers.Util.extend(options, {displayClass: 'olMousePositionLonLat'});
        OpenLayers.Control.MousePosition.prototype.initialize.apply(this, [options]);
    },

    redraw: function(evt) {

        var lonLat;

        if (evt === null) {
            lonLat = new OpenLayers.LonLat(0, 0);
        } else {
            if (this.lastXy === null ||
                Math.abs(evt.xy.x - this.lastXy.x) > this.granularity ||
                Math.abs(evt.xy.y - this.lastXy.y) > this.granularity)
            {
                this.lastXy = evt.xy;
                return;
            }

            lonLat = this.map.getLonLatFromPixel(evt.xy);
            lonLat = this.layer.inverseMercator(lonLat.lon, lonLat.lat);
            this.lastXy = evt.xy;
        }
        
        var digits = parseInt(this.numdigits, 10);
        var newHtml =
            this.prefix +
            lonLat.lon.toFixed(digits) +
            this.separator + 
            lonLat.lat.toFixed(digits) +
            this.suffix;

        if (newHtml != this.element.innerHTML) {
            this.element.innerHTML = newHtml;
        }
    }
});

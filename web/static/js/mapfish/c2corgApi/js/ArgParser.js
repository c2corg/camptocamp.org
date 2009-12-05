Ext.namespace("c2corg.API");

c2corg.API.ArgParser = OpenLayers.Class(MapFish.API.ArgParser, {

    setMap: function(map) {
        OpenLayers.Control.prototype.setMap.apply(this, arguments);

        var args = OpenLayers.Util.getParameters();

        if (args.layerNodes) { 
            if (typeof args.layerNodes == 'string') {
                args.layerNodes = [args.layerNodes];
            }
            this.api.layerTreeNodes = args.layerNodes;
        }

        var lon = args[this.coordsParams.lon];
        var lat = args[this.coordsParams.lat];
        if (lon && lat) {
            var mapPosition = OpenLayers.Projection.transform({ x: lon, y: lat }, this.api.epsg4326, this.api.epsg900913);
            this.api.argParserCenter = { lon: mapPosition.x, lat: mapPosition.y };
            if (args.zoom) {
                this.api.argParserCenter.zoom = parseInt(args.zoom);
            }
        }
    }, 

    CLASS_NAME: "c2corg.API.ArgParser"
});
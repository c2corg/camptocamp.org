CartoWeb.Query = OpenLayers.Class.create();
CartoWeb.Query.prototype =
    OpenLayers.Class.inherit(OpenLayers.Control, CartoWeb.Search, {

    layers: null,
 
    options: null,

    initialize: function(url, preCallback, postCallback, search, layers, options) {
        OpenLayers.Control.prototype.initialize.call(this, options);
        CartoWeb.Search.prototype.initialize.call(this, url, preCallback, postCallback, search);
        if (layers) {
            this.layers = {'layers': layers};
        } else {
            this.layers = {};
        }
    },

	getParams: function() {
	    return this.layers;
	}
});
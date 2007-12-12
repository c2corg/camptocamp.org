CartoWeb.Query.Extent = OpenLayers.Class.create();
CartoWeb.Query.Extent.prototype =
    OpenLayers.Class.inherit(CartoWeb.Query, {

    initialize: function(url, precCallback, postCallback, search, layers, options) {
        CartoWeb.Query.prototype.initialize.apply(this, arguments);
    },
    
    activate: function() {
    	CartoWeb.Query.prototype.activate.call(this);
    	this.map.events.register('moveend', this, this.onMoveend);
    },
    
    deactivate: function() {
        CartoWeb.Query.prototype.deactivate.call(this);
        this.map.events.unregister('moveend', this, this.onMoveend);
    },

	onMoveend: function(evt) {
	    this.search();
	},
	
	getParams: function() {
	    var params = CartoWeb.Query.prototype.getParams.call(this);
	    var extent = this.map.getExtent();
	    var bbox = extent.left + "," + extent.bottom + "," + extent.right + "," + extent.top;
	    return OpenLayers.Util.extend(params, {'bbox': bbox});
	}
});

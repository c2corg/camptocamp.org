/*
 * Uncomplete
 */
CartoWeb.Query.Box = OpenLayers.Class.create();
CartoWeb.Query.Box.prototype =
    OpenLayers.Class.inherit(CartoWeb.Query, {

    initialize: function(url, callback, layers, object, options) {
        CartoWeb.Query.prototype.initialize.apply(this, arguments);
    }
});

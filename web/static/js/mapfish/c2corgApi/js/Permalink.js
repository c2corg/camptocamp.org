/**
 * @requires MapFishApi/js/Permalink.js
 */

// FIXME: namespace should be c2corg.API but it seems to create a build conflict

Ext.namespace("c2corg");

c2corg.Permalink = OpenLayers.Class(MapFish.API.Permalink, {
    
    createParams: function(center, zoom, layers) {
        var params = MapFish.API.Permalink.prototype.createParams.apply(this, arguments);
        
        if (params && this.api.map.baseLayer.name) {
            params.bgLayer = this.api.map.baseLayer.name;
        }
        
        return params;
    }
});
/**
 * @requires plugins/Tool.js
 * @include OpenLayers/BaseTypes/LonLat.js
 * @include OpenLayers/Projection.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.MyPosition = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_myposition",

    constructor: function() {
        c2corg.plugins.MyPosition.superclass.constructor.apply(this, arguments);
    },

    addActions: function() {
        if (!('geolocation' in navigator)) return [];

        var map = this.target.mapPanel.map;
        var button = new Ext.Button({
            tooltip: OpenLayers.i18n("My position"),
            iconCls: "myposition",
            handler: function() {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var position = new OpenLayers.LonLat(position.coords.longitude, position.coords.latitude);
                    position.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
                    var zoom = Math.max(11, map.getZoom());
                    map.setCenter(position, zoom);
                });
            }
        });

        return c2corg.plugins.MyPosition.superclass.addActions.apply(this, [button]);
    }
});

Ext.preg(c2corg.plugins.MyPosition.prototype.ptype, c2corg.plugins.MyPosition);

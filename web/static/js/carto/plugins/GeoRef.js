/**
 * @requires plugins/Tool.js
 * @requires OpenLayers/Control.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.GeoRef = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_georef",

    georefActionText: "Georef",

    callback: null,

    addActions: function() {
        var control = new c2corg.controls.Click({
            callback: this.callback
        });
        var action = new GeoExt.Action(Ext.apply({
            allowDepress: true,
            enableToggle: true,
            map: this.target.mapPanel.map,
            text: this.georefActionText,
            toggleGroup: this.toggleGroup,
            control: control
        }, this.actionConfig));
        return c2corg.plugins.GeoRef.superclass.addActions.apply(this, [action]);
    }
});

Ext.preg(c2corg.plugins.GeoRef.prototype.ptype, c2corg.plugins.GeoRef);

Ext.namespace("c2corg.controls");

c2corg.controls.Click = OpenLayers.Class(OpenLayers.Control, {
    defaultHandlerOptions: {
        'single': true,
        'double': false,
        'pixelTolerance': 0,
        'stopSingle': false,
        'stopDouble': false
    },

    callback: null, // to be overloaded
    
    initialize: function(options) {
        this.handlerOptions = OpenLayers.Util.extend(
            {}, this.defaultHandlerOptions
        );
        OpenLayers.Control.prototype.initialize.apply(
            this, arguments
        ); 
        this.handler = new OpenLayers.Handler.Click(
            this, {
                'click': this.onClick,
                scope: this,
            }, this.handlerOptions
        );
    }, 

    onClick: function(evt) {
        var lonlat = this.map.getLonLatFromViewPortPx(evt.xy);
        if (this.callback) {
            this.callback(lonlat);
        }
    }
});

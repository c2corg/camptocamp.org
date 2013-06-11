/*
 * @requires plugins/Tool.js
 * @requires CGXP/plugins/Permalink.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.FullScreen = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_fullscreen",

    actionTooltip: "Expand map",

    baseUrl: "/map",

    addActions: function(config) {
        var link = '';
        var baseUrl = window.location.protocol + "//" +
                      window.location.host + this.baseUrl;
        var button = new Ext.Button({
            tooltip: this.actionTooltip,
            iconCls: 'fullscreen',
            handler: function() {
              window.location = link;
            },
            scope: this
        });

        // Registers a statechange listener to update the value
        // of the permalink text field.
        Ext.state.Manager.getProvider().on({
            statechange: function(provider) {

                var params = OpenLayers.Util.getParameters();
                if (params.debug !== undefined) {
                    baseUrl = Ext.urlAppend(baseUrl, 'debug=' + params.debug);
                }
                link = provider.getLink(baseUrl);
            }
        });

        return c2corg.plugins.FullScreen.superclass.addActions.apply(this, [button]);
    }
});

Ext.preg(c2corg.plugins.FullScreen.prototype.ptype, c2corg.plugins.FullScreen);


// FIXME
// Since the Permalink plugin is always compiled in app.js
// and it creates the permalink provider, it is not useful to
// create it ourself. BUT this is probably a bad way to do that.
// We require the Permalink plugin to make sure it that the provider
// will be created
/**
 * Creates the permalink provider.
 */
/*Ext.state.Manager.setProvider(
    new GeoExt.state.PermalinkProvider({encodeType: false})
);*/

Ext.namespace("c2corg");

c2corg.layout = (function() {
    /*
     * Private
     */

    var getHeader = function() {
        return {
            border: false,
            id: 'mapheader',
            contentEl: 'page_header',
            margins: '0 0 20 0'
        };
    };

    var getFooter = function() {
        return {
            border: false,
            id: 'mapfooter',
            contentEl: 'footer',
            margins: '20 0 0 0'
        };
    };

    var getSidePanel = function() {
        return {
            width: 200,
            id: 'sidepanel',
            layout: 'accordion',
            border: false,
            collapsible: true,
            collapseMode: 'mini',
            split: true,
            margins: '0 0 0 20',
            defaults: {
                border: false,
                frame: false,
                autoHeight: true,
                width: 'auto',
                bodyStyle: 'padding: 3px;',
                map: api.map
            },
            items: [
                getLayerTreePanel(),
                getSearchPanel(),
                getHelpPanel()
            ]
        };
    };

    var getLayerTreePanel = function() {
        return api.createLayerTree({title: OpenLayers.i18n('Map data')});
    };

    var getSearchPanel = function() {
        return {
            title: OpenLayers.i18n('Search'),
            html: 'ici le formulaire de recherche'
        };
    };

    var getHelpPanel = function() {
        return {
            title: OpenLayers.i18n('Help'),
            html: OpenLayers.i18n('help detail')
        };
    };

    var getMapPanel = function() {
        return Ext.apply(api.createMapPanel(), {
            id: 'mappanel',
            margins: '0 20 0 20',
            tbar: api.createToolbar(),
            bbar: new Ext.BoxComponent({el: 'mapinfo'})
        });
    };

    var getQueryResultsPanel = function() {
        return {
            id: 'queryResults',
            height: 150,
            split: true,
            collapsible: true,
            collapsed: true,
            collapseMode: 'mini',
            animCollapse: false,
            border: false,
            layout: 'accordion',
            defaults: {
                viewConfig: {
                    emptyText: OpenLayers.i18n('no item selected')
                },
                stripeRows: true,
                forceFit: true
            },
            margins: '0 5 0 0', 
            title: '',
            html: '',
            items: api.getQuery().getComponents() 
        };
    };

    var getPayloadPanel = function() {
        return {
            layout: 'border',
            region: 'center',
            id: 'payload',
            border: false,
            margins: '0 0 0 0',
            items: [
                Ext.apply(getMapPanel(), {region: 'center'}),
                Ext.apply(getQueryResultsPanel(), {region: 'south'})
            ]
        };
    };

    /*
     * Public
     */
    return {

        /**
         * APIMethod: init
         * Initialize the page layout.
         */
        init: function() {

            api = new c2corg.API({isMainApp: true, lang: 'fr'});
            api.createMap();

            // to avoid troubles with page header
            Ext.getDom('holder').style.position = 'static';

            // hide loading message
            Ext.removeNode(Ext.getDom('mapPort'));

            new Ext.Viewport({
                layout: "border",
                id: 'mainpanel',
                items: [
                    Ext.apply(getHeader(), {region: 'north'}),
                    Ext.apply(getPayloadPanel(), {region: 'center'}),
                    Ext.apply(getSidePanel(), {region: 'west'}),
                    Ext.apply(getFooter(), {region: 'south'}),
                ]
            });

            // FIXME: should be done automatically
            api.map.zoomToExtent(new OpenLayers.Bounds.fromArray(api.baseConfig.initialExtent));
        }
    };
})();

Ext.onReady(c2corg.layout.init);

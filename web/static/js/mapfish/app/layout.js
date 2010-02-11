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
            margins: '10 0 0 0'
        };
    };

    var getSidePanel = function() {
        return new Ext.Panel({
            width: 230,
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
                autoScroll: true,
                width: 'auto',
                bodyStyle: 'padding: 3px;',
                map: api.map
            },
            items: [
                getLayerTreePanel(),
                //getSearchPanel(),
                getHelpPanel()
            ]
        });
    };

    var getLayerTreePanel = function() {
        return api.createLayerTree({title: OpenLayers.i18n('Map data')});
    };

    var getSearchPanel = function() {
        return {
            title: OpenLayers.i18n('Search'),
            id: 'searchpanel',
            items: [getQuery().getFormPanel()]
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
            tbar: getToolbar(),
            bbar: new Ext.BoxComponent({el: 'mapinfo'})
        });
    };

    var query, getQuery = function() {
        if (!query) {
            query = new c2corg.Query({api: api});
        }
        return query;
    };

    var getToolbar = function() {
        var items = api.createToolbar();

        // add separation
        items.push(' ');
        items.push('-');
        items.push(' ');

        // query tool
        items.push(new GeoExt.Action({
            control: getQuery(),
            map: api.map,
            tooltip: OpenLayers.i18n('map query'),
            toggleGroup: 'navigation',
            allowDepress: false,
            iconCls: 'info',
            handler: function() {
                //var accordion = Ext.getCmp('searchpanel');
                // FIXME: open searchpanel
            }
        }));

        // TODO: use pictos in combos instead of labels?
        items.push(getQuery().getQueryCombo());

        items.push(new GeoExt.Action({
            id: 'clearFeaturesButton',
            disabled: true,
            iconCls: 'clearFeatures',
            tooltip: OpenLayers.i18n('Clear'),
            handler: function() {
                Ext.getCmp('queryResults').collapse(); 
                Ext.getCmp('clearFeaturesButton').disable();
                getQuery().clearPreviousResults();
            },
            scope: getQuery()
        }));
        
        items.push(' ');
        items.push('-');
        items.push(' ');
        
        // recentering combo using geonames service
        items.push(new MapFish.API.GeoNamesSearchCombo({
            api: api,
            width: 200,
            emptyText: OpenLayers.i18n('Go to...')
        }));

        items.push('->');

        // permalink
        var linkPanel = getLinkPanel();
        var permalink = new MapFish.API.Permalink('permalink', null, {api: api});
        permalink.activate();
        api.map.addControl(permalink);

        items.push(new Ext.Action({
            text: OpenLayers.i18n('permalink'),
            enableToggle: true,
            handler: function() {
                var lc = Ext.get('linkContainer');
                if (!lc.isVisible()) {
                    lc.show();
                    linkPanel.enable();
                    linkPanel.doLayout();
                } else {
                    lc.hide();
                    linkPanel.disable();
                }
            }
        }));

        // expand/reduce map
        items.push(new Ext.Button({
            text: OpenLayers.i18n('Expand map'),
            handler: function() {
                var mapheader = Ext.getCmp('mapheader');
                var mapfooter = Ext.getCmp('mapfooter');
                var mappanel = Ext.getCmp('mappanel');
                var sidepanel = Ext.getCmp('sidepanel');

                if (mapheader.isVisible()) {
                    mapheader.hide();
                    mapfooter.hide();
                    if (!sidepanel.collapsed) {
                        sidepanel.collapse();
                    }
                    mappanel.doLayout();
                    api.map.updateSize();
                    this.setText(OpenLayers.i18n('Reduce map'));
                } else {
                    mapheader.show();
                    mapfooter.show();
                    if (sidepanel.collapsed) {
                        sidepanel.expand();
                    }
                    mappanel.doLayout();
                    this.setText(OpenLayers.i18n('Expand map'));
                }
            }
        }));

        return items;
    };

    var getLinkPanel = function() {
        var linkPanel = new Ext.FormPanel({
            renderTo: 'linkContainer',
            width: 450,
            title: OpenLayers.i18n('Map URL'),
            border: false,
            labelAlign: 'top',
            items: [
                {
                    xtype: 'textfield',
                    hideLabel: true,
                    width: 440,
                    id: 'permalink',
                    listeners: {
                        'focus': function() {
                            this.selectText();
                        }
                    }
                }
            ]
        });
        return linkPanel;
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
            layout: 'fit',
            defaults: {
                viewConfig: {
                    emptyText: OpenLayers.i18n('no item selected')
                },
                stripeRows: true,
                forceFit: true
            },
            margins: '0 20 0 20', 
            title: '',
            html: '',
            items: [getQuery().currentGrid]
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

            api = new c2corg.API({
                isMainApp: true,
                lang: lang // JS var set in mapSuccess.php template 
            });
            api.createMap({
                easting: 6.8,//7,
                northing: 46,//45.5,
                zoom: 12 //6
            });

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
                    Ext.apply(getFooter(), {region: 'south'})
                ]
            });
            
            // normaly done in c2corg.API.createLayerTree but Ext.Viewport seems to incorrectly update the map center
            if (api.argParserCenter && api.layerTreeNodes.length > 0 && (
                    api.layerTreeNodes.indexOf('ign_map') != -1 ||
                    api.layerTreeNodes.indexOf('ign_orthos') != -1
            )) {
                api.centerOnArgParserCenter();
            }
        }
    };
})();

Ext.onReady(c2corg.layout.init);

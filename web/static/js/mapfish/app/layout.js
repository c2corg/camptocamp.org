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

    var getSidePanel = function(api) {
	    return {
			width: 200,
			id: 'sidepanel' + api.provider,
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
	            getLayerTreePanel(api)
			]
	    };
    };

    var getLayerTreePanel = function(api) {
        return api.createLayerTree();
    };

    var getMapPanel = function(api) {
        return Ext.apply(api.createMapPanel(), {
            margins: '0 20 0 20',
            id: 'mappanel' + api.provider,
            tbar: api.createToolbar({items: ['ZoomToMaxExtent', 'Navigation', 'ZoomBox', 'LengthMeasure', 'NavigationHistory']}),
            bbar: new Ext.BoxComponent({el: 'mapinfo'})
        });
    };

    var getContentPanel = function() {
	    return new Ext.TabPanel({
		    activeTab: 0,
		    defaults: {
			    layout: 'border',
		    },
		    items: [{
		        title: 'IGN',
		        items: [
			        Ext.apply(getSidePanel(ignApi), {region: 'west'}),
			        Ext.apply(getMapPanel(ignApi), {region: 'center'})
		        ]
		    },{
			    title: 'Google Maps',
		        items: [
			        Ext.apply(getSidePanel(gmapApi), {region: 'west'}),
			        Ext.apply(getMapPanel(gmapApi), {region: 'center'})
		        ]
		    }] 
	    });
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

            ignApi = new c2corg.API({isMainApp: true, provider: 'ign', lang: 'fr'});
            gmapApi = new c2corg.API({isMainApp: true, provider: 'gmap', lang: 'fr'});
            ignApi.createMap();
            gmapApi.createMap();

            Ext.getDom('holder').style.position = 'static';

            // hide loading message
            Ext.removeNode(Ext.getDom('mapPort'));

            new Ext.Viewport({
                layout: "border",
                id: 'mainpanel',
                items: [
                    Ext.apply(getHeader(), {region: 'north'}),
                    Ext.apply(getContentPanel(), {region: 'center'}),
                    Ext.apply(getFooter(), {region: 'south'}),
                ]
            });
        }
    };
})();

Ext.onReady(c2corg.layout.init);

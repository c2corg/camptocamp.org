Ext.onReady(function () {

    // Set OpenLayers/GeoExt params + lang
    c2corg.base.init(document.documentElement.lang || "fr");
    
    app = new gxp.Viewer({
        portalConfig: {
            layout: "border",
            items: [{
                region: "north",
                id: 'mapheader',
                //margins: '0 0 20 0',
                contentEl: 'page_header'
            },
            {
                region: 'center',
                layout: 'border',
                id: 'center',
                border: true,
                bodyStyle: 'background: none',
                margins: '5 20 0 20',
                tbar: [],
                bbar: [],
                items: ["app-map"]
            }]
        },

        tools: [{
            ptype: "c2corg_layertree",
            outputConfig: {
                closable: false,
                title: c2corg.i18n("c2c data"),
                collapsible: true,
                header: true,
                width: 250
            },
            //initialThemes: ['summits'],
            url: c2corg.config.mapserverUrl
        },
        {
            ptype: "cgxp_mapopacityslider",
            orthoRef: null,
            actionTarget: "center.tbar",
            defaultBaseLayerRef: "google_terrain"
        },
        {
            ptype: "gxp_zoomtoextent",
            actionTarget: "center.tbar",
            closest: true,
            extent: c2corg.base.initialExtent
        },
        {
            ptype: "cgxp_zoom",
            actionTarget: "center.tbar",
            toggleGroup: "maptools"
        },
        {
            ptype: "gxp_navigationhistory",
            actionTarget: "center.tbar"
        },
        {
            ptype: "cgxp_permalink",
            actionTarget: "center.tbar"
        },
        {
            ptype: "cgxp_measure",
            actionTarget: "center.tbar",
            toggleGroup: "maptools"
        },
        {
            ptype: "cgxp_menushortcut",
            actionTarget: "center.tbar",
            type: '->'
        },
        {
            ptype: "cgxp_geonames",
            actionTarget: "center.tbar"
        },
        {
            ptype: "cgxp_help",
            url: c2corg.config.helpUrl,
            actionTarget: "center.tbar"
        }],

        // layer sources
        sources: c2corg.base.sources,

        // map and layers
        map: Ext.apply({
            id: "app-map" // id needed to reference map in portalConfig above
        }, c2corg.base.getMap())
    });

    app.on('ready', function() {
        // remove loading message
        Ext.fly('mapPort').fadeOut({
            remove: true
        });
        Ext.get('holder').hide();
    }, app);
});

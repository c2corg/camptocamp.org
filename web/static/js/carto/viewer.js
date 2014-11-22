Ext.onReady(function () {

    // Set OpenLayers/GeoExt params + lang
    c2corg.base.init(document.documentElement.lang, map_connected || false);
    
    var app = new gxp.Viewer({
        portalConfig: {
            layout: "border",
            items: [{
                region: "north",
                id: 'mapheader',
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
                //bbar: [], // uncomment before adding tools to bottom toolbar
                items: ["app-map"]
            },
            {
                region: "south",
                id: "mapfooter",
                contentEl: "footer",
                margins: '10 0 0 0'
            }]
        },

        tools: [{
            ptype: "c2corg_layertree",
            outputConfig: {
                closable: false,
                title: OpenLayers.i18n("c2c data"),
                collapsible: true,
                header: true,
                width: 250
            },
            url: c2corg.config.mapserverUrl
        },
        {
            ptype: "cgxp_mapopacityslider",
            orthoRef: null,
            actionTarget: "center.tbar",
            defaultBaseLayerRef: "google_terrain"
        },
        {
            ptype: "cgxp_zoom",
            actionTarget: "center.tbar",
            toggleGroup: "maptools"
        },
        {
            ptype: "cgxp_myposition",
            actionTarget: "center.tbar"
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
            toggleGroup: "maptools",
            pointMeterTemplate: new Ext.Template(
                '<table class="measure point"><tr>',
                '<td>WGS 84: </td>',
                '<td>{latd}&deg;N {lond}&deg;E</td>',
                '</tr></table>', {compiled: true})
        },
        {
            ptype: "cgxp_menushortcut",
            actionTarget: "center.tbar",
            type: '->'
        },
        {
            ptype: "cgxp_geonames",
            actionTarget: "center.tbar",
            emptyText: OpenLayers.i18n("Go to..."),
            loadingText: OpenLayers.i18n("Please wait..."),
            url: "/geonames.php?lang=" + OpenLayers.Lang.getCode()
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

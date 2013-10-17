/*
 * @requires base.js
 */

Ext.namespace("c2corg");

c2corg.Map = function (config) {

    config = Ext.applyIf(config, {
        id: config.div + "-map",
        lang: "fr",
        layers: [],
        features: null,
        georef: null,
        basemap: "google_terrain"
    });

    // Set OpenLayers/GeoExt params + lang
    c2corg.base.init(config.lang);

    var mapConfig = c2corg.base.getMap({
        controls: {
            zoomWheelEnabled: false,
            keyboardEnabled: false
        }
    });
    mapConfig.id = config.id;

    var tools = [{
        ptype: "c2corg_showfeatures",
        features: config.features
    },
    {
        ptype: "c2corg_layertree",
        outputConfig: {
            closable: false,
            title: OpenLayers.i18n("c2c data"),
            collapsible: true,
            header: true,
            width: 250
        },
        initialThemes: config.layers,
        url: c2corg.config.mapserverUrl
    },
    {
        ptype: "cgxp_mapopacityslider",
        orthoRef: null,
        actionTarget: "map.tbar",
        defaultBaseLayerRef: config.basemap
    },
    {
        ptype: "cgxp_zoom",
        actionTarget: "map.tbar",
        toggleGroup: "maptools"
    },
    {
        ptype: "gxp_navigationhistory",
        actionTarget: "map.tbar"
    },
    {
        ptype: "c2corg_fullscreen",
        actionTarget: "map.tbar",
        actionTooltip: OpenLayers.i18n("Expand map")
    },
    {
        ptype: "cgxp_measure",
        actionTarget: "map.tbar",
        toggleGroup: "maptools",
        pointMeterTemplate: new Ext.Template(
            '<table class="measure point"><tr>',
            '<td>WGS 84: </td>',
            '<td>{latd}&deg;N {lond}&deg;E</td>',
            '</tr></table>', {compiled: true})
    }];

    if (config.georef) {
        tools.push({
            ptype: "c2corg_georef",
            actionTarget: "map.tbar",
            toggleGroup: "maptools",
            initialState: config.georef.initialState,
            callback: config.georef.callback
        });
    }

    tools.push({
        ptype: "cgxp_menushortcut",
        actionTarget: "map.tbar",
        type: "->"
    },
    {
        ptype: "cgxp_geonames",
        actionTarget: "map.tbar",
        emptyText: OpenLayers.i18n("Go to..."),
        loadingText: OpenLayers.i18n("Please wait..."),
        url: "http://api.geonames.org/searchJSON?featureClass=P&featureClass=T" +
             "&username=c2corg&lang=" + config.lang
    });
    
    var viewer = new gxp.Viewer({
        portalConfig: {
            renderTo: config.div,
            height: Ext.get(config.div).getHeight(),
            layout: "fit",
            items: [config.id]
        },
        tools: tools,
        sources: c2corg.base.sources,
        map: mapConfig
    });

    viewer.on("ready", function () {
        
        // remove loading message if any
        if (config.loading) {
            Ext.fly(config.loading).fadeOut({
                remove: true
            });
        }

        if (config.center) {
            var map = this.mapPanel.map, center = config.center;
            var lonlat = new OpenLayers.LonLat(center[0], center[1]);
            lonlat = lonlat.transform("EPSG:4326", map.getProjection());
            map.setCenter(lonlat, center[2]);
        }

        Ext.EventManager.onWindowResize(function () {
            viewer.portal.doLayout();
        });
    }, viewer, config);
};

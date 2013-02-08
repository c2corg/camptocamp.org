Ext.onReady(function() {

    // Ext global settings
    Ext.BLANK_IMAGE_URL = "/static/js/carto/cgxp/ext/Ext/resources/images/default/s.gif";
    Ext.QuickTips.init();

    // OpenLayers global settings
    OpenLayers.Number.thousandsSeparator = ' ';
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
    OpenLayers.DOTS_PER_INCH = 72;
    OpenLayers.ImgPath = "/static/js/carto/cgxp/core/src/theme/img/ol/";
    
    // TODO: lang
    OpenLayers.Lang.setCode("fr");
    // GeoExt global settings
    GeoExt.Lang.set("fr");

    
    var INITIAL_EXTENT = [376681.67533691, 5282104.4018836, 1504280.7164429, 6107624.3072486];
    var RESTRICTED_EXTENT = [-20037508.34, -20037508.34, 20037508.34, 20037508.34];

    var mapserverUrl = "/cgi-bin/c2corg_wms";

    // Server errors (if any)
    var serverError = [];

    // Used to transmit event throw the application
    var EVENTS = new Ext.util.Observable();

    var IGN_KEY = "36e6aep2t3dc3gibxl295mxt"; // valid for test-alex.dev.camptocamp.org (FIXME)
    var IGN_OPTIONS = {
        displayInLayerSwitcher: false,
        visibility: false,
        transitionEffect: "resize",
        url: "http://gpp3-wxs.ign.fr/" + IGN_KEY + "/wmts",
        matrixSet: "PM",
        style: "normal",
        numZoomLevels: 19,
        attribution: '&copy;IGN <a href="http://www.geoportail.fr/" target="_blank"><img src="http://api.ign.fr/geoportail/api/js/2.0.0beta/theme/geoportal/img/logo_gp.gif"></a> <a href="http://www.geoportail.gouv.fr/depot/api/cgu/licAPI_CGUF.pdf" alt="TOS" title="TOS" target="_blank">Terms of Service</a>'
    };

    app = new gxp.Viewer({
        portalConfig: {
            layout: "border",
            // by configuring items here, we don't need to configure portalItems
            // and save a wrapping container
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
                tbar: [],
                bbar: [],
                items: [
                    "app-map"
                ]
            }]
        },

        // configuration of all tool plugins for this application
        tools: [
        {
            ptype: "c2corg_layertree",
            id: "layertree",
            outputConfig: {
                closable: false,
                title: "Objets Camptocamp",
                collapsible: true,
                header: true,
                width: 250
            },
            initialThemes: ['summits'],
            url: mapserverUrl
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
            extent: INITIAL_EXTENT
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
            url: "http://www.camptocamp.org/articles/252637/fr/aide-topoguide-cartographie-tutoriel-d-utilisation-de-l-outil-cartographique-avec-captures-d-ecran",
            actionTarget: "center.tbar"
        }],

        // layer sources
        sources: {
            "olsource": {
                ptype: "gxp_olsource"
            },
            "osm": {
                ptype: "cgxp_osmsource"
            },
            "google": {
                ptype: "cgxp_googlesource"
            }
        },

        // map and layers
        map: {
            id: "app-map", // id needed to reference map in portalConfig above
            xtype: 'cgxp_mappanel',
            extent: INITIAL_EXTENT,
            maxExtent: RESTRICTED_EXTENT,
            restrictedExtent: RESTRICTED_EXTENT,
            stateId: "map",
            projection: new OpenLayers.Projection("EPSG:900913"),
            units: "m",
            controls: [
                new OpenLayers.Control.Navigation(),
                new OpenLayers.Control.KeyboardDefaults(),
                new OpenLayers.Control.PanZoomBar({panIcons: false}),
                new OpenLayers.Control.ArgParser(),
                new OpenLayers.Control.Attribution(),
                new OpenLayers.Control.ScaleLine({
                    geodesic: true,
                    bottomInUnits: false,
                    bottomOutUnits: false
                }),
                new OpenLayers.Control.OverviewMap({
                    size: new OpenLayers.Size(180, 120),
                    mapOptions: {
                        theme: null
                    },
                    minRatio: 16,
                    maxRatio: 32,
                    layers: [new OpenLayers.Layer.OSM("OSM", [
                            'http://a.tile.openstreetmap.org/${z}/${x}/${y}.png',
                            'http://b.tile.openstreetmap.org/${z}/${x}/${y}.png',
                            'http://c.tile.openstreetmap.org/${z}/${x}/${y}.png'
                        ], {
                            buffer: 0,
                            transitionEffect: 'resize'
                        }
                    )]
                })
            ],
            layers: [{
                source: "osm",
                name: "mapnik",
                group: 'background',
                ref: 'osm'
            },
            {
                source: "google",
                name: "TERRAIN",
                group: 'background',
                ref: "google_terrain"
            },
            {
                source: "google",
                name: "HYBRID",
                group: 'background',
                ref: "google_hybrid"
            },{
                source: "olsource",
                type: "OpenLayers.Layer.WMTS",
                group: 'background',
                args: [Ext.applyIf({ 
                    name: OpenLayers.i18n("IGN - cartes"),
                    layer: "GEOGRAPHICALGRIDSYSTEMS.MAPS",
                    ref: 'ign_maps',
                    group : 'background'
                }, IGN_OPTIONS)]
            },{
                source: "olsource",
                type: "OpenLayers.Layer.WMTS",
                group: 'background',
                args: [Ext.applyIf({ 
                    name: OpenLayers.i18n("IGN - Orthos"),
                    layer: "ORTHOIMAGERY.ORTHOPHOTOS",
                    numZoomLevels: 20,
                    ref: 'ign_ortho',
                    group : 'background'
                }, IGN_OPTIONS)]
            }],
            items: []
        }
    });

    app.on('ready', function() {
        // remove loading message
        Ext.fly('mapPort').fadeOut({
            remove: true
        });
        Ext.get('holder').hide();

        if (serverError.length > 0) {
            cgxp.tools.openWindow({
                html: serverError.join('<br />')
            },OpenLayers.i18n("Error notice"),600, 500);
        }
    }, app);
});

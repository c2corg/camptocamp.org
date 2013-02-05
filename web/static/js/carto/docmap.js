Ext.onReady(function() {

c2corg = {};
c2corg.Map = function(config) {
    if (!this.initMap) {

        /*
         * Initialize the API.
         * - Set globals
         * - Create child class
         */

        Ext.QuickTips.init();
        Ext.BLANK_IMAGE_URL = "/static/js/carto/cgxp/ext/Ext/resources/images/default/s.gif";
        OpenLayers.Number.thousandsSeparator = ' ';
        OpenLayers.IMAGE_RELOAD_ATTEMPTS = 2;
        OpenLayers.DOTS_PER_INCH = 72;
        OpenLayers.ImgPath = "/static/js/carto/cgxp/core/src/theme/img/ol/";
        OpenLayers.Lang.setCode("fr");
        GeoExt.Lang.set("fr");

        OpenLayers.inherit(c2corg.Map, cgxp.api.Map);

        c2corg.Map.prototype.initMap = function() {
            

        var INITIAL_EXTENT = [-20037508.34, -20037508.34, 20037508.34, 20037508.34];
        var RESTRICTED_EXTENT = [-20037508.34, -20037508.34, 20037508.34, 20037508.34];
        var wmsURL = "/cgi-bin/c2corg_wms";
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

        var mapConfig = {
            xtype: 'cgxp_mappanel',
            extent: INITIAL_EXTENT,
            maxExtent: RESTRICTED_EXTENT,
            restrictedExtent: RESTRICTED_EXTENT,
            stateId: "map",
            projection: new OpenLayers.Projection("EPSG:900913"),
            units: "m",
            controls: [
                new OpenLayers.Control.Navigation(),
                new OpenLayers.Control.PanZoomBar({panIcons: false}),
                new OpenLayers.Control.ArgParser(),
                new OpenLayers.Control.Attribution(),
                new OpenLayers.Control.ScaleLine({
                    geodesic: true,
                    bottomInUnits: false,
                    bottomOutUnits: false
                }),
                new OpenLayers.Control.LayerSwitcher(),
                new OpenLayers.Control.OverviewMap({
                    size: new OpenLayers.Size(200, 100),
                    mapOptions: {
                        theme: null
                    },
                    minRatio: 64,
                    maxRatio: 64,
                    layers: [new OpenLayers.Layer.OSM("OSM", [
                            'http://a.tile.openstreetmap.org/${z}/${x}/${y}.png',
                            'http://b.tile.openstreetmap.org/${z}/${x}/${y}.png',
                            'http://c.tile.openstreetmap.org/${z}/${x}/${y}.png'
                        ], {
                            transitionEffect: 'resize'
                        }
                    )]
                }),
                new OpenLayers.Control.MousePosition({numDigits: 0})
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
        };

        var config = this.adaptConfigForViewer(mapConfig);
            
        var viewer = new gxp.Viewer({
            portalConfig: {
                renderTo: config.div,
                height: Ext.get(config.div).getHeight(),
                layout: "fit",
                items: [config.id]
            },
            tools: [
            {
                ptype: "cgxp_mapopacityslider",
                orthoRef: null,
                actionTarget: "map.tbar",
                defaultBaseLayerRef: "google_terrain"
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
                ptype: "cgxp_permalink",
                actionTarget: "map.tbar"
            },
            {
                ptype: "cgxp_measure",
                actionTarget: "map.tbar",
                toggleGroup: "maptools"
            },
            {
                ptype: "c2corg_georef",
                actionTarget: "map.tbar",
                toggleGroup: "maptools",
                callback: function(lonlat) {
                    document.getElementById('lon').value = lonlat.lon;
                    document.getElementById('lat').value = lonlat.lat;
                }
            }
            ],
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
            map: config
        });

            viewer.on('ready', this.onViewerReady.createDelegate(this, [viewer]));
        };

        return new c2corg.Map(config);
    }

    this.wmsURL = "/cgi-bin/c2corg_wms";
    this.queryableLayers = [];
    return cgxp.api.Map.call(this, config);
};

/*
    app.on('ready', function() {
        // remove loading message
        Ext.fly('mapPort').fadeOut({
           remove: true
        });
        Ext.get('holder').hide();
    }, app);
*/
});

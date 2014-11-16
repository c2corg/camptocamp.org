/**
 * @requires c2corg/config/config.js
 * @requires c2corg/widgets/Map.js
 * @requires OpenLayers/Control/Navigation.js
 * @requires OpenLayers/Control/KeyboardDefaults.js
 * @requires OpenLayers/Control/PanZoomBar.js
 * @requires OpenLayers/Control/ArgParser.js
 * @requires OpenLayers/Control/Attribution.js
 * @requires OpenLayers/Control/ScaleLine.js
 * @requires OpenLayers/Control/NavigationHistory.js
 */

Ext.namespace("c2corg");

c2corg.base = {

    initialExtent: [376681.67533691, 5282104.4018836, 1504280.7164429, 6107624.3072486],
    restrictedExtent: [-20037508.34, -20037508.34, 20037508.34, 20037508.34],

    events: new Ext.util.Observable(),

    ignOptions: {
        displayInLayerSwitcher: false,
        visibility: false,
        transitionEffect: "resize",
        url: "//gpp3-wxs.ign.fr/" + c2corg.config.ignKey + "/wmts",
        matrixSet: "PM",
        style: "normal",
        numZoomLevels: 19,
        attribution: '&copy;IGN <a href="http://www.geoportail.fr/" target="_blank">' +
                     '<img src="//api.ign.fr/geoportail/api/js/2.0.0beta/theme/geoportal/img/logo_gp.gif">' +
                     '</a> <a href="http://www.geoportail.gouv.fr/depot/api/cgu/licAPI_CGUF.pdf" ' +
                     'alt="TOS" title="TOS" target="_blank">' + OpenLayers.i18n('Terms of Service') + '</a>'
    },

    swisstopoOptions: {
        version: "1.0.0",
        requestEncoding: "REST",
        url: ['http://wmts0.geo.admin.ch/', 'http://wmts1.geo.admin.ch/', 'http://wmts2.geo.admin.ch/',
              'http://wmts3.geo.admin.ch/', 'http://wmts4.geo.admin.ch/'],
        style: "default",
        matrixSet: "21781",
        formatSuffix: 'jpeg',
        dimensions: ['TIME'],
        params: {
            'time': '20140520'
        },
        projection: new OpenLayers.Projection('EPSG:21781'),
        units: 'm',
        format: 'image/jpeg',
        buffer: 0,
        opacity: 1.0,
        maxExtent: new OpenLayers.Bounds(420000, 30000, 900000, 350000),
        tileOrigin: OpenLayers.LonLat(420000, 350000),
        resolutions: [650, 500, 250, 100, 50, 20, 10, 5, 2.5],
        serverResolutions: [4000, 3750, 3500, 3250, 3000, 2750, 2500, 2250, 2000, 1750, 1500, 1250, 1000, 750, 650, 500, 250, 100, 50, 20, 10, 5, 2.5, 2, 1.5, 1, 0.5]
    },

    // layer sources
    sources: {
        "olsource": {
            ptype: "gxp_olsource"
        },
        "osm": {
            ptype: "cgxp_osmsource"
        },
        "google": {
            ptype: "cgxp_googlesource",
            otherParams: "sensor=false&key=" + c2corg.config.googleKey
        }
    },

    init: function (lang, connected) {
        // Ext global settings
        Ext.BLANK_IMAGE_URL = "/static/js/carto/cgxp/ext/Ext/resources/images/default/s.gif";
        Ext.QuickTips.init();
    
        // OpenLayers global settings
        OpenLayers.Number.thousandsSeparator = " ";
        OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
        OpenLayers.DOTS_PER_INCH = 72;
        OpenLayers.ImgPath = "/static/js/carto/cgxp/core/src/theme/img/ol/";
    
        lang = lang || "fr";
        OpenLayers.Lang.setCode(lang);
        // GeoExt global settings
        GeoExt.Lang.set(lang);

        c2corg.base.basemaps = [{
            source: "osm",
            name: "mapnik",
            title: OpenLayers.i18n("OpenStreetMap"),
            group: "background",
            visibility: false,
            ref: "osm"
        }, {
            source: "google",
            name: "TERRAIN",
            title: OpenLayers.i18n("Gmaps physical"),
            group: "background",
            visibility: false,
            ref: "google_terrain"
        }, {
            source: "google",
            name: "HYBRID",
            title: OpenLayers.i18n("Gmaps hybrid"),
            group: "background",
            visibility: false,
            ref: "google_hybrid"
        }, {
            source: "olsource",
            type: "OpenLayers.Layer.WMTS",
            group: "background",
            args: [Ext.applyIf({
                name: OpenLayers.i18n("IGN maps"),
                layer: "GEOGRAPHICALGRIDSYSTEMS.MAPS",
                ref: "ign_maps",
                visibility: false,
                group : "background"
            }, c2corg.base.ignOptions)]
        }, {
            source: "olsource",
            type: "OpenLayers.Layer.WMTS",
            group: "background",
            args: [Ext.applyIf({
                name: OpenLayers.i18n("IGN orthos"),
                layer: "ORTHOIMAGERY.ORTHOPHOTOS",
                numZoomLevels: 20,
                ref: "ign_ortho",
                visibility: false,
                group : "background"
            }, c2corg.base.ignOptions)]
        }];
        
        if (connected) {
            c2corg.base.basemaps.push({
                source: "olsource",
                type: "OpenLayers.Layer.WMTS",
                group: "background",
                args: [Ext.applyIf({
                    name: OpenLayers.i18n("Swisstopo maps"),
                    layer: "ch.swisstopo.pixelkarte-farbe",
                    ref: "swisstopo_pixelkarte_farbe",
                    visibility: false,
                    group : "background"
                }, c2corg.base.swisstopoOptions)]
            });
        }
    },

    getControls: function (options) {
        options = Ext.applyIf(options || {}, {
            zoomWheelEnabled: true,
            keyboardEnabled: true
        });
        return [
            new OpenLayers.Control.Navigation({
                zoomWheelEnabled: options.zoomWheelEnabled
            }),
            new OpenLayers.Control.KeyboardDefaults({
                autoActivate: options.keyboardEnabled
            }),
            new OpenLayers.Control.PanZoomBar({panIcons: false}),
            new OpenLayers.Control.ArgParser(),
            new OpenLayers.Control.Attribution(),
            new OpenLayers.Control.ScaleLine({
                geodesic: true,
                bottomInUnits: false,
                bottomOutUnits: false
            })
        ];
    }
};

c2corg.base.getMap = function(options) {
    options = Ext.applyIf(options || {}, {
        controls: {}
    });
    // Note: because of map projection must sometimes be changed when setting
    // a new baselayer, OpenLayers.setBaseLayer() is overridden
    // in c2corg/widgets/Map.js
    return {
        xtype: "cgxp_mappanel",
        extent: c2corg.base.initialExtent,
        maxExtent: c2corg.base.restrictedExtent,
        restrictedExtent: c2corg.base.restrictedExtent,
        stateId: "map",
        projection: new OpenLayers.Projection("EPSG:3857"),
        units: "m",
        controls: c2corg.base.getControls(options.controls),
        layers: c2corg.base.basemaps,
        items: []
    };
};
